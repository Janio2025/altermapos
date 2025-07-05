<?php
/**
 * Teste do Controller Corrigido
 * Acesse este arquivo via navegador: https://acell.tec.br/teste_controller_corrigido.php
 */

echo "<h1>Teste do Controller MercadoLivre Corrigido</h1>";

// Verificar se o controller foi corrigido
$controller_file = __DIR__ . '/application/controllers/MercadoLivre.php';
if (file_exists($controller_file)) {
    echo "<p style='color: green;'>✓ Controller MercadoLivre.php encontrado</p>";
    
    $controller_content = file_get_contents($controller_file);
    
    // Verificar se a herança foi corrigida
    if (strpos($controller_content, 'class MercadoLivre extends CI_Controller') !== false) {
        echo "<p style='color: green;'>✓ Herança corrigida para CI_Controller</p>";
    } else {
        echo "<p style='color: red;'>✗ Herança ainda está MY_Controller</p>";
    }
    
    // Verificar se o método autenticar existe
    if (strpos($controller_content, 'public function autenticar()') !== false) {
        echo "<p style='color: green;'>✓ Método autenticar() encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Método autenticar() não encontrado</p>";
    }
    
    // Verificar se o método callback existe
    if (strpos($controller_content, 'public function callback()') !== false) {
        echo "<p style='color: green;'>✓ Método callback() encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Método callback() não encontrado</p>";
    }
    
    // Verificar se a verificação de permissão foi corrigida
    if (strpos($controller_content, 'if ($this->session->userdata(\'logado\'))') !== false) {
        echo "<p style='color: green;'>✓ Verificação de permissão corrigida</p>";
    } else {
        echo "<p style='color: red;'>✗ Verificação de permissão não corrigida</p>";
    }
    
    // Verificar se não há verificação de sessão no construtor
    if (strpos($controller_content, 'redirect(\'login\')') === false) {
        echo "<p style='color: green;'>✓ Sem redirecionamento forçado para login</p>";
    } else {
        echo "<p style='color: red;'>✗ Ainda há redirecionamento forçado para login</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Controller MercadoLivre.php não encontrado</p>";
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

// Verificar se o debug anterior funcionou
echo "<h2>3. Teste do Debug Anterior</h2>";
echo "<p><a href='http://{$base_url}/debug_mercadolivre.php' target='_blank'>Executar Debug Completo</a></p>";

echo "<h2>4. O que foi corrigido</h2>";
echo "<ul>";
echo "<li>✓ Herança alterada de MY_Controller para CI_Controller</li>";
echo "<li>✓ Removida verificação de sessão forçada no construtor</li>";
echo "<li>✓ Adicionadas configurações básicas no construtor</li>";
echo "<li>✓ Adicionado método layout()</li>";
echo "<li>✓ Mantida verificação de permissão apenas quando necessário</li>";
echo "</ul>";

echo "<h2>5. Próximos Passos</h2>";
echo "<ol>";
echo "<li>Teste as URLs acima</li>";
echo "<li>Se funcionarem, teste a autenticação completa</li>";
echo "<li>Verifique se o callback está funcionando</li>";
echo "<li>Teste a integração completa com o Mercado Livre</li>";
echo "</ol>";

echo "<p><strong>Nota:</strong> Após confirmar que funciona, delete este arquivo por segurança.</p>";
?> 