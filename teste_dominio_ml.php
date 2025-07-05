<?php
// Teste de aceitação do domínio pela API do Mercado Livre
echo "<h2>Teste de Aceitação do Domínio pela API do Mercado Livre</h2>";

// Informações do domínio
$dominio = "duke.acell.tec.br";
$url_completa = "https://duke.acell.tec.br/os/";

echo "<h3>Informações do Domínio:</h3>";
echo "<ul>";
echo "<li><strong>Domínio:</strong> $dominio</li>";
echo "<li><strong>URL Completa:</strong> $url_completa</li>";
echo "<li><strong>IP:</strong> " . gethostbyname($dominio) . "</li>";
echo "</ul>";

// Teste 1: Verificar se o domínio está acessível
echo "<h3>1. Teste de Acessibilidade do Domínio</h3>";
$headers = get_headers($url_completa, 1);
if ($headers) {
    echo "<p style='color: green;'>✅ Domínio acessível - HTTP Code: " . $headers[0] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Domínio não acessível</p>";
}

// Teste 2: Verificar User-Agent e Headers
echo "<h3>2. Teste de Headers e User-Agent</h3>";

$user_agents = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'PostmanRuntime/7.32.3',
    'curl/7.68.0'
];

$url_test = "https://api.mercadolibre.com/sites/MLB/categories";

foreach ($user_agents as $index => $user_agent) {
    echo "<h4>Tentativa " . ($index + 1) . " - User-Agent: " . substr($user_agent, 0, 50) . "...</h4>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_test);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json, text/plain, */*',
        'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
        'Accept-Encoding: gzip, deflate, br',
        'Connection: keep-alive',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'Referer: https://www.mercadolivre.com.br/',
        'Origin: https://www.mercadolivre.com.br',
        'sec-ch-ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'sec-fetch-dest: empty',
        'sec-fetch-mode: cors',
        'sec-fetch-site: cross-site',
        'DNT: 1'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    echo "<p>HTTP Code: $http_code</p>";
    
    if ($response !== false && $http_code === 200) {
        echo "<p style='color: green;'>✅ Sucesso! API aceitou a requisição.</p>";
        break;
    } else {
        echo "<p style='color: red;'>❌ Falha - HTTP Code: $http_code</p>";
        if ($curl_error) {
            echo "<p>Erro cURL: $curl_error</p>";
        }
        if ($response) {
            $response_data = json_decode($response, true);
            if ($response_data) {
                echo "<p>Resposta da API: " . json_encode($response_data, JSON_PRETTY_PRINT) . "</p>";
            }
        }
    }
    
    // Aguardar antes da próxima tentativa
    usleep(1000000); // 1 segundo
}

// Teste 3: Verificar se é problema de rate limiting
echo "<h3>3. Teste de Rate Limiting</h3>";
echo "<p>Vamos testar com diferentes intervalos de tempo...</p>";

for ($i = 1; $i <= 3; $i++) {
    echo "<h4>Tentativa $i com intervalo de " . ($i * 2) . " segundos</h4>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_test);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>HTTP Code: $http_code</p>";
    
    if ($response !== false && $http_code === 200) {
        echo "<p style='color: green;'>✅ Sucesso na tentativa $i!</p>";
        break;
    } else {
        echo "<p style='color: red;'>❌ Falha na tentativa $i</p>";
    }
    
    // Aguardar antes da próxima tentativa
    sleep($i * 2);
}

// Teste 4: Verificar se é problema de proxy ou firewall
echo "<h3>4. Teste de Conectividade Direta</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.mercadolibre.com/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>Teste de conectividade direta com api.mercadolibre.com:</p>";
echo "<p>HTTP Code: $http_code</p>";

if ($http_code === 200) {
    echo "<p style='color: green;'>✅ Conectividade direta funcionando</p>";
} else {
    echo "<p style='color: red;'>❌ Problema de conectividade</p>";
}

echo "<h3>5. Recomendações</h3>";
echo "<ul>";
echo "<li><strong>Verificar Firewall:</strong> Certifique-se de que o servidor não está bloqueando requisições para api.mercadolibre.com</li>";
echo "<li><strong>Verificar Proxy:</strong> Se houver proxy, configure-o corretamente</li>";
echo "<li><strong>Verificar Rate Limiting:</strong> O Mercado Livre pode estar limitando requisições por IP</li>";
echo "<li><strong>Verificar User-Agent:</strong> Alguns User-Agents podem ser bloqueados</li>";
echo "<li><strong>Verificar Headers:</strong> Headers específicos podem ser necessários</li>";
echo "</ul>";

echo "<h3>6. Próximos Passos</h3>";
echo "<ol>";
echo "<li>Verificar se o servidor tem acesso à internet</li>";
echo "<li>Verificar configurações de firewall</li>";
echo "<li>Testar com diferentes User-Agents</li>";
echo "<li>Implementar retry com backoff exponencial</li>";
echo "<li>Considerar usar proxy se necessário</li>";
echo "</ol>";
?> 