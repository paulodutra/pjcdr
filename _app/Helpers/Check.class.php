<?php

/**
 *  Check.class[HELPER]
 * Classe responsável por manipular e validar dados do sistema
 * @copyright (c) 2015, Paulo Henrique 
 */
class Check {

    //Atributo responsável por definir  dados
    private static $Data;
    //Atributo responsável por definir formato
    private static $Format;

    public static function Email($Email) {
        self::$Data = (string) $Email;
        self::$Format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/ ';

        if (preg_match(self::$Format, self::$Data)):
            return true;
        else:
            return false;
        endif;
    }

    /**
     * <b>Url:</b> Metodo Responsável por retirar caracteres especiais dos titulos
     * para criar url amigaveis apartir dos mesmos
     * @param String $Url
     * @return String sem tags HTML e utf8-encode
     */
    public static function Url($Url) {
        self::$Format = array();
        self::$Format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        self::$Format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

        //Substitui todos os caracteres especiais e caracteres com acentos da format[a] por caractes normais da format[b]
        self::$Data = strtr(utf8_decode($Url), utf8_decode(self::$Format['a']), self::$Format['b']);
        //retira tags HTML
        self::$Data = strip_tags(trim(self::$Data));

        //Retira os espaços e coloca -(hiffens) no lugar
        self::$Data = str_replace(' ', '-', self::$Data);
        self::$Data = str_replace(array('-----', '----', '---', '--'), '-', self::$Data);

        //Coloca os caracteres em caixa baixa e 
        return strtolower(utf8_encode(self::$Data));
    }

    /**
     * <b>Data:</b> Metodo responsável por converter a data informada, para uma data no formato timestamp
     * @param  $Data
     */
    public static function Data($Data) {
        //Separa a data da hora
        self::$Format = explode(' ', $Data);
        //Ao separar a data da hora o indice da data fica sendo o indice [0]
        self::$Data = explode('/', self::$Format[0]);
        //Caso não seja informada a hora o mesmo ira pegar a hora automaticamente
        if (empty(self::$Format[1])):
            self::$Format[1] = date('H:i:s');
        endif;
        /**
         * <b>self::$Data:</b> Ao dar um explode em $Data a mesma retirou as / (barras) e ficou da seguinte maneira
         * $Data[0] = Dia;
         * $Data[1]= Mês;
         * $Data[2]= Ano
         *
         */
        // concatena a data de tras para frente, junto com o formato da hora informado em $Format[1];
        self::$Data = self::$Data[2] . '-' . self::$Data[1] . '-' . self::$Data[0] . ' ' . self::$Format[1];
        //Retorna a data
        return self::$Data;
    }

    /**
     * <b>Words:</b>Metodo responsável por limitar cacteres de uma string
     * Será utilizada para listar os artigos
     * @param String  $String
     * @param int $Limite
     * @param string $Pointer
     */
    public static function Words($String, $Limite, $Pointer = null) {
        //Elimina qualquer codigo HTML e espaços
        self::$Data = strip_tags(trim($String));
        self::$Format = (int) $Limite;
        //Qualquer espaço na string se torna um indice novo
        $ArrWords = explode(' ', self::$Data);
        $NumWords = count($ArrWords);
        $NewWords = implode(' ', array_slice($ArrWords, 0, self::$Format));

        $Pointer = (empty($Pointer) ? '...' : ' ' . $Pointer);

        $Result = (self::$Format < $NumWords ? $NewWords . $Pointer : self::$Data);

        return $Result;
    }

    /**
     * <b>CatByName:</b>Metodo utilizado para listar categorias por nome
     * retorna o primeiro resultado da consulta por causa do [0]indice zero
     * @param type $CategoryName
     */
    public static function CatByName($CategoryName) {
        $read = new Read();
        $read->ExeRead('blog_categories', "WHERE category_name = :name", "name={$CategoryName}");
        if ($read->getResult()):
            return $read->getResult()[0]['category_id'];
        else:
            echo "A categoria {$CategoryName} não foi encontrada!";
            die;
        endif;
    }

    //ws_siteviews_online
    /**
     * <b>UserOnline:</b> Responsável por verifica quantos usuários estão online no site 
     * @return $readUSerOnline
     */
    public static function UserOnline() {
        $now = date('Y-m-d H:i:s');
        $deleteUserOnline = new Delete();
        $deleteUserOnline->ExeDelete('ws_siteviews_online', "WHERE online_endview < :now", "now={$now}");

        $readUserOnline = new Read();
        $readUserOnline->ExeRead('ws_siteviews_online');
        return $readUserOnline->getRowCount();
    }
    
    /**
     * 
     * <b>Image:</b> Classe responsável por carregar imagem  e redimensioar a mesma de acordo com os parametros passados:
     * $ImagemUrl= Nome da imagem (Obrigatório)
     * $ImagemDesc= Descrição da imagem (Obrigatório)
     * $ImagemW= largura da imagem (Opcional)
     * $ImagemH=Altura da imagem(Opcional)
     * 
     * @param string $ImageUrl
     * @param string $ImageDesc
     * @param int ou String $ImageW
     * @param int ou string  $ImageH
     * @return boolean
     */

    public static function Image($ImageUrl, $ImageDesc, $ImageW = null, $ImageH = null) {
        self::$Data = $ImageUrl;
        //Verifica se o arquivo existe e se o mesmo não é uma outra pasta
        if (file_exists(self::$Data) && !is_dir(self::$Data)):
            $patch = HOME;
            $Imagem = self::$Data;
            //torma o caminho mais a imagem
           
            return "<img src=\"{$patch}/tim.php/?src={$patch}/{$Imagem}&w={$ImageW}&h={$ImageH}\" alt=\"{$ImageDesc}\" title=\"{$ImageDesc}\"/>";
        else:
            return false;
        endif;
    }

}
