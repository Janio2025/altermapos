<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-plus-circle"></i>
        </span>
        <h5>Cadastro de Carteira</h5>
    </div>
    <div class="widget-box">
        <div class="widget-content nopadding tab-content">
            <div id="tab1" class="tab-pane active" style="min-height: 300px">
                <form action="<?php echo base_url() ?>index.php/admincarteira/salvar" id="formCarteira" method="post" class="form-horizontal">
                    <div class="control-group">
                        <label for="usuario" class="control-label">Usuário<span class="required">*</span></label>
                        <div class="controls">
                            <select name="usuario" id="usuario" class="input-xlarge" required>
                                <option value="">Selecione um Usuário</option>
                                <?php foreach ($usuarios as $u) {
                                    echo '<option value="' . $u->idUsuarios . '">' . $u->nome . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="salario" class="control-label">Salário Base<span class="required">*</span></label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">R$</span>
                                <input id="salario" type="text" name="salario" class="money" required />
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="chave_pix" class="control-label">Chave PIX</label>
                        <div class="controls">
                            <input id="chave_pix" type="text" name="chave_pix" class="input-xlarge" placeholder="Digite a chave PIX (CPF, Email, Telefone ou Chave Aleatória)" />
                            <span class="help-inline">Esta chave PIX será usada para receber os saques da carteira</span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-success">
                                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span>
                                </button>
                                <a href="<?php echo base_url() ?>index.php/admincarteira" id="btnAdicionar" class="button btn btn-mini btn-warning" style="max-width: 160px; min-width: 150px">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>

<script>
    $(document).ready(function() {
        $('.money').maskMoney({
            prefix: '',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false,
            allowZero: true
        });

        $('#formCarteira').validate({
            rules: {
                usuario: {
                    required: true
                },
                salario: {
                    required: true
                }
            },
            messages: {
                usuario: {
                    required: 'Campo Requerido.'
                },
                salario: {
                    required: 'Campo Requerido.'
                }
            },
            submitHandler: function(form) {
                var salario = $('#salario').val();
                // Converte o valor formatado para o formato aceito pelo banco
                salario = salario.replace('.', '').replace(',', '.');
                $('#salario').val(salario);
                
                form.submit();
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
    });
</script>
