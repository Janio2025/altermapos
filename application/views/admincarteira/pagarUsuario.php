<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-cash-register"></i>
                </span>
                <h5>Carteira do Usuário</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                } ?>
                <div class="tab-pane active" id="tab1">
                    <form action="<?php echo base_url(); ?>index.php/admincarteira/realizarPagamento" id="formCarteira" method="post" class="form-horizontal">
                        <div class="control-group">
                            <label for="nome" class="control-label">Nome<span class="required">*</span></label>
                            <div class="controls">
                                <input id="nome" type="text" name="nome" value="<?php echo $carteira->nome; ?>" readonly/>
                                <input id="idCarteiraUsuario" type="hidden" name="idCarteiraUsuario" value="<?php echo $carteira->idCarteiraUsuario; ?>" />
                            </div>
                        </div>

                        <?php 
                        // Adiciona o token CSRF como campo oculto
                        $csrf = array(
                            'name' => $this->security->get_csrf_token_name(),
                            'hash' => $this->security->get_csrf_hash()
                        );
                        ?>
                        <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>" />

                        <div class="control-group">
                            <label for="saldo" class="control-label">Saldo Atual<span class="required">*</span></label>
                            <div class="controls">
                                <input id="saldo" type="text" name="saldo" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>" readonly/>
                                <input id="saldo_original" type="hidden" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="salario_base" class="control-label">Salário Base<span class="required">*</span></label>
                            <div class="controls">
                                <input id="salario_base" type="text" name="salario_base" value="<?php echo number_format($configuracao->salario_base, 2, ',', '.'); ?>" readonly/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="comissao_fixa" class="control-label">Comissão Fixa<span class="required">*</span></label>
                            <div class="controls">
                                <input id="comissao_fixa" type="text" name="comissao_fixa" value="<?php echo number_format($configuracao->comissao_fixa, 2, ',', '.'); ?>" readonly/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="data_pagamento" class="control-label">Data de Pagamento<span class="required">*</span></label>
                            <div class="controls">
                                <input id="data_pagamento" type="text" name="data_pagamento" value="<?php echo $configuracao->data_salario; ?>" readonly/>
                            </div>
                        </div>

                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) { ?>
                            <div class="control-group">
                                <label for="comissao_pendente" class="control-label">Comissão Pendente</label>
                                <div class="controls">
                                    <input id="comissao_pendente" type="text" name="comissao_pendente" value="<?php echo number_format($carteira->comissao_pendente, 2, ',', '.'); ?>" readonly/>
                                    <?php if ($carteira->comissao_pendente > 0) { ?>
                                        <button type="button" class="btn btn-success" onclick="receberComissao()">
                                            <i class="fas fa-check"></i> Receber Comissão
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="retirada_valor" class="control-label">Valor da Retirada<span class="required">*</span></label>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on">R$</span>
                                        <input id="retirada_valor" type="text" name="retirada_valor" value="" />
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="retirada_descricao" class="control-label">Descrição da Retirada<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="retirada_descricao" type="text" name="retirada_descricao" value="" class="span6" />
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="span12">
                                    <div class="span6 offset3" style="display:flex;justify-content: center">
                                        <button type="submit" class="button btn btn-primary">
                                            <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Realizar Retirada</span>
                                        </button>
                                        <a href="<?php echo base_url() ?>index.php/admincarteira" id="btnAdicionar" class="button btn btn-mini btn-warning" style="max-width: 160px">
                                            <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            data: {
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            }
        });

        // Inicializa a máscara de dinheiro
        $('#retirada_valor').maskMoney({
            prefix: 'R$ ',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });

        $('#formCarteira').validate({
            rules: {
                retirada_valor: {
                    required: true
                },
                retirada_descricao: {
                    required: true
                }
            },
            messages: {
                retirada_valor: {
                    required: 'Campo Requerido.'
                },
                retirada_descricao: {
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
            },
            submitHandler: function(form) {
                var valor = $("#retirada_valor").val();
                if (!valor) {
                    alert('Por favor, informe o valor da retirada.');
                    return false;
                }
                
                // Remove o prefixo R$ e converte para float
                valor = valor.replace('R$ ', '').replace('.', '').replace(',', '.');
                if (parseFloat(valor) <= 0) {
                    alert('O valor da retirada deve ser maior que zero!');
                    return false;
                }

                // Verifica se o valor não é maior que o saldo
                var saldo = $("#saldo_original").val().replace('R$ ', '').replace('.', '').replace(',', '.');
                if (parseFloat(valor) > parseFloat(saldo)) {
                    alert('O valor da retirada não pode ser maior que o saldo disponível!');
                    return false;
                }
                
                form.submit();
            }
        });

        // Atualiza o saldo previsto quando digitar o valor da retirada
        $("#retirada_valor").on('keyup', function() {
            var valor = $(this).val().replace('R$ ', '').replace('.', '').replace(',', '.');
            var saldo_original = $("#saldo_original").val().replace('R$ ', '').replace('.', '').replace(',', '.');
            
            if (valor === '' || isNaN(valor)) {
                valor = 0;
            }
            
            var novo_saldo = parseFloat(saldo_original) - parseFloat(valor);
            if (novo_saldo < 0) {
                novo_saldo = 0;
            }
            
            $("#saldo").val('R$ ' + novo_saldo.toFixed(2).replace('.', ','));
        });
    });

    function receberComissao() {
        if (confirm('Deseja realmente receber a comissão pendente?')) {
            var id = $("#idCarteiraUsuario").val();
            var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
            var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
            
            var data = {};
            data[csrf_name] = csrf_hash;
            
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/admincarteira/receberComissao/' + id,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Comissão recebida com sucesso!');
                        window.location.reload();
                    } else {
                        alert('Erro ao receber comissão: ' + response.message);
                    }
                },
                error: function() {
                    alert('Erro ao processar a requisição.');
                }
            });
        }
    }
</script>
