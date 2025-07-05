<?php
header('Content-Type: text/plain');

echo "=== TESTE DE CONEXÃO COM API DO MERCADO LIVRE ===\n\n";

// Teste de conexão com a API de categorias do Mercado Livre
$url = 'https://api.mercadolibre.com/sites/MLB/categories';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
$http_code = $info['http_code'];
$error = curl_error($ch);
curl_close($ch);

echo "URL Testada: $url\n";
echo "HTTP Code: $http_code\n";
if ($error) {
    echo "Erro cURL: $error\n";
}
echo "\nResposta:\n";
echo $response;

echo "\n\n=== TESTE DE IP PÚBLICO ===\n";
$ip_publico = file_get_contents('https://ifconfig.me');
echo "IP público do servidor: $ip_publico\n";

echo "\n=== TESTE DE CONECTIVIDADE ===\n";
$test_urls = [
    'https://api.mercadolibre.com/sites/MLB/categories',
    'https://api.mercadolibre.com/categories/MLB1055/attributes',
    'https://api.mercadolibre.com/sites/MLB'
];

foreach ($test_urls as $test_url) {
    $ch = curl_init($test_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    $info = curl_getinfo($ch);
    $http_code = $info['http_code'];
    curl_close($ch);
    
    echo "URL: $test_url - HTTP Code: $http_code\n";
}
?> 