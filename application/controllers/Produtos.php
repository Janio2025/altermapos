<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Produtos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('produtos_model');
        $this->load->model('Categorias_model');
        $this->data['menuProdutos'] = 'Produtos';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
{
    if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
        $this->session->set_flashdata('error', 'Você não tem permissão para visualizar produtos.');
        redirect(base_url());
    }

    $pesquisa = $this->input->get('pesquisa');
    
    $this->load->library('pagination');
    
    $this->data['configuration']['base_url'] = site_url('produtos/gerenciar/');
    $this->data['configuration']['total_rows'] = $this->produtos_model->count('produtos');
    
    if ($pesquisa) {
        $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}";
        $this->data['configuration']['first_url'] = base_url("index.php/produtos")."?pesquisa={$pesquisa}";
    }
    
    $this->pagination->initialize($this->data['configuration']);
    
    // Ajuste a consulta para incluir as novas colunas e tabelas relacionadas
    $this->db->select('
        produtos.*, 
        modelo.nomeModelo, 
        condicoes.descricaoCondicao, 
        direcao.descricaoDirecao, 
        compativeis.modeloCompativel,
        (SELECT GROUP_CONCAT(imagens_produto.anexo) FROM imagens_produto WHERE imagens_produto.produto_id = produtos.idProdutos) as imagens
    ');
    $this->db->from('produtos');
    $this->db->join('modelo', 'modelo.idModelo = produtos.idModelo');
    $this->db->join('condicoes', 'condicoes.idCondicao = produtos.idCondicao', 'left');
    $this->db->join('direcao', 'direcao.idDirecao = produtos.idDirecao', 'left');
    $this->db->join('compativeis', 'compativeis.idCompativel = produtos.idCompativel', 'left');
    
    if ($pesquisa) {
        $this->db->like('produtos.nome', $pesquisa);
    }
    
    $this->db->limit($this->data['configuration']['per_page'], $this->uri->segment(3));
    $this->data['results'] = $this->db->get()->result();
    
    // Buscar quantidade de produtos para sincronizar com Mercado Livre
    $this->db->where('status', 'pending');
    $qtd_ml_pendentes = $this->db->count_all_results('produtos_mercado_livre');
    $this->data['qtd_ml_pendentes'] = $qtd_ml_pendentes;
    
    $this->data['view'] = 'produtos/produtos';
    
    return $this->layout();
}



