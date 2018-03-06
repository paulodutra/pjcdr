<?php
  //$MobilizationID=filter_input(INPUT_GET,'', FILTER_VALIDATE_INT);
  $SchoolID=filter_input(INPUT_GET, 'school',FILTER_VALIDATE_INT);
  $ConcursoID=filter_input(INPUT_GET, 'concurso',  FILTER_VALIDATE_INT);

  $readMobilization=new Read();
  $readMobilization->FullRead("SELECT * FROM es_school INNER JOIN es_school_mobilization ON school_id=mobilization_school WHERE mobilization_concurso={$ConcursoID} AND mobilization_school={$SchoolID}");
  if($readMobilization->getResult()):
    foreach ($readMobilization->getResult() as $school):
        extract($school);
 

?>
<div class="panel panel-primary">

  <!-- Default panel contents -->
  <div class="panel-heading text-center"><?=$school_name?></div>
  <div class="panel-body">
        <p>CNPJ: <?=$school_cnpj?></p>
        <p>INEP: <?=$school_inep?></p>
        <p>Email da escola: <?=$school_email?></p>
        <p>Data de cadastro: <?=date('d/m/Y H:i:s', strtotime($school_date_registration))?></p>    
        <p>Situação: <?=$school_status=($school_status==0) ? 'Inativo': 'Ativo' ?></p>
        <hr>
        <p class="text-primary text-center"><b>Dados de mobilização</b></p>
        <p>Números de estudantes mobilizados: <?=$mobilization_number_student?></p>
        <p>Números de professores mobilizados: <?=$mobilization_number_teachers?></p>
        <p>Números de redações realizadas: <?=$mobilization_number_redaction?></p>
        <hr>
        <p class="text-primary text-center"><b>Descrição das atividades</b></p>
        <div class="panel panel-default">
          <div class="panel-body">
           <?=$mobilization_description?>
          </div>
        </div>
  <?php
  
     endforeach;
  endif;  
  ?>   
  </div>
   <?php
 /**
        * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
        que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
     */
    require_once('_models/AdminMobilization.class.php');
    $MobilizationID=filter_input(INPUT_GET,'mobilization',FILTER_VALIDATE_INT);
    $action=filter_input(INPUT_GET,'action', FILTER_DEFAULT);

    if($action && $MobilizationID):

        switch ($action):
            case 'delete':
               $deleteMobilization= new AdminMobilization();
               $deleteMobilization->deleteFiles($MobilizationID);
               MSGErro($deleteMobilization->getError()[0],$deleteMobilization->getError()[1]);
                break;
            
            default:
                MSGErro("<b>Ação não existe:</b> Utilize os botões!", MSG_ERROR);
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
      $readMobilization= new Read();
      $readMobilization->FullRead("SELECT * FROM es_school_mobilization_file INNER JOIN es_school_mobilization_type ON mobilization_file_type=mobilization_type_id WHERE mobilization_file_concurso={$ConcursoID} AND mobilization_file_school={$SchoolID} ");
      if($readMobilization->getResult()):

        foreach ($readMobilization->getResult() as $mobilization):
            extract($mobilization); 
            

    ?>
      <tr>
          <td><?=$mobilization_type_name?></td>
          <td>
            <span><a class="glyphicon glyphicon-download-alt" href="<?=HOME.'/uploads/concurso/'.$mobilization_file_directory ?>" target="_blank" title="Visualizar Arquivo"></a></span>
            <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=mobilization/details&concurso={$ConcursoID}&school={$SchoolID}&mobilization={$mobilization_file_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir este arquivo ?')\" title=\"Deletar\"></a>";?></span>
          </td>
      </tr>
     <?php
      
         endforeach;
         else:
          MSGErro("Nenhum arquivo de mobilização foi enviado !",MSG_ALERT);
      endif;  
      ?>   
    </tbody>

</div>