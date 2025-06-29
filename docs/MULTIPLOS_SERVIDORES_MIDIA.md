# Múltiplos Servidores de Mídia

## Visão Geral

Esta funcionalidade permite configurar múltiplos servidores de mídia para distribuir o armazenamento de arquivos (fotos, anexos, etc.) em diferentes locais, resolvendo o problema de espaço limitado em um único HD.

## Características

- **Múltiplos servidores**: Configure quantos servidores desejar
- **Priorização**: Defina a ordem de preferência para cada servidor
- **Controle de espaço**: Sistema monitora o espaço disponível em cada servidor
- **Escalabilidade**: Adicione novos servidores conforme necessário
- **Compatibilidade**: Mantém a funcionalidade existente

## Instalação

### 1. Executar a Migration

```bash
php index.php migrate
```

### 2. Executar o Script de Migração (Opcional)

Se você já tinha um servidor de mídia configurado, execute o script SQL para migrar os dados:

```sql
-- Executar o arquivo: updates/update_multiplos_servidores_midia.sql
```

## Configuração

### Acessando as Configurações

1. Vá para **Configurações do Sistema** > **Gerais**
2. Na seção **Servidores de Mídia**, você verá a nova interface

### Adicionando Servidores

1. Clique no botão **"Adicionar Servidor"**
2. Preencha os campos:
   - **Nome do Servidor**: Identificação do servidor (ex: "HD Principal", "HD Externo")
   - **URL do Servidor**: Endereço web (ex: `http://192.168.0.10/midia`)
   - **Caminho Físico**: Caminho no sistema de arquivos (ex: `C:/wamp64/www/midia`)
   - **Prioridade**: Número que define a ordem de preferência (0 = maior prioridade)
   - **Ativo**: Marque para ativar o servidor

### Configurando Prioridades

- **Prioridade 0**: Servidor principal (usado primeiro)
- **Prioridade 1**: Servidor secundário
- **Prioridade 2**: Servidor terciário
- E assim por diante...

### Monitoramento de Espaço

1. Clique em **"Atualizar Informações de Espaço"** para ver:
   - Espaço total de cada servidor
   - Espaço livre disponível
   - Espaço usado
   - Percentual de uso
   - Status do servidor

## Como Funciona

### Escolha do Servidor

O sistema escolhe automaticamente o servidor para salvar arquivos baseado em:

1. **Prioridade**: Servidores com prioridade menor são escolhidos primeiro
2. **Espaço disponível**: Se o espaço estiver configurado, escolhe o servidor com mais espaço livre
3. **Status ativo**: Apenas servidores ativos são considerados

### Exemplo de Uso

```
Servidor 1: HD Principal (Prioridade 0) - 500GB livre
Servidor 2: HD Externo (Prioridade 1) - 1TB livre
Servidor 3: NAS Remoto (Prioridade 2) - 2TB livre
```

**Cenário**: HD Principal enche
**Solução**: Configure a prioridade do HD Externo como 0 e do HD Principal como 1

## Vantagens

### Escalabilidade
- Adicione novos HDs conforme necessário
- Não perca acesso aos arquivos antigos
- Distribua a carga entre múltiplos servidores

### Flexibilidade
- Configure servidores locais e remotos
- Ative/desative servidores conforme necessário
- Ajuste prioridades dinamicamente

### Monitoramento
- Acompanhe o uso de espaço em tempo real
- Identifique servidores com pouco espaço
- Planeje expansões antecipadamente

## Casos de Uso

### Cenário 1: HD Principal Encheu
1. Adicione um novo HD externo
2. Configure como servidor de mídia
3. Defina prioridade 0 (maior prioridade)
4. O sistema automaticamente usará o novo HD

### Cenário 2: Múltiplos Locais
1. Configure servidores em diferentes locais físicos
2. Use para backup e redundância
3. Mantenha arquivos críticos em múltiplos locais

### Cenário 3: Servidor Remoto
1. Configure um servidor NAS ou cloud
2. Use para arquivos menos críticos
3. Economize espaço local

## Troubleshooting

### Problema: Arquivos não aparecem
**Solução**: Verifique se a URL do servidor está acessível e o caminho físico existe

### Problema: Erro ao salvar arquivos
**Solução**: Verifique as permissões de escrita no caminho físico

### Problema: Servidor não aparece na lista
**Solução**: Verifique se o servidor está marcado como "Ativo"

## API Helper

O sistema inclui um helper para programadores:

```php
// Carregar o helper
$this->load->helper('media_server_helper');

// Escolher servidor automaticamente
$servidor = Media_server_helper::escolherServidor();

// Salvar arquivo
$url = Media_server_helper::salvarArquivo($temp_file, $filename, $folder);

// Remover arquivo
Media_server_helper::removerArquivo($url);

// Verificar se arquivo existe
$existe = Media_server_helper::arquivoExiste($url);
```

## Migração de Dados Existentes

Se você já usava um servidor de mídia único:

1. Execute a migration
2. Execute o script SQL de migração
3. Seus dados existentes serão preservados
4. O servidor antigo será configurado como "Servidor Principal"

## Suporte

Para dúvidas ou problemas:
1. Verifique os logs do sistema
2. Teste a conectividade dos servidores
3. Verifique as permissões de arquivo
4. Consulte a documentação do CodeIgniter 