public function adicionar()
{
    if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) {
        $this->session->set_flashdata('error', 'Você não tem permissão para adicionar produtos.');
        redirect(base_url());
    }

    $this->load->library('form_validation');
    $this->data['custom_error'] = '';

    // Carregar organizadores para o select
    $this->data['organizadores'] = $this->db->where('ativa', true)->get('organizadores')->result();
    
    $this->data['todas_categorias'] = $this->Categorias_model->getAll();

    if ($this->form_validation->run('produtos') == false) {
        $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
    } else {
        $precoCompra = $this->input->post('precoCompra');
        $precoCompra = str_replace(',', '', $precoCompra);
        $precoVenda = $this->input->post('precoVenda');
        $precoVenda = str_replace(',', '', $precoVenda);
    
        // Salvar o modelo na tabela `modelo`
        $modeloProduto = set_value('modeloProduto');
        $modeloData = ['nomeModelo' => $modeloProduto];
        $this->produtos_model->add('modelo', $modeloData);
    
        $idModelo = $this->db->insert_id();
    
        // Salvar a condição na tabela `condicoes`
        $condicaoProduto = set_value('condicaoProduto');
        $condicaoData = ['descricaoCondicao' => $condicaoProduto];
        $this->produtos_model->add('condicoes', $condicaoData);
    
        $idCondicao = $this->db->insert_id();
    
        // Salvar a direção na tabela `direcao`
        $direcaoProduto = set_value('direcaoProduto');
        $direcaoData = ['descricaoDirecao' => $direcaoProduto];
        $this->produtos_model->add('direcao', $direcaoData);
    
        $idDirecao = $this->db->insert_id();
    
        // Salvar os modelos compatíveis na tabela `compativeis`
        $compativelProdutos = $this->input->post('compativelProduto');
        $idCompativeis = [];
    
        if (is_array($compativelProdutos)) {
            foreach ($compativelProdutos as $compativelProduto) {
                if (!empty($compativelProduto)) {
                    $compativelData = ['modeloCompativel' => $compativelProduto];
                    $this->produtos_model->add('compativeis', $compativelData);
                    $idCompativeis[] = $this->db->insert_id();
                }
            }
        }
    
        // Tratar o compartimento_id para ser NULL quando não houver compartimento
        $compartimento_id = $this->input->post('compartimento_id');
        if (empty($compartimento_id)) {
            $compartimento_id = null;
        }
    
        $data = [
            'codDeBarra' => set_value('codDeBarra'),
            'descricao' => set_value('descricao'),
            'marcaProduto' => set_value('marcaProduto'),
            'idModelo' => $idModelo,
            'nsProduto' => set_value('nsProduto'),
            'codigoPeca' => set_value('codigoPeca'),
            'organizador_id' => $this->input->post('organizador_id'),
            'compartimento_id' => $compartimento_id,
            'unidade' => set_value('unidade'),
            'precoCompra' => $precoCompra,
            'precoVenda' => $precoVenda,
            'estoque' => set_value('estoque'),
            'estoqueMinimo' => set_value('estoqueMinimo'),
            'saida' => set_value('saida'),
            'entrada' => set_value('entrada'),
            'idCondicao' => $idCondicao,
            'idDirecao' => $idDirecao,
            'dataPedido' => set_value('dataPedido'),
            'dataChegada' => set_value('dataChegada'),
            'numeroPeca' => set_value('numeroPeca'),
            'categoria_id' => $this->input->post('categoria_id'),
        ];
    
        if ($this->produtos_model->add('produtos', $data) == true) {
            $idProduto = $this->db->insert_id();
    
            foreach ($idCompativeis as $idCompativel) {
                $produtoCompativelData = [
                    'idProduto' => $idProduto,
                    'idCompativel' => $idCompativel,
                ];
                $this->produtos_model->add('produto_compativel', $produtoCompativelData);
            }
    
            if (!empty($_FILES['userfile']['name'][0])) {
                $this->imgAnexar($idProduto);
            }

            $mensagem_sucesso = 'Produto adicionado com sucesso!';
            
            $this->session->set_flashdata('success', $mensagem_sucesso);
            log_info('Adicionou um produto');
            redirect(site_url('produtos/adicionar/'));
        } else {
            $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured.</p></div>';
        }
    }
    
    $this->data['view'] = 'produtos/adicionarProduto';
    return $this->layout();
}

// Adicionar método para carregar compartimentos via AJAX
public function buscarCompartimentos() {
    if (!$this->input->is_ajax_request()) {
        exit('Acesso não permitido');
    }

    $organizador_id = $this->input->get('organizador_id');
    $this->db->where('organizador_id', $organizador_id);
    $this->db->where('ativa', true);
    $compartimentos = $this->db->get('compartimentos')->result();

    echo json_encode($compartimentos);
}

public function buscarOrganizadorPorId() {
    if (!$this->input->is_ajax_request()) {
        exit('Acesso não permitido');
    }

    $id = $this->input->get('id');
    $this->db->where('id', $id);
    $organizador = $this->db->get('organizadores')->row();

    if ($organizador) {
        echo json_encode($organizador);
    } else {
        echo json_encode([]);
    }
}


