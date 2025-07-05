<?php
/**
 * Teste de Salvamento de Atributos
 * Simula exatamente o que o JavaScript está enviando
 */

$base_url = 'https://duke.acell.tec.br/os/';

echo "<h1>Teste de Salvamento de Atributos</h1>";
echo "<p><strong>URL Base:</strong> {$base_url}</p>";
echo "<hr>";

// Primeiro, vamos verificar quais categorias existem
echo "<h2>1. Verificando Categorias Existentes</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'categorias');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    echo "<div style='color: green;'>✅ Página de categorias acessível</div>";
    
    // Procurar por categorias do Mercado Livre
    if (strpos($response, 'mercado_livre') !== false) {
        echo "<div style='color: green;'>✅ Categorias do Mercado Livre encontradas</div>";
        
        // Extrair IDs das categorias
        preg_match_all('/data-categoria-id="(\d+)"/', $response, $matches);
        if (!empty($matches[1])) {
            echo "<p><strong>Categorias encontradas:</strong></p>";
            echo "<ul>";
            foreach ($matches[1] as $id) {
                echo "<li>ID: {$id}</li>";
            }
            echo "</ul>";
            
            $categoria_id_teste = $matches[1][0]; // Usar o primeiro ID encontrado
        } else {
            echo "<div style='color: orange;'>⚠️ Nenhum ID de categoria encontrado</div>";
            $categoria_id_teste = 1; // Usar ID padrão
        }
    } else {
        echo "<div style='color: orange;'>⚠️ Nenhuma categoria do Mercado Livre encontrada</div>";
        $categoria_id_teste = 1; // Usar ID padrão
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code} ao acessar categorias</div>";
    $categoria_id_teste = 1; // Usar ID padrão
}

echo "<hr>";

// Teste 2: Simular salvamento de atributos
echo "<h2>2. Teste de Salvamento de Atributos</h2>";
echo "<p>Usando categoria_id: {$categoria_id_teste}</p>";

$atributos_teste = [
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
    'categoria_id' => $categoria_id_teste,
    'atributos' => $atributos_teste
]));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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
            echo "<div style='color: red;'>❌ Erro ao salvar: " . $data['message'] . "</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Resposta inválida</div>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<div style='color: red;'>❌ Erro HTTP {$http_code}</div>";
}

echo "<hr>";

// Teste 3: Verificar se os atributos foram salvos
echo "<h2>3. Verificação dos Atributos Salvos</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'produtos/getAtributosCategoria?categoria_id=' . $categoria_id_teste);
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
            if (in_array($atributo->ml_attribute_id, ['BRAND', 'MODEL', 'COLOR'])) {
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

echo "<h2>4. Resumo</h2>";
echo "<p><strong>Categoria ID usado:</strong> {$categoria_id_teste}</p>";
echo "<p><strong>Atributos testados:</strong> BRAND, MODEL, COLOR</p>";

echo "<p><em>Teste concluído em: " . date('d/m/Y H:i:s') . "</em></p>";
?> 