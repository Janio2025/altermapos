<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property CI_Pagination $pagination
 * @property CI_URI $uri
 * @property CI_Output $output
 * @property Permission $permission
 * @property Carteira_model $carteira_model
 * @property Usuarios_model $usuarios_model
 */
class Admincarteira extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mapos/login');
        }
        
        $this->load->model('carteira_model');
        $this->load->model('usuarios_model');
        $this->load->library('permission');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->database();
        $this->load->helper('url');
        
        $this->data['menuCarteiras'] = 'Carteiras';
        $this->data['menuAdminCarteira'] = 'Admin Carteiras';
    }
    
    public function index() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para visualizar carteiras.');
           redirect(base_url());
        }
        
        $this->load->library('pagination');
        
        $config = $this->data['configuration'];
        $config['base_url'] = base_url().'index.php/admincarteira/index/';
        $config['total_rows'] = $this->carteira_model->count('carteira_usuario');
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
            
            // Inicia a transação
            $this->db->trans_begin();
            
            try {
                // Adiciona a carteira
                $id_carteira = $this->carteira_model->add('carteira_usuario', $data);
                
                if ($id_carteira) {
                    // Prepara os dados de configuração
                    $config_data = array(
                        'carteira_usuario_id' => $id_carteira,
                        'salario_base' => $this->input->post('salario'),
                        'comissao_fixa' => 0,
                        'data_salario' => date('d'),
                        'tipo_repeticao' => 'mensal',
                        'tipo_valor_base' => 'servicos',
                        'chave_pix' => $this->input->post('chave_pix'),
                        'pagamento_automatico' => $this->input->post('pagamento_automatico') ? 1 : 0
                    );
                    
                    // Se o pagamento automático está sendo ativado, configura as datas
                    if ($config_data['pagamento_automatico']) {
                        $data_salario = strtotime($config_data['data_salario']);
                        $data_atual = strtotime('now');

                        // Se a data de pagamento já passou hoje, configura para o próximo período
                        if ($data_atual > $data_salario) {
                            $config_data['proximo_pagamento'] = $config_data['tipo_repeticao'] == 'mensal'
                                ? date('Y-m-d H:i:s', strtotime('+1 month', $data_salario))
                                : date('Y-m-d H:i:s', strtotime('+15 days', $data_salario));
                        } else {
                            $config_data['proximo_pagamento'] = date('Y-m-d H:i:s', $data_salario);
                        }

                        // Se não houver última data de pagamento, usa a data atual
                        if (empty($config->ultima_data_pagamento)) {
                            $config_data['ultima_data_pagamento'] = date('Y-m-d H:i:s');
                        }
                    } else {
                        // Se o pagamento automático está sendo desativado, limpa as datas
                        $config_data['proximo_pagamento'] = null;
                        $config_data['ultima_data_pagamento'] = null;
                    }
                    
                    // Salva a configuração
                    $this->carteira_model->salvarConfiguracao($config_data);
                    
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Carteira adicionada com sucesso!');
                    redirect(base_url() . 'index.php/admincarteira');
                } else {
                    throw new Exception('Erro ao adicionar carteira');
                }
            } catch (Exception $e) {
                $this->db->trans_rollback();
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
        $this->form_validation->set_rules('data_salario', 'Data do Pagamento', 'required');
        $this->form_validation->set_rules('tipo_repeticao', 'Tipo de Repetição', 'required');
        $this->form_validation->set_rules('chave_pix', 'Chave PIX', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', 'Campos obrigatórios não foram preenchidos.');
            redirect(base_url() . 'index.php/admincarteira/editar/' . $this->input->post('idCarteiraUsuario'));
            return;
        }
        
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
        
        // Formata a data de pagamento
        $data_salario = $this->input->post('data_salario');
        $data_salario = date('Y-m-d H:i:s', strtotime($data_salario));
        
        // Prepara os dados de configuração
        $config_data = array(
            'carteira_usuario_id' => $id,
            'salario_base' => str_replace(',', '.', str_replace('.', '', $this->input->post('salario_base'))),
            'comissao_fixa' => floatval(str_replace(',', '.', $this->input->post('comissao_fixa'))),
            'data_salario' => $data_salario,
            'tipo_repeticao' => $this->input->post('tipo_repeticao'),
            'tipo_valor_base' => $this->input->post('tipo_valor_base'),
            'chave_pix' => $this->input->post('chave_pix'),
            'pagamento_automatico' => $this->input->post('pagamento_automatico') ? 1 : 0
        );
        
        // Se o pagamento automático está sendo ativado, configura as datas
        if ($config_data['pagamento_automatico']) {
            $data_atual = strtotime('now');
            $data_pagamento = strtotime($data_salario);

            // Se a data de pagamento já passou hoje, configura para o próximo período
            if ($data_atual > $data_pagamento) {
                $config_data['proximo_pagamento'] = $config_data['tipo_repeticao'] == 'mensal'
                    ? date('Y-m-d H:i:s', strtotime('+1 month', $data_pagamento))
                    : date('Y-m-d H:i:s', strtotime('+15 days', $data_pagamento));
            } else {
                $config_data['proximo_pagamento'] = date('Y-m-d H:i:s', $data_pagamento);
            }

            // Se não houver última data de pagamento, usa a data atual
            if (empty($config->ultima_data_pagamento)) {
                $config_data['ultima_data_pagamento'] = date('Y-m-d H:i:s');
            }
        } else {
            // Se o pagamento automático está sendo desativado, limpa as datas
            $config_data['proximo_pagamento'] = null;
            $config_data['ultima_data_pagamento'] = null;
        }
        
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
                    'data_transacao' => date('Y-m-d H:i:s'),
                    'descricao' => $this->input->post('bonus_descricao'),
                    'carteira_usuario_id' => $id

                );
                $this->db->insert('transacoes_usuario', $bonus_data);
            }
            
            // Registra a comissão se houver
            if ($this->input->post('tem_comissao') == '1' && $this->input->post('comissao_descricao') && $this->input->post('comissao_valor') > 0) {
                $comissao = str_replace(',', '.', str_replace('.', '', $this->input->post('comissao_valor')));
                $comissao_data = array(
                    'tipo' => 'comissao',
                    'valor' => $comissao,
                    'data_transacao' => date('Y-m-d H:i:s'),
                    'descricao' => $this->input->post('comissao_descricao'),
                    'carteira_usuario_id' => $id
                );
                $this->db->insert('transacoes_usuario', $comissao_data);
            }
            
            // Registra a retirada se houver
            if ($this->input->post('tem_retirada') == '1') {
                $retirada = str_replace(',', '.', str_replace('.', '', $this->input->post('retirada_valor')));
                
                // Validação do valor da retirada
                if (!$this->carteira_model->validarRetirada($id, $retirada)) {
                    $this->session->set_flashdata('error', 'A retirada não pode deixar o saldo negativo.');
                    redirect(base_url() . 'index.php/admincarteira/editar/' . $id);
                    return;
                }
                
                $retirada_data = array(
                    'tipo' => 'retirada',
                    'valor' => $retirada,
                    'data_transacao' => date('Y-m-d H:i:s'),
                    'descricao' => $this->input->post('retirada_descricao'),
                    'carteira_usuario_id' => $id,
                    'considerado_saldo' => 0
                );
                
                // Marca todas as transações anteriores como consideradas
                $this->db->where('carteira_usuario_id', $id);
                $this->db->where('data_transacao <=', date('Y-m-d H:i:s'));
                $this->db->where('tipo !=', 'retirada');
                $this->db->update('transacoes_usuario', array('considerado_saldo' => 1));
                
                // Registra a transação que irá atualizar o saldo automaticamente
                if (!$this->carteira_model->registrarTransacao($retirada_data)) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('error', 'A retirada não pode deixar o saldo menor que R$ 0,01.');
                    redirect(base_url() . 'index.php/admincarteira/editar/' . $id);
                    return;
                }

                // Atualiza o salario_base na configuracao_carteira com o novo saldo
                $carteira = $this->carteira_model->getById($id);
                $config_data['salario_base'] = $carteira->saldo;
                $this->carteira_model->salvarConfiguracao($config_data);
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

    public function receberComissao($id) {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')){
           $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Você não tem permissão para receber comissões.')));
           return;
        }

        $this->load->model('carteira_model');
        $carteira = $this->carteira_model->getById($id);
        
        if (!$carteira) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Carteira não encontrada.')));
            return;
        }

        if ($carteira->comissao_pendente <= 0) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Não há comissão pendente para receber.')));
            return;
        }

        $this->db->trans_begin();
        
        try {
            // Prepara os dados da transação
            $data_transacao = array(
                'tipo' => 'comissao',
                'valor' => $carteira->comissao_pendente,
                'data_transacao' => date('Y-m-d H:i:s'),
                'carteira_usuario_id' => $id,
                'considerado_saldo' => 0
            );

            // Registra a transação
            if (!$this->carteira_model->registrarTransacao($data_transacao)) {
                $this->db->trans_rollback();
                $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Erro ao registrar a transação.')));
                return;
            }

            // Atualiza o saldo da carteira
            $novo_saldo = $carteira->saldo + $carteira->comissao_pendente;
            
            $this->carteira_model->edit('carteira_usuario', 
                array(
                    'saldo' => $novo_saldo,
                    'comissao_pendente' => 0
                ), 
                'idCarteiraUsuario', 
                $id
            );

            $this->db->trans_commit();
            $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => true)));
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Erro ao processar o recebimento da comissão.')));
        }
    }

    public function buscarValorBase()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para visualizar o valor base.']);
            return;
        }

        $carteira_id = $this->input->post('carteira_id');
        if (!$carteira_id) {
            echo json_encode(['success' => false, 'message' => 'ID da carteira não informado.']);
            return;
        }

        $this->load->model('carteira_model');
        
        try {
            // Busca a configuração da carteira
            $config = $this->carteira_model->getConfiguracao($carteira_id);
            if (!$config) {
                throw new Exception('Configuração não encontrada para esta carteira.');
            }

            // Busca o usuário da carteira
            $carteira = $this->carteira_model->getById($carteira_id);
            if (!$carteira) {
                throw new Exception('Carteira não encontrada.');
            }

            // Busca todas as OS em que o usuário está envolvido (como principal ou adicional)
            $this->db->select('os.idOs, os.valorTotal');
            $this->db->from('os_usuarios');
            $this->db->join('os', 'os.idOs = os_usuarios.os_id');
            $this->db->where('os_usuarios.usuario_id', $carteira->usuarios_id);
            $this->db->where('os.status', 'Faturado');
            $ordens = $this->db->get()->result();

            $valor_base = 0;
            $os_ids = [];
            foreach ($ordens as $ordem) {
                // Calcula o valor base conforme o tipo configurado
                if ($config->tipo_valor_base == 'servicos') {
                    $this->db->select_sum('servicos_os.subTotal');
                    $this->db->from('servicos_os');
                    $this->db->where('os_id', $ordem->idOs);
                    $query = $this->db->get();
                    $result = $query->row();
                    $valor_base += $result->subTotal ?: 0;
                } else {
                    // Para tipo 'total', considera o valor total menos o custo dos produtos
                    $valor_os = $ordem->valorTotal;
                    
                    // Subtrai o custo dos produtos
                    $this->db->select_sum('produtos.precoCompra');
                    $this->db->from('produtos_os');
                    $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
                    $this->db->where('produtos_os.os_id', $ordem->idOs);
                    $query_produtos = $this->db->get();
                    $result_produtos = $query_produtos->row();
                    
                    if ($result_produtos && $result_produtos->precoCompra) {
                        $valor_os -= $result_produtos->precoCompra;
                    }
                    
                    $valor_base += $valor_os;
                }

                // Adiciona o ID da OS à lista
                $os_ids[] = $ordem->idOs;
            }

            // Ordena os IDs das OS
            sort($os_ids, SORT_NUMERIC);
            
            // Processa os IDs para criar uma descrição concisa
            $descricao = 'OS: ';
            $start = $os_ids[0];
            $prev = $start;
            $sequence = false;
            $first = true;
            
            for ($i = 1; $i <= count($os_ids); $i++) {
                $current = ($i < count($os_ids)) ? $os_ids[$i] : null;
                
                // Se é o último número ou a sequência foi quebrada
                if ($current === null || $current != $prev + 1) {
                    if ($sequence && $start != $prev) {
                        // Sequência de 3 ou mais números
                        $descricao .= ($first ? '' : ', ') . $start . ' A ' . $prev;
                    } else {
                        // Número individual ou par de números
                        if ($start == $prev) {
                            $descricao .= ($first ? '' : ', ') . $start;
                        } else {
                            $descricao .= ($first ? '' : ', ') . $start . ',' . $prev;
                        }
                    }
                    
                    if ($current !== null) {
                        $start = $current;
                        $sequence = false;
                        $first = false;
                    }
                } else {
                    $sequence = true;
                }
                
                $prev = $current;
            }

            // Calcula a comissão
            $valor_comissao = ($valor_base * $config->comissao_fixa) / 100;

            echo json_encode([
                'success' => true,
                'valor_base' => number_format($valor_base, 2, ',', '.'),
                'valor_comissao' => number_format($valor_comissao, 2, ',', '.'),
                'descricao' => $descricao
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
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
            
            // Subquery para pegar todas as OS do usuário (principal ou adicional)
            $this->db->select('os_id');
            $this->db->from('os_usuarios');
            $this->db->where('usuario_id', $carteira->usuarios_id);
            $subquery = $this->db->get_compiled_select();
            
            // Get base value for commission
            $valor_base = 0;
            if ($tipo_valor_base) {
                if ($tipo_valor_base == 'servicos') {
                    $this->db->select_sum('servicos_os.subTotal');
                    $this->db->from('servicos_os');
                    $this->db->join('os', 'os.idOs = servicos_os.os_id');
                    $this->db->where('MONTH(os.dataFinal)', date('m'));
                    $this->db->where('YEAR(os.dataFinal)', date('Y'));
                    $this->db->where('os.status', 'Faturado');
                    $this->db->where("os.idOs IN ($subquery)"); // Usa a subquery
                    $query = $this->db->get();
                    $result = $query->row();
                    $valor_base = $result->subTotal ?: 0;
                } else {
                    // Para tipo total
                    $this->db->select('os.idOs, os.valorTotal');
                    $this->db->from('os');
                    $this->db->where('MONTH(dataFinal)', date('m'));
                    $this->db->where('YEAR(dataFinal)', date('Y'));
                    $this->db->where('status', 'Faturado');
                    $this->db->where("idOs IN ($subquery)"); // Usa a subquery
                    $ordens = $this->db->get()->result();
                    
                    foreach ($ordens as $ordem) {
                        $valor_base += $ordem->valorTotal;

                        // Subtrai o custo dos produtos
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

    public function historico($id = null) {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para visualizar histórico.');
           redirect(base_url());
        }
        
        if(!$id){
            $this->session->set_flashdata('error', 'ID da carteira não informado.');
            redirect(base_url() . 'admincarteira');
        }

        // Filtros
        $data_inicio = $this->input->get('data_inicio') ? $this->input->get('data_inicio') : date('Y-m-01');
        $data_fim = $this->input->get('data_fim') ? $this->input->get('data_fim') : date('Y-m-t');
        $tipo = $this->input->get('tipo');

        // Busca as transações
        $this->db->select('transacoes_usuario.*, usuarios.nome as usuario');
        $this->db->from('transacoes_usuario');
        $this->db->join('carteira_usuario', 'carteira_usuario.idCarteiraUsuario = transacoes_usuario.carteira_usuario_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = carteira_usuario.usuarios_id');
        $this->db->where('carteira_usuario_id', $id);
        $this->db->where('data_transacao >=', $data_inicio);
        $this->db->where('data_transacao <=', $data_fim);
        if($tipo) {
            $this->db->where('tipo', $tipo);
        }
        $this->db->order_by('data_transacao', 'desc');
        
        $this->data['transacoes'] = $this->db->get()->result();
        $this->data['carteira'] = $this->carteira_model->getById($id);
        $this->data['filtros'] = array(
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'tipo' => $tipo
        );
        
        // Calcula totais
        $totais = array(
            'retiradas' => 0,
            'bonus' => 0,
            'comissao' => 0,
            'salario' => 0
        );
        
        foreach($this->data['transacoes'] as $transacao) {
            $totais[$transacao->tipo] += $transacao->valor;
        }
        
        $this->data['totais'] = $totais;
        $this->data['view'] = 'admincarteira/historicoCarteiraAdmin';
        return $this->layout();
    }

    public function pagarusuario($id) {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para realizar pagamentos.');
           redirect(base_url());
        }
        
        $this->data['carteira'] = $this->carteira_model->getById($id);
        $this->data['config'] = $this->carteira_model->getConfiguracao($id);
        $this->data['ultima_transacao'] = $this->carteira_model->getUltimaTransacao($id);
        $this->data['custom_error'] = ''; // Inicializa a variável custom_error
        
        if(!$this->data['carteira']){
            $this->session->set_flashdata('error', 'Carteira não encontrada.');
            redirect(base_url() . 'admincarteira');
        }
        
        $this->data['view'] = 'admincarteira/pagarUsuario';
        return $this->layout();
    }

    public function realizarPagamento() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')){
           echo json_encode(['success' => false, 'message' => 'Você não tem permissão para realizar pagamentos.']);
           return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('retirada_valor', 'Valor da Retirada', 'required');
        $this->form_validation->set_rules('retirada_descricao', 'Descrição da Retirada', 'required');
        $this->form_validation->set_rules('idCarteiraUsuario', 'ID da Carteira', 'required');
        
        if ($this->form_validation->run() == false) {
            echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não foram preenchidos.']);
            return;
        }
            
        $id = $this->input->post('idCarteiraUsuario');
        $valor = str_replace(',', '.', str_replace('.', '', str_replace('R$ ', '', $this->input->post('retirada_valor'))));
        $descricao = $this->input->post('retirada_descricao');
        $codigo_pix = $this->input->post('codigo_pix');

        // Inicia a transação
        $this->db->trans_begin();
        
        try {
            // Busca a carteira e configuração
            $carteira = $this->carteira_model->getById($id);
            $config = $this->carteira_model->getConfiguracao($id);
            
            if (!$carteira || !$config) {
                throw new Exception('Carteira ou configuração não encontrada.');
            }

            // Valida se o valor não é maior que o saldo
            if ($valor > $carteira->saldo) {
                throw new Exception('O valor da retirada não pode ser maior que o saldo disponível.');
            }

            // Prepara os dados da transação
            $data_transacao = array(
                'tipo' => 'retirada',
                'valor' => $valor,
                'data_transacao' => date('Y-m-d H:i:s'),
                'descricao' => $descricao,
                'carteira_usuario_id' => $id,
                'considerado_saldo' => 1,
                'codigo_pix' => $codigo_pix
            );

            // Registra a transação
            $id_transacao = $this->carteira_model->registrarTransacao($data_transacao);
            if (!$id_transacao) {
                throw new Exception('Erro ao registrar a transação.');
            }

            // Atualiza o saldo da carteira
            $novo_saldo = $carteira->saldo - $valor;
            $this->carteira_model->edit('carteira_usuario', array('saldo' => $novo_saldo), 'idCarteiraUsuario', $id);

            $this->db->trans_commit();

            // Retorna os dados necessários para gerar o QR Code
            echo json_encode([
                'success' => true,
                'message' => 'Retirada realizada com sucesso!',
                'valor' => number_format($valor, 2, '.', ''),
                'chave_pix' => $config->chave_pix,
                'nome' => $carteira->nome,
                'txid' => 'RET' . date('YmdHis') . rand(1000, 9999),
                'id_transacao' => $id_transacao
            ]);

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function exportarHistorico($id = null) {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')){
           $this->session->set_flashdata('error', 'Você não tem permissão para exportar histórico.');
           redirect(base_url());
        }
        
        if(!$id){
            $this->session->set_flashdata('error', 'ID da carteira não informado.');
            redirect(base_url() . 'admincarteira');
        }

        // Busca as transações
        $this->db->select('transacoes_usuario.*, usuarios.nome as usuario');
        $this->db->from('transacoes_usuario');
        $this->db->join('carteira_usuario', 'carteira_usuario.idCarteiraUsuario = transacoes_usuario.carteira_usuario_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = carteira_usuario.usuarios_id');
        $this->db->where('carteira_usuario_id', $id);
        $this->db->order_by('data_transacao', 'desc');
        $transacoes = $this->db->get()->result();

        // Prepara o arquivo CSV
        $filename = 'historico_transacoes_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        // Cria o arquivo
        $output = fopen('php://output', 'w');

        // UTF-8 BOM para Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Cabeçalho
        fputcsv($output, array('Data', 'Tipo', 'Valor', 'Descrição', 'Usuário'));

        // Dados
        foreach ($transacoes as $t) {
            $tipo = '';
            switch($t->tipo) {
                case 'salario':
                    $tipo = 'Salário';
                    break;
                case 'bonus':
                    $tipo = 'Bônus';
                    break;
                case 'comissao':
                    $tipo = 'Comissão';
                    break;
                case 'retirada':
                    $tipo = 'Retirada';
                    break;
            }

            fputcsv($output, array(
                date('d/m/Y', strtotime($t->data_transacao)),
                $tipo,
                number_format($t->valor, 2, ',', '.'),
                $t->descricao,
                $t->usuario
            ));
        }

        fclose($output);
        exit;
    }

    public function pagarTodasComissoes()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para pagar comissões.']);
            return;
        }

        $this->load->model('carteira_model');
        $this->db->trans_begin();

        try {
            // Busca todas as carteiras ativas
            $carteiras = $this->carteira_model->getAll();
            if (empty($carteiras)) {
                throw new Exception('Nenhuma carteira encontrada.');
            }

            $sucessos = 0;
            $os_processadas = [];

            // Primeiro, vamos processar todas as carteiras e suas comissões
            foreach ($carteiras as $carteira) {
                // Busca a configuração da carteira
                $config = $this->carteira_model->getConfiguracao($carteira->idCarteiraUsuario);
                if (!$config) {
                    continue; // Pula carteiras sem configuração
                }

                // Busca todas as OS em que o usuário está envolvido (como principal ou adicional)
                $this->db->select('os.idOs, os.valorTotal');
                $this->db->from('os_usuarios');
                $this->db->join('os', 'os.idOs = os_usuarios.os_id');
                $this->db->where('os_usuarios.usuario_id', $carteira->usuarios_id);
                $this->db->where('os.status', 'Faturado');
                $ordens = $this->db->get()->result();

                $valor_base = 0;
                $os_ids = [];
                foreach ($ordens as $ordem) {
                    // Calcula o valor base conforme o tipo configurado
                    if ($config->tipo_valor_base == 'servicos') {
                        $this->db->select_sum('servicos_os.subTotal');
                        $this->db->from('servicos_os');
                        $this->db->where('os_id', $ordem->idOs);
                        $query = $this->db->get();
                        $result = $query->row();
                        $valor_base += $result->subTotal ?: 0;
                    } else {
                        // Para tipo 'total', considera o valor total menos o custo dos produtos
                        $valor_os = $ordem->valorTotal;
                        
                        // Subtrai o custo dos produtos
                        $this->db->select('produtos_os.quantidade, produtos.precoCompra');
                        $this->db->from('produtos_os');
                        $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
                        $this->db->where('produtos_os.os_id', $ordem->idOs);
                        $query_produtos = $this->db->get();
                        $produtos = $query_produtos->result();
                        
                        $custo_total = 0;
                        foreach ($produtos as $produto) {
                            $custo_total += ($produto->precoCompra * $produto->quantidade);
                        }
                        
                        $valor_os -= $custo_total;
                        $valor_base += $valor_os;
                    }

                    // Adiciona o ID da OS à lista
                    $os_ids[] = $ordem->idOs;

                    // Adiciona a OS à lista de processadas
                    if (!in_array($ordem->idOs, $os_processadas)) {
                        $os_processadas[] = $ordem->idOs;
                    }
                }

                // Calcula a comissão
                $valor_comissao = ($valor_base * $config->comissao_fixa) / 100;

                // Se houver comissão a pagar
                if ($valor_comissao > 0) {
                    $descricao = !empty($os_ids) ? 'OS: ' . implode(', ', $os_ids) : 'Comissão - ' . date('m/Y');
                    
                    $data = [
                        'tipo' => 'comissao',
                        'valor' => $valor_comissao,
                        'data_transacao' => date('Y-m-d H:i:s'),
                        'descricao' => $descricao,
                        'carteira_usuario_id' => $carteira->idCarteiraUsuario,
                        'considerado_saldo' => 1
                    ];

                    if ($this->carteira_model->registrarTransacao($data)) {
                        $sucessos++;
                    }
                }
            }

            // Depois que todas as comissões foram pagas, finaliza as OS processadas
            if (!empty($os_processadas)) {
                $this->db->where_in('idOs', $os_processadas);
                $this->db->update('os', ['status' => 'Finalizado']);
            }

            if ($sucessos > 0) {
                $this->db->trans_commit();
                $message = "Comissões pagas com sucesso para {$sucessos} usuário(s)!";
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                throw new Exception('Nenhuma comissão pendente para pagamento.');
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getValorBase()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) {
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para esta ação.']);
            return;
        }

        $tipo = $this->input->post('tipo');
        $usuario_id = $this->input->post('usuario_id');

        if (!$tipo || !$usuario_id) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
            return;
        }

        $valor = $this->carteira_model->getValorBase($usuario_id, $tipo);
        echo json_encode(['success' => true, 'valor' => $valor]);
    }

    public function gerarQRCodePix() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')){
           echo json_encode(['success' => false, 'message' => 'Você não tem permissão para gerar QR Code PIX.']);
           return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('valor', 'Valor', 'required');
        $this->form_validation->set_rules('chave_pix', 'Chave PIX', 'required');
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('txid', 'ID da Transação', 'required');

        if ($this->form_validation->run() == false) {
            echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não foram preenchidos.']);
            return;
        }

        try {
            // Dados do PIX
            $valor = $this->input->post('valor');
            $chave_pix = $this->input->post('chave_pix');
            $nome = $this->input->post('nome');
            $txid = $this->input->post('txid');

            // Carrega a biblioteca Piggly\Pix
            require_once APPPATH . 'vendor/autoload.php';
            
            // Cria o payload do PIX
            $pix = (new \Piggly\Pix\StaticPayload())
                ->setAmount($valor)
                ->setTid($txid)
                ->setDescription('Retirada de Carteira', true)
                ->setPixKey(getPixKeyType($chave_pix), $chave_pix)
                ->setMerchantName($nome)
                ->setMerchantCity('BRASIL');

            // Gera o QR Code
            $qrcode = $pix->getQRCode();

            echo json_encode([
                'success' => true,
                'qrcode' => $qrcode,
                'codigo_pix' => $pix->getPixCode(),
                'message' => 'QR Code gerado com sucesso!'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao gerar QR Code: ' . $e->getMessage()
            ]);
        }
    }

    public function salvarCodigoPix() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')){
            echo json_encode(array('success' => false, 'message' => 'Sem permissão para salvar código PIX.'));
            return;
        }

        $id_transacao = $this->input->post('id_transacao');
        $codigo_pix = $this->input->post('codigo_pix');

        if (!$id_transacao || !$codigo_pix) {
            echo json_encode(array('success' => false, 'message' => 'Dados incompletos.'));
            return;
        }

        $data = array(
            'codigo_pix' => $codigo_pix
        );

        if ($this->carteira_model->edit('transacoes_usuario', $data, 'id', $id_transacao)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Erro ao salvar código PIX.'));
        }
    }

    public function buscarRetirada() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para realizar pagamentos.');
            echo json_encode(['success' => false, 'message' => 'Sem permissão']);
            return;
        }

        $id = $this->input->post('id');
        $carteira_id = $this->input->post('carteira_id');

        if (!$id || !$carteira_id) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }

        $retirada = $this->carteira_model->getRetiradaById($id, $carteira_id);

        if ($retirada) {
            $retirada->data_transacao = date('d/m/Y H:i:s', strtotime($retirada->data_transacao));
            $retirada->valor = number_format($retirada->valor, 2, ',', '.');
            echo json_encode(['success' => true, 'data' => $retirada]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Retirada não encontrada']);
        }
    }

    public function verificarPagamentosAutomaticos() {
        if(!$this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')){
           echo json_encode(['success' => false, 'message' => 'Você não tem permissão para verificar pagamentos automáticos.']);
           return;
        }

        $this->load->model('carteira_model');
        $this->db->trans_begin();

        try {
            // Busca todas as carteiras com pagamento automático ativo
            $this->db->select('cc.*, cu.idCarteiraUsuario, cu.usuarios_id, u.nome');
            $this->db->from('configuracao_carteira cc');
            $this->db->join('carteira_usuario cu', 'cu.idCarteiraUsuario = cc.carteira_usuario_id');
            $this->db->join('usuarios u', 'u.idUsuarios = cu.usuarios_id');
            $this->db->where('cc.pagamento_automatico', 1);

            // Se foi especificado um ID de carteira, filtra por ele
            $carteira_id = $this->input->post('carteira_id');
            if ($carteira_id) {
                $this->db->where('cc.carteira_usuario_id', $carteira_id);
            }

            $configuracoes = $this->db->get()->result();

            if (empty($configuracoes)) {
                throw new Exception($carteira_id ? 'Nenhum pagamento automático configurado para esta carteira.' : 'Nenhuma carteira com pagamento automático ativo.');
            }

            $sucessos = 0;
            $erros = [];

            foreach ($configuracoes as $config) {
                // Verifica se é hora de fazer o pagamento
                $data_atual = date('Y-m-d H:i:s');
                $proximo_pagamento = strtotime($config->proximo_pagamento);
                
                if ($data_atual >= date('Y-m-d H:i:s', $proximo_pagamento)) {
                    // Registra o salário base
                    if ($config->salario_base > 0) {
                        $valor_salario = $config->salario_base;
                        
                        // Se for quinzenal, divide o salário em duas partes
                        if ($config->tipo_repeticao == 'quinzenal') {
                            $valor_salario = $valor_salario / 2;
                            
                            // Verifica se é primeira ou segunda quinzena
                            $dia_atual = date('d');
                            $dia_pagamento = date('d', strtotime($config->data_salario));
                            
                            // Se for primeira quinzena (1-15) e o dia de pagamento for maior que 15
                            // ou se for segunda quinzena (16-31) e o dia de pagamento for menor que 16
                            if (($dia_atual <= 15 && $dia_pagamento > 15) || 
                                ($dia_atual > 15 && $dia_pagamento <= 15)) {
                                continue; // Pula o pagamento se não for o dia correto
                            }
                        }

                        $data_transacao = array(
                            'tipo' => 'salario',
                            'valor' => $valor_salario,
                            'data_transacao' => date('Y-m-d H:i:s'),
                            'descricao' => $config->tipo_repeticao == 'quinzenal' 
                                ? 'Pagamento Quinzenal - Salário Base - ' . (date('d') <= 15 ? '1ª Quinzena' : '2ª Quinzena') . ' - ' . date('m/Y')
                                : 'Pagamento Mensal - Salário Base - ' . date('m/Y'),
                            'carteira_usuario_id' => $config->carteira_usuario_id,
                            'considerado_saldo' => 1
                        );

                        if (!$this->carteira_model->registrarTransacao($data_transacao)) {
                            $erros[] = "Erro ao registrar salário para {$config->nome}";
                            continue;
                        }
                    }

                    // Calcula e registra a comissão se houver
                    if ($config->comissao_fixa > 0) {
                        $valor_base = $this->carteira_model->calcularValorBase($config->usuarios_id, $config->tipo_valor_base);
                        $valor_comissao = ($valor_base * $config->comissao_fixa) / 100;
                        
                        if ($valor_comissao > 0) {
                            // Se for quinzenal, divide a comissão em duas partes
                            if ($config->tipo_repeticao == 'quinzenal') {
                                $valor_comissao = $valor_comissao / 2;
                            }

                            $data_comissao = array(
                                'tipo' => 'comissao',
                                'valor' => $valor_comissao,
                                'data_transacao' => date('Y-m-d H:i:s'),
                                'descricao' => $config->tipo_repeticao == 'quinzenal'
                                    ? 'Pagamento Quinzenal - Comissão - ' . (date('d') <= 15 ? '1ª Quinzena' : '2ª Quinzena') . ' - ' . date('m/Y')
                                    : 'Pagamento Mensal - Comissão - ' . date('m/Y'),
                                'carteira_usuario_id' => $config->carteira_usuario_id,
                                'considerado_saldo' => 1
                            );

                            if (!$this->carteira_model->registrarTransacao($data_comissao)) {
                                $erros[] = "Erro ao registrar comissão para {$config->nome}";
                                continue;
                            }
                        }
                    }

                    // Atualiza a data do próximo pagamento
                    $nova_data = $config->tipo_repeticao == 'mensal' 
                        ? date('Y-m-d H:i:s', strtotime('+1 month', $proximo_pagamento))
                        : date('Y-m-d H:i:s', strtotime('+15 days', $proximo_pagamento));

                    $this->db->where('carteira_usuario_id', $config->carteira_usuario_id);
                    $this->db->update('configuracao_carteira', array(
                        'ultima_data_pagamento' => date('Y-m-d H:i:s'),
                        'proximo_pagamento' => $nova_data
                    ));

                    $sucessos++;
                }
            }

            if ($sucessos > 0) {
                $this->db->trans_commit();
                $message = "Processados {$sucessos} pagamento(s) automático(s) com sucesso!";
                if (!empty($erros)) {
                    $message .= "\nErros encontrados:\n" . implode("\n", $erros);
                }
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['success' => false, 'message' => 'Nenhum pagamento automático pendente para processamento.']);
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Erro ao processar pagamentos automáticos: ' . $e->getMessage()]);
        }
    }
} 