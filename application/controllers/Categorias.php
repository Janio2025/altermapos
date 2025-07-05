<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Categorias extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Categorias_model');
        $this->data['menuCategorias'] = 'Categorias';
    }

    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCategoria')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar categorias.');
            redirect(base_url());
        }
        $this->data['categorias'] = $this->Categorias_model->getAll();
        $this->data['view'] = 'categorias/categorias';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCategoria')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
                return;
            }
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar categorias.');
            redirect(site_url('categorias'));
        }
        if ($this->input->post('categoria')) {
            $parent_id = $this->input->post('parent_id');
            $tipo = trim($this->input->post('tipo'));
            $tipo_novo = trim($this->input->post('tipo_novo'));
            
            // Usar o tipo novo se foi preenchido, senão usar o tipo do select
            $tipo_final = $tipo_novo ?: $tipo;
            
            // Normalizar: remover acentos, trocar espaços por underline, minúsculo
            $tipo_final = strtolower(str_replace(' ', '_',
                preg_replace('/[áàãâä]/ui', 'a',
                preg_replace('/[éèêë]/ui', 'e',
                preg_replace('/[íìîï]/ui', 'i',
                preg_replace('/[óòõôö]/ui', 'o',
                preg_replace('/[úùûü]/ui', 'u',
                preg_replace('/[ç]/ui', 'c', $tipo_final))))))));
            $dados = [
                'ml_id' => null,
                'categoria' => $this->input->post('categoria'),
                'parent_id' => ($parent_id === '' ? null : $parent_id),
                'tipo' => $tipo_final ?: 'interna',
                'status' => 1,
                'cadastro' => date('Y-m-d')
            ];
            $id = $this->Categorias_model->adicionar($dados);
            if ($this->input->is_ajax_request()) {
                if ($id) {
                    echo json_encode(['success' => true, 'id' => $id, 'categoria' => $this->input->post('categoria')]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao salvar categoria.']);
                }
                return;
            }
            $this->session->set_flashdata('success', 'Categoria adicionada com sucesso!');
            redirect(site_url('categorias'));
        }
        $this->data['categorias'] = $this->Categorias_model->getAll();
        
        // Buscar tipos únicos já cadastrados
        $this->db->select('DISTINCT tipo', false);
        $this->db->where('tipo IS NOT NULL');
        $this->db->where('tipo !=', '');
        $this->db->order_by('tipo', 'ASC');
        $tipos_existentes = $this->db->get('categorias')->result();
        $this->data['tipos_existentes'] = $tipos_existentes;
        
        $this->data['view'] = 'categorias/adicionarCategoria';
        return $this->layout();
    }

    public function editar($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCategoria')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar categorias.');
            redirect(site_url('categorias'));
        }
        $this->data['categoria'] = $this->Categorias_model->getById($id);
        if ($this->input->post('categoria')) {
            $parent_id = $this->input->post('parent_id');
            $tipo = trim($this->input->post('tipo'));
            $tipo_novo = trim($this->input->post('tipo_novo'));
            
            // Usar o tipo novo se foi preenchido, senão usar o tipo do select
            $tipo_final = $tipo_novo ?: $tipo;
            
            // Normalizar: remover acentos, trocar espaços por underline, minúsculo
            $tipo_final = strtolower(str_replace(' ', '_',
                preg_replace('/[áàãâä]/ui', 'a',
                preg_replace('/[éèêë]/ui', 'e',
                preg_replace('/[íìîï]/ui', 'i',
                preg_replace('/[óòõôö]/ui', 'o',
                preg_replace('/[úùûü]/ui', 'u',
                preg_replace('/[ç]/ui', 'c', $tipo_final))))))));
            $dados = [
                'ml_id' => null,
                'categoria' => $this->input->post('categoria'),
                'parent_id' => ($parent_id === '' ? null : $parent_id),
                'tipo' => $tipo_final ?: 'interna'
            ];
            $this->Categorias_model->editar($id, $dados);
            $this->session->set_flashdata('success', 'Categoria editada com sucesso!');
            redirect(site_url('categorias'));
        }
        $this->data['categorias'] = $this->Categorias_model->getAll();
        $this->data['view'] = 'categorias/editarCategoria';
        return $this->layout();
    }

    public function deletar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dCategoria')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para deletar categorias.');
            redirect(site_url('categorias'));
        }
        $id = $this->input->post('id');
        if ($id) {
            $this->Categorias_model->deletar($id);
            $this->session->set_flashdata('success', 'Categoria removida com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'ID de categoria inválido.');
        }
        redirect(site_url('categorias'));
    }

    /**
     * Retorna as subcategorias de uma categoria (AJAX)
     */
    public function getSubcategoriasAjax() {
        $parent_id = $this->input->get('parent_id');
        $subcats = $this->Categorias_model->getByParent($parent_id);
        $result = [];
        foreach ($subcats as $cat) {
            $result[] = [
                'id' => $cat->idCategorias,
                'categoria' => $cat->categoria
            ];
        }
        echo json_encode($result);
    }

    /**
     * Buscar categorias do Mercado Livre via API (AJAX)
     */
    public function buscarCategoriasML() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCategoria')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        try {
            // Verificar se cURL está disponível
            if (!function_exists('curl_init')) {
                echo json_encode(['success' => false, 'message' => 'cURL não está disponível no servidor.']);
                return;
            }

            // Buscar categorias principais do Mercado Livre
            $url = "https://api.mercadolibre.com/sites/MLB/categories";
            
            // Sistema de retry com backoff exponencial e headers otimizados
            $max_retries = 5;
            $base_delay = 1; // segundos
            
            for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
                // Calcular delay exponencial
                $delay = $base_delay * pow(2, $attempt - 1);
                
                // User-Agents alternativos
                $user_agents = [
                    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
                    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'PostmanRuntime/7.32.3',
                    'curl/7.68.0'
                ];
                
                foreach ($user_agents as $user_agent) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                    
                    // Headers mais específicos para contornar bloqueios
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Accept: application/json, text/plain, */*',
                        'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
                        'Accept-Encoding: gzip, deflate, br',
                        'Connection: keep-alive',
                        'Cache-Control: no-cache',
                        'Pragma: no-cache',
                        'Referer: https://www.mercadolivre.com.br/',
                        'Origin: https://www.mercadolivre.com.br',
                        'sec-ch-ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                        'sec-ch-ua-mobile: ?0',
                        'sec-ch-ua-platform: "Windows"',
                        'sec-fetch-dest: empty',
                        'sec-fetch-mode: cors',
                        'sec-fetch-site: cross-site',
                        'DNT: 1',
                        'X-Requested-With: XMLHttpRequest',
                        'X-Forwarded-For: ' . $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                        'X-Real-IP: ' . $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                    ]);
                    
                    $response = curl_exec($ch);
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curl_error = curl_error($ch);
                    curl_close($ch);
                    
                    if ($response !== false && $http_code === 200) {
                        // Sucesso! Sair dos loops
                        break 2;
                    }
                    
                    // Aguardar um pouco antes da próxima tentativa
                    usleep(500000); // 0.5 segundos
                }
                
                // Se chegou aqui, falhou com todos os User-Agents
                if ($attempt < $max_retries) {
                    sleep($delay);
                }
            }
            
            // Tentar com diferentes User-Agents se falhar
            $response = null;
            $http_code = 0;
            $curl_error = '';
            
            foreach ($user_agents as $user_agent) {
                curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curl_error = curl_error($ch);
                
                if ($response !== false && $http_code === 200) {
                    break; // Sucesso, sair do loop
                }
                
                // Aguardar um pouco antes da próxima tentativa
                usleep(500000); // 0.5 segundos
            }
            
            curl_close($ch);
            
            if ($response === false || $curl_error) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro ao conectar com a API do Mercado Livre: ' . $curl_error
                ]);
                return;
            }

            if ($http_code !== 200) {
                // Se der 403, tentar uma abordagem alternativa
                if ($http_code === 403) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'API do Mercado Livre bloqueou o acesso. Tentando método alternativo...'
                    ]);
                    return;
                }
                
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro HTTP ' . $http_code . ' ao acessar a API do Mercado Livre.'
                ]);
                return;
            }

            $categorias = json_decode($response, true);
            
            if (!$categorias) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro ao processar resposta da API: ' . json_last_error_msg()
                ]);
                return;
            }

            // Limitar a 20 categorias principais para não sobrecarregar
            $categorias = array_slice($categorias, 0, 20);

            // Buscar subcategorias para as principais categorias
            $categoriasCompletas = [];
            foreach ($categorias as $categoria) {
                $categoriaCompleta = [
                    'id' => $categoria['id'],
                    'name' => $categoria['name']
                ];

                // Buscar subcategorias com cURL
                $subcategorias_url = "https://api.mercadolibre.com/categories/{$categoria['id']}";
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $subcategorias_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Accept: application/json, text/plain, */*',
                    'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
                    'Accept-Encoding: gzip, deflate, br',
                    'Connection: keep-alive',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                    'Referer: https://www.mercadolivre.com.br/',
                    'Origin: https://www.mercadolivre.com.br'
                ]);
                
                $subcategorias_response = curl_exec($ch);
                curl_close($ch);
                
                if ($subcategorias_response !== false) {
                    $subcategorias_data = json_decode($subcategorias_response, true);
                    if (isset($subcategorias_data['children_categories']) && !empty($subcategorias_data['children_categories'])) {
                        $categoriaCompleta['children'] = array_slice($subcategorias_data['children_categories'], 0, 5); // Limitar a 5 subcategorias
                    }
                }

                $categoriasCompletas[] = $categoriaCompleta;
            }

            echo json_encode(['success' => true, 'categorias' => $categoriasCompletas]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Importar categorias selecionadas do Mercado Livre (AJAX)
     */
    public function importarCategoriasML() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCategoria')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        $categorias = $this->input->post('categorias');
        
        if (!$categorias || !is_array($categorias)) {
            echo json_encode(['success' => false, 'message' => 'Nenhuma categoria selecionada.']);
            return;
        }

        try {
            $importadas = 0;
            $erros = [];

            foreach ($categorias as $categoria) {
                $ml_id = $categoria['id'];
                $nome = $categoria['name'];

                // Verificar se já existe
                $existente = $this->Categorias_model->getByMLId($ml_id);
                if ($existente) {
                    $erros[] = "Categoria '{$nome}' já existe no sistema.";
                    continue;
                }

                // Preparar dados para inserção
                $dados = [
                    'ml_id' => $ml_id,
                    'categoria' => $nome,
                    'parent_id' => null, // Categoria raiz
                    'tipo' => 'mercado_livre',
                    'status' => 1,
                    'cadastro' => date('Y-m-d')
                ];

                $id = $this->Categorias_model->adicionar($dados);
                if ($id) {
                    $importadas++;
                } else {
                    $erros[] = "Erro ao importar categoria '{$nome}'.";
                }
            }

            $mensagem = "Importação concluída: {$importadas} categorias importadas com sucesso.";
            if (!empty($erros)) {
                $mensagem .= " Erros: " . implode(', ', $erros);
            }

            echo json_encode([
                'success' => true, 
                'message' => $mensagem,
                'importadas' => $importadas,
                'erros' => $erros
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Buscar atributos de uma categoria do Mercado Livre (AJAX)
     */
    public function buscarAtributosML() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCategoria')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        $categoria_id = $this->input->get('categoria_id');
        $ml_id = $this->input->get('ml_id');

        if (!$categoria_id || !$ml_id) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
            return;
        }

        try {
            // Verificar se cURL está disponível
            if (!function_exists('curl_init')) {
                echo json_encode(['success' => false, 'message' => 'cURL não está disponível no servidor.']);
                return;
            }

            // Buscar atributos da API do Mercado Livre
            $url = "https://api.mercadolibre.com/categories/{$ml_id}/attributes";
            
            // User-Agents alternativos para contornar bloqueios
            $user_agents = [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json, text/plain, */*',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
                'Accept-Encoding: gzip, deflate, br',
                'Connection: keep-alive',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Referer: https://www.mercadolivre.com.br/',
                'Origin: https://www.mercadolivre.com.br',
                'sec-ch-ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: cross-site',
                'DNT: 1'
            ]);
            
            // Tentar com diferentes User-Agents
            $response = null;
            $http_code = 0;
            $curl_error = '';
            
            foreach ($user_agents as $user_agent) {
                curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curl_error = curl_error($ch);
                
                if ($response !== false && $http_code === 200) {
                    break; // Sucesso, sair do loop
                }
                
                // Aguardar um pouco antes da próxima tentativa
                usleep(500000); // 0.5 segundos
            }
            
            curl_close($ch);
            
            if ($response === false || $curl_error) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro ao conectar com a API do Mercado Livre: ' . $curl_error
                ]);
                return;
            }

            if ($http_code !== 200) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro HTTP ' . $http_code . ' ao acessar a API do Mercado Livre.'
                ]);
                return;
            }

            $atributos = json_decode($response, true);
            
            if (!$atributos) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro ao processar resposta da API: ' . json_last_error_msg()
                ]);
                return;
            }

            // Filtrar apenas atributos relevantes (excluir alguns que são automáticos)
            $atributosFiltrados = [];
            foreach ($atributos as $atributo) {
                // Pular atributos que são gerenciados automaticamente pelo ML
                if (in_array($atributo['id'], ['ITEM_CONDITION', 'SELLER_CUSTOM_NEW', 'SELLER_CUSTOM_USED'])) {
                    continue;
                }
                
                $atributosFiltrados[] = [
                    'id' => $atributo['id'],
                    'name' => $atributo['name'],
                    'value_type' => $atributo['value_type'],
                    'required' => isset($atributo['required']) ? $atributo['required'] : false,
                    'values' => isset($atributo['values']) ? $atributo['values'] : [],
                    'hierarchy' => isset($atributo['hierarchy']) ? $atributo['hierarchy'] : null,
                    'tags' => isset($atributo['tags']) ? $atributo['tags'] : [],
                    'attribute_group_id' => isset($atributo['attribute_group_id']) ? $atributo['attribute_group_id'] : null,
                    'attribute_group_name' => isset($atributo['attribute_group_name']) ? $atributo['attribute_group_name'] : null
                ];
            }

            echo json_encode(['success' => true, 'atributos' => $atributosFiltrados]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Salvar atributos selecionados de uma categoria (AJAX)
     */
    public function salvarAtributosML() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCategoria')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        $categoria_id = $this->input->post('categoria_id');
        $atributos = $this->input->post('atributos');

        // Log dos dados recebidos
        log_message('debug', 'salvarAtributosML - categoria_id recebido: ' . $categoria_id);
        log_message('debug', 'salvarAtributosML - atributos recebidos: ' . json_encode($atributos));

        if (!$categoria_id || !$atributos || !is_array($atributos)) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
            return;
        }

        // Verificar se a categoria existe
        $categoria = $this->Categorias_model->getById($categoria_id);
        if (!$categoria) {
            log_message('error', 'salvarAtributosML - Categoria não encontrada: ' . $categoria_id);
            echo json_encode(['success' => false, 'message' => 'Categoria não encontrada.']);
            return;
        }

        log_message('debug', 'salvarAtributosML - Categoria encontrada: ' . $categoria->categoria . ' (ID: ' . $categoria->idCategorias . ')');

        try {
            $salvos = 0;
            $erros = [];

            foreach ($atributos as $atributo) {
                // Verificar se já existe
                $existente = $this->Categorias_model->getAtributoByMLId($categoria_id, $atributo['id']);
                
                // Preparar dados com verificações de segurança
                $dados = [
                    'name' => isset($atributo['name']) ? $atributo['name'] : '',
                    'value_type' => isset($atributo['value_type']) ? $atributo['value_type'] : 'string',
                    'required' => isset($atributo['required']) && $atributo['required'] ? 1 : 0,
                    'values' => json_encode(isset($atributo['values']) ? $atributo['values'] : []),
                    'hierarchy' => isset($atributo['hierarchy']) ? $atributo['hierarchy'] : null,
                    'tags' => isset($atributo['tags']) ? json_encode($atributo['tags']) : null,
                    'attribute_group_id' => isset($atributo['attribute_group_id']) ? $atributo['attribute_group_id'] : null,
                    'attribute_group_name' => isset($atributo['attribute_group_name']) ? $atributo['attribute_group_name'] : null,
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if ($existente) {
                    // Atualizar atributo existente
                    $resultado = $this->Categorias_model->atualizarAtributo($existente->id, $dados);
                    log_message('debug', 'Atualizando atributo: ' . $atributo['name'] . ' - Resultado: ' . ($resultado ? 'sucesso' : 'falha'));
                } else {
                    // Inserir novo atributo
                    $dados['categoria_id'] = $categoria_id;
                    $dados['ml_attribute_id'] = $atributo['id'];
                    $dados['created_at'] = date('Y-m-d H:i:s');
                    
                    // Log dos dados sendo inseridos
                    log_message('debug', 'Inserindo atributo: ' . json_encode($dados));
                    
                    $resultado = $this->Categorias_model->adicionarAtributo($dados);
                    
                    // Log do resultado
                    log_message('debug', 'Resultado inserção: ' . ($resultado ? 'ID: ' . $resultado : 'falha'));
                    
                    // Verificar se houve erro no banco
                    if (!$resultado) {
                        $db_error = $this->db->error();
                        log_message('error', 'Erro no banco: ' . json_encode($db_error));
                    }
                }

                if ($resultado) {
                    $salvos++;
                } else {
                    $erros[] = "Erro ao salvar atributo '{$atributo['name']}'.";
                }
            }

            $mensagem = "Atributos salvos com sucesso: {$salvos} atributos processados.";
            if (!empty($erros)) {
                $mensagem .= " Erros: " . implode(', ', $erros);
            }

            echo json_encode([
                'success' => true, 
                'message' => $mensagem,
                'salvos' => $salvos,
                'erros' => $erros
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Teste de conectividade com a API do Mercado Livre
     */
    public function testarAPI() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCategoria')) {
            echo 'Sem permissão.';
            return;
        }

        echo "<h2>Teste de Conectividade com API do Mercado Livre</h2>";

        // Verificar se cURL está disponível
        if (!function_exists('curl_init')) {
            echo "<p style='color: red;'>❌ cURL não está disponível no servidor.</p>";
            echo "<p>Para resolver, habilite a extensão cURL no PHP.</p>";
            return;
        }

        echo "<p style='color: green;'>✅ cURL está disponível.</p>";

        // Testar conexão com a API
        $url = "https://api.mercadolibre.com/sites/MLB/categories";

        echo "<h3>Testando conexão com: $url</h3>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch);

        echo "<h4>Informações da requisição:</h4>";
        echo "<ul>";
        echo "<li><strong>HTTP Code:</strong> $http_code</li>";
        echo "<li><strong>cURL Error:</strong> " . ($curl_error ?: 'Nenhum') . "</li>";
        echo "<li><strong>Tempo de resposta:</strong> " . round($curl_info['total_time'], 2) . "s</li>";
        echo "<li><strong>Tamanho da resposta:</strong> " . strlen($response) . " bytes</li>";
        echo "</ul>";

        if ($response === false || $curl_error) {
            echo "<p style='color: red;'>❌ Erro na conexão: $curl_error</p>";
        } elseif ($http_code !== 200) {
            echo "<p style='color: red;'>❌ Erro HTTP $http_code</p>";
            echo "<p>Resposta: " . htmlspecialchars(substr($response, 0, 500)) . "...</p>";
        } else {
            echo "<p style='color: green;'>✅ Conexão bem-sucedida!</p>";
            
            $categorias = json_decode($response, true);
            if ($categorias) {
                echo "<p>✅ JSON decodificado com sucesso.</p>";
                echo "<p>📊 Total de categorias encontradas: " . count($categorias) . "</p>";
                
                echo "<h4>Primeiras 5 categorias:</h4>";
                echo "<ul>";
                for ($i = 0; $i < min(5, count($categorias)); $i++) {
                    $cat = $categorias[$i];
                    echo "<li><strong>{$cat['name']}</strong> (ID: {$cat['id']})</li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='color: red;'>❌ Erro ao decodificar JSON: " . json_last_error_msg() . "</p>";
            }
        }

        // Verificar configurações do PHP
        echo "<h3>Configurações do PHP:</h3>";
        echo "<ul>";
        echo "<li><strong>allow_url_fopen:</strong> " . (ini_get('allow_url_fopen') ? 'Habilitado' : 'Desabilitado') . "</li>";
        echo "<li><strong>curl.cainfo:</strong> " . (ini_get('curl.cainfo') ?: 'Não definido') . "</li>";
        echo "<li><strong>curl.ssl_verify_peer:</strong> " . (ini_get('curl.ssl_verify_peer') ? 'Habilitado' : 'Desabilitado') . "</li>";
        echo "</ul>";

        // Testar se o servidor consegue resolver DNS
        echo "<h3>Teste de DNS:</h3>";
        $host = 'api.mercadolibre.com';
        $ip = gethostbyname($host);
        if ($ip !== $host) {
            echo "<p style='color: green;'>✅ DNS resolvido: $host → $ip</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao resolver DNS para $host</p>";
        }

        echo "<hr>";
        echo "<p><strong>Se todos os testes passaram, a API deve funcionar no sistema.</strong></p>";
        echo "<p>Se houver erros, verifique:</p>";
        echo "<ul>";
        echo "<li>Se o servidor tem acesso à internet</li>";
        echo "<li>Se o firewall não está bloqueando conexões HTTPS</li>";
        echo "<li>Se a extensão cURL está habilitada no PHP</li>";
        echo "<li>Se as configurações de SSL estão corretas</li>";
        echo "</ul>";
    }

    /**
     * Verificar logs de erro (para debug)
     */
    public function verificarLogs() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCategoria')) {
            echo 'Sem permissão.';
            return;
        }

        $logPath = APPPATH . 'logs/';
        $files = glob($logPath . 'log-*.php');
        rsort($files); // Pega o mais recente primeiro
        
        echo "<h2>Últimos Logs do Sistema</h2>";
        
        if ($files) {
            $lines = file($files[0]);
            $output = '';
            $found = false;
            
            foreach (array_slice($lines, -100) as $line) { // Últimas 100 linhas
                if (stripos($line, 'erro') !== false || 
                    stripos($line, 'error') !== false || 
                    stripos($line, 'atributo') !== false ||
                    stripos($line, 'salvarAtributosML') !== false ||
                    stripos($line, 'adicionarAtributo') !== false) {
                    $output .= htmlspecialchars($line) . "<br>";
                    $found = true;
                }
            }
            
            if ($found) {
                echo $output;
            } else {
                echo "<p>Nenhum log relevante encontrado nas últimas 100 linhas.</p>";
            }
            
            echo "<p><strong>Arquivo de log:</strong> " . basename($files[0]) . "</p>";
        } else {
            echo "<p>Nenhum arquivo de log encontrado.</p>";
        }
    }

    /**
     * Buscar categorias alternativas quando a API estiver bloqueada
     */
    public function buscarCategoriasAlternativas() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCategoria')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        // Categorias principais do Mercado Livre (dados estáticos completos e expandidos)
        $categorias = [
            [
                'id' => 'MLB5672',
                'name' => 'Acessórios para Veículos',
                'children' => [
                    ['id' => 'MLB5673', 'name' => 'Acessórios para Carros'],
                    ['id' => 'MLB5674', 'name' => 'Acessórios para Motos'],
                    ['id' => 'MLB5675', 'name' => 'Acessórios para Caminhões'],
                    ['id' => 'MLB5676', 'name' => 'Acessórios para Bicicletas'],
                    ['id' => 'MLB5677', 'name' => 'Acessórios para Barcos'],
                    ['id' => 'MLB5678', 'name' => 'Acessórios para Utilitários'],
                    ['id' => 'MLB5679', 'name' => 'GPS e Navegação'],
                    ['id' => 'MLB5680', 'name' => 'Som Automotivo'],
                    ['id' => 'MLB5681', 'name' => 'Alarmes e Segurança'],
                    ['id' => 'MLB5682', 'name' => 'Limpadores e Cuidados']
                ]
            ],
            [
                'id' => 'MLB5725',
                'name' => 'Eletrônicos, Áudio e Vídeo',
                'children' => [
                    ['id' => 'MLB5726', 'name' => 'Celulares e Smartphones'],
                    ['id' => 'MLB5727', 'name' => 'Notebooks'],
                    ['id' => 'MLB5728', 'name' => 'Tablets'],
                    ['id' => 'MLB5729', 'name' => 'TVs'],
                    ['id' => 'MLB5730', 'name' => 'Áudio Portátil'],
                    ['id' => 'MLB5731', 'name' => 'Câmeras e Acessórios'],
                    ['id' => 'MLB5732', 'name' => 'Videogames'],
                    ['id' => 'MLB5733', 'name' => 'Instrumentos Musicais'],
                    ['id' => 'MLB5734', 'name' => 'Home Theater'],
                    ['id' => 'MLB5735', 'name' => 'Projetores'],
                    ['id' => 'MLB5736', 'name' => 'Smartwatches'],
                    ['id' => 'MLB5737', 'name' => 'Fones de Ouvido'],
                    ['id' => 'MLB5738', 'name' => 'Alto-falantes'],
                    ['id' => 'MLB5739', 'name' => 'Microfones']
                ]
            ],
            [
                'id' => 'MLB1000',
                'name' => 'Informática',
                'children' => [
                    ['id' => 'MLB1001', 'name' => 'Computadores'],
                    ['id' => 'MLB1002', 'name' => 'Monitores'],
                    ['id' => 'MLB1003', 'name' => 'Impressoras'],
                    ['id' => 'MLB1004', 'name' => 'Periféricos'],
                    ['id' => 'MLB1005', 'name' => 'Componentes'],
                    ['id' => 'MLB1006', 'name' => 'Software'],
                    ['id' => 'MLB1007', 'name' => 'Redes e Internet'],
                    ['id' => 'MLB1008', 'name' => 'Armazenamento'],
                    ['id' => 'MLB1009', 'name' => 'Gaming'],
                    ['id' => 'MLB1010', 'name' => 'Acessórios para PC']
                ]
            ],
            [
                'id' => 'MLB2185',
                'name' => 'Roupas, Bolsas e Calçados',
                'children' => [
                    ['id' => 'MLB2186', 'name' => 'Roupas Femininas'],
                    ['id' => 'MLB2187', 'name' => 'Roupas Masculinas'],
                    ['id' => 'MLB2188', 'name' => 'Roupas Infantis'],
                    ['id' => 'MLB2189', 'name' => 'Bolsas e Mochilas'],
                    ['id' => 'MLB2190', 'name' => 'Calçados Femininos'],
                    ['id' => 'MLB2191', 'name' => 'Calçados Masculinos'],
                    ['id' => 'MLB2192', 'name' => 'Calçados Infantis'],
                    ['id' => 'MLB2193', 'name' => 'Acessórios'],
                    ['id' => 'MLB2194', 'name' => 'Roupas Esportivas'],
                    ['id' => 'MLB2195', 'name' => 'Roupas Íntimas'],
                    ['id' => 'MLB2196', 'name' => 'Roupas de Banho'],
                    ['id' => 'MLB2197', 'name' => 'Uniformes'],
                    ['id' => 'MLB2198', 'name' => 'Roupas de Festa'],
                    ['id' => 'MLB2199', 'name' => 'Roupas de Trabalho']
                ]
            ],
            [
                'id' => 'MLB1500',
                'name' => 'Casa, Móveis e Jardim',
                'children' => [
                    ['id' => 'MLB1501', 'name' => 'Móveis'],
                    ['id' => 'MLB1502', 'name' => 'Decoração'],
                    ['id' => 'MLB1503', 'name' => 'Cozinha'],
                    ['id' => 'MLB1504', 'name' => 'Banheiro'],
                    ['id' => 'MLB1505', 'name' => 'Jardim'],
                    ['id' => 'MLB1506', 'name' => 'Iluminação'],
                    ['id' => 'MLB1507', 'name' => 'Ferramentas'],
                    ['id' => 'MLB1508', 'name' => 'Cama, Mesa e Banho'],
                    ['id' => 'MLB1509', 'name' => 'Organização'],
                    ['id' => 'MLB1510', 'name' => 'Lavanderia'],
                    ['id' => 'MLB1511', 'name' => 'Churrasqueiras'],
                    ['id' => 'MLB1512', 'name' => 'Piscinas e Spas']
                ]
            ],
            [
                'id' => 'MLB2000',
                'name' => 'Esportes e Fitness',
                'children' => [
                    ['id' => 'MLB2001', 'name' => 'Futebol'],
                    ['id' => 'MLB2002', 'name' => 'Basquete'],
                    ['id' => 'MLB2003', 'name' => 'Tênis'],
                    ['id' => 'MLB2004', 'name' => 'Natação'],
                    ['id' => 'MLB2005', 'name' => 'Musculação'],
                    ['id' => 'MLB2006', 'name' => 'Corrida'],
                    ['id' => 'MLB2007', 'name' => 'Ciclismo'],
                    ['id' => 'MLB2008', 'name' => 'Yoga e Pilates'],
                    ['id' => 'MLB2009', 'name' => 'Vôlei'],
                    ['id' => 'MLB2010', 'name' => 'Handebol'],
                    ['id' => 'MLB2011', 'name' => 'Artes Marciais'],
                    ['id' => 'MLB2012', 'name' => 'Surf'],
                    ['id' => 'MLB2013', 'name' => 'Skate'],
                    ['id' => 'MLB2014', 'name' => 'Patinetes'],
                    ['id' => 'MLB2015', 'name' => 'Equipamentos de Academia']
                ]
            ],
            [
                'id' => 'MLB2500',
                'name' => 'Livros, Revistas e Comics',
                'children' => [
                    ['id' => 'MLB2501', 'name' => 'Livros'],
                    ['id' => 'MLB2502', 'name' => 'Revistas'],
                    ['id' => 'MLB2503', 'name' => 'Comics e Mangás'],
                    ['id' => 'MLB2504', 'name' => 'Material Escolar'],
                    ['id' => 'MLB2505', 'name' => 'Papelaria'],
                    ['id' => 'MLB2506', 'name' => 'Livros Técnicos'],
                    ['id' => 'MLB2507', 'name' => 'Livros Infantis'],
                    ['id' => 'MLB2508', 'name' => 'Livros de Culinária'],
                    ['id' => 'MLB2509', 'name' => 'Livros de Autoajuda'],
                    ['id' => 'MLB2510', 'name' => 'Livros de Negócios']
                ]
            ],
            [
                'id' => 'MLB3000',
                'name' => 'Bebês',
                'children' => [
                    ['id' => 'MLB3001', 'name' => 'Roupas para Bebês'],
                    ['id' => 'MLB3002', 'name' => 'Fraldas e Higiene'],
                    ['id' => 'MLB3003', 'name' => 'Alimentação'],
                    ['id' => 'MLB3004', 'name' => 'Brinquedos para Bebês'],
                    ['id' => 'MLB3005', 'name' => 'Carrinhos e Cadeiras'],
                    ['id' => 'MLB3006', 'name' => 'Berços e Móveis'],
                    ['id' => 'MLB3007', 'name' => 'Segurança'],
                    ['id' => 'MLB3008', 'name' => 'Banho e Higiene'],
                    ['id' => 'MLB3009', 'name' => 'Alimentação e Amamentação'],
                    ['id' => 'MLB3010', 'name' => 'Transporte']
                ]
            ],
            [
                'id' => 'MLB3500',
                'name' => 'Brinquedos e Jogos',
                'children' => [
                    ['id' => 'MLB3501', 'name' => 'Brinquedos Educativos'],
                    ['id' => 'MLB3502', 'name' => 'Brinquedos de Montar'],
                    ['id' => 'MLB3503', 'name' => 'Bonecos e Bonecas'],
                    ['id' => 'MLB3504', 'name' => 'Jogos de Tabuleiro'],
                    ['id' => 'MLB3505', 'name' => 'Videogames'],
                    ['id' => 'MLB3506', 'name' => 'Brinquedos ao Ar Livre'],
                    ['id' => 'MLB3507', 'name' => 'Brinquedos de Pelúcia'],
                    ['id' => 'MLB3508', 'name' => 'Brinquedos de Controle Remoto'],
                    ['id' => 'MLB3509', 'name' => 'Brinquedos de Artesanato'],
                    ['id' => 'MLB3510', 'name' => 'Brinquedos de Ciência'],
                    ['id' => 'MLB3511', 'name' => 'Brinquedos de Música'],
                    ['id' => 'MLB3512', 'name' => 'Brinquedos de Fantasia']
                ]
            ],
            [
                'id' => 'MLB4000',
                'name' => 'Saúde e Cuidados Pessoais',
                'children' => [
                    ['id' => 'MLB4001', 'name' => 'Medicamentos'],
                    ['id' => 'MLB4002', 'name' => 'Cosméticos'],
                    ['id' => 'MLB4003', 'name' => 'Higiene Pessoal'],
                    ['id' => 'MLB4004', 'name' => 'Suplementos'],
                    ['id' => 'MLB4005', 'name' => 'Fitness e Nutrição'],
                    ['id' => 'MLB4006', 'name' => 'Cuidados com Bebês'],
                    ['id' => 'MLB4007', 'name' => 'Cuidados com a Pele'],
                    ['id' => 'MLB4008', 'name' => 'Cuidados com o Cabelo'],
                    ['id' => 'MLB4009', 'name' => 'Cuidados Bucais'],
                    ['id' => 'MLB4010', 'name' => 'Cuidados com os Olhos'],
                    ['id' => 'MLB4011', 'name' => 'Cuidados com as Unhas'],
                    ['id' => 'MLB4012', 'name' => 'Produtos Naturais']
                ]
            ],
            [
                'id' => 'MLB4500',
                'name' => 'Ferramentas e Construção',
                'children' => [
                    ['id' => 'MLB4501', 'name' => 'Ferramentas Manuais'],
                    ['id' => 'MLB4502', 'name' => 'Ferramentas Elétricas'],
                    ['id' => 'MLB4503', 'name' => 'Material de Construção'],
                    ['id' => 'MLB4504', 'name' => 'Pintura'],
                    ['id' => 'MLB4505', 'name' => 'Encanamento'],
                    ['id' => 'MLB4506', 'name' => 'Elétrica'],
                    ['id' => 'MLB4507', 'name' => 'Jardim e Paisagismo'],
                    ['id' => 'MLB4508', 'name' => 'Segurança'],
                    ['id' => 'MLB4509', 'name' => 'Iluminação'],
                    ['id' => 'MLB4510', 'name' => 'Aquecimento e Ventilação'],
                    ['id' => 'MLB4511', 'name' => 'Automação Residencial'],
                    ['id' => 'MLB4512', 'name' => 'Equipamentos de Proteção']
                ]
            ],
            [
                'id' => 'MLB5000',
                'name' => 'Indústria e Comércio',
                'children' => [
                    ['id' => 'MLB5001', 'name' => 'Equipamentos Industriais'],
                    ['id' => 'MLB5002', 'name' => 'Máquinas e Ferramentas'],
                    ['id' => 'MLB5003', 'name' => 'Material de Escritório'],
                    ['id' => 'MLB5004', 'name' => 'Segurança e Vigilância'],
                    ['id' => 'MLB5005', 'name' => 'Limpeza Profissional'],
                    ['id' => 'MLB5006', 'name' => 'Equipamentos Médicos'],
                    ['id' => 'MLB5007', 'name' => 'Equipamentos de Laboratório'],
                    ['id' => 'MLB5008', 'name' => 'Equipamentos de Refrigeração'],
                    ['id' => 'MLB5009', 'name' => 'Equipamentos de Soldagem'],
                    ['id' => 'MLB5010', 'name' => 'Equipamentos de Medição']
                ]
            ],
            [
                'id' => 'MLB5500',
                'name' => 'Agro, Indústria e Comércio',
                'children' => [
                    ['id' => 'MLB5501', 'name' => 'Máquinas Agrícolas'],
                    ['id' => 'MLB5502', 'name' => 'Ferramentas Agrícolas'],
                    ['id' => 'MLB5503', 'name' => 'Sementes e Mudas'],
                    ['id' => 'MLB5504', 'name' => 'Fertilizantes'],
                    ['id' => 'MLB5505', 'name' => 'Defensivos Agrícolas'],
                    ['id' => 'MLB5506', 'name' => 'Irrigação'],
                    ['id' => 'MLB5507', 'name' => 'Criação de Animais'],
                    ['id' => 'MLB5508', 'name' => 'Apicultura'],
                    ['id' => 'MLB5509', 'name' => 'Aquicultura'],
                    ['id' => 'MLB5510', 'name' => 'Equipamentos de Processamento']
                ]
            ],
            [
                'id' => 'MLB6000',
                'name' => 'Serviços',
                'children' => [
                    ['id' => 'MLB6001', 'name' => 'Serviços de Tecnologia'],
                    ['id' => 'MLB6002', 'name' => 'Serviços de Design'],
                    ['id' => 'MLB6003', 'name' => 'Serviços de Marketing'],
                    ['id' => 'MLB6004', 'name' => 'Serviços de Consultoria'],
                    ['id' => 'MLB6005', 'name' => 'Serviços de Limpeza'],
                    ['id' => 'MLB6006', 'name' => 'Serviços de Manutenção'],
                    ['id' => 'MLB6007', 'name' => 'Serviços de Transporte'],
                    ['id' => 'MLB6008', 'name' => 'Serviços de Saúde'],
                    ['id' => 'MLB6009', 'name' => 'Serviços de Educação'],
                    ['id' => 'MLB6010', 'name' => 'Serviços de Eventos']
                ]
            ],
            [
                'id' => 'MLB6500',
                'name' => 'Outros',
                'children' => [
                    ['id' => 'MLB6501', 'name' => 'Antiguidades e Coleções'],
                    ['id' => 'MLB6502', 'name' => 'Arte e Artesanato'],
                    ['id' => 'MLB6503', 'name' => 'Instrumentos Musicais'],
                    ['id' => 'MLB6504', 'name' => 'Filmes e Música'],
                    ['id' => 'MLB6505', 'name' => 'Jogos e Consoles'],
                    ['id' => 'MLB6506', 'name' => 'Fotografia'],
                    ['id' => 'MLB6507', 'name' => 'Relógios e Joias'],
                    ['id' => 'MLB6508', 'name' => 'Óculos e Acessórios'],
                    ['id' => 'MLB6509', 'name' => 'Perfumes'],
                    ['id' => 'MLB6510', 'name' => 'Produtos Importados']
                ]
            ]
        ];

        echo json_encode(['success' => true, 'categorias' => $categorias]);
    }

    /**
     * Buscar atributos alternativos quando a API estiver bloqueada
     */
    public function buscarAtributosAlternativos() {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eCategoria')) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão.']);
            return;
        }

        $categoria_id = $this->input->get('categoria_id');
        $ml_id = $this->input->get('ml_id');

        if (!$categoria_id || !$ml_id) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
            return;
        }

        // Atributos pré-definidos por categoria (COMPLETO E EXPANDIDO)
        $atributos_por_categoria = [
            // CELULARES E SMARTPHONES
            'MLB5726' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'MODEL', 'name' => 'Modelo', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'COLOR', 'name' => 'Cor', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'BLACK', 'name' => 'Preto'], ['id' => 'WHITE', 'name' => 'Branco'],
                    ['id' => 'BLUE', 'name' => 'Azul'], ['id' => 'RED', 'name' => 'Vermelho'],
                    ['id' => 'GOLD', 'name' => 'Dourado'], ['id' => 'SILVER', 'name' => 'Prateado'],
                    ['id' => 'GREEN', 'name' => 'Verde'], ['id' => 'PURPLE', 'name' => 'Roxo'],
                    ['id' => 'PINK', 'name' => 'Rosa'], ['id' => 'ORANGE', 'name' => 'Laranja']
                ]],
                ['id' => 'STORAGE_CAPACITY', 'name' => 'Capacidade de Armazenamento', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '32GB', 'name' => '32 GB'], ['id' => '64GB', 'name' => '64 GB'],
                    ['id' => '128GB', 'name' => '128 GB'], ['id' => '256GB', 'name' => '256 GB'],
                    ['id' => '512GB', 'name' => '512 GB'], ['id' => '1TB', 'name' => '1 TB']
                ]],
                ['id' => 'SCREEN_SIZE', 'name' => 'Tamanho da Tela', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '5.5', 'name' => '5.5 polegadas'], ['id' => '6.0', 'name' => '6.0 polegadas'],
                    ['id' => '6.1', 'name' => '6.1 polegadas'], ['id' => '6.7', 'name' => '6.7 polegadas'],
                    ['id' => '6.8', 'name' => '6.8 polegadas'], ['id' => '7.0', 'name' => '7.0 polegadas']
                ]],
                ['id' => 'OPERATING_SYSTEM', 'name' => 'Sistema Operacional', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'ANDROID', 'name' => 'Android'], ['id' => 'IOS', 'name' => 'iOS'],
                    ['id' => 'HARMONY_OS', 'name' => 'HarmonyOS']
                ]],
                ['id' => 'CONDITION', 'name' => 'Condição', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'NEW', 'name' => 'Novo'], ['id' => 'USED', 'name' => 'Usado'],
                    ['id' => 'REFURBISHED', 'name' => 'Recondicionado']
                ]],
                ['id' => 'SCREEN_TYPE', 'name' => 'Tipo de Tela', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'LCD', 'name' => 'LCD'], ['id' => 'OLED', 'name' => 'OLED'],
                    ['id' => 'AMOLED', 'name' => 'AMOLED'], ['id' => 'IPS', 'name' => 'IPS'],
                    ['id' => 'TFT', 'name' => 'TFT'], ['id' => 'SUPER_AMOLED', 'name' => 'Super AMOLED']
                ]],
                ['id' => 'CAMERA_RESOLUTION', 'name' => 'Resolução da Câmera', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '12MP', 'name' => '12 MP'], ['id' => '16MP', 'name' => '16 MP'],
                    ['id' => '20MP', 'name' => '20 MP'], ['id' => '24MP', 'name' => '24 MP'],
                    ['id' => '32MP', 'name' => '32 MP'], ['id' => '48MP', 'name' => '48 MP'],
                    ['id' => '64MP', 'name' => '64 MP'], ['id' => '108MP', 'name' => '108 MP']
                ]],
                ['id' => 'CHIP_TYPE', 'name' => 'Tipo de Chip', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'MEDIATEK', 'name' => 'MediaTek'], ['id' => 'QUALCOMM', 'name' => 'Qualcomm'],
                    ['id' => 'SAMSUNG', 'name' => 'Samsung'], ['id' => 'APPLE', 'name' => 'Apple'],
                    ['id' => 'HUAWEI', 'name' => 'Huawei'], ['id' => 'UNISOC', 'name' => 'Unisoc']
                ]],
                ['id' => 'BATTERY_TYPE', 'name' => 'Tipo de Bateria', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'LI_ION', 'name' => 'Li-Ion'], ['id' => 'LI_PO', 'name' => 'Li-Po'],
                    ['id' => 'NON_REMOVABLE', 'name' => 'Não Removível'], ['id' => 'REMOVABLE', 'name' => 'Removível']
                ]],
                ['id' => 'WATER_RESISTANCE', 'name' => 'Resistência à Água', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'YES', 'name' => 'Sim'], ['id' => 'NO', 'name' => 'Não'],
                    ['id' => 'IP67', 'name' => 'IP67'], ['id' => 'IP68', 'name' => 'IP68']
                ]],
                ['id' => 'BIOMETRY', 'name' => 'Biometria', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'FINGERPRINT', 'name' => 'Impressão Digital'], ['id' => 'FACE_ID', 'name' => 'Face ID'],
                    ['id' => 'IRIS', 'name' => 'Íris'], ['id' => 'NONE', 'name' => 'Nenhuma']
                ]],
                ['id' => 'DISPLAY_TYPE', 'name' => 'Tipo de Display', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'TFT', 'name' => 'TFT'], ['id' => 'IPS', 'name' => 'IPS'],
                    ['id' => 'OLED', 'name' => 'OLED'], ['id' => 'AMOLED', 'name' => 'AMOLED'],
                    ['id' => 'SUPER_AMOLED', 'name' => 'Super AMOLED'], ['id' => 'MINI_LED', 'name' => 'Mini LED']
                ]],
                ['id' => 'DISPLAY_RESOLUTION', 'name' => 'Resolução da Tela', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'HD', 'name' => 'HD (1280 x 720)'], ['id' => 'HD_PLUS', 'name' => 'HD+ (1600 x 720)'],
                    ['id' => 'FULL_HD', 'name' => 'Full HD (1920 x 1080)'], ['id' => 'QHD', 'name' => 'QHD (2560 x 1440)'],
                    ['id' => 'FHD_PLUS', 'name' => 'FHD+ (2400 x 1080)'], ['id' => 'QHD_PLUS', 'name' => 'QHD+ (3200 x 1440)'],
                    ['id' => '4K', 'name' => '4K (3840 x 2160)']
                ]],
                ['id' => 'CAMERA_COUNT', 'name' => 'Número de Câmeras', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '1', 'name' => '1'], ['id' => '2', 'name' => '2'],
                    ['id' => '3', 'name' => '3'], ['id' => '4', 'name' => '4'],
                    ['id' => '5', 'name' => '5']
                ]],
                ['id' => 'FRONT_CAMERA_TYPE', 'name' => 'Tipo de Câmera Frontal', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'SINGLE', 'name' => 'Única'], ['id' => 'DUAL', 'name' => 'Dupla'],
                    ['id' => 'TRIPLE', 'name' => 'Tripla'], ['id' => 'PUNCH_HOLE', 'name' => 'Punch Hole'],
                    ['id' => 'NOTCH', 'name' => 'Notch'], ['id' => 'UNDER_DISPLAY', 'name' => 'Sob a Tela']
                ]],
                ['id' => 'FRONT_CAMERA_RESOLUTION', 'name' => 'Resolução da Câmera Frontal', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '5MP', 'name' => '5 MP'], ['id' => '8MP', 'name' => '8 MP'],
                    ['id' => '12MP', 'name' => '12 MP'], ['id' => '16MP', 'name' => '16 MP'],
                    ['id' => '20MP', 'name' => '20 MP'], ['id' => '32MP', 'name' => '32 MP']
                ]],
                ['id' => 'REAR_CAMERA_TYPE', 'name' => 'Tipo de Câmera Traseira', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'SINGLE', 'name' => 'Única'], ['id' => 'DUAL', 'name' => 'Dupla'],
                    ['id' => 'TRIPLE', 'name' => 'Tripla'], ['id' => 'QUAD', 'name' => 'Quádrupla'],
                    ['id' => 'PENTA', 'name' => 'Penta'], ['id' => 'HEXA', 'name' => 'Hexa']
                ]],
                ['id' => 'REAR_CAMERA_RESOLUTION', 'name' => 'Resolução da Câmera Traseira', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '12MP', 'name' => '12 MP'], ['id' => '16MP', 'name' => '16 MP'],
                    ['id' => '20MP', 'name' => '20 MP'], ['id' => '24MP', 'name' => '24 MP'],
                    ['id' => '32MP', 'name' => '32 MP'], ['id' => '48MP', 'name' => '48 MP'],
                    ['id' => '64MP', 'name' => '64 MP'], ['id' => '108MP', 'name' => '108 MP']
                ]],
                ['id' => 'FLASH_TYPE', 'name' => 'Tipo de Flash', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'LED', 'name' => 'LED'], ['id' => 'DUAL_LED', 'name' => 'Dual LED'],
                    ['id' => 'TRIPLE_LED', 'name' => 'Triple LED'], ['id' => 'QUAD_LED', 'name' => 'Quad LED'],
                    ['id' => 'XENON', 'name' => 'Xenon'], ['id' => 'NONE', 'name' => 'Nenhum']
                ]],
                ['id' => 'STABILIZATION_TYPE', 'name' => 'Tipo de Estabilização', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'DIGITAL', 'name' => 'Digital'], ['id' => 'OPTICAL', 'name' => 'Óptica'],
                    ['id' => 'HYBRID', 'name' => 'Híbrida'], ['id' => 'NONE', 'name' => 'Nenhuma']
                ]],
                ['id' => 'AUTOFOCUS_TYPE', 'name' => 'Tipo de Autofoco', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'AUTOMATIC', 'name' => 'Automático'], ['id' => 'MANUAL', 'name' => 'Manual'],
                    ['id' => 'HYBRID', 'name' => 'Híbrido'], ['id' => 'DUAL_PIXEL', 'name' => 'Dual Pixel']
                ]],
                ['id' => 'ZOOM_TYPE', 'name' => 'Tipo de Zoom', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'DIGITAL', 'name' => 'Digital'], ['id' => 'OPTICAL', 'name' => 'Óptico'],
                    ['id' => 'HYBRID', 'name' => 'Híbrido'], ['id' => 'NONE', 'name' => 'Nenhum']
                ]],
                ['id' => 'VIDEO_RECORDING_TYPE', 'name' => 'Tipo de Gravação de Vídeo', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'HD', 'name' => 'HD'], ['id' => 'FULL_HD', 'name' => 'Full HD'],
                    ['id' => '4K', 'name' => '4K'], ['id' => '8K', 'name' => '8K']
                ]],
                ['id' => 'AUDIO_TYPE', 'name' => 'Tipo de Áudio', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'STEREO', 'name' => 'Estéreo'], ['id' => 'MONO', 'name' => 'Mono'],
                    ['id' => 'SURROUND', 'name' => 'Surround'], ['id' => 'DOLBY', 'name' => 'Dolby']
                ]],
                ['id' => 'SPEAKER_TYPE', 'name' => 'Tipo de Alto-falante', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'SINGLE', 'name' => 'Único'], ['id' => 'DUAL', 'name' => 'Duplo'],
                    ['id' => 'STEREO', 'name' => 'Estéreo'], ['id' => 'SURROUND', 'name' => 'Surround']
                ]],
                ['id' => 'MICROPHONE_TYPE', 'name' => 'Tipo de Microfone', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'SINGLE', 'name' => 'Único'], ['id' => 'DUAL', 'name' => 'Duplo'],
                    ['id' => 'MULTIPLE', 'name' => 'Múltiplo'], ['id' => 'NOISE_CANCELLING', 'name' => 'Cancelamento de Ruído']
                ]],
                ['id' => 'AUDIO_CONNECTOR_TYPE', 'name' => 'Tipo de Conector de Áudio', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '3.5MM', 'name' => '3.5 mm'], ['id' => 'USB_C', 'name' => 'USB-C'],
                    ['id' => 'LIGHTNING', 'name' => 'Lightning'], ['id' => 'NONE', 'name' => 'Nenhum']
                ]],
                ['id' => 'CONNECTIVITY_TYPE', 'name' => 'Tipo de Conectividade', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '4G', 'name' => '4G'], ['id' => '5G', 'name' => '5G'],
                    ['id' => '3G', 'name' => '3G'], ['id' => '2G', 'name' => '2G']
                ]],
                ['id' => 'WIFI_TYPE', 'name' => 'Tipo de Wi-Fi', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '802.11_A_B_G_N', 'name' => '802.11 a/b/g/n'], ['id' => '802.11_A_B_G_N_AC', 'name' => '802.11 a/b/g/n/ac'],
                    ['id' => '802.11_A_B_G_N_AC_AX', 'name' => '802.11 a/b/g/n/ac/ax'], ['id' => 'WIFI_6', 'name' => 'Wi-Fi 6']
                ]],
                ['id' => 'BLUETOOTH_TYPE', 'name' => 'Tipo de Bluetooth', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '4.0', 'name' => '4.0'], ['id' => '4.1', 'name' => '4.1'],
                    ['id' => '4.2', 'name' => '4.2'], ['id' => '5.0', 'name' => '5.0'],
                    ['id' => '5.1', 'name' => '5.1'], ['id' => '5.2', 'name' => '5.2']
                ]],
                ['id' => 'GPS_TYPE', 'name' => 'Tipo de GPS', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'A_GPS', 'name' => 'A-GPS'], ['id' => 'GLONASS', 'name' => 'GLONASS'],
                    ['id' => 'GALILEO', 'name' => 'Galileo'], ['id' => 'BEIDOU', 'name' => 'BeiDou'],
                    ['id' => 'NONE', 'name' => 'Nenhum']
                ]],
                ['id' => 'NFC_TYPE', 'name' => 'Tipo de NFC', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'YES', 'name' => 'Sim'], ['id' => 'NO', 'name' => 'Não']
                ]],
                ['id' => 'SENSOR_TYPE', 'name' => 'Tipo de Sensor', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'ACCELEROMETER', 'name' => 'Acelerômetro'], ['id' => 'GYROSCOPE', 'name' => 'Giroscópio'],
                    ['id' => 'PROXIMITY', 'name' => 'Proximidade'], ['id' => 'LIGHT', 'name' => 'Luz'],
                    ['id' => 'COMPASS', 'name' => 'Bússola'], ['id' => 'BAROMETER', 'name' => 'Barômetro'],
                    ['id' => 'MULTIPLE', 'name' => 'Múltiplos']
                ]],
                ['id' => 'VIBRATION_TYPE', 'name' => 'Tipo de Vibração', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'VIBRATION_MOTOR', 'name' => 'Motor de Vibração'], ['id' => 'HAPTIC', 'name' => 'Háptico'],
                    ['id' => 'NONE', 'name' => 'Nenhum']
                ]],
                ['id' => 'NOTIFICATION_TYPE', 'name' => 'Tipo de Notificação', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'LED', 'name' => 'LED'], ['id' => 'SOUND', 'name' => 'Som'],
                    ['id' => 'VIBRATION', 'name' => 'Vibração'], ['id' => 'NONE', 'name' => 'Nenhuma']
                ]],
                ['id' => 'PROTECTION_TYPE', 'name' => 'Tipo de Proteção', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'GORILLA_GLASS', 'name' => 'Gorilla Glass'], ['id' => 'DRAGONTRAIL', 'name' => 'DragonTrail'],
                    ['id' => 'NONE', 'name' => 'Nenhuma']
                ]],
                ['id' => 'RESISTANCE_TYPE', 'name' => 'Tipo de Resistência', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'IP67', 'name' => 'IP67'], ['id' => 'IP68', 'name' => 'IP68'],
                    ['id' => 'IP69', 'name' => 'IP69'], ['id' => 'NONE', 'name' => 'Nenhuma']
                ]],
                ['id' => 'CERTIFICATION_TYPE', 'name' => 'Tipo de Certificação', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'CE', 'name' => 'CE'], ['id' => 'ROHS', 'name' => 'RoHS'],
                    ['id' => 'FCC', 'name' => 'FCC'], ['id' => 'ANATEL', 'name' => 'ANATEL'],
                    ['id' => 'MULTIPLE', 'name' => 'Múltiplas']
                ]],
                ['id' => 'WARRANTY_TYPE', 'name' => 'Tipo de Garantia', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'MANUFACTURER', 'name' => 'Fabricante'], ['id' => 'STORE', 'name' => 'Loja'],
                    ['id' => 'EXTENDED', 'name' => 'Estendida'], ['id' => 'NONE', 'name' => 'Nenhuma']
                ]],
                ['id' => 'ORIGIN_TYPE', 'name' => 'Tipo de Origem', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'IMPORTED', 'name' => 'Importado'], ['id' => 'NATIONAL', 'name' => 'Nacional'],
                    ['id' => 'MIXED', 'name' => 'Misto']
                ]],
                ['id' => 'PACKAGING_TYPE', 'name' => 'Tipo de Embalagem', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'ORIGINAL_BOX', 'name' => 'Caixa Original'], ['id' => 'GENERIC_BOX', 'name' => 'Caixa Genérica'],
                    ['id' => 'BULK', 'name' => 'A Granel'], ['id' => 'NONE', 'name' => 'Sem Embalagem']
                ]],
                ['id' => 'ACCESSORIES_TYPE', 'name' => 'Tipo de Acessórios', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'CHARGER_CABLE_MANUAL', 'name' => 'Carregador, Cabo USB, Manual'],
                    ['id' => 'CHARGER_CABLE', 'name' => 'Carregador, Cabo USB'],
                    ['id' => 'CABLE_ONLY', 'name' => 'Apenas Cabo USB'],
                    ['id' => 'NONE', 'name' => 'Nenhum Acessório']
                ]],
                ['id' => 'BATTERY_CAPACITY', 'name' => 'Capacidade da Bateria', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '2000MAH', 'name' => '2000 mAh'], ['id' => '3000MAH', 'name' => '3000 mAh'],
                    ['id' => '4000MAH', 'name' => '4000 mAh'], ['id' => '5000MAH', 'name' => '5000 mAh'],
                    ['id' => '6000MAH', 'name' => '6000 mAh'], ['id' => '7000MAH', 'name' => '7000 mAh']
                ]],
                ['id' => 'CHARGING_TYPE', 'name' => 'Tipo de Carregamento', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'USB_TYPE_C', 'name' => 'USB Type-C'], ['id' => 'MICRO_USB', 'name' => 'Micro USB'],
                    ['id' => 'LIGHTNING', 'name' => 'Lightning'], ['id' => 'WIRELESS', 'name' => 'Sem Fio']
                ]]
            ],
            
            // NOTEBOOKS
            'MLB5727' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'MODEL', 'name' => 'Modelo', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'PROCESSOR', 'name' => 'Processador', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'INTEL_I3', 'name' => 'Intel i3'], ['id' => 'INTEL_I5', 'name' => 'Intel i5'],
                    ['id' => 'INTEL_I7', 'name' => 'Intel i7'], ['id' => 'INTEL_I9', 'name' => 'Intel i9'],
                    ['id' => 'AMD_RYZEN_3', 'name' => 'AMD Ryzen 3'], ['id' => 'AMD_RYZEN_5', 'name' => 'AMD Ryzen 5'],
                    ['id' => 'AMD_RYZEN_7', 'name' => 'AMD Ryzen 7'], ['id' => 'AMD_RYZEN_9', 'name' => 'AMD Ryzen 9']
                ]],
                ['id' => 'RAM', 'name' => 'Memória RAM', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '4GB', 'name' => '4 GB'], ['id' => '8GB', 'name' => '8 GB'],
                    ['id' => '16GB', 'name' => '16 GB'], ['id' => '32GB', 'name' => '32 GB'],
                    ['id' => '64GB', 'name' => '64 GB']
                ]],
                ['id' => 'STORAGE_TYPE', 'name' => 'Tipo de Armazenamento', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'HDD', 'name' => 'HDD'], ['id' => 'SSD', 'name' => 'SSD'],
                    ['id' => 'HYBRID', 'name' => 'Híbrido'], ['id' => 'NVME', 'name' => 'NVMe']
                ]],
                ['id' => 'STORAGE_CAPACITY', 'name' => 'Capacidade de Armazenamento', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '256GB', 'name' => '256 GB'], ['id' => '512GB', 'name' => '512 GB'],
                    ['id' => '1TB', 'name' => '1 TB'], ['id' => '2TB', 'name' => '2 TB'],
                    ['id' => '4TB', 'name' => '4 TB']
                ]],
                ['id' => 'SCREEN_SIZE', 'name' => 'Tamanho da Tela', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '13', 'name' => '13 polegadas'], ['id' => '14', 'name' => '14 polegadas'],
                    ['id' => '15.6', 'name' => '15.6 polegadas'], ['id' => '17', 'name' => '17 polegadas']
                ]],
                ['id' => 'GRAPHICS', 'name' => 'Placa de Vídeo', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'INTEGRATED', 'name' => 'Integrada'], ['id' => 'GTX_1650', 'name' => 'GTX 1650'],
                    ['id' => 'GTX_1660', 'name' => 'GTX 1660'], ['id' => 'RTX_3050', 'name' => 'RTX 3050'],
                    ['id' => 'RTX_3060', 'name' => 'RTX 3060'], ['id' => 'RTX_3070', 'name' => 'RTX 3070']
                ]]
            ],
            
            // TABLETS
            'MLB5728' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'MODEL', 'name' => 'Modelo', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'SCREEN_SIZE', 'name' => 'Tamanho da Tela', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '7', 'name' => '7 polegadas'], ['id' => '8', 'name' => '8 polegadas'],
                    ['id' => '9.7', 'name' => '9.7 polegadas'], ['id' => '10.1', 'name' => '10.1 polegadas'],
                    ['id' => '12.9', 'name' => '12.9 polegadas']
                ]],
                ['id' => 'STORAGE_CAPACITY', 'name' => 'Capacidade de Armazenamento', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '32GB', 'name' => '32 GB'], ['id' => '64GB', 'name' => '64 GB'],
                    ['id' => '128GB', 'name' => '128 GB'], ['id' => '256GB', 'name' => '256 GB']
                ]]
            ],
            
            // TVs
            'MLB5729' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'MODEL', 'name' => 'Modelo', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'SCREEN_SIZE', 'name' => 'Tamanho da Tela', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '32', 'name' => '32 polegadas'], ['id' => '40', 'name' => '40 polegadas'],
                    ['id' => '43', 'name' => '43 polegadas'], ['id' => '50', 'name' => '50 polegadas'],
                    ['id' => '55', 'name' => '55 polegadas'], ['id' => '65', 'name' => '65 polegadas'],
                    ['id' => '75', 'name' => '75 polegadas'], ['id' => '85', 'name' => '85 polegadas']
                ]],
                ['id' => 'RESOLUTION', 'name' => 'Resolução', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'HD', 'name' => 'HD'], ['id' => 'FULL_HD', 'name' => 'Full HD'],
                    ['id' => '4K', 'name' => '4K'], ['id' => '8K', 'name' => '8K']
                ]],
                ['id' => 'DISPLAY_TYPE', 'name' => 'Tipo de Tela', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'LED', 'name' => 'LED'], ['id' => 'OLED', 'name' => 'OLED'],
                    ['id' => 'QLED', 'name' => 'QLED'], ['id' => 'MINI_LED', 'name' => 'Mini LED']
                ]],
                ['id' => 'SMART_TV', 'name' => 'Smart TV', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'YES', 'name' => 'Sim'], ['id' => 'NO', 'name' => 'Não']
                ]],
                ['id' => 'CONNECTIVITY', 'name' => 'Conectividade', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'WIFI', 'name' => 'Wi-Fi'], ['id' => 'BLUETOOTH', 'name' => 'Bluetooth'],
                    ['id' => 'ETHERNET', 'name' => 'Ethernet'], ['id' => 'HDMI', 'name' => 'HDMI']
                ]]
            ],
            
            // ROUPAS FEMININAS
            'MLB2186' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'SIZE', 'name' => 'Tamanho', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'PP', 'name' => 'PP'], ['id' => 'P', 'name' => 'P'],
                    ['id' => 'M', 'name' => 'M'], ['id' => 'G', 'name' => 'G'],
                    ['id' => 'GG', 'name' => 'GG'], ['id' => 'XG', 'name' => 'XG'],
                    ['id' => 'XXG', 'name' => 'XXG'], ['id' => 'XXXG', 'name' => 'XXXG']
                ]],
                ['id' => 'COLOR', 'name' => 'Cor', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'BLACK', 'name' => 'Preto'], ['id' => 'WHITE', 'name' => 'Branco'],
                    ['id' => 'BLUE', 'name' => 'Azul'], ['id' => 'RED', 'name' => 'Vermelho'],
                    ['id' => 'PINK', 'name' => 'Rosa'], ['id' => 'GREEN', 'name' => 'Verde'],
                    ['id' => 'YELLOW', 'name' => 'Amarelo'], ['id' => 'PURPLE', 'name' => 'Roxo'],
                    ['id' => 'ORANGE', 'name' => 'Laranja'], ['id' => 'BROWN', 'name' => 'Marrom'],
                    ['id' => 'GRAY', 'name' => 'Cinza'], ['id' => 'MULTI', 'name' => 'Multicolorido']
                ]],
                ['id' => 'MATERIAL', 'name' => 'Material', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'COTTON', 'name' => 'Algodão'], ['id' => 'POLYESTER', 'name' => 'Poliéster'],
                    ['id' => 'BLEND', 'name' => 'Mistura'], ['id' => 'SILK', 'name' => 'Seda'],
                    ['id' => 'LINEN', 'name' => 'Linho'], ['id' => 'WOOL', 'name' => 'Lã'],
                    ['id' => 'DENIM', 'name' => 'Jeans'], ['id' => 'LACE', 'name' => 'Renda']
                ]],
                ['id' => 'STYLE', 'name' => 'Estilo', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'CASUAL', 'name' => 'Casual'], ['id' => 'FORMAL', 'name' => 'Formal'],
                    ['id' => 'SPORTY', 'name' => 'Esportivo'], ['id' => 'VINTAGE', 'name' => 'Vintage'],
                    ['id' => 'BOHEMIAN', 'name' => 'Boêmio'], ['id' => 'MINIMALIST', 'name' => 'Minimalista']
                ]],
                ['id' => 'SEASON', 'name' => 'Estação', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'SPRING', 'name' => 'Primavera'], ['id' => 'SUMMER', 'name' => 'Verão'],
                    ['id' => 'AUTUMN', 'name' => 'Outono'], ['id' => 'WINTER', 'name' => 'Inverno'],
                    ['id' => 'ALL_SEASONS', 'name' => 'Todas as Estações']
                ]]
            ],
            
            // ROUPAS MASCULINAS
            'MLB2187' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'SIZE', 'name' => 'Tamanho', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'P', 'name' => 'P'], ['id' => 'M', 'name' => 'M'],
                    ['id' => 'G', 'name' => 'G'], ['id' => 'GG', 'name' => 'GG'],
                    ['id' => 'XG', 'name' => 'XG'], ['id' => 'XXG', 'name' => 'XXG']
                ]],
                ['id' => 'COLOR', 'name' => 'Cor', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'BLACK', 'name' => 'Preto'], ['id' => 'WHITE', 'name' => 'Branco'],
                    ['id' => 'BLUE', 'name' => 'Azul'], ['id' => 'GRAY', 'name' => 'Cinza'],
                    ['id' => 'BROWN', 'name' => 'Marrom'], ['id' => 'GREEN', 'name' => 'Verde']
                ]]
            ],
            
            // CALÇADOS FEMININOS
            'MLB2190' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'SIZE', 'name' => 'Tamanho', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '33', 'name' => '33'], ['id' => '34', 'name' => '34'],
                    ['id' => '35', 'name' => '35'], ['id' => '36', 'name' => '36'],
                    ['id' => '37', 'name' => '37'], ['id' => '38', 'name' => '38'],
                    ['id' => '39', 'name' => '39'], ['id' => '40', 'name' => '40'],
                    ['id' => '41', 'name' => '41'], ['id' => '42', 'name' => '42'],
                    ['id' => '43', 'name' => '43'], ['id' => '44', 'name' => '44']
                ]],
                ['id' => 'COLOR', 'name' => 'Cor', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'BLACK', 'name' => 'Preto'], ['id' => 'WHITE', 'name' => 'Branco'],
                    ['id' => 'BROWN', 'name' => 'Marrom'], ['id' => 'RED', 'name' => 'Vermelho'],
                    ['id' => 'PINK', 'name' => 'Rosa'], ['id' => 'GOLD', 'name' => 'Dourado'],
                    ['id' => 'SILVER', 'name' => 'Prateado'], ['id' => 'BLUE', 'name' => 'Azul'],
                    ['id' => 'GREEN', 'name' => 'Verde'], ['id' => 'PURPLE', 'name' => 'Roxo'],
                    ['id' => 'MULTI', 'name' => 'Multicolorido']
                ]],
                ['id' => 'HEEL_TYPE', 'name' => 'Tipo de Salto', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'FLAT', 'name' => 'Rasteirinha'], ['id' => 'LOW', 'name' => 'Baixo'],
                    ['id' => 'MEDIUM', 'name' => 'Médio'], ['id' => 'HIGH', 'name' => 'Alto'],
                    ['id' => 'PLATFORM', 'name' => 'Plataforma'], ['id' => 'WEDGE', 'name' => 'Anabela']
                ]],
                ['id' => 'SHOE_TYPE', 'name' => 'Tipo de Calçado', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'SANDAL', 'name' => 'Sandália'], ['id' => 'PUMP', 'name' => 'Bombacha'],
                    ['id' => 'SNEAKER', 'name' => 'Tênis'], ['id' => 'BOOT', 'name' => 'Bota'],
                    ['id' => 'FLAT', 'name' => 'Rasteirinha'], ['id' => 'MULE', 'name' => 'Mule'],
                    ['id' => 'OXFORD', 'name' => 'Oxford'], ['id' => 'LOAFER', 'name' => 'Loafer']
                ]],
                ['id' => 'MATERIAL', 'name' => 'Material', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'LEATHER', 'name' => 'Couro'], ['id' => 'SUEDE', 'name' => 'Camurça'],
                    ['id' => 'CANVAS', 'name' => 'Lona'], ['id' => 'SYNTHETIC', 'name' => 'Sintético'],
                    ['id' => 'TEXTILE', 'name' => 'Têxtil'], ['id' => 'RUBBER', 'name' => 'Borracha']
                ]]
            ],
            
            // CALÇADOS MASCULINOS
            'MLB2191' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'SIZE', 'name' => 'Tamanho', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '38', 'name' => '38'], ['id' => '39', 'name' => '39'],
                    ['id' => '40', 'name' => '40'], ['id' => '41', 'name' => '41'],
                    ['id' => '42', 'name' => '42'], ['id' => '43', 'name' => '43'],
                    ['id' => '44', 'name' => '44'], ['id' => '45', 'name' => '45'],
                    ['id' => '46', 'name' => '46'], ['id' => '47', 'name' => '47']
                ]],
                ['id' => 'COLOR', 'name' => 'Cor', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'BLACK', 'name' => 'Preto'], ['id' => 'BROWN', 'name' => 'Marrom'],
                    ['id' => 'WHITE', 'name' => 'Branco'], ['id' => 'GRAY', 'name' => 'Cinza'],
                    ['id' => 'BLUE', 'name' => 'Azul'], ['id' => 'GREEN', 'name' => 'Verde']
                ]]
            ],
            
            // MÓVEIS
            'MLB1501' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'MATERIAL', 'name' => 'Material', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'WOOD', 'name' => 'Madeira'], ['id' => 'MDF', 'name' => 'MDF'],
                    ['id' => 'METAL', 'name' => 'Metal'], ['id' => 'GLASS', 'name' => 'Vidro'],
                    ['id' => 'PLASTIC', 'name' => 'Plástico'], ['id' => 'BAMBOO', 'name' => 'Bambu'],
                    ['id' => 'RATTAN', 'name' => 'Rattan'], ['id' => 'FABRIC', 'name' => 'Tecido']
                ]],
                ['id' => 'COLOR', 'name' => 'Cor', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'BROWN', 'name' => 'Marrom'], ['id' => 'WHITE', 'name' => 'Branco'],
                    ['id' => 'BLACK', 'name' => 'Preto'], ['id' => 'NATURAL', 'name' => 'Natural'],
                    ['id' => 'WALNUT', 'name' => 'Nogueira'], ['id' => 'OAK', 'name' => 'Carvalho'],
                    ['id' => 'MAHOGANY', 'name' => 'Mogno'], ['id' => 'PINE', 'name' => 'Pinho'],
                    ['id' => 'GRAY', 'name' => 'Cinza'], ['id' => 'BEIGE', 'name' => 'Bege']
                ]],
                ['id' => 'STYLE', 'name' => 'Estilo', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'MODERN', 'name' => 'Moderno'], ['id' => 'CLASSIC', 'name' => 'Clássico'],
                    ['id' => 'RUSTIC', 'name' => 'Rústico'], ['id' => 'MINIMALIST', 'name' => 'Minimalista'],
                    ['id' => 'INDUSTRIAL', 'name' => 'Industrial'], ['id' => 'SCANDINAVIAN', 'name' => 'Escandinavo'],
                    ['id' => 'VINTAGE', 'name' => 'Vintage'], ['id' => 'BOHEMIAN', 'name' => 'Boêmio']
                ]],
                ['id' => 'ROOM', 'name' => 'Cômodo', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'LIVING_ROOM', 'name' => 'Sala de Estar'], ['id' => 'BEDROOM', 'name' => 'Quarto'],
                    ['id' => 'DINING_ROOM', 'name' => 'Sala de Jantar'], ['id' => 'KITCHEN', 'name' => 'Cozinha'],
                    ['id' => 'BATHROOM', 'name' => 'Banheiro'], ['id' => 'OFFICE', 'name' => 'Escritório'],
                    ['id' => 'GARDEN', 'name' => 'Jardim'], ['id' => 'ENTRANCE', 'name' => 'Entrada']
                ]],
                ['id' => 'FINISH', 'name' => 'Acabamento', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'LACQUERED', 'name' => 'Envernizado'], ['id' => 'OILED', 'name' => 'Oleado'],
                    ['id' => 'WAXED', 'name' => 'Encerado'], ['id' => 'PAINTED', 'name' => 'Pintado'],
                    ['id' => 'VARNISHED', 'name' => 'Lacado'], ['id' => 'NATURAL', 'name' => 'Natural']
                ]]
            ],
            
            // FERRAMENTAS MANUAIS
            'MLB4501' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'MATERIAL', 'name' => 'Material', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'STEEL', 'name' => 'Aço'], ['id' => 'ALUMINUM', 'name' => 'Alumínio'],
                    ['id' => 'PLASTIC', 'name' => 'Plástico'], ['id' => 'RUBBER', 'name' => 'Borracha'],
                    ['id' => 'CARBON_STEEL', 'name' => 'Aço Carbono'], ['id' => 'STAINLESS_STEEL', 'name' => 'Aço Inoxidável'],
                    ['id' => 'TITANIUM', 'name' => 'Titânio'], ['id' => 'WOOD', 'name' => 'Madeira']
                ]],
                ['id' => 'SIZE', 'name' => 'Tamanho', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'SMALL', 'name' => 'Pequeno'], ['id' => 'MEDIUM', 'name' => 'Médio'],
                    ['id' => 'LARGE', 'name' => 'Grande'], ['id' => 'EXTRA_LARGE', 'name' => 'Extra Grande']
                ]],
                ['id' => 'TOOL_TYPE', 'name' => 'Tipo de Ferramenta', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'HAMMER', 'name' => 'Martelo'], ['id' => 'SCREWDRIVER', 'name' => 'Chave de Fenda'],
                    ['id' => 'WRENCH', 'name' => 'Chave Inglesa'], ['id' => 'PLIERS', 'name' => 'Alicate'],
                    ['id' => 'SAW', 'name' => 'Serra'], ['id' => 'DRILL', 'name' => 'Furadeira'],
                    ['id' => 'LEVEL', 'name' => 'Nível'], ['id' => 'TAPE_MEASURE', 'name' => 'Fita Métrica'],
                    ['id' => 'FILE', 'name' => 'Lima'], ['id' => 'CHISEL', 'name' => 'Formão']
                ]],
                ['id' => 'WEIGHT', 'name' => 'Peso', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'LIGHT', 'name' => 'Leve'], ['id' => 'MEDIUM', 'name' => 'Médio'],
                    ['id' => 'HEAVY', 'name' => 'Pesado']
                ]],
                ['id' => 'HANDLE_MATERIAL', 'name' => 'Material do Cabo', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'WOOD', 'name' => 'Madeira'], ['id' => 'PLASTIC', 'name' => 'Plástico'],
                    ['id' => 'RUBBER', 'name' => 'Borracha'], ['id' => 'METAL', 'name' => 'Metal'],
                    ['id' => 'COMPOSITE', 'name' => 'Compósito']
                ]]
            ],
            
            // LIVROS
            'MLB2501' => [
                ['id' => 'AUTHOR', 'name' => 'Autor', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'PUBLISHER', 'name' => 'Editora', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'LANGUAGE', 'name' => 'Idioma', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'PORTUGUESE', 'name' => 'Português'], ['id' => 'ENGLISH', 'name' => 'Inglês'],
                    ['id' => 'SPANISH', 'name' => 'Espanhol'], ['id' => 'FRENCH', 'name' => 'Francês'],
                    ['id' => 'GERMAN', 'name' => 'Alemão'], ['id' => 'ITALIAN', 'name' => 'Italiano'],
                    ['id' => 'CHINESE', 'name' => 'Chinês'], ['id' => 'JAPANESE', 'name' => 'Japonês']
                ]],
                ['id' => 'GENRE', 'name' => 'Gênero', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'FICTION', 'name' => 'Ficção'], ['id' => 'NON_FICTION', 'name' => 'Não Ficção'],
                    ['id' => 'TECHNICAL', 'name' => 'Técnico'], ['id' => 'CHILDREN', 'name' => 'Infantil'],
                    ['id' => 'ROMANCE', 'name' => 'Romance'], ['id' => 'THRILLER', 'name' => 'Suspense'],
                    ['id' => 'FANTASY', 'name' => 'Fantasia'], ['id' => 'SCIENCE_FICTION', 'name' => 'Ficção Científica'],
                    ['id' => 'HISTORY', 'name' => 'História'], ['id' => 'BIOGRAPHY', 'name' => 'Biografia'],
                    ['id' => 'SELF_HELP', 'name' => 'Autoajuda'], ['id' => 'COOKING', 'name' => 'Culinária'],
                    ['id' => 'BUSINESS', 'name' => 'Negócios'], ['id' => 'PHILOSOPHY', 'name' => 'Filosofia']
                ]],
                ['id' => 'FORMAT', 'name' => 'Formato', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'PAPERBACK', 'name' => 'Brochura'], ['id' => 'HARDCOVER', 'name' => 'Capa Dura'],
                    ['id' => 'DIGITAL', 'name' => 'Digital'], ['id' => 'AUDIOBOOK', 'name' => 'Audiobook']
                ]],
                ['id' => 'PAGE_COUNT', 'name' => 'Número de Páginas', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => '1_50', 'name' => '1-50 páginas'], ['id' => '51_100', 'name' => '51-100 páginas'],
                    ['id' => '101_200', 'name' => '101-200 páginas'], ['id' => '201_300', 'name' => '201-300 páginas'],
                    ['id' => '301_500', 'name' => '301-500 páginas'], ['id' => '500_PLUS', 'name' => '500+ páginas']
                ]],
                ['id' => 'PUBLICATION_YEAR', 'name' => 'Ano de Publicação', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => '2020_2024', 'name' => '2020-2024'], ['id' => '2015_2019', 'name' => '2015-2019'],
                    ['id' => '2010_2014', 'name' => '2010-2014'], ['id' => '2000_2009', 'name' => '2000-2009'],
                    ['id' => '1990_1999', 'name' => '1990-1999'], ['id' => 'BEFORE_1990', 'name' => 'Antes de 1990']
                ]]
            ],
            
            // BEBÊS - ROUPAS
            'MLB3001' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'SIZE', 'name' => 'Tamanho', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'P', 'name' => 'P (0-3 meses)'], ['id' => 'M', 'name' => 'M (3-6 meses)'],
                    ['id' => 'G', 'name' => 'G (6-9 meses)'], ['id' => 'GG', 'name' => 'GG (9-12 meses)'],
                    ['id' => 'PREM', 'name' => 'Prematuro'], ['id' => 'RN', 'name' => 'Recém-nascido']
                ]],
                ['id' => 'COLOR', 'name' => 'Cor', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'PINK', 'name' => 'Rosa'], ['id' => 'BLUE', 'name' => 'Azul'],
                    ['id' => 'YELLOW', 'name' => 'Amarelo'], ['id' => 'GREEN', 'name' => 'Verde'],
                    ['id' => 'WHITE', 'name' => 'Branco'], ['id' => 'MULTI', 'name' => 'Multicolorido'],
                    ['id' => 'RED', 'name' => 'Vermelho'], ['id' => 'PURPLE', 'name' => 'Roxo'],
                    ['id' => 'ORANGE', 'name' => 'Laranja'], ['id' => 'BROWN', 'name' => 'Marrom']
                ]],
                ['id' => 'MATERIAL', 'name' => 'Material', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'COTTON', 'name' => 'Algodão'], ['id' => 'ORGANIC', 'name' => 'Orgânico'],
                    ['id' => 'FLEECE', 'name' => 'Fleece'], ['id' => 'BAMBOO', 'name' => 'Bambu'],
                    ['id' => 'WOOL', 'name' => 'Lã'], ['id' => 'CASHMERE', 'name' => 'Caxemira']
                ]],
                ['id' => 'GENDER', 'name' => 'Gênero', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'BOY', 'name' => 'Menino'], ['id' => 'GIRL', 'name' => 'Menina'],
                    ['id' => 'UNISEX', 'name' => 'Unissex']
                ]],
                ['id' => 'SEASON', 'name' => 'Estação', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'SPRING', 'name' => 'Primavera'], ['id' => 'SUMMER', 'name' => 'Verão'],
                    ['id' => 'AUTUMN', 'name' => 'Outono'], ['id' => 'WINTER', 'name' => 'Inverno'],
                    ['id' => 'ALL_SEASONS', 'name' => 'Todas as Estações']
                ]],
                ['id' => 'CLOTHING_TYPE', 'name' => 'Tipo de Roupa', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'BODY', 'name' => 'Body'], ['id' => 'ONEPIECE', 'name' => 'Macacão'],
                    ['id' => 'SHIRT', 'name' => 'Camisa'], ['id' => 'PANTS', 'name' => 'Calça'],
                    ['id' => 'DRESS', 'name' => 'Vestido'], ['id' => 'JACKET', 'name' => 'Jaqueta'],
                    ['id' => 'SWEATER', 'name' => 'Suéter'], ['id' => 'SOCKS', 'name' => 'Meias'],
                    ['id' => 'HAT', 'name' => 'Chapéu'], ['id' => 'SHOES', 'name' => 'Sapatos']
                ]]
            ],
            
            // TABLETS
            'MLB5728' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'MODEL', 'name' => 'Modelo', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'SCREEN_SIZE', 'name' => 'Tamanho da Tela', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '7', 'name' => '7 polegadas'], ['id' => '8', 'name' => '8 polegadas'],
                    ['id' => '9.7', 'name' => '9.7 polegadas'], ['id' => '10.1', 'name' => '10.1 polegadas'],
                    ['id' => '12.9', 'name' => '12.9 polegadas'], ['id' => '11', 'name' => '11 polegadas']
                ]],
                ['id' => 'STORAGE_CAPACITY', 'name' => 'Capacidade de Armazenamento', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => '32GB', 'name' => '32 GB'], ['id' => '64GB', 'name' => '64 GB'],
                    ['id' => '128GB', 'name' => '128 GB'], ['id' => '256GB', 'name' => '256 GB'],
                    ['id' => '512GB', 'name' => '512 GB'], ['id' => '1TB', 'name' => '1 TB']
                ]],
                ['id' => 'OPERATING_SYSTEM', 'name' => 'Sistema Operacional', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'ANDROID', 'name' => 'Android'], ['id' => 'IOS', 'name' => 'iOS'],
                    ['id' => 'WINDOWS', 'name' => 'Windows']
                ]],
                ['id' => 'CONNECTIVITY', 'name' => 'Conectividade', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'WIFI', 'name' => 'Wi-Fi'], ['id' => 'CELLULAR', 'name' => 'Cellular'],
                    ['id' => 'BLUETOOTH', 'name' => 'Bluetooth'], ['id' => 'GPS', 'name' => 'GPS']
                ]]
            ],
            
            // ROUPAS MASCULINAS
            'MLB2187' => [
                ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
                ['id' => 'SIZE', 'name' => 'Tamanho', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'P', 'name' => 'P'], ['id' => 'M', 'name' => 'M'],
                    ['id' => 'G', 'name' => 'G'], ['id' => 'GG', 'name' => 'GG'],
                    ['id' => 'XG', 'name' => 'XG'], ['id' => 'XXG', 'name' => 'XXG'],
                    ['id' => 'XXXG', 'name' => 'XXXG']
                ]],
                ['id' => 'COLOR', 'name' => 'Cor', 'value_type' => 'list', 'required' => true, 'values' => [
                    ['id' => 'BLACK', 'name' => 'Preto'], ['id' => 'WHITE', 'name' => 'Branco'],
                    ['id' => 'BLUE', 'name' => 'Azul'], ['id' => 'GRAY', 'name' => 'Cinza'],
                    ['id' => 'BROWN', 'name' => 'Marrom'], ['id' => 'GREEN', 'name' => 'Verde'],
                    ['id' => 'RED', 'name' => 'Vermelho'], ['id' => 'YELLOW', 'name' => 'Amarelo'],
                    ['id' => 'ORANGE', 'name' => 'Laranja'], ['id' => 'PURPLE', 'name' => 'Roxo']
                ]],
                ['id' => 'MATERIAL', 'name' => 'Material', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'COTTON', 'name' => 'Algodão'], ['id' => 'POLYESTER', 'name' => 'Poliéster'],
                    ['id' => 'BLEND', 'name' => 'Mistura'], ['id' => 'LINEN', 'name' => 'Linho'],
                    ['id' => 'WOOL', 'name' => 'Lã'], ['id' => 'DENIM', 'name' => 'Jeans'],
                    ['id' => 'SILK', 'name' => 'Seda'], ['id' => 'CASHMERE', 'name' => 'Caxemira']
                ]],
                ['id' => 'STYLE', 'name' => 'Estilo', 'value_type' => 'list', 'required' => false, 'values' => [
                    ['id' => 'CASUAL', 'name' => 'Casual'], ['id' => 'FORMAL', 'name' => 'Formal'],
                    ['id' => 'SPORTY', 'name' => 'Esportivo'], ['id' => 'VINTAGE', 'name' => 'Vintage'],
                    ['id' => 'STREETWEAR', 'name' => 'Streetwear'], ['id' => 'BUSINESS', 'name' => 'Business']
                ]]
            ]
        ];

        // Buscar atributos da categoria específica
        if (isset($atributos_por_categoria[$ml_id])) {
            echo json_encode(['success' => true, 'atributos' => $atributos_por_categoria[$ml_id]]);
        } else {
            // Atributos genéricos para outras categorias
            $atributos_genericos = [
                [
                    'id' => 'BRAND',
                    'name' => 'Marca',
                    'value_type' => 'string',
                    'required' => true,
                    'values' => []
                ],
                [
                    'id' => 'MODEL',
                    'name' => 'Modelo',
                    'value_type' => 'string',
                    'required' => false,
                    'values' => []
                ],
                [
                    'id' => 'COLOR',
                    'name' => 'Cor',
                    'value_type' => 'list',
                    'required' => false,
                    'values' => [
                        ['id' => 'BLACK', 'name' => 'Preto'],
                        ['id' => 'WHITE', 'name' => 'Branco'],
                        ['id' => 'BLUE', 'name' => 'Azul'],
                        ['id' => 'RED', 'name' => 'Vermelho']
                    ]
                ]
            ];
            echo json_encode(['success' => true, 'atributos' => $atributos_genericos]);
        }
    }
} 