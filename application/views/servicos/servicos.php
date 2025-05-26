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
      <i class="fas fa-wrench"></i>
    </span>
    <h5>Serviços</h5>
  </div>
  <div class="span12" style="margin-left: 0">
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aServico')) : ?>
      <div class="span3 flexxn" style="display: flex;">
        <a href="<?= base_url() ?>index.php/servicos/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
          <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2"> Serviços</span>
        </a>
      </div>
    <?php endif; ?>
    <form class="span9" method="get" action="<?= base_url() ?>index.php/servicos" style="display: flex; justify-content: flex-end;">
      <div class="span3">
        <input type="text" name="pesquisa" id="pesquisa" placeholder="Buscar por Nome ou Descrição..." class="span12" value="<?= $this->input->get('pesquisa') ?>">
      </div>
      <div class="span1">
        <button class="button btn btn-mini btn-warning" style="min-width: 30px">
          <span class="button__icon"><i class='bx bx-search-alt'></i></span>
        </button>
      </div>
    </form>
  </div>
  <div class="widget-box">
    <h5 style="padding: 11px 0"></h5>
    <div class="widget-content nopadding tab-content">
      <table id="tabela" class="table table-bordered">
        <thead>
          <tr>
            <th>Cod.</th>
            <th>Nome</th>
            <th>Preço</th>
            <th>Descrição</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (!$results) {
            echo '<tr>
                    <td colspan="5">Nenhum Serviço Cadastrado</td>
                  </tr>';
          }
          foreach ($results as $r) {
            echo '<tr>';
            echo '<td data-label="Cod.">' . $r->idServicos . '</td>';
            echo '<td data-label="Nome">' . $r->nome . '</td>';
            echo '<td data-label="Preço">R$ ' . number_format($r->preco, 2, ',', '.') . '</td>';
            echo '<td data-label="Descrição">' . $r->descricao . '</td>';
            echo '<td data-label="Ações">';
            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eServico')) {
              echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/servicos/editar/' . $r->idServicos . '" class="btn-nwe3" title="Editar Serviço"><i class="bx bx-edit bx-xs"></i></a>';
            }
            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dServico')) {
              echo '<a href="#modal-excluir" role="button" data-toggle="modal" servico="' . $r->idServicos . '" class="btn-nwe4" title="Excluir Serviço"><i class="bx bx-trash-alt bx-xs"></i></a>  ';
            }
            echo '</td>';
            echo '</tr>';
          } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form action="<?php echo base_url() ?>index.php/servicos/excluir" method="post">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h5 id="myModalLabel">Excluir Serviço</h5>
    </div>
    <div class="modal-body">
      <input type="hidden" id="idServico" name="id" value="" />
      <h5 style="text-align: center">Deseja realmente excluir este serviço?</h5>
    </div>
    <div class="modal-footer" style="display:flex;justify-content: center">
      <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
        <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span>
      </button>
      <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
    </div>
  </form>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $(document).on('click', 'a', function(event) {
      var servico = $(this).attr('servico');
      $('#idServico').val(servico);
    });
  });
</script>