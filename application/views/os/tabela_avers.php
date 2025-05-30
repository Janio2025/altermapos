<?php 
// Debug para verificar os dados recebidos
log_message('debug', 'Dados recebidos na view tabela_avers: ' . print_r($avers, true));
?>

<?php if (isset($avers) && is_array($avers)): ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Valor</th>
            <th>Data Pagamento</th>
            <th>Status</th>
            <th>Data Registro</th>
            <th>Registrado por</th>
            <th width="5%">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($avers)) {
            foreach ($avers as $a) {
                echo '<tr>';
                echo '<td>R$ ' . number_format($a->valor, 2, ',', '.') . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($a->data_pagamento)) . '</td>';
                echo '<td>' . ucfirst($a->status) . '</td>';
                echo '<td>' . date('d/m/Y H:i:s', strtotime($a->data_criacao)) . '</td>';
                echo '<td>' . ($a->nome_usuario ?? 'N/A') . '</td>';
                echo '<td>
                    <div align="center">
                        <a href="#modal-editar-aver" class="btn btn-info btn-editar-aver" 
                           data-id="' . $a->idAver . '"
                           data-valor="' . number_format($a->valor, 2, ',', '.') . '"
                           data-data="' . date('d/m/Y', strtotime($a->data_pagamento)) . '"
                           data-status="' . $a->status . '">
                            <i class="bx bx-edit"></i>
                        </a>
                        <a href="#modal-excluir-aver" class="btn btn-danger btn-excluir-aver" 
                           data-id="' . $a->idAver . '"
                           data-os="' . $a->os_id . '">
                            <i class="bx bx-trash"></i>
                        </a>
                    </div>
                </td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6" class="text-center">Nenhum aver registrado</td></tr>';
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="text-align: right">
                <strong>Total de Avers: R$ <?php 
                    $totalAvers = 0;
                    if (!empty($avers)) {
                        foreach ($avers as $a) {
                            $totalAvers += $a->valor;
                        }
                    }
                    echo number_format($totalAvers, 2, ',', '.');
                ?></strong>
            </td>
        </tr>
    </tfoot>
</table>

<script type="text/javascript">
$(document).ready(function() {
    // Handler para o botão de editar
    $(document).on('click', '.btn-editar-aver', function() {
        var id = $(this).data('id');
        var valor = $(this).data('valor');
        var data = $(this).data('data');
        var status = $(this).data('status');

        $('#id_aver_edit').val(id);
        $('#valor_aver_edit').val(valor);
        $('#data_pagamento_edit').val(data);
        $('#status_aver_edit').val(status);
        
        $('#modal-editar-aver').modal('show');
    });

    // Handler para o botão de excluir
    $(document).on('click', '.btn-excluir-aver', function() {
        var idAver = $(this).data('id');
        var os_id = $(this).data('os');
        
        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação não poderá ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Não, cancelar!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/os/excluirAver',
                    type: 'POST',
                    data: {
                        idAver: idAver,
                        os_id: os_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.result) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message
                            }).then(() => {
                                // Atualiza a tabela de avers
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/os/getAvers/' + os_id,
                                    type: 'GET',
                                    success: function(response) {
                                        $("#divAvers").html(response);
                                    }
                                });
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: response.message
                            });
                        }
                    }
                });
            }
        });
    });
});
</script>

<?php else: ?>
<div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>Informação!</strong> Não foi possível carregar os avers. Por favor, tente novamente.
</div>
<?php endif; ?> 