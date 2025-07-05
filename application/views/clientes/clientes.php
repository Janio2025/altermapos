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
            <i class="fas fa-user"></i>
        </span>
        <h5>Clientes</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCliente')) { ?>
            <div class="span3">
                <a href="<?= base_url() ?>index.php/clientes/adicionar" class="button btn btn-mini btn-success" style="max-width: 165px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Cliente / Fornecedor</span>
                </a>
            </div>
        <?php } ?>
        <form class="span9" method="get" action="<?= base_url() ?>index.php/clientes" style="display: flex; justify-content: flex-end;">
            <div class="span3">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Buscar por Nome, Doc, Email ou Telefone..." class="span12" value="<?= $this->input->get('pesquisa') ?>">
            </div>
            <div class="span1">
                <button class="button btn btn-mini btn-warning" style="min-width: 30px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                </button>
            </div>
        </form>
    </div>

    <div class="widget-box">
    <h5 style="padding: 11px 0"></h5>
    <div class="widget-content nopadding tab-content">
        <table id="tabela" class="table table-bordered">
            <thead>
                <tr>
                    <th>Cod.</th>
                    <th>Nome</th>
                    <th>Contato</th>
                    <th>CPF/CNPJ</th>
                    <th>Telefone</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Tipo de Interesse</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$results) {
                    echo '<tr><td colspan="10">Nenhum Cliente Cadastrado</td></tr>';
                }
                foreach ($results as $r) {
                    echo '<tr>';
                    echo '<td data-label="Cod.">' . $r->idClientes . '</td>';
                    echo '<td data-label="Nome"><a href="' . base_url() . 'index.php/clientes/visualizar/' . $r->idClientes . '">' . $r->nomeCliente . '</a></td>';
                    echo '<td data-label="Contato">' . $r->contato . '</td>';
                    echo '<td data-label="CPF/CNPJ">' . $r->documento . '</td>';
                    echo '<td data-label="Telefone">' . $r->telefone . '</td>';
                    echo '<td data-label="Celular">' . $r->celular . '</td>';
                    echo '<td data-label="Email">' . $r->email . '</td>';
                    echo '<td data-label="Tipo">' . 
                        ($r->fornecedor == 1 ? '<span class="label label-primary">Fornecedor</span>' : 
                        ($r->fornecedor == 2 ? '<span class="label label-warning">Colaborador</span>' : 
                        '<span class="label label-success">Cliente</span>')) . 
                    '</td>';
                    
                    // Buscar o tipo de interesse
                    $tipo_interesse = '';
                    if ($r->tipo_id) {
                        $tipo = $this->db->where('id', $r->tipo_id)->get('tipos')->row();
                        if ($tipo) {
                            $tipo_interesse = $tipo->nome;
                        }
                    }
                    echo '<td data-label="Tipo de Interesse">' . $tipo_interesse . '</td>';

                    echo '<td data-label="Ações">';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) {
                        echo '<a href="' . base_url() . 'index.php/clientes/visualizar/' . $r->idClientes . '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show bx-xs"></i></a>';
                        echo '<a href="' . base_url() . 'index.php/mine?e=' . $r->email . '" target="new" class="btn-nwe2" title="Área do cliente"><i class="bx bx-key bx-xs"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) {
                        echo '<a href="' . base_url() . 'index.php/clientes/editar/' . $r->idClientes . '" class="btn-nwe3" title="Editar Cliente"><i class="bx bx-edit bx-xs"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dCliente')) {
                        echo '<a href="#modal-excluir" role="button" data-toggle="modal" cliente="' . $r->idClientes . '" class="btn-nwe4" title="Excluir Cliente"><i class="bx bx-trash-alt bx-xs"></i></a>';
                    }
                    echo '</td>';
                    echo '</tr>';
                } ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/clientes/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Cliente</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idCliente" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este cliente e os dados associados a ele (OS, Vendas, Receitas)?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', 'a', function (event) {
            var cliente = $(this).attr('cliente');
            $('#idCliente').val(cliente);
        });
    });
</script>
