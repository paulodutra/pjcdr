 <article>

        <?php extract($_SESSION['userlogin']); ?>

        <h1>Olá <?= "{$user_name} {$user_lastname}"; ?>, atualize seu perfíl!</h1>


        <?php
        $user=filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $userid=$_SESSION['userlogin']['user_id'];

        if ($user && $user['sendUser']):
            if ($user['user_re_password'] == $user['user_password']):
                unset($user['sendUser']);
                extract($user);

                require_once ('_models/AdminUsers.class.php');
                $updateUser = new AdminUsers();
                $updateUser->ExeUpdate($userid, $user);

                if ($updateUser->getResult()):
                    MSGErro("<b>Sucesso ao atualizar:</b> {$_SESSION['userlogin']['user_name']} foi atualizado com sucesso! ", MSG_ACCEPT);
                else:
                    MSGErro($updateUser->getError()[0], $updateUser->getError()[1]);
                endif;
            else:
                MSGErro("<b>Erro ao atualizar:</b> A confirmação de senha não é igual a senha ! Favor informe ambas iguais", MSG_ERROR);
            endif;

        else:
            extract($_SESSION['userlogin']);
        endif;
        ?>
</article>        

<form name="formProfile"  action="" method="post" enctype="multipart/form-data">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
                <!--div class="form-group col-lg-7">
                    <label>Enviar Foto: </label>
                    <input type="file"   class="form-control" name="">
                </div-->
                <div class="form-group col-lg-7">
                    <label>Nome:</label>
                    <input type="text" class="form-control"  placeholder="Nome" value="<?=$user_name?>" name="user_name" required>
                </div>
                <div class="form-group col-lg-7">
                    <label>Sobrenome:</label>
                    <input type="text" class="form-control"  placeholder="Sobrenome" value="<?=$user_lastname?>" name="user_lastname" required>
                </div>
                <div class="form-group col-lg-7">
                    <label>Email:</label>
                    <input type="email" class="form-control"  placeholder="email@email.com" value="<?=$user_email ?>" name="user_email"  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required>
                </div>

                <div class="form-group col-lg-8">
                    <div class="form-group col-lg-4">
                        <label>Senha</label>
                        <input type="password" class="form-control"  placeholder="********" value="<?php if(isset($user['user_password'])) echo $user['user_password']; ?>" name="user_password" required>
                    </div>

                    <div class="form-group col-lg-4">
                        <label>Confirme a senha:</label>
                        <input type="password" class="form-control"  placeholder="********" value="<?php if(isset($user['user_re_password'])) echo $user['user_re_password']; ?>" name="user_re_password" required>
                    </div>
                </div>
            </div><!--row-->
            <div class="text-center">

                <input type="submit" class="btn btn-success" value="Salvar Usuário" name="sendUser">
            </div>
        </div>
    </div>
</form>
