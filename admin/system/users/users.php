<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminUsers.class.php');

$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
$userID = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT);



if ($action && $userID):

    $actionUser = new AdminUsers();

    switch ($action):

        case 'delete':
            $actionUser->ExeDelete($userID);
            MSGErro($actionUser->getError()[0], $actionUser->getError()[1]);
            break;

        default:
            MSGErro("<b>Ação não existe:</b> Utilize os botões!", MSG_ERROR);
            break;
    endswitch;

endif;
?>

<div class="text-right">
    <span><a href="dashboard.php?exe=users/create" class="btn btn-primary">Cadastrar Usuário</a></span>
</div><br>
<div class="dt-empresa">
    <table id="school" class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th> Nº </th>
                <th> Nome </th>
                <th> E-mail:</th>
                <th> Registro: </th>
                <th> Atualização </th>
                <th> Nível </th>
                <th>-</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $readUser = new Read();
            $readUser->FullRead("SELECT * FROM sys_user INNER JOIN sys_user_level ON user_level=level_id ORDER BY user_level  DESC, user_name ASC ");
            if ($readUser->getResult()):
                foreach ($readUser->getResult() as $user):
                    /** Permite utilizar o nome das colunas do banco como variaveis */
                    extract($user);
                    /** Se existir a utima atualização converte a data para o formato Brasileiro */
                    $user_lastupdate = ($user_lastupdate == '0000-00-00 00:00:00' ? '-' : date('d/m/Y H:i:s', strtotime($user_lastupdate)) . ' hs');
                    //$nivel = ['', 'User', 'Editor', 'Admin'];
                    ?>       
                    <tr>
                        <td><?= $user_id ?></td>
                        <td><?= $user_name ?></td>
                        <td><?= $user_email ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($user_registration)) ?></td>
                        <td><?= $user_lastupdate ?></td>
                        <td><?= $level_type ?></td>
                        <td>
                            <span><a class="glyphicon glyphicon-pencil" href="dashboard.php?exe=users/update&user=<?= $user_id ?>" ></a></span>
                            <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=users/users&user={$user_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir este usuário ?')\" title=\"Deletar\"></a>"; ?></span>

                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>

        </tbody>
    </table>
</div>