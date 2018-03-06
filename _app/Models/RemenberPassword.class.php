<?php

/**
 *  RemenberPassword.class[MODEL]
 * 
 * Classe Responsável por apoiar o processo de Recuperar a Senha.
 * @copyright (c) 2016, Paulo Henrique 
 */
class RemenberPassword {

    private $Data;
    private $RemenberToken;
    private $User;
    private $Email;
    private $Error;
    private $Result;

    /** Tabela no banco de dados */
    const Entity = 'sys_user_remenber';

    /**
     * <b>ExeCreate:</b> Método responsável por executar validar e delegar aos metodos a solicitação de recuperação
     * de senha.
     */
    public function ExeCreate(array $Data) {

        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao recuperar senha:</b> Para recuperar a senha preencha todos os campos"];
        else:
            $this->searchEmail();

            if ($this->Result):
                $this->Create();
            endif;



        endif;
    }

    /**
     * <b>ExeUpdate:</b> Método responsável por executar e validar a solicitação de recuperação de senha. 
     * Valida a senha e a confirmação de senha informada pelo usuário, criptografa a mesma e delega aos demais metodos 
     * o restante do processo
     */
    public function ExeUpdate($RemenberToken, array $Data) {
        $this->RemenberToken = $RemenberToken;
        $this->Data = $Data;

        if (strlen($this->Data['user_password']) < 6 || strlen($this->Data['user_password']) > 12):
            $this->Error = ["A senha deve ter entre 6 a 12 caracteres", MSG_ALERT];
            $this->Result = false;
        elseif (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao recuperar senha:</b> Para recuperar a senha preencha todos os campos",MSG_ERROR];
        elseif ($this->Data['user_password'] != $this->Data['user_re_password']):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar senha:</b> A senha e as confirmações de senha estão diferentes",MSG_ERROR];
        else:
            $this->Data['user_password'] = md5($this->Data['user_password']);

            unset($this->Data['user_re_password']);
            $readRemenber = new Read();
            $readRemenber->ExeRead(self::Entity, "WHERE remenber_token=:token", "token={$this->RemenberToken}");

            if($readRemenber->getResult()):
                $this->User=$readRemenber->getResult()[0]['remenber_user'];
                $this->Data['remenber_date_limit']=$readRemenber->getResult()[0]['remenber_date_limit'];

                if($readRemenber->getResult()[0]['remenber_status']!=1):
                    $this->searchDeadLine();
                else:
                   $this->Result = false;
                   $this->Error = ["<b>Você já alterou a sua senha utilizando esta solicitação:</b> Caso não recorde sua senha, faça outra solicitação de recuperação de senha !",MSG_ERROR]; 
                endif;
            else:
                    
                    $this->Result = false;
                   $this->Error = ["<b>Aviso:</b> Solicitação de alteração de senha não encontrada!",MSG_ERROR]; 
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
     * <b>setEmail:</b>Método responsável por verificar se o email informado pelo usuário para a recuperação de senha
     * esta cadastro como usuário no sistema. 
     */
    private function searchEmail() {

        $readEmail = new Read();
        $readEmail->ExeRead('sys_user', "WHERE user_email=:email", "email={$this->Data['remenber_email']}");

        if ($readEmail->getResult()):
            $this->Data['remenber_user'] = $readEmail->getResult()[0]['user_id'];
            $this->Data['remenber_token'] = md5($readEmail->getResult()[0]['user_email'] . time());
            $this->User = $readEmail->getResult()[0]['user_name'];

            $this->Result = true;
            $this->setTime();
        else:
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar o usuário:</b> Usuário informado não está cadastrado", MSG_ERROR];
        endif;
    }

    /**
     * <b>setTime:</b>Método responsável por setar o tempo limite, que o usuário tera,
     *  para alterar a senha apartir da solicitação. Seta também a data e hora da solicitação.
     * Obs: O tempo limite que o usuário terá para alterar a senha será de 3 horas.  
     */
    private function setTime() {

        $date_registration = new DateTime('NOW');
        /*         * Transformando um objeto em um array */
        $date_registration = (array) $date_registration;
        /*         * Sobreescrevendo e pegando o indice com a data */
        $date_registration = $date_registration['date'];


        $date_limit = new DateTime('+3 hour');
        /*         * Transformando um objeto em um array */
        $date_limit = (array) $date_limit;
        /*         * Sobreescrevendo e pegando o indice com a data */
        $date_limit = $date_limit['date'];


        $this->Data['remenber_date_registration'] = $date_registration;
        $this->Data['remenber_date_limit'] = $date_limit;
    }

    /**
     * <b>searchDeadLine:</b>Método responsável por verificar se alteração de senha do usuário esta sendo 
     * dentro do prazo da solicitação.
     */
    private function searchDeadLine() {

        $date_now = new DateTime('NOW');
        /*         * Transformando um objeto em um array */
        $date_now = (array) $date_now;
        /*         * Sobreescrevendo e pegando o indice com a data */
        $date_now = $date_now['date'];

        $this->Data['remenber_date_limit'] = date('Y-m-d H:i:s', strtotime($this->Data['remenber_date_limit']));
        $date_now = date('Y-m-d H:i:s', strtotime($date_now));

        if ($date_now > $this->Data['remenber_date_limit']):
            $this->Result = false;
            $this->Error = ["<b>Erro ao alterar a senha:</b>O Periodo para alterar a senha é de 3 horas, o seu periodo expirou.Faça outra solicitação para alterar a sua senha ", MSG_ACCEPT];
        else:
            unset($this->Data['remenber_date_limit']);
            $this->Data['remenber_status'] = 1;
            //var_dump($this->Data);
            $this->Update();
        endif;
    }

    /** <b>Create:</b> Método responsável por realizar o cadastro da solicitação de 
     * recuperação de senha propriamente dito no banco de dados ,
     */
    private function Create() {
        $createRemenber = new Create();
        $createRemenber->ExeCreate(self::Entity, $this->Data);

        if ($createRemenber->getResult()):
            $this->sendEmail();
            $this->Result = true;
            $this->Error = ["<b>Email de redefinição enviado :</b> ", MSG_ACCEPT];

        endif;
    }

    /** <b>Update:</b> Método responsável por realizar a atualização da solicitação de 
     * recuperação de senha propriamente dito no banco de dados e também a atualização da senha do usuário.
     */
    private function Update() {
        $password = $this->Data['user_password'];
        unset($this->Data['user_password']);
        $updateRemenber = new Update();
        $updateRemenber->ExeUpdate(self::Entity, $this->Data, "WHERE remenber_token=:token", "token={$this->RemenberToken}");

        if ($updateRemenber->getResult()):
            unset($this->Data['remenber_status']);
            $this->Data['user_password'] = $password;
            $updatePassword = new Update();
            $updatePassword->ExeUpdate('sys_user', $this->Data, "WHERE user_id=:id", "id={$this->User}");

            if ($updatePassword->getResult()):
                $this->Result = true;
                $this->Error = ["<b>Sucesso ao alterar a senha :</b> Senha alterada com sucesso! ", MSG_ACCEPT];
            endif;
        else:
            $this->Result = false;
            $this->Error = ["<b>Erro ao alterar a senha :</b> A senha não foi  alterada ! Faça outra solicitação, caso o erro persista entre em contato com o Administrador ! ", MSG_ACCEPT];
        endif;
    }
    
    /**
     *<b>sendEmail:</b>Método responsável por enviar o email para o usuário. No email contém o endereço do formulário
     * que será utilizado para alterar a senha
     */

    private function sendEmail() {

        $link = HOME . '/admin/redefinir-senha.php?token=' . $this->Data['remenber_token'];

        $this->Email = (array) $this->Email;

        $this->Email['Assunto'] = 'Redefinir senha usuário: ' . $this->Data['remenber_email'];

        $this->Email['Mensagem'] = "Prezado(a) <b>{$this->User}</b>, clique no link a seguir para redefinir a sua senha:<br> 
        {$link} <br> Atenciosamente, <br> DPU NAS ESCOLAS. ";

        $this->Email['RemetenteNome'] = 'Sistema DPU NAS ESCOLAS';

        $this->Email['RemetenteEmail'] = MAILUSER;

        $this->Email['DestinoEmail'] = $this->Data['remenber_email'];

        $this->Email['DestinoNome'] = $this->User;

        $sendMail = new Email();
        $sendMail->Enviar($this->Email);

        if ($sendMail->getError()):
            MSGErro($sendMail->getError()[0], $sendMail->getError()[1]);

        else:
            MSGErro($sendMail->getError()[0], $sendMail->getError()[1]);
        endif;
    }

}
