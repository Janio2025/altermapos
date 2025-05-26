<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style>
    select {
        width: 70px;
    }
    .btn-nwe2.btn-pagar {
        padding: 7px 12px;
        font-size: 1.1em;
        margin: 0 5px;
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
                        <th>Outros Valores</th>
                        <th>Total Comissões</th>
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
                            echo '<td data-label="Outros Valores">R$ ' . number_format($r->total_bonus, 2, ',', '.') . '</td>';
                            echo '<td data-label="Total Comissões">R$ ' . number_format($r->total_comissoes, 2, ',', '.') . '</td>';
                            
                            echo '<td data-label="Ações">';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')) {
                                echo '<a href="' . base_url() . 'index.php/admincarteira/visualizar/' . $r->idCarteiraUsuario . '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show bx-xs"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteiraAdmin')) {
                                echo '<a href="' . base_url() . 'index.php/admincarteira/editar/' . $r->idCarteiraUsuario . '" class="btn-nwe3" title="Editar Carteira"><i class="bx bx-edit bx-xs"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) {
                                echo '<a href="' . base_url() . 'index.php/admincarteira/pagarusuario/' . $r->idCarteiraUsuario . '" class="btn-nwe2 btn-pagar" title="Pagar Usuário"><i class="bx bx-money bx-xs"></i></a>';
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
</script>
