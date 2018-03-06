<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminSchool.class.php');
$school = filter_input(INPUT_GET, 'school', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);



if ($school && $action):

    $updateSchool = new AdminSchool();
    switch ($action):


        case 'active':
            $updateSchool->ExeStatus($school, '1');
            MSGErro("<b>Status atualizado:</b> A escola foi atualizada para o status <b>ATIVO(A)</b>!", MSG_ACCEPT);
            break;

        case 'inactive':
            $updateSchool->ExeStatus($school, '0');
            MSGErro("<b>Status atualizado:</b> A escola foi atualizada para o status <b>INATIVO(A)</b>!", MSG_ACCEPT);
            break;

        case 'delete':
            $updateSchool->ExeDelete($school);
            MSGErro($updateSchool->getError()[0], $updateSchool->getError()[1]);
            break;

        default:
            MSGErro("<b>Ação não existe:</b> Utilize os botões!", MSG_ERROR);
            break;
    endswitch;
endif;
?>

<h4 class="text text-primary text-center">Escolas cadastradas</h4>
<div class="dt-empresa">
    <table id="school" class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Escola:</th>
                <th>CNPJ:</th>
                <th>INEP</th>
                <th>Cidade-UF</th>
                <th class="text-center">-</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $readPhone = new Read();
            $readPhone->FullRead("SELECT * FROM es_school INNER JOIN app_estados ON school_uf=estado_id INNER JOIN  app_cidades ON school_city=cidade_id WHERE school_id={$userSchool['school_id']} ORDER BY school_id ASC");

            if ($readPhone->getResult()):
                foreach ($readPhone->getResult() as $schoolphone):
                    extract($schoolphone);
                    $status = (!$school_status ? 'warning' : '');
                    ?>
                    <tr class="<?= $status ?>">
                        <td><?= $school_id; ?></td>
                        <td><b><a href="dashboard.php?exe=school/details&school=<?= $school_id ?>" class="text-primary <?= $status ?>"><?= $school_name; ?></a></b></td>
                        <td><?= $school_cnpj; ?></td>
                        <td><?= $school_inep ?></td>
                        <td><?= $cidade_nome ?>-<?= $estado_uf ?></td>
                        <td class="text-center">
                            <span><a href="dashboard.php?exe=school/details&school=<?= $school_id ?>" class="glyphicon glyphicon-search" title="Visualizar Cadastro"></a></span>
                            <span><a href="dashboard.php?exe=phone/create&school=<?= $school_id ?>" class="glyphicon glyphicon-phone-alt" title="Cadastrar telefone(s)"></a></span>
                            <span><a href="dashboard.php?exe=school/update&school=<?= $school_id ?>" class="glyphicon glyphicon-pencil" title="Editar"></a></span> 
                            <?php if (!$school_status): ?>
                                <span><a href="dashboard.php?exe=school/index&school=<?= $school_id ?>&action=active" class="glyphicon glyphicon-ok" title="Ativar cadastro"></a><span>
                                    <?php else: ?>
                                        <span><a href="dashboard.php?exe=school/index&school=<?= $school_id ?>&action=inactive" class="glyphicon glyphicon-remove" title="Desativar cadastro"></a><span>
                                            <?php endif; ?>
                                            </td>
                                            </tr> 
                                            <?php
                                        endforeach;
                                    endif;
                                    ?>    
                                    </tbody>

                                    </table>
                                    </div>








