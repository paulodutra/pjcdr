<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminSubscribers.class.php');

$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
$subscriberID = filter_input(INPUT_GET, 'subscriber', FILTER_VALIDATE_INT);

if ($action && $subscriberID):


    switch ($action):

        case 'delete':
            $deleteSubscriber = new AdminSubscribers();
            $deleteSubscriber->ExeDelete($subscriberID);
            MSGErro($deleteSubscriber->getError()[0], $deleteSubscriber->getError()[1]);
            break;

        default:
            MSGErro("Ação não existe, utilize os botões!", MSG_ALERT);
            break;

    endswitch;



endif;
?>
<h4 class="text text-primary text-center">Inscritos no concurso</h4>
<div class="dt-empresa">
    <table id="school" class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Concurso</th>
                <th class="text-center">Escola</th>
                <th class="text-center">CNPJ</th>
                <th class="text-center">Categoria</th>
                <th class="text-center">Aluno</th>
                <th class="text-center">CPF</th>
                <th class="text-center">Serie</th>
                <!--th class="text-center">Data de Inscrição</th-->
                <th class="text-center">-</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $readSubscribers = new Read();
            $readSubscribers->FullRead("SELECT * FROM cs_concurso_subscribers INNER JOIN cs_concurso ON subscribers_concurso=concurso_id
                                    INNER JOIN es_school ON school_id=subscribers_school
                                    INNER JOIN cs_category ON category_id=subscribers_category
                                    INNER JOIN es_school_participant ON participant_id=subscribers_student
                                    INNER JOIN cs_series ON series_id=subscribers_series");

            if ($readSubscribers->getResult()):
                foreach ($readSubscribers->getResult() as $subscribers):
                    extract($subscribers);
                    ?>
                    <tr>
                        <td><?= $subscribers_id ?></td>
                        <td><?= $concurso_name ?></td>
                        <td><?= $school_name ?></td>
                        <td><?= $school_cnpj ?></td>
                        <td><?= $category_name ?></td>
                        <td><?= $participant_name ?></td>
                        <td><?= $participant_cpf ?></td>
                        <td><?= $series_name ?></td>
                        <!--td><?= date('d/m/Y H:i:s', strtotime($subscribers_date_registration)) ?></td-->                       
                        <td class="text-center">
                            <span><a class="glyphicon glyphicon-search" href="dashboard.php?exe=subscribers/details&school=<?= $school_id ?>&student=<?= $subscribers_student ?>" class="text-primary "></a></span>            
                            <span><a class="glyphicon glyphicon-pencil" href="dashboard.php?exe=subscribers/update&subscriber=<?= $subscribers_id ?>"  title="Editar"></a></span> 
                            <span><a class="glyphicon glyphicon-download-alt" href=" <?= HOME . '/uploads/concurso/' . $subscribers_redaction ?>" target="_blank" title="Visualizar Redação"></a></span>
                            <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=subscribers/index&subscriber={$subscribers_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir esta inscrição ?')\" title=\"Deletar\"></a>"; ?></span>
                        </td>

                    </tr>

                    <?php
                endforeach;
            endif;
            ?>

        </tbody>
    </table>  
</div>

