<?php
// Script para corrigir automaticamente a tabela atributos_ml
require_once 'application/config/config.php';
require_once 'application/config/database.php';

echo "<h1>Correção da Tabela atributos_ml</h1>";

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
    echo "<p style='color: red;'>❌ Erro: Não foi possível conectar ao banco de dados.</p>";
    exit;
}

echo "<p style='color: green;'>✅ Conexão com banco estabelecida.</p>";

// 1. Verificar se a tabela existe
$result = $db->query("SHOW TABLES LIKE 'atributos_ml'");
if ($result->num_rows() == 0) {
    echo "<p style='color: red;'>❌ A tabela 'atributos_ml' não existe!</p>";
    echo "<p>Criando a tabela com a estrutura correta...</p>";
    
    $sql = "CREATE TABLE `atributos_ml` (
        `id` int NOT NULL AUTO_INCREMENT,
        `categoria_id` int NOT NULL COMMENT 'ID da categoria relacionada',
        `ml_attribute_id` varchar(50) NOT NULL COMMENT 'ID do atributo no Mercado Livre',
        `name` varchar(255) NOT NULL COMMENT 'Nome do atributo',
        `value_type` varchar(20) DEFAULT NULL COMMENT 'Tipo do valor (string, number, boolean, list_unit, etc)',
        `required` tinyint(1) DEFAULT 0 COMMENT 'Se o atributo é obrigatório',
        `values` json DEFAULT NULL COMMENT 'Valores possíveis (para atributos do tipo list)',
        `hierarchy` varchar(255) DEFAULT NULL COMMENT 'Hierarquia do atributo',
        `tags` text COMMENT 'Tags associadas ao atributo',
        `attribute_group_id` varchar(50) DEFAULT NULL COMMENT 'ID do grupo do atributo',
        `attribute_group_name` varchar(255) DEFAULT NULL COMMENT 'Nome do grupo do atributo',
        `status` tinyint(1) DEFAULT 1 COMMENT 'Status do atributo (1=ativo, 0=inativo)',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `categoria_atributo` (`categoria_id`, `ml_attribute_id`),
        KEY `categoria_id` (`categoria_id`),
        KEY `ml_attribute_id` (`ml_attribute_id`),
        KEY `required` (`required`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Atributos do Mercado Livre por categoria';";
    
    if ($db->query($sql)) {
        echo "<p style='color: green;'>✅ Tabela 'atributos_ml' criada com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar tabela: " . $db->error()['message'] . "</p>";
        exit;
    }
} else {
    echo "<p style='color: green;'>✅ Tabela 'atributos_ml' existe.</p>";
}

// 2. Verificar estrutura atual
echo "<h2>Estrutura Atual:</h2>";
$result = $db->query("DESCRIBE atributos_ml");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    $campos_existentes = [];
    foreach ($result->result_array() as $row) {
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
} else {
    echo "<p style='color: red;'>Erro ao verificar estrutura da tabela.</p>";
    exit;
}

// 3. Adicionar campos que estão faltando
echo "<h2>Adicionando Campos Faltantes:</h2>";

$campos_necessarios = [
    'hierarchy' => "ALTER TABLE `atributos_ml` ADD COLUMN IF NOT EXISTS `hierarchy` varchar(255) DEFAULT NULL COMMENT 'Hierarquia do atributo'",
    'tags' => "ALTER TABLE `atributos_ml` ADD COLUMN IF NOT EXISTS `tags` text COMMENT 'Tags associadas ao atributo'",
    'attribute_group_id' => "ALTER TABLE `atributos_ml` ADD COLUMN IF NOT EXISTS `attribute_group_id` varchar(50) DEFAULT NULL COMMENT 'ID do grupo do atributo'",
    'attribute_group_name' => "ALTER TABLE `atributos_ml` ADD COLUMN IF NOT EXISTS `attribute_group_name` varchar(255) DEFAULT NULL COMMENT 'Nome do grupo do atributo'",
    'status' => "ALTER TABLE `atributos_ml` ADD COLUMN IF NOT EXISTS `status` tinyint(1) DEFAULT 1 COMMENT 'Status do atributo (1=ativo, 0=inativo)'",
    'updated_at' => "ALTER TABLE `atributos_ml` ADD COLUMN IF NOT EXISTS `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
];

foreach ($campos_necessarios as $campo => $sql) {
    if (!in_array($campo, $campos_existentes)) {
        echo "<p>Adicionando campo '$campo'...</p>";
        if ($db->query($sql)) {
            echo "<p style='color: green;'>✅ Campo '$campo' adicionado com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao adicionar campo '$campo': " . $db->error()['message'] . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Campo '$campo' já existe.</p>";
    }
}

// 4. Verificar se o campo values é JSON
echo "<h2>Verificando Campo 'values':</h2>";
$result = $db->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'atributos_ml' AND COLUMN_NAME = 'values'");
if ($result && $result->num_rows() > 0) {
    $row = $result->row();
    if ($row->DATA_TYPE !== 'json') {
        echo "<p>Convertendo campo 'values' para JSON...</p>";
        $sql = "ALTER TABLE `atributos_ml` MODIFY COLUMN `values` json DEFAULT NULL COMMENT 'Valores possíveis (para atributos do tipo list)'";
        if ($db->query($sql)) {
            echo "<p style='color: green;'>✅ Campo 'values' convertido para JSON!</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao converter campo 'values': " . $db->error()['message'] . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Campo 'values' já é do tipo JSON.</p>";
    }
}

// 5. Adicionar índices se não existirem
echo "<h2>Verificando Índices:</h2>";
$indices_necessarios = [
    'idx_categoria_id' => 'categoria_id',
    'idx_ml_attribute_id' => 'ml_attribute_id',
    'idx_required' => 'required',
    'idx_status' => 'status'
];

foreach ($indices_necessarios as $indice => $campo) {
    $result = $db->query("SHOW INDEX FROM `atributos_ml` WHERE Key_name = '$indice'");
    if ($result->num_rows() == 0) {
        echo "<p>Criando índice '$indice'...</p>";
        $sql = "CREATE INDEX `$indice` ON `atributos_ml` (`$campo`)";
        if ($db->query($sql)) {
            echo "<p style='color: green;'>✅ Índice '$indice' criado com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao criar índice '$indice': " . $db->error()['message'] . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Índice '$indice' já existe.</p>";
    }
}

// 6. Verificar dados existentes
echo "<h2>Dados na Tabela:</h2>";
$result = $db->query("SELECT COUNT(*) as total FROM atributos_ml");
if ($result) {
    $count = $result->row()->total;
    echo "<p>Total de registros: <strong>" . $count . "</strong></p>";
    
    if ($count > 0) {
        $result = $db->query("SELECT * FROM atributos_ml LIMIT 3");
        echo "<h3>Primeiros 3 registros:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Categoria ID</th><th>ML ID</th><th>Nome</th><th>Tipo</th><th>Obrigatório</th><th>Status</th></tr>";
        foreach ($result->result_array() as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['categoria_id'] . "</td>";
            echo "<td>" . $row['ml_attribute_id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['value_type'] . "</td>";
            echo "<td>" . $row['required'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

echo "<h2>✅ Correção Concluída!</h2>";
echo "<p>A tabela 'atributos_ml' foi corrigida e está pronta para uso.</p>";
echo "<p>Agora você pode testar o salvamento de atributos novamente.</p>";

echo "<h3>Próximos Passos:</h3>";
echo "<ol>";
echo "<li>Teste o salvamento de atributos no sistema</li>";
echo "<li>Verifique se os atributos são carregados no cadastro de produtos</li>";
echo "<li>Confirme se não há mais erros nos logs</li>";
echo "</ol>";
?> 