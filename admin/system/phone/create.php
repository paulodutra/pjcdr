<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminPhone.class.php');

$phone = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$schoolID = filter_input(INPUT_GET, 'school', FILTER_VALIDATE_INT);

$phone['phone_school'] = ($phone['phone_school'] ? $phone['phone_school'] : $schoolID);


$disabled = (!empty($schoolID) ? 'disabled=/"disabled/" ' : '');


if (isset($phone) && !empty($phone) && isset($phone['sendPhone'])):

    unset($phone['sendPhone']);

    $create = new AdminPhone();
    $create->ExeCreate($phone);

    if (!$create->getResult()):
        MSGErro($create->getError()[0], $create->getError()[1]);
    else:
        //header("Location: dashboard.php?exe=phone/index");  
        MSGErro($create->getError()[0], $create->getError()[1]);
    endif;

endif;
?>



<form name="formPhone" action="" method="post">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
                <h4 class="text text-primary text-center">Cadastro de telefones</h4>
                <div class="form-group col-lg-4">
                    <label>Carregar Escolas:</label>
                    <select id="phone_school" class="j_loadschoolpage form-control" name="phone_list" required>
                        <option value="" selected> Selecione antes Carregar Escolas  </option>
                        <option value="0">Carregar Escolas Inativas</option>
                        <option value="1">Carregar Escolas Ativas</option>
                    </select>
                </div>

                <div class="form-group col-lg-8">
                    <label>Escola:</label>
                    <select id="phone_school" class="j_loadschoollist form-control" <?= $disabled ?> name="phone_school" required>
                        <option value="" selected> Selecione antes Carregar Escolas </option>

                    </select>
                </div>
                <div class="form-group col-lg-8">
                    <div class="form-group col-lg-4">
                        <label>Telefone:</label>
                        <input type="text" id="phone" pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" class="form-control" name="phone_telephone"
                               value="<?php
                               if (isset($phone['phone_telephone'])): echo $phone['phone_telephone'];
                               endif;
                               ?>" title="Informe DDD e depois o Telefone" required>
                    </div> 

                    <div class="form-group col-lg-4">
                        <label>Tipo de telefone:</label>
                        <select class="form-control"  name="phone_type" required>
                            <option value="" selected> Selecione a escola </option>
                            <?php
                            $readTypePhone = new Read();
                            $readTypePhone->ExeRead("es_phone_type", "ORDER BY type_name ASC");
                            if ($readTypePhone->getResult()):
                                foreach ($readTypePhone->getResult() as $type):
                                    extract($type); //permite pegar o nome das colunas do banco como variaveis
                                    echo "<option value=\"{$type_id}\" ";
                                    if (isset($phone['phone_type']) && $phone['phone_type'] == $type_id):
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