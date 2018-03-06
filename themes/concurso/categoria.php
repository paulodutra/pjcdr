<?php
/**
 * $Link: Objeto da classe Link.class.php, que foi instanciado no index.php geral do sistema(que fica junto com o .htacess) este index é o frontcontroller
 * da aplicação é ele que os INCLUDE_PATH e realiza as inclusões de tela , css e js.
 */
if ($Link->getData()):
    /** Permiti utilizar os indices do array que faz a consulta no banco como variaveis */
    extract($Link->getData());
else:
    header('Location: ' . HOME . DIRECTORY_SEPARATOR . '404');
endif;
?>
        <header>
            <div class="jumbotron">
                <h1 class="text-uppercase text-center"><?= $category_name; ?></h1>
            </div>
             <p class="text-primary text-center"><?= $category_description; ?></p>
        </header>
        <hr>
        <div class="row">
           <div class="col-md-12">
                <?php
                /**
                 * $Link->getLocal()[2] = categoria/noticias/{id da paginação}
                 * Obs: $Link = objeto da classe link instanciado na index geral do sistema na mesma pasta do .htacess
                 */
                $getpage = (!empty($Link->getLocal()[2]) ? $Link->getLocal()[2] : 1 );
                /** Instancia um objeto da classe pager passando o link com todos os indices(do link) corretos  e executa a paginação */
                $Pager = new Pager(HOME . '/categoria/'.$category_url . '/');
                $Pager->ExePager($getpage,3);


                $readPostCat = new Read();
                $readPostCat->ExeRead('blog_posts', "WHERE post_status = 1 AND (post_category = :cat OR post_cat_parent = :cat) ORDER BY post_date DESC LIMIT :limit OFFSET :offset", "cat={$category_id}&limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
                if (!$readPostCat->getResult()):
                    /** Retorna para uma pagina que tenha resultados caso o usuário passe um numero de pagina que não possui resultado */
                    $Pager->ReturnPage();
                    /**  Exibe mensagem de erro caso a categoria não tenha resultados */
                    MSGErro("Não existe artigo(s) publicadas  na categoria: {$category_name} !, ou não existem publicações para esta pagina ! ", MSG_INFOR);
                else:
                    /*             * Instancia um objeto da classe view e realiza o carregamento da mesma apenas se existirem resultados */
                    $View = new View();
                    $tpl_cat = $View->Load('article_m');
                    foreach ($readPostCat->getResult() as $postcat):
                        $postcat['datetime'] = date('Y-m-d', strtotime($postcat['post_date']));
                        $postcat['pubdate'] = date('d-m-Y H:i', strtotime($postcat['post_date']));
                        $postcat['post_title'] = Check::Words($postcat['post_title'], 9);
                        $postcat['post_content'] = Check::Words($postcat['post_content'], 20);
                        /** apresenta a view */
                        $View->Show($postcat, $tpl_cat);
                    endforeach;
                endif;


                ?>  
            </div>
            <?php
                /** Exibe o painel de paginação */
                echo '<div class="text-center">';
                echo '<div class="row">';
                echo '<div class="col-md-8 col-md-offset-2">';
                //echo '<nav class="pager">';
                $Pager->ExePaginator("blog_posts", "WHERE post_status = 1 AND (post_category = :cat OR post_cat_parent = :cat)", "cat={$category_id}");
                echo $Pager->getPaginator();
                //echo '</nav>';
                echo '</div>';
                echo '</div>';
                echo '</div>';


            ?>
</div>