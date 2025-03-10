<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admincarteira extends MY_Controller {
    
    public function __construct() {
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
        
        $this->data['menuCarteira'] = null;
        $this->data['menuCarteiraAdmin'] = 'Admin';
    }
    
    public function index() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para visualizar carteiras.');
           redirect(base_url());
        }
        
        $this->load->library('pagination');
        
        $config = $this->data['configuration'];
        $config['base_url'] = base_url().'index.php/admincarteira/index/';
        $config['total_rows'] = $this->carteira_model->count();
        $config['per_page'] = 10;
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $this->data['carteiras'] = $this->carteira_model->getAll($config['per_page'], $page);
        $this->data['view'] = 'admincarteira/adminCarteira';
        return $this->layout();
    }
    
    public function adicionar() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para adicionar carteiras.');
           redirect(base_url());
        }
        
        $this->data['usuarios'] = $this->usuarios_model->getAll();
        $this->data['view'] = 'admincarteira/adicionarCarteiraAdmin';
        return $this->layout();
    }
    
    public function salvar() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para adicionar carteiras.');
           redirect(base_url());
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('usuario', 'Usuário', 'required');
        $this->form_validation->set_rules('salario', 'Salário', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', 'Campos obrigatórios não foram preenchidos.');
            redirect(base_url() . 'index.php/admincarteira/adicionar');
        } else {
            // Verifica se já existe uma carteira para este usuário
            $usuario_id = $this->input->post('usuario');
            $carteira_existente = $this->carteira_model->getByUsuarioId($usuario_id);
            
            if ($carteira_existente) {
                $this->session->set_flashdata('error', 'Este usuário já possui uma carteira cadastrada.');
                redirect(base_url() . 'index.php/admincarteira/adicionar');
                return;
            }
            
            $data = array(
                'usuarios_id' => $usuario_id,
                'saldo' => $this->input->post('salario'),
                'ativo' => 1
            );
            
            if ($this->carteira_model->add('carteira_usuario', $data) == TRUE) {
                $this->session->set_flashdata('success', 'Carteira adicionada com sucesso!');
                redirect(base_url() . 'index.php/admincarteira');
            } else {
                $this->session->set_flashdata('error', 'Erro ao adicionar carteira.');
                redirect(base_url() . 'index.php/admincarteira/adicionar');
            }
        }
    }
    
    public function editar($id = null) {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para editar carteiras.');
           redirect(base_url());
        }
        
        $this->data['usuarios'] = $this->usuarios_model->getAll();
        $this->data['carteira'] = $this->carteira_model->getById($id);
        $this->data['config'] = $this->carteira_model->getConfiguracao($id);
        
        if(!$this->data['carteira']){
            $this->session->set_flashdata('error', 'Carteira não encontrada.');
            redirect(base_url() . 'admincarteira');
        }
        
        $this->data['view'] = 'admincarteira/editarCarteiraAdmin';
        return $this->layout();
    }
    
    public function excluir() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'dCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para excluir carteiras.');
           redirect(base_url());
        }
        
        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir carteira.');
            redirect(base_url() . 'index.php/admincarteira');
        }
        
        if ($this->carteira_model->delete('carteira_usuario', 'idCarteiraUsuario', $id)) {
            $this->session->set_flashdata('success', 'Carteira excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir carteira.');
        }
        redirect(base_url() . 'index.php/admincarteira');
    }
    
    public function adicionarBonus() {
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
    
    public function adicionarComissao() {
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
    
    public function atualizar() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para editar carteiras.');
           redirect(base_url());
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('usuario', 'Usuário', 'required');
        $this->form_validation->set_rules('salario_base', 'Salário Base', 'required');
        $this->form_validation->set_rules('data_salario', 'Dia do Pagamento', 'required|integer|greater_than[0]|less_than[32]');
        $this->form_validation->set_rules('tipo_repeticao', 'Tipo de Repetição', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', 'Campos obrigatórios não foram preenchidos.');
            redirect(base_url() . 'index.php/admincarteira/editar/' . $this->input->post('idCarteiraUsuario'));
        } else {
            $id = $this->input->post('idCarteiraUsuario');
            $usuario_id = $this->input->post('usuario');
            
            // Verifica se já existe uma carteira para este usuário (exceto a atual)
            $carteira_existente = $this->carteira_model->getByUsuarioId($usuario_id);
            if ($carteira_existente && $carteira_existente->idCarteiraUsuario != $id) {
                $this->session->set_flashdata('error', 'Este usuário já possui uma carteira cadastrada.');
                redirect(base_url() . 'index.php/admincarteira/editar/' . $id);
                return;
            }
            
            // Prepara os dados para atualização
            $total = str_replace(',', '.', str_replace('.', '', $this->input->post('total')));
            $data = array(
                'usuarios_id' => $usuario_id,
                'saldo' => $total
            );
            
            // Prepara os dados de configuração
            $config_data = array(
                'carteira_usuario_id' => $id,
                'salario_base' => str_replace(',', '.', str_replace('.', '', $this->input->post('salario_base'))),
                'comissao_fixa' => floatval(str_replace(',', '.', $this->input->post('comissao_fixa'))),
                'data_salario' => $this->input->post('data_salario'),
                'tipo_repeticao' => $this->input->post('tipo_repeticao'),
                'tipo_valor_base' => $this->input->post('tipo_valor_base')
            );
            
            // Inicia a transação no banco
            $this->db->trans_begin();
            
            try {
                // Atualiza a carteira
                $this->carteira_model->edit('carteira_usuario', $data, 'idCarteiraUsuario', $id);
                
                // Salva ou atualiza a configuração
                $this->carteira_model->salvarConfiguracao($config_data);
                
                // Registra o bônus se houver
                if ($this->input->post('tem_bonus') == '1') {
                    $bonus = str_replace(',', '.', str_replace('.', '', $this->input->post('bonus_valor')));
                    $bonus_data = array(
                        'tipo' => 'bonus',
                        'valor' => $bonus,
                        'data_transacao' => date('Y-m-d'),
                        'descricao' => $this->input->post('bonus_descricao'),
                        'carteira_usuario_id' => $id
                    );
                    $this->db->insert('transacoes_usuario', $bonus_data);
                }
                
                // Registra a comissão se houver
                if ($this->input->post('tem_comissao') == '1') {
                    $comissao = str_replace(',', '.', str_replace('.', '', $this->input->post('comissao_valor')));
                    $comissao_data = array(
                        'tipo' => 'comissao',
                        'valor' => $comissao,
                        'data_transacao' => date('Y-m-d'),
                        'descricao' => $this->input->post('comissao_descricao'),
                        'carteira_usuario_id' => $id
                    );
                    $this->db->insert('transacoes_usuario', $comissao_data);
                }
                
                // Registra a retirada se houver
                if ($this->input->post('tem_retirada') == '1') {
                    $retirada = str_replace(',', '.', str_replace('.', '', $this->input->post('retirada_valor')));
                    $retirada_data = array(
                        'tipo' => 'retirada',
                        'valor' => $retirada,
                        'data_transacao' => date('Y-m-d'),
                        'descricao' => $this->input->post('retirada_descricao'),
                        'carteira_usuario_id' => $id
                    );
                    $this->db->insert('transacoes_usuario', $retirada_data);
                }
                
                // Confirma a transação
                $this->db->trans_commit();
                
                $this->session->set_flashdata('success', 'Carteira atualizada com sucesso!');
                redirect(base_url() . 'index.php/admincarteira');
                
            } catch (Exception $e) {
                // Se houver erro, desfaz tudo
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Erro ao atualizar carteira: ' . $e->getMessage());
                redirect(base_url() . 'index.php/admincarteira/editar/' . $id);
            }
        }
    }

    public function getValorBase() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')){
            echo json_encode(array('success' => false, 'message' => 'Você não tem permissão para esta ação.'));
            return;
        }

        $tipo = $this->input->post('tipo');
        $usuario_id = $this->input->post('usuario_id');

        if (!$tipo || !$usuario_id) {
            echo json_encode(array('success' => false, 'message' => 'Parâmetros inválidos.'));
            return;
        }

        $this->load->model('os_model');
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

    public function visualizar($id = null) {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para visualizar carteiras.');
           redirect(base_url());
        }
        
        // Get carteira with user data
        $this->db->select('carteira_usuario.*, usuarios.nome as nome');
        $this->db->from('carteira_usuario');
        $this->db->join('usuarios', 'usuarios.idUsuarios = carteira_usuario.usuarios_id');
        $this->db->where('carteira_usuario.idCarteiraUsuario', $id);
        $carteira = $this->db->get()->row();
        
        $this->data['carteira'] = $carteira;
        $this->data['config'] = $this->carteira_model->getConfiguracao($id);
        
        if(!$this->data['carteira']){
            $this->session->set_flashdata('error', 'Carteira não encontrada.');
            redirect(base_url() . 'admincarteira');
        }

        // Calcula o total
        $this->data['total'] = 0;
        if ($this->data['config']) {
            $config = $this->data['config'];
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
                $this->db->where('os.usuarios_id', $carteira->usuarios_id);
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
                    $this->db->where('usuarios_id', $carteira->usuarios_id);
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
        
        $this->data['view'] = 'admincarteira/visualizarCarteiraAdmin';
        return $this->layout();
    }
} 