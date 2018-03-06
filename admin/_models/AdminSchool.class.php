<?php

/**
 *  AdminSchool.class[MODEL ADMIN]
 * Classe responsável por administrar e manter o escolas de modo geral. 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminSchool {

    /** Atributos para manipulação no banco de dados */
    private $Data;
    private $SchoolCNPJ;
    private $SchoolID;
    private $SchoolPhone;
    private $Result;
    private $Error;

    /** Atributos para validação de CNPJ */
    private $CNPJ;
    private $NumberMultiplications;

    /*     * Tabela no banco de dados */

    const Entity = 'es_school';

    /**
     * <b>ExeCreate:</b> Método responsável por checar, validar o cadastro de escolas
     * @param Array $Data
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar:</b> Para cadastrar uma escola preencha todos os campos !", MSG_ALERT];
        elseif ($this->validateCNPJ() || !$this->Result):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar o CNPJ:</b> O CNPJ informado não é valido", MSG_ERROR];

        elseif (!Check::Email($this->Data['school_email']) || !Check::Email($this->Data['school_director_email'])):

            $this->Result = false;
            $this->Error = ["<b>Erro ao informar email:</b> O <b>email da escola</b> e/ou <b>email do diretor</b> não estão em um formato valido !", MSG_ERROR];

        elseif (!is_numeric($this->Data['school_inep'])):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Informe apenas número no campo do codigo do INEP", MSG_ERROR];

        elseif (isset($this->Data['school_cep']) && strlen($this->Data['school_cep']) < 8):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar CEP:</b> O CEP informado não está em um formato válido!", MSG_ERROR];


        else:
            $this->SchoolCNPJ = (isset($this->SchoolCNPJ) ? $this->SchoolCNPJ : $this->Data['school_cnpj']);
            $this->setData();
            if ($this->Result):
                $this->Create();
            endif;

        endif;
    }

    /**
     * <b>ExeUpdate:</b> Método responsável por checar, validar a atualização de escolas.
     * @param int $SchoolID
     * @param Array $Data
     */
    public function ExeUpdate($SchoolID, array $Data) {

        $this->SchoolID = (int) $SchoolID;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao Atualizar:</b> Para atualizar uma escola preencha todos os campos !", MSG_ALERT];
        elseif ($this->validateCNPJ() || !$this->Result):
            $this->Error = ["<b>Erro ao informar o CNPJ:</b> O CNPJ informado não é valido", MSG_ERROR];

        elseif (!Check::Email($this->Data['school_email']) || !Check::Email($this->Data['school_director_email'])):

            $this->Result = false;
            $this->Error = ["<b>Erro ao informar email:</b> O <b>email da escola</b> e/ou <b>email do diretor</b> não estão em um formato valido !", MSG_ERROR];

        elseif (isset($this->Data['school_cep']) && strlen($this->Data['school_cep']) < 8):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar CEP:</b> O CEP informado não está em um formato válido!", MSG_ERROR];

        else:
            $this->SchoolCNPJ = (isset($this->SchoolCNPJ) ? $this->SchoolCNPJ : $this->Data['school_cnpj']);
            $this->setData();
            if ($this->Result):
                $this->Update();
            endif;

        endif;
    }

    /**
     * <b>ExeDelete:</b>Método responsável por realizar a exclusão de cadastro de escolas
     * @param Int $SchoolID
     *
     */
    public function ExeDelete($SchoolID) {
        $this->SchoolID = $SchoolID;

        /* $readSchoolPhone = new Read();
          $readSchoolPhone->FullRead("SELECT * FROM es_school INNER JOIN es_school_phone ON school_id=phone_school INNER JOIN es_phone_type ON phone_type = type_id WHERE school_id=:id","id={$this->SchoolID}"); */

        $readSchool = new Read();
        $readSchool->ExeRead(self::Entity, "WHERE school_id=:id", "id={$this->SchoolID}");

        $readSchoolPhone = new Read();
        $readSchoolPhone->ExeRead('es_school_phone', "WHERE phone_school=:school", "school={$this->SchoolID}");

        if (!$readSchoolPhone->getResult()):
            MSGErro("<b>Erro ao deletar:</b> Você tentou excluir uma escola que não existe", MSG_ERROR);
        else:
            $deleteSchool = new Delete();
            $deleteSchool->ExeDelete("es_school", "WHERE school_id=:id", "id={$this->SchoolID}");
        endif;

        if ($readSchoolPhone->getResult()):
            $deleteSchoolPhone = new Delete();
            $deleteSchoolPhone->ExeDelete('es_school_phone', "WHERE phone_school=:school", "school={$this->SchoolID}");
        endif;

        MSGErro("<b>Sucesso ao deletar:</b> O cadastro da escola foi excluida com sucesso !", MSG_ACCEPT);
    }

    /**
     * <b>ExeStatus:</b>Metodo responsável por atualizar o status da escola  para ativo ou inativo de acordo com a 
     * escolha do usuário
     * 
     * @param int $SchoolID
     * @param string $SchoolStatus
     */
    public function ExeStatus($SchoolID, $SchoolStatus) {
        $this->SchoolID = (int) $SchoolID;
        $this->Data['school_status'] = (string) $SchoolStatus;

        $updateSchool = new Update();
        $updateSchool->ExeUpdate(self::Entity, $this->Data, "WHERE school_id=:id", "id={$this->SchoolID}");
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
     * <b>validateCNPJ:</b> Metódo responsável por realizar validações checar o campo de cnpj informado pela escola.
     * Verifica se o CNPJ possui pelo menos 14 digitos(sem mascara), o mesmo utiliza o metodo calculateCNPJ (metodo que realiza as multiplicações e soma dos digitos)
     * Após o metódo calculateCNPJ retornar o total da soma das multplicações dos digitos o restante da validação é realiza pelo metodo validateCNPJ	
     *
     */
    private function validateCNPJ() {

        if (isset($this->Data['school_cnpj']) && !strlen($this->Data['school_cnpj']) >= 14):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar CNPJ:</b>CNPJ invalido. O CNPJ informado possui menos que 14 caracteres!", MSG_ERROR];

        else:
            $cnpjReceiveid = preg_replace('/[^0-9]/', '', $this->Data['school_cnpj']);

            $cnpjReceiveid = (string) $cnpjReceiveid;

            $cnpjValidate = $cnpjReceiveid;

            /** Pega os 12 digitos do cpnj e validação do primeiro digito verificador */
            $firstDigitCNPJ = substr($cnpjValidate, 0, 12);

            $firstDigitCheck = $this->calculateCNPJ($firstDigitCNPJ, 5);
            $firstDigitCheck = ($firstDigitCheck % 11) < 2 ? 0 : 11 - ($firstDigitCheck % 11);

            /** concatena os 12  digitos com o primeiro digito verificador encontrado */
            $this->CNPJ.= $firstDigitCheck;

            /** Validação do segundo digito verificador */
            $secondDigitCNPJ = $this->calculateCNPJ($this->CNPJ, 6);

            $secondDigitCheck = ($secondDigitCNPJ % 11) < 2 ? 0 : 11 - ($secondDigitCNPJ % 11);


            /** concatena os 13  digitos com o segundo digito verificador encontrado */
            $this->CNPJ.=$secondDigitCheck;


            /** Verificando o CNPJ recebido com o CNPJ validado */
            if ($this->CNPJ == $cnpjReceiveid):
                $this->Result = true;

            else:
                $this->Result = false;
                $this->Error = ["<b>Erro ao informar o CNPJ:</b> O CNPJ informado não é valido", MSG_ERROR];

            endif;



        endif;
    }

    /**
     * <b>calculateCNPJ</b> Método responsável por realizar as multiplicações e soma dos digitos e retornar o valor da soma
     * @param $CNPJ
     * @param NumberMultiplications (Número de multiplicações a serem realizadas)
     */
    private function calculateCNPJ($CNPJ, $NumberMultiplications) {
        $this->CNPJ = (string) $CNPJ;
        $this->NumberMultiplications = (int) $NumberMultiplications;

        $total = 0;

        for ($i = 0; $i < strlen($this->CNPJ); $i++):

            $total = $total + ($this->CNPJ[$i] * $this->NumberMultiplications);

            $this->NumberMultiplications--;

            if ($this->NumberMultiplications < 2):

                $this->NumberMultiplications = 9;

            endif;

        endfor;


        return $total;
    }

    /*     * <b>setData:</b>Metodo responsável por tratar os dados, remover codigo html e espaços desnecessários, validar a data para timestamp antes de cadastrar */

    private function setData() {



        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);


        $this->setName();
        /*         * Convertendo a data para o formato timestamp */
        $this->Data['school_date_registration'] = date('Y-m-d H:i:s');
    }

    /*     * <b>setName:</b>Método responsável por verificar se a escola já foi cadastrada, caso não tenha sido cadastrado o mesmo cria a url da escola */

    private function setName() {
        $Condition = (isset($this->SchoolID) ? "school_id !={$this->SchoolID} AND" : '' );

        $readName = new Read();
        $readName->ExeRead(self::Entity, "WHERE {$Condition} school_cnpj=:cnpj OR school_inep=:inep", "cnpj={$this->Data['school_cnpj']}&inep={$this->Data['school_inep']}");


        if (!$readName->getResult() || $readName->getResult()[0]['school_id'] == $this->SchoolID):
            $CNPJ = preg_replace('/[^0-9]/', '', $this->Data['school_cnpj']);
            $this->Data['school_url'] = $this->Data['school_name'] . '-' . $CNPJ;
            $this->Data['school_url'] = Check::Url($this->Data['school_url']);
            $this->Result = true;
        else:
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar:</b> CNPJ e/ou INEP já cadastrado, caso você não tenha cadastrado entre em contato com o email: " . MAILUSER, MSG_ERROR];

        endif;
    }

    /*     * <b>Create:</b> Método responsável por realizar o cadastro da escola propriamente dito no banco de dados */

    private function Create() {

        $createSchool = new Create();
        $createSchool->ExeCreate(self::Entity, $this->Data);

        if ($createSchool->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Sucesso ao cadastrar:</b> Escola cadastrada com sucesso!", MSG_ACCEPT];
            $this->Result = $createSchool->getResult();

        endif;
    }

    /**
     * <b>Update:</b> Método responsável por realizar a atualização propriamente dita no banco de dados 
     * 	
     */
    private function Update() {

        $updateSchool = new Update();
        $updateSchool->ExeUpdate(self::Entity, $this->Data, "WHERE school_id=:schoolid", "schoolid={$this->SchoolID}");

        if ($updateSchool->getResult()):
            $this->Error = ["<b>Sucesso ao atualizar:</b> O {$this->Data['school_name']} foi atualizado com sucesso", MSG_ACCEPT];
            $this->Result = true;
        endif;
    }

}
?>





