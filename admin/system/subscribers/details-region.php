<?php
$region = filter_input(INPUT_GET, 'region', FILTER_DEFAULT);
?>
<h4 class="text text-primary text-center">Inscrições da Região <?= $region ?></h4>
<div class="dt-empresa">
    <table id="school" class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%" >
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Concurso</th>
                <th class="text-center">Escola</th>
                <th class="text-center">CNPJ</th>
                <th class="text-center">Categoria</th>
                <th class="text-center">Aluno</th>
                <th class="text-center">CPF</th>
                <th class="text-center">Series</th>
                <th class="text-center">Cidade/UF</>
            </tr>
        </thead>
        <tbody>
            <?php
            $readRegion = new Read();
            $readRegion->FullRead("SELECT * FROM cs_concurso INNER JOIN cs_concurso_subscribers ON concurso_id=subscribers_concurso INNER JOIN es_school ON school_id=subscribers_school INNER JOIN app_estados ON estado_id=school_uf INNER JOIN app_cidades ON cidade_id=school_city INNER JOIN es_school_participant ON participant_id=subscribers_student INNER JOIN cs_category ON category_id=subscribers_category INNER JOIN cs_series ON series_id=subscribers_series WHERE EXISTS (SELECT MAX(concurso_id) FROM cs_concurso) AND estado_regiao='{$region}'");

            if ($readRegion->getResult()):

                foreach ($readRegion->getResult() as $regiao):
                    extract($regiao);
                    ?>
                    <tr>
                        <td><?= $concurso_id ?></td>
                        <td><?= $concurso_name ?></td>
                        <td><?= $school_name ?></td>
                        <td><?= $school_cnpj ?></td>
                        <td><?= $category_name ?></td>
                        <td><?= $participant_name ?></td>
                        <td><?= $participant_cpf ?></td>
                        <td><?= $series_name ?></td>
                        <td><?= $cidade_nome ?>/<?= $cidade_uf ?></td>

                    </tr>
                    <?php
                endforeach;

            else:
                MSGErro("Não há inscrições para a região {$region}", MSG_ALERT);

            endif;
            ?>
        </tbody>

    </table>  

</div>

