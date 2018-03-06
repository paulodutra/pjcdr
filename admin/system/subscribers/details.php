<?php

$school=filter_input(INPUT_GET,'school', FILTER_VALIDATE_INT);
$student=filter_input(INPUT_GET,'student', FILTER_VALIDATE_INT);
$concurso=filter_input(INPUT_GET,'concurso', FILTER_VALIDATE_INT);

$readSchool= new Read();
$readSchool->FullRead("SELECT * FROM es_school INNER JOIN app_estados AS e ON school_uf=e.estado_id 
  INNER JOIN app_cidades AS c ON c.cidade_id=school_city WHERE school_id={$school}");
if($readSchool->getResult()):
  foreach ($readSchool->getResult() as $schoolResult):
    //var_dump($school);
  //die("Parando no details");
    extract($schoolResult);
    
?>
<div class="panel panel-primary">

  <!-- Default panel contents -->
  <div class="panel-heading text-center"><?=$school_name?></div>
  <div class="panel-body">
        <p>CNPJ:<?=$school_cnpj?></p>
        <p>Cidade-UF:<?=$cidade_nome?>-<?=$cidade_uf?></p>
        <p>Data de inscrição <?=date('d/m/Y H:i:s',strtotime($school_date_registration))?></p>
        <hr>
        <?php
            endforeach;

        endif;  
        ?>
       <p>Telefone(s) - Tipo de telefone:</p>
        <?php
          $readPhone = new Read();
          $readPhone->FullRead("SELECT * FROM `es_school_phone` INNER JOIN es_phone_type ON type_id=phone_type WHERE phone_school={$school}");
           if($readPhone->getResult()):
              foreach ($readPhone->getResult() as $schoolphone):
                extract($schoolphone);    
        ?>
        
        <p><?=$phone_telephone?> - <?=$type_name?></p><br>

        <?php
          endforeach;
         else:
          MSGErro("A escola não possui telefones cadastrados <a href=\"dashboard.php?exe=phone/create&school={$school}\">clique aqui</a> para cadastrar! ",MSG_ALERT);  
        endif;

        $readCount= new Read();
        $readCount->FullRead("SELECT COUNT(subscribers_teacher) AS teachers FROM `cs_concurso_subscribers` WHERE subscribers_school={$school}");
        
        $Teachers=$readCount->getResult()[0]['teachers'];

        $readCount->FullRead("SELECT COUNT(subscribers_student) AS students FROM `cs_concurso_subscribers` WHERE subscribers_school={$school}");
        
        $Students=$readCount->getResult()[0]['students'];
      
        ?>

        <hr>
        <p>Nº de alunos inscritos: <?=$Students?></p>
        <p>Nº de professores inscritos: <?=$Teachers?></p>
  </div>

  <?php
    $readTeacher= new Read();
    $readTeacher->FullRead("SELECT * FROM `cs_concurso_subscribers` INNER JOIN es_school_participant ON subscribers_teacher=participant_id WHERE subscribers_student={$student}");
    if($readTeacher->getResult()):
        foreach ($readTeacher->getResult() as $teachers):
            extract($teachers);

  ?>
  <!-- List group -->
  <ul class="list-group">
    <li class="list-group-item">Professor: <?=$participant_name?></li>
    <li class="list-group-item">CPF: <?=$participant_cpf?></li>
    <?php 
        endforeach;
    endif; 
    $readStudent=new Read();
    $readStudent->FullRead("SELECT * FROM cs_concurso_subscribers INNER JOIN es_school_participant ON participant_id=subscribers_student INNER JOIN cs_category ON category_id=subscribers_category INNER JOIN cs_category_education ON education_id=category_education INNER JOIN cs_category_modality ON modality_id=category_modality INNER JOIN cs_series ON series_id=subscribers_series WHERE participant_id={$student}");

      if($readStudent->getResult()):
        foreach ($readStudent->getResult() as $students):
          extract($students);
  
    ?>
    <li class="list-group-item">Estudante: <?=$participant_name?></li>
    <li class="list-group-item">CPF: <?=$participant_cpf?></li>
    <li class="list-group-item">Categoria: <?=$category_name?></li>
    <li class="list-group-item">Serie: <?=$series_name?></li>
    <li class="list-group-item">Modalidade: <?=$modality_name?></li>
    <li class="list-group-item">Tipo de ensino: <?=$education_name?></li>
    <?php
           endforeach;

      endif;  

    ?>
    <hr>
  </ul>

</div>
