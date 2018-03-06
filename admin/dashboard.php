<?php
ob_start();
session_start();

require('../_app/Config.inc.php');

$login = new Login(3);

$getexe = filter_input(INPUT_GET, 'exe', FILTER_DEFAULT);

$logoff = filter_input(INPUT_GET, 'logoff', FILTER_VALIDATE_BOOLEAN);

if (!$login->checkLogin()):
    unset($_SESSION['userlogin']);
    header('Location: index.php?exe=restrito');

else:
    $userSession = $_SESSION['userlogin'];

endif;

if ($logoff):
    unset($_SESSION['userlogin']);
    header('Location:index.php?exe=logoff');

endif;
?>

<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- Page title -->
        <title> ADM | Concurso</title>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="shortcut icon" type="image/ico" href="images/favico.png" />
        <link rel="stylesheet" href="css/font-awesome.css" />
        <link rel="stylesheet" href="css/jquery.datetimepicker.css" />
        <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
        <link rel="stylesheet" href="vendor/animate.css/animate.css" />
        <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />


        <!-- App styles -->
        <link rel="stylesheet" href="fonts/pe-icon-7-stroke/pe-icon-7-stroke.css" />
        <link rel="stylesheet" href="fonts/pe-icon-7-stroke/helper.css" />
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/jquery.dataTables.css">
        <link rel="stylesheet" href="css/dataTables.bootstrap.css">
        <link rel="stylesheet" href="css/dataTables.responsive.css">
        <link rel="stylesheet" href="css/buttons.dataTables.css">
        <link rel="stylesheet" href="css/buttons.bootstrap.css">

        <link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.9.2.custom.css">

        <style>
            body { font-size: 140% }

            table.dataTable th,
            table.dataTable td {
                white-space: nowrap;
            }
        </style> 

    </head>
    <body>


        <!--[if lt IE 7]>
        <p class="alert alert-danger">Você está utilizando uma versão <strong>ultrapassada</strong> de seu navegador de internet. Por favor <a href="http://browsehappy.com/">Atualize o seu navegador</a> para melhorar a sua experiência.</p>
        <![endif]-->


        <!-- Header -->
        <header id="header">
            <div class="color-line">
            </div>
            <div id="logo" class="light-version">
                <span>
                    <a href="dashboard.php"><img src="images/painel.png" alt="Logo"></a>
                </span>
            </div>
            <nav role="navigation">
                <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
                <div class="small-logo">
                    <img src="" alt="">
                </div>

                <div class="navbar-right">
                    <ul class="nav navbar-nav no-borders">
                        <li class="dropdown">
                            <a class="dropdown-toggle label-menu-corner" href="#" data-toggle="dropdown">
                                <i class="pe-7s-add-user"></i>

                            </a>
                            <ul class="dropdown-menu hdropdown animated flipInX">
                                <li>
                                    <a href="dashboard.php?exe=users/profile">Atualizar usuário</a>
                                </li>
                                <li>
                                    <a href="dashboard.php?exe=users/users">Usuários</a>
                                </li>

                            </ul>
                        </li>
                        <li class="dropdown">
                            <a  href="dashboard.php?logoff=true">
                                <i class="pe-7s-upload pe-rotate-90"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Navigation -->
        <nav id="menu">
            <div id="navigation">
                <div class="profile-picture">


                    <div class="stats-label text-color">
                        <span class="font-extra-bold font-uppercase"><a href="<?= HOME ?>/admin/dashboard.php"><?= $userSession['user_name'] ?> <?= $userSession['user_lastname'] ?></a></span>                
                    </div>

                </div>
                <?php
