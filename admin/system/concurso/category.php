<?php

/**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
    require_once('_models/AdminConcurso.class.php');

$categoryes=filter_input_array(INPUT_POST, FILTER_DEFAULT);
$concurso=filter_input(INPUT_GET,'concurso', FILTER_VALIDATE_INT);

$categoryes['concurso_category_concurso']=(isset($concurso) ? $concurso: $categoryes['concurso_category_concurso']);

$disable=(!empty($concurso) ? 'disabled' :'');

if($categoryes && isset($categoryes['sendCategory'])):
	unset($categoryes['sendCategory']);

	$createCategory= new AdminConcurso();
	$createCategory->ExeAddCategory($categoryes);

	if($createCategory->getResult()):
		MSGErro($createCategory->getError()[0],$createCategory->getError()[1]);
	else:
		MSGErro($createCategory->getError()[0],$createCategory->getError()[1]);
	endif;	


endif;



?>

<form name="formCategory" action="" method="post">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
            	<h4 class="text text-primary text-center">Adicione categorias a um concurso</h4>
            	<div class="form-group col-lg-9">
					<div class="form-group col-lg-5">
                        <label>Concurso:</label>
                        <select class="form-control" <?=$disable?>  name="concurso_category_concurso" required>
                            <option value="" selected> Selecione um concurso </option>
                            <?php
                                $readConcurso= new Read();
                                $readConcurso->ExeRead('cs_concurso',"WHERE concurso_status=1 ORDER BY concurso_name ASC");
                                foreach ($readConcurso->getResult() as $concursos):
                                    extract($concursos);

                                     echo "<option value=\"{$concurso_id}\" ";


                                    if(isset($categoryes['concurso_category_concurso'])&&$categoryes['concurso_category_concurso']==$concurso_id):
                                         echo 'selected';
                                    endif;    
                                    echo "> {$concurso_name}</option>";
                                endforeach;
                                

                            ?>
                        </select>    
                 	 </div><!--col-lg-5-->
                 	 <div class="form-group col-lg-4">
                        <label>Categoria:</label>
                        <select class="form-control"  name="concurso_category_category" required>
                            <option value="" selected> Selecione uma categoria </option>
                            <?php
                                $readCategory= new Read();
                                $readCategory->ExeRead('cs_category',"ORDER BY category_name ASC");
                                foreach ($readCategory->getResult() as $categories):
                                    extract($categories);

                                     echo "<option value=\"{$category_id}\" ";


                                    if(isset($categoryes['concurso_category_category'])&&$categoryes['concurso_category_category']==$category_id):
                                         echo 'selected';
                                    endif;    
                                    echo "> {$category_name}</option>";
                                endforeach;
                                

                            ?>
                        </select>    
                 	 </div><!--col-lg-4-->
                </div> 	 
			
			</div><!--row-->
		</div>
	</div>
	 <div class="text text-center">
        <input type="submit" class="btn btn-success" name="sendCategory" value="Adicionar Categoria"> 	
     </div>	
</form>				 