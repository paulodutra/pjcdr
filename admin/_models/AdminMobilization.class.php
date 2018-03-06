<?php

/**
 *  AdminMobilization.class[MODEL ADMIN]
 * Classe responsável por administrar e manter o escolas de modo geral. 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminMobilization {

    private $Data;
    private $MobilizationID;
    private $Result;
    private $Error;

    /** Atributos para manipulação do envio e exclusão de arquivos */
    private $FileName;

    /*     * Tabela no banco de dados */

    const Entity = 'es_school_mobilization';

    /**
     * <b>ExeCreate:</b> Método responsável por validar, checar e realizar o cadastro de mobilizações.
     * @param Array $Data 
     */
    public function ExeCreate(array $Data) {

        $this->Data = $Data;

        if (in_array('', $this->Data)):

            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para cadastrar uma mobilização preencha todos os campos !", MSG_ALERT];

        elseif (!is_numeric($this->Data['mobilization_number_teachers']) || !is_numeric($this->Data['mobilization_number_student']) || !is_numeric($this->Data['mobilization_number_redaction'])):

            $this->Error = ["<b>Erro ao Informar quantitativo de mobilizados :</b>Para realizar o cadastro informe apenas números no quantitativo de mobilizados! ", MSG_ERROR];
            $this->Result = false;

        elseif ($this->Data['mobilization_number_teachers'] < 1 || $this->Data['mobilization_number_student'] < 1 || $this->Data['mobilization_number_redaction'] < 1):

            $this->Error = ["<b>Erro ao Informar quantitativo de mobilizados :</b> Números negativos não são permitidos! ", MSG_ERROR];
            $this->Result = false;

        elseif ($this->setMobilization() || !$this->Result):
            $this->Result = false;


        else:
            $this->setData();
            $this->Create();


        endif;
    }

    /**
     * <b> ExeUpdate:</b>Método responsável por validar, checar e realizar a atualização do cadastro de mobilizações.
     * @param INT $MobilizationID
     * @param Array $Data 
     */
    public function ExeUpdate($MobilizationID, array $Data) {

        $this->MobilizationID = (int) $MobilizationID;
        $this->Data = $Data;

        if (in_array('', $this->Data)):

            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para cadastrar uma mobilização preencha todos os campos !", MSG_ALERT];

        elseif (!is_numeric($this->Data['mobilization_number_teachers']) || !is_numeric($this->Data['mobilization_number_student']) || !is_numeric($this->Data['mobilization_number_redaction'])):

            $this->Error = ["<b>Erro ao Informar quantitativo de mobilizados :</b>Para realizar o cadastro informe apenas números no quantitativo de mobilizados! ", MSG_ERROR];
            $this->Result = false;

        elseif ($this->Data['mobilization_number_teachers'] < 1 || $this->Data['mobilization_number_student'] < 1 || $this->Data['mobilization_number_redaction'] < 1):

            $this->Error = ["<b>Erro ao Informar quantitativo de mobilizados :</b> Números negativos não são permitidos! ", MSG_ERROR];
            $this->Result = false;

        elseif ($this->setMobilization() || !$this->Result):
            $this->Result = false;


        else:
            $this->setData();
            $this->Update();


        endif;
    }

    /**
     * <b>sendFiles:</b> Método responsávl por realizar verificação do cadastro de mobilização e o envio do arquivo de mobilização 
     * @param Array $Data 
     */
    public function sendFiles(array $Data) {
        $this->Data = $Data;

        //Verifica se a dados em branco
        if ($this->searchMobilization() || !$this->Result):
            $this->Result = false;

        elseif (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao envia o arquivo: </b> Para enviar um arquivo preencha todos os campos !", MSG_ALERT];
        //verifica se o arquivo é do tipo multimidia MP3 ou MP4
        elseif ($this->Data['mobilization_file_type'] == 1 || $this->Data['mobilization_file_type'] == 2):

            $this->fileMultimedia();

        //verifica se o arquivo e do tipo imagem( ou documento doc, docx, txt ou pdf
        elseif ($this->Data['mobilization_file_type'] == 3 || $this->Data['mobilization_file_type'] == 4 || $this->Data['mobilization_file_type'] == 5 || $this->Data['mobilization_file_type'] == 6):

            $this->fileDocument();

        endif;
    }

    /**
     * <b>deleteFiles:</b> Método responsável por excluir os arquivos de mobilização
     * @param INT $MobilizationID
     */
    public function deleteFiles($MobilizationID) {

        $this->MobilizationID = (int) $MobilizationID;

        $readMobilization = new Read();
        $readMobilization->ExeRead('es_school_mobilization_file', "WHERE mobilization_file_id=:id", "id={$this->MobilizationID}");

        if (!$readMobilization->getResult()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao deletar:</b> O arquivo que informado não existe ou já foi excluido anteriormente !", MSG_ALERT];

        else:
            $file = '../uploads/concurso/' . $readMobilization->getResult()[0]['mobilization_file_directory'];
            if (file_exists($file) && !is_dir($file)):
                unlink($file);
            endif;
            $deleteFile = new Delete();
            $deleteFile->ExeDelete('es_school_mobilization_file', "WHERE mobilization_file_id=:id", "id={$this->MobilizationID}");
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
     * <b>setData:</b>Metodo responsável por tratar os dados, remover codigo html e espaços desnecessários.
     */
    private function setData() {

        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);
    }

    /**
     * <b>setMobilization:</b> Método responsável por verificar se os dados de mobilização já não foi cadastrado anteriormente.
     */
    private function setMobilization() {

        $Condition = (isset($this->MobilizationID) ? "mobilization_id !={$this->MobilizationID} AND" : '');

        $readMobilization = new Read();
        $readMobilization->ExeRead(self::Entity, "WHERE {$Condition} mobilization_concurso=:concurso AND mobilization_school=:school", "concurso={$this->Data['mobilization_concurso']}&school={$this->Data['mobilization_school']}");

        if ($readMobilization->getRowCount() >= 1):

            $this->Result = false;

            $this->Error = ["<b>Erro ao cadastrar:</b>Escola informada já realizou o cadastro de sua mobilização! Caso deseje alterar alguma informação favor editar o cadastro de mobilização!", MSG_ERROR];
        else:
            $this->Result = true;
        endif;
    }

    /**
     * <b>setFile:</b>Método responsável por verificar se o arquivo de mobilização já não foi cadastrado anteriormente.
     */
    private function setFile() {

        $readFile = new Read();
        $readFile->ExeRead('es_school_mobilization_file', "WHERE mobilization_file_school=:school AND mobilization_file_concurso=:concurso AND mobilization_file_name=:file", "school={$this->Data['mobilization_file_school']}&concurso={$this->Data['mobilization_file_concurso']}&file={$this->Data['mobilization_file_directory']['name']}");

        if ($readFile->getRowCount() >= 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao enviar o arquivo:</b> O arquivo informado já foi enviado!", MSG_ERROR];
        else:
            $this->Result = true;
        endif;
    }

    /**
     * <b>setFileName:</b>Método responsável por renoamear o arquivo enviado de acordo com os criterios abaixo:
     *
     * [Nome da escola] - [CNPJ da escola] - [Nome do concurso]
     *
     */
    private function setFileName() {
        $readSchool = new Read();
        $readSchool->ExeRead('es_school', "WHERE school_id=:id", "id={$this->Data['mobilization_file_school']}");

        $readConcurso = new Read();
        $readConcurso->ExeRead('cs_concurso', "WHERE concurso_id=:id", "id={$this->Data['mobilization_file_concurso']}");

        $ConcursoName = $readConcurso->getResult()[0]['concurso_name'];

        $SchoolName = $readSchool->getResult()[0]['school_name'];
        $SchoolCNPJ = $readSchool->getResult()[0]['school_cnpj'];

        $this->FileName = $SchoolName . '-' . $SchoolCNPJ . '-' . $ConcursoName;

        $this->Data['mobilization_file_name'] = $this->Data['mobilization_file_directory']['name'];
    }

    /**
     * <b>fileMultimedia:</b>Método responsável por verificar se o arquivo é multimidia e se esta no formato aceito. E delegar ao 
     * respectivo método o seu upload.
     */
    private function fileMultimedia() {

        $FileAccept = [
            'audio/mp3', //arquivos MP3
            'video/mp4' //arquivos MP4 
        ];

        if (!in_array($this->Data['mobilization_file_directory']['type'], $FileAccept)):
            $this->Result = false;
            $this->Error = ['<b>Tipo de arquivo não aceito.</b> Envie arquivos do tipo: Audio MP3 ou Vídeo MP4 !', MSG_ERROR];
        elseif ($this->setFile() || !$this->Result):
            $this->Result = false;
        else:
            $this->setFileName();
            $uploadMobilization = new Upload('../uploads/concurso/');
            $uploadMobilization->Media($this->Data['mobilization_file_directory'], $this->FileName, 'mobilizacoes', 200);

            $this->Data['mobilization_file_directory'] = $uploadMobilization->getResult();
            $this->createFile();
        endif;
    }

    /**
     * <b>fileDocument:</b>Método responsável por verificar se o arquivo é do tipo documento ou imagem e se esta no formato aceito.
     * E delegar a classe "Upload" e a mesma delega ao método responsável o upload do arquivo.
     */
    private function fileDocument() {

        $FileAccept = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', //arquivos doc, docx (word)
            'application/pdf', //arquivos pdf
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', //arquivos (excel)
            'text/plain', //arquivos txt   
            'image/jpeg', //Imagem jpeg
            'image/jpg', //Imagem jpg
            'image/png'//Imagem png
        ];

        if (!in_array($this->Data['mobilization_file_directory']['type'], $FileAccept)):
            $this->Result = false;
            $this->Error = ['<b>Tipo de arquivo não aceito.</b> Envie arquivos do tipo: .PDF, . DOC, .DOCX, .XLS,  .XLSX, .TXT .JPEG, .JPG, OU .PNG  !', MSG_ERROR];
        elseif ($this->setFile() || !$this->Result):
            $this->Result = false;
        else:
            $this->setFileName();
            $uploadMobilization = new Upload('../uploads/concurso/');
            $uploadMobilization->File($this->Data['mobilization_file_directory'], $this->FileName, 'mobilizacoes', 50);
            
            $this->Data['mobilization_file_directory'] = $uploadMobilization->getResult();
            $this->createFile();
        endif;
    }

    /**
     * <b>searchMobilization:</b>Método responsável por verificar se a escola já cadastrou a mobilização.
     * Caso a mesma não tenha cadastrado o $Result será atribuido como FALSE e a não poderá enviar o arquivo
     * de mobilização
     */
    private function searchMobilization() {
        /* Realiza a validação,verificando se a escola realizou o cadastro de mobilização antes do envio do arquivo */
        $readSchool = new Read();
        $readSchool->ExeRead('es_school_mobilization', "WHERE mobilization_school=:school", "school={$this->Data['mobilization_file_school']}");

        if ($readSchool->getResult()):
            $this->Result = true;
        else:
            $this->Result = false;
            $this->Error = ["<b>Error ao enviar o arquivo:</b>Realize primeiro o cadastro dos dados de mobilizaçao, para depois enviar os arquivos !", MSG_ERROR];
        endif;
    }

    /** <b>Create:</b> Método responsável por realizar o cadastro dos dados de mobilização propriamente dito no banco de dados */

    private function Create() {
        $createMobilization = new Create();
        $createMobilization->ExeCreate(self::Entity, $this->Data);

        if ($createMobilization->getResult()):
            $this->Error = ["<b>Sucesso ao cadastrar:</b>Mobilização foi cadastrado com sucesso!", MSG_ACCEPT];
            $this->Result = $createMobilization->getResult();

        endif;
    }
    
    /**
     * <b>createFile:</b>Método responsável por realizar o cadastro dos dados do arquivo enviado para mobilização propriamente dito
     * no banco de dados.
     */

    private function createFile() {

        $createFile = new Create();
        $createFile->ExeCreate('es_school_mobilization_file', $this->Data);

        if ($createFile->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Sucesso ao enviar o arquivo:</b> Arquivo enviado com sucesso !", MSG_ACCEPT];
        endif;
    }

    /** <b>Update:</b> Método responsável por realizar a atualização do cadastro dos dados de mobilização propriamente dito no banco de dados */

    private function Update() {

        $updateMobilization = new Update();
        $updateMobilization->ExeUpdate(self::Entity, $this->Data, "WHERE mobilization_id=:id", "id={$this->MobilizationID}");

        if ($updateMobilization->getResult()):
            $this->Error = ["<b>Sucesso ao atualizar:</b> Inscrição atualizada com sucesso!!", MSG_ACCEPT];
            $this->Result = true;
        endif;
    }

}
