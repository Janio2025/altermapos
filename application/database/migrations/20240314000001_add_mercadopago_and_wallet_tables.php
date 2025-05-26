<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_mercadopago_and_wallet_tables extends CI_Migration
{
    public function up()
    {
        // Tabela carteira_usuario
        $this->dbforge->add_field([
            'idCarteiraUsuario' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true
            ],
            'saldo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00'
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => '1'
            ],
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ]
        ]);
        $this->dbforge->add_key('idCarteiraUsuario', true);
        $this->dbforge->add_key('usuarios_id');
        $this->dbforge->create_table('carteira_usuario', true);
        $this->db->query('ALTER TABLE carteira_usuario ADD CONSTRAINT fk_carteira_usuario_usuarios1 FOREIGN KEY (usuarios_id) REFERENCES usuarios(idUsuarios) ON DELETE NO ACTION ON UPDATE NO ACTION');

        // Tabela configuracao_carteira
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true
            ],
            'carteira_usuario_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'salario_base' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false
            ],
            'salario_fixo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false
            ],
            'chave_pix' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'comissao_fixa' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => '0.00'
            ],
            'data_salario' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'comment' => 'Dia do mês (1-31)'
            ],
            'tipo_repeticao' => [
                'type' => 'ENUM',
                'constraint' => ['mensal', 'quinzenal'],
                'default' => 'mensal',
                'null' => false
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => 'CURRENT_TIMESTAMP',
                'on update CURRENT_TIMESTAMP'
            ],
            'tipo_valor_base' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ]
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('carteira_usuario_id');
        $this->dbforge->create_table('configuracao_carteira', true);

        // Tabela transacoes_usuario
        $this->dbforge->add_field([
            'idTransacoesUsuario' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['salario', 'bonus', 'comissao', 'retirada'],
                'null' => false
            ],
            'valor' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false
            ],
            'data_transacao' => [
                'type' => 'DATE',
                'null' => false
            ],
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'carteira_usuario_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'considerado_saldo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => '0'
            ],
            'saldo_acumulado' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00'
            ]
        ]);
        $this->dbforge->add_key('idTransacoesUsuario', true);
        $this->dbforge->add_key('carteira_usuario_id');
        $this->dbforge->create_table('transacoes_usuario', true);
        $this->db->query('ALTER TABLE transacoes_usuario ADD CONSTRAINT fk_transacoes_usuario_carteira_usuario1 FOREIGN KEY (carteira_usuario_id) REFERENCES carteira_usuario(idCarteiraUsuario) ON DELETE NO ACTION ON UPDATE NO ACTION');

        // Tabela mercadopago_config
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true
            ],
            'access_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'public_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'client_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'client_secret' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'sandbox_mode' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'webhook_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
                'on update CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('mercadopago_config', true);

        // Tabela mercadopago_transactions
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true
            ],
            'external_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'wallet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['withdrawal', 'deposit', 'refund'],
                'null' => false
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'pix_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'response_data' => [
                'type' => 'JSON',
                'null' => true
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
                'on update CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key(['external_id', 'user_id', 'wallet_id', 'status']);
        $this->dbforge->create_table('mercadopago_transactions', true);

        // Tabela mercadopago_logs
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'auto_increment' => true
            ],
            'transaction_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false
            ],
            'data' => [
                'type' => 'JSON',
                'null' => true
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key(['transaction_id', 'type']);
        $this->dbforge->create_table('mercadopago_logs', true);

        // Adicionar campo url_image_user na tabela usuarios se não existir
        if (!$this->db->field_exists('url_image_user', 'usuarios')) {
            $this->dbforge->add_column('usuarios', [
                'url_image_user' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'dataExpiracao'
                ]
            ]);
        }

        // Adicionar campo asaas_id na tabela clientes se não existir
        if (!$this->db->field_exists('asaas_id', 'clientes')) {
            $this->dbforge->add_column('clientes', [
                'asaas_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'idClientes'
                ]
            ]);
        }
    }

    public function down()
    {
        // Remover as constraints primeiro
        $this->db->query('ALTER TABLE transacoes_usuario DROP FOREIGN KEY fk_transacoes_usuario_carteira_usuario1');
        $this->db->query('ALTER TABLE carteira_usuario DROP FOREIGN KEY fk_carteira_usuario_usuarios1');

        // Remover as tabelas na ordem correta
        $this->dbforge->drop_table('mercadopago_logs', true);
        $this->dbforge->drop_table('mercadopago_transactions', true);
        $this->dbforge->drop_table('mercadopago_config', true);
        $this->dbforge->drop_table('transacoes_usuario', true);
        $this->dbforge->drop_table('configuracao_carteira', true);
        $this->dbforge->drop_table('carteira_usuario', true);

        // Remover as colunas adicionadas
        if ($this->db->field_exists('url_image_user', 'usuarios')) {
            $this->dbforge->drop_column('usuarios', 'url_image_user');
        }
        if ($this->db->field_exists('asaas_id', 'clientes')) {
            $this->dbforge->drop_column('clientes', 'asaas_id');
        }
    }
} 