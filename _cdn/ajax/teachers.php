<?php


require('../../_app/Config.inc.php');

$escola = (int) strip_tags(trim($_POST['escola']));
$readTeachers = new Read;
$readTeachers->ExeRead("es_school_participant", "WHERE participant_school=:school AND participant_type=1 ", "school={$escola}");

sleep(1);

echo "<option value=\"\" disabled selected> Selecione o professor </option>";
foreach ($readTeachers->getResult() as $professores):
    extract($professores);
    echo "<option value=\"{$participant_id}\"> {$participant_name} </option>";
endforeach;

