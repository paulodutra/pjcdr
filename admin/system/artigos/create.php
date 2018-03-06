<?php
 $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
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
            require('_models/AdminPost.class.php');
            $create = new AdminPost();
            $create->ExeCreate($post);

            if ($create->getResult()):
                //Enviar a galeria caso exista
                if (!empty($_FILES['gallery_covers']['tmp_name'])):
                    $sendGallery = new AdminPost();
                    $sendGallery ->gallerySend($_FILES['gallery_covers'], $create->getResult());
                endif;
                /**Exibe a mensagem caso o resultado seja positvo*/
                //header('Location: painel.php?exe=posts/update&create=true&postid='  .$create->getResult());
                 MSGErro($create->getError()[0], $create->getError()[1]);
    
            else:
                /**
                 * Quando o erro vem da classe utiliza-se a função get da mesma 
                 * Pegando o indice apenas o RESULTADO com a mensagem [0] e o indice com o tipo de erro [1]
                 */
                MSGErro($create->getError()[0], $create->getError()[1]);
            endif;
        endif;

?>
<form name="formArtigo"  action="" method="post" enctype="multipart/form-data" novalidate>
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
                    <textarea  class="js_editor form-control" name="post_content" rows="10" required ><?php if (isset($post['post_content'])) echo htmlspecialchars($post['post_content']); ?></textarea>
                </div>
                <div class="form-group col-lg-9">
                    <div class="form-group col-lg-3">
                        <label>Data:</label>
                        <input type="text" class="form-control"  name="post_date" id="datepost" value="<?=date('d/m/Y H:i:s')?>" id="datepost" required>
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
                            <option value="<?=$_SESSION['userlogin']['user_id']?>"><?=$_SESSION['userlogin']['user_name']?> <?=$_SESSION['userlogin']['user_lastname']?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-lg-7">
                    <label>Enviar Galeria: </label>
                    <input type="file" multiple name="gallery_covers[]"/>
                </div>

            </div><!--row-->
            <div class="text-left">
                <input type="submit" class="btn btn-primary" name="SendPostForm" value="Salvar como rascunho">
                <input type="submit" class="btn btn-success" name="SendPostForm" value="Publicar artigo ">
            </div>
        </div>
    </div>
</form>