public function editar()
{
    if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
        $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
        redirect('mapos');
    }

    if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
        $this->session->set_flashdata('error', 'Você não tem permissão para editar produtos.');
        redirect(base_url());
    }

    $this->load->library('form_validation');
    $this->data['custom_error'] = '';

    // Carregar organizadores para o select
    $this->data['organizadores'] = $this->db->where('ativa', true)->get('organizadores')->result();
    
    // Carregar categorias para o select
    $this->data['todas_categorias'] = $this->Categorias_model->getAll();

    if ($this->form_validation->run('produtos') == false) {
        $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
    } else {
        // Tratamento de preços
        $precoCompra = str_replace(',', '', $this->input->post('precoCompra'));
        $precoVenda = str_replace(',', '', $this->input->post('precoVenda'));

        // Capturar valores do formulário
        $idProduto = $this->input->post('idProdutos');
        $idModelo = $this->input->post('idModelo'); // ID do modelo
        $nomeModelo = strtoupper($this->input->post('nomeModelo')); // Nome do modelo
        $marcaProduto = strtoupper($this->input->post('marcaProduto'));

        // Atualiza a tabela `produtos`
        $dataProduto = [
            'codDeBarra' => set_value('codDeBarra'),
            'descricao' => $this->input->post('descricao'),
            'marcaProduto' => $marcaProduto,
            'idModelo' => $idModelo, // Mantém a referência ao modelo correto
            'nsProduto' => $this->input->post('nsProduto'),
            'codigoPeca' => $this->input->post('codigoPeca'),
            'organizador_id' => $this->input->post('organizador_id'),
            'compartimento_id' => $this->input->post('compartimento_id'),
            'unidade' => $this->input->post('unidade'),
            'precoCompra' => $precoCompra,
            'precoVenda' => $precoVenda,
            'estoque' => $this->input->post('estoque'),
            'estoqueMinimo' => $this->input->post('estoqueMinimo'),
            'saida' => set_value('saida'),
            'entrada' => set_value('entrada'),
            'idCondicao' => $this->input->post('idCondicao'),
            'idDirecao' => $this->input->post('idDirecao'),
            'dataPedido' => $this->input->post('dataPedido'),
            'dataChegada' => $this->input->post('dataChegada'),
            'numeroPeca' => $this->input->post('numeroPeca'),
            'categoria_id' => $this->input->post('categoria_id')
        ];

        // Atualiza o produto
        $produtoAtualizado = $this->produtos_model->edit('produtos', $dataProduto, 'idProdutos', $idProduto);

        // Atualiza o nome do modelo na tabela `modelo`
        $modeloAtualizado = $this->produtos_model->edit('modelo', ['nomeModelo' => $nomeModelo], 'idModelo', $idModelo);

        if ($produtoAtualizado || $modeloAtualizado) {
            // Atualiza imagens se necessário
            if (!empty($_FILES['userfile']['name'][0])) {
                $this->imgAnexar($idProduto);
            }

            // Atualiza modelos compatíveis
            $modelosCompativeis = $this->input->post('compativelProduto');
            if (is_array($modelosCompativeis)) {
                $modelosCompativeisArray = [];
                foreach ($modelosCompativeis as $index => $modeloCompativel) {
                    $modelosCompativeisArray[] = [
                        'idCompativel' => $this->input->post('idCompativel')[$index] ?? null,
                        'modeloCompativel' => $modeloCompativel
                    ];
                }
                $this->produtos_model->update_modelos_compativeis($idProduto, $modelosCompativeisArray);
            }

            // Mensagem de sucesso
            $this->session->set_flashdata('success', 'Produto e modelo editados com sucesso!');
            log_info('Alterou um produto e modelo. ID: ' . $idProduto);
            redirect(site_url('produtos/editar/') . $idProduto);
        } else {
            $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro ao editar o produto ou modelo.</p></div>';
        }
    }

    // Buscar os dados do produto
    $produtoId = $this->uri->segment(3);
    $this->data['result'] = $this->produtos_model->getById($produtoId);
    $this->data['modelos_compativeis'] = $this->produtos_model->get_modelos_compativeis($produtoId);
    $this->data['imagensProduto'] = $this->produtos_model->getImagensProduto($produtoId);

    // Processar o campo localizacaoProduto
    if (!empty($this->data['result']->localizacaoProduto)) {
        $localizacao = explode(',', $this->data['result']->localizacaoProduto);
        if (count($localizacao) >= 3) {
            $this->data['organizadorId'] = $localizacao[0];
            $this->data['organizadorNome'] = $localizacao[1];
            $this->data['compartimentoNome'] = $localizacao[2];
        }
    }
    
    $this->data['view'] = 'produtos/editarProduto';

    return $this->layout();
}


