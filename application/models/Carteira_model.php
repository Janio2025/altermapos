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

    public function getAll()
    {
        $this->db->select('carteira_usuario.*, usuarios.nome as nome_usuario, usuarios.idUsuarios as usuarios_id, configuracao_carteira.salario_base');
        $this->db->from('carteira_usuario');
        $this->db->join('usuarios', 'usuarios.idUsuarios = carteira_usuario.usuarios_id');
        $this->db->join('configuracao_carteira', 'configuracao_carteira.carteira_usuario_id = carteira_usuario.idCarteiraUsuario', 'left');
        $this->db->where('carteira_usuario.ativo', 1); // Apenas carteiras ativas
        $carteiras = $this->db->get()->result();

        // Calcula os totais para cada carteira
        foreach ($carteiras as $carteira) {
            // Total de bônus do mês atual
            $this->db->select_sum('valor');
            $this->db->from('transacoes_usuario');
            $this->db->where('carteira_usuario_id', $carteira->idCarteiraUsuario);
            $this->db->where('tipo', 'bonus');
            $this->db->where('MONTH(data_transacao)', date('m'));
            $this->db->where('YEAR(data_transacao)', date('Y'));
            $bonus = $this->db->get()->row();
            $carteira->total_bonus = $bonus ? $bonus->valor : 0;

            // Total de comissões do mês atual
            $this->db->select_sum('valor');
            $this->db->from('transacoes_usuario');
            $this->db->where('carteira_usuario_id', $carteira->idCarteiraUsuario);
            $this->db->where('tipo', 'comissao');
            $this->db->where('MONTH(data_transacao)', date('m'));
            $this->db->where('YEAR(data_transacao)', date('Y'));
            $comissoes = $this->db->get()->row();
            $carteira->total_comissoes = $comissoes ? $comissoes->valor : 0;

            // Se não houver salário base configurado, define como 0
            if (!isset($carteira->salario_base)) {
                $carteira->salario_base = 0;
            }
        }

        return $carteiras;
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
                $valor_base = $this->calcularValorBase($carteira->usuarios_id, $config->tipo_valor_base);
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
        // Inicia a transação no banco
        $this->db->trans_begin();
        
        try {
            // Insere a transação
            if (!$this->db->insert('transacoes_usuario', $data)) {
                throw new Exception('Erro ao registrar a transação.');
            }
            
            $id_transacao = $this->db->insert_id();
            
            // Atualiza o saldo da carteira
            $valor = $data['tipo'] == 'retirada' ? -$data['valor'] : $data['valor'];
            if (!$this->updateSaldo($data['carteira_usuario_id'], $valor)) {
                throw new Exception('Erro ao atualizar o saldo da carteira.');
            }
            
            $this->db->trans_commit();
            return $id_transacao;
            
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
                $this->db->select('produtos_os.quantidade, produtos.precoCompra');
                $this->db->from('produtos_os');
                $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
                $this->db->where('produtos_os.os_id', $ordem->idOs);
                $query_produtos = $this->db->get();
                $produtos = $query_produtos->result();
                
                // Subtrai o custo total dos produtos
                $custo_total = 0;
                foreach ($produtos as $produto) {
                    $custo_total += ($produto->precoCompra * $produto->quantidade);
                }
                $valor_base -= $custo_total;
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
                        'data_transacao' => date('Y-m-d H:i:s'),
                        'descricao' => 'Salário Base - ' . date('m/Y'),
                        'carteira_usuario_id' => $carteira->idCarteiraUsuario,
                        'considerado_saldo' => 1
                    ));
                }

                // Calcula e registra a comissão do usuário principal
                if ($config->comissao_fixa > 0) {
                    $valor_base = $this->calcularValorBase($carteira->usuarios_id, $config->tipo_valor_base);
                    $valor_comissao = ($valor_base * $config->comissao_fixa) / 100;
                    
                    if ($valor_comissao > 0) {
                        $this->registrarTransacao(array(
                            'tipo' => 'comissao',
                            'valor' => $valor_comissao,
                            'data_transacao' => date('Y-m-d H:i:s'),
                            'descricao' => 'Comissão - ' . date('m/Y'),
                            'carteira_usuario_id' => $carteira->idCarteiraUsuario,
                            'considerado_saldo' => 1
                        ));
                    }
                }

                // Busca e processa as comissões dos usuários adicionais
                $this->db->select('c.*, u.nome');
                $this->db->from('configuracao_carteira_usuarios_adicionais c');
                $this->db->join('usuarios u', 'u.idUsuarios = c.usuario_id');
                $this->db->where('c.carteira_usuario_id', $carteira->idCarteiraUsuario);
                $configs_adicionais = $this->db->get()->result();

                foreach ($configs_adicionais as $config_adicional) {
                    if ($config_adicional->comissao_porcentagem > 0) {
                        $valor_base = $this->calcularValorBase($config_adicional->usuario_id, $config_adicional->tipo_valor_base);
                        $valor_comissao = ($valor_base * $config_adicional->comissao_porcentagem) / 100;

                        if ($valor_comissao > 0) {
                            // Busca a carteira do usuário adicional
                            $carteira_adicional = $this->getByUsuarioId($config_adicional->usuario_id);
                            if ($carteira_adicional) {
                                $this->registrarTransacao(array(
                                    'tipo' => 'comissao',
                                    'valor' => $valor_comissao,
                                    'data_transacao' => date('Y-m-d H:i:s'),
                                    'descricao' => 'Comissão (Técnico Adicional) - ' . date('m/Y'),
                                    'carteira_usuario_id' => $carteira_adicional->idCarteiraUsuario,
                                    'considerado_saldo' => 1
                                ));
                            }
                        }
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

    public function calcularValorBase($usuario_id, $tipo)
    {
        if (!$usuario_id || !$tipo) {
            return 0;
        }

        $valor = 0;

        // Subquery para pegar todas as OS do usuário (principal ou adicional)
        $this->db->select('os_id');
        $this->db->from('os_usuarios');
        $this->db->where('usuario_id', $usuario_id);
        $subquery = $this->db->get_compiled_select();

        if ($tipo == 'servicos') {
            // Soma apenas os serviços das OS do usuário
            $this->db->select_sum('servicos_os.subTotal');
            $this->db->from('servicos_os');
            $this->db->join('os', 'os.idOs = servicos_os.os_id');
            $this->db->where('os.status', 'Faturado');
            $this->db->where("os.idOs IN ($subquery)"); // Usa a subquery
            $query = $this->db->get();
            $result = $query->row();
            $valor = $result->subTotal ?: 0;
        } else if ($tipo == 'total') {
            // Primeiro, pega todas as OS do usuário
            $this->db->select('os.idOs, os.valorTotal');
            $this->db->from('os');
            $this->db->where('status', 'Faturado');
            $this->db->where("idOs IN ($subquery)"); // Usa a subquery
            $query = $this->db->get();
            $ordens = $query->result();

            foreach ($ordens as $ordem) {
                // Soma o valor total da OS
                $valor += $ordem->valorTotal;
                
                // Busca e subtrai o precoCompra dos produtos desta OS
                $this->db->select('produtos_os.quantidade, produtos.precoCompra');
                $this->db->from('produtos_os');
                $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
                $this->db->where('produtos_os.os_id', $ordem->idOs);
                $query_produtos = $this->db->get();
                $produtos = $query_produtos->result();
                
                // Subtrai o custo total dos produtos
                $custo_total = 0;
                foreach ($produtos as $produto) {
                    $custo_total += ($produto->precoCompra * $produto->quantidade);
                }
                $valor -= $custo_total;
            }
        }

        return $valor;
    }

    public function finalizarOsComissao($tipo, $usuario_id)
    {
        // Subquery para pegar todas as OS do usuário (principal ou adicional)
        $this->db->select('os_id');
        $this->db->from('os_usuarios');
        $this->db->where('usuario_id', $usuario_id);
        $subquery = $this->db->get_compiled_select();

        // Busca as OS relacionadas
        if ($tipo == 'servicos') {
            $this->db->select('DISTINCT os.idOs');
            $this->db->from('servicos_os');
            $this->db->join('os', 'os.idOs = servicos_os.os_id');
            $this->db->where('MONTH(os.dataFinal)', date('m'));
            $this->db->where('YEAR(os.dataFinal)', date('Y'));
            $this->db->where('os.status', 'Faturado');
            $this->db->where("os.idOs IN ($subquery)"); // Usa a subquery
        } else {
            $this->db->select('idOs');
            $this->db->from('os');
            $this->db->where('MONTH(dataFinal)', date('m'));
            $this->db->where('YEAR(dataFinal)', date('Y'));
            $this->db->where('status', 'Faturado');
            $this->db->where("idOs IN ($subquery)"); // Usa a subquery
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

    public function getValorBase($usuario_id, $tipo)
    {
        if (!$usuario_id || !$tipo) {
            return 0;
        }

        $valor = 0;

        // Subquery para pegar todas as OS do usuário (principal ou adicional)
        $this->db->select('os_id');
        $this->db->from('os_usuarios');
        $this->db->where('usuario_id', $usuario_id);
        $subquery = $this->db->get_compiled_select();

        if ($tipo == 'servicos') {
            // Soma apenas os serviços das OS do usuário
            $this->db->select_sum('servicos_os.subTotal');
            $this->db->from('servicos_os');
            $this->db->join('os', 'os.idOs = servicos_os.os_id');
            $this->db->where('os.status', 'Faturado');
            $this->db->where("os.idOs IN ($subquery)"); // Usa a subquery
            $query = $this->db->get();
            $result = $query->row();
            $valor = $result->subTotal ?: 0;
        } else if ($tipo == 'total') {
            // Primeiro, pega todas as OS do usuário
            $this->db->select('os.idOs, os.valorTotal');
            $this->db->from('os');
            $this->db->where('status', 'Faturado');
            $this->db->where("idOs IN ($subquery)"); // Usa a subquery
            $query = $this->db->get();
            $ordens = $query->result();

            foreach ($ordens as $ordem) {
                // Soma o valor total da OS
                $valor += $ordem->valorTotal;
                
                // Busca e subtrai o precoCompra dos produtos desta OS
                $this->db->select('produtos_os.quantidade, produtos.precoCompra');
                $this->db->from('produtos_os');
                $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
                $this->db->where('produtos_os.os_id', $ordem->idOs);
                $query_produtos = $this->db->get();
                $produtos = $query_produtos->result();
                
                // Subtrai o custo total dos produtos
                $custo_total = 0;
                foreach ($produtos as $produto) {
                    $custo_total += ($produto->precoCompra * $produto->quantidade);
                }
                $valor -= $custo_total;
            }
        }

        return $valor;
    }

    public function getComissaoPendente($carteira_id)
    {
        if (!$carteira_id) {
            return 0;
        }

        // Busca a carteira
        $carteira = $this->getById($carteira_id);
        if (!$carteira) {
            return 0;
        }

        // Busca a configuração da carteira
        $config = $this->getConfiguracao($carteira_id);
        if (!$config) {
            return 0;
        }

        // Calcula o valor base
        $valor_base = $this->calcularValorBase($carteira->usuarios_id, $config->tipo_valor_base);

        // Calcula a comissão pendente
        return ($valor_base * $config->comissao_fixa) / 100;
    }

    public function getUltimaTransacao($carteira_id) {
        $this->db->select('valor, descricao, codigo_pix, data_transacao');
        $this->db->where('carteira_usuario_id', $carteira_id);
        $this->db->where('tipo', 'retirada');
        $this->db->order_by('idTransacoesUsuario', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('transacoes_usuario');
        return $query->row();
    }

    public function getRetiradaById($id, $carteira_id) {
        $this->db->select('valor, descricao, codigo_pix, data_transacao');
        $this->db->where('idTransacoesUsuario', $id);
        $this->db->where('carteira_usuario_id', $carteira_id);
        $this->db->where('tipo', 'retirada');
        $query = $this->db->get('transacoes_usuario');
        return $query->row();
    }
}
