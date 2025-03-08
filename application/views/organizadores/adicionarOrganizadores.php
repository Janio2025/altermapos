<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>
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

    .control-group.error .help-inline {
        display: flex;
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
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-boxes"></i>
                </span>
                <h5>Cadastro de Organizador</h5>
            </div>
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?php echo current_url(); ?>" id="formOrganizador" method="post" class="form-horizontal">
                <div class="widget-content nopadding tab-content">
                    <div class="span6">
                        <div class="control-group">
                            <label for="nome_organizador" class="control-label">Nome do Organizador<span class="required">*</span></label>
                            <div class="controls">
                                <input id="nome_organizador" type="text" name="nome_organizador" value="<?php echo set_value('nome_organizador'); ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="localizacao" class="control-label">Localização</label>
                            <div class="controls">
                                <input id="localizacao" type="text" name="localizacao" value="<?php echo set_value('localizacao'); ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="ativa" class="control-label">Status</label>
                            <div class="controls">
                                <label for="ativa" class="btn btn-default">Ativo
                                    <input type="checkbox" id="ativa" name="ativa" class="badgebox" value="1" <?php echo set_checkbox('ativa', '1', true); ?>>
                                    <span class="badge">&check;</span>
                                </label>
                            </div>
                        </div>

                        <!-- Campo para adicionar compartimentos -->
                        <div class="control-group">
                            <label for="nome_compartimento" class="control-label">Compartimento</label>
                            <div class="controls">
                                <input id="nome_compartimento" class="" type="text" name="nome_compartimento[]"
                                    value="<?php echo set_value('nome_compartimento'); ?>"
                                    onChange="javascript:this.value=this.value.toUpperCase();" />
                                <button type="button" id="addCompartimento" class=" btn btn-primary">+</button>
                            </div>
                        </div>
                        <div id="additionalCompartimentos"></div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="span12">
                        <div class="span6 offset3" style="display:flex;justify-content: center">
                            <button type="submit" class="button btn btn-mini btn-success"><span class="button__icon"><i class='bx bx-save'></i></span> <span class="button__text2">Salvar</span></button>
                            <a title="Voltar" class="button btn btn-warning" href="<?php echo site_url() ?>/organizadores"><span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#nome_organizador").focus();
        $('#formOrganizador').validate({
            rules: {
                nome_organizador: {
                    required: true
                },
            },
            messages: {
                nome_organizador: {
                    required: 'Campo Requerido.'
                },
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

    // Script para adicionar campos de compartimento dinamicamente
    let compartimentoCounter = 1;

    document.getElementById('addCompartimento').addEventListener('click', function () {
        const inputs = document.querySelectorAll('input[name="nome_compartimento[]"]');
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
                <label for="nome_compartimento_${compartimentoCounter}" class="control-label">Compartimento<span class="required"></span></label>
                <div class="controls">
                    <input id="nome_compartimento_${compartimentoCounter}" class="" type="text" name="nome_compartimento[]"
                        value=""
                        onChange="javascript:this.value=this.value.toUpperCase();" />
                    <button type="button" class=" btn btn-danger removeCompartimento">x</button>
                </div>
            `;
            document.getElementById('additionalCompartimentos').appendChild(newInput);
            compartimentoCounter++;
        } else {
            alert('Por favor, preencha todos os campos de compartimento antes de adicionar um novo.');
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.className.includes('removeCompartimento')) {
            e.target.parentElement.parentElement.remove();
        }
    });
</script>

<script></script>