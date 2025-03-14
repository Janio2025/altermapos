<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bxs-wallet"></i>
        </span>
        <h5>Editar Carteira</h5>
    </div>

    <div class="widget-box">
        <div class="widget-content nopadding tab-content">
            <div id="tab1" class="tab-pane active" style="min-height: 300px">
                <div class="widget_box_Painel2">
                    <?php if ($this->session->flashdata('error') != null) { ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php } ?>
                    <?php if ($this->session->flashdata('success') != null) { ?>
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                    <?php } ?>

                    <form action="<?php echo base_url(); ?>index.php/admincarteira/atualizar" id="formCarteira" method="post" class="form-horizontal">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                        <input type="hidden" name="idCarteiraUsuario" value="<?php echo $carteira->idCarteiraUsuario; ?>">
                        
                        <div class="control-group">
                            <label for="usuario" class="control-label">Usuário<span class="required">*</span></label>
                            <div class="controls">
                                <input type="text" class="input-xlarge" value="<?php echo $carteira->nome; ?>" readonly>
                                <input type="hidden" name="usuario" id="usuario" value="<?php echo $carteira->usuarios_id; ?>">
                            </div>
                        </div>

                        <!-- Configurações de Pagamento -->
                        <div class="control-group">
                            <label class="control-label">Configurações de Pagamento</label>
                            <div class="controls">
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" name="salario_base" id="salario_base" placeholder="Salário Base" value="<?php echo isset($config) ? number_format($config->salario_base, 2, ',', '.') : ''; ?>" required>
                                </div>
                                
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">%</span>
                                    <input type="number" step="0.01" min="0" max="100" name="comissao_fixa" id="comissao_fixa" placeholder="Comissão Fixa (%)" value="<?php echo isset($config) ? $config->comissao_fixa : ''; ?>">
                                </div>
                                
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on"><i class="bx bx-calendar"></i></span>
                                    <input type="number" name="data_salario" id="data_salario" placeholder="Dia do Pagamento" min="1" max="31" class="input-mini" value="<?php echo isset($config) ? $config->data_salario : ''; ?>" required>
                                </div>
                                
                                <div style="margin-bottom: 10px;">
                                    <label class="radio" style="display: inline-block; margin-right: 15px;">
                                        <input type="radio" name="tipo_repeticao" value="mensal" <?php echo (!isset($config) || $config->tipo_repeticao == 'mensal') ? 'checked' : ''; ?>> Mensal
                                    </label>
                                    <label class="radio" style="display: inline-block;">
                                        <input type="radio" name="tipo_repeticao" value="quinzenal" <?php echo (isset($config) && $config->tipo_repeticao == 'quinzenal') ? 'checked' : ''; ?>> Quinzenal
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Widget de Comissão Pendente -->
                        <div class="control-group">
                            <label class="control-label">Comissão Pendente</label>
                            <div class="controls">
                                <div class="widget-box span6" style="margin-bottom: 0;">
                                    <div class="widget-content">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 15px; padding: 15px;">
                                            <div class="comissao-value" style="font-size: 28px; color: #ffc107;">
                                                R$ <span id="comissao-pendente">0,00</span>
                                            </div>
                                            <div class="alert alert-info" style="margin-bottom: 0;">
                                                <i class='bx bx-info-circle'></i>
                                                Para realizar retiradas ou receber comissões, acesse a opção "Pagar Usuário" na listagem de carteiras.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção de Bônus -->
                        <div class="control-group">
                            <label class="control-label">Bônus</label>
                            <div class="controls">
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" name="bonus_valor" id="bonus_valor" placeholder="Valor do Bônus">
                                </div>
                                
                                <div style="margin-top: 10px;">
                                    <input type="text" class="span6" name="bonus_descricao" placeholder="Descrição do Bônus" onChange="javascript:this.value=this.value.toUpperCase();">
                                </div>
                            </div>
                        </div>

                        <!-- Seção de Comissão -->
                        <div class="control-group">
                            <label class="control-label">Comissão Adicional</label>
                            <div class="controls">
                                <div style="margin-bottom: 10px;">
                                    <select name="tipo_valor_base" id="tipo_valor_base" class="input-xlarge">
                                        <option value="">Selecione o tipo de valor base</option>
                                        <option value="servicos" <?php echo (isset($config) && $config->tipo_valor_base == 'servicos') ? 'selected' : ''; ?>>Serviços</option>
                                        <option value="total" <?php echo (isset($config) && $config->tipo_valor_base == 'total') ? 'selected' : ''; ?>>Produtos e Serviços</option>
                                    </select>
                                </div>

                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" name="comissao_base" id="comissao_base" placeholder="Valor Base da Comissão" readonly>
                                </div>
                                
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <input type="number" name="comissao_porcentagem" id="comissao_porcentagem" placeholder="Porcentagem" min="0" max="100" class="input-mini">
                                    <span class="add-on">%</span>
                                </div>

                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" name="comissao_valor" id="comissao_valor" placeholder="Valor da Comissão" readonly>
                                </div>
                                
                                <div style="margin-top: 10px;">
                                    <input type="text" class="span6" name="comissao_descricao" placeholder="Descrição da Comissão">
                                </div>
                            </div>
                        </div>

                        <!-- Campo para mostrar o total -->
                        <div class="control-group">
                            <label class="control-label">Total</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    <span class="add-on">R$</span>
                                    <input type="text" id="total" name="total" class="money" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>" readonly>
                                    <input type="hidden" id="saldo_original" name="saldo_original" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Campos ocultos para armazenar as transações -->
                        <input type="hidden" name="tem_bonus" id="tem_bonus" value="0">
                        <input type="hidden" name="tem_comissao" id="tem_comissao" value="0">

                        <div class="form-actions">
                            <div class="span12">
                                <div class="span6 offset3" style="display:flex;justify-content: center">
                                    <button type="submit" class="button btn btn-primary">
                                        <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Atualizar</span>
                                    </button>
                                    <a href="<?php echo base_url('index.php/admincarteira'); ?>" class="button btn btn-mini btn-warning">
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
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    // Função para converter valor do formato brasileiro para número
    function parseMoneyBR(value) {
        if (!value) return 0;
        return parseFloat(value.replace('.', '').replace(',', '.'));
    }

    // Função para formatar número para dinheiro BR
    function formatMoneyBR(value) {
        return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Função para calcular o total
    function calcularTotal() {
        // Pega os valores dos campos
        let saldoOriginal = parseMoneyBR($('#saldo_original').val());
        let bonus = parseMoneyBR($('#bonus_valor').val());
        
        // Marca quais transações serão registradas
        $('#tem_bonus').val(bonus > 0 ? '1' : '0');
        
        // Calcula o total (saldo original + bonus)
        let total = saldoOriginal + bonus;
        
        // Atualiza o campo total
        $('#total').val(formatMoneyBR(total));
    }

    $(document).ready(function(){
        // Máscara para campos de dinheiro
        $('.money').maskMoney({
            prefix: '',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });

        // Eventos para recalcular os valores
        $('#salario_base').on('keyup', calcularTotal);
        $('#bonus_valor').on('keyup', calcularTotal);

        // Função para buscar o valor base da comissão
        function buscarValorBase() {
            let tipoValorBase = $('#tipo_valor_base').val();
            let usuarioId = $('#usuario').val();
            
            if (tipoValorBase && usuarioId) {
                $.ajax({
                    url: '<?php echo base_url('index.php/admincarteira/getValorBase'); ?>',
                    type: 'POST',
                    data: {
                        tipo: tipoValorBase,
                        usuario_id: usuarioId,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let valor = parseFloat(response.valor) || 0;
                            // Formata o valor para exibição
                            $('#comissao_base').val(formatMoneyBR(valor));
                            
                            // Calcula e atualiza a comissão pendente
                            let comissaoFixa = parseFloat($('#comissao_fixa').val()) || 0;
                            let comissaoPendente = valor * (comissaoFixa / 100);
                            $('#comissao-pendente').text(formatMoneyBR(comissaoPendente));
                            
                            // Usa a comissão fixa como porcentagem
                            if (comissaoFixa > 0) {
                                $('#comissao_porcentagem').val(comissaoFixa);
                                calcularComissao();
                            }
                        } else {
                            console.log('Erro ao buscar valor base:', response.message);
                        }
                    },
                    error: function() {
                        console.log('Erro ao buscar valor base');
                    }
                });
            }
        }

        // Calcula comissão quando valor base ou porcentagem mudar
        function calcularComissao() {
            let valorBase = parseMoneyBR($('#comissao_base').val());
            let porcentagem = parseFloat($('#comissao_porcentagem').val() || 0);
            let tipoValorBase = $('#tipo_valor_base').val();
            let usuarioId = $('#usuario').val();
            
            if (tipoValorBase && usuarioId) {
                $.ajax({
                    url: '<?php echo base_url('index.php/admincarteira/getValorBase'); ?>',
                    type: 'POST',
                    data: {
                        tipo: tipoValorBase,
                        usuario_id: usuarioId,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let valor = parseFloat(response.valor) || 0;
                            // Formata o valor para exibição
                            $('#comissao_base').val(formatMoneyBR(valor));
                            
                            if (!isNaN(valor) && !isNaN(porcentagem)) {
                                let valorComissao = (valor * (porcentagem / 100));
                                $('#comissao_valor').val(formatMoneyBR(valorComissao));
                                $('#tem_comissao').val(valorComissao > 0 ? '1' : '0');
                                calcularTotal();
                            }
                        }
                    }
                });
            }
        }

        // Eventos para recalcular os valores
        $('#tipo_valor_base').on('change', function() {
            calcularComissao();
            localStorage.setItem('tipo_valor_base', $(this).val());
        });

        $('#comissao_porcentagem').on('change keyup', function() {
            calcularComissao();
        });

        // Função para atualizar valor base periodicamente
        function atualizarValorBase() {
            let tipoValorBase = $('#tipo_valor_base').val();
            let usuarioId = $('#usuario').val();
            
            if (tipoValorBase && usuarioId) {
                $.ajax({
                    url: '<?php echo base_url('index.php/admincarteira/getValorBase'); ?>',
                    type: 'POST',
                    data: {
                        tipo: tipoValorBase,
                        usuario_id: usuarioId,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let valorAtual = parseMoneyBR($('#comissao_base').val());
                            let novoValor = parseFloat(response.valor) || 0;
                            
                            // Só atualiza se o valor mudou
                            if (valorAtual !== novoValor) {
                                $('#comissao_base').val(formatMoneyBR(novoValor));
                                calcularComissao();
                            }
                        }
                    }
                });
            }
        }

        // Verifica por atualizações a cada 30 segundos
        setInterval(atualizarValorBase, 30000);

        // Carrega a seleção anterior do tipo de valor base
        let tipoSalvo = localStorage.getItem('tipo_valor_base');
        if (tipoSalvo) {
            $('#tipo_valor_base').val(tipoSalvo);
        }

        // Carrega o valor base inicial se houver usuário selecionado
        if ($('#usuario').val()) {
            buscarValorBase();
        }

        // Validação do formulário
        $('#formCarteira').validate({
            rules: {
                usuario: {required: true},
                salario_base: {required: true},
                data_salario: {
                    required: true,
                    min: 1,
                    max: 31
                }
            },
            messages: {
                usuario: {required: 'Campo obrigatório'},
                salario_base: {required: 'Campo obrigatório'},
                data_salario: {
                    required: 'Campo obrigatório',
                    min: 'Dia inválido',
                    max: 'Dia inválido'
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
                // Verifica se há valores negativos
                let total = parseMoneyBR($('#total').val());
                if (total < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'O valor total não pode ser negativo!'
                    });
                    return false;
                }
                form.submit();
            }
        });

        // Calcula o total inicial
        calcularTotal();
    });
</script>


