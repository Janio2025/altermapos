<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid" style="margin: 10px;">
    <div class="span12" style="max-width: 1200px; margin-left: 0;">
        <div class="widget-box" style="margin-bottom: 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div class="widget-title" style="background: #f8f9fa; border-radius: 8px 8px 0 0; padding: 10px 15px; border-bottom: 1px solid #e9ecef;">
                <span class="icon" style="margin-right: 10px;">
                    <i class="fas fa-cash-register" style="color: #0056b3;"></i>
                </span>
                <h5 style="color: #495057; margin: 0; font-size: 1.2em;">Carteira do Usuário</h5>
            </div>
            <div class="widget-content" style="background: #fff; padding: 15px; border-radius: 0 0 8px 8px;">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger" style="border-radius: 4px; margin-bottom: 15px;">' . $custom_error . '</div>';
                } ?>
                <div class="tab-pane active" id="tab1">
                    <form action="<?php echo base_url(); ?>index.php/admincarteira/realizarPagamento" id="formCarteira" method="post" class="form-horizontal">
                        
                        <?php 
                        $csrf = array(
                            'name' => $this->security->get_csrf_token_name(),
                            'hash' => $this->security->get_csrf_hash()
                        );
                        ?>
                        <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>" />

                        <div style="max-width: 800px; margin: 0 auto;">
                            <div class="control-group" style="margin-bottom: 15px;">
                                <label for="nome" class="control-label" style="font-weight: 500; color: #495057;">Nome<span class="required" style="color: #dc3545;">*</span></label>
                                <div class="controls">
                                    <input id="nome" type="text" name="nome" value="<?php echo $carteira->nome; ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                    <input id="idCarteiraUsuario" type="hidden" name="idCarteiraUsuario" value="<?php echo $carteira->idCarteiraUsuario; ?>" />
                                </div>
                            </div>

                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="saldo" class="control-label" style="font-weight: 500; color: #495057;">Saldo Atual<span class="required" style="color: #dc3545;">*</span></label>
                                        <div class="controls">
                                            <input id="saldo" type="text" name="saldo" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                            <input id="saldo_original" type="hidden" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="salario_base" class="control-label" style="font-weight: 500; color: #495057;">Salário Base<span class="required" style="color: #dc3545;">*</span></label>
                                        <div class="controls">
                                            <input id="salario_base" type="text" name="salario_base" value="<?php echo number_format($configuracao->salario_base, 2, ',', '.'); ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="comissao_fixa" class="control-label" style="font-weight: 500; color: #495057;">Comissão Fixa<span class="required" style="color: #dc3545;">*</span></label>
                                        <div class="controls">
                                            <input id="comissao_fixa" type="text" name="comissao_fixa" value="<?php echo number_format($configuracao->comissao_fixa, 2, ',', '.'); ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="data_pagamento" class="control-label" style="font-weight: 500; color: #495057;">Data de Pagamento<span class="required" style="color: #dc3545;">*</span></label>
                                        <div class="controls">
                                            <input id="data_pagamento" type="text" name="data_pagamento" value="<?php echo $configuracao->data_salario; ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) { ?>
                                <div class="control-group" style="margin-bottom: 15px;">
                                    <label for="comissao_pendente" class="control-label" style="font-weight: 500; color: #495057;">Comissão Pendente</label>
                                    <div class="controls" style="display: flex; align-items: center; gap: 10px;">
                                        <input id="comissao_pendente" type="text" name="comissao_pendente" value="<?php echo number_format($carteira->comissao_pendente, 2, ',', '.'); ?>" readonly style="width: calc(50% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                        <?php if ($carteira->comissao_pendente > 0) { ?>
                                            <button type="button" class="btn btn-success" onclick="receberComissao()" style="padding: 8px 15px; border-radius: 4px;">
                                                <i class="fas fa-check" style="margin-right: 5px;"></i> Receber Comissão
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="control-group" style="margin-bottom: 15px;">
                                    <label for="retirada_valor" class="control-label" style="font-weight: 500; color: #495057;">Valor da Retirada<span class="required" style="color: #dc3545;">*</span></label>
                                    <div class="controls">
                                        <div class="input-prepend" style="width: 100%; display: flex;">
                                            <span class="add-on" style="background: #e9ecef; border: 1px solid #ced4da; border-right: none; border-radius: 4px 0 0 4px; padding: 8px;">R$</span>
                                            <input id="retirada_valor" type="text" name="retirada_valor" value="" style="flex: 1; padding: 8px; border: 1px solid #ced4da; border-left: none; border-radius: 0 4px 4px 0;"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="control-group" style="margin-bottom: 15px;">
                                    <label for="retirada_descricao" class="control-label" style="font-weight: 500; color: #495057;">Descrição da Retirada<span class="required" style="color: #dc3545;">*</span></label>
                                    <div class="controls">
                                        <input id="retirada_descricao" type="text" name="retirada_descricao" value="" style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" onChange="javascript:this.value=this.value.toUpperCase();"/>
                                    </div>
                                </div>

                                <div class="form-actions" style="background: none; border: none; padding: 15px 0 0; margin: 0;">
                                    <div class="span12">
                                        <div class="span6 offset3" style="display: flex; justify-content: center; gap: 10px;">
                                            <button type="submit" class="button btn btn-primary" style="width: 180px; height: 36px; padding: 0 15px; border-radius: 4px; display: flex; align-items: center; justify-content: center; gap: 5px; font-size: 13px;">
                                                <i class='bx bx-save'></i>
                                                <span>Realizar Retirada</span>
                                            </button>
                                            <a href="<?php echo base_url() ?>index.php/admincarteira" class="button btn btn-warning" style="width: 180px; height: 36px; padding: 0 15px; border-radius: 4px; display: flex; align-items: center; justify-content: center; gap: 5px; font-size: 13px; text-decoration: none;">
                                                <i class="bx bx-undo"></i>
                                                <span>Voltar</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            data: {
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            }
        });

        $('#retirada_valor').maskMoney({
            prefix: 'R$ ',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });

        $('#formCarteira').validate({
            rules: {
                retirada_valor: { required: true },
                retirada_descricao: { required: true }
            },
            messages: {
                retirada_valor: { required: 'Campo Requerido.' },
                retirada_descricao: { required: 'Campo Requerido.' }
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
                
                valor = valor.replace('R$ ', '').replace('.', '').replace(',', '.');
                if (parseFloat(valor) <= 0) {
                    alert('O valor da retirada deve ser maior que zero!');
                    return false;
                }

                var saldo = $("#saldo_original").val().replace('R$ ', '').replace('.', '').replace(',', '.');
                if (parseFloat(valor) > parseFloat(saldo)) {
                    alert('O valor da retirada não pode ser maior que o saldo disponível!');
                    return false;
                }
                
                form.submit();
            }
        });

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
        Swal.fire({
            title: 'Confirmar Recebimento',
            text: 'Deseja realmente receber a comissão pendente?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sim, receber',
            cancelButtonText: 'Cancelar',
            background: '#fff',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
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
                            Swal.fire({
                                title: 'Sucesso!',
                                text: 'Comissão recebida com sucesso!',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Erro ao receber comissão: ' + response.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Erro ao processar a requisição.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }
</script>
