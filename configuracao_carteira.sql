-- Criação da tabela configuracao_carteira
CREATE TABLE IF NOT EXISTS `configuracao_carteira` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `carteira_usuario_id` INT NOT NULL,
    `salario_base` DECIMAL(10,2) NOT NULL,
    `comissao_fixa` DECIMAL(5,2) DEFAULT 0.00,
    `data_salario` INT NOT NULL COMMENT 'Dia do mês (1-31)',
    `tipo_repeticao` ENUM('mensal', 'quinzenal') NOT NULL DEFAULT 'mensal',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_config_carteira_usuario`
        FOREIGN KEY (`carteira_usuario_id`)
        REFERENCES `carteira_usuario` (`idCarteiraUsuario`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4; 