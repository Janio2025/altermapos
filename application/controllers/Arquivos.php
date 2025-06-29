<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Arquivos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('arquivos_model');
        $this->data['menuArquivos'] = 'Arquivos';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar arquivos.');
            redirect(base_url());
        }

        $this->load->library('pagination');

        $pesquisa = $this->input->get('pesquisa');
        $de = $this->input->get('data');
        $ate = $this->input->get('data2');

        if ($pesquisa == null && $de == null && $ate == null) {
            $this->data['configuration']['base_url'] = site_url('arquivos/gerenciar');
            $this->data['configuration']['total_rows'] = $this->arquivos_model->count('documentos');

            $this->pagination->initialize($this->data['configuration']);

            $this->data['results'] = $this->arquivos_model->get('documentos', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3));
        } else {
            if ($de != null) {
                $de = explode('/', $de);
                $de = $de[2] . '-' . $de[1] . '-' . $de[0];

                if ($ate != null) {
                    $ate = explode('/', $ate);
                    $ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];
                } else {
                    $ate = $de;
                }
            }

            $this->data['results'] = $this->arquivos_model->search($pesquisa, $de, $ate);
        }

        $this->data['view'] = 'arquivos/arquivos';

        return $this->layout();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aArquivo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar arquivos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->load->helper('media_server_helper');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', '', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $arquivo = $this->do_upload();

            $file = $arquivo['file_name'];
            $path = $arquivo['full_path'];
            $url = $arquivo['url'];
            $tamanho = $arquivo['file_size'];
            $tipo = $arquivo['file_ext'];

            $data = $this->input->post('data');

            if ($data == null) {
                $data = date('Y-m-d');
            } else {
                $data = explode('/', $data);
                $data = $data[2] . '-' . $data[1] . '-' . $data[0];
            }

            $data = [
                'documento' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'file' => $file,
                'path' => $path,
                'url' => $url,
                'cadastro' => $data,
                'tamanho' => $tamanho,
                'tipo' => $tipo,
            ];

            if ($this->arquivos_model->add('documentos', $data) == true) {
                $this->session->set_flashdata('success', 'Arquivo adicionado com sucesso!');

                log_info('Adicionou um arquivo');

                redirect(site_url('arquivos/adicionar/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'arquivos/adicionarArquivo';

        return $this->layout();
    }

    public function editar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eArquivo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar arquivos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', '', 'trim|required');
        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = $this->input->post('data');
            if ($data == null) {
                $data = date('Y-m-d');
            } else {
                $data = explode('/', $data);
                $data = $data[2] . '-' . $data[1] . '-' . $data[0];
            }

            $data = [
                'documento' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'cadastro' => $data,
            ];

            if ($this->arquivos_model->edit('documentos', $data, 'idDocumentos', $this->input->post('idDocumentos')) == true) {
                $this->session->set_flashdata('success', 'Alterações efetuadas com sucesso!');
                log_info('Alterou um arquivo, ID: ' . $this->input->post('idDocumentos'));
                redirect(site_url('arquivos/editar/') . $this->input->post('idDocumentos'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['result'] = $this->arquivos_model->getById($this->uri->segment(3));
        $this->data['view'] = 'arquivos/editarArquivo';

        return $this->layout();
    }

    public function download($id = null)
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar arquivos.');
            redirect(base_url());
        }

        if ($id == null || ! is_numeric($id)) {
            $this->session->set_flashdata('error', 'Erro! O arquivo não pode ser localizado.');
            redirect(base_url() . 'index.php/arquivos/');
        }

        $file = $this->arquivos_model->getById($id);
        
        if (!$file) {
            $this->session->set_flashdata('error', 'Arquivo não encontrado.');
            redirect(base_url() . 'index.php/arquivos/');
        }

        $path = $file->path;
        
        // Verificar se o arquivo existe
        if (!file_exists($path)) {
            $this->session->set_flashdata('error', 'Arquivo não encontrado no servidor.');
            redirect(base_url() . 'index.php/arquivos/');
        }

        // Obter informações do arquivo
        $file_size = filesize($path);
        $file_name = $file->file;
        
        // Configurar headers para download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . $file_size);
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Expires: 0');

        // Para arquivos grandes, usar streaming
        if ($file_size > 10485760) { // 10MB
            // Usar streaming para arquivos grandes
            $handle = fopen($path, 'rb');
            if ($handle) {
                while (!feof($handle)) {
                    echo fread($handle, 8192); // 8KB chunks
                    flush();
                }
                fclose($handle);
            }
        } else {
            // Para arquivos menores, usar readfile
            readfile($path);
        }
        
        exit;
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dArquivo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir arquivos.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null || ! is_numeric($id)) {
            $this->session->set_flashdata('error', 'Erro! O arquivo não pode ser localizado.');
            redirect(site_url('arquivos'));
        }

        $file = $this->arquivos_model->getById($id);
        $this->db->where('idDocumentos', $id);
        if ($this->db->delete('documentos')) {
            $path = $file->path;
            unlink($path);
            $this->session->set_flashdata('success', 'Arquivo excluido com sucesso!');
            log_info('Removeu um arquivo. ID: ' . $id);
        } else {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar excluir o arquivo.');
        }
        redirect(site_url('arquivos'));
    }

    public function do_upload()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aArquivo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar arquivos.');
            redirect(base_url());
        }

        $this->load->helper('media_server_helper');
        
        // Usar o helper para determinar o diretório e URL
        $config = Media_server_helper::getDiretorioUpload('arquivos', date('Y-m-d'));
        $directory = $config['directory'];
        $url_base = $config['url_base'];

        $config['upload_path'] = $directory;
        $config['allowed_types'] = 'txt|jpg|jpeg|gif|png|pdf|PDF|JPG|JPEG|GIF|PNG|zip|ZIP';
        $config['max_size'] = 0;
        $config['max_width'] = '3000';
        $config['max_height'] = '2000';
        $config['encrypt_name'] = true;

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $this->load->library('upload', $config);

        if (! $this->upload->do_upload()) {
            $error = ['error' => $this->upload->display_errors()];

            $this->session->set_flashdata('error', 'Erro ao fazer upload do arquivo, verifique se a extensão do arquivo é permitida.');
            redirect(site_url('arquivos/adicionar'));
        } else {
            $upload_data = $this->upload->data();
            // Adicionar a URL base aos dados de upload
            $upload_data['url'] = $url_base . '/' . $upload_data['file_name'];
            return $upload_data;
        }
    }
}

/* End of file arquivos.php */
/* Location: ./application/controllers/arquivos.php */
