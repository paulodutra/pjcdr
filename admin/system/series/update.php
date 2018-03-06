<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminSeries.class.php');

$serie = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$serieID = filter_input(INPUT_GET, 'serie', FILTER_VALIDATE_INT);

if ($serie && isset($serie['sendSerie'])):
    unset($serie['sendSerie']);

    $createSerie = new AdminSeries();
    $createSerie->ExeUpdate($serieID, $serie);

    if ($createSerie->getResult()):
        MSGErro($createSerie->getError()[0], $createSerie->getError()[1]);
    else:
        MSGErro($createSerie->getError()[0], $createSerie->getError()[1]);
    endif;
else:

    $readSerie = new Read();
    $readSerie->ExeRead("cs_series", "WHERE series_id=:id", "id={$serieID}");

    if ($readSerie->getResult()):
        $serie = $readSerie->getResult()[0];
    endif;

endif;
?>
<form name="formSerie"  action="" method="post">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
                <h4 class="text text-primary text-center">Cadastro de series</h4>
                <p class="text-primary text-center"><b>OBS:</b>Informe apenas 1 número</p>
                <div class="form-group col-lg-1">
                    <label>Serie:</label>
                    <input type="number" class="form-control" name="series_number" value="<?php
                    if (isset($serie['series_number'])): echo $serie['series_number'];
                    endif;
                    ?>" min="1" max="9" maxlength="1" title="informe somente um número" required>
                </div>

            </div><!--row-->
            <div class="text text-center">
                <input type="submit" class="btn btn-success" name="sendSerie" value="Salvar Serie"> 	
            </div>	
        </div>
</form>            	