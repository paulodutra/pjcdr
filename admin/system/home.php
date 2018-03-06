<?php

$readContent= new Read();
/** ESCOLAS CADASTRADAS*/
$readContent->FullRead("SELECT COUNT(school_name) AS escolas FROM es_school ");
$school=$readContent->getResult()[0]['escolas'];

/** ESCOLAS COM CADASTRO ATIVO*/
$readContent->FullRead("SELECT COUNT(school_name) AS escolas_ativas FROM es_school WHERE school_status=1 ");
$schoolActive=$readContent->getResult()[0]['escolas_ativas'];

/** ESCOLAS COM CADASTRO INATIVO*/
$readContent->FullRead("SELECT COUNT(school_name) AS escolas_inativas FROM es_school WHERE school_status=0 ");
$schoolInative=$readContent->getResult()[0]['escolas_inativas'];

/** Nº GERAL DE PARTICIPANTES*/
$readContent->FullRead("SELECT COUNT(participant_id) AS participantes FROM es_school_participant ");
$participant=$readContent->getResult()[0]['participantes'];

/** Nº GERAL DE ALUNOS*/
$readContent->FullRead("SELECT COUNT(participant_id) AS estudantes FROM es_school_participant WHERE participant_type=2 ");
$student=$readContent->getResult()[0]['estudantes'];


/** Nº GERAL DE PROFESSORES*/
$readContent->FullRead("SELECT COUNT(participant_id) AS professores FROM es_school_participant WHERE participant_type=1 ");
$teacher=$readContent->getResult()[0]['professores'];

/** Nº DE INSCRIÇÕES NO ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT COUNT(subscribers_id) AS inscricoes FROM cs_concurso INNER JOIN cs_concurso_subscribers ON subscribers_concurso=concurso_id WHERE concurso_status=1 ORDER BY concurso_date_registration DESC LIMIT 1");
$subscribers=$readContent->getResult()[0]['inscricoes'];

/** Nº DE ESCOLAS INSCRITAS NO ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT COUNT(mobilization_concurso) AS schools FROM cs_concurso INNER JOIN es_school_mobilization ON concurso_id=mobilization_concurso WHERE concurso_status=1 ORDER BY concurso_date_registration DESC LIMIT 1");
$schools=$readContent->getResult()[0]['schools'];


/** Nº DE ALUNOS MOBILIZADOS NO  ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT SUM(mobilization_number_student) AS students FROM cs_concurso INNER JOIN es_school_mobilization ON concurso_id=mobilization_concurso WHERE concurso_status=1 ORDER BY concurso_date_registration DESC LIMIT 1");
$students=$readContent->getResult()[0]['students'];


/** Nº DE PROFESSORES MOBILIZADOS NO  ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT SUM(mobilization_number_teachers) AS teachers FROM cs_concurso INNER JOIN es_school_mobilization ON concurso_id=mobilization_concurso WHERE concurso_status=1 ORDER BY concurso_date_registration DESC LIMIT 1");
$teachers=$readContent->getResult()[0]['teachers'];



/** Nº DE REDAÇÔES REALIZADAS PELAS ESCOLAS NO ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT SUM(mobilization_number_redaction) AS redactions FROM cs_concurso INNER JOIN es_school_mobilization ON concurso_id=mobilization_concurso WHERE concurso_status=1 ORDER BY concurso_date_registration DESC LIMIT 1");
$redactions=$readContent->getResult()[0]['redactions'];

/** Nº DE REDAÇÕES ENVIADAS PELAS ESCOLAS NO  ULTIMO CONCURSO DE REDAÇÃO ATIVO*/
$readContent->FullRead("SELECT COUNT(subscribers_redaction) AS redactions_sender FROM cs_concurso INNER JOIN cs_concurso_subscribers ON concurso_id=subscribers_concurso WHERE concurso_status ORDER BY concurso_date_registration DESC LIMIT 1");
$redactions_sender=$readContent->getResult()[0]['redactions_sender'];

/** Nº DE ARQUIVOS DE MOBILIZAÇÔES ENVIADOS PELAS ESCOLAS NO ULTIMO CONCURSO DE REDAÇÂO ATIVO*/
$readContent->FullRead("SELECT COUNT(mobilization_file_directory) AS number_mobilizations FROM cs_concurso INNER JOIN es_school_mobilization_file ON concurso_id=mobilization_file_concurso WHERE concurso_status=1 ORDER BY concurso_date_registration DESC LIMIT 1");
$number_mobilizations=$readContent->getResult()[0]['number_mobilizations'];



?>
<div class="row">
    <div class="col-lg-6">
        <h1 class="text-primary text-uppercase">Estatísticas Gerais:</h1><hr>
        <ul class="list-group">
            <li class="list-group-item">
                <span class="badge"><?=$school?></span>
                Escolas inscritas
            </li>

             <li class="list-group-item">
                <span class="badge"><?=$schoolActive?></span>
                Escolas com cadastro Ativo
            </li>

             <li class="list-group-item">
                <span class="badge"><?=$schoolInative?></span>
                Escolas com cadastro Desativado
            </li>

            <li class="list-group-item">
                <span class="badge"><?=$participant?></span>
               Nº geral de participantes
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$student?></span>
                 Estudantes inscritos
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$teacher?></span>   
                 Professores inscritos   
               
            </li>
        </ul>
        <h1 class="text-primary text-uppercase">Dados do Concurso Ativo</h1><hr>
        <ul class="list-group">
            <li class="list-group-item">
                <span class="badge"><?=$subscribers?></span>
                 Inscrições
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$schools?></span>
                Escolas Inscritas
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$students=($students=='' ? '0' : $students)?></span>
                Nº de alunos mobilizados
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$teachers=($teachers=='' ? '0' :  $teachers)?></span>
                Nº de professores mobilizados
            </li>
             <li class="list-group-item">
                <span class="badge"><?=$number_mobilizations?></span>
                Nº de arquivos de mobilizações enviados pelas escolas
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$redactions=($redactions=='' ? '0' : $redactions)?></span>
                Nº de Redações Realizadas pelas escolas
            </li>
            <li class="list-group-item">
                <span class="badge"><?=$redactions_sender?></span>
                Nº de Redações enviadas pelas escolas
            </li>

        </ul>
        <h1 class="text-primary text-uppercase">Veja as inscrições do concurso Ativo por região</h1><hr> 
        <a  class="btn btn-block btn-primary" href="dashboard.php?exe=subscribers/region">Inscrições por <span class="badge">região</span></a>
       
      
    </div><!--col-lg-6-->
    <div class="col-lg-6">
    <h1 class="text-primary text-uppercase">Instruções de uso:</h1><hr>
        <ol>
            <li>Todos os recursos, presentes na área da escola, também estão aqui;</li>
            <li>Em caso de dúvida por parte da escola, temos o manual de utilização;</li>
            <li>Tome muito cuidado na exclusão de qualquer informação;</li>
            <li>Qualquer dúvida, entre em contato com a equipe de desenvolvimento.</li>
        </ol>
    </div>

</div><!--row-->