//ATIVA MENU: ativa a opção escolhida no menu
                if (isset($getexe)):
                    $linkto = explode('/', $getexe);
                else:
                    $linkto = array();
                endif;
                ?>

                <ul class="nav" id="side-menu">

                    <li>
                        <a href="#">
                            <span class="nav-label"> Serie(s) </span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li><?php if (in_array('series', $linkto))  ?><a href="dashboard.php?exe=series/create"> Cadastrar Serie(s) </a></li>                  
                            <li><a href="dashboard.php?exe=series/index"> Listar/ Editar Serie(s) </a></li>
                        </ul>
                    </li>


                    <li>
                        <a href="#">
                            <span class="nav-label"> Categorias </span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li><?php if (in_array('categorias', $linkto))  ?><a href="dashboard.php?exe=categorias/create"> Cadastrar Categorias </a></li>
                            <li><a href="dashboard.php?exe=categorias/series">Adicionar serie(s) a uma categoria</a></li>

                            <li><a href="dashboard.php?exe=categorias/index"> Listar/ Editar Categorias </a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label"> Concurso </span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li <?php if (in_array('concurso', $linkto))  ?>><a href="dashboard.php?exe=concurso/create"> Cadastrar concurso </a></li>
                            <li><a href="dashboard.php?exe=concurso/category"> Adicionar categoria(s) a um concurso</a></li>
                            <li><a href="dashboard.php?exe=concurso/index"> Listar/Editar concurso(s) </a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label">Escolas</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li <?php if (in_array('school', $linkto))  ?>><a href="dashboard.php?exe=school/create"> Cadastrar Escolas </a></li>
                            <li><a href="dashboard.php?exe=phone/create"> Cadastrar Telefone </a></li>
                            <li><a href="dashboard.php?exe=school/search-school"> Pesquisar Escola </a></li>
                            <li><a href="dashboard.php?exe=school/index"> Listar/Editar Escola(s) </a></li>

                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label">Telefones</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li <?php if (in_array('school', $linkto))  ?>><a href="dashboard.php?exe=phone/create">Cadastrar Telefone(s)</a></li>
                            <li><a href="dashboard.php?exe=phone/index"> Listar/Editar Telefone(s) </a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label">Conta Bancária</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li <?php if (in_array('bank', $linkto))  ?>><a href="dashboard.php?exe=bank/create">Cadastrar Conta Bancária</a></li>
                            <li><a href="dashboard.php?exe=bank/index"> Listar/Editar Conta Bancária</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label">Plano(s) de Mobilização</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li <?php if (in_array('mobilization', $linkto))  ?>><a href="dashboard.php?exe=mobilization/create">Cadastrar Mobilização</a></li>
                            <li><a href="dashboard.php?exe=mobilization/file"> Enviar arquivos de Mobilizações </a></li>
                            <li><a href="dashboard.php?exe=mobilization/index"> Listar/Editar Mobilizações</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label">Participantes</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li <?php if (in_array('participantes', $linkto))  ?>><a href="dashboard.php?exe=participantes/create">Cadastrar Participante(s)</a></li>
                            <li><a href="dashboard.php?exe=participantes/index"> Listar/Editar  Participante(s)</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label">Inscrição</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li<?php if (in_array('subscribers', $linkto))  ?>><a href="dashboard.php?exe=subscribers/create">Cadastrar Inscrição(s)</a></li>
                            <li><a href="dashboard.php?exe=subscribers/region"> Inscrições por Região </a></li>
                            <li><a href="dashboard.php?exe=subscribers/index"> Listar/Inscrições </a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label">Categoria de Noticias</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li <?php if (in_array('category', $linkto))  ?>><a href="dashboard.php?exe=category/create">Cadastrar categoria de noticia(s)</a></li>
                            <li><a href="dashboard.php?exe=category/index"> Listar/categoria de noticia(s)</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label">Noticias</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li <?php if (in_array('artigos', $linkto))  ?>><a href="dashboard.php?exe=artigos/create">Cadastrar Noticia</a></li>
                            <li><a href="dashboard.php?exe=artigos/index"> Listar/Noticia(s) </a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-label">Usuários</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <li><a href="dashboard.php?exe=users/profile"> Atualizar Perfil </a></li>
                        </ul>
                        <ul class="nav nav-second-level">
                            <li><a href="dashboard.php?exe=users/users">Cadastrar / Atualizar/ Excluir Usuário(s)</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="dashboard.php?logoff=true">
                            <span class="pe-7s-upload pe-2x pe-va pe-rotate-90"></span>
                            <span class="nav-label"> Logout </span>
                        </a>
                    </li>

                </ul>
            </div>
        </nav>

        <!-- Main Wrapper -->
        <div id="wrapper">

            <div class="normalheader transition animated fadeIn">
                <div class="hpanel">
                    <div class="panel-body">
                        <a class="small-header-action" href="">
                            <div class="clip-header">
                                <i class="fa fa-arrow-up"></i>
                            </div>
                        </a>
                        <?php
                        /**
                         * Identifica o controller e a ação do mesmo exemplo: artigos/create
                         * Porém se o array não for definido ele pega os valores Dashboard ,/ inicio
                         */
                        if (!empty($linkto[0]) || !empty($linkto[1])):
                            /* $linkto[0] = ($linkto[0] ? $linkto[0] : 'Painel Administrativo');
                              $linkto[1] = ($linkto[1] ? $linkto[1] : 'Inicio'); */


                            $linkto[0] = ucwords($linkto[0]);
                            $linkto[1] = ucwords($linkto[1]);

                            $linkto[0] = ($linkto[0] == 'Phone' ? 'Telefone' : ($linkto[0] == 'Bank' ? 'Conta Bancária' : ($linkto[0] == 'Mobilization' ? 'Plano de Mobilização' : ($linkto[0] == 'Subscribers' ? 'Inscrição' : ($linkto[0] == 'Category' ? 'Categoria de noticias' : ($linkto[0] == 'Users' ? 'Usuário' : ($linkto[0] == 'School' ? 'Escola' : ($linkto[0] ))))))));
                            $linkto[1] = ($linkto[1] == 'Create' ? 'Cadastrar' : ($linkto[1] == 'Update' ? 'Atualizar' : ( $linkto[1] == 'Index' ? 'Inicio' : ($linkto[1] == 'Profile' ? 'Perfil' : ($linkto[1] == 'File' ? 'Enviar Arquivo(s) de' : ($linkto[1] == 'Region' ? 'Região' : ($linkto[1] == 'Details' ? 'Detalhes' : ($linkto[1] == 'Details-region' ? 'Detalhes da Região de' : ($linkto[1] == 'Users' ? 'usuários' : ($linkto[1] == 'Search-school' ? 'Buscar Escola' : $linkto[1] == 'Result-school' ? 'Resultado da Busca' : ($linkto[1] == 'Create-phone' ? 'Cadastrar' : ($linkto[1] =='Terms' ? 'Termos' :$linkto[1]))))))))))));



                        else:
                            $linkto[0] = 'Painel Administrativo';
                            $linkto[1] = 'Inicio';


                        endif;
                        ?>
                        <div id="hbreadcrumb" class="pull-right m-t-lg">
                            <ol class="hbreadcrumb breadcrumb">
                                <li><a href="dashboard.php?exe=<?= $linkto[0] . '/' . $linkto[1]; ?>"></a><?= $linkto[0]; ?></li>
                                <li class="active">
                                    <span><?= $linkto[1]; ?> </span>
                                </li>
                            </ol>
                        </div>
                        <h2 class="font-light m-b-xs">
                            <?= $linkto[0]; ?>
                        </h2>
                        <small><?= $linkto[1]; ?> </small>
                    </div>
                </div>
            </div>

            <div class="content">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="hpanel">
                            <div class="panel-heading">
                                <div class="panel-tools">
                                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                    <a class="closebox"><i class="fa fa-times"></i></a>
                                </div>
                                <?= $linkto[1]; ?> 
                                <?= $linkto[0]; ?>
                            </div>

                            <div class="panel-body">
                                <?php
