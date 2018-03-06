<div class="row">

    <section>

        <h3 class="text-uppercase text-center text-primary">Seção e categorias de noticias</h3>

        <?php
        //Exibe mensagem caso o usuário tente editar ou remover uma categoria que não existe
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);

        if ($empty):
            MSGErro("Você tentou editar uma categoria que não existe", MSG_INFOR);
        endif;

        //Verifica a categoria antes de deletar
        $deleteCategorie = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
        if ($deleteCategorie):
            /**
             * modo de navegação front controle não precisa navegar entre as pastas
             * Todas as classes administrativas são incluidas manualmente
             */
            require('_models/AdminCategoryPost.class.php');
            $delete = new AdminCategoryPost();
            $delete->ExeDelete($deleteCategorie);
            //Exibe mensagem para o usuário
            MSGErro($delete->getError()[0], $delete->getError()[1]);
            
        endif;

        //Realiza a leitura das categorias

        $readSes = new Read();
        $readSes->ExeRead('blog_categories', "WHERE category_parent IS NULL ORDER BY category_name ASC");
        if (!$readSes->getResult()):
            MSGErro("Não há nenhuma seção ou categoria cadastrada !", MSG_INFOR);
        else:
            foreach ($readSes->getResult() as $ses):
                //Transforma o nome das colunas da tabela em variaveis   
                extract($ses);
           
                //Realiza a leitura que faz a contagem dos post
                $readPost = new Read();
                $readPost->ExeRead('blog_posts', "WHERE post_cat_parent = :parent", "parent={$category_id}");

                //Conta o numero de posts da categoria
                $countSesPost = $readPost->getRowCount();
                //Realiza a leitura que faz a contagem das categorias
                $readCats = new Read();
                $readCats->ExeRead('blog_categories', "WHERE category_parent = :parent", "parent={$category_id}");

                //Conta o numero de categorias
                $countSesCats = $readCats->getRowCount();
                ?>
                <section>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h4>Seção: <?= $category_name; ?><span>(<?= $countSesPost; ?>  Posts ) (<?= $countSesCats; ?> Categorias )</span></h4>
                            <p>Descrição da seção: <?= $category_description; ?></p>

                            <div>
                                <span><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($category_date)); ?>Hs</span>
                                <div class="panel panel-primary">
                                    <span><a class="glyphicon glyphicon-home" target="_blank" href="../categoria/<?=$category_url?>" title="Ver no site"></a></span>
                                    <span><a class="glyphicon glyphicon-pencil" href="dashboard.php?exe=category/update&categoria=<?= $category_id; ?>" title="Editar"></a></span>
                                    <span><a class="glyphicon glyphicon-trash" href="dashboard.php?exe=category/index&delete=<?= $category_id; ?>" title="Excluir"></a></span>
                                </div>
                            </div>


                    <h5 class="text-uppercase">categoria(s) da seção <?= $category_name; ?></h5>

                    <?php
                    //Realiza a leitura das subcategorias
                    $readSub = new Read();
                    $readSub->ExeRead('blog_categories', "WHERE category_parent = :subparent", "subparent={$category_id}");

                    if (!$readSub->getResult()):
                        MSGErro("A seção <b>{$category_name}</b> não possui categorias", MSG_INFOR);
                    else:
                        $a = 0;
                        foreach ($readSub->getResult() as $sub):
                            //variavel $A está validando a classe de estilo de css
                            $a++;

                            $readCatPost = new Read();
                            $readCatPost->ExeRead('blog_posts', "WHERE post_category = :categoryId", "categoryId={$sub['category_id']}");
                            ?>
                            <div class="col-lg-4 <?php if ($a % 3 == 0) echo 'right'; ?>">
                                <div class="jumbotron">
                                    <h4><a  class="text-info"target="_blank" href="../categoria/<?= $sub['category_url']; ?>" title="Ver Categoria"><?= $sub['category_name']; ?>:</a>  ( <?= $readCatPost->getRowCount(); ?> posts )</h4>

                                    <div>
                                        <p><span>Data:<?= date('d/m/Y H:i', strtotime($sub['category_date'])); ?>Hs</span></p>
                                        <div class="text-center">
                                            <span><a class="glyphicon glyphicon-home" target="_blank" href="../categoria/<?= $sub['category_url']; ?>" title="Ver no site"></a></span>
                                            <span><a class="glyphicon glyphicon-pencil" href="dashboard.php?exe=category/update&categoria=<?= $sub['category_id']; ?>" title="Editar"></a></span>
                                            <span><a class="glyphicon glyphicon-trash" href="dashboard.php?exe=category/index&delete=<?= $sub['category_id']; ?>" title="Excluir"></a></span>
                                        </div>
                                    </div>
                                </div>        
                            </div>
                            <?php
                            //End leitura de subcategorias
                        endforeach;
                    endif;
                    ?>

                </section>
                <?php
                //end leitura de seção
            endforeach;
        endif;
        ?>

            </div>    
        </div>

    </section>
</div> <!--row-->