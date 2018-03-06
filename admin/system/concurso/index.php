<h4 class="text text-primary text-center">Concursos cadastrados</h4>
<?php

require_once('_models/AdminConcurso.class.php');
/** Exibe mensagem depois de efetuar um cadastro*/

$checkCreate=filter_input(INPUT_GET,'create',FILTER_VALIDATE_BOOLEAN);
if($checkCreate):
    MSGErro("<b>Sucesso ao atualizar:</b> Concurso de redação atualizado com sucesso",MSG_ACCEPT);
endif;

$action=filter_input(INPUT_GET,'action',FILTER_DEFAULT);

if($action):
    $concursoid=filter_input(INPUT_GET,'concursoid',FILTER_VALIDATE_INT);

    $concursoUpdate= new AdminConcurso();
    switch ($action):
        case 'active':

            $concursoUpdate->ExeStatus($concursoid,'1');
            if($concursoUpdate->getResult()):
                 MSGErro("<b>Status atualizado:</b> O concurso foi atualizado para o status publicado!",MSG_ACCEPT);
            else:
                MSGErro($concursoUpdate->getError()[0],$concursoUpdate->getError()[1]);
            endif;

        break;

        case 'inactive':

            $concursoUpdate->ExeStatus($concursoid,'0');
            if($concursoUpdate->getResult()):
                MSGErro("<b>Status atualizado:</b> O concurso foi atualizado para o status rascunho!",MSG_ACCEPT);
            else:
                MSGErro($concursoUpdate->getError()[0],$concursoUpdate->getError()[1]);
            endif;

        break;    

        case 'delete':
            $concursoUpdate->ExeDelete($concursoid);
            //MSGErro("<b>Concurso Excluido:</b> O concurso foi excluido com sucesso !!",MSG_ACCEPT);
        break;        
        
        default:
            MSGErro("Ação não existe, utilize os botões!", MSG_ALERT);
            break;
      endswitch;
         MSGErro($concursoUpdate->getError()[0], $concursoUpdate->getError()[1]);
endif;    

$getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

$pagination= new Pager('dashboard.php?exe=concurso/index&page=');
$pagination->ExePager($getPage, 2);//Numero de concursos a ser exibidos por paginas

$readConcurso= new Read();
$readConcurso->ExeRead('cs_concurso', "ORDER BY concurso_date_registration DESC, concurso_status DESC LIMIT :limit OFFSET :offset", "limit={$pagination->getLimit()}&offset={$pagination->getOffset()}");

if ($readConcurso->getResult()):
    foreach ($readConcurso->getResult() as $concurso):
        //Permite pegar o nome da coluna do banco é utilizar como variavel
        extract($concurso);
        //responsável por formatar o stilo
        $status = (!$concurso_status ? 'style="background: #fffed8" ' : '');
?>
<div class="row">
    <div class="col-lg-6">
        <div class="thumbnail"<?= $status ?>>
            <img src="<?=HOME.'/uploads/'.$concurso_logo?>" alt="<?=$concurso_name?>" class="img-thumbnail" width="242" height="200">
              <div class="caption">
                <h3><a><?= Check::Words($concurso_name, 10) ?></a></h3>
                <div class="text-center">
                        <p class="text-left"><b>Data de inicio: </b><?=date('d/m/Y H:i:s',strtotime($concurso_start))?></p>
                        <p class="text-left"><b>Data de termino: </b><?=date('d/m/Y H:i:s',strtotime($concurso_end))?></p>
                    <span class="text-right">
                        <span><a class="glyphicon glyphicon-home" href="../concurso/<?=$concurso_url;?>" target="_blank" title="Ver no site"></a></span>
                        <span><a class="glyphicon glyphicon-search" href="dashboard.php?exe=concurso/details&concurso=<?=$concurso_id?>" title="Visualizar"></a></span>
                        <span><a class="glyphicon glyphicon-plus" href="dashboard.php?exe=concurso/category&concurso=<?=$concurso_id?>" title="Adicionar Categoria(s)"></a></span>
                        <span><a class="glyphicon glyphicon-pencil" href="dashboard.php?exe=concurso/update&concursoid=<?=$concurso_id?>" title="Editar"></a></span>
                        <span><a class="glyphicon glyphicon-upload" href="dashboard.php?exe=concurso/file&concurso=<?=$concurso_id?>" title="Enviar Arquivo"></a></span>
                        <?php if(!$concurso_status):?>
                        <span><a class="glyphicon glyphicon-ok"  href="dashboard.php?exe=concurso/index&concursoid=<?=$concurso_id?>&action=active" title="Ativar"></a></span>  
                        <?php else: ?>
                        <span><a class="glyphicon glyphicon-remove" href="dashboard.php?exe=concurso/index&concursoid=<?=$concurso_id?>&action=inactive" title="Desativar"></a></span>
                        <?php endif;?>
                        <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=concurso/index&concursoid={$concurso_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir este concurso ?')\" title=\"Deletar\"></a>";?></span>

                       
                    </span>
                </div>
            </div>
        </div>
    </div> 

    

<?php
    endforeach;
?>
</div><!--row-->
<?php
else:
  /**
    * Caso passe uma pagina que não exista, será retornado para a ultima pagina com resultados
    * Caso o metodo falhe exibe a mensagem de erro
    */
    $pagination->ReturnPage();
    MSGErro("Desculpe está página não possui concursos cadastrados !", MSG_ALERT);                     
endif;  
        $pagination->ExePaginator('cs_concurso');
        echo $pagination->getPaginator();
?>






    

    