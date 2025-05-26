<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="new122" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <div class="widget-title" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <span class="icon" style="margin-right: 10px;">
            <i class="bx bxs-wallet" style="font-size: 24px; color: #2c3e50;"></i>
        </span>
        <h5 style="display: inline-block; margin: 0; color: #2c3e50;">Visualizar Carteira</h5>
    </div>

    <div class="widget-box" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="widget-content" style="padding: 20px;">
            <!-- Informações Básicas -->
            <div style="margin-bottom: 25px;">
                <h6 style="color: #7f8c8d; font-size: 0.9em; margin-bottom: 8px;">USUÁRIO</h6>
                <p style="color: #2c3e50; font-size: 1.1em; margin: 0;"><?php echo $carteira->nome; ?></p>
            </div>

            <!-- Configurações de Pagamento -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                <h6 style="color: #34495e; font-size: 1em; margin-bottom: 15px; font-weight: 600;">Configurações de Pagamento</h6>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div>
                        <span style="color: #7f8c8d; font-size: 0.9em; display: block; margin-bottom: 5px;">Salário Base</span>
                        <span style="color: #2c3e50; font-size: 1.1em; font-weight: 500;">R$ <?php echo isset($config) ? number_format($config->salario_base, 2, ',', '.') : '0,00'; ?></span>
                    </div>
                    
                    <div>
                        <span style="color: #7f8c8d; font-size: 0.9em; display: block; margin-bottom: 5px;">Comissão Fixa</span>
                        <span style="color: #2c3e50; font-size: 1.1em; font-weight: 500;"><?php echo isset($config) ? $config->comissao_fixa : '0'; ?>%</span>
                    </div>
                    
                    <div>
                        <span style="color: #7f8c8d; font-size: 0.9em; display: block; margin-bottom: 5px;">Data do Salário</span>
                        <span style="color: #2c3e50; font-size: 1.1em; font-weight: 500;">Dia <?php echo isset($config) ? $config->data_salario : '-'; ?></span>
                    </div>
                    
                    <div>
                        <span style="color: #7f8c8d; font-size: 0.9em; display: block; margin-bottom: 5px;">Tipo de Pagamento</span>
                        <span style="color: #2c3e50; font-size: 1.1em; font-weight: 500;">
                            <?php echo (!isset($config) || $config->tipo_repeticao == 'mensal') ? 'Mensal' : 'Quinzenal'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Seção de Comissão -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                <h6 style="color: #34495e; font-size: 1em; margin-bottom: 15px; font-weight: 600;">Comissão Adicional</h6>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div>
                        <span style="color: #7f8c8d; font-size: 0.9em; display: block; margin-bottom: 5px;">Base de Cálculo</span>
                        <span style="color: #2c3e50; font-size: 1.1em; font-weight: 500;">
                            <?php echo (isset($config) && $config->tipo_valor_base == 'servicos') ? 'Serviços' : 'Produtos e Serviços'; ?>
                        </span>
                    </div>

                    <div>
                        <span style="color: #7f8c8d; font-size: 0.9em; display: block; margin-bottom: 5px;">Valor Base</span>
                        <span style="color: #2c3e50; font-size: 1.1em; font-weight: 500;" id="comissao_base_text">R$ 0,00</span>
                    </div>
                    
                    <div>
                        <span style="color: #7f8c8d; font-size: 0.9em; display: block; margin-bottom: 5px;">Porcentagem</span>
                        <span style="color: #2c3e50; font-size: 1.1em; font-weight: 500;" id="comissao_porcentagem_text">0%</span>
                    </div>

                    <div>
                        <span style="color: #7f8c8d; font-size: 0.9em; display: block; margin-bottom: 5px;">Valor da Comissão</span>
                        <span style="color: #2c3e50; font-size: 1.1em; font-weight: 500;" id="comissao_valor_text">R$ 0,00</span>
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div style="background: #2c3e50; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #fff; font-size: 1.1em;">Saldo em Carteira</span>
                    <span style="color: #fff; font-size: 1.4em; font-weight: 600;" id="total_text">
                        R$ <?php echo number_format($carteira->saldo, 2, ',', '.'); ?>
                    </span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="<?php echo base_url('index.php/admincarteira'); ?>" class="button btn btn-warning" style="padding: 10px 20px; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; background: #f39c12; border: none; color: white; transition: all 0.3s ease;">
                    <i class="bx bx-undo"></i>
                    <span>Voltar</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        function formatMoneyBR(value) {
            return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function parseMoneyBR(value) {
            if (!value) return 0;
            return parseFloat(value.replace('.', '').replace(',', '.'));
        }

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
                            $('#comissao_base_text').text('R$ ' + formatMoneyBR(valor));
                            
                            let comissaoFixa = <?php echo isset($config) ? $config->comissao_fixa : 0; ?>;
                            if (comissaoFixa > 0) {
                                $('#comissao_porcentagem_text').text(comissaoFixa + '%');
                                calcularComissao(valor, comissaoFixa);
                            }
                        }
                    }
                });
            }
        }

        function calcularComissao(valorBase, porcentagem) {
            if (!isNaN(valorBase) && !isNaN(porcentagem)) {
                let valorComissao = (valorBase * (porcentagem / 100));
                $('#comissao_valor_text').text('R$ ' + formatMoneyBR(valorComissao));
            }
        }

        buscarValorBase();
        setInterval(buscarValorBase, 30000);

        // Verificar pagamento automático
        $('#verificarPagamento').click(function() {
            if (confirm('Deseja verificar e processar o pagamento automático para esta carteira?')) {
                $.ajax({
                    url: '<?php echo base_url() ?>index.php/admincarteira/verificarPagamentosAutomaticos',
                    type: 'POST',
                    data: {
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>',
                        carteira_id: '<?php echo $carteira->idCarteiraUsuario; ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Erro ao verificar pagamento automático.');
                    }
                });
            }
        });
    });
</script> 