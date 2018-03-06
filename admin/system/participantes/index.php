<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminParticipant.class.php');

$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
$participante = filter_input(INPUT_GET, 'participante', FILTER_VALIDATE_INT);

if ($action && $participante):

    switch ($action):

        case 'delete':
            $deleteParticipant = new AdminParticipant();
            $deleteParticipant->ExeDelete($participante);
            MSGErro($deleteParticipant->getError()[0], $deleteParticipant->getError()[1]);
            break;

        default:
            MSGErro("<b>Ação não existe:</b> Utilize os botões!", MSG_ERROR);
            break;
    endswitch;

endif;
?>


<h4 class="text text-primary text-center">Participantes inscritos</h4>
<div class="dt-empresa">
    <table id="school" class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Escola</th>
                <th class="text-center">Nome</th>
                <th class="text-center">Data de nascimento</th>
                <th class="text-center">CPF</th>
                <th class="text-center">Tipo de participante</th>
                <th class="text-center">Data do cadastro</th>
                <th class="text-center">-</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $readParticipant = new Read();
            $readParticipant->FullRead("SELECT * FROM `es_school_participant` AS p INNER JOIN es_school AS S ON participant_school=school_id INNER JOIN es_school_participant_type AS t ON participant_type_id=participant_type ORDER BY participant_id ASC");
            if ($readParticipant->getResult()):
                foreach ($readParticipant->getResult() as $participante):
                    extract($participante);
                    ?>
                    <tr>
                        <td><?= $participant_id ?></td>
                        <td><?= $school_name ?></td>
                        <td><?= $participant_name ?></td>
                        <td><?= date('d/m/Y', strtotime($participant_date_nascimento)) ?></td>
                        <td><?= $participant_cpf ?></td>
                        <td><?= $participant_type_name ?></td>
                        <td><?= date('d/m/Y', strtotime($participant_date_registration)) ?></td>
                        <td class="text-center">
                            <span><a href="dashboard.php?exe=participantes/update&participante=<?= $participant_id ?>" class="glyphicon glyphicon-pencil" title="Editar"></a></span> 
                            <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=participantes/index&participante={$participant_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir este participante ?')\" title=\"Deletar\"></a>"; ?></span>
                        </td>
                    </tr>
                    <?php
                endforeach;

            endif;
            ?> 
        </tbody> 
    </table>     
</div>