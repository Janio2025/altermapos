<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />

<script>
    $(document).ready(function() {
        // Dynamic compartimentos loading
        $('#organizador_id').change(function() {
            var organizadorId = $(this).val();
            var compartimentoAtual = '<?php echo $result->compartimento_id; ?>'; // Pega o ID do compartimento atual
            
            if (organizadorId) {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/os/buscarCompartimentos',
                    type: 'POST',
                    data: {organizador_id: organizadorId},
                    dataType: 'json',
                    success: function(data) {
                        $('#compartimento_id').empty();
                        $('#compartimento_id').append('<option value="">Selecione um compartimento</option>');
                        $.each(data, function(key, value) {
                            var option = $('<option></option>')
                                .val(value.id)
                                .text(value.nome_compartimento);
                            
                            // Se o compartimento estiver ocupado, adiciona a quantidade
                            if (value.quantidade > 0) {
                                option.text(value.nome_compartimento + ' (' + value.quantidade + ' OS)');
                                option.addClass('compartimento-ocupado');
                            }
                            
                            // Se for o compartimento atual, marca como selecionado
                            if (value.id == compartimentoAtual) {
                                option.prop('selected', true);
                            }
                            
                            $('#compartimento_id').append(option);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao buscar compartimentos:', error);
                        $('#compartimento_id').empty();
                        $('#compartimento_id').append('<option value="">Erro ao carregar compartimentos</option>');
                    }
                });
            } else {
                $('#compartimento_id').empty();
                $('#compartimento_id').append('<option value="">Selecione primeiro um organizador</option>');
            }
        });

        // Carregar compartimentos ocupados ao iniciar a página
        var organizadorId = $('#organizador_id').val();
        if (organizadorId) {
            $('#organizador_id').trigger('change');
        }
    });
</script>

