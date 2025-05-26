<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid" style="margin: 10px;">
    <div class="span12" style="max-width: 1200px; margin-left: 0;">
        <div class="widget-box" style="margin-bottom: 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div class="widget-title" style="background: #f8f9fa; border-radius: 8px 8px 0 0; padding: 10px 15px; border-bottom: 1px solid #e9ecef;">
                <span class="icon" style="margin-right: 10px;">
                    <i class="fas fa-cash-register" style="color: #0056b3;"></i>
                </span>
                <h5 style="color: #495057; margin: 0; font-size: 1.2em;">Carteira do Usuário</h5>
            </div>
            <div class="widget-content" style="background: #fff; padding: 15px; border-radius: 0 0 8px 8px;">
                <?php if (isset($custom_error) && $custom_error != '') {
                    echo '<div class="alert alert-danger" style="border-radius: 4px; margin-bottom: 15px;">' . $custom_error . '</div>';
                } ?>
                <div class="tab-pane active" id="tab1">
                    <form action="<?php echo base_url(); ?>index.php/admincarteira/realizarPagamento" id="formCarteira" method="post" class="form-horizontal">
                        
                        <?php 
                        $csrf = array(
                            'name' => $this->security->get_csrf_token_name(),
                            'hash' => $this->security->get_csrf_hash()
                        );
                        ?>
                        <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>" />

                        <div style="max-width: 800px; margin: 0 auto;">
                            <div class="control-group" style="margin-bottom: 15px;">
                                <label for="nome" class="control-label" style="font-weight: 500; color: #495057;">Nome<span class="required" style="color: #dc3545;">*</span></label>
                                <div class="controls">
                                    <input id="nome" type="text" name="nome" value="<?php echo $carteira->nome; ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                    <input id="idCarteiraUsuario" type="hidden" name="idCarteiraUsuario" value="<?php echo $carteira->idCarteiraUsuario; ?>" />
                                </div>
                            </div>

                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="saldo" class="control-label" style="font-weight: 500; color: #495057;">Saldo Atual<span class="required" style="color: #dc3545;">*</span></label>
                                        <div class="controls">
                                            <input id="saldo" type="text" name="saldo" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                            <input id="saldo_original" type="hidden" value="<?php echo number_format($carteira->saldo, 2, ',', '.'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="salario_base" class="control-label" style="font-weight: 500; color: #495057;">Salário Base<span class="required" style="color: #dc3545;">*</span></label>
                                        <div class="controls">
                                            <input id="salario_base" type="text" name="salario_base" value="<?php echo number_format($config->salario_base, 2, ',', '.'); ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="comissao_fixa" class="control-label" style="font-weight: 500; color: #495057;">Comissão Fixa<span class="required" style="color: #dc3545;">*</span></label>
                                        <div class="controls">
                                            <input id="comissao_fixa" type="text" name="comissao_fixa" value="<?php echo number_format($config->comissao_fixa, 2, ',', '.'); ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="data_pagamento" class="control-label" style="font-weight: 500; color: #495057;">Data de Pagamento<span class="required" style="color: #dc3545;">*</span></label>
                                        <div class="controls">
                                            <input id="data_pagamento" type="text" name="data_pagamento" value="<?php echo $config->data_salario; ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="control-group" style="margin-bottom: 15px;">
                                <label for="comissao_pendente" class="control-label" style="font-weight: 500; color: #495057;">Comissão Pendente</label>
                                <div class="controls">
                                    <input id="comissao_pendente" type="text" name="comissao_pendente" value="<?php echo number_format($carteira->comissao_pendente, 2, ',', '.'); ?>" readonly style="width: calc(50% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                </div>
                            </div>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'pCarteiraAdmin')) { ?>
                                <div class="control-group" style="margin-bottom: 15px;">
                                    <label for="retirada_valor" class="control-label" style="font-weight: 500; color: #495057;">Valor da Retirada<span class="required" style="color: #dc3545;">*</span></label>
                                    <div class="controls">
                                        <div class="input-prepend" style="width: 100%; display: flex;">
                                            <span class="add-on" style="background: #e9ecef; border: 1px solid #ced4da; border-right: none; border-radius: 4px 0 0 4px; padding: 8px;">R$</span>
                                            <input id="retirada_valor" type="text" name="retirada_valor" value="" style="flex: 1; padding: 8px; border: 1px solid #ced4da; border-left: none; border-radius: 0 4px 4px 0;"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="control-group" style="margin-bottom: 15px;">
                                    <label for="retirada_descricao" class="control-label" style="font-weight: 500; color: #495057;">Descrição da Retirada<span class="required" style="color: #dc3545;">*</span></label>
                                    <div class="controls">
                                        <input id="retirada_descricao" type="text" name="retirada_descricao" value="" style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" onChange="javascript:this.value=this.value.toUpperCase();"/>
                                    </div>
                                </div>

                                <div class="form-actions" style="background: none; border: none; padding: 15px 0 0; margin: 0;">
                                    <div class="span12">
                                        <div class="span6 offset3" style="display: flex; justify-content: center; gap: 10px;">
                                            <button type="submit" class="button btn btn-primary" style="width: 180px; height: 36px; padding: 0 15px; border-radius: 4px; display: flex; align-items: center; justify-content: center; gap: 5px; font-size: 13px;">
                                                <i class='bx bx-save'></i>
                                                <span>Realizar Retirada</span>
                                            </button>
                                            <a href="<?php echo base_url() ?>index.php/admincarteira" class="button btn btn-warning" style="width: 180px; height: 36px; padding: 0 15px; border-radius: 4px; display: flex; align-items: center; justify-content: center; gap: 5px; font-size: 13px; text-decoration: none;">
                                                <i class="bx bx-undo"></i>
                                                <span>Voltar</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Seção de Detalhes da Transação -->
                                <?php if ($ultima_transacao): ?>
                                <div class="widget-box" style="margin-top: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                    <div class="widget-title" style="background: #f8f9fa; border-radius: 8px 8px 0 0; padding: 10px 15px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center;">
                                            <span class="icon" style="margin-right: 10px;">
                                                <i class="fas fa-history" style="color: #0056b3;"></i>
                                            </span>
                                            <h5 style="color: #495057; margin: 0; font-size: 1.2em;">Detalhes da Retirada</h5>
                                        </div>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <div class="input-prepend" style="margin: 0;">
                                                <span class="add-on" style="background: #e9ecef; border: 1px solid #ced4da; border-right: none; border-radius: 4px 0 0 4px; padding: 8px;">ID</span>
                                                <input type="text" id="busca_retirada" placeholder="Buscar por ID" style="width: 150px; padding: 8px; border: 1px solid #ced4da; border-left: none; border-radius: 0 4px 4px 0;"/>
                                            </div>
                                            <button type="button" class="btn btn-primary" onclick="buscarRetirada()" style="padding: 8px 15px;">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="widget-content" style="background: #fff; padding: 15px; border-radius: 0 0 8px 8px;">
                                        <div style="max-width: 800px; margin: 0 auto;">
                                            <div class="row-fluid" style="margin-bottom: 15px;">
                                                <div class="span6">
                                                    <div class="control-group">
                                                        <label class="control-label" style="font-weight: 500; color: #495057;">Data da Transação</label>
                                                        <div class="controls">
                                                            <input type="text" id="data_transacao" value="<?php echo date('d/m/Y H:i:s', strtotime($ultima_transacao->data_transacao)); ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="span6">
                                                    <div class="control-group">
                                                        <label class="control-label" style="font-weight: 500; color: #495057;">Valor</label>
                                                        <div class="controls">
                                                            <input type="text" id="valor_transacao" value="R$ <?php echo number_format($ultima_transacao->valor, 2, ',', '.'); ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="control-group" style="margin-bottom: 15px;">
                                                <label class="control-label" style="font-weight: 500; color: #495057;">Descrição</label>
                                                <div class="controls">
                                                    <input type="text" id="descricao_transacao" value="<?php echo $ultima_transacao->descricao; ?>" readonly style="width: calc(100% - 16px); padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                                </div>
                                            </div>

                                            <div id="codigo_pix_container" style="display: <?php echo ($ultima_transacao->codigo_pix) ? 'block' : 'none'; ?>">
                                                <div class="control-group" style="margin-bottom: 15px;">
                                                    <label class="control-label" style="font-weight: 500; color: #495057;">Código PIX</label>
                                                    <div class="controls">
                                                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                                            <input type="text" id="codigo_pix" value="<?php echo $ultima_transacao->codigo_pix; ?>" readonly style="flex: 1; min-width: 200px; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background-color: #e9ecef;"/>
                                                            <button type="button" class="btn btn-primary" onclick="copiarCodigoPix(document.getElementById('codigo_pix').value)" style="min-width: 100px; padding: 8px 12px;">
                                                                <i class="fas fa-copy"></i> Copiar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php } ?>
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
<script src="https://cdn.rawgit.com/cozmo/jsQR/master/dist/jsQR.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            data: {
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            }
        });

        // Configuração inicial da máscara
        $('#retirada_valor').maskMoney({
            prefix: 'R$ ',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });

        // Aplica a máscara em tempo real durante a digitação
        $('#retirada_valor').on('input', function() {
            $(this).maskMoney('mask');
        });

        $('#formCarteira').validate({
            rules: {
                retirada_valor: { required: true },
                retirada_descricao: { required: true }
            },
            messages: {
                retirada_valor: { required: 'Campo Requerido.' },
                retirada_descricao: { required: 'Campo Requerido.' }
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
                var valor = $("#retirada_valor").val();
                if (!valor) {
                    Swal.fire({
                        title: 'Atenção!',
                        text: 'Por favor, informe o valor da retirada.',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                
                valor = valor.replace('R$ ', '').replace('.', '').replace(',', '.');
                if (parseFloat(valor) <= 0) {
                    Swal.fire({
                        title: 'Atenção!',
                        text: 'O valor da retirada deve ser maior que zero!',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                var saldo = $("#saldo_original").val().replace('R$ ', '').replace('.', '').replace(',', '.');
                if (parseFloat(valor) > parseFloat(saldo)) {
                    Swal.fire({
                        title: 'Atenção!',
                        text: 'O valor da retirada não pode ser maior que o saldo disponível!',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                
                // Faz a requisição AJAX para gerar o QR Code primeiro
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/admincarteira/gerarQRCodePix',
                    type: 'POST',
                    data: {
                        valor: valor,
                        chave_pix: '<?php echo $config->chave_pix; ?>',
                        nome: '<?php echo $carteira->nome; ?>',
                        txid: 'RET' + Date.now() + Math.floor(Math.random() * 1000)
                    },
                    dataType: 'json',
                    success: function(qrResponse) {
                        if (qrResponse.success) {
                            // Se o QR Code foi gerado com sucesso, faz a retirada
                            $.ajax({
                                url: '<?php echo base_url(); ?>index.php/admincarteira/realizarPagamento',
                                type: 'POST',
                                data: $(form).serialize() + '&codigo_pix=' + qrResponse.codigo_pix,
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            title: 'QR Code PIX Gerado',
                                            html: `
                                                <div style="text-align: center;">
                                                    <div style="margin-bottom: 15px;">
                                                        <strong>Chave PIX:</strong><br>
                                                        <span style="font-size: 1.2em; color: #2196F3;">${response.chave_pix}</span>
                                                    </div>
                                                    <div>
                                                        <img src="${qrResponse.qrcode}" alt="QR Code PIX" style="max-width: 200px; margin: 10px auto;">
                                                        <p class="mt-2">Escaneie o QR Code para pagar</p>
                                                        <button class="btn btn-primary" onclick="copiarCodigoPix('${qrResponse.codigo_pix}')">
                                                            <i class="fas fa-copy"></i> Copiar Código PIX
                                                        </button>
                                                    </div>
                                                </div>
                                            `,
                                            showCancelButton: true,
                                            confirmButtonText: 'Imprimir',
                                            cancelButtonText: 'Fechar',
                                            showDenyButton: false
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                // Imprimir QR Code
                                                var printWindow = window.open('', '_blank');
                                                printWindow.document.write(`
                                                    <html>
                                                        <head>
                                                            <title>QR Code PIX</title>
                                                            <style>
                                                                body { text-align: center; padding: 20px; }
                                                                img { max-width: 200px; margin: 10px auto; }
                                                                .chave-pix { 
                                                                    font-size: 1.2em; 
                                                                    margin: 10px 0;
                                                                    word-break: break-all;
                                                                }
                                                            </style>
                                                        </head>
                                                        <body>
                                                            <h2>QR Code PIX</h2>
                                                            <div class="chave-pix">
                                                                <strong>Chave PIX:</strong><br>
                                                                ${response.chave_pix}
                                                            </div>
                                                            <img src="${qrResponse.qrcode}" alt="QR Code PIX">
                                                            <p>Escaneie o QR Code para pagar</p>
                                                        </body>
                                                    </html>
                                                `);
                                                printWindow.document.close();
                                                printWindow.print();
                                            }
                                            // Redireciona após fechar o modal
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Erro',
                                            text: response.message || 'Erro ao processar a retirada.'
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erro',
                                        text: 'Erro ao processar a requisição.'
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: qrResponse.message || 'Erro ao gerar QR Code PIX.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao gerar QR Code PIX.'
                        });
                    }
                });
                return false;
            }
        });

        // Verificar pagamento automático
        $('#verificarPagamento').click(function() {
            if (confirm('Deseja verificar e processar o pagamento automático para esta carteira?')) {
                $.ajax({
                    url: '<?php echo base_url() ?>index.php/admincarteira/verificarPagamentosAutomaticos',
                    type: 'POST',
                    data: {
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>',
                        carteira_id: '<?php echo $carteira->idCarteiraUsuario; ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Erro ao verificar pagamento automático.');
                    }
                });
            }
        });
    });

    function receberComissao() {
        Swal.fire({
            title: 'Confirmar Recebimento',
            text: 'Deseja realmente receber a comissão pendente?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sim, receber',
            cancelButtonText: 'Cancelar',
            background: '#fff',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $("#idCarteiraUsuario").val();
                var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
                
                var data = {};
                data[csrf_name] = csrf_hash;
                
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/admincarteira/receberComissao/' + id,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Sucesso!',
                                text: 'Comissão recebida com sucesso!',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Erro ao receber comissão: ' + response.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Erro ao processar a requisição.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    // Função para gerar QR Code PIX
    function gerarQRCodePix(valor, chavePix) {
        if (!chavePix) {
            Swal.fire({
                title: 'Atenção!',
                text: 'Usuário não possui chave PIX cadastrada.',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Formata o valor para o padrão PIX (2 casas decimais)
        valor = parseFloat(valor).toFixed(2);

        // Cria o payload do PIX
        var payload = {
            pixKey: chavePix,
            description: 'Retirada de Carteira',
            merchantName: '<?php echo $carteira->nome; ?>',
            amount: valor,
            txid: '<?php echo $carteira->idCarteiraUsuario; ?>' + Date.now()
        };

        // Faz a requisição para gerar o QR Code
        $.ajax({
            url: '<?php echo base_url(); ?>index.php/admincarteira/gerarQRCodePix',
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'QR Code PIX Gerado!',
                        html: `
                            <div style="text-align: center;">
                                <img src="${response.qrcode}" alt="QR Code PIX" style="max-width: 200px; margin: 10px 0;">
                                <div style="margin: 10px 0;">
                                    <p><strong>Chave PIX:</strong> ${chavePix}</p>
                                    <p><strong>Valor:</strong> R$ ${valor}</p>
                                </div>
                                <button class="btn btn-primary" onclick="copiarChavePix('${chavePix}')">
                                    <i class="fas fa-copy"></i> Copiar Chave PIX
                                </button>
                            </div>
                        `,
                        showConfirmButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Fechar',
                        cancelButtonText: 'Imprimir',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary'
                        }
                    }).then((result) => {
                        if (result.isDismissed) {
                            window.print();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Erro ao gerar QR Code PIX: ' + response.message,
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Erro!',
                    text: 'Erro ao processar a requisição do QR Code PIX.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Função para copiar a chave PIX
    function copiarChavePix(chave) {
        navigator.clipboard.writeText(chave).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso',
                text: 'Chave PIX copiada para a área de transferência!',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                // Aguarda 1 segundo antes de fechar o modal
                setTimeout(() => {
                    Swal.close();
                }, 1000);
            });
        }).catch(function(err) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao copiar a chave PIX.'
            });
        });
    }

    // Função para copiar o código PIX
    function copiarCodigoPix(codigoPix) {
        navigator.clipboard.writeText(codigoPix).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso',
                text: 'Código PIX copiado para a área de transferência!',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                // Aguarda 1 segundo antes de fechar o modal
                setTimeout(() => {
                    Swal.close();
                }, 1000);
            });
        }).catch(function(err) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao copiar o código PIX.'
            });
        });
    }

    // Função para buscar retirada por ID
    function buscarRetirada() {
        var id = $("#busca_retirada").val();
        if (!id) {
            Swal.fire({
                title: 'Atenção!',
                text: 'Por favor, informe o ID da retirada.',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }

        $.ajax({
            url: '<?php echo base_url(); ?>index.php/admincarteira/buscarRetirada',
            type: 'POST',
            data: {
                id: id,
                carteira_id: $("#idCarteiraUsuario").val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Atualiza os campos com os dados da retirada
                    $("#data_transacao").val(response.data.data_transacao);
                    $("#valor_transacao").val("R$ " + response.data.valor);
                    $("#descricao_transacao").val(response.data.descricao);
                    
                    // Atualiza o código PIX se existir
                    if (response.data.codigo_pix) {
                        $("#codigo_pix").val(response.data.codigo_pix);
                        $("#codigo_pix_container").show();
                    } else {
                        $("#codigo_pix_container").hide();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso',
                        text: 'Retirada encontrada!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.message || 'Retirada não encontrada.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao buscar a retirada.'
                });
            }
        });
    }

    // Adiciona evento de tecla Enter no campo de busca
    $(document).ready(function() {
        $("#busca_retirada").keypress(function(e) {
            if (e.which == 13) {
                buscarRetirada();
            }
        });
    });
</script>
