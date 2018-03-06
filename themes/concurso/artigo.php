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

<!--HOME CONTENT-->
<div class="container">
    <div class="row">
        <div class="col-lg-12">

            <article>
                <!--CABEÇALHO GERAL-->
                <header>
                    <hgroup>
                        <h1 class="text-center"><?= $post_title; ?></h1><p class="text-left"><span class="glyphicon glyphicon-time"></span> <time datetime=" <?= date('Y/m/d H:i', strtotime($post_date)); ?>" pubdate>Publicado em: <?= date('d/m/Y H:i', strtotime($post_date)); ?>Hs</time></p>
                        <hr>
                    </hgroup>
                </header>
                <!--CONTEUDO-->
                <div class="htmlchars">

                    <?= $post_content; ?>

                    <!--GALERIA-->
                    <?php
                    $readGallery = new Read();
                    $readGallery->ExeRead('blog_posts_gallery', "WHERE post_id= :postid ORDER BY gallery_date DESC", "postid={$post_id}");
                    if ($readGallery->getResult()):
                        ?>

                    <section class="jumbotron">
                            <hgroup>
                                <h3>
                                    
                                    <p class="text-primary">GALERIA: Veja fotos em <b><?= $post_title; ?></b></p>
                                </h3>
                            </hgroup>

                            <ul >
                                <?php
                                $gb = 0;
                                foreach ($readGallery->getResult() as $gallery):
                                    $gb++;
                                    /*                                     * Criando o indice Galeria para ser apresentado na view */
                                    $gallery['Galeria'] = "{$gb} do post {$post_title}";
                                    /** permiti utilizar os nome das colunas da tabela como variaveis */
                                    extract($gallery);
                                    /*                                     * Instanciando e utilizando um objeto da classe HELPER View, primeiro carregando a view em uma variavel e depois 
                                      passando os dados e a template view para o metodo show da classe VIEW */
                                    $View = new View();
                                    $galeria_article = $View->Load('galeria_article');
                                    $View->Show($gallery, $galeria_article);
                                endforeach;
                                ?>
                            </ul>

                            <div class="clear"></div>

                        <?php endif; ?>
                    </section>
                </div>

                <?php
                $readMore = new Read();
                $readMore->ExeRead('blog_posts', "WHERE post_status=1 AND post_id != :postid AND post_category=:category ORDER BY rand() LIMIT 3", "postid={$post_id}&category={$post_category}");
                if ($readMore->getResult()):
                    /** Carrega a view caso exista */
                    $View = new View();
                    $tpl = $View->Load('article_m');
                    ?>
                    <hr>
                    <!--Comentários aqui-->
                    <div class="fb-comments" data-href="<?= HOME . '/artigo/' . $post_name ?>" data-numposts="5" data-order-by="reverse_time" data-width="100%"></div>
                    <!--RELACIONADOS-->
                    <h3 class="lead">Veja também:</h3>
                    <?php
                    foreach ($readMore->getResult() as $more):
                        $more['datetime'] = date('Y-m-d', strtotime($more['post_date']));
                        $more['pubdate'] = date('d-m-Y H:i', strtotime($more['post_date']));
                        $more['post_content'] = Check::Words($more['post_content'], 10);
                        /** apresenta a view */
                        $View->Show($more, $tpl);
                    endforeach;
                    ?>
                <?php endif; ?>    
            </article>

        </div>
    </div>
</div>

