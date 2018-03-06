<?php

/**
 *  AdminBank.class[MODEL ADMIN]
 * Classe responsável por administrar e manter os dados bancários da escola de modo geral. 
 * @copyright (c) 2016, Paulo Henrique 
 */
Class AdminBank {

    private $Data;
    private $BankID;
    private $Result;
    private $Error;

    /** Atributos para validação de Conta Bancaria */
    private $AgencyOrAccount;
    private $Agency;
    private $Account;
    private $NumberMultiplications;

    /** Tabela no banco de dados */
    const Entity = 'es_school_data_bank';

    /**
     * <b>ExeCreate:</b> Método responsável por validar, checar e realizar o cadastro de conta bancária.
     * @param array $Data
     */
    public function ExeCreate(array $Data) {

        $this->Data = $Data;



        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para cadastrar uma conta bancária preencha todos os campos !", MSG_ALERT];

        elseif ($this->setBank() || !$this->Result):
            $this->Result = false;

        elseif ($this->Data['data_bank_bank'] == 1 && $this->validateBB() && !$this->Result):
            // $this->validateBB();
            $this->Result = false;
        elseif ($this->Data['data_bank_bank'] == 2 && $this->validateCEF() && !$this->Result):
            //$this->validateCEF();
            $this->Result = false;

        else:
            $this->setData();
            $this->Create();
        endif;
    }

    /**
     * <b>ExeCreate:</b> Método responsável por validar, checar e realizar o cadastro de conta bancária.
     * @param array $Data
     */
    public function ExeUpdate($BankID, array $Data) {

        $this->BankID = (int) $BankID;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b> Para cadastrar uma conta bancária preencha todos os campos !", MSG_ALERT];

        elseif ($this->setBank() || !$this->Result):
            $this->Result = false;

        elseif ($this->Data['data_bank_bank'] == 1 && $this->validateBB() && !$this->Result):
            // $this->validateBB();
            $this->Result = false;
        elseif ($this->Data['data_bank_bank'] == 2 && $this->validateCEF() && !$this->Result):
            //$this->validateCEF();
            $this->Result = false;

        else:
            $this->setData();
            $this->Update();
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
     * <b>setBank:</b>Método responsável por verificar se os dados de conta bancária já não foi cadastrado anteriormente.
     */
    private function setBank() {

        $Condition = (isset($this->BankID) ? "data_bank_id !={$this->BankID} AND" : '' );

        $readBank = new Read();
        $readBank->ExeRead(self::Entity, "WHERE {$Condition} data_bank_school=:school OR data_bank_account=:account", "school={$this->Data['data_bank_school']}&account={$this->Data['data_bank_account']}");


        if ($readBank->getRowCount() >= 1):
            $this->Result = false;
            $this->Error = ["<b>Erro ao cadastrar: </b>Escola informada já realizou o cadastro de sua conta bancária, Ou esse número de conta bancária já foi cadastrado!", MSG_ERROR];
        else:
            $this->Result = true;
        endif;
    }

    /**
     * <b>validateBB:</b> Método responsável por validar contas bancárias, cujo o banco seja Banco do Brasil
     */
    private function validateBB() {

        if (isset($this->Data['data_bank_agency']) && !strlen($this->Data['data_bank_agency']) >= 6):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar a agência:</b>A agência informada deve possui menos que 6 caracteres!", MSG_ERROR];

        elseif (isset($this->Data['data_bank_account']) && !strlen($this->Data['data_bank_account']) >= 10):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar a conta:</b>A conta informada deve possui menos que 10 caracteres!", MSG_ERROR];

        else:

            /** Trata os dados  da agência */
            $agencyReceiveid = preg_replace('/[^a-zA-Z0-9_]/', '', $this->Data['data_bank_agency']);
            $agencyReceiveid = (string) $agencyReceiveid;

            /** Trata os dados  da conta */
            $accountReceiveid = preg_replace('/[^a-zA-Z0-9_]/', '', $this->Data['data_bank_account']);
            $accountReceiveid = (string) $accountReceiveid;

            /** Atribui os dados tratados, para variaveis que sera utilizada na validação */
            $agencyValidate = $agencyReceiveid;
            $accountValidate = $accountReceiveid;

            /** Pega os 4 digitos da agência */
            $fourDigitAgency = substr($agencyValidate, 0, 4);

            /** Delega ao metodo que ira realizar o calculo, passando os 4
             * digitos da agência e o numero que ira começar a multiplicação
             */
            $digitAgencyCheck = $this->calculate($fourDigitAgency, 5);

            /** Mod - Resto da divisão por 11 */
            $restAgency = ($digitAgencyCheck % 11);

            /** Verifica o valor do resto da divisão e faz atribuições de acordo com o resultado obtido */
            //$digitAgencyCheck = $restAgency == 10 ? 'X' : ($restAgency == 11) ? '0' : 11 - $restAgency;

            $digitAgencyCheck = $this->checkDigitBB($restAgency);

            /** Concatena os 4 digitos da agência com o digito encontrado no calculo */
            $this->Agency = $fourDigitAgency . $digitAgencyCheck;


            /** Pega os 8 digitos da agência */
            $eightDigitAccount = substr($accountValidate, 0, 8);


            /** Delega ao metodo que ira realizar o calculo, passando os 8
             * digitos da agência e o numero que ira começar a multiplicação
             */
            $digitAccountCheck = $this->calculate($eightDigitAccount, 9);

            /** Mod - Resto da divisão por 11 */
            $restAccount = ($digitAccountCheck % 11);

            /** Verifica o valor do resto da divisão e faz atribuições de acordo com o resultado obtido */
            $digitAccountCheck = $this->checkDigitBB($restAccount);


            /** Concatena os 8 digitos da agência com o digito encontrado no calculo */
            $this->Account = $eightDigitAccount . $digitAccountCheck;

            /** Transformar a agencia e a conta juntamente com os DV encontrados, para case insentive
             * levando em consideração que o usuário pode informar <b>X</b> ou <b>x</b> nos DV de uma ou ambas.
             * Caso a comparação da função strcasecmp retorne 0 as strings são iguais
             */
            $agencyInsentive = strcasecmp($this->Agency, $agencyReceiveid);
            $accountInsentive = strcasecmp($this->Account, $accountReceiveid);

            if (($agencyInsentive == 0) && ($accountInsentive == 0)):
                $this->Result = true;
                $this->Error = ["<b>Sucesso:</b> Sucesso ao validar a conta bancária.", MSG_ACCEPT];
            else:
                $this->Result = false;
                $this->Error = ["<b>Erro:</b> Erro ao validar a conta bancária.", MSG_ERROR];
            endif;



        endif;
    }

    /**
     * <b>checkDigitBB</b>Método responsável por verificar o valor do digito verificador
     * encontrado, e realizar as atribuições corretas, de acordo com as regras de validação de conta
     * do Banco do Brasil. Deve receber como parametro o resto encontrado no calculo
     * @param String $Rest
     * @return String $Rest
     */
    private function checkDigitBB($Rest) {


        $Rest = (string) $Rest;

        $Rest = 11 - $Rest;

        if ($Rest == "10"):

            $Rest = "X";

        elseif ($Rest == "11"):

            $Rest = 0;

        else:

            $Rest = $Rest;
        endif;

        return $Rest;
    }

    /**
     * <b>validateCEF:</b>Método responsável por validar contas bancárias, cujo o banco seja Caixa Econômica Federal.
     */
    private function validateCEF() {

        if (isset($this->Data['data_bank_agency']) && !strlen($this->Data['data_bank_agency']) >= 4):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar a agência:</b>A agência informada deve possui menos que 4 caracteres!", MSG_ERROR];
        elseif (isset($this->Data['data_bank_account']) && !strlen($this->Data['data_bank_account']) >= 11):
            $this->Result = false;
            $this->Error = ["<b>Erro ao informar a conta:</b>A conta informada deve possui menos que 11 caracteres!", MSG_ERROR];
        else:

            /** Trata os dados  da agência */
            $agencyReceiveid = preg_replace('/[^0-9]/', '', $this->Data['data_bank_agency']);
            $agencyReceiveid = (string) $agencyReceiveid;
            /** Trata os dados  da conta */
            $accountReceiveid = preg_replace('/[^0-9]/', '', $this->Data['data_bank_account']);
            $accountReceiveid = (string) $accountReceiveid;
            /** Atribui os dados tratados, para variaveis que sera utilizada na validação */
            $agencyValidate = $agencyReceiveid;
            $accountValidate = $accountReceiveid;


            /** Pega os 4 digitos da agência */
            $fourDigitAgency = substr($agencyValidate, 0, 4);

            /** Pega os 3 digitos da operação */
            $treeDigitOperation = substr($accountReceiveid, 0, 3);
            /** concatena a agência e a operação */
            $agencyAndOperation = $fourDigitAgency . $treeDigitOperation;

            /** Soma da multiplicação dos digitos da agência e da operação */
            $sumAgencyAndOperation = $this->calculate($agencyAndOperation, 8);
            /** Pega os 7 digitos da conta  (desconsiderando a operação */
            $sevenDigitAccount = substr($accountValidate, 3, 8);

            $digitAccountCheck = $this->calculate($sevenDigitAccount, 9);

            /** Soma a multiplicação do agencia, operação e conta */
            $sum = $sumAgencyAndOperation + $digitAccountCheck;

            /** Multiplica o resultado da soma por 10 */
            $resultSum = $sum * 10;

            /** Divide o resultado da multiplicação por 11 */
            $division = $resultSum / 11;

            /** Converte o resultado da divisão para inteiro e multiplica o mesmo por 11 */
            $resultDivision = (int) $division * 11;

            /** Para obter o resto, deve se pegar o resultado das duas multiplicações e subtrair */
            $restAccount = $resultSum - $resultDivision;
            /** Verifica o valor do resto da divisão e faz atribuições de acordo com o resultado obtido */
            $digitAccountCheck = $this->checkDigitCEF($restAccount);
            /** Concatena o resultados os digitos da operação , os digitos da conta com o DV encontrado */
            $this->Account = $treeDigitOperation . $sevenDigitAccount . $digitAccountCheck;

            if ($this->Account == $accountReceiveid):
                $this->Result = true;
                $this->Error = ["<b>Sucesso:</b> Sucesso ao validar a conta bancária.", MSG_ACCEPT];
            else:
                $this->Result = false;
                $this->Error = ["<b>Erro ao validar conta bancária:</b> Verifique os dados da conta bancária que foram informados!.", MSG_ERROR];
            endif;



        endif;
    }

    /**
     * <b>checkDigitCEF</b>Método responsável por verificar o valor do digito verificador
     * encontrado, e realizar as atribuições corretas, de acordo com as regras de validação de conta
     * da Caixa Econômica. Deve receber como parametro o resto encontrado no calculo
     * @param Int $Rest
     * @return Int $Rest
     */
    private function checkDigitCEF($Rest) {

        $Rest = (int) $Rest;

        if ($Rest == 10):
            $Rest = 0;
        else:
            $Rest = $Rest;
        endif;

        return $Rest;
    }

    /**
     * <b>calculate</b>: Método responsável pela realização dos calculos dos digitos da
     * agência e da conta referente ao Banco do Brasil e Caixa Econômica Federal.
     * @param String $agencyOrAccount
     * @param Int $NumberMultiplications
     * @return Int $total;
     */
    private function calculate($agencyOrAccount, $NumberMultiplications) {
        $this->AgencyOrAccount = (string) $agencyOrAccount;
        $this->NumberMultiplications = (int) $NumberMultiplications;

        $total = 0;

        for ($i = 0; $i < strlen($this->AgencyOrAccount); $i++):

            $this->NumberMultiplications;

            $total = $total + ($this->AgencyOrAccount[$i] * $this->NumberMultiplications);

            $this->NumberMultiplications--;

        endfor;

        $this->NumberMultiplications = null;

        return $total;
    }

    /**
     * <b>Create:<b/>Método responsável por realizar o cadastro dos dados de cadastro de conta bancária propriamente dito no banco de dados
     */
    private function Create() {

        $createBank = new Create();
        $createBank->ExeCreate(self::Entity, $this->Data);

        if ($createBank->getResult()):
            $this->Error = ["<b>Sucesso ao cadastrar:</b>Conta Bancária foi cadastrado com sucesso!", MSG_ACCEPT];
            $this->Result = $createBank->getResult();
        endif;
    }

    /**
     * <b>Update:<b/> Método responsável por realizar a atualização do cadastro dos dados de conta bancária propriamente dito no banco de dados
     */
    private function Update() {

        $updateBank = new Update();
        $updateBank->ExeUpdate(self::Entity, $this->Data, "WHERE data_bank_id=:id", "id={$this->BankID}");

        if ($updateBank->getResult()):
            $this->Error = ["<b>Sucesso ao atualizar:</b> Conta bancária atualizada com sucesso!!", MSG_ACCEPT];
            $this->Result = true;
        endif;
    }

}
