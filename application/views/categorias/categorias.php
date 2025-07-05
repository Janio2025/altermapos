<style>
  select {
    width: 70px;
  }
  @media (max-width: 768px) {
    table#tabela thead { display: none; }
    table#tabela tbody tr { display: block; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; padding: 10px; }
    table#tabela tbody tr td { display: flex; justify-content: space-between; padding: 5px 10px; border: none; }
    table#tabela tbody tr td::before { content: attr(data-label); font-weight: bold; color: #666; margin-right: 10px; flex: 1; }
    table#tabela tbody tr td:last-child { display: flex; justify-content: flex-start; gap: 5px; }
  }
</style>
<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon">
      <i class="fas fa-tags"></i>
    </span>
    <h5>Categorias</h5>
  </div>
  <div class="flexxn" style="display: flex;">
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCategoria')) { ?>
      <a href="<?php echo base_url(); ?>index.php/categorias/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2"> Categoria</span>
      </a>
    <?php } ?>
  </div>
  <div class="widget-box">
    <h5 style="padding: 11px 0"></h5>
    <div class="widget-content nopadding tab-content">
      <table id="tabela" class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Categoria Pai</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$categorias) {
            echo '<tr><td colspan="5">Nenhuma Categoria Cadastrada</td></tr>';
          }
          foreach ($categorias as $cat) {
            echo '<tr>';
            echo '<td data-label="ID">' . $cat->idCategorias . '</td>';
            echo '<td data-label="Nome">' . $cat->categoria . '</td>';
            echo '<td data-label="Tipo">' . $cat->tipo . '</td>';
            echo '<td data-label="Categoria Pai">';
            if ($cat->parent_id) {
              $pai = array_filter($categorias, function($c) use ($cat) { return $c->idCategorias == $cat->parent_id; });
              $pai = reset($pai);
              echo $pai ? $pai->categoria : '-';
            } else {
              echo '-';
            }
            echo '</td>';
            echo '<td data-label="Ações">';
            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCategoria')) {
              echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/categorias/editar/' . $cat->idCategorias . '" class="btn-nwe3" title="Editar Categoria"><i class="bx bx-edit bx-xs"></i></a>';
            }
            if ($cat->tipo == 'mercado_livre' && $cat->ml_id && $this->permission->checkPermission($this->session->userdata('permissao'), 'eCategoria')) {
              echo '<a style="margin-right: 1%" href="#" class="btn-nwe5" title="Buscar Atributos" data-categoria-id="' . $cat->idCategorias . '" data-ml-id="' . $cat->ml_id . '" data-categoria-nome="' . $cat->categoria . '"><i class="bx bx-list-ul bx-xs"></i></a>';
            }
            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dCategoria')) {
              echo '<a style="margin-right: 1%" href="#modal-excluir" role="button" data-toggle="modal" categoria="' . $cat->idCategorias . '" class="btn-nwe4" title="Excluir Categoria"><i class="bx bx-trash-alt bx-xs"></i></a>';
            }
            echo '</td>';
            echo '</tr>';
          } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- Modal Excluir Categoria -->
<div id="modal-excluir-categoria" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form action="<?php echo base_url() ?>index.php/categorias/deletar" method="post">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h5 id="myModalLabel"><i class="fas fa-trash-alt"></i> Excluir Categoria</h5>
    </div>
    <div class="modal-body">
      <input type="hidden" id="idCategoriaExcluir" name="id" value="" />
      <h5 style="text-align: center">Deseja realmente excluir esta categoria?</h5>
    </div>
    <div class="modal-footer" style="display:flex;justify-content: center">
      <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
        <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span>
      </button>
      <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
    </div>
  </form>
</div>

