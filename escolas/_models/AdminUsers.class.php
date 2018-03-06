<?php

/**
 *  AdminUsers.class[MODEL ADMIN]
 * Classe responsável por gerenciar e manter os usuários do sistema.
 * 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminUsers {

    private $Data;
    private $User;
    private $Error;
    private $Result;

    /*     * Nome da tabela no banco de dados */

    const Entity = 'sys_user';

    /**
     * <b>ExeCreate:</b> Metodo responsável, validar a senha do usuario,chamar outro metodo em sua execução para validar os dados  e por executar executar o cadastro de usuários 
     * 
     * @param array $Data
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        /** Verifica se a confimação de senha é igual a senha */
        if ($this->Data['user_re_password'] == $this->Data['user_password']):
            unset($this->Data['user_re_password']);
            $this->checkUser();
        else:
            $this->Error = ["A confirmação de senha não é igual a senha, Favor digite ambas iguais", MSG_ERROR];
            $this->Result = false;
        endif;

        if ($this->Result):
            $this->Create();
        endif;
    }

    /**
     * <b>ExeUpdate:</b> Metodo responsável, validar a senha do usuario,chamar outro metodo em sua execução para validar os dados  e por executar executar atualizações no 
     * cadastro de usuários 
     * 
     * @param int $userid
     * @param array $Data
     */
    public function ExeUpdate($userid, array $Data) {
        $this->User = $userid;
        $this->Data = $Data;

        /** Se a confirmação de senha for igual a senha */
        if ($this->Data['user_re_password'] == $this->Data['user_password']):
            unset($this->Data['user_re_password']);
            $this->checkUser();
        /** Se a senha não for informada ele retira os indices de senha e confirma senha da atualização */
        /** Se as senhas  não forem iguais */
        else:
            $this->Error = ["A confirmação de senha não é igual a senha, Favor digite ambas iguais", MSG_ERROR];
            $this->Result = false;
        endif;

        if ($this->Result):
            $this->Update();
        endif;
    }

    public function ExeDelete($userid) {
        $this->User = (int) $userid;

        $readUser = new Read();
        $readUser->ExeRead(self::Entity, "WHERE user_id = :userid", "userid={$this->User}");

        if (!$readUser->getResult()):
            $this->Error = ["<b>Erro ao deletar:</b> Você tentou remover um usuarió que não existe!", MSG_ERROR];
            $this->Result = false;
        elseif ($this->User == $_SESSION['userlogin']['user_id']):
            $this->Error = ["<b>Erro ao deletar:</b> Você tentou remover o seu  usuarió ! Está ação não é permitida !", MSG_ERROR];
            $this->Result = false;
        else:
            if ($readUser->getResult()[0]['user_level'] == 3):
                $readAdmin = new Read();
                $readAdmin->ExeRead(self::Entity, "WHERE user_id != :userid AND user_level = :level", "userid={$this->User}&level=3");

                if (!$readAdmin->getResult()):
                    $this->Error = ["<b>Erro ao deletar:</b> Você tentou remover o unico administrador do sistema ! Está ação não é permitida !", MSG_ERROR];
                    $this->Result = false;
                else:
                    $this->Delete();
                endif;
            else:
                $this->Delete();
            endif;
        endif;
    }

    /**
     * <b>getError:</b>Metodo responsável por obter a mensagem da operação.
     * 
     * @return array Error (array associativo com a mensagem do erro e seu tipo)
     */
    public function getError() {
        return $this->Error;
    }

    /**
     * <b>getResult:</b>Metodo responsáve por obter o resultado da operação
     * 
     * @return bool Result
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */

    /**
     * <b>checkUser:</b> Metodo responsável por validar os dados , antes de realizar um cadastro ou atualiação de usuário
     */
    private function checkUser() {
        /** Condição para usar tanto no cadastro quanto no update */
        $status = ($this->User != '' ? 'Atualizar' : 'Cadastrar');
        /** Verifica se possui campos vazios e se a operação é um cadastro caso seja ira apresentar uma tela de erro ao usuário */
        if (in_array('', $this->Data) && $status == 'Cadastrar'):
            $this->Error = ["<b>Erro ao {$status}:</b> Para {$status}, favor preecha todos os campos", MSG_ALERT];
            $this->Result = false;
        /** Verifica se o e-mail informado é valido */
        elseif (!Check::Email($this->Data['user_email'])):
            $this->Error = ["<b>Erro ao {$status}:</b> O email informado, não é valido!", MSG_ALERT];
            $this->Result = false;
        /** Verifica a senha não esta vazia e se a operacao é um cadastro e se a senha é maior que 6 caracteres ou ,maior que doze. caso seja verdadeiro ira apresentar uma tela de erro */
        elseif (isset($this->Data['user_password']) && !($this->Data['user_password']) && $status == 'Cadastrar' && (strlen($this->Data['user_password']) < 6 || strlen($this->Data['user_password']) > 12)):
            $this->Error = ["A senha deve ter entre 6 a 12 caracteres", MSG_ALERT];
            $this->Result = false;
        /** Caso nenhum dos casos acima seja verdadeiro, ira verificar se o email já foi cadastrado */
        else:
            $this->checkUserRegister();
        endif;
    }

    /**
     * <b>checkUserRegister:</b> Metodo responsável por verificar se o email do usuário já existe, executado tanto no insert quanto no update
     */
    private function checkUserRegister() {
        /*         * Condição para usar tanto no cadastro quanto no update */
        $Where = (isset($this->User) ? "user_id!={$this->User} AND" : '');

        $readUser = new Read();
        $readUser->ExeRead(self::Entity, "WHERE {$Where} user_email= :useremail", "useremail={$this->Data['user_email']}");
        /** Caso tenha alguma linha com resultado */
        if ($readUser->getRowCount()):
            $this->Error = ["<b>Email já existe:</b> O e-mail informado já foi cadastrado por outro usuário", MSG_ERROR];
            $this->Result = false;
        /** Caso não resultado */
        else:
            $this->Result = true;
        endif;
    }

    /**
     * <b>Create:</b>Metodo responsável por executar o cadastro propriamente dito na tabela definida na constante Entity
     */
    private function Create() {
        /*         * Criptografa a senha antes de realizar o cadastro */
        $this->Data['user_password'] = md5($this->Data['user_password']);
        /** Setando a data de cadastro do usuário para o indice user_registration */
        $this->Data['user_registration'] = date('Y-m-d H:i:s');

        $createUser = new Create();
        $createUser->ExeCreate(self::Entity, $this->Data);

        if ($createUser->getResult()):
            $this->Error = ["<b>Sucesso ao cadastrar:</b> Conta do usuário <b>{$this->Data['user_name']}</b>,  foi cadastrada com sucesso !", MSG_ACCEPT];
            $this->Result = $createUser->getResult();/** Retorna o ID do insert */
        endif;
    }

    /**
     * <b>Update:</b>Metodo responsável por executar a atualização  propriamente dito na tabela definida na constante Entity
     */
    private function Update() {
        /** Se o campo de senha for atualizado ele criptografa a senha antes de realizar o update no banco */
        if (!empty($this->Data['user_password'])):
            $this->Data['user_password'] = md5($this->Data['user_password']);
        /** Caso a senha não senha atualizada ele retirar o indice do array para a senha não ser  atualizada com vazia no banco de dados */
        else:
            unset($this->Data['user_password']);
        endif;

        $updateUser = new Update();
        $updateUser->ExeUpdate(self::Entity, $this->Data, "WHERE user_id= :userid", "userid={$this->User}");

        if ($updateUser->getResult()):
            $this->Error = ["<b>Sucesso ao atualizar:</b> Conta do usuário <b>{$this->Data['user_name']}</b>, foi cadastrada com sucesso!", MSG_ACCEPT];
            $this->Result = true;
        endif;
    }

    private function Delete() {

        $deleteUser = new Delete();
        $deleteUser->ExeDelete(self::Entity, "WHERE user_id= :userid", "userid={$this->User}");

        if ($deleteUser):
            $this->Error = ["<b>Sucesso ao deletar:</b> O usuário foi removido com sucesso!", MSG_ACCEPT];
            $this->Result = true;
        endif;
    }

}
