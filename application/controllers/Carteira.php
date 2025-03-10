<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Carteira extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mapos/login');
        }
        
        $this->load->model('carteira_model');
        $this->load->model('usuarios_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('permission');
        $this->load->library('pagination');
        $this->load->database();
        $this->load->helper('url');
        
        $this->data['menuCarteira'] = 'Carteira';
        $this->data['menuCarteiraAdmin'] = null;
    }

    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteira')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar carteira.');
            redirect(base_url());
        }

        $this->data['view'] = 'carteira/carteira';

        // Get user's wallet
        $userId = $this->session->userdata('id');
        $this->data['saldo'] = $this->carteira_model->getSaldo($userId);
        
        // Inicializa o total como zero
        $this->data['total'] = 0;
        
        // Get user's wallet configuration
        $carteira = $this->carteira_model->getByUsuarioId($userId);
        if ($carteira) {
            $config = $this->carteira_model->getConfiguracao($carteira->idCarteiraUsuario);
            
            if ($config) {
                // Calculate total based on configuration
                $salario_base = floatval($config->salario_base);
                $comissao_fixa = floatval($config->comissao_fixa);
                $tipo_valor_base = $config->tipo_valor_base;
                
                // Get base value for commission
                $valor_base = 0;
                if ($tipo_valor_base) {
                    $this->db->select_sum($tipo_valor_base == 'servicos' ? 'servicos_os.subTotal' : 'os.valorTotal');
                    $this->db->from($tipo_valor_base == 'servicos' ? 'servicos_os' : 'os');
                    if ($tipo_valor_base == 'servicos') {
                        $this->db->join('os', 'os.idOs = servicos_os.os_id');
                    }
                    $this->db->where('os.usuarios_id', $userId);
                    $this->db->where('MONTH(os.dataFinal)', date('m'));
                    $this->db->where('YEAR(os.dataFinal)', date('Y'));
                    $this->db->where('os.status', 'Faturado');
                    $query = $this->db->get();
                    $result = $query->row();
                    $valor_base = $result ? ($tipo_valor_base == 'servicos' ? $result->subTotal : $result->valorTotal) : 0;
                    
                    // If total type, subtract product costs
                    if ($tipo_valor_base == 'total') {
                        $this->db->select('os.idOs');
                        $this->db->from('os');
                        $this->db->where('usuarios_id', $userId);
                        $this->db->where('MONTH(dataFinal)', date('m'));
                        $this->db->where('YEAR(dataFinal)', date('Y'));
                        $this->db->where('status', 'Faturado');
                        $ordens = $this->db->get()->result();
                        
                        foreach ($ordens as $ordem) {
                            $this->db->select_sum('produtos.precoCompra');
                            $this->db->from('produtos_os');
                            $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
                            $this->db->where('produtos_os.os_id', $ordem->idOs);
                            $query_produtos = $this->db->get();
                            $result_produtos = $query_produtos->row();
                            if ($result_produtos && $result_produtos->precoCompra) {
                                $valor_base -= $result_produtos->precoCompra;
                            }
                        }
                    }
                }
                
                // Calculate commission
                $comissao = ($valor_base * $comissao_fixa) / 100;
                
                // Calculate total (salário base + comissão)
                $this->data['total'] = $salario_base + $comissao;
            }
        }
        
        $this->data['transacoes'] = $this->carteira_model->getTransacoes($userId);
        
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCarteira')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar transações.');
            redirect(base_url('carteira'));
        }

        $this->data['custom_error'] = '';

        if ($this->form_validation->run('carteira') == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {
            $data = [
                'tipo' => $this->input->post('tipo'),
                'valor' => str_replace(',', '.', str_replace('.', '', $this->input->post('valor'))),
                'data_transacao' => $this->input->post('data_transacao'),
                'descricao' => $this->input->post('descricao'),
                'carteira_usuario_id' => $this->carteira_model->getCarteiraId($this->session->userdata('id'))
            ];

            if ($this->carteira_model->add('transacoes_usuario', $data) == true) {
                // Update wallet balance
                if ($data['tipo'] == 'retirada') {
                    $data['valor'] = -$data['valor'];
                }
                $this->carteira_model->updateSaldo($data['carteira_usuario_id'], $data['valor']);

                $this->session->set_flashdata('success', 'Transação adicionada com sucesso!');
                redirect(base_url('carteira'));
            } else {
                $this->data['custom_error'] = 'Ocorreu um erro ao tentar adicionar transação.';
            }
        }

        $this->data['view'] = 'carteira/adicionarTransacao';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteira')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar transações.');
            redirect(base_url('carteira'));
        }

        $this->data['custom_error'] = '';

        if ($this->form_validation->run('carteira') == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {
            $data = [
                'tipo' => $this->input->post('tipo'),
                'valor' => str_replace(',', '.', str_replace('.', '', $this->input->post('valor'))),
                'data_transacao' => $this->input->post('data_transacao'),
                'descricao' => $this->input->post('descricao')
            ];

            $id = $this->input->post('idTransacoesUsuario');
            $oldTransaction = $this->carteira_model->getById($id);

            if ($this->carteira_model->edit('transacoes_usuario', $data, 'idTransacoesUsuario', $id) == true) {
                // Update wallet balance
                $oldValue = $oldTransaction->tipo == 'retirada' ? -$oldTransaction->valor : $oldTransaction->valor;
                $newValue = $data['tipo'] == 'retirada' ? -$data['valor'] : $data['valor'];
                $difference = $newValue - $oldValue;
                
                $this->carteira_model->updateSaldo($oldTransaction->carteira_usuario_id, $difference);

                $this->session->set_flashdata('success', 'Transação editada com sucesso!');
                redirect(base_url('carteira'));
            } else {
                $this->data['custom_error'] = 'Ocorreu um erro ao tentar editar transação.';
            }
        }

        $this->data['result'] = $this->carteira_model->getById($this->uri->segment(3));
        $this->data['view'] = 'carteira/adicionarTransacao';
        return $this->layout();
    }

    public function visualizar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteira')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar transações.');
            redirect(base_url('carteira'));
        }

        $this->data['result'] = $this->carteira_model->getById($this->uri->segment(3));
        if ($this->data['result'] == null) {
            $this->session->set_flashdata('error', 'Transação não encontrada.');
            redirect(base_url('carteira'));
        }

        $this->data['view'] = 'carteira/visualizar';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dCarteira')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir transações.');
            redirect(base_url('carteira'));
        }

        $id = $this->input->post('id');
        $transaction = $this->carteira_model->getById($id);

        if ($transaction == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir transação.');
            redirect(base_url('carteira'));
        }

        // Update wallet balance
        $value = $transaction->tipo == 'retirada' ? $transaction->valor : -$transaction->valor;
        $this->carteira_model->updateSaldo($transaction->carteira_usuario_id, $value);

        if ($this->carteira_model->delete('transacoes_usuario', 'idTransacoesUsuario', $id)) {
            $this->session->set_flashdata('success', 'Transação excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir transação.');
        }
        redirect(base_url('carteira'));
    }

    public function admin()
    {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para visualizar carteiras.');
           redirect(base_url());
        }
        
        $this->data['carteiras'] = $this->carteira_model->getAll();
        $this->data['view'] = 'carteira/vizualizarCarteiraAdmin';
        $this->load->view('tema/topo', $this->data);
    }
    
    public function adicionarCarteira()
    {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para adicionar carteiras.');
           redirect(base_url());
        }
        
        $this->data['usuarios'] = $this->usuarios_model->getAll();
        $this->data['view'] = 'carteira/adicionarCarteira';
        $this->load->view('tema/topo', $this->data);
    }
    
    public function salvarCarteira()
    {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para adicionar carteiras.');
           redirect(base_url());
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nomeCarteira', 'Nome da Carteira', 'required');
        $this->form_validation->set_rules('usuario', 'Usuário', 'required');
        $this->form_validation->set_rules('salario', 'Salário', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', 'Campos obrigatórios não foram preenchidos.');
            redirect(base_url() . 'carteira/adicionar');
        } else {
            $data = array(
                'nome' => $this->input->post('nomeCarteira'),
                'usuario_id' => $this->input->post('usuario'),
                'salario' => $this->input->post('salario')
            );
            
            if ($this->carteira_model->add('carteiras', $data) == TRUE) {
                $this->session->set_flashdata('success', 'Carteira adicionada com sucesso!');
                redirect(base_url() . 'carteira/admin');
            } else {
                $this->session->set_flashdata('error', 'Erro ao adicionar carteira.');
                redirect(base_url() . 'carteira/adicionar');
            }
        }
    }
    
    public function editarCarteira()
    {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para editar carteiras.');
           redirect(base_url());
        }
        
        $this->data['usuarios'] = $this->usuarios_model->getAll();
        $this->data['carteira'] = $this->carteira_model->getById($this->uri->segment(3));
        $this->data['view'] = 'carteira/editarCarteira';
        $this->load->view('tema/topo', $this->data);
    }
    
    public function excluirCarteira()
    {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'dCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para excluir carteiras.');
           redirect(base_url());
        }
        
        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir carteira.');
            redirect(base_url() . 'carteira/admin');
        }
        
        if ($this->carteira_model->delete('carteiras', 'id', $id)) {
            $this->session->set_flashdata('success', 'Carteira excluída com sucesso!');
            redirect(base_url() . 'carteira/admin');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir carteira.');
            redirect(base_url() . 'carteira/admin');
        }
    }
    
    public function adicionarBonus()
    {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteiraAdmin')){
           echo json_encode(array('success' => false, 'message' => 'Sem permissão para adicionar bônus.'));
           return;
        }
        
        $data = array(
            'carteira_id' => $this->input->post('carteira_id'),
            'valor' => $this->input->post('valor'),
            'descricao' => $this->input->post('descricao'),
            'data_adicao' => date('Y-m-d H:i:s')
        );
        
        if ($this->carteira_model->add('bonus', $data)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Erro ao adicionar bônus.'));
        }
    }
    
    public function adicionarComissao()
    {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteiraAdmin')){
           echo json_encode(array('success' => false, 'message' => 'Sem permissão para adicionar comissão.'));
           return;
        }
        
        $data = array(
            'carteira_id' => $this->input->post('carteira_id'),
            'valor' => $this->input->post('valor'),
            'descricao' => $this->input->post('descricao'),
            'data_adicao' => date('Y-m-d H:i:s')
        );
        
        if ($this->carteira_model->add('comissoes', $data)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Erro ao adicionar comissão.'));
        }
    }

    public function getValorBase() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteira')){
            echo json_encode(array('success' => false, 'message' => 'Você não tem permissão para esta ação.'));
            return;
        }

        $tipo = $this->input->post('tipo');
        $usuario_id = $this->input->post('usuario_id');

        if (!$tipo || !$usuario_id) {
            echo json_encode(array('success' => false, 'message' => 'Parâmetros inválidos.'));
            return;
        }

        $valor = 0;

        if ($tipo == 'servicos') {
            // Soma apenas os serviços das OS do usuário do mês atual
            $this->db->select_sum('servicos_os.subTotal');
            $this->db->from('servicos_os');
            $this->db->join('os', 'os.idOs = servicos_os.os_id');
            $this->db->where('os.usuarios_id', $usuario_id);
            $this->db->where('MONTH(os.dataFinal)', date('m'));
            $this->db->where('YEAR(os.dataFinal)', date('Y'));
            $this->db->where('os.status', 'Faturado');
            $query = $this->db->get();
            $result = $query->row();
            $valor = $result->subTotal ?: 0;
        } else if ($tipo == 'total') {
            // Primeiro, pega todas as OS do usuário do mês atual
            $this->db->select('os.idOs, os.valorTotal');
            $this->db->from('os');
            $this->db->where('usuarios_id', $usuario_id);
            $this->db->where('MONTH(dataFinal)', date('m'));
            $this->db->where('YEAR(dataFinal)', date('Y'));
            $this->db->where('status', 'Faturado');
            $query = $this->db->get();
            $ordens = $query->result();

            foreach ($ordens as $ordem) {
                // Soma o valor total da OS
                $valor += $ordem->valorTotal;

                // Busca e subtrai o precoCompra dos produtos desta OS
                $this->db->select_sum('produtos.precoCompra');
                $this->db->from('produtos_os');
                $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
                $this->db->where('produtos_os.os_id', $ordem->idOs);
                $query_produtos = $this->db->get();
                $result_produtos = $query_produtos->row();
                
                // Subtrai o custo dos produtos (se houver)
                if ($result_produtos && $result_produtos->precoCompra) {
                    $valor -= $result_produtos->precoCompra;
                }
            }
        }

        echo json_encode(array('success' => true, 'valor' => $valor));
    }
}
