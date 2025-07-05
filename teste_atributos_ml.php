<?php
// Teste de atributos do Mercado Livre
echo "<h2>Teste de Atributos do Mercado Livre</h2>";

// Simular o ambiente do CodeIgniter
define('FCPATH', __DIR__ . '/');

// Verificar se os arquivos existem
$controller_produtos = FCPATH . 'application/controllers/Produtos.php';
$controller_ml = FCPATH . 'application/controllers/MercadoLivre.php';
$model_categorias = FCPATH . 'application/models/Categorias_model.php';
$model_ml = FCPATH . 'application/models/MercadoLivre_model.php';

if (file_exists($controller_produtos)) {
    echo "<p style='color: green;'>✓ Controlador Produtos.php encontrado</p>";
    
    // Verificar se o método adicionar foi modificado para salvar atributos
    $content = file_get_contents($controller_produtos);
    if (strpos($content, 'ml_atributo_') !== false) {
        echo "<p style='color: green;'>✓ Código para coletar atributos do formulário encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Código para coletar atributos do formulário NÃO encontrado</p>";
    }
    
    if (strpos($content, 'json_encode($atributos_ml)') !== false) {
        echo "<p style='color: green;'>✓ Código para salvar atributos em JSON encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Código para salvar atributos em JSON NÃO encontrado</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Controlador Produtos.php NÃO encontrado</p>";
}

if (file_exists($controller_ml)) {
    echo "<p style='color: green;'>✓ Controlador MercadoLivre.php encontrado</p>";
    
    // Verificar se o método getAtributosProduto foi modificado
    $content = file_get_contents($controller_ml);
    if (strpos($content, 'ml_atributos') !== false) {
        echo "<p style='color: green;'>✓ Código para usar atributos salvos do banco encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Código para usar atributos salvos do banco NÃO encontrado</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Controlador MercadoLivre.php NÃO encontrado</p>";
}

if (file_exists($model_categorias)) {
    echo "<p style='color: green;'>✓ Modelo Categorias_model.php encontrado</p>";
    
    // Verificar se o método getAtributosByCategoria existe
    $content = file_get_contents($model_categorias);
    if (strpos($content, 'getAtributosByCategoria') !== false) {
        echo "<p style='color: green;'>✓ Método getAtributosByCategoria encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Método getAtributosByCategoria NÃO encontrado</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Modelo Categorias_model.php NÃO encontrado</p>";
}

if (file_exists($model_ml)) {
    echo "<p style='color: green;'>✓ Modelo MercadoLivre_model.php encontrado</p>";
    
    // Verificar se o método getProdutosPendentes foi modificado
    $content = file_get_contents($model_ml);
    if (strpos($content, 'p.categoria_id') !== false) {
        echo "<p style='color: green;'>✓ Código para buscar categoria_id encontrado</p>";
    } else {
        echo "<p style='color: red;'>✗ Código para buscar categoria_id NÃO encontrado</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Modelo MercadoLivre_model.php NÃO encontrado</p>";
}

echo "<h3>Resumo das Correções Implementadas:</h3>";
echo "<ul>";
echo "<li>✓ Modificado método adicionar() para coletar atributos do formulário</li>";
echo "<li>✓ Adicionado código para salvar atributos em JSON no campo ml_atributos</li>";
echo "<li>✓ Modificado método getAtributosProduto() para usar atributos salvos</li>";
echo "<li>✓ Modificado método getProdutosPendentes() para incluir categoria_id</li>";
echo "<li>✓ Implementado fallback para atributos padrão quando não há atributos salvos</li>";
echo "</ul>";

echo "<h3>Fluxo de Funcionamento:</h3>";
echo "<ol>";
echo "<li>Usuário seleciona categoria do Mercado Livre</li>";
echo "<li>Sistema carrega atributos dinamicamente via AJAX</li>";
echo "<li>Usuário preenche os atributos obrigatórios</li>";
echo "<li>Ao salvar produto, atributos são coletados do formulário</li>";
echo "<li>Atributos são salvos em JSON no campo ml_atributos</li>";
echo "<li>Na sincronização, sistema usa atributos salvos em vez de hardcoded</li>";
echo "<li>Se não houver atributos salvos, usa atributos padrão</li>";
echo "</ol>";

echo "<h3>Como Testar:</h3>";
echo "<ol>";
echo "<li>Acesse: <a href='https://duke.acell.tec.br/os/index.php/produtos/adicionar' target='_blank'>Adicionar Produto</a></li>";
echo "<li>Marque o checkbox 'Sincronizar Com Mercado Livre'</li>";
echo "<li>Selecione uma categoria do Mercado Livre</li>";
echo "<li>Preencha os atributos que aparecerem</li>";
echo "<li>Salve o produto</li>";
echo "<li>Verifique no banco se o campo ml_atributos foi preenchido</li>";
echo "<li>Tente sincronizar o produto</li>";
echo "<li>Verifique os logs para confirmar que não há mais erro 'body.required_fields'</li>";
echo "</ol>";

echo "<h3>Verificações no Banco:</h3>";
echo "<ul>";
echo "<li>Verificar se o campo ml_atributos na tabela produtos_mercado_livre está sendo preenchido</li>";
echo "<li>Verificar se os atributos estão em formato JSON válido</li>";
echo "<li>Verificar se a categoria_id está sendo salva corretamente</li>";
echo "<li>Verificar se os logs mostram 'Usando X atributos salvos do banco'</li>";
echo "</ul>";

echo "<h3>Possíveis Problemas:</h3>";
echo "<ul>";
echo "<li>Se atributos não aparecerem: Verificar se a categoria tem atributos salvos na tabela atributos_ml</li>";
echo "<li>Se atributos não forem salvos: Verificar se os campos têm o nome correto (ml_atributo_X)</li>";
echo "<li>Se sincronização falhar: Verificar se os atributos obrigatórios estão sendo enviados</li>";
echo "<li>Se der erro de categoria: Verificar se o ml_id da categoria está correto</li>";
echo "</ul>";
?> 