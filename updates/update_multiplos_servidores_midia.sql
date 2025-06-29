-- Migração para múltiplos servidores de mídia
-- Este script migra os dados existentes de servidor de mídia para a nova estrutura

-- Inserir servidor padrão se existir configuração antiga
INSERT INTO servidores_midia (nome, url, caminho_fisico, ativo, prioridade, data_criacao, data_modificacao)
SELECT 
    'Servidor Principal' as nome,
    valor as url,
    (SELECT valor FROM configuracoes WHERE config = 'media_server_path' LIMIT 1) as caminho_fisico,
    1 as ativo,
    0 as prioridade,
    NOW() as data_criacao,
    NOW() as data_modificacao
FROM configuracoes 
WHERE config = 'media_server_url' 
AND valor IS NOT NULL 
AND valor != ''
AND (SELECT valor FROM configuracoes WHERE config = 'media_server_path' LIMIT 1) IS NOT NULL
AND (SELECT valor FROM configuracoes WHERE config = 'media_server_path' LIMIT 1) != ''
AND NOT EXISTS (SELECT 1 FROM servidores_midia LIMIT 1);

-- Remover as configurações antigas de servidor de mídia apenas se a migração foi bem-sucedida
DELETE FROM configuracoes 
WHERE config IN ('media_server_url', 'media_server_path')
AND EXISTS (SELECT 1 FROM servidores_midia LIMIT 1); 