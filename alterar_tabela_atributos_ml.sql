-- Script para alterar a tabela atributos_ml existente
-- Execute este script no seu banco de dados MySQL

-- 1. Primeiro, vamos verificar se a tabela existe e sua estrutura atual
SHOW TABLES LIKE 'atributos_ml';

-- 2. Verificar estrutura atual
DESCRIBE atributos_ml;

-- 3. Adicionar campos que estão faltando (se não existirem)
-- Adicionar campo hierarchy se não existir
ALTER TABLE `atributos_ml` 
ADD COLUMN IF NOT EXISTS `hierarchy` varchar(255) DEFAULT NULL COMMENT 'Hierarquia do atributo';

-- Adicionar campo tags se não existir
ALTER TABLE `atributos_ml` 
ADD COLUMN IF NOT EXISTS `tags` text COMMENT 'Tags associadas ao atributo';

-- Adicionar campo attribute_group_id se não existir
ALTER TABLE `atributos_ml` 
ADD COLUMN IF NOT EXISTS `attribute_group_id` varchar(50) DEFAULT NULL COMMENT 'ID do grupo do atributo';

-- Adicionar campo attribute_group_name se não existir
ALTER TABLE `atributos_ml` 
ADD COLUMN IF NOT EXISTS `attribute_group_name` varchar(255) DEFAULT NULL COMMENT 'Nome do grupo do atributo';

-- Adicionar campo status se não existir
ALTER TABLE `atributos_ml` 
ADD COLUMN IF NOT EXISTS `status` tinyint(1) DEFAULT 1 COMMENT 'Status do atributo (1=ativo, 0=inativo)';

-- Adicionar campo updated_at se não existir
ALTER TABLE `atributos_ml` 
ADD COLUMN IF NOT EXISTS `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 4. Verificar se o campo values é do tipo JSON, se não, alterar
-- Primeiro verificar o tipo atual
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml' 
AND COLUMN_NAME = 'values';

-- Se o campo values não for JSON, alterar para JSON
-- (Descomente a linha abaixo se necessário)
-- ALTER TABLE `atributos_ml` MODIFY COLUMN `values` json DEFAULT NULL COMMENT 'Valores possíveis (para atributos do tipo list)';

-- 5. Adicionar índices se não existirem
-- Índice para categoria_id
CREATE INDEX IF NOT EXISTS `idx_categoria_id` ON `atributos_ml` (`categoria_id`);

-- Índice para ml_attribute_id
CREATE INDEX IF NOT EXISTS `idx_ml_attribute_id` ON `atributos_ml` (`ml_attribute_id`);

-- Índice para required
CREATE INDEX IF NOT EXISTS `idx_required` ON `atributos_ml` (`required`);

-- Índice para status
CREATE INDEX IF NOT EXISTS `idx_status` ON `atributos_ml` (`status`);

-- 6. Adicionar chave única se não existir
-- Primeiro verificar se já existe
SHOW INDEX FROM `atributos_ml` WHERE Key_name = 'categoria_atributo';

-- Se não existir, adicionar
-- ALTER TABLE `atributos_ml` ADD UNIQUE KEY `categoria_atributo` (`categoria_id`, `ml_attribute_id`);

-- 7. Adicionar foreign key se não existir
-- Primeiro verificar se já existe
SELECT CONSTRAINT_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml' 
AND CONSTRAINT_NAME = 'fk_atributos_ml_categoria';

-- Se não existir, adicionar
-- ALTER TABLE `atributos_ml` 
-- ADD CONSTRAINT `fk_atributos_ml_categoria` 
-- FOREIGN KEY (`categoria_id`) 
-- REFERENCES `categorias` (`idCategorias`) 
-- ON DELETE CASCADE 
-- ON UPDATE CASCADE;

-- 8. Verificar estrutura final
DESCRIBE atributos_ml;

-- 9. Verificar se há dados na tabela
SELECT COUNT(*) as total_registros FROM atributos_ml;

-- 10. Mostrar alguns registros de exemplo (se houver)
SELECT * FROM atributos_ml LIMIT 5;

-- 11. Verificar se os índices foram criados
SHOW INDEX FROM atributos_ml; 