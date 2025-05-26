<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Adicionar Carteira</title>
    <style>
        .form-group { margin-bottom: 15px; }
        .bonus-commission-buttons { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Adicionar Nova Carteira</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open('carteira/salvar'); ?>
                            <div class="form-group">
                                <label for="nomeCarteira">Nome da Carteira</label>
                                <input type="text" class="form-control" name="nomeCarteira" id="nomeCarteira" required>
                            </div>

                            <div class="form-group">
                                <label for="usuario">Usuário</label>
                                <select name="usuario" id="usuario" class="form-control" required>
                                    <option value="">Selecione um usuário</option>
                                    <?php if(isset($usuarios)): ?>
                                        <?php foreach($usuarios as $usuario): ?>
                                            <option value="<?php echo $usuario->idUsuarios; ?>">
                                                <?php echo $usuario->nome; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="salario">Salário Base</label>
                                <div class="input-group">
                                    <span class="input-group-addon">R$</span>
                                    <input type="number" step="0.01" class="form-control" name="salario" id="salario" required>
                                </div>
                            </div>

                            <div class="row bonus-commission-buttons">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#modalBonus">
                                        <i class="fa fa-plus"></i> Adicionar Bônus
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#modalComissao">
                                        <i class="fa fa-plus"></i> Adicionar Comissão
                                    </button>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 20px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Salvar Carteira
                                </button>
                                <a href="<?php echo base_url('carteira'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Voltar
                                </a>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bônus -->
    <div class="modal fade" id="modalBonus" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Adicionar Bônus</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="valorBonus">Valor do Bônus</label>
                        <div class="input-group">
                            <span class="input-group-addon">R$</span>
                            <input type="number" step="0.01" class="form-control" id="valorBonus" name="valorBonus">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descricaoBonus">Descrição</label>
                        <textarea class="form-control" id="descricaoBonus" name="descricaoBonus" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-success" onclick="adicionarBonus()">Adicionar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Comissão -->
    <div class="modal fade" id="modalComissao" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Adicionar Comissão</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="valorComissao">Valor da Comissão</label>
                        <div class="input-group">
                            <span class="input-group-addon">R$</span>
                            <input type="number" step="0.01" class="form-control" id="valorComissao" name="valorComissao">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descricaoComissao">Descrição</label>
                        <textarea class="form-control" id="descricaoComissao" name="descricaoComissao" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-info" onclick="adicionarComissao()">Adicionar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adicionarBonus() {
            // Aqui você pode implementar a lógica para adicionar o bônus via AJAX
            var valor = document.getElementById('valorBonus').value;
            var descricao = document.getElementById('descricaoBonus').value;
            
            // Exemplo de chamada AJAX (você precisará implementar no controller)
            $.ajax({
                url: '<?php echo base_url("carteira/adicionarBonus"); ?>',
                type: 'POST',
                data: {
                    valor: valor,
                    descricao: descricao,
                    carteira_id: carteiraId // Você precisará definir esta variável
                },
                success: function(response) {
                    alert('Bônus adicionado com sucesso!');
                    $('#modalBonus').modal('hide');
                },
                error: function() {
                    alert('Erro ao adicionar bônus');
                }
            });
        }

        function adicionarComissao() {
            // Aqui você pode implementar a lógica para adicionar a comissão via AJAX
            var valor = document.getElementById('valorComissao').value;
            var descricao = document.getElementById('descricaoComissao').value;
            
            // Exemplo de chamada AJAX (você precisará implementar no controller)
            $.ajax({
                url: '<?php echo base_url("carteira/adicionarComissao"); ?>',
                type: 'POST',
                data: {
                    valor: valor,
                    descricao: descricao,
                    carteira_id: carteiraId // Você precisará definir esta variável
                },
                success: function(response) {
                    alert('Comissão adicionada com sucesso!');
                    $('#modalComissao').modal('hide');
                },
                error: function() {
                    alert('Erro ao adicionar comissão');
                }
            });
        }
    </script>
</body>
</html>
