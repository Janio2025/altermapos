<?php

class Migration_create_servidores_midia_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'idServidorMidia' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'caminho_fisico' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'prioridade' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'espaco_disponivel' => [
                'type' => 'BIGINT',
                'null' => true,
            ],
            'data_criacao' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'data_modificacao' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            ],
        ]);
        
        $this->dbforge->add_key('idServidorMidia', true);
        $this->dbforge->add_key('ativo');
        $this->dbforge->add_key('prioridade');
        $this->dbforge->create_table('servidores_midia', true);
        $this->db->query('ALTER TABLE `servidores_midia` ENGINE = InnoDB');
    }

    public function down()
    {
        $this->dbforge->drop_table('servidores_midia', true);
    }
} 