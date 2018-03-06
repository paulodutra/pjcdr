<?php


require('../../_app/Config.inc.php');

$categoria = (int) strip_tags(trim($_POST['categoria']));
$readSeries = new Read;
$readSeries->FullRead("SELECT * FROM cs_category_series INNER JOIN cs_series ON category_series=series_id WHERE series_status=1 AND category_category={$categoria} ORDER BY series_name");

sleep(1);

echo "<option value=\"\" disabled selected> Selecione a serie </option>";
foreach ($readSeries->getResult() as $series):
    extract($series);
    echo "<option value=\"{$series_id}\"> {$series_name} </option>";
endforeach;