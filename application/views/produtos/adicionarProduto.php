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
                                                <label for="userfile"></label>
                                                <input type="file" class="span12" name="userfile[]" id="userfile"
                                                    multiple="multiple" size="20" onchange="previewImages(event)" />
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
                                            class=" span2 btn btn-primary">+</button>
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
                                    <label for="nsProduto" class="control-label">Número de Série<span
                                            class=""></span></label>
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
                                        <label for="localizacaoProduto" class="control-label">Localização<span
                                                class="required">*</span></label>
                                        <div class="controls">
                                            <input id="localizacaoProduto" class="span12" type="text"
                                                name="localizacaoProduto"
                                                value="<?php echo set_value('localizacaoProduto'); ?>"
                                                onChange="javascript:this.value=this.value.toUpperCase();" />
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
                                                <input type="checkbox" id="entrada" name="entrada" class=" badgebox"
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
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
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
        // Usando parseFloat para converter a string em número, e limpando a string antes da conversão
        var precoCompra = parseFloat($("#precoCompra").val().replace(/[^0-9.]/g, '')) || 0;
        var lucro = parseFloat($("#Lucro").val().replace(/[^0-9.]/g, '')) || 0;

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
        $(".money").maskMoney({ allowZero: true });
        $(".money").maskMoney();
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

        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        document.getElementById('precoCompra').type = 'tel';
        document.getElementById('precoVenda').type = 'tel';
        // Adicionando o atributo inputmode para otimizar o teclado virtual
        document.getElementById('precoCompra').inputMode = 'decimal'; 
        document.getElementById('precoVenda').inputMode = 'decimal'; 
    }
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
            alert('Por favor, preencha todos os campos Modelo Compatível antes de adicionar um novo.');
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.className.includes('removeCompativelProduto')) {
            e.target.parentElement.parentElement.remove();
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

