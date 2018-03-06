<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminBank.class.php');

$bank = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (isset($bank) && $bank['sendBank']):
    unset($bank['sendBank']);
    $createBank = new AdminBank();
    $createBank->ExeCreate($bank);

    if ($createBank->getResult()):
        MSGErro($createBank->getError()[0], $createBank->getError()[1]);
    else:
        MSGErro($createBank->getError()[0], $createBank->getError()[1]);
    endif;


endif;
?>
<form id="" name="formBank" action="" enctype="multipart/form-data" method="post">
    <div class="tab-content">
        <div id="step1" class="p-m tab-pane active">
            <div class="row">
                <h4 class="text text-primary text-center">Cadastro de Conta Bancária</h4>
                
                <div class="form-group col-lg-4">
                    <label>Carregar Escolas:</label>
                    <select class="j_loadschoolpage form-control" required>
                        <option value="" selected> Selecione antes Carregar Escolas  </option>
                        <option value="0">Carregar Escolas Inativas</option>
                        <option value="1">Carregar Escolas Ativas</option>
                    </select>
                </div>

                <div class="form-group col-lg-4">
                    <label>Escola:</label>
                    <select  class="j_loadschoollist form-control" name="data_bank_school" required>
                        <option value="" selected> Selecione antes Carregar Escolas </option>
                    </select>
                </div>
                
                <div class="form-group col-lg-4">
                    <label>Informe o banco:</label>
                    <select class="form-control" id="data_bank_bank" name="data_bank_bank" required>
                        <option value="" selected> Selecione o Banco</option>
                        <?php
                        $readType = new Read();
                        $readType->ExeRead('cs_concurso_bank', "ORDER BY bank_name ASC");
                        foreach ($readType->getResult() as $type):
                            extract($type);

                            echo "<option value=\"{$bank_id}\" ";

                            if (isset($bank['data_bank_bank']) && $bank['data_bank_bank'] == $bank_id):
                                echo 'selected';
                            endif;
                            echo "> {$bank_name}</option>";
                        endforeach;
                        ?>
                    </select>    
                </div><!--col-lg-4-->

                <div id="bb">
                    <div class="form-group col-lg-6">
                        <p class="text-center text-primary">001-Banco do Brasil</p>
                        <div class="form-group col-lg-3">
                            <label>Agência:</label>
                            <input type="text" id="agenciabb" class="form-control" placeholder="xxxx-x"  name="data_bank_agency" pattern="([0-9]{4})-[a-zA-Z0-9]{1}"  maxlength="6" value="<?php
                            if (isset($bank['data_bank_agency'])): echo $bank['data_bank_agency'];
                            endif;
                            ?>" title="Informe os 4 números da agência e o DV" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Conta:</label>
                            <input type="text" id="contabb" class="form-control" placeholder="xxxxxxxx-x"  name="data_bank_account"  pattern="([0-9]{8})-[a-zA-Z0-9]+{1}" maxlength="10" value="<?php
                            if (isset($bank['data_bank_account'])): echo $bank['data_bank_account'];
                            endif;
                            ?>" title="informe os 8 digitos da conta e o DV" required>
                        </div>

                    </div>
                </div>
                <div class="caixa">
                    <div id="cef">
                        <div class="form-group col-lg-6">
                            <p class="text-center text-primary">104-Caixa Econômica Federal</p>
                            <div class="form-group col-lg-2">
                                <label>Agência:</label>
                                <input type="text" id="agenciacef" class="form-control" placeholder="xxxx"  name="data_bank_agency" pattern=".{4}"  maxlength="4" value="<?php
                                if (isset($bank['data_bank_agency'])): echo $bank['data_bank_agency'];
                                endif;
                                ?>" title="Informe os 4 números da agência" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Operação/Conta:</label>
                                <input type="text" id="contacef" class="form-control" placeholder="xxxxxxxxxxx-x"  name="data_bank_account"  pattern="([0-9]{11})-[0-9]{1}" maxlength="13" value="<?php
                                if (isset($bank['data_bank_account'])): echo $bank['data_bank_account'];
                                endif;
                                ?>" title="informe os 11 digitos da conta e o DV" required>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="text-center">
                <input type="submit" class="btn btn-success" name="sendBank" value="Salvar Conta Bancária"> 
            </div>
        </div>
    </div>
</form> 








