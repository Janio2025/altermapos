<div class="widget-title">
  <span class="icon"><i class="fas fa-tags"></i></span>
  <h5>Editar Categoria</h5>
</div>
<div class="widget-content nopadding tab-content">
  <form action="<?php echo site_url('categorias/editar/' . $categoria->idCategorias); ?>" method="post" class="form-horizontal">
    <div class="control-group">
      <label class="control-label">Nome da Categoria</label>
      <div class="controls">
        <input type="text" name="categoria" class="span6" value="<?php echo $categoria->categoria; ?>" required />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">ML ID (Mercado Livre)</label>
      <div class="controls">
        <input type="text" name="ml_id" class="span4" value="<?php echo $categoria->ml_id; ?>" placeholder="Ex: MLB1182" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">Tipo</label>
      <div class="controls">
        <select name="tipo_id" class="span3">
          <option value="">Selecione um tipo...</option>
          <?php foreach ($tipos_existentes as $tipo): ?>
            <option value="<?php echo $tipo->id; ?>" <?php echo ($categoria->tipo_id == $tipo->id) ? 'selected' : ''; ?>>
              <?php echo $tipo->nome; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">Categoria Pai</label>
      <div class="controls">
        <select name="parent_id" class="span4">
          <option value="">Nenhuma (Categoria Raiz)</option>
          <?php foreach ($categorias as $cat) : ?>
            <option value="<?php echo $cat->idCategorias; ?>" <?php if ($categoria->parent_id == $cat->idCategorias) echo 'selected'; ?>><?php echo $cat->categoria; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">Subcategorias</label>
      <div class="controls">
        <select id="subcategoriasSelect" class="span6">
          <option value="">Selecione uma subcategoria</option>
          <!-- opções serão carregadas via JS -->
        </select>
        <button type="button" id="btnIrSub" class="btn btn-info" style="margin-left:10px"><i class="bx bx-chevron-right"></i> Ir</button>
        <button type="button" id="btnAddSub" class="btn btn-success" style="margin-left:10px"><i class="bx bx-plus"></i> Adicionar Subcategoria</button>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
      <a href="<?php echo site_url('categorias'); ?>" class="btn btn-warning">Voltar</a>
      <button type="button" class="btn btn-danger" id="btnExcluirCat" style="float:right"><i class="fas fa-trash-alt"></i> Excluir</button>
    </div>
  </form>
</div>
<!-- Modal Excluir Categoria -->
<div id="modal-excluir-categoria" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form action="<?php echo base_url() ?>index.php/categorias/deletar" method="post">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h5 id="myModalLabel"><i class="fas fa-trash-alt"></i> Excluir Categoria</h5>
    </div>
    <div class="modal-body">
      <input type="hidden" id="idCategoriaExcluir" name="id" value="<?php echo $categoria->idCategorias; ?>" />
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
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  // Carregar subcategorias ao abrir a tela
  function carregarSubcategorias(parent_id, selected_id) {
    $.get('<?php echo site_url('categorias/getSubcategoriasAjax'); ?>', {parent_id: parent_id}, function(data) {
      var options = '<option value="">Selecione uma subcategoria</option>';
      var subcats = JSON.parse(data);
      for (var i=0; i<subcats.length; i++) {
        options += '<option value="'+subcats[i].id+'"'+(selected_id==subcats[i].id?' selected':'')+'>'+subcats[i].categoria+'</option>';
      }
      $('#subcategoriasSelect').html(options);
    });
  }
  var current_id = '<?php echo $categoria->idCategorias; ?>';
  carregarSubcategorias(current_id);

  // Ao clicar em Ir, recarrega a tela para editar a subcategoria
  $('#btnIrSub').click(function() {
    var sub_id = $('#subcategoriasSelect').val();
    if (sub_id) {
      window.location.href = '<?php echo site_url('categorias/editar/'); ?>'+sub_id;
    }
  });

  // Ao clicar em Adicionar Subcategoria, vai para o cadastro já com parent_id preenchido
  $('#btnAddSub').click(function() {
    var sub_id = $('#subcategoriasSelect').val();
    var parent_id = sub_id ? sub_id : current_id;
    window.location.href = '<?php echo site_url('categorias/adicionar'); ?>?parent_id='+parent_id;
  });

  $('#btnExcluirCat').click(function() {
    $('#modal-excluir-categoria').modal('show');
  });
});
</script> 