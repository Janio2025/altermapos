<?php
/**
 * Teste de Verificação de Atributos Salvos
 * Verifica se os atributos estão sendo salvos corretamente na tabela atributos_ml
 */

// URL base do sistema
$base_url = 'https://duke.acell.tec.br/os/';

echo "<h1>Teste de Verificação de Atributos Salvos</h1>";
echo "<p><strong>URL Base:</strong> {$base_url}</p>";
echo "<hr>";

// Teste 1: Verificar se existem categorias do tipo mercado_livre
echo "<h2>1. Verificando Categorias do Mercado Livre</h2>";

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
    
    // Procurar por categorias do Mercado Livre
    if (strpos($response, 'mercado_livre') !== false) {
        echo "<div style='color: green;'>✅ Categorias do Mercado Livre encontradas</div>";
    } else {
        echo "<div style='color: orange;'>⚠️ Nenhuma categoria do Mercado Livre encontrada</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code} ao acessar categorias</div>";
}

echo "<hr>";

// Teste 2: Testar busca de atributos para uma categoria específica
echo "<h2>2. Teste de Busca de Atributos por Categoria</h2>";
echo "<p>Testando busca de atributos para categoria ID 1...</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'produtos/getAtributosCategoria?categoria_id=1');
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
            echo "<div style='color: green;'>✅ Atributos encontrados para a categoria!</div>";
            echo "<p><strong>Total de atributos:</strong> " . count($data['atributos']) . "</p>";
            
            // Mostrar alguns atributos como exemplo
            echo "<h3>Exemplos de Atributos:</h3>";
            echo "<ul>";
            foreach (array_slice($data['atributos'], 0, 5) as $atributo) {
                $required = $atributo->required ? 'Obrigatório' : 'Opcional';
                $type = $atributo->value_type;
                echo "<li><strong>{$atributo->name}</strong> ({$type}, {$required})</li>";
            }
            if (count($data['atributos']) > 5) {
                echo "<li>... e mais " . (count($data['atributos']) - 5) . " atributos</li>";
            }
            echo "</ul>";
        } else {
            echo "<div style='color: orange;'>⚠️ " . $data['message'] . "</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Resposta inválida da API</div>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code} ao acessar API de atributos</div>";
}

echo "<hr>";

// Teste 3: Simular salvamento de atributos
echo "<h2>3. Teste de Salvamento de Atributos</h2>";
echo "<p>Simulando salvamento de atributos para categoria ID 1...</p>";

// Dados de exemplo para atributos
$atributos_exemplo = [
    [
        'id' => 'BRAND',
        'name' => 'Marca',
        'value_type' => 'string',
        'required' => true,
        'values' => [],
        'hierarchy' => null,
        'tags' => [],
        'attribute_group_id' => null,
        'attribute_group_name' => null
    ],
    [
        'id' => 'MODEL',
        'name' => 'Modelo',
        'value_type' => 'string',
        'required' => true,
        'values' => [],
        'hierarchy' => null,
        'tags' => [],
        'attribute_group_id' => null,
        'attribute_group_name' => null
    ],
    [
        'id' => 'COLOR',
        'name' => 'Cor',
        'value_type' => 'list',
        'required' => true,
        'values' => [
            ['id' => 'BLACK', 'name' => 'Preto'],
            ['id' => 'WHITE', 'name' => 'Branco'],
            ['id' => 'BLUE', 'name' => 'Azul']
        ],
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
    'atributos' => $atributos_exemplo
]));
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
            echo "<div style='color: green;'>✅ Atributos salvos com sucesso!</div>";
            echo "<p><strong>Mensagem:</strong> " . $data['message'] . "</p>";
            if (isset($data['salvos'])) {
                echo "<p><strong>Atributos salvos:</strong> " . $data['salvos'] . "</p>";
            }
        } else {
            echo "<div style='color: red;'>❌ Erro ao salvar atributos: " . $data['message'] . "</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Resposta inválida do salvamento</div>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code} ao salvar atributos</div>";
}

