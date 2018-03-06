<?php

/**
 *  LoginSchool.class[MODEL]
 * 
 * Responsável por autenticar , validar e checar usuários da área da escola
 * @copyright (c) 2016, Paulo Henrique 
 */
class LoginSchool {

    private $Level;
    private $CNPJ;
    private $INEP;
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
        $this->CNPJ = (string) strip_tags(trim($UserData['cnpj'])); //o indice e o name do input html do formulario
        $this->INEP = (string) strip_tags(trim($UserData['inep'])); //o indice e o name do input html do formulario
        //var_dump($this->CNPJ);

        if (!is_numeric($this->INEP)):
            $this->Error = ["<b>Erro ao informar INEP</b> Informe apenas números neste campo", MSG_ERROR];
            $this->Result = false;
        else:
            $this->setLogin();
        endif;
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
     * <b>checkLogin:</b>Metodo responsável por verificar a sessão userloginSchool e revalidar o acesso para proteger telas restritias
     * retorna true ou mata a sessão e retorna false;
     *  @return bool login 
     */
    public function checkLogin() {

        if (empty($_SESSION['userloginSchool']) || $_SESSION['userloginSchool']['school_status'] < $this->Level):
            unset($_SESSION['userloginSchool']);
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
     * esteja correto o mesmo executa o metodo privado desta classe chamado execute();
     * OBS: Toda vez que retornar error o atributo return receberá false, quando for verdadeiro não necessita retornar true 
     * pois o metodo Execute() já faz isso.
     * Motivos das verificações:
     *  if (!$this->CNPJ || !$this->INEP || !strlen($this->CNPJ>=14)): Verifica se o CNPJ ou INEP não foi informado ou se o CNPJ não é maior ou igual a 14 caracteres;
     * 
     *  elseif (!$this->getUser()): Verifica se o CNPJ ou a INEP estão incorretos ou se não existem na tabela
     * 
     * elseif ($this->Result['school_status'] < $this->Level): Verifica se level de acesso(nível de acesso) do usuário é menor do que o exigido;
     * 
     * else: Verifica se o usuário está correto, caso esteja executa o metodo privado Execute()
     */
    private function setLogin() {
        //Verifica se o email ou a senha não foi informado ou se o email não é valido
        if (!$this->CNPJ || !$this->INEP):
            $this->Error = ['Informe seu CNPJ e INEP para efetuar o login!', MSG_INFOR];
            $this->Result = false;
        //se não encontrar o usuário e senha corretos
        elseif (!$this->getUser()):
            $this->Error = ['CNPJ ou INEP invalidos ! Por favor verifique os dados informados e tente novamente !', MSG_ERROR];
            $this->Result = false;
        //caso o usuário não possui nivel de acesso
        elseif ($this->Result['school_status'] < $this->Level):
            $this->Error = ["Desculpe, {$this->Result['user_name']} , você não possui permissão para acessar está área ", MSG_ERROR];
            $this->Result = false;
        else:
            $this->Execute();
        endif;
    }

    /**
     * <b>getUser:</b> Metodo responsável por realiza consulta na tabela do usuário e verifica se CNPJ e INEP estão corretos
     * Caso esteja armazena o atributo Result o primeiro resultado (indice [0] e retorna true 
     * Caso esteja incorreta retorna false;
     * @return boolean Result
     */
    private function getUser() {


        $readUser = new Read();
        $readUser->ExeRead('es_school', "WHERE school_cnpj = :cnpj AND school_inep = :inep", "cnpj={$this->CNPJ}&inep={$this->INEP}");

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

        $_SESSION['userloginSchool'] = $this->Result;

        $this->Error = ["Olá {$this->Result['school_name']}, seja bem vindo, Favor aguarde o redirecionamento!", MSG_ACCEPT];

        $this->Result = true;
    }

}
