<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon">
      <i class="fas fa-tags"></i>
    </span>
    <h5>Adicionar Categoria</h5>
  </div>
  <div class="widget-box">
    <div class="widget-content nopadding tab-content">
      
      <!-- Seleção do tipo de cadastro -->
      <div class="control-group" style="margin-bottom: 20px;">
        <label class="control-label">Tipo de Cadastro</label>
        <div class="controls">
          <label class="radio inline">
            <input type="radio" name="tipo_cadastro" value="local" checked> Cadastro Local
          </label>
          <label class="radio inline">
            <input type="radio" name="tipo_cadastro" value="mercado_livre"> Importar do Mercado Livre
          </label>
        </div>
      </div>

      <!-- Formulário de cadastro local -->
      <form action="<?php echo site_url('categorias/adicionar'); ?>" method="post" class="form-horizontal" id="formCategoria">
        <div class="control-group">
          <label class="control-label">Nome da Categoria</label>
          <div class="controls">
            <input type="text" name="categoria" class="span6" required />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">ML ID (Mercado Livre)</label>
          <div class="controls">
            <input type="text" name="ml_id" class="span4" placeholder="Ex: MLB1182" />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Tipo</label>
          <div class="controls">
            <input type="text" name="tipo" class="span4" placeholder="Ex: interna, mercado_livre" />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Categoria Pai</label>
          <div class="controls">
            <select name="parent_id" class="span6">
              <option value="">Nenhuma (Categoria Raiz)</option>
              <?php foreach ($categorias as $cat) { echo '<option value="' . $cat->idCategorias . '">' . $cat->categoria . '</option>'; } ?>
            </select>
          </div>
        </div>
        <div class="form-actions" style="display:flex;justify-content: center; gap: 10px">
          <button type="submit" class="button btn btn-success"><span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
          <button type="button" id="btnAddSub" class="button btn btn-info"><span class="button__icon"><i class='bx bx-git-branch'></i></span><span class="button__text2">Adicionar Subcategoria</span></button>
          <a title="Voltar" class="button btn btn-mini btn-warning" href="<?php echo site_url() ?>/categorias">
            <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
        </div>
      </form>

      <!-- Interface de importação do Mercado Livre -->
      <div id="importacaoML" style="display: none;">
        <div class="control-group">
          <label class="control-label">Buscar Categorias do Mercado Livre</label>
          <div class="controls">
            <button type="button" id="btnBuscarCategorias" class="button btn btn-primary">
              <span class="button__icon"><i class='bx bx-search'></i></span>
              <span class="button__text2">Buscar Categorias</span>
            </button>
            <div id="loadingCategorias" style="display: none; margin-top: 10px;">
              <i class="fas fa-spinner fa-spin"></i> Buscando categorias...
            </div>
          </div>
        </div>
        
        <div id="listaCategorias" style="display: none; margin-top: 20px;">
          <h6>Selecione as categorias que deseja importar:</h6>
          <div id="categoriasContainer" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
            <!-- Categorias serão carregadas aqui -->
          </div>
          <div style="margin-top: 15px;">
            <button type="button" id="btnSelecionarTodos" class="button btn btn-mini btn-info">
              <span class="button__icon"><i class='bx bx-check-square'></i></span>
              <span class="button__text2">Selecionar Todos</span>
            </button>
            <button type="button" id="btnLimparSelecao" class="button btn btn-mini btn-warning">
              <span class="button__icon"><i class='bx bx-square'></i></span>
              <span class="button__text2">Limpar Seleção</span>
            </button>
            <button type="button" id="btnImportarCategorias" class="button btn btn-success">
              <span class="button__icon"><i class='bx bx-download'></i></span>
              <span class="button__text2">Importar Selecionadas</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="msgCategoria" style="margin-top:10px;"></div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    // Seleciona automaticamente o parent_id se vier na URL
    var urlParams = new URLSearchParams(window.location.search);
    var parentId = urlParams.get('parent_id');
    if (parentId) {
      $("#formCategoria select[name='parent_id']").val(parentId);
    }

    // Controle de exibição dos formulários
    $('input[name="tipo_cadastro"]').change(function() {
      if ($(this).val() === 'local') {
        $('#formCategoria').show();
        $('#importacaoML').hide();
      } else {
        $('#formCategoria').hide();
        $('#importacaoML').show();
      }
    });

    $('#formCategoria').validate({
      rules: { categoria: { required: true } },
      messages: { categoria: { required: 'Campo obrigatório' } },
      errorClass: "help-inline",
      errorElement: "span",
      highlight: function(element, errorClass, validClass) {
        $(element).parents('.control-group').addClass('error');
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).parents('.control-group').removeClass('error');
        $(element).parents('.control-group').addClass('success');
      }
    });

    $('#btnAddSub').click(function(e) {
      e.preventDefault();
      if (!$('#formCategoria').valid()) return;
      var dados = $('#formCategoria').serialize();
      $.ajax({
        url: '<?php echo site_url('categorias/adicionar'); ?>',
        type: 'POST',
        data: dados,
        dataType: 'json',
        success: function(resp) {
          if (resp.success) {
            $('#msgCategoria').html('<div class="alert alert-success">Categoria cadastrada! Agora cadastre uma subcategoria para <b>' + resp.categoria + '</b>.</div>');
            // Limpa campos, exceto parent_id
            $("#formCategoria input[name='categoria']").val('');
            $("#formCategoria input[name='ml_id']").val('');
            $("#formCategoria input[name='tipo']").val('');
            // Adiciona a nova categoria ao select se não existir
            var select = $("#formCategoria select[name='parent_id']");
            if (select.find('option[value="'+resp.id+'"]').length === 0) {
              select.append('<option value="'+resp.id+'">'+resp.categoria+'</option>');
            }
            select.val(resp.id);
          } else {
            $('#msgCategoria').html('<div class="alert alert-error">' + resp.message + '</div>');
          }
        },
        error: function() {
          $('#msgCategoria').html('<div class="alert alert-error">Erro ao salvar categoria.</div>');
        }
      });
    });

    // Buscar categorias do Mercado Livre
    $('#btnBuscarCategorias').click(function() {
      $('#loadingCategorias').show();
      $('#btnBuscarCategorias').prop('disabled', true);
      
      $.ajax({
        url: '<?php echo site_url('categorias/buscarCategoriasML'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(resp) {
          $('#loadingCategorias').hide();
          $('#btnBuscarCategorias').prop('disabled', false);
          
          if (resp.success) {
            renderizarCategorias(resp.categorias);
            $('#listaCategorias').show();
          } else {
            // Se a API principal falhar, tentar o método alternativo
            if (resp.message && resp.message.includes('bloqueou o acesso')) {
              $('#loadingCategorias').show();
              $.ajax({
                url: '<?php echo site_url('categorias/buscarCategoriasAlternativas'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(respAlt) {
                  $('#loadingCategorias').hide();
                  if (respAlt.success) {
                    renderizarCategorias(respAlt.categorias);
                    $('#listaCategorias').show();
                    $('#msgCategoria').html('<div class="alert alert-info">Usando categorias pré-definidas (API bloqueada).</div>');
                  } else {
                    $('#msgCategoria').html('<div class="alert alert-error">' + respAlt.message + '</div>');
                  }
                },
                error: function() {
                  $('#loadingCategorias').hide();
                  $('#msgCategoria').html('<div class="alert alert-error">Erro ao carregar categorias alternativas.</div>');
                }
              });
            } else {
              $('#msgCategoria').html('<div class="alert alert-error">' + resp.message + '</div>');
            }
          }
        },
        error: function() {
          $('#loadingCategorias').hide();
          $('#btnBuscarCategorias').prop('disabled', false);
          $('#msgCategoria').html('<div class="alert alert-error">Erro ao buscar categorias do Mercado Livre.</div>');
        }
      });
    });

    // Renderizar categorias em formato de árvore
    function renderizarCategorias(categorias) {
      var html = '<div class="categorias-tree">';
      categorias.forEach(function(cat) {
        html += '<div class="categoria-item" style="margin: 5px 0; padding: 5px; border: 1px solid #eee; border-radius: 3px;">';
        html += '<label style="display: flex; align-items: center; margin: 0;">';
        html += '<input type="checkbox" name="categorias_ml[]" value="' + cat.id + '" data-nome="' + cat.name + '" style="margin-right: 8px;">';
        html += '<span style="font-weight: bold;">' + cat.name + '</span>';
        html += '<span style="margin-left: 10px; color: #666; font-size: 12px;">(' + cat.id + ')</span>';
        html += '</label>';
        if (cat.children && cat.children.length > 0) {
          html += '<div style="margin-left: 20px; margin-top: 5px;">';
          cat.children.forEach(function(subcat) {
            html += '<div style="margin: 3px 0;">';
            html += '<label style="display: flex; align-items: center; margin: 0;">';
            html += '<input type="checkbox" name="categorias_ml[]" value="' + subcat.id + '" data-nome="' + subcat.name + '" style="margin-right: 8px;">';
            html += '<span>' + subcat.name + '</span>';
            html += '<span style="margin-left: 10px; color: #666; font-size: 12px;">(' + subcat.id + ')</span>';
            html += '</label>';
            html += '</div>';
          });
          html += '</div>';
        }
        html += '</div>';
      });
      html += '</div>';
      $('#categoriasContainer').html(html);
    }

    // Selecionar todos
    $('#btnSelecionarTodos').click(function() {
      $('input[name="categorias_ml[]"]').prop('checked', true);
    });

    // Limpar seleção
    $('#btnLimparSelecao').click(function() {
      $('input[name="categorias_ml[]"]').prop('checked', false);
    });

    // Importar categorias selecionadas
    $('#btnImportarCategorias').click(function() {
      var categoriasSelecionadas = [];
      $('input[name="categorias_ml[]"]:checked').each(function() {
        categoriasSelecionadas.push({
          id: $(this).val(),
          name: $(this).data('nome')
        });
      });

      if (categoriasSelecionadas.length === 0) {
        $('#msgCategoria').html('<div class="alert alert-warning">Selecione pelo menos uma categoria.</div>');
        return;
      }

      $('#btnImportarCategorias').prop('disabled', true);
      $('#btnImportarCategorias').html('<i class="fas fa-spinner fa-spin"></i> Importando...');

      $.ajax({
        url: '<?php echo site_url('categorias/importarCategoriasML'); ?>',
        type: 'POST',
        data: { categorias: categoriasSelecionadas },
        dataType: 'json',
        success: function(resp) {
          $('#btnImportarCategorias').prop('disabled', false);
          $('#btnImportarCategorias').html('<span class="button__icon"><i class="bx bx-download"></i></span><span class="button__text2">Importar Selecionadas</span>');
          
          if (resp.success) {
            $('#msgCategoria').html('<div class="alert alert-success">' + resp.message + '</div>');
            setTimeout(function() {
              window.location.href = '<?php echo site_url('categorias'); ?>';
            }, 2000);
          } else {
            $('#msgCategoria').html('<div class="alert alert-error">' + resp.message + '</div>');
          }
        },
        error: function() {
          $('#btnImportarCategorias').prop('disabled', false);
          $('#btnImportarCategorias').html('<span class="button__icon"><i class="bx bx-download"></i></span><span class="button__text2">Importar Selecionadas</span>');
          $('#msgCategoria').html('<div class="alert alert-error">Erro ao importar categorias.</div>');
        }
      });
    });
  });
</script> 