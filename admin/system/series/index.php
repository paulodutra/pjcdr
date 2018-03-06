<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminSeries.class.php');
$serie = filter_input(INPUT_GET, 'serie', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);

if ($action && $serie):
    $actionSerie = new AdminSeries();

    switch ($action):

        case 'active':
            $actionSerie->ExeStatus($serie, '1');
            MSGErro("<b>Status da serie Atualizada:</b> Para <b>Ativo</b>. Serie Publicado !", MSG_ACCEPT);

            break;

        case 'inactive':
            $actionSerie->ExeStatus($serie, '0');
            MSGErro("<b>Status da serie Atualizada:</b> Para <b>Inativo</b>. A Serie agora é um rascunho !", MSG_ALERT);

            break;

        case 'delete':
            $actionSerie->ExeDelete($serie);
            MSGErro($actionSerie->getError()[0], $actionSerie->getError()[1]);
            break;

        default:
            MSGErro("<b>Ação não existe:</b> Utilize os botões!", MSG_ERROR);
            break;

    endswitch;

endif;
?> 
<h4 class="text text-primary text-center">Series cadastradas</h4>
<div class="dt-empresa">
    <table id="school"  class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="text-center">Serie</th>
                <th class="text-center">Data de cadastro</th>
                <th class="text-center">-</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $readSerie = new Read();
            $readSerie->ExeRead('cs_series');
            if ($readSerie->getResult()):
                foreach ($readSerie->getResult() as $serie):
                    extract($serie);

                    $status = (!$series_status ? 'warning' : '');
                    ?>
                    <tr class="<?= $status ?>">
                        <td class="text-primary <?= $status ?>"><?= $series_name ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($series_date_registration)) ?></td>
                        <td class="text-center">
                            <span><a href="dashboard.php?exe=series/update&serie=<?= $series_id ?>" class="glyphicon glyphicon-pencil" title="Editar"></a></span> 
                            <?php if (!$series_status): ?>
                                <span><a class="glyphicon glyphicon-ok" href="dashboard.php?exe=series/index&serie=<?= $series_id ?>&action=active" title="Ativar"></a></span>
                            <?php else: ?>  
                                <span><a class="glyphicon glyphicon-remove" href="dashboard.php?exe=series/index&serie=<?= $series_id ?>&action=inactive" title="Desativar"></a></span>
                                <?php endif; ?>
                            <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=series/index&serie={$series_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir esta serie ?')\" title=\"Deletar\"></a>"; ?></span>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>        
        </tbody> 
    </table>
</div>
