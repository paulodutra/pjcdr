<?php

/**
 *  Link.class[MODEL]
 * Classe responsável por organizar o SEO do sistema e realizar a navegação
 * 
 * OBS: 
 * $Local: Url completa de acesso
 * $File: Identifica o arquivo acessado
 * $Link: Identifica o name para obter os dados do banco de dados;
 * $Patch: O caminho e o arquivo de inclusão para fazer a navegação
 * 
 * @copyright (c) 2015, Paulo Henrique 
 */
class Link {

    /** Identificar o file e o link acessado */
    private $File;
    private $Link;

    /** DATA: utilizado para a obtenção de dados */
    private $Local;
    private $Patch;
    private $Tags;
    private $Data;

    /** @var Seo:  Atributo responsável por instanciar e utilizar os metodos da classe SEO*/
    private $Seo;

    /**
     * <b>__construct:</b>Construtor da classe, altera o comportamento inicial da mesma, realizando algumas validações nos 
     * atribuitos $Local, $File, $Link e instanciar um objeto da classe SEO no atributo da $SEO
     */
    function __construct() {
        $this->Local = strip_tags(trim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT))); //url vem do htacess , que esta realizando a navegação amigavel
        $this->Local = ($this->Local ? $this->Local : 'index');
        $this->Local = explode('/', $this->Local);
        $this->File = (isset($this->Local[0]) ? $this->Local[0] : 'index');
        $this->Link = (isset($this->Local[1]) ? $this->Local[1] : null);
        $this->Seo = new Seo($this->File, $this->Link);
    }

    /**
     * <b>getTags:</b>Obtém as tags da classe SEO, realizando a otimização da pagina
     */
    public function getTags() {  
        $this->Tags = $this->Seo->getTags();
        echo $this->Tags;
    }

    /**
     * <b>getData:</b>Obtém aos dados da classe SEO, que foi utilizado para a realização da obtenção das tags
     */
    public function getData() {
        $this->Data = $this->Seo->getData();
        return $this->Data;
    }

    /**
     * <b>getLocal:</b>Obtém o local da página, para que seja possível realizar a navegação 
     * 
     * @return string $Local
     */
    public function getLocal() {
        return $this->Local;
    }

    /**
     * <b>getPatch:</b>Chama o metodo setPacth  e depois obtém o mesmo valor do $Patch.
     * 
     * @return string $Pacth
     */
    public function getPatch() {
        $this->setPatch();
        return $this->Patch;
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */

    /**
     * <b>setPatch:</b> Método responsavel em realizar a validação e realizar o require da pasta e do arquivo acessado 
     */
    private function setPatch() {

        if (file_exists(REQUIRE_PATH . DIRECTORY_SEPARATOR . $this->File . '.php')):
            $this->Patch = REQUIRE_PATH . DIRECTORY_SEPARATOR . $this->File . '.php';

        elseif (file_exists(REQUIRE_PATH . DIRECTORY_SEPARATOR . $this->File . DIRECTORY_SEPARATOR . $this->Link . '.php')):
            $this->Patch = REQUIRE_PATH . DIRECTORY_SEPARATOR . $this->File . DIRECTORY_SEPARATOR . $this->Link . '.php';
        else:
            $this->Patch = REQUIRE_PATH . DIRECTORY_SEPARATOR . '404.php';

        endif;
    }

}