<style>
    /* Estilos para compartimentos ocupados */
    .compartimento-ocupado {
        color: #ff0000;
        font-weight: bold;
    }
    
    /* Estilo para o select2 quando o compartimento está ocupado */
    .select2-results__option.compartimento-ocupado {
        color: #ff0000;
        font-weight: bold;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Editar Ordem de Serviço</h5>
                <div class="buttons">
                    <?php if ($result->faturado == 0) { ?>
                        <a href="#modal-faturar" id="btn-faturar" role="button" data-toggle="modal"
                            class="button btn btn-mini btn-danger">
                            <span class="button__icon"><i class='bx bx-dollar'></i></span> <span
                                class="button__text">Faturar</span></a>
                        <?php
                    } ?>
                    <a title="Visualizar OS" class="button btn btn-primary"
                        href="<?php echo site_url() ?>/os/visualizar/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-show"></i></span><span
                            class="button__text">Visualizar OS</span></a>
                    <a target="_blank" title="Imprimir OS Papel A4" class="button btn btn-mini btn-inverse"
                        href="<?php echo site_url() ?>/os/imprimir/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">Papel
                            A4</span></a>
                    <a target="_blank" title="Imprimir OS Cupom Não Fiscal" class="button btn btn-mini btn-inverse"
                        href="<?php echo site_url() ?>/os/imprimirTermica/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">CP
                            Não Fiscal</span></a>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
                        $this->load->model('os_model');
                        $zapnumber = preg_replace("/[^0-9]/", "", $result->celular_cliente);
                        $troca = [$result->nomeCliente, $result->idOs, $result->status, 'R$ ' . ($result->desconto != 0 && $result->valor_desconto != 0 ? number_format($result->valor_desconto, 2, ',', '.') : number_format($totalProdutos + $totalServico, 2, ',', '.')), strip_tags($result->descricaoProduto), ($emitente ? $emitente->nome : ''), ($emitente ? $emitente->telefone : ''), strip_tags($result->observacoes), strip_tags($result->defeito), strip_tags($result->laudoTecnico), date('d/m/Y', strtotime($result->dataFinal)), date('d/m/Y', strtotime($result->dataInicial)), $result->garantia . ' dias'];
                        $texto_de_notificacao = $this->os_model->criarTextoWhats($texto_de_notificacao, $troca);
                        if (!empty($zapnumber)) {
                            echo '<a title="Via WhatsApp" class="button btn btn-mini btn-success" id="enviarWhatsApp" target="_blank" href="https://wa.me/send?phone=55' . $zapnumber . '&text=' . $texto_de_notificacao . '" ' . ($zapnumber == '' ? 'disabled' : '') . '>
                            <span class="button__icon"><i class="bx bxl-whatsapp"></i></span> <span class="button__text">WhatsApp</span></a>';
                        }
                    } ?>

                    <?php if (strtolower($result->status) != "cancelado") { ?>
                        <a title="Registrar Aver" class="button btn btn-mini btn-info" href="#modal-aver" role="button" data-toggle="modal">
                            <span class="button__icon"><i class="bx bx-money-withdraw"></i></span> <span class="button__text">Registrar Aver</span>
                        </a>
                    <?php } ?>

                    <a title="Enviar por E-mail" class="button btn btn-mini btn-warning"
                        href="<?php echo site_url() ?>/os/enviar_email/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-envelope"></i></span> <span class="button__text">Via
                            E-mail</span></a>
                    <?php if ($result->garantias_id) { ?> <a target="_blank" title="Imprimir Garantia"
                            class="button btn btn-mini btn-inverse"
                            href="<?php echo site_url() ?>/garantias/imprimirGarantiaOs/<?php echo $result->garantias_id; ?>">
                            <span class="button__icon"><i class="bx bx-printer"></i></span> <span
                                class="button__text">Garantia</span></a> <?php } ?>
                </div>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes da OS</a></li>
                        <li id="tabDesconto"><a href="#tab2" data-toggle="tab">Desconto</a></li>
                        <li id="tabProdutos"><a href="#tab3" data-toggle="tab">Produtos</a></li>
                        <li id="tabServicos"><a href="#tab4" data-toggle="tab">Serviços</a></li>
                        <li id="tabAnexos"><a href="#tab5" data-toggle="tab">Anexos</a></li>
                        <li id="tabAnotacoes"><a href="#tab6" data-toggle="tab">Anotações</a></li>
                        <li id="tabAvers"><a href="#tab7" data-toggle="tab">Total de Avers:<strong style="color: blue"> R$ <?php 
                    $totalAvers = 0;
                    if (!empty($avers)) {
                        foreach ($avers as $a) {
                            $totalAvers += $a->valor;
                        }
                    }
                    echo number_format($totalAvers, 2, ',', '.');
                ?></strong></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divCadastrarOs">
                                <form action="<?php echo current_url(); ?>" method="post" id="formOs">
                                    <?php echo form_hidden('idOs', $result->idOs) ?>
                                    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                    
                                    <!-- Campos hidden para usuários adicionais existentes -->
                                    <?php if (isset($usuarios_adicionais)): ?>
                                        <?php foreach ($usuarios_adicionais as $usuario): ?>
                                            <input type="hidden" name="usuarios_adicionais[]" value="<?php echo $usuario->usuario_id; ?>">
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <h3>N° OS:
                                            <?php echo $result->idOs; ?>
                                        </h3>
                                        <div class="span5" style="margin-left: 0">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente"
                                                value="<?php echo $result->nomeCliente ?>" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id"
                                                value="<?php echo $result->clientes_id ?>" />
                                            <input id="valor" type="hidden" name="valor" value="" />
                                        </div>
                                        <div class="span4">
                                            <label for="tecnico">Técnico / Responsável<span
                                                    class="required">*</span></label>
                                            <div class="">
                                                <input id="tecnico" class="span8" type="text" name="tecnico"
                                                    value="<?php echo $result->nome ?>" />
                                                <div class="span4">
                                                    <button type="button" class="btn span12" style="margin-left: 0" data-toggle="modal" data-target="#modalUsuarios">
                                                        <i class="bx bx-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <input id="usuarios_id" class="span12" type="hidden" name="usuarios_id"
                                                value="<?php echo $result->usuarios_id ?>" />
                                        </div>

                                        <div class="span3">
                                            <label for="status">Status<span class="required">*</span></label>
                                            <select class="span12" name="status" id="status" value="">
                                                <option <?php if ($result->status == 'Orçamento') {
                                                    echo 'selected';
                                                } ?> value="Orçamento">Orçamento
                                                </option>
                                                <option <?php if ($result->status == 'Aberto') {
                                                    echo 'selected';
                                                } ?>   value="Aberto">Aberto
                                                </option>
                                                <option <?php if ($result->status == 'Faturado') {
                                                    echo 'selected';
                                                } ?> value="Faturado" <?php echo ($result->faturado == 0) ? 'disabled' : ''; ?>>Faturado
                                                </option>
                                                <option <?php if ($result->status == 'Negociação') {
                                                    echo 'selected';
                                                } ?> value="Negociação">Negociação
                                                </option>
                                                <option <?php if ($result->status == 'Em Andamento') {
                                                    echo 'selected';
                                                } ?> value="Em Andamento">Em Andamento
                                                </option>
                                                <option <?php if ($result->status == 'Finalizado') {
                                                    echo 'selected';
                                                } ?> value="Finalizado">Finalizado
                                                </option>
                                                <option <?php if ($result->status == 'Cancelado') {
                                                    echo 'selected';
                                                } ?>   value="Cancelado">Cancelado
                                                </option>
                                                <option <?php if ($result->status == 'Aguardando Peças') {
                                                    echo 'selected';
                                                } ?> value="Aguardando Peças">Aguardando Peças
                                                </option>
                                                <option <?php if ($result->status == 'Aprovado') {
                                                    echo 'selected';
                                                } ?>   value="Aprovado">Aprovado
                                                </option>
                                            </select>
                                        </div>


                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">

                                        <div class="span3">
                                            <label for="dataInicial">Data Inicial<span class="required">*</span></label>
                                            <input id="dataInicial" autocomplete="off" class="span12 datepicker"
                                                type="text" name="dataInicial"
                                                value="<?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>" />
                                        </div>
                                        <div class="span3">
                                            <label for="dataFinal">Data Final<span class="required">*</span></label>
                                            <input id="dataFinal" autocomplete="off" class="span12 datepicker"
                                                type="text" name="dataFinal"
                                                value="<?php echo date('d/m/Y', strtotime($result->dataFinal)); ?>" />
                                        </div>
                                        <div class="span3">
                                            <label for="garantia">Garantia (dias)</label>
                                            <input id="garantia" type="number" placeholder="Status s/g inserir nº/0"
                                                min="0" max="9999" class="span12" name="garantia"
                                                value="<?php echo $result->garantia ?>" />
                                            <?php echo form_error('garantia'); ?>
                                        </div>

                                        <div class="span3">
                                            <label for="termoGarantia">Termo Garantia</label>
                                            <input id="termoGarantia" class="span12" type="text" name="termoGarantia"
                                                value="<?php echo $result->refGarantia ?>" />
                                            <input id="garantias_id" class="span12" type="hidden" name="garantias_id"
                                                value="<?php echo $result->garantias_id ?>" />

                                        </div>

                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">

                                        <div class="span3">
                                            <label for="descricaoProduto">PRODUTO</label>
                                            <input name="descricaoProduto" class="span12" type="text"
                                                id="descricaoProduto" value="<?php echo $result->descricaoProduto ?>"
                                                onChange="javascript:this.value=this.value.toUpperCase();" />

                                        </div>

                                        <div class="span3">
                                            <label for="marcaProdutoOs">MARCA </label>
                                            <input name="marcaProdutoOs" class="span12" type="text" id="marcaProdutoOs"
                                                value="<?php echo $result->marcaProdutoOs ?>"
                                                onChange="javascript:this.value=this.value.toUpperCase();" />

                                        </div>

                                        <div class="span3">
                                            <label for="modeloProdutoOs">MODELO</label>
                                            <input name="modeloProdutoOs" class="span12" type="text"
                                                id="modeloProdutoOs" value="<?php echo $result->modeloProdutoOs ?>"
                                                onChange="javascript:this.value=this.value.toUpperCase();" />

                                        </div>

                                        <div class="span3">
                                            <label for="nsProdutoOs">NÚMERO DE SÉRIE</label>
                                            <input name="nsProdutoOs" class="span12" type="text" id="nsProdutoOs"
                                                value="<?php echo $result->nsProdutoOs ?>"
                                                onChange="javascript:this.value=this.value.toUpperCase();" />

                                        </div>

                                    </div>

                                    <div class="span12" style="padding: 1%; margin-left: 0">

                                        

                                        
                                        <div class="span3">
                                            <label for="organizador_id">Organizador</label>
                                            <select id="organizador_id" name="organizador_id" class="span12 select2">
                                                <option value="">Buscar organizador...</option>
                                                <?php foreach ($organizadores as $organizador) : ?>
                                                    <option value="<?php echo $organizador->id; ?>" <?php echo ($result->organizador_id == $organizador->id) ? 'selected' : ''; ?>>
                                                        <?php echo $organizador->nome_organizador; ?>
                                                        <label for="">-</label> 
                                                        <?php echo $organizador->localizacao; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="span3">
                                            <label for="compartimento_id">Compartimento</label>
                                            <select id="compartimento_id" name="compartimento_id" class="span12">
                                                <option value="">Selecione primeiro um organizador</option>
                                                <?php if ($result->organizador_id && $result->compartimento_id): ?>
                                                    <?php 
                                                    $compartimentos = $this->db->where('organizador_id', $result->organizador_id)->where('ativa', true)->get('compartimentos')->result();
                                                    foreach ($compartimentos as $compartimento): 
                                                    ?>
                                                        <option value="<?php echo $compartimento->id; ?>" <?php echo ($result->compartimento_id == $compartimento->id) ? 'selected' : ''; ?>>
                                                            <?php echo $compartimento->nome_compartimento; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="span3">
                                            <label for="ucProdutoOs">UNIDADE CONSUMIDORA</label>
                                            <input name="ucProdutoOs" class="span12" type="text" id="ucProdutoOs"
                                                value="<?php echo $result->ucProdutoOs ?>"
                                                oninput="limparOutroCampo(this, 'contrato_seguradora')" 
                                                onchange="this.value = this.value.toUpperCase();" />
                                        </div>

                                        <div class="span3">
                                            <label for="contrato_seguradora">NÚMERO DO SEGURO</label>
                                            <input name="contrato_seguradora" class="span12" type="text" id="contrato_seguradora"
                                                value="<?php echo $result->contrato_seguradora ?>"
                                                oninput="limparOutroCampo(this, 'ucProdutoOs')" 
                                                onchange="this.value = this.value.toUpperCase();" />
                                        </div>

                                       

                                    </div>

                                    <div>


                                    </div>

                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                    <div class="span6">
                                            <label for="defeito">Defeito descrito pelo cliente</label>
                                            <input name="defeito" class="span12" type="text" id="defeito"
                                                value="<?php echo $result->defeito ?>" />
                                        </div>

                                        <div class="span6">
                                            <label for="analiseBasica">Defeito constatado em pré-análise</label>
                                            <input name="analiseBasica" class="span12" type="text" id="analiseBasica"
                                                value="<?php echo $result->analiseBasica ?>" />
                                        </div>
                                    </div>

                                    
                                    <div class="span12" style="padding: 1%; margin-left: 0">

                                        <div class="span6">
                                            <label for="laudoTecnico">

                                                <strong>LAUDO TÉCNICO</strong>
                                            </label>
                                            <textarea class="span12 editor" name="laudoTecnico" id="laudoTecnico"
                                                cols="30" rows="5"><?php echo $result->laudoTecnico ?></textarea>
                                        </div>

                                        <div class="span6">
                                            <label for="observacoes">

                                                <strong>OBSERVAÇÕES</strong>
                                            </label>
                                            <textarea class="span12 editor" name="observacoes" id="observacoes"
                                                cols="30" rows="5"><?php echo $result->observacoes ?></textarea>
                                        </div>
                                    </div>

                                    <div class="span12" style="padding: 0; margin-left: 0">
                                        <div class="span6 offset3" style="display:flex;justify-content: center">
                                            <button class="button btn btn-primary" id="btnContinuar"><span
                                                    class="button__icon"><i class="bx bx-sync"></i></span><span
                                                    class="button__text2">Atualizar</span></button>
                                            <a href="<?php echo base_url() ?>index.php/os"
                                                class="button btn btn-mini btn-warning"><span class="button__icon"><i
                                                        class="bx bx-undo"></i></span> <span
                                                    class="button__text2">Voltar</span></a>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <!--Desconto-->
                        <?php
                        $total = 0;
                        foreach ($produtos as $p) {
                            $total = $total + $p->subTotal;
                        }
                        ?>
                        <?php
                        $totals = 0;
                        foreach ($servicos as $s) {
                            $preco = $s->preco ?: $s->precoVenda;
                            $subtotals = $preco * ($s->quantidade ?: 1);
                            $totals = $totals + $subtotals;
                        }
                        ?>

                        <div class="tab-pane" id="tab2">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <form id="formDesconto" action="<?php echo base_url(); ?>index.php/os/adicionarDesconto"
                                    method="POST">
                                    <div id="divValorTotal">
                                        <div class="span2">
                                            <label for="">Valor Total Da OS:</label>
                                            <input class="span12 money" id="valorTotal" name="valorTotal" type="text"
                                                data-affixes-stay="true" data-thousands="" data-decimal="." name="valor"
                                                value="<?php echo number_format($totals + $total, 2, '.', ''); ?>"
                                                readonly />
                                        </div>
                                    </div>
                                    <div class="span1">
                                        <label for="">Tipo Desc.</label>
                                        <select style="width: 4em;" name="tipoDesconto" id="tipoDesconto">
                                            <option value="real">R$</option>
                                            <option value="porcento" <?= $result->tipo_desconto == "porcento" ? "selected" : "" ?>>%</option>
                                        </select>
                                        <strong><span style="color: red" id="errorAlert"></span></strong>
                                    </div>
                                    <div class="span3">
                                        <input type="hidden" name="idOs" id="idOs"
                                            value="<?php echo $result->idOs; ?>" />
                                        <label for="">Desconto</label>
                                        <input style="width: 4em;" id="desconto" name="desconto" type="text"
                                            placeholder="" maxlength="6" size="2" value="<?= $result->desconto ?>" />
                                        <strong><span style="color: red" id="errorAlert"></span></strong>
                                    </div>
                                    <div class="span2">
                                        <label for="">Total com Desconto</label>
                                        <input class="span12 money" id="resultado" type="text" data-affixes-stay="true"
                                            data-thousands="" data-decimal="." name="resultado"
                                            value="<?php echo $result->valor_desconto ?>" readonly />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button class="button btn btn-success" id="btnAdicionarDesconto">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span
                                                class="button__text2">Aplicar</span></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!--Produtos-->
                        <div class="tab-pane" id="tab3">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <form id="formProdutos" action="<?php echo base_url() ?>index.php/os/adicionarProduto"
                                    method="post">
                                    <div class="span6">
                                        <input type="hidden" name="idProduto" id="idProduto" />
                                        <input type="hidden" name="idOsProduto" id="idOsProduto"
                                            value="<?php echo $result->idOs; ?>" />
                                        <input type="hidden" name="estoque" id="estoque" value="" />
                                        <label for="">Produto</label>
                                        <input type="text" class="span12" name="produto" id="produto"
                                            placeholder="Digite o nome do produto" />
                                    </div>
                                    <div class="span2">
                                        <label for="">Preço</label>
                                        <input type="text" placeholder="Preço" id="preco" name="preco"
                                            class="span12 money" data-affixes-stay="true" data-thousands=""
                                            data-decimal="." />
                                    </div>
                                    <div class="span2">
                                        <label for="">Quantidade</label>
                                        <input type="text" placeholder="Quantidade" id="quantidade" name="quantidade"
                                            class="span12" />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button class="button btn btn-success" id="btnAdicionarProduto">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span
                                                class="button__text2">Adicionar</span></button>
                                    </div>
                                </form>
                            </div>
                            <div class="widget-box" id="divProdutos">
                                <div class="widget_content nopadding">
                                    <table width="100%" class="table table-bordered" id="tblProdutos">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th>Marca</th>
                                                <th>Modelo</th>
                                                <th width="8%">Quantidade</th>
                                                <th width="10%">Preço unit.</th>
                                                <th width="6%">Ações</th>
                                                <th width="10%">Sub-total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $total = 0;
                                            foreach ($produtos as $p) {
                                                $total = $total + $p->subTotal;
                                                echo '<tr>';
                                                echo '<td>' . $p->descricao . '</td>';
                                                echo '<td>' . $p->marcaProduto . '</td>';
                                                echo '<td>' . $p->nomeModelo . '</td>';
                                                echo '<td><div align="center">' . $p->quantidade . '</td>';
                                                echo '<td><div align="center">R$: ' . ($p->preco ?: $p->precoVenda) . '</td>';
                                                echo (strtolower($result->status) != "cancelado") ? '<td><div align="center"><a href="" idAcao="' . $p->idProdutos_os . '" prodAcao="' . $p->idProdutos . '" quantAcao="' . $p->quantidade . '" title="Excluir Produto" class="btn-nwe4"><i class="bx bx-trash-alt"></i></a></td>' : '<td></td>';
                                                echo '<td><div align="center">R$: ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                                echo '</tr>';
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6" style="text-align: right"><strong>Total:</strong></td>
                                                <td>
                                                    <div align="center"><strong>R$
                                                            <?php echo number_format($total, 2, ',', '.'); ?><input
                                                                type="hidden" id="total-venda"
                                                                value="<?php echo number_format($total, 2); ?>"></strong>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!--Serviços-->
                        <div class="tab-pane" id="tab4">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <form id="formServicos" action="<?php echo base_url() ?>index.php/os/adicionarServico"
                                    method="post">
                                    <div class="span6">
                                        <input type="hidden" name="idServico" id="idServico" />
                                        <input type="hidden" name="idOsServico" id="idOsServico"
                                            value="<?php echo $result->idOs; ?>" />
                                        <label for="">Serviço</label>
                                        <input type="text" class="span12" name="servico" id="servico"
                                            placeholder="Digite o nome do serviço" />
                                    </div>
                                    <div class="span2">
                                        <label for="">Preço</label>
                                        <input type="text" placeholder="Preço" id="preco_servico" name="preco"
                                            class="span12 money" data-affixes-stay="true" data-thousands=""
                                            data-decimal="." />
                                    </div>
                                    <div class="span2">
                                        <label for="">Quantidade</label>
                                        <input type="text" placeholder="Quantidade" id="quantidade_servico"
                                            name="quantidade" class="span12" />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button class="button btn btn-success">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span
                                                class="button__text2">Adicionar</span></button>
                                    </div>
                                </form>
                            </div>
                            <div class="widget-box" id="divServicos">
                                <div class="widget_content nopadding">
                                    <table width="100%" class="table table-bordered" id="tblServicos">
                                        <thead>
                                            <tr>
                                                <th>Serviço</th>
                                                <th>Descrição</th>
                                                <th width="8%">Quantidade</th>
                                                <th width="10%">Preço</th>
                                                <th width="6%">Ações</th>
                                                <th width="10%">Sub-totals</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totals = 0;
                                            foreach ($servicos as $s) {
                                                $preco = $s->preco ?: $s->precoVenda;
                                                $subtotals = $preco * ($s->quantidade ?: 1);
                                                $totals = $totals + $subtotals;
                                                echo '<tr>';
                                                echo '<td>' . $s->nome . '</td>';
                                                echo '<td>' . $s->descricao . '</td>';
                                                echo '<td><div align="center">' . ($s->quantidade ?: 1) . '</div></td>';
                                                echo '<td><div align="center">R$ ' . $preco . '</div></td>';
                                                echo '<td><div align="center"><span idAcao="' . $s->idServicos_os . '" title="Excluir Serviço" class="btn-nwe4 servico"><i class="bx bx-trash-alt"></i></span></div></td>';
                                                echo '<td><div align="center">R$: ' . number_format($subtotals, 2, ',', '.') . '</div></td>';
                                                echo '</tr>';
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" style="text-align: right"><strong>Total:</strong></td>
                                                <td>
                                                    <div align="center"><strong>R$
                                                            <?php echo number_format($totals, 2, ',', '.'); ?><input
                                                                type="hidden" id="total-servico"
                                                                value="<?php echo number_format($totals, 2); ?>"></strong>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!--Anexos-->
                        <div class="tab-pane" id="tab5">
                            <div class="span12" style="padding: 1%; margin-left: 0">
                                <div class="span12 well" style="padding: 1%; margin-left: 0" id="form-anexos">
                                    <form id="formAnexos" enctype="multipart/form-data" action="javascript:;"
                                        accept-charset="utf-8" s method="post">
                                        <div class="span10">
                                            <input type="hidden" name="idOsServico" id="idOsServico"
                                                value="<?php echo $result->idOs; ?>" />
                                            <label for="">Anexo</label>
                                            <input type="file" class="span12" name="userfile[]" multiple="multiple"
                                                size="20" />
                                        </div>
                                        <div class="span2">
                                            <label for="">.</label>
                                            <button class="button btn btn-success">
                                                <span class="button__icon"><i class='bx bx-paperclip'></i></span><span
                                                    class="button__text2">Anexar</span></button>
                                        </div>
                                    </form>
                                </div>
                                <div class="span12 pull-left" id="divAnexos" style="margin-left: 0">
                                    <div class="row-fluid">
                                        <?php
                                        $count = 0;
                                        foreach ($anexos as $a) {
                                            if ($a->thumb == null) {
                                                $thumb = base_url() . 'assets/img/icon-file.png';
                                                $link = base_url() . 'assets/img/icon-file.png';
                                            } else {
                                                $thumb = $a->url . '/thumbs/' . $a->thumb;
                                                $link = $a->url . '/' . $a->anexo;
                                            }
                                            if ($count % 3 == 0 && $count > 0) {
                                                echo '</div><div class="row-fluid">';
                                            }
                                            echo '<div class="span4" style="min-height: 180px; text-align: center; margin-bottom: 20px;">
                                                    <a style="min-height: 150px; display: block;" href="#modal-anexo" imagem="' . $a->idAnexos . '" link="' . $link . '" role="button" class="btn anexo span12" data-toggle="modal">
                                                        <img src="' . $thumb . '" alt="" style="max-width: 100%; max-height: 120px; margin-bottom: 8px;">
                                                    </a>
                                                    <div style="word-break: break-all; font-size: 13px; color: #333; margin-top: 5px;">' . $a->anexo . '</div>
                                                </div>';
                                            $count++;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Anotações-->
                        <div class="tab-pane" id="tab6">
                            <div class="span12" style="padding: 1%; margin-left: 0">

                                <div class="span12" id="divAnotacoes" style="margin-left: 0">

                                    <a href="#modal-anotacao" id="btn-anotacao" role="button" data-toggle="modal"
                                        class="button btn btn-success" style="max-width: 160px">
                                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span
                                            class="button__text2">Adicionar anotação</span></a>
                                    <hr>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Anotação</th>
                                                <th>Data/Hora</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($anotacoes as $a) {
                                                echo '<tr>';
                                                echo '<td>' . $a->anotacao . '</td>';
                                                echo '<td>' . date('d/m/Y H:i:s', strtotime($a->data_hora)) . '</td>';
                                                echo '<td><span idAcao="' . $a->idAnotacoes . '" title="Excluir Anotação" class="btn-nwe4 anotacao"><i class="bx bx-trash-alt"></i></span></td>';
                                                echo '</tr>';
                                            }
                                            if (!$anotacoes) {
                                                echo '<tr><td colspan="3">Nenhuma anotação cadastrada</td></tr>';
                                            }

                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <!-- Fim tab anotações -->

                        <!-- Avers -->
                        <div class="tab-pane" id="tab7">
                            <div class="span12" style="padding: 1%; margin-left: 0">
                                <div class="span12" id="divAvers" style="margin-left: 0">
                                    <?php 
                                    $this->load->model('os_model');
                                    $data['avers'] = $this->os_model->getAvers($result->idOs);
                                    $this->load->view('os/tabela_avers', $data);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <!-- Fim tab avers -->
                    </div>
                </div>
                &nbsp
            </div>
        </div>
    </div>
</div>

<!-- Modal visualizar anexo -->
<div id="modal-anexo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Visualizar Anexo</h3>
    </div>
    <div class="modal-body">
        <div class="span12" id="div-visualizar-anexo" style="text-align: center">
            <div class='progress progress-info progress-striped active'>
                <div class='bar' style='width: 100%'></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <a href="" id-imagem="" class="btn btn-inverse" id="download">Download</a>
        <a href="" link="" class="btn btn-danger" id="excluir-anexo">Excluir Anexo</a>
    </div>
</div>

<!-- Modal cadastro anotações -->
<div id="modal-anotacao" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <form action="#" method="POST" id="formAnotacao">
        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Adicionar Anotação</h3>
        </div>
        <div class="modal-body">
            <div class="span12" id="divFormAnotacoes" style="margin-left: 0"></div>
            <div class="span12" style="margin-left: 0">
                <label for="anotacao">Anotação</label>
                <textarea class="span12" name="anotacao" id="anotacao" cols="30" rows="3"></textarea>
                <input type="hidden" name="os_id" value="<?php echo $result->idOs; ?>">
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="btn-close-anotacao">Fechar</button>
            <button class="btn btn-primary">Adicionar</button>
        </div>
    </form>
</div>

<!-- Modal Faturar-->
<div id="modal-faturar" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <form id="formFaturar" action="<?php echo current_url() ?>" method="post">
        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Faturar OS</h3>
        </div>
        <div class="modal-body">
            <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com
                asterisco.</div>
            <div class="span12" style="margin-left: 0">
                <label for="descricao">Descrição</label>
                <input class="span12" id="descricao" type="text" name="descricao"
                    value="Fatura de OS Nº: <?php echo $result->idOs; ?> " />
            </div>
            <div class="span12" style="margin-left: 0">
                <div class="span12" style="margin-left: 0">
                    <label for="cliente">Cliente*</label>
                    <input class="span12" id="cliente" type="text" name="cliente"
                        value="<?php echo $result->nomeCliente ?>" />
                    <input type="hidden" name="clientes_id" id="clientes_id" value="<?php echo $result->clientes_id ?>">
                    <input type="hidden" name="os_id" id="os_id" value="<?php echo $result->idOs; ?>">
                    <input type="hidden" name="tipoDesconto" id="tipoDesconto"
                        value="<?php echo $result->tipo_desconto; ?>">
                </div>
            </div>
            <div class="span12" style="margin-left: 0">
                <div class="span6" style="margin-left: 0">
                    <label for="valor">Valor*</label>
                    <input type="hidden" id="tipo" name="tipo" value="receita" />
                    <input class="span12 money" id="valor" type="text" data-affixes-stay="true" data-thousands=""
                        data-decimal="." name="valor" readonly style="background-color: #f5f5f5; cursor: not-allowed;"
                        value="<?php echo number_format($totals + $total, 2, '.', ''); ?>" onkeydown="return false" onmousedown="return false" />
                </div>
                <div class="span6" style="margin-left: 2;">
                    <label for="valor">Valor Com Desconto*</label>
                    <input class="span12 money" id="faturar-desconto" type="text" name="faturar-desconto" readonly style="background-color: #f5f5f5; cursor: not-allowed;"
                        value="<?php echo number_format($result->valor_desconto, 2, '.', ''); ?> " onkeydown="return false" onmousedown="return false" />
                    <strong><span style="color: red" id="resultado"></span></strong>
                </div>
            </div>
            <div class="span12" style="margin-left: 0">
                <div class="span4" style="margin-left: 0">
                    <label for="vencimento">Data Entrada*</label>
                    <input class="span12 datepicker" autocomplete="off" id="vencimento" type="text" name="vencimento" />
                </div>
            </div>
            <div class="span12" style="margin-left: 0">
                <div class="span4" style="margin-left: 0">
                    <label for="recebido">Recebido?</label>
                    &nbsp &nbsp &nbsp &nbsp <input id="recebido" type="checkbox" name="recebido" value="1"/>
                </div>
                <div id="divRecebimento" class="span8" style=" display: none">
                    <div class="span6">
                        <label for="recebimento">Data Recebimento</label>
                        <input class="span12 datepicker" autocomplete="off" id="recebimento" type="text"
                            name="recebimento" />
                    </div>
                    <div class="span6">
                        <label for="formaPgto">Forma Pgto</label>
                        <select name="formaPgto" id="formaPgto" class="span12">
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Débito">Débito</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Depósito">Depósito</option>
                            <option value="Pix">Pix</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"
                id="btn-cancelar-faturar"><span class="button__icon"><i class="bx bx-x"></i></span><span
                    class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-dollar'></i></span> <span
                    class="button__text2">Faturar</span></button>
        </div>
    </form>
</div>

<!-- Modal de Usuários Adicionais -->
<div id="modalUsuarios" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalUsuariosLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modalUsuariosLabel">Adicionar Técnicos Responsáveis</h3>
    </div>
    <div class="modal-body">
        <div class="span12" style="margin-left: 0">
            <label for="usuarioAdicional">Selecionar Técnico</label>
            <input id="usuarioAdicional" class="span12" type="text" />
            <input id="usuarios_id_adicional" type="hidden" />
        </div>
        <div class="span12" style="margin-left: 0; margin-top: 10px">
            <table id="tabelaUsuarios" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($usuarios_adicionais)) {
                        foreach ($usuarios_adicionais as $usuario) {
                            echo '<tr id="usuario-' . $usuario->usuario_id . '">';
                            echo '<td>' . $usuario->nome . '</td>';
                            echo '<td class="text-right">';
                            echo '<button type="button" class="btn btn-info btn-fix-user" onclick="fixarUsuario(' . $usuario->usuario_id . ', \'' . $usuario->nome . '\')" title="Fixar usuário">';
                            echo '<i class="bx bx-pin"></i></button> ';
                            echo '<button type="button" class="btn btn-danger" onclick="removerUsuario(' . $usuario->usuario_id . ')">';
                            echo '<i class="bx bx-trash"></i></button></td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
    </div>
</div>

<style>
    /* Estilos para o autocomplete */
    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999 !important;
    }

    .ui-autocomplete .ui-menu-item {
        padding: 5px 10px;
        border-bottom: 1px solid #f4f4f4;
    }

    .ui-autocomplete .ui-menu-item:hover {
        background: #f4f4f4;
        cursor: pointer;
    }

    /* Estilos para o modal */
    #modalUsuarios .modal-body {
        max-height: 400px;
        overflow-y: auto;
    }

    #tabelaUsuarios {
        margin-top: 15px;
    }

    #tabelaUsuarios th, #tabelaUsuarios td {
        padding: 8px;
        vertical-align: middle;
    }

    .text-right {
        text-align: right;
    }

    /* Ajuste do z-index do modal para ficar acima do autocomplete */
    .modal {
        z-index: 9998 !important;
    }

    /* Estilos para o badge de usuários fixados */
    .badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #d9534f;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 11px;
    }

    /* Ajuste para o botão de adicionar com badge */
    [data-target="#modalUsuarios"] {
        position: relative;
    }

    /* Estilos para botões de ação na tabela */
    #tabelaUsuarios .btn {
        margin: 0 2px;
    }

    #tabelaUsuarios .btn-fix-user.btn-success {
        background-color: #28a745;
    }

    /* Ajuste para o botão de adicionar quando tem fixados */
    [data-target="#modalUsuarios"].btn-info {
        background-color: #17a2b8;
    }
