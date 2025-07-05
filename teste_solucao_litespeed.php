<?php
/**
 * Teste da solução manual de rotas para LiteSpeed
 * Acesse este arquivo via navegador: https://acell.tec.br/teste_solucao_litespeed.php
 */

echo "<h1>Teste da Solução Manual de Rotas - LiteSpeed</h1>";

// Simular o processamento manual de rotas
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';

echo "<h2>1. Informações da Requisição</h2>";
echo "<ul>";
echo "<li>REQUEST_URI: " . $request_uri . "</li>";
echo "<li>SCRIPT_NAME: " . $script_name . "</li>";
echo "<li>PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'N/A') . "</li>";
echo "</ul>";

// Testar o processamento manual
echo "<h2>2. Teste do Processamento Manual</h2>";

if (strpos($request_uri, 'index.php') === false && strpos($request_uri, '/mercadolivre/') !== false) {
    $path_info = str_replace(dirname($script_name), '', $request_uri);
    $path_info = ltrim($path_info, '/');
    
    echo "<p style='color: green;'>✓ Processamento manual aplicado</p>";
    echo "<ul>";
    echo "<li>Path Info calculado: " . $path_info . "</li>";
    echo "<li>Novo PATH_INFO: /" . $path_info . "</li>";
    echo "<li>Novo REQUEST_URI: " . $script_name . "/" . $path_info . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>⚠ Processamento manual não aplicado</p>";
    echo "<p>Motivo: Requisição já inclui index.php ou não é uma rota do Mercado Livre</p>";
}

// Testar URLs específicas
echo "<h2>3. Teste de URLs Específicas</h2>";
$base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/');

echo "<p>Testando URLs com processamento manual:</p>";
echo "<ul>";
echo "<li><a href='http://{$base_url}/mercadolivre/autenticar' target='_blank'>MercadoLivre Autenticar</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre/callback' target='_blank'>MercadoLivre Callback</a></li>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre/autenticar' target='_blank'>Com index.php</a></li>";
echo "<li><a href='http://{$base_url}/index.php/mercadolivre/callback' target='_blank'>Callback com index.php</a></li>";
echo "</ul>";

// Verificar se o index.php foi modificado
echo "<h2>4. Verificação do index.php</h2>";
$index_file = __DIR__ . '/index.php';
if (file_exists($index_file)) {
    $index_content = file_get_contents($index_file);
    
    if (strpos($index_content, 'Processamento manual de rotas para LiteSpeed') !== false) {
        echo "<p style='color: green;'>✓ Processamento manual adicionado ao index.php</p>";
    } else {
        echo "<p style='color: red;'>✗ Processamento manual não encontrado no index.php</p>";
    }
    
    if (strpos($index_content, 'mercadolivre') !== false) {
        echo "<p style='color: green;'>✓ Rotas do Mercado Livre configuradas</p>";
    } else {
        echo "<p style='color: red;'>✗ Rotas do Mercado Livre não configuradas</p>";
    }
} else {
    echo "<p style='color: red;'>✗ index.php não encontrado</p>";
}

// Verificar controller
echo "<h2>5. Verificação do Controller</h2>";
$controller_file = __DIR__ . '/application/controllers/MercadoLivre.php';
if (file_exists($controller_file)) {
    $controller_content = file_get_contents($controller_file);
    
    if (strpos($controller_content, 'public function autenticar()') !== false) {
        echo "<p style='color: green;'>✓ Método autenticar() encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Método autenticar() não encontrado</p>";
    }
    
    if (strpos($controller_content, 'if ($this->session->userdata(\'logado\'))') !== false) {
        echo "<p style='color: green;'>✓ Verificação de permissão corrigida</p>";
    } else {
        echo "<p style='color: red;'>✗ Verificação de permissão não corrigida</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Controller MercadoLivre.php não encontrado</p>";
}

echo "<h2>6. Instruções de Teste</h2>";
echo "<ol>";
echo "<li>Teste primeiro a URL com index.php: <a href='http://{$base_url}/index.php/mercadolivre/autenticar' target='_blank'>Aqui</a></li>";
echo "<li>Se funcionar, teste sem index.php: <a href='http://{$base_url}/mercadolivre/autenticar' target='_blank'>Aqui</a></li>";
echo "<li>Se ambas funcionarem, o problema está resolvido</li>";
echo "<li>Se apenas a com index.php funcionar, o .htaccess não está sendo processado</li>";
echo "</ol>";

echo "<h2>7. Solução Alternativa</h2>";
echo "<p>Se o problema persistir, você pode:</p>";
echo "<ol>";
echo "<li>Usar sempre URLs com index.php</li>";
echo "<li>Configurar o LiteSpeed para processar .htaccess</li>";
echo "<li>Usar um proxy reverso (Nginx) na frente do LiteSpeed</li>";
echo "</ol>";

echo "<p><strong>Nota:</strong> Após confirmar que funciona, delete este arquivo por segurança.</p>";
?> 