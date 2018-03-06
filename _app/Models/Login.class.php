<?php

/**
 *  Login.class[MODEL]
 * 
 * Responsável por autenticar , validar e checar usuários do sistema de login
 * @copyright (c) 2015, Paulo Henrique 
 */
class Login {

    private $Level;
    private $Email;
    private $Senha;
    private $Error;
    private $Result;

    /**
     * <b>__construct:</b>Metodo construtor da classe, o unico parametro que deve ser informado é o level de acesso
     * (nível de acesso)
     * @param int $Level
     */
    function __construct($Level) {
        $this->Level = (int) $Level;
    
    }

    /**
     * <b>ExeLogin:</b>Metodo responsável por executar o login, atribuindo os valores informados no formulário para os indices do array
     * lembrando que o nome dos indices são os mesmos dos names do inputs e executa o metodo setLogin();
     * @param array $UserData
     */
    public function ExeLogin(array $UserData) {
        $this->Email = (string) strip_tags(trim( $UserData['user'])); //o indice e o name do input html do formulario
        $this->Senha = (string) strip_tags(trim($UserData['pass'])); //o indice e o name do input html do formulario
        $this->setLogin();
    }

    /**
     * <b>getResult:</b> Metodo responsável por retornar o resultado da operação
     * @return bool Result
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>getError:</b>Metodo responsável por retornar a mensagem da operação
     * @return array Error
     */
    public function getError() {
        return $this->Error;
    }
    /**
     * <b>checkLogin:</b>Metodo responsável por verificar a sessão userlogin e revalidar o acesso para proteger telas restritias
     *retorna true ou mata a sessão e retorna false;
     *  @return bool login 
     */
    public function checkLogin() {
        if(empty($_SESSION['userlogin']) || $_SESSION['userlogin']['user_level'] < $this->Level):
            unset($_SESSION['userlogin']);
            return false;
        else:
            return true;
        endif;
        
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */

    /**
     * <b>setLogin:</b> Metodo responsável por realizar as verificações e exibir as mensagens, caso o login informado 
     * esteja correto o mesmo executa o methodo privado desta classe chamado execute();
     * OBS: Toda vez que retornar error o atributo return receberá false, quando for verdadeiro não necessita retornar true 
     * pois o metodo Execute() já faz isso.
     * Motivos das verificações:
     *  if (!$this->Email || !$this->Senha || !Check::Email($this->Email)): Verifica se o email ou senha não foi informado ou se o email não é valido;
     * 
     *  elseif (!$this->getUser()): Verifica se o usuário ou a senha estão incorretos ou se não existem na tabela
     * 
     * elseif ($this->Result['user_level'] < $this->Level): Verifica se level de acesso(nível de acesso) do usuário é menor do que o exigido;
     * 
     * else: Verifica se o usuário está correto, caso esteja executa o metodo privado Execute()
     */
    private function setLogin() {
        //Verifica se o email ou a senha não foi informado ou se o email não é valido
        if (!$this->Email || !$this->Senha || !Check::Email($this->Email)):
            $this->Error = ['Informe seu E-mail e senha para efetuar o login!', MSG_INFOR];
            $this->Result = false;
        //se não encontrar o usuário e senha corretos
        elseif (!$this->getUser()):
            $this->Error = ['Email ou Senha invalidos ! Por favor verifique os dados informados e tente novamente !', MSG_ERROR];
            $this->Result = false;
        //caso o usuário não possui nivel de acesso
        elseif ($this->Result['user_level'] < $this->Level):
            $this->Error = ["Desculpe, {$this->Result['user_name']} , você não possui permissão para acessar está área ", MSG_ERROR];
            $this->Result = false;
        else:
            $this->Execute();
        endif;
    }

    /**
     * <b>getUser:</b> Metodo responsável por realiza consulta na tabela do usuário e verifica se email e senha estão corretos
     * Caso esteja armazena o atributo Result o primeiro resultado (indice [0] e retorna true 
     * Caso esteja incorreta retorna false;
     * @return boolean Result
     */
    private function getUser() {
        $this-> Senha = md5($this->Senha);
        
        $readUser = new Read();
        $readUser->ExeRead('sys_user', "WHERE user_email = :email AND user_password = :pass AND user_status=:status", "email={$this->Email}&pass={$this->Senha}&status=1");

        //Se obter resultado
        if ($readUser->getResult()):
            //indice [0] armazena o primeiro resultado
            $this->Result = $readUser->getResult()[0];
            return true;
        else:
            return false;
        endif;
    }

    /**
     * <b>Execute:</b>Metodo responsável por verifica se a sessão existe, caso não exista starta a sessão e armazena
      no atributo Error a mensagem do usuário e armazena o Result como true
     *      
     */
    private function Execute() {
        //Verifica se a sessão não foi startada, caso verdadeiro, starta a mesma;
        if (!session_id()):
            session_start();
        endif;

        $_SESSION['userlogin'] = $this->Result;

        $this->Error = ["Olá {$this->Result['user_name']}, seja bem vindo, Favor aguarde o redirecionamento!", MSG_ACCEPT];

        $this->Result = true;
    }

}
