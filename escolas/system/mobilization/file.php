<?php
     /**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminMobilization.class.php');
 $files=filter_input_array(INPUT_POST, FILTER_DEFAULT);
 if($files && isset($files['sendArquivo'])):
    unset($files['sendArquivo']);
     $files['mobilization_file_directory']=($_FILES['mobilization_file_directory']);
        

  

     $createFile= new AdminMobilization();
     $createFile->sendFiles($files);

    if($createFile->getResult()):
       

        MSGErro($createFile->getError()[0],$createFile->getError()[1]);
    else:
       
        MSGErro($createFile->getError()[0],$createFile->getError()[1]);
    endif;  

endif; 

    

?>
 <form name="formFile"  action="" method="post" enctype="multipart/form-data">
        <div class="tab-content">
        <h4 class="text text-primary text-center">Enviar Arquivos de Mobilização</h4>
            <div class="row">
            <div class="form-group col-lg-4">
                    <label>Concurso:</label>
                        <select  class="form-control"  name="mobilization_file_concurso" required>
                            <option value="" selected> Selecione o concurso </option>
                             <?php
                                $readConcurso = new Read();
                                $readConcurso->ExeRead("cs_concurso","WHERE concurso_status=1 ORDER BY concurso_name ASC");
                                if($readConcurso->getResult()):
                                    foreach ($readConcurso->getResult() as $concurso): 
                                        extract($concurso);//permite pegar o nome das colunas do banco como variaveis
                                        echo "<option value=\"{$concurso_id}\" ";
                                        if(isset($files['mobilization_file_concurso']) && $files['mobilization_file_concurso']==$concurso_id):
                                             echo 'selected';
                                        endif;      
                                        echo ">{$concurso_name}</option>";
                                    endforeach;
                                    
                                endif;  
                              ?>

                        </select>
              </div>
              <div class="form-group col-lg-6">
                    <label>Escola:</label>
                        <select  class="form-control"  name="mobilization_file_school" required>
                            <option value="<?=$userSchool['school_id']?>" selected><?=$userSchool['school_name']?> / <?= 'CNPJ:'.$userSchool['school_cnpj']?></option>
                             
                        </select>
              </div>
              <div class="form-group col-lg-9">
                    <div class="form-group col-lg-5">
                        <label>Arquivo</label>        
                        <input type="file" class="form-control"  name="mobilization_file_directory" required>
                    </div>
                    <div class="form-group col-lg-5">
                                    
                        <label>Tipo de arquivo:</label>
                        <select class="form-control" name="mobilization_file_type" required>
                            <option value="" selected> Selecione o tipo de arquivo </option>
                            <?php
                                $readType= new Read();
                                $readType->ExeRead('es_school_mobilization_type',"ORDER BY mobilization_type_name ASC");
                                foreach ($readType->getResult() as $file):
                                    extract($file);

                                    echo "<option value=\"{$mobilization_type_id}\" ";


                                    if(isset($files['mobilization_file_type'])&&$files['mobilization_file_type']==$mobilization_type_id):
                                        echo 'selected';
                                    endif;    
                                    echo "> {$mobilization_type_name}</option>";
                                endforeach;                        

                            ?>
                        </select>    
                    </div> 
                      
                </div> 
                          
            </div><!--row-->
        </div> 
        <div class="text-center">
            <input type="submit" class="btn btn-success"  value="Enviar Arquivo" name="sendArquivo"/>   
        </div>
    </form>
