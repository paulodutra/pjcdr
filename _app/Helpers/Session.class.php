<?php

/**
 *  Session.class[HELPÈR]
 * Responsável pelas estatísticas, sessões e atualizações de tráfego. 
 * 
 * @copyright (c) 2015, Paulo Henrique 
 */
class Session {

    private $Date;
    private $Cache;
    private $Traffic;
    private $Browser;

    function __construct($Cache = null) {
        session_start();
        $this->CheckSession($Cache);
    }

    //Verifica e executa todos os metódos da classe

    /*
     * <b>CheckSession:</b> Metodo responsável por verifica a sessão e executa todos os metodos da classe que gerenciam de forma isolada o trafego, a sessão ou cookie.
     */
    private function CheckSession($Cache = null) {
        $this->Date = date('Y-m-d');
        //se for informado o um tempo para o cache ele ira receber caso contrario recebe 20 minutos por default     
        $this->Cache = ( (int) $Cache ? $Cache : 20 );

        if (empty($_SESSION['useronline'])):

            $this->setTraffic();
            $this->setSession();
            $this->CheckBrowser();
            $this->setUsuario();
            //Se não existir a sessão atualiza os dados de navegadores
            $this->BrowserUpdate();

        else:
            $this->trafficUpdate();
            $this->sessionUpdate();
            $this->CheckBrowser();
            $this->usuarioUpdate();

        endif;

        $this->Date = null;
    }

    /**
     *  ***************************************************
     *  **********  SESSÃO DO USUÁRIO *********
     * ****************************************************
     */

