<style>
/* Estilos personalizados */
.badgebox {
    opacity: 0;
}

.badgebox+.badge {
    text-indent: -999999px;
    width: 27px;
}

.badgebox:focus+.badge {
    box-shadow: inset 0px 0px 5px;
}

.badgebox:checked+.badge {
    text-indent: 0;
}

.form-horizontal .control-group {
    border-bottom: 1px solid #ffffff;
}

.form-horizontal .controls {
    margin-left: 20px;
    padding-bottom: 8px 0;
}

.form-horizontal .control-label {
    text-align: left;
    padding-top: 15px;
}

.nopadding {
    padding: 0 20px !important;
    margin-right: 20px;
}

.widget-title h5 {
    padding-bottom: 30px;
    text-align-last: left;
    font-size: 2em;
    font-weight: 500;
}

@media (max-width: 480px) {
    form {
        display: contents !important;
    }

    .form-horizontal .control-label {
        margin-bottom: -6px;
    }

    .btn-xs {
        position: initial !important;
    }
}

.div-bord {
    border: 1px solid black;
    padding: 1%;
    margin-bottom: 2%;
}
</style>

<div class="accordion" id="collapse-group">
    <div class="accordion-group widget-box">
        <div class="accordion-heading">
            <div class="widget-title" style="margin: -20px 0 0">
                <a data-parent="#collapse-group" href="#collapseGOne" data-toggle="collapse">
                    <span class="icon"><i class="fas fa-boxes"></i></span>
                    <h5>Dados do Organizador</h5>
                </a>
            </div>
        </div>
        <div class="collapse in accordion-body">
            <div class="widget-content span12">
                <!-- Dados do Organizador -->
                <div class="span6 div-bord">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Nome do Organizador</strong></td>
                                <td><?php echo isset($result->nome_organizador) ? $result->nome_organizador : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Localização</strong></td>
                                <td><?php echo isset($result->localizacao) ? $result->localizacao : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>
                                    <?php if (isset($result->ativa)) : ?>
                                        <?php echo ($result->ativa == 1) ? 'Ativo' : 'Inativo'; ?>
                                    <?php else : ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Compartimentos do Organizador -->
                <div class="span6 div-bord">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><strong>Compartimentos</strong></th>
                                <th><strong>Status</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($result->compartimentos)) : ?>
                                <?php foreach ($result->compartimentos as $compartimento) : ?>
                                    <tr>
                                        <td><?php echo isset($compartimento->nome_compartimento) ? $compartimento->nome_compartimento : 'N/A'; ?></td>
                                        <td>
                                            <?php if (isset($compartimento->ativa)) : ?>
                                                <?php echo ($compartimento->ativa == 1) ? 'Ativo' : 'Inativo'; ?>
                                            <?php else : ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="2">Nenhum compartimento encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>