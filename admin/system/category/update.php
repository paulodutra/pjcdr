<?php

/**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */

 require('_models/AdminCategoryPost.class.php');

if (!class_exists('Login')):
    header("Location: ../../index.php");
    die();
endif;
?>
<form name="" novalidate id="" action="" method="post" >
    <div class="tab-content">
        <?php
            $Data=filter_input_array(INPUT_POST, FILTER_DEFAULT);
            $categoryID=filter_input(INPUT_GET,'categoria', FILTER_VALIDATE_INT);
            
            if (!empty($Data['SendForm'])):
               
                unset($Data['SendForm']);
               
                $createCategory = new AdminCategoryPost();
                $createCategory->ExeUpdate($categoryID,$Data);
                if (!$createCategory->getResult()):
                    MSGErro($createCategory->getError()[0], $createCategory->getError()[1]);
                else:
                     MSGErro($createCategory->getError()[0], $createCategory->getError()[1]);
                    //header('Location: dashboard.php?exe=category/index&create=true&catid=' . $createCategory->getResult());
                endif;

            else:
                $readCategory= new Read();
                $readCategory->ExeRead('blog_categories',"WHERE category_id=:id","id={$categoryID}");    
                if($readCategory->getResult()):
                    $Data=$readCategory->getResult()[0];
                endif;

            endif;
        ?>
        <div id="step1" class="p-m tab-pane active">
            <div class="row">

                <div class="form-group col-lg-7">
                    <label>Titulo:</label>
                    <input type="text" class="form-control"  placeholder="Titulo" name="category_name" value="<?php if (isset($Data)) echo $Data['category_name']; ?>" required>
                </div>
                <div class="form-group col-lg-7">
                    <label>Descrição:</label>
                    <textarea class="form-control" name="category_description" rows="10" required><?php if (isset($Data)) echo $Data['category_description']; ?></textarea>
                </div>
                <div class="form-group col-lg-9">
                    <div class="form-group col-lg-4">
                        <label>Data:</label>
                        <input type="text" class="form-control"  id="datetime" name="category_date"  value="<?= date('d/m/Y H:i:s') ?>" required>
                    </div>
                    <div class="form-group col-lg-5">
                        <label>Seção:</label>
                        <select name="category_parent" class="form-control">
                            <option value="null">Selecionar seção</option>
                            <?php
                            $readSession = new Read();
                            $readSession->ExeRead('blog_categories', "WHERE category_parent IS NULL ORDER BY category_name ASC");
                            if (!$readSession->getResult()):
                                echo '   <option disabled="disabled" value="null"> Cadastre antes uma seção ! </option>';
                            else:
                                foreach ($readSession->getResult() as $Session):
                                    echo "<option value=\"{$Session['category_id']}\" ";

                                    if ($Session['category_id'] == $Data['category_parent']):
                                        echo 'selected="selected" ';
                                    endif;

                                    echo ">{$Session['category_name']}</option>";
                                endforeach;
                            endif;
                            ?>

                        </select>
                    </div>

                </div>


            </div><!--row-->
            <div class="text-center">
                <input type="submit" class="btn btn-success" name="SendForm" value="Salvar categoria "> 

            </div>
        </div>
    </div>
</form>