<?php
 /**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminConcurso.class.php');

$concurso=filter_input_array(INPUT_POST,FILTER_DEFAULT);
$concursoid=filter_input(INPUT_GET,'concursoid', FILTER_VALIDATE_INT);

if(isset($concurso)&&$concurso['sendConcurso']):

    $concurso['concurso_status']=($concurso['sendConcurso']== 'Salvar como rascunho' ? '0' : '1');

    $concurso['concurso_logo'] = ($_FILES['concurso_logo']['tmp_name'] ? $_FILES['concurso_logo'] : null);

   

    unset($concurso['sendConcurso']);


    $create = new AdminConcurso();
    $create->ExeUpdate($concursoid,$concurso);


    MSGErro($create->getError()[0], $create->getError()[1]);
    
    //if($create->getResult()):
        //header('Location: dashboard.php?exe=concurso/update&create=true&concursoid='.$create->getResult());
    //endif; 
 else:      

    //echo '<pre>';
    //var_dump($create);
    //echo '</pre>';

    $readConcurso= new Read();
    $readConcurso->ExeRead("cs_concurso","WHERE concurso_id=:concursoid","concursoid={$concursoid}");
    if(!$readConcurso->getResult()):
        header('Location:dashboard.php?exe=concurso/index&empty=true');
    else:
        //atribui  a leitura para o array do concurso, o mesmo mantem a persistencia de dados e faz o databind
        $concurso=$readConcurso->getResult()[0];
        $concurso_status=$readConcurso->getResult()[0]['concurso_status'];

        //converte a data 
        $concurso['concurso_start']=date('d/m/Y H:i:s',strtotime($concurso['concurso_start']));
        $concurso['concurso_end']=date('d/m/Y H:i:s',strtotime($concurso['concurso_end']));
    endif;    

endif;    

?>

<form name="formConcurso" action="" method="post" enctype="multipart/form-data">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
                <div class="form-group col-lg-7">
                    <label>Enviar Logo: </label>
                    <input type="file"   class="form-control" name="concurso_logo" required>
                </div>
                <div class="form-group col-lg-8">
                    <label>Tipo de concurso:</label>
                    <select class="form-control" name="concurso_type" required>
                        <option value="" selected> Selecione o tipo de concurso </option>
                        <?php
                            $readType= new Read();
                            $readType->ExeRead('cs_concurso_type',"ORDER BY type_name ASC");
                            foreach ($readType->getResult() as $type):
                                extract($type);

                                 echo "<option value=\"{$type_id}\" ";


                                if(isset($concurso['concurso_type'])&&$concurso['concurso_type']==$type_id):
                                     echo 'selected';
                                endif;    
                                echo "> {$type_name}</option>";
                            endforeach;
                            

                        ?>
                    </select>    
                </div>
                <div class="form-group col-lg-8">
                     <div class="form-group col-lg-4">
                        <label>Data de Inicio:</label>
                     
                        <input type="text" class="form-control" id="start" value="<?php if(isset($concurso['concurso_start'])): 
                        echo $concurso['concurso_start']; else: echo date('d/m/Y H:i:s'); endif; ?>" 
                        placeholder="Inicio" name="concurso_start" required>
                     </div>
                     <div class="form-group col-lg-4">
                        <label>Data de Termino:</label>
                        <input type="text" class="form-control" id="end" value="<?php if(isset($concurso['concurso_end'])):
                            echo $concurso['concurso_end'];
                            else: echo date('d/m/Y H:i:s'); endif; ?>"placeholder="Termino" name="concurso_end" required>
                     </div>      
                </div>
                <div class="form-group col-lg-7">
                     <label>Descrição:</label>
                    <textarea class="form-control" name="concurso_description"rows="10" required><?php if (isset($concurso['concurso_description'])) echo htmlspecialchars($concurso['concurso_description']); ?></textarea>
                   
                </div>    
                
            </div><!--row-->
            <div class="text-left">
            <?php if(!$concurso['concurso_status']): ?>      
                <input type="submit"  value="Salvar como rascunho" name="sendConcurso" class="btn btn-primary"/> 
            <?php else: ?>    
                <input type="submit"  value="Salvar & Publicar" name="sendConcurso" class="btn btn-success"/> 
            <?php endif; ?>    
            </div>
        </div>
    </div>
</form>