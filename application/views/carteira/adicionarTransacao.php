<style>
    .form-horizontal {
        margin-top: 0;
    }
    .help-block {
        margin-bottom: 0;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="bx bx-money"></i>
                </span>
                <h5><?php echo $this->uri->segment(2) === 'editar' ? 'Editar Transação' : 'Nova Transação' ?></h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                } ?>
                <form action="<?php echo current_url(); ?>" id="formTransacao" method="post" class="form-horizontal">
                    <?php if ($this->uri->segment(2) === 'editar') {
                        echo '<input type="hidden" name="idTransacoesUsuario" value="' . $result->idTransacoesUsuario . '" />';
                    } ?>

                    <div class="control-group">
                        <label for="tipo" class="control-label">Tipo de Transação<span class="required">*</span></label>
                        <div class="controls">
                            <select name="tipo" id="tipo" class="span8">
                                <option value="salario" <?php if (isset($result) && $result->tipo == 'salario') {
                                    echo 'selected';
                                } ?>>Salário</option>
                                <option value="bonus" <?php if (isset($result) && $result->tipo == 'bonus') {
                                    echo 'selected';
                                } ?>>Bônus</option>
                                <option value="comissao" <?php if (isset($result) && $result->tipo == 'comissao') {
                                    echo 'selected';
                                } ?>>Comissão</option>
                                <option value="retirada" <?php if (isset($result) && $result->tipo == 'retirada') {
                                    echo 'selected';
                                } ?>>Retirada</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="valor" class="control-label">Valor<span class="required">*</span></label>
                        <div class="controls">
                            <input id="valor" class="money span8" type="text" name="valor" value="<?php echo isset($result) ? $result->valor : ''; ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="data_transacao" class="control-label">Data<span class="required">*</span></label>
                        <div class="controls">
                            <input id="data_transacao" class="span8" type="date" name="data_transacao" value="<?php echo isset($result) ? $result->data_transacao : date('Y-m-d'); ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="descricao" class="control-label">Descrição</label>
                        <div class="controls">
                            <textarea name="descricao" id="descricao" class="span8" rows="3"><?php echo isset($result) ? $result->descricao : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success"><span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Salvar</span></button>
                                <a href="<?php echo base_url() ?>index.php/carteira" class="button btn btn-mini btn-warning"><span class="button__icon"><i class='bx bx-undo'></i></span><span class="button__text2">Voltar</span></a>
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
    $(document).ready(function() {
        $(".money").maskMoney();
        
        $('#formTransacao').validate({
            rules: {
                tipo: {
                    required: true
                },
                valor: {
                    required: true
                },
                data_transacao: {
                    required: true
                }
            },
            messages: {
                tipo: {
                    required: 'Campo obrigatório'
                },
                valor: {
                    required: 'Campo obrigatório'
                },
                data_transacao: {
                    required: 'Campo obrigatório'
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
    });
</script> 