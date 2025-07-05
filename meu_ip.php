<?php
header('Content-Type: text/plain');

echo "=== INFORMAÇÕES DO SERVIDOR ===\n\n";

echo "IP público do servidor: " . file_get_contents('https://ifconfig.me') . "\n";
echo "Data/Hora: " . date('Y-m-d H:i:s') . "\n";
echo "Servidor: " . $_SERVER['SERVER_NAME'] . "\n";
echo "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";

echo "\n=== TESTE DE CONECTIVIDADE BÁSICA ===\n";

$test_urls = [
    'https://www.google.com',
    'https://api.mercadolibre.com',
    'https://ifconfig.me'
];

foreach ($test_urls as $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    $info = curl_getinfo($ch);
    $http_code = $info['http_code'];
    curl_close($ch);
    
    echo "URL: $url - HTTP Code: $http_code\n";
}
?> 