<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Organizadores extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true,
            ],
            'nome_organizador' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'localizacao' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'ativa' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
            ],
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('organizadores', true);
        $this->db->query('ALTER TABLE `organizadores` ENGINE = InnoDB');
    }

    public function down() {
        $this->dbforge->drop_table('organizadores');
    }
}
