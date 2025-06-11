<!-- #mudanças -->
<style>
/* Hiding the checkbox, but allowing it to be focused */
.badgebox {
    opacity: 0;
}

.badgebox+.badge {
    /* Move the check mark away when unchecked */
    text-indent: -999999px;
    /* Makes the badge's width stay the same checked and unchecked */
    width: 27px;
}

.badgebox:focus+.badge {
    /* Set something to make the badge looks focused */
    /* This really depends on the application, in my case it was: */

    /* Adding a light border */
    box-shadow: inset 0px 0px 5px;
    /* Taking the difference out of the padding */
}

.badgebox:checked+.badge {
    /* Move the check mark back when checked */
    text-indent: 0;
}

.form-horizontal .control-group {
        border-bottom: 1px solid #ffffff;
    }

    .form-horizontal .controls {
        margin-left: 20px;
        padding-bottom: 8px 0;
    }

    .form-horizontal .control-label {
        text-align: left;
        padding-top: 15px;
    }

    .nopadding {
        padding: 0 20px !important;
        margin-right: 20px;
    }

    .widget-title h5 {
        padding-bottom: 30px;
        text-align-last: left;
        font-size: 2em;
        font-weight: 500;
    }

    @media (max-width: 480px) {
        form {
            display: contents !important;
        }

        .form-horizontal .control-label {
            margin-bottom: -6px;
        }

        .btn-xs {
            position: initial !important;
        }
    }
/* meus  */  

.image-slider {
    position: relative;
    width: 100%;
    max-width: 500px; /* Ajuste conforme necessário */
    margin: 10px;
}

.image-slider img {
    width: 100%;
    height: 100%;
}

.prev, .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    width: auto;
    padding: 16px;
    margin-top: -22px;
    color: blue;
    font-weight: bold;
    font-size: 18px;
    transition: 0.6s ease;
    border-radius: 0 3px 3px 0;
    user-select: none;
}

.next {
    right: 0;
    border-radius: 3px 0 0 3px;
}

.prev:hover, .next:hover {
    background-color: rgba(0,0,0,0.8);
}

.div-bord {
            border: 1px solid black; /* Define a borda */
            padding: 1%; /* Define o padding de 5% em todos os lados */
            margin-bottom: 2%; /* Adiciona um espaço de 5% abaixo de cada div */
}


</style>
<!-- #st -->
<?php
// Processar o campo localizacaoProduto para remover o ID
$localizacaoProduto = isset($result->localizacaoProduto) ? $result->localizacaoProduto : 'N/A';
if ($localizacaoProduto !== 'N/A') {
    $localizacao = explode(',', $localizacaoProduto);
    $localizacaoProduto = implode(',', array_slice($localizacao, 1)); // Remove o ID
}
?>


