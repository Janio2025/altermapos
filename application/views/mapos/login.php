<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?php echo $this->config->item('app_name') ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php echo $this->config->item('app_name') . ' - ' . $this->config->item('app_subname') ?>">
    <meta name="csrf-token-name" content="<?= config_item("csrf_token_name") ?>">
    <meta name="csrf-cookie-name" content="<?= config_item("csrf_cookie_name") ?>">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/matrix-login.css" />
    <link href="<?= base_url('assets/css/particula.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <script src="<?php echo base_url() ?>assets/js/jquery-1.12.4.min.js"></script>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
    <script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <!-- Script webeddy.com.br -->
    <script>
        function formatar(mascara, documento) {
            var i = documento.value.length;
            var saida = mascara.substring(0, 1);
            var texto = mascara.substring(i)

            if (texto.substring(0, 1) != saida) {
                documento.value += texto.substring(0, 1);
            }
        }
    </script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .tabs {
            display: flex;
        }
        .tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            background-color: #F8F8FF;
            border: 1px solid #FF6347;
            cursor: pointer;
        }
        .tab.active {
            background-color: #fd5e33;
        }
        .form-container {
            border: 1px solid #FF6347;
            padding: 20px;
        }
    </style>
</head>

<?php
$parse_email = $this->input->get('e');
$parse_cpfcnpj = $this->input->get('c');
?>

<body>
<div class="main-login">
    <div class="left-login">
      <!-- Saudação -->
      <h1 class="h-one">
        <?php
        function saudacao($nome = '')
        {
            $hora = date('H');
            if ($hora >= 00 && $hora < 12) {
                return 'Olá! Bom dia' . (empty($nome) ? '' : ', ' . $nome);
            } elseif ($hora >= 12 && $hora < 18) {
                return 'Olá! Boa tarde' . (empty($nome) ? '' : ', ' . $nome);
            } else {
                return 'Olá! Boa noite' . (empty($nome) ? '' : ', ' . $nome);
            }
        }
        $login = 'bem-vindo';
        echo saudacao($login);
        // Irá retornar conforme o horário:
        ?>
      </h1>
      <h2 class="h-two"> Ao Sistema de Controle de Ordens de Serviço</h2>
      <img src="<?php echo base_url() ?>assets/img/dashboard-animate.svg" class="left-login-image" alt="Map-OS - Versão: <?= $this->config->item('app_version'); ?>">
    </div>
    <form class="form-vertical" id="formLogin" method="post" action="<?= site_url('login/verificarLogin') ?>">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
      <?php if ($this->session->flashdata('error') != null) { ?>
        <div id="loginbox">
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('error'); ?>
          </div>
        </div>
      <?php } ?>
      <div class="d-flex flex-column">
        <div class="right-login">
          <div class="container">
            <div class="card">
              <div class="content">
                <div id="newlog">
                  <div class="icon2">
                    <img src="<?php echo base_url() ?>assets/img/logo-two.png">
                  </div>
                  <div class="title01">
                    <?= '<img src="' . base_url() . 'assets/img/logo-mapos-branco.png">'; ?>
                  </div>
                </div>
                <div id="mcell">Versão: <?= $this->config->item('app_version'); ?></div>


                  <div class="tabs" style="">
                      <div class="tab active" id="loginAdmin">EMPRESA</div>
                      <div class="tab" id="loginCliente">CLIENTE</div>
                  </div>

                  <div class="form-container" id="loginForm">
                    <form method="post" action="index.html">
                      <div class="input-field">
                          <label class="fas fa-user" for="nome"></label>
                          <input id="email" name="email" type="text" placeholder="Email">
                      </div>

                      <div class="input-field">
                          <label class="fas fa-lock" for="senha"></label>
                          <input name="senha" type="password" placeholder="Senha">
                      </div>

                      <div class="center"><button id="btn-acessar">Acessar</button></div>

                      <div class="links-uteis">
                          <a href="">
                              <p><?= date('Y'); ?> &copy; Map-OS</p>
                          </a>
                      </div>
                    </form>
                  </div>


                  <div class="form-container" style="display: none;">
                      <form method="post" id="formLoginCliente" action="<?php echo site_url() ?>/mine/login">
                          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                          <div class="control-group">
                              <div class="controls">
                                  <div class="main_input_box">
                                      <span class="add-on bg_lg"><i class='bx bx-user-plus iconU'></i></span>
                                      <input id="email" name="email" type="text" placeholder="Email" value="<?php echo trim($parse_email); ?>" />
                                  </div>
                              </div>
                          </div>

                          <div class="control-group">
                              <div class="controls">
                                  <div class="main_input_box">
                                      <span class="add-on bg_ly"><i class='bx bx-id-card iconU'></i></span>
                                      <input class="" maxlength="18" size="18" name="senha" type="password" placeholder="Senha" value="" />
                                  </div>
                              </div>
                          </div>

                          <button style="margin: 0" class="btn btn-info btn-large"> Acessar</button>
                          <div style="padding: 10px;"></div>

                          <div class="links-uteis">
                              <a href="<?= site_url('mine/resetarSenha') ?>">
                                  <p style="margin:0px 0 18px">Esqueceu a senha?</p>
                              </a>
                          </div>
                          <div class="links-uteis">
                              <a href="">
                                  <p><?= date('Y'); ?> &copy; Map-OS</p>
                              </a>
                          </div>
                      </form>
                  </div>

                 


                  
                
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>



      <a href="#notification" id="call-modal" role="button" class="btn" data-toggle="modal" style="display: none ">notification</a>
      <div id="notification" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
          <h4 id="myModalLabel">Map-OS</h4>
        </div>
        <div class="modal-body">
          <h5 style="text-align: center" id="message">Os dados de acesso estão incorretos, por favor tente novamente!</h5>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Fechar</button>
        </div>
      </div>
    </form>
  </div>
    
