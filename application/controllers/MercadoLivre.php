<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MercadoLivre extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MercadoLivre_model');
        $this->load->library('session');
        
        // Carregar configurações básicas
        $this->load->database();
        $configuracoes = $this->db->get('configuracoes')->result();
        $this->data = [
            'configuration' => [
                'per_page' => 10,
                'next_link' => 'Próxima',
                'prev_link' => 'Anterior',
                'full_tag_open' => '<div class="pagination alternate"><ul>',
                'full_tag_close' => '</ul></div>',
                'num_tag_open' => '<li>',
                'num_tag_close' => '</li>',
                'cur_tag_open' => '<li><a style="color: #2D335B"><b>',
                'cur_tag_close' => '</b></a></li>',
                'prev_tag_open' => '<li>',
                'prev_tag_close' => '</li>',
                'next_tag_open' => '<li>',
                'next_tag_close' => '</li>',
                'first_link' => 'Primeira',
                'last_link' => 'Última',
                'first_tag_open' => '<li>',
                'first_tag_close' => '</li>',
                'last_tag_open' => '<li>',
                'last_tag_close' => '</li>',
                'app_name' => 'Map-OS',
                'app_theme' => 'default',
                'os_notification' => 'cliente',
                'control_estoque' => '1',
                'notifica_whats' => '',
                'control_baixa' => '0',
                'control_editos' => '1',
                'control_datatable' => '1',
                'pix_key' => '',
            ],
        ];
        
        foreach ($configuracoes as $c) {
            $this->data['configuration'][$c->config] = $c->valor;
        }
    }

    /**
     * Página principal de configurações
     */
    public function index()
    {
        $this->data['config'] = $this->MercadoLivre_model->getConfiguracao();
        $this->data['produtos_integrados'] = $this->MercadoLivre_model->getProdutosIntegrados();
        $this->data['logs'] = $this->MercadoLivre_model->getLogs(20);
        
        $this->data['view'] = 'mercadolivre/configuracoes';
        return $this->layout();
    }

    /**
     * Salvar configurações
     */
    public function salvarConfiguracao()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar configurações.');
            redirect(base_url());
        }

        $data = [
            'access_token' => $this->input->post('access_token'),
            'refresh_token' => $this->input->post('refresh_token'),
            'user_id' => $this->input->post('user_id'),
            'nickname' => $this->input->post('nickname'),
            'site_id' => $this->input->post('site_id') ?: 'MLB',
            'ativo' => $this->input->post('ativo') ? 1 : 0
        ];

        if ($this->MercadoLivre_model->salvarConfiguracao($data)) {
            $this->session->set_flashdata('success', 'Configurações salvas com sucesso!');
            log_info('Configurações do Mercado Livre atualizadas.');
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar configurações.');
        }

        redirect(site_url('mercadolivre'));
    }
    
    public function layout()
    {
        // load views
        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu');
        $this->load->view('tema/conteudo');
        $this->load->view('tema/rodape');
    }

    /**
     * Iniciar processo de autenticação OAuth2
     */
    public function autenticar()
    {
        // Verificar permissão apenas se o usuário estiver logado
        if ($this->session->userdata('logado')) {
            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) {
                $this->session->set_flashdata('error', 'Você não tem permissão para acessar a integração com Mercado Livre.');
                redirect(site_url('mapos/configurar'));
            }
        }
        
        $client_id = $_ENV['MERCADO_LIVRE_CLIENT_ID'] ?? '';
        $redirect_uri = $_ENV['MERCADO_LIVRE_REDIRECT_URI'] ?? site_url('mercadolivre/callback');
        
        if (!$client_id) {
            $this->session->set_flashdata('error', 'CLIENT_ID do Mercado Livre não configurado.');
            redirect(site_url('mapos/configurar'));
        }
        
        // URL de autorização do Mercado Livre
        $auth_url = "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id={$client_id}&redirect_uri=" . urlencode($redirect_uri);
        
        redirect($auth_url);
    }

    /**
     * Callback da autenticação OAuth2
     */
    public function callback()
    {
        $code = $this->input->get('code');
        
        if (!$code) {
            $this->session->set_flashdata('error', 'Código de autorização não recebido.');
            redirect(site_url('mapos/configurar'));
        }

        // Trocar código por token
        $token_data = $this->trocarCodigoPorToken($code);
        
        $nickname = '';
        if ($token_data && isset($token_data['user_id']) && isset($token_data['access_token'])) {
            $user_id = $token_data['user_id'];
            $access_token = $token_data['access_token'];
            $user_url = "https://api.mercadolibre.com/users/{$user_id}?access_token={$access_token}";
            $user_response = @file_get_contents($user_url);
            if ($user_response !== false) {
                $user_data = json_decode($user_response, true);
                if (isset($user_data['nickname'])) {
                    $nickname = $user_data['nickname'];
                }
            }
        }
        
        if ($token_data) {
            // Salvar no .env diretamente
            $env_file_path = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . '.env';
            $env_file = file_get_contents($env_file_path);
            
            $dataDotEnv = [
                'MERCADO_LIVRE_ACCESS_TOKEN' => $token_data['access_token'],
                'MERCADO_LIVRE_REFRESH_TOKEN' => $token_data['refresh_token'],
                'MERCADO_LIVRE_USER_ID' => $token_data['user_id'],
                'MERCADO_LIVRE_NICKNAME' => '"' . $nickname . '"',
                'MERCADO_LIVRE_TOKEN_EXPIRES_AT' => '"' . date('Y-m-d H:i:s', time() + $token_data['expires_in']) . '"'
            ];
            
            foreach ($dataDotEnv as $constante => $valor) {
                if (isset($_ENV[$constante])) {
                    $env_file = str_replace("$constante={$_ENV[$constante]}", "$constante={$valor}", $env_file);
                } else {
                    $env_file .= "\n{$constante}={$valor}\n";
                }
            }
            
            file_put_contents($env_file_path, $env_file);
            
            $this->session->set_flashdata('success', 'Autenticação com Mercado Livre realizada com sucesso!');
            log_info('Autenticação ML realizada para usuário: ' . $nickname);
        } else {
            $this->session->set_flashdata('error', 'Erro na autenticação com Mercado Livre.');
        }

        redirect(site_url('mapos/configurar'));
    }

    /**
     * Trocar código de autorização por token
     */
    private function trocarCodigoPorToken($code)
    {
        $client_id = $_ENV['MERCADO_LIVRE_CLIENT_ID'] ?? '';
        $client_secret = $_ENV['MERCADO_LIVRE_CLIENT_SECRET'] ?? '';
        $redirect_uri = $_ENV['MERCADO_LIVRE_REDIRECT_URI'] ?? site_url('mercadolivre/callback');

        $url = 'https://api.mercadolibre.com/oauth/token';
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code' => $code,
            'redirect_uri' => $redirect_uri
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Log para depuração
        $log_path = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'ml_debug.txt';
        file_put_contents($log_path, date('Y-m-d H:i:s') . "\n" . $response . "\n\n", FILE_APPEND);

        if ($http_code == 200) {
            return json_decode($response, true);
        }

        return false;
    }

    /**
     * Renovar token de acesso
     */
    public function renovarToken()
    {
        $config = $this->MercadoLivre_model->getConfiguracao();
        
        if (!$config || !$config->refresh_token) {
            return false;
        }

        $client_id = $_ENV['MERCADO_LIVRE_CLIENT_ID'] ?? '';
        $client_secret = $_ENV['MERCADO_LIVRE_CLIENT_SECRET'] ?? '';

        $url = 'https://api.mercadolibre.com/oauth/token';
        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $config->refresh_token
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $token_data = json_decode($response, true);
            
            $config_update = [
                'access_token' => $token_data['access_token'],
                'refresh_token' => $token_data['refresh_token'],
                'token_expires_at' => date('Y-m-d H:i:s', time() + $token_data['expires_in'])
            ];

            return $this->MercadoLivre_model->salvarConfiguracao($config_update);
        }

        return false;
    }

    /**
     * Sincronizar produtos pendentes
     */
    public function sincronizarProdutos()
    {
        log_info('ML: Iniciando sincronização de produtos');
        
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            log_info('ML: Sem permissão para sincronizar produtos');
            echo json_encode(['success' => false, 'message' => 'Sem permissão']);
            return;
        }

        log_info('ML: Permissão verificada, buscando produtos pendentes');
        $produtos_pendentes = $this->MercadoLivre_model->getProdutosPendentes();
        $sucessos = 0;
        $erros = 0;

        log_info('ML: Produtos pendentes encontrados: ' . count($produtos_pendentes));

        if (empty($produtos_pendentes)) {
            log_info('ML: Nenhum produto pendente encontrado');
            echo json_encode([
                'success' => true,
                'message' => 'Nenhum produto pendente de sincronização'
            ]);
            return;
        }

        foreach ($produtos_pendentes as $produto) {
            log_info('ML: ==========================================');
            log_info('ML: INICIANDO PROCESSAMENTO - Produto ID: ' . $produto->produto_id);
            log_info('ML: Nome: ' . $produto->descricao);
            log_info('ML: Categoria ML: ' . ($produto->ml_categoria ?: 'VAZIO'));
            log_info('ML: Preço: ' . ($produto->precoVenda ?: 'VAZIO'));
            log_info('ML: Estoque: ' . ($produto->estoque ?: '0'));
            log_info('ML: Descrição ML: ' . ($produto->ml_descricao ?: 'VAZIO'));
            log_info('ML: Condição: ' . ($produto->ml_condicao ?: 'new'));
            log_info('ML: Envios: ' . ($produto->ml_envios ? 'SIM' : 'NÃO'));
            log_info('ML: Garantia: ' . ($produto->ml_garantia ?: '0') . ' dias');
            log_info('ML: ==========================================');
            
            $resultado = $this->publicarProduto($produto);
            
            log_info('ML: Resultado da publicação - Sucesso: ' . ($resultado['success'] ? 'SIM' : 'NÃO') . ', Erro: ' . ($resultado['error'] ?? 'N/A'));
            
            if ($resultado['success']) {
                $sucessos++;
                $this->MercadoLivre_model->atualizarStatus(
                    $produto->produto_id, 
                    'active', 
                    $resultado['ml_id'], 
                    $resultado['permalink']
                );
                log_info('ML: Produto ID ' . $produto->produto_id . ' sincronizado com sucesso. ML ID: ' . $resultado['ml_id']);
            } else {
                $erros++;
                $this->MercadoLivre_model->atualizarSincronizacao($produto->produto_id, $resultado['error']);
                log_info('ML: Produto ID ' . $produto->produto_id . ' falhou na sincronização. Erro: ' . $resultado['error']);
            }

            $this->MercadoLivre_model->salvarLog(
                $produto->produto_id,
                'sync',
                $resultado['success'] ? 'success' : 'error',
                $resultado['success'] ? 'Produto sincronizado com sucesso' : $resultado['error']
            );
        }

        $message = "Sincronização concluída: $sucessos sucessos, $erros erros";
        log_info('ML: ' . $message);
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Publicar produto no ML
     */
    private function publicarProduto($produto)
    {
        $config = $this->MercadoLivre_model->getConfiguracao();
        
        if (!$config || !$config->ativo) {
            log_info('ML: Integração não configurada ou inativa');
            return ['success' => false, 'error' => 'Integração não configurada'];
        }

        // Verificar se token expirou
        if ($config->token_expires_at && strtotime($config->token_expires_at) < time()) {
            if (!$this->renovarToken()) {
                log_info('ML: Token expirado e não foi possível renovar');
                return ['success' => false, 'error' => 'Token expirado e não foi possível renovar'];
            }
            $config = $this->MercadoLivre_model->getConfiguracao();
        }

        // Preparar dados do produto
        $dados_produto = $this->prepararDadosProduto($produto);
        
        if ($dados_produto === false) {
            log_info('ML: FALHA CRÍTICA - Erro ao preparar dados do produto - ID: ' . $produto->produto_id);
            log_info('ML: O produto não será enviado para o ML devido a dados incompletos');
            return ['success' => false, 'error' => 'Dados do produto incompletos'];
        }
        
        // Log dos dados que serão enviados
        log_info('ML: Dados do produto a serem enviados: ' . json_encode($dados_produto));
        
        // Fazer requisição para API do ML
        $url = "https://api.mercadolibre.com/items";
        $headers = [
            'Authorization: Bearer ' . $config->access_token,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados_produto));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Log da resposta
        log_info('ML: HTTP Code: ' . $http_code);
        log_info('ML: Response: ' . $response);
        if ($curl_error) {
            log_info('ML: CURL Error: ' . $curl_error);
        }

        if ($http_code == 201) {
            $resultado = json_decode($response, true);
            log_info('ML: Produto publicado com sucesso. ID: ' . $resultado['id']);
            return [
                'success' => true,
                'ml_id' => $resultado['id'],
                'permalink' => $resultado['permalink']
            ];
        } else {
            $erro = json_decode($response, true);
            $error_message = isset($erro['message']) ? $erro['message'] : 'Erro desconhecido';
            log_info('ML: Erro ao publicar produto. HTTP: ' . $http_code . ', Erro: ' . $error_message);
            return [
                'success' => false,
                'error' => $error_message
            ];
        }
    }

    /**
     * Preparar dados do produto para o ML
     */
    private function prepararDadosProduto($produto)
    {
        log_info('ML: Validando campos do produto ID: ' . $produto->produto_id);
        
        // Validar campos obrigatórios
        if (empty($produto->descricao)) {
            log_info('ML: ERRO - Produto ID ' . $produto->produto_id . ' sem descrição');
            return false;
        }
        
        if (empty($produto->ml_categoria)) {
            log_info('ML: ERRO - Produto ID ' . $produto->produto_id . ' sem categoria ML (ml_categoria está vazio)');
            return false;
        }
        
        if (empty($produto->precoVenda) || $produto->precoVenda <= 0) {
            log_info('ML: ERRO - Produto ID ' . $produto->produto_id . ' sem preço válido (precoVenda: ' . $produto->precoVenda . ')');
            return false;
        }
        
        if (empty($produto->ml_descricao)) {
            log_info('ML: ERRO - Produto ID ' . $produto->produto_id . ' sem descrição ML (ml_descricao está vazio)');
            return false;
        }

        log_info('ML: Todos os campos obrigatórios validados com sucesso para produto ID: ' . $produto->produto_id);

        $dados = [
            'title' => $produto->descricao,
            'category_id' => $produto->ml_categoria,
            'price' => (float)$produto->precoVenda,
            'currency_id' => 'BRL',
            'available_quantity' => (int)$produto->estoque,
            'buying_mode' => 'buy_it_now',
            'condition' => $produto->ml_condicao ?: 'new',
            'description' => [
                'plain_text' => $produto->ml_descricao ?: $produto->descricao
            ]
        ];

        // Adicionar imagens se existirem
        $pictures = $this->getImagensProduto($produto->produto_id);
        if (!empty($pictures)) {
            $dados['pictures'] = $pictures;
            log_info('ML: Adicionadas ' . count($pictures) . ' imagens para produto ID: ' . $produto->produto_id);
        } else {
            log_info('ML: Nenhuma imagem encontrada para produto ID: ' . $produto->produto_id);
        }

        // Adicionar atributos se existirem
        $attributes = $this->getAtributosProduto($produto);
        if (!empty($attributes)) {
            $dados['attributes'] = $attributes;
            log_info('ML: Adicionados ' . count($attributes) . ' atributos para produto ID: ' . $produto->produto_id);
            
            // Log detalhado dos atributos para debug
            $atributos_ids = [];
            foreach ($attributes as $attr) {
                $atributos_ids[] = $attr['id'];
            }
            log_info('ML: IDs dos atributos enviados: ' . implode(', ', $atributos_ids));
        } else {
            log_info('ML: Nenhum atributo encontrado para produto ID: ' . $produto->produto_id);
        }

        // Adicionar configurações de envio
        $dados['shipping'] = [
            'mode' => $produto->ml_envios ? 'me2' : 'not_specified'
        ];

        // Adicionar garantia se especificada
        if (!empty($produto->ml_garantia) && $produto->ml_garantia > 0) {
            $dados['warranty'] = $produto->ml_garantia . ' days';
            log_info('ML: Garantia de ' . $produto->ml_garantia . ' dias adicionada para produto ID: ' . $produto->produto_id);
        }

        log_info('ML: Dados preparados com sucesso para produto ID: ' . $produto->produto_id . ' - Categoria: ' . $produto->ml_categoria . ', Preço: ' . $produto->precoVenda);
        return $dados;
    }

    /**
     * Buscar imagens do produto
     */
    private function getImagensProduto($produto_id)
    {
        $this->db->where('produto_id', $produto_id);
        $imagens = $this->db->get('imagens_produto')->result();
        
        $pictures = [];
        foreach ($imagens as $img) {
            $pictures[] = [
                'source' => base_url($img->urlImagem . '/' . $img->anexo)
            ];
        }
        
        return $pictures;
    }

    /**
     * Buscar atributos do produto
     */
    private function getAtributosProduto($produto)
    {
        $atributos = [];
        
        // Primeiro, tentar buscar atributos salvos no banco
        if (!empty($produto->ml_atributos)) {
            $atributos_salvos = json_decode($produto->ml_atributos, true);
            if (is_array($atributos_salvos)) {
                foreach ($atributos_salvos as $atributo) {
                    // Buscar informações do atributo na tabela atributos_ml
                    $atributo_info = $this->db->where('ml_attribute_id', $atributo['ml_attribute_id'])
                                             ->where('categoria_id', $produto->categoria_id)
                                             ->get('atributos_ml')
                                             ->row();
                    
                    if ($atributo_info) {
                        $atributos[] = [
                            'id' => $atributo_info->ml_attribute_id,
                            'name' => $atributo_info->name,
                            'value_name' => $atributo['value']
                        ];
                    }
                }
                
                if (!empty($atributos)) {
                    log_info('ML: Usando ' . count($atributos) . ' atributos salvos do banco para produto ID: ' . $produto->produto_id);
                    return $atributos;
                }
            }
        }
        
        // Se não tem atributos salvos, tentar buscar da API do Mercado Livre
        if (!empty($produto->ml_categoria)) {
            log_info('ML: Tentando buscar atributos da API para categoria: ' . $produto->ml_categoria);
            
            // Buscar atributos da API
            $atributosAPI = $this->buscarAtributosAPI($produto->ml_categoria);
            if (!empty($atributosAPI)) {
                log_info('ML: Encontrados ' . count($atributosAPI) . ' atributos da API');
                return $atributosAPI;
            }
        }
        
        // Se não houver atributos salvos, usar atributos padrão para celulares
        if ($produto->ml_categoria == 'MLB5726' || $produto->ml_categoria == 'MLB1055') {
            // Condição do produto (obrigatório para celulares)
            $atributos[] = [
                'id' => 'ITEM_CONDITION',
                'name' => 'Condição',
                'value_name' => $produto->ml_condicao ?: 'new'
            ];
            
            // Modelo (obrigatório para celulares)
            if ($produto->modeloProduto) {
                $atributos[] = [
                    'id' => 'MODEL',
                    'name' => 'Modelo',
                    'value_name' => $produto->modeloProduto
                ];
            } else {
                // Se não tiver modelo, usar um padrão
                $atributos[] = [
                    'id' => 'MODEL',
                    'name' => 'Modelo',
                    'value_name' => 'Smartphone'
                ];
            }
            
            // Marca (obrigatório para celulares)
            if ($produto->marcaProduto) {
                $atributos[] = [
                    'id' => 'BRAND',
                    'name' => 'Marca',
                    'value_name' => $produto->marcaProduto
                ];
            } else {
                // Se não tiver marca, usar um padrão
                $atributos[] = [
                    'id' => 'BRAND',
                    'name' => 'Marca',
                    'value_name' => 'Genérica'
                ];
            }
            
            // Linha (obrigatório para celulares)
            $atributos[] = [
                'id' => 'LINE',
                'name' => 'Linha',
                'value_name' => 'Smartphone'
            ];
            
            // Capacidade de armazenamento (obrigatório para celulares)
            $atributos[] = [
                'id' => 'STORAGE_CAPACITY',
                'name' => 'Capacidade de armazenamento',
                'value_name' => '64 GB'
            ];
            
            // Sistema operacional
            $atributos[] = [
                'id' => 'OPERATING_SYSTEM',
                'name' => 'Sistema operacional',
                'value_name' => 'Android'
            ];
            
            // Tipo de tela
            $atributos[] = [
                'id' => 'SCREEN_TYPE',
                'name' => 'Tipo de tela',
                'value_name' => 'LCD'
            ];
            
            // Tamanho da tela (estimado para A12)
            $atributos[] = [
                'id' => 'SCREEN_SIZE',
                'name' => 'Tamanho da tela',
                'value_name' => '6.5 polegadas'
            ];
            
            // Resolução da câmera (estimada)
            $atributos[] = [
                'id' => 'CAMERA_RESOLUTION',
                'name' => 'Resolução da câmera',
                'value_name' => '48 MP'
            ];
            
            // Tipo de chip
            $atributos[] = [
                'id' => 'CHIP_TYPE',
                'name' => 'Tipo de chip',
                'value_name' => 'MediaTek'
            ];
            
            // Cor (estimada baseada no modelo)
            $atributos[] = [
                'id' => 'COLOR',
                'name' => 'Cor',
                'value_name' => 'Preto'
            ];
            
            // Tipo de bateria
            $atributos[] = [
                'id' => 'BATTERY_TYPE',
                'name' => 'Tipo de bateria',
                'value_name' => 'Li-Ion'
            ];
            
            // Capacidade da bateria (estimada para A12)
            $atributos[] = [
                'id' => 'BATTERY_CAPACITY',
                'name' => 'Capacidade da bateria',
                'value_name' => '5000 mAh'
            ];
            
            // Tipo de carregamento
            $atributos[] = [
                'id' => 'CHARGING_TYPE',
                'name' => 'Tipo de carregamento',
                'value_name' => 'USB Type-C'
            ];
            
            // Tipo de tela (obrigatório para celulares)
            $atributos[] = [
                'id' => 'SCREEN_TYPE',
                'name' => 'Tipo de tela',
                'value_name' => 'LCD'
            ];
            
            // Resolução da câmera (obrigatório para celulares)
            $atributos[] = [
                'id' => 'CAMERA_RESOLUTION',
                'name' => 'Resolução da câmera',
                'value_name' => '48 MP'
            ];
            
            // Tipo de chip (obrigatório para celulares)
            $atributos[] = [
                'id' => 'CHIP_TYPE',
                'name' => 'Tipo de chip',
                'value_name' => 'MediaTek'
            ];
            
            // Tipo de bateria (obrigatório para celulares)
            $atributos[] = [
                'id' => 'BATTERY_TYPE',
                'name' => 'Tipo de bateria',
                'value_name' => 'Li-Ion'
            ];
            
            // Resistência à água (obrigatório para celulares)
            $atributos[] = [
                'id' => 'WATER_RESISTANCE',
                'name' => 'Resistência à água',
                'value_name' => 'Não'
            ];
            
            // Biometria (obrigatório para celulares)
            $atributos[] = [
                'id' => 'BIOMETRY',
                'name' => 'Biometria',
                'value_name' => 'Impressão digital'
            ];
            
            // Tipo de display (obrigatório para celulares)
            $atributos[] = [
                'id' => 'DISPLAY_TYPE',
                'name' => 'Tipo de display',
                'value_name' => 'TFT'
            ];
            
            // Resolução da tela (obrigatório para celulares)
            $atributos[] = [
                'id' => 'DISPLAY_RESOLUTION',
                'name' => 'Resolução da tela',
                'value_name' => 'HD+ (1600 x 720)'
            ];
            
            // Número de câmeras (obrigatório para celulares)
            $atributos[] = [
                'id' => 'CAMERA_COUNT',
                'name' => 'Número de câmeras',
                'value_name' => '4'
            ];
            
            // Tipo de câmera frontal (obrigatório para celulares)
            $atributos[] = [
                'id' => 'FRONT_CAMERA_TYPE',
                'name' => 'Tipo de câmera frontal',
                'value_name' => 'Única'
            ];
            
            // Resolução da câmera frontal (obrigatório para celulares)
            $atributos[] = [
                'id' => 'FRONT_CAMERA_RESOLUTION',
                'name' => 'Resolução da câmera frontal',
                'value_name' => '8 MP'
            ];
            
            // Tipo de câmera traseira (obrigatório para celulares)
            $atributos[] = [
                'id' => 'REAR_CAMERA_TYPE',
                'name' => 'Tipo de câmera traseira',
                'value_name' => 'Múltipla'
            ];
            
            // Resolução da câmera traseira (obrigatório para celulares)
            $atributos[] = [
                'id' => 'REAR_CAMERA_RESOLUTION',
                'name' => 'Resolução da câmera traseira',
                'value_name' => '48 MP'
            ];
            
            // Tipo de flash (obrigatório para celulares)
            $atributos[] = [
                'id' => 'FLASH_TYPE',
                'name' => 'Tipo de flash',
                'value_name' => 'LED'
            ];
            
            // Tipo de estabilização (obrigatório para celulares)
            $atributos[] = [
                'id' => 'STABILIZATION_TYPE',
                'name' => 'Tipo de estabilização',
                'value_name' => 'Digital'
            ];
            
            // Tipo de autofoco (obrigatório para celulares)
            $atributos[] = [
                'id' => 'AUTOFOCUS_TYPE',
                'name' => 'Tipo de autofoco',
                'value_name' => 'Automático'
            ];
            
            // Tipo de zoom (obrigatório para celulares)
            $atributos[] = [
                'id' => 'ZOOM_TYPE',
                'name' => 'Tipo de zoom',
                'value_name' => 'Digital'
            ];
            
            // Tipo de gravação de vídeo (obrigatório para celulares)
            $atributos[] = [
                'id' => 'VIDEO_RECORDING_TYPE',
                'name' => 'Tipo de gravação de vídeo',
                'value_name' => 'Full HD'
            ];
            
            // Tipo de áudio (obrigatório para celulares)
            $atributos[] = [
                'id' => 'AUDIO_TYPE',
                'name' => 'Tipo de áudio',
                'value_name' => 'Estéreo'
            ];
            
            // Tipo de alto-falante (obrigatório para celulares)
            $atributos[] = [
                'id' => 'SPEAKER_TYPE',
                'name' => 'Tipo de alto-falante',
                'value_name' => 'Único'
            ];
            
            // Tipo de microfone (obrigatório para celulares)
            $atributos[] = [
                'id' => 'MICROPHONE_TYPE',
                'name' => 'Tipo de microfone',
                'value_name' => 'Múltiplo'
            ];
            
            // Tipo de conector de áudio (obrigatório para celulares)
            $atributos[] = [
                'id' => 'AUDIO_CONNECTOR_TYPE',
                'name' => 'Tipo de conector de áudio',
                'value_name' => '3.5 mm'
            ];
            
            // Tipo de conectividade (obrigatório para celulares)
            $atributos[] = [
                'id' => 'CONNECTIVITY_TYPE',
                'name' => 'Tipo de conectividade',
                'value_name' => '4G'
            ];
            
            // Tipo de Wi-Fi (obrigatório para celulares)
            $atributos[] = [
                'id' => 'WIFI_TYPE',
                'name' => 'Tipo de Wi-Fi',
                'value_name' => '802.11 a/b/g/n/ac'
            ];
            
            // Tipo de Bluetooth (obrigatório para celulares)
            $atributos[] = [
                'id' => 'BLUETOOTH_TYPE',
                'name' => 'Tipo de Bluetooth',
                'value_name' => '5.0'
            ];
            
            // Tipo de GPS (obrigatório para celulares)
            $atributos[] = [
                'id' => 'GPS_TYPE',
                'name' => 'Tipo de GPS',
                'value_name' => 'A-GPS'
            ];
            
            // Tipo de NFC (obrigatório para celulares)
            $atributos[] = [
                'id' => 'NFC_TYPE',
                'name' => 'Tipo de NFC',
                'value_name' => 'Sim'
            ];
            
            // Tipo de sensor (obrigatório para celulares)
            $atributos[] = [
                'id' => 'SENSOR_TYPE',
                'name' => 'Tipo de sensor',
                'value_name' => 'Acelerômetro, Giroscópio, Proximidade'
            ];
            
            // Tipo de vibração (obrigatório para celulares)
            $atributos[] = [
                'id' => 'VIBRATION_TYPE',
                'name' => 'Tipo de vibração',
                'value_name' => 'Motor de vibração'
            ];
            
            // Tipo de notificação (obrigatório para celulares)
            $atributos[] = [
                'id' => 'NOTIFICATION_TYPE',
                'name' => 'Tipo de notificação',
                'value_name' => 'LED'
            ];
            
            // Tipo de proteção (obrigatório para celulares)
            $atributos[] = [
                'id' => 'PROTECTION_TYPE',
                'name' => 'Tipo de proteção',
                'value_name' => 'Gorilla Glass'
            ];
            
            // Tipo de resistência (obrigatório para celulares)
            $atributos[] = [
                'id' => 'RESISTANCE_TYPE',
                'name' => 'Tipo de resistência',
                'value_name' => 'IP68'
            ];
            
            // Tipo de certificação (obrigatório para celulares)
            $atributos[] = [
                'id' => 'CERTIFICATION_TYPE',
                'name' => 'Tipo de certificação',
                'value_name' => 'CE, RoHS'
            ];
            
            // Tipo de garantia (obrigatório para celulares)
            $atributos[] = [
                'id' => 'WARRANTY_TYPE',
                'name' => 'Tipo de garantia',
                'value_name' => 'Fabricante'
            ];
            
            // Tipo de origem (obrigatório para celulares)
            $atributos[] = [
                'id' => 'ORIGIN_TYPE',
                'name' => 'Tipo de origem',
                'value_name' => 'Importado'
            ];
            
            // Tipo de embalagem (obrigatório para celulares)
            $atributos[] = [
                'id' => 'PACKAGING_TYPE',
                'name' => 'Tipo de embalagem',
                'value_name' => 'Caixa original'
            ];
            
            // Tipo de acessórios (obrigatório para celulares)
            $atributos[] = [
                'id' => 'ACCESSORIES_TYPE',
                'name' => 'Tipo de acessórios',
                'value_name' => 'Carregador, Cabo USB, Manual'
            ];
            
            // Resistência à água
            $atributos[] = [
                'id' => 'WATER_RESISTANCE',
                'name' => 'Resistência à água',
                'value_name' => 'Não'
            ];
            
            // Biometria
            $atributos[] = [
                'id' => 'BIOMETRY',
                'name' => 'Biometria',
                'value_name' => 'Impressão digital'
            ];
            
            // Tipo de tela
            $atributos[] = [
                'id' => 'DISPLAY_TYPE',
                'name' => 'Tipo de display',
                'value_name' => 'TFT'
            ];
            
            // Resolução da tela
            $atributos[] = [
                'id' => 'DISPLAY_RESOLUTION',
                'name' => 'Resolução da tela',
                'value_name' => 'HD+ (1600 x 720)'
            ];
            
            // Número de câmeras
            $atributos[] = [
                'id' => 'CAMERA_COUNT',
                'name' => 'Número de câmeras',
                'value_name' => '4'
            ];
            
            // Tipo de câmera frontal
            $atributos[] = [
                'id' => 'FRONT_CAMERA_TYPE',
                'name' => 'Tipo de câmera frontal',
                'value_name' => 'Única'
            ];
            
            // Resolução da câmera frontal
            $atributos[] = [
                'id' => 'FRONT_CAMERA_RESOLUTION',
                'name' => 'Resolução da câmera frontal',
                'value_name' => '8 MP'
            ];
            
            // Tipo de câmera traseira
            $atributos[] = [
                'id' => 'REAR_CAMERA_TYPE',
                'name' => 'Tipo de câmera traseira',
                'value_name' => 'Múltipla'
            ];
            
            // Resolução da câmera traseira
            $atributos[] = [
                'id' => 'REAR_CAMERA_RESOLUTION',
                'name' => 'Resolução da câmera traseira',
                'value_name' => '48 MP'
            ];
            
            // Tipo de flash
            $atributos[] = [
                'id' => 'FLASH_TYPE',
                'name' => 'Tipo de flash',
                'value_name' => 'LED'
            ];
            
            // Tipo de estabilização
            $atributos[] = [
                'id' => 'STABILIZATION_TYPE',
                'name' => 'Tipo de estabilização',
                'value_name' => 'Digital'
            ];
            
            // Tipo de autofoco
            $atributos[] = [
                'id' => 'AUTOFOCUS_TYPE',
                'name' => 'Tipo de autofoco',
                'value_name' => 'Automático'
            ];
            
            // Tipo de zoom
            $atributos[] = [
                'id' => 'ZOOM_TYPE',
                'name' => 'Tipo de zoom',
                'value_name' => 'Digital'
            ];
            
            // Tipo de gravação de vídeo
            $atributos[] = [
                'id' => 'VIDEO_RECORDING_TYPE',
                'name' => 'Tipo de gravação de vídeo',
                'value_name' => 'Full HD'
            ];
            
            // Tipo de áudio
            $atributos[] = [
                'id' => 'AUDIO_TYPE',
                'name' => 'Tipo de áudio',
                'value_name' => 'Estéreo'
            ];
            
            // Tipo de alto-falante
            $atributos[] = [
                'id' => 'SPEAKER_TYPE',
                'name' => 'Tipo de alto-falante',
                'value_name' => 'Único'
            ];
            
            // Tipo de microfone
            $atributos[] = [
                'id' => 'MICROPHONE_TYPE',
                'name' => 'Tipo de microfone',
                'value_name' => 'Múltiplo'
            ];
            
            // Tipo de conector de áudio
            $atributos[] = [
                'id' => 'AUDIO_CONNECTOR_TYPE',
                'name' => 'Tipo de conector de áudio',
                'value_name' => '3.5 mm'
            ];
            
            // Tipo de conectividade
            $atributos[] = [
                'id' => 'CONNECTIVITY_TYPE',
                'name' => 'Tipo de conectividade',
                'value_name' => '4G'
            ];
            
            // Tipo de Wi-Fi
            $atributos[] = [
                'id' => 'WIFI_TYPE',
                'name' => 'Tipo de Wi-Fi',
                'value_name' => '802.11 a/b/g/n/ac'
            ];
            
            // Tipo de Bluetooth
            $atributos[] = [
                'id' => 'BLUETOOTH_TYPE',
                'name' => 'Tipo de Bluetooth',
                'value_name' => '5.0'
            ];
            
            // Tipo de GPS
            $atributos[] = [
                'id' => 'GPS_TYPE',
                'name' => 'Tipo de GPS',
                'value_name' => 'A-GPS'
            ];
            
            // Tipo de NFC
            $atributos[] = [
                'id' => 'NFC_TYPE',
                'name' => 'Tipo de NFC',
                'value_name' => 'Sim'
            ];
            
            // Tipo de sensor
            $atributos[] = [
                'id' => 'SENSOR_TYPE',
                'name' => 'Tipo de sensor',
                'value_name' => 'Acelerômetro, Giroscópio, Proximidade'
            ];
            
            // Tipo de vibração
            $atributos[] = [
                'id' => 'VIBRATION_TYPE',
                'name' => 'Tipo de vibração',
                'value_name' => 'Motor de vibração'
            ];
            
            // Tipo de notificação
            $atributos[] = [
                'id' => 'NOTIFICATION_TYPE',
                'name' => 'Tipo de notificação',
                'value_name' => 'LED'
            ];
            
            // Tipo de proteção
            $atributos[] = [
                'id' => 'PROTECTION_TYPE',
                'name' => 'Tipo de proteção',
                'value_name' => 'Gorilla Glass'
            ];
            
            // Tipo de resistência
            $atributos[] = [
                'id' => 'RESISTANCE_TYPE',
                'name' => 'Tipo de resistência',
                'value_name' => 'IP68'
            ];
            
            // Tipo de certificação
            $atributos[] = [
                'id' => 'CERTIFICATION_TYPE',
                'name' => 'Tipo de certificação',
                'value_name' => 'CE, RoHS'
            ];
            
            // Tipo de garantia
            $atributos[] = [
                'id' => 'WARRANTY_TYPE',
                'name' => 'Tipo de garantia',
                'value_name' => 'Fabricante'
            ];
            
            // Tipo de origem
            $atributos[] = [
                'id' => 'ORIGIN_TYPE',
                'name' => 'Tipo de origem',
                'value_name' => 'Importado'
            ];
            
            // Tipo de embalagem
            $atributos[] = [
                'id' => 'PACKAGING_TYPE',
                'name' => 'Tipo de embalagem',
                'value_name' => 'Caixa original'
            ];
            
            // Tipo de acessórios
            $atributos[] = [
                'id' => 'ACCESSORIES_TYPE',
                'name' => 'Tipo de acessórios',
                'value_name' => 'Carregador, Cabo USB, Manual'
            ];
            
            log_info('ML: Adicionados ' . count($atributos) . ' atributos específicos para celular (MLB1055)');
        }
        
        return $atributos;
    }

    /**
     * Logs de integração
     */
    public function logs()
    {
        $this->data['logs'] = $this->MercadoLivre_model->getLogs(100);
        $this->data['view'] = 'mercadolivre/logs';
        return $this->layout();
    }

    /**
     * Buscar subcategorias de uma categoria do Mercado Livre (AJAX)
     */
    public function buscarSubcategorias()
    {
        $category_id = $this->input->get('category_id');
        if (!$category_id) {
            echo json_encode(['success' => false, 'error' => 'ID da categoria não informado']);
            return;
        }
        $url = "https://api.mercadolibre.com/categories/{$category_id}";
        $response = @file_get_contents($url);
        if ($response === false) {
            echo json_encode(['success' => false, 'error' => 'Erro ao buscar subcategorias']);
            return;
        }
        $data = json_decode($response, true);
        $subcategories = $data['children_categories'] ?? [];
        echo json_encode(['success' => true, 'subcategories' => $subcategories, 'category' => $data]);
    }

    /**
     * Buscar atributos obrigatórios de uma categoria do Mercado Livre (AJAX)
     */
    public function buscarAtributosCategoria()
    {
        $category_id = $this->input->get('category_id');
        if (!$category_id) {
            echo json_encode(['success' => false, 'error' => 'ID da categoria não informado']);
            return;
        }
        $url = "https://api.mercadolibre.com/categories/{$category_id}/attributes";
        $response = @file_get_contents($url);
        if ($response === false) {
            echo json_encode(['success' => false, 'error' => 'Erro ao buscar atributos']);
            return;
        }
        $data = json_decode($response, true);
        echo json_encode(['success' => true, 'attributes' => $data]);
    }

    /**
     * Forçar sincronização das configurações do .env
     */
    public function sincronizarConfiguracoes()
    {
        log_info('ML: Iniciando sincronização de configurações');
        
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            log_info('ML: Sem permissão para sincronizar configurações');
            echo json_encode(['success' => false, 'message' => 'Sem permissão']);
            return;
        }

        try {
            log_info('ML: Verificando variáveis do .env');
            log_info('ML: ACCESS_TOKEN: ' . (isset($_ENV['MERCADO_LIVRE_ACCESS_TOKEN']) ? 'Presente' : 'Ausente'));
            log_info('ML: REFRESH_TOKEN: ' . (isset($_ENV['MERCADO_LIVRE_REFRESH_TOKEN']) ? 'Presente' : 'Ausente'));
            log_info('ML: USER_ID: ' . (isset($_ENV['MERCADO_LIVRE_USER_ID']) ? 'Presente' : 'Ausente'));
            log_info('ML: NICKNAME: ' . (isset($_ENV['MERCADO_LIVRE_NICKNAME']) ? 'Presente' : 'Ausente'));
            log_info('ML: ENABLED: ' . (isset($_ENV['MERCADO_LIVRE_ENABLED']) ? $_ENV['MERCADO_LIVRE_ENABLED'] : 'Ausente'));
            
            $result = $this->MercadoLivre_model->sincronizarConfiguracaoDoEnv();
            
            if ($result) {
                log_info('ML: Configurações sincronizadas com sucesso');
                echo json_encode(['success' => true, 'message' => 'Configurações sincronizadas com sucesso']);
            } else {
                log_info('ML: Erro ao sincronizar configurações');
                echo json_encode(['success' => false, 'message' => 'Erro ao sincronizar configurações']);
            }
        } catch (Exception $e) {
            log_info('ML: Exceção ao sincronizar configurações: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Buscar logs do Mercado Livre
     */
    public function getLogs()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão']);
            return;
        }

        try {
            $logs = $this->MercadoLivre_model->getLogs();
            
            if ($logs) {
                echo json_encode([
                    'success' => true,
                    'logs' => $logs
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'logs' => []
                ]);
            }
        } catch (Exception $e) {
            log_info('ML: Erro ao buscar logs: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao buscar logs: ' . $e->getMessage()
            ]);
        }
    }
} 