<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Categorias_model extends CI_Model
{
    public function getAll()
    {
        return $this->db->order_by('categoria', 'ASC')->get('categorias')->result();
    }

    public function getById($id)
    {
        return $this->db->where('idCategorias', $id)->get('categorias')->row();
    }

    public function adicionar($dados)
    {
        $this->db->insert('categorias', $dados);
        return $this->db->insert_id();
    }

    public function editar($id, $dados)
    {
        $this->db->where('idCategorias', $id);
        return $this->db->update('categorias', $dados);
    }

    public function deletar($id)
    {
        $this->db->where('idCategorias', $id);
        return $this->db->delete('categorias');
    }

    public function getByParent($parent_id)
    {
        return $this->db->where('parent_id', $parent_id)->order_by('categoria', 'ASC')->get('categorias')->result();
    }

    public function getByMLId($ml_id)
    {
        return $this->db->where('ml_id', $ml_id)->get('categorias')->row();
    }

    public function getAtributoByMLId($categoria_id, $ml_attribute_id)
    {
        return $this->db->where('categoria_id', $categoria_id)
                        ->where('ml_attribute_id', $ml_attribute_id)
                        ->get('atributos_ml')->row();
    }

    public function adicionarAtributo($dados)
    {
        // Log dos dados sendo inseridos
        log_message('debug', 'Categorias_model::adicionarAtributo - Dados: ' . json_encode($dados));
        
        $resultado = $this->db->insert('atributos_ml', $dados);
        
        if ($resultado) {
            $insert_id = $this->db->insert_id();
            log_message('debug', 'Categorias_model::adicionarAtributo - Sucesso, ID: ' . $insert_id);
            return $insert_id;
        } else {
            $error = $this->db->error();
            log_message('error', 'Categorias_model::adicionarAtributo - Erro: ' . json_encode($error));
            return false;
        }
    }

    public function atualizarAtributo($id, $dados)
    {
        log_message('debug', 'Categorias_model::atualizarAtributo - ID: ' . $id . ', Dados: ' . json_encode($dados));
        
        $this->db->where('id', $id);
        $resultado = $this->db->update('atributos_ml', $dados);
        
        if ($resultado) {
            log_message('debug', 'Categorias_model::atualizarAtributo - Sucesso');
            return true;
        } else {
            $error = $this->db->error();
            log_message('error', 'Categorias_model::atualizarAtributo - Erro: ' . json_encode($error));
            return false;
        }
    }

    public function getAtributosByCategoria($categoria_id)
    {
        return $this->db->where('categoria_id', $categoria_id)
                        ->where('status', 1)
                        ->order_by('name', 'ASC')
                        ->get('atributos_ml')->result();
    }

    public function getAtributosByCategoriaId($categoria_id)
    {
        return $this->db->where('categoria_id', $categoria_id)
                        ->where('status', 1)
                        ->order_by('name', 'ASC')
                        ->get('atributos_ml')->result();
    }
} 