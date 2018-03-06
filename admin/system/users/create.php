<?php
/**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminUsers.class.php');

$user=filter_input_array(INPUT_POST, FILTER_DEFAULT);

if($user && isset($user['sendUser'])):
    $user['user_status']=($user['sendUser']== 'Salvar como rascunho' ? '0' : '1');
    unset($user['sendUser']);

    $users= new AdminUsers();
    $users->ExeCreate($user);

    if($users->getResult()):
        MSGErro($users->getError()[0],$users->getError()[1]);
    else:
        MSGErro($users->getError()[0],$users->getError()[1]); 
    endif;    

endif;    




?>


<form name="formUsers" action="" method="post" enctype="multipart/form-data">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
                <!--div class="form-group col-lg-7">
                    <label>Enviar Foto: </label>
                    <input type="file"   class="form-control" name="">
                </div-->
                <div class="form-group col-lg-7">
                    <label>Nome:</label>
                    <input type="text" class="form-control"  placeholder="Nome" value="<?php if(isset($user['user_name'])) echo $user['user_name']; ?>" name="user_name" required>
                </div>
                <div class="form-group col-lg-7">
                    <label>Sobrenome:</label>
                    <input type="text" class="form-control"  placeholder="Sobrenome" value="<?php if(isset($user['user_lastname'])) echo $user['user_lastname']; ?>" name="user_lastname" required>
                </div>
                <div class="form-group col-lg-7">
                    <label>Email:</label>
                    <input type="email" class="form-control"  placeholder="email@email.com" value="<?php if(isset($user['user_email'])) echo $user['user_email']; ?>" name="user_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required>
                </div>

                <div class="form-group col-lg-9">
                    <div class="form-group col-lg-3">
                        <label>Senha</label>
                        <input type="password" class="form-control"  placeholder="********"  value="<?php if(isset($user['user_password'])) echo $user['user_password']; ?>" name="user_password" required>
                    </div>

                    <div class="form-group col-lg-3">
                        <label>Confirme a senha:</label>
                        <input type="password" class="form-control"  placeholder="********" value="<?php if(isset($user['user_re_password'])) echo $user['user_re_password']; ?>" name="user_re_password" required>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Nível de acesso:</label>
                        <select class="form-control" name="user_level" required>
                            <option value="" selected> Selecione o nível </option>
                            <?php
                                $readLevel= new Read();
                                $readLevel->ExeRead('sys_user_level',"ORDER BY level_type ASC");
                                foreach ($readLevel->getResult() as $level):
                                    extract($level);
                                     echo "<option value=\"{$level_id}\" ";
                                    if(isset($user['user_level'])&&$user['user_level']==$level_id):
                                         echo 'selected';
                                    endif;    
                                    echo "> {$level_type}</option>";
                                endforeach;                   
                             ?>
                        </select>
                    </div>
                </div>
            </div><!--row-->
            <div class="text-center">
                <input type="submit" class="btn btn-primary" value="Salvar como rascunho" name="sendUser">
                <input type="submit" class="btn btn-success" value="Salvar Usuário" name="sendUser">
            </div>
        </div>
    </div>
</form>