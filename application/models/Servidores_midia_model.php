<?php

class Servidores_midia_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('prioridade', 'ASC');
        if ($where != '' && is_array($where)) {
            $this->db->where($where);
        }
        if ($perpage > 0) {
            $this->db->limit($perpage, $start);
        }
        
        if ($one == true) {
            return $this->db->get()->row_array();
        } else {
            if ($array == 'array') {
                return $this->db->get()->result_array();
            } else {
                return $this->db->get()->result();
            }
        }
    }

    public function getById($id)
    {
        $this->db->where('idServidorMidia', $id);
        return $this->db->get('servidores_midia')->row();
    }

    public function getAtivos()
    {
        $this->db->where('ativo', 1);
        $this->db->order_by('prioridade', 'ASC');
        return $this->db->get('servidores_midia')->result();
    }

    public function getAll()
    {
        $this->db->order_by('prioridade', 'ASC');
        return $this->db->get('servidores_midia')->result();
    }

    public function add($data)
    {
        $this->db->insert('servidores_midia', $data);
        return $this->db->insert_id();
    }

    public function edit($data, $id)
    {
        $this->db->where('idServidorMidia', $id);
        return $this->db->update('servidores_midia', $data);
    }

    public function delete($id)
    {
        $this->db->where('idServidorMidia', $id);
        return $this->db->delete('servidores_midia');
    }

    public function count($table, $where = '')
    {
        if ($where != '' && is_array($where)) {
            $this->db->where($where);
        }
        return $this->db->count_all_results($table);
    }

    public function getServidorComEspaco()
    {
        $this->db->where('ativo', 1);
        $this->db->order_by('prioridade', 'ASC');
        $this->db->order_by('espaco_disponivel', 'DESC');
        $this->db->limit(1);
        return $this->db->get('servidores_midia')->row();
    }

    public function atualizarEspacoDisponivel($id, $espaco)
    {
        $this->db->where('idServidorMidia', $id);
        return $this->db->update('servidores_midia', ['espaco_disponivel' => $espaco]);
    }
} 