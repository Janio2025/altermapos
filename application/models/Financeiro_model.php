<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Financeiro_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $per_page = 0, $start = 0, $join = '')
    {
        $this->db->select($fields);
        $this->db->from($table);
        if ($join) {
            $this->db->join('usuarios', 'usuarios.idUsuarios = lancamentos.usuarios_id', 'left');
            if (strpos($join, 'vendas') !== false) {
                $this->db->join('vendas', 'vendas.lancamentos_id = lancamentos.idLancamentos', 'left');
            }
        }
        if ($where) {
            $this->db->where($where);
        }
        if ($per_page > 0) {
            $this->db->limit($per_page, $start);
        }
        return $this->db->get()->result();
    }

    public function getTotals($where = '', $join = '')
    {
        $usuario_id = $this->input->get('usuario_id');
        $considerar_os = $this->input->get('considerar_os');
        
        $this->db->select('COALESCE(SUM(CASE WHEN lancamentos.tipo = "receita" THEN lancamentos.valor ELSE 0 END), 0) as receitas, 
                          COALESCE(SUM(CASE WHEN lancamentos.tipo = "despesa" THEN lancamentos.valor ELSE 0 END), 0) as despesas');
        $this->db->from('lancamentos');
        
        if ($join) {
            $this->db->join('usuarios', 'usuarios.idUsuarios = lancamentos.usuarios_id', 'left');
            if (strpos($join, 'vendas') !== false) {
                $this->db->join('vendas', 'vendas.lancamentos_id = lancamentos.idLancamentos', 'left');
            }
        }
        
        if (!empty($usuario_id)) {
            $this->db->where('(lancamentos.usuarios_id = ' . $usuario_id . ' OR vendas.usuarios_id = ' . $usuario_id . ')');
        }
        
        if (!$considerar_os) {
            $this->db->where('os_id IS NULL');
        }
        
        if ($where) {
            if (strpos($where, 'OR') !== false) {
                $this->db->where("($where)");
            } else {
                $this->db->where($where);
            }
        }
        
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            $result = $query->row();
            return array(
                'receitas' => floatval($result->receitas),
                'despesas' => floatval($result->despesas)
            );
        }
        
        return array(
            'receitas' => 0,
            'despesas' => 0
        );
    }

    public function getEstatisticasFinanceiro2()
    {
        $usuario_id = $this->input->get('usuario_id');
        $considerar_os = $this->input->get('considerar_os');
        $where_clause = '';
        
        if (!empty($usuario_id)) {
            $where_clause = "WHERE (lancamentos.usuarios_id = " . $usuario_id . " OR vendas.usuarios_id = " . $usuario_id . ")";
        }
        
        if (!$considerar_os) {
            if (empty($where_clause)) {
                $where_clause = "WHERE os_id IS NULL";
            } else {
                $where_clause .= " AND os_id IS NULL";
            }
        }

        $sql = "SELECT 
                COALESCE(SUM(CASE WHEN baixado = 1 AND tipo = 'receita' THEN IF(valor_desconto = 0, valor, valor_desconto) END), 0) as total_receita,
                COALESCE(SUM(CASE WHEN baixado = 1 AND tipo = 'despesa' THEN valor - desconto END), 0) as total_despesa,
                COALESCE(SUM(CASE WHEN baixado = 1 THEN desconto END), 0) as total_valor_desconto,
                COALESCE(SUM(CASE WHEN baixado = 0 THEN valor - valor_desconto END), 0) as total_valor_desconto_pendente,
                COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor END), 0) as total_receita_sem_desconto,
                COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor END), 0) as total_despesa_sem_desconto,
                COALESCE(SUM(CASE WHEN baixado = 0 AND tipo = 'receita' THEN valor_desconto END), 0) as total_receita_pendente,
                COALESCE(SUM(CASE WHEN baixado = 0 AND tipo = 'despesa' THEN valor_desconto END), 0) as total_despesa_pendente 
                FROM lancamentos " . $where_clause;

        $query = $this->db->query($sql);
        
        if ($query === false) {
            // Return default object with zeros if query fails
            return (object)[
                'total_receita' => 0,
                'total_despesa' => 0,
                'total_valor_desconto' => 0,
                'total_valor_desconto_pendente' => 0,
                'total_receita_sem_desconto' => 0,
                'total_despesa_sem_desconto' => 0,
                'total_receita_pendente' => 0,
                'total_despesa_pendente' => 0
            ];
        }

        $result = $query->row();
        
        // If no results found, return default object with zeros
        if (!$result) {
            return (object)[
                'total_receita' => 0,
                'total_despesa' => 0,
                'total_valor_desconto' => 0,
                'total_valor_desconto_pendente' => 0,
                'total_receita_sem_desconto' => 0,
                'total_despesa_sem_desconto' => 0,
                'total_receita_pendente' => 0,
                'total_despesa_pendente' => 0
            ];
        }

        return $result;
    }

    public function getById($id)
    {
        $this->db->where('idClientes', $id);
        $this->db->limit(1);

        return $this->db->get('clientes')->row();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function add1($table, $data1)
    {
        $this->db->insert($table, $data1);
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
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table, $where = '', $join = '')
    {
        $this->db->from($table);
        
        if ($join) {
            $this->db->join('usuarios', 'usuarios.idUsuarios = lancamentos.usuarios_id', 'left');
            if (strpos($join, 'vendas') !== false) {
                $this->db->join('vendas', 'vendas.lancamentos_id = lancamentos.idLancamentos', 'left');
            }
        }
        
        if ($where) {
            $this->db->where($where);
        }
        
        return $this->db->count_all_results();
    }

    public function autoCompleteClienteFornecedor($q)
    {
        $this->db->select('DISTINCT(cliente_fornecedor) as cliente_fornecedor');
        $this->db->limit(5);
        $this->db->like('cliente_fornecedor', $q);
        $query = $this->db->get('lancamentos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['cliente_fornecedor'], 'id' => $row['cliente_fornecedor']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteClienteReceita($q)
    {
        $this->db->select('idClientes, nomeCliente');
        $this->db->limit(5);
        $this->db->like('nomeCliente', $q);
        $query = $this->db->get('clientes');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nomeCliente'], 'id' => $row['idClientes']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteUsuario($q)
    {
        $this->db->select('idUsuarios, nome');
        $this->db->limit(5);
        $this->db->like('nome', $q);
        $this->db->where('situacao', 1);
        $query = $this->db->get('usuarios');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nome'], 'id' => $row['idUsuarios']];
            }
            echo json_encode($row_set);
        }
    }

    public function getGastosMensaisPorTipo()
    {
        $ano = date('Y');
        $sql = "SELECT 
                    MONTH(l.data_vencimento) as mes,
                    SUM(CASE 
                        WHEN c.fornecedor = 1 AND l.tipo = 'despesa' AND l.baixado = 1 
                        THEN l.valor - l.desconto 
                        ELSE 0 
                    END) as gastos_fornecedores,
                    SUM(CASE 
                        WHEN c.fornecedor = 2 AND l.tipo = 'despesa' AND l.baixado = 1 
                        THEN l.valor - l.desconto 
                        ELSE 0 
                    END) as gastos_colaboradores
                FROM lancamentos l
                LEFT JOIN clientes c ON l.clientes_id = c.idClientes
                WHERE YEAR(l.data_vencimento) = ?
                GROUP BY MONTH(l.data_vencimento)
                ORDER BY MONTH(l.data_vencimento)";

        $query = $this->db->query($sql, array($ano));
        
        // Inicializa arrays com zeros para todos os meses
        $gastos_fornecedores = array_fill(0, 12, 0);
        $gastos_colaboradores = array_fill(0, 12, 0);
        
        // Preenche com os valores reais onde existirem
        foreach ($query->result() as $row) {
            $mes = $row->mes - 1; // Ajusta para índice 0-11
            $gastos_fornecedores[$mes] = floatval($row->gastos_fornecedores);
            $gastos_colaboradores[$mes] = floatval($row->gastos_colaboradores);
        }

        return array(
            'gastos_fornecedores' => $gastos_fornecedores,
            'gastos_colaboradores' => $gastos_colaboradores
        );
    }

    /**
     * Busca todos os lançamentos pagos (baixado = 1) que ainda não foram fechados em nenhum fechamento de caixa.
     * @return array Lista de lançamentos pagos não fechados
     */
    public function getLancamentosPagosNaoFechados()
    {
        $sql = "SELECT l.* FROM lancamentos l
                WHERE l.baixado = 1
                AND l.idLancamentos NOT IN (
                    SELECT lancamento_id FROM fechamento_lancamentos
                )";
        return $this->db->query($sql)->result();
    }

    /**
     * Marca os lançamentos como fechados, vinculando-os a um fechamento de caixa.
     * @param int $fechamento_id ID do fechamento de caixa
     * @param array $lancamentos_ids IDs dos lançamentos a vincular
     * @return void
     */
    public function marcarLancamentosComoFechados($fechamento_id, $lancamentos_ids)
    {
        if (empty($lancamentos_ids)) return;
        $data = array();
        foreach ($lancamentos_ids as $id) {
            $data[] = array(
                'fechamento_id' => $fechamento_id,
                'lancamento_id' => $id
            );
        }
        $this->db->insert_batch('fechamento_lancamentos', $data);
    }

    /**
     * Cria um novo registro de fechamento de caixa.
     * @param int $usuario_id ID do usuário que está fechando o caixa
     * @param float $valor_fechado Valor total do fechamento
     * @return int ID do fechamento criado
     */
    public function criarFechamentoCaixa($usuario_id, $valor_fechado)
    {
        $data = array(
            'data_fechamento' => date('Y-m-d H:i:s'),
            'usuario_id' => $usuario_id,
            'valor_fechado' => $valor_fechado
        );
        $this->db->insert('fechamentos_caixa', $data);
        return $this->db->insert_id();
    }
}
