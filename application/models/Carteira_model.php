<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Carteira_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }
        
        $query = $this->db->get();
        
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    public function getAll($limit = null, $offset = null)
    {
        $this->db->select('cu.*, u.nome as usuario, 
            COALESCE((SELECT SUM(valor) FROM transacoes_usuario WHERE tipo = "bonus" AND carteira_usuario_id = cu.idCarteiraUsuario), 0) as total_bonus,
            COALESCE((SELECT SUM(valor) FROM transacoes_usuario WHERE tipo = "comissao" AND carteira_usuario_id = cu.idCarteiraUsuario), 0) as total_comissoes');
        $this->db->from('carteira_usuario cu');
        $this->db->join('usuarios u', 'u.idUsuarios = cu.usuarios_id', 'left');
        
        if($limit){
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        
        if ($query && $query->num_rows() > 0) {
            return $query->result();
        }
        return array();
    }

    public function getById($id)
    {
        if (!$id) return null;
        
        $this->db->select('cu.*, u.nome as usuario, 
            COALESCE((SELECT SUM(valor) FROM transacoes_usuario WHERE tipo = "bonus" AND carteira_usuario_id = cu.idCarteiraUsuario), 0) as total_bonus,
            COALESCE((SELECT SUM(valor) FROM transacoes_usuario WHERE tipo = "comissao" AND carteira_usuario_id = cu.idCarteiraUsuario), 0) as total_comissoes');
        $this->db->from('carteira_usuario cu');
        $this->db->join('usuarios u', 'u.idUsuarios = cu.usuarios_id', 'left');
        $this->db->where('cu.idCarteiraUsuario', $id);
        
        $query = $this->db->get();
        
        if ($query && $query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }
        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->trans_begin();
        
        try {
            // Primeiro exclui as configurações da carteira
            if ($table == 'carteira_usuario') {
                $this->db->where('carteira_usuario_id', $ID);
                $this->db->delete('configuracao_carteira');
                
                // Exclui as transações da carteira
                $this->db->where('carteira_usuario_id', $ID);
                $this->db->delete('transacoes_usuario');
            }
            
            // Depois exclui a carteira
            $this->db->where($fieldID, $ID);
            $this->db->delete($table);
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            }
            
            $this->db->trans_commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function count($table = null)
    {
        try {
            return $this->db->count_all('carteira_usuario');
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getCarteiraId($userId)
    {
        $this->db->where('usuarios_id', $userId);
        $carteira = $this->db->get('carteira_usuario')->row();
        
        if (!$carteira) {
            // Create wallet if it doesn't exist
            $this->db->insert('carteira_usuario', [
                'usuarios_id' => $userId,
                'saldo' => 0,
                'ativo' => 1
            ]);
            return $this->db->insert_id();
        }
        
        return $carteira->idCarteiraUsuario;
    }

    public function getSaldo($userId)
    {
        $this->db->select('saldo');
        $this->db->where('usuarios_id', $userId);
        $carteira = $this->db->get('carteira_usuario')->row();
        
        if (!$carteira) {
            // Create wallet if it doesn't exist
            $this->db->insert('carteira_usuario', [
                'usuarios_id' => $userId,
                'saldo' => 0,
                'ativo' => 1
            ]);
            return 0;
        }
        
        return $carteira->saldo;
    }

    public function updateSaldo($carteiraId, $valor)
    {
        $this->db->set('saldo', 'saldo + ' . $valor, false);
        $this->db->where('idCarteiraUsuario', $carteiraId);
        return $this->db->update('carteira_usuario');
    }

    public function getTransacoes($userId)
    {
        $this->db->select('transacoes_usuario.*');
        $this->db->from('transacoes_usuario');
        $this->db->join('carteira_usuario', 'carteira_usuario.idCarteiraUsuario = transacoes_usuario.carteira_usuario_id');
        $this->db->where('carteira_usuario.usuarios_id', $userId);
        $this->db->order_by('transacoes_usuario.data_transacao', 'desc');
        return $this->db->get()->result();
    }

    public function getByUsuarioId($usuario_id)
    {
        if (!$usuario_id) return null;
        
        $this->db->where('usuarios_id', $usuario_id);
        return $this->db->get('carteira_usuario')->row();
    }

    public function getConfiguracao($carteira_id) {
        $this->db->where('carteira_usuario_id', $carteira_id);
        return $this->db->get('configuracao_carteira')->row();
    }

    public function salvarConfiguracao($data) {
        // Verifica se já existe configuração para esta carteira
        $this->db->where('carteira_usuario_id', $data['carteira_usuario_id']);
        $existente = $this->db->get('configuracao_carteira')->row();
        
        if ($existente) {
            // Atualiza a configuração existente
            $this->db->where('id', $existente->id);
            return $this->db->update('configuracao_carteira', $data);
        } else {
            // Cria nova configuração
            return $this->db->insert('configuracao_carteira', $data);
        }
    }

    public function processarPagamentosAutomaticos() {
        $data_atual = date('Y-m-d');
        $dia_atual = (int)date('d');
        
        // Busca todas as configurações
        $configs = $this->db->get('configuracao_carteira')->result();
        
        foreach ($configs as $config) {
            // Verifica se é dia de pagamento
            if ($config->data_salario == $dia_atual) {
                $valor_pagamento = $config->salario_base;
                
                // Se for quinzenal, divide o valor
                if ($config->tipo_repeticao == 'quinzenal') {
                    $valor_pagamento = $config->salario_base / 2;
                }
                
                // Inicia a transação
                $this->db->trans_begin();
                
                try {
                    // Registra o salário
                    $salario_data = array(
                        'tipo' => 'salario',
                        'valor' => $valor_pagamento,
                        'data_transacao' => $data_atual,
                        'descricao' => 'Salário ' . ($config->tipo_repeticao == 'quinzenal' ? 'Quinzenal' : 'Mensal'),
                        'carteira_usuario_id' => $config->carteira_usuario_id
                    );
                    $this->db->insert('transacoes_usuario', $salario_data);
                    
                    // Atualiza o saldo da carteira
                    $carteira = $this->getById($config->carteira_usuario_id);
                    $novo_saldo = $carteira->saldo + $valor_pagamento;
                    
                    $this->db->where('idCarteiraUsuario', $config->carteira_usuario_id);
                    $this->db->update('carteira_usuario', array('saldo' => $novo_saldo));
                    
                    $this->db->trans_commit();
                } catch (Exception $e) {
                    $this->db->trans_rollback();
                    log_message('error', 'Erro ao processar pagamento automático: ' . $e->getMessage());
                }
            }
        }
    }
}
