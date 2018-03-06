<?php

require('../../_app/Config.inc.php');

$list = (int) strip_tags(trim($_POST['lista']));

$readSchool = new Read;
$readSchool->ExeRead('es_school', "WHERE school_status = {$list} ORDER BY school_name ASC");

if ($readSchool->getResult()):

    sleep(2);

    echo "<option value=\"\" disabled selected> Selecione a escola </option>";
    foreach ($readSchool->getResult() as $escolas):
        extract($escolas);
        echo "<option value=\"{$school_id}\">{$school_name}/CNPJ:{$school_cnpj} </option>";
    endforeach;
else:
     echo "<option value=\"\">Não há escolas com o paramétro informado</option>";
endif;



