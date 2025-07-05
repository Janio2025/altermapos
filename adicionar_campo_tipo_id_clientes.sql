-- Adicionar campo tipo_id na tabela clientes
ALTER TABLE `clientes` 
ADD COLUMN `tipo_id` INT DEFAULT NULL AFTER `fornecedor`;

-- Adicionar índice para o campo tipo_id
ALTER TABLE `clientes` 
ADD INDEX `tipo_id` (`tipo_id`);

-- Adicionar foreign key para a tabela tipos
ALTER TABLE `clientes` 
ADD CONSTRAINT `clientes_ibfk_tipo`
FOREIGN KEY (`tipo_id`) 
REFERENCES `tipos`(`id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE; 