<?php
// Teste de atributos dinâmicos para celulares
require_once 'application/config/autoload.php';

echo "<h2>Teste de Atributos Dinâmicos para Celulares (MLB5726)</h2>";

// Simular busca de atributos da categoria MLB5726
$ml_id = "MLB5726";
$categoria_id = 1; // ID da categoria no banco

echo "<h3>Buscando atributos para categoria: $ml_id</h3>";

// Simular chamada para buscarAtributosAlternativos
$atributos_por_categoria = [
    'MLB5726' => [
        ['id' => 'BRAND', 'name' => 'Marca', 'value_type' => 'string', 'required' => true, 'values' => []],
        ['id' => 'MODEL', 'name' => 'Modelo', 'value_type' => 'string', 'required' => true, 'values' => []],
        ['id' => 'COLOR', 'name' => 'Cor', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'BLACK', 'name' => 'Preto'], ['id' => 'WHITE', 'name' => 'Branco'],
            ['id' => 'BLUE', 'name' => 'Azul'], ['id' => 'RED', 'name' => 'Vermelho'],
            ['id' => 'GOLD', 'name' => 'Dourado'], ['id' => 'SILVER', 'name' => 'Prateado'],
            ['id' => 'GREEN', 'name' => 'Verde'], ['id' => 'PURPLE', 'name' => 'Roxo'],
            ['id' => 'PINK', 'name' => 'Rosa'], ['id' => 'ORANGE', 'name' => 'Laranja']
        ]],
        ['id' => 'STORAGE_CAPACITY', 'name' => 'Capacidade de Armazenamento', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '32GB', 'name' => '32 GB'], ['id' => '64GB', 'name' => '64 GB'],
            ['id' => '128GB', 'name' => '128 GB'], ['id' => '256GB', 'name' => '256 GB'],
            ['id' => '512GB', 'name' => '512 GB'], ['id' => '1TB', 'name' => '1 TB']
        ]],
        ['id' => 'SCREEN_SIZE', 'name' => 'Tamanho da Tela', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '5.5', 'name' => '5.5 polegadas'], ['id' => '6.0', 'name' => '6.0 polegadas'],
            ['id' => '6.1', 'name' => '6.1 polegadas'], ['id' => '6.7', 'name' => '6.7 polegadas'],
            ['id' => '6.8', 'name' => '6.8 polegadas'], ['id' => '7.0', 'name' => '7.0 polegadas']
        ]],
        ['id' => 'OPERATING_SYSTEM', 'name' => 'Sistema Operacional', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'ANDROID', 'name' => 'Android'], ['id' => 'IOS', 'name' => 'iOS'],
            ['id' => 'HARMONY_OS', 'name' => 'HarmonyOS']
        ]],
        ['id' => 'CONDITION', 'name' => 'Condição', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'NEW', 'name' => 'Novo'], ['id' => 'USED', 'name' => 'Usado'],
            ['id' => 'REFURBISHED', 'name' => 'Recondicionado']
        ]],
        ['id' => 'SCREEN_TYPE', 'name' => 'Tipo de Tela', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'LCD', 'name' => 'LCD'], ['id' => 'OLED', 'name' => 'OLED'],
            ['id' => 'AMOLED', 'name' => 'AMOLED'], ['id' => 'IPS', 'name' => 'IPS'],
            ['id' => 'TFT', 'name' => 'TFT'], ['id' => 'SUPER_AMOLED', 'name' => 'Super AMOLED']
        ]],
        ['id' => 'CAMERA_RESOLUTION', 'name' => 'Resolução da Câmera', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '12MP', 'name' => '12 MP'], ['id' => '16MP', 'name' => '16 MP'],
            ['id' => '20MP', 'name' => '20 MP'], ['id' => '24MP', 'name' => '24 MP'],
            ['id' => '32MP', 'name' => '32 MP'], ['id' => '48MP', 'name' => '48 MP'],
            ['id' => '64MP', 'name' => '64 MP'], ['id' => '108MP', 'name' => '108 MP']
        ]],
        ['id' => 'CHIP_TYPE', 'name' => 'Tipo de Chip', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'MEDIATEK', 'name' => 'MediaTek'], ['id' => 'QUALCOMM', 'name' => 'Qualcomm'],
            ['id' => 'SAMSUNG', 'name' => 'Samsung'], ['id' => 'APPLE', 'name' => 'Apple'],
            ['id' => 'HUAWEI', 'name' => 'Huawei'], ['id' => 'UNISOC', 'name' => 'Unisoc']
        ]],
        ['id' => 'BATTERY_TYPE', 'name' => 'Tipo de Bateria', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'LI_ION', 'name' => 'Li-Ion'], ['id' => 'LI_PO', 'name' => 'Li-Po'],
            ['id' => 'NON_REMOVABLE', 'name' => 'Não Removível'], ['id' => 'REMOVABLE', 'name' => 'Removível']
        ]],
        ['id' => 'WATER_RESISTANCE', 'name' => 'Resistência à Água', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'YES', 'name' => 'Sim'], ['id' => 'NO', 'name' => 'Não'],
            ['id' => 'IP67', 'name' => 'IP67'], ['id' => 'IP68', 'name' => 'IP68']
        ]],
        ['id' => 'BIOMETRY', 'name' => 'Biometria', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'FINGERPRINT', 'name' => 'Impressão Digital'], ['id' => 'FACE_ID', 'name' => 'Face ID'],
            ['id' => 'IRIS', 'name' => 'Íris'], ['id' => 'NONE', 'name' => 'Nenhuma']
        ]],
        ['id' => 'DISPLAY_TYPE', 'name' => 'Tipo de Display', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'TFT', 'name' => 'TFT'], ['id' => 'IPS', 'name' => 'IPS'],
            ['id' => 'OLED', 'name' => 'OLED'], ['id' => 'AMOLED', 'name' => 'AMOLED'],
            ['id' => 'SUPER_AMOLED', 'name' => 'Super AMOLED'], ['id' => 'MINI_LED', 'name' => 'Mini LED']
        ]],
        ['id' => 'DISPLAY_RESOLUTION', 'name' => 'Resolução da Tela', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'HD', 'name' => 'HD (1280 x 720)'], ['id' => 'HD_PLUS', 'name' => 'HD+ (1600 x 720)'],
            ['id' => 'FULL_HD', 'name' => 'Full HD (1920 x 1080)'], ['id' => 'QHD', 'name' => 'QHD (2560 x 1440)'],
            ['id' => 'FHD_PLUS', 'name' => 'FHD+ (2400 x 1080)'], ['id' => 'QHD_PLUS', 'name' => 'QHD+ (3200 x 1440)'],
            ['id' => '4K', 'name' => '4K (3840 x 2160)']
        ]],
        ['id' => 'CAMERA_COUNT', 'name' => 'Número de Câmeras', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '1', 'name' => '1'], ['id' => '2', 'name' => '2'],
            ['id' => '3', 'name' => '3'], ['id' => '4', 'name' => '4'],
            ['id' => '5', 'name' => '5']
        ]],
        ['id' => 'FRONT_CAMERA_TYPE', 'name' => 'Tipo de Câmera Frontal', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'SINGLE', 'name' => 'Única'], ['id' => 'DUAL', 'name' => 'Dupla'],
            ['id' => 'TRIPLE', 'name' => 'Tripla'], ['id' => 'PUNCH_HOLE', 'name' => 'Punch Hole'],
            ['id' => 'NOTCH', 'name' => 'Notch'], ['id' => 'UNDER_DISPLAY', 'name' => 'Sob a Tela']
        ]],
        ['id' => 'FRONT_CAMERA_RESOLUTION', 'name' => 'Resolução da Câmera Frontal', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '5MP', 'name' => '5 MP'], ['id' => '8MP', 'name' => '8 MP'],
            ['id' => '12MP', 'name' => '12 MP'], ['id' => '16MP', 'name' => '16 MP'],
            ['id' => '20MP', 'name' => '20 MP'], ['id' => '32MP', 'name' => '32 MP']
        ]],
        ['id' => 'REAR_CAMERA_TYPE', 'name' => 'Tipo de Câmera Traseira', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'SINGLE', 'name' => 'Única'], ['id' => 'DUAL', 'name' => 'Dupla'],
            ['id' => 'TRIPLE', 'name' => 'Tripla'], ['id' => 'QUAD', 'name' => 'Quádrupla'],
            ['id' => 'PENTA', 'name' => 'Penta'], ['id' => 'HEXA', 'name' => 'Hexa']
        ]],
        ['id' => 'REAR_CAMERA_RESOLUTION', 'name' => 'Resolução da Câmera Traseira', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '12MP', 'name' => '12 MP'], ['id' => '16MP', 'name' => '16 MP'],
            ['id' => '20MP', 'name' => '20 MP'], ['id' => '24MP', 'name' => '24 MP'],
            ['id' => '32MP', 'name' => '32 MP'], ['id' => '48MP', 'name' => '48 MP'],
            ['id' => '64MP', 'name' => '64 MP'], ['id' => '108MP', 'name' => '108 MP']
        ]],
        ['id' => 'FLASH_TYPE', 'name' => 'Tipo de Flash', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'LED', 'name' => 'LED'], ['id' => 'DUAL_LED', 'name' => 'Dual LED'],
            ['id' => 'TRIPLE_LED', 'name' => 'Triple LED'], ['id' => 'QUAD_LED', 'name' => 'Quad LED'],
            ['id' => 'XENON', 'name' => 'Xenon'], ['id' => 'NONE', 'name' => 'Nenhum']
        ]],
        ['id' => 'STABILIZATION_TYPE', 'name' => 'Tipo de Estabilização', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'DIGITAL', 'name' => 'Digital'], ['id' => 'OPTICAL', 'name' => 'Óptica'],
            ['id' => 'HYBRID', 'name' => 'Híbrida'], ['id' => 'NONE', 'name' => 'Nenhuma']
        ]],
        ['id' => 'AUTOFOCUS_TYPE', 'name' => 'Tipo de Autofoco', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'AUTOMATIC', 'name' => 'Automático'], ['id' => 'MANUAL', 'name' => 'Manual'],
            ['id' => 'HYBRID', 'name' => 'Híbrido'], ['id' => 'DUAL_PIXEL', 'name' => 'Dual Pixel']
        ]],
        ['id' => 'ZOOM_TYPE', 'name' => 'Tipo de Zoom', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'DIGITAL', 'name' => 'Digital'], ['id' => 'OPTICAL', 'name' => 'Óptico'],
            ['id' => 'HYBRID', 'name' => 'Híbrido'], ['id' => 'NONE', 'name' => 'Nenhum']
        ]],
        ['id' => 'VIDEO_RECORDING_TYPE', 'name' => 'Tipo de Gravação de Vídeo', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'HD', 'name' => 'HD'], ['id' => 'FULL_HD', 'name' => 'Full HD'],
            ['id' => '4K', 'name' => '4K'], ['id' => '8K', 'name' => '8K']
        ]],
        ['id' => 'AUDIO_TYPE', 'name' => 'Tipo de Áudio', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'STEREO', 'name' => 'Estéreo'], ['id' => 'MONO', 'name' => 'Mono'],
            ['id' => 'SURROUND', 'name' => 'Surround'], ['id' => 'DOLBY', 'name' => 'Dolby']
        ]],
        ['id' => 'SPEAKER_TYPE', 'name' => 'Tipo de Alto-falante', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'SINGLE', 'name' => 'Único'], ['id' => 'DUAL', 'name' => 'Duplo'],
            ['id' => 'STEREO', 'name' => 'Estéreo'], ['id' => 'SURROUND', 'name' => 'Surround']
        ]],
        ['id' => 'MICROPHONE_TYPE', 'name' => 'Tipo de Microfone', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'SINGLE', 'name' => 'Único'], ['id' => 'DUAL', 'name' => 'Duplo'],
            ['id' => 'MULTIPLE', 'name' => 'Múltiplo'], ['id' => 'NOISE_CANCELLING', 'name' => 'Cancelamento de Ruído']
        ]],
        ['id' => 'AUDIO_CONNECTOR_TYPE', 'name' => 'Tipo de Conector de Áudio', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '3.5MM', 'name' => '3.5 mm'], ['id' => 'USB_C', 'name' => 'USB-C'],
            ['id' => 'LIGHTNING', 'name' => 'Lightning'], ['id' => 'NONE', 'name' => 'Nenhum']
        ]],
        ['id' => 'CONNECTIVITY_TYPE', 'name' => 'Tipo de Conectividade', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '4G', 'name' => '4G'], ['id' => '5G', 'name' => '5G'],
            ['id' => '3G', 'name' => '3G'], ['id' => '2G', 'name' => '2G']
        ]],
        ['id' => 'WIFI_TYPE', 'name' => 'Tipo de Wi-Fi', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '802.11_A_B_G_N', 'name' => '802.11 a/b/g/n'], ['id' => '802.11_A_B_G_N_AC', 'name' => '802.11 a/b/g/n/ac'],
            ['id' => '802.11_A_B_G_N_AC_AX', 'name' => '802.11 a/b/g/n/ac/ax'], ['id' => 'WIFI_6', 'name' => 'Wi-Fi 6']
        ]],
        ['id' => 'BLUETOOTH_TYPE', 'name' => 'Tipo de Bluetooth', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '4.0', 'name' => '4.0'], ['id' => '4.1', 'name' => '4.1'],
            ['id' => '4.2', 'name' => '4.2'], ['id' => '5.0', 'name' => '5.0'],
            ['id' => '5.1', 'name' => '5.1'], ['id' => '5.2', 'name' => '5.2']
        ]],
        ['id' => 'GPS_TYPE', 'name' => 'Tipo de GPS', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'A_GPS', 'name' => 'A-GPS'], ['id' => 'GLONASS', 'name' => 'GLONASS'],
            ['id' => 'GALILEO', 'name' => 'Galileo'], ['id' => 'BEIDOU', 'name' => 'BeiDou'],
            ['id' => 'NONE', 'name' => 'Nenhum']
        ]],
        ['id' => 'NFC_TYPE', 'name' => 'Tipo de NFC', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'YES', 'name' => 'Sim'], ['id' => 'NO', 'name' => 'Não']
        ]],
        ['id' => 'SENSOR_TYPE', 'name' => 'Tipo de Sensor', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'ACCELEROMETER', 'name' => 'Acelerômetro'], ['id' => 'GYROSCOPE', 'name' => 'Giroscópio'],
            ['id' => 'PROXIMITY', 'name' => 'Proximidade'], ['id' => 'LIGHT', 'name' => 'Luz'],
            ['id' => 'COMPASS', 'name' => 'Bússola'], ['id' => 'BAROMETER', 'name' => 'Barômetro'],
            ['id' => 'MULTIPLE', 'name' => 'Múltiplos']
        ]],
        ['id' => 'VIBRATION_TYPE', 'name' => 'Tipo de Vibração', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'VIBRATION_MOTOR', 'name' => 'Motor de Vibração'], ['id' => 'HAPTIC', 'name' => 'Háptico'],
            ['id' => 'NONE', 'name' => 'Nenhum']
        ]],
        ['id' => 'NOTIFICATION_TYPE', 'name' => 'Tipo de Notificação', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'LED', 'name' => 'LED'], ['id' => 'SOUND', 'name' => 'Som'],
            ['id' => 'VIBRATION', 'name' => 'Vibração'], ['id' => 'NONE', 'name' => 'Nenhuma']
        ]],
        ['id' => 'PROTECTION_TYPE', 'name' => 'Tipo de Proteção', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'GORILLA_GLASS', 'name' => 'Gorilla Glass'], ['id' => 'DRAGONTRAIL', 'name' => 'DragonTrail'],
            ['id' => 'NONE', 'name' => 'Nenhuma']
        ]],
        ['id' => 'RESISTANCE_TYPE', 'name' => 'Tipo de Resistência', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'IP67', 'name' => 'IP67'], ['id' => 'IP68', 'name' => 'IP68'],
            ['id' => 'IP69', 'name' => 'IP69'], ['id' => 'NONE', 'name' => 'Nenhuma']
        ]],
        ['id' => 'CERTIFICATION_TYPE', 'name' => 'Tipo de Certificação', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'CE', 'name' => 'CE'], ['id' => 'ROHS', 'name' => 'RoHS'],
            ['id' => 'FCC', 'name' => 'FCC'], ['id' => 'ANATEL', 'name' => 'ANATEL'],
            ['id' => 'MULTIPLE', 'name' => 'Múltiplas']
        ]],
        ['id' => 'WARRANTY_TYPE', 'name' => 'Tipo de Garantia', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'MANUFACTURER', 'name' => 'Fabricante'], ['id' => 'STORE', 'name' => 'Loja'],
            ['id' => 'EXTENDED', 'name' => 'Estendida'], ['id' => 'NONE', 'name' => 'Nenhuma']
        ]],
        ['id' => 'ORIGIN_TYPE', 'name' => 'Tipo de Origem', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'IMPORTED', 'name' => 'Importado'], ['id' => 'NATIONAL', 'name' => 'Nacional'],
            ['id' => 'MIXED', 'name' => 'Misto']
        ]],
        ['id' => 'PACKAGING_TYPE', 'name' => 'Tipo de Embalagem', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'ORIGINAL_BOX', 'name' => 'Caixa Original'], ['id' => 'GENERIC_BOX', 'name' => 'Caixa Genérica'],
            ['id' => 'BULK', 'name' => 'A Granel'], ['id' => 'NONE', 'name' => 'Sem Embalagem']
        ]],
        ['id' => 'ACCESSORIES_TYPE', 'name' => 'Tipo de Acessórios', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'CHARGER_CABLE_MANUAL', 'name' => 'Carregador, Cabo USB, Manual'],
            ['id' => 'CHARGER_CABLE', 'name' => 'Carregador, Cabo USB'],
            ['id' => 'CABLE_ONLY', 'name' => 'Apenas Cabo USB'],
            ['id' => 'NONE', 'name' => 'Nenhum Acessório']
        ]],
        ['id' => 'BATTERY_CAPACITY', 'name' => 'Capacidade da Bateria', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => '2000MAH', 'name' => '2000 mAh'], ['id' => '3000MAH', 'name' => '3000 mAh'],
            ['id' => '4000MAH', 'name' => '4000 mAh'], ['id' => '5000MAH', 'name' => '5000 mAh'],
            ['id' => '6000MAH', 'name' => '6000 mAh'], ['id' => '7000MAH', 'name' => '7000 mAh']
        ]],
        ['id' => 'CHARGING_TYPE', 'name' => 'Tipo de Carregamento', 'value_type' => 'list', 'required' => true, 'values' => [
            ['id' => 'USB_TYPE_C', 'name' => 'USB Type-C'], ['id' => 'MICRO_USB', 'name' => 'Micro USB'],
            ['id' => 'LIGHTNING', 'name' => 'Lightning'], ['id' => 'WIRELESS', 'name' => 'Sem Fio']
        ]]
    ]
];

