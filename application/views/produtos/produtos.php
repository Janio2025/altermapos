<style>
  select {
    width: 70px;
  }

  /* Estilos para a tabela em telas pequenas */
  @media (max-width: 768px) {
    table#tabela thead {
      display: none;
    }

    table#tabela tbody tr {
      display: block;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 10px;
    }

    table#tabela tbody tr td {
      display: flex;
      justify-content: space-between;
      padding: 5px 10px;
      border: none;
    }

    table#tabela tbody tr td::before {
      content: attr(data-label);
      font-weight: bold;
      color: #666;
      margin-right: 10px;
      flex: 1;
    }

    table#tabela tbody tr td:last-child {
      display: flex;
      justify-content: flex-start;
      gap: 5px;
    }
  }
</style>

<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon">
      <i class="fas fa-shopping-bag"></i>
    </span>
    <h5>Produtos</h5>
  </div>
  <div class="flexxn" style="display: flex;">
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) { ?>
      <a href="<?php echo base_url(); ?>index.php/produtos/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2"> Produtos</span>
      </a>
      <a href="#modal-etiquetas" role="button" data-toggle="modal" class="button btn btn-mini btn-warning" style="max-width: 160px">
        <span class="button__icon"><i class='bx bx-barcode-reader'></i></span><span class="button__text2">Gerar Etiquetas</span>
      </a>

  </div>
<?php } ?>

<div class="widget-box">
  <h5 style="padding: 11px 0"></h5>
  <div class="widget-content nopadding tab-content">
    <table id="tabela" class="table table-bordered">
      <thead>
        <tr>
          <th>Cod.</th>
          <th>Produto</th>
          <th>Marca</th>
          <th>Modelo</th>
          <th>Estoque</th>
          <th>Preço</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (!$results) {
          echo '<tr>
                  <td colspan="7">Nenhum Produto Cadastrado</td>
                </tr>';
        }
        foreach ($results as $r) {
          echo '<tr>';
          echo '<td data-label="Cod.">' . $r->idProdutos . '</td>';
          echo '<td data-label="Produto">' . $r->descricao . '</td>';
          echo '<td data-label="Marca">' . $r->marcaProduto . '</td>';
          echo '<td data-label="Modelo">' . $r->nomeModelo . '</td>';
          echo '<td data-label="Estoque">' . $r->estoque . '</td>';
          echo '<td data-label="Preço">R$ ' . number_format($r->precoVenda, 2, ',', '.') . '</td>';
          echo '<td data-label="Ações">';
          if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/produtos/visualizar/' . $r->idProdutos . '" class="btn-nwe" title="Visualizar Produto"><i class="bx bx-show bx-xs"></i></a>  ';
          }
          if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/produtos/editar/' . $r->idProdutos . '" class="btn-nwe3" title="Editar Produto"><i class="bx bx-edit bx-xs"></i></a>';
          }
          if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dProduto')) {
            echo '<a style="margin-right: 1%" href="#modal-excluir" role="button" data-toggle="modal" produto="' . $r->idProdutos . '" class="btn-nwe4" title="Excluir Produto"><i class="bx bx-trash-alt bx-xs"></i></a>';
          }
          if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            echo '<a href="#atualizar-estoque" role="button" data-toggle="modal" produto="' . $r->idProdutos . '" estoque="' . $r->estoque . '" class="btn-nwe5" title="Atualizar Estoque"><i class="bx bx-plus-circle bx-xs"></i></a>';
          }
          echo '</td>';
          echo '</tr>';
        } ?>
      </tbody>
    </table>
  </div>
</div>
<?php echo $this->pagination->create_links(); ?>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalExcluirLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h5 id="modalExcluirLabel"><i class="fas fa-trash-alt"></i> Excluir Produto</h5>
  </div>
  <div class="modal-body">
    <h5 style="text-align: center">Deseja realmente excluir este produto?</h5>
  </div>
  <div class="modal-footer" style="display:flex;justify-content: center">
    <a href="#" id="delete-confirm" class="button btn btn-mini btn-danger"><span class="button__icon"><i class="bx bx-trash-alt"></i></span><span class="button__text2">Excluir</span></a>
    <button type="button" class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
      <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span>
    </button>
  </div>
</div>

