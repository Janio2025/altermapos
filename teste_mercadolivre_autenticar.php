<?php
/**
 * Teste específico para o método autenticar do Mercado Livre
 * Acesse este arquivo via navegador: http://seudominio.com/teste_mercadolivre_autenticar.php
 */

echo "<h1>Teste do Método Autenticar - Mercado Livre</h1>";

// Verificar se o controller existe
$controller_file = __DIR__ . '/application/controllers/MercadoLivre.php';
if (file_exists($controller_file)) {
    echo "<p style='color: green;'>✓ Controller MercadoLivre.php encontrado</p>";
    
    $controller_content = file_get_contents($controller_file);
    
    // Verificar se o método autenticar existe
    if (strpos($controller_content, 'public function autenticar()') !== false) {
        echo "<p style='color: green;'>✓ Método autenticar() encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Método autenticar() não encontrado</p>";
    }
    
    // Verificar se a verificação de permissão foi removida do construtor
    if (strpos($controller_content, 'checkPermission($this->session->userdata(\'permissao\'), \'aProduto\')') !== false) {
        echo "<p style='color: orange;'>⚠ Verificação de permissão encontrada no código</p>";
    } else {
        echo "<p style='color: green;'>✓ Verificação de permissão removida do construtor</p>";
    }
    
    // Verificar se existe verificação de permissão no método autenticar
    if (strpos($controller_content, 'if ($this->session->userdata(\'logado\'))') !== false) {
        echo "<p style='color: green;'>✓ Verificação de permissão adicionada no método autenticar</p>";
    } else {
        echo "<p style='color: red;'>✗ Verificação de permissão não encontrada no método autenticar</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Controller MercadoLivre.php não encontrado</p>";
}

// Verificar arquivo .env
$env_file = __DIR__ . '/application/.env';
if (file_exists($env_file)) {
    echo "<p style='color: green;'>✓ Arquivo .env encontrado</p>";
    
    $env_content = file_get_contents($env_file);
    
    // Verificar configurações do ML
    $configs = [
        'MERCADO_LIVRE_ENABLED' => 'Integração habilitada',
        'MERCADO_LIVRE_CLIENT_ID' => 'CLIENT_ID configurado',
        'MERCADO_LIVRE_CLIENT_SECRET' => 'CLIENT_SECRET configurado',
        'MERCADO_LIVRE_REDIRECT_URI' => 'URL de redirecionamento'
    ];
    
    echo "<h3>Configurações do Mercado Livre:</h3>";
    foreach ($configs as $config => $desc) {
        if (strpos($env_content, $config) !== false) {
            echo "<p style='color: green;'>✓ {$desc}</p>";
        } else {
            echo "<p style='color: red;'>✗ {$desc} não encontrado</p>";
        }
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo .env não encontrado</p>";
}

// Verificar rotas
$routes_file = __DIR__ . '/application/config/routes.php';
if (file_exists($routes_file)) {
    echo "<p style='color: green;'>✓ Arquivo routes.php encontrado</p>";
    
    $routes_content = file_get_contents($routes_file);
    if (strpos($routes_content, 'mercadolivre/autenticar') !== false) {
        echo "<p style='color: green;'>✓ Rota mercadolivre/autenticar configurada</p>";
    } else {
        echo "<p style='color: red;'>✗ Rota mercadolivre/autenticar não configurada</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo routes.php não encontrado</p>";
}

// Testar URL
$base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/');

echo "<h3>URLs para testar:</h3>";
echo "<ul>";
echo "<li><a href='http://{$base_url}/mercadolivre/autenticar' target='_blank'>Testar Autenticação</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/callback' target='_blank'>Testar Callback</a></li>";
echo "</ul>";

echo "<h3>Informações do Servidor:</h3>";
echo "<ul>";
echo "<li>PHP Version: " . PHP_VERSION . "</li>";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</li>";
echo "<li>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</li>";
echo "<li>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</li>";
echo "</ul>";

echo "<h3>Recomendações:</h3>";
echo "<ol>";
echo "<li>Certifique-se de que o mod_rewrite está habilitado</li>";
echo "<li>Verifique se o arquivo .htaccess está sendo lido</li>";
echo "<li>Teste a URL de autenticação diretamente</li>";
echo "<li>Verifique os logs do servidor para erros</li>";
echo "</ol>";

echo "<p><strong>Nota:</strong> Após fazer as correções, delete este arquivo por segurança.</p>";
?> 