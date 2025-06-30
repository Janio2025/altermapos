<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style>
    select {
        width: 70px;
    }
    /* Configuração única para todos os botões da coluna Ações */
    .btn-nwe, .btn-nwe2, .btn-nwe3, .btn-nwe4 {
        padding: 4px 6px !important;
        font-size: 0.85em !important;
        margin: 0 2px !important;
        border-radius: 4px !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
        width: 30px !important;
        height: 25px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        color: #495057 !important;
        text-decoration: none !important;
        text-align: center !important;
    }
    
    .btn-nwe:hover, .btn-nwe2:hover, .btn-nwe3:hover, .btn-nwe4:hover {
        background-color: #e9ecef !important;
        border-color: #adb5bd !important;
        color: #212529 !important;
    }
    
    /* Cores específicas para botões especiais */
    .btn-nwe2.btn-pagar {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: white !important;
    }
    
    .btn-nwe2.btn-pagar:hover {
        background-color: #0056b3 !important;
        border-color: #0056b3 !important;
        color: white !important;
    }
    
    .btn-nwe2.btn-adicionar-salario {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }
    
    .btn-nwe2.btn-adicionar-salario:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
        color: white !important;
    }
    @media (max-width: 768px) {
        table#tabela thead {
            display: none;
        }
        table#tabela tbody tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
        }
        table#tabela tbody tr td {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            border: none;
        }
        table#tabela tbody tr td::before {
            content: attr(data-label);
            font-weight: bold;
            color: #666;
        }
        table#tabela tbody tr td:last-child {
            display: flex;
            justify-content: flex-start;
            gap: 5px;
        }
    }
    .swal2-actions {
        gap: 1rem !important;
    }
    
    /* Estilos para o modal do SweetAlert2 */
    .swal2-popup-custom {
        border-radius: 8px;
        padding: 2rem;
    }

    .swal2-title-custom {
        color: #2D335B;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .swal2-html-container-custom {
        font-size: 1rem;
        color: #666;
        line-height: 1.5;
    }

    .swal2-html-container-custom ul {
        margin: 1rem 0;
    }

    .swal2-html-container-custom li {
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .swal2-html-container-custom i {
        font-size: 1.2rem;
    }

    .swal2-confirm-button-custom {
        padding: 0.5rem 1.5rem !important;
        font-size: 1rem !important;
        border-radius: 4px !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }

    .swal2-cancel-button-custom {
        padding: 0.5rem 1.5rem !important;
        font-size: 1rem !important;
        border-radius: 4px !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }
</style>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bxs-wallet"></i>
        </span>
        <h5>Carteiras</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <div class="span4" style="display: flex; gap: 10px;">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCarteiraAdmin')) { ?>
                <a href="<?= base_url() ?>index.php/admincarteira/adicionar" class="button btn btn-mini btn-success" style="max-width: 165px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Criar Carteira</span>
                </a>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) { ?>
                <button onclick="pagarTodasComissoes()" class="button btn btn-mini btn-warning" style="max-width: 200px">
                    <span class="button__icon"><i class='bx bx-money'></i></span><span class="button__text2">Pagar Comissões</span>
                </button>
            <?php } ?>
        </div>
    </div>

    <div class="widget-box">
        <h5 style="padding: 11px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Cod.</th>
                        <th>Usuário</th>
                        <th>Salário Base</th>
                        <th>Saldo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!isset($carteiras)) {
                        echo '<tr><td colspan="7">Nenhuma Carteira Cadastrada</td></tr>';
                    } else {
                        foreach ($carteiras as $r) {
                            echo '<tr>';
                            echo '<td data-label="Cod.">' . $r->idCarteiraUsuario . '</td>';
                            echo '<td data-label="Usuário">' . $r->nome_usuario . '</td>';
                            echo '<td data-label="Salário Base">R$ ' . number_format($r->salario_base, 2, ',', '.') . '</td>';
                            echo '<td data-label="Saldo">R$ ' . number_format($r->saldo, 2, ',', '.') . '</td>';
                            
                            echo '<td data-label="Ações">';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')) {
                                echo '<a href="' . base_url() . 'index.php/admincarteira/visualizar/' . $r->idCarteiraUsuario . '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show bx-xs"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteiraAdmin')) {
                                echo '<a href="' . base_url() . 'index.php/admincarteira/editar/' . $r->idCarteiraUsuario . '" class="btn-nwe3" title="Editar Carteira"><i class="bx bx-edit bx-xs"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) {
                                echo '<a href="' . base_url() . 'index.php/admincarteira/pagarusuario/' . $r->idCarteiraUsuario . '" class="btn-nwe2 btn-pagar" title="Pagar Usuário"><i class="bx bx-money bx-xs"></i></a>';
                                echo '<a href="javascript:void(0)" onclick="verificarPagamentoAutomatico(' . $r->idCarteiraUsuario . ')" class="btn-nwe2 btn-adicionar-salario" title="Adicionar Salário"><i class="bx bx-plus-circle bx-xs"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dCarteiraAdmin')) {
                                echo '<a href="#modal-excluir" role="button" data-toggle="modal" carteira="' . $r->idCarteiraUsuario . '" class="btn-nwe4" title="Excluir Carteira"><i class="bx bx-trash-alt bx-xs"></i></a>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo $this->pagination->create_links(); ?>

    <!-- Modal Excluir -->
    <div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?php echo base_url() ?>index.php/admincarteira/excluir" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel">Excluir Carteira</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idCarteira" name="id" value="" />
                <h5 style="text-align: center">Deseja realmente excluir esta carteira?</h5>
            </div>
            <div class="modal-footer" style="display:flex;justify-content: center">
                <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                    <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span>
                </button>
                <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var carteira = $(this).attr('carteira');
            $('#idCarteira').val(carteira);
        });

        // Verificar pagamentos automáticos
        $('#verificarPagamentos').click(function() {
            if (confirm('Deseja verificar e processar os pagamentos automáticos pendentes?')) {
                $.ajax({
                    url: '<?php echo base_url() ?>index.php/admincarteira/verificarPagamentosAutomaticos',
                    type: 'POST',
                    data: {
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
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
                        alert('Erro ao verificar pagamentos automáticos.');
                    }
                });
            }
        });
    });

    function pagarTodasComissoes() {
        console.log('Função pagarTodasComissoes chamada');
        
        Swal.fire({
            title: 'Confirmação',
            text: "Deseja realmente pagar todas as comissões pendentes?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, pagar!',
            cancelButtonText: 'Não'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('Usuário confirmou o pagamento');
                
                let csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
                console.log('CSRF Token:', csrf_token);
                
                $.ajax({
                    url: '<?php echo base_url('index.php/admincarteira/pagarTodasComissoes'); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': csrf_token
                    },
                    beforeSend: function() {
                        console.log('Iniciando requisição AJAX');
                        Swal.fire({
                            title: 'Processando',
                            text: 'Aguarde enquanto processamos o pagamento das comissões...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        console.log('Resposta recebida:', response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: response.message || 'Comissões pagas com sucesso!'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message || 'Ocorreu um erro ao processar os pagamentos.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro na requisição:', {
                            xhr: xhr,
                            status: status,
                            error: error
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Ocorreu um erro ao processar os pagamentos. Por favor, tente novamente.'
                        });
                    }
                });
            }
        });
    }

    function verificarPagamentoAutomatico(carteiraId) {
        Swal.fire({
            title: 'Verificar Pagamentos Automáticos',
            html: `
                <div style="text-align: left;">
                    <p><i class="bx bx-info-circle" style="color: #17a2b8;"></i> Esta ação irá:</p>
                    <ul style="list-style: none; padding-left: 20px;">
                        <li><i class="bx bx-check" style="color: #28a745;"></i> Verificar pagamentos pendentes</li>
                        <li><i class="bx bx-check" style="color: #28a745;"></i> Processar pagamentos automáticos</li>
                        <li><i class="bx bx-check" style="color: #28a745;"></i> Atualizar datas de próximos pagamentos</li>
                    </ul>
                    <p style="margin-top: 15px; color: #dc3545;"><i class="bx bx-error-circle"></i> Deseja continuar?</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: '<i class="bx bx-check"></i> Sim, verificar',
            cancelButtonText: '<i class="bx bx-x"></i> Cancelar',
            customClass: {
                popup: 'swal2-popup-custom',
                title: 'swal2-title-custom',
                htmlContainer: 'swal2-html-container-custom',
                confirmButton: 'swal2-confirm-button-custom',
                cancelButton: 'swal2-cancel-button-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo base_url() ?>index.php/admincarteira/verificarPagamentosAutomaticos',
                    type: 'POST',
                    data: {
                        carteira_id: carteiraId,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message,
                                confirmButtonColor: '#28a745',
                                confirmButtonText: '<i class="bx bx-check"></i> OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: response.message,
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: '<i class="bx bx-x"></i> OK'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro ao verificar pagamentos automáticos.',
                            confirmButtonColor: '#dc3545',
                            confirmButtonText: '<i class="bx bx-x"></i> OK'
                        });
                    }
                });
            }
        });
    }
</script>
