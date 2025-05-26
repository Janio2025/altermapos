<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mercadopago_lib {
    private $CI;
    private $access_token;
    private $sandbox_mode;
    private $api_base_url = 'https://api.mercadopago.com';

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('mercadopago_model');
        $this->initialize();
    }

    private function initialize() {
        $config = $this->CI->mercadopago_model->get_config();
        if ($config) {
            $this->access_token = $config->access_token;
            $this->sandbox_mode = $config->sandbox_mode;
        }
    }

    private function make_request($method, $endpoint, $data = null) {
        $url = $this->api_base_url . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($this->sandbox_mode) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($error = curl_error($ch)) {
            curl_close($ch);
            throw new Exception('Erro na requisição: ' . $error);
        }
        
        curl_close($ch);
        
        return [
            'http_code' => $http_code,
            'response' => json_decode($response)
        ];
    }

    public function process_pix_withdrawal($transaction_id) {
        try {
            // Busca a transação
            $transaction = $this->CI->mercadopago_model->get_transaction($transaction_id);
            if (!$transaction) {
                throw new Exception('Transação não encontrada');
            }

            // Busca o usuário
            $this->CI->load->model('usuarios_model');
            $user = $this->CI->usuarios_model->getById($transaction->user_id);
            if (!$user) {
                throw new Exception('Usuário não encontrado');
            }

            // Prepara os dados para a API do Mercado Pago
            $payment_data = [
                'amount' => floatval($transaction->amount),
                'payment_method_id' => 'pix',
                'description' => $transaction->description,
                'payer' => [
                    'email' => $user->email,
                    'first_name' => explode(' ', $user->nome)[0],
                    'last_name' => end(explode(' ', $user->nome)),
                    'identification' => [
                        'type' => 'CPF',
                        'number' => preg_replace('/[^0-9]/', '', $user->cpf)
                    ]
                ],
                'transaction_amount' => floatval($transaction->amount),
                'description' => $transaction->description,
                'payment_method_id' => 'pix',
                'pix_key_type' => 'CPF',
                'pix_key' => $transaction->pix_key
            ];

            // Faz a requisição para a API
            $result = $this->make_request('POST', '/v1/payments', $payment_data);

            // Atualiza a transação com a resposta
            $update_data = [
                'status' => $result['http_code'] == 201 ? 'completed' : 'failed',
                'response_data' => json_encode($result['response'])
            ];
            
            $this->CI->mercadopago_model->update_transaction($transaction_id, $update_data);
            
            // Log do resultado
            $this->CI->mercadopago_model->log(
                'withdrawal_processed',
                'Processamento do saque finalizado',
                $result,
                $transaction_id
            );

            return [
                'success' => $result['http_code'] == 201,
                'data' => $result['response']
            ];

        } catch (Exception $e) {
            $this->CI->mercadopago_model->log(
                'withdrawal_error',
                $e->getMessage(),
                null,
                $transaction_id
            );
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function validate_credentials() {
        try {
            $result = $this->make_request('GET', '/users/me');
            return [
                'success' => $result['http_code'] == 200,
                'data' => $result['response']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
} 