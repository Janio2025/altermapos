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