<?php
/**
 * Teste Final MercadoLivre
 * Acesse este arquivo via navegador: https://acell.tec.br/teste_final_mercadolivre.php
 */

echo "<h1>Teste Final - MercadoLivre</h1>";

// Verificar todas as correções aplicadas
echo "<h2>1. Verificação das Correções</h2>";

// Verificar controller
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

// Verificar CSRF
$config_file = __DIR__ . '/application/config/config.php';
if (file_exists($config_file)) {
    $config_content = file_get_contents($config_file);
    
    if (strpos($config_content, "'csrf_protection' => false") !== false) {
        echo "<p style='color: green;'>✓ CSRF está desativado</p>";
    } else {
        echo "<p style='color: orange;'>⚠ CSRF pode estar ativo</p>";
    }
    
    if (strpos($config_content, "'mercadolivre.*+'") !== false) {
        echo "<p style='color: green;'>✓ URLs do Mercado Livre estão excluídas do CSRF</p>";
    } else {
        echo "<p style='color: orange;'>⚠ URLs do Mercado Livre não estão excluídas do CSRF</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo config.php não encontrado</p>";
}

// Verificar rotas
$routes_file = __DIR__ . '/application/config/routes.php';
if (file_exists($routes_file)) {
    $routes_content = file_get_contents($routes_file);
    
    if (strpos($routes_content, 'mercadolivre/autenticar') !== false) {
        echo "<p style='color: green;'>✓ Rota mercadolivre/autenticar configurada</p>";
    } else {
        echo "<p style='color: red;'>✗ Rota mercadolivre/autenticar não configurada</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo routes.php não encontrado</p>";
}

// Verificar .htaccess
$htaccess_file = __DIR__ . '/.htaccess';
if (file_exists($htaccess_file)) {
    echo "<p style='color: green;'>✓ Arquivo .htaccess encontrado</p>";
} else {
    echo "<p style='color: red;'>✗ Arquivo .htaccess não encontrado</p>";
}

// Verificar index.php
$index_file = __DIR__ . '/index.php';
if (file_exists($index_file)) {
    $index_content = file_get_contents($index_file);
    
    if (strpos($index_content, 'mercadolivre') !== false) {
        echo "<p style='color: green;'>✓ Processamento manual de rotas para mercadolivre</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Processamento manual de rotas para mercadolivre não encontrado</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo index.php não encontrado</p>";
}

// Testar URLs
echo "<h2>2. Teste de URLs</h2>";
$base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/');

echo "<p>Testando URLs do Mercado Livre:</p>";
echo "<ul>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre/autenticar' target='_blank'>index.php/mercadolivre/autenticar</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/autenticar' target='_blank'>mercadolivre/autenticar</a></li>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre/callback' target='_blank'>index.php/mercadolivre/callback</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/callback' target='_blank'>mercadolivre/callback</a></li>";
echo "</ul>";

// Verificar configurações do ambiente
echo "<h2>3. Configurações do Ambiente</h2>";
$env_file = __DIR__ . '/application/.env';
if (file_exists($env_file)) {
    $env_content = file_get_contents($env_file);
    
    if (strpos($env_content, 'MERCADO_LIVRE_CLIENT_ID') !== false) {
        echo "<p style='color: green;'>✓ MERCADO_LIVRE_CLIENT_ID configurado</p>";
    } else {
        echo "<p style='color: red;'>✗ MERCADO_LIVRE_CLIENT_ID não configurado</p>";
    }
    
    if (strpos($env_content, 'MERCADO_LIVRE_CLIENT_SECRET') !== false) {
        echo "<p style='color: green;'>✓ MERCADO_LIVRE_CLIENT_SECRET configurado</p>";
    } else {
        echo "<p style='color: red;'>✗ MERCADO_LIVRE_CLIENT_SECRET não configurado</p>";
    }
    
    if (strpos($env_content, 'MERCADO_LIVRE_REDIRECT_URI') !== false) {
        echo "<p style='color: green;'>✓ MERCADO_LIVRE_REDIRECT_URI configurado</p>";
    } else {
        echo "<p style='color: orange;'>⚠ MERCADO_LIVRE_REDIRECT_URI não configurado (usando padrão)</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo .env não encontrado</p>";
}

// Resumo das correções aplicadas
echo "<h2>4. Correções Aplicadas</h2>";
echo "<ul>";
echo "<li>✓ Herança do controller alterada de MY_Controller para CI_Controller</li>";
echo "<li>✓ Verificação de sessão removida do construtor</li>";
echo "<li>✓ CSRF desativado temporariamente</li>";
echo "<li>✓ URLs do Mercado Livre excluídas do CSRF</li>";
echo "<li>✓ Processamento manual de rotas no index.php</li>";
echo "<li>✓ Configurações básicas adicionadas ao controller</li>";
echo "</ul>";

// Próximos passos
echo "<h2>5. Próximos Passos</h2>";
echo "<ol>";
echo "<li>Teste as URLs acima</li>";
echo "<li>Se funcionarem, teste a autenticação completa</li>";
echo "<li>Verifique se o callback está funcionando</li>";
echo "<li>Teste a integração completa com o Mercado Livre</li>";
echo "<li>Se tudo funcionar, reative o CSRF com as exclusões</li>";
echo "</ol>";

// Teste específico
echo "<h2>6. Teste Específico</h2>";
echo "<p>Clique no link abaixo para testar a autenticação:</p>";
echo "<a href='http://{$base_url}/index.php/mercadolivre/autenticar' target='_blank' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Testar Autenticação Mercado Livre</a>";

echo "<h2>7. Se ainda não funcionar</h2>";
echo "<p>Se o erro 404 persistir, verifique:</p>";
echo "<ul>";
echo "<li>Logs do servidor (Apache/LiteSpeed)</li>";
echo "<li>Logs do CodeIgniter em application/logs/</li>";
echo "<li>Se há conflitos com outras configurações</li>";
echo "<li>Se o servidor está processando o .htaccess corretamente</li>";
echo "</ul>";

echo "<p><strong>Nota:</strong> Após confirmar que funciona, delete este arquivo por segurança.</p>";
?> 