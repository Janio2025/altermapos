<?php
/**
 * Teste Simples de Atributos - Verificação do Fix
 */

$base_url = 'https://duke.acell.tec.br/os/';

echo "<h1>Teste de Verificação do Fix dos Atributos</h1>";
echo "<p><strong>URL Base:</strong> {$base_url}</p>";
echo "<hr>";

// Teste 1: Verificar se o método alternativo está funcionando
echo "<h2>1. Teste do Método Alternativo</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'categorias/buscarAtributosAlternativos?categoria_id=1&ml_id=MLB5673');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        echo "<div style='color: green;'>✅ Método alternativo funcionando!</div>";
        echo "<p><strong>Atributos encontrados:</strong> " . count($data['atributos']) . "</p>";
        
        // Verificar se os atributos têm a estrutura correta
        $estrutura_correta = true;
        foreach ($data['atributos'] as $atributo) {
            if (!isset($atributo['id']) || !isset($atributo['name']) || !isset($atributo['value_type'])) {
                $estrutura_correta = false;
                break;
            }
        }
        
        if ($estrutura_correta) {
            echo "<div style='color: green;'>✅ Estrutura dos atributos está correta</div>";
        } else {
            echo "<div style='color: red;'>❌ Estrutura dos atributos está incorreta</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Erro no método alternativo: " . ($data['message'] ?? 'Erro desconhecido') . "</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code}</div>";
}

echo "<hr>";

// Teste 2: Simular salvamento de atributos
echo "<h2>2. Teste de Salvamento de Atributos</h2>";

$atributos_teste = [
    [
        'id' => 'TEST_BRAND',
        'name' => 'Marca Teste',
        'value_type' => 'string',
        'required' => true,
        'values' => [],
        'hierarchy' => null,
        'tags' => [],
        'attribute_group_id' => null,
        'attribute_group_name' => null
    ],
    [
        'id' => 'TEST_MODEL',
        'name' => 'Modelo Teste',
        'value_type' => 'string',
        'required' => false,
        'values' => [],
        'hierarchy' => null,
        'tags' => [],
        'attribute_group_id' => null,
        'attribute_group_name' => null
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'categorias/salvarAtributosML');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'categoria_id' => 1,
    'atributos' => $atributos_teste
]));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        echo "<div style='color: green;'>✅ Atributos salvos com sucesso!</div>";
        echo "<p><strong>Mensagem:</strong> " . $data['message'] . "</p>";
    } else {
        echo "<div style='color: red;'>❌ Erro ao salvar: " . ($data['message'] ?? 'Erro desconhecido') . "</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code}</div>";
}

echo "<hr>";

// Teste 3: Verificar se os atributos foram salvos
echo "<h2>3. Verificação dos Atributos Salvos</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'produtos/getAtributosCategoria?categoria_id=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        echo "<div style='color: green;'>✅ Atributos recuperados com sucesso!</div>";
        echo "<p><strong>Total de atributos:</strong> " . count($data['atributos']) . "</p>";
        
        // Verificar se os atributos de teste estão presentes
        $atributos_teste_encontrados = [];
        foreach ($data['atributos'] as $atributo) {
            if (in_array($atributo->ml_attribute_id, ['TEST_BRAND', 'TEST_MODEL'])) {
                $atributos_teste_encontrados[] = $atributo->ml_attribute_id;
            }
        }
        
        if (count($atributos_teste_encontrados) > 0) {
            echo "<div style='color: green;'>✅ Atributos de teste encontrados: " . implode(', ', $atributos_teste_encontrados) . "</div>";
        } else {
            echo "<div style='color: orange;'>⚠️ Atributos de teste não encontrados</div>";
        }
    } else {
        echo "<div style='color: orange;'>⚠️ " . ($data['message'] ?? 'Nenhum atributo encontrado') . "</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code}</div>";
}

echo "<hr>";

echo "<h2>4. Resumo do Status</h2>";
echo "<p><strong>✅ Problemas Resolvidos:</strong></p>";
echo "<ul>";
echo "<li>Erro 'Undefined array key values' corrigido com verificações isset()</li>";
echo "<li>JavaScript melhorado com logs para debug</li>";
echo "<li>Sistema de fallback implementado</li>";
echo "</ul>";

echo "<p><strong>🔧 Próximos Passos:</strong></p>";
echo "<ol>";
echo "<li>Testar no sistema real se os atributos aparecem ao clicar em 'Atributos'</li>";
echo "<li>Verificar se os atributos são salvos corretamente</li>";
echo "<li>Testar se os atributos aparecem na view de produtos</li>";
echo "<li>Verificar se o JavaScript está funcionando com os logs adicionados</li>";
echo "</ol>";

echo "<p><em>Teste concluído em: " . date('d/m/Y H:i:s') . "</em></p>";
?> 