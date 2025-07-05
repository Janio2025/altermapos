<?php
// Verificar estrutura da tabela atributos_ml
require_once 'application/config/config.php';
require_once 'application/config/database.php';

echo "<h1>Verificação da Estrutura da Tabela atributos_ml</h1>";

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

// Verificar se a tabela existe
$result = $db->query("SHOW TABLES LIKE 'atributos_ml'");
if ($result->num_rows() == 0) {
    echo "<p style='color: red;'>❌ A tabela 'atributos_ml' não existe!</p>";
    echo "<p>Vou criar a tabela com a estrutura correta...</p>";
    
    $sql = "CREATE TABLE `atributos_ml` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `categoria_id` int(11) NOT NULL,
        `ml_id` varchar(50) DEFAULT NULL,
        `nome` varchar(255) NOT NULL,
        `tipo` varchar(50) DEFAULT 'string',
        `obrigatorio` tinyint(1) DEFAULT 0,
        `valores` text DEFAULT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `categoria_id` (`categoria_id`),
        KEY `ml_id` (`ml_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    
    if ($db->query($sql)) {
        echo "<p style='color: green;'>✅ Tabela 'atributos_ml' criada com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar tabela: " . $db->error()['message'] . "</p>";
        exit;
    }
} else {
    echo "<p style='color: green;'>✅ Tabela 'atributos_ml' existe.</p>";
}

// Verificar estrutura atual
$result = $db->query("DESCRIBE atributos_ml");
if ($result) {
    echo "<h2>Estrutura Atual da Tabela 'atributos_ml':</h2>";
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
    echo "<p style='color: red;'>Erro ao verificar estrutura da tabela.</p>";
}

// Verificar se há dados na tabela
$result = $db->query("SELECT COUNT(*) as total FROM atributos_ml");
if ($result) {
    $count = $result->row()->total;
    echo "<h2>Dados na Tabela:</h2>";
    echo "<p>Total de registros: <strong>" . $count . "</strong></p>";
    
    if ($count > 0) {
        $result = $db->query("SELECT * FROM atributos_ml LIMIT 3");
        echo "<h3>Primeiros 3 registros:</h3>";
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
    }
}

echo "<h2>Próximos Passos:</h2>";
echo "<ol>";
echo "<li>Corrigir o modelo para usar apenas os campos que existem na tabela</li>";
echo "<li>Testar o salvamento de atributos novamente</li>";
echo "<li>Verificar se os atributos são carregados corretamente no cadastro de produtos</li>";
echo "</ol>";

echo "<p><strong>Status:</strong> <span style='color: green;'>Verificação concluída</span></p>";
?> 