<?php

/**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminSubscribers.class.php');



	$subscribers=filter_input_array(INPUT_POST, FILTER_DEFAULT);

	if(!empty($subscribers) && isset($subscribers['sendSubscribers'])):

		unset($subscribers['sendSubscribers']);

		$subscribers['subscribers_redaction']=($_FILES['subscribers_redaction']);

		

		$createSubscriber= new AdminSubscribers();

		$createSubscriber->ExeCreate($subscribers);

		

		if($createSubscriber->getResult()):
			MSGErro($createSubscriber->getError()[0],$createSubscriber->getError()[1]);

		else:	
			MSGErro($createSubscriber->getError()[0],$createSubscriber->getError()[1]);

		endif;	
		//MSGErro($createSubscriber->getError()[0],$createSubscriber->getError()[1]);
	endif;	



?>


<form name="formSubscribers" action="" method="post" enctype="multipart/form-data">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
              <h4 class="text text-primary text-center">Inscrição de participantes no concurso</h4>
            	<div class="form-group col-lg-10 ">
	              	<label>Concurso:</label>
	                	<select  class="j_loadconcurso form-control"  name="subscribers_concurso" required>
	                    	<option value="" selected> Selecione o concurso </option>
	                    	<?php
	                            $readConcurso= new Read();
	                            $readConcurso->ExeRead('cs_concurso',"WHERE concurso_status=1 ORDER BY concurso_name ASC");
	                            foreach ($readConcurso->getResult() as $concurso):
	                                extract($concurso);
	                                 echo "<option value=\"{$concurso_id}\" ";
	                                if(isset($subscribers['subscribers_concurso'])&&$subscribers['subscribers_concurso']==$concurso_id):
	                                     echo 'selected';
	                                endif;    
	                                echo "> {$concurso_name}</option>";
	                            endforeach;                   
                       		 ?>
	                    </select>                   
	            </div>
	             
	            <div class="form-group col-lg-5">
	              	<label>Escola:</label>
	                	<select  class="j_loadschool form-control"  name="subscribers_school" required>
	                    	<option value="" selected> Selecione a Escola </option>
	                    	<?php
                                $readSchool = new Read;
                                $readSchool->ExeRead("es_school", "WHERE school_status=1 AND school_id={$userSchool['school_id']} ORDER BY school_name ASC");
                                foreach ($readSchool->getResult() as $school):
                                    extract($school);
                                    echo "<option value=\"{$school_id}\" ";

                                    if (isset($subscribers['subscribers_school']) && $subscribers['subscribers_school'] == $school_id):
                                        echo 'selected';
                                    endif;

                                    echo "> {$school_name} / {$school_cnpj} </option>";
                                endforeach;
                                ?>                        
                            </select>

	                    </select>

	                   
	              </div>
	              <div class="form-group col-lg-5 ">
	              	<label>Professor:</label>
	                	<select  class="j_loadteachers form-control"  name="subscribers_teacher" required>
	                	 <?php if (!isset($subscribers['subscribers_school'])): ?>
	                    	<option value="" selected> Selecione antes a escola </option>
	                    	<?php
	                    	else:
                                $readTeacher = new Read;
                                $readTeacher->ExeRead("es_school_participant", "WHERE participant_type=1 AND participant_school={$subscribers['subscribers_school']} ORDER BY participant_name ASC");
                                foreach ($readTeacher->getResult() as $teacher):
                                    extract($teacher);
                                    echo "<option value=\"{$participant_id}\" ";

                                    if (isset($subscribers['subscribers_teacher']) && $subscribers['subscribers_teacher'] == $participant_id):
                                        echo 'selected';
                                    endif;

                                    echo "> {$participant_name}</option>";
                                endforeach;
                            endif;    
                                ?>     
	                    </select>

	                   
	              </div>
	            <div class="form-group col-lg-4 ">
	              	<label>Estudante:</label>
	                	<select  class="j_loadstudent form-control"  name="subscribers_student" required>
	                	<?php if (!isset($subscribers['subscribers_school'])): ?>
	                    	<option value="" selected> Selecione antes a escola </option>
	                    	<?php
	                    	else:
                                $readStudent = new Read;
                                $readStudent->ExeRead("es_school_participant", "WHERE participant_type=2 AND participant_school={$subscribers['subscribers_school']} ORDER BY participant_name ASC");
                                foreach ($readStudent->getResult() as $estudente):
                                    extract($estudente);
                                    echo "<option value=\"{$participant_id}\" ";

                                    if (isset($subscribers['subscribers_student']) && $subscribers['subscribers_student'] == $participant_id):
                                        echo 'selected';
                                    endif;

                                    echo "> {$participant_name}</option>";
                                endforeach;
                            endif;    
                                ?>       
	                    	
	                    </select>

	                   
	              </div>

	              <div class="form-group col-lg-3 ">
	              	<label>Categoria:</label>
	                	<select  class="j_loadcategory form-control"  name="subscribers_category" required>
	                    	<option value="" selected> Selecione o Categoria </option>
	                    	<?php
	                            $readCategory= new Read();
	                            $readCategory->ExeRead('cs_category',"ORDER BY category_name ASC");
	                            foreach ($readCategory->getResult() as $category):
	                                extract($category);
	                                 echo "<option value=\"{$category_id}\" ";
	                                if(isset($subscribers['subscribers_category'])&&$subscribers['subscribers_category']==$category_id):
	                                     echo 'selected';
	                                endif;    
	                                echo "> {$category_name}</option>";
	                            endforeach;                   
                       		 ?>
	                    </select>

	                   
	              </div>
	              
	              <div class="form-group col-lg-4">
	              	<label>Serie:</label>
	                	<select  class="j_loadseries form-control"  name="subscribers_series" required>
	                    	<?php if (!isset($subscribers['subscribers_category'])): ?>
	                    	<option value="" selected> Selecione antes a categoria </option>
	                    	<?php
	                    	else:
                               $readSeries = new Read;
							   $readSeries->FullRead("SELECT * FROM cs_category_series INNER JOIN cs_series ON category_series=series_id WHERE category_category={$subscribers['subscribers_category']} ORDER BY series_name");
                                foreach ($readSeries->getResult() as $series):
                                    extract($series);
                                    echo "<option value=\"{$series_id}\" ";

                                    if (isset($subscribers['subscribers_series']) && $subscribers['subscribers_series'] == $series_id):
                                        echo 'selected';
                                    endif;

                                    echo "> {$series_name}</option>";
                                endforeach;
                            endif;    
                                ?>     

	                    </select>
	                   
	              </div>
	                <div class="form-group col-lg-10">
                        <label>Redação:</label>        
                        <input type="file" class="form-control"  name="subscribers_redaction" required>
                    </div>


            </div><!--row-->
           <div class="text text-center">
            	<input type="submit" class="btn btn-success" name="sendSubscribers" value="Realizar Inscrição"> 	
            </div>	
        </div>
    </div>
</form>            