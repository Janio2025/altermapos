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
      <a href="#" class="button btn btn-mini btn-info btn-sincronizar-ml" style="max-width: 180px; margin-left: 10px;">
        <span class="button__icon"><i class='bx bx-sync'></i></span>
        <span class="button__text2">Sincronizar ML (<?php echo isset($qtd_ml_pendentes) ? $qtd_ml_pendentes : 0; ?>)</span>
      </a>
      <a href="#" class="button btn btn-mini btn-warning btn-logs-ml" style="max-width: 140px; margin-left: 10px;">
        <span class="button__icon"><i class='bx bx-file-doc'></i></span>
        <span class="button__text2">Logs ML</span>
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
  <form id="formEstoque" action="<?php echo base_url() ?>index.php/produtos/atualizarEstoque" method="post">
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

<!-- Modal Sincronizar Mercado Livre -->
<div id="modal-sincronizar-ml" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalSincronizarMLLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h5 id="modalSincronizarMLLabel"><i class="fas fa-sync"></i> Sincronizar com Mercado Livre</h5>
  </div>
  <div class="modal-body">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
    <div class="alert alert-warning" role="alert">Tem certeza que deseja sincronizar os produtos abaixo com o Mercado Livre?</div>
    <div id="ml-lista-produtos">
      <div class="text-center"><i class="fas fa-spinner fa-spin"></i> Carregando produtos...</div>
    </div>
  </div>
  <div class="modal-footer" style="display:flex;justify-content: center">
    <button type="button" class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
      <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span>
    </button>
    <button type="button" class="button btn btn-success" id="btn-confirmar-sincronizar-ml">
      <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Sincronizar Agora</span>
    </button>
  </div>
</div>

<!-- Modal Logs Mercado Livre -->
<div id="modal-logs-ml" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalLogsMLLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h5 id="modalLogsMLLabel"><i class="fas fa-file-alt"></i> Logs do Mercado Livre</h5>
  </div>
  <div class="modal-body">
    <div class="alert alert-info" role="alert">
      <strong>Logs de Sincronização:</strong> Últimos logs de integração com o Mercado Livre
    </div>
    <div id="ml-logs-content">
      <div class="text-center"><i class="fas fa-spinner fa-spin"></i> Carregando logs...</div>
    </div>
  </div>
  <div class="modal-footer" style="display:flex;justify-content: center">
    <button type="button" class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
      <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Fechar</span>
    </button>
    <button type="button" class="button btn btn-info" id="btn-atualizar-logs-ml">
      <span class="button__icon"><i class="bx bx-refresh"></i></span><span class="button__text2">Atualizar</span>
    </button>
  </div>
