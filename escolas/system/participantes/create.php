<?php
	 /**
		* Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
		    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
	*/
	require_once('_models/AdminParticipant.class.php');

	$participant= filter_input_array(INPUT_POST, FILTER_DEFAULT);
	$schoolID= filter_input(INPUT_GET,'school', FILTER_VALIDATE_INT);

	$disable=(!empty($schoolID) ? 'disabled' :'');

	if($participant && isset($participant['sendParticipant'])):

		unset($participant['sendParticipant']);

		$createParticipante= new AdminParticipant();
		$createParticipante->ExeCreate($participant);

		if($createParticipante->getResult()):
			MSGErro($createParticipante->getError()[0],$createParticipante->getError()[1]);
		else:
			MSGErro($createParticipante->getError()[0],$createParticipante->getError()[1]);
		endif;	


	endif;

	

?>
<form name="formParticipante" action="" method="post">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
            	<h4 class="text text-primary text-center">Cadastro de Participantes do Concurso</h4>

            	 <div class="form-group col-lg-10">
	              	<label>Escola:</label>
	                	<select  class="form-control" <?=$disable?> name="participant_school" required>
	                    	 <option value="<?=$userSchool['school_id']?>" selected><?=$userSchool['school_name']?> / <?= 'CNPJ:'.$userSchool['school_cnpj']?></option>

	                    </select>

	                   
	              </div>
            	<div class="form-group col-lg-6">
            		<label>Nome:</label>
            		<input type="text" class="form-control" name="participant_name" value="<?php if(isset($participant['participant_name'])): echo $participant['participant_name']; endif; ?>" required>
            	</div>

            	<div class="form-group col-lg-3">
	              	<label>Tipo de participante:</label>
	                	<select  class="form-control" id="participante" name="participant_type" required>
	                    	<option value="" selected>Selecione o participante: </option>
	                    	 <?php
		             			$readParticipant = new Read();
		             			$readParticipant->ExeRead("es_school_participant_type","ORDER BY participant_type_name ASC");
		             			if($readParticipant->getResult()):
		             				foreach ($readParticipant->getResult() as $participante): 
		             					extract($participante);//permite pegar o nome das colunas do banco como variaveis
		             					echo "<option value=\"{$participant_type_id}\" ";
		             					if(isset($participant['participant_type']) && $participant['participant_type']==$participant_type_id):
		             						 echo 'selected';
		             					endif;		
		             					echo "> {$participant_type_name}</option>";
		             				endforeach;
		             				
		             			endif;	
		             		  ?>

	                    </select>

	                   
	              </div>
	            
            	<div class="form-group col-lg-7">
            		<div class="form-group col-lg-4">
            			<label>Data de nascimento:</label>
            			<input type="text" class="form-control" name="participant_date_nascimento" id="nascimento" value="<?php if(isset($participant['participant_date_nascimento'])): echo $participant['participant_date_nascimento']; endif; ?>" required>
            		</div>
            		<div class="form-group col-lg-3">
            			<label>CPF:</label>
            			<input type="text" class="form-control" name="participant_cpf" id="cpf" value="<?php if(isset($participant['participant_cpf'])): echo $participant['participant_cpf']; endif; ?>" required>
            		</div>
            	</div>

        	</div><!--row-->
        	 <div class="text text-center">
            	<input type="submit" class="btn btn-success" name="sendParticipant" value="Salvar Participante"> 	
            </div>	
    </div>
</form>     

      	