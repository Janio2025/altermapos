<div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-money"></i>
        </span>
        <h5>Detalhes da Transação</h5>
    </div>
    <div class="widget-content">
        <div class="row-fluid">
            <div class="span12">
                <div class="span6">
                    <h4>Informações da Transação</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 150px;"><strong>Tipo</strong></td>
                                <td>
                                    <?php
                                    switch ($result->tipo) {
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
                            </tr>
                            <tr>
                                <td><strong>Valor</strong></td>
                                <td>R$ <?php echo number_format($result->valor, 2, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Data</strong></td>
                                <td><?php echo date('d/m/Y', strtotime($result->data_transacao)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Descrição</strong></td>
                                <td><?php echo $result->descricao; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer" style="display:flex;justify-content: center">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCarteira')) : ?>
            <a href="<?php echo base_url() ?>index.php/carteira/editar/<?php echo $result->idTransacoesUsuario; ?>" class="button btn btn-mini btn-info">
                <span class="button__icon"><i class="bx bx-edit"></i></span>
                <span class="button__text2">Editar</span>
            </a>
        <?php endif; ?>
        <a href="<?php echo base_url() ?>index.php/carteira" class="button btn btn-mini btn-warning">
            <span class="button__icon"><i class="bx bx-undo"></i></span>
            <span class="button__text2">Voltar</span>
        </a>
    </div>
</div> 