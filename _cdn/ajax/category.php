<?php


require('../../_app/Config.inc.php');

$concursoAtivo = (int) strip_tags(trim($_POST['concurso']));
$readCategory = new Read;
$readCategory->FullRead("SELECT * FROM `cs_concurso_category` INNER JOIN cs_category ON concurso_category_category=category_id WHERE concurso_category_concurso={$concursoAtivo} ORDER BY category_name ASC");

sleep(1);

echo "<option value=\"\" disabled selected> Selecione a categoria </option>";
foreach ($readCategory->getResult() as $categorias):
    extract($categorias);
    echo "<option value=\"{$category_id}\"> {$category_name} </option>";
endforeach;
