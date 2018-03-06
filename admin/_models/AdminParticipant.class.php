<?php

/**
 *  AdminParticipant.class[MODEL ADMIN]
 * Classe responsável por administrar e manter o Participantes de um concurso(alunos e professores) de modo geral. 
 * @copyright (c) 2015, Paulo Henrique 
 */
class AdminParticipant {

    private $Data;
    private $ParticipantID;
    private $Result;
    private $Error;

    /** Atributos para validação de CPF */
    private $CPF;
    private $NumberMultiplications;

    /*     * Tabela no banco de dados */

    const Entity = 'es_school_participant';

    /**
     * <b>ExeCreate:</b> Método responsável por checar, validar o cadastro de participantes
     * @param Array $Data
     */
    public function ExeCreate(array $Data) {

        $this->Data = $Data;


        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar:</b> Para cadastrar um participante preencha todos os campos !", MSG_ALERT];

        elseif (isset($this->Data['participant_name']) && strlen($this->Data['participant_name']) <= 10):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar o nome:</b> o nome informado possui 10 caracteres ou menos por favor informe o nome completo !", MSG_ALERT];

        elseif (!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->Data['participant_date_nascimento'])):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar data de nascimento:</b> A data de nascimento está em um formato invalido!", MSG_ALERT];


        elseif ($this->validateCPF() || !$this->Result):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar o CPF:</b> O CPF informado não é valido !", MSG_ERROR];

        elseif ($this->setIdade() || !$this->Result):
            $this->Result = false;
       
        elseif ($this->setParticipant() || !$this->Result):
            $this->Result = false;

