<?php


/**
 *  Conn.class[CONEXÃO]
 * Classe Abstrata de coneão ao banco de dados utilizando o PDO e o Padrão SingleTon
 * Que Previne que tenha apenas uma instancia do Objeto sendo executada na memória.
 * Retorna um objeto do tipo PDO pelo metodo estático getConn();
 * @copyright (c) 2015, Paulo Henrique 
 */
class Conn {
    
    private static $Host = HOST;
    private static $User = USER;
    private static $Pass = PASS;
    private static $Dbsa = DBSA;
    
    /** @var PDO*/
    
    private static  $Connect=null;
    
    /**
     * Conecta com o banco de dados utilizando o PDO e o padrão Singleton
     * Retorna um objeto do tipo PDO
     */
    
    private static function Conectar(){
        try{
            if(self::$Connect==null):
                //Setando o host e o dbname para a dsn
                $dsn='mysql:host=' .  self::$Host  .  ';dbname=' . self::$Dbsa;
                //Setando a options para o banco receber caracteres UTF8
                $options= [ PDO::MYSQL_ATTR_INIT_COMMAND  => 'SET NAMES UTF8' ];
                self::$Connect= new PDO($dsn, self::$User, self::$Pass, $options);
            endif;
            
        } catch (PDOException $ex) {
            PHPErro($ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
            die;
        }
        
        
        self::$Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return self::$Connect;
        
        
    }
    
    /**
     * 
     * Retorna um objeto PDO SingleTon
     */
    public static function getConn() {
        return self::Conectar();
        
    }
    
   
  
}
