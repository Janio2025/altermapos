<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Os extends MY_Controller
{
    /**
     * author: Ramon Silva
     * email: silva018-mg@yahoo.com.br
     *
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('os_model');
        $this->data['menuOs'] = 'OS';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->load->library('pagination');
        $this->load->model('mapos_model');

        $where_array = [];

        $pesquisa = $this->input->get('pesquisa');
        $status = $this->input->get('status');
        $de = $this->input->get('data');
        $ate = $this->input->get('data2');

        if ($pesquisa) {
            $where_array['pesquisa'] = $pesquisa;
        }
        if ($status) {
            $where_array['status'] = $status;
        }
        if ($de) {
            $de = explode('/', $de);
            $de = $de[2] . '-' . $de[1] . '-' . $de[0];

            $where_array['de'] = $de;
        }
        if ($ate) {
            $ate = explode('/', $ate);
            $ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

            $where_array['ate'] = $ate;
        }

        $this->data['configuration']['base_url'] = site_url('os/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->os_model->count('os');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->os_model->getOs(
            'os',
            'os.*,
            COALESCE((SELECT SUM(produtos_os.preco * produtos_os.quantidade ) FROM produtos_os WHERE produtos_os.os_id = os.idOs), 0) totalProdutos,
            COALESCE((SELECT SUM(servicos_os.preco * servicos_os.quantidade ) FROM servicos_os WHERE servicos_os.os_id = os.idOs), 0) totalServicos',
            $where_array,
            $this->data['configuration']['per_page'],
            $this->uri->segment(3)
        );

        $this->data['texto_de_notificacao'] = $this->data['configuration']['notifica_whats'];
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['view'] = 'os/os';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar O.S.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->load->model('clientes_model');
        $this->load->model('usuarios_model');
        $this->load->model('organizadores_model');
        
        $this->data['organizadores'] = $this->organizadores_model->get('organizadores', '*');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('os') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $dataInicial = $this->input->post('dataInicial');
            $dataFinal = $this->input->post('dataFinal');
            $termoGarantiaId = $this->input->post('termoGarantia');

            try {
                $dataInicial = explode('/', $dataInicial);
                $dataInicial = $dataInicial[2] . '-' . $dataInicial[1] . '-' . $dataInicial[0];

                if ($dataFinal) {
                    $dataFinal = explode('/', $dataFinal);
                    $dataFinal = $dataFinal[2] . '-' . $dataFinal[1] . '-' . $dataFinal[0];
                } else {
                    $dataFinal = date('Y/m/d');
                }

                $termoGarantiaId = (!$termoGarantiaId == null || !$termoGarantiaId == '')
                    ? $this->input->post('garantias_id')
                    : null;
            } catch (Exception $e) {
                $dataInicial = date('Y/m/d');
                $dataFinal = date('Y/m/d');
            }

            $data = [
                'dataInicial' => $dataInicial,
                'clientes_id' => $this->input->post('clientes_id'),
                'usuarios_id' => $this->input->post('usuarios_id'),
                'dataFinal' => $dataFinal,
                'garantia' => set_value('garantia'),
                'garantias_id' => $termoGarantiaId,
                'descricaoProduto' => set_value('descricaoProduto'),
                'marcaProdutoOs' => set_value('marcaProdutoOs'),
                'modeloProdutoOs' => set_value('modeloProdutoOs'),
                'nsProdutoOs' => set_value('nsProdutoOs'),
                'ucProdutoOs' => set_value('ucProdutoOs'),
                'contrato_seguradora' => set_value('contrato_seguradora'),
                'localizacaoProdutoOs' => set_value('localizacaoProdutoOs'),
                'analiseBasica' => set_value('analiseBasica'),
                'defeito' => set_value('defeito'),
                'status' => set_value('status'),
                'observacoes' => set_value('observacoes'),
                'laudoTecnico' => set_value('laudoTecnico'),
                'faturado' => 0,
                'organizador_id' => $this->input->post('organizador_id'),
                'compartimento_id' => $this->input->post('compartimento_id')
            ];

            if (is_numeric($id = $this->os_model->add('os', $data, true))) {
                $this->load->model('mapos_model');
                $this->load->model('usuarios_model');

                // Debug dos dados do POST
                log_info('Dados do POST: ' . print_r($_POST, true));
                
                // Salvar usuários adicionais
                $usuarios_adicionais = $this->input->post('usuarios_adicionais');
                log_info('Usuários adicionais recebidos (raw): ' . print_r($usuarios_adicionais, true));
                
                if (!empty($usuarios_adicionais)) {
                    foreach ($usuarios_adicionais as $usuario_id) {
                        $dados_usuario = [
                            'os_id' => $id,
                            'usuario_id' => $usuario_id,
                            'data_adicao' => date('Y-m-d H:i:s'),
                            'principal' => 0
                        ];
                        log_info('Tentando adicionar usuário adicional: ' . print_r($dados_usuario, true));
                        $resultado = $this->os_model->add('os_usuarios', $dados_usuario, false);
                        log_info('Resultado da adição: ' . ($resultado ? 'sucesso' : 'falha'));
                        if (!$resultado) {
                            log_info('Erro ao adicionar usuário: ' . print_r($this->db->error(), true));
                        }
                    }
                } else {
                    log_info('Nenhum usuário adicional recebido');
                }

                $idOs = $id;
                $os = $this->os_model->getById($idOs);
                $emitente = $this->mapos_model->getEmitente();

                // Atualiza ocupação do compartimento
                $compartimento_id = $this->input->post('compartimento_id');
                if (!empty($compartimento_id)) {
                    $this->load->model('compartimentos_model');
                    $this->compartimentos_model->atualizarOcupacao($compartimento_id, 'os', $idOs, 'adicionar');
                }

                $tecnico = $this->usuarios_model->getById($os->usuarios_id);

                // Verificar configuração de notificação
                if ($this->data['configuration']['os_notification'] != 'nenhum' && $this->data['configuration']['email_automatico'] == 1) {
                    $remetentes = [];
                    switch ($this->data['configuration']['os_notification']) {
                        case 'todos':
                            array_push($remetentes, $os->email);
                            array_push($remetentes, $tecnico->email);
                            array_push($remetentes, $emitente->email);
                            break;
                        case 'cliente':
                            array_push($remetentes, $os->email);
                            break;
                        case 'tecnico':
                            array_push($remetentes, $tecnico->email);
                            break;
                        case 'emitente':
                            array_push($remetentes, $emitente->email);
                            break;
                        default:
                            array_push($remetentes, $os->email);
                            break;
                    }
                    $this->enviarOsPorEmail($idOs, $remetentes, 'Ordem de Serviço - Criada');
                }

                $this->session->set_flashdata('success', 'OS adicionada com sucesso, você pode adicionar produtos ou serviços a essa OS nas abas de Produtos e Serviços!');
                log_info('Adicionou uma OS. ID: ' . $id);
                redirect(site_url('os/editar/') . $id);
            } else {
                $this->data['custom_error'] = '<div class="alert">Ocorreu um erro.</div>';
            }
        }

        $this->data['view'] = 'os/adicionarOs';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar O.S.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $this->data['texto_de_notificacao'] = $this->data['configuration']['notifica_whats'];

        // Get the OS data first
        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));

        // Carrega os usuários adicionais
        $this->load->model('usuarios_model');
        $this->load->model('organizadores_model');
        $this->load->model('compartimentos_model');
        $this->data['organizadores'] = $this->organizadores_model->get('organizadores', '*');
        if ($this->data['result']->organizador_id) {
            $this->data['compartimentos'] = $this->compartimentos_model->getCompartimentosByOrganizador($this->data['result']->organizador_id);
        } else {
            $this->data['compartimentos'] = [];
        }
        $this->data['usuarios_adicionais'] = $this->os_model->getUsuariosAdicionais($this->uri->segment(3));

        $this->data['editavel'] = $this->os_model->isEditable($this->input->post('idOs'));
        if (!$this->data['editavel']) {
            $this->session->set_flashdata('error', 'Esta OS já e seu status não pode ser alterado e nem suas informações atualizadas. Por favor abrir uma nova OS.');
            redirect(site_url('os'));
        }

        if ($this->form_validation->run('os') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $dataInicial = $this->input->post('dataInicial');
            $dataFinal = $this->input->post('dataFinal');
            $termoGarantiaId = $this->input->post('garantias_id') ?: null;

            try {
                $dataInicial = explode('/', $dataInicial);
                $dataInicial = $dataInicial[2] . '-' . $dataInicial[1] . '-' . $dataInicial[0];

                $dataFinal = explode('/', $dataFinal);
                $dataFinal = $dataFinal[2] . '-' . $dataFinal[1] . '-' . $dataFinal[0];
            } catch (Exception $e) {
                $dataInicial = date('Y/m/d');
            }

            $data = [
                'dataInicial' => $dataInicial,
                'dataFinal' => $dataFinal,
                'garantia' => $this->input->post('garantia'),
                'garantias_id' => $termoGarantiaId,
                'descricaoProduto' => $this->input->post('descricaoProduto'),
                'marcaProdutoOs' => $this->input->post('marcaProdutoOs'),
                'modeloProdutoOs' => $this->input->post('modeloProdutoOs'),
                'nsProdutoOs' => $this->input->post('nsProdutoOs'),
                'ucProdutoOs' => $this->input->post('ucProdutoOs'),
                'contrato_seguradora' => $this->input->post('contrato_seguradora'),
                'localizacaoProdutoOs'=> $this->input->post('localizacaoProdutoOs'),
                'analiseBasica' => $this->input->post('analiseBasica'),
                'defeito' => $this->input->post('defeito'),
                'status' => $this->input->post('status'),
                'observacoes' => $this->input->post('observacoes'),
                'laudoTecnico' => $this->input->post('laudoTecnico'),
                'usuarios_id' => $this->input->post('usuarios_id'),
                'clientes_id' => $this->input->post('clientes_id'),
                'organizador_id' => $this->input->post('organizador_id'),
                'compartimento_id' => $this->input->post('compartimento_id')
            ];
            $os = $this->os_model->getById($this->input->post('idOs'));

            //Verifica para poder fazer a devolução do produto para o estoque caso OS seja cancelada.

            if (strtolower($this->input->post('status')) == "cancelado" && strtolower($os->status) != "cancelado") {
                $this->devolucaoEstoque($this->input->post('idOs'));
            }

            if (strtolower($os->status) == "cancelado" && strtolower($this->input->post('status')) != "cancelado") {
                $this->debitarEstoque($this->input->post('idOs'));
            }

            if ($this->os_model->edit('os', $data, 'idOs', $this->input->post('idOs')) == true) {
                $this->load->model('mapos_model');
                $this->load->model('usuarios_model');

                // Atualiza os usuários adicionais
                $os_id = $this->input->post('idOs');
                
                // Obtém os usuários adicionais atuais
                $usuarios_adicionais_atuais = $this->os_model->getUsuariosAdicionais($os_id);
                $ids_atuais = array_map(function($usuario) {
                    return $usuario->usuario_id;
                }, $usuarios_adicionais_atuais);
                
                // Obtém os novos usuários selecionados
                $usuarios_adicionais = $this->input->post('usuarios_adicionais');
                if (!$usuarios_adicionais) {
                    $usuarios_adicionais = [];
                }
                
                // Remove usuários que não estão mais selecionados
                foreach ($ids_atuais as $id) {
                    if (!in_array($id, $usuarios_adicionais)) {
                        $this->os_model->removerUsuarioAdicional($os_id, $id);
                    }
                }
                
                // Adiciona novos usuários que ainda não existem
                foreach ($usuarios_adicionais as $usuario_id) {
                    if (!$this->os_model->usuarioAdicionalExiste($os_id, $usuario_id)) {
                        $dados_usuario = [
                            'os_id' => $os_id,
                            'usuario_id' => $usuario_id,
                            'data_adicao' => date('Y-m-d H:i:s'),
                            'principal' => 0
                        ];
                        $this->os_model->adicionarUsuarioAdicional($dados_usuario);
                    }
                }

                // Prepara dados para notificação por email
                $os = $this->os_model->getById($os_id);
                $emitente = $this->mapos_model->getEmitente();
                $tecnico = $this->usuarios_model->getById($os->usuarios_id);

                // Verificar configuração de notificação
                if ($this->data['configuration']['os_notification'] != 'nenhum' && $this->data['configuration']['email_automatico'] == 1) {
                    $remetentes = [];
                    switch ($this->data['configuration']['os_notification']) {
                        case 'todos':
                            array_push($remetentes, $os->email);
                            array_push($remetentes, $tecnico->email);
                            array_push($remetentes, $emitente->email);
                            break;
                        case 'cliente':
                            array_push($remetentes, $os->email);
                            break;
                        case 'tecnico':
                            array_push($remetentes, $tecnico->email);
                            break;
                        case 'emitente':
                            array_push($remetentes, $emitente->email);
                            break;
                        default:
                            array_push($remetentes, $os->email);
                            break;
                    }
                    $this->enviarOsPorEmail($os_id, $remetentes, 'Ordem de Serviço - Editada');
                }

                $this->session->set_flashdata('success', 'Os editada com sucesso!');
                log_info('Alterou uma OS. ID: ' . $this->input->post('idOs'));
                redirect(site_url('os/editar/') . $this->input->post('idOs'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));
        $this->data['anotacoes'] = $this->os_model->getAnotacoes($this->uri->segment(3));

        if ($return = $this->os_model->valorTotalOS($this->uri->segment(3))) {
            $this->data['totalServico'] = $return['totalServico'];
            $this->data['totalProdutos'] = $return['totalProdutos'];
        }

        $this->load->model('mapos_model');
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        // Carrega os avers da OS
        $this->data['avers'] = $this->os_model->getAvers($this->uri->segment(3));

        $this->data['view'] = 'os/editarOs';
        return $this->layout();
    }

    public function visualizar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->data['texto_de_notificacao'] = $this->data['configuration']['notifica_whats'];

        $this->load->model('mapos_model');
        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));
        $this->data['anotacoes'] = $this->os_model->getAnotacoes($this->uri->segment(3));
        $this->data['editavel'] = $this->os_model->isEditable($this->uri->segment(3));

        $this->data['qrCode'] = null;

        if ($return = $this->os_model->valorTotalOS($this->uri->segment(3))) {
            $this->data['totalServico'] = $return['totalServico'];
            $this->data['totalProdutos'] = $return['totalProdutos'];
            $this->data['result']->total_aver = $return['total_aver'];
        }

        if (!empty($this->data['configuration']['pix_key'])) {
            $this->data['qrCode'] = $this->os_model->getQrCode(
                $this->uri->segment(3),
                $this->data['configuration']['pix_key'],
                $this->data['emitente']
            );
            
            // Formata a chave PIX
            $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);
        }

        $this->data['modalGerarPagamento'] = $this->load->view(
            'cobrancas/modalGerarPagamento',
            [
                'id' => $this->uri->segment(3),
                'tipo' => 'os',
            ],
            true
        );
        $this->data['view'] = 'os/visualizarOs';

        return $this->layout();
    }

    public function imprimir()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->load->model('os_model'); // Certifique-se de carregar o modelo correto

        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        if ($return = $this->os_model->valorTotalOS($this->uri->segment(3))) {
            $this->data['totalServico'] = $return['totalServico'];
            $this->data['totalProdutos'] = $return['totalProdutos'];
            $this->data['result']->total_aver = $return['total_aver'];
        }

        if (!empty($this->data['configuration']['pix_key'])) {
            $this->data['qrCode'] = $this->os_model->getQrCode(
                $this->uri->segment(3),
                $this->data['configuration']['pix_key'],
                $this->data['emitente']
            );
            
            // Chamando o método formatarChave corretamente
            $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);
        }

        $this->data['imprimirAnexo'] = isset($_ENV['IMPRIMIR_ANEXOS']) ? filter_var($_ENV['IMPRIMIR_ANEXOS'], FILTER_VALIDATE_BOOLEAN) : false;

        $this->load->view('os/imprimirOs', $this->data);
    }

    public function imprimirLaudo()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->load->model('os_model'); // Certifique-se de carregar o modelo correto

        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        if (!empty($this->data['configuration']['pix_key'])) {
            $this->data['qrCode'] = $this->os_model->getQrCode(
                $this->uri->segment(3),
                $this->data['configuration']['pix_key'],
                $this->data['emitente']
            );
            
            // Chamando o método formatarChave corretamente
            $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);
        }

        $this->data['imprimirAnexo'] = isset($_ENV['IMPRIMIR_ANEXOS']) ? filter_var($_ENV['IMPRIMIR_ANEXOS'], FILTER_VALIDATE_BOOLEAN) : false;

        $this->load->view('os/imprimirLaudo', $this->data);
    }

    /**
     * Método para formatar a chave PIX
     */
    private function formatarChave($chave)
    {
        // Remove espaços e caracteres especiais
        $chave = preg_replace('/[^a-zA-Z0-9]/', '', $chave);
        
        // Formata a chave em grupos de 4 caracteres
        $chave = str_split($chave, 4);
        $chave = implode(' ', $chave);
        
        return $chave;
    }

    public function imprimirTermica()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        $this->load->view('os/imprimirOsTermica', $this->data);
    }

    public function enviar_email()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para enviar O.S. por e-mail.');
            redirect(base_url());
        }

        $this->load->model('mapos_model');
        $this->load->model('usuarios_model');
        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        if (!isset($this->data['result']->email)) {
            $this->session->set_flashdata('error', 'O cliente não tem e-mail cadastrado.');
            redirect(site_url('os'));
        }

        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();

        if (!isset($this->data['emitente']->email)) {
            $this->session->set_flashdata('error', 'Efetue o cadastro dos dados de emitente');
            redirect(site_url('os'));
        }

        $idOs = $this->uri->segment(3);

        $emitente = $this->data['emitente'];
        $tecnico = $this->usuarios_model->getById($this->data['result']->usuarios_id);

        // Verificar configuração de notificação
        $ValidarEmail = false;
        if ($this->data['configuration']['os_notification'] != 'nenhum') {
            $remetentes = [];
            switch ($this->data['configuration']['os_notification']) {
                case 'todos':
                    array_push($remetentes, $this->data['result']->email);
                    array_push($remetentes, $tecnico->email);
                    array_push($remetentes, $emitente->email);
                    $ValidarEmail = true;
                    break;
                case 'cliente':
                    array_push($remetentes, $this->data['result']->email);
                    $ValidarEmail = true;
                    break;
                case 'tecnico':
                    array_push($remetentes, $tecnico->email);
                    break;
                case 'emitente':
                    array_push($remetentes, $emitente->email);
                    break;
                default:
                    array_push($remetentes, $this->data['result']->email);
                    $ValidarEmail = true;
                    break;
            }

            if ($ValidarEmail) {
                if (empty($this->data['result']->email) || !filter_var($this->data['result']->email, FILTER_VALIDATE_EMAIL)) {
                    $this->session->set_flashdata('error', 'Por favor preencha o email do cliente');
                    redirect(site_url('os/visualizar/') . $this->uri->segment(3));
                }
            }

            $enviouEmail = $this->enviarOsPorEmail($idOs, $remetentes, 'Ordem de Serviço');

            if ($enviouEmail) {
                $this->session->set_flashdata('success', 'O email está sendo processado e será enviado em breve.');
                log_info('Enviou e-mail para o cliente: ' . $this->data['result']->nomeCliente . '. E-mail: ' . $this->data['result']->email);
                redirect(site_url('os'));
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao enviar e-mail.');
                redirect(site_url('os'));
            }
        }

        $this->session->set_flashdata('success', 'O sistema está com uma configuração ativada para não notificar. Entre em contato com o administrador.');
        redirect(site_url('os'));
    }

    private function devolucaoEstoque($id)
    {
        if ($produtos = $this->os_model->getProdutos($id)) {
            $this->load->model('produtos_model');
            if ($this->data['configuration']['control_estoque']) {
                foreach ($produtos as $p) {
                    $this->produtos_model->updateEstoque($p->produtos_id, $p->quantidade, '+');
                    log_info('ESTOQUE: Produto id ' . $p->produtos_id . ' voltou ao estoque. Quantidade: ' . $p->quantidade . '. Motivo: Cancelamento/Exclusão');
                }
            }
        }
    }

    private function debitarEstoque($id)
    {
        if ($produtos = $this->os_model->getProdutos($id)) {
            $this->load->model('produtos_model');
            if ($this->data['configuration']['control_estoque']) {
                foreach ($produtos as $p) {
                    $this->produtos_model->updateEstoque($p->produtos_id, $p->quantidade, '-');
                    log_info('ESTOQUE: Produto id ' . $p->produtos_id . ' baixa do estoque. Quantidade: ' . $p->quantidade . '. Motivo: Mudou status que já estava Cancelado para outro');
                }
            }
        }
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir O.S.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        $os = $this->os_model->getByIdCobrancas($id);
        if ($os == null) {
            $os = $this->os_model->getById($id);
            if ($os == null) {
                $this->session->set_flashdata('error', 'Erro ao tentar excluir OS.');
                redirect(base_url() . 'index.php/os/gerenciar/');
            }
        }

        if (isset($os->idCobranca) != null) {
            if ($os->status == "canceled") {
                $this->os_model->delete('cobrancas', 'os_id', $id);
            } else {
                $this->session->set_flashdata('error', 'Existe uma cobrança associada a esta OS, deve cancelar e/ou excluir a cobrança primeiro!');
                redirect(site_url('os/gerenciar/'));
            }
        }

        $osStockRefund = $this->os_model->getById($id);
        //Verifica para poder fazer a devolução do produto para o estoque caso OS seja excluida.
        if (strtolower($osStockRefund->status) != "cancelado") {
            $this->devolucaoEstoque($id);
        }

        $this->os_model->delete('servicos_os', 'os_id', $id);
        $this->os_model->delete('produtos_os', 'os_id', $id);
        $this->os_model->delete('anexos', 'os_id', $id);
        $this->os_model->delete('os', 'idOs', $id);
        if ((int)$os->faturado === 1) {
            $this->os_model->delete('lancamentos', 'descricao', "Fatura de OS - #${id}");
        }

        log_info('Removeu uma OS. ID: ' . $id);
        $this->session->set_flashdata('success', 'OS excluída com sucesso!');
        redirect(site_url('os/gerenciar/'));
    }

    public function autoCompleteProduto()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->os_model->autoCompleteProduto($q);
        }
    }

    public function autoCompleteProdutoSaida()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->os_model->autoCompleteProdutoSaida($q);
        }
    }

    public function autoCompleteCliente()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->os_model->autoCompleteCliente($q);
        }
    }

    public function autoCompleteUsuario()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->os_model->autoCompleteUsuario($q);
        }
    }

    public function autoCompleteTermoGarantia()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->os_model->autoCompleteTermoGarantia($q);
        }
    }

    public function autoCompleteServico()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->os_model->autoCompleteServico($q);
        }
    }

    public function adicionarProduto()
    {
        $this->load->library('form_validation');

        if ($this->form_validation->run('adicionar_produto_os') === false) {
            $errors = validation_errors();

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $preco = $this->input->post('preco');
        $quantidade = $this->input->post('quantidade');
        $subtotal = $preco * $quantidade;
        $produto = $this->input->post('idProduto');
        $data = [
            'quantidade' => $quantidade,
            'subTotal' => $subtotal,
            'produtos_id' => $produto,
            'preco' => $preco,
            'os_id' => $this->input->post('idOsProduto'),
        ];

        $id = $this->input->post('idOsProduto');
        $os = $this->os_model->getById($id);
        if ($os == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar inserir produto na OS.');
            redirect(base_url() . 'index.php/os/gerenciar/');
        }

        if ($this->os_model->add('produtos_os', $data) == true) {
            $this->load->model('produtos_model');

            if ($this->data['configuration']['control_estoque']) {
                $this->produtos_model->updateEstoque($produto, $quantidade, '-');
            }

            $this->db->set('desconto', 0.00);
            $this->db->set('valor_desconto', 0.00);
            $this->db->set('tipo_desconto', null);
            $this->db->where('idOs', $id);
            $this->db->update('os');

            log_info('Adicionou produto a uma OS. ID (OS): ' . $this->input->post('idOsProduto'));

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false]));
        }
    }

    public function excluirProduto()
    {
        $id = $this->input->post('idProduto');
        $idOs = $this->input->post('idOs');

        $os = $this->os_model->getById($idOs);
        if ($os == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir produto na OS.');
            redirect(base_url() . 'index.php/os/gerenciar/');
        }

        if ($this->os_model->delete('produtos_os', 'idProdutos_os', $id) == true) {
            $quantidade = $this->input->post('quantidade');
            $produto = $this->input->post('produto');

            $this->load->model('produtos_model');

            if ($this->data['configuration']['control_estoque']) {
                $this->produtos_model->updateEstoque($produto, $quantidade, '+');
            }

            $this->db->set('desconto', 0.00);
            $this->db->set('valor_desconto', 0.00);
            $this->db->set('tipo_desconto', null);
            $this->db->where('idOs', $idOs);
            $this->db->update('os');

            log_info('Removeu produto de uma OS. ID (OS): ' . $idOs);

            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function adicionarServico()
    {
        $this->load->library('form_validation');

        if ($this->form_validation->run('adicionar_servico_os') === false) {
            $errors = validation_errors();

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $data = [
            'servicos_id' => $this->input->post('idServico'),
            'quantidade' => $this->input->post('quantidade'),
            'preco' => $this->input->post('preco'),
            'os_id' => $this->input->post('idOsServico'),
            'subTotal' => $this->input->post('preco') * $this->input->post('quantidade'),
        ];

        if ($this->os_model->add('servicos_os', $data) == true) {
            log_info('Adicionou serviço a uma OS. ID (OS): ' . $this->input->post('idOsServico'));

            $this->db->set('desconto', 0.00);
            $this->db->set('valor_desconto', 0.00);
            $this->db->set('tipo_desconto', null);
            $this->db->where('idOs', $this->input->post('idOsServico'));
            $this->db->update('os');

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false]));
        }
    }

    public function excluirServico()
    {
        $ID = $this->input->post('idServico');
        $idOs = $this->input->post('idOs');

        if ($this->os_model->delete('servicos_os', 'idServicos_os', $ID) == true) {
            log_info('Removeu serviço de uma OS. ID (OS): ' . $idOs);
            $this->db->set('desconto', 0.00);
            $this->db->set('valor_desconto', 0.00);
            $this->db->set('tipo_desconto', null);
            $this->db->where('idOs', $idOs);
            $this->db->update('os');
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function anexar()
    {
        $this->load->library('upload');
        $this->load->library('image_lib');
        $this->load->helper('media_server_helper');

        // Usar o helper para determinar o diretório e URL
        $config = Media_server_helper::getDiretorioUpload('os', $this->input->post('idOsServico'));
        $directory = $config['directory'];
        $url_base = $config['url_base'];

        // If it exist, check if it's a directory
        if (!is_dir($directory . DIRECTORY_SEPARATOR . 'thumbs')) {
            // make directory for images and thumbs
            try {
                mkdir($directory . DIRECTORY_SEPARATOR . 'thumbs', 0755, true);
            } catch (Exception $e) {
                echo json_encode(['result' => false, 'mensagem' => $e->getMessage()]);
                die();
            }
        }

        $upload_conf = [
            'upload_path' => $directory,
            'allowed_types' => 'jpg|png|gif|jpeg|JPG|PNG|GIF|JPEG|pdf|PDF|cdr|CDR|docx|DOCX|txt', // formatos permitidos para anexos de os
            'max_size' => 0,
        ];

        $this->upload->initialize($upload_conf);

        foreach ($_FILES['userfile'] as $key => $val) {
            $i = 1;
            foreach ($val as $v) {
                $field_name = "file_" . $i;
                $_FILES[$field_name][$key] = $v;
                $i++;
            }
        }
        unset($_FILES['userfile']);

        $error = [];
        $success = [];

        foreach ($_FILES as $field_name => $file) {
            if (!$this->upload->do_upload($field_name)) {
                $error['upload'][] = $this->upload->display_errors();
            } else {
                $upload_data = $this->upload->data();
        
                // Gera um nome de arquivo baseado na data e hora
                $ext = pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                $base_name = date('d-m-Y_H-i-s');
                $new_file_name = $base_name . '.' . $ext;
                $new_file_path = $upload_data['file_path'] . $new_file_name;

                // Se já existir um arquivo com o mesmo nome, adiciona um sufixo incremental
                $contador = 1;
                while (file_exists($new_file_path)) {
                    $sufixo = sprintf('-%02d', $contador);
                    $new_file_name = $base_name . $sufixo . '.' . $ext;
                    $new_file_path = $upload_data['file_path'] . $new_file_name;
                    $contador++;
                }

                rename($upload_data['full_path'], $new_file_path);

                // O campo url deve ser apenas a URL da pasta, e anexo apenas o nome do arquivo
                $thumb_name = $upload_data['is_image'] == 1 ? 'thumb_' . $new_file_name : '';

                if ($upload_data['is_image'] == 1) {
                    $resize_conf = [
                        'source_image' => $new_file_path,
                        'new_image' => $upload_data['file_path'] . 'thumbs' . DIRECTORY_SEPARATOR . 'thumb_' . $new_file_name,
                        'width' => 200,
                        'height' => 125,
                    ];
        
                    $this->image_lib->initialize($resize_conf);
        
                    if (!$this->image_lib->resize()) {
                        $error['resize'][] = $this->image_lib->display_errors();
                    } else {
                        $success[] = $upload_data;
                        $this->load->model('Os_model');
                        $result = $this->Os_model->anexar($this->input->post('idOsServico'), $new_file_name, $url_base, $thumb_name, $directory);
                        if (!$result) {
                            $error['db'][] = 'Erro ao inserir no banco de dados.';
                        }
                    }
                } else {
                    $success[] = $upload_data;
        
                    $this->load->model('Os_model');
        
                    $result = $this->Os_model->anexar($this->input->post('idOsServico'), $new_file_name, $url_base, '', $directory);
                    if (!$result) {
                        $error['db'][] = 'Erro ao inserir no banco de dados.';
                    }
                }
            }
        }
        
        if (count($error) > 0) {
            echo json_encode(['result' => false, 'mensagem' => 'Ocorreu um erro ao processar os arquivos.', 'errors' => $error]);
        } else {
            log_info('Adicionou anexo(s) a uma OS. ID (OS): ' . $this->input->post('idOsServico'));
            echo json_encode(['result' => true, 'mensagem' => 'Arquivo(s) anexado(s) com sucesso.']);
        }
    }

    public function excluirAnexo($id = null)
    {
        if ($id == null || !is_numeric($id)) {
            echo json_encode(['result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.']);
        } else {
            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos', 1)->row();
            $idOs = $this->input->post('idOs');

            unlink($file->path . DIRECTORY_SEPARATOR . $file->anexo);

            if ($file->thumb != null) {
                unlink($file->path . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $file->thumb);
            }

            if ($this->os_model->delete('anexos', 'idAnexos', $id) == true) {
                log_info('Removeu anexo de uma OS. ID (OS): ' . $idOs);
                echo json_encode(['result' => true, 'mensagem' => 'Anexo excluído com sucesso.']);
            } else {
                echo json_encode(['result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.']);
            }
        }
    }

    public function downloadanexo($id = null)
    {
        if ($id != null && is_numeric($id)) {
            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos', 1)->row();

            $this->load->library('zip');
            $path = $file->path;
            $this->zip->read_file($path . '/' . $file->anexo);
            $this->zip->download('file' . date('d-m-Y-H.i.s') . '.zip');
        }
    }

    public function adicionarDesconto()
    {
        if ($this->input->post('desconto') == "") {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['messages' => 'Campo desconto vazio']));
        } else {
            $idOs = $this->input->post('idOs');
            $data = [
                'tipo_desconto' => $this->input->post('tipoDesconto'),
                'desconto' => $this->input->post('desconto'),
                'valor_desconto' => $this->input->post('resultado')
            ];
            $editavel = $this->os_model->isEditable($idOs);
            if (!$editavel) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false, 'messages' => 'Desconto não pode ser adicionado. OS já Faturada/Cancelada']));
            }
            if ($this->os_model->edit('os', $data, 'idOs', $idOs) == true) {
                log_info('Adicionou um desconto na OS. ID: ' . $idOs);
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(201)
                    ->set_output(json_encode(['result' => true, 'messages' => 'Desconto adicionado com sucesso!']));
            } else {
                log_info('Ocorreu um erro ao tentar adicionar desconto a OS: ' . $idOs);
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false, 'messages' => 'Ocorreu um erro ao tentar adicionar desconto a OS.']));
            }
        }
    }

    public function faturar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('receita') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $vencimento = $this->input->post('vencimento');
            $recebimento = $this->input->post('recebimento');

            try {
                $vencimento = explode('/', $vencimento);
                $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];

                if ($recebimento != null) {
                    $recebimento = explode('/', $recebimento);
                    $recebimento = $recebimento[2] . '-' . $recebimento[1] . '-' . $recebimento[0];
                }
            } catch (Exception $e) {
                $vencimento = date('Y/m/d');
            }

            // Buscar os dados da OS antes de qualquer alteração
            $os_id = $this->input->post('os_id');
            $os = $this->os_model->getById($os_id);
            
            $data = [
                'descricao' => set_value('descricao'),
                'valor' => getAmount($this->input->post('valor')),
                'tipo_desconto' => ($this->input->post('tipoDesconto')),
                'desconto' => $os->desconto,
                'valor_desconto' => $os->valor_desconto,
                'clientes_id' => $this->input->post('clientes_id'),
                'data_vencimento' => $vencimento,
                'data_pagamento' => $recebimento,
                'baixado' => $this->input->post('recebido') ?: 0,
                'cliente_fornecedor' => set_value('cliente'),
                'forma_pgto' => $this->input->post('formaPgto'),
                'tipo' => $this->input->post('tipo'),
                'observacoes' => set_value('observacoes'),
                'usuarios_id' => $os->usuarios_id,
                'os_id' => $os_id
            ];

            $editavel = $this->os_model->isEditable($this->input->post('idOs'));
            if (!$editavel) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false]));
            }

            if ($this->os_model->add('lancamentos', $data) == true) {
                $this->db->set('faturado', 1);
                $this->db->set('valorTotal', $this->input->post('valor'));
                $this->db->set('status', 'Faturado');
                $this->db->where('idOs', $os_id);
                $this->db->update('os');

                // Remover a OS do compartimento
                if ($os->compartimento_id) {
                    $this->load->model('compartimentos_model');
                    $this->compartimentos_model->atualizarOcupacao($os->compartimento_id, 'os', $os_id, 'remover');
                    
                    // Limpar o compartimento_id na tabela os
                    $this->db->set('compartimento_id', null);
                    $this->db->where('idOs', $os_id);
                    $this->db->update('os');
                }

                log_info('Faturou uma OS. ID: ' . $os_id);

                $this->session->set_flashdata('success', 'OS faturada com sucesso!');
                $json = ['result' => true];
                echo json_encode($json);
                die();
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar faturar OS.');
                $json = ['result' => false];
                echo json_encode($json);
                die();
            }
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar faturar OS.');
        $json = ['result' => false];
        echo json_encode($json);
    }

    private function enviarOsPorEmail($idOs, $remetentes, $assunto)
    {
        $dados = [];

        $this->load->model('mapos_model');
        $dados['result'] = $this->os_model->getById($idOs);
        if (!isset($dados['result']->email)) {
            return false;
        }

        $dados['produtos'] = $this->os_model->getProdutos($idOs);
        $dados['servicos'] = $this->os_model->getServicos($idOs);
        $dados['emitente'] = $this->mapos_model->getEmitente();
        $emitente = $dados['emitente'];
        if (!isset($emitente->email)) {
            return false;
        }

        $html = $this->load->view('os/emails/os', $dados, true);

        $this->load->model('email_model');

        $remetentes = array_unique($remetentes);
        foreach ($remetentes as $remetente) {
            $headers = ['From' => $emitente->email, 'Subject' => $assunto, 'Return-Path' => ''];
            $email = [
                'to' => $remetente,
                'message' => $html,
                'status' => 'pending',
                'date' => date('Y-m-d H:i:s'),
                'headers' => serialize($headers),
            ];
            $this->email_model->add('email_queue', $email);
        }

        return true;
    }

    public function adicionarAnotacao()
    {
        $this->load->library('form_validation');
        if ($this->form_validation->run('anotacoes_os') == false) {
            echo json_encode(validation_errors());
        } else {
            $data = [
                'anotacao' => $this->input->post('anotacao'),
                'data_hora' => date('Y-m-d H:i:s'),
                'os_id' => $this->input->post('os_id'),
            ];

            if ($this->os_model->add('anotacoes_os', $data) == true) {
                log_info('Adicionou anotação a uma OS. ID (OS): ' . $this->input->post('os_id'));
                echo json_encode(['result' => true]);
            } else {
                echo json_encode(['result' => false]);
            }
        }
    }

    public function excluirAnotacao()
    {
        $id = $this->input->post('idAnotacao');
        $idOs = $this->input->post('idOs');

        if ($this->os_model->delete('anotacoes_os', 'idAnotacoes', $id) == true) {
            log_info('Removeu anotação de uma OS. ID (OS): ' . $idOs);
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function removerUsuarioAdicional()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar OS.');
            redirect(base_url());
        }

        $os_id = $this->input->post('os_id');
        $usuario_id = $this->input->post('usuario_id');

        if ($this->os_model->removerUsuarioAdicional($os_id, $usuario_id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao remover usuário']);
        }
    }

    public function buscarCompartimentos() {
        $this->load->model('compartimentos_model');
        $organizador_id = $this->input->post('organizador_id') ?: $this->input->get('organizador_id');
        
        if ($organizador_id) {
            $compartimentos = $this->compartimentos_model->getCompartimentosComOcupacao($organizador_id);
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($compartimentos));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([]));
        }
    }

    public function fixarUsuario()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar O.S.');
            redirect(base_url());
        }

        $usuario_id = $this->input->post('usuario_id');
        $usuario_fixador_id = $this->session->userdata('id_admin');

        $this->load->model('usuarios_fixados_model');
        
        if ($this->usuarios_fixados_model->fixarUsuario($usuario_id, $usuario_fixador_id)) {
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function desfixarUsuario()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar O.S.');
            redirect(base_url());
        }

        $usuario_id = $this->input->post('usuario_id');
        $usuario_fixador_id = $this->session->userdata('id_admin');

        $this->load->model('usuarios_fixados_model');
        
        if ($this->usuarios_fixados_model->desfixarUsuario($usuario_id, $usuario_fixador_id)) {
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function getUsuariosFixados()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $usuario_fixador_id = $this->session->userdata('id_admin');

        $this->load->model('usuarios_fixados_model');
        $usuarios_fixados = $this->usuarios_fixados_model->getByUsuarioId($usuario_fixador_id);
        
        echo json_encode(['result' => true, 'usuarios' => $usuarios_fixados]);
    }

    private function normalizar_valor($valor) {
        // Se o valor já contém vírgula, assume formato brasileiro (1.234,56)
        if (strpos($valor, ',') !== false) {
            return str_replace(',', '.', str_replace('.', '', $valor));
        }
        // Se não contém vírgula, assume formato americano (1234.56)
        return $valor;
    }

    public function adicionarAver()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Log para debug
        log_message('debug', 'Iniciando adicionarAver');
        log_message('debug', 'POST data: ' . print_r($_POST, true));

        if ($this->form_validation->run('aver') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
            log_message('debug', 'Validação falhou: ' . validation_errors());
        } else {
            $data = array(
                'os_id' => $this->input->post('os_id'),
                'valor' => $this->normalizar_valor($this->input->post('valor')),
                'data_pagamento' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('data_pagamento')))),
                'status' => $this->input->post('status'),
                'usuarios_id' => $this->session->userdata('id_admin'),
                'data_criacao' => date('Y-m-d H:i:s')
            );

            log_message('debug', 'Dados preparados para inserção: ' . print_r($data, true));

            if ($this->os_model->add('aver_os', $data) == true) {
                log_message('debug', 'Aver adicionado com sucesso');
                $json = array('result' => true);
                echo json_encode($json);
                return;
            } else {
                log_message('error', 'Erro ao adicionar aver');
                $json = array('result' => false, 'mensagem' => 'Erro ao adicionar aver.');
                echo json_encode($json);
                return;
            }
        }

        $json = array('result' => false, 'mensagem' => $this->data['custom_error']);
        echo json_encode($json);
    }

    public function editarAver()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Log para debug
        log_message('debug', 'Iniciando editarAver');
        log_message('debug', 'POST data: ' . print_r($_POST, true));

        if ($this->form_validation->run('aver') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
            log_message('debug', 'Validação falhou: ' . validation_errors());
        } else {
            $data = array(
                'valor' => $this->normalizar_valor($this->input->post('valor')),
                'data_pagamento' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('data_pagamento')))),
                'status' => $this->input->post('status'),
                'usuarios_id' => $this->session->userdata('id_admin')
            );

            log_message('debug', 'Dados preparados para atualização: ' . print_r($data, true));
            log_message('debug', 'ID do aver a ser editado: ' . $this->input->post('id_aver'));

            if ($this->os_model->edit('aver_os', $data, 'idAver', $this->input->post('id_aver')) == true) {
                log_message('debug', 'Aver editado com sucesso');
                $json = array('result' => true);
                echo json_encode($json);
                return;
            } else {
                log_message('error', 'Erro ao editar aver');
                $json = array('result' => false, 'mensagem' => 'Erro ao editar aver.');
                echo json_encode($json);
                return;
            }
        }

        $json = array('result' => false, 'mensagem' => $this->data['custom_error']);
        echo json_encode($json);
    }

    public function getAvers($os_id)
    {
        try {
            $this->load->model('os_model');
            $data['avers'] = $this->os_model->getAvers($os_id);
            
            // Log para debug
            log_message('debug', 'Controller getAvers - OS ID: ' . $os_id);
            log_message('debug', 'Controller getAvers - Dados: ' . print_r($data['avers'], true));
            
            $this->load->view('os/tabela_avers', $data);
        } catch (Exception $e) {
            log_message('error', 'Erro no controller getAvers: ' . $e->getMessage());
            echo '<div class="alert alert-danger">Erro ao carregar avers. Por favor, tente novamente.</div>';
        }
    }

    public function excluirAver()
    {
        $this->load->model('os_model');
        $idAver = $this->input->post('idAver');
        $os_id = $this->input->post('os_id');

        if ($this->os_model->excluirAver($idAver)) {
            $response = array('result' => true, 'message' => 'Aver excluído com sucesso!');
        } else {
            $response = array('result' => false, 'message' => 'Erro ao excluir aver.');
        }

        echo json_encode($response);
    }
}