public function visualizar()
{
    // Verificação do ID e permissão do usuário
    if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
        $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
        redirect('mapos');
    }

    if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
        $this->session->set_flashdata('error', 'Você não tem permissão para visualizar produtos.');
        redirect(base_url());
    }

    $produtoId = $this->uri->segment(3);
    $this->data['result'] = $this->produtos_model->getById($produtoId);

    if ($this->data['result'] == null) {
        $this->session->set_flashdata('error', 'Produto não encontrado.');
        redirect(site_url('produtos/editar/') . $this->input->post('idProdutos'));
    }

    // Buscar modelos compatíveis
    $this->data['modelosCompativeis'] = $this->produtos_model->get_modelos_compativeis($produtoId);

    // Buscar imagens do produto
    $this->data['imagensProduto'] = $this->produtos_model->getImagensProduto($produtoId);

    // Adicionar informações extras do produto (se necessário)
    

    $this->data['view'] = 'produtos/visualizarProduto';

    return $this->layout();
}


public function excluir()
{
    // Verificação de permissão do usuário para excluir produtos
    if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dProduto')) {
        $this->session->set_flashdata('error', 'Você não tem permissão para excluir produtos.');
        redirect(base_url());
    }

    $id = $this->input->post('id');
    if ($id == null) {
        $this->session->set_flashdata('error', 'Erro ao tentar excluir produto.');
        redirect(base_url() . 'index.php/produtos/gerenciar/');
    }

    // Obter o idModelo antes de excluir o produto
    $produto = $this->produtos_model->getById($id);
    $idModelo = $produto->idModelo;

    // Obter os ids dos modelos compatíveis
    $modelosCompativeis = $this->produtos_model->get_modelos_compativeis($id);
    $idCompativeis = array_map(function($modelo) {
        return $modelo->idCompativel;
    }, $modelosCompativeis);

    // Excluir as imagens vinculadas ao produto e a pasta do produto
    $imagens = $this->produtos_model->getImagensProduto($id);
    foreach ($imagens as $imagem) {
        $imagemPath = FCPATH . $imagem->path . DIRECTORY_SEPARATOR . $imagem->anexo;
        $thumbPath = FCPATH . $imagem->path . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $imagem->thumb;
        
        if (file_exists($imagemPath)) {
            unlink($imagemPath);
        }
        if ($imagem->thumb != null && file_exists($thumbPath)) {
            unlink($thumbPath);
        }

        $this->produtos_model->delete('imagens_produto', 'idImagem', $imagem->idImagem);
    }

    // Excluir a pasta do produto
    $diretorioProduto = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . 'produtos' . DIRECTORY_SEPARATOR . 'Produto-' . $id;
    if (is_dir($diretorioProduto . DIRECTORY_SEPARATOR . 'thumbs')) {
        rmdir($diretorioProduto . DIRECTORY_SEPARATOR . 'thumbs');
    }
    if (is_dir($diretorioProduto)) {
        rmdir($diretorioProduto);
    }

    // Excluir os registros das tabelas relacionadas
    $this->produtos_model->delete('produtos_os', 'produtos_id', $id);
    $this->produtos_model->delete('itens_de_vendas', 'produtos_id', $id);

    // Excluir o produto
    $this->produtos_model->delete('produtos', 'idProdutos', $id);

    // Excluir o modelo da tabela `modelo`
    $this->produtos_model->delete('modelo', 'idModelo', $idModelo);

    // Excluir os modelos compatíveis da tabela `compativeis`
    foreach ($idCompativeis as $idCompativel) {
        $this->produtos_model->delete('compativeis', 'idCompativel', $idCompativel);
    }

    // Excluir os registros da tabela `produto_compativel`
    $this->produtos_model->delete('produto_compativel', 'idProduto', $id);

    log_info('Removeu um produto, seu modelo, modelos compatíveis e imagens. ID: ' . $id);

    $this->session->set_flashdata('success', 'Produto, modelos compatíveis e imagens excluídos com sucesso!');
    redirect(site_url('produtos/gerenciar/'));
}


