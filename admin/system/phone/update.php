 <?php
 /**
	    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
	    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
	 */
	require_once('_models/AdminPhone.class.php');

	$phone= filter_input_array(INPUT_POST, FILTER_DEFAULT);
	$schoolID= filter_input(INPUT_GET,'school', FILTER_DEFAULT);
	$phoneid= filter_input(INPUT_GET, 'phone',FILTER_DEFAULT);

	$phone['phone_school']=$schoolID;
	$disable=(!empty($schoolID) ? 'disabled' :'');

	if(isset($phone)&&!empty($phone)&&isset($phone['sendPhone'])):

		unset($phone['sendPhone']);

		$create = new AdminPhone();
		$create->ExeUpdate($phoneid,$phone);

		if(!$create->getResult()):
			MSGErro($create->getError()[0], $create->getError()[1]);
		else:
			//header("Location: dashboard.php?exe=phone/create&school=$schoolID");  
			MSGErro($create->getError()[0], $create->getError()[1]);
		endif;	
	else:		
		$readPhone= new Read();
		$readPhone->ExeRead('es_school_phone', "WHERE phone_school=:school AND phone_id=:id","school={$schoolID}&id={$phoneid}");
		if($readPhone->getResult()):
			$phone=$readPhone->getResult()[0];
		endif;	
	endif;		

?>



<form name="formPhone"  action="" method="post">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
            	<h4 class="text text-primary text-center">Cadastro de telefones</h4>

	              <div class="form-group col-lg-12">
	              	<label>Escola:</label>
	                	<select id="phone_school" class="form-control" <?=$disable?> name="phone_school" required>
	                    	<option value="" selected> Selecione a escola </option>
	                    	 <?php
		             			$readSchool = new Read();
		             			$readSchool->ExeRead("es_school","ORDER BY school_name ASC");
		             			if($readSchool->getResult()):
		             				foreach ($readSchool->getResult() as $school): 
		             					extract($school);//permite pegar o nome das colunas do banco como variaveis
		             					echo "<option value=\"{$school_id}\" ";
		             					if(isset($phone['phone_school']) && $phone['phone_school']==$school_id):
		             						 echo 'selected';
		             					endif;		
		             					echo "> {$school_name} / CNPJ: {$school_cnpj}</option>";
		             				endforeach;
		             				
		             			endif;	
		             		  ?>

	                    </select>

	                   
	              </div>
	              <div class="form-group col-lg-8">
	              	 <div class="form-group col-lg-4">
		              	<label>Telefone:</label>
		              	<input type="text" id="phone" pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" class="form-control" name="phone_telephone"
		              	value="<?php if(isset($phone['phone_telephone'])): echo $phone['phone_telephone']; endif; ?>" title="Informe DDD e depois o Telefone" required>
		             </div> 

		             <div class="form-group col-lg-4">
	              		<label>Tipo de telefone:</label>
	                	<select class="form-control"  name="phone_type" required>
	                    	<option value="" selected> Selecione a escola </option>
	                    	  <?php
		             			$readTypePhone = new Read();
		             			$readTypePhone->ExeRead("es_phone_type","ORDER BY type_name ASC");
		             			if($readTypePhone->getResult()):
		             				foreach ($readTypePhone->getResult() as $type): 
		             					extract($type);//permite pegar o nome das colunas do banco como variaveis
		             					echo "<option value=\"{$type_id}\" ";
		             					if(isset($phone['phone_type']) && $phone['phone_type']==$type_id):
		             						 echo 'selected';
		             					endif;	
		             					echo ">{$type_name}</option>";
		             				endforeach;
		             			endif;	
		             		  ?>
	                    </select>
	              	</div>	

	              </div>
	              
            </div>
            <div class="text text-center">
            	<input type="submit" class="btn btn-success" name="sendPhone" value="Salvar Telefone"> 	
            </div>	