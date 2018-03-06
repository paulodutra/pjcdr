<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminCategory.class.php');

$Category = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (isset($Category) && $Category['sendCategory']):

    unset($Category['sendCategory']);

    $createCategory = new AdminCategory();

    $createCategory->ExeCreate($Category);

    if ($createCategory->getResult()):
        MSGErro($createCategory->getError()[0], $createCategory->getError()[1]);
    else:
        MSGErro($createCategory->getError()[0], $createCategory->getError()[1]);
    endif;

endif;
?>
<form name="formCategoria" action="" method="post" >
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
                <h4 class="text text-primary text-center">Cadastro categorias</h4>
                <div class="form-group col-lg-7">
                    <label>Nome da categoria:</label>
                    <input type="text" class="form-control"  placeholder="Nome da Categoria" name="category_name" value="<?php if (isset($Category)) echo $Category['category_name']; ?>" required>
                </div>
                <div class="form-group col-lg-7">
                    <label>Descrição da categoria:</label>
                    <textarea class="form-control" name="category_description" rows="10" required><?php if (isset($Category)) echo $Category['category_description']; ?></textarea>
                </div>
                <div class="form-group col-lg-8">
                    <div class="form-group col-lg-4">
                        <label>Tipo de ensino:</label>
                        <select class="form-control" name="category_education" required>
                            <option value="" selected> Selecione o tipo de ensino </option>
                            <?php
                            $readType = new Read();
                            $readType->ExeRead('cs_category_education', "ORDER BY education_name ASC");
                            foreach ($readType->getResult() as $type):
                                extract($type);

                                echo "<option value=\"{$education_id}\" ";


                                if (isset($concurso['category_education']) && $concurso['category_education'] == $education_id):
                                    echo 'selected';
                                endif;
                                echo "> {$education_name}</option>";
                            endforeach;
                            ?>
                        </select>    
                    </div><!--col-lg-4-->
                    <div class="form-group col-lg-4">
                        <label>Tipo de Modalidade:</label>
                        <select class="form-control" name="category_modality" required>
                            <option value="" selected> Selecione o tipo de modalidade </option>
                            <?php
                            $readModality = new Read();
                            $readModality->ExeRead('cs_category_modality', "ORDER BY modality_name ASC");
                            foreach ($readModality->getResult() as $modality):
                                extract($modality);

                                echo "<option value=\"{$modality_id}\" ";


                                if (isset($concurso['category_modality']) && $concurso['category_modality'] == $modality_id):
                                    echo 'selected';
                                endif;
                                echo "> {$modality_name}</option>";
                            endforeach;
                            ?>
                        </select>    
                    </div><!--col-lg-4-->

                </div>

            </div><!--row-->
            <div class="text-center">
                <input type="submit" class="btn btn-success" name="sendCategory" value="Salvar categoria "> 

            </div>
        </div>
    </div>
</form>