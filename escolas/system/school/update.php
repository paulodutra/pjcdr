<?php
 /**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminSchool.class.php');

$School = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$SchoolID=filter_input(INPUT_GET,'school', FILTER_VALIDATE_INT);
if(isset($School)&& $School['sendSchool']):
    $School['school_status']=($School['sendSchool']=='Salvar como rascunho'?'0' : '1');
    //$School['school_complement']=(isset($School['school_complement']) ? $School['school_complement'] : unset($School['school_complement']))

    unset($School['sendSchool']);


    $create= new AdminSchool();
    $create->ExeUpdate($SchoolID,$School);

    if(!$create->getResult()):
         MSGErro($create->getError()[0], $create->getError()[1]);
    else:
         //header('Location: dashboard.php?exe=school/phone&update=true&school='.$create->getResult());  
        MSGErro($create->getError()[0], $create->getError()[1]);
         
    endif;    

else:

    $readSchool = new Read();
    $readSchool->ExeRead("es_school","WHERE school_id = :schoolid","schoolid={$SchoolID}");

    if(!$readSchool->getResult()):
        header('Location:dashboard.php?exe=school/index&empty=true');
    else:    
        $School=$readSchool->getResult()[0];
    endif;    

endif;


?>
<form name="formSchool"  action="" method="post" enctype="multipart/form-data">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">

                <div class="form-group col-lg-7">
                    <label>Nome da Escola: </label>
                    <input type="text" class="form-control" name="school_name" value="<?php if(isset($School['school_name'])): echo $School['school_name']; endif; ?>" required>
                </div>

                <div class="form-group col-lg-7">
                    <label>Email da escola:</label>
                    <input type="email" class="form-control" name="school_email" value="<?php if(isset($School['school_email'])): echo $School['school_email']; endif; ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required>
                </div>

                 <div class="form-group col-lg-8">
                   <div class="form-group col-lg-4">
                        <label>Nome do diretor:</label>
                        <input type="text" class="form-control" name="school_director" value="<?php if(isset($School['school_director'])): echo $School['school_director']; endif; ?>" required>
                    </div>
                     <div class="form-group col-lg-4">
                        <label>Email do diretor:</label>
                        <input type="email" class="form-control" name="school_director_email" value="<?php if(isset($School['school_director_email'])): echo $School['school_director_email']; endif; ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required>
                    </div>
                </div>

                <div class="form-group col-lg-8">
                     <div class="form-group col-lg-4">
                        <label>CNPJ:</label>
                        <input type="text"  id="cnpj" class="form-control" name="school_cnpj" value="<?php if(isset($School['school_cnpj'])): echo $School['school_cnpj']; endif; ?>" required>
                    </div>
                    <div class="form-group col-lg-4">
                        <label>INEP:</label>
                        <input type="text"  class="form-control" name="school_inep" value="<?php if(isset($School['school_inep'])): echo $School['school_inep']; endif; ?>" title="Informe somente números" pattern="[0-9]+$" required>*somente números
                    </div>
                   
                </div>
                <div class="form-group col-lg-8">
                   <div class="form-group col-lg-4">
                        <label>Estado UF:</label>
                            <select class="j_loadstate form-control" name="school_uf" required>
                                <option value="" selected> Selecione o estado </option>
                                <?php
                                $readState = new Read;
                                $readState->ExeRead("app_estados", "ORDER BY estado_nome ASC");
                                foreach ($readState->getResult() as $estado):
                                    extract($estado);
                                    echo "<option value=\"{$estado_id}\" ";

                                    if (isset($School['school_uf']) && $School['school_uf'] == $estado_id):
                                        echo 'selected';
                                    endif;

                                    echo "> {$estado_uf} / {$estado_nome} </option>";
                                endforeach;
                                ?>                        
                            </select>
                    </div>                 
                    <div class="form-group col-lg-4">
                        <label>Cidade:</label>
                        <select class="j_loadcity form-control" name="school_city" required>
                            <?php if (!isset($School['school_city'])): ?>
                                <option value="" selected disabled> Selecione antes um estado </option>
                                <?php
                            else:
                                $city = new Read();
                                $city->ExeRead('app_cidades', "WHERE estado_id=:uf ORDER BY cidade_nome ASC", "uf={$School['school_uf']}");

                                if ($city->getResult()):
                                    foreach ($city->getResult() as $cidade):
                                        /** Permite pegar os indices da coluna da tabela como variaveis */
                                        extract($cidade);
                                        echo "<option value=\"{$cidade_id}\" ";

                                        if (isset($School['school_city']) && $School['school_city'] == $cidade_id):
                                            echo 'selected';
                                        endif;

                                        echo ">{$cidade_nome}</option>";
                                    endforeach;
                                endif;
                            endif;
                            ?>
                        </select>
                    </div>
                    
                </div>
               
               <div class="form-group col-lg-8">
                    <div class="form-group col-lg-3">
                        <label>CEP:</label>
                        <input type="text" class="form-control" id="cep" name="school_cep" value="<?php if(isset($School['school_cep'])): echo $School['school_cep']; endif; ?>" required>
                    </div>

                    <div class="form-group col-lg-5">
                        <label>Endereço:</label>
                        <input type="text" class="form-control" name="school_address" value="<?php if(isset($School['school_address'])): echo $School['school_address']; endif; ?>" required>
                    </div>
               </div>  

                <div class="form-group col-lg-8">
                     <div class="form-group col-lg-4">
                            <label>Bairro:</label>
                            <input type="text" class="form-control" name="school_district" value="<?php if(isset($School['school_district'])): echo $School['school_district']; endif; ?>" required>
                     </div>
                     <div class="form-group col-lg-4">
                            <label>Complemento:</label>
                            <input type="text" class="form-control" name="school_complement" value="<?php if(isset($School['school_complement'])): echo $School['school_complement']; endif; ?>" required>
                     </div>
                </div>
            </div><!--row-->
            <div class="text-left">
                <input type="submit" class="btn btn-primary" value="Salvar como rascunho" name="sendSchool">
                <input type="submit" class="btn btn-success" value="Salvar & Publicar" name="sendSchool">
               
            </div>
        </div>
    </div>
</form>
