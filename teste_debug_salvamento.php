<?php
// Teste de debug para salvamento de atributos
require_once 'application/config/config.php';
require_once 'application/config/database.php';
require_once 'application/models/Categorias_model.php';

echo "<h1>Debug do Salvamento de Atributos</h1>";

// Conectar ao banco
$db = new CI_DB_mysqli_driver([
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'os',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => TRUE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
]);

$db->initialize();

// Verificar se a conexão foi estabelecida
if (!$db->conn_id) {
    echo "<p style='color: red;'>Erro: Não foi possível conectar ao banco de dados.</p>";
    exit;
}

echo "<p style='color: green;'>✓ Conexão com banco estabelecida.</p>";

// Verificar estrutura das tabelas
echo "<h2>1. Verificação da Estrutura das Tabelas</h2>";

// Verificar tabela categorias
$result = $db->query("DESCRIBE categorias");
if ($result) {
    echo "<h3>Tabela 'categorias':</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($result->result_array() as $row) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Erro ao verificar estrutura da tabela 'categorias'</p>";
}

// Verificar tabela atributos_ml
$result = $db->query("DESCRIBE atributos_ml");
if ($result) {
    echo "<h3>Tabela 'atributos_ml':</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($result->result_array() as $row) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Erro ao verificar estrutura da tabela 'atributos_ml'</p>";
}

// Verificar dados existentes
echo "<h2>2. Dados Existentes</h2>";

// Verificar categorias
$result = $db->query("SELECT idCategorias, categoria, ml_id, tipo FROM categorias LIMIT 5");
if ($result) {
    echo "<h3>Primeiras 5 categorias:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>ML ID</th><th>Tipo</th></tr>";
    foreach ($result->result_array() as $row) {
        echo "<tr>";
        echo "<td>" . $row['idCategorias'] . "</td>";
        echo "<td>" . $row['categoria'] . "</td>";
        echo "<td>" . $row['ml_id'] . "</td>";
        echo "<td>" . $row['tipo'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Erro ao buscar categorias</p>";
}

// Verificar atributos
$result = $db->query("SELECT * FROM atributos_ml LIMIT 5");
if ($result) {
    echo "<h3>Primeiros 5 atributos:</h3>";
    if ($result->num_rows() > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Categoria ID</th><th>ML ID</th><th>Nome</th><th>Tipo</th><th>Obrigatório</th></tr>";
        foreach ($result->result_array() as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['categoria_id'] . "</td>";
            echo "<td>" . $row['ml_id'] . "</td>";
            echo "<td>" . $row['nome'] . "</td>";
            echo "<td>" . $row['tipo'] . "</td>";
            echo "<td>" . $row['obrigatorio'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>Nenhum atributo encontrado na tabela.</p>";
    }
} else {
    echo "<p style='color: red;'>Erro ao buscar atributos</p>";
}

// Teste de inserção manual
echo "<h2>3. Teste de Inserção Manual</h2>";

// Pegar primeira categoria
$result = $db->query("SELECT idCategorias, categoria, ml_id FROM categorias WHERE ml_id IS NOT NULL AND ml_id != '' LIMIT 1");
if ($result && $result->num_rows() > 0) {
    $categoria = $result->row_array();
    echo "<p>Testando inserção para categoria: <strong>" . $categoria['categoria'] . "</strong> (ID: " . $categoria['idCategorias'] . ")</p>";
    
    // Dados de teste
    $dadosTeste = [
        'categoria_id' => $categoria['idCategorias'],
        'ml_id' => 'TESTE_001',
        'nome' => 'Teste Atributo',
        'tipo' => 'string',
        'obrigatorio' => 0,
        'valores' => json_encode(['valor1', 'valor2']),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    echo "<p>Dados para inserção:</p>";
    echo "<pre>" . print_r($dadosTeste, true) . "</pre>";
    
    // Tentar inserção
    $sql = "INSERT INTO atributos_ml (categoria_id, ml_id, nome, tipo, obrigatorio, valores, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params = [
        $dadosTeste['categoria_id'],
        $dadosTeste['ml_id'],
        $dadosTeste['nome'],
        $dadosTeste['tipo'],
        $dadosTeste['obrigatorio'],
        $dadosTeste['valores'],
        $dadosTeste['created_at']
    ];
    
    $result = $db->query($sql, $params);
    if ($result) {
        echo "<p style='color: green;'>✓ Inserção manual bem-sucedida!</p>";
        
        // Verificar se foi inserido
        $result = $db->query("SELECT * FROM atributos_ml WHERE ml_id = 'TESTE_001'");
        if ($result && $result->num_rows() > 0) {
            $atributo = $result->row_array();
            echo "<p>Atributo inserido:</p>";
            echo "<pre>" . print_r($atributo, true) . "</pre>";
        }
        
        // Limpar teste
        $db->query("DELETE FROM atributos_ml WHERE ml_id = 'TESTE_001'");
        echo "<p style='color: blue;'>Teste limpo.</p>";
    } else {
        echo "<p style='color: red;'>✗ Erro na inserção manual: " . $db->error()['message'] . "</p>";
    }
} else {
    echo "<p style='color: red;'>Nenhuma categoria com ML ID encontrada para teste.</p>";
}

// Verificar logs do sistema
echo "<h2>4. Verificação de Logs</h2>";
$logPath = 'application/logs/';
$files = glob($logPath . 'log-*.php');
if ($files) {
    rsort($files);
    $latestLog = $files[0];
    echo "<p>Último arquivo de log: <strong>" . basename($latestLog) . "</strong></p>";
    
    $lines = file($latestLog);
    $errorLines = [];
    
    foreach (array_slice($lines, -50) as $line) { // Últimas 50 linhas
        if (stripos($line, 'erro') !== false || 
            stripos($line, 'error') !== false || 
            stripos($line, 'atributo') !== false ||
            stripos($line, 'salvarAtributosML') !== false) {
            $errorLines[] = $line;
        }
    }
    
    if ($errorLines) {
        echo "<h3>Últimas linhas relevantes do log:</h3>";
        echo "<div style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 300px; overflow-y: auto;'>";
        foreach ($errorLines as $line) {
            echo htmlspecialchars($line) . "<br>";
        }
        echo "</div>";
    } else {
        echo "<p>Nenhuma linha relevante encontrada nas últimas 50 linhas do log.</p>";
    }
} else {
    echo "<p style='color: orange;'>Nenhum arquivo de log encontrado.</p>";
}

echo "<h2>5. Recomendações</h2>";
echo "<ul>";
echo "<li>Verifique se a tabela 'atributos_ml' existe e tem a estrutura correta</li>";
echo "<li>Confirme se o campo 'categoria_id' está sendo enviado corretamente</li>";
echo "<li>Verifique se há permissões de escrita no banco de dados</li>";
echo "<li>Teste a inserção manual para identificar problemas de estrutura</li>";
echo "</ul>";

echo "<p><strong>Status:</strong> <span style='color: green;'>Teste concluído</span></p>";
?> 