</body>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
  <script src="<?= base_url() ?>assets/js/jquery-1.12.4.min.js"></script>
  <script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
  <script src="<?= base_url() ?>assets/js/validate.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#email').focus();
      $("#formLogin").validate({
        rules: {
          email: {
            required: true,
            email: true
          },
          senha: {
            required: true
          }
        },
        messages: {
          email: {
            required: '',
            email: 'Insira Email válido'
          },
          senha: {
            required: 'Campos Requeridos.'
          }
        },
        submitHandler: function(form) {
          var dados = $(form).serialize();
          $('#btn-acessar').addClass('disabled');
          $('#progress-acessar').removeClass('hide');

          $.ajax({
            type: "POST",
            url: "<?= site_url('login/verificarLogin?ajax=true'); ?>",
            data: dados,
            dataType: 'json',
            success: function(data) {
                if (data.result == true) {
                    window.location.href = "<?= site_url('mapos'); ?>";
                } else {
                    $('#btn-acessar').removeClass('disabled');
                    $('#progress-acessar').addClass('hide');
                    $('#message').text(data.message || 'Os dados de acesso estão incorretos, por favor tente novamente!');
                    $('#call-modal').trigger('click');

                    // Atualiza o token a cada requisição
                    var newCsrfToken = data.MAPOS_TOKEN; 
                    $("input[name='<?= $this->security->get_csrf_token_name(); ?>']").val(newCsrfToken);
                    
                }
            }
          });

          return false;
        },

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
    });
  </script>


<script>
// Função para alternar entre as abas
function changeTab(tabId) {
const tabs = document.querySelectorAll(".tab");
const forms = document.querySelectorAll(".form-container");

tabs.forEach(tab => tab.classList.remove("active"));
document.getElementById(tabId).classList.add("active");

if (tabId === "loginAdmin") {
    forms[0].style.display = "block";
    forms[1].style.display = "none";
} else if (tabId === "loginCliente") {
    forms[0].style.display = "none";
    forms[1].style.display = "block";
}
}

// Adiciona um ouvinte de eventos às abas
document.getElementById("loginAdmin").addEventListener("click", () => changeTab("loginAdmin"));
document.getElementById("loginCliente").addEventListener("click", () => changeTab("loginCliente"));

// Define a aba "EMPRESA" como ativa ao carregar a página
document.addEventListener("DOMContentLoaded", () => {
changeTab("loginAdmin");
});
</script>




    <?php if ($this->session->flashdata('success') != null) { ?>
        <script>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: '<?php echo $this->session->flashdata('success'); ?>',
                showConfirmButton: false,
                timer: 4000
            })
        </script>
    <?php } ?>

    <?php if ($this->session->flashdata('error') != null) { ?>
        <script>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: '<?php echo $this->session->flashdata('error'); ?>',
                showConfirmButton: false,
                timer: 4000
            })
        </script>
    <?php } ?>

    <script type="text/javascript">
        $(document).ready(function() {

            $("#formLoginCliente").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    senha: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        required: 'Campo Requerido.',
                        email: 'Insira Email válido'
                    },
                    senha: {
                        required: 'Campo Requerido.'
                    }
                },
                submitHandler: function(form) {
                    var dados = $(form).serialize();


                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>index.php/mine/login?ajax=true",
                        data: dados,
                        dataType: 'json',
                        success: function(data) {
                            if (data.result == true) {
                                window.location.href = "<?php echo base_url(); ?>index.php/mine/painel";
                            } else {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'error',
                                    title: 'Os dados de acesso estão incorretos.\n Por favor tente novamente!',
                                    showConfirmButton: false,
                                    timer: 4000
                                })

                                var newCsrfToken = data.MAPOS_TOKEN;
                                $("input[name='<?= $this->security->get_csrf_token_name(); ?>']").val(newCsrfToken);
                            }
                        }
                    });

                    return false;
                },

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

        });
    </script>

</html>



