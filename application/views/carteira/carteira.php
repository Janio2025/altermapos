<style>
    select {
        width: 70px;
    }
    .widget-title h5 {
        color: #666;
    }
    .widget-box {
        margin-bottom: 20px;
    }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-wallet"></i>
        </span>
        <h5>Carteira</h5>
    </div>
    
    <!-- Saldo da Carteira -->


    <!-- Total -->
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon">
                <i class="bx bx-money"></i>
            </span>
            <h5>Total</h5>
        </div>
        <div class="widget-content">
            <div class="row-fluid" style="min-height: 100px; padding: 10px;">
                <div class="span12">
                    <div class="total-value" style="font-size: 30px; text-align: center; color: #666;">
                        R$ <?php echo number_format($saldo, 2, ',', '.'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações -->
    <div class="widget-box" style="margin-top: 20px;">
        <div class="widget-content nopadding" style="padding: 20px !important">
            <div style="display: flex; gap: 10px;">
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCarteira')) { ?>
                    <a href="<?= site_url('carteira/adicionar') ?>" class="button btn btn-mini btn-success">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                        <span class="button__text2">Nova Transação</span>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Lista de Transações -->
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon">
                <i class="bx bx-time"></i>
            </span>
            <h5>Histórico de Transações</h5>
        </div>
        <div class="widget-content nopadding">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$transacoes) : ?>
                        <tr>
                            <td colspan="5">Nenhuma transação encontrada</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($transacoes as $t) : ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($t->data_transacao)); ?></td>
                                <td>
                                    <?php
                                    switch ($t->tipo) {
                                        case 'salario':
                                            echo '<span class="label label-success">Salário</span>';
                                            break;
                                        case 'bonus':
                                            echo '<span class="label label-info">Bônus</span>';
                                            break;
                                        case 'comissao':
                                            echo '<span class="label label-warning">Comissão</span>';
                                            break;
                                        case 'retirada':
                                            echo '<span class="label label-important">Retirada</span>';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td>R$ <?php echo number_format($t->valor, 2, ',', '.'); ?></td>
                                <td><?php echo $t->descricao; ?></td>
                                <td>
                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteira')) : ?>
                                        <a href="<?= site_url('carteira/visualizar/' . $t->idTransacoesUsuario) ?>" class="btn-nwe" title="Ver mais detalhes">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    <?php endif ?>
                                    
                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteira')) : ?>
                                        <a href="<?= site_url('carteira/editar/' . $t->idTransacoesUsuario) ?>" class="btn-nwe3" title="Editar">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                    <?php endif ?>
                                    
                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dCarteira')) : ?>
                                        <a href="#modal-excluir" role="button" data-toggle="modal" transacao="<?= $t->idTransacoesUsuario ?>" class="btn-nwe4" title="Excluir">
                                            <i class="bx bx-trash-alt"></i>
                                        </a>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?= site_url('carteira/excluir') ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Transação</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idTransacao" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir esta transação?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Código existente do modal
        $(document).on('click', 'a', function(event) {
            var transacao = $(this).attr('transacao');
            $('#idTransacao').val(transacao);
        });

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
            let usuarioId = '<?php echo $this->session->userdata('id'); ?>';
            
            if (tipoValorBase && usuarioId) {
                $.ajax({
                    url: '<?php echo base_url('index.php/carteira/getValorBase'); ?>',
                    type: 'POST',
                    data: {
                        tipo: tipoValorBase,
                        usuario_id: usuarioId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let valor = parseFloat(response.valor) || 0;
                            let salarioBase = <?php echo isset($config) ? $config->salario_base : 0; ?>;
                            let comissaoFixa = <?php echo isset($config) ? $config->comissao_fixa : 0; ?>;
                            
                            // Calcula a comissão
                            let valorComissao = (valor * (comissaoFixa / 100));
                            
                            // Calcula o total
                            let total = salarioBase + valorComissao;
                            
                            // Atualiza o total na tela
                            $('.total-value').text('R$ ' + formatMoneyBR(total));
                        }
                    }
                });
            }
        }

        // Busca o valor base inicial
        buscarValorBase();

        // Atualiza o valor base a cada 30 segundos
        setInterval(buscarValorBase, 30000);
    });
</script>
