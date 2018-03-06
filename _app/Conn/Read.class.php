<?php

/**
 *  <b>Read.class:</b>
 *  Classe responsável por leituras genericas no banco de dados 
 * @copyright (c) 2015, Paulo Henrique 
 */
class Read extends Conn {

    private $Select;
    private $Places;
    private $Result;

    /** @var PDOStatement */
    private $Read;

    /*     * @var PDO */
    private $Conn;

    
    public function ExeRead($Tabela, $Termos = null, $ParseString = null) {
        if(!empty($ParseString)):
            parse_str($ParseString, $this->Places);
        endif;
        
        $this->Select = "SELECT * FROM {$Tabela} {$Termos}";
        
        $this->Execute();
    }
    
    public function ExeReadMaxID($Tabela, $Coluna){
        
        $this->Select="SELECT MAX({$Coluna}) FROM {$Tabela}";
        $this->Execute();
        
    }

   
    public function getResult() {
        return $this->Result;
    }
    
    public function getRowCount() {
        return $this->Read->rowCount();
    }
    /**
     * <b>FullRead:</b>Permite que passe a query manualmente, 
     * Deve ser utilizada para query mais robustas com duas ou mais tabelas.
     * Exemplo: Join, com sub consultas etc.
     * 
     * @param STRING $Query
     * @param STRING $ParseString
     */
    public function FullRead($Query, $ParseString=null) {
        
        $this->Select = (string) $Query;
         if(!empty($ParseString)):
            parse_str($ParseString, $this->Places);
        endif;
        $this->Execute();
    }
    /**
     * <b>setPlaces:</b>Altera o ParseString para poder exibir resultados diferentes sem ter que manipular a query
     * Deve passar todos os parseString da consulta anterior, caso isso não ocorra a mesma irá dar erro
     */
    public function setPlaces($ParseString) {
           parse_str($ParseString, $this->Places);
           $this->Execute();
        
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */
    //Padrao conexão obtenção de sintexe , e execute 
    
    
/**
 * <b>Connect:</b>Realiza a conexão com banco, Atributo Read recebe a conexão e o metodo prepare do PDO
 * passando com parametro o atributo Select( Que contem a query)
 *Depois o setFetchMode, seta  o objeto para retorna a consulta em forma de array associativo(similar ao mysql_assoc)
 */
    private function Connect() {
        $this->Conn = parent::getConn();
        $this-> Read = $this->Conn->prepare($this->Select);
        $this->Read->setFetchMode(PDO::FETCH_ASSOC);
        
       
    }
    
/**
 * <b>getSyntax:</b>Verifica se o atributo Places tem valor, 
 * Caso tenha realiza um foreach nos valores do atributo, após realizar o foreach, 
 * Verifica se a variavel vinculo(contem o nome das colunas que ira compor a query) possui a palavra reservada
 * limit ou offset, caso tenha esta a variavel valor neste momento ira receber int(pois se passar o limit ou offset com inteiro a query irá dar erro
 * Depois realiza o bindValue Pegando a variavel vinculo que contem o nome das colunas  e colocando os : (dois pontos)
 * depois o operador ternario verifica se o variavel valor é inteiro caso seja ira receber PDO::PARAM_INT caso contrario PDO::PARAM_STR

 * OBS: Para passar a palavra reservada offset na consulta é necessário passar a palavra limit
 *  */
    private function getSyntax() {
       if($this->Places):
           foreach ($this->Places as $Vinculo => $Valor ):
                if($Vinculo == 'limit' || $Vinculo=='offset'):
                    $Valor = (int) $Valor;
                endif;
                $this->Read->bindValue(":{$Vinculo}", $Valor, (is_int($Valor) ? PDO::PARAM_INT : PDO::PARAM_STR  ));
           endforeach;
       endif;

    }
    
   

    private function Execute() {
        $this->Connect();
        try{
            $this->getSyntax();
            $this->Read->execute();
            $this->Result = $this->Read->fetchAll();
            
        } catch (PDOException $ex) {
            $this->Result = null;
            MSGErro("<b>Erro ao Efetuar leitura:</b>{$ex->getMessage()}", $ex->getCode());

        }
        
      
    }

}
