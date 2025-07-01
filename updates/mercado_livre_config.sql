-- Configurações padrão para integração com Mercado Livre
-- Execute este arquivo após a instalação das tabelas do Mercado Livre

-- Configurações básicas do sistema para Mercado Livre
INSERT INTO `configuracoes` (`config`, `valor`) VALUES 
('mercado_livre_enabled', 'false'),
('mercado_livre_auto_sync', 'false'),
('mercado_livre_stock_sync', 'true'),
('mercado_livre_price_sync', 'true'),
('mercado_livre_default_condition', 'new'),
('mercado_livre_default_listing_type', 'gold_special'),
('mercado_livre_accepts_mercadoenvios', 'true'),
('mercado_livre_free_shipping', 'false'),
('mercado_livre_warranty', '90'),
('mercado_livre_log_level', 'info')
ON DUPLICATE KEY UPDATE `valor` = VALUES(`valor`);

-- Comentário: Estas configurações serão usadas como padrão
-- e podem ser sobrescritas pelas configurações do arquivo .env 