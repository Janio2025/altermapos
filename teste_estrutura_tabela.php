<?php
/**
 * Teste da Estrutura da Tabela atributos_ml
 */

// Configurações do banco

echo "<h1>Teste da Estrutura da Tabela atributos_ml</h1>";
echo "<hr>";

// Conectar ao banco
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'mapos';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='color: green;'>✅ Conexão com banco estabelecida</div>";
    
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'atributos_ml'");
    if ($stmt->rowCount() > 0) {
        echo "<div style='color: green;'>✅ Tabela atributos_ml existe</div>";
        
        // Verificar estrutura da tabela
        $stmt = $pdo->query("DESCRIBE atributos_ml");
        $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h2>Estrutura da Tabela atributos_ml:</h2>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($colunas as $coluna) {
            echo "<tr>";
            echo "<td>{$coluna['Field']}</td>";
            echo "<td>{$coluna['Type']}</td>";
            echo "<td>{$coluna['Null']}</td>";
            echo "<td>{$coluna['Key']}</td>";
            echo "<td>{$coluna['Default']}</td>";
            echo "<td>{$coluna['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar se há registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM atributos_ml");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "<p><strong>Total de registros:</strong> {$total}</p>";
        
        if ($total > 0) {
            echo "<h3>Últimos 5 registros:</h3>";
            $stmt = $pdo->query("SELECT * FROM atributos_ml ORDER BY id DESC LIMIT 5");
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            if (!empty($registros)) {
                echo "<tr>";
                foreach (array_keys($registros[0]) as $coluna) {
                    echo "<th>{$coluna}</th>";
                }
                echo "</tr>";
                
                foreach ($registros as $registro) {
                    echo "<tr>";
                    foreach ($registro as $valor) {
                        echo "<td>" . htmlspecialchars($valor) . "</td>";
                    }
                    echo "</tr>";
                }
            }
            echo "</table>";
        }
        
    } else {
        echo "<div style='color: red;'>❌ Tabela atributos_ml não existe</div>";
    }
    
    // Testar inserção manual
    echo "<h2>Teste de Inserção Manual</h2>";
    
    try {
        $dados_teste = [
            'categoria_id' => 1,
            'ml_attribute_id' => 'TEST_INSERT',
            'name' => 'Teste de Inserção',
            'value_type' => 'string',
            'required' => 0,
            'values' => json_encode([]),
            'hierarchy' => null,
            'tags' => json_encode([]),
            'attribute_group_id' => null,
            'attribute_group_name' => null,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $sql = "INSERT INTO atributos_ml (categoria_id, ml_attribute_id, name, value_type, required, values, hierarchy, tags, attribute_group_id, attribute_group_name, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            $dados_teste['categoria_id'],
            $dados_teste['ml_attribute_id'],
            $dados_teste['name'],
            $dados_teste['value_type'],
            $dados_teste['required'],
            $dados_teste['values'],
            $dados_teste['hierarchy'],
            $dados_teste['tags'],
            $dados_teste['attribute_group_id'],
            $dados_teste['attribute_group_name'],
            $dados_teste['status'],
            $dados_teste['created_at'],
            $dados_teste['updated_at']
        ]);
        
        if ($resultado) {
            echo "<div style='color: green;'>✅ Inserção manual bem-sucedida!</div>";
            
            // Remover o registro de teste
            $stmt = $pdo->prepare("DELETE FROM atributos_ml WHERE ml_attribute_id = ?");
            $stmt->execute(['TEST_INSERT']);
            echo "<div style='color: green;'>✅ Registro de teste removido</div>";
        } else {
            echo "<div style='color: red;'>❌ Erro na inserção manual</div>";
            $erro = $stmt->errorInfo();
            echo "<p>Erro: " . json_encode($erro) . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Erro na inserção manual: " . $e->getMessage() . "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Erro de conexão: " . $e->getMessage() . "</div>";
}

echo "<hr>";
echo "<h2>Análise</h2>";
echo "<p>Se a estrutura da tabela estiver correta e a inserção manual funcionar, o problema pode estar:</p>";
echo "<ul>";
echo "<li>Nos dados sendo enviados pelo JavaScript</li>";
echo "<li>No método adicionarAtributo do modelo</li>";
echo "<li>Na validação dos dados no controlador</li>";
echo "</ul>";

echo "<p><em>Teste concluído em: " . date('d/m/Y H:i:s') . "</em></p>";
?> 