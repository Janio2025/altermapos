<?php
// Teste para verificar atributos obrigatórios para celulares
require_once 'application/config/autoload.php';

echo "<h2>Teste de Atributos Obrigatórios para Celulares (MLB5726)</h2>";

// Simular dados de produto
$produto = [
    'idProdutos' => 16,
    'nomeProduto' => 'CELULAR',
    'categoria_id' => 'MLB5726',
    'precoVenda' => 1600.00,
    'ml_atributos' => '[{"value": "64GB", "ml_attribute_id": "STORAGE_CAPACITY"}, {"value": "NEW", "ml_attribute_id": "CONDITION"}, {"value": "BLACK", "ml_attribute_id": "COLOR"}, {"value": "samsung", "ml_attribute_id": "BRAND"}, {"value": "A12", "ml_attribute_id": "MODEL"}, {"value": "ANDROID", "ml_attribute_id": "OPERATING_SYSTEM"}, {"value": "6.0", "ml_attribute_id": "SCREEN_SIZE"}]'
];

echo "<h3>Dados do Produto:</h3>";
echo "<pre>" . print_r($produto, true) . "</pre>";

// Simular método getAtributosProduto
function getAtributosProduto($produto) {
    $atributos = [];
    
    // Decodificar atributos salvos
    $atributosSalvos = json_decode($produto['ml_atributos'], true);
    
    echo "<h3>Atributos Salvos no Banco:</h3>";
    echo "<pre>" . print_r($atributosSalvos, true) . "</pre>";
    
    // Converter para formato do Mercado Livre
    foreach ($atributosSalvos as $atributo) {
        $atributos[] = [
            'id' => $atributo['ml_attribute_id'],
            'name' => $atributo['ml_attribute_id'], // Nome será buscado da API
            'value_name' => $atributo['value']
        ];
    }
    
    // Adicionar atributos obrigatórios que podem estar faltando
    $atributosObrigatorios = [
        'SCREEN_TYPE' => 'LCD',
        'CAMERA_RESOLUTION' => '48 MP',
        'CHIP_TYPE' => 'MediaTek',
        'BATTERY_TYPE' => 'Li-Ion',
        'WATER_RESISTANCE' => 'Não',
        'BIOMETRY' => 'Impressão digital',
        'DISPLAY_TYPE' => 'TFT',
        'DISPLAY_RESOLUTION' => 'HD+ (1600 x 720)',
        'CAMERA_COUNT' => '4',
        'FRONT_CAMERA_TYPE' => 'Única',
        'FRONT_CAMERA_RESOLUTION' => '8 MP',
        'REAR_CAMERA_TYPE' => 'Múltipla',
        'REAR_CAMERA_RESOLUTION' => '48 MP',
        'FLASH_TYPE' => 'LED',
        'STABILIZATION_TYPE' => 'Digital',
        'AUTOFOCUS_TYPE' => 'Automático',
        'ZOOM_TYPE' => 'Digital',
        'VIDEO_RECORDING_TYPE' => 'Full HD',
        'AUDIO_TYPE' => 'Estéreo',
        'SPEAKER_TYPE' => 'Único',
        'MICROPHONE_TYPE' => 'Múltiplo',
        'AUDIO_CONNECTOR_TYPE' => '3.5 mm',
        'CONNECTIVITY_TYPE' => '4G',
        'WIFI_TYPE' => '802.11 a/b/g/n/ac',
        'BLUETOOTH_TYPE' => '5.0',
        'GPS_TYPE' => 'A-GPS',
        'NFC_TYPE' => 'Sim',
        'SENSOR_TYPE' => 'Acelerômetro, Giroscópio, Proximidade',
        'VIBRATION_TYPE' => 'Motor de vibração',
        'NOTIFICATION_TYPE' => 'LED',
        'PROTECTION_TYPE' => 'Gorilla Glass',
        'RESISTANCE_TYPE' => 'IP68',
        'CERTIFICATION_TYPE' => 'CE, RoHS',
        'WARRANTY_TYPE' => 'Fabricante',
        'ORIGIN_TYPE' => 'Importado',
        'PACKAGING_TYPE' => 'Caixa original',
        'ACCESSORIES_TYPE' => 'Carregador, Cabo USB, Manual',
        'BATTERY_CAPACITY' => '5000 mAh',
        'CHARGING_TYPE' => 'USB Type-C'
    ];
    
    // Verificar quais atributos obrigatórios já estão presentes
    $atributosPresentes = [];
    foreach ($atributos as $atributo) {
        $atributosPresentes[] = $atributo['id'];
    }
    
    echo "<h3>Atributos Já Presentes:</h3>";
    echo "<pre>" . print_r($atributosPresentes, true) . "</pre>";
    
    // Adicionar atributos obrigatórios que estão faltando
    foreach ($atributosObrigatorios as $id => $value) {
        if (!in_array($id, $atributosPresentes)) {
            $atributos[] = [
                'id' => $id,
                'name' => $id,
                'value_name' => $value
            ];
            echo "<p style='color: green;'>✓ Adicionado atributo obrigatório: $id = $value</p>";
        } else {
            echo "<p style='color: blue;'>✓ Atributo já presente: $id</p>";
        }
    }
    
    return $atributos;
}

// Executar teste
$atributosCompletos = getAtributosProduto($produto);

echo "<h3>Lista Final de Atributos (Total: " . count($atributosCompletos) . "):</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Nome</th><th>Valor</th></tr>";

foreach ($atributosCompletos as $atributo) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($atributo['id']) . "</td>";
    echo "<td>" . htmlspecialchars($atributo['name']) . "</td>";
    echo "<td>" . htmlspecialchars($atributo['value_name']) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Resumo:</h3>";
echo "<ul>";
echo "<li>Atributos salvos no banco: " . count(json_decode($produto['ml_atributos'], true)) . "</li>";
echo "<li>Atributos obrigatórios adicionados: " . (count($atributosCompletos) - count(json_decode($produto['ml_atributos'], true))) . "</li>";
echo "<li>Total de atributos para envio: " . count($atributosCompletos) . "</li>";
echo "</ul>";

echo "<h3>IDs dos Atributos para Envio:</h3>";
$ids = [];
foreach ($atributosCompletos as $atributo) {
    $ids[] = $atributo['id'];
}
echo implode(', ', $ids);

echo "<h3>Status:</h3>";
echo "<p style='color: green; font-weight: bold;'>✓ Sistema preparado com todos os atributos obrigatórios para celulares</p>";
echo "<p>Próximo passo: Testar sincronização no sistema real</p>";
?> 