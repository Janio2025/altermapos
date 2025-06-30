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
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 8px 8px 0 0;
        padding: 15px 20px;
    }
    .modal-saque .modal-title {
        color: #2D335B;
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
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
        padding: 1px;
        margin: 0;
        width: 100%;
    }
    
    .summary-card {
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        min-width: 0;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        background: linear-gradient(135deg, #28a745, #20c997); /* padrão verde */
        color: #fff;
    }

    .summary-card.span12 {
        grid-column: 1 / -1;
    }
    
    /* Estilos para o modal de movimentações */
    .modal-movimentacoes .modal-header {
        background: linear-gradient(90deg, #28a745 0%, #17a2b8 100%);
        color: #fff;
        border-radius: 8px 8px 0 0;
        box-shadow: 0 2px 8px rgba(40,167,69,0.08);
        padding: 18px 24px;
    }
    
    .modal-movimentacoes .modal-title {
        font-size: 1.3rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
    }
    
    .modal-movimentacoes .modal-title i {
        font-size: 1.7rem;
        color: #fff;
    }
    
    .modal-movimentacoes .modal-body {
        background: #f8f9fa;
        padding: 28px 18px 18px 18px;
        border-radius: 0 0 8px 8px;
    }
    
    .modal-movimentacoes .table {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    
    .modal-movimentacoes .table th {
        background: #e9ecef;
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 10px;
    }
    
    .modal-movimentacoes .table td {
        padding: 10px 10px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f1f1;
        font-size: 1.05em;
    }
    
    .modal-movimentacoes .table tr:last-child td {
        border-bottom: none;
    }
    
    .modal-movimentacoes .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f6f8fa;
    }
    
    .modal-movimentacoes .total-row {
        background: linear-gradient(90deg, #28a745 0%, #17a2b8 100%);
        color: #fff;
        font-weight: bold;
        font-size: 1.1em;
    }
    
    .modal-movimentacoes .empty-state {
        text-align: center;
        color: #6c757d;
        padding: 40px 0 20px 0;
    }
    
    .modal-movimentacoes .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #17a2b8;
    }
    
    .summary-card .card-title {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #fff;
    }
    
    .summary-card .card-title i {
        font-size: 24px;
        color: #fff;
    }
    
    .summary-card .card-title span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 16px;
        color: #fff;
    }
    
    .summary-card .card-value {
        font-size: 24px;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 15px;
        font-weight: bold;
    }
    
    /* Cores específicas para cada card */
    .summary-card[data-card="retirada"] {
        background: linear-gradient(135deg, #dc3545, #c82333);
    }
    .summary-card[data-card="comissao"] {
        background: linear-gradient(135deg, #ffc107, #ff9800);
        color: #2D335B;
    }
    .summary-card[data-card="comissao"] .card-title,
    .summary-card[data-card="comissao"] .card-title i,
    .summary-card[data-card="comissao"] .card-title span,
    .summary-card[data-card="comissao"] .card-value {
        color: #2D335B;
    }
    .summary-card[data-card="bonus"] {
        background: linear-gradient(135deg, #17a2b8, #007bff);
    }
    .summary-card[data-card="salario"] {
        background: linear-gradient(135deg, #6f42c1, #8e44ad);
    }
    .summary-card[data-card="ganhos"] {
        background: linear-gradient(135deg, #28a745, #20c997);
    }
    
    /* Media queries para responsividade */
    @media screen and (max-width: 1200px) {
        .cards-grid {
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }
    }
    
    @media screen and (max-width: 992px) {
        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        
        .summary-card:nth-child(5) {
            grid-column: 1 / -1;
        }
    }
    
    @media screen and (max-width: 768px) {
        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }
        
        .summary-card {
            padding: 8px;
        }
        
        .summary-card .card-title {
            margin-bottom: 6px;
        }
        
        .summary-card .card-title i {
            font-size: 14px;
        }
        
        .summary-card .card-title span {
            font-size: 12px;
        }
        
        .summary-card .card-value {
            font-size: 16px;
            margin-top: 6px;
        }
    }

    /* Estilo para limitar o tamanho da descrição */
    .table-transactions .col-descricao {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table-scroll-container {
        max-height: 750px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 4px;
        position: relative;
    }
    .table-scroll-container table {
        margin-bottom: 0;
        width: 100%;
    }
    .table-header-fixed {
        position: sticky;
        top: 0;
        background-color: #F9F9F9;
        z-index: 1;
        width: 100%;
    }
    .table-transactions {
        margin-bottom: 0;
        width: 100%;
    }
    .table-transactions th {
        background-color: #f5f5f5;
        border-bottom: 2px solid #ddd;
        font-weight: 600;
        text-align: left;
        padding: 12px 8px;
        white-space: nowrap;
    }
    .table-transactions td {
        padding: 10px 8px;
        vertical-align: middle;
    }
    /* Definindo larguras fixas para cada coluna apenas em telas maiores */
    @media screen and (min-width: 769px) {
        .table-transactions {
            table-layout: fixed;
        }
        .table-transactions th:nth-child(1),
        .table-transactions td:nth-child(1) {
            width: 100px; /* Data */
        }
        .table-transactions th:nth-child(2),
        .table-transactions td:nth-child(2) {
            width: 100px; /* Tipo */
        }
        .table-transactions th:nth-child(3),
        .table-transactions td:nth-child(3) {
            width: 100px; /* ID */
        }
        .table-transactions th:nth-child(4),
        .table-transactions td:nth-child(4) {
            width: 120px; /* Valor */
            text-align: right;
        }
        .table-transactions th:nth-child(5),
        .table-transactions td:nth-child(5) {
            width: 300px; /* Descrição */
        }
        .table-transactions th:nth-child(6),
        .table-transactions td:nth-child(6) {
            width: 80px; /* Ações */
            text-align: center;
        }
    }
    @media screen and (max-width: 768px) {
        .table-scroll-container {
            max-height: none;
            border: none;
            overflow: visible;
        }
        .table-transactions {
            display: block;
        }
        .table-transactions thead {
            display: none;
        }
        .table-transactions tbody {
            display: block;
            padding: 10px;
            max-height: 600px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }
        .table-transactions tbody::-webkit-scrollbar {
            width: 6px;
        }
        .table-transactions tbody::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        .table-transactions tbody::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        .table-transactions tbody::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .table-transactions tr {
            display: block;
            margin-bottom: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px;
        }
        .table-transactions td {
            display: block;
            padding: 8px 0;
            border: none;
            position: relative;
            width: 100%;
        }
        .table-transactions td:before {
            content: attr(data-label);
            font-weight: 600;
            color: #666;
            margin-right: 10px;
            display: inline-block;
            min-width: 100px;
        }
        .table-transactions .col-data:before {
            content: "Data:";
        }
        .table-transactions .col-tipo:before {
            content: "Tipo:";
        }
        .table-transactions .col-id:before {
            content: "ID:";
        }
        .table-transactions .col-valor:before {
            content: "Valor:";
        }
        .table-transactions .col-descricao:before {
            content: "Descrição:";
        }
        .table-transactions .col-acoes {
            text-align: right;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .table-transactions .col-acoes:before {
            display: none;
        }
        .table-transactions .label {
            display: inline-block;
            min-width: 85px;
            text-align: center;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
        }
    }
    .table-transactions .label {
        display: inline-block;
        min-width: 85px;
        text-align: center;
        font-weight: 600;
    }
    .table-transactions tbody tr:hover {
        background-color: #f9f9f9;
    }
    .table-transactions .btn-nwe {
        padding: 5px 10px;
        border-radius: 3px;
    }
    .table-transactions .btn-nwe i {
        font-size: 16px;
    }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bx-wallet"></i>
        </span>
        <h5>Carteira</h5>
    </div>
    
    <!-- Saldo da Carteira e Comissão Pendente -->
    <div class="row-fluid">
        <!-- Saldo da Carteira -->
        <div class="span6">
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
        </div>

        <!-- Comissão Pendente -->
        <div class="span6">
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
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <div class="comissao-value" style="font-size: 28px; color: #ffc107; margin-top: 10px;">
                                    R$ <span id="comissao-pendente">0,00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Mensal -->
    <div class="widget-box">
        <div class="widget-title" style="display: flex; align-items: center; padding: 10px 20px;">
            <span class="icon">
                <i class="bx bx-chart"></i>
            </span>
            <div class="filtro-periodo" style="margin-left: 10px; display: flex; align-items: center;">
                <select id="tipo-filtro" class="form-control" style="width: auto; display: inline-block;">
                    <option value="mes">Mês</option>
                    <option value="ano">Ano</option>
                    <option value="periodo">Período</option>
                </select>
                <div id="filtro-periodo-campos" style="display: none; margin-left: 10px;">
                    <input type="date" id="data-inicio" class="form-control" style="width: auto; display: inline-block;">
                    <input type="date" id="data-fim" class="form-control" style="width: auto; display: inline-block;">
                    <button id="btn-buscar" style="margin-top: -10px;" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </div>
        <div class="widget-content">
            <div class="row-fluid" style="padding: 0;">
                <div class="span12" style="padding: 0;">
                    <div class="cards-grid">
                        <!-- Card Retiradas -->
                        <div class="summary-card" data-card="retirada" onclick="abrirModalMovimentacoes('retirada', 'Retiradas', '#dc3545', 'bx-transfer-alt')">
                            <div class="card-title">
                                <i class="bx bx-transfer-alt"></i>
                                <span>Retiradas</span>
                            </div>
                            <div class="card-value">
                                R$ <span id="total-retiradas">0,00</span>
                            </div>
                        </div>
                        <!-- Card Comissões -->
                        <div class="summary-card" data-card="comissao" onclick="abrirModalMovimentacoes('comissao', 'Comissões', '#ffc107', 'bx-money-withdraw')">
                            <div class="card-title">
                                <i class="bx bx-money-withdraw"></i>
                                <span>Comissões</span>
                            </div>
                            <div class="card-value">
                                R$ <span id="total-comissoes">0,00</span>
                            </div>
                        </div>
                        <!-- Card Bônus -->
                        <div class="summary-card" data-card="bonus" onclick="abrirModalMovimentacoes('bonus', 'Bônus', '#17a2b8', 'bx-gift')">
                            <div class="card-title">
                                <i class="bx bx-gift"></i>
                                <span>Valores</span>
                            </div>
                            <div class="card-value">
                                R$ <span id="total-bonus">0,00</span>
                            </div>
                        </div>
                        <!-- Card Salário -->
                        <div class="summary-card" data-card="salario" onclick="abrirModalMovimentacoes('salario', 'Salário', '#6f42c1', 'bx-wallet')">
                            <div class="card-title">
                                <i class="bx bx-wallet"></i>
                                <span>Salário</span>
                            </div>
                            <div class="card-value">
                                R$ <span id="total-salario">0,00</span>
                            </div>
                        </div>
                        <!-- Card Ganhos -->
                        <div class="summary-card" data-card="ganhos" onclick="abrirModalMovimentacoes('ganhos', 'Ganhos Totais', '#28a745', 'bx-money')">
                            <div class="card-title">
                                <i class="bx bx-money"></i>
                                <span>Ganhos</span>
                            </div>
                            <div class="card-value">
                                R$ <span id="total-ganhos">0,00</span>
                            </div>
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
                    position: relative;
                }
                .table-scroll-container table {
                    margin-bottom: 0;
                    width: 100%;
                }
                .table-header-fixed {
                    position: sticky;
                    top: 0;
                    background-color: #F9F9F9;
                    z-index: 1;
                    width: 100%;
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
                    white-space: nowrap;
                }
                .table-transactions td {
                    padding: 10px 8px;
                    vertical-align: middle;
                }
                @media screen and (max-width: 768px) {
                    .table-scroll-container {
                        max-height: none;
                        border: none;
                        overflow: visible;
                    }
                    .table-transactions {
                        display: block;
                    }
                    .table-transactions thead {
                        display: none;
                    }
                    .table-transactions tbody {
                        display: block;
                        padding: 10px;
                        max-height: 600px; /* Altura para 5 cards aproximadamente */
                        overflow-y: auto;
                        scrollbar-width: thin;
                        scrollbar-color: #888 #f1f1f1;
                    }
                    .table-transactions tbody::-webkit-scrollbar {
                        width: 6px;
                    }
                    .table-transactions tbody::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 3px;
                    }
                    .table-transactions tbody::-webkit-scrollbar-thumb {
                        background: #888;
                        border-radius: 3px;
                    }
                    .table-transactions tbody::-webkit-scrollbar-thumb:hover {
                        background: #555;
                    }
                    .table-transactions tr {
                        display: block;
                        margin-bottom: 15px;
                        background: #fff;
                        border-radius: 8px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        padding: 15px;
                    }
                    .table-transactions td {
                        display: block;
                        padding: 8px 0;
                        border: none;
                        position: relative;
                    }
                    .table-transactions td:before {
                        content: attr(data-label);
                        font-weight: 600;
                        color: #666;
                        margin-right: 10px;
                        display: inline-block;
                        min-width: 100px;
                    }
                    .table-transactions .col-data:before {
                        content: "Data:";
                    }
                    .table-transactions .col-tipo:before {
                        content: "Tipo:";
                    }
                    .table-transactions .col-id:before {
                        content: "ID:";
                    }
                    .table-transactions .col-valor:before {
                        content: "Valor:";
                    }
                    .table-transactions .col-descricao:before {
                        content: "Descrição:";
                    }
                    .table-transactions .col-acoes {
                        text-align: right;
                        padding-top: 10px;
                        border-top: 1px solid #eee;
                    }
                    .table-transactions .col-acoes:before {
                        display: none;
                    }
                    .table-transactions .label {
                        display: inline-block;
                        min-width: 85px;
                        text-align: center;
                        font-weight: 600;
                        padding: 4px 8px;
                        border-radius: 4px;
                    }
                }
                .table-transactions .label {
                    display: inline-block;
                    min-width: 85px;
                    text-align: center;
                    font-weight: 600;
                }
                .table-transactions tbody tr:hover {
                    background-color: #f9f9f9;
                }
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
                        <th>ID</th>
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
                                    <td class="col-data"><?php echo date('d/m/Y H:i:s', strtotime($t->data_transacao)); ?></td>
                                    <td class="col-tipo">
                                        <?php
                                        switch ($t->tipo) {
                                            case 'salario':
                                                echo '<span class="label label-success" data-tipo="salario">Salário</span>';
                                                break;
                                            case 'bonus':
                                                echo '<span class="label label-info" data-tipo="bonus">Valores</span>';
                                                break;
                                            case 'comissao':
                                                echo '<span class="label label-warning" data-tipo="comissao">Comissão</span>';
                                                break;
                                            case 'retirada':
                                                echo '<span class="label label-important" data-tipo="retirada">Retirada</span>';
                                                break;
                                        }
                                        ?>
                                    </td>

                                    <td class="col-id" title="<?php echo htmlspecialchars($t->idTransacoesUsuario); ?>">
                                        <?php 
                                            echo htmlspecialchars($t->idTransacoesUsuario); 
                                            if (strlen($t->idTransacoesUsuario) > 50) {
                                                echo '&nbsp;<a href="#" class="btn-nwe" onclick="mostrarDescricaoCompleta(\'' . htmlspecialchars(addslashes($t->idTransacoesUsuario)) . '\'); return false;" title="Ver descrição completa"><i class=""></i></a>';
                                            }
                                        ?>
                                    </td>


                                    <td class="col-valor">R$ <?php echo number_format($t->valor, 2, ',', '.'); ?></td>

                                    <td class="col-descricao" title="<?php echo htmlspecialchars($t->descricao); ?>">
                                        <?php 
                                            echo htmlspecialchars($t->descricao); 
                                            if (strlen($t->descricao) > 50) {
                                                echo '&nbsp;<a href="#" class="btn-nwe" onclick="mostrarDescricaoCompleta(\'' . htmlspecialchars(addslashes($t->descricao)) . '\'); return false;" title="Ver descrição completa"><i class=""></i></a>';
                                            }
                                        ?>
                                    </td>
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
                            <i class="bx bx-info-circle"></i>
                            O valor será enviado para a chave PIX cadastrada na sua carteira
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="bx bx-x"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success btn-confirmar" onclick="realizarSaquePix()">
                        <i class="bx bx-check"></i> Confirmar Saque
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove o campo de chave PIX -->
    <div class="control-group" style="display: none;">
        <label for="chave_pix" class="control-label">Chave PIX para Saques</label>
        <div class="controls">
            <input type="text" name="chave_pix" id="chave_pix" class="input-xlarge" 
                value="<?php echo isset($config) ? $config->chave_pix : ''; ?>" 
                placeholder="Digite sua chave PIX (CPF, Email, Telefone ou Chave Aleatória)">
            <span class="help-inline">Esta chave PIX será usada para receber os saques da sua carteira</span>
        </div>
    </div>

    <!-- Modal de Movimentações -->
    <div class="modal fade modal-movimentacoes" id="modalMovimentacoes" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx" id="modalMovimentacoesIcon"></i>
                        <span id="modalMovimentacoesTitulo"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modalMovimentacoesConteudo"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            let valorBase = parseFloat(response.valor);
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

        // Atualiza o valor da comissão pendente a cada 30 segundos
        buscarValorBase(); // Chama imediatamente ao carregar
        setInterval(buscarValorBase, 30000); // Atualiza a cada 30 segundos

        // Função para atualizar os totais baseado no filtro selecionado
        function atualizarTotais() {
            let totalRetiradas = 0;
            let totalComissoes = 0;
            let totalBonus = 0;
            let totalSalario = 0;

            const tipoFiltro = $('#tipo-filtro').val();
            let dataInicio, dataFim;

            if (tipoFiltro === 'mes') {
                // Mantém a lógica atual para mês
                const dataAtual = new Date();
                dataInicio = new Date(dataAtual.getFullYear(), dataAtual.getMonth(), 1);
                dataFim = new Date(dataAtual.getFullYear(), dataAtual.getMonth() + 1, 0);
            } else if (tipoFiltro === 'ano') {
                // Filtro por ano
                const dataAtual = new Date();
                dataInicio = new Date(dataAtual.getFullYear(), 0, 1);
                dataFim = new Date(dataAtual.getFullYear(), 11, 31);
            } else {
                // Filtro por período personalizado
                dataInicio = new Date($('#data-inicio').val());
                dataFim = new Date($('#data-fim').val());
                // Adiciona um dia à data final para incluir todo o dia selecionado
                dataFim.setDate(dataFim.getDate() + 1);
            }

            $('.table-transactions tbody tr').each(function() {
                let data = $(this).find('.col-data').text();
                if (!data) return;

                let tipo = $(this).find('.col-tipo span').attr('data-tipo');
                let valorText = $(this).find('.col-valor').text().replace('R$ ', '');
                let valor = parseFloat(valorText.replace('.', '').replace(',', '.'));
                
                if (isNaN(valor)) return;

                // Extrai apenas a parte da data (antes do espaço)
                let dataParte = data.split(' ')[0];
                let [dia, mes, ano] = dataParte.split('/');
                let dataTransacao = new Date(ano, mes - 1, dia);
                
                // Verifica se a data está dentro do período selecionado
                if (dataTransacao >= dataInicio && dataTransacao <= dataFim) {
                    switch (tipo) {
                        case 'retirada':
                            totalRetiradas += valor;
                            break;
                        case 'comissao':
                            totalComissoes += valor;
                            break;
                        case 'bonus':
                            totalBonus += valor;
                            break;
                        case 'salario':
                            totalSalario += valor;
                            break;
                    }
                }
            });

            // Atualiza os valores nos cards
            $('#total-retiradas').text(formatarMoeda(totalRetiradas));
            $('#total-comissoes').text(formatarMoeda(totalComissoes));
            $('#total-bonus').text(formatarMoeda(totalBonus));
            $('#total-salario').text(formatarMoeda(totalSalario));
            $('#total-ganhos').text(formatarMoeda(totalComissoes + totalBonus + totalSalario));
        }

        // Manipuladores de eventos para o filtro
        $('#tipo-filtro').change(function() {
            const tipoFiltro = $(this).val();
            if (tipoFiltro === 'periodo') {
                $('#filtro-periodo-campos').show();
            } else {
                $('#filtro-periodo-campos').hide();
                atualizarTotais();
            }
        });

        $('#btn-buscar').click(function() {
            if ($('#data-inicio').val() && $('#data-fim').val()) {
                atualizarTotais();
            } else {
                alert('Por favor, selecione as datas de início e fim do período.');
            }
        });

        // Inicializa os campos de data com valores padrão
        const hoje = new Date();
        const primeiroDiaMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
        const ultimoDiaMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);
        
        $('#data-inicio').val(primeiroDiaMes.toISOString().split('T')[0]);
        $('#data-fim').val(ultimoDiaMes.toISOString().split('T')[0]);

        // Função para abrir o modal de movimentações
        window.abrirModalMovimentacoes = function(tipo, titulo, cor, icone) {
            let total = 0;
            let movimentacoes = [];
            const tipoFiltro = $('#tipo-filtro').val();
            let dataInicio, dataFim;
            if (tipoFiltro === 'mes') {
                const dataAtual = new Date();
                dataInicio = new Date(dataAtual.getFullYear(), dataAtual.getMonth(), 1);
                dataFim = new Date(dataAtual.getFullYear(), dataAtual.getMonth() + 1, 0);
            } else if (tipoFiltro === 'ano') {
                const dataAtual = new Date();
                dataInicio = new Date(dataAtual.getFullYear(), 0, 1);
                dataFim = new Date(dataAtual.getFullYear(), 11, 31);
            } else {
                dataInicio = new Date($('#data-inicio').val());
                dataFim = new Date($('#data-fim').val());
                dataFim.setDate(dataFim.getDate() + 1);
            }
            $('.table-transactions tbody tr').each(function() {
                let data = $(this).find('.col-data').text();
                if (!data) return;
                let tipoTransacao = $(this).find('.col-tipo span').attr('data-tipo');
                if (tipo === 'ganhos') {
                    if (tipoTransacao !== 'comissao' && tipoTransacao !== 'bonus' && tipoTransacao !== 'salario') return;
                } else if (tipoTransacao !== tipo) {
                    return;
                }
                let dataParte = data.split(' ')[0];
                let [dia, mes, ano] = dataParte.split('/');
                let dataTransacao = new Date(ano, mes - 1, dia);
                if (dataTransacao >= dataInicio && dataTransacao <= dataFim) {
                    let valorText = $(this).find('.col-valor').text().replace('R$ ', '');
                    let valor = parseFloat(valorText.replace('.', '').replace(',', '.'));
                    let descricao = $(this).find('.col-descricao').text();
                    movimentacoes.push({
                        data: data,
                        valor: valor,
                        descricao: descricao
                    });
                    total += valor;
                }
            });
            let html = '';
            if (movimentacoes.length > 0) {
                html = `
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Data</th>
                                <th style="width: 130px;">Valor</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                movimentacoes.forEach(mov => {
                    html += `
                        <tr>
                            <td style="text-align: center;">${mov.data}</td>
                            <td style="text-align: right;">R$ ${formatarMoeda(mov.valor)}</td>
                            <td>${mov.descricao}</td>
                        </tr>
                    `;
                });
                html += `
                        <tr class="total-row">
                            <td colspan="1" style="text-align: right;">Total:</td>
                            <td style="text-align: right;">R$ ${formatarMoeda(total)}</td>
                            <td></td>
                        </tr>
                    </tbody>
                    </table>
                `;
            } else {
                html = `
                    <div class="empty-state">
                        <i class="bx bx-info-circle"></i>
                        <p>Nenhuma movimentação encontrada para este período.</p>
                    </div>
                `;
            }
            $('#modalMovimentacoesIcon').attr('class', `bx ${icone}`).css('color', '#fff');
            $('#modalMovimentacoesTitulo').text(titulo +
                (tipoFiltro === 'mes' ? ' do Mês' : tipoFiltro === 'ano' ? ' do Ano' : ' do Período'));
            $('#modalMovimentacoesConteudo').html(html);
            $('#modalMovimentacoes').modal('show');
        }

        function mostrarDescricaoCompleta(descricao) {
            Swal.fire({
                title: 'Descrição Completa',
                text: descricao,
                confirmButtonText: 'Fechar'
            });
        }

        // Executa a atualização dos totais assim que a página carregar
        atualizarTotais();
    });

    function formatarMoeda(valor) {
        return valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
</script>
