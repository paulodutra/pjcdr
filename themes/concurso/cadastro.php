<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once 'admin/_models/AdminSchool.class.php';
$School = filter_input_array(INPUT_POST, FILTER_DEFAULT);
if (isset($School) && $School['sendSchool']):
    $School['school_status'] = ($School['sendSchool'] == 'Salvar como rascunho' ? '0' : '1');
    //$School['school_complement']=(isset($School['school_complement']) ? $School['school_complement'] : unset($School['school_complement']))

    unset($School['sendSchool']);

    $create = new AdminSchool();
    $create->ExeCreate($School);

    if (!$create->getResult()):
        MSGErro($create->getError()[0], $create->getError()[1]);
    else:
        MSGErro($create->getError()[0], $create->getError()[1]);

    endif;



endif;
?>
<div class="row">
    <div class="jumbotron">
        <h1 class="text-uppercase text-center">Avisos</h1>
        <p class="text-primary">-Todos os campos são obrigatórios</p>
        <p>-Após realizar a inscrição entre com o CNPJ e Codigo do INEP na área da escola. <a  class="glyphicon glyphicon-home" target="_blank"  title="Área da escola" href="<?=HOME?>/escolas"></a></p>
    </div>
    <h1 class="text-center text-uppercase">Cadastro de escolas</h1>
    <form name="formCadastro" action="" method="post" >
        <div class="col-lg-9">
            <div class="control-group form-group">
                <div class="controls">
                    <label>Nome da Escola:<i class="text-red">*</i></label>
                    <input type="text" class="form-control" name="school_name" id="name" required>
                    <p class="help-block"></p>
                </div>
            </div>
        </div>    
        <div class="col-lg-9">
            <div class="control-group form-group">
                <div class="controls">
                    <label>Email da Escola: <i class="text-red">*</i></label>
                    <input type="email" class="form-control" name="school_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="Informe o email com letras minusculas" required>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="control-group form-group">
                <div class="controls">
                    <label>Nome do diretor: <i class="text-red">*</i></label>
                    <input type="text" class="form-control" name="school_director" required>
                </div>
            </div>
        </div>  

        <div class="col-lg-5">
            <div class="control-group form-group">
                <div class="controls">
                    <label>Email do diretor: <i class="text-red">*</i></label>
                    <input type="email" class="form-control"  name="school_director_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="Informe o email com letras minusculas"  required>
                </div>
            </div>
        </div>  
        <div class="col-lg-5">
            <div class="control-group form-group">
                <div class="controls">
                    <label>CNPJ: <i class="text-red">*</i></label>
                    <input type="text" class="form-control" name="school_cnpj" id="cnpj" required>
                </div>
            </div>
        </div>  

        <div class="col-lg-5">
            <div class="control-group form-group">
                <div class="controls">
                    <label>INEP: <i class="text-red">*</i></label>
                    <input type="text" class="form-control" name="school_inep" pattern="[0-9]+$" title="Informe somente números" required>*somente números
                </div>
            </div>
        </div>  
        <div class="col-lg-5">
            <div class="control-group">
                <div class="controls">
                    <label>Estado/UF: <i class="text-red">*</i></label>
                    <select name="school_uf" class="j_loadstate form-control" required>
                        <option value="" selected>Selecione o estado</option>
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
            </div>
        </div>
        <div class="col-lg-5">
            <div class="control-group">
                <div class="controls">
                    <label>Cidade: <i class="text-red">*</i></label>
                    <select name="school_city" class=" j_loadcity form-control" required>
                        <option value="" selected>Selecione a cidade</option>
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
        </div>
        <div class="col-lg-5">
            <div class="control-group form-group">
                <div class="controls">
                    <label>CEP: <i class="text-red">*</i></label>
                    <input type="text" class="form-control" name="school_cep" id="cep" required>
                </div>
            </div>
        </div> 
        <div class="col-lg-5">
            <div class="control-group form-group">
                <div class="controls">
                    <label>Endereço: <i class="text-red">*</i></label>
                    <input type="text" class="form-control"  name="school_address"  required>
                </div>
            </div>
        </div> 
        <div class="col-lg-5">
            <div class="control-group form-group">
                <div class="controls">
                    <label>Bairro: <i class="text-red">*</i></label>
                    <input type="text" class="form-control" name="school_district"  required>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="control-group form-group">
                <div class="controls">
                    <label>Complemento: <i class="text-red">*</i></label>
                    <input type="text" class="form-control" name="school_complement"  required="required">
                </div>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="control-group form-group">
                <div class="controls">
                    <div class="text-center">
                        <input type="submit" class="btn btn-primary" value="Realizar Inscrição" name="sendSchool">
                    </div>
                </div>
            </div>
        </div>        
        <div id="success"></div>
        <!-- For success/fail messages -->

    </form>
    <div id="j_ajaxident" class="<?= HOME . "/_cdn/ajax/" ?>"></div>
</div><!-- /.row -->




