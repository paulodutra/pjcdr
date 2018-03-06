<?php

/**
 *  <b>Create.class:</b>
 *  Classe responsável por cadastros genericos no banco de dados 
 * @copyright (c) 2015, Paulo Henrique 
 */
class Create extends Conn {

    private $Tabela;
    private $Dados;
    private $Result;

    /** @var PDOStatement */
    private $Create;

    /*     * @var PDO */
    private $Conn;

    /**
     * <b>ExeCreate:</b>Executa um cadastro no banco de dados utilizando prepared statement.
     * Para que o cadastro seja realizado deve-se informar o nome da tabela e um array atribuitivo com o nome da coluna e valor
     * 
     * @param STRING $Tabela = Nome da tabela que ira realizar o cadastro no banco de dados
     * @param array $Dados = Array atribuitivo.(NOME DA COLUNA=> VALOR)
     */
    public function ExeCreate($Tabela, array $Dados) {
        $this->Tabela = (string) $Tabela;
        $this->Dados = $Dados;

        $this->getSyntax();

        $this->Execute();
    }

    /**
     * <b>getResult:</b> Retorna o ultimo id inserido no banco caso o cadsatro seja realizado ou retorna false caso ocorra alguma falha no cadastro.
     * @return true ou false
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */
    //Padrao conexão obtenção de sintexe , e execute 
    
    
/**
 * <b>Connect:</b> Retornar a conexão  e atribui  ao objeto create o prepare staments dentro do metodo 
 * prepare do PDO
 */
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Create = $this->Conn->prepare($this->Create);
    }
    
/**
 * <b>getSyntax:</b> Monta a query apartir das chaves do array dados
 * dividindo o processo em duas etapas que foram atribuidas as seguintes variaveis:
 * $Fields: da um implode na chave do arrays Dados pegando a coluna da tabela
 * 
 * $Places da um implode na chave do array Dados pegando a coluna e colocando dois pontos antes para realizar o bindvalue do PDO
 */    

    private function getSyntax() {
        $Fields = implode(', ', array_keys($this->Dados));
        $Places = ':' . implode(', :', array_keys($this->Dados));
        
        $this->Create = "INSERT INTO {$this->Tabela} ({$Fields}) VALUES ({$Places})";

    }
    
    /**
     * <b>Execute:</b> Realiza o cadastro no banco, 
     * Atribui ao metodo execute o Array Dados e atrubui ao Result o metodo com que 
     * pega o ultimo ID inserido no banco
     */

    private function Execute() {
        $this->Connect();

        try {

            $this->Create->execute($this->Dados);
            $this->Result = $this->Conn->lastInsertId();
        } catch (PDOException $ex) {
            $this->Result = null;
            MSGErro("<b>Erro ao Efetuar o cadastro:</b>{$ex->getMessage()}", $ex->getCode());
        }
    }

}
