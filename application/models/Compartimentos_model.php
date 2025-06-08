<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
        $this->db->where('ativa', true);
        $this->db->order_by('nome_compartimento', 'asc');
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->select('*');
        $this->db->from('compartimentos');
        $this->db->where('id', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function getOcupacaoCompartimento($compartimento_id)
    {
        $this->db->select('COUNT(*) as quantidade');
        $this->db->from('compartimento_equipamentos');
        $this->db->where('compartimento_id', $compartimento_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getCompartimentosComOcupacao($organizador_id)
    {
        $this->db->select('c.*, COUNT(ce.id) as quantidade');
        $this->db->from('compartimentos c');
        $this->db->join('compartimento_equipamentos ce', 'c.id = ce.compartimento_id', 'left');
        $this->db->where('c.organizador_id', $organizador_id);
        $this->db->group_by('c.id');
        $query = $this->db->get();
        return $query->result();
    }

    public function atualizarOcupacao($compartimento_id, $tipo, $item_id, $acao)
    {
        $data = array(
            'compartimento_id' => $compartimento_id,
            'data_entrada' => date('Y-m-d H:i:s')
        );

        if ($tipo == 'produto') {
            $data['produtos_id'] = $item_id;
        } else {
            $data['os_id'] = $item_id;
        }

        if ($acao == 'adicionar') {
            $this->db->insert('compartimento_equipamentos', $data);
            $this->registrarAlteracaoCompartimento($compartimento_id, 'adicionar', $item_id, $tipo);
        } else {
            $this->db->where('compartimento_id', $compartimento_id);
            if ($tipo == 'produto') {
                $this->db->where('produtos_id', $item_id);
            } else {
                $this->db->where('os_id', $item_id);
            }
            $this->db->delete('compartimento_equipamentos');
            $this->registrarAlteracaoCompartimento($compartimento_id, 'remover', $item_id, $tipo);
        }

        return array('success' => true);
    }

    public function validarCapacidadeCompartimento($compartimento_id)
    {
        $ocupacao = $this->getOcupacaoCompartimento($compartimento_id);
        // Aqui você pode definir um limite máximo de itens por compartimento
        $limite = 10; // Exemplo: máximo 10 itens por compartimento
        return array('valido' => $ocupacao->quantidade < $limite);
    }

    private function registrarAlteracaoCompartimento($compartimento_id, $acao, $item_id, $tipo)
    {
        $data = array(
            'compartimento_id' => $compartimento_id,
            'acao' => $acao,
            'item_id' => $item_id,
            'tipo_item' => $tipo,
            'data_alteracao' => date('Y-m-d H:i:s'),
            'usuario_id' => $this->session->userdata('id_admin')
        );
        $this->db->insert('log_compartimentos', $data);
    }
} 