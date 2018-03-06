<?php

/**
 *  AdminSubscribers.class[MODEL ADMIN]
 * Classe responsável por administrar e manter as inscrições de um concurso de modo geral. 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminSubscribers {

    private $Data;
    private $SubscribersID;
    private $Result;
    private $Error;

    /** Atributo(s) para manipulação do envio da redação */
    private $RedactionName;

    /*     * Tabela no banco de dados */

    const Entity = 'cs_concurso_subscribers';

    /**
     * <b>ExeCreate:</b> Método responsável por checar, validar o cadastro de inscrições no concurso de redação
     * @param Array $Data
     */
    public function ExeCreate(array $Data) {

        $this->Data = $Data;
        $Redaction = $this->Data['subscribers_redaction']['tmp_name'];

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao Realizar inscrição:</b> Para realizar uma inscrição preencha todos os campos !", MSG_ALERT];

        elseif ($this->setSubscriber() || !$this->Result):
            $this->Result = false;

        elseif ($this->setStudent() || !$this->Result):
            $this->Result = false;

        elseif ($this->sendRedaction() || !$this->Result):
            $this->Result = false;
        //$this->Error=["<b>Erro ao Realizar inscrição: </b> Só é permitido uma inscrição por Serie!", MSG_ERROR];		
        //$this->Error=["<b>Erro ao Realizar inscrição: </b> Só é permitido uma inscrição por Serie!", MSG_ERROR];	
        elseif ($this->setDeadline() || !$this->Result):
            $this->Result = false;
        else:
            $this->setData();
            $this->Create();

        endif;
    }

    /**
     * <b>ExeUpdate:</b> Método responsável por checar, validar o Atualizar o cadastro de inscrições no concurso de redação
     * @param Array $Data
     * @param INT $SubscribersID
     */
    public function ExeUpdate($SubscribersID, array $Data) {
        $this->SubscribersID = (int) $SubscribersID;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao Realizar inscrição:</b> Para realizar uma inscrição preencha todos os campos !", MSG_ALERT];

        elseif ($this->setSubscriber() || !$this->Result):
            $this->Result = false;


        elseif ($this->setStudent() || !$this->Result):
            $this->Result = false;

        elseif ($this->setDeadline() || !$this->Result):
            $this->Result = false;

        else:
            $this->setData();
            if ($this->Data['subscribers_redaction']):

                $readRedaction = new Read();
                $readRedaction->ExeRead(self::Entity, "WHERE subscribers_id=:id", "id={$this->SubscribersID}");


                $redaction = '../uploads/concurso/' . $readRedaction->getResult()[0]['subscribers_redaction'];
                if (is_file($redaction) && !is_dir($redaction)):
                    unlink($redaction);
                endif;
                $this->sendRedaction();
                if ($this->Result):
                    $this->Update();
                endif;
            endif;

        endif;
    }

    /**
     * <b>ExeDelete:</b>Método responsável por realizar a exclusão de cadastro de inscrições no concurso de redação
     * @param Int $ParticipantID
     *
     */
    public function ExeDelete($SubscribersID) {

        $this->SubscribersID = (int) $SubscribersID;

        $readSubscriber = new Read();
        $readSubscriber->ExeRead(self::Entity, "WHERE subscribers_id=:id", "id={$this->SubscribersID}");

        if (!$readSubscriber->getResult()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao deletar:</b> Você tentou retirar uma inscrição que não existe ou que já foi excluida anteriormente !", MSG_ERROR];
        else:
            $deleteSubscriber = new Delete();
            $deleteSubscriber->ExeDelete(self::Entity, "WHERE subscribers_id=:id", "id={$this->SubscribersID}");

            $this->Result = true;
            $this->Error = ["<b>Sucesso ao deletar:</b> A inscrição foi excluida com sucesso !", MSG_ACCEPT];

        endif;
    }

    /**
     * <b>sendRedaction:</b>Método responsável por realizar o envio da redação e renoamear a mesma
     *
     */
    public function sendRedaction() {

        $readSubscriber = new Read();
        $readSubscriber->ExeRead('es_school_participant', "WHERE participant_id=:id", "id={$this->Data['subscribers_student']}");


        if (!$readSubscriber->getResult()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao enviar  a redação:</b> O aluno informado não foi encontrado em nossa base de dados!", MSG_ERROR];

        elseif (!$this->Data['subscribers_redaction']['error'] == 0):

            $this->Result = false;
            $this->Error = ["<b>Erro ao enviar a redação:</b> Por favor informe o arquivo a ser enviado, caso o não envio do arquivo persista entre em contato com a equipe <b>DPU NAS ESCOLAS</b>!", MSG_ERROR];

        else:

            $readNameConcurso = new Read();
            $readNameConcurso->ExeRead('cs_concurso', "WHERE concurso_id=:id", "id={$this->Data['subscribers_concurso']}");

            $Name = $readSubscriber->getResult()[0]['participant_name'];
            $CPF = $readSubscriber->getResult()[0]['participant_cpf'];
            $Concurso = $readNameConcurso->getResult()[0]['concurso_name'];

            $this->RedactionName = $Name . '-' . $CPF . '-' . $Concurso;

            $uploadRedaction = new Upload('../uploads/concurso/');
            $uploadRedaction->File($this->Data['subscribers_redaction'], $this->RedactionName, 'redacoes', 50);

            if ($uploadRedaction->getResult()):
                $this->Result = true;
                $this->Data['subscribers_redaction'] = $uploadRedaction->getResult();
            else:
                $this->Result = false;
                $this->Error = ["<b>Erro ao enviar a redação:</b> Ocorreu um erro no envio do arquivo !", MSG_ERROR];
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
     * <b>setSubscriber:</b>Método responsável por verificar se a inscrição no concurso já não foi realizada anteriomente
     */
    private function setSubscriber() {

        $Condition = (isset($this->SubscribersID) ? "subscribers_id !={$this->SubscribersID} AND" : '');



        $readSubscriber = new Read();
        $readSubscriber->ExeRead(self::Entity, "WHERE {$Condition} subscribers_concurso=:concurso AND subscribers_school=:school AND subscribers_series=:series", "concurso={$this->Data['subscribers_concurso']}&school={$this->Data['subscribers_school']}&series={$this->Data['subscribers_series']}");



        if ($readSubscriber->getRowCount() >= 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao Realizar inscrição: </b> Só é permitido uma inscrição por Serie!", MSG_ERROR];
        else:
            $this->Result = true;
        endif;
    }

    /**
     * <b>setStudent:</b>Método responsável por verificar se a inscrição do estudante no concurso já não foi realizada anteriomente
     */
    private function setStudent() {

        $Condition = (isset($this->SubscribersID) ? "subscribers_id !={$this->SubscribersID} AND" : '');

        $readStudent = new Read();
        $readStudent->ExeRead(self::Entity, "WHERE {$Condition} subscribers_concurso=:concurso AND subscribers_student=:student", "concurso={$this->Data['subscribers_concurso']}&student={$this->Data['subscribers_student']}");


        if ($readStudent->getRowCount() >= 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao Realizar inscrição: </b> O Estudante informado já foi inscrito no concurso!", MSG_ERROR];

        else:
            $this->Result = true;
        endif;
    }

    /**
     * <b>setData:</b>Metodo responsável por tratar os dados, remover codigo html e espaços desnecessários. exceto no indice $this->Data['subscribers_redaction'];
     */
    private function setData() {
        $Redacao = $this->Data['subscribers_redaction'];

        unset($this->Data['subscribers_redaction']);

        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);

        $this->Data['subscribers_redaction'] = $Redacao;
    }

    /**
     * <b>setDeadline:</b> Método responsável por verificar se a inscrição esta sendo dentro do prazo ou não e realizar as ações necessárias
     */
    private function setDeadline() {

        $readConcurso = new Read();
        $readConcurso->ExeRead('cs_concurso', "WHERE concurso_id=:id", "id={$this->Data['subscribers_concurso']}");


        if ($readConcurso->getResult()):

            $dateStart = new DateTime(date('Y/m/d H:i:s', strtotime($readConcurso->getResult()[0]['concurso_start'])));
            $dateEnd = new DateTime(date('Y/m/d H:i:s', strtotime($readConcurso->getResult()[0]['concurso_end'])));


            $dateSubscriber = new DateTime(date('Y/m/d H:i:s'));
            
          

            if ($dateSubscriber > $dateEnd ):

                $dateStart = date('d/m/Y H:i:s', strtotime($readConcurso->getResult()[0]['concurso_start']));
                $dateEnd = date('d/m/Y H:i:s', strtotime($readConcurso->getResult()[0]['concurso_end']));

                $this->Error = ["<b>Erro ao realizar inscrição</b>: O prazo de inscrição era de <b>{$dateStart} a {$dateEnd} </b>", MSG_ERROR];
                $this->Result = false;
                
            endif;    

            if ($dateSubscriber < $dateStart):


                $dateStart = date('d/m/Y H:i:s', strtotime($readConcurso->getResult()[0]['concurso_start']));
                $dateEnd = date('d/m/Y H:i:s', strtotime($readConcurso->getResult()[0]['concurso_end']));


                $this->Error = ["<b>Erro ao realizar inscrição</b>: O prazo de inscrição iniciará em <b>{$dateStart} e ira até {$dateEnd}</b>", MSG_ERROR];
                $this->Result = false;

            

            endif;

        else:
            $this->Error = ["<b>Erro ao realizar inscrição</b>: O Concurso informado, não foi localizado em nossa base de dados !", MSG_ERROR];
            $this->Result = false;

        endif;
    }

    /*     * <b>Create:</b> Método responsável por realizar o cadastro de inscrição propriamente dito no banco de dados */

    private function Create() {

        $createSubscriber = new Create();
        $createSubscriber->ExeCreate(self::Entity, $this->Data);

        if ($createSubscriber->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Sucesso ao cadastrar:</b> Inscrição cadastrada com sucesso!", MSG_ACCEPT];
            $this->Result = $createSubscriber->getResult();

        endif;
    }

    /*     * <b>Update:</b> Método responsável por realizar o a atualização docadastro de inscrição propriamente dito no banco de dados */

    private function Update() {

        $updateSubscriber = new Update();
        $updateSubscriber->ExeUpdate(self::Entity, $this->Data, "WHERE subscribers_id=:id", "id={$this->SubscribersID}");

        if ($updateSubscriber->getResult()):
            $this->Error = ["<b>Sucesso ao atualizar:</b> Inscrição atualizada com sucesso!!", MSG_ACCEPT];
            $this->Result = true;
        endif;
    }

}