//QUERY STRING: Front Controller, irá decidir qual controlador será executado. A estrutura de navegação não entra dentro da pasta apena inclui as mesma atraves do parametro exe
                                if (!empty($getexe)):
                                    $includepatch = __DIR__ . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . strip_tags(trim($getexe) . '.php');
                                else:
                                    $includepatch = __DIR__ . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'home.php';
                                endif;

                                if (file_exists($includepatch)):
                                    require_once($includepatch);
                                else:
                                    echo "<div class=\"alert alert-danger\"role=\"alert\">";
                                    MSGErro("<strong>Erro ao incluir tela:</strong> Erro ao incluir o controller /$getexe}.php!", MSG_ERROR);
                                    echo "</div>";
                                endif;
                                ?>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Vendor scripts -->
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="vendor/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript"src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
    <script type="text/javascript" src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="vendor/metisMenu/dist/metisMenu.min.js"></script>
    <script type="text/javascript" src="vendor/iCheck/icheck.min.js"></script>
    <script type="text/javascript" src="vendor/sparkline/index.js"></script>

    <!-- App scripts -->
    <script type="text/javascript" src="js/homer.js"></script>

    <!-- Data Table -->

    <script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/dataTables.responsive.js"></script>
    <script type="text/javascript" src="js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="js/dataTables.buttons.js"></script>
    <script type="text/javascript" src="js/buttons.html5.js"></script>
    <!--script type="text/javascript" src="js/buttons.bootstrap.js"></script-->
    <script type="text/javascript" src="js/buttons.print.js"></script>
    <script type="text/javascript" src="js/buttons.flash.js"></script>




    <script type="text/javascript" src="js/shCore.js"></script>
    <script type="text/javascript" src="js/demo.js"></script>



    <!--Essencials-->
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="../_cdn/jmask.js"></script>
    <script src="../_cdn/combo.js"></script>
    <script src="js/tiny_mce/tiny_mce.js"></script>
    <script src="js/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>
    <script src="js/admin.js"></script>

</body>
</html>
<?php
ob_end_flush();
?>