<div class="accordion" id="collapse-group">
    <div class="accordion-group widget-box">
        <div class="accordion-heading">
            <div class="widget-title" style="margin: -20px 0 0">
                <a data-parent="#collapse-group" href="#collapseGOne" data-toggle="collapse">
                    <span class="icon"><i class="fas fa-shopping-bag"></i></span>
                    <h5>Dados do Produto</h5>
                </a>
            </div>
        </div>
        <div class="collapse in accordion-body">
            <div class="widget-content span12">
              <div class="span6 div-bord" >
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Produto / Peça</strong></td>
                            <td><?php echo isset($result->descricao) ? $result->descricao : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Marca</strong></td>
                            <td><?php echo isset($result->marcaProduto) ? $result->marcaProduto : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Modelo</strong></td>
                            <td><?php echo isset($result->nomeModelo) ? $result->nomeModelo : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Compatíveis</strong></td>
                            <td>
                                <?php if (!empty($modelosCompativeis)) : ?>
                                    <ul>
                                        <?php foreach ($modelosCompativeis as $modeloCompativel) : ?>
                                            <li><?php echo isset($modeloCompativel->modeloCompativel) ? $modeloCompativel->modeloCompativel : 'N/A'; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <p>Nenhum modelo compatível encontrado.</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Código da Peça</strong></td>
                            <td><?php echo isset($result->codigoPeca) ? $result->codigoPeca : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Número de Série</strong></td>
                            <td><?php echo isset($result->nsProduto) ? $result->nsProduto : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Condições do Produto</strong></td>
                            <td><?php echo isset($result->descricaoCondicao) ? $result->descricaoCondicao : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Direcionado (a)</strong></td>
                            <td><?php echo isset($result->descricaoDirecao) ? $result->descricaoDirecao : 'N/A'; ?></td>
                        </tr>
                    </tbody>
                </table>
              </div>

              <div class="span6 div-bord" >
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="text-align: right; width: 30%"><strong>Localização</strong></td>
                            <td>
                                <?php 
                                if ($result->organizador_id) {
                                    $organizador = $this->db->where('id', $result->organizador_id)->get('organizadores')->row();
                                    $compartimento = $this->db->where('id', $result->compartimento_id)->get('compartimentos')->row();
                                    echo $organizador ? $organizador->nome_organizador : '';
                                    echo $organizador ? ' - ' . $organizador->localizacao : '';
                                    echo $compartimento ? ' - ' . $compartimento->nome_compartimento : '';
                                } else {
                                    echo 'Não definida';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Unidade</strong></td>
                            <td><?php echo isset($result->unidade) ? $result->unidade : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Preço de Compra</strong></td>
                            <td>R$ <?php echo isset($result->precoCompra) ? $result->precoCompra : '0,00'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Preço de Venda</strong></td>
                            <td>R$ <?php echo isset($result->precoVenda) ? $result->precoVenda : '0,00'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Estoque</strong></td>
                            <td><?php echo isset($result->estoque) ? $result->estoque : '0'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Estoque Mínimo</strong></td>
                            <td><?php echo isset($result->estoqueMinimo) ? $result->estoqueMinimo : '0'; ?></td>
                        </tr>
                        
                    </tbody>
                </table>
              </div>

              <div class="span6 div-bord">
              <div id="divAnexos" style="margin-left: 10px;">
                    <?php if (!empty($imagensProduto)) : ?>
                        <?php foreach ($imagensProduto as $imagem) : ?>
                            <div class="span4" style="min-height: 150px; margin-left: 0; padding: 10px;">
                                <?php 
                                    $thumb = isset($imagem->thumb) ? $imagem->urlImagem . '/thumbs/' . $imagem->thumb : base_url() . 'assets/img/icon-file.png';
                                    $link = isset($imagem->anexo) ? $imagem->urlImagem . '/' . $imagem->anexo : base_url() . 'assets/img/icon-file.png';
                                ?>
                                <!-- Alterado href para #modal-produto e mantida a classe "anexo" -->
                                <a href="#modal-produto" imagem="<?php echo $imagem->idImagem; ?>" link="<?php echo $link; ?>" role="button" class="btn anexo span12" data-toggle="modal">
                                    <img src="<?php echo $thumb; ?>" alt="Anexo">
                                </a>
                                <!-- A linha abaixo foi removida para não exibir o nome do arquivo -->
                                <!-- <span><?php echo isset($imagem->anexo) ? $imagem->anexo : 'N/A'; ?></span> -->
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>Nenhuma imagem encontrada.</p>
                    <?php endif; ?>
                </div>
            </div>

<!-- Modal visualizar produto -->
<div id="modal-produto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Visualizar Produto</h3>
    </div>
    <div class="modal-body">
        <div class="span12" id="div-visualizar-produto" style="text-align: center">
            <!-- Contêiner de imagem com zoom -->
            <a href="" id="imagem-zoom" data-lightbox="image-1">
                <img id="imagem-produto" src="" alt="Imagem do Produto" style="max-width: 100%; height: auto; cursor: zoom-in;">
            </a>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <a href="" id="download" class="btn btn-inverse">Download</a>
        
    </div>
</div>

<script>
$(document).ready(function() {
    // Quando uma imagem é clicada
    $(document).on('click', '.anexo', function(event) {
        event.preventDefault();
        
        var link = $(this).attr('link'); // link para visualizar a imagem
        var id = $(this).attr('imagem'); // id da imagem
        var url = '<?php echo base_url(); ?>index.php/produtos/excluirImgAnexo/'; // URL para exclusão
        
        // Exibe a imagem no modal
        $("#div-visualizar-produto").html('<img src="' + link + '" alt="Produto">');
        
        // Atualiza o link de exclusão
        $("#excluir-produto").attr('link', url + id);
        
        // Atualiza o link de download
        $("#download").attr('href', "<?php echo base_url(); ?>index.php/produtos/downloadProduto/" + id);
        
        // Abre o modal
        $('#modal-produto').modal('show');
    });

    // Quando clicar para excluir o produto
    $(document).on('click', '#excluir-produto', function(event) {
        event.preventDefault();

        var link = $(this).attr('link');
        var idProduto = "<?php echo isset($result->idProduto) ? $result->idProduto : ''; ?>"; // Verifica se o idProduto está disponível

        // Esconde o modal
        $('#modal-produto').modal('hide');
        
        // Exibe uma barra de progresso enquanto exclui
        $("#divProdutos").html(
            "<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>"
        );

        // Faz a requisição AJAX para excluir o produto
        $.ajax({
            type: "POST",
            url: link,
            dataType: 'json',
            data: "idProduto=" + idProduto, // Envia o ID do produto
            success: function(data) {
                if (data.result == true) {
                    // Atualiza a lista de produtos
                    $("#divProdutos").load("<?php echo current_url(); ?> #divProdutos");
                } else {
                    // Exibe erro se houver problema
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: data.mensagem
                    });
                }
            }
        });
    });
});
</script>


