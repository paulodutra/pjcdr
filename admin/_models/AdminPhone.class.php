<?php
/**
 *  AdminSchool.class[MODEL ADMIN]
 * Classe responsável por administrar e manter o escolas de modo geral. 
 * @copyright (c) 2015, Paulo Henrique 
 */


class AdminPhone{
	private $Data;
	private $SchoolID;
	private $Telephone;
	private $Error;
	private $Result;

	
	/**Tabela no banco de dados*/
	const Entity = 'es_school_phone';

	/**
	 *<b>ExeCreate:</b> Método responsável por checar, validar o cadastro de telefones
	 * @param Array $Data
     */
	public function ExeCreate(array $Data){

		$this->Data=$Data;

		$this->SchoolID=$this->Data['phone_school'];
		$this->Telephone=$this->Data['phone_telephone'];
		
		$this->setData();
	

		if(in_array('', $this->Data)):

			$this->Result=false;
			$this->Error=["<b>Erro ao cadastrar:</b> Para cadastrar o cadastro de telefone(s), preencha todos os campos !", MSG_ERROR];	

		elseif($this->setPhone() || !$this->Result):
			$this->Result=false;
		else:
			
			$this->Create();
		endif;		



	}


	/**
	 *<b>ExeUpdate:</b> Método responsável por checar, validar a atualização de telefnes.
	 * @param int $SchoolID
	 * @param Array $Data
	*/
	public function ExeUpdate($SchoolID,array $Data){
		
		$this->$SchoolID=(int)$SchoolID;
		$this->Data=$Data;

		$this->SchoolID=$this->Data['phone_school'];
		$this->Telephone=$this->Data['phone_telephone'];

		$this->setData();

		if(in_array('', $this->Data)):

			$this->Result=false;
			$this->Error=["<b>Erro ao cadastrar:</b> Para cadastrar o cadastro de telefone(s), preencha todos os campos !", MSG_ERROR];	

		elseif($this->setPhone() || !$this->Result):
			$this->Result=false;
		else:
			$this->Update();
		endif;		



	}


	/**
	*<b>ExeDelete:</b>Método responsável por realizar a exclusão de cadastro de telefone
	*@param Int $Telephone
	*
	*/
	public function ExeDelete($Telephone){
		

		$readPhoneDelete= new Read();
		$readPhoneDelete->ExeRead(self::Entity,"WHERE phone_id=:id","id={$Telephone}");

		if(!$readPhoneDelete->getResult()):

			$this->Result=false;
			$this->Error=["<b>Erro ao deletar:</b> Você tentou excluir um telefone que não existe ou que já foi excluido antes !", MSG_ERROR];
		else:

			$deletePhone = new Delete();
			$deletePhone->ExeDelete(self::Entity, "WHERE phone_id=:id", "id={$Telephone}");	
			$this->Error=["<b>Sucesso ao deletar:</b> Telefone excluido com sucesso !", MSG_ACCEPT];		
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
	 *<b>setData:</b>Metodo responsável por tratar os dados, remover codigo html e espaços desnecessários.
     */

     private function setData(){
     	$this->Data= array_map('strip_tags', $this->Data);
     	$this->Data= array_map('trim', $this->Data);
     }


     /**
	 *<b>setPhone:</b>Método responsável por verificar se o telefone informado já não foi cadastrado anteriormente
     */
     
     private function setPhone(){
     	$Condition=(isset($this->SchoolID) ? "phone_school={$this->SchoolID} AND " : '' );
     	
     	$readPhone = new Read();
		$readPhone->ExeRead(self::Entity, "WHERE {$Condition} phone_telephone=:phone","phone={$this->Telephone}");

		if($readPhone->getRowCount()>=1):
			$this->Result=false;
			$this->Error=["<b>Erro ao cadastrar:</b> O telefone informado já foi cadastrado !", MSG_ERROR];	
		else:
			$this->Result = true;
		endif;	
     }


    /**<b>Create:</b> Método responsável por realizar o cadastro de telefone propriamente dito no banco de dados*/

     private function Create(){

     	$createPhone = new Create();
     	$createPhone->ExeCreate(self::Entity,$this->Data);

     	if($createPhone->getResult()):
     		$this->Result = true;
     		$this->Error=["<b>Sucesso ao cadastrar:</b> Telefone cadastrado com sucesso !",MSG_ACCEPT];

     	endif;	

     }

 /**<b>Update:</b> Método responsável por realizar o a atualização do cadastro de telefone propriamente dito no banco de dados*/

      private function Update(){

     	$updatePhone = new Update();
     	$updatePhone->ExeUpdate(self::Entity, $this->Data, "WHERE phone_id=:id","id={$this->SchoolID}");

     	if($updatePhone->getResult()):
    		$this->Error=["<b>Sucesso ao atualizar:</b> Telefone atualizado com sucesso",MSG_ACCEPT];
    		$this->Result=true;
    	endif;	
     }





















	
	
}








?>