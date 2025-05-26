<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Organizadores extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Organizadores_model');
        $this->data['menuOrganizadores'] = 'organizadores';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOrganizador')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar organizadores.');
            redirect(base_url());
        }

        $pesquisa = $this->input->get('pesquisa');

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('organizadores/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->Organizadores_model->count_organizadores();
        if ($pesquisa) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}";
            $this->data['configuration']['first_url'] = base_url("index.php/organizadores") . "?pesquisa={$pesquisa}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->Organizadores_model->get_organizadores($pesquisa, $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'organizadores/organizadores';

        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aOrganizador')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar organizadores.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Define as regras de validação
        $this->form_validation->set_rules('nome_organizador', 'Nome do Organizador', 'required|trim');
        $this->form_validation->set_rules('localizacao', 'Localização', 'trim');
        $this->form_validation->set_rules('ativa', 'Status', 'required|in_list[0,1]');

        if ($this->form_validation->run() == false) {
            // Se a validação falhar, exibe os erros
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            // Se a validação for bem-sucedida, prepara os dados para inserção
            $data = [
                'nome_organizador' => $this->input->post('nome_organizador'),
                'localizacao' => $this->input->post('localizacao'),
                'ativa' => $this->input->post('ativa') ? 1 : 0, // Garante que seja 0 ou 1
            ];

            // Insere o organizador no banco de dados
            $organizador_id = $this->Organizadores_model->add_organizador($data);

            if ($organizador_id) {
                // Processa os compartimentos
                $compartimentos = $this->input->post('nome_compartimento[]');
                if (!empty($compartimentos)) {
                    foreach ($compartimentos as $nome_compartimento) {
                        if (!empty($nome_compartimento)) {
                            $compartimento_data = [
                                'organizador_id' => $organizador_id,
                                'nome_compartimento' => $nome_compartimento,
                                'ativa' => 1, // Define o compartimento como ativo por padrão
                            ];
                            $this->Organizadores_model->add_compartimento($compartimento_data);
                        }
                    }
                }

                $this->session->set_flashdata('success', 'Organizador e compartimentos adicionados com sucesso!');
                log_info('Adicionou um organizador e seus compartimentos.');
                redirect(site_url('organizadores/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro ao salvar no banco de dados.</p></div>';
            }
        }

        // Carrega a view de adicionar organizador
        $this->data['view'] = 'organizadores/adicionarOrganizadores';
        return $this->layout();
    }

    public function editar()
{
    if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
        $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
        redirect('mapos');
    }

    if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOrganizador')) {
        $this->session->set_flashdata('error', 'Você não tem permissão para editar organizadores.');
        redirect(base_url());
    }

    $this->load->library('form_validation');
    $this->data['custom_error'] = '';

    // Define as regras de validação
    $this->form_validation->set_rules('nome_organizador', 'Nome do Organizador', 'required|trim');
    $this->form_validation->set_rules('localizacao', 'Localização', 'trim');
    $this->form_validation->set_rules('ativa', 'Status', 'required|in_list[0,1]');

    if ($this->form_validation->run() == false) {
        // Se a validação falhar, exibe os erros
        $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
    } else {
        // Inicia uma transação
        $this->db->trans_start();

        // Prepara os dados para atualização
        $data = [
            'nome_organizador' => $this->input->post('nome_organizador'),
            'localizacao' => $this->input->post('localizacao'),
            'ativa' => $this->input->post('ativa') ? 1 : 0,
        ];

        $organizador_id = $this->input->post('id');

        // Atualiza o organizador
        $this->Organizadores_model->update_organizador($organizador_id, $data);

        // Processa os compartimentos apenas se forem enviados
        $compartimentos = $this->input->post('nome_compartimento[]');
        if (!empty($compartimentos)) {
            // Remove os compartimentos antigos
            $this->Organizadores_model->delete_compartimentos_by_organizador($organizador_id);

            // Adiciona os novos compartimentos
            foreach ($compartimentos as $nome_compartimento) {
                if (!empty($nome_compartimento)) {
                    $compartimento_data = [
                        'organizador_id' => $organizador_id,
                        'nome_compartimento' => $nome_compartimento,
                        'ativa' => 1,
                    ];
                    $this->Organizadores_model->add_compartimento($compartimento_data);
                }
            }
        }

        // Finaliza a transação
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            // Se a transação falhar, exibe uma mensagem de erro
            $this->session->set_flashdata('error', 'Ocorreu um erro ao atualizar o organizador.');
        } else {
            // Se a transação for bem-sucedida, exibe uma mensagem de sucesso
            $this->session->set_flashdata('success', 'Organizador atualizado com sucesso!');
        }

        redirect(site_url('organizadores/editar/') . $organizador_id);
    }

    // Carrega os dados do organizador e compartimentos para a view
    $this->data['result'] = $this->Organizadores_model->get_organizador_by_id($this->uri->segment(3));
    $this->data['view'] = 'organizadores/editarOrganizadores';

    return $this->layout();
}

    public function buscarOrganizadores()
    {
        $term = $this->input->get('term'); // Termo de busca
        $this->db->select("id, CONCAT(nome_organizador, ' / ', localizacao) as value", false);
        $this->db->like('nome_organizador', $term);
        $query = $this->db->get('organizadores');
        echo json_encode($query->result());
    }

    public function buscarCompartimentos()
    {
        $organizador_id = $this->input->get('organizador_id');
        $this->db->select('id, nome_compartimento');
        $this->db->where('organizador_id', $organizador_id);
        $query = $this->db->get('compartimentos');
        echo json_encode($query->result());
    }

    public function visualizar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOrganizador')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar organizadores.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->data['result'] = $this->Organizadores_model->get_organizador_by_id($this->uri->segment(3));
        $this->data['view'] = 'organizadores/visualizarOrganizadores';

        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dOrganizador')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir organizadores.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir organizador.');
            redirect(site_url('organizadores/gerenciar/'));
        }

        if ($this->Organizadores_model->delete_organizador($id)) {
            $this->session->set_flashdata('success', 'Organizador excluído com sucesso!');
            log_info('Removeu um organizador. ID' . $id);
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir organizador.');
        }

        redirect(site_url('organizadores/gerenciar/'));
    }
}