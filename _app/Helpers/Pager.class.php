<?php
/**
 *  Pager.class[HELPER]
 * Realiza a gestão e a paginação de resultados do sistema;
 * @copyright (c) 2015, Paulo Henrique 
 */
class Pager {

    /**  DEFINE O PAGER */
    private $Page;
    private $Limit;
    private $Offset;

    /** REALIZA A LEITURA */
    private $Tabela;
    private $Consulta;
    private $Termos;
    private $Places;

    /** DEFINE O PAGINATOR */
    private $Rows;
    private $Link;
    private $MaxLinks;
    private $First;
    private $Last;

    /** RENDERIZA O PAGINATOR */
    private $Paginator;

    function __construct($Link, $First = null, $Last = null, $MaxLinks = null) {
        $this->Link = (string) $Link;
        //Caso a primeira pagina seja personalizada ira ser definido se não recebe Primeira Pagina Por Default
        $this->First = ((string) $First ? $First : 'Primeira Página');
        //Caso a última pagina seja personalizada ira ser definido se não recebe Última Pagina Por Default
        $this->Last = ((string) $Last ? $Last : 'Última Página');
        //Se for informado um numero maximo de links por pagina ele define se não recebera 5 por default
        $this->MaxLinks = ((int) $MaxLinks ? $MaxLinks : 5);
    }

    /**
     * <b>ExePager:</b> Metodo Responsável por definir a pagina , caso a mesma seja informada se não recebera um por default e o 
     * limit de exibição por pagina que devera ser informado
     * @param int $Page
     * @param int $Limit
     */
    public function ExePager($Page, $Limit) {
        //Se for informado uma pagina a mesma será definida se não recebe 1 por default
        $this->Page = ( (int) $Page ? $Page : 1);
        $this->Limit = (int) $Limit;
        //Pega a pagina o valor da pagina atual multiplica pelo limite e depois subtrai o limite
        $this->Offset = ($this->Page * $this->Limit) - $this->Limit;
    }

    /**
     * <b>ReturnPage</b> Metodo responsável por retornar a pagina anterior
     */
    public function ReturnPage() {
        if ($this->Page > 1):
            $nPage = $this->Page - 1;
            header("Location:{$this->Link}{$nPage}");
        endif;
    }
    /**
     * <b>getPage:</b> Metodo responsável por retorna o numero da pagina atual
     * @return Page
     */

    public function getPage() {
        return $this->Page;
    }
    /**
     * <b>getLimit:</b> Metodo responsável por retornar o Limit
     * @return Limit
     */

    public function getLimit() {
        return $this->Limit;
    }
    /**
     * <b>getOffset:</b> Metodo responsável por retorna o numero de Offset
     * @return Offset
     */

    public function getOffset() {
        return $this->Offset;
    }
    
    /**
     * <b>ExePaginator:</b> Metodo responsável por executar todo o processo de paginação
     * recebendo por parametro:
     * $Tabela=  Tabela a ser realizada a leitura(Obrigatório)
     * $Termos = Condiçao de leitura exemplo Limit , Offset, Where Etc (Opcional)
     * $ParseString= Parametros das condições geramente o mesmo nome informado nos termos em caixa baixa com pois pontos antes para a realização do bindValue(PDO) (Opcional)
     * 
     * @param String $Tabela
     * @param String $Termos
     * @param String $ParseString
     */

    public function ExePaginator($Tabela, $Termos = null, $ParseString = null) {
        $this->Tabela = (string) $Tabela;
        $this->Termos = (string) $Termos;
        $this->Places = (string) $ParseString;

        $this->getSyntax();
    }
    /**
     * <b>ExePaginatorFull:</b> Metodo responsável por executar todo o processo de paginação
     * recebendo por parametro:(Geralmente utilizado para pagina com consultas em MAIS de uma tabela)
     * $Consulta = Consulta SQL a ser executada.
     * $Termos = Condiçao de leitura exemplo Limit , Offset, Where Etc (Opcional)
     * $ParseString= Parametros das condições geramente o mesmo nome informado nos termos em caixa baixa com pois pontos antes para a realização do bindValue(PDO) (Opcional)
     * 
     * @param String $Consulta
     * @param String $Termos
     * @param String $ParseString
     */
    public function ExePaginatorFull($Consulta, $Termos = null, $ParseString = null) {
        $this->Consulta = (string) $Consulta;
        $this->Termos = (string) $Termos;
        $this->Places = (string) $ParseString;

        $this->getSyntaxFull();
    }
    