    /**
     * <b>setSession:</b>Metodo respon´savel por startar a sessão, e captar todos os dados pertinente a mesma. 
     * online_session: id da sessão;
     * online_startviews : data e hora que a sessão foi iniciada
     * online_endviews: data e hora mais total da duração da sessão
     * online_ip: endereço ip da maquina que realizou o acesso
     * online_url: url acessada
     * online_agent: navegador utilizada para a realização do acesso
     */
    private function setSession() {
        $_SESSION['useronline'] = [
            "online_session" => session_id(),
            "online_startview" => date('Y-m-d H:i:s'),
            "online_endview" => date('Y-m-d H:i:s', strtotime("+ {$this->Cache}minutes")),
            "online_ip" => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
            "online_url" => filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_DEFAULT),
            "online_agent" => filter_input(INPUT_SERVER, "HTTP_USER_AGENT", FILTER_DEFAULT)
        ];
    }

    //Atualiza a sessão do usuário

    /**
     * <b>sessionUpdate:</b>Metodo Responsável por atualizar dados de uma sessão existente,
     * os dados que este metodo atualizam na tabela ws_siteviews são:
     * 
     * online_endviews: horario da ultima visualização mais o tempo da sessão
     * online_url: ultima url visualizada
     * 
     */
    private function sessionUpdate() {
        $_SESSION['useronline']['online_endview'] = date('Y-m-d H:i:s', strtotime("+ {$this->Cache}minutes"));
        $_SESSION['useronline']['online_url'] = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_DEFAULT);
    }

    /**
     *  **********************************************************
     *  *** USUÁRIOS, VISITAS , ATUALIZAÇÔES  ***
     * ***********************************************************
     */
    //Verifica e insere o trafego na tabela

    /**
     * <b>setTraffic:</b> Metodo responsável por inserir na tabela o a data de visualização, o usuário, visualização e paginas
     */
    private function setTraffic() {
        $this->getTraffic();
        if (!$this->Traffic):
            $ArrSiteViews = ['siteviews_date' => $this->Date, 'siteviews_users' => 1, 'siteviews_views' => 1, 'siteviews_pages' => 1];

            $createSiteViews = new Create();
            $createSiteViews->ExeCreate('blog_siteviews', $ArrSiteViews);
        else:
            if (!$this->getCookie())://se não existir o usuario com cookie
                $ArrSiteViews = [ 'siteviews_users' => $this->Traffic['siteviews_users'] + 1, 'siteviews_views' => $this->Traffic['siteviews_views'] + 1, 'siteviews_pages' => $this->Traffic['siteviews_pages'] + 1];

            else:
                $ArrSiteViews = [ 'siteviews_views' => $this->Traffic['siteviews_views'] + 1, 'siteviews_pages' => $this->Traffic['siteviews_pages'] + 1]; //se obtiver o cookie não ira atualizar o usuário
            endif;

            $updateSiteViews = new Update();
            $updateSiteViews->ExeUpdate('blog_siteviews', $ArrSiteViews, "WHERE siteviews_date = :date", "date={$this->Date}");

        endif;
    }

    //verifica e atualiza os pagesviews

    /**
     * <b>trafficUpdate:</b>Metodo responsável por atualizar o trafego, atualizando a pagina visualizada na tabela ws_siteviews
     */
    private function trafficUpdate() {
        $this->getTraffic();
        $ArrSiteViews = [ 'siteviews_pages' => $this->Traffic['siteviews_pages'] + 1];

        $updatePageViews = new Update();
        $updatePageViews->ExeUpdate('ws_siteviews', $ArrSiteViews, "WHERE siteviews_date = :date", "date={$this->Date}");

        //Limpa o objeto da memória
        $this->Traffic = null;
    }

    //Obtem dados da tabela [Helper traffic ] 
    //ws_siteviews

    /**
     * <b>getTraffic:Obtem os dados de navegação da tabela.</b>
     */
    private function getTraffic() {
        //Trabalhando com composição
        $readSiteViews = new Read();
        $readSiteViews->ExeRead('blog_siteviews', "WHERE siteviews_date = :date", "date= {$this->Date}");

        if ($readSiteViews->getRowCount()):
            $this->Traffic = $readSiteViews->getResult()[0]; //indice [0] pega apenas o resultado atual
        endif;
    }

    //verifca, cria e atualiza o cookie do usuário [Helper Traffic ]

    /**
     * <b>getCookie:</b>Verifica se existe o cookie, caso exista retorna true se não retorna false e depois cria um cookie valido por 24 horas
     * @return boolean
     */
    private function getCookie() {
        $Cookie = filter_input(INPUT_COOKIE, 'useronline', FILTER_DEFAULT);
        setcookie("useronline", base64_decode("GERACAO DEV"), time() + 86400); //86400 time de um dia, esse é o tempo que o cookie sera valido
        if (!$Cookie):
            return false;
        else:
            return true;
        endif;
    }

    /**
     *  ***********************************************************
     *  ********** NAGEVADORES DE ACESSO *********
     * ************************************************************
     */
    //indentifica o navegador do usuário

    /**
     * <b>CheckBrowser:</b> Metodo responsável por verificar qual é o navegador que o usuário esta realizando o acesso
     */
    private function CheckBrowser() {
        $this->Browser = $_SESSION['useronline']['online_agent'];
        //verifica a posição de uma string
        if (strpos($this->Browser, 'Chrome')):
            $this->Browser = 'Chrome';
        elseif (strpos($this->Browser, 'Firefox')):
            $this->Browser = 'Firefox';

        elseif (strpos($this->Browser, 'MSIE') || strpos($this->Browser, 'Trident/')):
            $this->Browser = 'IE';

        else:
            $this->Browser = 'Outros';
        endif;
    }

    //Atualiza tabela com dados de navegadores
    /**
     * <b>BrowserUpdate:</b> Metodo responsável por atualizar a estatica geral da tabela ws_siteviews_agent contando o acesso por navegador
     * lembrando que este acesso é contado um por sessão e não um por carregamento da pagina
     */
    private function BrowserUpdate() {
        $readAgent = new Read();
        
        $readAgent->ExeRead('blog_siteviews_agent', "WHERE agent_name = :agent ", "agent={$this->Browser}");

        if (!$readAgent->getResult()):
            $ArrAgent = ['agent_name' => $this->Browser, 'agent_views' => 1];
            $createAgent = new Create();
            $createAgent->ExeCreate('ws_siteviews_agent', $ArrAgent);
        else:
            //pega o primeiro resultado [0] da coluna agent_views e soma + 1
            $ArrAgent = [ 'agent_views' => $readAgent->getResult()[0]['agent_views'] + 1];
            $updateAgent = new Update();
            $updateAgent->ExeUpdate('blog_siteviews_agent', $ArrAgent, "WHERE agent_name = :name", "name={$this->Browser}");

        endif;
    }

    /**
     *  ************************************************************
     *   ****************   USUÁRIOS ONLINE ****************
     * *************************************************************
     */
    
    /**
     * <b>setUsuario:</b> Metodo responsável por inserir dados de navegação de usuário na tabela
     * ws_siteviews_online , os dados que são inseridos são oriundos do metodo setSession
     * atualiza o nome do navegador no indice da sessão agent_name pegando o valor do atributo browser
     */
    private function setUsuario() {
        $sesOnline = $_SESSION['useronline'];
        $sesOnline['agent_name'] = $this->Browser;

        $userCreate = new Create();
        $userCreate->ExeCreate('ws_siteviews_online', $sesOnline);
    }

    /**
     * <b>usuarioUpdate:</b>Metodo responsável por atualizar os dados de navegação de usuário 
     * atualizando os dados da ultima url acessada pelo usuário e da data e horario da ultima visualização da mesma
     */
    private function usuarioUpdate() {

        $ArrOnline = [
            'online_endview' => $_SESSION['useronline']['online_endview'],
            'online_url' => $_SESSION ['useronline']['online_url']
        ];

        $userUpdate = new Update();
        $userUpdate->ExeUpdate('blog_siteviews_online', $ArrOnline, "WHERE online_session=:session", "session={$_SESSION['useronline']['online_session']}");
        //Verifica se os dados da atualização não tiver resultado,entra no primeiro if 
        if (!$userUpdate->getRowCount()):
            //se Realmente não obtiver resultado, verifica se a sessão do usuários realmente existe       
            $readSession = new Read();
            $readSession->ExeRead('blog_siteviews_online', "WHERE online_session=:activeSession", "activeSession={$_SESSION['useronline']['online_session']}");
            //Se não obtiver resultados da consulta starta o usuário novamente
            if (!$readSession->getRowCount()):
                $this->setUsuario();
            endif;

        endif;
    }

}