</style>

<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>

<script type="text/javascript">
    function calcDesconto(valor, desconto, tipoDesconto) {
        var resultado = 0;
        if (tipoDesconto == 'real') {
            resultado = valor - desconto;
        }
        if (tipoDesconto == 'porcento') {
            resultado = (valor - desconto * valor / 100).toFixed(2);
        }
        return resultado;
    }

    function validarDesconto(resultado, valor) {
        if (resultado == valor) {
            return resultado = "";
        } else {
            return resultado.toFixed(2);
        }
    }
    var valorBackup = $("#valorTotal").val();

    $("#quantidade").keyup(function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    $("#quantidade_servico").keyup(function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
    $('#tipoDesconto').on('change', function () {
        if (Number($("#desconto").val()) >= 0) {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val()), $("#tipoDesconto").val()));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        }
    });
    $("#desconto").keyup(function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ($("#valorTotal").val() == null || $("#valorTotal").val() == '') {
            $('#errorAlert').text('Valor não pode ser apagado.').css("display", "inline").fadeOut(5000);
            $('#desconto').val('');
            $('#resultado').val('');
            $("#valorTotal").val(valorBackup);
            $("#desconto").focus();

        } else if (Number($("#desconto").val()) >= 0) {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val()), $("#tipoDesconto").val()));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        } else {
            $('#errorAlert').text('Erro desconhecido.').css("display", "inline").fadeOut(5000);
            $('#desconto').val('');
            $('#resultado').val('');
        }
    });

    $("#valorTotal").focusout(function () {
        $("#valorTotal").val(valorBackup);
        if ($("#valorTotal").val() == '0.00' && $('#resultado').val() != '') {
            $('#errorAlert').text('Você não pode apagar o valor.').css("display", "inline").fadeOut(6000);
            $('#resultado').val('');
            $("#valorTotal").val(valorBackup);
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val())));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
            $("#desconto").focus();
        } else {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val())));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        }
    });

    $('#resultado').focusout(function () {
        if (Number($('#resultado').val()) > Number($("#valorTotal").val())) {
            $('#errorAlert').text('Desconto não pode ser maior que o Valor.').css("display", "inline").fadeOut(6000);
            $('#resultado').val('');
        }
        if ($("#desconto").val() != "" || $("#desconto").val() != null) {
            $('#resultado').val(calcDesconto(Number($("#valorTotal").val()), Number($("#desconto").val())));
            $('#resultado').val(validarDesconto(Number($('#resultado').val()), Number($("#valorTotal").val())));
        }
    });
    $(document).ready(function () {

        $(".money").maskMoney();

        $('#recebido').click(function (event) {
            var flag = $(this).is(':checked');
            if (flag == true) {
                $('#divRecebimento').show();
            } else {
                $('#divRecebimento').hide();
            }
        });

        $("#formFaturar").validate({
            rules: {
                descricao: {
                    required: true
                },
                cliente: {
                    required: true
                },
                valor: {
                    required: true
                },
                vencimento: {
                    required: true
                }

            },
            messages: {
                descricao: {
                    required: 'Campo Requerido.'
                },
                cliente: {
                    required: 'Campo Requerido.'
                },
                valor: {
                    required: 'Campo Requerido.'
                },
                vencimento: {
                    required: 'Campo Requerido.'
                }
            },
            submitHandler: function (form) {
                var dados = $(form).serialize();
                var qtdProdutos = $('#tblProdutos >tbody >tr').length;
                var qtdServicos = $('#tblServicos >tbody >tr').length;
                var qtdTotalProdutosServicos = qtdProdutos + qtdServicos;

                $('#btn-cancelar-faturar').trigger('click');

                if (qtdTotalProdutosServicos <= 0) {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: "Não é possível faturar uma OS sem serviços e/ou produtos"
                    });
                } else if (qtdTotalProdutosServicos > 0) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>index.php/os/faturar",
                        data: dados,
                        dataType: 'json',
                        success: function (data) {
                            if (data.result == true) {
                                window.location.reload(true);
                            } else {
                                Swal.fire({
                                    type: "error",
                                    title: "Atenção",
                                    text: "Ocorreu um erro ao tentar faturar OS."
                                });
                                $('#progress-fatura').hide();
                            }
                        }
                    });

                    return false;
                }
            }
        });
        $('#formDesconto').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                beforeSend: function () {
                    Swal.fire({
                        title: 'Processando',
                        text: 'Registrando desconto...',
                        icon: 'info',
                        showCloseButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                },
                success: function (response) {
                    if (response.result) {
                        Swal.fire({
                            type: "success",
                            title: "Sucesso",
                            text: response.messages
                        });
                        setTimeout(function () {
                            window.location.href = window.BaseUrl + 'index.php/os/editar/' + <?php echo $result->idOs ?>;
                        }, 2000);
                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Atenção",
                            text: response.messages
                        });
                    }

                },
                error: function (response) {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: response.responseJSON.messages
                    });
                }
            });
        });

        $("#formwhatsapp").validate({
            rules: {
                descricao: {
                    required: true
                },
                cliente: {
                    required: true
                },
                valor: {
                    required: true
                },
                vencimento: {
                    required: true
                }

            },
            messages: {
                descricao: {
                    required: 'Campo Requerido.'
                },
                cliente: {
                    required: 'Campo Requerido.'
                },
                valor: {
                    required: 'Campo Requerido.'
                },
                vencimento: {
                    required: 'Campo Requerido.'
                }
            },
            submitHandler: function (form) {
                var dados = $(form).serialize();
                $('#btn-cancelar-faturar').trigger('click');
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/faturar",
                    data: dados,
                    dataType: 'json',
                    success: function (data) {
                        if (data.result == true) {

                            window.location.reload(true);
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar  OS."
                            });
                            $('#progress-fatura').hide();
                        }
                    }
                });

                return false;
            }
        });

        $("#produto").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteProduto",
            minLength: 2,
            select: function (event, ui) {
                $("#codDeBarra").val(ui.item.codbar);
                $("#idProduto").val(ui.item.id);
                $("#estoque").val(ui.item.estoque);
                $("#preco").val(ui.item.preco);
                $("#quantidade").focus();
            }
        });

        $("#servico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteServico",
            minLength: 2,
            select: function (event, ui) {
                $("#idServico").val(ui.item.id);
                $("#preco_servico").val(ui.item.preco);
                $("#quantidade_servico").focus();
            }
        });


        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteCliente",
            minLength: 2,
            select: function (event, ui) {
                $("#clientes_id").val(ui.item.id);
            }
        });

        $("#tecnico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteUsuario",
            minLength: 2,
            select: function (event, ui) {
                $("#usuarios_id").val(ui.item.id);
            }
        });

        $("#termoGarantia").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteTermoGarantia",
            minLength: 1,
            select: function (event, ui) {
                if (ui.item.id) {
                    $("#garantias_id").val(ui.item.id);
                }
            }
        });

        $('#termoGarantia').on('change', function () {
            if (!$(this).val() && $("#garantias_id").val()) {
                $("#garantias_id").val('');
                Swal.fire({
                    type: "success",
                    title: "Sucesso",
                    text: "Termo de garantia removido"
                });
            }
        });

        $("#formOs").validate({
            rules: {
                cliente: {
                    required: true
                },
                tecnico: {
                    required: true
                },
                dataInicial: {
                    required: true
                }
            },
            messages: {
                cliente: {
                    required: 'Campo Requerido.'
                },
                tecnico: {
                    required: 'Campo Requerido.'
                },
                dataInicial: {
                    required: 'Campo Requerido.'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        $("#formProdutos").validate({
            rules: {
                preco: {
                    required: true
                },
                quantidade: {
                    required: true
                }
            },
            messages: {
                preco: {
                    required: 'Inserir o preço'
                },
                quantidade: {
                    required: 'Insira a quantidade'
                }
            },
            submitHandler: function (form) {
                var quantidade = parseInt($("#quantidade").val());
                var estoque = parseInt($("#estoque").val());

                <?php if (!$configuration['control_estoque']) {
                    echo 'estoque = 1000000';
                }
                ; ?>

                if (estoque < quantidade) {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: "Você não possui estoque suficiente."
                    });
                } else {
                    var dados = $(form).serialize();
                    $("#divProdutos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>index.php/os/adicionarProduto",
                        data: dados,
                        dataType: 'json',
                        success: function (data) {
                            if (data.result == true) {
                                $("#divProdutos").load("<?php echo current_url(); ?> #divProdutos");
                                $("#quantidade").val('');
                                $("#preco").val('');
                                $("#resultado").val('');
                                $("#desconto").val('');
                                $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                                $("#produto").val('').focus();
                            } else {
                                Swal.fire({
                                    type: "error",
                                    title: "Atenção",
                                    text: "Ocorreu um erro ao tentar adicionar produto."
                                });
                            }
                        }
                    });
                    return false;
                }
            }
        });

        $("#formServicos").validate({
            rules: {
                servico: {
                    required: true
                },
                preco: {
                    required: true
                },
                quantidade: {
                    required: true
                },
            },
            messages: {
                servico: {
                    required: 'Insira um serviço'
                },
                preco: {
                    required: 'Insira o preço'
                },
                quantidade: {
                    required: 'Insira a quantidade'
                },
            },
            submitHandler: function (form) {
                var dados = $(form).serialize();

                $("#divServicos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/adicionarServico",
                    data: dados,
                    dataType: 'json',
                    success: function (data) {
                        if (data.result == true) {
                            $("#divServicos").load("<?php echo current_url(); ?> #divServicos");
                            $("#quantidade_servico").val('');
                            $("#preco_servico").val('');
                            $("#resultado").val('');
                            $("#desconto").val('');
                            $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                            $("#servico").val('').focus();
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar adicionar serviço."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $("#formAnotacao").validate({
            rules: {
                anotacao: {
                    required: true
                }
            },
            messages: {
                anotacao: {
                    required: 'Insira a anotação'
                }
            },
            submitHandler: function (form) {
                var dados = $(form).serialize();
                $("#divFormAnotacoes").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/adicionarAnotacao",
                    data: dados,
                    dataType: 'json',
                    success: function (data) {
                        if (data.result == true) {
                            $("#divAnotacoes").load("<?php echo current_url(); ?> #divAnotacoes");
                            $("#anotacao").val('');
                            $('#btn-close-anotacao').trigger('click');
                            $("#divFormAnotacoes").html('');
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar adicionar anotação."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $("#formAnexos").validate({
            submitHandler: function (form) {
                //var dados = $( form ).serialize();
                var dados = new FormData(form);
                $("#form-anexos").hide('1000');
                $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/anexar",
                    data: dados,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data.result == true) {
                            $("#divAnexos").load("<?php echo current_url(); ?> #divAnexos");
                            $("#userfile").val('');

                        } else {
                            $("#divAnexos").html('<div class="alert fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>Atenção!</strong> ' + data.mensagem + '</div>');
                        }
                    },
                    error: function () {
                        $("#divAnexos").html('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong>Atenção!</strong> Ocorreu um erro. Verifique se você anexou o(s) arquivo(s).</div>');
                    }
                });
                $("#form-anexos").show('1000');
                return false;
            }
        });

        // Validação e submissão do formulário de Aver
        $("#formAver").validate({
            rules: {
                valor: {
                    required: true,
                    number: true,
                    min: function() {
                        return 0;
                    },
                    max: function() {
                        var valorTotal = parseFloat($("#valorTotal").val().replace(/\./g, '').replace(',', '.'));
                        return valorTotal;
                    }
                },
                data_pagamento: {
                    required: true
                },
                status: {
                    required: true
                }
            },
            messages: {
                valor: {
                    required: 'Campo obrigatório',
                    number: 'Digite um valor válido',
                    min: 'O valor deve ser maior ou igual a zero',
                    max: 'O valor não pode ser maior que o valor total da OS'
                },
                data_pagamento: {
                    required: 'Campo obrigatório'
                },
                status: {
                    required: 'Campo obrigatório'
                }
            },
            submitHandler: function(form) {
                var dados = $(form).serialize();
                var valorAver = parseFloat($("#valor_aver").val().replace(/\./g, '').replace(',', '.'));
                var valorTotal = parseFloat($("#valorTotal").val().replace(/\./g, '').replace(',', '.'));

                if (valorAver > valorTotal) {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: "O valor do aver não pode ser maior que o valor total da OS."
                    });
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/adicionarAver",
                    data: dados,
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processando',
                            text: 'Registrando aver...',
                            icon: 'info',
                            showCloseButton: false,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                    },
                    success: function(data) {
                        if (data.result == true) {
                            $('#modal-aver').modal('hide');
                            $("#formAver")[0].reset();
                            
                            // Atualiza a tabela de avers via AJAX
                            $.ajax({
                                url: "<?php echo base_url(); ?>index.php/os/getAvers/<?php echo $result->idOs; ?>",
                                type: "GET",
                                dataType: "html",
                                success: function(response) {
                                    $("#divAvers").html(response);
                                    Swal.fire({
                                        type: "success",
                                        title: "Sucesso",
                                        text: "Aver registrado com sucesso!"
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: data.mensagem || "Ocorreu um erro ao tentar registrar o aver."
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            type: "error",
                            title: "Atenção",
                            text: "Ocorreu um erro ao tentar registrar o aver. Erro: " + error
                        });
                    }
                });
                return false;
            }
        });

        $(document).on('click', 'a', function (event) {
            var idProduto = $(this).attr('idAcao');
            var quantidade = $(this).attr('quantAcao');
            var produto = $(this).attr('prodAcao');
            var idOS = "<?php echo $result->idOs ?>"
            if ((idProduto % 1) == 0) {
                $("#divProdutos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/excluirProduto",
                    data: "idProduto=" + idProduto + "&quantidade=" + quantidade + "&produto=" + produto + "&idOs=" + idOS,
                    dataType: 'json',
                    success: function (data) {
                        if (data.result == true) {
                            $("#divProdutos").load("<?php echo current_url(); ?> #divProdutos");
                            $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                            $("#resultado").val('');
                            $("#desconto").val('');

                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar excluir produto."
                            });
                        }
                    }
                });
                return false;
            }

        });

        $(document).on('click', '.servico', function (event) {
            var idServico = $(this).attr('idAcao');
            var idOS = "<?php echo $result->idOs ?>"
            if ((idServico % 1) == 0) {
                $("#divServicos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/excluirServico",
                    data: "idServico=" + idServico + "&idOs=" + idOS,
                    data: "idServico=" + idServico,
                    dataType: 'json',
                    success: function (data) {
                        if (data.result == true) {
                            $("#divServicos").load("<?php echo current_url(); ?> #divServicos");
                            $("#divValorTotal").load("<?php echo current_url(); ?> #divValorTotal");
                            $("#resultado").val('');
                            $("#desconto").val('');

                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar excluir serviço."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $(document).on('click', '.anexo', function (event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            var url = '<?php echo base_url(); ?>index.php/os/excluirAnexo/';
            $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
            $("#excluir-anexo").attr('link', url + id);

            $("#download").attr('href', "<?php echo base_url(); ?>index.php/os/downloadanexo/" + id);

        });

        $(document).on('click', '#excluir-anexo', function (event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var idOS = "<?php echo $result->idOs ?>"
            $('#modal-anexo').modal('hide');
            $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

            $.ajax({
                type: "POST",
                url: link,
                dataType: 'json',
                data: "idOs=" + idOS,
                success: function (data) {
                    if (data.result == true) {
                        $("#divAnexos").load("<?php echo current_url(); ?> #divAnexos");
                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Atenção",
                            text: data.mensagem
                        });
                    }
                }
            });
        });

        $(document).on('click', '.anotacao', function (event) {
            var idAnotacao = $(this).attr('idAcao');
            var idOS = "<?php echo $result->idOs ?>"
            if ((idAnotacao % 1) == 0) {
                $("#divAnotacoes").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/excluirAnotacao",
                    data: "idAnotacao=" + idAnotacao + "&idOs=" + idOS,
                    dataType: 'json',
                    success: function (data) {
                        if (data.result == true) {
                            $("#divAnotacoes").load("<?php echo current_url(); ?> #divAnotacoes");

                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: "Ocorreu um erro ao tentar excluir Anotação."
                            });
                        }
                    }
                });
                return false;
            }
        });

        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });

        $('.editor').trumbowyg({
            lang: 'pt_br'
        });

        // Autocomplete para usuário adicional no modal
        $("#usuarioAdicional").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteUsuario",
            minLength: 1,
            select: function(event, ui) {
                // Previne o comportamento padrão do autocomplete
                event.preventDefault();
                // Limpa o campo de busca
                $(this).val('');
                // Adiciona o usuário à tabela
                adicionarUsuario(ui.item.id, ui.item.label);
            }
        });

        // Função para adicionar usuário à tabela
        window.adicionarUsuario = function(id, nome) {
            // Verifica se o usuário já está na tabela
            if ($("#usuario-" + id).length > 0) {
                if (!window.carregandoUsuariosFixados) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        text: 'Este usuário já está na lista!'
                    });
                }
                return;
            }

            // Adiciona linha na tabela
            var linha = '<tr id="usuario-' + id + '">';
            linha += '<td>' + nome + '</td>';
            linha += '<td class="text-right">';
            
            // Verifica se é o usuário principal para decidir o que mostrar
            if (id == $("#usuarios_id").val()) {
                linha += '<span class="label label-info">Responsável</span>'; // Texto indicativo para o usuário principal
            } else {
                const usuariosFixados = JSON.parse(localStorage.getItem('usuariosFixadosOS') || '[]');
                const isFixado = usuariosFixados.some(u => u.id === id);
                
                linha += '<button type="button" class="btn ' + (isFixado ? 'btn-success' : 'btn-info') + ' btn-fix-user" onclick="fixarUsuario(' + id + ', \'' + nome + '\')" title="' + (isFixado ? 'Usuário fixado' : 'Fixar usuário') + '">';
                linha += '<i class="bx bx-pin"></i></button> ';
                linha += '<button type="button" class="btn btn-danger" onclick="removerUsuario(' + id + ')">';
                linha += '<i class="bx bx-trash"></i></button>';
            }
            
            linha += '</td></tr>';
            
            // Se for o usuário principal, adiciona no início da tabela
            if (id == $("#usuarios_id").val()) {
                $("#tabelaUsuarios tbody").prepend(linha);
            } else {
                $("#tabelaUsuarios tbody").append(linha);
            }

            // Adiciona o campo hidden apenas se não existir
            if ($("#formOs input[name='usuarios_adicionais[]'][value='" + id + "']").length === 0) {
                var hiddenInput = '<input type="hidden" name="usuarios_adicionais[]" value="' + id + '">';
                $("#formOs").append(hiddenInput);
            }
        }

        // Função para carregar usuários fixados
        function carregarUsuariosFixados() {
            window.carregandoUsuariosFixados = true;
            $("#tabelaUsuarios tbody").empty();
            $("#formOs input[name='usuarios_adicionais[]']").remove();
            
            // Primeiro adiciona o usuário principal
            const usuarioPrincipalId = $("#usuarios_id").val();
            const usuarioPrincipalNome = $("#tecnico").val();
            if (usuarioPrincipalId && usuarioPrincipalNome) {
                adicionarUsuario(usuarioPrincipalId, usuarioPrincipalNome);
            }
            
            // Depois carrega os usuários fixados do banco
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/os/getUsuariosFixados',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.result) {
                        response.usuarios.forEach(usuario => {
                            if (usuario.usuario_id != usuarioPrincipalId) { // Não adiciona o usuário principal novamente
                                adicionarUsuario(usuario.usuario_id, usuario.nome_usuario);
                            }
                        });
                    }
                },
                complete: function() {
                    window.carregandoUsuariosFixados = false;
                    atualizarBotaoAdicionar();
                }
            });
        }

        // Função para atualizar o botão de adicionar
        function atualizarBotaoAdicionar() {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/os/getUsuariosFixados',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const btnAdicionar = $('[data-target="#modalUsuarios"]');
                    
                    if (response.result && response.usuarios.length > 0) {
                        btnAdicionar.addClass('btn-info').attr('title', 'Há usuários fixados');
                        // Adiciona um badge com o número de usuários fixados
                        if (!btnAdicionar.find('.badge').length) {
                            btnAdicionar.append('<span class="badge">' + response.usuarios.length + '</span>');
                        } else {
                            btnAdicionar.find('.badge').text(response.usuarios.length);
                        }
                    } else {
                        btnAdicionar.removeClass('btn-info').attr('title', 'Adicionar técnicos');
                        btnAdicionar.find('.badge').remove();
                    }
                }
            });
        }

        // Função para fixar usuário
        window.fixarUsuario = function(id, nome) {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/os/fixarUsuario',
                type: 'POST',
                data: { usuario_id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.result) {
                        $('#usuario-' + id + ' .btn-fix-user')
                            .removeClass('btn-info')
                            .addClass('btn-success')
                            .attr('title', 'Usuário fixado');

                        atualizarBotaoAdicionar();

                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso',
                            text: 'Usuário fixado com sucesso!'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao fixar usuário!'
                        });
                    }
                }
            });
        }

        // Função para remover usuário
        window.removerUsuario = function(id) {
            Swal.fire({
                title: 'Atenção',
                text: "Você tem certeza que deseja remover este usuário?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, remover!',
                cancelButtonText: 'Não, cancelar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Primeiro verifica se o usuário está fixado
                    $.ajax({
                        url: '<?php echo base_url(); ?>index.php/os/getUsuariosFixados',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.result) {
                                const usuarioFixado = response.usuarios.some(u => u.usuario_id == id);
                                
                                if (usuarioFixado) {
                                    // Se estiver fixado, primeiro desfixa
                                    $.ajax({
                                        url: '<?php echo base_url(); ?>index.php/os/desfixarUsuario',
                                        type: 'POST',
                                        data: { usuario_id: id },
                                        dataType: 'json',
                                        success: function(desfixarResponse) {
                                            if (desfixarResponse.result) {
                                                // Remove o usuário da tabela
                                                $("#usuario-" + id).remove();
                                                // Remove o campo hidden
                                                $("#usuarios_adicionais_container input[value='" + id + "']").remove();
                                                // Atualiza o botão de adicionar
                                                atualizarBotaoAdicionar();
                                            }
                                        }
                                    });
                                } else {
                                    // Se não estiver fixado, remove diretamente
                                    $("#usuario-" + id).remove();
                                    $("#usuarios_adicionais_container input[value='" + id + "']").remove();
                                    // Atualiza o botão de adicionar
                                    atualizarBotaoAdicionar();
                                }
                            }
                        }
                    });
                    
                    Swal.fire(
                        'Removido!',
                        'O usuário foi removido com sucesso.',
                        'success'
                    );
                }
            });
        }

        // Chama carregarUsuariosFixados quando o documento estiver pronto
        $(document).ready(function() {
            carregarUsuariosFixados();
        });

        // Previne edição dos campos de valor
        $('#valor, #faturar-desconto').on('keydown keypress keyup paste', function(e) {
            e.preventDefault();
            return false;
        });

        // Previne seleção do texto
        $('#valor, #faturar-desconto').on('selectstart', function(e) {
            e.preventDefault();
            return false;
        });

        // Previne o menu de contexto (botão direito)
        $('#valor, #faturar-desconto').on('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Manipulação do botão de editar aver
        $(document).on('click', '.btn-editar-aver', function() {
            var id = $(this).data('id');
            var valor = $(this).data('valor');
            var data = $(this).data('data');
            var status = $(this).data('status');

            $('#id_aver_edit').val(id);
            $('#valor_aver_edit').val(valor);
            $('#data_pagamento_edit').val(data);
            $('#status_aver_edit').val(status);
            
            // Abre o modal
            $('#modal-editar-aver').modal('show');
        });

        // Validação e submissão do formulário de edição de Aver
        $("#formEditarAver").validate({
            rules: {
                valor: {
                    required: true,
                    number: true,
                    min: function() {
                        return 0;
                    },
                    max: function() {
                        var valorTotal = parseFloat($("#valorTotal").val().replace(/\./g, '').replace(',', '.'));
                        return valorTotal;
                    }
                },
                data_pagamento: {
                    required: true
                },
                status: {
                    required: true
                }
            },
            messages: {
                valor: {
                    required: 'Campo obrigatório',
                    number: 'Digite um valor válido',
                    min: 'O valor deve ser maior ou igual a zero',
                    max: 'O valor não pode ser maior que o valor total da OS'
                },
                data_pagamento: {
                    required: 'Campo obrigatório'
                },
                status: {
                    required: 'Campo obrigatório'
                }
            },
            submitHandler: function(form) {
                var dados = $(form).serialize();
                var valorAver = parseFloat($("#valor_aver_edit").val().replace(/\./g, '').replace(',', '.'));
                var valorTotal = parseFloat($("#valorTotal").val().replace(/\./g, '').replace(',', '.'));

                if (valorAver > valorTotal) {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: "O valor do aver não pode ser maior que o valor total da OS."
                    });
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/os/editarAver",
                    data: dados,
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processando',
                            text: 'Atualizando aver...',
                            icon: 'info',
                            showCloseButton: false,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                    },
                    success: function(data) {
                        if (data.result == true) {
                            $('#modal-editar-aver').modal('hide');
                            $("#formEditarAver")[0].reset();
                            
                            // Atualiza a tabela de avers via AJAX
                            $.ajax({
                                url: "<?php echo base_url(); ?>index.php/os/getAvers/<?php echo $result->idOs; ?>",
                                type: "GET",
                                dataType: "html",
                                success: function(response) {
                                    $("#divAvers").html(response);
                                    Swal.fire({
                                        type: "success",
                                        title: "Sucesso",
                                        text: "Aver atualizado com sucesso!"
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: data.mensagem || "Ocorreu um erro ao tentar atualizar o aver."
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            type: "error",
                            title: "Atenção",
                            text: "Ocorreu um erro ao tentar atualizar o aver."
                        });
                    }
                });
                return false;
            }
        });
    });
