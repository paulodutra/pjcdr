<?php

/**
 *  Upload.class[HELPER]
 * Resposável por executar upload de imagens, arquivos e midias no sistema
 * @copyright (c) 2015, Paulo Henrique 
 */
class Upload {
    
    private $File;
    private $Name;
    private $Send;

    /** IMAGE UPLOAD */
    private $Width;
    private $Image;

    /** RESULTSET */
    private $Result;
    private $Error;

    /** DIRETÓRIOS */
    private $Folder;
    private static $BaseDir;

    /**
     * <b>__construct:</b> Metodo construtor da classe, o mesmo verifica se foi informado um diretorio
     * caso não seja o diretorio padrao sera '../uploads/', caso o diretorio não exista 
     * o mesmo realiza a criação do mesmo , dando permissão total 0777
     * @param String $BaseDir
     */
    function __construct($BaseDir = null) {
        self::$BaseDir = ( (string) $BaseDir ? $BaseDir : '../uploads/');

        if (!file_exists(self::$BaseDir) && !is_dir(self::$BaseDir)):
            mkdir(self::$BaseDir, 0777); //0777 permissão maxima para a pasta
        endif;
    }

    /**
     * <b>Image</b> Metodo responsável por executar o upload de imagem, podendo redimencionar 
     * a imagem atraves do atributo $Width, podendo alterar o nome da imagem atraves do atributo $Name
     * podendo alterar a pasta que a imagem sera enviada atraves do atributo  $Folder
     *
     *  @param array $Image (Obrigatório);
     * @param String $Name (Opcional);
     * @param Int $Width (Opcional);
     * @param string $Folder (Opcional);
     */
    public function Image(array $Image, $Name = null, $Width = null, $Folder = null) {
        $this->File = $Image;
        $this->Name = ( (string) $Name ? $Name : substr($Image['name'], 0, strrpos($Image['name'], '.')) );
        $this->Width = ( (int) $Width ? $Width : 1024 );
        $this->Folder = ( (string) $Folder ? $Folder : 'images' );

        $this->CheckFolder($this->Folder); //Passando a pasta images acima

        $this->setFileName();
        $this->UploadImage();
    }
    //realiza o envio de arquivos
    /**
     * <b>File:</b>Metodo responsável por realizar o envio de arquivos do tipo doc, docx, xls, xlsx, pdf e txt
     * @param array $File : arquivo a ser enviado (Obrigatorio)
     * @param String $Name : nome do arquivo (Opcional)
     * @param String $Folder : Pasta para qual o arquivo sera enviado caso a pasta não seja informada sera falva em files (Opcional)
     * @param String $MaxFileSize : Tamanho permitido no upload, lembrando que as configurações do php tem que aceitar o tamanho setado neste atributo (Opcional) por padrão defini 2 mb
     */
    public function File(array $File, $Name = null, $Folder = null, $MaxFileSize = null) {
        $this->File = $File;
        $this->Name = ( (string) $Name ? $Name : substr($File['name'], 0, strrpos($File['name'], '.')) );//deini o nome do arquivo
        $this->Folder = ( (string) $Folder ? $Folder : 'files' );//defini a pasta aonde sera salvo o arquivo 
        $MaxFileSize = ( (int) $MaxFileSize ? $MaxFileSize : 2); //Tamanho maximo a ser enviados em mega caso não seja informado por default 2mb 
        //Tipos de arquivos aceitos
        $FileAccept = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', //arquivos doc, docx (word)
            'application/pdf', //arquivos pdf
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', //arquivos (excel)
            'text/plain',//arquivos txt   
            'image/jpeg',//Imagem jpeg
            'image/jpg', //Imagem jpg
            'image/png'//Imagem png
        ];
        //verica se o arquivo enviado é maior que o permitido
        if ($this->File['size'] > ($MaxFileSize * (1024 * 1024))):
            $this->Result = false;
            $this->Error = "Arquivo muito grande, tamanho maximo  permítido é de {$MaxFileSize}mb";
        elseif (!in_array($this->File['type'], $FileAccept))://verifica se o tipo de arquivo é aceito
            $this->Result = false;
            $this->Error = ['Tipo de arquivo não aceito. Envie arquivos do tipo: .PDF, . DOC, .DOCX, .XLS,  .XLSX, .TXT .JPEG, .JPG, OU .PNG  !',MSG_ERROR];

