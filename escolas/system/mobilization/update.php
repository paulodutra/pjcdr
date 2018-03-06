<?php

 /**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
  require_once('_models/AdminMobilization.class.php');


  $Mobilization=filter_input_array(INPUT_POST, FILTER_DEFAULT);
  $MobilizationID=filter_input(INPUT_GET,'mobilization', FILTER_VALIDATE_INT);

  if($Mobilization && isset($Mobilization['sendMobilization'])):
    		unset($Mobilization['sendMobilization']);

    	
    		//die("Parando no create");
  		
  		$createMobilization= new AdminMobilization();
  		$createMobilization->ExeUpdate($MobilizationID,$Mobilization);

  		if($createMobilization->getResult()):
  			MSGErro($createMobilization->getError()[0], $createMobilization->getError()[1]);	

  		else:
  			 MSGErro($createMobilization->getError()[0], $createMobilization->getError()[1]);		

  		endif;	
  else:
    $readMobilization= new Read();
    $readMobilization->ExeRead("es_school_mobilization","WHERE mobilization_id=:id","id={$MobilizationID}");
    if($readMobilization->getResult()):
      $Mobilization=$readMobilization->getResult()[0];
    endif;    

  endif;	



?>







<form name="formMobilization"  action="" method="post" enctype="multipart/form-data">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">


            		<div class="form-group col-lg-5">
                        <label>Concurso:</label>
                        <select class="form-control" name="mobilization_concurso" required>
                            <option value="" selected> Selecione um concurso </option>
                            <?php
                                $readConcurso= new Read();
                                $readConcurso->ExeRead('cs_concurso',"WHERE concurso_status=1 ORDER BY concurso_name ASC");
                                foreach ($readConcurso->getResult() as $concurso):
                                    extract($concurso);

                                     echo "<option value=\"{$concurso_id}\" ";


                                    if(isset($Mobilization['mobilization_concurso']) &&  $Mobilization['mobilization_concurso']==$concurso_id):
                                         echo 'selected';
                                    endif;    
                                    echo "> {$concurso_name}";
                                endforeach;
                                

                            ?>
                        </select>    
                 	 </div><!--col-lg-5-->

           			 <div class="form-group col-lg-5">
                        <label>Escola:</label>
                        <select class="form-control" name="mobilization_school" required>
                            <option value="" selected> Selecione uma escola </option>
                            <?php
                                $readSchool= new Read();
                                $readSchool->ExeRead('es_school',"WHERE school_status=1 AND school_id={$userSchool['school_id']} ORDER BY school_name ASC");
                                foreach ($readSchool->getResult() as $school):
                                    extract($school);

                                     echo "<option value=\"{$school_id}\" ";


                                    if(isset($Mobilization['mobilization_school']) && $Mobilization['mobilization_school']==$school_id):
                                         echo 'selected';
                                    endif;    
                                    echo "> {$school_name}/ CNPJ: {$school_cnpj}</option>";
                                endforeach;
                                

                            ?>
                        </select>    
                 	 </div><!--col-lg-5-->
                 	 <div class="form-group- col-lg-11">
	                 	 <p class="text-center text-primary">Quantitativo Mobilizado</p><hr>
		                 	 <div class="form-group col-lg-3">
		                   	 	<label>Nº Total de Professores Mobilizados:</label>
		                    	<input type="Number" class="form-control"  placeholder="Nº de Professores" name="mobilization_number_teachers" value="<?php if (isset($Mobilization['mobilization_number_teachers'])): echo $Mobilization['mobilization_number_teachers']; endif; ?>" required>*somente números
		                	</div>
		                	<div class="form-group col-lg-3">
		                   	 	<label>Nº Total de Estudantes Mobilizados:</label>
		                    	<input type="Number" class="form-control"  placeholder="Nº de Estudantes" name="mobilization_number_student" value="<?php if (isset($Mobilization['mobilization_number_student'])): echo $Mobilization['mobilization_number_student']; endif; ?>" required>*somente números
		                	</div>
		                	<div class="form-group col-lg-3">
		                   	 	<label>Nº Total de Redações Realizadas:</label>
		                    	<input type="Number" class="form-control"  placeholder="Nº de Estudantes" name="mobilization_number_redaction" value="<?php if (isset($Mobilization['mobilization_number_redaction'])): echo $Mobilization['mobilization_number_redaction']; endif; ?>" required>*somente números
		                	</div>
	                </div>	
                  
                  <div class="form-group col-lg-7 ">
                      <label>Descrição das atividades:</label>
                        <textarea class="form-control" name="mobilization_description"rows="10" required><?php if (isset($Mobilization['mobilization_description'])) echo htmlspecialchars($Mobilization['mobilization_description']); ?></textarea>
                  </div>
                  
                  <div class="form-group col-lg-7">
                    <input type="checkbox" name="mobilization_terms" value="1" title="Leia o termo clicando no icone ao lado, e caso concorde com o mesmo, marque a caixa" <?php if(isset($Mobilization['mobilization_terms'])): echo 'checked=\"checked\"'; endif; ?> required>Li e concordo com todos os termos <a class="glyphicon glyphicon-file" href="dashboard.php?exe=mobilization/terms" target="_blank" title="Visualizar termo"></a></div>
                  </div>

            </div><!--row-->
             <div class="text-center">
                <input type="submit" class="btn btn-success" name="sendMobilization" value="Salvar Mobilização"> 
            </div>
        </div>
    </div>
</form>            