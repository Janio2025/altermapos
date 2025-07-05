-- Script para alterar a tabela atributos_ml existente (INTELIGENTE)
-- Execute este script no seu banco de dados MySQL

-- 1. Primeiro, vamos verificar se a tabela existe e sua estrutura atual
SHOW TABLES LIKE 'atributos_ml';

-- 2. Verificar estrutura atual
DESCRIBE atributos_ml;

-- 3. Verificar quais campos já existem
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml';

-- 4. Adicionar campos que estão faltando (com verificação)
-- Verificar se hierarchy existe
SELECT COUNT(*) as existe 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml' 
AND COLUMN_NAME = 'hierarchy';

-- Se hierarchy não existir, adicionar
-- (Execute apenas se o resultado acima for 0)
-- ALTER TABLE `atributos_ml` ADD COLUMN `hierarchy` varchar(255) DEFAULT NULL COMMENT 'Hierarquia do atributo';

-- Verificar se tags existe
SELECT COUNT(*) as existe 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml' 
AND COLUMN_NAME = 'tags';

-- Se tags não existir, adicionar
-- (Execute apenas se o resultado acima for 0)
-- ALTER TABLE `atributos_ml` ADD COLUMN `tags` text COMMENT 'Tags associadas ao atributo';

-- Verificar se attribute_group_id existe
SELECT COUNT(*) as existe 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml' 
AND COLUMN_NAME = 'attribute_group_id';

-- Se attribute_group_id não existir, adicionar
-- (Execute apenas se o resultado acima for 0)
-- ALTER TABLE `atributos_ml` ADD COLUMN `attribute_group_id` varchar(50) DEFAULT NULL COMMENT 'ID do grupo do atributo';

-- Verificar se attribute_group_name existe
SELECT COUNT(*) as existe 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml' 
AND COLUMN_NAME = 'attribute_group_name';

-- Se attribute_group_name não existir, adicionar
-- (Execute apenas se o resultado acima for 0)
-- ALTER TABLE `atributos_ml` ADD COLUMN `attribute_group_name` varchar(255) DEFAULT NULL COMMENT 'Nome do grupo do atributo';

-- Verificar se status existe
SELECT COUNT(*) as existe 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml' 
AND COLUMN_NAME = 'status';

-- Se status não existir, adicionar
-- (Execute apenas se o resultado acima for 0)
-- ALTER TABLE `atributos_ml` ADD COLUMN `status` tinyint(1) DEFAULT 1 COMMENT 'Status do atributo (1=ativo, 0=inativo)';

-- Verificar se updated_at existe
SELECT COUNT(*) as existe 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml' 
AND COLUMN_NAME = 'updated_at';

-- Se updated_at não existir, adicionar
-- (Execute apenas se o resultado acima for 0)
-- ALTER TABLE `atributos_ml` ADD COLUMN `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 5. Verificar se o campo values é do tipo JSON
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'atributos_ml' 
AND COLUMN_NAME = 'values';

-- Se o campo values não for JSON, alterar para JSON
-- (Descomente a linha abaixo se DATA_TYPE não for 'json')
-- ALTER TABLE `atributos_ml` MODIFY COLUMN `values` json DEFAULT NULL COMMENT 'Valores possíveis (para atributos do tipo list)';

-- 6. Verificar índices existentes
SHOW INDEX FROM `atributos_ml`;

-- 7. Adicionar índices se não existirem
-- (Execute apenas se os índices não existirem)
-- CREATE INDEX `idx_categoria_id` ON `atributos_ml` (`categoria_id`);
-- CREATE INDEX `idx_ml_attribute_id` ON `atributos_ml` (`ml_attribute_id`);
-- CREATE INDEX `idx_required` ON `atributos_ml` (`required`);
-- CREATE INDEX `idx_status` ON `atributos_ml` (`status`);

-- 8. Verificar estrutura final
DESCRIBE atributos_ml;

-- 9. Verificar se há dados na tabela
SELECT COUNT(*) as total_registros FROM atributos_ml;

-- 10. Mostrar alguns registros de exemplo (se houver)
SELECT * FROM atributos_ml LIMIT 5; 