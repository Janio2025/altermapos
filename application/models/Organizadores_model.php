<?php

class Organizadores_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Método para buscar todos os organizadores
    public function get_organizadores($where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select('*');
        $this->db->from('organizadores');
        if ($where) {
            $this->db->like('nome_organizador', $where);
            $this->db->or_like('localizacao', $where);
        }
        $this->db->order_by('id', 'desc');
        $this->db->limit($perpage, $start);

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();

        return $result;
    }

    // Método para buscar um organizador pelo ID
    public function get_organizador_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('organizadores');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $organizador = $this->db->get()->row();

        if ($organizador) {
            $this->db->select('*');
            $this->db->from('compartimentos');
            $this->db->where('organizador_id', $id);
            $organizador->compartimentos = $this->db->get()->result();
        } else {
            $organizador->compartimentos = [];
        }

        return $organizador;
    }

    // Método para adicionar um novo organizador
    public function add_organizador($data)
    {
        $this->db->insert('organizadores', $data);
        return $this->db->insert_id(); // Retorna o ID do organizador inserido
    }

    // Método para adicionar um compartimento
    public function add_compartimento($data)
    {
        return $this->db->insert('compartimentos', $data);
    }

    // Método para atualizar um organizador
    public function update_organizador($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('organizadores', $data);
        return $this->db->affected_rows() > 0; // Retorna true se a atualização for bem-sucedida
    }

    // Método para excluir todos os compartimentos de um organizador
    public function delete_compartimentos_by_organizador($organizador_id)
    {
        $this->db->where('organizador_id', $organizador_id);
        return $this->db->delete('compartimentos'); // Retorna true se a exclusão for bem-sucedida
    }

    // Método para excluir um organizador (e seus compartimentos, devido ao ON DELETE CASCADE)
    public function delete_organizador($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('organizadores');
        return $this->db->affected_rows() > 0; // Retorna true se a exclusão for bem-sucedida
    }

    // Método para contar o total de organizadores
    public function count_organizadores()
    {
        return $this->db->count_all('organizadores');
    }

    // Método para atualizar um compartimento
    public function update_compartimento($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('compartimentos', $data);
        return $this->db->affected_rows() > 0; // Retorna true se a atualização for bem-sucedida
    }

    // Método para excluir um compartimento
    public function delete_compartimento($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('compartimentos');
        return $this->db->affected_rows() > 0; // Retorna true se a exclusão for bem-sucedida
    }

    // Método para buscar todos os compartimentos de um organizador
    public function get_compartimentos_by_organizador($organizador_id)
    {
        $this->db->select('*');
        $this->db->from('compartimentos');
        $this->db->where('organizador_id', $organizador_id);
        return $this->db->get()->result();
    }

    // Método para buscar um compartimento pelo ID
    public function get_compartimento_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('compartimentos');
        $this->db->where('id', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    // Método para verificar se um compartimento com o mesmo nome já existe para um organizador
    public function get_compartimento_by_name($nome_compartimento, $organizador_id)
    {
        $this->db->select('*');
        $this->db->from('compartimentos');
        $this->db->where('nome_compartimento', $nome_compartimento);
        $this->db->where('organizador_id', $organizador_id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }
}