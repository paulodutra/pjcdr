<?php


require('../../_app/Config.inc.php');

$escola = (int) strip_tags(trim($_POST['escola']));
$readStudents = new Read;
$readStudents->ExeRead("es_school_participant", "WHERE participant_school=:school AND participant_type=2 ", "school={$escola}");

sleep(1);

echo "<option value=\"\" disabled selected> Selecione o estudante </option>";
foreach ($readStudents->getResult() as $estudantes):
    extract($estudantes);
    echo "<option value=\"{$participant_id}\"> {$participant_name} </option>";
endforeach;