public function excluirImgAnexo($id = null)
{
    // Valida o ID recebido
    if (empty($id) || !is_numeric($id)) {
        echo json_encode(['result' => false, 'mensagem' => 'Erro: ID inválido para exclusão do anexo.']);
        return;
    }
    
    // Busca o registro da imagem na tabela "imagens_produto"
    $this->db->where('idImagem', $id);
    $file = $this->db->get('imagens_produto', 1)->row();
    
    if (!$file) {
        echo json_encode(['result' => false, 'mensagem' => 'Erro: Anexo não encontrado.']);
        return;
    }
    
    // Monta o caminho completo do arquivo principal (usando o caminho absoluto armazenado no banco)
    $file_path = $file->path . DIRECTORY_SEPARATOR . $file->anexo;
    if (file_exists($file_path)) {
        if (!unlink($file_path)) {
            echo json_encode(['result' => false, 'mensagem' => 'Erro ao excluir o arquivo principal.']);
            return;
        }
    }
    // Se o arquivo não existir, podemos prosseguir (ou registrar um log, se preferir)
    
    // Se houver thumbnail, tenta removê-la
    if (!empty($file->thumb)) {
        $thumb_path = $file->path . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $file->thumb;
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }
    }
    
    // Remove o registro do banco de dados
    if ($this->produtos_model->delete('imagens_produto', 'idImagem', $id)) {
        log_info('Removeu anexo do produto. ID do Produto: ' . $file->produto_id);
        echo json_encode(['result' => true, 'mensagem' => 'Anexo excluído com sucesso.']);
    } else {
        echo json_encode(['result' => false, 'mensagem' => 'Erro ao excluir registro do banco de dados.']);
    }
}

