# Solução para Erro 404 na Autenticação do Mercado Livre

## Problema
O erro 404 "Page Not Found" ocorre quando você tenta autenticar com o Mercado Livre no servidor web, mas funciona perfeitamente no servidor local.

## ⚠️ ATENÇÃO: Erro Internal Server Error

Se após aplicar as correções você receber um **"Internal Server Error"**, isso indica que o arquivo `.htaccess` está causando problemas. 

### Solução para Internal Server Error:

1. **Simplifique o arquivo `.htaccess`** para apenas o essencial:
   ```apache
   RewriteEngine On
   
   # Handle Authorization Header
   RewriteCond %{HTTP:Authorization} ^(.+)$
   RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
   
   # Remove index.php from URLs
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php/$1 [L]
   ```

2. **Teste com o arquivo simplificado**:
   - Acesse `http://seudominio.com/teste_simples.php`
   - Se funcionar, o problema era o `.htaccess` complexo

3. **Verifique os logs do Apache**:
   - WAMP: `C:\wamp64\logs\apache_error.log`
   - XAMPP: `C:\xampp\apache\logs\error.log`

## Causas Principais

### 1. Configuração do .htaccess
O arquivo `.htaccess` na raiz do projeto estava incompleto e não estava configurando corretamente o mod_rewrite.

### 2. URL de Redirecionamento Incorreta
A URL configurada no aplicativo do Mercado Livre pode não estar correta para o servidor web.

### 3. Configuração do Servidor
O servidor web pode não estar processando corretamente as rotas do CodeIgniter.

## Soluções Implementadas

### ✅ 1. Arquivo .htaccess Corrigido (Versão Simplificada)
O arquivo `.htaccess` na raiz do projeto foi atualizado com:
- Configuração básica do mod_rewrite
- Apenas as regras essenciais para funcionar

### ✅ 2. Script de Diagnóstico Simples
Foi criado o arquivo `teste_simples.php` para diagnosticar problemas:
- Verifica se o servidor está funcionando
- Testa as configurações básicas
- Verifica se os arquivos necessários existem

### ✅ 3. Script de Diagnóstico Completo
Foi criado o arquivo `teste_rotas.php` para diagnóstico detalhado:
- Verifica se o mod_rewrite está ativo
- Testa as configurações do ambiente
- Verifica se os arquivos necessários existem
- Testa as URLs do Mercado Livre

## Passos para Resolver

### Passo 1: Testar o Servidor
1. Acesse: `http://seudominio.com/teste_simples.php`
2. Se funcionar, o servidor está OK
3. Se der erro, verifique os logs do Apache

### Passo 2: Testar o Diagnóstico Completo
1. Acesse: `http://seudominio.com/teste_rotas.php`
2. Verifique todos os itens marcados com ✓
3. Corrija os itens marcados com ✗

### Passo 3: Verificar Configurações do Servidor
1. **mod_rewrite**: Certifique-se de que o mod_rewrite está habilitado no servidor
2. **AllowOverride**: Verifique se o AllowOverride está configurado como "All" no Apache
3. **Arquivo .htaccess**: Confirme que o arquivo está sendo lido pelo servidor

### Passo 4: Configurar URL de Redirecionamento
No aplicativo do Mercado Livre (https://developers.mercadolivre.com.br/apps):

1. Acesse seu aplicativo
2. Vá em "Configurações"
3. Configure a URL de redirecionamento como:
   ```
   https://seudominio.com/mercadolivre/callback
   ```
   (Substitua "seudominio.com" pelo seu domínio real)

### Passo 5: Verificar Configurações do .env
No arquivo `application/.env`, verifique:

```env
# URL base do sistema
APP_BASEURL=https://seudominio.com/

# Configurações do Mercado Livre
MERCADO_LIVRE_ENABLED=true
MERCADO_LIVRE_CLIENT_ID=seu_client_id_aqui
MERCADO_LIVRE_CLIENT_SECRET=seu_client_secret_aqui
MERCADO_LIVRE_REDIRECT_URI=https://seudominio.com/mercadolivre/callback
```

### Passo 6: Testar as URLs
Teste estas URLs no navegador:
- `https://seudominio.com/mercadolivre`
- `https://seudominio.com/mercadolivre/autenticar`
- `https://seudominio.com/mercadolivre/callback`

## Configurações Específicas por Servidor

### WAMP (Windows)
Se estiver usando WAMP:
1. Clique no ícone do WAMP na bandeja do sistema
2. Apache > Modules > mod_rewrite (deve estar marcado)
3. Verifique se o arquivo `httpd.conf` tem `AllowOverride All`

### Apache
Se estiver usando Apache, adicione ao arquivo de configuração do virtual host:

```apache
<Directory /var/www/html>
    AllowOverride All
    Require all granted
</Directory>
```

### Nginx
Se estiver usando Nginx, adicione ao arquivo de configuração:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### cPanel
Se estiver usando cPanel:
1. Acesse o cPanel
2. Vá em "MultiPHP Manager"
3. Certifique-se de que o PHP está configurado corretamente
4. Vá em "Apache Configuration" e verifique se o mod_rewrite está ativo

## Verificações Adicionais

### 1. Logs do Servidor
Verifique os logs de erro do servidor:
- WAMP: `C:\wamp64\logs\apache_error.log`
- XAMPP: `C:\xampp\apache\logs\error.log`
- Apache: `/var/log/apache2/error.log`
- Nginx: `/var/log/nginx/error.log`
- cPanel: Logs de erro no cPanel

### 2. Permissões de Arquivo
Certifique-se de que os arquivos têm as permissões corretas:
```bash
chmod 644 .htaccess
chmod 755 application/
chmod 644 application/.env
```

### 3. Configuração do PHP
Verifique se estas extensões estão habilitadas:
- curl
- openssl
- json
- mbstring

## Teste Final

Após fazer todas as correções:

1. Acesse o painel administrativo do Map-OS
2. Vá em **Configurações** > **Sistema** > **Mercado Livre**
3. Clique em "Autenticar com Mercado Livre"
4. Se tudo estiver correto, você será redirecionado para o Mercado Livre
5. Após autorizar, você será redirecionado de volta para o sistema

## Arquivos Modificados

- ✅ `.htaccess` - Configuração simplificada do mod_rewrite
- ✅ `teste_simples.php` - Script de teste básico
- ✅ `teste_rotas.php` - Script de diagnóstico completo
- ✅ `SOLUCAO_MERCADO_LIVRE_404.md` - Este guia

## Limpeza

Após confirmar que tudo está funcionando:
1. Delete os arquivos `teste_simples.php` e `teste_rotas.php` por segurança
2. Mantenha este guia para referência futura

## Suporte

Se o problema persistir:
1. Execute o script `teste_simples.php` primeiro
2. Se funcionar, execute o `teste_rotas.php`
3. Compartilhe os resultados com o suporte
4. Verifique os logs do servidor
5. Confirme as configurações do aplicativo no Mercado Livre 