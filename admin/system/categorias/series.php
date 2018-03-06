<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminSeries.class.php');

$series = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$category = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);

$series['category_category'] = (isset($category) ? $category : $series['category_category']);

$disable = (!empty($category) ? 'disabled' : '');

if ($series && !empty($series) && isset($series['sendSeries'])):
    unset($series['sendSeries']);


    $createSeries = new AdminSeries();
    $createSeries->ExeAddSerie($series);

    if ($createSeries->getResult()):
        MSGErro($createSeries->getError()[0], $createSeries->getError()[1]);
    else:
        MSGErro($createSeries->getError()[0], $createSeries->getError()[1]);
    endif;

endif;
?>
<form name="formSerie" action="" method="post" >
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
                <h4 class="text text-primary text-center">Adicione serie(s) a uma categoria</h4>
                <div class="form-group col-lg-8">
                    <div class="form-group col-lg-4">
                        <label>Categoria:</label>
                        <select class="form-control"  <?= $disable ?>  name="category_category" required>
                            <option value="" selected> Selecione uma categoria </option>
                            <?php
                            $readCategory = new Read();
                            $readCategory->ExeRead('cs_category', "ORDER BY category_name ASC");
                            foreach ($readCategory->getResult() as $category):
                                extract($category);

                                echo "<option value=\"{$category_id}\" ";


                                if (isset($series['category_category']) && $series['category_category'] == $category_id):
                                    echo 'selected';
                                endif;
                                echo "> {$category_name}</option>";
                            endforeach;
                            ?>
                        </select>    
                    </div><!--col-lg-4-->
                    <div class="form-group col-lg-4">
                        <label>Serie:</label>
                        <select class="form-control"  name="category_series" required>
                            <option value="" selected> Selecione uma serie </option>
                            <?php
                            $readSeries = new Read();
                            $readSeries->ExeRead('cs_series', "WHERE series_status=1 ORDER BY series_name ASC");
                            foreach ($readSeries->getResult() as $serie):
                                extract($serie);

                                echo "<option value=\"{$series_id}\" ";


                                if (isset($series['category_series']) && $series['category_series'] == $category_id):
                                    echo 'selected';
                                endif;
                                echo "> {$series_name}</option>";
                            endforeach;
                            ?>
                        </select>    
                    </div>	
                </div>	
            </div><!--row-->
            <div class="text-left">
                <input type="submit" class="btn btn-success" name="sendSeries" value="Adicionar Serie"> 
            </div>
        </div>
    </div>
</form>