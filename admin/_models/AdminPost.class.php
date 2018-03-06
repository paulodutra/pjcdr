<?php

/**
 *  AdminPost.class[MODEL ADMIN]
 * Classe responsável por gerenciar os posts no administrador
 * 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminPost{

    private $Data;
    private $Post;
    private $Error;
    private $Result;

    //Nome da tabela do banco
    const Entity = 'blog_posts';

    /**
     * <b>ExeCreate:</b> Metodo responsável por checar, validar e executar o cadastro de posts 
     * @param array $Data
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        //Validando campos em Branco 
        if (in_array('', $this->Data)):
            $this->Error = ["<b>Erro ao cadastrar:</b> Para criar um post, favor informe todos os campos", MSG_ALERT];
            $this->Result = false;
        else:
            $this->setData();
            $this->setName();
            //Se existir a imagem a ser enviada
            if ($this->Data['post_cover']):
                $upload = new Upload();
                $upload->Image($this->Data['post_cover'], $this->Data['post_name']);
            endif;
            /**
             * Se a imagem for enviada ele cadastrar o artigo e passa o caminho e nome da imagem 
             * Para ser cadastrado também
             */
            if(isset($upload) && $upload->getResult()):
                $this->Data['post_cover'] = $upload->getResult();
                $this->Create();
            //Se não for enviado ele cadastra o artigo(post)
            else:
                $this->Data['post_cover'] = null;
                $this->Create();
            endif;

        endif;
    }

    /**
     * <b>ExeUpdate:</b> Metodo responsável por checar, validar e atualizações de posts 
     * @param array $Data
     * 
     * OBS: 
     *  if($this->Data['post_cover'] != 'null'): Verifica se o a imagem foi enviada
     *   if (file_exists($capa) && !is_dir($capa)): verifica se o arquivo existe e não é uma pasta 
     * e deleta o mesmo 
     * logo apos reenvia a capa
     */
    public function ExeUpdate($PostId, array $Data) {
        $this->Post = (int) $PostId;
        $this->Data = $Data;
        /** Verifica se posssui campos em branco */
        if (empty( $this->Data)):
            $this->Error = ["Para atualizar este post preencha todos os campos, A capa não precisar ser enviada novamente! "];
            $this->Result = false;
        else:
            //** Caso não possua campos em branco*/
            $this->setData();
            $this->setName();
            /** BLOCO responsável por reenvio da capa */
            if (is_array($this->Data['post_cover'])):
                $readCapa = new Read();
                $readCapa->ExeRead(self::Entity, "WHERE post_id = :post", "post={$this->Post}");
                $capa = '../uploads/' . $readCapa->getResult()[0]['post_cover'];
                if (file_exists($capa) && !is_dir($capa)):
                    unlink($capa);
                endif;

                $uploadCapa = new Upload();
                $uploadCapa->Image($this->Data['post_cover'], $this->Data['post_name']);
            endif;
            // ** END BLOCO responsável por reenvio da capa */

            if (isset($uploadCapa) && $uploadCapa->getResult()):
                $this->Data['post_cover'] = $uploadCapa->getResult();
                $this->Update();
            //Se não for enviado ele capastrada a capa  do artigo(post)
            else:
                unset($this->Data['post_cover']);
                $this->Update();
            endif;

        endif;
    }
    /**
     * <b>ExeDelete:</b>Metodo responsável por Executar processo de exclusão do post da tabela, deletando também 
     * as imagens de capa e de galeria do post da pasta e das tabelas  ws_posts(dados do post) ws_posts_gallery(dados de galeria do post)
     * 
     * @param int $PostId
     */
    public function ExeDelete($PostId) {
        $this->Post = (int) $PostId;

        $postRead = new Read();
        $postRead->ExeRead(self::Entity, "WHERE post_id= :postid", "postid={$this->Post}");
        /**
         * !$postRead: Realiza leitura é verifica se o id do post não existe, caso seja verdade emite mensagem ao usuário caso contrario realiza todo o 
         * processo de exclusão do post
         */
        if (!$postRead->getResult()):
            $this->Error = ["O post que você tentou deletar não existe !", MSG_ERROR];
            $this->Result = false;
        else:
            /**
             * $deleteCover: Verifica se a capa do post existe e se não é um diretório(pasta) é deleta a imagem da pasta(uploads)
             */
            $deleteCover = $postRead->getResult()[0];
            if (file_exists('../uploads/' . $deleteCover['post_cover']) && !is_dir('../uploads/' . $deleteCover['post_cover'])):
                unlink('../uploads/' . $deleteCover['post_cover']);
            endif;
            $readGallery = new Read();
            $readGallery->ExeRead('blog_posts_gallery', "WHERE post_id= :postid", "postid={$this->Post}");
            /**
             * $readGallery: Verifica se o post possui galeria de imagens, caso exista realiza deleta as imagens da pasta(uploads)
             */
            if ($readGallery->getResult()):
                foreach ($readGallery->getResult() as $galleryDelete):
                    if (file_exists('../uploads/' . $galleryDelete['gallery_image']) && !is_dir('../uploads/' . $galleryDelete['gallery_image'])):
                        unlink('../uploads/' . $galleryDelete['gallery_image']);
                    endif;
                endforeach;
            endif;
            /**
             *$deletePost: Realiza a exclusão do Post no banco de dados, primeiro deletando as informações da galeria na tabela ws_posts_gallery e depois deletando o post
             */
            $deletePost = new Delete();
            $deletePost->ExeDelete('blog_posts_gallery', "WHERE post_id= :gallerypost", "gallerypost={$this->Post}");
            $deletePost->ExeDelete(self::Entity, "WHERE post_id= :postid", "postid={$this->Post}");
            /** Emite mensagem da operação para o usuário */
            $this->Error =["<b>Sucesso ao deletar:</b> O post <b>{$deleteCover['post_title']}, foi removido com sucesso!</b>", MSG_ACCEPT];
            $this->Result = true;


        endif;
       
    }

    /**
     * <b>ExeStatus:</b>Metodo responsável por atualizar o status do post  para ativo ou inativo de acordo com a 
     * escolha do usuário
     * 
     * @param int $PostId
     * @param string $PostStatus
     */
    public function ExeStatus($PostId, $PostStatus) {
        $this->Post=(int)$PostId;
        $this->Data['post_status']=(string)$PostStatus;

        $updateStatus = new Update();
        $updateStatus->ExeUpdate(self::Entity, $this->Data, "WHERE post_id=:postid", "postid={$this->Post}");
    }

    /**
     * <b>gallerySend:</b>Metodo responsável por enviar os arquivos de galeria para a pasta de uploads e cadastrar os dados na tabela 
     * ws_posts_gallery
     * 
     * @param array $Images
     * @param int $PostId
     */
    public function gallerySend(array $Images, $PostId) {
        $this->Post = (int) $PostId;
        $this->Data = $Images;
        //Realiza leitura para pegar o utilimo id de post da tabela, informado ao instanciar a classe
        $ImageName = new Read();
        $ImageName->ExeRead(self::Entity, "WHERE post_id = :id", "id={$this->Post}");
        //caso exista encontre  exibe mensagem de erro
        if (!$ImageName->getResult()):
            $this->Error = ["<b>Erro ao enviar galeria:</b> O código {$this->Post} não foi encontrado na base de dados ! ", MSG_ERROR];
            $this->Result = false;
        //Caso exista realiza o processo abaixo
        else:
            //Pega o nome do Post
            $ImageName = $ImageName->getResult()[0]['post_name'];

            //Criar um array para armazenar as imagen
            $galleryFiles = array();
            //Conta quantas imagens tem realmente através do indice tmp_name
            $galleryCount = count($this->Data['tmp_name']);
            //Pega a chave do array type, name, tmp_name entre outros
            $galleryKeys = array_keys($this->Data);
            //Realizar o loop enquanto for menor do que a contagem da imagens
            for ($gallery = 0; $gallery < $galleryCount; $gallery++):
                //Realiza a leitura da chave do array
                foreach ($galleryKeys as $keys):
                    //Atribuir para cada indice do array um imagem com o seu respectivo key
                    $galleryFiles[$gallery][$keys] = $this->Data[$keys][$gallery];
                endforeach;
            endfor;
            //Realiza o upload da galeria
            $gallerySendFiles = new Upload();

            //imagem 
            $i = 0;
            //upload
            $u = 0;
            //Realiza o envio para a pasta de uploads e cadastro dos dados das imagens na tabela
            foreach ($galleryFiles as $galleryUpload):
                $i++;
                $ImgNameSend = "{$ImageName}-gb-{$this->Post}-" . (substr(md5(time() + $i), 0, 5));
                $gallerySendFiles->Image($galleryUpload, $ImgNameSend);

                if ($gallerySendFiles->getResult()):
                    $galleryImage = $gallerySendFiles->getResult();
                    //Array de inserção
                    $galleryCreate = ['post_id' => $this->Post, "gallery_image" => $galleryImage, "gallery_date" => date('Y-m-d H:i:s')];
                    //cadastra a galeria no banco
                    $insertGallery = new Create();
                    $insertGallery->ExeCreate('blog_posts_gallery', $galleryCreate);
                    //conta o numero de upload
                    $u++;
                endif;
            endforeach;
            //Valida a quantidade de uploads e exibe mensagem ao usuário
            if ($u > 1):
                $this->Error = ["<b>Galeria Atualizada:</b> Foram enviados{$u} imagens para a galeria deste post", MSG_ACCEPT];
                $this->Result = true;
            endif;

        endif;
    }

    public function galleryRemove($galleryImageId) {
        $this->Post = (int) $galleryImageId;
        $readGallery = new Read();
        $readGallery->ExeRead('blog_posts_gallery', "WHERE gallery_id = :galleryid", "galleryid={$this->Post}");
        if ($readGallery->getResult()):
            $Imagem = '../uploads/' . $readGallery->getResult()[0]['gallery_image'];
            if (file_exists($Imagem) && !is_dir($Imagem)):
                unlink($Imagem);
            endif;
            $DeleteImageGallery = new Delete();
            $DeleteImageGallery->ExeDelete('blog_posts_gallery', "WHERE gallery_id=:galleryid", "galleryid={$this->Post}");
            if ($DeleteImageGallery->getResult()):
                $this->Error = ["<b>Sucesso ao Deletar:</b>A imagem foi removida com sucesso da galeria", MSG_ACCEPT];
                $this->Result = true;
            endif;

        endif;
    }

    /**
     * <b>getResult:</b> Metodo responsável por retornar o resultado da operação, podendo ser true ou false
     * @return bool Result
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>getError:</b>Metodo responsável por retornar a mensagem da operação em formato de array 
     * contendo 2 indices, o primeiro é a mensagem e o segundo é o tipo da mensagem
     * 
     * @return Array Error
     */
    public function getError() {
        return $this->Error;
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */

    /**
     * <b>setData:</b>Metodo responsável por tratar os dados, remover codigo html e espaços  exceto do indice content, 
     * Validar a url do Post e validar a data para timestamp antes de cadastrar
     */
    private function setData() {
        //Armazenando o indice de imagem e de conteudo para depois elimina-los
        $Cover = $this->Data['post_cover'];
        $Content = $this->Data['post_content'];

        //Eliminando indices
        unset($this->Data['post_cover'], $this->Data['post_content']);

        /**
         * Limpando codigos html, caracteres especiais etc
         * array_map('nome da função a ser executada no array', objeto a ser realizado)
         */
        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);

        /**
         * Criando url do post utilizando a função static Check:: Name
         */
        $this->Data['post_name'] = Check::Url($this->Data['post_title']);
        /**
         * Convertendo a data para formato timestamp(para cadastrar no banco) utilizando a função static Check :: Data
         */
        $this->Data['post_date'] = Check::Data($this->Data['post_date']);
        /** indentificando o tipo de post manualmente */
        $this->Data['post_type'] = 'post';

        /**
         * Armazenando novamente a imagem e o conteudo armazenado , antes de limpar o array Data
         */
        $this->Data['post_cover'] = $Cover;
        $this->Data['post_content'] = $Content;
        //valida a seção pai  da categoria selecionada no formulário
        $this->Data['post_cat_parent'] = $this->getCatParent();
    }

    /**
     * <b>getCatParent:</b>Metodo responsável por validar seção pai da categoria selecionada no formulario
     */
    private function getCatParent() {

        $readCat = new Read();
        $readCat->ExeRead('blog_categories', "WHERE category_id = :catId", "catId={$this->Data['post_category']}");

        if ($readCat->getResult()):
            return $readCat->getResult()[0]['category_parent'];
        else:
            return null;
        endif;
    }

    /**
     * <b>setName:</b>Metodo responsável por verificar se existe post com o mesmo nome a ser cadastrado, caso tenha 
     * ele ira concatenar com o numero de vezes que aquele nome foi cadastrado 
     * ex: teste, teste-1;
     * Para não ter dois artigos cadastrados com o mesmo link
     */
    private function setName() {

        $Where = (isset($this->Post) ? "post_id != {$this->Post} AND" : '' );

        $readName = new Read();
        $readName->ExeRead(self::Entity, "Where {$Where} post_title = :title ", "title={$this->Data['post_title']}");

        if ($readName->getResult()):
            $this->Data['post_name'] = $this->Data['post_name'].'-'.$readName->getRowCount();
        endif;
    }

    /**
     * <b>Create:</b>Metodo responsável por cadastrar os dados propriamente dito
     * na tabela que foi definida na constante const Entity
     */
    private function Create() {
        $create = new Create();
        $create->ExeCreate(self::Entity, $this->Data);

        if ($create->getResult()):
            $this->Error = [" O post <b>{$this->Data['post_title']}</b> , foi cadastrado com sucesso! ", MSG_ACCEPT];
            $this->Result = $create->getResult(); //Retornar o ultimo ID inserido
        endif;
    }

    /**
     * <b>Update:</b>Metodo responsável por atualizar os dados propriamente dito
     * na tabela que foi definida na constante const Entity
     */
    private function Update() {
        $update = new Update();
        $update->ExeUpdate(self::Entity, $this->Data, "WHERE post_id=:id", "id={$this->Post}");

        if ($update->getResult()):
            $this->Error = [" O post <b> {$this->Data['post_title']}</b> , foi atualizado com sucesso! ", MSG_ACCEPT];
            $this->Result = true;
        endif;
    }

}