        else:
            $this->Data['participant_date_registration'] = date('Y-m-d H:i:s');
            $this->setData();
            $this->Create();
        endif;
    }

    /**
     * <b>ExeUpdate:</b> Método responsável por checar, validar a atualização de participantes.
     * @param int $ParticipantID
     * @param Array $Data
     */
    public function ExeUpdate($ParticipantID, array $Data) {

        $this->ParticipantID = (int) $ParticipantID;
        $this->Data = $Data;


        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar:</b> Para cadastrar um participante preencha todos os campos !", MSG_ALERT];


        elseif ($this->validateCPF() || !$this->Result):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar o CPF:</b> O CPF informado não é valido !", MSG_ERROR];


        elseif (isset($this->Data['participant_name']) && strlen($this->Data['participant_name']) <= 10):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar o nome:</b> o nome informado possui 10 caracteres ou menos por favor informe o nome completo !", MSG_ALERT];

        elseif (!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $this->Data['participant_date_nascimento'])):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar data de nascimento:</b> A data de nascimento está em um formato invalido!", MSG_ALERT];


        elseif ($this->setIdade() || !$this->Result):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar a data de nascimento:</b> Data de nascimento invalido !", MSG_ERROR];

        elseif ($this->setParticipant() || !$this->Result):
            $this->Result = false;

        else:
            //$this->CPF=(isset($this->CPF) ? $this->CPF : $this->Data['participant_cpf']);
            $this->setData();
            $this->Update();
        endif;
    }

    /**
     * <b>ExeDelete:</b>Método responsável por realizar a exclusão de cadastro de participantes
     * @param Int $ParticipantID
     *
     */
    public function ExeDelete($ParticipantID) {

        $this->ParticipantID = (int) $ParticipantID;

        $readSubscriber = new Read();
        $readSubscriber->FullRead("SELECT * FROM `cs_concurso_subscribers` INNER JOIN cs_concurso ON subscribers_concurso=concurso_id WHERE concurso_status=1 AND subscribers_student={$this->ParticipantID} OR subscribers_teacher={$this->ParticipantID} ORDER BY concurso_date_registration DESC LIMIT 1");

        if (!$readSubscriber->getResult()):

            $readParticipant = new Read();
            $readParticipant->ExeRead(self::Entity, "WHERE participant_id=:id", "id={$this->ParticipantID}");

            if (!$readParticipant->getResult()):
                $this->Result = false;
                $this->Error = ["<b>Erro ao deletar:</b> Você tentou excluir um participante que não existe ou que já foi excluido!", MSG_ERROR];

            else:

                $deleteParticipant = new Delete();
                $deleteParticipant->ExeDelete(self::Entity, "WHERE participant_id=:id", "id={$this->ParticipantID}");

                $this->Result = true;
                $this->Error = ["<b>Sucesso ao deletar:</b>Participante excluido com sucesso !", MSG_ACCEPT];
            endif;

        else:

            $this->Result = false;
            $this->Error = ["<b>Erro ao deletar:</b> Você tentou excluir um participante que está inscrito no concurso atual, para excluir o mesmo, exclua antes a sua inscrição !", MSG_ERROR];

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
     * <b>validateCPF:</b> Metódo responsável por realizar validações checar o campo de CPF informado pelo participante.
     * Verifica se o CPF possui pelo menos 11 digitos(sem mascara), o mesmo utiliza o metodo calculateCPF (metodo que realiza as multiplicações e soma dos digitos)
     * Após o metódo calculateCPF retornar o total da soma das multplicações dos digitos o restante da validação é realiza pelo metodo validateCPF
     *
     */
    private function validateCPF() {

        if (isset($this->Data['participant_cpf']) && !strlen($this->Data['participant_cpf']) >= 11):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar CPF:</b>CPF invalido. O CPF informado possui menos que 11 números!", MSG_ERROR];

        else:

            $cpfReceiveid = preg_replace('/[^0-9]/', '', $this->Data['participant_cpf']);

            $cpfReceiveid = (string) $cpfReceiveid;

            $cpfValidate = $cpfReceiveid;

            /** Pega os 9 digitos do cpf e validação do primeiro digito verificador */
            $firstDigitCPF = substr($cpfReceiveid, 0, 9);

            $firstDigitCheck = $this->calculateCPF($firstDigitCPF, 10);
            $firstDigitCheck = ($firstDigitCheck % 11) < 2 ? 0 : 11 - ($firstDigitCheck % 11);

            /** concatena os 10  digitos com o primeiro digito verificador encontrado */
            $this->CPF.= $firstDigitCheck;

            /** Validação do segundo digito verificador */
            $secondDigitCPF = $this->calculateCPF($this->CPF, 11);

            $secondDigitCheck = ($secondDigitCPF % 11) < 2 ? 0 : 11 - ($secondDigitCPF % 11);

            /** concatena os 11  digitos com o segundo digito verificador encontrado */
            $this->CPF.=$secondDigitCheck;

            /** Verificando o CPF recebido com o CPF validado */
            if ($this->CPF == $cpfReceiveid):
                $this->Result = true;

            else:
                $this->Result = false;
                $this->Error = ["<b>Erro ao informar o CPF:</b> O CPF informado não é valido", MSG_ERROR];

            endif;





        endif;
    }

    /**
     * <b>calculateCNPJ</b> Método responsável por realizar as multiplicações e soma dos digitos e retornar o valor da soma
     * @param $CPF
     * @param NumberMultiplications (Número de multiplicações a serem realizadas)
     */
    private function calculateCPF($CPF, $NumberMultiplications) {

        $this->CPF = (string) $CPF;
        $this->NumberMultiplications = (int) $NumberMultiplications;

        $total = 0;

        for ($i = 0; $i < strlen($this->CPF); $i++):

            $total = $total + ($this->CPF[$i] * $this->NumberMultiplications);

            $this->NumberMultiplications--;

        endfor;


        return $total;
    }

    /**
     * <b>setIdade:</b>Método responsável por verificar a idade e não deixar cadastrar uma idade menor ou igual a 1 ano
     *
     */
    private function setIdade() {
        /** Convertendo a data recebida para o formato timestamp */
        $this->Data['participant_date_nascimento'] = Check::Data($this->Data['participant_date_nascimento']);

        $indiceIdade = explode('-', $this->Data['participant_date_nascimento']);
        /**
         * Ano
         * $indiceIdade[0];
         * Mês
         * $indiceIdade[1];
         * Dia e hora
         * $indiceIdade[2]; 
         */
        $anoNascimento = date('Y', strtotime($this->Data['participant_date_nascimento']));
        $anoAtual = date('Y');

        $Idade = $anoAtual - $anoNascimento;

        if ($Idade <= 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar a data de nascimento:</b>A data de nascimento informada não é valida!", MSG_ERROR];
        elseif (in_array($indiceIdade[0] > $anoAtual, $indiceIdade)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar a data de nascimento:</b>O ano informado não é válido!", MSG_ERROR];

        elseif (in_array($indiceIdade[1] > 12, $indiceIdade)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar a data de nascimento:</b>O mês informado não é válido!", MSG_ERROR];

        elseif (in_array($indiceIdade[2] > 31, $indiceIdade)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar a data de nascimento:</b>O dia informado não é válido!", MSG_ERROR];
        else:
            $this->Result = true;
        endif;
    }

    /**
     * <b>setData:</b>Metodo responsável por tratar os dados, remover codigo html e espaços desnecessários.
     */
    private function setData() {

        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);
    }

    /**
     * <b>setParticipant:</b> Método responsável por verificar se o participante informado , já não foi cadastrado anteriormente.
     */
    private function setParticipant() {
        $Condition = (!empty($this->ParticipantID) ? "participant_id !={$this->ParticipantID} AND" : '' );

        $readParticipant = new Read();
        $readParticipant->ExeRead(self::Entity, "WHERE {$Condition} participant_cpf=:cpf", "cpf={$this->Data['participant_cpf']}");

        if ($readParticipant->getResult()):
            $this->Result = false;
            $this->Error = ["<b>Error ao cadastrar:</b> Participante informado já foi cadastrado!", MSG_ERROR];
        endif;
    }

    /*     * <b>Create:</b> Método responsável por realizar o cadastro de participante propriamente dito no banco de dados */

    private function Create() {

        $createParticipant = new Create();
        $createParticipant->ExeCreate(self::Entity, $this->Data);

        if ($createParticipant->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Sucesso ao cadastrar:</b> Participante cadastrado com sucesso!", MSG_ACCEPT];
            $this->Result = $createParticipant->getResult();

        endif;
    }

    /*     * <b>Update:</b> Método responsável por realizar o a atualização do cadastro de participante propriamente dito no banco de dados */

    private function Update() {

        $updateParticipant = new Update();
        $updateParticipant->ExeUpdate(self::Entity, $this->Data, "WHERE participant_id=:participantid", "participantid={$this->ParticipantID}");

        if ($updateParticipant->getResult()):
            $this->Error = ["<b>Sucesso ao atualizar:</b> O {$this->Data['participant_name']} foi atualizado com sucesso", MSG_ACCEPT];
            $this->Result = true;
        endif;
    }

}

?>