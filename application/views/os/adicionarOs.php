<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/trumbowyg/langs/pt_br.js"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Cadastro de OS</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">

                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes da OS</a></li>
                        
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divCadastrarOs">
                                <?php if ($custom_error == true) { ?>
                                    <div class="span12 alert alert-danger" id="divInfo" style="padding: 1%;">Dados
                                        incompletos, verifique os campos com asterisco ou se selecionou corretamente
                                        cliente, responsável e garantia.<br />Ou se tem um cliente e um termo de garantia
                                        cadastrado.</div>
                                    <?php } ?>
                                <form action="<?php echo current_url(); ?>" method="post" id="formOs">
                                    <div class="span12" style="padding: 1%">
                                        <div class="span5">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id"
                                                value="" />
                                        </div>
                                        <div class="span4">
                                            <label for="tecnico">Técnico / Responsável<span
                                                    class="required">*</span></label>
                                            <input id="tecnico" class="span12" type="text" name="tecnico"
                                                value="<?= $this->session->userdata(
                                                    "nome_admin"
                                                ) ?>" />
                                            <input id="usuarios_id" class="span12" type="hidden" name="usuarios_id"
                                                value="<?= $this->session->userdata(
                                                    "id_admin"
                                                ) ?>" />
                                        </div>

                                        <div class="span3">
                                            <label for="status">Status<span class="required">*</span></label>
                                            <select class="span12" name="status" id="status" value="">
                                                <option value="Orçamento">Orçamento</option>
                                                <option value="Aberto">Aberto</option>
                                                <option value="Em Andamento">Em Andamento</option>
                                                <option value="Finalizado">Finalizado</option>
                                                <option value="Cancelado">Cancelado</option>
                                                <option value="Aguardando Peças">Aguardando Peças</option>
                                                <option value="Aprovado">Aprovado</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">

                                        <div class="span3">
                                            <label for="dataInicial">Data Inicial<span class="required">*</span></label>
                                            <input id="dataInicial" autocomplete="off" class="span12 datepicker"
                                                type="text" name="dataInicial" value="<?php echo date(
                                                    "d/m/Y"
                                                ); ?>" />
                                        </div>


                                        <div class="span3">
                                            <label for="dataFinal">Previsão de Entrega<span
                                                    class="required">*</span></label>
                                            <input id="dataFinal" autocomplete="off" class="span12 datepicker"
                                                type="text" name="dataFinal" value="" />
                                        </div>
                                        <div class="span3">
                                            <label for="garantia">Garantia (dias)</label>
                                            <input id="garantia" type="number" placeholder="Status s/g inserir nº/0"
                                                min="0" max="9999" class="span12" name="garantia" value="" />
                                            <?php echo form_error(
                                                "garantia"
                                            ); ?>
                                        </div>

                                        <div class="span3">

                                            <label for="termoGarantia">Termo Garantia</label>
                                            <input id="termoGarantia" class="span12" type="text" name="termoGarantia"
                                                value="" />
                                            <input id="garantias_id" class="span12" type="hidden" name="garantias_id"
                                                value="" />
                                        </div>


                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span3">
                                            <label for="descricaoProduto">PRODUTO</label>
                                            <input name="descricaoProduto" class="span12" type="text" 
                                            
                                                id="descricaoProduto" value="" onChange="javascript:this.value=this.value.toUpperCase();"/>

                                        </div>

                                        <div class="span3">
                                            <label for="marcaProdutoOs">MARCA</label>
                                            <input name="marcaProdutoOs" class="span12" type="text" id="marcaProdutoOs"
                                                value="" onChange="javascript:this.value=this.value.toUpperCase();"/> 

                                        </div>

                                        <div class="span3">
                                            <label for="modeloProdutoOs">MODELO</label>
                                            <input name="modeloProdutoOs" class="span12" type="text"
                                                id="modeloProdutoOs" value="" onChange="javascript:this.value=this.value.toUpperCase();"/>

                                        </div>

                                        <div class="span3">
                                            <label for="nsProdutoOs">NÚMERO DE SÉRIE</label>
                                            <input name="nsProdutoOs" class="span12" type="text" id="nsProdutoOs"
                                                value="" onChange="javascript:this.value=this.value.toUpperCase();"/>

                                        </div>

                                    </div>


                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        
                                        <div class="span4">
                                            <label for="localizacaoProdutoOs">LOCALIZAÇÃO</label>
                                            <input name="localizacaoProdutoOs" class="span12" type="text" id="localizacaoProdutoOs"
                                                value="" onChange="javascript:this.value=this.value.toUpperCase();"/>

                                        </div>

                                        <div class="span4">
                                            <label for="defeito">Defeito reclamado pelo cliente</label>
                                            <input name="defeito" class="span12" type="text" id="defeito" value="" />

                                        </div>

                                        <div class="span4">
                                            <label for="analiseBasica">Defeito constatado em pré-análise</label>
                                            <input name="analiseBasica" class="span12" type="text" id="analiseBasica" value="" />

                                        </div>

                                        

                                    </div>


                                    <div class="span12" style="padding: 1%; margin-left: 0">

                                        

                                    </div>

                                    
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span6 offset3" style="display:flex">
                                            <button class="button btn btn-success" id="btnContinuar">
                                                <span class="button__icon"><i
                                                        class='bx bx-chevrons-right'></i></span><span
                                                    class="button__text2">Continuar</span></button>
                                            <a href="<?php echo base_url(); ?>index.php/os"
                                                class="button btn btn-mini btn-warning" style="max-width: 160px">
                                                <span class="button__icon"><i class="bx bx-undo"></i></span><span
                                                    class="button__text2">Voltar</span></a>
                                        </div>
                                    </div>


                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                .
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteCliente",
            minLength: 1,
            select: function (event, ui) {
                $("#clientes_id").val(ui.item.id);
            }
        });
        $("#tecnico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteUsuario",
            minLength: 1,
            select: function (event, ui) {
                $("#usuarios_id").val(ui.item.id);
            }
        });
        $("#termoGarantia").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteTermoGarantia",
            minLength: 1,
            select: function (event, ui) {
                $("#garantias_id").val(ui.item.id);
            }
        });

        $("#formOs").validate({
            rules: {
                cliente: {
                    required: true
                },
                tecnico: {
                    required: true
                },
                dataInicial: {
                    required: true
                },
                dataFinal: {
                    required: true
                }

            },
            messages: {
                cliente: {
                    required: 'Campo Requerido.'
                },
                tecnico: {
                    required: 'Campo Requerido.'
                },
                dataInicial: {
                    required: 'Campo Requerido.'
                },
                dataFinal: {
                    required: 'Campo Requerido.'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });
        $('.editor').trumbowyg({
            lang: 'pt_br'
        });
    });


    






    

</script>




