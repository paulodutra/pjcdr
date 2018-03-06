<?php
$View = new View();
$article_duvidas = $View->Load('article_duvidas');
?>
<h1 class="text-uppercase text-center">Duvidas Frequentes</h1>
<?php
        $getpage=(!empty($Link->getLocal()[1]) ? $Link->getLocal()[1] : '');
        /** Instancia um objeto da classe pager passando o link com todos os indices(do link) corretos  e executa a paginação */
        $Pager = new Pager(HOME . '/duvidas/');
        $Pager->ExePager($getpage,10);

        $cat=Check::CatByName('Duvidas');
        $duvidas= new Read();
        $duvidas->ExeRead('blog_posts',"WHERE post_status= 1 AND (post_cat_parent=:cat OR post_category =:cat) ORDER BY post_id ASC LIMIT :limit OFFSET :offset ", "cat={$cat}&limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
        if (!$duvidas->getResult()):
            MSGErro("Desculpe ainda não existem duvidas cadastradas !", MSG_INFOR);
        else:
                   
            foreach ($duvidas->getResult() as $duvida):
                extract($duvida);    
                $View->Show($duvida, $article_duvidas);

            endforeach;

    endif;   
     /** Exibe o painel de paginação */
    echo '<div class="text-center">';
    echo '<div class="row">';
    echo '<div class="col-md-8 col-md-offset-2">';
    $Pager->ExePaginator('blog_posts',"WHERE post_status= 1 AND (post_cat_parent=:cat OR post_category =:cat) ORDER BY post_id ASC", "cat={$cat}");
    echo $Pager->getPaginator();
    echo '</div>';
    echo '</div>';
    echo '</div>';  
?> 

         
 