<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mercadopago_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Métodos de Configuração
    public function get_config() {
        return $this->db->get('mercadopago_config')->row();
    }

    public function save_config($data) {
        if ($this->get_config()) {
            return $this->db->update('mercadopago_config', $data);
        }
        return $this->db->insert('mercadopago_config', $data);
    }

    // Métodos de Transação
    public function create_transaction($data) {
        $this->db->insert('mercadopago_transactions', $data);
        return $this->db->insert_id();
    }

    public function update_transaction($id, $data) {
        return $this->db->update('mercadopago_transactions', $data, ['id' => $id]);
    }

    public function get_transaction($id) {
        return $this->db->get_where('mercadopago_transactions', ['id' => $id])->row();
    }

    public function get_transaction_by_external_id($external_id) {
        return $this->db->get_where('mercadopago_transactions', ['external_id' => $external_id])->row();
    }

    public function get_user_transactions($user_id, $limit = 10, $offset = 0) {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get_where('mercadopago_transactions', ['user_id' => $user_id], $limit, $offset)->result();
    }

    // Métodos de Log
    public function log($type, $message, $data = null, $transaction_id = null) {
        $log_data = [
            'type' => $type,
            'message' => $message,
            'data' => $data ? json_encode($data) : null,
            'transaction_id' => $transaction_id
        ];
        return $this->db->insert('mercadopago_logs', $log_data);
    }

    public function get_logs($transaction_id = null, $limit = 50) {
        if ($transaction_id) {
            $this->db->where('transaction_id', $transaction_id);
        }
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('mercadopago_logs')->result();
    }

    // Métodos de Validação
    public function validate_config() {
        $config = $this->get_config();
        if (!$config) {
            return ['status' => false, 'message' => 'Configurações do Mercado Pago não encontradas'];
        }
        if (empty($config->access_token)) {
            return ['status' => false, 'message' => 'Token de acesso não configurado'];
        }
        return ['status' => true, 'config' => $config];
    }

    // Métodos de Integração com a Carteira
    public function process_withdrawal($user_id, $wallet_id, $amount, $pix_key) {
        // Validar configuração
        $config_validation = $this->validate_config();
        if (!$config_validation['status']) {
            return ['success' => false, 'message' => $config_validation['message']];
        }

        // Criar registro de transação
        $transaction_data = [
            'user_id' => $user_id,
            'wallet_id' => $wallet_id,
            'type' => 'withdrawal',
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'pix',
            'pix_key' => $pix_key,
            'external_id' => 'MP_' . time() . '_' . uniqid(),
            'description' => 'Saque via PIX'
        ];

        $transaction_id = $this->create_transaction($transaction_data);
        
        try {
            // Log da tentativa
            $this->log('withdrawal_attempt', 'Iniciando processo de saque', [
                'amount' => $amount,
                'user_id' => $user_id,
                'wallet_id' => $wallet_id
            ], $transaction_id);

            return [
                'success' => true,
                'transaction_id' => $transaction_id,
                'message' => 'Transação registrada com sucesso'
            ];

        } catch (Exception $e) {
            $this->log('withdrawal_error', $e->getMessage(), null, $transaction_id);
            return ['success' => false, 'message' => 'Erro ao processar saque: ' . $e->getMessage()];
        }
    }
} 