<?php
     /**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
    require_once('_models/AdminConcurso.class.php');
    $files=filter_input_array(INPUT_POST,FILTER_DEFAULT);
    $concurso=filter_input(INPUT_GET,'concurso',FILTER_DEFAULT);
    $files['file_concurso']=(isset($concurso) ? $concurso: '');
    
    $disable=(!empty($concurso) ? 'disabled' :'');
    
    if(isset($files) && !empty($files) && isset($files['sendArquivo'])):
        unset($files['sendArquivo']);
        $files['file_directory']=($_FILES['file_directory']);

        $createFile= new AdminConcurso();
        $createFile->sendFiles($files,$concurso);

        if($createFile->getResult()):
            MSGErro($createFile->getError()[0],$createFile->getError()[1]);
        else:
            MSGErro($createFile->getError()[0],$createFile->getError()[1]); 
        endif;    

    endif;

?>

    <form name="formFile"  action="" method="post" enctype="multipart/form-data">
        <div class="tab-content">
        <h4 class="text text-primary text-center">Enviar Arquivos</h4>
            <div class="row">
            <div class="form-group col-lg-8">
                    <label>Concurso:</label>
                        <select  class="form-control" <?=$disable?> name="file_concurso" required>
                            <option value="" selected> Selecione o concurso </option>
                             <?php
                                $readConcurso = new Read();
                                $readConcurso->ExeRead("cs_concurso","ORDER BY concurso_name ASC");
                                if($readConcurso->getResult()):
                                    foreach ($readConcurso->getResult() as $concurso): 
                                        extract($concurso);//permite pegar o nome das colunas do banco como variaveis
                                        echo "<option value=\"{$concurso_id}\" ";
                                        if(isset($files['file_concurso']) && $files['file_concurso']==$concurso_id):
                                             echo 'selected';
                                        endif;      
                                        echo ">{$concurso_name}</option>";
                                    endforeach;
                                    
                                endif;  
                              ?>

                        </select>
              </div>
              <div class="form-group col-lg-9">
                    <div class="form-group col-lg-5">
                        <label>Arquivo</label>        
                        <input type="file" class="form-control"  name="file_directory" required>
                    </div>
                    <div class="form-group col-lg-4">
                                    
                        <label>Tipo de arquivo:</label>
                        <select class="form-control" name="file_type" required>
                            <option value="" selected> Selecione o tipo de arquivo </option>
                            <?php
                                $readType= new Read();
                                $readType->ExeRead('cs_concurso_file_type',"ORDER BY file_type_name ASC");
                                foreach ($readType->getResult() as $file):
                                    extract($file);

                                    echo "<option value=\"{$file_type_id}\" ";


                                    if(isset($files['file_type'])&&$files['file_type']==$file_type_id):
                                        echo 'selected';
                                    endif;    
                                    echo "> {$file_type_name}</option>";
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
