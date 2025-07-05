<?php
/**
 * Teste específico para LiteSpeed
 * Acesse este arquivo via navegador: https://acell.tec.br/teste_litespeed.php
 */

echo "<h1>Teste Específico para LiteSpeed</h1>";

// Verificar informações do servidor
echo "<h2>1. Informações do Servidor</h2>";
echo "<ul>";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</li>";
echo "<li>PHP Version: " . PHP_VERSION . "</li>";
echo "<li>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</li>";
echo "<li>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</li>";
echo "<li>Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "</li>";
echo "</ul>";

// Verificar se o .htaccess está sendo lido
echo "<h2>2. Verificação do .htaccess</h2>";
$htaccess_file = __DIR__ . '/.htaccess';
if (file_exists($htaccess_file)) {
    echo "<p style='color: green;'>✓ Arquivo .htaccess encontrado</p>";
    
    $htaccess_content = file_get_contents($htaccess_file);
    if (strpos($htaccess_content, 'RewriteEngine On') !== false) {
        echo "<p style='color: green;'>✓ RewriteEngine configurado</p>";
    } else {
        echo "<p style='color: red;'>✗ RewriteEngine não encontrado</p>";
    }
    
    if (strpos($htaccess_content, 'LiteSpeed') !== false) {
        echo "<p style='color: green;'>✓ Regras específicas para LiteSpeed encontradas</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Regras específicas para LiteSpeed não encontradas</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo .htaccess não encontrado</p>";
}

// Testar se o mod_rewrite está funcionando
echo "<h2>3. Teste do mod_rewrite</h2>";
$test_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$test_url = rtrim($test_url, '/');

echo "<p>Testando URLs:</p>";
echo "<ul>";
echo "<li><a href='http://{$test_url}/index.php/mapos' target='_blank'>Com index.php</a></li>";
echo "<li><a href='http://{$test_url}/mapos' target='_blank'>Sem index.php</a></li>";
echo "<li><a href='http://{$test_url}/mercadolivre' target='_blank'>MercadoLivre</a></li>";
echo "<li><a href='http://{$test_url}/mercadolivre/autenticar' target='_blank'>MercadoLivre Autenticar</a></li>";
echo "</ul>";

// Verificar configurações do CodeIgniter
echo "<h2>4. Verificação do CodeIgniter</h2>";
$index_file = __DIR__ . '/index.php';
if (file_exists($index_file)) {
    echo "<p style='color: green;'>✓ index.php encontrado</p>";
    
    $index_content = file_get_contents($index_file);
    if (strpos($index_content, 'ENVIRONMENT') !== false) {
        echo "<p style='color: green;'>✓ Configuração de ambiente encontrada</p>";
    } else {
        echo "<p style='color: red;'>✗ Configuração de ambiente não encontrada</p>";
    }
} else {
    echo "<p style='color: red;'>✗ index.php não encontrado</p>";
}

// Verificar rotas
echo "<h2>5. Verificação das Rotas</h2>";
$routes_file = __DIR__ . '/application/config/routes.php';
if (file_exists($routes_file)) {
    echo "<p style='color: green;'>✓ routes.php encontrado</p>";
    
    $routes_content = file_get_contents($routes_file);
    if (strpos($routes_content, 'mercadolivre/autenticar') !== false) {
        echo "<p style='color: green;'>✓ Rota mercadolivre/autenticar configurada</p>";
    } else {
        echo "<p style='color: red;'>✗ Rota mercadolivre/autenticar não configurada</p>";
    }
} else {
    echo "<p style='color: red;'>✗ routes.php não encontrado</p>";
}

// Verificar controller
echo "<h2>6. Verificação do Controller</h2>";
$controller_file = __DIR__ . '/application/controllers/MercadoLivre.php';
if (file_exists($controller_file)) {
    echo "<p style='color: green;'>✓ Controller MercadoLivre.php encontrado</p>";
    
    $controller_content = file_get_contents($controller_file);
    if (strpos($controller_content, 'public function autenticar()') !== false) {
        echo "<p style='color: green;'>✓ Método autenticar() encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Método autenticar() não encontrado</p>";
    }
    
    // Verificar se a verificação de permissão foi removida do construtor
    if (strpos($controller_content, 'checkPermission($this->session->userdata(\'permissao\'), \'aProduto\')') !== false) {
        if (strpos($controller_content, 'if ($this->session->userdata(\'logado\'))') !== false) {
            echo "<p style='color: green;'>✓ Verificação de permissão corrigida</p>";
        } else {
            echo "<p style='color: red;'>✗ Verificação de permissão ainda no construtor</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Verificação de permissão não encontrada</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Controller MercadoLivre.php não encontrado</p>";
}

// Teste de URL direta
echo "<h2>7. Teste de URL Direta</h2>";
$base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/');

echo "<p>Testando URLs com diferentes formatos:</p>";
echo "<ul>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre/autenticar' target='_blank'>Com index.php</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/autenticar' target='_blank'>Sem index.php</a></li>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre/callback' target='_blank'>Callback com index.php</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/callback' target='_blank'>Callback sem index.php</a></li>";
echo "</ul>";

echo "<h2>8. Recomendações para LiteSpeed</h2>";
echo "<ol>";
echo "<li>Verifique se o mod_rewrite está habilitado no LiteSpeed</li>";
echo "<li>Configure o AllowOverride como 'All' no LiteSpeed</li>";
echo "<li>Verifique se o arquivo .htaccess está sendo lido</li>";
echo "<li>Teste com as URLs que incluem index.php</li>";
echo "<li>Verifique os logs do LiteSpeed para erros</li>";
echo "</ol>";

echo "<h2>9. Logs para Verificar</h2>";
echo "<ul>";
echo "<li>LiteSpeed Error Log: /usr/local/lsws/logs/error.log</li>";
echo "<li>LiteSpeed Access Log: /usr/local/lsws/logs/access.log</li>";
echo "<li>PHP Error Log: /usr/local/lsws/logs/php_error.log</li>";
echo "</ul>";

echo "<p><strong>Nota:</strong> Após fazer as correções, delete este arquivo por segurança.</p>";
?> 