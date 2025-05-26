<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="bx bxs-wallet"></i>
        </span>
        <h5>Editar Carteira</h5>
    </div>

    <div class="widget-box">
        <div class="widget-content nopadding tab-content">
            <div id="tab1" class="tab-pane active" style="min-height: 300px">
                <div class="widget_box_Painel2">
                    <?php if ($this->session->flashdata('error') != null) { ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php } ?>
                    <?php if ($this->session->flashdata('success') != null) { ?>
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                    <?php } ?>

                    <form id="formCarteira" action="<?php echo base_url() ?>index.php/admincarteira/atualizar" id="formCarteira" method="post" class="form-horizontal" >
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                        <input type="hidden" name="idCarteiraUsuario" value="<?php echo $carteira->idCarteiraUsuario; ?>" />
                        
                        <div class="control-group">
                            <label for="usuario" class="control-label">Usuário<span class="required">*</span></label>
                            <div class="controls">
                                <input type="text" class="input-xlarge" value="<?php echo $carteira->nome; ?>" readonly>
                                <input type="hidden" name="usuario" id="usuario" value="<?php echo $carteira->usuarios_id; ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="chave_pix" class="control-label">Chave Pix<span class="required">*</span></label>
                            <div class="controls">
                                <input type="text" class="input-xlarge" name="chave_pix" id="chave_pix" placeholder="Digite a chave PIX (CPF, Email, Telefone ou Chave Aleatória)" value="<?php echo isset($config) ? $config->chave_pix : ''; ?>">
                            </div>
                        </div>

                        <!-- Configurações de Pagamento Automático -->
                        <div class="control-group">
                            <label for="pagamento_automatico" class="control-label">Pagamento Automático</label>
                            <div class="controls">
                                <div class="switch">
                                    <input type="checkbox" id="pagamento_automatico" name="pagamento_automatico" value="1" <?php echo (isset($config) && $config->pagamento_automatico) ? 'checked' : ''; ?>>
                                    <label for="pagamento_automatico"></label>
                                </div>
                            </div>
                        </div>

                        <div class="control-group" id="config_pagamento_automatico" style="display: <?php echo (isset($config) && $config->pagamento_automatico) ? 'block' : 'none'; ?>">
                            <label for="data_salario" class="control-label">Data do Pagamento<span class="required">*</span></label>
                            <div class="controls">
                                <input type="datetime-local" class="input-xlarge" name="data_salario" id="data_salario" value="<?php echo isset($config) ? date('Y-m-d\TH:i', strtotime($config->data_salario)) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="control-group" id="info_pagamento_automatico" style="display: <?php echo (isset($config) && $config->pagamento_automatico) ? 'block' : 'none'; ?>">
                            <label class="control-label">Informações do Pagamento</label>
                            <div class="controls">
                                <p>Último pagamento: <?php echo isset($config) && $config->ultima_data_pagamento ? date('d/m/Y H:i', strtotime($config->ultima_data_pagamento)) : 'Nenhum'; ?></p>
                                <p>Próximo pagamento: <?php echo isset($config) && $config->proximo_pagamento ? date('d/m/Y H:i', strtotime($config->proximo_pagamento)) : 'Nenhum'; ?></p>
                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) { ?>
                                    <button type="button" id="verificarPagamento" class="btn btn-success" style="margin-top: 10px;">
                                        <i class="fas fa-sync"></i> Verificar Pagamento
                                    </button>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Configurações de Pagamento -->
                        <div class="control-group">
                            <label class="control-label">Configurações de Pagamento</label>
                            <div class="controls">
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" name="salario_base" id="salario_base" placeholder="Salário Base" value="<?php echo isset($config) ? number_format($config->salario_base, 2, ',', '.') : ''; ?>">
                                </div>

                               
                                
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">%</span>
                                    <input type="number" step="0.01" min="0" max="100" name="comissao_fixa" id="comissao_fixa" placeholder="Comissão Fixa (%)" value="<?php echo isset($config) ? $config->comissao_fixa : ''; ?>">
                                </div>
                                
                                <div style="margin-bottom: 10px;">
                                    <label class="radio" style="display: inline-block; margin-right: 15px;">
                                        <input type="radio" name="tipo_repeticao" value="mensal" <?php echo (!isset($config) || $config->tipo_repeticao == 'mensal') ? 'checked' : ''; ?>> Mensal
                                    </label>
                                    <label class="radio" style="display: inline-block;">
                                        <input type="radio" name="tipo_repeticao" value="quinzenal" <?php echo (isset($config) && $config->tipo_repeticao == 'quinzenal') ? 'checked' : ''; ?>> Quinzenal
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Widget de Comissão Pendente -->
                        <div class="control-group">
                            <label class="control-label">Comissão Pendente</label>
                            <div class="controls">
                                <div class="widget-box span6" style="margin-bottom: 0;">
                                    <div class="widget-content">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 15px; padding: 15px;">
                                            <div class="comissao-value" style="font-size: 28px; color: #ffc107;">
                                                R$ <span id="comissao-pendente">0,00</span>
                                            </div>
                                            <div class="alert alert-info" style="margin-bottom: 0;">
                                                <i class='bx bx-info-circle'></i>
                                                Para realizar retiradas ou receber comissões, acesse a opção "Pagar Usuário" na listagem de carteiras.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção de Bônus -->
                        <div class="control-group">
                            <label class="control-label">Outros Valores</label>
                            <div class="controls">
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" name="bonus_valor" id="bonus_valor" placeholder="Dividendos ou Bônus">
                                </div>
                                
                                <div style="margin-top: 10px;">
                                    <input type="text" class="span6" name="bonus_descricao" placeholder="Descrição do Bônus" onChange="javascript:this.value=this.value.toUpperCase();">
                                </div>
                            </div>
                        </div>

                        <!-- Seção de Comissão -->
                        <div class="control-group">
                            <label class="control-label">Comissão Adicional</label>
                            <div class="controls">
                                <div style="margin-bottom: 10px;">
                                    <select name="tipo_valor_base" id="tipo_valor_base" class="input-xlarge">
                                        <option value="">Selecione o tipo de valor base</option>
                                        <option value="servicos" <?php echo (isset($config) && $config->tipo_valor_base == 'servicos') ? 'selected' : ''; ?>>Serviços</option>
                                        <option value="total" <?php echo (isset($config) && $config->tipo_valor_base == 'total') ? 'selected' : ''; ?>>Produtos e Serviços</option>
                                    </select>
                                </div>

                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" name="comissao_base" id="comissao_base" placeholder="Valor Base da Comissão" readonly>
                                </div>
                                
                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <input type="number" name="comissao_porcentagem" id="comissao_porcentagem" placeholder="Porcentagem" min="0" max="100" class="input-mini">
                                    <span class="add-on">%</span>
                                </div>

                                <div class="input-prepend" style="margin-bottom: 10px;">
                                    <span class="add-on">R$</span>
                                    <input type="text" class="money" name="comissao_valor" id="comissao_valor" placeholder="Valor da Comissão" readonly>
                                </div>
                                
                                <div style="margin-top: 10px;">
                                    <input type="text" class="span6" name="comissao_descricao" placeholder="Descrição da Comissão">
                                </div>
                            </div>
                        </div>

                        <!-- Campo para mostrar o total -->
                        <div class="control-group">
                            <label class="control-label">Total</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    <span class="add-on">R$</span>
                                    <input type="text" id="total" name="total" class="money" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>" readonly>
                                    <input type="hidden" id="saldo_original" name="saldo_original" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Campos ocultos para armazenar as transações -->
                        <input type="hidden" name="tem_bonus" id="tem_bonus" value="0">
                        <input type="hidden" name="tem_comissao" id="tem_comissao" value="0">

                        

                        <div class="form-actions">
                            <div class="span12">
                                <div class="span6 offset3" style="display:flex;justify-content: center">
                                    <button type="submit" class="button btn btn-primary">
                                        <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Atualizar</span>
                                    </button>
                                    <a href="<?php echo base_url('index.php/admincarteira'); ?>" class="button btn btn-mini btn-warning">
                                        <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    // Função para converter valor do formato brasileiro para número
    function parseMoneyBR(value) {
        if (!value) return 0;
        return parseFloat(value.replace('.', '').replace(',', '.'));
    }

    // Função para formatar número para dinheiro BR
    function formatMoneyBR(value) {
        return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Função para calcular o total
    function calcularTotal() {
        // Pega os valores dos campos
        let saldoOriginal = parseMoneyBR($('#saldo_original').val());
        let bonus = parseMoneyBR($('#bonus_valor').val());
        
        // Marca quais transações serão registradas
        $('#tem_bonus').val(bonus > 0 ? '1' : '0');
        
        // Calcula o total (saldo original + bonus)
        let total = saldoOriginal + bonus;
        
        // Atualiza o campo total
        $('#total').val(formatMoneyBR(total));
    }

    $(document).ready(function(){
        // Máscara para campos de dinheiro
        $('.money').maskMoney({
            prefix: '',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });

        // Aplica a máscara em tempo real durante a digitação para o campo de bônus
        $('#bonus_valor').on('input', function() {
            $(this).maskMoney('mask');
        });

        // Eventos para recalcular os valores
        $('#salario_base').on('keyup', calcularTotal);
        $('#bonus_valor').on('keyup', calcularTotal);

        // Função para buscar o valor base da comissão
        function buscarValorBase() {
            let tipoValorBase = $('#tipo_valor_base').val();
            let usuarioId = $('#usuario').val();
            let comissaoFixa = parseFloat($('#comissao_fixa').val()) || 0;
            
            // Primeiro, copiamos o valor da comissão fixa para o campo de porcentagem
            $('#comissao_porcentagem').val(comissaoFixa);
            
            console.log('Tipo selecionado:', tipoValorBase);
            console.log('ID do usuário:', usuarioId);
            
            if (tipoValorBase && usuarioId) {
                // Constrói a URL completa
                let baseUrl = '<?php echo base_url(); ?>';
                let apiUrl = baseUrl + 'index.php/admincarteira/getValorBase';
                console.log('URL da API:', apiUrl);

                $.ajax({
                    url: apiUrl,
                    type: 'POST',
                    data: {
                        tipo: tipoValorBase,
                        usuario_id: usuarioId,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Resposta da API:', response);
                        
                        if (response.success) {
                            let valor = parseFloat(response.valor) || 0;
                            console.log('Valor base recebido:', valor);
                            
                            // Formata o valor para exibição no campo base
                            let valorFormatado = formatMoneyBR(valor);
                            console.log('Valor formatado:', valorFormatado);
                            $('#comissao_base').val(valorFormatado);
                            
                            // Calcula o valor da comissão usando a comissão fixa
                            let valorComissao = valor * (comissaoFixa / 100);
                            console.log('Valor da comissão calculado:', valorComissao);
                            
                            // Atualiza os campos com os valores calculados
                            $('#comissao_valor').val(formatMoneyBR(valorComissao));
                            $('#tem_comissao').val(valorComissao > 0 ? '1' : '0');
                            $('#comissao-pendente').text(formatMoneyBR(valorComissao));
                            
                            // Força o recálculo do total
                            calcularTotal();
                        } else {
                            console.log('Erro ao buscar valor base:', response.message);
                            // Limpa os campos em caso de erro
                            $('#comissao_base').val('0,00');
                            $('#comissao_valor').val('0,00');
                            $('#comissao-pendente').text('0,00');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Erro na requisição:', error);
                        console.log('Status:', status);
                        console.log('URL tentada:', apiUrl);
                        console.log('Resposta:', xhr.responseText);
                        // Limpa os campos em caso de erro
                        $('#comissao_base').val('0,00');
                        $('#comissao_valor').val('0,00');
                        $('#comissao-pendente').text('0,00');
                    }
                });
            } else {
                console.log('Tipo ou usuário não selecionado');
                // Limpa os campos se não tiver tipo ou usuário selecionado
                $('#comissao_base').val('0,00');
                $('#comissao_valor').val('0,00');
                $('#comissao-pendente').text('0,00');
            }
        }

        // Eventos para recalcular os valores
        $('#tipo_valor_base').on('change', function() {
            console.log('Tipo alterado para:', $(this).val());
            buscarValorBase();
            localStorage.setItem('tipo_valor_base', $(this).val());
        });

        // Quando a comissão fixa mudar, atualizar o campo de porcentagem e recalcular
        $('#comissao_fixa').on('change keyup', function() {
            console.log('Comissão fixa alterada para:', $(this).val());
            buscarValorBase();
        });

        // Torna o campo de porcentagem somente leitura
        $('#comissao_porcentagem').prop('readonly', true);

        // Inicializa os valores quando a página carrega
        if ($('#tipo_valor_base').val() && $('#usuario').val()) {
            console.log('Inicializando valores...');
            buscarValorBase();
        }

        // Carrega a seleção anterior do tipo de valor base e força a atualização
        let tipoSalvo = localStorage.getItem('tipo_valor_base');
        if (tipoSalvo) {
            console.log('Carregando tipo salvo:', tipoSalvo);
            $('#tipo_valor_base').val(tipoSalvo).trigger('change');
        }

        // Função para calcular a comissão de um usuário adicional
        function calcularComissaoUsuarioAdicional(usuarioId, tipoValorBase, elementoBase) {
            if (tipoValorBase && usuarioId) {
                $.ajax({
                    url: '<?php echo base_url('index.php/admincarteira/getValorBase'); ?>',
                    type: 'POST',
                    data: {
                        tipo: tipoValorBase,
                        usuario_id: usuarioId,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let valor = parseFloat(response.valor) || 0;
                            // Atualiza o valor base no elemento correspondente
                            $(elementoBase).val(formatMoneyBR(valor));
                            
                            // Calcula a comissão para este usuário adicional
                            let porcentagem = parseFloat($(elementoBase).closest('.usuario-adicional').find('.comissao-porcentagem').val() || 0);
                            if (!isNaN(valor) && !isNaN(porcentagem)) {
                                let valorComissao = (valor * (porcentagem / 100));
                                $(elementoBase).closest('.usuario-adicional').find('.comissao-valor').val(formatMoneyBR(valorComissao));
                            }
                        } else {
                            console.log('Erro ao buscar valor base:', response.message);
                            // Limpa os campos em caso de erro
                            $(elementoBase).val('0,00');
                            $(elementoBase).closest('.usuario-adicional').find('.comissao-valor').val('0,00');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Erro na requisição:', error);
                        // Limpa os campos em caso de erro
                        $(elementoBase).val('0,00');
                        $(elementoBase).closest('.usuario-adicional').find('.comissao-valor').val('0,00');
                    }
                });
            } else {
                // Se não tiver tipo ou usuário, limpa os campos
                $(elementoBase).val('0,00');
                $(elementoBase).closest('.usuario-adicional').find('.comissao-valor').val('0,00');
            }
        }

        // Evento para quando mudar o tipo de valor base de um usuário adicional
        $(document).on('change', '.tipo-valor-base-adicional', function() {
            let usuarioId = $(this).closest('.usuario-adicional').find('.usuario-id').val();
            let tipoValorBase = $(this).val();
            let elementoBase = $(this).closest('.usuario-adicional').find('.comissao-base');
            
            calcularComissaoUsuarioAdicional(usuarioId, tipoValorBase, elementoBase);
        });

        // Evento para quando mudar a porcentagem de comissão de um usuário adicional
        $(document).on('change keyup', '.comissao-porcentagem', function() {
            let usuarioId = $(this).closest('.usuario-adicional').find('.usuario-id').val();
            let tipoValorBase = $(this).closest('.usuario-adicional').find('.tipo-valor-base-adicional').val();
            let elementoBase = $(this).closest('.usuario-adicional').find('.comissao-base');
            
            calcularComissaoUsuarioAdicional(usuarioId, tipoValorBase, elementoBase);
        });

        // Atualiza os valores base dos usuários adicionais periodicamente
        function atualizarValoresBaseUsuariosAdicionais() {
            $('.usuario-adicional').each(function() {
                let usuarioId = $(this).find('.usuario-id').val();
                let tipoValorBase = $(this).find('.tipo-valor-base-adicional').val();
                let elementoBase = $(this).find('.comissao-base');
                
                if (tipoValorBase && usuarioId) {
                    calcularComissaoUsuarioAdicional(usuarioId, tipoValorBase, elementoBase);
                }
            });
        }

        // Verifica por atualizações a cada 30 segundos para usuários adicionais
        setInterval(atualizarValoresBaseUsuariosAdicionais, 30000);

        // Inicializa os valores base para usuários adicionais
        $('.usuario-adicional').each(function() {
            let usuarioId = $(this).find('.usuario-id').val();
            let tipoValorBase = $(this).find('.tipo-valor-base-adicional').val();
            let elementoBase = $(this).find('.comissao-base');
            
            if (tipoValorBase && usuarioId) {
                calcularComissaoUsuarioAdicional(usuarioId, tipoValorBase, elementoBase);
            }
        });

        // Validação do formulário
        $('#formCarteira').validate({
            rules: {
                usuario: {required: true},
                data_salario: {required: true}
            },
            messages: {
                usuario: {required: 'Campo obrigatório'},
                data_salario: {required: 'Campo obrigatório'}
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            },
            submitHandler: function(form) {
                // Verifica se há valores negativos
                let total = parseMoneyBR($('#total').val());
                if (total < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'O valor total não pode ser negativo!'
                    });
                    return false;
                }
                form.submit();
            }
        });

        // Calcula o total inicial
        calcularTotal();

        // Controle do pagamento automático
        $('#pagamento_automatico').change(function() {
            if ($(this).is(':checked')) {
                $('#config_pagamento_automatico, #info_pagamento_automatico').show();
            } else {
                $('#config_pagamento_automatico, #info_pagamento_automatico').hide();
            }
        });

        // Verificar pagamento automático
        $('#verificarPagamento').on('click', function() {
            Swal.fire({
                title: 'Verificar Pagamentos Automáticos',
                html: `
                    <div style="text-align: left;">
                        <p><i class="bx bx-info-circle" style="color: #17a2b8;"></i> Esta ação irá:</p>
                        <ul style="list-style: none; padding-left: 20px;">
                            <li><i class="bx bx-check" style="color: #28a745;"></i> Verificar pagamentos pendentes</li>
                            <li><i class="bx bx-check" style="color: #28a745;"></i> Processar pagamentos automáticos</li>
                            <li><i class="bx bx-check" style="color: #28a745;"></i> Atualizar datas de próximos pagamentos</li>
                        </ul>
                        <p style="margin-top: 15px; color: #dc3545;"><i class="bx bx-error-circle"></i> Deseja continuar?</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: '<i class="bx bx-check"></i> Sim, verificar',
                cancelButtonText: '<i class="bx bx-x"></i> Cancelar',
                customClass: {
                    popup: 'swal2-popup-custom',
                    title: 'swal2-title-custom',
                    htmlContainer: 'swal2-html-container-custom',
                    confirmButton: 'swal2-confirm-button-custom',
                    cancelButton: 'swal2-cancel-button-custom'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?php echo base_url() ?>index.php/admincarteira/verificarPagamentosAutomaticos',
                        type: 'POST',
                        data: {
                            carteira_id: '<?php echo $carteira->idCarteiraUsuario; ?>',
                            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso!',
                                    text: response.message,
                                    confirmButtonColor: '#28a745',
                                    confirmButtonText: '<i class="bx bx-check"></i> OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro!',
                                    text: response.message,
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: '<i class="bx bx-x"></i> OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Erro ao verificar pagamentos automáticos.',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: '<i class="bx bx-x"></i> OK'
                            });
                        }
                    });
                }
            });
        });

        // Configuração do datetimepicker
        $('.datetimepicker').datetimepicker({
            format: 'd/m/Y H:i',
            lang: 'pt-BR',
            step: 30,
            validateOnBlur: false,
            allowTimes: [
                '00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30',
                '04:00', '04:30', '05:00', '05:30', '06:00', '06:30', '07:00', '07:30',
                '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
                '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
                '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30',
                '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30'
            ]
        });

        // Configuração do select2
        $('.select2').select2({
            width: '100%'
        });

        // Função para formatar valores monetários
        function formatMoney(value) {
            return value.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Atualiza o total quando o salário base ou comissão mudar
        function atualizarTotal() {
            let salario_base = parseMoneyBR($('#salario_base').val());
            let comissao = parseMoneyBR($('#comissao_fixa').val());
            let total = salario_base + comissao;
            $('#total').val(formatMoney(total));
        }

        $('#salario_base, #comissao_fixa').on('input', function() {
            atualizarTotal();
        });

        // Configuração do botão de buscar valor base
        $('#buscarValorBase').click(function() {
            $.ajax({
                url: '<?php echo base_url() ?>index.php/admincarteira/buscarValorBase',
                type: 'POST',
                data: {
                    carteira_id: '<?php echo $carteira->idCarteiraUsuario; ?>',
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#valor_base').val(response.valor_base);
                        $('#valor_comissao').val(response.valor_comissao);
                        $('#descricao_comissao').val(response.descricao);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao buscar valor base.'
                    });
                }
            });
        });
    });

    function adicionarUsuario() {
        // Clona o template
        var template = $('#template-usuario-adicional').html();
        $('#usuarios_adicionais').append(template);

        // Inicializa os campos money do novo usuário
        $('#usuarios_adicionais .money').maskMoney({
            prefix: '',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });
    }

    function removerUsuario(button) {
        $(button).closest('.usuario-adicional').remove();
    }

    function atualizarUsuarioId(select) {
        var usuarioId = $(select).val();
        $(select).closest('.usuario-adicional').find('.usuario-id').val(usuarioId);
        
        // Limpa os campos de comissão
        var container = $(select).closest('.usuario-adicional');
        container.find('.tipo-valor-base-adicional').val('');
        container.find('.comissao-base').val('0,00');
        container.find('.comissao-porcentagem').val('');
        container.find('.comissao-valor').val('0,00');
    }
</script>

<style>
    /* Estilos para o modal do SweetAlert2 */
    .swal2-popup-custom {
        border-radius: 8px;
        padding: 2rem;
    }

    .swal2-title-custom {
        color: #2D335B;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .swal2-html-container-custom {
        font-size: 1rem;
        color: #666;
        line-height: 1.5;
    }

    .swal2-html-container-custom ul {
        margin: 1rem 0;
    }

    .swal2-html-container-custom li {
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .swal2-html-container-custom i {
        font-size: 1.2rem;
    }

    .swal2-confirm-button-custom {
        padding: 0.5rem 1.5rem !important;
        font-size: 1rem !important;
        border-radius: 4px !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }

    .swal2-cancel-button-custom {
        padding: 0.5rem 1.5rem !important;
        font-size: 1rem !important;
        border-radius: 4px !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }

    .swal2-actions {
        gap: 1rem !important;
    }
</style>


