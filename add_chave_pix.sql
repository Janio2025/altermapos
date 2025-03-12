ALTER TABLE configuracao_carteira 
ADD COLUMN chave_pix VARCHAR(255) DEFAULT NULL 
COMMENT 'Chave PIX para recebimento de saques' 
AFTER tipo_valor_base; 