        else:
            $this->CheckFolder($this->Folder);
            $this->setFileName();
            $this->MoveFile();
        endif;
    }
    
    /**
     * <b>Media:</b>Metodo responsável por realizar o envio de arquivos de midias do tipo  audio MP3 ou video MP4
     * @param array $Media :arquivo a ser enviado (Obrigatorio)
     * @param String $Name : nome do arquivo (Opcional)
     * @param String $Folder : Pasta para qual o arquivo sera enviado caso a pasta não seja informada sera salva em medias (Opcional)
     * @param String $MaxFileSize : Tamanho permitido no upload, lembrando que as configurações do php tem que aceitar o tamanho setado neste atributo (Opcional) por padrão defini 40 mb
     */
    
     public function Media(array $Media, $Name = null, $Folder = null, $MaxFileSize = null) {
        $this->File = $Media;
        $this->Name = ( (string) $Name ? $Name : substr($Media['name'], 0, strrpos($Media['name'], '.')) );//defini o nome do arquivo
        $this->Folder = ( (string) $Folder ? $Folder : 'medias' );//defini a pasta aonde sera salvo o arquivo
        $MaxFileSize = ( (int) $MaxFileSize ? $MaxFileSize : 40); //Tamanho maximo a ser enviados em mega caso não seja informado por default 40mb 
        //Tipos de arquivos aceitos
        $FileAccept = [
            'audio/mp3', //arquivos MP3
            'video/mp4' //arquivos MP4
            
        ];
        //verica se o arquivo enviado é maior que o permitido
        if ($this->File['size'] > ($MaxFileSize * (1024 * 1024))):
            $this->Result = false;
            $this->Error = "Arquivo muito grande, tamanho maximo  permítido é de {$MaxFileSize}mb";
        elseif (!in_array($this->File['type'], $FileAccept))://verifica se o tipo de arquivo é aceito
            $this->Result = false;
            $this->Error = ['Tipo de arquivo não aceito. Envie arquivos do tipo: Audio MP3 ou Vídeo MP4 !',MSG_ERROR];

        else:
            $this->CheckFolder($this->Folder);
            $this->setFileName();
            $this->MoveFile();
        endif;
    }

    /**
     * <b>getResult:</b>Metodo responsável por retornar o resultado da operação podendo ser true ou false
     * @return bool Result
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>getError:</b>Metodo responsável por retornar a mensagem após a operação
     * @return Error
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
     * <b>CheckFolder:</b> Metodo responsável por verifica se dentro do folder existe a pasta com o numero do ano e mes 
     * para separar as pastas por ano e mes de cada upload realizados 
     * @param string $Folder
     */
    private function CheckFolder($Folder) {
        list( $y, $m) = explode('/', date('Y/m'));
        $this->CreateFolder("{$Folder}");
        $this->CreateFolder("{$Folder}/{$y}");
        $this->CreateFolder("{$Folder}/{$y}/{$m}/");

        $this->Send = "{$Folder}/{$y}/{$m}/"; //caminho aonde o upload sera armazenado
    }

    /**
     * <b>CreateFolder:</b>Metodo responsável por criar a pasta caso a mesma não exista e não for um diretorio
     * @param String $Folder
     */
    private function CreateFolder($Folder) {
        if (!file_exists(self::$BaseDir . $Folder) && !is_dir(self::$BaseDir . $Folder)):
            mkdir(self::$BaseDir . "/" . $Folder, 0777); //0777 permissão maxima para a pasta
        endif;
    }

    /**
     * <b>setFileName:</b>Metodo Responsável por verificar o nome do arquivo, 
     * caso o mesmo esteja repetido ele concatena o nome do arquivo mais o time para que o arquivo seja enviado
     */
    private function setFileName() {
        $FileName = Check::Url($this->Name) . strrchr($this->File['name'], '.'); //strrchr: encontra a ultima ocorrência de um caractere em uma string
        //pasta de uplods/subpastas/nome do arquivo
        if (file_exists(self::$BaseDir . $this->Send . $FileName)):
            //Se o arquivo existe concatena o nome do arquivo com a hora mais a extensão
            $FileName = Check::Url($this->Name) . '-' . time() . strrchr($this->File['name'], '.'); //strrchr: encontra a ultima ocorrência de um caractere em uma string
        endif;

        $this->Name = $FileName;
    }

    //Realiza o upload de imagens redimensionando a mesma!
    //Validação por mime type referencia: reference.sitepoint.com/html/mime-types-full

    /**
     * <b>UploadImage:</b> Metodo responsavel por realizar o upload de imagens, verificando(mime-types) se o tipo de imagem enviado é png ou jpg. 
     * Realiza o redimencionamento da imagem envia para o servidor;
     */
    private function UploadImage() {
        switch ($this->File['type']):
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $this->Image = imagecreatefromjpeg($this->File['tmp_name']);
                break;
            case 'image/png':
            case 'image/x-png':
                $this->Image = imagecreatefrompng($this->File['tmp_name']);
                break;
        endswitch;

        if (!$this->Image):
            $this->Result = false;
            $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG !';
        else:
            $x = imagesx($this->Image); //Eixo que ira definir  largura
            $y = imagesy($this->Image); //Eixo que ira definir altura

            $ImageW = ($this->Width < $x ? $this->Width : $x ); //Defini a largura
            $ImageH = ($ImageW * $y) / $x; //Defini altura
            //Criar uma nova imagem passando a largura e altura
            $NewImage = imagecreatetruecolor($ImageW, $ImageH);
            imagealphablending($NewImage, false);
            //Salva a imagem com o fundo transparente passando  como parametro a imagem e a flag true ou false
            imagesavealpha($NewImage, true);
            //mover a imagem criada sendo dst=destino e src= source
            imagecopyresampled($NewImage, $this->Image, 0, 0, 0, 0, $ImageW, $ImageH, $x, $y);

            switch ($this->File['type']):
                case 'image/jpg':
                case 'image/jpeg':
                case 'image/pjpeg':
                    //Imagem , diretorio/caminho com subpasta / nome da imagem
                    imagejpeg($NewImage, self::$BaseDir . $this->Send . $this->Name);
                    break;
                case 'image/png':
                case 'image/x-png':
                    //Imagem , diretorio/caminho com subpasta / nome da imagem
                    imagepng($NewImage, self::$BaseDir . $this->Send . $this->Name);
                    break;
            endswitch;
            //Verifica se a imagem foi criada
            if (!$NewImage):
                $this->Result = false;
                $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG !';
            else:
                $this->Result = $this->Send . $this->Name;
                $this->Error = null;
            endif;
            //Limpa da memória a imagem que foi enviada e a imagem que foi redimencionada pois o envio já foi realizado
            imagedestroy($this->Image);
            imagedestroy($NewImage);
        endif;
    }

    //Envia arquivos e midias
    /**
     * <b>MoveFile:</b>Realiza o envio do arquivo para a sua respectiva pasta que esta definida nos metodos  publicos que tratão de questão arquivos. 
     */
    private function MoveFile() {
        //Verifica se o arquivo foi enviado na pasta com o caminho da subpasta sendo ano e mes  e nome do arquivo
        if (move_uploaded_file($this->File['tmp_name'], self::$BaseDir . $this->Send . $this->Name)):
            $this->Result = $this->Send . $this->Name;//Seta o resultado com o nome do arquivo. 
            $this->Error = null;//seta a variavel de erro como nula.
        else:
            $this->Result = false;//seta a variavel de resultado com falsa
            $this->Error = 'Erro ao enviar o arquivo. Favor tente mais tarde';
        endif;
    }

}
