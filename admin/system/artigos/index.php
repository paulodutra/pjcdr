<?php

 /**
 * Todas as classes administrativas serão carregadas manualmente 
 * De acordo com a estrutura Front Controller que não necessita navegar 
 * Entre as pastas(definido em painel.php)
 */
    require_once('_models/AdminPost.class.php'); //só ira incluir caso não tenha incluido

        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);

        if ($empty):
            MSGErro("<b>Erro ao editar:</b> Você tentou editar um post que não existe !", MSG_ALERT);
        endif;

        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);

        if ($action):
            $postAction = filter_input(INPUT_GET, 'artigo', FILTER_VALIDATE_INT);
            $postUpdate = new AdminPost();
            switch ($action):
                case'active':
                    $postUpdate->ExeStatus($postAction, '1');
                        MSGErro("<b>Status do post Atualizado:</b> Para <b>Ativo</b>. Post Publicado !", MSG_ACCEPT);
                    break;
                case'inactive':
                    $postUpdate->ExeStatus($postAction, '0');
                    MSGErro("<b>Status do post Atualizado:</b> Para <b>Inativo</b>.O Post agora é uma rascunho !", MSG_ALERT);
                    break;
                case'delete':
                    $postUpdate->ExeDelete($postAction);

                    MSGErro($postUpdate->getError()[0], $postUpdate->getError()[1]);

                    break;
                default:
                    MSGErro("Ação não existe, utilize os botões!", MSG_ALERT);

            endswitch;

        endif;

    $posti = 0;
    $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

    $Pager = new Pager('dashboard.php?exe=artigos/index&page=');
    $Pager->ExePager($getPage, 4);

   $readPosts= new Read();
   $readPosts->ExeRead('blog_posts', "ORDER BY post_status ASC, post_date DESC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");

    if ($readPosts->getResult()):
                foreach ($readPosts->getResult() as $post):
                    //Permite pegar o nome da coluna do banco é utilizar como variavel
                    $posti++;
                    extract($post);
                    //responsável por formatar o stilo
                    $status=(!$post_status ? 'style="background: #fffed8" ' : '');
?>

<div class="row">
    <div class="col-lg-6">
        <div class="thumbnail"  <?= $status ?>>
            <img src="<?=HOME.'/uploads/'.$post_cover?>" alt="<?=$post_title?>" width="120" heigth="70">
             
            <div class="caption">
                <h3><a><?=$post_title?></a></h3>

                <div class="text-center">
                    <p class="text-left"><strong>Data:</strong><?= date('d/m/Y H:i', strtotime($post_date));?>Hs</p>
                    <span class="text-right">
                        <span><a class="glyphicon glyphicon-home" href="../artigo/<?=$post_name?>" title="Ver no site"></a></span>
                        <span><a class="glyphicon glyphicon-pencil" href="dashboard.php?exe=artigos/update&artigo=<?=$post_id?>" title="Editar"></a></span>
                        <?php if (!$post_status): ?>
                            <span><a class="glyphicon glyphicon-ok"  href="dashboard.php?exe=artigos/index&artigo=<?=$post_id?>&action=active" title="Ativar"></a></span>
                        <?php else: ?>
                            <span><a class="glyphicon glyphicon-remove" href="dashboard.php?exe=artigos/index&artigo=<?=$post_id?>&action=inactive" title="Desativar"></a></span>
                         <?php endif; ?>
                         <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=artigos/index&artigo={$post_id}&action=delete\" onclick=\"return confirm('Deseja realmente excluir este artigo ?')\" title=\"Deletar\"></a>";?></span>
                    </span>
                </div>
            </div>
        </div>
        
    </div><!--row-->
     <?php 
            endforeach;
        else:
            /**
             * Caso passe uma pagina que não exista, será retornado para a ultima pagina com resultados
             * Caso o metodo falhe exibe a mensagem de erro
             */
            $Pager->ReturnPage();
            MSGErro("Desculpe está página não possui posts !", MSG_ALERT);

        endif;
         $Pager->ExePaginator('blog_posts');
        echo $Pager->getPaginator();
        ?>
   