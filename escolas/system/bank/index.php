<h4 class="text text-primary text-center">Escolas com contas bancárias cadastradas</h4>
<div class="dt-empresa">
    <table id="school" class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Escola:</th>
                <th>CNPJ:</th>
                <th>Banco</th>
                <th>Agência</th>
                <th>Conta</th>
                <th>Cidade-UF</th>
                <th class="text-center">-</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $readBank = new Read();
            $readBank->FullRead("SELECT * FROM es_school INNER JOIN app_estados AS e ON school_uf=e.estado_id INNER JOIN app_cidades AS c ON school_city=c.cidade_id INNER JOIN es_school_data_bank ON data_bank_school=school_id INNER JOIN cs_concurso_bank ON data_bank_bank=bank_id WHERE school_id={$userSchool['school_id']}");
            if($readBank->getResult()):
            foreach ($readBank->getResult () as $bank):
            extract($bank);
            ?>
            <tr>
                <td><?= $data_bank_id ?></td>
                <td><?= $school_name ?></td>
                <td><?= $school_cnpj ?></td>
                <td><?= $bank_name ?></td>
                <td><?= $data_bank_agency ?></td>
                <td><?= $data_bank_account ?></td>
                <td><?= $cidade_nome?>-<?=$estado_uf ?></td>
                <td class="text-center">
                     <span><a href="dashboard.php?exe=bank/update&bank=<?=$data_bank_id?>" class="glyphicon glyphicon-pencil"></a></span>
                </td>
            </tr>
            <?php
            endforeach;
            endif;
            ?>    
        </tbody>

    </table>
</div>