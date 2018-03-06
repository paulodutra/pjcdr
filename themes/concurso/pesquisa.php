<?php
/**
 * $Link->getData objeto da classe LINK instanciado no index geral do sistema  que fica na mesma 
  pasta do arquivo .htacess
 * $Link->getLocal()[1] = (url do site/pesquisa/{oque foi pesquisado pelo usuário} 
 */
$search = $Link->getLocal()[1];
$count = ($Link->getData()['count'] ? $Link->getData()['count'] : '0');
?>

<!--HOME CONTENT-->
<div class="site-container">

    <section class="page_categorias">
        <header class="cat_header">
            <h2>Pesquisa por : <?= $search; ?></h2>
            <p class="tagline">Sua pesquisa por <?= $search; ?> retornou <?= $count; ?> resultado(s)!</p>
        </header>

        <?php
        /**
         * $Link->getLocal()[2] = categoria/noticias/{id da paginação}
         * Obs: $Link = objeto da classe link instanciado na index geral do sistema na mesma pasta do .htacess
         */
        $getpage = (!empty($Link->getLocal()[2]) ? $Link->getLocal()[2] : 1 );
        /** Instancia um objeto da classe pager passando o link com todos os indices(do link) corretos  e executa a paginação */
        $Pager = new Pager(HOME . '/pesquisa/' . $search . '/');
        $Pager->ExePager($getpage, 12);


        $readSearch = new Read();
        $readSearch->ExeRead('ws_posts', "WHERE  post_status = 1 AND (post_title LIKE '%' :link  '%' OR post_content LIKE '%' :link '%') ORDER BY post_date DESC LIMIT :limit OFFSET :offset ", "link={$search}&limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
        if (!$readSearch->getResult()):
            /** Retorna para uma pagina que tenha resultados caso o usuário passe um numero de pagina que não possui resultado */
            $Pager->ReturnPage();
            /**  Exibe mensagem de erro caso a categoria não tenha resultados */
            MSGErro("Desculpe sua pesquisa por: {$search} não retornou resultados ! Você pode resumir sua pesquisa ou tentar outros termos ! ", MSG_INFOR);
        else:
            /*             * Instancia um objeto da classe view e realiza o carregamento da mesma apenas se existirem resultados */
            $View = new View();
            $tpl_search = $View->Load('article_m');
            /*             * $cc: conta o numero de categorias para aplicar o css caso seja necessário */
            $cc = 0;
            foreach ($readSearch->getResult() as $postsearch):
                $cc++;
                $class = ($cc % 3 == 0 ? ' class=right' : null);
                echo "<span {$class}>";
                $postsearch['datetime'] = date('Y-m-d', strtotime($postsearch['post_date']));
                $postsearch['pubdate'] = date('d-m-Y H:i', strtotime($postsearch['post_date']));
                $postsearch['post_title'] = Check::Words($postsearch['post_title'], 9);
                $postsearch['post_content'] = Check::Words($postsearch['post_content'], 20);
                /** apresenta a view */
                $View->Show($postsearch, $tpl_search);
                echo "</span>";
            endforeach;
        endif;

        /** Exibe o painel de paginação */
        echo '<nav class="paginator">';
        echo '<h2>Mais resultados para</h2>';

        $Pager->ExePaginator('ws_posts', "WHERE  post_status = 1 AND (post_title LIKE '%' :link  '%' OR post_content LIKE '%' :link '%')", "link={$search}");
        echo $Pager->getPaginator();

        echo '</nav>';
        ?>  

    </section>

    <div class="clear"></div>
</div><!--/ site container -->