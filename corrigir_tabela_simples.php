<?php
// Script simples para corrigir a tabela atributos_ml
// Acesse via: https://duke.acell.tec.br/os/corrigir_tabela_simples.php

echo "<h1>Correção da Tabela atributos_ml</h1>";

// Configurações do banco
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'os';

// Conectar ao banco
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Conexão com banco estabelecida.</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erro de conexão: " . $e->getMessage() . "</p>";
    exit;
}

// 1. Verificar se a tabela existe
$stmt = $pdo->query("SHOW TABLES LIKE 'atributos_ml'");
if ($stmt->rowCount() == 0) {
    echo "<p style='color: red;'>❌ A tabela 'atributos_ml' não existe!</p>";
    echo "<p>Criando a tabela...</p>";
    
    $sql = "CREATE TABLE `atributos_ml` (
        `id` int NOT NULL AUTO_INCREMENT,
        `categoria_id` int NOT NULL,
        `ml_attribute_id` varchar(50) NOT NULL,
        `name` varchar(255) NOT NULL,
        `value_type` varchar(20) DEFAULT NULL,
        `required` tinyint(1) DEFAULT 0,
        `values` json DEFAULT NULL,
        `hierarchy` varchar(255) DEFAULT NULL,
        `tags` text,
        `attribute_group_id` varchar(50) DEFAULT NULL,
        `attribute_group_name` varchar(255) DEFAULT NULL,
        `status` tinyint(1) DEFAULT 1,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `categoria_id` (`categoria_id`),
        KEY `ml_attribute_id` (`ml_attribute_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    try {
        $pdo->exec($sql);
        echo "<p style='color: green;'>✅ Tabela criada com sucesso!</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Erro ao criar tabela: " . $e->getMessage() . "</p>";
        exit;
    }
} else {
    echo "<p style='color: green;'>✅ Tabela 'atributos_ml' existe.</p>";
}

// 2. Verificar estrutura atual
echo "<h2>Estrutura Atual:</h2>";
$stmt = $pdo->query("DESCRIBE atributos_ml");
$campos_existentes = [];
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
    $campos_existentes[] = $row['Field'];
}
echo "</table>";

// 3. Adicionar campos faltantes
echo "<h2>Adicionando Campos Faltantes:</h2>";

$campos_para_adicionar = [
    'hierarchy' => "ALTER TABLE `atributos_ml` ADD COLUMN `hierarchy` varchar(255) DEFAULT NULL",
    'tags' => "ALTER TABLE `atributos_ml` ADD COLUMN `tags` text",
    'attribute_group_id' => "ALTER TABLE `atributos_ml` ADD COLUMN `attribute_group_id` varchar(50) DEFAULT NULL",
    'attribute_group_name' => "ALTER TABLE `atributos_ml` ADD COLUMN `attribute_group_name` varchar(255) DEFAULT NULL",
    'status' => "ALTER TABLE `atributos_ml` ADD COLUMN `status` tinyint(1) DEFAULT 1",
    'updated_at' => "ALTER TABLE `atributos_ml` ADD COLUMN `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
];

foreach ($campos_para_adicionar as $campo => $sql) {
    if (!in_array($campo, $campos_existentes)) {
        echo "<p>Adicionando campo '$campo'...</p>";
        try {
            $pdo->exec($sql);
            echo "<p style='color: green;'>✅ Campo '$campo' adicionado!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Erro ao adicionar '$campo': " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Campo '$campo' já existe.</p>";
    }
}

// 4. Verificar dados
echo "<h2>Dados na Tabela:</h2>";
$stmt = $pdo->query("SELECT COUNT(*) as total FROM atributos_ml");
$count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
echo "<p>Total de registros: <strong>$count</strong></p>";

if ($count > 0) {
    $stmt = $pdo->query("SELECT * FROM atributos_ml LIMIT 3");
    echo "<h3>Primeiros 3 registros:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Categoria ID</th><th>ML ID</th><th>Nome</th><th>Tipo</th><th>Obrigatório</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['categoria_id'] . "</td>";
        echo "<td>" . $row['ml_attribute_id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['value_type'] . "</td>";
        echo "<td>" . $row['required'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>✅ Correção Concluída!</h2>";
echo "<p>A tabela 'atributos_ml' foi corrigida e está pronta para uso.</p>";
echo "<p>Agora você pode testar o salvamento de atributos no sistema.</p>";

echo "<h3>Próximos Passos:</h3>";
echo "<ol>";
echo "<li>Teste o salvamento de atributos no sistema</li>";
echo "<li>Verifique se os atributos são carregados no cadastro de produtos</li>";
echo "<li>Confirme se não há mais erros nos logs</li>";
echo "</ol>";
?> 