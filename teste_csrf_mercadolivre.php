<?php
/**
 * Teste CSRF MercadoLivre
 * Acesse este arquivo via navegador: https://acell.tec.br/teste_csrf_mercadolivre.php
 */

echo "<h1>Teste CSRF MercadoLivre</h1>";

// Verificar configurações CSRF
echo "<h2>1. Configurações CSRF</h2>";

$config_file = __DIR__ . '/application/config/config.php';
if (file_exists($config_file)) {
    $config_content = file_get_contents($config_file);
    
    // Verificar se CSRF está ativo
    if (strpos($config_content, "'csrf_protection' => true") !== false) {
        echo "<p style='color: orange;'>⚠ CSRF está ativo</p>";
    } else {
        echo "<p style='color: green;'>✓ CSRF está desativado</p>";
    }
    
    // Verificar exclusões CSRF
    if (strpos($config_content, "'mercadolivre.*+'") !== false) {
        echo "<p style='color: green;'>✓ URLs do Mercado Livre estão excluídas do CSRF</p>";
    } else {
        echo "<p style='color: red;'>✗ URLs do Mercado Livre NÃO estão excluídas do CSRF</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo config.php não encontrado</p>";
}

// Verificar se o .env tem configurações CSRF
echo "<h2>2. Configurações do .env</h2>";
$env_file = __DIR__ . '/application/.env';
if (file_exists($env_file)) {
    $env_content = file_get_contents($env_file);
    
    if (strpos($env_content, 'APP_CSRF_PROTECTION') !== false) {
        echo "<p style='color: green;'>✓ APP_CSRF_PROTECTION configurado no .env</p>";
    } else {
        echo "<p style='color: orange;'>⚠ APP_CSRF_PROTECTION não configurado no .env (usando padrão)</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo .env não encontrado</p>";
}

// Testar URLs com diferentes métodos
echo "<h2>3. Teste de URLs</h2>";
$base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/');

echo "<p>Testando diferentes métodos de acesso:</p>";
echo "<ul>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre/autenticar' target='_blank'>GET - index.php/mercadolivre/autenticar</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/autenticar' target='_blank'>GET - mercadolivre/autenticar</a></li>";
echo "</ul>";

// Simular POST para testar CSRF
echo "<h2>4. Teste POST (Simulação)</h2>";
echo "<form method='post' action='http://{$base_url}/mercadolivre/autenticar' target='_blank'>";
echo "<input type='hidden' name='test' value='1'>";
echo "<button type='submit'>Testar POST para mercadolivre/autenticar</button>";
echo "</form>";

// Verificar se há tokens CSRF sendo gerados
echo "<h2>5. Verificação de Tokens CSRF</h2>";

// Carregar o CodeIgniter para testar
if (file_exists(__DIR__ . '/application/config/config.php')) {
    // Simular carregamento do CI
    define('BASEPATH', true);
    require_once __DIR__ . '/application/config/config.php';
    
    if (isset($config['csrf_protection']) && $config['csrf_protection']) {
        echo "<p style='color: orange;'>⚠ CSRF Protection está ativo</p>";
        
        if (isset($config['csrf_exclude_uris'])) {
            echo "<p>URLs excluídas do CSRF:</p>";
            echo "<ul>";
            foreach ($config['csrf_exclude_uris'] as $uri) {
                echo "<li>{$uri}</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: green;'>✓ CSRF Protection está desativado</p>";
    }
}

// Verificar se o controller está carregando corretamente
echo "<h2>6. Teste do Controller</h2>";
$controller_file = __DIR__ . '/application/controllers/MercadoLivre.php';
if (file_exists($controller_file)) {
    $controller_content = file_get_contents($controller_file);
    
    if (strpos($controller_content, 'class MercadoLivre extends CI_Controller') !== false) {
        echo "<p style='color: green;'>✓ Controller herda de CI_Controller</p>";
    } else {
        echo "<p style='color: red;'>✗ Controller não herda de CI_Controller</p>";
    }
    
    if (strpos($controller_content, 'public function autenticar()') !== false) {
        echo "<p style='color: green;'>✓ Método autenticar() existe</p>";
    } else {
        echo "<p style='color: red;'>✗ Método autenticar() não existe</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Controller não encontrado</p>";
}

// Verificar logs de erro
echo "<h2>7. Verificação de Logs</h2>";
$log_file = __DIR__ . '/application/logs/log-' . date('Y-m-d') . '.php';
if (file_exists($log_file)) {
    echo "<p style='color: green;'>✓ Arquivo de log encontrado</p>";
    echo "<p><a href='http://{$base_url}/application/logs/log-" . date('Y-m-d') . ".php' target='_blank'>Ver log de hoje</a></p>";
} else {
    echo "<p style='color: orange;'>⚠ Arquivo de log não encontrado</p>";
}

echo "<h2>8. Recomendações</h2>";
echo "<ol>";
echo "<li>Teste as URLs acima</li>";
echo "<li>Se ainda der erro 404, verifique os logs do servidor</li>";
echo "<li>Confirme se o CSRF está sendo aplicado corretamente</li>";
echo "<li>Teste com e sem index.php na URL</li>";
echo "</ol>";

echo "<h2>9. Debug Completo</h2>";
echo "<p><a href='http://{$base_url}/debug_mercadolivre.php' target='_blank'>Executar Debug Completo</a></p>";

echo "<p><strong>Nota:</strong> Após confirmar que funciona, delete este arquivo por segurança.</p>";
?> 