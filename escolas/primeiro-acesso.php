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

        <title>Área da escola</title>

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
                        $accessToken = filter_input(INPUT_GET, 'token', FILTER_DEFAULT);

                        $access = filter_input_array(INPUT_POST, FILTER_DEFAULT);

                        if (isset($access) && !empty($access['school_cnpj']) && isset($access['sendFirstAccess'])):
                            unset($access['sendFirstAccess']);
                            $FirstAccess = new FirstAccess();
                            $FirstAccess->ExeUpdate($accessToken, $access);

                            if ($FirstAccess->getResult()):
                                MSGErro($FirstAccess->getError()[0], $FirstAccess->getError()[1]);
                            else:
                                MSGErro($FirstAccess->getError()[0], $FirstAccess->getError()[1]);
                            endif;

                        endif;
                        ?>
                        <div class="hpanel">
                            <div class="panel-body">
                                <h3 class="text-uppercase text-primary text-center">Informe o CNPJ da escola</h3>
                                <form name="loginForm" id="loginForm" action="" method="post" >
                                    <label>CNPJ:</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="99.999.999/9999-99" title="Por favor entre com o seu CNPJ!"  name="school_cnpj"  class="form-control cnpj" required>
                                    </div>
                                    <input type="submit" name="sendFirstAccess" value="Enviar" class="btn btn-primary btn-block"> 
                                </form>
                                <div class="row">
                                    <div style="padding-top:3%;">
                                        <div class="text-right">
                                            <span><a class="btn btn-info" href="<?= HOME . DIRECTORY_SEPARATOR . 'escolas' ?>">Área da Escola</a></span>   
                                        </div>   
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--row-->
            </div>
        </div> 


        <div class="row">
            <div class="col-md-12 text-center">
                <strong>Área Escola</strong> -Concurso de Redação <?= date('Y') ?>  &copy;
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
        <!--Default-->
        <script src="<?= HOME ?>/_cdn/jmask.js"></script>
        <script src="js/login.js"></script>


    </body>
</html>
<?php
/* * Limpa o buffer de navegação */
ob_end_flush();
