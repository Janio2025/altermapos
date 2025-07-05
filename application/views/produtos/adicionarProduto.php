<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- jQuery (versão 3.6.0) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI (versão 1.12.1) -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Outros scripts da página -->
<script src="<?php echo base_url(); ?>assets/js/funcoes.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>

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

    /* Estilo para os itens do Select2 */
    .select2-container--default .select2-results__option {
        padding: 8px 12px; /* Espaçamento interno */
        margin: 2px 0; /* Espaçamento entre os itens */
    }

    /* Estilo para o dropdown do Select2 */
    .select2-container--default .select2-dropdown {
        border-radius: 4px; /* Bordas arredondadas */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra */
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

    .image-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgb(0 109 204);
        ;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
        transition: background-color 0.3s ease;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-nav-btn:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }

    #prevBtn {
        left: 0;
    }

    #nextBtn {
        right: 0;
    }

    .div-bord {
        border: 0px solid black;
        /* Define a borda */
        padding: 1%;
        /* Define o padding de 5% em todos os lados */
        margin-bottom: 2%;
        /* Adiciona um espaço de 5% abaixo de cada div */
    }

    .div-teste {
        border: 1px solid black;
        /* Define a borda */
        padding: 1%;
        /* Define o padding de 5% em todos os lados */
        margin-bottom: 2%;
        /* Adiciona um espaço de 5% abaixo de cada div */
    }

    /* Estilos para a nova seção de movimento e preços */
    .movimento-btns {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .movimento-btns label {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .movimento-btns label:hover {
        background-color: #f8f9fa;
    }

    .movimento-btns .badge {
        margin-left: 5px;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .span4 {
            width: 100% !important;
            margin-left: 0 !important;
            margin-bottom: 20px;
        }

        .row-fluid [class*="span"] {
            width: 100%;
            margin-left: 0;
            float: none;
        }

        .movimento-btns {
            flex-direction: row;
            width: 100%;
        }

        .movimento-btns label {
            width: 50%;
        }
    }

    /* Estilos para compartimentos ocupados */
    .ocupado {
        color: red;
        font-weight: bold;
    }

    /* Estilo para o select2 com itens ocupados */
    .select2-results__option.ocupado {
        color: red;
        font-weight: bold;
    }


    
    .checkbox input[type="checkbox"] {
        margin-right: 8px;
    }
    
    .checkbox label {
        cursor: pointer;
        font-weight: normal;
    }
    
    .checkbox input[type="checkbox"]:checked + i {
        color: #28a745;
    }
    
    #preview_anuncio {
        background: linear-gradient(135deg, #17a2b8, #138496);
        border: none;
        transition: all 0.3s ease;
    }
    
    #preview_anuncio:hover {
        background: linear-gradient(135deg, #138496, #117a8b);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(23,162,184,0.3);
    }

    .dropdown-categorias-ml {
        position: relative;
        width: 100%;
        max-width: 400px;
    }
    .dropdown-categorias-ml .menu {
        border: 1px solid #ccc;
        background: #fff;
        width: 100%;
        position: absolute;
        z-index: 1000;
        display: none;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .dropdown-categorias-ml .menu .item {
        padding: 8px 16px;
        cursor: pointer;
        position: relative;
        white-space: nowrap;
    }
    .dropdown-categorias-ml .menu .item:hover {
        background: #f0f0f0;
    }
    .dropdown-categorias-ml .menu .submenu {
        display: none;
        position: absolute;
        left: 100%;
        top: 0;
        min-width: 180px;
        border: 1px solid #ccc;
        background: #fff;
        z-index: 1001;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .dropdown-categorias-ml .menu .item.has-sub:hover > .submenu {
        display: block;
    }
    .dropdown-categorias-ml .selected {
        border: 1px solid #ccc;
        padding: 8px 16px;
        background: #fff;
        cursor: pointer;
        width: 100%;
        box-sizing: border-box;
    }
</style>



<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-shopping-bag"></i>
                </span>
                <h5>Cadastro de Produto</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formProduto" enctype="multipart/form-data" method="post"
                    class="form-horizontal">
                    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    <div class="span12 div-teste">
                        <div>
                            <div class="span3 div-bord" style="padding: 1%; margin-left: 1">
                                <div class="control-group span12">
                                    <div class="span12">
                                        <div class="span12" style="position: relative; text-align: center;">
                                            <button id="prevBtn" type="button" onclick="prevImage()"
                                                class="image-nav-btn">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <img id="preview"
                                                src="<?php echo base_url('assets/img/produtoIcon.png'); ?>"
                                                alt="Pré-visualização da Imagem"
                                                style="max-height: 300px; width: auto; margin-top: 20px;" />
                                            <button id="nextBtn" type="button" onclick="nextImage()"
                                                class="image-nav-btn">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <div id="formAnexos" action="javascript:;" accept-charset="utf-8" method="post">
                                            <div class="span10">
                                                <input type="hidden" name="idProdutoImg" id="idProdutoImg" value="" />
                                                <label for="userfile">Imagens do Produto</label>
                                                <input type="file" class="span12" name="userfile[]" id="userfile"
                                                    multiple="multiple" size="20" onchange="previewImages(event)" />
                                            </div>
                                        </div>
                                    </div>

                                    
                                </div>
                                </div>
                            </div>

            
                            
                          
                            <div class="span3 div-bord" style="padding: 1%; margin-left: 1">
                                <div class="control-group">
                                    <label for="descricao" class="control-label">Produto / Peça <span
                                            class="required">*</span></label>
                                    <div class="controls">
                                        <input id="descricao" class="span12" type="text" name="descricao"
                                            value="<?php echo set_value('descricao'); ?>"
                                            onChange="javascript:this.value=this.value.toUpperCase();" />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="marcaProduto" class="control-label">Marca<span
                                            class="required">*</span></label>
                                    <div class="controls">
                                        <input id="marcaProduto" class="span12" type="text" name="marcaProduto"
                                            value="<?php echo set_value('marcaProduto'); ?>"
                                            onChange="javascript:this.value=this.value.toUpperCase();" />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="modeloProduto" class="control-label">Modelo<span
                                            class="required">*</span></label>
                                    <div class="controls">
                                        <input id="modeloProduto" class="span10" type="text" name="modeloProduto"
                                            value="<?php echo set_value('modeloProduto'); ?>"
                                            onChange="javascript:this.value=this.value.toUpperCase();" />
                                        <button type="button" id="addCompativelProduto"
                                            class="span2 btn btn-primary">+</button>
                                    </div>
                                </div>
                                <div id="additionalCompativelProdutos"></div>
                            </div>
                            <div class="span3 div-bord" style="padding: 1%; margin-left: 1">
                                <div class=" control-group">
                                    <label for="codigoPeca" class="control-label">Código da Peça<span
                                            class="required">*</span></label>
                                    <div class="controls">
                                        <input id="codigoPeca" class="span12" type="text" name="codigoPeca"
                                            value="<?php echo set_value('codigoPeca'); ?>"
                                            onChange="javascript:this.value=this.value.toUpperCase();" />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="nsProduto" class="control-label">Número de Série</label>
                                    <div class="controls">
                                        <input id="nsProduto" class="span12" type="text" name="nsProduto"
                                            value="<?php echo set_value('nsProduto'); ?>"
                                            onChange="javascript:this.value=this.value.toUpperCase();" />
                                    </div>
                                </div>
                                <div>
                                    <div class="">
                                        <div class="control-group">
                                            <label for="condicaoProduto" class="control-label">Condições do Produto<span
                                                    class="required"></span></label>
                                            <div class="controls">
                                                <select class="span12" name="condicaoProduto" id="condicaoProduto">
                                                    <option value="Novo">Novo</option>
                                                    <option value="Usado">Usado</option>
                                                    <option value="Recondicionado">Recondicionado</option>
                                                    <option value="Suspeito">Suspeito</option>
                                                    <option value="Defeito">Defeito</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label for="direcaoProduto" class="control-label">Direcionado (a)<span
                                                    class="required"></span></label>
                                            <div class="controls">
                                                <select class="span12" name="direcaoProduto" id="direcaoProduto">
                                                    <option value="Estoque">Estoque</option>
                                                    <option value="Garantia">Garantia</option>
                                                    <option value="Pedido">Pedido</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label for="organizador_id" class="control-label">Organizador<span class="required">*</span></label>
                                        <div class="controls">
                                            <select id="organizador_id" name="organizador_id" class="span12 select2">
                                                <option value="">Buscar organizador...</option>
                                                <?php foreach ($organizadores as $organizador) : ?>
                                                    <option value="<?php echo $organizador->id; ?>">
                                                        <?php echo $organizador->nome_organizador; ?> - <?php echo $organizador->localizacao; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label for="compartimento_id" class="control-label">Compartimento</label>
                                        <div class="controls">
                                            <select id="compartimento_id" name="compartimento_id" class="span12">
                                                <option value="">Selecione primeiro um organizador</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="span3 div-bord" style="padding: 1%; margin-left: 1">

                                <div class="span12 control-group">
                                    <label class="control-label">Tipo de Movimento</label>
                                    <div>
                                        <div class="span12 controls">
                                            <label for="entrada" class="span6 btn btn-default"
                                                style="margin-top: 5px;">Entrada
                                                <input type="checkbox" id="entrada" name="entrada" class="badgebox"
                                                    value="1" checked>
                                                <span class="badge">&check;</span>
                                            </label>
                                            <label for="saida" class="span6 btn btn-default"
                                                style="margin-top: 5px;">Saída
                                                <input type="checkbox" id="saida" name="saida" class="badgebox"
                                                    value="1" checked>
                                                <span class="badge">&check;</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- #region -->
                                <div>
                                    <div class="span12 control-group">
                                        <label for="unidade" class="control-label">Unidade<span
                                                class="required">*</span></label>
                                        <div class="controls">
                                            <select class="span12" id="unidade" name="unidade"></select>
                                        </div>
                                    </div>
                                </div>
                                <!-- #region -->
                                <div>
                                    <div class="span12 control-group">
                                        <label for="estoque" class="control-label">Estoque<span
                                                class="required">*</span></label>
                                        <div class="controls">
                                            <input class="span12" id="estoque" type="text" name="estoque"
                                                value="<?php echo set_value('estoque'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <!-- #region -->
                                <div>
                                    <div class="span12 control-group">
                                        <label for="estoqueMinimo" class="control-label">Estoque Mínimo</label>
                                        <div class="controls">
                                            <input class="span12" id="estoqueMinimo" type="text" name="estoqueMinimo"
                                                value="<?php echo set_value('estoqueMinimo'); ?>" />
                                        </div>
                                    </div>
                                </div>

                                <!-- #region -->
                                
                                <!-- #modi -->
                                <div>
                                    <div class="span12 control-group">
                                        <div class="span6">
                                            <label for="precoCompra" class="control-label">Preço de Compra<span
                                                    class="required">*</span></label>
                                            <div class="controls">
                                                <input id="precoCompra" class="money span12" data-affixes-stay="true"
                                                    data-thousands="" data-decimal="." type="text" name="precoCompra"
                                                    value="<?php echo set_value('precoCompra'); ?>" />
                                            </div>

                                        </div>

                                        <div class="span6">
                                        <label for="Lucro" class="control-label">Lucro<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                <select id="selectLucro" name="selectLucro" class="span12">
                                                    <option value="markup">Markup</option>
                                                    <option value="margemLucro">Margem de Lucro</option>
                                                    </select>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                    <div>
                                        <div class="span5">
                                            <label for="Lucro" class="control-label">Porcentagem<span
                                                    class="required">*</span></label>
                                            <div class="controls">
                                            <input id="Lucro" name="Lucro" type="text" placeholder="%" maxlength="3" size="2" class="span12" />
                                            <i class="icon-info-sign tip-left" title="Markup: Porcentagem aplicada ao valor de compra | Margem de Lucro: Porcentagem aplicada ao valor de venda"></i>
                                            </div>

                                        </div>

                                        <div class="span7">
                                            <label for="precoVenda" class="control-label">Preço de Venda<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input id="precoVenda" class="money span12" data-affixes-stay="true"
                                                        data-thousands="" data-decimal="." type="text" name="precoVenda"
                                                        value="<?php echo set_value('precoVenda'); ?>" />
                                                </div>

                                            </div>
                                            
                                            <!-- Checkbox Sincronizar Mercado Livre -->
                                            <div class="span12 control-group" style="margin-top: 10px;">
                                                <div class="controls">
                                                    <label class="checkbox" style="font-weight: bold;">
                                                        <input type="checkbox" id="sincronizar_ml" name="sincronizar_ml" value="1" />
                                                        Sincronizar Com Mercado Livre
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="control-group ml-campos-adicionais" style="display:none;">
                                        <!-- Primeira linha: Categoria -->
                                        <div class="row-fluid" style="margin-bottom: 15px;">
                                            <div class="control-group span12">
                                                <label for="categoria_id" class="control-label">Categoria <span class="required">*</span></label>
                                                <div class="controls">
                                                    <select id="categoria_id" name="categoria_id" class="span12 select2">
                                                        <option value="">Selecione uma categoria...</option>
                                                        <?php if (isset($todas_categorias) && is_array($todas_categorias)): ?>
                                                            <?php foreach ($todas_categorias as $cat): ?>
                                                                <option value="<?php echo $cat->idCategorias; ?>">
                                                                    <?php echo $cat->categoria; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Segunda linha: Moeda, Modo de Compra, Tipo de Anúncio -->
                                        <div class="row-fluid" style="margin-bottom: 15px;">
                                            <div class="control-group span4">
                                                <label for="currency_id" class="control-label">Moeda</label>
                                                <div class="controls">
                                                    <select id="currency_id" name="currency_id" class="span12">
                                                        <option value="BRL">BRL (Real)</option>
                                                        <option value="USD">USD (Dólar)</option>
                                                        <option value="EUR">EUR (Euro)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group span4">
                                                <label for="buying_mode" class="control-label">Modo de Compra</label>
                                                <div class="controls">
                                                    <select id="buying_mode" name="buying_mode" class="span12">
                                                        <option value="">Selecione...</option>
                                                        <option value="buy_it_now">Comprar Agora</option>
                                                        <option value="classified">Classificado</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group span4">
                                                <label for="listing_type_id" class="control-label">Tipo de Anúncio</label>
                                                <div class="controls">
                                                    <select id="listing_type_id" name="listing_type_id" class="span12">
                                                        <option value="">Selecione...</option>
                                                        <option value="gold_pro">Gold Pro</option>
                                                        <option value="gold_special">Gold Special</option>
                                                        <option value="silver">Silver</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Terceira linha: Modo de Envio, Garantia -->
                                        <div class="row-fluid" style="margin-bottom: 15px;">
                                            <div class="control-group span6">
                                                <label for="shipping_mode" class="control-label">Modo de Envio</label>
                                                <div class="controls">
                                                    <select id="shipping_mode" name="shipping_mode" class="span12">
                                                        <option value="">Selecione...</option>
                                                        <option value="me2">Mercado Envios</option>
                                                        <option value="custom">Personalizado</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group span6">
                                                <label for="ml_garantia" class="control-label">Garantia (em dias)</label>
                                                <div class="controls">
                                                    <input id="ml_garantia" name="ml_garantia" type="number" min="0" class="span12" placeholder="Ex: 90" value="<?php echo set_value('ml_garantia'); ?>" />
                                                </div>
                                            </div>
                                        </div>

                                         <!-- Seção de Atributos Dinâmicos do Mercado Livre -->
                                        <div id="atributosMLContainer" style="display: none;">
                                            <div class="row-fluid" style="margin-bottom: 15px;">
                                                <div class="control-group span12">
                                                    <div class="controls">
                                                        <div id="atributosML">
                                                            <p style="color: #666; margin-bottom: 15px;">
                                                                <i class="fas fa-info-circle"></i> 
                                                                Selecione uma categoria do Mercado Livre para carregar os atributos necessários.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quarta linha: Descrição (largura total) -->
                                        <div class="row-fluid" style="margin-bottom: 15px;">
                                            <div class="control-group span12">
                                                <label for="ml_descricao" class="control-label">Descrição para o Mercado Livre <span class="required">*</span></label>
                                                <div class="controls">
                                                    <textarea id="ml_descricao" name="ml_descricao" class="span12" rows="3" maxlength="2000" placeholder="Descrição detalhada para o anúncio no Mercado Livre..."><?php echo set_value('ml_descricao'); ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                       
                                        
                                    </div>
                                    </div>
                                    
                               

                                
                                
                            </div>
                        </div>

                        </div>
                    </div>

            </div>

            <!-- #modificação 2 final //////////////////////////////////////////////////////////////////////////////////////////////////////-->
            <div class="form-actions">
                <div class="span12">
                    <div class="span6 offset3" style="display: flex;justify-content: center">
                        <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px"><span
                                class="button__icon"><i class='bx bx-plus-circle'></i></span><span
                                class="button__text2">Adicionar</span>
                        </button>
                        <a href="<?php echo base_url() ?>index.php/produtos" id=""
                            class="button btn btn-mini btn-warning"><span class="button__icon"><i
                                    class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
</div>


<script type="text/javascript">
    function calcLucro(precoCompra, Lucro) {
        var lucroTipo = $('#selectLucro').val();
        var precoVenda;
        
        if (lucroTipo === 'markup') {
            precoVenda = (precoCompra * (1 + Lucro / 100)).toFixed(2);
        } else if (lucroTipo === 'margemLucro') {
            precoVenda = (precoCompra / (1 - (Lucro / 100))).toFixed(2);
        }
        
        return precoVenda;
    }
    
    function atualizarPrecoVenda() {
        var precoCompra = Number($("#precoCompra").val());
        var lucro = Number($("#Lucro").val());
        
        if (precoCompra > 0 && lucro >= 0) {
            $('#precoVenda').val(calcLucro(precoCompra, lucro));
        }
    }
    
    $("#precoCompra, #Lucro, #selectLucro").on('input change', atualizarPrecoVenda);

    $("#precoCompra, #Lucro").on('input change', function() {
        if ($("#precoCompra").val() == '0.00' && $('#precoVenda').val() != '') {
            $('#errorAlert').text('Você não pode preencher valor de compra e depois apagar.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
            $("#precoCompra").focus();
        } else if ($("#precoCompra").val() != '' && $("#Lucro").val() != '') {
            atualizarPrecoVenda();
        }
    });

    $("#Lucro").keyup(function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ($("#precoCompra").val() == null || $("#precoCompra").val() == '') {
            $('#errorAlert').text('Preencher valor da compra primeiro.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#precoVenda').val('');
            $("#precoCompra").focus();

        } else if (Number($("#Lucro").val()) >= 0) {
            $('#precoVenda').val(calcLucro(Number($("#precoCompra").val()), Number($("#Lucro").val())));
        } else {
            $('#errorAlert').text('Não é permitido número negativo.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#precoVenda').val('');
        }
    });

    $('#precoVenda').focusout(function () {
        if (Number($('#precoVenda').val()) < Number($("#precoCompra").val())) {
            $('#errorAlert').text('Preço de venda não pode ser menor que o preço de compra.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
        }
    });

    $(document).ready(function() {
        $(".money").maskMoney({
            // Opções adicionais do maskMoney para atualização imediata (teste essas opções)
            // updateOnFocus: true,
            // selectOnKeydown: true
        });

        // Atualização imediata da máscara no evento 'input'
        $("#precoCompra, #Lucro").on('input', function() {
            $(this).maskMoney('mask'); // Aplica a máscara imediatamente
            atualizarPrecoVenda();
        });

        // Removendo o evento blur pois a mascara será aplicada imediatamente com o input.
        //$("#precoCompra, #Lucro").on('blur', function() {
        //    $(this).maskMoney('mask');
        //    atualizarPrecoVenda();
        //});



        $.getJSON('<?php echo base_url() ?>assets/json/tabela_medidas.json', function(data) {
            for (i in data.medidas) {
                $('#unidade').append(new Option(data.medidas[i].descricao, data.medidas[i].sigla));
            }
        });
        $('#formProduto').validate({
            rules: {
                descricao: {
                    required: true
                },
                unidade: {
                    required: true
                },
                precoCompra: {
                    required: true
                },
                precoVenda: {
                    required: true
                },
                estoque: {
                    required: true
                },
                organizador_id: {
                    required: true
                }
            },
            messages: {
                descricao: {
                    required: 'Campo Requerido.'
                },
                unidade: {
                    required: 'Campo Requerido.'
                },
                precoCompra: {
                    required: 'Campo Requerido.'
                },
                precoVenda: {
                    required: 'Campo Requerido.'
                },
                estoque: {
                    required: 'Campo Requerido.'
                },
                organizador_id: {
                    required: 'Selecione um organizador.'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        // Mapeamento automático da condição do produto
        $("#condicaoProduto").change(function() {
            var condicao = $(this).val();
            if (condicao === "Novo") {
                $("#ml_condicao").val("new");
            } else if (condicao === "Usado") {
                $("#ml_condicao").val("used");
            }
        });

        // Auto-preenchimento da descrição ML baseada na descrição do produto
        $("#descricao").on('input', function() {
            if (!$("#ml_descricao").val()) {
                var descricao = $(this).val();
                var marca = $("#marcaProduto").val();
                var modelo = $("#modeloProduto").val();
                
                if (descricao && marca) {
                    var descricaoML = descricao + " - " + marca;
                    if (modelo) {
                        descricaoML += " " + modelo;
                    }
                    descricaoML += "\n\nProduto de qualidade com garantia.\nEntrega rápida e segura.";
                    $("#ml_descricao").val(descricaoML);
                }
            }
        });

        // Auto-preenchimento de tags baseado na descrição
        $("#descricao, #marcaProduto, #modeloProduto").on('input', function() {
            if (!$("#ml_tags").val()) {
                var descricao = $("#descricao").val();
                var marca = $("#marcaProduto").val();
                var modelo = $("#modeloProduto").val();
                
                var tags = [];
                if (descricao) tags.push(descricao.toLowerCase());
                if (marca) tags.push(marca.toLowerCase());
                if (modelo) tags.push(modelo.toLowerCase());
                
                if (tags.length > 0) {
                    $("#ml_tags").val(tags.join(", "));
                }
            }
        });

        // Preview do anúncio
        $("#preview_anuncio").click(function() {
            var titulo = $("#descricao").val();
            var preco = $("#precoVenda").val();
            var categoria = $("#ml_categoria option:selected").text();
            var condicao = $("#ml_condicao option:selected").text();
            var garantia = $("#ml_garantia").val();
            var descricao = $("#ml_descricao").val();
            
            if (!titulo || !preco) {
                alert("Preencha pelo menos o título e preço do produto para visualizar o anúncio.");
                return;
            }

            var preview = "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 5px; background: #fff;'>";
            preview += "<h4 style='color: #333; margin-bottom: 10px;'>" + titulo + "</h4>";
            preview += "<p style='font-size: 18px; color: #28a745; font-weight: bold;'>R$ " + preco + "</p>";
            preview += "<p style='color: #666;'><strong>Categoria:</strong> " + categoria + "</p>";
            preview += "<p style='color: #666;'><strong>Condição:</strong> " + condicao + "</p>";
            if (garantia) {
                preview += "<p style='color: #666;'><strong>Garantia:</strong> " + garantia + " dias</p>";
            }
            if (descricao) {
                preview += "<p style='color: #666; margin-top: 10px;'><strong>Descrição:</strong><br>" + descricao + "</p>";
            }
            preview += "</div>";

            // Exibe o preview em um modal personalizado
            $("#modal-preview").find(".modal-body").html(preview);
            $("#modal-preview").modal('show');
        });

        // Autocomplete para buscar organizadores
    $("#buscarOrganizador").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "<?php echo site_url('organizadores/buscarOrganizadores'); ?>",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2, // Número mínimo de caracteres para iniciar a busca
        select: function(event, ui) {
            // Quando um organizador é selecionado, carregar seus compartimentos
            carregarCompartimentos(ui.item.id, ui.item.value); // Passar o nome do organizador
        }
    });

    // Função para carregar os compartimentos de um organizador
    function carregarCompartimentos(organizadorId, organizadorNome) {
        $.ajax({
            url: "<?php echo site_url('organizadores/buscarCompartimentos'); ?>",
            dataType: "json",
            data: {
                organizador_id: organizadorId
            },
            success: function(data) {
                // Limpar o dropdown de compartimentos
                $("#compartimentosDisponiveis").empty();

                // Adicionar os compartimentos ao dropdown
                if (data.length > 0) {
                    $.each(data, function(index, compartimento) {
                        $("#compartimentosDisponiveis").append(
                            `<option value="${compartimento.id}">${compartimento.nome_compartimento}</option>`
                        );
                    });
                } else {
                    $("#compartimentosDisponiveis").append(
                        `<option value="">Nenhum compartimento disponível</option>`
                    );
                }

                // Quando um compartimento é selecionado, preencher o campo oculto
                $('#compartimentosDisponiveis').on('change', function() {
                    const compartimentoId = $(this).val();
                    const compartimentoNome = $(this).find('option:selected').text();

                    // Preencher o campo oculto com o ID do organizador, nome do organizador e compartimento
                    $('#localizacaoProduto').val(`${organizadorId},${organizadorNome},${compartimentoNome}`);
                });
            }
        });
    }

    // Quando o formulário for enviado, salvar os compartimentos selecionados
    $("#formProduto").on("submit", function() {
        var compartimentosSelecionados = $("#compartimentosDisponiveis").val();
        if (compartimentosSelecionados) {
            $("#localizacaoProduto").val(compartimentosSelecionados.join(","));
        }
    });
    
    });


    /// anexos 

    $("#formAnexos").validate({
        submitHandler: function (form) {
            //var dados = $( form ).serialize();
            var dados = new FormData(form);
            $("#form-anexos").hide('1000');
            $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/produtos/imgAnexar",
                data: dados,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                success: function (data) {
                    if (data.result == true) {
                        $("#divAnexos").load("<?php echo current_url(); ?> #divAnexos");
                        $("#userfile").val('');

                    } else {
                        $("#divAnexos").html('<div class="alert fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>Atenção!</strong> ' + data.mensagem + '</div>');
                    }
                },
                error: function () {
                    $("#divAnexos").html('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>Atenção!</strong> Ocorreu um erro. Verifique se você anexou o(s) arquivo(s).</div>');
                }
            });
            $("#form-anexos").show('1000');
            return false;
        }
    });
    ///////

</script>

<script>
    let compativelProdutoCounter = 1;

    document.getElementById('addCompativelProduto').addEventListener('click', function () {
        const inputs = document.querySelectorAll('input[name="compativelProduto[]"]');
        let allFilled = true;

        inputs.forEach(function (input) {
            if (input.value.trim() === '') {
                allFilled = false;
            }
        });

        if (allFilled) {
            const newInput = document.createElement('div');
            newInput.className = 'control-group';
            newInput.innerHTML = `
            <label for="compativelProduto_${compativelProdutoCounter}" class="control-label">Modelo Compatível<span class="required"></span></label>
            <div class="controls">
                <input id="compativelProduto_${compativelProdutoCounter}" class="span10" type="text" name="compativelProduto[]"
                    value=""
                    onChange="javascript:this.value=this.value.toUpperCase();" />
                <button type="button" class="span2 btn btn-danger removeCompativelProduto">x</button>
            </div>
        `;
            document.getElementById('additionalCompativelProdutos').appendChild(newInput);
            compativelProdutoCounter++;
        } else {
            Swal.fire({
                title: 'Campo Obrigatório',
                text: 'Por favor, preencha todos os campos Modelo Compatível antes de adicionar um novo.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                background: '#fff',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    title: 'text-danger'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.className.includes('removeCompativelProduto')) {
            const elementToRemove = e.target.parentElement.parentElement;
            
            Swal.fire({
                title: 'Confirmar Exclusão',
                text: 'Deseja remover este modelo compatível?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar',
                background: '#fff',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    elementToRemove.remove();
                }
            });
        }
    });
</script>

<script>
    let currentImageIndex = 0;
    let images = [];

    function previewImages(event) {
        images = [];
        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = function (e) {
                images.push(e.target.result);
                if (i === 0) {
                    document.getElementById('preview').src = e.target.result;
                }
            };
            reader.readAsDataURL(files[i]);
        }
    }

    function prevImage() {
        if (images.length > 0) {
            currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
            document.getElementById('preview').src = images[currentImageIndex];
        }
    }

    function nextImage() {
        if (images.length > 0) {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            document.getElementById('preview').src = images[currentImageIndex];
        }
    }
</script>



<script>
$(document).ready(function() {
    // Autocomplete para buscar organizadores
    $("#buscarOrganizador").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "<?php echo site_url('organizadores/buscarOrganizadores'); ?>",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2, // Número mínimo de caracteres para iniciar a busca
        select: function(event, ui) {
            // Quando um organizador é selecionado, carregar seus compartimentos
            carregarCompartimentos(ui.item.id, ui.item.value); // Passar o nome do organizador
        }
    });

    // Inicializar o Select2 com seleção única
    $('#compartimentosDisponiveis').select2({
        placeholder: "Selecione um compartimento", // Texto de placeholder
        allowClear: true, // Permite limpar a seleção
        width: '100%', // Define a largura do dropdown
        maximumSelectionLength: 1 // Permite selecionar apenas um item
    });

    // Função para carregar os compartimentos de um organizador
    function carregarCompartimentos(organizadorId, organizadorNome) {
        $.ajax({
            url: "<?php echo site_url('organizadores/buscarCompartimentos'); ?>",
            dataType: "json",
            data: {
                organizador_id: organizadorId
            },
            success: function(data) {
                // Limpar o dropdown de compartimentos
                $("#compartimentosDisponiveis").empty();

                // Adicionar os compartimentos ao dropdown
                if (data.length > 0) {
                    $.each(data, function(index, compartimento) {
                        $("#compartimentosDisponiveis").append(
                            `<option value="${compartimento.id}">${compartimento.nome_compartimento}</option>`
                        );
                    });
                } else {
                    $("#compartimentosDisponiveis").append(
                        `<option value="">Nenhum compartimento disponível</option>`
                    );
                }

                // Quando um compartimento é selecionado, preencher o campo oculto
                $('#compartimentosDisponiveis').on('change', function() {
                    const compartimentoId = $(this).val();
                    const compartimentoNome = $(this).find('option:selected').text();

                    // Preencher o campo oculto com o ID do organizador, nome do organizador e compartimento
                    $('#localizacaoProduto').val(`${organizadorId},${organizadorNome},${compartimentoNome}`);
                });
            }
        });
    }
});
</script>

<script type="text/javascript">
    $(document).ready(function() {
        // Quando um organizador é selecionado
        $('#organizador_id').change(function() {
            var organizador_id = $(this).val();
            var compartimento_select = $('#compartimento_id');
            
            // Limpar o select de compartimentos
            compartimento_select.empty();
            
            if (organizador_id) {
                // Carregar compartimentos via AJAX
                $.ajax({
                    url: '<?php echo site_url('compartimentos/buscarCompartimentos'); ?>',
                    type: 'GET',
                    data: { organizador_id: organizador_id },
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.length > 0) {
                            // Se houver compartimentos, adiciona a opção de selecionar
                            compartimento_select.append('<option value="">Selecione um compartimento</option>');
                            // Adicionar os compartimentos ao select
                            $.each(data, function(index, item) {
                                let optionClass = item.quantidade > 0 ? 'ocupado' : '';
                                let optionText = item.quantidade > 0 ? 
                                    `${item.nome_compartimento} (${item.quantidade})` : 
                                    item.nome_compartimento;
                                
                                compartimento_select.append(
                                    $('<option></option>')
                                        .val(item.id)
                                        .text(optionText)
                                        .addClass(optionClass)
                                );
                            });
                        } else {
                            compartimento_select.append('<option value="">Organizador sem compartimentos</option>');
                        }
                    },
                    error: function() {
                        compartimento_select.append('<option value="">Erro ao carregar compartimentos</option>');
                    }
                });
            } else {
                compartimento_select.append('<option value="">Selecione primeiro um organizador</option>');
            }
        });

        // Inicializar o Select2 com template personalizado
        $('#compartimento_id').select2({
            templateResult: formatCompartimento,
            templateSelection: formatCompartimento
        });

        // Função para formatar a exibição do compartimento no Select2
        function formatCompartimento(compartimento) {
            if (!compartimento.id) return compartimento.text;
            
            let $compartimento = $(
                '<span class="' + $(compartimento.element).attr('class') + '">' + 
                compartimento.text + 
                '</span>'
            );
            
            return $compartimento;
        }

        // Quando um compartimento é selecionado, validar capacidade
        $('#compartimento_id').change(function() {
            var compartimento_id = $(this).val();
            if (compartimento_id) {
                $.ajax({
                    url: '<?php echo site_url('compartimentos/validarCapacidadeCompartimento'); ?>',
                    type: 'POST',
                    data: { compartimento_id: compartimento_id },
                    dataType: 'json',
                    success: function(response) {
                        if (!response.valido) {
                            Swal.fire({
                                title: 'Atenção!',
                                text: 'Este compartimento está com alta ocupação. Deseja continuar mesmo assim?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Sim, continuar',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (!result.isConfirmed) {
                                    $('#compartimento_id').val('').trigger('change');
                                }
                            });
                        }
                    }
                });
            }
        });
    });
</script>

<!-- Modal para Preview do Anúncio -->
<div id="modal-preview" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><i class="fas fa-eye"></i> Preview do Anúncio - Mercado Livre</h3>
    </div>
    <div class="modal-body">
        <!-- Conteúdo do preview será inserido aqui -->
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <button class="btn btn-success" onclick="salvarProdutoComML()">
            <i class="fas fa-save"></i> Salvar e Publicar no ML
        </button>
    </div>
</div>

<script>
// Função para salvar produto e publicar no Mercado Livre
function salvarProdutoComML() {
    // Fecha o modal de preview
    $("#modal-preview").modal('hide');
    
    // Submete o formulário
    $("#formProduto").submit();
}
</script>

<script>
// Mercado Livre: Busca dinâmica de subcategorias e atributos obrigatórios
$(document).ready(function() {
    // Elementos
    const categoriaSelect = $('#ml_categoria');
    const atributosContainer = $('<div id="ml_atributos_container"></div>');
    categoriaSelect.parent().append(atributosContainer);

    // Função para buscar subcategorias
    function buscarSubcategorias(category_id) {
        $.getJSON('<?php echo site_url('mercadolivre/buscarSubcategorias'); ?>', {category_id: category_id}, function(resp) {
            if (resp.success) {
                if (resp.subcategories && resp.subcategories.length > 0) {
                    // Monta select de subcategorias
                    let subSelect = $('<select class="span12 ml_subcategoria_select"></select>');
                    subSelect.append('<option value="">Selecione uma subcategoria...</option>');
                    resp.subcategories.forEach(function(sub) {
                        subSelect.append(`<option value="${sub.id}">${sub.name}</option>`);
                    });
                    atributosContainer.html(subSelect);
                    // Ao selecionar subcategoria, buscar recursivamente
                    subSelect.change(function() {
                        let subId = $(this).val();
                        if (subId) {
                            buscarSubcategorias(subId);
                            categoriaSelect.val(subId); // Atualiza o select principal
                        }
                    });
                } else {
                    // Categoria folha: buscar atributos obrigatórios
                    buscarAtributosObrigatorios(category_id);
                }
            } else {
                atributosContainer.html('<div class="alert alert-danger">Erro ao buscar subcategorias</div>');
            }
        });
    }

    // Função para buscar atributos obrigatórios
    function buscarAtributosObrigatorios(category_id) {
        $.getJSON('<?php echo site_url('mercadolivre/buscarAtributosCategoria'); ?>', {category_id: category_id}, function(resp) {
            if (resp.success) {
                let html = '<div class="ml-atributos">';
                resp.attributes.forEach(function(attr) {
                    if (attr.required || attr.catalog_required) {
                        html += `<div class="control-group">
                            <label class="control-label">${attr.name} <span class="required">*</span></label>
                            <div class="controls">`;
                        if (attr.values && attr.values.length > 0) {
                            html += `<select name="ml_atributo_${attr.id}" class="span12 ml-atributo-campo" data-ml-attr-id="${attr.id}" required>`;
                            html += '<option value="">Selecione...</option>';
                            attr.values.forEach(function(val) {
                                html += `<option value="${val.id}">${val.name}</option>`;
                            });
                            html += '</select>';
                        } else {
                            html += `<input type="text" name="ml_atributo_${attr.id}" class="span12 ml-atributo-campo" data-ml-attr-id="${attr.id}" required />`;
                        }
                        html += '</div></div>';
                    }
                });
                html += '</div>';
                atributosContainer.html(html);
            } else {
                atributosContainer.html('<div class="alert alert-danger">Erro ao buscar atributos</div>');
            }
        });
    }

    // Ao selecionar categoria, buscar subcategorias
    categoriaSelect.change(function() {
        let catId = $(this).val();
        if (catId) {
            buscarSubcategorias(catId);
        } else {
            atributosContainer.html('');
        }
    });

    // Validação dos atributos obrigatórios antes de enviar
    $('#formProduto').submit(function(e) {
        let valid = true;
        $('.ml-atributo-campo[required]').each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        if (!valid) {
            alert('Preencha todos os atributos obrigatórios do Mercado Livre!');
            e.preventDefault();
        }
    });
});
</script>

<script>
$(function() {
    var categorias = <?php echo json_encode($categorias_ml_agrupadas); ?>;
    var $dropdown = $('#dropdown-categorias-ml');
    var $input = $('#ml_categoria');
    var selectedText = 'Selecione uma categoria...';

    function buildMenu() {
        var html = '<div class="selected span12">' + selectedText + '</div><div class="menu">';
        categorias.forEach(function(item) {
            if (item.subcats.length > 0) {
                html += '<div class="item has-sub" data-id="' + item.mae.ml_id + '">' + item.mae.categoria;
                html += '<div class="submenu">';
                item.subcats.forEach(function(sub) {
                    html += '<div class="item" data-id="' + sub.ml_id + '">' + sub.categoria + '</div>';
                });
                html += '</div></div>';
            } else {
                html += '<div class="item" data-id="' + item.mae.ml_id + '">' + item.mae.categoria + '</div>';
            }
        });
        html += '</div>';
        $dropdown.html(html);
    }

    buildMenu();

    // Mostrar menu ao clicar
    $dropdown.on('click', '.selected', function(e) {
        e.stopPropagation();
        $dropdown.find('.menu').toggle();
    });

    // Selecionar categoria mãe sem sub ou subcategoria
    $dropdown.on('click', '.menu .item:not(.has-sub)', function(e) {
        var id = $(this).data('id');
        var text = $(this).text();
        $input.val(id);
        $dropdown.find('.selected').text(text);
        $dropdown.find('.menu').hide();
    });

    // Selecionar subcategoria
    $dropdown.on('click', '.submenu .item', function(e) {
        var id = $(this).data('id');
        var text = $(this).text();
        $input.val(id);
        $dropdown.find('.selected').text(text);
        $dropdown.find('.menu').hide();
        e.stopPropagation();
    });

    // Fechar menu ao clicar fora
    $(document).on('click', function() {
        $dropdown.find('.menu').hide();
    });
});
</script>

<script>
$(document).ready(function() {
    $('#sincronizar_ml').change(function() {
        if ($(this).is(':checked')) {
            $('.ml-campos-adicionais').show();
            $('#ml_descricao').attr('required', true);
        } else {
            $('.ml-campos-adicionais').hide();
            $('#ml_descricao').attr('required', false);
            $('#ml_descricao').val('');
            $('#ml_garantia').val('');
        }
    });
    // Exibir se já estiver marcado ao carregar
    if ($('#sincronizar_ml').is(':checked')) {
        $('.ml-campos-adicionais').show();
        $('#ml_descricao').attr('required', true);
    }

    // Carregar atributos dinamicamente quando categoria for selecionada
    $('#categoria_id').change(function() {
        var categoriaId = $(this).val();
        console.log('Categoria selecionada:', categoriaId);
        
        if (categoriaId) {
            carregarAtributosCategoria(categoriaId);
        } else {
            $('#atributosMLContainer').hide();
        }
    });

    // Função para carregar atributos da categoria
    function carregarAtributosCategoria(categoriaId) {
        console.log('Carregando atributos para categoria:', categoriaId);
        
        $.ajax({
            url: '<?php echo site_url('produtos/getAtributosCategoria'); ?>',
            type: 'GET',
            data: { categoria_id: categoriaId },
            dataType: 'json',
            success: function(resp) {
                console.log('Resposta da API de atributos:', resp);
                
                if (resp.success && resp.atributos && resp.atributos.length > 0) {
                    renderizarAtributos(resp.atributos);
                    $('#atributosMLContainer').show();
                    console.log('Atributos carregados com sucesso:', resp.atributos.length, 'atributos');
                } else {
                    $('#atributosMLContainer').hide();
                    console.log('Nenhum atributo encontrado para esta categoria');
                }
            },
            error: function(xhr, status, error) {
                console.log('Erro ao carregar atributos:', {xhr: xhr, status: status, error: error});
                $('#atributosMLContainer').hide();
            }
        });
    }

    // Função para renderizar atributos
    function renderizarAtributos(atributos) {
        console.log('Renderizando atributos:', atributos);
        
        var html = '';
        
        // Agrupar atributos em linhas de 3, 2 ou 1
        for (var i = 0; i < atributos.length; i += 3) {
            var atributosLinha = atributos.slice(i, i + 3);
            var totalLinha = atributosLinha.length;
            
            // Determinar as classes CSS baseado no número de atributos na linha
            var classeSpan;
            if (totalLinha === 3) {
                classeSpan = 'span4';
            } else if (totalLinha === 2) {
                classeSpan = 'span6';
            } else {
                classeSpan = 'span12';
            }
            
            html += '<div class="row-fluid" style="margin-bottom: 15px;">';
            
            atributosLinha.forEach(function(atributo) {
                console.log('Processando atributo:', atributo);
                
                html += '<div class="control-group ' + classeSpan + '">';
                html += '<label class="control-label">' + atributo.name;
                if (atributo.required) {
                    html += ' <span class="required">*</span>';
                }
                html += '</label>';
                html += '<div class="controls">';
                
                // Renderizar campo baseado no tipo
                if (atributo.value_type === 'list' && atributo.values && atributo.values.length > 0) {
                    html += '<select name="ml_atributo_' + atributo.ml_attribute_id + '" class="span12"';
                    if (atributo.required) {
                        html += ' required';
                    }
                    html += '>';
                    html += '<option value="">Selecione...</option>';
                    
                    // Decodificar valores JSON se necessário
                    var valores = atributo.values;
                    if (typeof valores === 'string') {
                        try {
                            valores = JSON.parse(valores);
                        } catch (e) {
                            console.log('Erro ao parsear valores JSON:', e);
                            valores = [];
                        }
                    }
                    
                    if (Array.isArray(valores)) {
                        valores.forEach(function(valor) {
                            html += `<option value="${valor.id}">${valor.name}</option>`;
                        });
                    }
                    html += '</select>';
                } else if (atributo.value_type === 'boolean') {
                    html += '<select name="ml_atributo_' + atributo.ml_attribute_id + '" class="span12"';
                    if (atributo.required) {
                        html += ' required';
                    }
                    html += '>';
                    html += '<option value="">Selecione...</option>';
                    html += '<option value="true">Sim</option>';
                    html += '<option value="false">Não</option>';
                    html += '</select>';
                } else if (atributo.value_type === 'number') {
                    html += '<input type="number" name="ml_atributo_' + atributo.ml_attribute_id + '" class="span12"';
                    if (atributo.required) {
                        html += ' required';
                    }
                    html += ' placeholder="Digite um número" />';
                } else {
                    // Campo de texto padrão
                    html += '<input type="text" name="ml_atributo_' + atributo.ml_attribute_id + '" class="span12"';
                    if (atributo.required) {
                        html += ' required';
                    }
                    html += ' placeholder="Digite o valor" />';
                }
                
                html += '</div>';
                html += '</div>';
            });
            
            html += '</div>'; // Fecha row-fluid
        }
        
        console.log('HTML gerado:', html);
        $('#atributosML').html(html);
    }
});
</script>
 <div></div>