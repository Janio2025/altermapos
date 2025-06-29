# Múltiplos Servidores de Mídia - MapOS

## Visão Geral

Esta funcionalidade permite configurar múltiplos servidores de mídia no sistema MapOS, distribuindo o armazenamento de arquivos entre diferentes HDs externos ou servidores de rede. O sistema mantém compatibilidade total com a configuração anterior, funcionando da seguinte forma:

### Lógica de Funcionamento

1. **Se há servidores configurados**: O sistema salva os arquivos nos servidores de mídia configurados
2. **Se não há servidores configurados**: O sistema salva na pasta padrão local (`assets/anexos/`) como sempre funcionou

## Instalação

### 1. Executar a Migration

```bash
php index.php migrate
```

### 2. Executar o Script de Migração (Opcional)

Se você já tinha um servidor de mídia configurado anteriormente, execute o script SQL para migrar os dados:

```sql
-- Executar o arquivo: updates/update_multiplos_servidores_midia.sql
```

### 3. Configuração

1. Acesse **Configurações do Sistema** → **Gerais**
2. Na seção **Servidores de Mídia**, configure seus servidores:
   - **Nome**: Identificação do servidor (ex: "HD Principal", "HD Externo 1")
   - **URL**: URL de acesso ao servidor (ex: `http://192.168.0.10/midia`)
   - **Caminho Físico**: Caminho físico no servidor (ex: `C:/wamp64/www/midia`)
   - **Prioridade**: Ordem de preferência (0 = mais alta)
   - **Ativo**: Se o servidor está disponível

## Funcionalidades

### Configuração Dinâmica
- Adicionar/remover servidores dinamicamente via interface
- Configurar prioridade de cada servidor
- Ativar/desativar servidores individualmente

### Monitoramento de Espaço
- Visualizar espaço total, livre e usado de cada servidor
- Atualizar informações de espaço em tempo real
- Alertas visuais baseados no percentual de uso

### Seleção Inteligente de Servidor
- O sistema escolhe automaticamente o servidor com mais espaço disponível
- Fallback para servidores com menor prioridade se necessário
- Mantém compatibilidade com configuração única anterior

## Compatibilidade

### Configuração Anterior
Se você não configurar nenhum servidor, o sistema continuará funcionando exatamente como antes, salvando os arquivos em:
- `assets/anexos/os/` para anexos de OS
- `assets/anexos/produtos/` para imagens de produtos

### Migração Automática
O sistema detecta automaticamente se há configuração anterior e migra os dados para a nova estrutura, mantendo a funcionalidade existente.

## Estrutura de Arquivos

```
application/
├── controllers/
│   ├── Mapos.php (atualizado)
│   ├── Os.php (atualizado)
│   └── Produtos.php (atualizado)
├── models/
│   └── Servidores_midia_model.php (novo)
├── helpers/
│   └── media_server_helper.php (novo)
├── views/
│   └── mapos/
│       └── configurar.php (atualizado)
└── database/
    └── migrations/
        └── 20241201000001_create_servidores_midia_table.php (novo)
```

## Uso

### Configuração de Servidores

1. **Acesse as configurações**: Sistema → Configurações → Gerais
2. **Adicione servidores**: Clique em "Adicionar Servidor"
3. **Configure cada servidor**:
   - Nome descritivo
   - URL de acesso
   - Caminho físico
   - Prioridade (0 = mais alta)
   - Status ativo

### Monitoramento

1. **Visualizar espaço**: Clique em "Atualizar Informações de Espaço"
2. **Interpretar cores**:
   - Verde: < 60% usado
   - Amarelo: 60-80% usado
   - Vermelho: > 80% usado

## Troubleshooting

### Problemas Comuns

1. **Erro de permissão**: Verifique se o PHP tem permissão de escrita no caminho físico
2. **Servidor não acessível**: Confirme se a URL e caminho físico estão corretos
3. **Espaço não atualiza**: Verifique se o caminho físico existe e é acessível

### Logs

O sistema registra logs para:
- Adição/remoção de servidores
- Erros de acesso a servidores
- Atualizações de espaço

### Fallback Automático

Se todos os servidores configurados ficarem indisponíveis, o sistema automaticamente:
1. Tenta usar o próximo servidor na lista de prioridade
2. Se não houver servidores disponíveis, usa o caminho local padrão
3. Registra o erro nos logs para investigação

## Desenvolvimento

### Adicionando Novos Tipos de Upload

Para adicionar suporte a novos tipos de upload (ex: clientes, vendas), use o helper:

```php
$this->load->helper('media_server_helper');

$config = Media_server_helper::getDiretorioUpload('tipo', $id);
$directory = $config['directory'];
$url_base = $config['url_base'];
```

### API

O helper fornece métodos para:
- `getServidoresAtivos()`: Lista servidores ativos
- `escolherServidor()`: Seleciona o melhor servidor
- `getDiretorioUpload($tipo, $id)`: Determina diretório e URL
- `temServidoresConfigurados()`: Verifica se há configuração

## Suporte

Para problemas ou dúvidas:
1. Verifique os logs do sistema
2. Confirme as permissões de arquivo
3. Teste a conectividade com os servidores
4. Verifique se a migration foi executada corretamente 