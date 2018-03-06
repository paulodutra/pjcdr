<?php

/**
 *  FirstAccess.class[MODEL]
 * 
 * Classe Responsável por apoiar o processo 1º Acesso das escolas cadastradas..
 * @copyright (c) 2016, Paulo Henrique 
 */
Class FirstAccess {

    private $Data;
    private $AccessToken;
    private $School;
    private $Email;
    private $Error;
    private $Result;
    
    /** Tabela no banco de dados */
    const Entity = 'es_school_first_access';

    /**
     * <b>ExeCreate:</b>Método responsável por executar validar e delegar aos metodos a solicitação de 1º acesso
     * 
     * @param array $Data
     */
    
    public function ExeCreate(array $Data) {

        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao realizar primeiro acesso:</b> Para realizar primeiro acesso, preencha todos os campos"];
        else:

            $this->Data['access_school_date_registration'] = date('Y-m-d H:i:s');
            $this->searchINEP();

            if ($this->Result):
                $this->sendEmail();
                $this->Create();
            endif;

        endif;
    }
    
    /**
     * <b>ExeUpdate:</b>Método responsável por executar e validar a solicitação de 1º acesso. 
     * Recebe um token que foi gerado na solicitação (ExeCreate) e os dados necessários para a realização do mesmo ($this->Data).
     * Delega para o método de validação de CNPJ e o metodo de validação de token e de verificação de prazo do token.
     * @param type $AccessToken
     * @param array $Data
     */

    public function ExeUpdate($AccessToken, array $Data) {

        $this->AccessToken = $AccessToken;
        $this->Data = $Data;

        if (in_array('', $this->Data)):

            $this->Result = false;
            $this->Error = ["<b>Erro ao realizar primeiro acesso:</b> Para realizar primeiro acesso, preencha todos os campos"];

        elseif ($this->validateCNPJ() || !$this->Result):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar o CNPJ:</b> O CNPJ informado não é valido", MSG_ERROR];

        else:

            $this->verifyToken();

            if ($this->Result):
                $this->searchDeadLine();
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
     * <b>searchINEP:</b>Método responsável por verificar se o codigo inep informado pelo usuário para a solicitação de 1º acesso
     * esta cadastro no sistema. 
     */
    
    private function searchINEP() {

        $readINEP = new Read();
        $readINEP->ExeRead('es_school', "WHERE school_cnpj='' AND school_inep=:inep ORDER BY `school_cnpj` IS NULL", "inep={$this->Data['access_school_inep']}");
        //Fazer o restante
        if ($readINEP->getResult()):
            $this->Data['access_school_user'] = $readINEP->getResult()[0]['school_id'];
            $this->Data['access_school_token'] = md5($readINEP->getResult()[0]['school_email'] . time());
            $this->Data['access_school_email'] = $readINEP->getResult()[0]['school_email'];
            $this->School = $readINEP->getResult()[0]['school_name'];
            $this->Result = true;
            $this->setTime();
        else:
            $this->Result = false;
            $this->Error = ["<b>Erro ao solicitar 1 acesso:</b> O codigo INEP informado n�o foi localizado ou o processo de primeiro acesso j� foi realizado pela escola.", MSG_ERROR];
        endif;
    }

    /**
     * <b>setTime:</b>Método responsável por setar o tempo limite, que o usuário tera,
     *  para informar o CNPJ para realizar o 1º acesso, apartir da solicitação. Seta também a data e hora da solicitação.
     * Obs: O tempo limite que o usuário terá para informar o CNPJ, será de 3 horas.  
     */
    private function setTime() {

        $date_registration = new DateTime('NOW');
        /** Transformando um objeto em um array */
        $date_registration = (array) $date_registration;
        /** Sobreescrevendo e pegando o indice com a data */
        $date_registration = $date_registration['date'];

        $date_limit = new DateTime('+3 hour');

        /** Transformando um objeto em um array */
        $date_limit = (array) $date_limit;
        /** Sobreescrevendo e pegando o indice com a data */
        $date_limit = $date_limit['date'];

        $this->Data['access_school_date_registration'] = $date_registration;
        $this->Data['access_school_date_limit'] = $date_limit;
    }

    
    /**
     *<b>verifyToken:</b>Método responsável por verificar se o token da solicitação de 1º acesso exista. Caso exista verifica se a 
     * solicitação não foi utilizada para a realização do mesmo.
     * 
     */
    private function verifyToken() {

        $readToken = new Read();
        $readToken->ExeRead(self::Entity, "WHERE access_school_token=:token", "token={$this->AccessToken}");

        if ($readToken->getResult()):

            $this->School = $readToken->getResult()[0]['access_school_user'];
            $this->Data['access_school_date_limit'] = $readToken->getResult()[0]['access_school_date_limit'];
            if ($readToken->getResult()[0]['access_school_status'] != 1):
                $this->Result = true;
            else:
                $this->Result = false;
                $this->Error = ["<b>A Escola já realizou o processo de 1º acesso:</b> Caso não recorde dos dados necessários para realizar o acesso, vá na <b>�?rea da Escola</b> clique em esqueci minha senha!", MSG_ERROR];
            endif;

        else:
            $this->Result = false;
            $this->Error = ["<b>Aviso:</b> Solicitação de <b>1º acesso</b> não encontrada!", MSG_ERROR];

        endif;
    }

    
    /**
     * <b>searchDeadLine:</b>Método responsável por verificar se o CNPJ informado para a realização do 1º acesso da escola esta sendo 
     * dentro do prazo da solicitação.
     */
    private function searchDeadLine() {

        $date_now = new DateTime('NOW');

        /** Transformando um objeto em um array */
        $date_now = (array) $date_now;
        /*         * Sobreescrevendo e pegando o indice com a data */
        $date_now = $date_now['date'];

        $date_now = date('Y-m-d H:i:s', strtotime($date_now));

        $this->Data['access_school_date_limit'] = date('Y-m-d H:i:s', strtotime($this->Data['access_school_date_limit']));

        if ($date_now > $this->Data['access_school_date_limit']):
            $this->Result = false;
            $this->Error = ["<b>Erro ao realizar 1º acesso:</b>O Periodo para alterar a realizar o 1º acesso é de 3 horas, o seu periodo expirou.Faça outra solicitação de 1º acesso ", MSG_ALERT];

        else:

            unset($this->Data['access_school_date_limit']);
            $this->Data['access_school_status'] = 1;
            $this->Update();


        endif;
    }

    /**
     * <b>validateCNPJ:</b> Metódo responsável por realizar validações checar o campo de cnpj informado pela escola.
     * Verifica se o CNPJ possui pelo menos 14 digitos(sem mascara), o mesmo utiliza o metodo calculateCNPJ (metodo que realiza as multiplicações e soma dos digitos)
     * Após o metódo calculateCNPJ retornar o total da soma das multplicações dos digitos o restante da validação é realiza pelo metodo validateCNPJ	
     *
     */
    private function validateCNPJ() {

        if (isset($this->Data['school_cnpj']) && !strlen($this->Data['school_cnpj']) >= 14):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar CNPJ:</b>CNPJ invalido. O CNPJ informado possui menos que 14 números!", MSG_ERROR];

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

    /**
     *<b>Create:</b> Método responsável por realizar o cadastro da solicitação de 
     * 1º acesso, propriamente dito no banco de dados ,
     */
    private function Create() {

        $createFirstAccess = new Create();
        $createFirstAccess->ExeCreate(self::Entity, $this->Data);

        if ($createFirstAccess->getResult()):
            $this->Result = true;
            $this->Error = ["Parabéns <b>{$this->School}!</b> <br>Foi enviada uma mensagem para o e-mail: <b> {$this->Data['access_school_email']} </b>. Neste e-mail, conterá uma breve instrução e um link para o primeiro acesso. <br/>Após acessar o link, você deverá informar o CNPJ da escola para o acesso e clicar em Enviar.", MSG_ACCEPT];
        endif;
    }

    /**
     * <b>Update:</b> Método responsável por realizar a atualização da solicitação de 
     * 1º acesso, propriamente dito no banco de dados e também a atualização do CNPJ da escola np cadastro.
     */
    private function Update() {
        $CNPJ = $this->Data['school_cnpj'];
        unset($this->Data['school_cnpj']);

        $updateFirstAccess = new Update();
        $updateFirstAccess->ExeUpdate(self::Entity, $this->Data, "WHERE access_school_token=:token", "token={$this->AccessToken}");

        if ($updateFirstAccess->getResult()):

            unset($this->Data['access_school_status']);
            $this->Data['school_cnpj'] = $CNPJ;

            $updateCNPJ = new Update();
            $updateCNPJ->ExeUpdate('es_school', $this->Data, "WHERE school_id=:id", "id={$this->School}");

            if ($updateCNPJ->getResult()):
                $this->Result = true;
                $this->Error = ["<b>Sucesso para o 1º acesso:</b> Agora basta ir <b>�?rea da Escola</b> e efetuar o login! ", MSG_ACCEPT];
            endif;

        else:
            $this->Result = false;
            $this->Error = ["<b>Erro no processo de 1º Acesso :</b> Erro no processo de 1º Acesso ! Faça outra solicitação, caso o erro persista entre em contato com o Administrador ! ", MSG_ACCEPT];

        endif;
    }

    /**
     * <b>sendEmail:</b> Método responsável por enviar o email para o usuário. No email contém o endereço do formulário
     * que será utilizado informar o CNPJ, para a realização do 1º acesso.
     */
    private function sendEmail() {

        $link = HOME . '/escolas/primeiro-acesso.php?token=' . $this->Data['access_school_token'];

        $this->Email = (array) $this->Email;

        $this->Email['Assunto'] = 'Primeiro Acesso: ' . $this->School;

        $this->Email['Mensagem'] = "Prezado(a) <b>{$this->School}</b>, clique no link a seguir para iniciar o processo de 1º acesso ao sistema:<br> 
        {$link} <br> Atenciosamente, <br> DPU NAS ESCOLAS. ";

        $this->Email['RemetenteNome'] = 'Sistema DPU NAS ESCOLAS';

        $this->Email['RemetenteEmail'] = MAILUSER;

        $this->Email['DestinoEmail'] = $this->Data['access_school_email'];

        $this->Email['DestinoNome'] = $this->School;

        $sendMail = new Email();
        $sendMail->Enviar($this->Email);

        if ($sendMail->getError()):
            MSGErro($sendMail->getError()[0], $sendMail->getError()[1]);

        else:
            MSGErro($sendMail->getError()[0], $sendMail->getError()[1]);
        endif;

        unset($this->Data['access_school_inep']);
    }

}
