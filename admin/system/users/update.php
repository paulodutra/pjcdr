<?php
/**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminUsers.class.php');

$user=filter_input_array(INPUT_POST, FILTER_DEFAULT);
$userID=filter_input(INPUT_GET,'user', FILTER_VALIDATE_INT);

if($user && isset($user['sendUser'])):
    $user['user_status']=($user['sendUser']== 'Salvar como rascunho' ? '0' : '1');
    unset($user['sendUser']);

    $users= new AdminUsers();
    $users->ExeUpdate($userID,$user);

    if($users->getResult()):
        MSGErro($users->getError()[0],$users->getError()[1]);
    else:
        MSGErro($users->getError()[0],$users->getError()[1]); 
    endif;    
else:
    $readUsers= new Read();
    $readUsers->ExeRead('sys_user',"WHERE user_id=:id","id={$userID}");
    if($readUsers->getResult()):
        $user=$readUsers->getResult()[0];
    endif;    

endif;    




?>


<form name="formUsers"  action="" method="post" enctype="multipart/form-data">
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
                    <input type="email" class="form-control"  placeholder="email@email.com" value="<?php if(isset($user['user_email'])) echo $user['user_email']; ?>" name="user_email" required>
                </div>

                <div class="form-group col-lg-9">
                    <div class="form-group col-lg-3">
                        <label>Senha</label>
                        <input type="password" class="form-control"  placeholder="********"  value="" name="user_password" required>
                    </div>

                    <div class="form-group col-lg-3">
                        <label>Confirme a senha:</label>
                        <input type="password" class="form-control"  placeholder="********" value="" name="user_re_password" required>
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