<?php
$parametro = filter_input(INPUT_GET, 'p', FILTER_DEFAULT);
$parametro = urldecode($parametro);

?>

<h4 class="text text-primary text-center">Resultado da Busca INEP,CNPJ ou Nome da escola: <?= $parametro ?></h4>
<div class="dt-empresa">
    <table class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Escola:</th>
                <th>CNPJ:</th>
                <th>INEP</th>
                <th class="text-center">-</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $readSchool = new Read();
            $readSchool->ExeRead('es_school', "WHERE school_inep=:inep OR school_cnpj=:cnpj OR (school_name LIKE '%' :name  '%')", "inep={$parametro}&cnpj={$parametro}&name={$parametro}");
            if ($readSchool->getResult()):
                foreach ($readSchool->getResult() as $escola):
                    extract($escola);
                    $status = (!$school_status ? 'warning' : '');
                    ?>
                    <tr class="<?= $status ?>">
                        <td><?= $school_id; ?></td>
                        <td><b><a href="dashboard.php?exe=school/details&school=<?= $school_id ?>" class="text-primary <?= $status ?>"><?= $school_name; ?></a></b></td>
                        <td><?= $school_cnpj; ?></td>
                        <td><?= $school_inep ?></td>
                        <td class="text-center">
                            <span><a href="dashboard.php?exe=school/details&school=<?= $school_id ?>" class="glyphicon glyphicon-search"></a></span>
                            <span><a href="dashboard.php?exe=phone/create-phone&school=<?= $school_id ?>" class="glyphicon glyphicon-phone-alt"></a></span>
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
                                        MSGErro("Nenhum Resultado Encontrado !", MSG_ALERT);
                                    endif;
                                    ?>

                                    </tbody>
                                    </table>