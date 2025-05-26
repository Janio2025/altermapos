<?php
// Define o timezone
date_default_timezone_set('America/Sao_Paulo');

// Define o caminho base do CodeIgniter
define('BASEPATH', dirname(__FILE__));

// Carrega o arquivo de configuração do CodeIgniter
require_once BASEPATH . '/../application/config/config.php';
require_once BASEPATH . '/../application/config/database.php';

// Inicializa a conexão com o banco de dados
$db = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);

if ($db->connect_error) {
    die("Erro na conexão com o banco de dados: " . $db->connect_error);
}

// Registra o início da execução
$log_file = BASEPATH . '/cron_log.txt';
$log_message = date('Y-m-d H:i:s') . " - Iniciando verificação de pagamentos automáticos\n";
file_put_contents($log_file, $log_message, FILE_APPEND);

try {
    // Busca todas as carteiras com pagamento automático ativo
    $query = "SELECT cc.*, cu.idCarteiraUsuario, u.nome 
              FROM configuracao_carteira cc 
              JOIN carteira_usuario cu ON cc.carteira_usuario_id = cu.idCarteiraUsuario 
              JOIN usuarios u ON cu.usuarios_id = u.idUsuarios 
              WHERE cc.pagamento_automatico = 1";
    
    $result = $db->query($query);
    
    if (!$result) {
        throw new Exception("Erro ao buscar configurações: " . $db->error);
    }
    
    $pagamentos_processados = 0;
    $erros = [];
    
    while ($config = $result->fetch_assoc()) {
        try {
            // Verifica se é hora de fazer o pagamento
            $data_atual = new DateTime();
            $data_pagamento = new DateTime($config['data_salario']);
            
            // Se a data de pagamento já passou
            if ($data_atual >= $data_pagamento) {
                // Inicia a transação
                $db->begin_transaction();
                
                // Registra o salário base
                $valor_salario = $config['salario_base'];
                $query_salario = "INSERT INTO transacoes_carteira (carteira_usuario_id, tipo, valor, descricao, data_transacao) 
                                VALUES (?, 'salario', ?, 'Salário Base', NOW())";
                $stmt = $db->prepare($query_salario);
                $stmt->bind_param('id', $config['carteira_usuario_id'], $valor_salario);
                $stmt->execute();
                
                // Atualiza o saldo da carteira
                $query_update = "UPDATE carteira_usuario SET saldo = saldo + ? WHERE idCarteiraUsuario = ?";
                $stmt = $db->prepare($query_update);
                $stmt->bind_param('di', $valor_salario, $config['carteira_usuario_id']);
                $stmt->execute();
                
                // Calcula a próxima data de pagamento
                $proxima_data = clone $data_pagamento;
                if ($config['tipo_repeticao'] == 'mensal') {
                    $proxima_data->modify('+1 month');
                } else {
                    $proxima_data->modify('+15 days');
                }
                
                // Atualiza a data do próximo pagamento
                $query_update_data = "UPDATE configuracao_carteira 
                                    SET ultima_data_pagamento = NOW(), 
                                        proximo_pagamento = ? 
                                    WHERE carteira_usuario_id = ?";
                $stmt = $db->prepare($query_update_data);
                $proxima_data_str = $proxima_data->format('Y-m-d H:i:s');
                $stmt->bind_param('si', $proxima_data_str, $config['carteira_usuario_id']);
                $stmt->execute();
                
                // Commit da transação
                $db->commit();
                
                $pagamentos_processados++;
                $log_message = date('Y-m-d H:i:s') . " - Pagamento processado para {$config['nome']}\n";
                file_put_contents($log_file, $log_message, FILE_APPEND);
            }
        } catch (Exception $e) {
            $db->rollback();
            $erros[] = "Erro ao processar pagamento para {$config['nome']}: " . $e->getMessage();
            $log_message = date('Y-m-d H:i:s') . " - ERRO: " . $e->getMessage() . "\n";
            file_put_contents($log_file, $log_message, FILE_APPEND);
        }
    }
    
    // Registra o resumo da execução
    $resumo = date('Y-m-d H:i:s') . " - Resumo da execução:\n";
    $resumo .= "Pagamentos processados: $pagamentos_processados\n";
    if (!empty($erros)) {
        $resumo .= "Erros encontrados:\n";
        foreach ($erros as $erro) {
            $resumo .= "- $erro\n";
        }
    }
    $resumo .= "----------------------------------------\n";
    file_put_contents($log_file, $resumo, FILE_APPEND);
    
} catch (Exception $e) {
    $log_message = date('Y-m-d H:i:s') . " - ERRO CRÍTICO: " . $e->getMessage() . "\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Fecha a conexão
$db->close(); 