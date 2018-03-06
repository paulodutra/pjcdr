<?php

/**
 *  AdminCategory.class[MODEL ADMIN]
 * Responsável por gerenciar as categorias do sistema na administração
 * 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminCategoryPost{

    private $Data;
    private $CatId;
    private $Error;
    private $Result;

    //Nome da tabela no banco
    const Entity = 'blog_categories';

    /**
     * <b>ExeCreate:</b> Metodo responsável por checar, validar e executar o cadastro de categorias
     * @param array $Data
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;
        //verifica se possui campos em branco
        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ['<b>Erro ao cadastrar:</b> Para cadastrar uma categoria, informe todos os campos', MSG_ALERT];

        else:
            $this->setData();
            $this->setName();
            $this->Create();
        endif;
    }

    /**
     * <b>ExeCreate:</b> Metodo responsável por checar, validar e executar atualizações de categorias
     * @param array $Data
     */
    public function ExeUpdate($CategoryId, array $Data) {
        $this->CatId = (int) $CategoryId;
        $this->Data = $Data;
        //verifica se possui campos em branco
        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao Atualizar:</b> Para atualizar a categoria {$this->Data['category_title']}, informe todos os campos", MSG_ALERT];

        else:
            $this->setData();
            $this->setName();
            $this->Update();
        endif;
    }

    /**
     * <b>ExeDelete:</b> Metodo responsável por realizar as validações e deletar a categoria caso a mesma 
     * possa ser deletada
     * @param int $CategoryId
     */
    public function ExeDelete($CategoryId) {
        $this->CatId = (int) $CategoryId;

        /**
         * Verifica se é uma seção(categoria pai) ou uma categoria(subcategoria) verifica se tem posts cadastrados
         * e todas as exceções antes de deletar
         */
        $readDelete = new Read();
        $readDelete->ExeRead(self::Entity, "WHERE category_id = :deleteId", "deleteId={$this->CatId}");
        //Se a categoria não existir
        if (!$readDelete->getResult()):
            $this->Result = false;
            $this->Error = ['<b>Erro ao deletar:</b> Você tentou deletar uma categoria que não existe !', MSG_ALERT];
        //Se a categoria existir
        else:
            /**
             * extract: permiti utilizar as colunas da tabela como variaveis
             * indice [0] pegando apenas o resultado
             */
            extract($readDelete->getResult()[0]);
            //! category_parent : signigica que é uma seção
            if (!$category_parent && !$this->checkCats()):
                $this->Result = false;
                $this->Error = ["<b>Erro ao deletar:</b> A seção {$category_name} possui categorias cadastradas. Para deletar, antes altere ou remova as categorias filhas !", MSG_ALERT];
             //! category_parent : signigica que é uma seção e tiver artigos não deleta     
           elseif (!$category_parent && !$this->checkPosts()):
                $this->Result = false;
                $this->Error = ["<b>Erro ao deletar:</b> A seção {$category_name} possui artigos cadastrados. Para deletar, antes altere ou remova todos os posts desta seção  !", MSG_ALERT];
            //category_parent : signigica que é uma categoria
            elseif ($category_parent && !$this->checkPosts()) :
                $this->Result = false;
                $this->Error = ["<b>Erro ao deletar:</b> A categoria {$category_name} possui artigos cadastrados. Para deletar, antes altere ou remova todos os posts desta categoria !", MSG_ALERT];
            //caso não seja uma seção ou uma categoria ou não tenha post cadastros pode deletar
            else:
                /**$deleteSesCat:deleta seção ou categoria*/
                $deleteSesCat = new Delete();
                $deleteSesCat->ExeDelete(self::Entity, "WHERE category_id = :deleteId", "deleteId={$this->CatId}");

                //$tipo: realiza validação para ver se é uma categoria ou subcategoria
                $tipo = (empty($category_parent) ? 'seção' : 'categoria');
                $this->Result = true;
                $this->Error = ["<b>Sucesso ao deletar:</b> A {$tipo} {$category_name}, foi removida com sucesso  !", MSG_ACCEPT];


            endif;



        endif;
    }

    /**
     * <b>getResult:</b>Metodo responsável por  retornar o resultado da operação
     * @return boll Result
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>getError:</b>Metodo responsável por  retornar a mensagem da operação
     * @return string Error
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
     * 
     */

    /**
     * <b>setData:</b>Metodo responsável por limpar codigos html, caracteries especiais e atribuir o valor para os indices do array
     */
    private function setData() {
        //pega cada indice do array e realiza a função informada
        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);
        //Criar o nome da categoria no proprio indice a função static check::name verifica se possui caracteres especiais no nome para transforma-la em url
        $this->Data['category_url']=Check::Url($this->Data['category_name']);
        //Valida a data no proprio indice a função static check::date converte a data informada para o formato timestamp
        $this->Data['category_date']=Check::Data($this->Data['category_date']);
        //verifica se a category_parent foi informada se sim recebe ela mesma se não recebe por padrão null
        $this->Data['category_parent'] = ($this->Data['category_parent'] == 'null' ? null : $this->Data['category_parent']);
    }

    /**
     * <b></b>Metodo responsável por verificar se o nome da categoria já foi cadastrado, caso a resposta seja verdadeira
     * o metodo ira reescrever o nome e passando o numero de vezes que o mesmo foi cadastrado na tabela exemplo: categoria-1
     */
    private function setName() {
        //Verifica se o id da categoria foi informado(alterar) ou se é um novo cadastro
        $Where = (!empty($this->CatId) ? "category_id != {$this->CatId} AND" : '');

        $readName = new Read();
        $readName->ExeRead(self::Entity, "WHERE {$Where}  category_url = :title", "title={$this->Data['category_url']}");
        //Se o nomeda categoria já for cadastrado, pega o nome e sobreescvre com o numero de resultados com o mesmo nome na tabela.
        if ($readName->getResult()):
            $this->Data['category_name'] = $this->Data['category_name'] . '-' . $readName->getRowCount();
        endif;
    }

    /**
     * <b>checkCats:</b>Metodo responsável por verifica se as categorias da seção, podem ser deletadas ou não 
     */
    private function checkCats() {
        $readSes = new Read();
        $readSes->ExeRead(self::Entity, "WHERE category_parent = :parent", "parent={$this->CatId}");
        //Se obter resultados não pode deletar a categoria pois a mesma pois subcategorias
        if ($readSes->getResult()):
            return false;
        else:
            //Se  não obter resultados  pode deletar pois a mesma não possui subcategorias    
            return true;
        endif;
        
    }

    /**
     * <b>checkPosts:</b> Metodo responsável por verifica artigos da categoria se podem ou não ser deletados
     * @param int $param
     */
    private function checkPosts() {
        $readPosts = new Read();
        $readPosts->ExeRead('blog_posts', "WHERE post_category = :categoryId OR post_cat_parent=:categoryId", "categoryId={$this->CatId}");
        //se tiver artigos não pode deletar a categoria
        if ($readPosts->getResult()):
            return false;
        //se não tiver artigos pode deletar a categoria
        else:
            return true;
        endif;
    }

    /**
     * <b>Create:</b> Metodo responsável realizar o cadastro das categorias para a tabela informada na constante : const Entity
     */
    private function Create() {

        $createCategory = new Create();
        $createCategory->ExeCreate(self::Entity, $this->Data);

        if ($createCategory->getResult()):
            $this->Result = $createCategory->getResult(); //pega o id do registro
            $this->Error = ["<b>Sucesso:</b> A categoria {$this->Data['category_name']} foi cadastrada com sucesso !", MSG_ACCEPT];
        endif;
    }

    /**
     * <b>Update:</b> Metodo responsável realizar atualização das categorias para a tabela informada na constante : const Entity
     */
    private function Update() {
        $updateCategory = new Update();
        $updateCategory->ExeUpdate(self::Entity, $this->Data, "WHERE category_id = :catid", "catid={$this->CatId}");
        if ($updateCategory->getResult()):
            $tipo = (empty($this->Data['category_parent']) ? 'seção' : 'categoria');
            $this->Result = true;
            $this->Error = ["<b>Sucesso:</b> A {$tipo} {$this->Data['category_name']} foi atualizada com sucesso !", MSG_ACCEPT];
        endif;
    }

}