</script>

<!-- Modal Aver -->
<div id="modal-aver" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalAverLabel" aria-hidden="true">
    <form id="formAver" action="<?php echo base_url(); ?>index.php/os/adicionarAver" method="post">
        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="modalAverLabel">Registrar Aver</h3>
        </div>
        <div class="modal-body">
            <div class="span12" style="margin-left: 0">
                <div class="span12" style="margin-left: 0">
                    <label for="valor_aver">Valor do Aver*</label>
                    <input type="hidden" name="os_id" value="<?php echo $result->idOs; ?>">
                    <input type="hidden" name="usuarios_id" value="<?php echo $this->session->userdata('id_admin'); ?>">
                    <input type="text" class="span12 money" id="valor_aver" name="valor" required 
                           data-affixes-stay="true" data-thousands="" data-decimal="." 
                           max="<?php echo number_format($totals + $total, 2, '.', ''); ?>"/>
                </div>
                <div class="span12" style="margin-left: 0">
                    <label for="data_pagamento">Data do Pagamento*</label>
                    <input type="text" class="span12 datepicker" id="data_pagamento" name="data_pagamento" 
                           value="<?php echo date('d/m/Y'); ?>" required/>
                </div>
                <div class="span12" style="margin-left: 0">
                    <label for="status_aver">Status*</label>
                    <select class="span12" name="status" id="status_aver" required>
                        <option value="pago">Pago</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span>
                <span class="button__text2">Cancelar</span>
            </button>
            <button class="button btn btn-success">
                <span class="button__icon"><i class="bx bx-save"></i></span>
                <span class="button__text2">Salvar</span>
            </button>
        </div>
    </form>
