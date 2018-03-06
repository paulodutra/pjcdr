<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminPhone.class.php');

$school = filter_input(INPUT_GET, 'school', FILTER_DEFAULT);
$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
$phoneid = filter_input(INPUT_GET, 'phone', FILTER_DEFAULT);

if ($phoneid & $action):

    switch ($action):


        case 'delete':
            //fazer o delete dos telefone(s) da escola
            $phoneDelete = new AdminPhone();
            $phoneDelete->ExeDelete($phoneid);

            var_dump($phoneDelete);

            if ($phoneDelete->getResult()):

                MSGErro($phoneDelete->getError()[0], $phoneDelete->getError()[1]);
            else:
                MSGErro($phoneDelete->getError()[0], $phoneDelete->getError()[1]);
            endif;

            break;

        default:
            MSGErro("<b>Ação não existe:</b> Utilize os botões!", MSG_ERROR);
            break;

    endswitch;

endif;
?>
<h4 class="text text-primary text-center">Escolas com telefones cadastrados</h4>
<div class="dt-empresa">
    <table id="school" class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Escola:</th>
                <th>CNPJ:</th>
                <th>Telefone</th>
                <th>Tipo de telefone</th>
                <th class="text-center">-</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $readPhone = new Read();
            $readPhone->FullRead("SELECT * FROM es_school INNER JOIN es_school_phone ON school_id=phone_school INNER JOIN es_phone_type ON phone_type = type_id ORDER BY school_id ASC ");

            if ($readPhone->getResult()):
                foreach ($readPhone->getResult() as $schoolphone):
                    extract($schoolphone);
                    ?>
                    <tr>
                        <td><?= $phone_school; ?></td>
                        <td><?= $school_name; ?></td>
                        <td><?= $school_cnpj; ?></td>
                        <td><?= $phone_telephone; ?></td>
                        <td><?= $type_name; ?></td>
                        <td class="text-center">
                            <span><a href="dashboard.php?exe=phone/details&school=<?= $school_id ?>" class="glyphicon glyphicon-search"></a></span>
                            <span><a href="dashboard.php?exe=phone/create&school=<?= $school_id ?>" class="glyphicon glyphicon-phone-alt"></a></span>
                            <span><a href="dashboard.php?exe=phone/update&school=<?= $phone_school ?>&phone=<?= $phone_id ?>" class="glyphicon glyphicon-pencil"></a><span>
                            <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=phone/index&phone={$phone_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir este telefone ?')\" title=\"Deletar\"></a>"; ?></span>
                        </td>
                    </tr>	
                    <?php
                       endforeach;
                            else:
                                MSGErro("<p class=\"text-center\"><b>As escolas não possui telefones cadastrados !</b></p>", MSG_ALERT);
                            endif;
                    ?>
        </tbody>

    </table>  
</div>