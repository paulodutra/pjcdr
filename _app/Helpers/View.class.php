<?php

/**
 *  View.class[HELPER MVC]
 * Responsável por carregar o template, povoar e exibir a view, povoar e incluir arquivos PHP no sistema
 * arquitetura MVC
 * 
 * @copyright (c) 2015, Paulo Henrique 
 */
class View {

    private $Data;
    private $Keys;
    private $Values;
    private $Template;

    public function Load($Template) {
        $this->Template = REQUIRE_PATH. DIRECTORY_SEPARATOR . '_tpl' . DIRECTORY_SEPARATOR .(string) $Template;
        $this->Template = file_get_contents($this->Template . '.tpl.html');
        return $this->Template;
    }

    public function Show(array $Data, $View) {
        $this->setKeys($Data);
        $this->setValues();
        $this->ShowView($View);
    }

    public function Request($File, array $Data) {
        //permite utilizar o nome das colunas da tabela na qual foi realizada a consulta como variaveis
        extract($Data);
        //Faz requisição de arquivos PHP do tipo inc(inclusão)
        require("{$File}.inc.php");
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */
    private function setKeys($Data) {
        $this->Data = $Data;
        $this->Data['HOME'] = HOME;
        $this->Keys = explode('&', '#' . implode("#&#", array_keys($this->Data)) . '#');
        $this->Keys[] = '#HOME#';
    }

    private function setValues() {
        $this->Values = array_values($this->Data);
    }

    private function ShowView($View) {
        $this->Template = $View;
        echo str_replace($this->Keys, $this->Values, $this->Template);
    }

}
