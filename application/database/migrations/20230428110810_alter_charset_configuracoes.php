<?php

class Migration_alter_charset_configuracoes extends CI_Migration
{
    public function up()
    {
        // Alteração na tabela configuracoes
        $this->db->query('ALTER TABLE `configuracoes` CHANGE `config` `config` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;');
        
        // Adição de colunas na tabela produtos
        $this->db->query('ALTER TABLE `produtos` ADD `organizador_id` INT NOT NULL, ADD `compartimento_id` INT NULL;');
        
        // Atualização de valor na tabela configuracoes
        $this->db->query("UPDATE `configuracoes` SET `valor` = 'Prezado(a), {CLIENTE_NOME} a OS de nº {NUMERO_OS} teve o status alterado para: {STATUS_OS} segue a descrição {DESCRI_PRODUTOS} com valor total de {VALOR_OS}! Para mais informações entre em contato conosco. Atenciosamente, {EMITENTE} {TELEFONE_EMITENTE}.' WHERE `configuracoes`.`idConfig` = 7");
        
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
        
        // Deleta a atualização feita na tabela configuracoes
        $this->db->query('DELETE FROM `configuracoes` WHERE `configuracoes`.`idConfig` = 7');
    }
}