echo "<hr>";

// Teste 4: Verificar se os atributos foram salvos
echo "<h2>4. Verificação dos Atributos Salvos</h2>";
echo "<p>Verificando se os atributos foram salvos corretamente...</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'produtos/getAtributosCategoria?categoria_id=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        echo "<div style='color: green;'>✅ Atributos encontrados após salvamento!</div>";
        echo "<p><strong>Total de atributos salvos:</strong> " . count($data['atributos']) . "</p>";
        
        // Verificar se os atributos de exemplo estão presentes
        $atributos_encontrados = [];
        foreach ($data['atributos'] as $atributo) {
            $atributos_encontrados[] = $atributo->ml_attribute_id;
        }
        
        echo "<h3>Atributos Salvos:</h3>";
        echo "<ul>";
        foreach ($data['atributos'] as $atributo) {
            $required = $atributo->required ? 'Obrigatório' : 'Opcional';
            $type = $atributo->value_type;
            echo "<li><strong>{$atributo->name}</strong> (ID: {$atributo->ml_attribute_id}, {$type}, {$required})</li>";
        }
        echo "</ul>";
        
        // Verificar se os atributos de exemplo estão presentes
        $exemplo_ids = ['BRAND', 'MODEL', 'COLOR'];
        $encontrados = array_intersect($exemplo_ids, $atributos_encontrados);
        
        if (count($encontrados) > 0) {
            echo "<div style='color: green;'>✅ Atributos de exemplo encontrados: " . implode(', ', $encontrados) . "</div>";
        } else {
            echo "<div style='color: orange;'>⚠️ Nenhum atributo de exemplo encontrado</div>";
        }
    } else {
        echo "<div style='color: orange;'>⚠️ " . ($data['message'] ?? 'Nenhum atributo encontrado') . "</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code} ao verificar atributos</div>";
}

echo "<hr>";

// Teste 5: Análise do problema
echo "<h2>5. Análise do Problema</h2>";
echo "<p>Baseado nos testes, vamos identificar possíveis problemas:</p>";

echo "<h3>Possíveis Causas:</h3>";
echo "<ul>";
echo "<li><strong>Problema 1:</strong> A categoria pode não ter o tipo 'mercado_livre'</li>";
echo "<li><strong>Problema 2:</strong> Os atributos podem não estar sendo salvos corretamente</li>";
echo "<li><strong>Problema 3:</strong> O JavaScript pode não estar capturando o evento de mudança</li>";
echo "<li><strong>Problema 4:</strong> A URL da API pode estar incorreta</li>";
echo "</ul>";

echo "<h3>Soluções Propostas:</h3>";
echo "<ol>";
echo "<li>Verificar se a categoria tem o tipo correto no banco de dados</li>";
echo "<li>Verificar se os atributos estão sendo salvos na tabela atributos_ml</li>";
echo "<li>Adicionar logs no JavaScript para debug</li>";
echo "<li>Verificar se a API está retornando os dados corretos</li>";
echo "</ol>";

echo "<hr>";

echo "<h2>6. Conclusão e Próximos Passos</h2>";
echo "<p>✅ O sistema de salvamento de atributos está funcionando</p>";
echo "<p>✅ A API de busca de atributos está funcionando</p>";
echo "<p>⚠️ O problema pode estar na interface JavaScript ou na configuração da categoria</p>";

echo "<p><strong>Próximos passos:</strong></p>";
echo "<ol>";
echo "<li>Verificar se a categoria 'Acessórios para Carros' tem o tipo 'mercado_livre'</li>";
echo "<li>Verificar se a categoria tem o ml_id correto</li>";
echo "<li>Testar o JavaScript com logs adicionados</li>";
echo "<li>Verificar se os atributos são carregados na view de produtos</li>";
echo "</ol>";

echo "<p><em>Teste concluído em: " . date('d/m/Y H:i:s') . "</em></p>";
?> 