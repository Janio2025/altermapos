<?php

class Mapos_model extends CI_Model
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

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->from('usuarios');
        $this->db->select('usuarios.*, permissoes.nome as permissao');
        $this->db->join('permissoes', 'permissoes.idPermissao = usuarios.permissoes_id', 'left');
        $this->db->where('idUsuarios', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function alterarSenha($senha)
    {
        $this->db->set('senha', password_hash($senha, PASSWORD_DEFAULT));
        $this->db->where('idUsuarios', $this->session->userdata('id_admin'));
        $this->db->update('usuarios');

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function pesquisar($termo)
    {
        $data = [];
        
        // buscando clientes
        $this->db->like('nomeCliente', $termo);
        $this->db->or_like('telefone', $termo);
        $this->db->or_like('celular', $termo);
        $this->db->or_like('documento', $termo);
        $this->db->limit(15);
        $data['clientes'] = $this->db->get('clientes')->result();

        // buscando os
        $this->db->like('idOs', $termo);
        $this->db->or_like('descricaoProduto', $termo);
        $this->db->limit(15);
        $data['os'] = $this->db->get('os')->result();

        // buscando produtos
        // Buscando produtos pelo nomeModelo ou modeloCompativel
        $this->db->select('produtos.*, modelo.nomeModelo, compativeis.modeloCompativel');
        $this->db->from('produtos');
        $this->db->join('modelo', 'modelo.idModelo = produtos.idModelo', 'left');  // LEFT JOIN para permitir produtos sem modelo
        $this->db->join('produto_compativel', 'produto_compativel.idProduto = produtos.idProdutos', 'left');  // LEFT JOIN para evitar exclusão
        $this->db->join('compativeis', 'compativeis.idCompativel = produto_compativel.idCompativel', 'left');  // LEFT JOIN para permitir produtos sem modeloCompativel
        $this->db->group_start();  // Início do bloco de pesquisa
        $this->db->like('modelo.nomeModelo', $termo);  // Pesquisando pelo nomeModelo
        $this->db->or_like('compativeis.modeloCompativel', $termo);  // Pesquisando pelo modeloCompativel
        $this->db->or_like('produtos.codDeBarra', $termo);  // Pesquisando também por codDeBarra
        $this->db->or_like('produtos.descricao', $termo);  // Pesquisando também por descrição
        $this->db->group_end();  // Fim do bloco de pesquisa
        $this->db->limit(50);  // Limita a 50 resultados
        $data['produtos'] = $this->db->get()->result();  // Executa a consulta e armazena os resultados

        // buscando serviços
        $this->db->like('nome', $termo);
        $this->db->limit(15);
        $data['servicos'] = $this->db->get('servicos')->result();

        return $data;
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
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }

    public function getOsOrcamentos()
    {
        $this->db->select('os.*, clientes.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where('os.status', 'Orçamento');
        $this->db->limit(10);

        return $this->db->get()->result();
    }
    
    public function getOsAbertas()
    {
        $this->db->select('os.*, clientes.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where('os.status', 'Aberto');
        $this->db->limit(10);

        return $this->db->get()->result();
    }

    public function getOsFinalizadas()
    {
        $this->db->select('os.*, clientes.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where('os.status', 'Finalizado');
        $this->db->order_by('os.idOs', 'DESC');
        $this->db->limit(10);

        return $this->db->get()->result();
    }

    public function getOsAprovadas()
    {
        $this->db->select('os.*, clientes.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where('os.status', 'Aprovado');
        $this->db->limit(10);

        return $this->db->get()->result();
    }

    public function getOsAguardandoPecas()
    {
        $this->db->select('os.*, clientes.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where('os.status', 'Aguardando Peças');
        $this->db->limit(10);

        return $this->db->get()->result();
    }

    public function getOsAndamento()
    {
        $this->db->select('os.*, clientes.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where('os.status', 'Em Andamento');
        $this->db->limit(10);

        return $this->db->get()->result();
    }

    public function getOsStatus($status)
    {
        $this->db->select('os.*, clientes.nomeCliente');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->where_in('os.status', $status);
        $this->db->order_by('os.idOs', 'DESC');
        $this->db->limit(10);

        return $this->db->get()->result();
    }
    
    public function getVendasStatus($vstatus)
    {
        $this->db->select('vendas.*, clientes.nomeCliente');
        $this->db->from('vendas');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
        $this->db->where_in('vendas.status', $vstatus);
        $this->db->order_by('vendas.idVendas', 'DESC');
        $this->db->limit(10);

        return $this->db->get()->result();
    }

    public function getLancamentos()
    {
        $this->db->select('idLancamentos, tipo, cliente_fornecedor, descricao, data_vencimento, forma_pgto, valor_desconto, baixado');
        $this->db->from('lancamentos');
        $this->db->where('baixado', 0);
        $this->db->order_by('idLancamentos', 'DESC');
        $this->db->limit(10);

        $query = $this->db->get();
        return $query->result();
    }

    public function calendario($start, $end, $status = null)
    {
        $this->db->select(
            'os.*,
            clientes.nomeCliente,
            COALESCE((SELECT SUM(produtos_os.preco * produtos_os.quantidade ) FROM produtos_os WHERE produtos_os.os_id = os.idOs), 0) totalProdutos,
            COALESCE((SELECT SUM(servicos_os.preco * servicos_os.quantidade ) FROM servicos_os WHERE servicos_os.os_id = os.idOs), 0) totalServicos'
        );
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->join('produtos_os', 'produtos_os.os_id = os.idOs', 'left');
        $this->db->join('servicos_os', 'servicos_os.os_id = os.idOs', 'left');
        $this->db->where('os.dataFinal >=', $start);
        $this->db->where('os.dataFinal <=', $end);
        $this->db->group_by('os.idOs');

        if (! empty($status)) {
            $this->db->where('os.status', $status);
        }

        return $this->db->get()->result();
    }

    public function getProdutosMinimo()
    {
        $sql = 'SELECT * FROM produtos WHERE estoque <= estoqueMinimo AND estoqueMinimo > 0 LIMIT 10';

        return $this->db->query($sql)->result();
    }

    public function getOsEstatisticas()
    {
        $sql = 'SELECT status, COUNT(status) as total FROM os GROUP BY status ORDER BY status';

        return $this->db->query($sql)->result();
    }

    public function getEstatisticasFinanceiro()
    {
        $sql = "SELECT 
                    (
                        SELECT SUM(CASE WHEN baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) 
                        FROM lancamentos
                    ) + COALESCE(
                        (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado')), 0
                    ) as total_receita,
                    
                    (
                        SELECT SUM(CASE WHEN baixado = 1 AND tipo = 'despesa' THEN valor END) 
                        FROM lancamentos
                    ) as total_despesa,
                    
                    (
                        SELECT SUM(CASE WHEN baixado = 0 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) 
                        FROM lancamentos
                    ) as total_receita_pendente,
                    
                    (
                        SELECT SUM(CASE WHEN baixado = 0 AND tipo = 'despesa' THEN valor END) 
                        FROM lancamentos
                    ) as total_despesa_pendente";

        if ($this->db->query($sql) !== false) {
            return $this->db->query($sql)->row();
        }
        return false;
    }

    public function getEstatisticasFinanceiroMes($year)
    {
        $numbersOnly = preg_replace('/[^0-9]/', '', $year);

        if (!$numbersOnly) {
            $numbersOnly = date('Y');
        }

        $sql = "
            SELECT
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 1) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_JAN_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 1 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_JAN_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 1) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_JAN_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 2) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_FEV_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 2 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_FEV_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 2) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_FEV_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 3) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_MAR_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 3 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_MAR_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 3) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_MAR_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 4) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_ABR_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 4 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_ABR_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 4) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_ABR_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 5) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_MAI_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 5 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_MAI_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 5) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_MAI_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 6) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_JUN_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 6 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_JUN_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 6) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_JUN_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 7) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_JUL_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 7 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_JUL_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 7) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_JUL_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 8) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_AGO_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 8 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_AGO_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 8) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_AGO_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 9) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_SET_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 9 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_SET_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 9) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_SET_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 10) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_OUT_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 10 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_OUT_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 10) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_OUT_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 11) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_NOV_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 11 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_NOV_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 11) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_NOV_DES,
                
                (SELECT SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 12) AND baixado = 1 AND tipo = 'receita' THEN valor - (IF(tipo_desconto = 'real', desconto, (desconto * valor) / 100)) END) FROM lancamentos WHERE EXTRACT(YEAR FROM data_pagamento) = ?) AS VALOR_DEZ_REC,
                (SELECT SUM(valorTotal) FROM os WHERE status IN ('Faturado', 'Finalizado') AND EXTRACT(MONTH FROM dataFinal) = 12 AND EXTRACT(YEAR FROM dataFinal) = ?) AS VALOR_DEZ_OS,
                
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 12) AND baixado = 1 AND tipo = 'despesa' THEN valor END) AS VALOR_DEZ_DES
            FROM lancamentos
            WHERE EXTRACT(YEAR FROM data_pagamento) = ?
        ";

        $params = array_fill(0, 25, intval($numbersOnly));

        if ($this->db->query($sql, $params) !== false) {
            return $this->db->query($sql, $params)->row();
        }

        return false;
    }

    public function getEstatisticasFinanceiroDia($year)
    {
        $numbersOnly = preg_replace('/[^0-9]/', '', $year);
        if (!$numbersOnly) {
            $numbersOnly = date('Y');
        }
        $sql = '
            SELECT
                (
                    SELECT SUM(CASE 
                        WHEN (EXTRACT(DAY FROM data_pagamento) = ' . date('d') . ') 
                        AND EXTRACT(MONTH FROM data_pagamento) = ' . date('m') . ' 
                        AND baixado = 1 
                        AND tipo = "receita" 
                        THEN IF(valor_desconto > 0, valor_desconto, valor - (IF(tipo_desconto = "real", desconto, (desconto * valor) / 100)))
                        ELSE 0 
                    END)
                    FROM lancamentos 
                    WHERE EXTRACT(YEAR FROM data_pagamento) = ?
                ) AS VALOR_' . date('m') . '_REC,
                
                (
                    SELECT SUM(CASE 
                        WHEN (EXTRACT(DAY FROM data_pagamento) = ' . date('d') . ') 
                        AND EXTRACT(MONTH FROM data_pagamento) = ' . date('m') . ' 
                        AND baixado = 1 
                        AND tipo = "despesa" 
                        THEN valor 
                        ELSE 0 
                    END)
                    FROM lancamentos 
                    WHERE EXTRACT(YEAR FROM data_pagamento) = ?
                ) AS VALOR_' . date('m') . '_DES
        ';

        if ($this->db->query($sql, [$numbersOnly, $numbersOnly]) !== false) {
            return $this->db->query($sql, [$numbersOnly, $numbersOnly])->row();
        }

        return false;
    }

    public function getEstatisticasFinanceiroMesInadimplencia($year)
    {
        $numbersOnly = preg_replace('/[^0-9]/', '', $year);

        if (! $numbersOnly) {
            $numbersOnly = date('Y');
        }

        $sql = "
            SELECT
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 1) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_JAN_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 1) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_JAN_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 2) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_FEV_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 2) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_FEV_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 3) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_MAR_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 3) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_MAR_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 4) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_ABR_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 4) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_ABR_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 5) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_MAI_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 5) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_MAI_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 6) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_JUN_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 6) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_JUN_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 7) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_JUL_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 7) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_JUL_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 8) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_AGO_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 8) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_AGO_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 9) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_SET_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 9) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_SET_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 10) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_OUT_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 10) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_OUT_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 11) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_NOV_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 11) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_NOV_DES,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 12) AND baixado = 0 AND tipo = 'receita' THEN valor END) AS VALOR_DEZ_REC,
                SUM(CASE WHEN (EXTRACT(MONTH FROM data_pagamento) = 12) AND baixado = 0 AND tipo = 'despesa' THEN valor END) AS VALOR_DEZ_DES
            FROM lancamentos
            WHERE EXTRACT(YEAR FROM data_pagamento) = ?
        ";
        if ($this->db->query($sql, [intval($numbersOnly)]) !== false) {
            return $this->db->query($sql, [intval($numbersOnly)])->row();
        }

        return false;
    }

    public function getEmitente()
    {
        return $this->db->get('emitente')->row();
    }

    public function addEmitente($nome, $cnpj, $ie, $cep, $logradouro, $numero, $bairro, $cidade, $uf, $telefone, $email, $logo)
    {
        $this->db->set('nome', $nome);
        $this->db->set('cnpj', $cnpj);
        $this->db->set('ie', $ie);
        $this->db->set('cep', $cep);
        $this->db->set('rua', $logradouro);
        $this->db->set('numero', $numero);
        $this->db->set('bairro', $bairro);
        $this->db->set('cidade', $cidade);
        $this->db->set('uf', $uf);
        $this->db->set('telefone', $telefone);
        $this->db->set('email', $email);
        $this->db->set('url_logo', $logo);

        return $this->db->insert('emitente');
    }

    public function editEmitente($id, $nome, $cnpj, $ie, $cep, $logradouro, $numero, $bairro, $cidade, $uf, $telefone, $email)
    {
        $this->db->set('nome', $nome);
        $this->db->set('cnpj', $cnpj);
        $this->db->set('ie', $ie);
        $this->db->set('cep', $cep);
        $this->db->set('rua', $logradouro);
        $this->db->set('numero', $numero);
        $this->db->set('bairro', $bairro);
        $this->db->set('cidade', $cidade);
        $this->db->set('uf', $uf);
        $this->db->set('telefone', $telefone);
        $this->db->set('email', $email);
        $this->db->where('id', $id);

        return $this->db->update('emitente');
    }

    public function editLogo($id, $logo)
    {
        $this->db->set('url_logo', $logo);
        $this->db->where('id', $id);

        return $this->db->update('emitente');
    }

    public function editImageUser($id, $imageUserPath)
    {
        $this->db->set('url_image_user', $imageUserPath);
        $this->db->where('idUsuarios', $id);

        return $this->db->update('usuarios');
    }

    public function check_credentials($email)
    {
        $this->db->where('email', $email);
        $this->db->where('situacao', 1);
        $this->db->limit(1);

        return $this->db->get('usuarios')->row();
    }

    /**
     * Salvar configurações do sistema
     *
     * @param  array  $data
     * @return bool
     */
    public function saveConfiguracao($data)
    {
        try {
            foreach ($data as $key => $valor) {
                $this->db->set('valor', $valor);
                $this->db->where('config', $key);
                $this->db->update('configuracoes');
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