</div>
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

    // Sincronizar produtos com Mercado Livre ao clicar no botão do modal
    $(document).on('click', '#btn-confirmar-sincronizar-ml', function(e) {
      e.preventDefault();
      var btn = $(this);
      btn.prop('disabled', true).html('<span class="button__icon"><i class="fas fa-spinner fa-spin"></i></span> <span class="button__text2">Sincronizando...</span>');
      
      console.log('Iniciando sincronização...');
      
      // Obter token CSRF
      var csrfToken = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val();
      console.log('CSRF Token:', csrfToken);
      
      // Primeiro, sincronizar configurações do .env
      $.ajax({
        url: '<?php echo base_url('index.php/MercadoLivre/sincronizarConfiguracoes'); ?>',
        type: 'POST',
        dataType: 'json',
        data: { 
          '<?php echo $this->security->get_csrf_token_name(); ?>': csrfToken
        },
        success: function(configResp) {
          console.log('Resposta sincronização config:', configResp);
          if (configResp.success) {
            console.log('Configurações sincronizadas, iniciando sincronização de produtos...');
            // Agora sincronizar produtos
            $.ajax({
              url: '<?php echo base_url('index.php/MercadoLivre/sincronizarProdutos'); ?>',
              type: 'POST',
              dataType: 'json',
              data: { 
                '<?php echo $this->security->get_csrf_token_name(); ?>': csrfToken
              },
              success: function(resp) {
                console.log('Resposta sincronização produtos:', resp);
                if (resp && resp.success) {
                  $('#ml-lista-produtos').html('<div class="alert alert-success">' + resp.message + '</div>');
                  // Manter modal aberto por 5 segundos para mostrar resultado
                  setTimeout(function() {
                    $('#modal-sincronizar-ml').modal('hide');
                    // Recarregar a página para atualizar a badge de pendentes
                    location.reload();
                  }, 5000);
                } else {
                  var errorMsg = resp && resp.message ? resp.message : 'Erro desconhecido ao sincronizar produtos';
                  console.error('Erro na sincronização de produtos:', errorMsg);
                  $('#ml-lista-produtos').html('<div class="alert alert-danger"><strong>Erro na Sincronização:</strong><br>' + errorMsg + '<br><br><small>Verifique os logs do sistema para mais detalhes.</small></div>');
                  btn.prop('disabled', false).html('<span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Tentar Novamente</span>');
                  // Manter modal aberto para mostrar o erro
                }
              },
              error: function(xhr, status, error) {
                console.error('Erro AJAX na sincronização de produtos:', {xhr: xhr, status: status, error: error});
                var errorMsg = 'Erro ao sincronizar produtos: ' + error;
                if (xhr.responseText) {
                  try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.message) errorMsg = resp.message;
                  } catch(e) {
                    errorMsg += ' - ' + xhr.responseText;
                  }
                }
                $('#ml-lista-produtos').html('<div class="alert alert-danger"><strong>Erro na Sincronização:</strong><br>' + errorMsg + '<br><br><small>Verifique os logs do sistema para mais detalhes.</small></div>');
                btn.prop('disabled', false).html('<span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Tentar Novamente</span>');
                // Manter modal aberto para mostrar o erro
              }
            });
          } else {
            var errorMsg = configResp.message || 'Erro desconhecido ao sincronizar configurações';
            console.error('Erro na sincronização de configurações:', errorMsg);
            $('#ml-lista-produtos').html('<div class="alert alert-danger"><strong>Erro ao Sincronizar Configurações:</strong><br>' + errorMsg + '<br><br><small>Verifique se as configurações do Mercado Livre estão corretas no arquivo .env</small></div>');
            btn.prop('disabled', false).html('<span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Tentar Novamente</span>');
            // Manter modal aberto para mostrar o erro
          }
        },
        error: function(xhr, status, error) {
          console.error('Erro AJAX na sincronização de configurações:', {xhr: xhr, status: status, error: error});
          var errorMsg = 'Erro ao sincronizar configurações: ' + error;
          if (xhr.responseText) {
            try {
              var resp = JSON.parse(xhr.responseText);
              if (resp.message) errorMsg = resp.message;
            } catch(e) {
              errorMsg += ' - ' + xhr.responseText;
            }
          }
          $('#ml-lista-produtos').html('<div class="alert alert-danger"><strong>Erro ao Sincronizar Configurações:</strong><br>' + errorMsg + '<br><br><small>Verifique se as configurações do Mercado Livre estão corretas no arquivo .env</small></div>');
          btn.prop('disabled', false).html('<span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Tentar Novamente</span>');
          // Manter modal aberto para mostrar o erro
        }
      });
    });

    // Abrir modal de sincronização ML
    $(document).on('click', '.btn-sincronizar-ml', function (e) {
      e.preventDefault();
      $('#modal-sincronizar-ml').modal('show');
      // Carregar lista de produtos via AJAX
      $('#ml-lista-produtos').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Carregando produtos...</div>');
      $.getJSON('<?php echo base_url('index.php/produtos/ml_pendentes'); ?>', function (data) {
        if (data && data.length > 0) {
          var html = '<table class="table table-bordered"><thead><tr><th>Produto</th><th>Categoria</th><th>Preço</th><th>Ações</th></tr></thead><tbody>';
          $.each(data, function(i, prod) {
            html += '<tr>' +
              '<td>' + prod.descricao + '</td>' +
              '<td>' + (prod.categoria_nome ? prod.categoria_nome : '-') + '</td>' +
              '<td>R$ ' + prod.precoVenda + '</td>' +
              '<td>' +
                '<a href="<?php echo base_url('index.php/produtos/editar/'); ?>' + prod.idProdutos + '" class="btn btn-mini btn-primary" title="Editar"><i class="bx bx-edit"></i></a> ' +
                '<a href="#" class="btn btn-mini btn-danger btn-excluir-ml" data-id="' + prod.produto_id + '" title="Remover da lista"><i class="bx bx-trash"></i></a>' +
              '</td>' +
            '</tr>';
          });
          html += '</tbody></table>';
          $('#ml-lista-produtos').html(html);
        } else {
          $('#ml-lista-produtos').html('<div class="alert alert-info">Nenhum produto pendente de sincronização.</div>');
        }
      });
    });

    // Abrir modal de logs ML
    $(document).on('click', '.btn-logs-ml', function (e) {
      e.preventDefault();
      $('#modal-logs-ml').modal('show');
      carregarLogsML();
    });

    // Atualizar logs ML
    $(document).on('click', '#btn-atualizar-logs-ml', function (e) {
      e.preventDefault();
      carregarLogsML();
    });

    // Função para carregar logs do ML
    function carregarLogsML() {
      $('#ml-logs-content').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Carregando logs...</div>');
      
      $.ajax({
        url: '<?php echo base_url('index.php/MercadoLivre/getLogs'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          if (data && data.success && data.logs && data.logs.length > 0) {
            var html = '<div class="table-responsive"><table class="table table-bordered table-striped">';
            html += '<thead><tr><th>Data/Hora</th><th>Produto</th><th>Ação</th><th>Status</th><th>Mensagem</th></tr></thead><tbody>';
            
            $.each(data.logs, function(i, log) {
              var statusClass = log.status === 'success' ? 'success' : (log.status === 'error' ? 'danger' : (log.status === 'info' ? 'info' : 'warning'));
              var statusText = log.status === 'success' ? 'Sucesso' : (log.status === 'error' ? 'Erro' : (log.status === 'info' ? 'Info' : 'Aviso'));
              
              html += '<tr class="' + statusClass + '">';
              html += '<td><small>' + log.created_at + '</small></td>';
              html += '<td>' + (log.produto_nome || 'Sistema') + '</td>';
              html += '<td>' + log.acao + '</td>';
              html += '<td><span class="label label-' + statusClass + '">' + statusText + '</span></td>';
              html += '<td><small>' + (log.mensagem || '') + '</small></td>';
              html += '</tr>';
            });
            
            html += '</tbody></table></div>';
            html += '<div class="alert alert-info"><small><strong>Total de logs:</strong> ' + data.logs.length + ' registros encontrados</small></div>';
            $('#ml-logs-content').html(html);
          } else {
            $('#ml-logs-content').html('<div class="alert alert-info">Nenhum log encontrado.</div>');
          }
        },
        error: function(xhr, status, error) {
          $('#ml-logs-content').html('<div class="alert alert-danger">Erro ao carregar logs: ' + error + '</div>');
        }
      });
    }
  });
</script>