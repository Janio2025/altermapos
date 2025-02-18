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
                    LAUDO TÉCNICO - OS #<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?>
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

               
                <?php if ($result->laudoTecnico) : ?>
					<div class="subtitle">LAUDO</div>
							<div class="dados">
									<div style="text-align: justify;">
						<?= htmlspecialchars_decode($result->laudoTecnico) ?>
						</div>
						</div>
					<?php endif; ?>

                
            </section>
            <footer>
                <div class="detalhes">
                    <span>Data inicial: <b><?= date('d/m/Y', strtotime($result->dataInicial)) ?></b></span>
                    <span>LAUDO ORDEM DE SERVIÇO <b>#<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?></b></span>
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
                        LAUDO ORDEM DE SERVIÇO #<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?>
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

               
                <?php if ($result->laudoTecnico) : ?>
					<div class="subtitle">LAUDO</div>
							<div class="dados">
									<div style="text-align: justify;">
						<?= htmlspecialchars_decode($result->laudoTecnico) ?>
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
