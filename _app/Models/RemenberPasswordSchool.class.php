<?php

/**
 *  RemenberPasswordSchool.class[MODEL]
 * 
 * Classe Responsável por apoiar o processo de Recuperar a Senha do painel administrativo 
 * das escolas participantes do concurso de redação.
 * @copyright (c) 2016, Paulo Henrique 
 */
class RemenberPasswordSchool {

    private $Data;
    private $Email;
    private $School;
    private $CNPJ;
    private $INEP;
    private $Error;
    private $Result;

    /** Tabela no banco de dados */
    const Entity = 'es_school_remenber';

    public function ExeCreate(array $Data) {
        $this->Data=$Data;
        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao recuperar senha:</b> Para recuperar a senha preencha todos os campos"];
        else:
            $this->Data['remenber_school_date_registration'] = date('Y-m-d H:i:s');
            $this->searchCNPJ();

            if ($this->Result):
                $this->sendEmail();
                $this->Create();
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
    private function searchCNPJ() {
        $readCNPJ = new Read();
        $readCNPJ->ExeRead('es_school', "WHERE school_cnpj=:cnpj", "cnpj={$this->Data['remenber_school_cnpj']}");

        if ($readCNPJ->getResult()):
            $this->Data['remenber_school_user'] = $readCNPJ->getResult()[0]['school_id'];
            $this->Data['remenber_school_email']=$readCNPJ->getResult()[0]['school_email'];
            $this->School= $readCNPJ->getResult()[0]['school_name'];
            $this->CNPJ=$readCNPJ->getResult()[0]['school_cnpj'];
            $this->INEP=$readCNPJ->getResult()[0]['school_inep'];
            $this->Data['remenber_school_token'] = md5($this->CNPJ . time());
            $this->Result = true;
        else:
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar o email:</b> Usuário informado não está cadastrado", MSG_ERROR];
        endif;
    }

    private function sendEmail() {
        $this->Email = (array) $this->Email;

        $this->Email['Assunto'] = 'Dados de login: ' . $this->School;

        $this->Email['Mensagem'] = "Prezado(a) Diretor(a) ou responsável escolar: <br> Para efetuar login no sistema, utilize os dados abaixo:<br> "
                . "<b>CNPJ:</b> $this->CNPJ <br>"
                . "<b>INEP</b> $this->INEP <br>"
                . "<b>OBS:</b> Caso não consiga acessar entre em contato conosco.<br>"
                . "Atenciosamente<br>"
                . "DPU NAS ESCOLAS.";

        $this->Email['RemetenteNome'] = 'Sistema DPU NAS ESCOLAS';

        $this->Email['RemetenteEmail'] = MAILUSER;

        $this->Email['DestinoEmail'] = $this->Data['remenber_school_email'];

        $this->Email['DestinoNome'] = $this->School;

        $sendMail = new Email();
        $sendMail->Enviar($this->Email);

        if ($sendMail):
            $this->Data['remenber_school_status']=1;
            MSGErro($sendMail->getError()[0], $sendMail->getError()[1]);

        else:
            MSGErro($sendMail->getError()[0], $sendMail->getError()[1]);
        endif;
        
        $this->Data['remenber_school_date_email']=  date('Y-m-d H:i:s');
        unset($this->Data['remenber_school_cnpj']);
    }

    private function Create() {
        $createRemenber = new Create();
        $createRemenber->ExeCreate(self::Entity, $this->Data);

        if ($createRemenber->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Email enviado :</b>Foi enviado um e-mail com os dados ! ", MSG_ACCEPT];

        endif;
    }

}

?>