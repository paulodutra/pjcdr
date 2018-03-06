<?php
/**
 * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
  que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
require_once('_models/AdminCategory.class.php');

$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
$categoria = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
$empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);


if ($action && $categoria):

    switch ($action):
        case 'delete':
            $deleteCategory = new AdminCategory();
            $deleteCategory->ExeDelete($categoria);
            MSGErro($deleteCategory->getError()[0], $deleteCategory->getError()[1]);
            break;

        default:
            MSGErro("<b>Ação não existe:</b> Utilize os botões!", MSG_ERROR);
            break;

    endswitch;

endif;

if ($empty):
    MSGErro("<b>Categoria</b> Não encontrada ou não existe!", MSG_ERROR);
endif;
?>

<h4 class="text text-primary text-center">Categorias cadastradas</h4>
<div class="dt-empresa">
    <table id="school"  class="table table-striped table-hover dt-responsive container" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome da categoria:</th>
                <th>Descrição:</th>
                <th>Tipo de Ensino:</th>
                <th>Tipo de Modalidade:</th>
                <th>Data de cadastro</th>
                <th class="text-center">-</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $readCategory = new Read();
            $readCategory->FullRead("SELECT * FROM cs_category AS cat INNER JOIN cs_category_education AS educ ON cat.category_education=educ.education_id 
                                         INNER JOIN cs_category_modality AS mo ON mo.modality_id=cat.category_modality ORDER BY category_id ASC");

            if ($readCategory->getResult()):
                foreach ($readCategory->getResult() as $category):
                    extract($category);
                    ?>
                    <tr>
                        <td><?= $category_id ?></td>
                        <td><?= $category_name ?></td>
                        <td><?= $category_description ?></td>
                        <td><?= $education_name ?></td>
                        <td><?= $modality_name ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($category_date_registration)) ?></td>
                        <td class="text-center">
                            <span><a href="dashboard.php?exe=categorias/details&category=<?= $category_id ?>" class="glyphicon glyphicon-search" title="Ver detalhes"></a></span>
                            <span><a href="dashboard.php?exe=categorias/series&category=<?= $category_id ?>" class="glyphicon glyphicon-plus" title="Adicionar Series"></a></span> 
                            <span><a href="dashboard.php?exe=categorias/update&category=<?= $category_id ?>" class="glyphicon glyphicon-pencil" title="Editar"></a></span> 
                            <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=categorias/index&category={$category_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir esta categoria ?')\" title=\"Deletar\"></a>"; ?></span>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
</div>