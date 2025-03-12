<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mercadopago extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('mercadopago_model');
        $this->load->library('mercadopago_lib');
        
        // Verifica se está logado
        if (!$this->session->userdata('logado')) {
            redirect('login');
        }
    }

    public function index() {
        $data['config'] = $this->mercadopago_model->get_config();
        $data['transactions'] = $this->mercadopago_model->get_user_transactions($this->session->userdata('id'));
        
        $this->load->view('mercadopago/index', $data);
    }

    public function config() {
        // Verifica permissão de administrador
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aConfig')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar esta página.');
            redirect('mercadopago');
        }

        $data['config'] = $this->mercadopago_model->get_config();
        $this->load->view('mercadopago/config', $data);
    }

    public function save_config() {
        // Verifica permissão de administrador
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aConfig')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar esta página.');
            redirect('mercadopago');
        }

        $data = array(
            'access_token' => $this->input->post('access_token'),
            'public_key' => $this->input->post('public_key'),
            'client_id' => $this->input->post('client_id'),
            'client_secret' => $this->input->post('client_secret'),
            'sandbox_mode' => $this->input->post('sandbox_mode') ? 1 : 0,
            'webhook_url' => $this->input->post('webhook_url')
        );

        if ($this->mercadopago_model->save_config($data)) {
            // Valida as credenciais
            $validation = $this->mercadopago_lib->validate_credentials();
            
            if ($validation['success']) {
                $this->session->set_flashdata('success', 'Configurações salvas com sucesso!');
            } else {
                $this->session->set_flashdata('warning', 'Configurações salvas, mas as credenciais podem estar inválidas: ' . $validation['message']);
            }
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar configurações.');
        }

        redirect('mercadopago/config');
    }

    public function process_withdrawal() {
        // Verifica se está logado
        if (!$this->session->userdata('logado')) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Usuário não está logado'
            ]));
            return;
        }

        $amount = $this->input->post('amount');
        $pix_key = $this->input->post('pix_key');
        $wallet_id = $this->input->post('wallet_id');

        if (!$amount || !$pix_key || !$wallet_id) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Dados incompletos'
            ]));
            return;
        }

        // Processa o saque
        $result = $this->mercadopago_model->process_withdrawal(
            $this->session->userdata('id'),
            $wallet_id,
            $amount,
            $pix_key
        );

        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    public function get_transaction_status($transaction_id) {
        $transaction = $this->mercadopago_model->get_transaction($transaction_id);
        
        if (!$transaction) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Transação não encontrada'
            ]));
            return;
        }

        // Verifica se a transação pertence ao usuário
        if ($transaction->user_id != $this->session->userdata('id')) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Transação não pertence ao usuário'
            ]));
            return;
        }

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'success' => true,
            'data' => $transaction
        ]));
    }

    public function get_logs($transaction_id = null) {
        // Verifica permissão de administrador
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aConfig')) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Sem permissão'
            ]));
            return;
        }

        $logs = $this->mercadopago_model->get_logs($transaction_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'success' => true,
            'data' => $logs
        ]));
    }
} 