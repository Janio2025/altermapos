<?php
/**
 * Debug específico para mercadolivre/autenticar
 * Acesse este arquivo via navegador: https://acell.tec.br/debug_mercadolivre.php
 */

echo "<h1>Debug MercadoLivre - Autenticar</h1>";

// Simular o que o CodeIgniter faria
echo "<h2>1. Simulação do CodeIgniter</h2>";

// Verificar se o controller existe
$controller_file = __DIR__ . '/application/controllers/MercadoLivre.php';
if (file_exists($controller_file)) {
    echo "<p style='color: green;'>✓ Controller MercadoLivre.php encontrado</p>";
    
    // Verificar se a classe pode ser carregada
    require_once $controller_file;
    
    if (class_exists('MercadoLivre')) {
        echo "<p style='color: green;'>✓ Classe MercadoLivre pode ser carregada</p>";
        
        // Verificar se o método existe
        $reflection = new ReflectionClass('MercadoLivre');
        if ($reflection->hasMethod('autenticar')) {
            echo "<p style='color: green;'>✓ Método autenticar() existe</p>";
            
            $method = $reflection->getMethod('autenticar');
            if ($method->isPublic()) {
                echo "<p style='color: green;'>✓ Método autenticar() é público</p>";
            } else {
                echo "<p style='color: red;'>✗ Método autenticar() não é público</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Método autenticar() não existe</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Classe MercadoLivre não pode ser carregada</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Controller MercadoLivre.php não encontrado</p>";
}

// Verificar configurações do ambiente
echo "<h2>2. Configurações do Ambiente</h2>";
$env_file = __DIR__ . '/application/.env';
if (file_exists($env_file)) {
    echo "<p style='color: green;'>✓ Arquivo .env encontrado</p>";
    
    $env_content = file_get_contents($env_file);
    if (strpos($env_content, 'MERCADO_LIVRE_CLIENT_ID') !== false) {
        echo "<p style='color: green;'>✓ MERCADO_LIVRE_CLIENT_ID configurado</p>";
    } else {
        echo "<p style='color: red;'>✗ MERCADO_LIVRE_CLIENT_ID não configurado</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo .env não encontrado</p>";
}

// Verificar rotas
echo "<h2>3. Verificação das Rotas</h2>";
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

// Testar URLs específicas
echo "<h2>4. Teste de URLs</h2>";
$base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/');

echo "<p>Testando diferentes formatos de URL:</p>";
echo "<ul>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre/autenticar' target='_blank'>index.php/mercadolivre/autenticar</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/autenticar' target='_blank'>mercadolivre/autenticar</a></li>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre' target='_blank'>index.php/mercadolivre</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre' target='_blank'>mercadolivre</a></li>";
echo "</ul>";

// Verificar se há erros de sintaxe no controller
echo "<h2>5. Verificação de Sintaxe</h2>";
if (file_exists($controller_file)) {
    $controller_content = file_get_contents($controller_file);
    
    // Verificar se há erros de sintaxe básicos
    if (strpos($controller_content, '<?php') !== false) {
        echo "<p style='color: green;'>✓ Tag PHP encontrada</p>";
    } else {
        echo "<p style='color: red;'>✗ Tag PHP não encontrada</p>";
    }
    
    if (strpos($controller_content, 'class MercadoLivre') !== false) {
        echo "<p style='color: green;'>✓ Declaração da classe encontrada</p>";
    } else {
        echo "<p style='color: red;'>✗ Declaração da classe não encontrada</p>";
    }
    
    if (strpos($controller_content, 'public function autenticar()') !== false) {
        echo "<p style='color: green;'>✓ Método autenticar() encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Método autenticar() não encontrado</p>";
    }
    
    // Verificar se há chaves desbalanceadas
    $open_braces = substr_count($controller_content, '{');
    $close_braces = substr_count($controller_content, '}');
    
    if ($open_braces === $close_braces) {
        echo "<p style='color: green;'>✓ Chaves balanceadas ({$open_braces} abertas, {$close_braces} fechadas)</p>";
    } else {
        echo "<p style='color: red;'>✗ Chaves desbalanceadas ({$open_braces} abertas, {$close_braces} fechadas)</p>";
    }
}

// Verificar configurações do CodeIgniter
echo "<h2>6. Configurações do CodeIgniter</h2>";
$config_file = __DIR__ . '/application/config/config.php';
if (file_exists($config_file)) {
    echo "<p style='color: green;'>✓ Arquivo config.php encontrado</p>";
    
    $config_content = file_get_contents($config_file);
    if (strpos($config_content, 'uri_protocol') !== false) {
        echo "<p style='color: green;'>✓ Configuração uri_protocol encontrada</p>";
    } else {
        echo "<p style='color: red;'>✗ Configuração uri_protocol não encontrada</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo config.php não encontrado</p>";
}

echo "<h2>7. Recomendações</h2>";
echo "<ol>";
echo "<li>Verifique se não há erros de sintaxe no controller</li>";
echo "<li>Teste a URL com e sem index.php</li>";
echo "<li>Verifique os logs do servidor para erros específicos</li>";
echo "<li>Confirme se o controller está sendo carregado corretamente</li>";
echo "</ol>";

echo "<p><strong>Nota:</strong> Após fazer as correções, delete este arquivo por segurança.</p>";
?> 