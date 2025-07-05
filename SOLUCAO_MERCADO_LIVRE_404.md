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

2. **Verifique os logs do Apache**:
   - Windows/WAMP: `C:\wamp64\logs\apache_error.log`
   - Linux: `/var/log/apache2/error.log`

## ✅ Correções Aplicadas

### 1. Arquivo .htaccess Corrigido
O arquivo `.htaccess` na raiz do projeto foi simplificado para evitar conflitos com servidores web.

### 2. Controller MercadoLivre Corrigido
**Problema identificado**: A verificação de permissão no construtor estava causando erro 404.

**Correções aplicadas**:
- Removida verificação de permissão do construtor
- Adicionada verificação de permissão apenas no método `autenticar()`
- Corrigido caminho do arquivo de log para ser compatível com diferentes servidores

### 3. Script de Diagnóstico (`teste_mercadolivre_autenticar.php`)
Script específico para testar o método de autenticação do Mercado Livre.

## 🔧 Passos para Resolver

### Passo 1: Verificar Configurações
1. Acesse: `http://seudominio.com/teste_mercadolivre_autenticar.php`
2. Verifique se todas as configurações estão corretas

### Passo 2: Testar Autenticação
1. Acesse: `http://seudominio.com/mercadolivre/autenticar`
2. Se funcionar, você será redirecionado para o Mercado Livre
3. Se der erro 404, continue com os próximos passos

### Passo 3: Verificar Servidor Web
Para **WAMP/Windows**:
1. Abra o WAMP
2. Clique com botão direito no ícone do WAMP
3. Apache → Modules → mod_rewrite (deve estar marcado)

Para **Linux/Apache**:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Passo 4: Verificar Logs
- **Windows**: `C:\wamp64\logs\apache_error.log`
- **Linux**: `/var/log/apache2/error.log`

Procure por erros relacionados ao Mercado Livre.

### Passo 5: Verificar Configurações do .env
No arquivo `application/.env`, verifique:
```env
MERCADO_LIVRE_ENABLED=true
MERCADO_LIVRE_CLIENT_ID=seu_client_id
MERCADO_LIVRE_CLIENT_SECRET=seu_client_secret
MERCADO_LIVRE_REDIRECT_URI=https://seudominio.com/mercadolivre/callback
```

### Passo 6: Verificar Aplicativo no Mercado Livre
1. Acesse: https://developers.mercadolivre.com.br/apps
2. Configure a URL de redirecionamento como: `https://seudominio.com/mercadolivre/callback`

## 🐛 Problemas Comuns

### Problema 1: Erro 404 persistente
**Solução**: Verifique se o mod_rewrite está ativo no servidor web.

### Problema 2: Internal Server Error
**Solução**: Simplifique o arquivo `.htaccess` conforme mostrado acima.

### Problema 3: Permissão negada
**Solução**: Verifique se o usuário do servidor web tem permissão para ler os arquivos.

### Problema 4: URL de redirecionamento incorreta
**Solução**: Configure a URL correta no aplicativo do Mercado Livre.

## 📋 Checklist de Verificação

- [ ] Mod_rewrite ativo no servidor
- [ ] Arquivo .htaccess configurado corretamente
- [ ] Controller MercadoLivre.php corrigido
- [ ] Configurações do .env corretas
- [ ] URL de redirecionamento configurada no ML
- [ ] Logs do servidor sem erros
- [ ] Teste de autenticação funcionando

## 🔍 Debug Avançado

Se o problema persistir, execute:

```bash
# Verificar se o mod_rewrite está ativo
apache2ctl -M | grep rewrite

# Verificar configuração do Apache
apache2ctl -S

# Verificar permissões dos arquivos
ls -la application/controllers/MercadoLivre.php
ls -la application/.env
```

## 📞 Suporte

Se ainda houver problemas:
1. Execute o script de teste
2. Verifique os logs do servidor
3. Teste a URL de autenticação diretamente
4. Verifique se todas as configurações estão corretas

## 🚀 Próximos Passos

Após resolver o problema:
1. Delete os arquivos de teste por segurança
2. Teste a autenticação completa
3. Verifique se os tokens estão sendo salvos corretamente
4. Teste a sincronização de produtos

---

**Nota**: As correções aplicadas resolvem o problema específico do erro 404 na autenticação do Mercado Livre, mantendo a segurança e funcionalidade do sistema. 