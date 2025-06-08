<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Compartimentos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('compartimentos_model');
        $this->load->model('produtos_model');
        $this->load->model('os_model');
    }

    public function getOcupacaoCompartimento($compartimento_id)
    {
        $ocupacao = $this->compartimentos_model->getOcupacaoCompartimento($compartimento_id);
        echo json_encode($ocupacao);
    }

    public function buscarCompartimentos()
    {
        $organizador_id = $this->input->get('organizador_id');
        $compartimentos = $this->compartimentos_model->getCompartimentosComOcupacao($organizador_id);
        echo json_encode($compartimentos);
    }

    public function atualizarOcupacao()
    {
        $compartimento_id = $this->input->post('compartimento_id');
        $tipo = $this->input->post('tipo'); // 'produto' ou 'equipamento'
        $item_id = $this->input->post('item_id');
        $acao = $this->input->post('acao'); // 'adicionar' ou 'remover'

        $result = $this->compartimentos_model->atualizarOcupacao($compartimento_id, $tipo, $item_id, $acao);
        echo json_encode($result);
    }

    public function validarCapacidadeCompartimento()
    {
        $compartimento_id = $this->input->post('compartimento_id');
        $result = $this->compartimentos_model->validarCapacidadeCompartimento($compartimento_id);
        echo json_encode($result);
    }
} 