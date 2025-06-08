<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/trumbowyg/langs/pt_br.js"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Cadastro de OS</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">

                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes da OS</a></li>
                        
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divCadastrarOs">
                                <?php if ($custom_error == true) { ?>
                                    <div class="span12 alert alert-danger" id="divInfo" style="padding: 1%;">Dados
                                        incompletos, verifique os campos com asterisco ou se selecionou corretamente
                                        cliente, responsável e garantia.<br />Ou se tem um cliente e um termo de garantia
                                        cadastrado.</div>
                                    <?php } ?>
                                <form action="<?php echo current_url(); ?>" method="post" id="formOs">
                                    <div class="span12" style="padding: 1%">
                                        <div class="span5">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id"
                                                value="" />
                                        </div>
                                        <div class="span4">
                                            <label for="tecnico">Técnicos / Responsáveis<span
                                                    class="required">*</span></label>
                                            <div class="">
                                                <input id="tecnico" class="span8" type="text" name="tecnico" value="<?= $this->session->userdata("nome_admin") ?>" />
                                                <div class="span4">
                                                    <button type="button" class="btn span12" style="margin-left: 0" data-toggle="modal" data-target="#modalUsuarios">
                                                        <i class="bx bx-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <input id="usuarios_id" class="span12" type="hidden" name="usuarios_id"
                                                value="<?= $this->session->userdata("id_admin") ?>" />
                                        </div>

                                        <div class="span3">
                                            <label for="status">Status<span class="required">*</span></label>
                                            <select class="span12" name="status" id="status" value="">
                                                <option value="Orçamento">Orçamento</option>
                                                <option value="Aberto">Aberto</option>
                                                <option value="Em Andamento">Em Andamento</option>
                                                <option value="Finalizado">Finalizado</option>
                                                <option value="Cancelado">Cancelado</option>
                                                <option value="Aguardando Peças">Aguardando Peças</option>
                                                <option value="Aprovado">Aprovado</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">

                                        <div class="span3">
                                            <label for="dataInicial">Data Inicial<span class="required">*</span></label>
                                            <input id="dataInicial" autocomplete="off" class="span12 datepicker"
                                                type="text" name="dataInicial" value="<?php echo date(
                                                    "d/m/Y"
                                                ); ?>" />
                                        </div>


                                        <div class="span3">
                                            <label for="dataFinal">Previsão de Entrega<span
                                                    class="required">*</span></label>
                                            <input id="dataFinal" autocomplete="off" class="span12 datepicker"
                                                type="text" name="dataFinal" value="" />
                                        </div>
                                        <div class="span3">
                                            <label for="garantia">Garantia (dias)</label>
                                            <input id="garantia" type="number" placeholder="Status s/g inserir nº/0"
                                                min="0" max="9999" class="span12" name="garantia" value="" />
                                            <?php echo form_error(
                                                "garantia"
                                            ); ?>
                                        </div>

                                        <div class="span3">

                                            <label for="termoGarantia">Termo Garantia</label>
                                            <input id="termoGarantia" class="span12" type="text" name="termoGarantia"
                                                value="" />
                                            <input id="garantias_id" class="span12" type="hidden" name="garantias_id"
                                                value="" />
                                        </div>

                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span3">
                                            <label for="descricaoProduto">PRODUTO</label>
                                            <input name="descricaoProduto" class="span12" type="text" 
                                            
                                                id="descricaoProduto" value="" onChange="javascript:this.value=this.value.toUpperCase();"/>

                                        </div>

                                        <div class="span3">
                                            <label for="marcaProdutoOs">MARCA</label>
                                            <input name="marcaProdutoOs" class="span12" type="text" id="marcaProdutoOs"
                                                value="" onChange="javascript:this.value=this.value.toUpperCase();"/> 

                                        </div>

                                        <div class="span3">
                                            <label for="modeloProdutoOs">MODELO</label>
                                            <input name="modeloProdutoOs" class="span12" type="text"
                                                id="modeloProdutoOs" value="" onChange="javascript:this.value=this.value.toUpperCase();"/>

                                        </div>

                                        <div class="span3">
                                            <label for="nsProdutoOs">NÚMERO DE SÉRIE</label>
                                            <input name="nsProdutoOs" class="span12" type="text" id="nsProdutoOs"
                                                value="" onChange="javascript:this.value=this.value.toUpperCase();"/>

                                        </div>

                                    </div>

                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        
                                        <div class="span3">
                                            <label for="organizador_id">Organizador<span class="required">*</span></label>
                                            <select id="organizador_id" name="organizador_id" class="span12 select2">
                                                <option value="">Buscar organizador...</option>
                                                <?php foreach ($organizadores as $organizador) : ?>
                                                    <option value="<?php echo $organizador->id; ?>">
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
                                            </select>
                                        </div>

                                        <div class="span3">
                                            <label for="defeito">Defeito reclamado pelo cliente</label>
                                            <input name="defeito" class="span12" type="text" id="defeito" value="" />

                                        </div>

                                        <div class="span3">
                                            <label for="analiseBasica">Defeito constatado em pré-análise</label>
                                            <input name="analiseBasica" class="span12" type="text" id="analiseBasica" value="" />

                                        </div>

                                    </div>

                                   

                                    <div class="span12" style="padding: 1%; margin-left: 0">

                                    </div>

                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span6 offset3" style="display:flex">
                                            <button class="button btn btn-success" id="btnContinuar">
                                                <span class="button__icon"><i
                                                        class='bx bx-chevrons-right'></i></span><span
                                                    class="button__text2">Continuar</span></button>
                                            <a href="<?php echo base_url(); ?>index.php/os"
                                                class="button btn btn-mini btn-warning" style="max-width: 160px">
                                                <span class="button__icon"><i class="bx bx-undo"></i></span><span
                                                    class="button__text2">Voltar</span></a>
                                        </div>
                                    </div>

                                    <!-- Campo hidden para usuários adicionais -->
                                    <div id="usuarios_adicionais_container">
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                .
            </div>
        </div>
    </div>
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

    /* Estilos para compartimentos ocupados */
    .ocupado {
        color: red;
        font-weight: bold;
    }

    /* Estilo para o select2 com itens ocupados */
    .select2-results__option.ocupado {
        color: red;
        font-weight: bold;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteCliente",
            minLength: 1,
            select: function (event, ui) {
                $("#clientes_id").val(ui.item.id);
            }
        });
        $("#tecnico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteUsuario",
            minLength: 1,
            select: function (event, ui) {
                $("#usuarios_id").val(ui.item.id);
            }
        });
        $("#termoGarantia").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteTermoGarantia",
            minLength: 1,
            select: function (event, ui) {
                $("#garantias_id").val(ui.item.id);
            }
        });

        // Autocomplete para usuário adicional no modal
        $("#usuarioAdicional").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteUsuario",
            minLength: 1,
            select: function (event, ui) {
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
                linha += '<button type="button" class="btn btn-info btn-fix-user" onclick="fixarUsuario(' + id + ', \'' + nome + '\')" title="Fixar usuário">';
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

        // Função para desfixar usuário
        window.desfixarUsuario = function(id) {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/os/desfixarUsuario',
                type: 'POST',
                data: { usuario_id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.result) {
                        $('#usuario-' + id + ' .btn-fix-user')
                            .removeClass('btn-success')
                            .addClass('btn-info')
                            .attr('title', 'Fixar usuário');
                        
                        atualizarBotaoAdicionar();
                    }
                }
            });
        }

        // Função para remover usuário
        window.removerUsuario = function(id) {
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
        }

        // Atualiza a função fixarUsuario para usar o banco
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

        // Adiciona log antes do envio do formulário
        $("#formOs").on('submit', function(e) {
            console.log('Formulário sendo enviado');
            console.log('Campos hidden de usuários:', $("#usuarios_adicionais_container").html());
            return true;
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
                },
                dataFinal: {
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
                },
                dataFinal: {
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
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });
        $('.editor').trumbowyg({
            lang: 'pt_br'
        });

        // Função para limpar a tabela de usuários
        function limparTabelaUsuarios() {
            $("#tabelaUsuarios tbody").empty();
            $("#usuarios_adicionais_container").empty();
        }

        // Função para carregar usuários fixados
        function carregarUsuariosFixados() {
            window.carregandoUsuariosFixados = true;
            limparTabelaUsuarios();
            
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

        // Carrega os usuários fixados ao iniciar
        carregarUsuariosFixados();

        // Inicializar o Select2 para o campo de organizador
        $('#organizador_id').select2({
            placeholder: "Buscar organizador...",
            allowClear: true
        });

        // Quando um organizador é selecionado
        $('#organizador_id').change(function() {
            var organizador_id = $(this).val();
            var compartimento_select = $('#compartimento_id');
            
            // Limpar o select de compartimentos
            compartimento_select.empty();
            
            if (organizador_id) {
                // Carregar compartimentos via AJAX
                $.ajax({
                    url: '<?php echo site_url('compartimentos/buscarCompartimentos'); ?>',
                    type: 'GET',
                    data: { organizador_id: organizador_id },
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.length > 0) {
                            // Se houver compartimentos, adiciona a opção de selecionar
                            compartimento_select.append('<option value="">Selecione um compartimento</option>');
                            // Adicionar os compartimentos ao select
                            $.each(data, function(index, item) {
                                let optionClass = item.quantidade > 0 ? 'ocupado' : '';
                                let optionText = item.quantidade > 0 ? 
                                    `${item.nome_compartimento} (${item.quantidade})` : 
                                    item.nome_compartimento;
                                
                                compartimento_select.append(
                                    $('<option></option>')
                                        .val(item.id)
                                        .text(optionText)
                                        .addClass(optionClass)
                                );
                            });
                        } else {
                            compartimento_select.append('<option value="">Organizador sem compartimentos</option>');
                        }
                    },
                    error: function() {
                        compartimento_select.append('<option value="">Erro ao carregar compartimentos</option>');
                    }
                });
            } else {
                compartimento_select.append('<option value="">Selecione primeiro um organizador</option>');
            }
        });

        // Inicializar o Select2 com template personalizado
        $('#compartimento_id').select2({
            templateResult: formatCompartimento,
            templateSelection: formatCompartimento
        });

        // Função para formatar a exibição do compartimento no Select2
        function formatCompartimento(compartimento) {
            if (!compartimento.id) return compartimento.text;
            
            let $compartimento = $(
                '<span class="' + $(compartimento.element).attr('class') + '">' + 
                compartimento.text + 
                '</span>'
            );
            
            return $compartimento;
        }

        // Quando um compartimento é selecionado, validar capacidade
        $('#compartimento_id').change(function() {
            var compartimento_id = $(this).val();
            if (compartimento_id) {
                $.ajax({
                    url: '<?php echo site_url('compartimentos/validarCapacidadeCompartimento'); ?>',
                    type: 'POST',
                    data: { compartimento_id: compartimento_id },
                    dataType: 'json',
                    success: function(response) {
                        if (!response.valido) {
                            Swal.fire({
                                title: 'Atenção!',
                                text: 'Este compartimento está com alta ocupação. Deseja continuar mesmo assim?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Sim, continuar',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (!result.isConfirmed) {
                                    $('#compartimento_id').val('').trigger('change');
                                }
                            });
                        }
                    }
                });
            }
        });
    });

</script>