    /**
     * <b>getPaginator:</b> Metodo responsável por retornar o numero de paginas renderizadas 
     * @return Paginator
     */

    public function getPaginator() {

        return $this->Paginator;
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */
    
    /**
     * <b>getSyntax:</b>Metodo responsável por retorna a sintaxe da paginação.
     * Realiza a leitura da tabela, que ira apresentar os dados
     * Verifica se o numero de resultados e maior que o limite de  resultados que podem ser exibidos por pagina.
     * Caso seja realiza uma calculo ceil(numero de resultados / limite) sempre retornando numero inteiros de paginas
        Depois disso realiza a renderização do html, 
     *
     */
    private function getSyntax() {
        
        $read = new Read;
        $read->ExeRead($this->Tabela, $this->Termos, $this->Places);
        $this->Rows = $read->getRowCount();

        if ($this->Rows > $this->Limit):
            $Paginas = ceil($this->Rows / $this->Limit);
            $MaxLinks = $this->MaxLinks;

            $this->Paginator = "<nav>";
            $this->Paginator .="<ul class=\"pagination\">";
            $this->Paginator .= "<li>";
            $this->Paginator .= "<a title=\"{$this->First}\" href=\"{$this->Link}1\" aria-label=\"Previous\">{$this->First}</a>";
            $this->Paginator .= "</li>";

            for($iPag = $this->Page - $MaxLinks; $iPag <= $this->Page - 1; $iPag ++):
                if($iPag >=1):
                  $this->Paginator .= "<li><a  title=\"Pagina {$iPag}\" href=\"{$this->Link}{$iPag}\">{$iPag}</a></li>";
               endif;   
           endfor;

            $this->Paginator .="<li class=\"active\"><a>{$this->Page}</a></li>";
           
            for($dPag = $this->Page + 1; $dPag <= $this->Page  + $MaxLinks; $dPag ++):
                if($dPag <= $Paginas):
                  $this->Paginator .= "<li><a  title=\"Pagina {$dPag}\" href=\"{$this->Link}{$dPag}\">{$dPag}</a></li>";
               endif;   
           endfor;
           
            $this->Paginator .="<li><a  title=\"{$this->Last}\"  href=\"{$this->Link}{$Paginas}\">{$this->Last}</a></li>";
            $this->Paginator .="</ul>";
            $this->Paginator ."</nav>";




        endif;
    }
    
    /**
     * <b>getSyntaxFull:</b>Metodo responsável por retorna a sintaxe da paginação.
     * Realiza a leitura em mais de uma tabela, que ira apresentar os dados
     * Verifica se o numero de resultados e maior que o limite de  resultados que podem ser exibidos por pagina.
     * Caso seja realiza uma calculo ceil(numero de resultados / limite) sempre retornando numero inteiros de paginas
        Depois disso realiza a renderização do html,
     */
     private function getSyntaxFull() {
        
        $read = new Read;
        $read->FullRead($this->Consulta,$this->Places);
        $this->Rows = $read->getRowCount();

        if ($this->Rows > $this->Limit):
            $Paginas = ceil($this->Rows / $this->Limit);
            $MaxLinks = $this->MaxLinks;

            $this->Paginator = "<nav>";
            $this->Paginator .="<ul class=\"pagination\">";
            $this->Paginator .= "<li>";
            $this->Paginator .= "<a title=\"{$this->First}\" href=\"{$this->Link}1\" aria-label=\"Previous\">{$this->First}</a>";
            $this->Paginator .= "</li>";

            for($iPag = $this->Page - $MaxLinks; $iPag <= $this->Page - 1; $iPag ++):
                if($iPag >=1):
                  $this->Paginator .= "<li><a  title=\"Pagina {$iPag}\" href=\"{$this->Link}{$iPag}\">{$iPag}</a></li>";
               endif;   
           endfor;

            $this->Paginator .="<li class=\"active\"><a>{$this->Page}</a></li>";
           
            for($dPag = $this->Page + 1; $dPag <= $this->Page  + $MaxLinks; $dPag ++):
                if($dPag <= $Paginas):
                  $this->Paginator .= "<li><a  title=\"Pagina {$dPag}\" href=\"{$this->Link}{$dPag}\">{$dPag}</a></li>";
               endif;   
           endfor;
           
            $this->Paginator .="<li><a  title=\"{$this->Last}\"  href=\"{$this->Link}{$Paginas}\">{$this->Last}</a></li>";
            $this->Paginator .="</ul>";
            $this->Paginator ."</nav>";




        endif;
    }

}
