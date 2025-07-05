-- Corrigir foreign key da tabela clientes para apontar para tipos
ALTER TABLE `clientes` 
DROP FOREIGN KEY `clientes_ibfk_tipo`;

ALTER TABLE `clientes` 
ADD CONSTRAINT `clientes_ibfk_tipo`
FOREIGN KEY (`tipo_id`) 
REFERENCES `tipos`(`id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE; 