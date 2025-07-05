<div class="widget-box" style="margin: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <div class="widget-title" style="margin: 0; font-size: 1.1em; background: #f8f9fa; border-radius: 8px 8px 0 0; padding: 15px;">
        <ul class="nav nav-tabs" style="border-bottom: none;">
            <li class="active"><a data-toggle="tab" href="#tab1" style="color: #495057; font-weight: 500;">Dados do Cliente</a></li>
            <li><a data-toggle="tab" href="#tab2" style="color: #495057;">Ordens de Serviço</a></li>
            <li><a data-toggle="tab" href="#tab3" style="color: #495057;">Vendas</a></li>
        </ul>
    </div>
    <div class="widget-content tab-content" style="background: #fff; padding: 20px; border-radius: 0 0 8px 8px;">
        <div id="tab1" class="tab-pane active" style="min-height: 300px">
            <div class="accordion" id="collapse-group">
                <div class="accordion-group widget-box" style="margin-bottom: 15px; border: 1px solid #e9ecef; border-radius: 6px;">
                    <div class="accordion-heading">
                        <div class="widget-title" style="background: #f8f9fa; padding: 12px; border-radius: 6px 6px 0 0;">
                            <a data-parent="#collapse-group" href="#collapseGOne" data-toggle="collapse" style="text-decoration: none; color: #495057;">
                                <span><i class='bx bx-user icon-cli' style="font-size: 1.2em; color: #0056b3;"></i></span>
                                <h5 style="padding-left: 28px; margin: 0; display: inline-block;">Dados Pessoais</h5>
                            </a>
                        </div>
                    </div>
                    <div class="collapse in accordion-body" id="collapseGOne">
                        <div class="widget-content" style="padding: 15px;">
                            <table class="table table-bordered" style="border: 1px solid #e9ecef; margin-bottom: 0;">
                                <tbody>
                                <tr>
                                    <td style="text-align: right; width: 30%"><strong>Nome</strong></td>
                                    <td>
                                        <?php echo $result->nomeCliente ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Documento</strong></td>
                                    <td>
                                        <?php echo $result->documento ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Data de Cadastro</strong></td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($result->dataCadastro)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Tipo do Cliente</strong></td>
                                    <td>
                                        <?php 
                                        if ($result->fornecedor == 1) {
                                            echo '<span class="label label-primary">Fornecedor</span>';
                                        } elseif ($result->fornecedor == 2) {
                                            echo '<span class="label label-warning">Colaborador</span>';
                                        } else {
                                            echo '<span class="label label-success">Cliente</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Tipo de Interesse</strong></td>
                                    <td>
                                        <?php 
                                        if ($result->tipo_id) {
                                            $tipo = $this->db->where('id', $result->tipo_id)->get('tipos')->row();
                                            if ($tipo) {
                                                echo $tipo->nome;
                                            } else {
                                                echo '<span class="text-muted">Não informado</span>';
                                            }
                                        } else {
                                            echo '<span class="text-muted">Não informado</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-group widget-box" style="margin-bottom: 15px; border: 1px solid #e9ecef; border-radius: 6px;">
                    <div class="accordion-heading">
                        <div class="widget-title" style="background: #f8f9fa; padding: 12px; border-radius: 6px 6px 0 0;">
                            <a data-parent="#collapse-group" href="#collapseGTwo" data-toggle="collapse" style="text-decoration: none; color: #495057;">
                                <span><i class='bx bx-phone icon-cli' style="font-size: 1.2em; color: #0056b3;"></i></span>
                                <h5 style="padding-left: 28px; margin: 0; display: inline-block;">Contatos</h5>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGTwo">
                        <div class="widget-content" style="padding: 15px;">
                            <table class="table table-bordered" style="border: 1px solid #e9ecef; margin-bottom: 0;">
                                <tbody>
                                <tr>
                                    <td style="text-align: right; width: 30%"><strong>Contato:</strong></td>
                                    <td>
                                        <?php echo $result->contato ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 30%"><strong>Telefone</strong></td>
                                    <td>
                                        <?php echo $result->telefone ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Celular</strong></td>
                                    <td>
                                        <?php echo $result->celular ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Email</strong></td>
                                    <td>
                                        <?php echo $result->email ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-group widget-box" style="margin-bottom: 15px; border: 1px solid #e9ecef; border-radius: 6px;">
                    <div class="accordion-heading">
                        <div class="widget-title" style="background: #f8f9fa; padding: 12px; border-radius: 6px 6px 0 0;">
                            <a data-parent="#collapse-group" href="#collapseGThree" data-toggle="collapse" style="text-decoration: none; color: #495057;">
                                <span><i class='bx bx-map-alt icon-cli' style="font-size: 1.2em; color: #0056b3;"></i></span>
                                <h5 style="padding-left: 28px; margin: 0; display: inline-block;">Endereço</h5>
                            </a>
                        </div>
                    </div>
                    <div class="collapse accordion-body" id="collapseGThree">
                        <div class="widget-content" style="padding: 15px;">
                            <table class="table table-bordered" style="border: 1px solid #e9ecef; margin-bottom: 0;">
                                <tbody>
                                <tr>
                                    <td style="text-align: right; width: 30%;"><strong>Rua</strong></td>
                                    <td>
                                        <?php echo $result->rua ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Número</strong></td>
                                    <td>
                                        <?php echo $result->numero ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Complemento</strong></td>
                                    <td>
                                        <?php echo $result->complemento ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Bairro</strong></td>
                                    <td>
                                        <?php echo $result->bairro ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>Cidade</strong></td>
                                    <td>
                                        <?php echo $result->cidade ?> -
                                        <?php echo $result->estado ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>CEP</strong></td>
                                    <td>
                                        <?php echo $result->cep ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Tab 2-->
        <div id="tab2" class="tab-pane" style="min-height: 300px">
            <?php if (!$results) { ?>
                <table class="table table-bordered" style="border: 1px solid #e9ecef; margin-top: 20px;">
                    <thead style="background: #f8f9fa;">
                    <tr>
                        <th>N° OS</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
                        <th>Descricao</th>
                        <th>Defeito</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">Nenhuma OS Cadastrada</td>
                    </tr>
                    </tbody>
                </table>
            <?php } else { ?>
                <table class="table table-bordered" style="border: 1px solid #e9ecef; margin-top: 20px;">
                    <thead style="background: #f8f9fa;">
                    <tr>
                        <th>N° OS</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
                        <th>Descricao</th>
                        <th>Defeito</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($results as $r) {
                        $dataInicial = date(('d/m/Y'), strtotime($r->dataInicial));
                        $dataFinal = date(('d/m/Y'), strtotime($r->dataFinal));
                        echo '<tr>';
                        echo '<td>' . $r->idOs . '</td>';
                        echo '<td>' . $dataInicial . '</td>';
                        echo '<td>' . $dataFinal . '</td>';
                        echo '<td>' . $r->descricaoProduto . '</td>';
                        echo '<td>' . $r->defeito . '</td>';
                        echo '<td style="text-align: center;">';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                            echo '<a href="' . base_url() . 'index.php/os/visualizar/' . $r->idOs . '" class="btn btn-sm btn-default" style="margin-right: 5px;" title="Ver mais detalhes"><i class="fas fa-eye"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
                            echo '<a href="' . base_url() . 'index.php/os/editar/' . $r->idOs . '" class="btn btn-sm btn-info" title="Editar OS"><i class="fas fa-edit"></i></a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
        <!--Tab 3-->
        <div id="tab3" class="tab-pane" style="min-height: 300px">
            <?php if (!$result_vendas) { ?>
                <table class="table table-bordered" style="border: 1px solid #e9ecef; margin-top: 20px;">
                    <thead style="background: #f8f9fa;">
                    <tr>
                        <th>N° Venda</th>
                        <th>Data</th>
                        <th>Faturado</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">Nenhuma Venda Cadastrada</td>
                    </tr>
                    </tbody>
                </table>
            <?php } else { ?>
                <table class="table table-bordered" style="border: 1px solid #e9ecef; margin-top: 20px;">
                    <thead style="background: #f8f9fa;">
                    <tr>
                        <th>N° Venda</th>
                        <th>Data</th>
                        <th>Faturado</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($result_vendas as $r) {
                        $dataVenda = date(('d/m/Y'), strtotime($r->dataVenda));
                        $faturado = $r->faturado == 1 ? '<span class="badge" style="background-color: #28a745; color: white;">Sim</span>' : '<span class="badge" style="background-color: #dc3545; color: white;">Não</span>';
                        echo '<tr>';
                        echo '<td>' . $r->idVendas . '</td>';
                        echo '<td>' . $dataVenda . '</td>';
                        echo '<td style="text-align: center;">' . $faturado . '</td>';
                        echo '<td>R$ ' . number_format($r->valorTotal, 2, ',', '.') . '</td>';
                        echo '<td style="text-align: center;">';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                            echo '<a href="' . base_url() . 'index.php/vendas/visualizar/' . $r->idVendas . '" class="btn btn-sm btn-default" style="margin-right: 5px;" title="Ver mais detalhes"><i class="fas fa-eye"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
                            echo '<a href="' . base_url() . 'index.php/vendas/editar/' . $r->idVendas . '" class="btn btn-sm btn-info" title="Editar Venda"><i class="fas fa-edit"></i></a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
    <div class="modal-footer" style="display: flex; justify-content: center; padding: 20px; background: #f8f9fa; border-radius: 0 0 8px 8px;">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) {
            echo '<a title="Editar Cliente" class="button btn btn-info" style="min-width: 140px; margin-right: 10px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" href="' . base_url() . 'index.php/clientes/editar/' . $result->idClientes . '">
                <i class="bx bx-edit" style="margin-right: 5px;"></i>Editar
            </a>';
        } ?>
        <a title="Voltar" class="button btn btn-warning" style="min-width: 140px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" href="<?php echo site_url() ?>/clientes">
            <i class="bx bx-undo" style="margin-right: 5px;"></i>Voltar
        </a>
    </div>
</div>