</div>

<!-- Modal Editar Aver -->
<div id="modal-editar-aver" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalEditarAverLabel" aria-hidden="true">
    <form id="formEditarAver" action="<?php echo base_url(); ?>index.php/os/editarAver" method="post">
        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="modalEditarAverLabel">Editar Aver</h3>
        </div>
        <div class="modal-body">
            <div class="span12" style="margin-left: 0">
                <div class="span12" style="margin-left: 0">
                    <label for="valor_aver_edit">Valor do Aver*</label>
                    <input type="hidden" name="id_aver" id="id_aver_edit">
                    <input type="hidden" name="os_id" value="<?php echo $result->idOs; ?>">
                    <input type="text" class="span12 money" id="valor_aver_edit" name="valor" required 
                           data-affixes-stay="true" data-thousands="" data-decimal="." 
                           max="<?php echo number_format($totals + $total, 2, '.', ''); ?>"/>
                </div>
                <div class="span12" style="margin-left: 0">
                    <label for="data_pagamento_edit">Data do Pagamento*</label>
                    <input type="text" class="span12 datepicker" id="data_pagamento_edit" name="data_pagamento" required/>
                </div>
                <div class="span12" style="margin-left: 0">
                    <label for="status_aver_edit">Status*</label>
                    <select class="span12" name="status" id="status_aver_edit" required>
                        <option value="pago">Pago</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span>
                <span class="button__text2">Cancelar</span>
            </button>
            <button class="button btn btn-success">
                <span class="button__icon"><i class="bx bx-save"></i></span>
                <span class="button__text2">Salvar</span>
            </button>
        </div>
    </form>
</div>
