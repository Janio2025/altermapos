<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MercadoLivre_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Salvar configurações do Mercado Livre
     */
    public function salvarConfiguracao($data)
    {
        $this->db->where('id', 1);
        $result = $this->db->update('ml_configuracoes', $data);
        
        if (!$result) {
            // Se não existe, criar
            $data['id'] = 1;
            $result = $this->db->insert('ml_configuracoes', $data);
        }
        
        return $result;
    }

    /**
     * Buscar configurações do ML
     */
    public function getConfiguracao()
    {
        return $this->db->get('ml_configuracoes', 1)->row();
    }

    /**
     * Salvar produto no ML
     */
    public function salvarProdutoML($produto_id, $dados_ml)
    {
        // Verificar se já existe
        $this->db->where('produto_id', $produto_id);
        $existe = $this->db->get('produtos_mercado_livre')->row();
        
        if ($existe) {
            // Atualizar
            $this->db->where('produto_id', $produto_id);
            return $this->db->update('produtos_mercado_livre', $dados_ml);
        } else {
            // Inserir
            $dados_ml['produto_id'] = $produto_id;
            return $this->db->insert('produtos_mercado_livre', $dados_ml);
        }
    }

    /**
     * Buscar produto ML por ID do produto
     */
    public function getProdutoML($produto_id)
    {
        $this->db->where('produto_id', $produto_id);
        return $this->db->get('produtos_mercado_livre')->row();
    }

    /**
     * Listar todos os produtos integrados
     */
    public function getProdutosIntegrados()
    {
        $this->db->select('pml.*, p.descricao, p.precoVenda, p.estoque');
        $this->db->from('produtos_mercado_livre pml');
        $this->db->join('produtos p', 'p.idProdutos = pml.produto_id');
        $this->db->order_by('pml.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Atualizar status do anúncio
     */
    public function atualizarStatus($produto_id, $status, $ml_id = null, $permalink = null)
    {
        $dados = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($ml_id) {
            $dados['ml_id'] = $ml_id;
        }
        
        if ($permalink) {
            $dados['ml_permalink'] = $permalink;
        }
        
        $this->db->where('produto_id', $produto_id);
        return $this->db->update('produtos_mercado_livre', $dados);
    }

    /**
     * Salvar log de operação
     */
    public function salvarLog($produto_id, $acao, $status, $mensagem, $dados_enviados = null, $resposta_ml = null)
    {
        $log = [
            'produto_id' => $produto_id,
            'acao' => $acao,
            'status' => $status,
            'mensagem' => $mensagem,
            'dados_enviados' => $dados_enviados ? json_encode($dados_enviados) : null,
            'resposta_ml' => $resposta_ml ? json_encode($resposta_ml) : null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('ml_logs', $log);
    }

    /**
     * Buscar logs de um produto
     */
    public function getLogsProduto($produto_id, $limit = 10)
    {
        $this->db->where('produto_id', $produto_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('ml_logs')->result();
    }

    /**
     * Buscar logs gerais
     */
    public function getLogs($limit = 50)
    {
        $this->db->select('ml.*, p.descricao as produto_nome');
        $this->db->from('ml_logs ml');
        $this->db->join('produtos p', 'p.idProdutos = ml.produto_id', 'left');
        $this->db->order_by('ml.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Verificar se produto está integrado
     */
    public function produtoIntegrado($produto_id)
    {
        $this->db->where('produto_id', $produto_id);
        $this->db->where('status !=', 'closed');
        return $this->db->get('produtos_mercado_livre')->row();
    }

    /**
     * Buscar produtos pendentes de sincronização
     */
    public function getProdutosPendentes()
    {
        $this->db->select('pml.*, p.descricao, p.precoVenda, p.estoque, p.marcaProduto, p.modeloProduto');
        $this->db->from('produtos_mercado_livre pml');
        $this->db->join('produtos p', 'p.idProdutos = pml.produto_id');
        $this->db->where('pml.status', 'pending');
        $this->db->order_by('pml.created_at', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Atualizar última sincronização
     */
    public function atualizarSincronizacao($produto_id, $erro = null)
    {
        $dados = [
            'ultima_sincronizacao' => date('Y-m-d H:i:s')
        ];
        
        if ($erro) {
            $dados['erro_sincronizacao'] = $erro;
        } else {
            $dados['erro_sincronizacao'] = null;
        }
        
        $this->db->where('produto_id', $produto_id);
        return $this->db->update('produtos_mercado_livre', $dados);
    }
} 