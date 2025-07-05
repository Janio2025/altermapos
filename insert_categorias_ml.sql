-- Inserir categorias básicas do Mercado Livre para teste
INSERT INTO `categorias` (`ml_id`, `categoria`, `parent_id`, `cadastro`, `status`, `tipo`) VALUES
-- Categorias principais
('MLB5672', 'Acessórios para Veículos', NULL, NOW(), 1, 'mercado_livre'),
('MLB5725', 'Eletrônicos, Áudio e Vídeo', NULL, NOW(), 1, 'mercado_livre'),
('MLB1000', 'Informática', NULL, NOW(), 1, 'mercado_livre'),
('MLB1430', 'Casa, Móveis e Decoração', NULL, NOW(), 1, 'mercado_livre'),
('MLB1500', 'Esportes e Fitness', NULL, NOW(), 1, 'mercado_livre'),
('MLB1648', 'Ferramentas', NULL, NOW(), 1, 'mercado_livre'),
('MLB1953', 'Livros, Revistas e Comics', NULL, NOW(), 1, 'mercado_livre'),
('MLB2184', 'Moda', NULL, NOW(), 1, 'mercado_livre'),
('MLB3025', 'Bebês', NULL, NOW(), 1, 'mercado_livre'),
('MLB3576', 'Brinquedos e Jogos', NULL, NOW(), 1, 'mercado_livre'),
('MLB3937', 'Saúde e Cuidados Pessoais', NULL, NOW(), 1, 'mercado_livre'),
('MLB407134', 'Alimentos e Bebidas', NULL, NOW(), 1, 'mercado_livre'),
('MLB420040', 'Construção', NULL, NOW(), 1, 'mercado_livre'),
('MLB440027', 'Indústria e Comércio', NULL, NOW(), 1, 'mercado_livre'),
('MLB441003', 'Agro', NULL, NOW(), 1, 'mercado_livre'),
('MLB441004', 'Serviços', NULL, NOW(), 1, 'mercado_livre'),
('MLB441005', 'Outros', NULL, NOW(), 1, 'mercado_livre'); 