<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MercadoLivre extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MercadoLivre_model');
        $this->load->library('session');
        
        // Verificar permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar a integração com Mercado Livre.');
            redirect(base_url());
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

    /**
     * Iniciar processo de autenticação OAuth2
     */
    public function autenticar()
    {
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
        file_put_contents('C:\\wamp64\\tmp\\ml_debug.txt', date('Y-m-d H:i:s') . "\n" . $response . "\n\n", FILE_APPEND);

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
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão']);
            return;
        }

        $produtos_pendentes = $this->MercadoLivre_model->getProdutosPendentes();
        $sucessos = 0;
        $erros = 0;

        foreach ($produtos_pendentes as $produto) {
            $resultado = $this->publicarProduto($produto);
            
            if ($resultado['success']) {
                $sucessos++;
                $this->MercadoLivre_model->atualizarStatus(
                    $produto->produto_id, 
                    'active', 
                    $resultado['ml_id'], 
                    $resultado['permalink']
                );
            } else {
                $erros++;
                $this->MercadoLivre_model->atualizarSincronizacao($produto->produto_id, $resultado['error']);
            }

            $this->MercadoLivre_model->salvarLog(
                $produto->produto_id,
                'sync',
                $resultado['success'] ? 'success' : 'error',
                $resultado['success'] ? 'Produto sincronizado com sucesso' : $resultado['error']
            );
        }

        echo json_encode([
            'success' => true,
            'message' => "Sincronização concluída: $sucessos sucessos, $erros erros"
        ]);
    }

    /**
     * Publicar produto no ML
     */
    private function publicarProduto($produto)
    {
        $config = $this->MercadoLivre_model->getConfiguracao();
        
        if (!$config || !$config->ativo) {
            return ['success' => false, 'error' => 'Integração não configurada'];
        }

        // Verificar se token expirou
        if ($config->token_expires_at && strtotime($config->token_expires_at) < time()) {
            if (!$this->renovarToken()) {
                return ['success' => false, 'error' => 'Token expirado e não foi possível renovar'];
            }
            $config = $this->MercadoLivre_model->getConfiguracao();
        }

        // Preparar dados do produto
        $dados_produto = $this->prepararDadosProduto($produto);
        
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

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 201) {
            $resultado = json_decode($response, true);
            return [
                'success' => true,
                'ml_id' => $resultado['id'],
                'permalink' => $resultado['permalink']
            ];
        } else {
            $erro = json_decode($response, true);
            return [
                'success' => false,
                'error' => isset($erro['message']) ? $erro['message'] : 'Erro desconhecido'
            ];
        }
    }

    /**
     * Preparar dados do produto para o ML
     */
    private function prepararDadosProduto($produto)
    {
        return [
            'title' => $produto->descricao,
            'category_id' => $produto->ml_categoria,
            'price' => (float)$produto->precoVenda,
            'currency_id' => 'BRL',
            'available_quantity' => (int)$produto->estoque,
            'buying_mode' => 'buy_it_now',
            'condition' => $produto->ml_condicao,
            'description' => [
                'plain_text' => $produto->ml_descricao ?: $produto->descricao
            ],
            'pictures' => $this->getImagensProduto($produto->produto_id),
            'attributes' => $this->getAtributosProduto($produto),
            'shipping' => [
                'mode' => $produto->ml_envios ? 'me2' : 'not_specified'
            ]
        ];
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
        
        if ($produto->marcaProduto) {
            $atributos[] = [
                'id' => 'BRAND',
                'name' => 'Marca',
                'value_name' => $produto->marcaProduto
            ];
        }
        
        if ($produto->modeloProduto) {
            $atributos[] = [
                'id' => 'MODEL',
                'name' => 'Modelo',
                'value_name' => $produto->modeloProduto
            ];
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
} 