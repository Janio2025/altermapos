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
        if ($where) {
            $this->db->where($where);
        }
        if ($perpage > 0) {
            $this->db->limit($perpage, $start);
        }
        if ($one) {
            $result = $this->db->get()->row();
        } else {
            $result = $this->db->get()->result();
        }
        return $result;
    }

    public function getAll($limit = 0, $start = 0)
    {
        $this->db->select('cu.*, u.nome as nome_usuario,
            COALESCE((SELECT SUM(valor) FROM transacoes_usuario 
                WHERE carteira_usuario_id = cu.idCarteiraUsuario 
                AND tipo = "bonus" 
                AND MONTH(data_transacao) = MONTH(CURRENT_DATE())
                AND YEAR(data_transacao) = YEAR(CURRENT_DATE())), 0) as total_bonus,
            COALESCE((SELECT SUM(valor) FROM transacoes_usuario 
                WHERE carteira_usuario_id = cu.idCarteiraUsuario 
                AND tipo = "comissao" 
                AND MONTH(data_transacao) = MONTH(CURRENT_DATE())
                AND YEAR(data_transacao) = YEAR(CURRENT_DATE())), 0) as total_comissoes');
        $this->db->from('carteira_usuario cu');
        $this->db->join('usuarios u', 'u.idUsuarios = cu.usuarios_id');
        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->select('carteira_usuario.*, usuarios.nome, usuarios.idUsuarios as usuarios_id');
        $this->db->from('carteira_usuario');
        $this->db->join('usuarios', 'usuarios.idUsuarios = carteira_usuario.usuarios_id');
        $this->db->where('carteira_usuario.idCarteiraUsuario', $id);
        $carteira = $this->db->get()->row();

        if ($carteira) {
            // Busca a configuração da carteira para calcular a comissão
            $config = $this->getConfiguracao($id);
            if ($config) {
                // Calcula o valor base usando o método calcularValorBase
                $valor_base = $this->calcularValorBase($config->tipo_valor_base, $carteira->usuarios_id);
                // Calcula a comissão pendente baseada no valor base e na comissão fixa
                $carteira->comissao_pendente = ($valor_base * $config->comissao_fixa) / 100;
            } else {
                $carteira->comissao_pendente = 0;
            }
        }

        return $carteira;
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return $this->db->insert_id();
        }
        return FALSE;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);
        if ($this->db->affected_rows() >= 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function delete($table, $fieldID, $ID)
    {
        // Se for uma carteira, primeiro exclui os registros relacionados
        if ($table == 'carteira_usuario') {
            $this->db->trans_begin();
            
            try {
                // Remove configurações da carteira
                $this->db->where('carteira_usuario_id', $ID);
                $this->db->delete('configuracao_carteira');
                
                // Remove transações da carteira
                $this->db->where('carteira_usuario_id', $ID);
                $this->db->delete('transacoes_usuario');
                
                // Por fim, remove a carteira
                $this->db->where($fieldID, $ID);
                $this->db->delete($table);
                
                if ($this->db->affected_rows() > 0) {
                    $this->db->trans_commit();
                    return TRUE;
                }
                
                $this->db->trans_rollback();
                return FALSE;
                
            } catch (Exception $e) {
                $this->db->trans_rollback();
                return FALSE;
            }
        }
        
        // Para outras tabelas, mantém o comportamento padrão
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        }
        return FALSE;
    }

    public function count($table, $where = '')
    {
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->count_all_results($table);
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

    public function getTransacoes($carteira_id, $data_inicio = null, $data_fim = null, $tipo = null)
    {
        $this->db->select('t.*, u.nome as nome_usuario');
        $this->db->from('transacoes_usuario t');
        $this->db->join('carteira_usuario cu', 'cu.idCarteiraUsuario = t.carteira_usuario_id');
        $this->db->join('usuarios u', 'u.idUsuarios = cu.usuarios_id');
        $this->db->where('t.carteira_usuario_id', $carteira_id);
        
        if ($data_inicio) {
            $this->db->where('t.data_transacao >=', $data_inicio);
        }
        if ($data_fim) {
            $this->db->where('t.data_transacao <=', $data_fim);
        }
        if ($tipo) {
            $this->db->where('t.tipo', $tipo);
        }
        
        $this->db->order_by('t.data_transacao DESC, t.idTransacoesUsuario DESC');
        return $this->db->get()->result();
    }

    public function getByUsuarioId($usuario_id)
    {
        return $this->get('carteira_usuario', '*', array('usuarios_id' => $usuario_id), 0, 0, TRUE);
    }

    public function getConfiguracao($carteira_id)
    {
        return $this->get('configuracao_carteira', '*', array('carteira_usuario_id' => $carteira_id), 0, 0, TRUE);
    }

    public function salvarConfiguracao($data)
    {
        $config = $this->getConfiguracao($data['carteira_usuario_id']);
        if ($config) {
            return $this->edit('configuracao_carteira', $data, 'carteira_usuario_id', $data['carteira_usuario_id']);
        }
        return $this->add('configuracao_carteira', $data);
    }

    public function registrarTransacao($data)
    {
        $this->db->trans_begin();

        try {
            // Busca a carteira e configuração antes de qualquer operação
            $carteira = $this->getById($data['carteira_usuario_id']);
            if (!$carteira) {
                throw new Exception('Carteira não encontrada');
            }
            
            $config = $this->getConfiguracao($data['carteira_usuario_id']);
            if (!$config) {
                throw new Exception('Configuração não encontrada');
            }

            // Se for comissão, finaliza as OS relacionadas e atualiza a descrição
            if ($data['tipo'] == 'comissao') {
                // Busca as OS relacionadas
                if ($config->tipo_valor_base == 'servicos') {
                    $this->db->select('DISTINCT os.idOs');
                    $this->db->from('servicos_os');
                    $this->db->join('os', 'os.idOs = servicos_os.os_id');
                    $this->db->where('os.usuarios_id', $carteira->usuarios_id);
                    $this->db->where('MONTH(os.dataFinal)', date('m'));
                    $this->db->where('YEAR(os.dataFinal)', date('Y'));
                    $this->db->where('os.status', 'Faturado');
                } else {
                    $this->db->select('idOs');
                    $this->db->from('os');
                    $this->db->where('usuarios_id', $carteira->usuarios_id);
                    $this->db->where('MONTH(dataFinal)', date('m'));
                    $this->db->where('YEAR(dataFinal)', date('Y'));
                    $this->db->where('status', 'Faturado');
                }

                $query = $this->db->get();
                $ordens = $query->result();
                
                if (!empty($ordens)) {
                    // Formata os IDs das OS para a descrição
                    $os_ids = array_map(function($ordem) {
                        return $ordem->idOs;
                    }, $ordens);
                    
                    // Atualiza a descrição com os IDs das OS
                    $data['descricao'] = 'OS: ' . implode(', ', $os_ids);

                    // Atualiza o status das OS para Finalizado
                    $this->db->where_in('idOs', $os_ids);
                    if (!$this->db->update('os', ['status' => 'Finalizado'])) {
                        throw new Exception('Erro ao finalizar OS');
                    }
                }
            }

            // Insere a transação
            if (!$this->db->insert('transacoes_usuario', $data)) {
                throw new Exception('Erro ao inserir transação');
            }

            $novo_saldo = $carteira->saldo;

            // Atualiza o saldo baseado no tipo de transação
            if ($data['tipo'] == 'retirada') {
                $novo_saldo -= $data['valor'];
                
                // Atualiza também o salário base na configuração
                $novo_salario_base = $config->salario_base - $data['valor'];
                $this->salvarConfiguracao([
                    'carteira_usuario_id' => $data['carteira_usuario_id'],
                    'salario_base' => $novo_salario_base,
                    'comissao_fixa' => $config->comissao_fixa,
                    'data_salario' => $config->data_salario,
                    'tipo_repeticao' => $config->tipo_repeticao,
                    'tipo_valor_base' => $config->tipo_valor_base
                ]);
            } 
            else if (in_array($data['tipo'], array('salario', 'bonus', 'comissao'))) {
                $novo_saldo += $data['valor'];
            }

            // Atualiza o saldo da carteira
            if (!$this->edit('carteira_usuario', array('saldo' => $novo_saldo), 'idCarteiraUsuario', $data['carteira_usuario_id'])) {
                throw new Exception('Erro ao atualizar saldo');
            }

            $this->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function calcularComissaoOS($usuario_id, $tipo_valor_base = 'servicos')
    {
        $valor_base = 0;
        
        if ($tipo_valor_base == 'servicos') {
            // Soma apenas os serviços das OS do usuário do mês atual
            $this->db->select_sum('servicos_os.subTotal');
            $this->db->from('servicos_os');
            $this->db->join('os', 'os.idOs = servicos_os.os_id');
            $this->db->where('os.usuarios_id', $usuario_id);
            $this->db->where('MONTH(os.dataFinal)', date('m'));
            $this->db->where('YEAR(os.dataFinal)', date('Y'));
            $this->db->where('os.status', 'Faturado');
            $query = $this->db->get();
            $result = $query->row();
            $valor_base = $result->subTotal ?: 0;
        } else {
            // Calcula baseado no valor total das OS menos o custo dos produtos
            $this->db->select('os.idOs, os.valorTotal');
            $this->db->from('os');
            $this->db->where('usuarios_id', $usuario_id);
            $this->db->where('MONTH(dataFinal)', date('m'));
            $this->db->where('YEAR(dataFinal)', date('Y'));
            $this->db->where('status', 'Faturado');
            $ordens = $this->db->get()->result();

            foreach ($ordens as $ordem) {
                $valor_base += $ordem->valorTotal;

                // Subtrai o custo dos produtos
                $this->db->select_sum('produtos.precoCompra');
                $this->db->from('produtos_os');
                $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
                $this->db->where('produtos_os.os_id', $ordem->idOs);
                $query_produtos = $this->db->get();
                $result_produtos = $query_produtos->row();
                
                if ($result_produtos && $result_produtos->precoCompra) {
                    $valor_base -= $result_produtos->precoCompra;
                }
            }
        }

        return $valor_base;
    }

    public function processarPagamentoAutomatico()
    {
        // Busca todas as carteiras ativas
        $carteiras = $this->getAll();
        $data_atual = date('d');
        
        foreach ($carteiras as $carteira) {
            $config = $this->getConfiguracao($carteira->idCarteiraUsuario);
            if (!$config) continue;

            // Verifica se é dia de pagamento
            if ($config->data_salario == $data_atual || 
                ($config->tipo_repeticao == 'quinzenal' && ($data_atual == $config->data_salario || $data_atual == 15))) {
                
                // Registra o salário base
                if ($config->salario_base > 0) {
                    $this->registrarTransacao(array(
                        'tipo' => 'salario',
                        'valor' => $config->salario_base,
                        'data_transacao' => date('Y-m-d'),
                        'descricao' => 'Salário Base - ' . date('m/Y'),
                        'carteira_usuario_id' => $carteira->idCarteiraUsuario,
                        'considerado_saldo' => 1
                    ));
                }

                // Calcula e registra a comissão
                if ($config->comissao_fixa > 0) {
                    $valor_base = $this->calcularComissaoOS($carteira->usuarios_id, $config->tipo_valor_base);
                    $valor_comissao = ($valor_base * $config->comissao_fixa) / 100;
                    
                    if ($valor_comissao > 0) {
                        $this->registrarTransacao(array(
                            'tipo' => 'comissao',
                            'valor' => $valor_comissao,
                            'data_transacao' => date('Y-m-d'),
                            'descricao' => 'Comissão - ' . date('m/Y'),
                            'carteira_usuario_id' => $carteira->idCarteiraUsuario,
                            'considerado_saldo' => 1
                        ));
                    }
                }
            }
        }
    }

    public function validarRetirada($carteira_id, $valor)
    {
        $config = $this->getConfiguracao($carteira_id);
        if (!$config) return false;
        
        // Verifica se o salário base após a retirada não ficaria negativo
        return ($config->salario_base - $valor) >= 0;
    }

    public function getTransacaoById($id)
    {
        $this->db->select('t.*, cu.saldo as saldo_atual');
        $this->db->from('transacoes_usuario t');
        $this->db->join('carteira_usuario cu', 'cu.idCarteiraUsuario = t.carteira_usuario_id');
        $this->db->where('t.idTransacoesUsuario', $id);
        return $this->db->get()->row();
    }

    public function calcularValorBase($tipo, $usuario_id)
    {
        $valor_base = 0;
        
        if ($tipo == 'servicos') {
            // Soma apenas os serviços das OS do usuário do mês atual que estão Faturadas
            $this->db->select_sum('servicos_os.subTotal');
            $this->db->from('servicos_os');
            $this->db->join('os', 'os.idOs = servicos_os.os_id');
            $this->db->where('os.usuarios_id', $usuario_id);
            $this->db->where('MONTH(os.dataFinal)', date('m'));
            $this->db->where('YEAR(os.dataFinal)', date('Y'));
            $this->db->where('os.status', 'Faturado');
            $query = $this->db->get();
            $result = $query->row();
            $valor_base = $result->subTotal ?: 0;
        } else {
            // Para outros tipos, calcula baseado no valor total das OS menos produtos
            $this->db->select('os.idOs, os.valorTotal');
            $this->db->from('os');
            $this->db->where('usuarios_id', $usuario_id);
            $this->db->where('MONTH(dataFinal)', date('m'));
            $this->db->where('YEAR(dataFinal)', date('Y'));
            $this->db->where('status', 'Faturado');
            $ordens = $this->db->get()->result();

            foreach ($ordens as $ordem) {
                $valor_base += $ordem->valorTotal;

                // Subtrai o custo dos produtos
                $this->db->select_sum('produtos.precoCompra');
                $this->db->from('produtos_os');
                $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
                $this->db->where('produtos_os.os_id', $ordem->idOs);
                $query_produtos = $this->db->get();
                $result_produtos = $query_produtos->row();
                
                if ($result_produtos && $result_produtos->precoCompra) {
                    $valor_base -= $result_produtos->precoCompra;
                }
            }
        }

        return $valor_base;
    }

    public function finalizarOsComissao($tipo, $usuario_id)
    {
        // Busca as OS relacionadas
        if ($tipo == 'servicos') {
            $this->db->select('DISTINCT os.idOs');
            $this->db->from('servicos_os');
            $this->db->join('os', 'os.idOs = servicos_os.os_id');
            $this->db->where('os.usuarios_id', $usuario_id);
            $this->db->where('MONTH(os.dataFinal)', date('m'));
            $this->db->where('YEAR(os.dataFinal)', date('Y'));
            $this->db->where('os.status', 'Faturado');
        } else {
            $this->db->select('idOs');
            $this->db->from('os');
            $this->db->where('usuarios_id', $usuario_id);
            $this->db->where('MONTH(dataFinal)', date('m'));
            $this->db->where('YEAR(dataFinal)', date('Y'));
            $this->db->where('status', 'Faturado');
        }

        $query = $this->db->get();
        $ordens = $query->result();
        
        if (empty($ordens)) {
            return true;
        }

        $os_ids = array_map(function($ordem) {
            return $ordem->idOs;
        }, $ordens);

        // Atualiza o status das OS para Finalizado
        $this->db->where_in('idOs', $os_ids);
        return $this->db->update('os', ['status' => 'Finalizado']);
    }

    public function getConfig()
    {
        return $this->db->get('configuracao_carteira')->row();
    }

    public function getConfigByUsuarioId($usuario_id)
    {
        $carteira = $this->getByUsuarioId($usuario_id);
        if (!$carteira) {
            return null;
        }
        return $this->getConfiguracao($carteira->idCarteiraUsuario);
    }
}
