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
        $config = $this->db->get('ml_configuracoes', 1)->row();
        
        // Se não há configuração na tabela, sincronizar do .env
        if (!$config) {
            $this->sincronizarConfiguracaoDoEnv();
            $config = $this->db->get('ml_configuracoes', 1)->row();
        }
        
        return $config;
    }

    /**
     * Sincronizar configurações do .env com a tabela
     */
    public function sincronizarConfiguracaoDoEnv()
    {
        $data = [
            'access_token' => $_ENV['MERCADO_LIVRE_ACCESS_TOKEN'] ?? '',
            'refresh_token' => $_ENV['MERCADO_LIVRE_REFRESH_TOKEN'] ?? '',
            'user_id' => $_ENV['MERCADO_LIVRE_USER_ID'] ?? '',
            'nickname' => $_ENV['MERCADO_LIVRE_NICKNAME'] ?? '',
            'site_id' => $_ENV['MERCADO_LIVRE_SITE_ID'] ?? 'MLB',
            'token_expires_at' => $_ENV['MERCADO_LIVRE_TOKEN_EXPIRES_AT'] ?? null,
            'ativo' => ($_ENV['MERCADO_LIVRE_ENABLED'] ?? 'false') === 'true' ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Limpar aspas se existirem
        foreach ($data as $key => $value) {
            if (is_string($value) && (strpos($value, '"') === 0 || strpos($value, "'") === 0)) {
                $data[$key] = trim($value, '"\'');
            }
        }

        // Verificar se já existe configuração
        $existing = $this->db->get('ml_configuracoes', 1)->row();
        
        if ($existing) {
            // Atualizar configuração existente
            $this->db->where('id', $existing->id);
            return $this->db->update('ml_configuracoes', $data);
        } else {
            // Inserir nova configuração
            $data['id'] = 1;
            return $this->db->insert('ml_configuracoes', $data);
        }
    }

    /**
     * Salvar produto no ML
     */
    public function salvarProdutoML($produto_id, $dados_ml)
    {
        $data = [
            'produto_id' => $produto_id,
            'ml_categoria' => $dados_ml['ml_categoria'],
            'ml_condicao' => $dados_ml['ml_condicao'],
            'ml_garantia' => $dados_ml['ml_garantia'],
            'ml_tags' => $dados_ml['ml_tags'],
            'ml_descricao' => $dados_ml['ml_descricao'],
            'ml_envios' => $dados_ml['ml_envios'],
            'ml_premium' => $dados_ml['ml_premium'],
            'ml_destaque' => $dados_ml['ml_destaque'],
            'ml_classico' => $dados_ml['ml_classico'],
            'status' => $dados_ml['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('produtos_mercado_livre', $data);
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
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($ml_id) {
            $data['ml_id'] = $ml_id;
        }
        
        if ($permalink) {
            $data['ml_permalink'] = $permalink;
        }
        
        $this->db->where('produto_id', $produto_id);
        return $this->db->update('produtos_mercado_livre', $data);
    }

    /**
     * Salvar log de operação
     */
    public function salvarLog($produto_id, $acao, $status, $mensagem, $dados_enviados = null, $resposta_ml = null)
    {
        $data = [
            'produto_id' => $produto_id,
            'acao' => $acao,
            'status' => $status,
            'mensagem' => $mensagem,
            'dados_enviados' => $dados_enviados ? json_encode($dados_enviados) : null,
            'resposta_ml' => $resposta_ml ? json_encode($resposta_ml) : null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('ml_logs', $data);
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
        // Buscar logs específicos do ML
        $this->db->select('ml.*, p.descricao as produto_nome');
        $this->db->from('ml_logs ml');
        $this->db->join('produtos p', 'p.idProdutos = ml.produto_id', 'left');
        $this->db->order_by('ml.created_at', 'DESC');
        $this->db->limit($limit);
        
        $ml_logs = $this->db->get()->result();
        
        // Buscar logs gerais que contêm "ML:"
        $this->db->select('*');
        $this->db->from('logs');
        $this->db->where('tarefa LIKE', '%ML:%');
        $this->db->order_by('data DESC, hora DESC');
        $this->db->limit($limit);
        
        $geral_logs = $this->db->get()->result();
        
        // Combinar e ordenar todos os logs
        $all_logs = [];
        
        // Adicionar logs específicos do ML
        foreach ($ml_logs as $log) {
            $all_logs[] = [
                'created_at' => $log->created_at,
                'produto_nome' => $log->produto_nome,
                'acao' => $log->acao,
                'status' => $log->status,
                'mensagem' => $log->mensagem,
                'tipo' => 'ml_specific'
            ];
        }
        
        // Adicionar logs gerais do ML
        foreach ($geral_logs as $log) {
            $all_logs[] = [
                'created_at' => $log->data . ' ' . $log->hora,
                'produto_nome' => 'N/A',
                'acao' => 'sistema',
                'status' => 'info',
                'mensagem' => $log->tarefa,
                'tipo' => 'geral'
            ];
        }
        
        // Ordenar por data/hora decrescente
        usort($all_logs, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Retornar apenas o limite solicitado
        return array_slice($all_logs, 0, $limit);
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
        $this->db->select('pml.*, p.descricao, p.precoVenda, p.estoque, p.marcaProduto, p.categoria_id, m.nomeModelo as modeloProduto');
        $this->db->from('produtos_mercado_livre pml');
        $this->db->join('produtos p', 'p.idProdutos = pml.produto_id');
        $this->db->join('modelo m', 'p.idModelo = m.idModelo', 'left');
        $this->db->where('pml.status', 'pending');
        $this->db->order_by('pml.created_at', 'ASC');
        
        $query = $this->db->get();
        $result = $query->result();
        
        // Log para debug
        log_info('ML: Produtos pendentes encontrados: ' . count($result));
        foreach ($result as $prod) {
            log_info('ML: Produto pendente - ID: ' . $prod->produto_id . ', Descrição: ' . $prod->descricao . ', Categoria ML: ' . $prod->ml_categoria);
        }
        
        return $result;
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

    /**
     * Busca configurações do Mercado Livre
     */
    public function getConfiguracoes()
    {
        return $this->db->get('ml_configuracoes')->row();
    }
} 