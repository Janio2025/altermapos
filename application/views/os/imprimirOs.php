<?php
    $totalServico  = 0;
    $totalProdutos = 0;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->config->item('app_name') ?> - <?= $result->idOs ?> - <?= $result->nomeCliente ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap5.3.2.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/imprimir.css">
</head>
<body>
    <div class="main-page">
        <div class="sub-page">
            <header>
                <?php if ($emitente == null) : ?>
                    <div class="alert alert-danger" role="alert">
                        Você precisa configurar os dados do emitente. >>> <a href="<?=base_url()?>index.php/mapos/emitente">Configurar</a>
                    </div>
                <?php else : ?>
                    <div class="imgLogo" class="align-middle">
                        <img src="<?= $emitente->url_logo ?>" class="img-fluid" style="width:100px;">
                    </div>
                    <div class="emitente">
                        <span style="font-size: 16px;"><b><?= $emitente->nome ?></b></span></br>
                        <?php if($emitente->cnpj != "00.000.000/0000-00") : ?>
                            <span class="align-middle">CNPJ: <?= $emitente->cnpj ?></span></br>
                        <?php endif; ?>
                        <span class="align-middle">
                            <?= $emitente->rua.', '.$emitente->numero.', '.$emitente->bairro ?><br>
                            <?= $emitente->cidade.' - '.$emitente->uf.' - '.$emitente->cep ?>
                        </span>
                    </div>
                    <div class="contatoEmitente">
                        <span style="font-weight: bold;">Tel: <?= $emitente->telefone ?></span></br>
                        <span style="font-weight: bold;"><?= $emitente->email ?></span></br>
                        <span style="word-break: break-word;">Responsável: <b><?= $result->nome ?></b></span>
                    </div>
                <?php endif; ?>
            </header>
            <section>
                <div class="title">
                    <?php if ($configuration['control_2vias']) : ?><span class="via">Via cliente</span><?php endif; ?>
                    ORDEM DE SERVIÇO #<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?>
                    <span class="emissao">Emissão: <?= date('d/m/Y H:i:s') ?></span>
                </div>

                <?php if ($result->dataInicial != null): ?>
                    <div class="tabela">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-secondary">
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center">DATA INICIAL</th>
                                    <th class="text-center">DATA FINAL</th>
                                    <?php if ($result->garantia) : ?>
                                        <th class="text-center">GARANTIA</th>
                                    <?php endif; ?>
                                    <?php if (in_array($result->status, ['Finalizado', 'Faturado'])) : ?>
                                        <th class="text-center">VENC. GARANTIA</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center"><?= $result->status ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($result->dataInicial)) ?></td>
                                    <td class="text-center"><?= $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : '' ?></td>
                                    <?php if ($result->garantia) : ?>
                                        <td class="text-center"><?= $result->garantia . ' dia(s)' ?></td>
                                    <?php endif; ?>
                                    <?php if (in_array($result->status, ['Finalizado', 'Faturado'])) : ?>
                                        <td class="text-center"><?= dateInterval($result->dataFinal, $result->garantia) ?></td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="subtitle">DADOS DO CLIENTE</div>
                <div class="dados">
                    <div>
                        <span><b><?= $result->nomeCliente ?></b></span><br />
                        <span>CPF/CNPJ: <?= $result->documento ?></span><br />
                        <span><?= $result->contato_cliente.' '.$result->telefone ?><?= $result->telefone && $result->celular ? ' / '.$result->celular : $result->celular ?></span><br />
                        <span><?= $result->email ?></span><br />
                    </div>
                    <div style="text-align: right;">
                        <span><?= $result->rua.', '.$result->numero.', '.$result->bairro ?></span><br />
                        <span><?= $result->complemento.' - '.$result->cidade.' - '.$result->estado ?></span><br />
                        <span>CEP: <?= $result->cep ?></span><br />
                                    <?php if ($result->ucProdutoOs) : ?>
                                        <div class="">
                                            <div style="font-weight: bold;"> UC: 
                                                <?= htmlspecialchars_decode($result->ucProdutoOs) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($result->contrato_seguradora) : ?>
                                        <div class="">
                                            <div style="font-weight: bold;"> Nº Seguro: 
                                                <?= htmlspecialchars_decode($result->contrato_seguradora) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                    </div>
                </div>

            
                <?php if ($result->descricaoProduto) : ?>
                    <div class="tabela table-bordered">
                        <table class="table ">
                            <thead>
                                <tr class="table-secondary">
                                    <th class="text-center">PRODUTO</th>
                                    <th class="text-center">MARCA</th>
                                    <th class="text-center">MODELO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center"><?= $result->descricaoProduto ?></td>
                                    <td class="text-center"><?= $result->marcaProdutoOs ?></td>
                                    <td class="text-center"><?= $result->modeloProdutoOs ?></td>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($result->defeito) : ?>
                    <div class="subtitle">DEFEITO APRESENTADO</div>
                    <div class="dados">
                        <div style="text-align: justify;">
                            <?= htmlspecialchars_decode($result->defeito) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($result->observacoes) : ?>
                    <div class="subtitle">OBSERVAÇÕES</div>
                    <div class="dados">
                        <div style="text-align: justify;">
                            <?= htmlspecialchars_decode($result->observacoes) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($result->laudoTecnico) : ?>
					<div class="subtitle">LAUDO TÉCNICO</div>
							<div class="dados">
									<div style="text-align: justify;">
						<?= htmlspecialchars_decode($result->laudoTecnico) ?>
						</div>
						</div>
					<?php endif; ?>

                <?php if ($produtos) : ?>
                    <div class="tabela">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-secondary">
                                    <th>PEÇA / PRODUTO(S)</th>
                                    <th>MARCA</th>
                                    <th>MODELO</th>
                                    <th class="text-center" width="10%">QTD</th>
                                    <th class="text-center" width="10%">UNT</th>
                                    <th class="text-end" width="15%" >SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $p) :
                                    $totalProdutos = $totalProdutos + $p->subTotal;
                                    echo '<tr>';
                                    echo '  <td>' . $p->descricao . '</td>';
                                    echo '  <td>' . $p->marcaProduto . '</td>';
                                    echo '  <td>' . $p->nomeModelo . '</td>';
                                    echo '  <td class="text-center">' . $p->quantidade . '</td>';
                                    echo '  <td class="text-center">' . number_format($p->preco ?: $p->precoVenda, 2, ',', '.') . '</td>';
                                    echo '  <td class="text-end">R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                    echo '</tr>';
                                endforeach; ?>
                                <tr>
                                    <td colspan="5" class="text-end"><b>TOTAL PRODUTOS:</b></td>
                                    <td class="text-end"><b>R$ <?= number_format($totalProdutos, 2, ',', '.') ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <?php if ($servicos) : ?>
                    <div class="tabela">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-secondary">
                                    <th>SERVIÇO(S)</th>
                                    <th class="text-center" width="10%">QTD</th>
                                    <th class="text-center" width="10%">UNT</th>
                                    <th class="text-end" width="15%" >SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    setlocale(LC_MONETARY, 'en_US'); 
                                    foreach ($servicos as $s) :
                                        $preco = $s->preco ?: $s->precoVenda;
                                        $subtotal = $preco * ($s->quantidade ?: 1);
                                        $totalServico = $totalServico + $subtotal;
                                        echo '<tr>';
                                        echo '  <td>' . $s->nome . '</td>';
                                        echo '  <td class="text-center">' . ($s->quantidade ?: 1) . '</td>';
                                        echo '  <td class="text-center">' . number_format($preco, 2, ',', '.') . '</td>';
                                        echo '  <td class="text-end">R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end"><b>TOTAL SERVIÇOS:</b></td>
                                    <td class="text-end"><b>R$ <?= number_format($totalServico, 2, ',', '.') ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($totalProdutos != 0 || $totalServico != 0) { ?>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <!-- Coluna 1: QR Code -->
                        <div style="width: 30%; text-align: center;">
                            <?php if (!empty($qrCode)) { ?>
                                <img src="<?php echo $qrCode; ?>" alt="QR Code Pix" style="max-width: 150px;">
                            <?php } ?>
                        </div>

                        <!-- Coluna 2: Informações do Pix -->
                        <div style="width: 30%; text-align: center; margin-top: 15px;">
                            <h5 style="text-align: center; margin-bottom: 5px;">Pagamento via Pix</h5>
                            <?php if (!empty($chaveFormatada)) { ?>
                                <p style="font-size: 13px; margin: 0;">Chave Pix: <?php echo $chaveFormatada; ?></p>
                            <?php } ?>
                        </div>

                        <!-- Coluna 3: Valores -->
                        <div style="width: 30%; margin-top: 15px;">
                            <div class="row">
                                <div class="col-xs-12">
                                    <p style="text-align: right; margin: 3px 0; font-size: 12px;">Total: R$ <?php echo number_format($totalProdutos + $totalServico, 2, ',', '.'); ?></p>
                                </div>
                            </div>

                            <?php if ($result->valor_desconto != 0) { ?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <p style="text-align: right; margin: 3px 0; font-size: 12px;">Com Desconto: R$ <?php echo number_format($result->valor_desconto, 2, ',', '.'); ?></p>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($result->total_aver > 0) { ?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <p style="text-align: right; margin: 3px 0; font-size: 12px;">AVER: R$ <?php echo number_format($result->total_aver, 2, ',', '.'); ?></p>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row">
                                <div class="col-xs-12">
                                    <p style="text-align: right; margin: 3px 0; font-size: 14px; font-weight: bold;">Valor Final: R$ <?php 
                                        $total = $totalProdutos + $totalServico;
                                        $valorFinal = $total;
                                        if ($result->valor_desconto != 0) {
                                            $valorFinal = $result->valor_desconto;
                                        }
                                        if ($result->total_aver > 0) {
                                            $valorFinal -= $result->total_aver;
                                        }
                                        echo number_format($valorFinal, 2, ',', '.');
                                    ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </section>
            <footer>
                <div class="detalhes">
                    <span>Data inicial: <b><?= date('d/m/Y', strtotime($result->dataInicial)) ?></b></span>
                    <span>ORDEM DE SERVIÇO <b>#<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?></b></span>
                    <span>Data final: <b><?= $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : '' ?></b></span>
                </div>
                <div class="assinaturas">
                    <span>Assinatura do cliente</span>
                    <span>Assinatura do técnico</span>
                </div>
            </footer>
        </div>

        <?php if ($configuration['control_2vias']) : ?>
            <div class="sub-page novaPagina">
                <header>
                    <?php if ($emitente == null) : ?>
                        <div class="alert alert-danger" role="alert">
                            Você precisa configurar os dados do emitente. >>> <a href="<?=base_url()?>index.php/mapos/emitente">Configurar</a>
                        </div>
                    <?php else : ?>
                        <div class="imgLogo" class="align-middle">
                            <img src="<?= $emitente->url_logo ?>" class="img-fluid" style="width:140px;">
                        </div>
                        <div class="emitente">
                            <span style="font-size: 16px;"><b><?= $emitente->nome ?></b></span></br>
                            <?php if($emitente->cnpj != "00.000.000/0000-00") : ?>
                                <span class="align-middle">CNPJ: <?= $emitente->cnpj ?></span></br>
                            <?php endif; ?>
                            <span class="align-middle">
                                <?= $emitente->rua.', '.$emitente->numero.', '.$emitente->bairro ?><br>
                                <?= $emitente->cidade.' - '.$emitente->uf.' - '.$emitente->cep ?>
                            </span>
                        </div>
                        <div class="contatoEmitente">
                            <span style="font-weight: bold;">Tel: <?= $emitente->telefone ?></span></br>
                            <span style="font-weight: bold;"><?= $emitente->email ?></span></br>
                            <span style="word-break: break-word;">Responsável: <b><?= $result->nome ?></b></span>
                        </div>
                    <?php endif; ?>
                </header>
                <section>
                    <div class="title">
                        <!-- VIA EMPRESA  -->
                        <?php $totalServico = 0; $totalProdutos = 0; ?>
                        <?php if ($configuration['control_2vias']) : ?><span class="via">Via Empresa</span><?php endif; ?>
                        ORDEM DE SERVIÇO #<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?>
                        <span class="emissao">Emissão: <?= date('d/m/Y') ?></span>
                    </div>

                    <?php if ($result->dataInicial != null): ?>
                        <div class="tabela">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center">DATA INICIAL</th>
                                        <th class="text-center">DATA FINAL</th>
                                        <?php if ($result->garantia) : ?>
                                            <th class="text-center">GARANTIA</th>
                                        <?php endif; ?>
                                        <?php if (in_array($result->status, ['Finalizado', 'Faturado'])) : ?>
                                            <th class="text-center">VENC. GARANTIA</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center"><?= $result->status ?></td>
                                        <td class="text-center"><?= date('d/m/Y', strtotime($result->dataInicial)) ?></td>
                                        <td class="text-center"><?= $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : '' ?></td>
                                        <?php if ($result->garantia) : ?>
                                            <td class="text-center"><?= $result->garantia . ' dia(s)' ?></td>
                                        <?php endif; ?>
                                        <?php if (in_array($result->status, ['Finalizado', 'Faturado'])) : ?>
                                            <td class="text-center"><?= dateInterval($result->dataFinal, $result->garantia) ?></td>
                                        <?php endif; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <div class="subtitle">DADOS DO CLIENTE</div>
                    <div class="dados">
                        <div>
                            <span><b><?= $result->nomeCliente ?></b></span><br />
                            <span>CPF/CNPJ: <?= $result->documento ?></span><br />
                            <span><?= $result->contato_cliente.' '.$result->telefone ?><?= $result->telefone && $result->celular ? ' / '.$result->celular : $result->celular ?></span><br />
                            <span><?= $result->email ?></span><br />
                        </div>
                        <div style="text-align: right;">
                            <span><?= $result->rua.', '.$result->numero.', '.$result->bairro ?></span><br />
                            <span><?= $result->complemento.' - '.$result->cidade.' - '.$result->estado ?></span><br />
                            <span>CEP: <?= $result->cep ?></span><br />
                        </div>
                    </div>

                    <?php if ($result->descricaoProduto) : ?>
                        <div class="subtitle">DESCRIÇÃO</div>
                        <div class="dados">
                            <div>
                                <?= htmlspecialchars_decode($result->descricaoProduto) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($result->defeito) : ?>
                        <div class="subtitle">DEFEITO APRESENTADO</div>
                        <div class="dados">
                            <div>
                                <?= htmlspecialchars_decode($result->defeito) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($result->observacoes) : ?>
                        <div class="subtitle">OBSERVAÇÕES</div>
                        <div class="dados">
                            <div>
                                <?= htmlspecialchars_decode($result->observacoes) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($result->laudoTecnico) : ?>
                        <div class="subtitle">PARECER TÉCNICO</div>
                        <div class="dados">
                            <div>
                                <?= htmlspecialchars_decode($result->laudoTecnico) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($produtos) : ?>
                        <div class="tabela">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-secondary">
                                        <th>PRODUTO(S)</th>
                                        <th class="text-center" width="10%">QTD</th>
                                        <th class="text-center" width="10%">UNT</th>
                                        <th class="text-end" width="15%" >SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $p) :
                                        $totalProdutos = $totalProdutos + $p->subTotal;
                                        echo '<tr>';
                                        echo '  <td>' . $p->descricao . '</td>';
                                        echo '  <td class="text-center">' . $p->quantidade . '</td>';
                                        echo '  <td class="text-center">' . number_format($p->preco ?: $p->precoVenda, 2, ',', '.') . '</td>';
                                        echo '  <td class="text-end">R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    endforeach; ?>
                                    <tr>
                                        <td colspan="3" class="text-end"><b>TOTAL PRODUTOS:</b></td>
                                        <td class="text-end"><b>R$ <?= number_format($totalProdutos, 2, ',', '.') ?></b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($servicos) : ?>
                        <div class="tabela">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-secondary">
                                        <th>SERVIÇO(S)</th>
                                        <th class="text-center" width="10%">QTD</th>
                                        <th class="text-center" width="10%">UNT</th>
                                        <th class="text-end" width="15%" >SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        setlocale(LC_MONETARY, 'en_US'); 
                                        foreach ($servicos as $s) :
                                            $preco = $s->preco ?: $s->precoVenda;
                                            $subtotal = $preco * ($s->quantidade ?: 1);
                                            $totalServico = $totalServico + $subtotal;
                                            echo '<tr>';
                                            echo '  <td>' . $s->nome . '</td>';
                                            echo '  <td class="text-center">' . ($s->quantidade ?: 1) . '</td>';
                                            echo '  <td class="text-center">' . number_format($preco, 2, ',', '.') . '</td>';
                                            echo '  <td class="text-end">R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                                            echo '</tr>';
                                        endforeach; ?>
                                    <tr>
                                        <td colspan="3" class="text-end"><b>TOTAL SERVIÇOS:</b></td>
                                        <td class="text-end"><b>R$ <?= number_format($totalServico, 2, ',', '.') ?></b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if ($totalProdutos != 0 || $totalServico != 0) : ?>
                        <div class="pagamento" style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div class="span4" style="width: 30%;">
                                <?php if ($this->data['configuration']['pix_key']) : ?>
                                    <div style="text-align: center;">
                                        <img width="130px" src="<?= $qrCode ?>" alt="QR Code de Pagamento" />
                                        <div style="margin-top: 10px;">
                                            <div style="text-align: center;">
                                                <i class="fas fa-camera"></i><br />
                                                Escaneie o QRCode para pagar por Pix
                                            </div>
                                            <div class="chavePix" style="text-align: center; margin-top: 5px;">
                                                Pix: <b><?= $chaveFormatada ?></b>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="span8" style="width: 65%;">
                                <div class="tabela">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="table-secondary">
                                                <th colspan="2">RESUMO DOS VALORES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="65%">Valor Total</td>
                                                <td>R$ <?= number_format($totalProdutos + $totalServico, 2, ',', '.') ?></td>
                                            </tr>
                                            <?php if ($result->valor_desconto != 0) : ?>
                                                <tr>
                                                    <td>Desconto</td>
                                                    <td>R$ <?= number_format($result->valor_desconto, 2, ',', '.') ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if ($result->total_aver > 0) : ?>
                                                <tr>
                                                    <td>AVER</td>
                                                    <td>R$ <?= number_format($result->total_aver, 2, ',', '.') ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td><b>Valor a Pagar</b></td>
                                                <td><b>R$ <?= number_format(($totalProdutos + $totalServico) - $result->valor_desconto - $result->total_aver, 2, ',', '.') ?></b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
                <footer>
                    <div class="detalhes">
                        <span>Data inicial: <b><?= date('d/m/Y', strtotime($result->dataInicial)) ?></b></span>
                        <span>ORDEM DE SERVIÇO <b>#<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?></b></span>
                        <span>Data final: <b><?= $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : '' ?></b></span>
                    </div>
                    <div class="assinaturas">
                        <span>Assinatura do cliente</span>
                        <span>Assinatura do técnico</span>
                    </div>
                </footer>
            </div>
        <?php endif; ?>

        <?php if ($anexos && $imprimirAnexo) : ?>
            <div class="sub-page" id="anexos">
                <header style="border: 1px solid #cdcdcd">
                    <?php if ($emitente == null) : ?>
                        <div class="alert alert-danger" role="alert">
                            Você precisa configurar os dados do emitente. >>> <a href="<?= base_url() ?>index.php/mapos/emitente">Configurar</a>
                        </div>
                    <?php else : ?>
                        <div id="imgLogo" class="align-middle">
                            <img src="<?= $emitente->url_logo ?>" class="img-fluid" style="width:140px;">
                        </div>
                        <div style="padding-left: 10px; padding-right: 10px; margin-top: 3px;">
                            <span style="font-size: 16px;"><b><?= $emitente->nome ?></b></span></br>
                            <?php if ($emitente->cnpj != "00.000.000/0000-00") : ?>
                                <span class="align-middle">CNPJ: <?= $emitente->cnpj ?></span></br>
                            <?php endif; ?>
                            <span class="align-middle">
                                <?= $emitente->rua.', '.$emitente->numero.', '.$emitente->bairro ?><br>
                                <?= $emitente->cidade.' - '.$emitente->uf.' - '.$emitente->cep ?>
                            </span>
                        </div>
                        <div style="text-align: right; max-width: 230px; margin-top: 10px;">
                            <span style="font-weight: bold;">Tel: <?= $emitente->telefone ?></span></br>
                            <span style="font-weight: bold;"><?= $emitente->email ?></span></br>
                            <span style="word-break: break-word;">Responsável: <b><?= $result->nome ?></b></span>
                        </div>
                    <?php endif; ?>
                </header>
                <section>
                    <div class="title">
                        ORDEM DE SERVIÇO #<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?>
                        <span class="emissao">Emissão: <?= date('d/m/Y') ?></span>
                    </div>
                    <div class="subtitle">ANEXO(S)</div>
                    <div class="dados">
                        <div style="width: 100%; display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap;">
                            <?php
                                $contaAnexos = 0;
                                foreach ($anexos as $a) :
                                    if ($a->thumb) :
                                        $thumb = $a->url.'/thumbs/'.$a->thumb;
                                        $link  = $a->url.'/'.$a->anexo;
                            ?>
                                        <img src="<?= $link ?>" alt="">
                            <?php
                                    endif;
                                endforeach;
                            ?>
                        </div>
                    </div>
                <section>
            </div>
        <?php endif; ?>
    </div>
    <script type="text/javascript">
        window.print();
    </script>
</body>
</html>















