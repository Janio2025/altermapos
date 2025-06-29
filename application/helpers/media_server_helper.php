<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Helper para gerenciar servidores de mídia
 */
class Media_server_helper
{
    private static $ci;

    public static function init()
    {
        if (!self::$ci) {
            self::$ci = &get_instance();
            if (!isset(self::$ci->servidores_midia_model)) {
                self::$ci->load->model('servidores_midia_model');
            }
        }
    }

    /**
     * Obtém o servidor de mídia com mais espaço disponível
     */
    public static function getServidorComEspaco()
    {
        self::init();
        return self::$ci->servidores_midia_model->getServidorComEspaco();
    }

    /**
     * Obtém todos os servidores ativos ordenados por prioridade
     */
    public static function getServidoresAtivos()
    {
        self::init();
        return self::$ci->servidores_midia_model->getAtivos();
    }

    /**
     * Escolhe o melhor servidor para salvar um arquivo
     */
    public static function escolherServidor()
    {
        $servidores = self::getServidoresAtivos();
        
        if (empty($servidores)) {
            return null;
        }

        // Primeiro, tenta encontrar um servidor com espaço disponível
        foreach ($servidores as $servidor) {
            if ($servidor->espaco_disponivel !== null && $servidor->espaco_disponivel > 0) {
                return $servidor;
            }
        }

        // Se não encontrar um com espaço definido, retorna o primeiro ativo
        return $servidores[0];
    }

    /**
     * Salva um arquivo no servidor de mídia
     */
    public static function salvarArquivo($arquivo_temp, $nome_arquivo, $pasta = '')
    {
        $servidor = self::escolherServidor();
        
        if (!$servidor) {
            return false;
        }

        $caminho_completo = rtrim($servidor->caminho_fisico, '/') . '/' . ltrim($pasta, '/');
        
        // Cria a pasta se não existir
        if (!is_dir($caminho_completo)) {
            mkdir($caminho_completo, 0755, true);
        }

        $caminho_arquivo = $caminho_completo . '/' . $nome_arquivo;
        
        if (move_uploaded_file($arquivo_temp, $caminho_arquivo)) {
            // Retorna a URL do arquivo
            return rtrim($servidor->url, '/') . '/' . ltrim($pasta, '/') . '/' . $nome_arquivo;
        }

        return false;
    }

    /**
     * Remove um arquivo do servidor de mídia
     */
    public static function removerArquivo($url_arquivo)
    {
        $servidores = self::getServidoresAtivos();
        
        foreach ($servidores as $servidor) {
            if (strpos($url_arquivo, $servidor->url) === 0) {
                $caminho_relativo = str_replace($servidor->url, '', $url_arquivo);
                $caminho_fisico = rtrim($servidor->caminho_fisico, '/') . '/' . ltrim($caminho_relativo, '/');
                
                if (file_exists($caminho_fisico)) {
                    return unlink($caminho_fisico);
                }
            }
        }

        return false;
    }

    /**
     * Verifica se um arquivo existe em algum servidor
     */
    public static function arquivoExiste($url_arquivo)
    {
        $servidores = self::getServidoresAtivos();
        
        foreach ($servidores as $servidor) {
            if (strpos($url_arquivo, $servidor->url) === 0) {
                $caminho_relativo = str_replace($servidor->url, '', $url_arquivo);
                $caminho_fisico = rtrim($servidor->caminho_fisico, '/') . '/' . ltrim($caminho_relativo, '/');
                
                return file_exists($caminho_fisico);
            }
        }

        return false;
    }

    /**
     * Atualiza o espaço disponível de um servidor
     */
    public static function atualizarEspacoDisponivel($id_servidor)
    {
        self::init();
        
        $servidor = self::$ci->servidores_midia_model->getById($id_servidor);
        if (!$servidor) {
            return false;
        }

        $caminho = $servidor->caminho_fisico;
        if (!is_dir($caminho)) {
            return false;
        }

        $espaco_total = disk_total_space($caminho);
        $espaco_livre = disk_free_space($caminho);
        
        return self::$ci->servidores_midia_model->atualizarEspacoDisponivel($id_servidor, $espaco_livre);
    }

    /**
     * Obtém informações de espaço de todos os servidores
     */
    public static function getInfoEspacoServidores()
    {
        $servidores = self::getServidoresAtivos();
        $info = [];

        foreach ($servidores as $servidor) {
            $caminho = $servidor->caminho_fisico;
            if (is_dir($caminho)) {
                $espaco_total = disk_total_space($caminho);
                $espaco_livre = disk_free_space($caminho);
                $espaco_usado = $espaco_total - $espaco_livre;
                $percentual_usado = ($espaco_usado / $espaco_total) * 100;

                $info[] = [
                    'id' => $servidor->idServidorMidia,
                    'nome' => $servidor->nome,
                    'espaco_total' => $espaco_total,
                    'espaco_livre' => $espaco_livre,
                    'espaco_usado' => $espaco_usado,
                    'percentual_usado' => round($percentual_usado, 2),
                    'url' => $servidor->url,
                    'caminho_fisico' => $servidor->caminho_fisico,
                    'ativo' => $servidor->ativo,
                    'prioridade' => $servidor->prioridade
                ];
            }
        }

        return $info;
    }

    /**
     * Obtém o primeiro servidor ativo (para compatibilidade)
     */
    public static function getPrimeiroServidor()
    {
        $servidores = self::getServidoresAtivos();
        return !empty($servidores) ? $servidores[0] : null;
    }

    /**
     * Verifica se há servidores configurados
     */
    public static function temServidoresConfigurados()
    {
        $servidores = self::getServidoresAtivos();
        return !empty($servidores);
    }

    /**
     * Obtém URL e caminho do servidor (para compatibilidade com código antigo)
     */
    public static function getConfiguracaoServidor()
    {
        $servidor = self::getPrimeiroServidor();
        
        if ($servidor) {
            return [
                'url' => $servidor->url,
                'caminho_fisico' => $servidor->caminho_fisico
            ];
        }
        
        return [
            'url' => '',
            'caminho_fisico' => ''
        ];
    }

    /**
     * Determina o diretório e URL base para upload (compatibilidade)
     */
    public static function getDiretorioUpload($tipo = 'os', $id = null)
    {
        $servidor = self::getPrimeiroServidor();
        
        if ($servidor && !empty($servidor->url) && !empty($servidor->caminho_fisico)) {
            // Usar servidor de mídia configurado
            $directory = rtrim($servidor->caminho_fisico, '/\\') . '/anexos/' . $tipo . '/' . date('m-Y') . '/' . strtoupper($tipo) . '-' . $id;
            $url_base = rtrim($servidor->url, '/') . '/anexos/' . $tipo . '/' . date('m-Y') . '/' . strtoupper($tipo) . '-' . $id;
        } else {
            // Usar caminho padrão local
            $directory = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . $tipo . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . strtoupper($tipo) . '-' . $id;
            $url_base = base_url('assets/anexos/' . $tipo . '/' . date('m-Y') . '/' . strtoupper($tipo) . '-' . $id);
        }
        
        return [
            'directory' => $directory,
            'url_base' => $url_base
        ];
    }
} 