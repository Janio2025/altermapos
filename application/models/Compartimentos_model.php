<?php

class Compartimentos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCompartimentosByOrganizador($organizador_id)
    {
        $this->db->select('*');
        $this->db->from('compartimentos');
        $this->db->where('organizador_id', $organizador_id);
        $this->db->where('ativa', 1);
        $this->db->order_by('nome_compartimento', 'asc');
        return $this->db->get()->result();
    }
} 