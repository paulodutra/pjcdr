<?php

/** Trabalhando com front controller que foi definido na index.php, 
  Não navegando entre as pastas de modo convencional */

$Dir= basename(getcwd());

if($Dir=='admin' || $Dir=='escolas'):

    require('../_app/Library/PHPMailer/class.phpmailer.php');
else:
    require('_app/Library/PHPMailer/class.phpmailer.php');

endif;


//require('../_app/Library/PHPMailer/class.phpmailer.php');
 

/**
 *  Email [MODEL]
 * Modelo responsável por configurar o PHPMailer, validar os dados e disparar os e-mails
 * @copyright (c) 2015, Paulo Henrique 
 */
class Email {

    /** @var PHPMailer */
    private $Mail;

    /** Email Data */
    private $Data;

    /** Corpo do E-mail */
    private $Assunto;
    private $Mensagem;

    /** Remetente */
    private $RemetenteNome;
    private $RemetenteEmail;

    /** Destinatário (Destino) */
    private $DestinoNome;
    private $DestinoEmail;

    /** Controle */
    private $Error;
    private $Result;

    function __construct() {
        $this->Mail = new PHPMailer();
        $this->Mail->Host = MAILHOST;
        $this->Mail->Port = MAILPORT;
        $this->Mail->Username = MAILUSER;
        $this->Mail->Password = MAILPASS;
        $this->Mail->CharSet = 'UTF-8';
        $this->Mail->SMTPSecure=MAILOPTION;
    }

    public function Enviar(array $Data) {
        $this->Data = $Data;
        $this->Clear();

        if (in_array('', $this->Data)):
            $this->Error = ['<b>Erro ao enviar:</b> Para enviar a mensagem, preecha todos os campos ', MSG_INFOR];
            $this->Result = false;
        elseif (!Check::Email($this->Data['RemetenteEmail'])):
            $this->Error = ['<b>Erro ao enviar:</b>E-mail informado não é valido ! Informe o seu e-mail', MSG_INFOR];
            $this->Result = false;
        else:
            $this->setMail();
            $this->Config();
            $this->sendMail();
        endif;
    }

    public function getResult() {
        return $this->Result;
    }

    public function getError() {
        return $this->Error;
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */
    private function Clear() {
        array_map('strip_tags', $this->Data);
        array_map('trim', $this->Data);
    }

    private function setMail() {
        $this->Assunto = $this->Data['Assunto'];
        $this->Mensagem = $this->Data['Mensagem'];
        $this->RemetenteNome = $this->Data['RemetenteNome'];
        $this->RemetenteEmail = $this->Data['RemetenteEmail'];
        $this->DestinoNome = $this->Data['DestinoNome'];
        $this->DestinoEmail = $this->Data['DestinoEmail'];

        $this->Data = null;
        $this->setMsg();
    }

    private function setMsg() {
        $this->Mensagem = "{$this->Mensagem}<hr><small></small> Recebida em: " . date('d/m/Y H:i:s') . "</small>";
    }

    private function Config() {
        //SMTP AUTH
        $this->Mail->isSMTP();
        $this->Mail->SMTPAuth=true;
        $this->Mail->isHTML();
        

        //REMETENTE E RETORNO
        $this->Mail->From = MAILUSER;
        $this->Mail->FromName = $this->RemetenteNome;
        $this->Mail->addReplyTo($this->RemetenteEmail, $this->RemetenteNome);

        //ASSUNTO, MENSAGEM E DESTINO
        $this->Mail->Subject = $this->Assunto;
        $this->Mail->Body = $this->Mensagem;
        $this->Mail->addAddress($this->DestinoEmail, $this->DestinoNome);
    }

    private function sendMail() {
        if ($this->Mail->send()):
            //$this->Error = ['<b>Sucesso ao enviar:</b> Obrigado por entrar em contato, estaremos respondendo em breve ! ', MSG_ACCEPT];
            $this->Result = true;
        else:
            $this->Error = ["<b>Erro ao enviar:</b> Entre em contato com o administrador.({$this->Mail->ErrorInfo})", MSG_ERROR];
            $this->Result = false;
        endif;
    }

}
