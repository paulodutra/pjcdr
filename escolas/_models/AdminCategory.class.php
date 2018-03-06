<?php

/**
 *  AdminCategory.class[MODEL ADMIN]
 * Classe responsável por administrar as categorias 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminCategory {

    private $Data;
    private $CategoryID;
    private $Error;
    private $Result;
    
    /**Tabela no banco de dados*/
    const Entity = 'cs_category';

    /**
	*<b>ExeCreate:</b> Método responsável por checar, validar o cadastro de categorias
	* @param Array $Data
	*
    */
    public function ExeCreate(array $Data){

    	$this->Data=$Data;
    	
    	if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para cadastrar uma categoria preencha todos os campos !", MSG_ALERT];
        
        elseif($this->setCategory() || !$this->Result):
        	$this->Result=false;
        else:
            $this->setData();
            $this->setUrl();
            $this->Create();
        endif;


    }

    /**
	 *<b>ExeUpdate:</b> Método responsável por checar, validar a atualização de categorias.
	 * @param int $CategoryID
	 * @param Array $Data
	*/
    public function ExeUpdate($CategoryID,array $Data){
    	
    	$this->CategoryID =(int) $CategoryID;
    	$this->Data=$Data;
    	
    	if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para cadastrar uma categoria preencha todos os campos !", MSG_ALERT];
            
        elseif($this->setCategory() || !$this->Result):
            $this->Result=false;    
        else:
            $this->setData();
            $this->setUrl();
            $this->Update();
        endif;


    }

	/**
	*<b>ExeDelete:</b>Método responsável por realizar a exclusão de cadastro de categorias
	*@param $CategoryID
	*
	*/
    public function ExeDelete($CategoryID) {

        $this->CategoryID = (int) $CategoryID;
        $readDelete = new Read();
        $readDelete->ExeRead(self::Entity, "WHERE category_id= :deleteid", "deleteid={$this->CategoryID}");
        if (!$readDelete->getResult()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao deletar:</b> Você tentou deletar uma categoria que não existe!", MSG_ERROR];
        else:
        	$this->Delete();
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
     *<b>setData:</b>Metodo responsável por tratar os dados, remover codigo html e espaços desnecessários, validar a url antes de realizar o cadastro
     */	

     private function setData() {

        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);

        $this->Data['category_url']=Check::Url($this->Data['category_name']);
        $this->Data['category_date_registration'] = date('Y/m/d H:i:s');

       
    }

    /**
	*<b>setCategory:</b> Método responsável por verificar se a categoria informada não foi cadastrada antes
    */

    private function setCategory(){

    	$Condition=(isset($this->CategoryID) ? "category_id !={$this->CategoryID} AND " : '');

    	$readCategory= new Read();
    	$readCategory->ExeRead(self::Entity,"WHERE category_name=:category","category={$this->Data['category_name']}");

    	if($readCategory->getRowCount()>=1):
    		$this->Result=false;
			$this->Error=["<b>Erro ao cadastrar:</b> A categoria informada já foi cadastrado !", MSG_ERROR];
		else:		
			$this->Result=true;
    	endif;	


    }

    /**
	*<b>setUrl:</b> Método responsável por validar a url da categoria, caso a mesma exista ela irá ser renomeada
	*
    */
      private function setUrl() {

        $Condition = (!empty($this->CategoryID) ? "category_id !={$this->CategoryID} AND" : '');


        $readUrl = new Read();
        $readUrl->ExeRead(self::Entity, "WHERE {$Condition}  category_name=:categoryname", "categoryname={$this->Data['category_name']}");

        if ($readUrl->getResult()):
            $this->Data['category_url'] = $this->Data['category_url'] . '-' . $readUrl->getRowCount();
        endif;
    }

    /*
	*<b>Create:</b> Método responsável por realizar o cadastro propriamente dito no banco de dados
	*/
    

    private function Create() {

        $createCategory = new Create();
        $createCategory->ExeCreate(self::Entity, $this->Data);

        if ($createCategory->getResult()):
            $this->Result = $createCategory->getResult(); //obtem o id do registro
            $this->Error = ["<b>Sucesso ao cadastrar:</b> A categoria <b>{$this->Data['category_name']}</b> foi cadastrada com sucesso !", MSG_ACCEPT];
        endif;
    }

	/*
	*<b>Update:</b> Método responsável por realizar a atualização  propriamente dito no banco de dados
	*/
       private function Update() {
        $updateCategory = new Update();
        $updateCategory->ExeUpdate(self::Entity, $this->Data, "WHERE category_id= :categoryid", "categoryid={$this->CategoryID}");

        if ($updateCategory->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Sucesso ao atualizar: </b> A <b>{$this->Data['category_name']}</b> foi atualizada com sucesso !", MSG_ACCEPT];
        endif;
    }

    /*
	*<b>Update:</b> Método responsável por realizar a exclusão  propriamente dito no banco de dados
	*/
     private function Delete() {
        $deleteCategory = new Delete();
        $deleteCategory->ExeDelete(self::Entity, "WHERE category_id=:categoryid", "categoryid={$this->CategoryID}");

        if ($deleteCategory->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Sucesso a deletar:</b> A Categoria foi removida com sucesso!", MSG_ACCEPT];
        endif;
    }







}
?>