-- =====================================================
-- INTEGRAÇÃO COM MERCADO LIVRE - TABELAS
-- =====================================================
-- Este arquivo contém as tabelas necessárias para a integração
-- com o Mercado Livre no sistema Map-OS
-- =====================================================

-- -----------------------------------------------------
-- Table `produtos_mercado_livre`
-- Armazena as configurações de cada produto no ML
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `produtos_mercado_livre` (
    `id` int NOT NULL AUTO_INCREMENT,
    `produto_id` int NOT NULL,
    `ml_id` varchar(50) DEFAULT NULL COMMENT 'ID do anúncio no Mercado Livre',
    `ml_permalink` varchar(255) DEFAULT NULL COMMENT 'Link do anúncio no ML',
    `ml_categoria` varchar(20) DEFAULT NULL COMMENT 'Categoria do ML (ex: MLB5672)',
    `ml_condicao` enum('new','used') DEFAULT 'new' COMMENT 'Condição do produto',
    `ml_garantia` int DEFAULT NULL COMMENT 'Garantia em dias',
    `ml_tags` text COMMENT 'Tags/palavras-chave',
    `ml_descricao` text COMMENT 'Descrição específica para o ML',
    `ml_envios` tinyint(1) DEFAULT 0 COMMENT 'Aceita Mercado Envios',
    `ml_premium` tinyint(1) DEFAULT 0 COMMENT 'Anúncio Premium',
    `ml_destaque` tinyint(1) DEFAULT 0 COMMENT 'Anúncio Destaque',
    `ml_classico` tinyint(1) DEFAULT 0 COMMENT 'Anúncio Clássico',
    `status` enum('active','paused','closed','pending') DEFAULT 'pending' COMMENT 'Status do anúncio',
    `ultima_sincronizacao` timestamp NULL DEFAULT NULL COMMENT 'Última sincronização com ML',
    `erro_sincronizacao` text COMMENT 'Erro na última sincronização',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `produto_id` (`produto_id`),
    KEY `ml_id` (`ml_id`),
    KEY `status` (`status`),
    CONSTRAINT `fk_produtos_ml_produto` 
        FOREIGN KEY (`produto_id`) 
        REFERENCES `produtos` (`idProdutos`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Configurações de integração com Mercado Livre';

-- -----------------------------------------------------
-- Table `ml_configuracoes`
-- Configurações gerais da integração com ML
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ml_configuracoes` (
    `id` int NOT NULL AUTO_INCREMENT,
    `access_token` text COMMENT 'Token de acesso do ML',
    `refresh_token` text COMMENT 'Token de renovação do ML',
    `user_id` varchar(50) DEFAULT NULL COMMENT 'ID do usuário no ML',
    `nickname` varchar(100) DEFAULT NULL COMMENT 'Nickname do vendedor no ML',
    `site_id` varchar(10) DEFAULT 'MLB' COMMENT 'ID do site (MLB para Brasil)',
    `token_expires_at` timestamp NULL DEFAULT NULL COMMENT 'Data de expiração do token',
    `ativo` tinyint(1) DEFAULT 0 COMMENT 'Se a integração está ativa',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Configurações gerais da integração com ML';

-- -----------------------------------------------------
-- Table `ml_logs`
-- Logs de todas as operações com o ML
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ml_logs` (
    `id` int NOT NULL AUTO_INCREMENT,
    `produto_id` int DEFAULT NULL,
    `acao` varchar(50) NOT NULL COMMENT 'Ação realizada (create, update, delete, sync)',
    `status` enum('success','error','pending') NOT NULL,
    `mensagem` text COMMENT 'Mensagem de log',
    `dados_enviados` text COMMENT 'Dados enviados para o ML',
    `resposta_ml` text COMMENT 'Resposta do Mercado Livre',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `produto_id` (`produto_id`),
    KEY `acao` (`acao`),
    KEY `status` (`status`),
    KEY `created_at` (`created_at`),
    CONSTRAINT `fk_ml_logs_produto` 
        FOREIGN KEY (`produto_id`) 
        REFERENCES `produtos` (`idProdutos`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Logs de integração com Mercado Livre';

-- -----------------------------------------------------
-- INSERIR DADOS INICIAIS
-- -----------------------------------------------------

-- Inserir configuração padrão (desativada)
INSERT INTO `ml_configuracoes` (`ativo`, `site_id`) VALUES (0, 'MLB');

-- -----------------------------------------------------
-- COMENTÁRIOS SOBRE A ESTRUTURA
-- -----------------------------------------------------
/*
ESTRUTURA DAS TABELAS:

1. produtos_mercado_livre:
   - Armazena configurações específicas de cada produto
   - Relacionamento 1:1 com a tabela produtos
   - Campos para todas as opções do formulário

2. ml_configuracoes:
   - Configurações gerais da integração
   - Tokens de autenticação
   - Informações do vendedor

3. ml_logs:
   - Logs de todas as operações
   - Rastreamento de erros
   - Histórico de sincronizações

COMO USAR:
1. Execute este arquivo no seu banco de dados
2. Configure os tokens do Mercado Livre na tabela ml_configuracoes
3. Os produtos marcados para integração serão salvos automaticamente
*/ 