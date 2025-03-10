<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bxs-wallet"></i>
        </span>
        <h5>Visualizar Carteira</h5>
    </div>

    <div class="widget-box">
        <div class="widget-content nopadding tab-content">
            <div id="tab1" class="tab-pane active" style="min-height: 300px">
                <div class="widget_box_Painel2">
                    <div class="form-horizontal">
                        <!-- Usuário -->
                        <div class="control-group">
                            <label class="control-label">Usuário</label>
                            <div class="controls">
                                <input type="text" class="input-xlarge" value="<?php echo $carteira->nome; ?>" readonly>
                            </div>
                        </div>

                        <!-- Configurações de Pagamento -->
                        <div class="control-group">
                            <label class="control-label">Configurações de Pagamento</label>
                            <div class="controls">
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" value="<?php echo isset($config) ? number_format($config->salario_base, 2, ',', '.') : ''; ?>" readonly>
                                </div>
                                
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">%</span>
                                    <input type="number" value="<?php echo isset($config) ? $config->comissao_fixa : ''; ?>" readonly>
                                </div>
                                
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on"><i class="bx bx-calendar"></i></span>
                                    <input type="number" value="<?php echo isset($config) ? $config->data_salario : ''; ?>" readonly>
                                </div>
                                
                                <div style="margin-bottom: 10px;">
                                    <label class="radio" style="display: inline-block; margin-right: 15px;">
                                        <input type="radio" <?php echo (!isset($config) || $config->tipo_repeticao == 'mensal') ? 'checked' : ''; ?> disabled> Mensal
                                    </label>
                                    <label class="radio" style="display: inline-block;">
                                        <input type="radio" <?php echo (isset($config) && $config->tipo_repeticao == 'quinzenal') ? 'checked' : ''; ?> disabled> Quinzenal
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Seção de Comissão -->
                        <div class="control-group">
                            <label class="control-label">Comissão Adicional</label>
                            <div class="controls">
                                <div style="margin-bottom: 10px;">
                                    <select class="input-xlarge" disabled>
                                        <option value="servicos" <?php echo (isset($config) && $config->tipo_valor_base == 'servicos') ? 'selected' : ''; ?>>Serviços</option>
                                        <option value="total" <?php echo (isset($config) && $config->tipo_valor_base == 'total') ? 'selected' : ''; ?>>Produtos e Serviços</option>
                                    </select>
                                </div>

                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" id="comissao_base" readonly>
                                </div>
                                
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <input type="number" id="comissao_porcentagem" readonly>
                                    <span class="add-on">%</span>
                                </div>

                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" id="comissao_valor" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Campo para mostrar o total -->
                        <div class="control-group">
                            <label class="control-label">Total</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    <span class="add-on">R$</span>
                                    <input type="text" id="total" class="money" value="<?php echo number_format($total, 2, ',', '.'); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions" style="background-color: transparent;">
                            <div class="span12">
                                <div class="span6 offset3" style="display:flex;justify-content: center">
                                    <a href="<?php echo base_url('index.php/admincarteira'); ?>" class="button btn btn-mini btn-warning">
                                        <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        // Máscara para campos de dinheiro
        $('.money').maskMoney({
            prefix: '',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });

        // Função para converter valor do formato brasileiro para número
        function parseMoneyBR(value) {
            if (!value) return 0;
            return parseFloat(value.replace('.', '').replace(',', '.'));
        }

        // Função para formatar número para dinheiro BR
        function formatMoneyBR(value) {
            return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Função para buscar o valor base da comissão
        function buscarValorBase() {
            let tipoValorBase = '<?php echo isset($config) ? $config->tipo_valor_base : ""; ?>';
            let usuarioId = '<?php echo $carteira->usuarios_id; ?>';
            
            if (tipoValorBase && usuarioId) {
                $.ajax({
                    url: '<?php echo base_url('index.php/admincarteira/getValorBase'); ?>',
                    type: 'POST',
                    data: {
                        tipo: tipoValorBase,
                        usuario_id: usuarioId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let valor = parseFloat(response.valor) || 0;
                            $('#comissao_base').val(formatMoneyBR(valor));
                            
                            let comissaoFixa = <?php echo isset($config) ? $config->comissao_fixa : 0; ?>;
                            if (comissaoFixa > 0) {
                                $('#comissao_porcentagem').val(comissaoFixa);
                                calcularComissao();
                            }
                        }
                    }
                });
            }
        }

        // Calcula comissão quando valor base ou porcentagem mudar
        function calcularComissao() {
            let valorBase = parseMoneyBR($('#comissao_base').val());
            let porcentagem = parseFloat($('#comissao_porcentagem').val() || 0);
            
            if (!isNaN(valorBase) && !isNaN(porcentagem)) {
                let valorComissao = (valorBase * (porcentagem / 100));
                $('#comissao_valor').val(formatMoneyBR(valorComissao));
                calcularTotal();
            }
        }

        // Função para calcular o total
        function calcularTotal() {
            let salarioBase = <?php echo isset($config) ? $config->salario_base : 0; ?>;
            let comissao = parseMoneyBR($('#comissao_valor').val());
            let total = salarioBase + comissao;
            $('#total').val(formatMoneyBR(total));
        }

        // Busca o valor base inicial
        buscarValorBase();

        // Atualiza o valor base a cada 30 segundos
        setInterval(buscarValorBase, 30000);
    });
</script> 