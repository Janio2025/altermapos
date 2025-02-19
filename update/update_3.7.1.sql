

-- minhas alterações

ALTER TABLE `produtos` ADD `organizador_id` INT NOT NULL, ADD `compartimento_id` INT NULL;

CREATE TABLE IF NOT EXISTS `organizadores` (
  `id` INT AUTO_INCREMENT,
  `nome_organizador` VARCHAR(255) NOT NULL,
  `localizacao` VARCHAR(255) NULL,
  `ativa` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `compartimentos` (
  `id` INT AUTO_INCREMENT,
  `organizador_id` INT NOT NULL,
  `nome_compartimento` VARCHAR(255) NOT NULL,
  `ativa` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`organizador_id`) REFERENCES `organizadores`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
