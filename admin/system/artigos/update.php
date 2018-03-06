<?php
/**
 * Todas as classes administrativas serão carregadas manualmente 
 * De acordo com a estrutura Front Controller que não necessita navegar 
 * Entre as pastas(definido em painel.php)
 */
require_once('_models/AdminPost.class.php'); //só ira incluir caso não tenha incluido
$post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$postid = filter_input(INPUT_GET, 'artigo', FILTER_VALIDATE_INT);
$post['post_author'] = $_SESSION['userlogin']['user_id'];
if (isset($post) && isset($post['SendPostForm'])):
    $post['post_status'] = ($post['SendPostForm'] == 'Cadastrar' ? '0' : '1');
    /**
     * Validando a imagem: se existir no indice post_cover o tmp_name 
     * então possui arquivo o post_cover irá receber ele mesmo caso contrario recebe null
     */
    $post['post_cover'] = ($_FILES['post_cover']['tmp_name'] ? $_FILES['post_cover'] : null );
    //Retirando o value do botão submit
    unset($post['SendPostForm']);
    /**
     * Todas as classes administrativas serão carregadas manualmente 
     * De acordo com a estrutura Front Controller que não necessita navegar 
     * Entre as pastas(definido em painel.php)
     */
    $create = new AdminPost();
    $create->ExeUpdate($postid, $post);

    if ($create->getResult()):
        //Enviar a galeria caso exista
        if (!empty($_FILES['gallery_covers']['tmp_name'])):
            $sendGallery = new AdminPost();
            $sendGallery->gallerySend($_FILES['gallery_covers'], $create->getResult());
        endif;
        /*         * Exibe a mensagem caso o resultado seja positvo */
        //header('Location: painel.php?exe=posts/update&create=true&postid='  .$create->getResult());
        MSGErro($create->getError()[0], $create->getError()[1]);

    else:
        /**
         * Quando o erro vem da classe utiliza-se a função get da mesma 
         * Pegando o indice apenas o RESULTADO com a mensagem [0] e o indice com o tipo de erro [1]
         */
        MSGErro($create->getError()[0], $create->getError()[1]);
    endif;
else:
    $readPost = new Read();
    $readPost->ExeRead("blog_posts", "WHERE post_id = :id", "id={$postid}");
    if (!$readPost->getResult()):
        header('Location: dashboard.php?exe=artigos/index&empty=true');
    else:
        $post = $readPost->getResult()[0];
        $post['post_date'] = date('d/m/Y H:i:s', strtotime($post['post_date']));
    endif;

endif;
?>
<form name="formArtigo"  action="" method="post" enctype="multipart/form-data">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">
                <div class="form-group col-lg-7">
                    <label>Enviar Capa: </label>
                    <input type="file"   class="form-control" name="post_cover" required>
                </div>
                <div class="form-group col-lg-7">
                    <label>Titulo:</label>
                    <input type="text" class="form-control"  placeholder="Titulo" name="post_title" value="<?php if (isset($post['post_title'])) echo $post['post_title']; ?>" required>
                </div>
                <div class="form-group col-lg-7">
                    <label>Conteudo:</label>
                    <textarea class="js_editor" class="form-control" name="post_content" rows="10" required><?php if (isset($post['post_content'])) echo htmlspecialchars($post['post_content']); ?></textarea>
                </div>
                <div class="form-group col-lg-9">
                    <div class="form-group col-lg-3">
                        <label>Data:</label>
                        <input type="text" class="form-control"  name="post_date" value="<?= date('d/m/Y H:i:s') ?>" id="datepost" required>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Categoria:</label>
                        <select name="post_category" class="form-control" required>
                            <option value="">Selecionar categoria</option>
                            <?php
                            $readSes = new Read;
                            $readSes->ExeRead("blog_categories", "WHERE category_parent IS NULL ORDER BY category_url ASC");
                            //Caso houver seção sem categoria não deixar seleciona-las
                            if ($readSes->getRowCount() >= 1):
                                foreach ($readSes->getResult() as $ses):
                                    //disabled: para não deixar selecionar as categorias sem seção
                                    echo " <option disabled=\"disabled\" value=\"\">{$ses['category_name']}</option>";
                                    //realiza a leitura das categorias da seção caso houver
                                    $readCat = new Read;
                                    $readCat->ExeRead("blog_categories", "WHERE category_parent =:parent ORDER BY category_url ASC", "parent={$ses['category_id']}");
                                    if ($readCat->getRowCount() >= 1):
                                        foreach ($readCat->getResult() as $cat):

                                            echo"  <option ";

                                            /* Persistencia de dados Se o opção selecionada no option for igual a de leitura  então selected selected */
                                            if ($post['post_category'] == $cat['category_id']):
                                                echo "selected=\"selected\" ";
                                            endif;

                                            echo "value=\"{$cat['category_id']}\">&raquo;&raquo;{$cat['category_name']}</option>";
                                        endforeach; //end leitura categoria
                                    else:
                                        echo"  <option  disabled=\"disabled\" value=\"\"> &raquo;&raquo; Seção não possui categoria cadastradas</option>";
                                    endif; //end leitura categoria

                                endforeach; //end leitura seção
                            endif; //End Leitura seção
                            ?>

                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Autor:</label>
                        <select name="post_author" class="form-control" required>
                            <option value="<?= $_SESSION['userlogin']['user_id'] ?>"><?= $_SESSION['userlogin']['user_name'] ?> <?= $_SESSION['userlogin']['user_lastname'] ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-lg-7">
                    <label>Enviar Galeria: </label>
                    <input type="file" multiple name="gallery_covers[]" />
                </div>

            </div><!--row-->
            <?php
            //**Realiza a exclusão de imagen(s) da galeria */
            $galleryDel = filter_input(INPUT_GET, 'gallerydel', FILTER_VALIDATE_INT);
            if ($galleryDel):
                $DeleteGallery = new AdminPost();
                $DeleteGallery->galleryRemove($galleryDel);

                MSGErro($DeleteGallery->getError()[0], $DeleteGallery->getError()[1]);
            endif;
            ?>
            <div class="row">
                <?php
                $gallery = 0;
                $readGallery = new Read();
                $readGallery->ExeRead("blog_posts_gallery", "WHERE post_id=:post", "post={$postid}");
                if ($readGallery->getResult()):
                    foreach ($readGallery->getResult() as $galleries):
                        $gallery++;
                        ?>
                        <div class="col-lg-4" id="galleryfoco">

                            <?php if ($gallery % 5 == 0) "class=text-right" ?>
                            <div class="thumbnail">
                                <!--imprimi a(s) imagem(s) da galeria-->
                                <?= Check::Image('../uploads/' . $galleries['gallery_image'], $gallery, 146, 100) ?>
                                <?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=artigos/update&artigo={$postid}&gallerydel={$galleries['gallery_id']}#galleryfoco\" onclick=\"return confirm('Deseja realmente excluir esta imagem ?')\" title=\"Deletar\"></a>";?>
                            </div>

                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <div class="text-center">
                <input type="submit" class="btn btn-primary" name="SendPostForm" value="Salvar como rascunho">
                <input type="submit" class="btn btn-success" name="SendPostForm" value="Atualizar artigo ">
            </div>
        </div>
    </div>
</form>