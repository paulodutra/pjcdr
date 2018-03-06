<h4 class="text text-primary text-center">Escolas com planos de mobilizações cadastrados</h4>
<div class="dt-empresa">
    <table id="school" class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Concurso</th>
                <th>Escola</th>
                <th>CNPJ</th>
                <th>Cidade-UF</th>
                <th>Nº de arquivos enviados</th>
                <th class="text-center">-</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $readMobilization = new Read();
            $readMobilization->FullRead("SELECT * FROM `es_school_mobilization` INNER JOIN es_school ON mobilization_school=school_id INNER JOIN app_cidades AS c ON c.cidade_id=school_city INNER JOIN app_estados AS e ON e.estado_id=school_uf INNER JOIN cs_concurso ON concurso_id=mobilization_concurso  WHERE school_id={$userSchool['school_id']} AND concurso_status=1");
            if ($readMobilization->getResult()):
                foreach ($readMobilization->getResult() as $mobilization):
                    extract($mobilization);
                    $readFiles = new Read();
                    $readFiles->FullRead("SELECT COUNT(mobilization_file_directory) AS arquivos FROM es_school_mobilization_file WHERE mobilization_file_school={$mobilization_school} AND mobilization_file_concurso={$mobilization_concurso}");
                    $files = $readFiles->getResult()[0]['arquivos'];
                    ?>
                    <tr>
                        <td><?= $mobilization_id ?></td>
                        <td><?= $concurso_name ?></td>
                        <td><?= $school_name ?></td>
                        <td><?= $school_cnpj ?></td>
                        <td><?= $cidade_nome ?>-<?= $cidade_uf ?></td>
                        <td><?= $files = ($files == '' ? 0 : $files); ?></td>
                        <td>
                            <span><a href="dashboard.php?exe=mobilization/details&school=<?= $school_id ?>&concurso=<?= $concurso_id ?>" class="glyphicon glyphicon-search" title="Visualizar Mobilização"></a></span>
                            <span><a href="dashboard.php?exe=mobilization/update&mobilization=<?= $mobilization_id ?>" class="glyphicon glyphicon-pencil" title="Editar Mobilização"></a></span> 
                        </td>
                    </tr>
                    <?php
                endforeach;
            else:
               
                MSGErro("<p class=\"text-center\"><b>A escola não possui mobilizações cadastradas !</b></p>", MSG_ALERT);

            endif;
            ?>

        </tbody>

    </table> 
</div>