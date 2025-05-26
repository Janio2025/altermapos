# Configuração do Cronjob para Pagamentos Automáticos

Este diretório contém os arquivos necessários para configurar o processamento automático de pagamentos no sistema.

## Arquivos

- `verificar_pagamentos.php`: Script principal que verifica e processa os pagamentos automáticos
- `cron_log.txt`: Arquivo de log que registra todas as execuções e erros

## Configuração do Cronjob

### 1. Acesso ao Painel de Controle da Hospedagem

1. Faça login no painel de controle da sua hospedagem
2. Procure a seção "Cron Jobs" ou "Agendador de Tarefas"

### 2. Configuração do Cronjob

Adicione uma nova tarefa cron com as seguintes configurações:

```
0 0 * * * php /caminho/completo/para/seu/site/cron/verificar_pagamentos.php
```

Explicação da configuração:
- `0 0 * * *`: Executa todos os dias à meia-noite
- `php`: Comando para executar o PHP
- `/caminho/completo/para/seu/site/cron/verificar_pagamentos.php`: Caminho completo para o script

### 3. Permissões de Arquivo

Certifique-se de que o arquivo `verificar_pagamentos.php` tenha as permissões corretas:
```bash
chmod 755 verificar_pagamentos.php
chmod 666 cron_log.txt
```

### 4. Teste Manual

Antes de ativar o cronjob, você pode testar o script manualmente:
```bash
php /caminho/completo/para/seu/site/cron/verificar_pagamentos.php
```

### 5. Monitoramento

O script gera logs detalhados no arquivo `cron_log.txt`. Você pode monitorar a execução verificando este arquivo.

## Funcionalidades do Script

1. Verifica todas as carteiras com pagamento automático ativo
2. Processa os pagamentos que estão vencidos
3. Atualiza o saldo das carteiras
4. Registra as transações
5. Calcula a próxima data de pagamento
6. Gera logs detalhados de todas as operações

## Solução de Problemas

Se o cronjob não estiver funcionando:

1. Verifique os logs de erro do servidor
2. Confirme se o caminho do PHP está correto
3. Verifique as permissões dos arquivos
4. Confirme se o timezone está configurado corretamente
5. Verifique se as credenciais do banco de dados estão corretas

## Suporte

Em caso de problemas, verifique:
1. O arquivo `cron_log.txt` para detalhes sobre erros
2. Os logs de erro do servidor
3. As configurações do banco de dados
4. As permissões dos arquivos 