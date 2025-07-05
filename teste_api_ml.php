<?php
/**
 * Teste de Conectividade com API do Mercado Livre
 * Este arquivo testa se conseguimos acessar a API do ML
 */

echo "<h1>🔍 Teste de Conectividade - API do Mercado Livre</h1>";

// Verificar se cURL está disponível
if (!function_exists('curl_init')) {
    echo "<p style='color: red;'>❌ cURL não está disponível no servidor.</p>";
    echo "<p>Para resolver, habilite a extensão cURL no PHP.</p>";
    exit;
}

echo "<p style='color: green;'>✅ cURL está disponível.</p>";

// Testar diferentes endpoints da API
$endpoints = [
    'Categorias' => 'https://api.mercadolibre.com/sites/MLB/categories',
    'Categoria Específica' => 'https://api.mercadolibre.com/categories/MLB5726',
    'Atributos' => 'https://api.mercadolibre.com/categories/MLB5726/attributes'
];

foreach ($endpoints as $nome => $url) {
    echo "<h3>📡 Testando: $nome</h3>";
    echo "<p><strong>URL:</strong> $url</p>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json, text/plain, */*',
        'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
        'Accept-Encoding: gzip, deflate, br',
        'Connection: keep-alive',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'Referer: https://www.mercadolivre.com.br/',
        'Origin: https://www.mercadolivre.com.br'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    $curl_info = curl_getinfo($ch);
    curl_close($ch);
    
    echo "<ul>";
    echo "<li><strong>HTTP Code:</strong> $http_code</li>";
    echo "<li><strong>cURL Error:</strong> " . ($curl_error ?: 'Nenhum') . "</li>";
    echo "<li><strong>Tempo de resposta:</strong> " . round($curl_info['total_time'], 2) . "s</li>";
    echo "<li><strong>Tamanho da resposta:</strong> " . strlen($response) . " bytes</li>";
    echo "</ul>";
    
    if ($response === false || $curl_error) {
        echo "<p style='color: red;'>❌ Erro na conexão: $curl_error</p>";
    } elseif ($http_code !== 200) {
        echo "<p style='color: red;'>❌ Erro HTTP $http_code</p>";
        echo "<p><strong>Resposta:</strong> " . htmlspecialchars(substr($response, 0, 500)) . "...</p>";
    } else {
        echo "<p style='color: green;'>✅ Conexão bem-sucedida!</p>";
        
        $data = json_decode($response, true);
        if ($data) {
            echo "<p>✅ JSON decodificado com sucesso.</p>";
            
            if ($nome === 'Categorias') {
                echo "<p>📊 Total de categorias: " . count($data) . "</p>";
                echo "<h4>Primeiras 3 categorias:</h4>";
                echo "<ul>";
                for ($i = 0; $i < min(3, count($data)); $i++) {
                    $cat = $data[$i];
                    echo "<li><strong>{$cat['name']}</strong> (ID: {$cat['id']})</li>";
                }
                echo "</ul>";
            } elseif ($nome === 'Categoria Específica') {
                echo "<p><strong>Nome:</strong> {$data['name']}</p>";
                echo "<p><strong>ID:</strong> {$data['id']}</p>";
                if (isset($data['children_categories'])) {
                    echo "<p><strong>Subcategorias:</strong> " . count($data['children_categories']) . "</p>";
                }
            } elseif ($nome === 'Atributos') {
                echo "<p>📊 Total de atributos: " . count($data) . "</p>";
                echo "<h4>Primeiros 3 atributos:</h4>";
                echo "<ul>";
                for ($i = 0; $i < min(3, count($data)); $i++) {
                    $attr = $data[$i];
                    echo "<li><strong>{$attr['name']}</strong> (ID: {$attr['id']}, Tipo: {$attr['value_type']})</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p style='color: red;'>❌ Erro ao decodificar JSON: " . json_last_error_msg() . "</p>";
        }
    }
    
    echo "<hr>";
}

// Verificar configurações do PHP
echo "<h3>⚙️ Configurações do PHP:</h3>";
echo "<ul>";
echo "<li><strong>allow_url_fopen:</strong> " . (ini_get('allow_url_fopen') ? 'Habilitado' : 'Desabilitado') . "</li>";
echo "<li><strong>curl.cainfo:</strong> " . (ini_get('curl.cainfo') ?: 'Não definido') . "</li>";
echo "<li><strong>curl.ssl_verify_peer:</strong> " . (ini_get('curl.ssl_verify_peer') ? 'Habilitado' : 'Desabilitado') . "</li>";
echo "<li><strong>max_execution_time:</strong> " . ini_get('max_execution_time') . "s</li>";
echo "<li><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</li>";
echo "</ul>";

// Testar DNS
echo "<h3>🌐 Teste de DNS:</h3>";
$host = 'api.mercadolibre.com';
$ip = gethostbyname($host);
if ($ip !== $host) {
    echo "<p style='color: green;'>✅ DNS resolvido: $host → $ip</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao resolver DNS para $host</p>";
}

echo "<hr>";
echo "<h3>📋 Resumo:</h3>";
echo "<p><strong>Se todos os testes passaram, a API deve funcionar no sistema.</strong></p>";
echo "<p>Se houver erros, verifique:</p>";
echo "<ul>";
echo "<li>Se o servidor tem acesso à internet</li>";
echo "<li>Se o firewall não está bloqueando conexões HTTPS</li>";
echo "<li>Se a extensão cURL está habilitada no PHP</li>";
echo "<li>Se as configurações de SSL estão corretas</li>";
echo "<li>Se o provedor de hospedagem não está bloqueando a API</li>";
echo "</ul>";

echo "<p><strong>💡 Dica:</strong> Se a API estiver bloqueada, o sistema usará dados pré-definidos automaticamente.</p>";

// Teste da API do Mercado Livre com melhorias
require_once 'application/config/autoload.php';

echo "<h2>Teste da API do Mercado Livre</h2>";

// Teste 1: Buscar categorias
echo "<h3>1. Teste de Busca de Categorias</h3>";

$url = "https://api.mercadolibre.com/sites/MLB/categories";

// User-Agents alternativos
$user_agents = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
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

$response = null;
$http_code = 0;
$curl_error = '';

foreach ($user_agents as $index => $user_agent) {
    echo "<p>Tentativa " . ($index + 1) . " com User-Agent: " . substr($user_agent, 0, 50) . "...</p>";
    
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    echo "<p>HTTP Code: $http_code</p>";
    
    if ($response !== false && $http_code === 200) {
        echo "<p style='color: green;'>✓ Sucesso na tentativa " . ($index + 1) . "!</p>";
        break;
    } else {
        echo "<p style='color: red;'>✗ Falha na tentativa " . ($index + 1) . "</p>";
        if ($curl_error) {
            echo "<p>Erro cURL: $curl_error</p>";
        }
    }
    
    // Aguardar antes da próxima tentativa
    usleep(500000);
}

curl_close($ch);

if ($response !== false && $http_code === 200) {
    $categorias = json_decode($response, true);
    echo "<p style='color: green; font-weight: bold;'>✓ API funcionando! Encontradas " . count($categorias) . " categorias principais.</p>";
    
    echo "<h4>Primeiras 5 categorias:</h4>";
    echo "<ul>";
    for ($i = 0; $i < min(5, count($categorias)); $i++) {
        echo "<li><strong>" . $categorias[$i]['name'] . "</strong> (ID: " . $categorias[$i]['id'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ API não funcionou. Usando dados estáticos.</p>";
}

// Teste 2: Buscar atributos de uma categoria específica
echo "<h3>2. Teste de Busca de Atributos (MLB5726 - Celulares)</h3>";

$ml_id = "MLB5726";
$url_atributos = "https://api.mercadolibre.com/categories/{$ml_id}/attributes";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_atributos);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
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

$response_atributos = null;
$http_code_atributos = 0;
$curl_error_atributos = '';

foreach ($user_agents as $index => $user_agent) {
    echo "<p>Tentativa " . ($index + 1) . " para atributos com User-Agent: " . substr($user_agent, 0, 50) . "...</p>";
    
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    
    $response_atributos = curl_exec($ch);
    $http_code_atributos = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error_atributos = curl_error($ch);
    
    echo "<p>HTTP Code: $http_code_atributos</p>";
    
    if ($response_atributos !== false && $http_code_atributos === 200) {
        echo "<p style='color: green;'>✓ Sucesso na busca de atributos!</p>";
        break;
    } else {
        echo "<p style='color: red;'>✗ Falha na busca de atributos</p>";
        if ($curl_error_atributos) {
            echo "<p>Erro cURL: $curl_error_atributos</p>";
        }
    }
    
    usleep(500000);
}

curl_close($ch);

if ($response_atributos !== false && $http_code_atributos === 200) {
    $atributos = json_decode($response_atributos, true);
    echo "<p style='color: green; font-weight: bold;'>✓ Atributos encontrados! Total: " . count($atributos) . " atributos.</p>";
    
    // Filtrar atributos obrigatórios
    $atributos_obrigatorios = [];
    foreach ($atributos as $atributo) {
        if (isset($atributo['required']) && $atributo['required']) {
            $atributos_obrigatorios[] = $atributo;
        }
    }
    
    echo "<h4>Atributos Obrigatórios (" . count($atributos_obrigatorios) . "):</h4>";
    echo "<ul>";
    foreach ($atributos_obrigatorios as $atributo) {
        echo "<li><strong>" . $atributo['name'] . "</strong> (ID: " . $atributo['id'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ Não foi possível buscar atributos da API. Usando dados estáticos.</p>";
}

echo "<h3>3. Resumo</h3>";
echo "<ul>";
echo "<li><strong>Categorias:</strong> " . ($http_code === 200 ? "✓ Funcionando" : "✗ Bloqueado") . "</li>";
echo "<li><strong>Atributos:</strong> " . ($http_code_atributos === 200 ? "✓ Funcionando" : "✗ Bloqueado") . "</li>";
echo "</ul>";

if ($http_code === 200 && $http_code_atributos === 200) {
    echo "<p style='color: green; font-weight: bold;'>🎉 API do Mercado Livre funcionando perfeitamente!</p>";
    echo "<p>Agora você pode buscar categorias e atributos diretamente da API.</p>";
} else {
    echo "<p style='color: orange; font-weight: bold;'>⚠️ API bloqueada. O sistema usará dados estáticos como fallback.</p>";
    echo "<p>Isso não afeta o funcionamento do sistema, mas os dados podem não estar 100% atualizados.</p>";
}
?> 