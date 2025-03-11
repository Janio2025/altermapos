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
    .modal-saque .modal-content {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .modal-saque .modal-header {
        
        background:rgb(66, 69, 197);
        border-bottom: 1px solid #e9ecef;
        border-radius: 8px 8px 0 0;
        padding: 15px 20px;
    }
    .modal-saque .modal-title {
        color:rgb(253, 253, 253);
        font-size: 1.25rem;
        font-weight: 600;
    }
    .modal-saque .modal-body {
        padding: 30px 20px;
    }
    .modal-saque .valor-saque {
        font-size: 2.5rem;
        color: #28a745;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .modal-saque .info-pix {
        color: #6c757d;
        font-size: 1rem;
        margin-bottom: 0;
    }
    .modal-saque .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 15px 20px;
    }
    .modal-saque .btn-confirmar {
        min-width: 150px;
        padding: 8px 20px;
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
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon">
                <i class="bx bx-wallet"></i>
            </span>
            <h5>Saldo Disponível</h5>
        </div>
        <div class="widget-content">
            <div class="row-fluid" style="min-height: 100px; padding: 10px;">
                <div class="span12">
                    <?php if(isset($carteira)): ?>
                    <div class="saldo-value" style="font-size: 36px; text-align: center; color: #28a745;">
                        R$ <?php echo number_format($carteira->saldo, 2, ',', '.'); ?>
                    </div>
                    <?php if($carteira->saldo > 0): ?>
                    <div style="text-align: center; margin-top: 15px;">
                        <button type="button" onclick="abrirModalSaque()" class="button btn btn-success">
                            <span class="button__icon"><i class='bx bx-money'></i></span>
                            <span class="button__text2">Realizar Saque via PIX</span>
                        </button>
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="saldo-value" style="font-size: 36px; text-align: center; color: #28a745;">
                        R$ <?php echo number_format($saldo, 2, ',', '.'); ?>
                    </div>
                    <?php if($saldo > 0): ?>
                    <div style="text-align: center; margin-top: 15px;">
                        <button type="button" onclick="abrirModalSaque()" class="button btn btn-success">
                            <span class="button__icon"><i class='bx bx-money'></i></span>
                            <span class="button__text2">Realizar Saque via PIX</span>
                        </button>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Comissão Pendente -->
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon">
                <i class="bx bx-money"></i>
            </span>
            <h5>Comissão Pendente</h5>
        </div>
        <div class="widget-content">
            <div class="row-fluid" style="min-height: 100px; padding: 10px;">
                <div class="span12">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                        <div class="comissao-value" style="font-size: 28px; color: #ffc107;">
                            R$ <span id="comissao-pendente">0,00</span>
                        </div>
                        
                    </div>
                </div>
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
            <style>
                .table-scroll-container {
                    max-height: 750px;
                    overflow-y: auto;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
                .table-scroll-container table {
                    margin-bottom: 0;
                }
                .table-header-fixed {
                    position: sticky;
                    top: 0;
                    background-color: #F9F9F9;
                    z-index: 1;
                }
                .table-transactions {
                    margin-bottom: 0;
                }
                .table-transactions th {
                    background-color: #f5f5f5;
                    border-bottom: 2px solid #ddd;
                    font-weight: 600;
                    text-align: left;
                    padding: 12px 8px;
                }
                .table-transactions td {
                    padding: 10px 8px;
                    vertical-align: middle;
                }
                /* Larguras e alinhamentos das colunas */
                .table-transactions th:nth-child(1),
                .table-transactions td:nth-child(1) {
                    width: 110px;
                    text-align: center;
                }
                .table-transactions th:nth-child(2),
                .table-transactions td:nth-child(2) {
                    width: 130px;
                    text-align: center;
                }
                .table-transactions th:nth-child(3),
                .table-transactions td:nth-child(3) {
                    width: 130px;
                    text-align: right;
                }
                .table-transactions th:nth-child(4),
                .table-transactions td:nth-child(4) {
                    min-width: 200px;
                }
                .table-transactions th:nth-child(5),
                .table-transactions td:nth-child(5) {
                    width: 80px;
                    text-align: center;
                }
                /* Estilo para os labels de tipo */
                .table-transactions .label {
                    display: inline-block;
                    min-width: 85px;
                    text-align: center;
                    font-weight: 600;
                }
                /* Hover na linha */
                .table-transactions tbody tr:hover {
                    background-color: #f9f9f9;
                }
                /* Botão de ação */
                .table-transactions .btn-nwe {
                    padding: 5px 10px;
                    border-radius: 3px;
                }
                .table-transactions .btn-nwe i {
                    font-size: 16px;
                }
            </style>
            <table class="table table-bordered table-transactions">
                <thead>
                    <tr class="table-header-fixed">
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
            </table>
            <div class="table-scroll-container">
                <table class="table table-bordered table-transactions">
                    <tbody>
                        <?php if (!$transacoes) : ?>
                            <tr>
                                <td colspan="5">Nenhuma transação encontrada</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($transacoes as $t) : ?>
                                <tr>
                                    <td class="col-data"><?php echo date('d/m/Y', strtotime($t->data_transacao)); ?></td>
                                    <td class="col-tipo">
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
                                    <td class="col-valor">R$ <?php echo number_format($t->valor, 2, ',', '.'); ?></td>
                                    <td class="col-descricao"><?php echo $t->descricao; ?></td>
                                    <td class="col-acoes">
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteira')) : ?>
                                            <a href="<?php echo site_url('carteira/visualizar/' . $t->idTransacoesUsuario); ?>" class="btn-nwe" title="Ver mais detalhes">
                                                <i class="bx bx-show"></i>
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

    <!-- Modal de Saque -->
    <div class="modal fade modal-saque" id="modalSaque" tabindex="-1" role="dialog" aria-labelledby="modalSaqueLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSaqueLabel">
                        <i class="bx bx-money"></i> Confirmar Saque via PIX
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="valor-saque">
                            R$ <span id="valorSaque">0,00</span>
                        </div>
                        <p class="info-pix">
                            <i class="bx bx bx-money"></i>
                            O valor será enviado para a chave 
                        </p>
                        <p class="info-pix">
                            <i class="bx bx-document"></i>
                           PIX: <?php echo isset($config) ? $config->chave_pix : ''; ?>
                        </p>
                        <p class="info-pix">
                            <i class=""></i>
                            cadastrada na sua carteira
                        </p>

                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-confirmar" data-dismiss="modal">
                        <i class="bx bx-check"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success btn-confirmar" onclick="realizarSaquePix()">
                        <i class="bx bx-check"></i> Confirmar Saque
                    </button>
                </div>
            </div>
        </div>
    </div>

    
</div>

<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
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
            let tipoValorBase = '<?php echo isset($config) ? $config->tipo_valor_base : "servicos"; ?>';
            let usuarioId = '<?php echo $this->session->userdata('id_admin'); ?>';
            
            if (tipoValorBase && usuarioId) {
                $.ajax({
                    url: '<?php echo base_url('index.php/carteira/getValorBase'); ?>',
                    type: 'POST',
                    data: {
                        tipo: tipoValorBase,
                        usuario_id: usuarioId,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let valorBase = parseFloat(response.valor_base);
                            let percentualComissao = <?php echo isset($config) ? $config->comissao_fixa : 0; ?>;
                            let comissaoPendente = (valorBase * percentualComissao) / 100;
                            
                            $('#comissao-pendente').text(formatMoneyBR(comissaoPendente));
                        } else {
                            $('#comissao-pendente').text('0,00');
                        }
                    },
                    error: function() {
                        $('#comissao-pendente').text('0,00');
                    }
                });
            }
        }

        // Função para receber a comissão
        window.receberComissao = function() {
            let tipoValorBase = '<?php echo isset($config) ? $config->tipo_valor_base : "servicos"; ?>';
            let usuarioId = '<?php echo $this->session->userdata('id_admin'); ?>';

            Swal.fire({
                title: 'Confirmação',
                text: "Deseja realmente receber esta comissão?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, receber!',
                cancelButtonText: 'Não'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?php echo base_url('index.php/carteira/receberComissao'); ?>',
                        type: 'POST',
                        data: {
                            tipo: tipoValorBase,
                            usuario_id: usuarioId,
                            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso!',
                                    text: 'Comissão recebida com sucesso!'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message || 'Erro ao receber comissão!'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Erro ao processar a requisição!'
                            });
                        }
                    });
                }
            });
        }

        // Função para abrir o modal de saque
        window.abrirModalSaque = function() {
            let saldoAtual = <?php echo isset($carteira) ? $carteira->saldo : $saldo; ?>;
            $('#valorSaque').text(formatarMoeda(saldoAtual));
            $('#modalSaque').modal('show');
        }

        // Função para realizar saque via PIX
        window.realizarSaquePix = function() {
            $('#modalSaque').modal('hide');
            
            Swal.fire({
                title: 'Processando...',
                text: 'Aguarde enquanto processamos seu saque.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?php echo base_url('index.php/carteira/realizarSaquePix'); ?>',
                type: 'POST',
                data: {
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: response.message
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message || 'Erro ao realizar saque!'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Erro ao processar a requisição!'
                    });
                }
            });
        }

        // Atualiza o valor da comissão pendente a cada 30 segundos
        buscarValorBase(); // Chama imediatamente ao carregar
        setInterval(buscarValorBase, 30000); // Atualiza a cada 30 segundos
    });

    function formatarMoeda(valor) {
        return valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
</script>
