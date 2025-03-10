<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style>
    select {
        width: 70px;
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

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bxs-wallet"></i>
        </span>
        <h5>Carteiras</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCarteiraAdmin')) { ?>
            <div class="span3">
                <a href="<?= base_url() ?>index.php/admincarteira/adicionar" class="button btn btn-mini btn-success" style="max-width: 165px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Criar Carteira</span>
                </a>
            </div>
        <?php } ?>
    </div>

    <div class="widget-box">
        <h5 style="padding: 11px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Cod.</th>
                        <th>Nome</th>
                        <th>Usuário</th>
                        <th>Salário</th>
                        <th>Total Bônus</th>
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
                            echo '<td data-label="Nome">' . $r->usuario . '</td>';
                            echo '<td data-label="Usuário">' . $r->usuario . '</td>';
                            echo '<td data-label="Salário">R$ ' . number_format($r->saldo, 2, ',', '.') . '</td>';
                            echo '<td data-label="Total Bônus">R$ ' . number_format($r->total_bonus, 2, ',', '.') . '</td>';
                            echo '<td data-label="Total Comissões">R$ ' . number_format($r->total_comissoes, 2, ',', '.') . '</td>';
                            
                            echo '<td data-label="Ações">';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCarteiraAdmin')) {
                                echo '<a href="' . base_url() . 'index.php/admincarteira/visualizar/' . $r->idCarteiraUsuario . '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show bx-xs"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteiraAdmin')) {
                                echo '<a href="' . base_url() . 'index.php/admincarteira/editar/' . $r->idCarteiraUsuario . '" class="btn-nwe3" title="Editar Carteira"><i class="bx bx-edit bx-xs"></i></a>';
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
    });
</script>
