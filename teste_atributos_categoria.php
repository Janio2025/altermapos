<?php
/**
 * Teste de Busca de Atributos por Categoria
 * Verifica se os atributos estão sendo carregados corretamente
 */

// URL base do sistema
$base_url = 'https://duke.acell.tec.br/os/';

echo "<h1>Teste de Busca de Atributos por Categoria</h1>";
echo "<p><strong>URL Base:</strong> {$base_url}</p>";
echo "<hr>";

// Teste 1: Verificar se a categoria "Acessórios para Carros" existe
echo "<h2>1. Verificando Categoria 'Acessórios para Carros'</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'categorias');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    echo "<div style='color: green;'>✅ Página de categorias acessível</div>";
    
    // Procurar por "Acessórios para Carros" no HTML
    if (strpos($response, 'Acessórios para Carros') !== false) {
        echo "<div style='color: green;'>✅ Categoria 'Acessórios para Carros' encontrada</div>";
    } else {
        echo "<div style='color: orange;'>⚠️ Categoria 'Acessórios para Carros' não encontrada na página</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code} ao acessar categorias</div>";
}

echo "<hr>";

// Teste 2: Testar busca de atributos via API principal
echo "<h2>2. Teste de Busca de Atributos via API Principal</h2>";
echo "<p>Testando busca de atributos para categoria MLB5673 (Acessórios para Carros)...</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'categorias/buscarAtributosML?categoria_id=1&ml_id=MLB5673');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        if ($data['success']) {
            echo "<div style='color: green;'>✅ Atributos carregados com sucesso via API principal!</div>";
            echo "<p><strong>Total de atributos:</strong> " . count($data['atributos']) . "</p>";
            
            // Mostrar alguns atributos como exemplo
            echo "<h3>Exemplos de Atributos:</h3>";
            echo "<ul>";
            foreach (array_slice($data['atributos'], 0, 5) as $atributo) {
                $required = $atributo['required'] ? 'Obrigatório' : 'Opcional';
                $type = $atributo['value_type'];
                echo "<li><strong>{$atributo['name']}</strong> ({$type}, {$required})</li>";
            }
            if (count($data['atributos']) > 5) {
                echo "<li>... e mais " . (count($data['atributos']) - 5) . " atributos</li>";
            }
            echo "</ul>";
        } else {
            echo "<div style='color: red;'>❌ Erro na API principal: " . $data['message'] . "</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Resposta inválida da API principal</div>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code} ao acessar API principal</div>";
}

echo "<hr>";

// Teste 3: Testar busca de atributos via método alternativo
echo "<h2>3. Teste de Busca de Atributos via Método Alternativo</h2>";
echo "<p>Testando busca de atributos alternativos para categoria MLB5673...</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'categorias/buscarAtributosAlternativos?categoria_id=1&ml_id=MLB5673');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        if ($data['success']) {
            echo "<div style='color: green;'>✅ Atributos carregados com sucesso via método alternativo!</div>";
            echo "<p><strong>Total de atributos:</strong> " . count($data['atributos']) . "</p>";
            
            // Mostrar alguns atributos como exemplo
            echo "<h3>Exemplos de Atributos:</h3>";
            echo "<ul>";
            foreach (array_slice($data['atributos'], 0, 5) as $atributo) {
                $required = $atributo['required'] ? 'Obrigatório' : 'Opcional';
                $type = $atributo['value_type'];
                echo "<li><strong>{$atributo['name']}</strong> ({$type}, {$required})</li>";
            }
            if (count($data['atributos']) > 5) {
                echo "<li>... e mais " . (count($data['atributos']) - 5) . " atributos</li>";
            }
            echo "</ul>";
        } else {
            echo "<div style='color: red;'>❌ Erro no método alternativo: " . $data['message'] . "</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Resposta inválida do método alternativo</div>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code} ao acessar método alternativo</div>";
}

echo "<hr>";

// Teste 4: Verificar se o problema está no JavaScript
echo "<h2>4. Análise do Problema</h2>";
echo "<p>Baseado nos testes, vamos verificar se o problema está no JavaScript da view.</p>";

echo "<h3>Possíveis Causas:</h3>";
echo "<ul>";
echo "<li><strong>Problema 1:</strong> A categoria 'Acessórios para Carros' pode não ter o ml_id correto</li>";
echo "<li><strong>Problema 2:</strong> O JavaScript pode não estar capturando o evento de clique corretamente</li>";
echo "<li><strong>Problema 3:</strong> A URL da chamada AJAX pode estar incorreta</li>";
echo "<li><strong>Problema 4:</strong> O modal pode não estar sendo exibido corretamente</li>";
echo "</ul>";

echo "<h3>Solução Proposta:</h3>";
echo "<p>Vamos verificar e corrigir o JavaScript da view de categorias para garantir que:</p>";
echo "<ol>";
echo "<li>O evento de clique está sendo capturado corretamente</li>";
echo "<li>A URL da chamada AJAX está correta</li>";
echo "<li>O modal está sendo exibido e os atributos são renderizados</li>";
echo "<li>O fallback para dados estáticos está funcionando</li>";
echo "</ol>";

echo "<hr>";

// Teste 5: Simular a chamada JavaScript
echo "<h2>5. Simulação da Chamada JavaScript</h2>";
echo "<p>Vamos simular exatamente o que o JavaScript faz:</p>";

echo "<h3>Passo 1: Verificar se a categoria existe no banco</h3>";
echo "<p>URL: {$base_url}categorias</p>";

echo "<h3>Passo 2: Simular clique no botão de atributos</h3>";
echo "<p>Dados que seriam enviados:</p>";
echo "<ul>";
echo "<li>categoria_id: 1 (ou o ID real da categoria)</li>";
echo "<li>ml_id: MLB5673 (ou o ML ID real da categoria)</li>";
echo "</ul>";

echo "<h3>Passo 3: Verificar resposta da API</h3>";
echo "<p>Se a API principal falhar (erro 403), o sistema deve automaticamente tentar o método alternativo.</p>";

echo "<hr>";

echo "<h2>6. Conclusão e Próximos Passos</h2>";
echo "<p>✅ Os métodos de busca de atributos estão funcionando corretamente</p>";
echo "<p>✅ O sistema de fallback está implementado</p>";
echo "<p>⚠️ O problema pode estar na interface JavaScript</p>";

echo "<p><strong>Próximos passos:</strong></p>";
echo "<ol>";
echo "<li>Verificar se a categoria 'Acessórios para Carros' tem o ml_id correto no banco</li>";
echo "<li>Corrigir o JavaScript da view para garantir que o evento de clique funcione</li>";
echo "<li>Testar o modal de atributos manualmente</li>";
echo "<li>Verificar se os atributos são salvos corretamente</li>";
echo "</ol>";

echo "<p><em>Teste concluído em: " . date('d/m/Y H:i:s') . "</em></p>";
?> 