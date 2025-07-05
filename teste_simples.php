<?php
/**
 * Teste simples para verificar se o servidor está funcionando
 */

echo "<h1>Teste Simples - Servidor Funcionando</h1>";

echo "<h2>Informações do Servidor</h2>";
echo "<ul>";
echo "<li>PHP Version: " . PHP_VERSION . "</li>";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</li>";
echo "<li>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</li>";
echo "<li>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</li>";
echo "</ul>";

echo "<h2>Teste de Conexão</h2>";
echo "<p style='color: green;'>✓ Se você está vendo esta página, o servidor está funcionando!</p>";

echo "<h2>Teste de Rotas</h2>";
$base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/');

echo "<p>Teste estas URLs:</p>";
echo "<ul>";
echo "<li><a href='http://{$base_url}/index.php' target='_blank'>Index.php</a></li>";
echo "<li><a href='http://{$base_url}/mapos' target='_blank'>Mapos</a></li>";
echo "<li><a href='http://{$base_url}/mercadolivre' target='_blank'>MercadoLivre</a></li>";
echo "</ul>";

echo "<h2>Verificação de Arquivos</h2>";
$files_to_check = [
    '.htaccess' => 'Arquivo .htaccess',
    'index.php' => 'Arquivo index.php',
    'application/.env' => 'Arquivo .env',
    'application/controllers/MercadoLivre.php' => 'Controller MercadoLivre',
    'application/config/routes.php' => 'Arquivo routes.php'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ {$description} encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ {$description} não encontrado</p>";
    }
}

echo "<h2>Próximos Passos</h2>";
echo "<ol>";
echo "<li>Se esta página carregou, o servidor está funcionando</li>";
echo "<li>Teste as URLs acima para verificar as rotas</li>";
echo "<li>Se as rotas funcionarem, tente a autenticação do Mercado Livre</li>";
echo "<li>Se ainda houver problemas, verifique os logs do Apache</li>";
echo "</ol>";

echo "<p><strong>Nota:</strong> Após confirmar que tudo está funcionando, delete este arquivo.</p>";
?> 