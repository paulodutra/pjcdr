<?php
	$email= filter_input_array(INPUT_POST,FILTER_DEFAULT);

	if(isset($email) && isset($email['sendEmail'])):
  		$email['anexo']=($_FILES['anexo']);
		var_dump($email);
	endif;	



?>
			<form name="formEmail" action="" method="post" enctype="multipart/form-data">
				<div class="tab-content">
		        	<div id="step1" class="p-m tab-pane active">
		           		 <div class="row">
			           		 	<h4 class="text text-primary text-center">Configure o email a ser enviado</h4>
			           			<div class="form-group col-lg-7">
				            		<label>Assunto:</label>
				            		<input type="text" class="form-control" name="assunto" required>
			            		</div>
			            		<div class="form-group col-lg-8">
			            			<label>Anexo:</label>
			            			<input type="file" class="form-control" name="anexo">
			            		</div>
			            		<div class="form-group col-lg-8">
                     				<label>Mensagem:</label>
                    				<textarea class="form-control" name="msg"rows="10" required></textarea>
                				</div>     
		           		 </div>
		           		 <div class="text-center">
                			<input type="submit"  value="Enviar Email" id="sendEmail" name="sendEmail" class="btn btn-success"/> 
            			</div>
		           	</div>
		        </div>   		 

			</form>
	
	

	                