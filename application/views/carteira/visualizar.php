<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-money"></i>
        </span>
        <h5>Detalhes da Transação</h5>
    </div>

    <div class="widget-box">
        <div class="widget-content">
            <div class="row-fluid" style="min-height: 100px;">
                <div class="span12">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 150px;"><strong>Data</strong></td>
                                <td><?php echo date('d/m/Y', strtotime($result->data_transacao)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tipo</strong></td>
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
                                <td><strong>Descrição</strong></td>
                                <td><?php echo $result->descricao; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Data de Registro</strong></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($result->data_transacao)); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="widget-box" style="margin-top: 20px;">
        <div class="widget-content nopadding" style="padding: 20px !important">
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="<?php echo base_url('index.php/carteira'); ?>" class="button btn btn-warning">
                    <span class="button__icon"><i class="bx bx-undo"></i></span>
                    <span class="button__text2">Voltar</span>
                </a>
            </div>
        </div>
    </div>
</div> 