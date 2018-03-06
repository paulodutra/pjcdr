<?php
/**
 * $Link: Objeto da classe Link.class.php, que foi instanciado no index.php geral do sistema(que fica junto com o .htacess) este index é o frontcontroller
 * da aplicação é ele que os INCLUDE_PATH e realiza as inclusões de tela , css e js.
 */
if ($Link->getData()):
    /** Permiti utilizar os indices do array que faz a consulta no banco como variaveis */
    extract($Link->getData());
else:
    header('Location: ' . HOME . DIRECTORY_SEPARATOR . '404');
endif;
?>

<div class="jumbotron">
        <h1 class="text-uppercase text-center">Concurso</h1>
        <p>-Faça download dos arquivos do <b><?=$concurso_name?></b></p>
        
</div>
<div class="panel panel-primary">
<div class="panel-heading"><?=$concurso_name?></div>
  <div class="panel-body">
   <span><b><a href=" <?=HOME.'/uploads/concurso/'.$file_directory?>" target="_blank" title="Visualizar Arquivo"><?=$file_type_name?></a></b></span>
  </div>
</div>