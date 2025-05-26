<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-history"></i>
        </span>
        <h5>Histórico de Transações - <?php echo $carteira->nome; ?></h5>
    </div>

    <div class="widget-box">
        <div class="widget-title">
            <h5><i class="fas fa-history"></i> Histórico de Transações</h5>
            <div class="buttons">
                <?php if (isset($config) && $config->pagamento_automatico) { ?>
                    <button type="button" id="verificarPagamento" class="btn btn-info"><i class="fas fa-sync"></i> Verificar Pagamento Automático</button>
                <?php } ?>
            </div>
        </div>
        <div class="widget-content">
            <!-- Filtros -->
            <div class="row-fluid" style="margin-bottom: 20px;">
                <form action="" method="GET" class="form-inline">
                    <div class="span12">
                        <div class="span3">
                            <label>Data Início:</label>
                            <input type="date" name="data_inicio" value="<?php echo $filtros['data_inicio']; ?>" class="span12">
                        </div>
                        <div class="span3">
                            <label>Data Fim:</label>
                            <input type="date" name="data_fim" value="<?php echo $filtros['data_fim']; ?>" class="span12">
                        </div>
                        <div class="span3">
                            <label>Tipo:</label>
                            <select name="tipo" class="span12">
                                <option value="">Todos</option>
                                <option value="salario" <?php echo $filtros['tipo'] == 'salario' ? 'selected' : ''; ?>>Salário</option>
                                <option value="bonus" <?php echo $filtros['tipo'] == 'bonus' ? 'selected' : ''; ?>>Bônus</option>
                                <option value="comissao" <?php echo $filtros['tipo'] == 'comissao' ? 'selected' : ''; ?>>Comissão</option>
                                <option value="retirada" <?php echo $filtros['tipo'] == 'retirada' ? 'selected' : ''; ?>>Retirada</option>
                            </select>
                        </div>
                        <div class="span3" style="margin-top: 22px;">
                            <button type="submit" class="button btn btn-primary">
                                <span class="button__icon"><i class='bx bx-filter-alt'></i></span>
                                <span class="button__text2">Filtrar</span>
                            </button>
                            <a href="<?php echo base_url('index.php/admincarteira/exportarHistorico/'.$carteira->idCarteiraUsuario); ?>" class="button btn btn-success">
                                <span class="button__icon"><i class='bx bx-export'></i></span>
                                <span class="button__text2">Exportar</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Resumo -->
            <div class="row-fluid" style="margin-bottom: 20px;">
                <div class="span12">
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon">
                                <i class="bx bx-bar-chart"></i>
                            </span>
                            <h5>Resumo do Período</h5>
                        </div>
                        <div class="widget-content">
                            <div class="row-fluid">
                                <div class="span3">
                                    <div class="widget-box">
                                        <div class="widget-title bg_lg">
                                            <span class="icon">
                                                <i class="bx bx-money"></i>
                                            </span>
                                            <h5>Salários</h5>
                                        </div>
                                        <div class="widget-content">
                                            <h3>R$ <?php echo number_format($totais['salario'], 2, ',', '.'); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="span3">
                                    <div class="widget-box">
                                        <div class="widget-title bg_ly">
                                            <span class="icon">
                                                <i class="bx bx-gift"></i>
                                            </span>
                                            <h5>Bônus</h5>
                                        </div>
                                        <div class="widget-content">
                                            <h3>R$ <?php echo number_format($totais['bonus'], 2, ',', '.'); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="span3">
                                    <div class="widget-box">
                                        <div class="widget-title bg_lb">
                                            <span class="icon">
                                                <i class="bx bx-dollar-circle"></i>
                                            </span>
                                            <h5>Comissões</h5>
                                        </div>
                                        <div class="widget-content">
                                            <h3>R$ <?php echo number_format($totais['comissao'], 2, ',', '.'); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="span3">
                                    <div class="widget-box">
                                        <div class="widget-title bg_lr">
                                            <span class="icon">
                                                <i class="bx bx-minus-circle"></i>
                                            </span>
                                            <h5>Retiradas</h5>
                                        </div>
                                        <div class="widget-content">
                                            <h3>R$ <?php echo number_format($totais['retiradas'], 2, ',', '.'); ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Transações -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($transacoes)): ?>
                            <?php foreach($transacoes as $t): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($t->data_transacao)); ?></td>
                                    <td>
                                        <?php
                                        switch($t->tipo) {
                                            case 'salario':
                                                echo '<span class="label label-success">Salário</span>';
                                                break;
                                            case 'bonus':
                                                echo '<span class="label label-warning">Bônus</span>';
                                                break;
                                            case 'comissao':
                                                echo '<span class="label label-info">Comissão</span>';
                                                break;
                                            case 'retirada':
                                                echo '<span class="label label-important">Retirada</span>';
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td>R$ <?php echo number_format($t->valor, 2, ',', '.'); ?></td>
                                    <td class="col-descricao" title="<?php echo htmlspecialchars($t->descricao); ?>">
                                        <?php 
                                            echo htmlspecialchars($t->descricao); 
                                            if (strlen($t->descricao) > 50) {
                                                echo '&nbsp;<a href="#" class="btn-nwe" onclick="mostrarDescricaoCompleta(\'' . htmlspecialchars(addslashes($t->descricao)) . '\'); return false;" title="Ver descrição completa"><i class="bx bx-show"></i></a>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Nenhuma transação encontrada para o período.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <div class="span12">
                    <div class="span6 offset3" style="display:flex;justify-content: center">
                        <a href="<?php echo base_url('index.php/admincarteira'); ?>" class="button btn btn-mini btn-warning">
                            <span class="button__icon"><i class="bx bx-undo"></i></span>
                            <span class="button__text2">Voltar</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos -->
<style>
    .table td.col-descricao {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<script type="text/javascript">
    $(document).ready(function(){
        // Atualiza a página quando os filtros mudarem
        $('select[name="tipo"]').change(function(){
            $(this).closest('form').submit();
        });

        // Função para mostrar descrição completa
        window.mostrarDescricaoCompleta = function(descricao) {
            Swal.fire({
                title: 'Descrição Completa',
                text: descricao,
                confirmButtonText: 'Fechar'
            });
        };

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