<?php
/**
 * Script de teste para verificar se as rotas do Mercado Livre estão funcionando
 * Acesse este arquivo via navegador: http://seudominio.com/teste_rotas.php
 */

echo "<h1>Teste de Rotas - Mercado Livre</h1>";

// Verificar se o mod_rewrite está ativo
echo "<h2>1. Verificação do mod_rewrite</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color: green;'>✓ mod_rewrite está ativo</p>";
    } else {
        echo "<p style='color: red;'>✗ mod_rewrite não está ativo</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Não foi possível verificar o mod_rewrite (função não disponível)</p>";
}

// Verificar se o .htaccess está sendo lido
echo "<h2>2. Verificação do .htaccess</h2>";
$htaccess_path = __DIR__ . '/.htaccess';
if (file_exists($htaccess_path)) {
    echo "<p style='color: green;'>✓ Arquivo .htaccess encontrado</p>";
    $htaccess_content = file_get_contents($htaccess_path);
    if (strpos($htaccess_content, 'RewriteEngine On') !== false) {
        echo "<p style='color: green;'>✓ RewriteEngine está configurado</p>";
    } else {
        echo "<p style='color: red;'>✗ RewriteEngine não encontrado no .htaccess</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo .htaccess não encontrado</p>";
}

// Verificar configurações do ambiente
echo "<h2>3. Verificação das configurações do ambiente</h2>";
$env_file = __DIR__ . '/application/.env';
if (file_exists($env_file)) {
    echo "<p style='color: green;'>✓ Arquivo .env encontrado</p>";
    
    // Carregar variáveis do .env
    $env_content = file_get_contents($env_file);
    $env_lines = explode("\n", $env_content);
    $env_vars = [];
    
    foreach ($env_lines as $line) {
        $line = trim($line);
        if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            $parts = explode('=', $line, 2);
            $env_vars[$parts[0]] = $parts[1] ?? '';
        }
    }
    
    // Verificar configurações do Mercado Livre
    echo "<h3>Configurações do Mercado Livre:</h3>";
    echo "<ul>";
    
    if (isset($env_vars['MERCADO_LIVRE_ENABLED'])) {
        echo "<li>MERCADO_LIVRE_ENABLED: " . $env_vars['MERCADO_LIVRE_ENABLED'] . "</li>";
    } else {
        echo "<li style='color: red;'>MERCADO_LIVRE_ENABLED: Não configurado</li>";
    }
    
    if (isset($env_vars['MERCADO_LIVRE_CLIENT_ID'])) {
        echo "<li>MERCADO_LIVRE_CLIENT_ID: " . (strlen($env_vars['MERCADO_LIVRE_CLIENT_ID']) > 0 ? 'Configurado' : 'Vazio') . "</li>";
    } else {
        echo "<li style='color: red;'>MERCADO_LIVRE_CLIENT_ID: Não configurado</li>";
    }
    
    if (isset($env_vars['MERCADO_LIVRE_CLIENT_SECRET'])) {
        echo "<li>MERCADO_LIVRE_CLIENT_SECRET: " . (strlen($env_vars['MERCADO_LIVRE_CLIENT_SECRET']) > 0 ? 'Configurado' : 'Vazio') . "</li>";
    } else {
        echo "<li style='color: red;'>MERCADO_LIVRE_CLIENT_SECRET: Não configurado</li>";
    }
    
    if (isset($env_vars['APP_BASEURL'])) {
        echo "<li>APP_BASEURL: " . $env_vars['APP_BASEURL'] . "</li>";
    } else {
        echo "<li style='color: red;'>APP_BASEURL: Não configurado</li>";
    }
    
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ Arquivo .env não encontrado</p>";
}

// Testar URLs
echo "<h2>4. Teste de URLs</h2>";
$base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/');

echo "<p>URLs para testar:</p>";
echo "<ul>";
echo "<li><a href='http://{$base_url}/mercadolivre' target='_blank'>MercadoLivre Index</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/autenticar' target='_blank'>MercadoLivre Autenticar</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/callback' target='_blank'>MercadoLivre Callback</a></li>";
echo "</ul>";

// Verificar se o controller existe
echo "<h2>5. Verificação do Controller</h2>";
$controller_file = __DIR__ . '/application/controllers/MercadoLivre.php';
if (file_exists($controller_file)) {
    echo "<p style='color: green;'>✓ Controller MercadoLivre.php encontrado</p>";
    
    $controller_content = file_get_contents($controller_file);
    if (strpos($controller_content, 'public function autenticar()') !== false) {
        echo "<p style='color: green;'>✓ Método autenticar() encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Método autenticar() não encontrado</p>";
    }
    
    if (strpos($controller_content, 'public function callback()') !== false) {
        echo "<p style='color: green;'>✓ Método callback() encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Método callback() não encontrado</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Controller MercadoLivre.php não encontrado</p>";
}

// Verificar rotas
echo "<h2>6. Verificação das Rotas</h2>";
$routes_file = __DIR__ . '/application/config/routes.php';
if (file_exists($routes_file)) {
    echo "<p style='color: green;'>✓ Arquivo routes.php encontrado</p>";
    
    $routes_content = file_get_contents($routes_file);
    if (strpos($routes_content, 'mercadolivre/autenticar') !== false) {
        echo "<p style='color: green;'>✓ Rota mercadolivre/autenticar configurada</p>";
    } else {
        echo "<p style='color: red;'>✗ Rota mercadolivre/autenticar não configurada</p>";
    }
    
    if (strpos($routes_content, 'mercadolivre/callback') !== false) {
        echo "<p style='color: green;'>✓ Rota mercadolivre/callback configurada</p>";
    } else {
        echo "<p style='color: red;'>✗ Rota mercadolivre/callback não configurada</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo routes.php não encontrado</p>";
}

echo "<h2>7. Informações do Servidor</h2>";
echo "<ul>";
echo "<li>PHP Version: " . PHP_VERSION . "</li>";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</li>";
echo "<li>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</li>";
echo "<li>Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "</li>";
echo "<li>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</li>";
echo "</ul>";

echo "<h2>8. Recomendações</h2>";
echo "<ol>";
echo "<li>Certifique-se de que o mod_rewrite está habilitado no servidor</li>";
echo "<li>Verifique se o arquivo .htaccess está sendo lido pelo servidor</li>";
echo "<li>Configure corretamente a URL de redirecionamento no aplicativo do Mercado Livre</li>";
echo "<li>A URL de redirecionamento deve ser: http://seudominio.com/mercadolivre/callback</li>";
echo "<li>Verifique se o APP_BASEURL no arquivo .env está correto</li>";
echo "</ol>";

echo "<p><strong>Nota:</strong> Após fazer as correções, delete este arquivo por segurança.</p>";
?> 