public function downloadProduto($id = null)
{
    // Valida o ID recebido
    if (empty($id) || !is_numeric($id)) {
        $this->session->set_flashdata('error', 'Parâmetro inválido para download.');
        redirect(site_url('produtos/gerenciar'));
        return;
    }

    // Carrega o helper de download
    $this->load->helper('download');

    // Busca o registro na tabela 'imagens_produto' usando o ID informado
    $this->db->where('idImagem', $id);
    $file = $this->db->get('imagens_produto', 1)->row();

    if (!$file) {
        $this->session->set_flashdata('error', 'Arquivo não encontrado.');
        redirect(site_url('produtos/gerenciar'));
        return;
    }

    // Monta o caminho completo do arquivo utilizando o campo 'path'
    // Certifique-se de que o campo 'path' armazena um caminho absoluto (ex.: FCPATH . 'assets/...')
    $file_path = $file->path . DIRECTORY_SEPARATOR . $file->anexo;

    if (!file_exists($file_path)) {
        $this->session->set_flashdata('error', 'Arquivo não existe no servidor.');
        redirect(site_url('produtos/gerenciar'));
        return;
    }

    // Força o download do arquivo, usando o nome do arquivo original
    force_download($file->anexo, file_get_contents($file_path));
}



    public function atualizar_estoque()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para atualizar estoque de produtos.');
            redirect(base_url());
        }

        $idProduto = $this->input->post('id');
        $novoEstoque = $this->input->post('estoque');
        $estoqueAtual = $this->input->post('estoqueAtual');

        $estoque = $estoqueAtual + $novoEstoque;

        $data = [
            'estoque' => $estoque,
        ];

        if ($this->produtos_model->edit('produtos', $data, 'idProdutos', $idProduto) == true) {
            $this->session->set_flashdata('success', 'Estoque de Produto atualizado com sucesso!');
            log_info('Atualizou estoque de um produto. ID: ' . $idProduto);
            redirect(site_url('produtos/visualizar/') . $idProduto);
        } else {
            $this->data['custom_error'] = '<div class="alert">Ocorreu um erro.</div>';
        }
    }

    // modificações

    /**
     * Limpa o nome do produto removendo caracteres inválidos para nomes de arquivos
     * @param string $nomeProduto
     * @return string
     */
    private function limparNomeArquivo($nomeProduto) {
        // Caracteres inválidos no Windows: < > : " | ? * \ /
        // Também removemos outros caracteres problemáticos
        $caracteresInvalidos = ['<', '>', ':', '"', '|', '?', '*', '\\', '/', 'º', '°', 'ª', '™', '®', '©'];
        
        // Remove caracteres inválidos
        $nomeLimpo = str_replace($caracteresInvalidos, '', $nomeProduto);
        
        // Remove espaços extras e caracteres de controle
        $nomeLimpo = trim($nomeLimpo);
        $nomeLimpo = preg_replace('/\s+/', ' ', $nomeLimpo); // Múltiplos espaços viram um só
        
        // Remove caracteres de controle (ASCII 0-31)
        $nomeLimpo = preg_replace('/[\x00-\x1F\x7F]/', '', $nomeLimpo);
        
        // Limita o tamanho do nome (máximo 50 caracteres para evitar problemas)
        if (strlen($nomeLimpo) > 50) {
            $nomeLimpo = substr($nomeLimpo, 0, 50);
        }
        
        // Se ficou vazio, usa um nome padrão
        if (empty($nomeLimpo)) {
            $nomeLimpo = 'produto';
        }
        
        return $nomeLimpo;
    }

    public function imgAnexar($idProduto)
{
    $this->load->library('upload');
    $this->load->library('image_lib');
    $this->load->helper('media_server_helper');

    // Usar o helper para determinar o diretório e URL
    $config = Media_server_helper::getDiretorioUpload('produtos', $idProduto);
    $directory = $config['directory'];
    $url_base = $config['url_base'];
    
    if (!is_dir($directory . DIRECTORY_SEPARATOR . 'thumbs')) {
        // Criar diretório para imagens e thumbs
        try {
            mkdir($directory . DIRECTORY_SEPARATOR . 'thumbs', 0755, true);
        } catch (Exception $e) {
            echo json_encode(['result' => false, 'mensagem' => $e->getMessage()]);
            die();
        }
    }

    // Configuração do upload
    $upload_conf = [
        'upload_path' => $directory,
        'allowed_types' => 'jpg|png|gif|jpeg|JPG|PNG|GIF|JPEG', // formatos permitidos
        'max_size' => 0,
    ];
    $this->upload->initialize($upload_conf);

    if (isset($_FILES['userfile'])) {
        $files = $_FILES['userfile'];
        $file_count = count($files['name']);
        for ($i = 0; $i < $file_count; $i++) {
            $_FILES['file_' . $i] = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];
        }
        unset($_FILES['userfile']);
    }

    $error = [];
    $success = [];
    $nomeProduto = $this->input->post('descricao'); // Captura o nome do produto (campo 'descricao')

    foreach ($_FILES as $field_name => $file) {
        if (!$this->upload->do_upload($field_name)) {
            $error['upload'][] = $this->upload->display_errors();
        } else {
            $upload_data = $this->upload->data();
            $new_file_name = $this->limparNomeArquivo($nomeProduto) . '-' . uniqid() . '.' . pathinfo($upload_data['file_name'], PATHINFO_EXTENSION); // Renomeia o arquivo
            $new_file_path = $upload_data['file_path'] . $new_file_name;
            rename($upload_data['full_path'], $new_file_path); // Move e renomeia o arquivo

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
                    $this->load->model('produtos_model');
                    $result = $this->produtos_model->imgAnexar($idProduto, $new_file_name, $url_base, 'thumb_' . $new_file_name, $directory);
                    if (!$result) {
                        $error['db'][] = 'Erro ao inserir no banco de dados.';
                    }
                }
            } else {
                $success[] = $upload_data;
                $this->load->model('produtos_model');
                $result = $this->produtos_model->imgAnexar($this->input->post('idProdutoImg'), $new_file_name, $url_base, '', $directory);
                if (!$result) {
                    $error['db'][] = 'Erro ao inserir no banco de dados.';
                }
            }
        }
    }

    if (count($error) > 0) {
        echo json_encode(['result' => false, 'mensagem' => 'Ocorreu um erro ao processar os arquivos.', 'errors' => $error]);
    } else {
        log_info('Adicionou imagem(s) a um Produto. ID (Produto): ' . $this->input->post('idProdutoImg'));
        echo json_encode(['result' => true, 'mensagem' => 'Arquivo(s) anexado(s) com sucesso.']);
    }
}

    // Métodos do Mercado Livre removidos

}