<!-- Modal Buscar Atributos -->
<div id="modal-atributos" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalAtributosLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h5 id="modalAtributosLabel"><i class="fas fa-list-ul"></i> Atributos do Mercado Livre</h5>
  </div>
  <div class="modal-body">
    <div id="loadingAtributos" style="display: none; text-align: center; padding: 20px;">
      <i class="fas fa-spinner fa-spin fa-2x"></i>
      <p>Buscando atributos...</p>
    </div>
    <div id="atributosContainer" style="display: none;">
      <h6>Selecione os atributos que deseja usar:</h6>
      <div id="listaAtributos" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <!-- Atributos serão carregados aqui -->
      </div>
      <div style="margin-top: 15px;">
        <button type="button" id="btnSelecionarTodosAtributos" class="button btn btn-mini btn-info">
          <span class="button__icon"><i class='bx bx-check-square'></i></span>
          <span class="button__text2">Selecionar Todos</span>
        </button>
        <button type="button" id="btnLimparSelecaoAtributos" class="button btn btn-mini btn-warning">
          <span class="button__icon"><i class='bx bx-square'></i></span>
          <span class="button__text2">Limpar Seleção</span>
        </button>
        <button type="button" id="btnSalvarAtributos" class="button btn btn-success">
          <span class="button__icon"><i class='bx bx-save'></i></span>
          <span class="button__text2">Salvar Selecionados</span>
        </button>
      </div>
    </div>
  </div>
  <div class="modal-footer" style="display:flex;justify-content: center">
    <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
      <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Fechar</span>
    </button>
  </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function() {

  // Excluir categoria
  $(document).on('click', '.btn-nwe4', function (event) {
    var categoria = $(this).attr('categoria');
    $('#idCategoriaExcluir').val(categoria);
    $('#modal-excluir-categoria').modal('show');
  });

  // Buscar atributos
  $(document).on('click', '.btn-nwe5', function (event) {
    event.preventDefault();
    var categoriaId = $(this).data('categoria-id');
    var mlId = $(this).data('ml-id');
    var categoriaNome = $(this).data('categoria-nome');
    
    console.log('Clique detectado:', {categoriaId: categoriaId, mlId: mlId, categoriaNome: categoriaNome});
    
    // Atualizar título do modal
    $('#modalAtributosLabel').html('<i class="fas fa-list-ul"></i> Atributos: ' + categoriaNome);
    
    // Mostrar loading e abrir modal
    $('#loadingAtributos').show();
    $('#atributosContainer').hide();
    $('#listaAtributos').html('');
    $('#modal-atributos').modal('show');
    
    // Primeiro tentar o método alternativo (dados estáticos)
    console.log('Tentando método alternativo primeiro...');
    $.ajax({
      url: '<?php echo site_url('categorias/buscarAtributosAlternativos'); ?>',
      type: 'GET',
      data: { 
        categoria_id: categoriaId,
        ml_id: mlId 
      },
      dataType: 'json',
      success: function(respAlt) {
        $('#loadingAtributos').hide();
        console.log('Resposta método alternativo:', respAlt);
        
        if (respAlt.success) {
          renderizarAtributos(respAlt.atributos);
          $('#atributosContainer').show();
          $('#modalAtributosLabel').html('<i class="fas fa-list-ul"></i> Atributos: ' + categoriaNome + ' (Pré-definidos)');
        } else {
          // Se o método alternativo falhar, tentar a API principal
          console.log('Método alternativo falhou, tentando API principal...');
          $('#loadingAtributos').show();
          
          $.ajax({
            url: '<?php echo site_url('categorias/buscarAtributosML'); ?>',
            type: 'GET',
            data: { 
              categoria_id: categoriaId,
              ml_id: mlId 
            },
            dataType: 'json',
            success: function(resp) {
              $('#loadingAtributos').hide();
              console.log('Resposta API principal:', resp);
              
              if (resp.success) {
                renderizarAtributos(resp.atributos);
                $('#atributosContainer').show();
                $('#modalAtributosLabel').html('<i class="fas fa-list-ul"></i> Atributos: ' + categoriaNome);
              } else {
                $('#listaAtributos').html('<div class="alert alert-error">' + resp.message + '</div>');
                $('#atributosContainer').show();
              }
            },
            error: function(xhr, status, error) {
              $('#loadingAtributos').hide();
              console.log('Erro na API principal:', {xhr: xhr, status: status, error: error});
              $('#listaAtributos').html('<div class="alert alert-error">Erro ao buscar atributos da API principal.</div>');
              $('#atributosContainer').show();
            }
          });
        }
      },
      error: function(xhr, status, error) {
        $('#loadingAtributos').hide();
        console.log('Erro no método alternativo:', {xhr: xhr, status: status, error: error});
        $('#listaAtributos').html('<div class="alert alert-error">Erro ao carregar atributos alternativos.</div>');
        $('#atributosContainer').show();
      }
    });
  });

  // Renderizar atributos
  function renderizarAtributos(atributos) {
    var html = '';
    atributos.forEach(function(atributo) {
      html += '<div class="atributo-item" style="margin: 8px 0; padding: 8px; border: 1px solid #eee; border-radius: 3px;">';
      html += '<label style="display: flex; align-items: center; margin: 0;">';
      html += '<input type="checkbox" name="atributos_ml[]" value="' + atributo.id + '" data-atributo=\'' + JSON.stringify(atributo) + '\' style="margin-right: 8px;">';
      html += '<div style="flex: 1;">';
      html += '<span style="font-weight: bold;">' + atributo.name + '</span>';
      if (atributo.required) {
        html += ' <span style="color: red; font-size: 12px;">(Obrigatório)</span>';
      }
      html += '<br><span style="color: #666; font-size: 12px;">Tipo: ' + atributo.value_type + '</span>';
      if (atributo.values && atributo.values.length > 0) {
        html += '<br><span style="color: #666; font-size: 12px;">Opções: ' + atributo.values.length + ' valores</span>';
      }
      html += '</div>';
      html += '</label>';
      html += '</div>';
    });
    $('#listaAtributos').html(html);
  }

  // Selecionar todos os atributos
  $('#btnSelecionarTodosAtributos').click(function() {
    $('input[name="atributos_ml[]"]').prop('checked', true);
  });

  // Limpar seleção de atributos
  $('#btnLimparSelecaoAtributos').click(function() {
    $('input[name="atributos_ml[]"]').prop('checked', false);
  });

  // Variável global para armazenar o ID da categoria atual
  var categoriaAtualId = null;

  // Buscar atributos
  $(document).on('click', '.btn-nwe5', function (event) {
    event.preventDefault();
    var categoriaId = $(this).data('categoria-id');
    var mlId = $(this).data('ml-id');
    var categoriaNome = $(this).data('categoria-nome');
    
    // Armazenar o ID da categoria para uso posterior
    categoriaAtualId = categoriaId;
    
    console.log('Clique detectado:', {categoriaId: categoriaId, mlId: mlId, categoriaNome: categoriaNome});
    
    // Atualizar título do modal
    $('#modalAtributosLabel').html('<i class="fas fa-list-ul"></i> Atributos: ' + categoriaNome);
    
    // Mostrar loading e abrir modal
    $('#loadingAtributos').show();
    $('#atributosContainer').hide();
    $('#listaAtributos').html('');
    $('#modal-atributos').modal('show');
    
    // Primeiro tentar o método alternativo (dados estáticos)
    console.log('Tentando método alternativo primeiro...');
    $.ajax({
      url: '<?php echo site_url('categorias/buscarAtributosAlternativos'); ?>',
      type: 'GET',
      data: { 
        categoria_id: categoriaId,
        ml_id: mlId 
      },
      dataType: 'json',
      success: function(respAlt) {
        $('#loadingAtributos').hide();
        console.log('Resposta método alternativo:', respAlt);
        
        if (respAlt.success) {
          renderizarAtributos(respAlt.atributos);
          $('#atributosContainer').show();
          $('#modalAtributosLabel').html('<i class="fas fa-list-ul"></i> Atributos: ' + categoriaNome + ' (Pré-definidos)');
        } else {
          // Se o método alternativo falhar, tentar a API principal
          console.log('Método alternativo falhou, tentando API principal...');
          $('#loadingAtributos').show();
          
          $.ajax({
            url: '<?php echo site_url('categorias/buscarAtributosML'); ?>',
            type: 'GET',
            data: { 
              categoria_id: categoriaId,
              ml_id: mlId 
            },
            dataType: 'json',
            success: function(resp) {
              $('#loadingAtributos').hide();
              console.log('Resposta API principal:', resp);
              
              if (resp.success) {
                renderizarAtributos(resp.atributos);
                $('#atributosContainer').show();
                $('#modalAtributosLabel').html('<i class="fas fa-list-ul"></i> Atributos: ' + categoriaNome);
              } else {
                $('#listaAtributos').html('<div class="alert alert-error">' + resp.message + '</div>');
                $('#atributosContainer').show();
              }
            },
            error: function(xhr, status, error) {
              $('#loadingAtributos').hide();
              console.log('Erro na API principal:', {xhr: xhr, status: status, error: error});
              $('#listaAtributos').html('<div class="alert alert-error">Erro ao buscar atributos da API principal.</div>');
              $('#atributosContainer').show();
            }
          });
        }
      },
      error: function(xhr, status, error) {
        $('#loadingAtributos').hide();
        console.log('Erro no método alternativo:', {xhr: xhr, status: status, error: error});
        $('#listaAtributos').html('<div class="alert alert-error">Erro ao carregar atributos alternativos.</div>');
        $('#atributosContainer').show();
      }
    });
  });

  // Salvar atributos selecionados
  $('#btnSalvarAtributos').click(function() {
    var atributosSelecionados = [];
    $('input[name="atributos_ml[]"]:checked').each(function() {
      var atributoData = $(this).data('atributo');
      atributosSelecionados.push(atributoData);
    });

    if (atributosSelecionados.length === 0) {
      alert('Selecione pelo menos um atributo.');
      return;
    }

    if (!categoriaAtualId) {
      alert('Erro: ID da categoria não encontrado.');
      return;
    }

    console.log('Salvando atributos para categoria:', categoriaAtualId);
    console.log('Atributos selecionados:', atributosSelecionados);

    $('#btnSalvarAtributos').prop('disabled', true);
    $('#btnSalvarAtributos').html('<i class="fas fa-spinner fa-spin"></i> Salvando...');

    $.ajax({
      url: '<?php echo site_url('categorias/salvarAtributosML'); ?>',
      type: 'POST',
      data: { 
        categoria_id: categoriaAtualId,
        atributos: atributosSelecionados 
      },
      dataType: 'json',
      success: function(resp) {
        $('#btnSalvarAtributos').prop('disabled', false);
        $('#btnSalvarAtributos').html('<span class="button__icon"><i class="bx bx-save"></i></span><span class="button__text2">Salvar Selecionados</span>');
        
        console.log('Resposta do salvamento:', resp);
        
        if (resp.success) {
          // Criar modal de sucesso bonito
          var modalHtml = '<div class="modal fade" id="modalSucesso" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog modal-sm" role="document">' +
            '<div class="modal-content">' +
            '<div class="modal-header" style="background-color: #5cb85c; color: white; border-radius: 6px 6px 0 0;">' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white;">×</button>' +
            '<h4 class="modal-title"><i class="fas fa-check-circle"></i> Sucesso!</h4>' +
            '</div>' +
            '<div class="modal-body text-center" style="padding: 20px;">' +
            '<i class="fas fa-check-circle" style="font-size: 48px; color: #5cb85c; margin-bottom: 15px;"></i>' +
            '<p style="font-size: 16px; margin-bottom: 10px;"><strong>Atributos salvos com sucesso!</strong></p>' +
            '<p style="color: #666; font-size: 14px;">' + resp.message + '</p>' +
            '</div>' +
            '<div class="modal-footer" style="text-align: center; border-top: none;">' +
            '<button type="button" class="btn btn-success" data-dismiss="modal">' +
            '<i class="fas fa-check"></i> OK' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
          
          // Remover modal anterior se existir
          $('#modalSucesso').remove();
          
          // Adicionar novo modal ao body
          $('body').append(modalHtml);
          
          // Mostrar modal
          $('#modalSucesso').modal('show');
          
          // Fechar modal de atributos
          $('#modal-atributos').modal('hide');
          
          // Auto-fechar modal de sucesso após 3 segundos
          setTimeout(function() {
            $('#modalSucesso').modal('hide');
          }, 3000);
          
        } else {
          // Criar modal de erro bonito
          var modalHtml = '<div class="modal fade" id="modalErro" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog modal-sm" role="document">' +
            '<div class="modal-content">' +
            '<div class="modal-header" style="background-color: #d9534f; color: white; border-radius: 6px 6px 0 0;">' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white;">×</button>' +
            '<h4 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Erro!</h4>' +
            '</div>' +
            '<div class="modal-body text-center" style="padding: 20px;">' +
            '<i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #d9534f; margin-bottom: 15px;"></i>' +
            '<p style="font-size: 16px; margin-bottom: 10px;"><strong>Erro ao salvar atributos!</strong></p>' +
            '<p style="color: #666; font-size: 14px;">' + resp.message + '</p>' +
            '</div>' +
            '<div class="modal-footer" style="text-align: center; border-top: none;">' +
            '<button type="button" class="btn btn-danger" data-dismiss="modal">' +
            '<i class="fas fa-times"></i> Fechar' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
          
          // Remover modal anterior se existir
          $('#modalErro').remove();
          
          // Adicionar novo modal ao body
          $('body').append(modalHtml);
          
          // Mostrar modal
          $('#modalErro').modal('show');
        }
      },
      error: function(xhr, status, error) {
        $('#btnSalvarAtributos').prop('disabled', false);
        $('#btnSalvarAtributos').html('<span class="button__icon"><i class="bx bx-save"></i></span><span class="button__text2">Salvar Selecionados</span>');
        console.log('Erro ao salvar atributos:', {xhr: xhr, status: status, error: error});
        
        // Criar modal de erro bonito
        var modalHtml = '<div class="modal fade" id="modalErro" tabindex="-1" role="dialog">' +
          '<div class="modal-dialog modal-sm" role="document">' +
          '<div class="modal-content">' +
          '<div class="modal-header" style="background-color: #d9534f; color: white; border-radius: 6px 6px 0 0;">' +
          '<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white;">×</button>' +
          '<h4 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Erro!</h4>' +
          '</div>' +
          '<div class="modal-body text-center" style="padding: 20px;">' +
          '<i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #d9534f; margin-bottom: 15px;"></i>' +
          '<p style="font-size: 16px; margin-bottom: 10px;"><strong>Erro ao salvar atributos!</strong></p>' +
          '<p style="color: #666; font-size: 14px;">Ocorreu um erro inesperado. Tente novamente.</p>' +
          '</div>' +
          '<div class="modal-footer" style="text-align: center; border-top: none;">' +
          '<button type="button" class="btn btn-danger" data-dismiss="modal">' +
          '<i class="fas fa-times"></i> Fechar' +
          '</button>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '</div>';
        
        // Remover modal anterior se existir
        $('#modalErro').remove();
        
        // Adicionar novo modal ao body
        $('body').append(modalHtml);
        
        // Mostrar modal
        $('#modalErro').modal('show');
      }
    });
  });
});
</script> 