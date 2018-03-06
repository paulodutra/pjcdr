<!DOCTYPE html>
<?php
/* * Inicia o buffer de navegação, pois aqui possui varios header location, caso tenha alguma coisa acima deles pode dar erro de output */
ob_start();
require_once('./_app/Config.inc.php');
?>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="Concurso de Redação da DPU"> 

        <?php
        $Link = new Link();
        $Link->getTags();
        ?>
        <!--[if lt IE 9]>
            <script src="../../_cdn/html5.js"></script>
         <![endif]-->     
        <link rel="shortcut icon" type="image/ico" href="themes/concurso/images/favico.png" />   
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/bootstrap.css">
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/bootstrap-social.css">
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/style.css">
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/font-awesome.css">
        <link rel="stylesheet" href="<?= HOME; ?>/_cdn/shadowbox/shadowbox.css">
        <link rel="stylesheet" href="<?= HOME; ?>/themes/concurso/css/magnific-popup.css">
    </head>
    <body>

        <div class="container">
            <div class="space">

                <?php
                require(REQUIRE_PATH . '/inc/header.inc.php');
                /*                 * Instancia da classe link metodo getPath contém o front controller responsavel
                  por fazer a navegação e incluir os arquivos */

                if (!require$Link->getPatch()):
                    MSGErro('Erro ao incluir arquivo de navegação !', MSG_ERROR, true);
                endif;
                ?>
            </div>
        </div>
        <?php
        require(REQUIRE_PATH . '/inc/footer.inc.php');
        /* require(REQUIRE_PATH . DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'header.inc.php');
          /*         * Instancia da classe link metodo getPath contém o front controller responsavel
          por fazer a navegação e incluir os arquivos */



        /*
         * FRONT CONTROLLER Para realizar a navegação
         * 
         * o parametro recebido via GET['url'] vem do arquivo .htacess que esta realizando a navegação amigavel

          $url = ( isset($_GET['url']) ? strip_tags(trim($_GET['url'])) : 'index');
          $url = explode('/', $url);
          $url[0] = ($url[0] == null ? 'index' : $url[0]);
          $url[1] = ( empty($url[1]) ? null : $url[1]); //EVITA NOCICE
          //var_dump($url);

          if (file_exists(REQUIRE_PATH . '/' . $url[0] . '.php')) :
          require_once(REQUIRE_PATH . '/' . $url[0] . '.php');
          elseif (file_exists(REQUIRE_PATH . '/' . $url[0] . '/' . $url[1] . '.php')) :
          require_once(REQUIRE_PATH . '/' . $url[0] . '/' . $url[1] . '.php');
          else:
          if (file_exists(REQUIRE_PATH . '/404.php')):
          require_once(REQUIRE_PATH . '/404.php');
          else:
          echo "<p style=\"text-align:center; padding:50px 0;\">404 Erro - Arquivo não existe!</p>";
          endif;
          endif;

          require(REQUIRE_PATH . '/inc/footer.inc.php'); */
        ?>


    </body>

    <script src="<?= HOME ?>/_cdn/jquery.js"></script>
    <script src="<?= HOME ?>/_cdn/jcycle.js"></script>
    <script src="<?= HOME ?>/_cdn/jmask.js"></script>
    <script src="<?= HOME ?>/_cdn/shadowbox/shadowbox.js"></script>
    <script src="<?= HOME ?>/_cdn/jquery.magnific-popup.min.js"></script>

    <script src="<?= HOME ?>/_cdn/combo.js"></script>
    <script src="<?= HOME ?>/_cdn/admin.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?= HOME ?>/_cdn/bootstrap.min.js"></script>

    <!-- Script to Activate the Carousel -->
    <script>
        $('.carousel').carousel({
            interval: 1000
        });
    </script>
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id))
                return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.5";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id))
                return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.5";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>



    <script>
        !function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
            if (!d.getElementById(id)) {
                js = d.createElement(s);
                js.id = id;
                js.src = p + '://platform.twitter.com/widgets.js';
                fjs.parentNode.insertBefore(js, fjs);
            }
        }(document, 'script', 'twitter-wjs');
    </script>
    <script>
        $(document).ready(function () {

            $('.image-popup-vertical-fit').magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                mainClass: 'mfp-img-mobile',
                image: {
                    verticalFit: true
                }

            });

        });
    </script>

</html>
<?php
/* * Limpa o buffer de navegação */
ob_end_flush();
