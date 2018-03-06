<?php
 /**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
  require_once('_models/AdminConcurso.class.php');

  $concurso=filter_input(INPUT_GET,'concurso',FILTER_VALIDATE_INT); 

  $readConcurso= new Read();
  $readConcurso->ExeRead("cs_concurso","WHERE concurso_id=:id","id={$concurso}");

  if($readConcurso->getResult()):
    foreach ($readConcurso->getResult() as $concursoRedacao):
        extract($concursoRedacao);

?>
<div class="panel panel-primary">

  <!-- Default panel contents -->
  <div class="panel-heading text-center"><?=$concurso_name?></div>
  <div class="panel-body">
        <p>Data de inicio: <?= date('d/m/Y H:i:s',strtotime($concurso_start))?></p>
        <p>Data de termíno: <?= date('d/m/Y H:i:s',strtotime($concurso_end))?></p>
        <p>Status:  <?=$concurso_status=($concurso_status=='0' ?  'Inativo': 'Ativo')?></p>
  </div>
    <?php
    endforeach;  
  endif;  
  ?>    
  <?php
    /**Ação de alguns botões presentes da tabela*/
    $action=filter_input(INPUT_GET,'action',FILTER_DEFAULT);
    $file=filter_input(INPUT_GET,'file', FILTER_VALIDATE_INT);

    if($action):

      switch ($action):

        case 'delete':
          $deleteFile= new AdminConcurso();
          $deleteFile->deleteFile($file);
  
          MSGErro($deleteFile->getError()[0],$deleteFile->getError()[1]);

        break;
        
        default:
          MSGErro("Ação não existe, utilize os botões!", MSG_ALERT);
        break;

      endswitch;

    endif;  

  ?>
  <table class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
            <th class="text-center">Tipo do arquivo</th>
            <th class="text-center">-</th>
        </tr>
      </thead>
    <tbody>
    <?php
          $readConcursoFile= new Read();
          $readConcursoFile->FullRead("SELECT * FROM cs_concurso AS c INNER JOIN cs_concurso_file AS f ON c.concurso_id=f.file_concurso INNER JOIN cs_concurso_file_type AS t ON t.file_type_id= f.file_type WHERE concurso_id={$concurso}");
          if($readConcursoFile->getResult()):
              foreach ($readConcursoFile->getResult() as $file):
                extract($file);
        
        ?> 
        <tr>
          <td>
            <span><b><a href=" <?=HOME.'/uploads/concurso/'.$file_directory?>" target="_blank" title="Visualizar Arquivo"><?=$file_type_name?></a></b></span>
          </td>

          <td class="text-center">
            <span><a class="glyphicon glyphicon-download-alt" href=" <?=HOME.'/uploads/concurso/'.$file_directory?>" target="_blank" title="Visualizar Arquivo"></a></span>
            <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=concurso/details&concurso={$concurso_id}&file={$file_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir este arquivo ?')\" title=\"Deletar\"></a>";?></span>
          </td>

        </tr>
        <?php
              endforeach;
            else:
            MSGErro("<b>Não foram enviados arquivos para este concurso!</b>",MSG_ALERT);    
          endif;  
        ?>
    </tbody>

  </table>
  

</div>