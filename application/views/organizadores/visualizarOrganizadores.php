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

.widget-box {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    max-width: 1000px;
    margin: 20px auto;
}

.widget-title {
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
    border-bottom: 1px solid #eee;
}

.widget-title a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #2c3e50;
}

.widget-title .icon {
    margin-right: 10px;
    font-size: 1.2em;
    color: #3498db;
}

.widget-title h5 {
    margin: 0;
    font-size: 1.2em;
    font-weight: 600;
    color: #2c3e50;
}

.widget-content {
    padding: 20px;
}

.info-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.info-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.info-table th,
.info-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
}

.info-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #34495e;
    text-transform: uppercase;
    font-size: 0.85em;
    letter-spacing: 0.5px;
}

.info-table tr:last-child td {
    border-bottom: none;
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: 500;
}

.status-active {
    background: #e1f6ed;
    color: #2ecc71;
}

.status-inactive {
    background: #feeaea;
    color: #e74c3c;
}

@media (max-width: 768px) {
    .widget-content {
        padding: 15px;
    }
    
    .info-table {
        display: block;
        overflow-x: auto;
    }
}
</style>

<div class="accordion" id="collapse-group">
    <div class="widget-box">
        <div class="accordion-heading">
            <div class="widget-title">
                <a data-parent="#collapse-group" href="#collapseGOne" data-toggle="collapse">
                    <span class="icon"><i class="fas fa-boxes"></i></span>
                    <h5>Dados do Organizador</h5>
                </a>
            </div>
        </div>
        <div class="collapse in accordion-body">
            <div class="widget-content">
                <div class="row-fluid">
                    <!-- Dados do Organizador -->
                    <div class="span6">
                        <div class="info-card">
                            <h6 style="color: #34495e; font-size: 1em; margin-bottom: 15px; font-weight: 600;">Informações Básicas</h6>
                            <table class="info-table">
                                <tbody>
                                    <tr>
                                        <td style="color: #7f8c8d; width: 40%;">Nome do Organizador</td>
                                        <td style="color: #2c3e50; font-weight: 500;">
                                            <?php echo isset($result->nome_organizador) ? $result->nome_organizador : 'N/A'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: #7f8c8d;">Localização</td>
                                        <td style="color: #2c3e50; font-weight: 500;">
                                            <?php echo isset($result->localizacao) ? $result->localizacao : 'N/A'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: #7f8c8d;">Status</td>
                                        <td>
                                            <?php if (isset($result->ativa)) : ?>
                                                <span class="status-badge <?php echo ($result->ativa == 1) ? 'status-active' : 'status-inactive'; ?>">
                                                    <?php echo ($result->ativa == 1) ? 'Ativo' : 'Inativo'; ?>
                                                </span>
                                            <?php else : ?>
                                                <span class="status-badge status-inactive">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Compartimentos do Organizador -->
                    <div class="span6">
                        <div class="info-card">
                            <h6 style="color: #34495e; font-size: 1em; margin-bottom: 15px; font-weight: 600;">Compartimentos</h6>
                            <table class="info-table">
                                <thead>
                                    <tr>
                                        <th>Compartimento</th>
                                        <th style="width: 120px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($result->compartimentos)) : ?>
                                        <?php foreach ($result->compartimentos as $compartimento) : ?>
                                            <tr>
                                                <td style="color: #2c3e50;">
                                                    <?php echo isset($compartimento->nome_compartimento) ? $compartimento->nome_compartimento : 'N/A'; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($compartimento->ativa)) : ?>
                                                        <span class="status-badge <?php echo ($compartimento->ativa == 1) ? 'status-active' : 'status-inactive'; ?>">
                                                            <?php echo ($compartimento->ativa == 1) ? 'Ativo' : 'Inativo'; ?>
                                                        </span>
                                                    <?php else : ?>
                                                        <span class="status-badge status-inactive">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="2" style="text-align: center; color: #7f8c8d;">
                                                Nenhum compartimento encontrado.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>