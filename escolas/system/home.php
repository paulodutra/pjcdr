<?php

$readContent= new Read();

/** Nº GERAL DE PARTICIPANTES*/
$readContent->FullRead("SELECT COUNT(participant_id) AS participantes FROM es_school_participant WHERE participant_school={$userSchool['school_id']}");
$participant=$readContent->getResult()[0]['participantes'];

/** Nº GERAL DE ALUNOS*/
$readContent->FullRead("SELECT COUNT(participant_id) AS estudantes FROM es_school_participant WHERE participant_type=2 AND participant_school={$userSchool['school_id']}");
$student=$readContent->getResult()[0]['estudantes'];


/** Nº GERAL DE PROFESSORES*/
$readContent->FullRead("SELECT COUNT(participant_id) AS professores FROM es_school_participant WHERE participant_type=1 AND participant_school={$userSchool['school_id']}");
$teacher=$readContent->getResult()[0]['professores'];



/** Nº DE ALUNOS MOBILIZADOS NO  ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT SUM(mobilization_number_student) AS students FROM cs_concurso INNER JOIN es_school_mobilization ON concurso_id=mobilization_concurso WHERE concurso_status=1 AND mobilization_school={$userSchool['school_id']} ORDER BY concurso_date_registration DESC LIMIT 1");
$students=$readContent->getResult()[0]['students'];


/** Nº DE PROFESSORES MOBILIZADOS NO  ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT SUM(mobilization_number_teachers) AS teachers FROM cs_concurso INNER JOIN es_school_mobilization ON concurso_id=mobilization_concurso WHERE concurso_status=1 AND mobilization_school={$userSchool['school_id']} ORDER BY concurso_date_registration DESC LIMIT 1");
$teachers=$readContent->getResult()[0]['teachers'];



/** Nº DE REDAÇÔES REALIZADAS PELAS ESCOLAS NO ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT SUM(mobilization_number_redaction) AS redactions FROM cs_concurso INNER JOIN es_school_mobilization ON concurso_id=mobilization_concurso WHERE concurso_status=1 AND mobilization_school={$userSchool['school_id']} ORDER BY concurso_date_registration DESC LIMIT 1");
$redactions=$readContent->getResult()[0]['redactions'];

/** Nº DE REDAÇÕES ENVIADAS PELAS ESCOLAS NO  ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT COUNT(subscribers_redaction) AS redactions_sender FROM cs_concurso INNER JOIN cs_concurso_subscribers ON concurso_id=subscribers_concurso WHERE concurso_status=1 AND subscribers_school={$userSchool['school_id']} ORDER BY concurso_date_registration DESC LIMIT 1");
$redactions_sender=$readContent->getResult()[0]['redactions_sender'];

/** Nº DE ARQUIVOS DE MOBILIZAÇÔES ENVIADOS PELAS ESCOLAS NO ULTIMO CONCURSO DE REDAÇÂO ATIVO*/
$readContent->FullRead("SELECT COUNT(mobilization_file_directory) AS number_mobilizations FROM cs_concurso INNER JOIN es_school_mobilization_file ON concurso_id=mobilization_file_concurso WHERE concurso_status=1 AND mobilization_file_school={$userSchool['school_id']} ORDER BY concurso_date_registration DESC LIMIT 1");
$number_mobilizations=$readContent->getResult()[0]['number_mobilizations'];

?>
<div class="row">
    <div class="col-lg-6">
        <h1 class="text-primary text-uppercase">Estatísticas Gerais Da Escola:</h1><hr>
        <ul class="list-group">
            
            <li class="list-group-item">
                <span class="badge"><?=$participant?></span>
               Nº geral de participantes cadastrados
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$student=($student ? $student : '0')?></span>
                 Estudantes inscritos
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$teacher?></span>   
                 Professores inscritos   
               
            </li>
              <li class="list-group-item">
                <span class="badge"><?=$students=($students ? $students : '0')?></span>
                Nº de alunos mobilizados
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$teachers=($teachers=='' ? '0' : $teachers)?></span>
                Nº de professores mobilizados
            </li>
             <li class="list-group-item">
                <span class="badge"><?=$number_mobilizations=($number_mobilizations=='' ? '0' : $number_mobilizations)?></span>
                Nº de arquivos de mobilizações enviados 
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$redactions=($redactions ?  $redactions : '0')?></span>
                Nº de Redações Realizadas 
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$redactions_sender=($redactions_sender='' ? '0' : $redactions_sender)?></span>
                Nº de Redações enviadas 
            </li>
        </ul>
        
    </div><!--col-lg-6-->
    <div class="col-lg-6">
        
       <h1 class="text-primary  text-uppercase">Instruções de cadastro</h1><hr>
       <ol>
           <li>Cadastre os itens de acordo com a ordem do menu;</li>
           <li>Fique atento ao prazo de inscrição da redação;</li>
           <li>Em casos de dúvidas entre em contato: dpunasescolas@dpu.gov.br.</li>
       </ol>



    </div>

    
</div><!--row-->
