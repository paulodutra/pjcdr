<?php

/**
 *  <b>Update.class:</b>
 *  Classe responsável por atualizações genericas no banco de dados 
 * @copyright (c) 2015, Paulo Henrique 
 */
class Update extends Conn {

    private $Tabela;
    private $Dados;
    private $Termos;
    private $Places;
    private $Result;

    /** @var PDOStatement */
    private $Update;

    /*     * @var PDO */
    private $Conn;
    
    /**
     * <b>ExeUpdate:</b> Responsavel por reunir os metodos e executar o Update
     * @param String $Tabela
     * @param array $Dados
     * @param String $Termos
     * @param String $ParseString
     */

    public function ExeUpdate($Tabela, array $Dados, $Termos, $ParseString) {
        $this->Tabela = (string) $Tabela;
        $this->Dados = $Dados;
        $this->Termos = (string) $Termos;

        parse_str($ParseString, $this->Places);
        $this->getSyntax();
        $this->Execute();
    }

    /**
     * <b>getResult:</b>Retorna o resultado da atualização sendo true ou false
     * @return bool Result 
     */
    public function getResult() {
        return $this->Result;
    }
    
    /**
     * <b>getRowCount:</b>Retorna o numero de linhas que foram atualizadas
     * @return Update
     */

    public function getRowCount() {
        return $this->Update->rowCount();
    }
    
    /**
     * <b>setPlaces:</b> Permite executar a query novamente apenas mudando o parametro de atualização
     * @param string $ParseString
     */

    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->Places);
        $this->getSyntax();
        $this->Execute();
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */
    //Padrao conexão obtenção de sintexe , e execute 


/**
 * <b>Connect:</b>Responsável pela conexão com o banco de dados e por preparar a query
 */
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Update = $this->Conn->prepare($this->Update);
    }
    
    /**
     * <b>getSyntax:</b>Responsável por montar a query, passando os bindvalues com o mesmo nome da coluna porém com : antes do nome
     */

    private function getSyntax() {
        foreach ($this->Dados as $Key => $Value):
            $Places[] = $Key . ' = :' . $Key;
        endforeach;
        $Places = implode(', ', $Places);
        $this->Update = "UPDATE {$this->Tabela} SET {$Places} {$this->Termos}";
    }
    
    /**
     * <b>Execute:</b>Responsável pela execução e mesclagem dos dois arrays, um contendo as colunas a serem atualizadas e o outro array os dados
     */

    private function Execute() {
        $this->Connect();
        try {
            $this->Update->execute(array_merge($this->Dados, $this->Places));
            $this->Result=true;
            
        } catch (PDOException $ex) {
            $this->Result = null;
            MSGErro("<b>Erro ao Efetuar Atualizar:</b>{$ex->getMessage()}", $ex->getCode());
        }
    }

}
