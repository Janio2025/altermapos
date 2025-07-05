<?php
header('Content-Type: text/plain');

echo "=== TESTE COMPLETO DA API DO MERCADO LIVRE ===\n\n";

// Array de User-Agents para testar
$user_agents = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'PostmanRuntime/7.32.3',
    'curl/7.68.0'
];

// Array de URLs para testar
$urls_teste = [
    'https://api.mercadolibre.com/sites/MLB/categories',
    'https://api.mercadolibre.com/categories/MLB1055/attributes',
    'https://api.mercadolibre.com/sites/MLB'
];

echo "IP público do servidor: " . file_get_contents('https://ifconfig.me') . "\n\n";

// Teste 1: Diferentes User-Agents
echo "=== TESTE 1: DIFERENTES USER-AGENTS ===\n";
foreach ($user_agents as $index => $user_agent) {
    echo "\nTentativa " . ($index + 1) . " - User-Agent: " . substr($user_agent, 0, 50) . "...\n";
    
    $ch = curl_init('https://api.mercadolibre.com/sites/MLB/categories');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $http_code = $info['http_code'];
    curl_close($ch);
    
    echo "HTTP Code: $http_code\n";
    
    if ($http_code == 200) {
        echo "✅ SUCESSO! API acessível com este User-Agent.\n";
        break;
    } else {
        echo "❌ Falha - HTTP Code: $http_code\n";
    }
    
    sleep(1); // Pausa entre tentativas
}

// Teste 2: Diferentes Headers
echo "\n\n=== TESTE 2: HEADERS ADICIONAIS ===\n";
$headers = [
    'Accept: application/json',
    'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
    'Accept-Encoding: gzip, deflate, br',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1'
];

$ch = curl_init('https://api.mercadolibre.com/sites/MLB/categories');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
$http_code = $info['http_code'];
curl_close($ch);

echo "HTTP Code com headers adicionais: $http_code\n";

// Teste 3: Rate Limiting
echo "\n\n=== TESTE 3: RATE LIMITING ===\n";
echo "Fazendo 5 requisições com intervalos crescentes...\n";

for ($i = 1; $i <= 5; $i++) {
    $ch = curl_init('https://api.mercadolibre.com/sites/MLB/categories');
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
    
    if ($i < 5) {
        $sleep_time = $i * 2; // 2, 4, 6, 8 segundos
        echo "Aguardando $sleep_time segundos...\n";
        sleep($sleep_time);
    }
}

// Teste 4: Proxy (simulado)
echo "\n\n=== TESTE 4: CONECTIVIDADE DIRETA ===\n";
$ch = curl_init('https://api.mercadolibre.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_exec($ch);
$info = curl_getinfo($ch);
$http_code = $info['http_code'];
curl_close($ch);

echo "Conectividade direta com api.mercadolibre.com - HTTP Code: $http_code\n";

echo "\n=== RESUMO ===\n";
if ($http_code == 200) {
    echo "✅ API do Mercado Livre está acessível neste servidor!\n";
} else {
    echo "❌ API do Mercado Livre continua bloqueada (HTTP $http_code)\n";
    echo "Recomendações:\n";
    echo "- Verificar se o IP está na blacklist do Mercado Livre\n";
    echo "- Contatar suporte do Mercado Livre\n";
    echo "- Considerar uso de proxy externo\n";
    echo "- Verificar se há necessidade de autenticação OAuth\n";
}
?> 