<?php
define('BASEPATH', true);
require_once('application/config/database.php');

// Carrega configuração do banco de dados
$db = $db['default'];
$mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($mysqli->connect_error) {
    die('Erro de conexão: ' . $mysqli->connect_error);
}

// Busca todas as configurações
$query = "SELECT * FROM configuracao_carteira";
$result = $mysqli->query($query);

$dia_atual = (int)date('d');
$data_atual = date('Y-m-d H:i:s');

while ($config = $result->fetch_object()) {
    // Verifica se é dia de pagamento
    if ($config->data_salario == $dia_atual) {
        $valor_pagamento = $config->salario_base;
        $comissao = $config->comissao_fixa;
        
        // Se for quinzenal, divide o valor
        if ($config->tipo_repeticao == 'quinzenal') {
            $valor_pagamento = $config->salario_base / 2;
            $comissao = $config->comissao_fixa / 2;
        }
        
        // Inicia a transação
        $mysqli->begin_transaction();
        
        try {
            // Registra o salário
            $query = "INSERT INTO transacoes_usuario (tipo, valor, data_transacao, descricao, carteira_usuario_id) 
                     VALUES ('salario', ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $descricao = 'Salário ' . ($config->tipo_repeticao == 'quinzenal' ? 'Quinzenal' : 'Mensal');
            $stmt->bind_param('dssi', $valor_pagamento, $data_atual, $descricao, $config->carteira_usuario_id);
            $stmt->execute();
            
            // Registra a comissão fixa se houver
            if ($comissao > 0) {
                // Busca as OS relacionadas
                $query = "SELECT DISTINCT os.idOs 
                         FROM " . ($config->tipo_valor_base == 'servicos' ? 'servicos_os' : 'os') . " 
                         " . ($config->tipo_valor_base == 'servicos' ? "JOIN os ON os.idOs = servicos_os.os_id" : "") . "
                         WHERE " . ($config->tipo_valor_base == 'servicos' ? "os.usuarios_id" : "usuarios_id") . " = ?
                         AND MONTH(" . ($config->tipo_valor_base == 'servicos' ? "os.dataFinal" : "dataFinal") . ") = MONTH(CURRENT_DATE())
                         AND YEAR(" . ($config->tipo_valor_base == 'servicos' ? "os.dataFinal" : "dataFinal") . ") = YEAR(CURRENT_DATE())
                         AND " . ($config->tipo_valor_base == 'servicos' ? "os.status" : "status") . " = 'Faturado'";
                
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $config->carteira_usuario_id);
                $stmt->execute();
                $result_os = $stmt->get_result();
                
                $os_ids = array();
                while ($row = $result_os->fetch_object()) {
                    $os_ids[] = $row->idOs;
                }
                
                // Registra a transação com os IDs das OS
                $query = "INSERT INTO transacoes_usuario (tipo, valor, data_transacao, descricao, carteira_usuario_id) 
                         VALUES ('comissao', ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                $descricao = !empty($os_ids) ? 'OS: ' . implode(', ', $os_ids) : 'Comissão Fixa ' . ($config->tipo_repeticao == 'quinzenal' ? 'Quinzenal' : 'Mensal');
                $stmt->bind_param('dssi', $comissao, $data_atual, $descricao, $config->carteira_usuario_id);
                $stmt->execute();
                
                // Atualiza o status das OS para Finalizado
                if (!empty($os_ids)) {
                    $query = "UPDATE os SET status = 'Finalizado' WHERE idOs IN (" . implode(',', $os_ids) . ")";
                    $mysqli->query($query);
                }
            }
            
            // Atualiza o saldo da carteira
            $query = "UPDATE carteira_usuario 
                     SET saldo = saldo + ? 
                     WHERE idCarteiraUsuario = ?";
            $stmt = $mysqli->prepare($query);
            $valor_total = $valor_pagamento + $comissao;
            $stmt->bind_param('di', $valor_total, $config->carteira_usuario_id);
            $stmt->execute();
            
            // Confirma a transação
            $mysqli->commit();
            
            echo "Pagamento processado com sucesso para carteira ID: " . $config->carteira_usuario_id . "\n";
            
        } catch (Exception $e) {
            $mysqli->rollback();
            echo "Erro ao processar pagamento para carteira ID: " . $config->carteira_usuario_id . " - " . $e->getMessage() . "\n";
        }
    }
}

$mysqli->close(); 