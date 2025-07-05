<?php
/**
 * Teste de Rotas no LiteSpeed
 * Acesse este arquivo via navegador: https://acell.tec.br/teste_rotas_litespeed.php
 */

echo "<h1>Teste de Rotas no LiteSpeed</h1>";

// Verificar se o .htaccess está sendo processado
echo "<h2>1. Verificação do .htaccess</h2>";
$htaccess_file = __DIR__ . '/.htaccess';
if (file_exists($htaccess_file)) {
    echo "<p style='color: green;'>✓ Arquivo .htaccess encontrado</p>";
    
    $htaccess_content = file_get_contents($htaccess_file);
    
    if (strpos($htaccess_content, 'RewriteEngine On') !== false) {
        echo "<p style='color: green;'>✓ RewriteEngine está ativo</p>";
    } else {
        echo "<p style='color: red;'>✗ RewriteEngine não está ativo</p>";
    }
    
    if (strpos($htaccess_content, 'RewriteCond') !== false) {
        echo "<p style='color: green;'>✓ Regras RewriteCond encontradas</p>";
    } else {
        echo "<p style='color: red;'>✗ Regras RewriteCond não encontradas</p>";
    }
    
    if (strpos($htaccess_content, 'mercadolivre') !== false) {
        echo "<p style='color: green;'>✓ Regras específicas para mercadolivre encontradas</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Regras específicas para mercadolivre não encontradas</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo .htaccess não encontrado</p>";
}

// Verificar se o index.php está processando rotas manualmente
echo "<h2>2. Verificação do index.php</h2>";
$index_file = __DIR__ . '/index.php';
if (file_exists($index_file)) {
    echo "<p style='color: green;'>✓ Arquivo index.php encontrado</p>";
    
    $index_content = file_get_contents($index_file);
    
    if (strpos($index_content, 'mercadolivre') !== false) {
        echo "<p style='color: green;'>✓ Processamento manual de rotas para mercadolivre encontrado</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Processamento manual de rotas para mercadolivre não encontrado</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Arquivo index.php não encontrado</p>";
}

// Testar diferentes formatos de URL
echo "<h2>3. Teste de URLs</h2>";
$base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/');

echo "<p>Testando diferentes formatos de URL:</p>";
echo "<ul>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre/autenticar' target='_blank'>index.php/mercadolivre/autenticar</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/autenticar' target='_blank'>mercadolivre/autenticar</a></li>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre' target='_blank'>index.php/mercadolivre</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre' target='_blank'>mercadolivre</a></li>";
echo "</ul>";

// Verificar se o servidor é LiteSpeed
echo "<h2>4. Informações do Servidor</h2>";
echo "<p>Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Não identificado') . "</p>";
echo "<p>Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Não identificado') . "</p>";

if (strpos(strtolower($_SERVER['SERVER_SOFTWARE'] ?? ''), 'litespeed') !== false) {
    echo "<p style='color: orange;'>⚠ Servidor LiteSpeed detectado</p>";
    echo "<p>O LiteSpeed pode ter problemas com .htaccess. Verifique se as regras estão sendo aplicadas.</p>";
} else {
    echo "<p style='color: green;'>✓ Servidor não é LiteSpeed</p>";
}

// Verificar se mod_rewrite está ativo
echo "<h2>5. Verificação do mod_rewrite</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color: green;'>✓ mod_rewrite está ativo</p>";
    } else {
        echo "<p style='color: red;'>✗ mod_rewrite não está ativo</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Não foi possível verificar mod_rewrite</p>";
}

// Testar se o problema é específico do LiteSpeed
echo "<h2>6. Teste Específico para LiteSpeed</h2>";
echo "<p>Se você está usando LiteSpeed, tente estas soluções:</p>";
echo "<ol>";
echo "<li>Verifique se o .htaccess está sendo lido corretamente</li>";
echo "<li>Teste com regras mais simples no .htaccess</li>";
echo "<li>Verifique se há conflitos com outras configurações</li>";
echo "<li>Teste com o processamento manual de rotas no index.php</li>";
echo "</ol>";

// Verificar se há logs específicos do LiteSpeed
echo "<h2>7. Logs do Servidor</h2>";
echo "<p>Verifique os logs do LiteSpeed para erros específicos:</p>";
echo "<ul>";
echo "<li>Logs de erro do Apache/LiteSpeed</li>";
echo "<li>Logs de acesso</li>";
echo "<li>Logs de rewrite</li>";
echo "</ul>";

// Criar um teste de URL simples
echo "<h2>8. Teste de URL Simples</h2>";
echo "<p>Teste esta URL diretamente no navegador:</p>";
echo "<code>https://acell.tec.br/index.php/mercadolivre/autenticar</code>";
echo "<br><br>";
echo "<a href='https://acell.tec.br/index.php/mercadolivre/autenticar' target='_blank' class='btn btn-primary'>Testar URL</a>";

echo "<h2>9. Solução Alternativa</h2>";
echo "<p>Se o problema persistir, podemos:</p>";
echo "<ol>";
echo "<li>Desativar temporariamente o CSRF para testar</li>";
echo "<li>Criar uma rota alternativa</li>";
echo "<li>Usar um controller diferente</li>";
echo "<li>Verificar se há conflitos com outras configurações</li>";
echo "</ol>";

echo "<p><strong>Nota:</strong> Após confirmar que funciona, delete este arquivo por segurança.</p>";
?> 