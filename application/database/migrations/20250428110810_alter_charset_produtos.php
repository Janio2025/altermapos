<?php

class Migration_alter_charset_produtos extends CI_Migration
{
    public function up()
    {
        
        // Adição de colunas na tabela produtos
        $this->db->query('ALTER TABLE `produtos` ADD `organizador_id` INT(11) NULL, ADD `compartimento_id` INT(11) NULL;');
        
        // Adiciona as chaves estrangeiras
        $this->db->query('ALTER TABLE `produtos` 
            ADD CONSTRAINT `fk_produtos_organizador` FOREIGN KEY (`organizador_id`) REFERENCES `organizadores`(`id`) ON DELETE CASCADE,
            ADD CONSTRAINT `fk_produtos_compartimento` FOREIGN KEY (`compartimento_id`) REFERENCES `compartimentos`(`id`) ON DELETE SET NULL;');
    }

    public function down()
    {
        // Remove as chaves estrangeiras caso precise reverter a migração
        $this->db->query('ALTER TABLE `produtos` 
            DROP FOREIGN KEY `fk_produtos_organizador`,
            DROP FOREIGN KEY `fk_produtos_compartimento`;');
        
        // Remover colunas adicionadas
        $this->db->query('ALTER TABLE `produtos` DROP COLUMN `organizador_id`, DROP COLUMN `compartimento_id`;');
        
        
    }
}

