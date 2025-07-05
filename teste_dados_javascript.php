<?php
/**
 * Teste dos Dados do JavaScript
 * Simula os dados que o JavaScript está enviando para debug
 */

echo "<h1>Teste dos Dados do JavaScript</h1>";
echo "<hr>";

// Simular os dados que o JavaScript está enviando
$dados_simulados = [
    'categoria_id' => 1,
    'atributos' => [
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
    ]
];

echo "<h2>Dados Simulados:</h2>";
echo "<pre>" . json_encode($dados_simulados, JSON_PRETTY_PRINT) . "</pre>";

echo "<hr>";

// Simular o processamento do controlador
echo "<h2>Simulação do Processamento:</h2>";

$categoria_id = $dados_simulados['categoria_id'];
$atributos = $dados_simulados['atributos'];

echo "<p><strong>Categoria ID:</strong> {$categoria_id}</p>";
echo "<p><strong>Total de atributos:</strong> " . count($atributos) . "</p>";

$salvos = 0;
$erros = [];

foreach ($atributos as $atributo) {
    echo "<h3>Processando atributo: {$atributo['name']}</h3>";
    
    // Preparar dados com verificações de segurança
    $dados = [
        'name' => isset($atributo['name']) ? $atributo['name'] : '',
        'value_type' => isset($atributo['value_type']) ? $atributo['value_type'] : 'string',
        'required' => isset($atributo['required']) && $atributo['required'] ? 1 : 0,
        'values' => json_encode(isset($atributo['values']) ? $atributo['values'] : []),
        'hierarchy' => isset($atributo['hierarchy']) ? $atributo['hierarchy'] : null,
        'tags' => json_encode(isset($atributo['tags']) ? $atributo['tags'] : []),
        'attribute_group_id' => isset($atributo['attribute_group_id']) ? $atributo['attribute_group_id'] : null,
        'attribute_group_name' => isset($atributo['attribute_group_name']) ? $atributo['attribute_group_name'] : null,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Adicionar campos para inserção
    $dados['categoria_id'] = $categoria_id;
    $dados['ml_attribute_id'] = $atributo['id'];
    $dados['status'] = 1;
    $dados['created_at'] = date('Y-m-d H:i:s');
    
    echo "<p><strong>Dados preparados:</strong></p>";
    echo "<pre>" . json_encode($dados, JSON_PRETTY_PRINT) . "</pre>";
    
    // Verificar se os dados estão corretos
    $campos_obrigatorios = ['categoria_id', 'ml_attribute_id', 'name', 'value_type', 'required', 'values', 'status'];
    $campos_faltando = [];
    
    foreach ($campos_obrigatorios as $campo) {
        if (!isset($dados[$campo])) {
            $campos_faltando[] = $campo;
        }
    }
    
    if (empty($campos_faltando)) {
        echo "<div style='color: green;'>✅ Todos os campos obrigatórios estão presentes</div>";
        $salvos++;
    } else {
        echo "<div style='color: red;'>❌ Campos faltando: " . implode(', ', $campos_faltando) . "</div>";
        $erros[] = "Campos obrigatórios faltando para '{$atributo['name']}'";
    }
    
    echo "<hr>";
}

echo "<h2>Resultado da Simulação:</h2>";
echo "<p><strong>Atributos processados com sucesso:</strong> {$salvos}</p>";
echo "<p><strong>Erros encontrados:</strong> " . count($erros) . "</p>";

if (!empty($erros)) {
    echo "<h3>Erros:</h3>";
    echo "<ul>";
    foreach ($erros as $erro) {
        echo "<li>{$erro}</li>";
    }
    echo "</ul>";
}

echo "<hr>";

echo "<h2>Conclusão</h2>";
if ($salvos > 0 && empty($erros)) {
    echo "<div style='color: green;'>✅ Os dados estão corretos. O problema pode estar na conexão com o banco ou na estrutura da tabela.</div>";
} else {
    echo "<div style='color: red;'>❌ Há problemas nos dados. Verifique os erros acima.</div>";
}

echo "<p><em>Teste concluído em: " . date('d/m/Y H:i:s') . "</em></p>";
?> 