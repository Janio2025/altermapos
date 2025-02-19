<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Compartimentos extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true,
            ],
            'organizador_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'nome_compartimento' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'ativa' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (organizador_id) REFERENCES organizadores(id) ON DELETE CASCADE');
        $this->dbforge->create_table('compartimentos', true);
        $this->db->query('ALTER TABLE `compartimentos` ENGINE = InnoDB');
    }

    public function down() {
        $this->dbforge->drop_table('compartimentos');
    }
}
