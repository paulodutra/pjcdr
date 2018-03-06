<?php

//CONFIGURAÇÕES DO BANCO #####################
define('HOST', '172.28.64.136');
define('USER', 'dsv_portalnovo');
define('PASS', '123456789');
define('DBSA', 'concurso_redacao');

//DEFINE SERVIDOR DE E-MAIL ######################
define('MAILHOST', '172.28.64.155');
define('MAILUSER', 'dpunasescolas@dpu.gov.br');
define('MAILPASS', 'escolas@123');
define('MAILPORT', '2525');
define('MAILOPTION', 'tsl');

//DEFINE IDENTIDADE DO SITE #######################
define('SITENAME', 'Concurso de redação da DPU');
define('SITEDESC', 'Descrição do site');
define('SITEAUTHOR', 'Paulo Dutra');

//DEFINE A BASE DO SITE ##########################

/* * URL  DO SITE */
define('HOME', 'http://dsvportal.dpu.gov.br/concursoderedacao2016');

/* * NOME DA PASTA DO TEMA */
define('THEME', 'concurso');

/** INCLUDE_PATH: Realiza a inclusão de imagens, css,js etc entre na url pasta theme e a pasta do tema */
define('INCLUDE_PATH', HOME . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . THEME);

/** REQUIRE_PATH: Realiza o require de arquivos */
define('REQUIRE_PATH', 'themes' . DIRECTORY_SEPARATOR . THEME);

//AUTO LOAD DE CLASSES ##########################

function __autoload($Class) {
    //Configurações de diretorio
    $cDir = ['Conn', 'Helpers', 'Models'];
    //Include diretorio
    $iDir = null;

    foreach ($cDir as $dirname):
        if (!$iDir && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $dirname . DIRECTORY_SEPARATOR . $Class . '.class.php') && !is_dir(__DIR__ . DIRECTORY_SEPARATOR . $dirname . DIRECTORY_SEPARATOR . $Class . '.class.php')):
            include_once(__DIR__ . DIRECTORY_SEPARATOR . $dirname . DIRECTORY_SEPARATOR . $Class . '.class.php');

            $iDir = true;

        endif;
    endforeach;

    if (!$iDir):
        trigger_error("Não foi possivel incluir{$Class}.class.php", E_USER_ERROR);
    endif;
}

//Fim do metodo magiaco autoload
//TRATAMENTO DE ERROS ####################
//Css constantes :: Mensagens de Erro

define('MSG_ACCEPT', 'alert-success');
define('MSG_INFOR', 'alert-info');
define('MSG_ALERT', 'alert-warning');
define('MSG_ERROR', 'alert-danger');

//MSGERRO :: Exibe erros lançados :: Front Obs:Não exibe arquivo do erro
function MSGErro($ErrorMsg, $ErrorNo, $ErrorDie = null) {
    //
    $CssClass = ($ErrorNo == E_USER_NOTICE ? MSG_INFOR : ($ErrorNo == E_USER_WARNING ? MSG_ALERT : ($ErrorNo == E_USER_ERROR ? MSG_ERROR : $ErrorNo)));

    echo "<div class=\"alert {$CssClass}\" role=\"alert\">"
    . "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"
    . "<span>{$ErrorMsg}</span>"
    . "</div>";

    if ($ErrorDie):
        die;
    endif;
}

//PHPErro :: personaliza o gatilho do PHP
function PHPErro($ErrorNo, $ErrorMsg, $ErrorFile, $ErrorLine) {
    $CssClass = ($ErrorNo == E_USER_NOTICE ? MSG_INFOR : ($ErrorNo == E_USER_WARNING ? MSG_ALERT : ($ErrorNo == E_USER_ERROR ? MSG_ERROR : $ErrorNo)));

    echo "<p class=\"alert {$CssClass}\">";
    echo "<b>Erro na Linha: {$ErrorLine} :: </b> {$ErrorMsg} <br>";
    echo "<small>{$ErrorFile}</small>";
    echo "</p>";

    if ($ErrorNo == E_USER_ERROR):
        die;
    endif;
}

/* Determina para o PHP que a função de erro que sera utilizada quando 
  houver erros no PHP, será a função PHPError.
 * Isso  é possivel utilizando:
 * set_error_handler("NOME_DA_FUNÇÂO")
 *  */
set_error_handler("PHPErro");

