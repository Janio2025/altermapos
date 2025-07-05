<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon">
      <i class="fas fa-tags"></i>
    </span>
    <h5>Adicionar Categoria</h5>
  </div>
  <div class="widget-box">
    <div class="widget-content nopadding tab-content">
      


      <!-- Formulário de cadastro local -->
      <form action="<?php echo site_url('categorias/adicionar'); ?>" method="post" class="form-horizontal" id="formCategoria">
        <div class="control-group">
          <label class="control-label">Nome da Categoria</label>
          <div class="controls">
            <input type="text" name="categoria" class="span6" required />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Tipo</label>
          <div class="controls">
            <select name="tipo" id="tipoSelect" class="span3" style="margin-right: 10px;">
              <option value="">Selecione um tipo...</option>
              <?php if (isset($tipos_existentes) && $tipos_existentes): ?>
                <?php foreach ($tipos_existentes as $tipo): ?>
                  <option value="<?php echo $tipo->tipo; ?>"><?php echo ucfirst(str_replace('_', ' ', $tipo->tipo)); ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
            <input type="text" name="tipo_novo" id="tipoNovo" class="span3" placeholder="Ou digite um novo tipo" style="display: none;" />
            <button type="button" id="btnNovoTipo" class="btn btn-mini btn-info" style="margin-left: 5px;">
              <i class="bx bx-plus"></i> Novo Tipo
            </button>
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

      <!-- Interface de importação ML removida -->
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

    // Controle do campo tipo
    $('#btnNovoTipo').click(function() {
      if ($('#tipoNovo').is(':visible')) {
        // Se o campo novo está visível, voltar para o select
        $('#tipoNovo').hide();
        $('#tipoSelect').show();
        $('#btnNovoTipo').html('<i class="bx bx-plus"></i> Novo Tipo');
        $('#tipoNovo').val('');
      } else {
        // Se o select está visível, mostrar campo novo
        $('#tipoSelect').hide();
        $('#tipoNovo').show();
        $('#btnNovoTipo').html('<i class="bx bx-arrow-back"></i> Usar Existente');
        $('#tipoNovo').focus();
      }
    });

    // Quando selecionar um tipo no select, limpar o campo novo
    $('#tipoSelect').change(function() {
      if ($(this).val()) {
        $('#tipoNovo').val('');
      }
    });

    // Quando digitar no campo novo, limpar o select
    $('#tipoNovo').on('input', function() {
      $('#tipoSelect').val('');
    });

    // Scripts de importação ML removidos
  });
</script> 