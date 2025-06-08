SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `ci_sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ci_sessions` (
    `id` varchar(128) NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
    `data` blob NOT NULL,
    KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `permissoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `permissoes` (
    `idPermissao` int NOT NULL AUTO_INCREMENT,
    `nome` varchar(80) NOT NULL,
    `permissoes` text,
    `situacao` tinyint(1) DEFAULT NULL,
    `data` date DEFAULT NULL,
    PRIMARY KEY (`idPermissao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `despesas_recorrentes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `despesas_recorrentes` (
    `idDespesaRecorrente` int NOT NULL AUTO_INCREMENT,
    `descricao` varchar(255) NOT NULL,
    `valor` decimal(10,2) NOT NULL,
    `frequencia` enum('semanal','mensal','anual') NOT NULL,
    `data_inicial` date NOT NULL,
    `data_final` date DEFAULT NULL,
    `status` tinyint(1) DEFAULT 1,
    `cliente_fornecedor` varchar(255) NOT NULL,
    `idCliente` int DEFAULT NULL,
    `categoria` varchar(100) DEFAULT NULL,
    `observacoes` text,
    `data_criacao` date DEFAULT NULL,
    `usuario_criacao` int DEFAULT NULL,
    `data_modificacao` date DEFAULT NULL,
    `usuario_modificacao` int DEFAULT NULL,
    PRIMARY KEY (`idDespesaRecorrente`),
    KEY `fk_despesa_recorrente_cliente` (`idCliente`),
    KEY `fk_despesa_recorrente_usuario_criacao` (`usuario_criacao`),
    KEY `fk_despesa_recorrente_usuario_modificacao` (`usuario_modificacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `idUsuarios` int NOT NULL AUTO_INCREMENT,
    `nome` varchar(80) NOT NULL,
    `rg` varchar(20) DEFAULT NULL,
    `cpf` varchar(20) NOT NULL,
    `cep` varchar(9) NOT NULL,
    `rua` varchar(70) DEFAULT NULL,
    `numero` varchar(15) DEFAULT NULL,
    `bairro` varchar(45) DEFAULT NULL,
    `cidade` varchar(45) DEFAULT NULL,
    `estado` varchar(20) DEFAULT NULL,
    `email` varchar(80) NOT NULL,
    `senha` varchar(200) NOT NULL,
    `telefone` varchar(20) NOT NULL,
    `celular` varchar(20) DEFAULT NULL,
    `situacao` tinyint(1) NOT NULL,
    `dataCadastro` date NOT NULL,
    `permissoes_id` int NOT NULL,
    `dataExpiracao` date DEFAULT NULL,
    `url_image_user` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`idUsuarios`),
    KEY `fk_usuarios_permissoes1_idx` (`permissoes_id`),
    CONSTRAINT `fk_usuarios_permissoes1`
        FOREIGN KEY (`permissoes_id`)
        REFERENCES `permissoes` (`idPermissao`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `carteira_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carteira_usuario` (
    `idCarteiraUsuario` int NOT NULL AUTO_INCREMENT,
    `saldo` decimal(10,2) DEFAULT '0.00',
    `ativo` tinyint(1) DEFAULT '1',
    `usuarios_id` int NOT NULL,
    PRIMARY KEY (`idCarteiraUsuario`),
    KEY `fk_carteira_usuario_usuarios1_idx` (`usuarios_id`),
    CONSTRAINT `fk_carteira_usuario_usuarios1`
        FOREIGN KEY (`usuarios_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela para configurações do Mercado Pago
CREATE TABLE IF NOT EXISTS `mercadopago_config` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `access_token` VARCHAR(255) NOT NULL,
    `public_key` VARCHAR(255) NOT NULL,
    `client_id` VARCHAR(100),
    `client_secret` VARCHAR(255),
    `sandbox_mode` TINYINT(1) DEFAULT 1,
    `webhook_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela para transações do Mercado Pago
CREATE TABLE IF NOT EXISTS `mercadopago_transactions` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `external_id` VARCHAR(100) NOT NULL,
    `user_id` INT NOT NULL,
    `wallet_id` INT NOT NULL,
    `type` ENUM('withdrawal', 'deposit', 'refund') NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `status` VARCHAR(50) NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `pix_key` VARCHAR(255),
    `description` TEXT,
    `response_data` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_external_id` (`external_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_wallet_id` (`wallet_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela para logs do Mercado Pago
CREATE TABLE IF NOT EXISTS `mercadopago_logs` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `transaction_id` INT,
    `type` VARCHAR(50) NOT NULL,
    `message` TEXT NOT NULL,
    `data` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_transaction_id` (`transaction_id`),
    INDEX `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `configuracao_carteira`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `configuracao_carteira` (
    `id` int NOT NULL AUTO_INCREMENT,
    `carteira_usuario_id` int NOT NULL,
    `salario_base` decimal(10,2) NOT NULL,
    `salario_fixo` decimal(10,2) NOT NULL,
    `chave_pix` varchar(255) DEFAULT NULL,
    `comissao_fixa` decimal(5,2) DEFAULT '0.00',
    `data_salario` datetime NOT NULL COMMENT 'Data e hora do pagamento',
    `ultima_data_pagamento` datetime DEFAULT NULL COMMENT 'Data do último pagamento automático realizado',
    `pagamento_automatico` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = ativo, 0 = inativo',
    `proximo_pagamento` datetime DEFAULT NULL COMMENT 'Data do próximo pagamento programado',
    `tipo_repeticao` enum('mensal','semanal','quinzenal') NOT NULL DEFAULT 'mensal',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `tipo_valor_base` varchar(20) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_config_carteira_usuario` (`carteira_usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `transacoes_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `transacoes_usuario` (
    `idTransacoesUsuario` int NOT NULL AUTO_INCREMENT,
    `tipo` enum('salario','bonus','comissao','retirada') NOT NULL,
    `valor` decimal(10,2) NOT NULL,
    `data_transacao` datetime NOT NULL,
    `descricao` varchar(255) DEFAULT NULL,
    `codigo_pix` varchar(255) DEFAULT NULL,
    `carteira_usuario_id` int NOT NULL,
    `considerado_saldo` tinyint(1) DEFAULT '0',
    `saldo_acumulado` decimal(10,2) DEFAULT '0.00',
    PRIMARY KEY (`idTransacoesUsuario`),
    KEY `fk_transacoes_usuario_carteira_usuario1_idx` (`carteira_usuario_id`),
    CONSTRAINT `fk_transacoes_usuario_carteira_usuario1`
        FOREIGN KEY (`carteira_usuario_id`)
        REFERENCES `carteira_usuario` (`idCarteiraUsuario`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `usuarios_fixados`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios_fixados` (
    `idUsuarioFixado` int NOT NULL AUTO_INCREMENT,
    `usuario_id` int NOT NULL,
    `usuario_fixador_id` int NOT NULL,
    `data_fixacao` datetime NOT NULL,
    `status` tinyint(1) DEFAULT '1',
    PRIMARY KEY (`idUsuarioFixado`),
    KEY `fk_usuarios_fixados_usuario_idx` (`usuario_id`),
    KEY `fk_usuarios_fixados_usuario_fixador_idx` (`usuario_fixador_id`),
    CONSTRAINT `fk_usuarios_fixados_usuario`
        FOREIGN KEY (`usuario_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_usuarios_fixados_usuario_fixador`
        FOREIGN KEY (`usuario_fixador_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `clientes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
    `idClientes` int NOT NULL AUTO_INCREMENT,
    `asaas_id` varchar(255) DEFAULT NULL,
    `nomeCliente` varchar(255) NOT NULL,
    `sexo` varchar(20) DEFAULT NULL,
    `pessoa_fisica` tinyint(1) NOT NULL DEFAULT '1',
    `documento` varchar(20) NOT NULL,
    `telefone` varchar(20) NOT NULL,
    `celular` varchar(20) DEFAULT NULL,
    `email` varchar(100) NOT NULL,
    `senha` varchar(200) NOT NULL,
    `dataCadastro` date DEFAULT NULL,
    `rua` varchar(70) DEFAULT NULL,
    `numero` varchar(15) DEFAULT NULL,
    `bairro` varchar(45) DEFAULT NULL,
    `cidade` varchar(45) DEFAULT NULL,
    `estado` varchar(20) DEFAULT NULL,
    `cep` varchar(20) DEFAULT NULL,
    `contato` varchar(45) DEFAULT NULL,
    `complemento` varchar(45) DEFAULT NULL,
    `fornecedor` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`idClientes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `resets_de_senha`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `resets_de_senha` (
    `id` int NOT NULL AUTO_INCREMENT,
    `email` varchar(200) NOT NULL,
    `token` varchar(255) NOT NULL,
    `data_expiracao` datetime NOT NULL,
    `token_utilizado` tinyint NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `categorias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
    `idCategorias` int NOT NULL AUTO_INCREMENT,
    `categoria` varchar(80) DEFAULT NULL,
    `cadastro` date DEFAULT NULL,
    `status` tinyint(1) DEFAULT NULL,
    `tipo` varchar(15) DEFAULT NULL,
    PRIMARY KEY (`idCategorias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `contas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `contas` (
    `idContas` int NOT NULL AUTO_INCREMENT,
    `conta` varchar(45) DEFAULT NULL,
    `banco` varchar(45) DEFAULT NULL,
    `numero` varchar(45) DEFAULT NULL,
    `saldo` decimal(10,2) DEFAULT NULL,
    `cadastro` date DEFAULT NULL,
    `status` tinyint(1) DEFAULT NULL,
    `tipo` varchar(80) DEFAULT NULL,
    PRIMARY KEY (`idContas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `lancamentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lancamentos` (
    `idLancamentos` int NOT NULL AUTO_INCREMENT,
    `descricao` varchar(255) DEFAULT NULL,
    `valor` decimal(10,2) DEFAULT '0.00',
    `desconto` decimal(10,2) DEFAULT '0.00',
    `valor_desconto` decimal(10,2) DEFAULT '0.00',
    `tipo_desconto` varchar(8) DEFAULT NULL,
    `data_vencimento` date NOT NULL,
    `data_pagamento` date DEFAULT NULL,
    `baixado` tinyint(1) DEFAULT '0',
    `cliente_fornecedor` varchar(255) DEFAULT NULL,
    `forma_pgto` varchar(100) DEFAULT NULL,
    `tipo` varchar(45) DEFAULT NULL,
    `anexo` varchar(250) DEFAULT NULL,
    `observacoes` text,
    `clientes_id` int DEFAULT NULL,
    `categorias_id` int DEFAULT NULL,
    `contas_id` int DEFAULT NULL,
    `vendas_id` int DEFAULT NULL,
    `usuarios_id` int NOT NULL,
    `os_id` int DEFAULT NULL,
    PRIMARY KEY (`idLancamentos`),
    KEY `fk_lancamentos_clientes1` (`clientes_id`),
    KEY `fk_lancamentos_categorias1_idx` (`categorias_id`),
    KEY `fk_lancamentos_contas1_idx` (`contas_id`),
    KEY `fk_lancamentos_usuarios1` (`usuarios_id`),
    KEY `fk_lancamentos_os` (`os_id`),
    CONSTRAINT `fk_lancamentos_clientes1`
        FOREIGN KEY (`clientes_id`)
        REFERENCES `clientes` (`idClientes`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_lancamentos_categorias1`
        FOREIGN KEY (`categorias_id`)
        REFERENCES `categorias` (`idCategorias`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_lancamentos_contas1`
        FOREIGN KEY (`contas_id`)
        REFERENCES `contas` (`idContas`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_lancamentos_usuarios1`
        FOREIGN KEY (`usuarios_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_lancamentos_os`
        FOREIGN KEY (`os_id`)
        REFERENCES `os` (`idOs`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `forma_pagamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_pagamento` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nome` varchar(255) DEFAULT NULL,
    `pct` decimal(5,2) DEFAULT NULL,
    `pct_parcela` decimal(5,2) DEFAULT NULL,
    `qtd_parcelas` int DEFAULT NULL,
    `ativa` tinyint(1) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `garantias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `garantias` (
    `idGarantias` int NOT NULL AUTO_INCREMENT,
    `dataGarantia` date DEFAULT NULL,
    `refGarantia` varchar(15) DEFAULT NULL,
    `textoGarantia` text,
    `usuarios_id` int DEFAULT NULL,
    PRIMARY KEY (`idGarantias`),
    KEY `fk_garantias_usuarios1` (`usuarios_id`),
    CONSTRAINT `fk_garantias_usuarios1`
        FOREIGN KEY (`usuarios_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os` (
    `idOs` int NOT NULL AUTO_INCREMENT,
    `dataInicial` date DEFAULT NULL,
    `dataFinal` date DEFAULT NULL,
    `garantia` varchar(45) DEFAULT NULL,
    `descricaoProduto` text,
    `marcaProdutoOs` text,
    `modeloProdutoOs` text,
    `nsProdutoOs` text,
    `ucProdutoOs` text,
    `contrato_seguradora` text,
    `imagemProdutoOs` text,
    `localizacaoProdutoOs` varchar(80) DEFAULT NULL,
    `analiseBasica` text,
    `defeito` text,
    `status` varchar(45) DEFAULT NULL,
    `observacoes` text,
    `laudoTecnico` text,
    `valorTotal` decimal(10,2) DEFAULT '0.00',
    `desconto` decimal(10,2) DEFAULT '0.00',
    `valor_desconto` decimal(10,2) DEFAULT '0.00',
    `tipo_desconto` varchar(8) DEFAULT NULL,
    `clientes_id` int NOT NULL,
    `usuarios_id` int NOT NULL,
    `lancamento` int DEFAULT NULL,
    `faturado` tinyint(1) NOT NULL,
    `garantias_id` int DEFAULT NULL,
    `organizador_id` int DEFAULT NULL,
    `compartimento_id` int DEFAULT NULL,
    PRIMARY KEY (`idOs`),
    KEY `fk_os_clientes1` (`clientes_id`),
    KEY `fk_os_usuarios1` (`usuarios_id`),
    KEY `fk_os_lancamentos1` (`lancamento`),
    KEY `fk_os_garantias1` (`garantias_id`),
    KEY `fk_os_organizador` (`organizador_id`),
    KEY `fk_os_compartimento` (`compartimento_id`),
    CONSTRAINT `fk_os_clientes1`
        FOREIGN KEY (`clientes_id`)
        REFERENCES `clientes` (`idClientes`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_os_lancamentos1`
        FOREIGN KEY (`lancamento`)
        REFERENCES `lancamentos` (`idLancamentos`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_os_usuarios1`
        FOREIGN KEY (`usuarios_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_os_organizador`
        FOREIGN KEY (`organizador_id`)
        REFERENCES `organizadores` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_os_compartimento`
        FOREIGN KEY (`compartimento_id`)
        REFERENCES `compartimentos` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `compartimento_equipamentos`
-- -----------------------------------------------------
CREATE TABLE compartimento_equipamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    compartimento_id INT,
    os_id INT,
    produtos_id INT,
    data_entrada DATETIME,
    FOREIGN KEY (compartimento_id) REFERENCES compartimentos(id),
    FOREIGN KEY (os_id) REFERENCES os(id),
    FOREIGN KEY (produtos_id) REFERENCES produtos(id)
);

-- -----------------------------------------------------
-- Table `log_compartimentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `log_compartimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compartimento_id` int(11) NOT NULL,
  `acao` varchar(20) NOT NULL,
  `item_id` int(11) NOT NULL,
  `tipo_item` varchar(20) NOT NULL,
  `data_alteracao` datetime NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compartimento_id` (`compartimento_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- -----------------------------------------------------
-- Table `aver_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aver_os` (
    `idAver` int NOT NULL AUTO_INCREMENT,
    `os_id` int NOT NULL,
    `valor` decimal(10,2) NOT NULL,
    `data_pagamento` datetime NOT NULL,
    `status` enum('pago','pendente') NOT NULL DEFAULT 'pago',
    `usuarios_id` int DEFAULT NULL,
    `data_criacao` datetime NOT NULL,
    PRIMARY KEY (`idAver`),
    KEY `fk_aver_os_os1` (`os_id`),
    KEY `fk_aver_os_usuarios1` (`usuarios_id`),
    CONSTRAINT `fk_aver_os_os1`
        FOREIGN KEY (`os_id`)
        REFERENCES `os` (`idOs`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_aver_os_usuarios1`
        FOREIGN KEY (`usuarios_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `aver_comissoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aver_comissoes` (
    `idAverComissao` int NOT NULL AUTO_INCREMENT,
    `aver_id` int NOT NULL,
    `usuario_id` int NOT NULL,
    `valor_comissao` decimal(10,2) NOT NULL,
    `data_pagamento` datetime NOT NULL,
    `status` enum('pago','pendente') NOT NULL DEFAULT 'pago',
    `carteira_usuario_id` int NOT NULL,
    PRIMARY KEY (`idAverComissao`),
    KEY `fk_aver_comissoes_aver_os1` (`aver_id`),
    KEY `fk_aver_comissoes_usuarios1` (`usuario_id`),
    KEY `fk_aver_comissoes_carteira_usuario1` (`carteira_usuario_id`),
    CONSTRAINT `fk_aver_comissoes_aver_os1`
        FOREIGN KEY (`aver_id`)
        REFERENCES `aver_os` (`idAver`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_aver_comissoes_usuarios1`
        FOREIGN KEY (`usuario_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_aver_comissoes_carteira_usuario1`
        FOREIGN KEY (`carteira_usuario_id`)
        REFERENCES `carteira_usuario` (`idCarteiraUsuario`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_usuarios`
-- -----------------------------------------------------    
CREATE TABLE IF NOT EXISTS `os_usuarios` (
    `id` int NOT NULL AUTO_INCREMENT,
    `os_id` int NOT NULL,
    `usuario_id` int NOT NULL,
    `funcao` varchar(50) DEFAULT NULL,
    `data_adicao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `principal` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `fk_os_usuarios_os1` (`os_id`),
    KEY `fk_os_usuarios_usuarios1` (`usuario_id`),
    CONSTRAINT `fk_os_usuarios_os1` 
        FOREIGN KEY (`os_id`) 
        REFERENCES `os` (`idOs`) 
        ON DELETE CASCADE 
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_os_usuarios_usuarios1` 
        FOREIGN KEY (`usuario_id`) 
        REFERENCES `usuarios` (`idUsuarios`) 
        ON DELETE NO ACTION 
        ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `status_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `status_os` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nome_status` varchar(255) NOT NULL,
    `descricao` varchar(255) NOT NULL,
    `ativa` tinyint(1) DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `usuario_status_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario_status_os` (
    `status_id` int NOT NULL,
    `usuario_id` int NOT NULL,
    PRIMARY KEY (`status_id`, `usuario_id`),
    KEY `usuario_id` (`usuario_id`),
    CONSTRAINT `usuario_status_os_ibfk_1`
        FOREIGN KEY (`status_id`)
        REFERENCES `status_os` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `usuario_status_os_ibfk_2`
        FOREIGN KEY (`usuario_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `modelo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `modelo` (
    `idModelo` int NOT NULL AUTO_INCREMENT,
    `nomeModelo` varchar(80) NOT NULL,
    PRIMARY KEY (`idModelo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `compativeis`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `compativeis` (
    `idCompativel` int NOT NULL AUTO_INCREMENT,
    `modeloCompativel` varchar(80) NOT NULL,
    PRIMARY KEY (`idCompativel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `condicoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `condicoes` (
    `idCondicao` int NOT NULL AUTO_INCREMENT,
    `descricaoCondicao` varchar(50) NOT NULL,
    PRIMARY KEY (`idCondicao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `direcao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `direcao` (
    `idDirecao` int NOT NULL AUTO_INCREMENT,
    `descricaoDirecao` varchar(50) NOT NULL,
    PRIMARY KEY (`idDirecao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `organizadores`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `organizadores` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nome_organizador` varchar(255) NOT NULL,
    `localizacao` varchar(255) DEFAULT NULL,
    `ativa` tinyint(1) DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `compartimentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `compartimentos` (
    `id` int NOT NULL AUTO_INCREMENT,
    `organizador_id` int NOT NULL,
    `nome_compartimento` varchar(255) NOT NULL,
    `ativa` tinyint(1) DEFAULT '1',
    PRIMARY KEY (`id`),
    KEY `organizador_id` (`organizador_id`),
    CONSTRAINT `compartimentos_ibfk_1`
        FOREIGN KEY (`organizador_id`)
        REFERENCES `organizadores` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `produtos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `produtos` (
    `idProdutos` int NOT NULL AUTO_INCREMENT,
    `codDeBarra` varchar(70) NOT NULL,
    `descricao` varchar(80) NOT NULL,
    `marcaProduto` varchar(80) NOT NULL,
    `idModelo` int NOT NULL,
    `codigoPeca` varchar(80) NOT NULL,
    `nsProduto` varchar(80) NOT NULL,
    `localizacaoProduto` varchar(80) DEFAULT NULL,
    `unidade` varchar(10) DEFAULT NULL,
    `precoCompra` decimal(10,2) DEFAULT NULL,
    `precoVenda` decimal(10,2) NOT NULL,
    `estoque` int NOT NULL,
    `estoqueMinimo` int DEFAULT NULL,
    `saida` tinyint(1) DEFAULT NULL,
    `entrada` tinyint(1) DEFAULT NULL,
    `organizador_id` int DEFAULT NULL,
    `compartimento_id` int DEFAULT NULL,
    `idCondicao` int DEFAULT NULL,
    `idDirecao` int DEFAULT NULL,
    `dataPedido` date DEFAULT NULL,
    `dataChegada` date DEFAULT NULL,
    `idCompativel` int DEFAULT NULL,
    `numeroPeca` varchar(80) DEFAULT NULL,
    PRIMARY KEY (`idProdutos`),
    KEY `idModelo` (`idModelo`),
    KEY `idCondicao` (`idCondicao`),
    KEY `idDirecao` (`idDirecao`),
    KEY `idCompativel` (`idCompativel`),
    KEY `organizador_id` (`organizador_id`),
    KEY `compartimento_id` (`compartimento_id`),
    CONSTRAINT `produtos_ibfk_1`
        FOREIGN KEY (`idModelo`)
        REFERENCES `modelo` (`idModelo`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `produtos_ibfk_2`
        FOREIGN KEY (`idCondicao`)
        REFERENCES `condicoes` (`idCondicao`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `produtos_ibfk_3`
        FOREIGN KEY (`idDirecao`)
        REFERENCES `direcao` (`idDirecao`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `produtos_ibfk_4`
        FOREIGN KEY (`idCompativel`)
        REFERENCES `compativeis` (`idCompativel`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `produtos_ibfk_5`
        FOREIGN KEY (`organizador_id`)
        REFERENCES `organizadores` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `produtos_ibfk_6`
        FOREIGN KEY (`compartimento_id`)
        REFERENCES `compartimentos` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `produto_compativel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `produto_compativel` (
    `idProdutoCompativel` int NOT NULL AUTO_INCREMENT,
    `idProduto` int DEFAULT NULL,
    `idCompativel` int DEFAULT NULL,
    PRIMARY KEY (`idProdutoCompativel`),
    KEY `idProduto` (`idProduto`),
    KEY `idCompativel` (`idCompativel`),
    CONSTRAINT `produto_compativel_ibfk_1`
        FOREIGN KEY (`idProduto`)
        REFERENCES `produtos` (`idProdutos`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `produto_compativel_ibfk_2`
        FOREIGN KEY (`idCompativel`)
        REFERENCES `compativeis` (`idCompativel`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `imagens_produto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `imagens_produto` (
    `idImagem` int NOT NULL AUTO_INCREMENT,
    `anexo` varchar(45) DEFAULT NULL,
    `thumb` varchar(45) DEFAULT NULL,
    `urlImagem` varchar(300) DEFAULT NULL,
    `path` varchar(300) DEFAULT NULL,
    `produto_id` int NOT NULL,
    PRIMARY KEY (`idImagem`),
    KEY `fk_img_produto1` (`produto_id`),
    CONSTRAINT `fk_img_produto1`
        FOREIGN KEY (`produto_id`)
        REFERENCES `produtos` (`idProdutos`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `produtos_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `produtos_os` (
    `idProdutos_os` int NOT NULL AUTO_INCREMENT,
    `quantidade` int NOT NULL,
    `descricao` varchar(80) DEFAULT NULL,
    `marcaProduto` varchar(80) DEFAULT NULL,
    `modeloProduto` varchar(80) DEFAULT NULL,
    `nsProduto` varchar(80) DEFAULT NULL,
    `condicaoProduto` varchar(80) DEFAULT NULL,
    `localizacaoProduto` varchar(80) DEFAULT NULL,
    `preco` decimal(10,2) DEFAULT '0.00',
    `os_id` int NOT NULL,
    `produtos_id` int NOT NULL,
    `subTotal` decimal(10,2) DEFAULT '0.00',
    PRIMARY KEY (`idProdutos_os`),
    KEY `fk_produtos_os_os1` (`os_id`),
    KEY `fk_produtos_os_produtos1` (`produtos_id`),
    CONSTRAINT `fk_produtos_os_os1`
        FOREIGN KEY (`os_id`)
        REFERENCES `os` (`idOs`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_produtos_os_produtos1`
        FOREIGN KEY (`produtos_id`)
        REFERENCES `produtos` (`idProdutos`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `servicos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `servicos` (
    `idServicos` int NOT NULL AUTO_INCREMENT,
    `nome` varchar(45) NOT NULL,
    `descricao` varchar(45) DEFAULT NULL,
    `preco` decimal(10,2) NOT NULL,
    PRIMARY KEY (`idServicos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `servicos_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `servicos_os` (
    `idServicos_os` int NOT NULL AUTO_INCREMENT,
    `servico` varchar(80) DEFAULT NULL,
    `quantidade` double DEFAULT NULL,
    `preco` decimal(10,2) DEFAULT '0.00',
    `os_id` int NOT NULL,
    `servicos_id` int NOT NULL,
    `subTotal` decimal(10,2) DEFAULT '0.00',
    PRIMARY KEY (`idServicos_os`),
    KEY `fk_servicos_os_os1` (`os_id`),
    KEY `fk_servicos_os_servicos1` (`servicos_id`),
    CONSTRAINT `fk_servicos_os_os1`
        FOREIGN KEY (`os_id`)
        REFERENCES `os` (`idOs`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_servicos_os_servicos1`
        FOREIGN KEY (`servicos_id`)
        REFERENCES `servicos` (`idServicos`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `vendas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendas` (
    `idVendas` int NOT NULL AUTO_INCREMENT,
    `dataVenda` date DEFAULT NULL,
    `valorTotal` decimal(10,2) DEFAULT '0.00',
    `desconto` decimal(10,2) DEFAULT '0.00',
    `valor_desconto` decimal(10,2) DEFAULT '0.00',
    `tipo_desconto` varchar(8) DEFAULT NULL,
    `faturado` tinyint(1) DEFAULT NULL,
    `observacoes` text,
    `observacoes_cliente` text,
    `clientes_id` int NOT NULL,
    `usuarios_id` int DEFAULT NULL,
    `lancamentos_id` int DEFAULT NULL,
    `status` varchar(45) DEFAULT NULL,
    `garantia` int DEFAULT NULL,
    `garantias_id` int DEFAULT NULL,
    PRIMARY KEY (`idVendas`),
    KEY `fk_vendas_clientes1` (`clientes_id`),
    KEY `fk_vendas_usuarios1` (`usuarios_id`),
    KEY `fk_vendas_lancamentos1` (`lancamentos_id`),
    CONSTRAINT `fk_vendas_clientes1`
        FOREIGN KEY (`clientes_id`)
        REFERENCES `clientes` (`idClientes`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_vendas_usuarios1`
        FOREIGN KEY (`usuarios_id`)
        REFERENCES `usuarios` (`idUsuarios`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_vendas_lancamentos1`
        FOREIGN KEY (`lancamentos_id`)
        REFERENCES `lancamentos` (`idLancamentos`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `cobrancas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cobrancas` (
    `idCobranca` int NOT NULL AUTO_INCREMENT,
    `charge_id` varchar(255) DEFAULT NULL,
    `conditional_discount_date` date DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `custom_id` int DEFAULT NULL,
    `expire_at` date NOT NULL,
    `message` varchar(255) NOT NULL,
    `payment_method` varchar(11) DEFAULT NULL,
    `payment_url` varchar(255) DEFAULT NULL,
    `request_delivery_address` varchar(64) DEFAULT NULL,
    `status` varchar(36) NOT NULL,
    `total` varchar(15) DEFAULT NULL,
    `barcode` varchar(255) NOT NULL,
    `link` varchar(255) NOT NULL,
    `payment_gateway` varchar(255) DEFAULT NULL,
    `payment` varchar(64) NOT NULL,
    `pdf` varchar(255) DEFAULT NULL,
    `vendas_id` int DEFAULT NULL,
    `os_id` int DEFAULT NULL,
    `clientes_id` int DEFAULT NULL,
    PRIMARY KEY (`idCobranca`),
    KEY `fk_cobrancas_os1` (`os_id`),
    KEY `fk_cobrancas_vendas1` (`vendas_id`),
    KEY `fk_cobrancas_clientes1` (`clientes_id`),
    CONSTRAINT `fk_cobrancas_os1`
        FOREIGN KEY (`os_id`)
        REFERENCES `os` (`idOs`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_cobrancas_vendas1`
        FOREIGN KEY (`vendas_id`)
        REFERENCES `vendas` (`idVendas`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_cobrancas_clientes1`
        FOREIGN KEY (`clientes_id`)
        REFERENCES `clientes` (`idClientes`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `itens_de_vendas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itens_de_vendas` (
    `idItens` int NOT NULL AUTO_INCREMENT,
    `subTotal` decimal(10,2) DEFAULT '0.00',
    `quantidade` int DEFAULT NULL,
    `preco` decimal(10,2) DEFAULT '0.00',
    `vendas_id` int NOT NULL,
    `produtos_id` int NOT NULL,
    PRIMARY KEY (`idItens`),
    KEY `fk_itens_de_vendas_vendas1` (`vendas_id`),
    KEY `fk_itens_de_vendas_produtos1` (`produtos_id`),
    CONSTRAINT `fk_itens_de_vendas_vendas1`
        FOREIGN KEY (`vendas_id`)
        REFERENCES `vendas` (`idVendas`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_itens_de_vendas_produtos1`
        FOREIGN KEY (`produtos_id`)
        REFERENCES `produtos` (`idProdutos`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `anexos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `anexos` (
    `idAnexos` int NOT NULL AUTO_INCREMENT,
    `anexo` varchar(45) DEFAULT NULL,
    `thumb` varchar(45) DEFAULT NULL,
    `url` varchar(300) DEFAULT NULL,
    `path` varchar(300) DEFAULT NULL,
    `os_id` int NOT NULL,
    PRIMARY KEY (`idAnexos`),
    KEY `fk_anexos_os1` (`os_id`),
    CONSTRAINT `fk_anexos_os1`
        FOREIGN KEY (`os_id`)
        REFERENCES `os` (`idOs`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `documentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `documentos` (
    `idDocumentos` int NOT NULL AUTO_INCREMENT,
    `documento` varchar(70) DEFAULT NULL,
    `descricao` text,
    `file` varchar(100) DEFAULT NULL,
    `path` varchar(300) DEFAULT NULL,
    `url` varchar(300) DEFAULT NULL,
    `cadastro` date DEFAULT NULL,
    `categoria` varchar(80) DEFAULT NULL,
    `tipo` varchar(15) DEFAULT NULL,
    `tamanho` varchar(45) DEFAULT NULL,
    PRIMARY KEY (`idDocumentos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `marcas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `marcas` (
    `idMarcas` int NOT NULL AUTO_INCREMENT,
    `marca` varchar(100) DEFAULT NULL,
    `cadastro` date DEFAULT NULL,
    `situacao` tinyint(1) DEFAULT NULL,
    PRIMARY KEY (`idMarcas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `equipamentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `equipamentos` (
    `idEquipamentos` int NOT NULL AUTO_INCREMENT,
    `equipamento` varchar(150) NOT NULL,
    `num_serie` varchar(80) DEFAULT NULL,
    `modelo` varchar(80) DEFAULT NULL,
    `cor` varchar(45) DEFAULT NULL,
    `descricao` varchar(150) DEFAULT NULL,
    `tensao` varchar(45) DEFAULT NULL,
    `potencia` varchar(45) DEFAULT NULL,
    `voltagem` varchar(45) DEFAULT NULL,
    `data_fabricacao` date DEFAULT NULL,
    `marcas_id` int DEFAULT NULL,
    `clientes_id` int DEFAULT NULL,
    PRIMARY KEY (`idEquipamentos`),
    KEY `fk_equipanentos_marcas1_idx` (`marcas_id`),
    KEY `fk_equipanentos_clientes1_idx` (`clientes_id`),
    CONSTRAINT `fk_equipanentos_marcas1`
        FOREIGN KEY (`marcas_id`)
        REFERENCES `marcas` (`idMarcas`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_equipanentos_clientes1`
        FOREIGN KEY (`clientes_id`)
        REFERENCES `clientes` (`idClientes`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `equipamentos_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `equipamentos_os` (
    `idEquipamentos_os` int NOT NULL AUTO_INCREMENT,
    `defeito_declarado` varchar(200) DEFAULT NULL,
    `defeito_encontrado` varchar(200) DEFAULT NULL,
    `solucao` varchar(45) DEFAULT NULL,
    `equipamentos_id` int DEFAULT NULL,
    `os_id` int DEFAULT NULL,
    PRIMARY KEY (`idEquipamentos_os`),
    KEY `fk_equipamentos_os_equipanentos1_idx` (`equipamentos_id`),
    KEY `fk_equipamentos_os_os1_idx` (`os_id`),
    CONSTRAINT `fk_equipamentos_os_equipanentos1`
        FOREIGN KEY (`equipamentos_id`)
        REFERENCES `equipamentos` (`idEquipamentos`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_equipamentos_os_os1`
        FOREIGN KEY (`os_id`)
        REFERENCES `os` (`idOs`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `logs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `logs` (
    `idLogs` int NOT NULL AUTO_INCREMENT,
    `usuario` varchar(80) DEFAULT NULL,
    `tarefa` varchar(100) DEFAULT NULL,
    `data` date DEFAULT NULL,
    `hora` time DEFAULT NULL,
    `ip` varchar(45) DEFAULT NULL,
    PRIMARY KEY (`idLogs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `emitente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `emitente` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nome` varchar(255) DEFAULT NULL,
    `cnpj` varchar(45) DEFAULT NULL,
    `ie` varchar(50) DEFAULT NULL,
    `rua` varchar(70) DEFAULT NULL,
    `numero` varchar(15) DEFAULT NULL,
    `bairro` varchar(45) DEFAULT NULL,
    `cidade` varchar(45) DEFAULT NULL,
    `uf` varchar(20) DEFAULT NULL,
    `telefone` varchar(20) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `url_logo` varchar(225) DEFAULT NULL,
    `url_carimbo` varchar(225) DEFAULT NULL,
    `cep` varchar(20) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `email_queue`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_queue` (
    `id` int NOT NULL AUTO_INCREMENT,
    `to` varchar(255) NOT NULL,
    `cc` varchar(255) DEFAULT NULL,
    `bcc` varchar(255) DEFAULT NULL,
    `message` text NOT NULL,
    `status` enum('pending','sending','sent','failed') DEFAULT NULL,
    `date` datetime DEFAULT NULL,
    `headers` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `anotacoes_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `anotacoes_os` (
    `idAnotacoes` int NOT NULL AUTO_INCREMENT,
    `anotacao` varchar(255) NOT NULL,
    `data_hora` datetime NOT NULL,
    `os_id` int NOT NULL,
    PRIMARY KEY (`idAnotacoes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `configuracoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `configuracoes` (
    `idConfig` int NOT NULL AUTO_INCREMENT,
    `config` varchar(20) NOT NULL,
    `valor` text,
    PRIMARY KEY (`idConfig`),
    UNIQUE KEY `config` (`config`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `migrations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
    `version` bigint NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Inserções iniciais
-- -----------------------------------------------------
INSERT IGNORE INTO `configuracoes` (`idConfig`, `config`, `valor`) VALUES
(2, 'app_name', 'Map-OS'),
(3, 'app_theme', 'white'),
(4, 'per_page', '10'),
(5, 'os_notification', 'cliente'),
(6, 'control_estoque', '1'),
(7, 'notifica_whats', 'Prezado(a), {CLIENTE_NOME} a OS de nº {NUMERO_OS} - {DESCRI_PRODUTOS} - {MARCA_OS_PRODUTOS} - Teve seu status alterado para: {STATUS_OS},  Valor: {VALOR_OS}, Atenciosamente: {TELEFONE_EMITENTE}.'),
(8, 'control_baixa', '0'),
(9, 'control_editos', '1'),
(10, 'control_datatable', '1'),
(11, 'pix_key', ''),
(12, 'os_status_list', '[\"Aberto\",\"Faturado\",\"Negocia\\u00e7\\u00e3o\",\"Em Andamento\",\"Or\\u00e7amento\",\"Finalizado\",\"Cancelado\",\"Aguardando Pe\\u00e7as\",\"Aprovado\"]'),
(13, 'control_edit_vendas', '1'),
(14, 'email_automatico', '1'),
(15, 'control_2vias', '0');

INSERT IGNORE INTO `permissoes` (`idPermissao`, `nome`, `permissoes`, `situacao`, `data`) VALUES
(1, 'Administrador', 'a:66:{s:12:"vOrganizador";s:1:"1";s:12:"aOrganizador";s:1:"1";s:12:"eOrganizador";s:1:"1";s:12:"dOrganizador";s:1:"1";s:9:"vCarteira";s:1:"1";s:9:"aCarteira";s:1:"1";s:9:"eCarteira";s:1:"1";s:9:"dCarteira";s:1:"1";s:14:"vCarteiraAdmin";s:1:"1";s:14:"aCarteiraAdmin";s:1:"1";s:14:"eCarteiraAdmin";s:1:"1";s:14:"dCarteiraAdmin";s:1:"1";s:14:"pCarteiraAdmin";s:1:"1";s:8:"aCliente";s:1:"1";s:8:"eCliente";s:1:"1";s:8:"dCliente";s:1:"1";s:8:"vCliente";s:1:"1";s:8:"aProduto";s:1:"1";s:8:"eProduto";s:1:"1";s:8:"dProduto";s:1:"1";s:8:"vProduto";s:1:"1";s:8:"aServico";s:1:"1";s:8:"eServico";s:1:"1";s:8:"dServico";s:1:"1";s:8:"vServico";s:1:"1";s:3:"aOs";s:1:"1";s:3:"eOs";s:1:"1";s:3:"dOs";s:1:"1";s:3:"vOs";s:1:"1";s:6:"aVenda";s:1:"1";s:6:"eVenda";s:1:"1";s:6:"dVenda";s:1:"1";s:6:"vVenda";s:1:"1";s:9:"aGarantia";s:1:"1";s:9:"eGarantia";s:1:"1";s:9:"dGarantia";s:1:"1";s:9:"vGarantia";s:1:"1";s:8:"aArquivo";s:1:"1";s:8:"eArquivo";s:1:"1";s:8:"dArquivo";s:1:"1";s:8:"vArquivo";s:1:"1";s:10:"aPagamento";N;s:10:"ePagamento";N;s:10:"dPagamento";N;s:10:"vPagamento";N;s:11:"aLancamento";s:1:"1";s:11:"eLancamento";s:1:"1";s:11:"dLancamento";s:1:"1";s:11:"vLancamento";s:1:"1";s:8:"cUsuario";s:1:"1";s:9:"cEmitente";s:1:"1";s:10:"cPermissao";s:1:"1";s:7:"cBackup";s:1:"1";s:10:"cAuditoria";s:1:"1";s:6:"cEmail";s:1:"1";s:8:"cSistema";s:1:"1";s:8:"rCliente";s:1:"1";s:8:"rProduto";s:1:"1";s:8:"rServico";s:1:"1";s:3:"rOs";s:1:"1";s:6:"rVenda";s:1:"1";s:11:"rFinanceiro";s:1:"1";s:9:"aCobranca";s:1:"1";s:9:"eCobranca";s:1:"1";s:9:"dCobranca";s:1:"1";s:9:"vCobranca";s:1:"1";}', 1, '2025-03-10');


INSERT IGNORE INTO `usuarios` (`idUsuarios`, `nome`, `rg`, `cpf`, `cep`, `rua`, `numero`, `bairro`, `cidade`, `estado`, `email`, `senha`, `telefone`, `celular`, `situacao`, `dataCadastro`, `permissoes_id`,`dataExpiracao`) VALUES
(1, 'admin_name', 'admin_rg', 'admin_cpf', '70005-115', 'Rua Acima', '12', 'Alvorada', 'Teste', 'MG', 'admin_email', 'admin_password', '000000-0000', '', 1, 'admin_created_at', 1, '3000-01-01');

INSERT IGNORE INTO `migrations` (`version`) VALUES (20210125173741);

-- -----------------------------------------------------
-- Restaurar configurações
-- -----------------------------------------------------
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @saldo := 0;
UPDATE transacoes_usuario 
SET saldo_acumulado = (@saldo := @saldo + 
    CASE 
        WHEN tipo = 'retirada' THEN -valor 
        ELSE valor 
    END)
ORDER BY data_transacao ASC, idTransacoesUsuario ASC;

UPDATE transacoes_usuario 
SET considerado_saldo = 1 
WHERE tipo IN ('salario', 'bonus', 'comissao', 'retirada');