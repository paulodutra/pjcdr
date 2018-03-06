<?php

/**
 *  AdminConcurso.class[MODEL ADMIN]
 * Classe responsável por administrar e manter o concurso de modo geral. 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminConcurso {

    private $Data;
    private $ConcursoID;
    private $Error;
    private $Result;

    /** Atributos para manipulação do envio e exclusão de arquivos */
    private $FileName;
    private $FileType;

    /*     * Tabela no banco de dados */

    const Entity = 'cs_concurso';

    /**
     * <b>ExeCreate:</b> Método responsável por validar, checar e realizar o cadastro de concursos.
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para cadastrar um concurso preencha todos os campos !", MSG_ALERT];
        else:
            $this->setTime();

            if ($this->Result):
                $this->setData();
                //Enviar a logo caso a mesma exista
                if ($this->Data['concurso_logo']):
                    $upload = new Upload();
                    $upload->Image($this->Data['concurso_logo'], $this->Data['concurso_url'], 120, 'concurso');
                endif;

                /*                 * Realizar o cadastro se a imagem do concurso for enviada ou não */
                if (isset($upload) && $upload->getResult()):
                    $this->Data['concurso_logo'] = $upload->getResult();
                    $this->Create();
                //Se não for enviado ele cadastra o artigo(post)
                else:
                    $this->Data['concurso_logo'] = null;
                    $this->Create();
                endif;

            endif;
        endif;
    }

    /**
     * <b> ExeUpdate:</b>Método responsável por validar, checar e realizar a atualização do cadastro de concursos.  
     * @param INT $ConcursoID
     * @param Array $Data
     */
    public function ExeUpdate($ConcursoID, array $Data) {
        $this->Data = $Data;
        $this->ConcursoID = (int) $ConcursoID;

        if (empty($this->Data)):
            $this->Error = ["<b>Temos campos em branco:</b> Para efetuar o cadastro preencha todos os campos"];
            $this->Result = false;

        else:

            $this->setTime();

            if ($this->Result):

                $this->setData();

                /** verifica a existencia da capa, faz o reenvio e deleta a antiga */
                if (is_array($this->Data['concurso_logo']) && ($this->Data['concurso_logo']['tmp_name'])):
                    $readCover = new Read();
                    $readCover->ExeRead(self::Entity, "WHERE concurso_id=:concursoid", "concursoid={$this->ConcursoID}");
                    $cover = '../uploads/' . $readCover->getResult()[0]['concurso_logo'];
                    if (file_exists($cover) && !is_dir($cover)):
                        unlink($cover);
                    endif;
                    $uploadCover = new Upload();
                    $uploadCover->Image($this->Data['concurso_logo'], $this->Data['concurso_url'], 242, 'concurso');

                endif;

                if (isset($uploadCover) && $uploadCover->getResult()):
                    $this->Data['concurso_logo'] = $uploadCover->getResult();
                    $this->Update();
                else:
                    unset($this->Data['concurso_logo']);
                    $this->Update();
                endif;


            endif;

        endif;
    }

    /*     * <b>ExeDelete:</b> Método responsável por realizar a exclusão do cadastro de concurso */

    public function ExeDelete($ConcursoID) {
        $this->ConcursoID = (int) $ConcursoID;



        $readSubscribersConcurso = new Read();
        $readSubscribersConcurso->FullRead("SELECT * FROM cs_concurso INNER JOIN cs_concurso_subscribers ON concurso_id=subscribers_concurso WHERE concurso_id=:concurso", "concurso={$this->ConcursoID}");

        if (!$readSubscribersConcurso->getResult()):

            $readConcurso = new Read();
            $readConcurso->ExeRead(self::Entity, "WHERE concurso_id=:concursoid", "concursoid={$this->ConcursoID}");


            if (!$readConcurso->getResult()):
                $this->Result = false;
                $this->Error = ["<b>Erro ao deletar:</b> O concurso  informado não existe ou já foi excluido anteriormente !", MSG_ALERT];
            else:

                $file = '../uploads/' . $readConcurso->getResult()[0]['concurso_logo'];
                if (file_exists($file) && !is_dir($file)):
                    unlink($file);
                endif;

                $deleteConcurso = new Delete();
                $deleteConcurso->ExeDelete(self::Entity, "WHERE concurso_id=:concursoid", "concursoid={$this->ConcursoID}");
                $this->Error = ["<b>Sucesso ao deletar:</b>Concurso excluido com sucesso!", MSG_ACCEPT];
                $this->Result = true;
            endif;

        else:
            $this->Error = ["<b>Erro ao deletar:</b>O concurso que você tentou excluir, possui inscrições realizadas pelas escolas, por esse motivo não pode ser deletado!", MSG_ERROR];
            $this->Result = false;

        endif;
    }

    /** <b>ExeStatus:</b> Método responsável por realizar a atualização do status do concurso */
    public function ExeStatus($ConcursoID, $concurso_status) {
        $this->ConcursoID = (int) $ConcursoID;
        $this->Data['concurso_status'] = (string) $concurso_status;

        //Verificar se possui pelo menos um arquivo enviado caso exista atualizar status se não não permitir a atualização do mesmo
        $readConcursoFile = new Read();
        $readConcursoFile->ExeRead('cs_concurso_file', "WHERE file_concurso=:concurso", "concurso={$this->ConcursoID}");

        if (!$readConcursoFile->getRowCount() >= 1):
            $this->Result = false;
            $this->Error = ["<b>Para atualizar status do concurso:</b> Para publicar o concurso enviei pelo menos 1 arquivo!", MSG_ERROR];
        else:

            $updateConcurso = new Update();
            $updateConcurso->ExeUpdate(self::Entity, $this->Data, "WHERE concurso_id = :concursoid", "concursoid={$this->ConcursoID}");
            if ($updateConcurso->getResult()):
                $this->Result = true;
                $this->Error = ["<b>Sucesso ao atualizar status do concurso: </b> status atualizado com sucesso!", MSG_ACCEPT];
            endif;

        endif;
    }

    /**
     * <b>ExeAddCategory:</b> Método responsável por adicionar categorias a um concurso.
     * @param ARRAY $Data
     */
    public function ExeAddCategory(array $Data) {

        $this->Data = $Data;

        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para adicionar uma categoria, preencha todos os campos !", MSG_ALERT];

        elseif ($this->setAddCategory() || !$this->Result):
            $this->Result = false;

        else:
            $createCategory = new Create();
            $createCategory->ExeCreate('cs_concurso_category', $this->Data);

            if ($createCategory->getResult()):
                $this->Result = true;
                $this->Error = ["<b>Sucesso ao cadastrar: </b>Categoria adicionada com sucesso !", MSG_ACCEPT];
            endif;



        endif;
    }

    /**
     * <b>sendFiles:</b> Método responsável por realizar o envio dos arquivos do concurso tais como regulmento formulários e etc
     */
    public function sendFiles(array $File, $ConcursoID) {

        $this->ConcursoID = (int) $ConcursoID;
        $this->Data = $File;
        $this->FileType = $this->Data['file_type'];

        $readConcurso = new Read();
        $readConcurso->ExeRead(self::Entity, "WHERE concurso_id = :id", "id={$this->ConcursoID}");

        $this->FileName = $readConcurso->getResult()[0]['concurso_name'];



        if (!$readConcurso->getResult()):

            $this->Error = ["<b>Erro ao enviar os arquivos:</b> O código {$this->ConcursoID} não foi encontrado na base de dados ! ", MSG_ERROR];
            $this->Result = false;

        else:

            if (in_array('', $this->Data)):
                $this->Result = false;
                $this->Error = ["<b>Erro ao envia o arquivo: </b> Para enviar um arquivo preencha todos os campos !", MSG_ALERT];

            else:
                $this->setFileName();
                //Fazer a função de upload
                $uploadFile = new Upload('../uploads/concurso/');
                $uploadFile->File($this->Data['file_directory'], $this->FileName, 'arquivos', 50);


                //pega o caminho do diretorio do arquivo
                $this->Data['file_directory'] = $uploadFile->getResult();

                $createFile = new Create();
                $createFile->ExeCreate('cs_concurso_file', $this->Data);

                if ($createFile->getResult()):
                    $this->Result = true;
                    $this->Error = ["<b>Sucesso ao enviar o arquivo:</b> Arquivo enviado com sucesso !", MSG_ACCEPT];

                endif;

            endif;

        endif;
    }

    /**
     * <b>deleteFile:</b>Método responsável por excluir um arquivo do concurso
     * @param INT $File
     */
    public function deleteFile($File) {

        $this->FileName = (int) $File;

        $readFile = new Read();
        $readFile->ExeRead('cs_concurso_file', "WHERE file_id=:id", "id={$this->FileName}");

        if (!$readFile->getResult()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao deletar:</b> O arquivo que informado não existe ou já foi excluido anteriormente !", MSG_ALERT];
        else:

            $file = '../uploads/concurso/' . $readFile->getResult()[0]['file_directory'];
            if (file_exists($file) && !is_dir($file)):
                unlink($file);
            endif;

            $deleteFile = new Delete();
            $deleteFile->ExeDelete('cs_concurso_file', "WHERE file_id=:id", "id={$this->FileName}");

            if ($deleteFile->getResult()):
                $this->Error = ["<b>Sucesso ao Deletar:</b>O arquivo foi excluido com sucesso!", MSG_ACCEPT];
                $this->Result = true;
            endif;

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
     * <b>setData:</b>Metodo responsável por tratar os dados, remover codigo html e espaços  exceto do indice description, 
     * Validar a url do concurso e validar a data para timestamp antes de cadastrar
     */
    private function setData() {
        $cover = $this->Data['concurso_logo'];
        $description = $this->Data['concurso_description'];

        unset($this->Data['concurso_logo'], $this->Data['concurso_description']);

        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);

        $this->setName();
        /*         * Convertendo a data para o formato timestamp */
        $this->Data['concurso_date_registration'] = date('Y-m-d H:i:s');

        /**
         * Armazenando novamente a imagem e o conteudo armazenado , antes de limpar o array Data
         */
        $this->Data['concurso_logo'] = $cover;
        $this->Data['concurso_description'] = $description;
        $this->Data['concurso_url'] = Check::Url($this->Data['concurso_name']);
    }

    /**
     * <b>setName:</b>: Método responsável por validar o nome do concurso de acordo com o seu tipo e o numero de vezes que o mesmo aconteceu no ano
     */
    private function setName() {


        //$Condition = (isset($this->ConcursoID) ? "concurso_id != {$this->ConcursoID} AND" : '' );



        if (!empty($this->ConcursoID)):
            $readConcurso = new Read();
            $readConcurso->ExeRead(self::Entity, "WHERE concurso_id = :id", "id={$this->ConcursoID}");

            $Year = $readConcurso->getResult()[0]['concurso_start'];
            $Year = date('Y');

            $readType = new Read();
            $readType->ExeRead('cs_concurso_type', "WHERE type_id =:type", "type={$this->Data['concurso_type']}");

            $this->Data['concurso_name'] = $readConcurso->getResult()[0]['concurso_name'];

        else:
            $readConcurso = new Read();
            $readConcurso->ExeRead(self::Entity, "WHERE concurso_type=:type","type={$this->Data['concurso_type']}");
            
            $Year = $this->Data['concurso_start'];
            $Year = date('Y');
            
            $readType = new Read();
            $readType->ExeRead('cs_concurso_type', "WHERE type_id =:type", "type={$this->Data['concurso_type']}");
            
            $Number = count($readConcurso->getResult());
            $Number = $Number + 1;
            
            $this->Data['concurso_name'] = $Number . '° - Concurso de ' . $readType->getResult()[0]['type_name'] . ' ' . $Year;

        endif;

      
    }

    /*     * <b>setTime:</b> Método responsável por validar as datas informadas como prazo do concurso */

    private function setTime() {

        $this->Data['concurso_start'] = Check::Data($this->Data['concurso_start']);
        $this->Data['concurso_end'] = Check::Data($this->Data['concurso_end']);

        $dateStart = $this->Data['concurso_start'];
        $dateEnd = $this->Data['concurso_end'];



        $dateStart = new DateTime($this->Data['concurso_start']);
        $dateEnd = new DateTime($this->Data['concurso_end']);

        $dateInterval = $dateStart->diff($dateEnd);

        if ($dateInterval->days <= 0):
            $this->Error = ["<b>Erro ao informar as datas:</b> A <b>data de inicio</b> está igual a <b>data de termino</b>", MSG_ERROR];
            $this->Result = false;
            return false;

        elseif ($dateStart > $dateEnd):
            $this->Error = ["<b>Erro ao informar as datas:</b> A data de inicio está maior que a data de termino", MSG_ERROR];
            $this->Result = false;
            return false;

        else:
            $this->Result = true;
            return true;
        endif;
    }

    /** <b>setFileName:</b> Método responsável por validar o nome do arquivo a ser enviado de acordo com o seu tipo */
    private function setFileName() {

        $readType = new Read();
        $readType->ExeRead('cs_concurso_file_type', "WHERE file_type_id=:id", "id={$this->FileType}");

        $this->FileType = $readType->getResult()[0]['file_type_name'];
        /** Indice[0] aplication indeice[1] = extensão exemplo: pdf, txt, doc, dox , xlsx */
        $Extension = (explode('/', $this->Data['file_directory']['type']));



        $this->FileName = $this->FileName . '-' . $this->FileType;
        $this->FileName = Check::Url($this->FileName);
        $this->FileName = $this->FileName . '.' . $Extension[1];

        $readFileName = new Read();
        $readFileName->ExeRead("cs_concurso_file", "WHERE file_directory=:file", "file={$this->FileName}");

        if ($readFileName->getResult()):
            $Number = count($readFileName->getRowCount());
            $Number = $Number + 1;
            $this->FileName = $this->FileName . '-' . $Number;
        endif;
    }

    /**
     * <b>setAddCategory:</b>Método responsável por verificar se uma categoria a ser adicionada, já não foi adicionada antes, caso tenha sido retorna Falso (false) caso 
     * contrário retorna Verdadeiro(true)
     */
    private function setAddCategory() {

        $readCategory = new Read();
        $readCategory->ExeRead("cs_concurso_category", "WHERE concurso_category_concurso=:concurso AND concurso_category_category=:category", "concurso={$this->Data['concurso_category_concurso']}&category={$this->Data['concurso_category_category']}");

        if ($readCategory->getRowCount() >= 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar:</b> A Categoria informada já foi adicionada !", MSG_ERROR];
        else:
            $this->Result = true;
        endif;
    }

    /*     * <b>Create:</b> Método responsável por realizar o cadastro do concurso propriamente dito no banco de dados */

    private function Create() {
        $createConcurso = new Create();
        $createConcurso->ExeCreate(self::Entity, $this->Data);

        if ($createConcurso->getResult()):

            $this->Result = $createConcurso->getResult();
            $this->Error = ["<b>Sucesso ao cadastrar:</b> O {$this->Data['concurso_name']} foi cadastrado com sucesso!", MSG_ACCEPT];

        endif;
    }

    /*     * <b>Update:</b> Método responsável por realizar a atualização do cadastro do concurso propriamente dito no banco de dados */

    private function Update() {
        $updateConcurso = new Update();
        $updateConcurso->ExeUpdate(self::Entity, $this->Data, "WHERE concurso_id=:concursoid", "concursoid={$this->ConcursoID}");

        if ($updateConcurso->getResult()):
            $this->Error = ["<b>Sucesso ao atualizar:</b> O {$this->Data['concurso_name']} foi atualizado com sucesso", MSG_ACCEPT];
            $this->Result = true;
        endif;
    }

}

?>