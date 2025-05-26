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
        $userId = $this->session->userdata('id_admin');
        
        // Verifica se tem usuário na sessão
        if (!$userId) {
            $this->session->set_flashdata('error', 'Usuário não identificado na sessão.');
            redirect(base_url());
        }

        // Busca a carteira do usuário
        $carteira = $this->carteira_model->getByUsuarioId($userId);
        if (!$carteira) {
            // Se não existir carteira, cria uma nova
            $dados = array(
                'usuarios_id' => $userId,
                'saldo' => 0,
                'ativo' => 1
            );
            $idCarteira = $this->carteira_model->add('carteira_usuario', $dados);
            $carteira = $this->carteira_model->getById($idCarteira);
        }

        // Busca a configuração da carteira
        $this->data['config'] = $this->carteira_model->getConfigByUsuarioId($userId);
        if (!$this->data['config']) {
            // Se não existir configuração, cria uma nova
            $config_data = array(
                'carteira_usuario_id' => $carteira->idCarteiraUsuario,
                'salario_base' => 0,
                'comissao_fixa' => 0,
                'data_salario' => date('d'),
                'tipo_repeticao' => 'mensal',
                'tipo_valor_base' => 'servicos'
            );
            $this->carteira_model->salvarConfiguracao($config_data);
            $this->data['config'] = $this->carteira_model->getConfiguracao($carteira->idCarteiraUsuario);
        }

        // Define o saldo na view
        $this->data['saldo'] = $carteira->saldo;
        $this->data['carteira'] = $carteira;
        
        // Busca as transações
        $this->data['transacoes'] = $this->carteira_model->getTransacoes($carteira->idCarteiraUsuario);
        
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
                'carteira_usuario_id' => $this->carteira_model->getCarteiraId($this->session->userdata('id_admin')),
                'considerado_saldo' => 1
            ];

            if ($this->carteira_model->registrarTransacao($data)) {
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

    /**
     * @property CI_Session $session
     * @property Permission $permission
     * @property Carteira_model $carteira_model
     */
    public function visualizar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteira')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar transações.');
            redirect(base_url('carteira'));
        }

        if (!$id) {
            $id = $this->uri->segment(3);
        }

        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID da transação não informado.');
            redirect(base_url('carteira'));
        }

        // Busca a carteira do usuário logado
        $carteira = $this->carteira_model->getByUsuarioId($this->session->userdata('id_admin'));
        if (!$carteira) {
            $this->session->set_flashdata('error', 'Carteira não encontrada.');
            redirect(base_url('carteira'));
        }

        // Busca a transação
        $transacao = $this->carteira_model->getTransacaoById($id);
        if (!$transacao) {
            $this->session->set_flashdata('error', 'Transação não encontrada.');
            redirect(base_url('carteira'));
        }

        // Verifica se a transação pertence à carteira do usuário logado
        if ($carteira->idCarteiraUsuario != $transacao->carteira_usuario_id) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar esta transação.');
            redirect(base_url('carteira'));
        }

        $this->data['result'] = $transacao;
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
        if (!$this->permission->checkPermission($this->permission->getPermissao($this->session->userdata('permissao')), 'vCarteiraAdmin')) {
           $this->session->set_flashdata('error', 'Você não tem permissão para visualizar carteiras.');
           redirect(base_url());
        }
        
        $this->data['menuAdminCarteira'] = 'AdminCarteira';
        $this->data['carteiras'] = $this->carteira_model->getAll();
        $this->data['view'] = 'carteira/vizualizarCarteiraAdmin';
        return $this->layout();
    }
    
    public function adicionarCarteira()
    {
        if (!$this->permission->checkPermission($this->permission->getPermissao($this->session->userdata('permissao')), 'aCarteiraAdmin')) {
           $this->session->set_flashdata('error', 'Você não tem permissão para adicionar carteiras.');
           redirect(base_url());
        }
        
        $this->data['menuAdminCarteira'] = 'AdminCarteira';
        $this->data['usuarios'] = $this->usuarios_model->getAll();
        $this->data['view'] = 'carteira/adicionarCarteira';
        return $this->layout();
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
    
    public function editarCarteira($id = null)
    {
        if (!$this->permission->checkPermission($this->permission->getPermissao($this->session->userdata('permissao')), 'eCarteiraAdmin')) {
           $this->session->set_flashdata('error', 'Você não tem permissão para editar carteiras.');
           redirect(base_url());
        }
        
        $this->data['menuAdminCarteira'] = 'AdminCarteira';
        $this->data['usuarios'] = $this->usuarios_model->getAll();
        $this->data['carteira'] = $this->carteira_model->getById($this->uri->segment(3));
        $this->data['view'] = 'carteira/editarCarteira';
        return $this->layout();
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

    public function getValorBase()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteira')) {
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

    public function debug()
    {
        $userId = $this->session->userdata('id');
        echo "ID do usuário na sessão: " . $userId . "<br>";
        
        $carteira = $this->carteira_model->getByUsuarioId($userId);
        echo "Dados da carteira:<br>";
        echo "<pre>";
        print_r($carteira);
        echo "</pre>";
        
        die();
    }

    public function receberComissao()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCarteira')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão para receber comissão']);
            return;
        }

        $tipo = $this->input->post('tipo');
        $usuario_id = $this->input->post('usuario_id');

        if (!$tipo || !$usuario_id) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
            return;
        }

        $this->db->trans_begin();

        try {
            // Calcula o valor base e a comissão
            $valor_base = $this->carteira_model->calcularValorBase($tipo, $usuario_id);
            $config = $this->carteira_model->getConfigByUsuarioId($usuario_id);
            $percentual_comissao = $config ? $config->comissao_fixa : 0;
            $valor_comissao = ($valor_base * $percentual_comissao) / 100;

            if ($valor_comissao <= 0) {
                echo json_encode(['success' => false, 'message' => 'Não há comissão pendente para receber']);
                return;
            }

            // Busca a carteira do usuário
            $carteira = $this->carteira_model->getByUsuarioId($usuario_id);
            if (!$carteira) {
                throw new Exception('Carteira não encontrada');
            }

            // Busca a configuração da carteira
            $config_carteira = $this->carteira_model->getConfiguracao($carteira->idCarteiraUsuario);
            if (!$config_carteira) {
                throw new Exception('Configuração da carteira não encontrada');
            }

            // Atualiza o salário base
            $novo_salario_base = $config_carteira->salario_base + $valor_comissao;
            $config_data = [
                'carteira_usuario_id' => $carteira->idCarteiraUsuario,
                'salario_base' => $novo_salario_base,
                'comissao_fixa' => $config_carteira->comissao_fixa,
                'data_salario' => $config_carteira->data_salario,
                'tipo_repeticao' => $config_carteira->tipo_repeticao,
                'tipo_valor_base' => $config_carteira->tipo_valor_base
            ];

            // Salva a nova configuração
            if (!$this->carteira_model->salvarConfiguracao($config_data)) {
                throw new Exception('Erro ao atualizar salário base');
            }

            // Registra a transação de comissão
            $dados_transacao = [
                'tipo' => 'comissao',
                'valor' => $valor_comissao,
                'data_transacao' => date('Y-m-d H:i:s'),
                'carteira_usuario_id' => $carteira->idCarteiraUsuario,
                'considerado_saldo' => 1
            ];

            if (!$this->carteira_model->registrarTransacao($dados_transacao)) {
                throw new Exception('Erro ao registrar transação de comissão');
            }

            $this->db->trans_commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function debugComissao()
    {
        $usuario_id = $this->session->userdata('id_admin');
        $carteira = $this->carteira_model->getByUsuarioId($usuario_id);
        $config = $this->carteira_model->getConfigByUsuarioId($usuario_id);
        
        echo "ID do usuário: " . $usuario_id . "<br>";
        echo "Configuração da carteira:<br>";
        echo "<pre>";
        print_r($config);
        echo "</pre>";
        
        if ($config) {
            $tipo = $config->tipo_valor_base;
            $valor_base = $this->carteira_model->calcularValorBase($tipo, $usuario_id);
            echo "Tipo de valor base: " . $tipo . "<br>";
            echo "Valor base calculado: R$ " . number_format($valor_base, 2, ',', '.') . "<br>";
            echo "Percentual de comissão: " . $config->comissao_fixa . "%<br>";
            echo "Valor da comissão: R$ " . number_format(($valor_base * $config->comissao_fixa / 100), 2, ',', '.') . "<br>";
            
            // Lista as OS do mês atual
            $this->db->select('os.*, servicos_os.subTotal');
            $this->db->from('os');
            $this->db->join('servicos_os', 'os.idOs = servicos_os.os_id', 'left');
            $this->db->where('os.usuarios_id', $usuario_id);
            $this->db->where('MONTH(os.dataFinal)', date('m'));
            $this->db->where('YEAR(os.dataFinal)', date('Y'));
            $this->db->where('os.status', 'Faturado');
            $ordens = $this->db->get()->result();
            
            echo "<br>Ordens de Serviço do mês atual:<br>";
            echo "<pre>";
            print_r($ordens);
            echo "</pre>";
        }
        
        die();
    }

    public function realizarSaquePix()
    {
        header('Content-Type: application/json');
        
        try {
            // 1. Validações iniciais
            if (!$this->session->userdata('logado')) {
                throw new Exception('Sessão expirada. Faça login novamente.');
            }

            $usuario_id = $this->session->userdata('id_admin');
            
            // 2. Carrega dados necessários
            $carteira = $this->carteira_model->getByUsuarioId($usuario_id);
            $config = $this->carteira_model->getConfigByUsuarioId($usuario_id);
            $usuario = $this->usuarios_model->getById($usuario_id);

            // 3. Validações de dados
            if (!$carteira) {
                throw new Exception('Carteira não encontrada.');
            }

            if (!$config || empty($config->chave_pix)) {
                throw new Exception('Chave PIX não configurada na carteira.');
            }

            if ($carteira->saldo <= 0) {
                throw new Exception('Saldo insuficiente para realizar saque.');
            }

            // 4. Inicia a transação no banco
            $this->db->trans_begin();

            try {
                // 5. Prepara os dados da transação
                $valor_saque = $carteira->saldo;
                $transacao = array(
                    'tipo' => 'retirada',
                    'valor' => $valor_saque,
                    'data_transacao' => date('Y-m-d H:i:s'),
                    'descricao' => 'Saque via PIX em processamento',
                    'carteira_usuario_id' => $carteira->idCarteiraUsuario,
                    'considerado_saldo' => 1
                );

                // 6. Registra a transação
                if (!$this->carteira_model->registrarTransacao($transacao)) {
                    throw new Exception('Erro ao registrar transação.');
                }

                // 7. Atualiza o saldo
                if (!$this->carteira_model->updateSaldo($carteira->idCarteiraUsuario, -$valor_saque)) {
                    throw new Exception('Erro ao atualizar saldo.');
                }

                // 8. Carrega configuração do Mercado Pago
                $this->load->config('payment_gateways');
                $mercadoPagoConfig = $this->config->item('payment_gateways')['MercadoPago'];
                
                if (empty($mercadoPagoConfig['credentials']['access_token'])) {
                    throw new Exception('Token do Mercado Pago não configurado.');
                }

                // 9. Prepara os dados para a API do Mercado Pago
                $payment_data = array(
                    'amount' => floatval($valor_saque),
                    'payment_method_id' => 'pix',
                    'description' => "Saque via PIX - Carteira #" . $carteira->idCarteiraUsuario,
                    'payer' => array(
                        'email' => $usuario->email,
                        'first_name' => explode(' ', $usuario->nome)[0],
                        'last_name' => end(explode(' ', $usuario->nome)),
                        'identification' => array(
                            'type' => 'CPF',
                            'number' => preg_replace('/[^0-9]/', '', $usuario->cpf)
                        )
                    ),
                    'transaction_amount' => floatval($valor_saque),
                    'description' => "Saque via PIX - Carteira #" . $carteira->idCarteiraUsuario,
                    'payment_method_id' => "pix",
                    'pix_key_type' => 'CPF',
                    'pix_key' => $config->chave_pix
                );

                // 10. Faz a requisição para a API do Mercado Pago
                $ch = curl_init('https://api.mercadopago.com/v1/payments');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desativa verificação SSL para localhost
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: Bearer ' . $mercadoPagoConfig['credentials']['access_token'],
                    'Content-Type: application/json'
                ));

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                if ($error = curl_error($ch)) {
                    throw new Exception('Erro na requisição: ' . $error);
                }
                
                curl_close($ch);

                // 11. Processa a resposta
                $result = json_decode($response);

                if ($http_code !== 201 && $http_code !== 200) {
                    $error_msg = isset($result->message) ? $result->message : 'Erro desconhecido';
                    throw new Exception('Erro no Mercado Pago: ' . $error_msg);
                }

                if (!isset($result->id)) {
                    throw new Exception('ID do pagamento não retornado pelo Mercado Pago');
                }

                // 12. Atualiza a descrição da transação com o ID do pagamento
                $this->carteira_model->edit('transacoes_usuario', 
                    ['descricao' => 'Saque via PIX - ID: ' . $result->id],
                    'carteira_usuario_id',
                    $carteira->idCarteiraUsuario
                );

                $this->db->trans_commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Saque realizado com sucesso! O valor será enviado para sua chave PIX.',
                    'payment_id' => $result->id,
                    'debug_info' => [
                        'http_code' => $http_code,
                        'response' => $result
                    ]
                ]);
                return;

            } catch (Exception $error) {
                $this->db->trans_rollback();
                throw new Exception('Erro ao processar pagamento: ' . $error->getMessage());
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            return;
        }
    }
}
