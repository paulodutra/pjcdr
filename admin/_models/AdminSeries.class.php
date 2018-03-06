<?php

/**
 *  AdminSeries.class[MODEL ADMIN]
 * Classe responsável por administrar as series 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminSeries {

    private $Data;
    private $SeriesID;
    private $SerieCategory;
    private $Error;
    private $Result;

    const Entity = 'cs_series';

    /**
     *
     * <b>ExeCreate:</b>Método responsável por checar, validar o cadastro de series
     * @param Array $Data
     */
    public function ExeCreate(array $Data) {

        $this->Data = $Data;
        $this->setData();

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para adicionar uma serie, preencha todos os campos !", MSG_ALERT];

        elseif (!is_numeric($this->Data['series_number']) || $this->Data['series_number'] <= 0 || strlen($this->Data['series_number']) > 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para adicionar uma serie, informe 1 número sendo maior que 0!", MSG_ERROR];

        elseif ($this->setName() || !$this->Result):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Serie informada já foi cadastrada !", MSG_ERROR];

        elseif ($this->setSeries() || !$this->Result):
            $this->Result = false;

        else:
            $this->Create();

        endif;
    }

    /**
     *
     * <b>ExeUpdate:</b>Método responsável por validar, checar e realizar a atualização do cadastro de series.  
     * @param INT $SeriesID
     * @param Array $Data
     */
    public function ExeUpdate($SeriesID, array $Data) {
        $this->SeriesID = (int) $SeriesID;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para adicionar uma serie, preencha todos os campos !", MSG_ALERT];

        elseif (!is_numeric($this->Data['series_number']) || strlen($this->Data['series_number']) > 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para adicionar uma serie, informe 1 número !", MSG_ERROR];

        elseif ($this->setName() || !$this->Result):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Serie informada já foi cadastrada !", MSG_ERROR];

        elseif ($this->setSeries() || !$this->Result):
            $this->Result = false;

        else:
            $this->Update();

        endif;
    }

    /**
     * <b>ExeStatus:</b>Metodo responsável por atualizar o status da serie  para ativo ou inativo de acordo com a 
     * escolha do usuário
     * 
     * @param int $SerieID
     * @param string $SerieStatus
     */
    public function ExeStatus($SerieID, $SerieStatus) {
        $this->SerieID = (int) $SerieID;
        $this->Data['series_status'] = (string) $SerieStatus;

        $updateSchool = new Update();
        $updateSchool->ExeUpdate(self::Entity, $this->Data, "WHERE series_id=:id", "id={$this->SerieID}");
    }

    /**
     * <b>ExeDelete:</b> Método responsável por realizar a exclusão das series
     * @param Int $SeriesID
     */
    public function ExeDelete($SeriesID) {

        $this->SeriesID = (int) $SeriesID;

        $readSerie = new Read();
        $readSerie->ExeRead(self::Entity, "WHERE series_id=:id", "id={$this->SeriesID}");

        if (!$readSerie->getResult()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao deletar: </b> Você tentou excluir uma serie que não existe ou que já foi excluida antes !", MSG_ERROR];

        elseif ($this->searchSubscribers() || !$this->Result):
            $this->Result = false;
        else:
            $this->Delete();
        endif;
    }

    /**
     * <b>ExeAddSerie:</b> Método responsável por checar, validar o cadastro de series em uma categoria do concurso
     */
    public function ExeAddSerie(array $Data) {
        $this->Data = $Data;
        $this->setData();

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para adicionar uma serie, preencha todos os campos !", MSG_ALERT];

        elseif ($this->setAddSeries() || !$this->Result):
            $this->Result = false;

        else:
            $createSerie = new Create();
            $createSerie->ExeCreate('cs_category_series', $this->Data);



            if ($createSerie->getResult()):
                $this->Result = true;
                $this->Error = ["<b>Sucesso ao cadastrar: </b>Serie adicionada com sucesso !", MSG_ACCEPT];
            endif;

        endif;
    }

    /**
     * <b>ExeDeleteSerie:</b> Método responsável por realizar a exclusão de uma serie de uma categoria do concurso.
     * 
     */
    public function ExeDeleteSerie($SeriesID) {
        $this->SeriesID = (int) $SeriesID;

        $readSerie = new Read();
        $readSerie->ExeRead('cs_category_series', "WHERE category_series_id=:serie", "serie={$this->SeriesID}");

        if (!$readSerie->getResult()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao deletar:</b> Você tentou retirar uma serie que não está mais na lista da categoria ou que já foi retirada antes !", MSG_ERROR];
        else:
            $deleteSerie = new Delete();
            $deleteSerie->ExeDelete('cs_category_series', "WHERE category_series_id=:serie", "serie={$this->SeriesID}");
            $this->Result = true;
            $this->Error = ["<b>Sucesso ao deletar:</b> A serie foi retirada com sucesso !", MSG_ACCEPT];
        endif;
    }

    /**
     * <b>getResult:</b> Metodo responsável por retornar o resultado da operação, podendo ser true ou false
     * @return bool Result
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>getError:</b>Metodo responsável por retornar a mensagem da operação em formato de array 
     * contendo 2 indices, o primeiro é a mensagem e o segundo é o tipo da mensagem
     * 
     * @return Array Error
     */
    public function getError() {
        return $this->Error;
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */

    /**
     *
     * <b>setData:</b>Metodo responsável por tratar os dados, remover codigo html e espaços desnecessários, antes de realizar o cadastro.
     */
    private function setData() {
        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);
    }

    /**
     * <b>setName:</b>Método responsável por validar o nome da serie, e verificar se o mesmo não foi cadastrado 
     */
    private function setName() {

        $Name = $this->Data['series_number'] . 'º Ano';
        $this->Data['series_name'] = $Name;



        $readSerie = new Read();
        $readSerie->ExeRead(self::Entity, "WHERE series_name=:name", "name={$Name}");

        if ($readSerie->getRowCount() >= 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar:</b> A serie informada já foi cadastrada !", MSG_ERROR];
        else:
            $this->Result = true;
        endif;
    }

    /**
     * <b>setSeries:</b>Método responsável por verifica se a serie informada já foi cadastrada anteriormente, caso seja verdadeiro não deixa proseguir o cadastro
     * Caso contrario seta o atributo Result como verdadeiro
     */
    private function setSeries() {
        $readSerie = new Read();
        $readSerie->ExeRead('cs_series', "WHERE series_name=:serie", "serie={$this->Data['series_name']}");

        if ($readSerie->getRowCount() >= 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar:</b> A serie informada já foi cadastrada !", MSG_ERROR];
        else:
            $this->Data['series_status'] = 1;
            $this->Result = true;
        endif;
    }

    /**
     *
     * <b>setAddSeries:</b>Método responsável por verificar se a serie informada, já foi adicionada na categoria, caso seja verdadeiro não deixa proseguir o cadastro
     * Caso contrario seta o atributo Result como verdadeiro
     */
    private function setAddSeries() {

        //$Condition=(isset($this->SerieCategory) ? "category_category={$this->SerieCategory} AND ": '');

        $readSerie = new Read();
        $readSerie->ExeRead('cs_category_series', "WHERE category_category=:category AND category_series=:series", "series={$this->Data['category_series']}&category={$this->Data['category_category']}");



        if ($readSerie->getRowCount() >= 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar:</b> A serie informada já foi adicionada !", MSG_ERROR];
        else:
            $this->Result = true;
        endif;
    }

    /**
     * <b>searchSubscribers:</b>Método responsável por verificar se a serie que vai ser excluida tem ou não inscrições
     * realizadas. Caso a mesma tenha inscrições realizadas, não poderá ser excluida.
     */
    private function searchSubscribers() {

        $readSubscribersSerie = new Read();
        $readSubscribersSerie->FullRead("SELECT * FROM cs_series INNER JOIN cs_concurso_subscribers ON series_id=subscribers_series WHERE series_id=:series", "series={$this->SeriesID}");

        if (!$readSubscribersSerie->getResult()):
            $this->Result = true;
        else:
            $this->Error = ["<b>Erro ao deletar:</b>A serie que você tentou excluir, possui inscrições realizadas pelas escolas, por esse motivo não pode ser deletado!", MSG_ERROR];
            $this->Result = false;
        endif;
    }

    /**
     *
     * <b>Create:</b>Método responsável por realizar o cadastro propriamente dito no banco de dados
     */
    private function Create() {

        $createSerie = new Create();
        $createSerie->ExeCreate(self::Entity, $this->Data);

        if ($createSerie->getResult()):
            $this->Result = $createSerie->getResult(); //obtem o id do registro
            $this->Error = ["<b>Sucesso ao cadastrar:</b> A serie foi adicionada com sucesso !", MSG_ACCEPT];
        endif;
    }

    /**
     *
     * <b>Update:</b>Método responsável por realizar a atualização do cadastro propriamente dito no banco de dados
     */
    private function Update() {

        $updateSerie = new Update();
        $updateSerie->ExeUpdate(self::Entity, $this->Data, "WHERE series_id=:serie", "serie={$this->SeriesID}");

        if ($updateSerie->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Sucesso ao atualizar:</b> A serie foi atualizada com sucesso!", MSG_ACCEPT];
        endif;
    }

    /**
     * <b>Delete</b>Método responsável por realizar a exclusão do cadastro propriamente dito no banco de dados
     */
    private function Delete() {

        $deleteSerie = new Delete();
        $deleteSerie->ExeDelete(self::Entity, "WHERE series_id=:id", "id={$this->SeriesID}");

        if ($deleteSerie->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Sucesso ao deletar:</b> A <b>Serie</b> foi excluida com sucesso !", MSG_ACCEPT];
        endif;
    }

}

?>