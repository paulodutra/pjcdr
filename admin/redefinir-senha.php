<?php
/* * Inicia o buffer de navegação, pois aqui possui  header location, caso tenha alguma coisa acima deles pode dar erro de output */
ob_start();
session_start();
require('../_app/Config.inc.php');
?>
<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- Page title -->
        <title>Admin</title>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="shortcut icon" type="image/ico" href="images/favico.png" />


        <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
        <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
        <link rel="stylesheet" href="vendor/animate.css/animate.css" />
        <link rel="stylesheet" href="css/bootstrap.min.css" />

        <!-- App styles -->
        <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
        <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
        <link rel="stylesheet" href="css/style.css">

    </head>
    <body class="blank">

        <!--[if lt IE 7]>
        <p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="color-line"></div>
        <div class="login-container">
            <div id="formLogin">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center m-b-md">
                            <img src="" alt="">
                        </div>
                        <?php
                        $passwordToken = filter_input(INPUT_GET, 'token', FILTER_DEFAULT);
                        $password = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                        if (isset($password) && !empty($password) && $password['sendRemenber']):
                            unset($password['sendRemenber']);
                            $redefinePass = new RemenberPassword();
                            $redefinePass->ExeUpdate($passwordToken, $password);
                            if ($redefinePass->getResult()):

                                MSGErro($redefinePass->getError()[0], $redefinePass->getError()[1]);
                            else:
                                MSGErro($redefinePass->getError()[0], $redefinePass->getError()[1]);
                            endif;
                        //var_dump($redefinePass);


                        endif;


                        ;
                        ?>

                        <div class="hpanel">
                            <div class="panel-body">
                                <h3 class="text-uppercase text-primary text-center">Redefina sua senha</h3>
                                <form name="remenberForm" id="remenberForm" action="" method="post" >
                                    <label>Senha:</label>
                                    <div class="form-group">
                                        <input type="password" title="Por favor entre com sua senha!" placeholder="******" required="required" value="" name="user_password"  class="form-control">
                                    </div>
                                    <label>Confirme a senha:</label>
                                    <div class="form-group">
                                        <input type="password" title="Por favor entre com confirme a senha!" placeholder="******" required="required" value="" name="user_re_password" class="form-control">
                                    </div>
                                    <div class="text-center">
                                        <input type="submit" name="sendRemenber" value="Enviar" class="btn btn-primary btn-block"> 
                                    </div> 
                                </form>

                            </div>
                        </div>
                    </div>
                </div><!--row-->
            </div>    

            <div class="row">
                <div class="col-md-12 text-center">
                    <strong>Redefinir senha</strong> -Concurso de Redação <?= date('Y') ?>  &copy;
                </div>
            </div>
        </div>
        <!-- Vendor scripts -->
        <script src="vendor/jquery/dist/jquery.min.js"></script>
        <script src="vendor/jquery-ui/jquery-ui.min.js"></script>
        <script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
        <script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
        <script src="vendor/iCheck/icheck.min.js"></script>
        <script src="vendor/sparkline/index.js"></script>

        <!-- App scripts -->
        <script src="js/homer.js"></script>
        <script>
            $(document).ready(function() {


                $("#formRemenber").hide();

                $("#Remenber").click(function() {
                    $("#formRemenber").show();
                    $("#formLogin").hide();
                });

                $("#Login").click(function() {
                    $("#formRemenber").hide();
                    $("#formLogin").show();

                });

                $("#loginForm").submit(function() {
                    $("#formLogin").show();
                    $("#formRemenber").hide();

                });


                $("#formRemenberPass").submit(function() {
                    $("#formRemenber").show();
                    $("#formLogin").hide();
                });

            });
        </script>

    </body>
</html>
<?php
/* * Limpa o buffer de navegação */
ob_end_flush();
