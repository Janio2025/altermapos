<?php
header('Content-Type: text/plain');

echo "=== TESTE DE ATRIBUTOS DA API DO MERCADO LIVRE ===\n\n";

// Teste de atributos de celulares (MLB1055)
$url = 'https://api.mercadolibre.com/categories/MLB1055/attributes';
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

echo "\n\n=== TESTE DE OUTRAS CATEGORIAS ===\n";

$categorias_teste = [
    'MLB5726' => 'Eletrônicos, Áudio e Vídeo',
    'MLB1648' => 'Computação',
    'MLB1144' => 'Acessórios para Veículos'
];

foreach ($categorias_teste as $id => $nome) {
    $url = "https://api.mercadolibre.com/categories/$id/attributes";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    $info = curl_getinfo($ch);
    $http_code = $info['http_code'];
    curl_close($ch);
    
    echo "Categoria: $nome ($id) - HTTP Code: $http_code\n";
}

echo "\n=== TESTE DE RATE LIMITING ===\n";
echo "Fazendo 3 requisições com intervalo de 2 segundos...\n";

for ($i = 1; $i <= 3; $i++) {
    $url = 'https://api.mercadolibre.com/sites/MLB/categories';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    $info = curl_getinfo($ch);
    $http_code = $info['http_code'];
    curl_close($ch);
    
    echo "Tentativa $i - HTTP Code: $http_code\n";
    
    if ($i < 3) {
        sleep(2);
    }
}
?> 