<!-- Modal Estoque -->
<div id="atualizar-estoque" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalEstoqueLabel" aria-hidden="true">
  <form id="formEstoque" action="<?php echo base_url() ?>index.php/produtos/atualizar_estoque" method="post">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h5 id="modalEstoqueLabel"><i class="fas fa-plus-square"></i> Atualizar Estoque</h5>
    </div>
    <div class="modal-body">
      <div class="control-group">
        <label for="estoqueAtual" class="control-label">Estoque Atual</label>
        <div class="controls">
          <input id="estoqueAtual" type="text" name="estoqueAtual" value="" readonly />
        </div>
      </div>

      <div class="control-group">
        <label for="estoque" class="control-label">Adicionar Produtos<span class="required">*</span></label>
        <div class="controls">
          <input type="hidden" id="idProduto" class="idProduto" name="id" value="" />
          <input id="estoque" type="text" name="estoque" value="" />
        </div>
      </div>
    </div>
    <div class="modal-footer" style="display:flex;justify-content: center">
      <button type="submit" class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
      <button type="button" class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
    </div>
  </form>
</div>

<!-- Modal Etiquetas -->
<div id="modal-etiquetas" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalEtiquetasLabel" aria-hidden="true">
  <form action="<?php echo base_url() ?>index.php/relatorios/produtosEtiquetas" method="get">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h5 id="modalEtiquetasLabel">Gerar etiquetas com Código de Barras</h5>
    </div>
    <div class="modal-body">
      <div class="span12 alert alert-info" style="margin-left: 0"> Escolha o intervalo de produtos para gerar as etiquetas.</div>

      <div class="span12" style="margin-left: 0;">
        <div class="span6" style="margin-left: 0;">
          <label for="de_id">De</label>
          <input class="span9" style="margin-left: 0" type="text" id="de_id" name="de_id" placeholder="ID do primeiro produto" value="" />
        </div>

        <div class="span6">
          <label for="ate_id">Até</label>
          <input class="span9" type="text" id="ate_id" name="ate_id" placeholder="ID do último produto" value="" />
        </div>

        <div class="span4">
          <label for="qtdEtiqueta">Qtd. do Estoque</label>
          <input class="span12" type="checkbox" id="qtdEtiqueta" name="qtdEtiqueta" value="true" />
        </div>

        <div class="span6">
          <label class="span12" for="etiquetaCode">Formato Etiqueta</label>
          <select class="span5" id="etiquetaCode" name="etiquetaCode">
            <option value="EAN13">EAN-13</option>
            <option value="UPCA">UPCA</option>
            <option value="C93">CODE 93</option>
            <option value="C128A">CODE 128</option>
            <option value="CODABAR">CODABAR</option>
            <option value="QR">QR-CODE</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="display:flex;justify-content: center">
      <button type="button" class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
      <button type="submit" class="button btn btn-success"><span class="button__icon"><i class='bx bx-barcode'></i></span><span class="button__text2">Gerar</span></button>
    </div>
  </form>
</div>

<!-- Modal Sincronizar ML removido -->

<!-- Modal Logs ML removido -->
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
  $(document).ready(function () {
    // Handler específico para links com atributo produto (estoque)
    $(document).on('click', 'a[produto]', function (event) {
      var produto = $(this).attr('produto');
      var estoque = $(this).attr('estoque');
      $('.idProduto').val(produto);
      $('#estoqueAtual').val(estoque);
    });

    // Handler específico para o botão de exclusão
    $(document).on('click', '#delete-confirm', function (event) {
      event.preventDefault();
      // Pegar o ID do produto do modal atual
      var produto = $('#modal-excluir').data('produto-id');
      if (produto) {
        // Criar um formulário temporário para enviar via POST
        var form = $('<form method="post" action="<?php echo base_url(); ?>index.php/produtos/excluir"></form>');
        form.append('<input type="hidden" name="id" value="' + produto + '">');
        form.append('<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">');
        $('body').append(form);
        form.submit();
      }
    });

    // Capturar o ID do produto quando o modal de exclusão for aberto
    $(document).on('click', 'a[href="#modal-excluir"]', function (event) {
      var produto = $(this).attr('produto');
      $('#modal-excluir').data('produto-id', produto);
    });

    $('#formEstoque').validate({
      rules: {
        estoque: {
          required: true,
          number: true
        }
      },
      messages: {
        estoque: {
          required: 'Campo Requerido.',
          number: 'Informe um número válido.'
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

    // Scripts de sincronização ML removidos

    // Scripts de logs ML removidos
  });
</script>