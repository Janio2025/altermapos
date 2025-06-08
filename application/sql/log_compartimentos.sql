CREATE TABLE IF NOT EXISTS `log_compartimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compartimento_id` int(11) NOT NULL,
  `acao` varchar(20) NOT NULL,
  `item_id` int(11) NOT NULL,
  `tipo_item` varchar(20) NOT NULL,
  `data_alteracao` datetime NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compartimento_id` (`compartimento_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 