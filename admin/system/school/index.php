<?php
/**
 * Todas as classes administrativas serÃ£o carregadas manualmente de acordo com o padrÃ£o front controller 
  que foi definido no painel.php(nÃ£o sendo necessÃ¡rio navegar nas estruturas de pastas convencionais)
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


$getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$pagination = new Pager('dashboard.php?exe=school/index&page=');
$pagination->ExePager($getPage, 25);
?>

<h4 class="text text-primary text-center">Escolas cadastradas</h4>
<div class="dt-empresa">
    <table  class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
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
            $readPhone->FullRead("SELECT es.school_id, es.school_name, es.school_cnpj, es.school_inep, es.school_status, c.cidade_nome, e.estado_uf
                                    FROM es_school AS es 
                                    INNER JOIN app_cidades AS c ON es.school_city=c.cidade_id 
                                    INNER JOIN app_estados AS e ON e.estado_id=es.school_uf ORDER BY school_id ASC LIMIT :limit OFFSET :offset", "limit={$pagination->getLimit()}&offset={$pagination->getOffset()}");
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
                            <span><a href="dashboard.php?exe=school/details&school=<?= $school_id ?>" class="glyphicon glyphicon-search"></a></span>
                            <span><a href="dashboard.php?exe=phone/create&school=<?= $school_id ?>" class="glyphicon glyphicon-phone-alt"></a></span>
                            <span><a href="dashboard.php?exe=school/update&school=<?= $school_id ?>" class="glyphicon glyphicon-pencil"></a></span> 
                            <?php if (!$school_status): ?>
                                <span><a href="dashboard.php?exe=school/index&school=<?= $school_id ?>&action=active" class="glyphicon glyphicon-ok"></a><span>
                                    <?php else: ?>
                                        <span><a href="dashboard.php?exe=school/index&school=<?= $school_id ?>&action=inactive" class="glyphicon glyphicon-remove"></a><span>
                                            <?php endif; ?>
                                            <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=school/index&school={$school_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir esta escola ?')\" title=\"Deletar\"></a>"; ?></span>
                                            </td>
                                            </tr> 
                                            <?php
                                        endforeach;
                                    else:

                                        /**
                                         * Caso passe uma pagina que não exista, será retornado para a ultima pagina com resultados
                                         * Caso o metodo falhe exibe a mensagem de erro
                                         */
                                        $pagination->ReturnPage();
                                        MSGErro("Desculpe está página não possui concursos cadastrados !", MSG_ALERT);
                                    endif;
                                    $pagination->ExePaginatorFull("SELECT es.school_id, es.school_name, es.school_cnpj, es.school_inep, es.school_status, c.cidade_nome, e.estado_uf
                                                                    FROM es_school AS es 
                                                                    INNER JOIN app_cidades AS c ON es.school_city=c.cidade_id 
                                                                    INNER JOIN app_estados AS e ON e.estado_id=es.school_uf ORDER BY school_id ASC");
                                    echo $pagination->getPaginator();
                                    ?>    
                                    </tbody>

                                    </table>
                                    </div>