$atributos = isset($atributos_por_categoria[$ml_id]) ? $atributos_por_categoria[$ml_id] : [];

echo "<h3>Atributos Encontrados: " . count($atributos) . "</h3>";

// Contar atributos obrigatórios
$obrigatorios = 0;
foreach ($atributos as $atributo) {
    if (isset($atributo['required']) && $atributo['required']) {
        $obrigatorios++;
    }
}

echo "<p><strong>Atributos Obrigatórios:</strong> $obrigatorios</p>";
echo "<p><strong>Atributos Opcionais:</strong> " . (count($atributos) - $obrigatorios) . "</p>";

echo "<h3>Lista de Atributos Dinâmicos:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Obrigatório</th><th>Valores</th></tr>";

foreach ($atributos as $atributo) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($atributo['id']) . "</td>";
    echo "<td>" . htmlspecialchars($atributo['name']) . "</td>";
    echo "<td>" . htmlspecialchars($atributo['value_type']) . "</td>";
    echo "<td>" . (isset($atributo['required']) && $atributo['required'] ? 'Sim' : 'Não') . "</td>";
    echo "<td>";
    if (isset($atributo['values']) && is_array($atributo['values'])) {
        echo count($atributo['values']) . " opções";
        if (count($atributo['values']) <= 5) {
            echo "<br><small>";
            foreach ($atributo['values'] as $value) {
                echo htmlspecialchars($value['name']) . ", ";
            }
            echo "</small>";
        }
    } else {
        echo "Campo livre";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Resumo:</h3>";
echo "<ul>";
echo "<li>✅ Total de atributos: " . count($atributos) . "</li>";
echo "<li>✅ Atributos obrigatórios: $obrigatorios</li>";
echo "<li>✅ Atributos opcionais: " . (count($atributos) - $obrigatorios) . "</li>";
echo "<li>✅ Tipos de campos: string, list</li>";
echo "</ul>";

echo "<h3>Status:</h3>";
if (count($atributos) >= 50) {
    echo "<p style='color: green; font-weight: bold;'>✅ Sistema preparado com todos os atributos obrigatórios para celulares!</p>";
    echo "<p>Agora o formulário de cadastro de produtos terá todos os campos necessários.</p>";
} else {
    echo "<p style='color: orange; font-weight: bold;'>⚠️ Ainda faltam alguns atributos obrigatórios.</p>";
}

echo "<h3>Próximos Passos:</h3>";
echo "<ol>";
echo "<li>Teste o cadastro de produtos no sistema</li>";
echo "<li>Selecione a categoria 'Celulares e Telefones'</li>";
echo "<li>Verifique se todos os atributos aparecem no formulário</li>";
echo "<li>Preencha os campos obrigatórios</li>";
echo "<li>Teste a sincronização com Mercado Livre</li>";
echo "</ol>";
?> 