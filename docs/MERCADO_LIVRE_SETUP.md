# Configuração da Integração com Mercado Livre

Este documento explica como configurar a integração do Map-OS com a API do Mercado Livre.

## Pré-requisitos

1. Conta ativa no Mercado Livre
2. Acesso ao painel de desenvolvedores do Mercado Livre
3. Permissões de administrador no Map-OS

## Passo 1: Criar Aplicativo no Mercado Livre

1. Acesse [https://developers.mercadolivre.com.br/apps](https://developers.mercadolivre.com.br/apps)
2. Faça login com sua conta do Mercado Livre
3. Clique em "Criar aplicativo"
4. Preencha os dados:
   - **Nome do aplicativo**: Map-OS Integration
   - **Descrição**: Integração do sistema Map-OS com Mercado Livre
   - **Tipo de aplicativo**: Web App
   - **URL de redirecionamento**: `https://seudominio.com/mercadolivre/callback`
   - **Permissões**: 
     - `read` (leitura)
     - `write` (escrita)
     - `offline_access` (acesso offline)

5. Clique em "Criar aplicativo"
6. Anote o **CLIENT_ID** e **CLIENT_SECRET** gerados

## Passo 2: Configurar no Map-OS

1. Acesse o painel administrativo do Map-OS
2. Vá em **Configurações** > **Sistema**
3. Clique na aba **"Mercado Livre"**
4. Configure os campos:

### Configurações Básicas
- **Ativar Integração**: Sim
- **CLIENT_ID**: Cole o CLIENT_ID obtido no passo anterior
- **CLIENT_SECRET**: Cole o CLIENT_SECRET obtido no passo anterior
- **URL de Redirecionamento**: Deixe como está (será preenchida automaticamente)

### Configurações de Produtos
- **Site ID**: Selecione o país (MLB para Brasil)
- **Categoria Padrão**: ID da categoria padrão no ML (opcional)
- **Condição Padrão**: Novo ou Usado
- **Tipo de Listagem**: Premium, Clássica ou Gratuita
- **Aceita Mercado Envios**: Sim/Não
- **Frete Grátis**: Sim/Não
- **Garantia Padrão**: Número de dias (ex: 90)

### Configurações de Sincronização
- **Sincronização Automática**: Ativar/Desativar
- **Sincronizar Estoque**: Ativar/Desativar
- **Sincronizar Preços**: Ativar/Desativar
- **Nível de Log**: Apenas Erros, Informações ou Debug Completo

5. Clique em **"Salvar Alterações"**

## Passo 3: Autenticar com Mercado Livre

1. Após salvar as configurações, você verá o status da autenticação
2. Se não estiver autenticado, clique em **"Autenticar com Mercado Livre"**
3. Você será redirecionado para o Mercado Livre
4. Faça login e autorize o aplicativo
5. Você será redirecionado de volta para o Map-OS
6. O status deve mostrar "Autenticado!" com seu nickname

## Passo 4: Configurar Produtos

1. Vá em **Produtos** > **Adicionar Produto**
2. Preencha os dados do produto normalmente
3. Na seção **"Integração com Mercado Livre"**:
   - Marque **"Publicar no Mercado Livre"**
   - Configure as opções específicas do produto
   - Clique em **"Salvar"**

## Funcionalidades Disponíveis

### Sincronização Automática
- Produtos são automaticamente sincronizados com o ML
- Estoque e preços são atualizados automaticamente
- Logs de todas as operações são mantidos

### Publicação Manual
- Produtos podem ser publicados individualmente
- Controle total sobre as configurações de cada produto
- Preview do anúncio antes da publicação

### Gestão de Produtos
- Visualizar produtos já publicados no ML
- Atualizar informações de produtos existentes
- Remover produtos do ML

### Logs e Monitoramento
- Logs detalhados de todas as operações
- Monitoramento de erros e sucessos
- Histórico de sincronizações

## Troubleshooting

### Erro de Autenticação
- Verifique se o CLIENT_ID e CLIENT_SECRET estão corretos
- Confirme se a URL de redirecionamento está configurada corretamente no ML
- Verifique se o aplicativo tem as permissões necessárias

### Erro de Publicação
- Verifique se o produto tem todas as informações obrigatórias
- Confirme se a categoria selecionada é válida
- Verifique se o token de acesso não expirou

### Sincronização Não Funciona
- Verifique se a sincronização automática está ativada
- Confirme se o produto está marcado para integração
- Verifique os logs para identificar erros específicos

## Limitações da API

- **Rate Limiting**: A API do ML tem limites de requisições por minuto
- **Categorias**: Algumas categorias podem ter restrições específicas
- **Imagens**: Imagens devem seguir os padrões do ML
- **Preços**: Preços devem estar dentro dos limites aceitos pelo ML

## Suporte

Para suporte técnico:
1. Verifique os logs do sistema
2. Consulte a documentação da API do Mercado Livre
3. Entre em contato com o suporte do Map-OS

## Changelog

### v1.0.0
- Integração inicial com Mercado Livre
- Autenticação OAuth2
- Publicação de produtos
- Sincronização automática
- Sistema de logs 