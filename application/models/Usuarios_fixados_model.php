<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Usuarios_fixados_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->select('usuarios_fixados.*, usuarios.nome as nome_usuario, usuarios_fixador.nome as nome_fixador');
        $this->db->from('usuarios_fixados');
        $this->db->join('usuarios', 'usuarios.idUsuarios = usuarios_fixados.usuario_id');
        $this->db->join('usuarios as usuarios_fixador', 'usuarios_fixador.idUsuarios = usuarios_fixados.usuario_fixador_id');
        $this->db->where('usuarios_fixados.idUsuarioFixado', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getByUsuarioId($usuario_id)
    {
        $this->db->select('usuarios_fixados.*, usuarios.nome as nome_usuario, usuarios_fixador.nome as nome_fixador');
        $this->db->from('usuarios_fixados');
        $this->db->join('usuarios', 'usuarios.idUsuarios = usuarios_fixados.usuario_id');
        $this->db->join('usuarios as usuarios_fixador', 'usuarios_fixador.idUsuarios = usuarios_fixados.usuario_fixador_id');
        $this->db->where('usuarios_fixados.usuario_fixador_id', $usuario_id);
        $this->db->where('usuarios_fixados.status', 1);

        return $this->db->get()->result();
    }

    public function add($data)
    {
        $this->db->insert('usuarios_fixados', $data);
        if ($this->db->affected_rows() == '1') {
            return $this->db->insert_id();
        }

        return false;
    }

    public function edit($data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update('usuarios_fixados', $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete('usuarios_fixados');
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }

    public function isFixado($usuario_id, $usuario_fixador_id)
    {
        $this->db->where('usuario_id', $usuario_id);
        $this->db->where('usuario_fixador_id', $usuario_fixador_id);
        $this->db->where('status', 1);
        return $this->db->get('usuarios_fixados')->num_rows() > 0;
    }

    public function fixarUsuario($usuario_id, $usuario_fixador_id)
    {
        // Verifica se já existe uma fixação
        $this->db->where('usuario_id', $usuario_id);
        $this->db->where('usuario_fixador_id', $usuario_fixador_id);
        $fixacao = $this->db->get('usuarios_fixados')->row();

        if ($fixacao) {
            // Se existe, apenas atualiza o status
            $this->db->where('idUsuarioFixado', $fixacao->idUsuarioFixado);
            $this->db->update('usuarios_fixados', ['status' => 1, 'data_fixacao' => date('Y-m-d H:i:s')]);
        } else {
            // Se não existe, cria uma nova fixação
            $data = [
                'usuario_id' => $usuario_id,
                'usuario_fixador_id' => $usuario_fixador_id,
                'data_fixacao' => date('Y-m-d H:i:s'),
                'status' => 1
            ];
            $this->db->insert('usuarios_fixados', $data);
        }

        return $this->db->affected_rows() > 0;
    }

    public function desfixarUsuario($usuario_id, $usuario_fixador_id)
    {
        $this->db->where('usuario_id', $usuario_id);
        $this->db->where('usuario_fixador_id', $usuario_fixador_id);
        $this->db->update('usuarios_fixados', ['status' => 0]);

        return $this->db->affected_rows() > 0;
    }
} 