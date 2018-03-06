<?php

/**
 *  Seo [MODEL]
 * Classe de apoio para o modelo LINK SSEO(Search Engine Optimization) para as paginas
 * 
 * @copyright (c) 2015, Paulo Henrique 
 */
class Seo {

    private $File;
    private $Link;
    private $Data;
    private $Tags;

    /** Dados povoados: Serão retornados após gerar o SEO */
    private $seoData;
    private $seoTags;

    /**
     * <b>__construct:</b> Método construtor da classe, toda vez ao instanciar um objeto da mesma, sera obrigatório informar o arquivo(pagina) e o link
     * Antes de atribuir cada parametro recebido no método para os atributos da classe realiza uma limpeza de codigos(strip_tags) e retirar espaços desnecessários(trim)
     * 
     * @param string $File
     * @param string $Link
     */
    function __construct($File, $Link) {
        $this->File = strip_tags(trim($File));
        $this->Link = strip_tags(trim($Link));
    }

    /**
     * <b>getTags:</b> Obtém as tags que foram montadas é otimizadas no método getSeo() 
     * 
     * @return string
     */
    public function getTags() {
        $this->checkData();
        return $this->seoTags;
    }

    /**
     * <b>getData:</b> Obtém os dados que foram utilizadas para montar as tags no método setTags() 
     * 
     * @return string
     */
    public function getData() {
        $this->checkData();
        return $this->seoData;
    }

    /**
     * **************************************************
     * *********** PRIVATE METHODS ***********
     * **************************************************
     */

    /**
     * <b>checkData:</b> Metodo responsável por verificar se as tags <b>não</b> foram montagas e otimizadas,caso seja verdadeiro
     * é realizado um desvio para o metodo getSeo() - metodo que realiza a montagem e otimização
     */
    private function checkData() {
        if (!$this->seoData):
            $this->getSeo();
        endif;
    }

    /**
     * <b>getSeo:</b> Método responsável por verificar qual arquivo foi acessado,  
     * realizar a leitura no banco de dados na respectiva tabela e povoar os dados no seoData, para que o metodo setTags monte as tags de 
     * acordo com o arquivo acessado. 
     * Caso haja o povoamento dos dados invoca o metodo setTags()
     */
    private function getSeo() {
        $readSeo = new Read();

        switch ($this->File):
            //SEO :: artigo
            case 'artigo':
                /** Condição para administradores logados(ou não) e post ativos(ou não) */
                $Admin = (isset($_SESSION['userlogin']['user_level']) && $_SESSION['userlogin']['user_level'] == 3) ? true : false;
                $CheckPost = ($Admin ? ' ' : 'post_status = 1 AND');

                $readSeo->ExeRead('blog_posts', "WHERE {$CheckPost} post_name = :link", "link={$this->Link}");
                /** Se não retornar resultados realiza a limpeza dos dados e das tags
                  caso contrario da um extract(para utilizar o nome das colunas da tabela como variavel na leitura em getresult()[0](indice 0 apenas o resultado) e armazena
                 * o resultado em seoData */
                if (!$readSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($readSeo->getResult()[0]);
                    $this->seoData = $readSeo->getResult()[0];
                    $this->Data = [$post_title . ' - ' .SITENAME, $post_content, HOME . "/artigo/{$post_name}", HOME . "/uploads/{$post_cover}"];

                    //post:: post_view : Realiza a contagem da visualização dos posts e armazena na tabela
                    $arrUpdate = ['post_views' => $post_views + 1];
                    $updateView = new Update();
                    $updateView->ExeUpdate('blog_posts', $arrUpdate, "WHERE post_id=:postid", "postid={$post_id}");
                endif;
                break;
            //SEO :: CATEGORIA
            case 'categoria':
                $readSeo->ExeRead('blog_categories', "WHERE  category_url = :link", "link={$this->Link}");
                /** Se não retornar resultados realiza a limpeza dos dados e das tags
                  caso contrario da um extract(para utilizar o nome das colunas da tabela como variavel na leitura em getresult()[0](indice 0 apenas o resultado) e armazena
                 * o resultado em seoData */
                if (!$readSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($readSeo->getResult()[0]);
                    $this->seoData = $readSeo->getResult()[0];
                    $this->Data = [$category_name . ' - ' . SITENAME, $category_description, HOME . "/categoria/{$category_url}", INCLUDE_PATH . '/images/site.png'];

                    //post:: post_view : Realiza a contagem da visualização dos posts e armazena na tabela
                    $arrUpdate = ['category_views' => $category_views + 1];
                    $updateView = new Update();
                    $updateView->ExeUpdate('blog_categories', $arrUpdate, "WHERE category_id=:catid", "catid={$category_id}");
                endif;
                break;

                //SEO CONCURSO
                case 'concurso':
                        $readSeo->FullRead("SELECT * FROM cs_concurso AS c INNER JOIN cs_concurso_file AS f ON c.concurso_id=f.file_concurso INNER JOIN cs_concurso_file_type AS t ON t.file_type_id= f.file_type WHERE concurso_url=:link","link={$this->Link}");
                        if (!$readSeo->getResult()):
                            $this->seoData = null;
                            $this->seoTags = null;
                        else:
                            extract($readSeo->getResult()[0]);
                            $this->seoData = $readSeo->getResult()[0];
                            $this->Data = [ $concurso_name.'-'.SITENAME, $concurso_description, HOME . '/concurso', INCLUDE_PATH . '/uploads/{$concurso_logo}'];
                        endif;
                   
                    break;
                //SEO CONCURSOS
                case 'concursos':
                        $this->Data = [ 'Concursos - '.SITENAME, SITEDESC, HOME . '/concursos', INCLUDE_PATH . '/images/site.png'];
                    break;

                 // SEO:: DUVIDAS FREQUENTES   
                 case 'duvidas':
                   $cat=Check::CatByName('Duvidas');
                   $readSeo->ExeRead('blog_posts', "WHERE post_status= 1 AND (post_cat_parent=:link OR post_category =:link) ORDER BY post_id", "link={$cat}");
                /** Se não retornar resultados realiza a limpeza dos dados e das tags
                  caso contrario da um extract(para utilizar o nome das colunas da tabela como variavel na leitura em getresult()[0](indice 0 apenas o resultado) e armazena
                 * o resultado em seoData */
                if (!$readSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($readSeo->getResult()[0]);
                    $this->seoData = $readSeo->getResult()[0];
                    $this->Data = ['Duvidas Frequentes'.' - '. SITENAME, 'Duvidas Frequentes',HOME . "/duvidas/", INCLUDE_PATH . '/images/site.png'];
                endif;
                   // $this->Data = ['Duvidas Frequentes- '.SITENAME, SITEDESC, HOME . '/duvidas', INCLUDE_PATH . '/images/site.png'];
                    break;
            // SEO:: PESQUISA   
            case 'pesquisa':
                $readSeo->ExeRead('blog_posts', "WHERE  post_status = 1 AND (post_title LIKE '%' :link  '%' OR post_content LIKE '%' :link '%')  ", "link={$this->Link}");
                /** Se não retornar resultados realiza a limpeza dos dados e das tags
                  caso contrario da um extract(para utilizar o nome das colunas da tabela como variavel na leitura em getresult()[0](indice 0 apenas o resultado) e armazena
                 * o resultado em seoData */
                if (!$readSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    /** Armazena o numero de resultados no seoData['count'] */
                    $this->seoData['count'] = $readSeo->getRowCount();
                    $this->Data = ["Pesquisa por {$this->Link} " . ' - ' . SITENAME, "Sua pesquisa {$this->Link} retornou {$this->seoData['count']} resultados !", HOME . "/pesquisa/{$this->Link}", INCLUDE_PATH . '/images/site.png'];
                endif;
                break;


       
            //SEO CADASTRO DE ESCOLAS

            case 'cadastro':
                 $this->Data = [ 'Cadastro de escolas - '.SITENAME, SITEDESC, HOME . '/cadastro', INCLUDE_PATH . '/images/site.png'];
                break;

             //SEO CADASTRO DE ESCOLAS
            case 'contato':
                 $this->Data = [ 'Contato - '.SITENAME, SITEDESC, HOME . '/contato', INCLUDE_PATH . '/images/site.png'];
                break;
            
            //SEO::INDEX
            case 'index':

                $this->Data = [SITENAME . ' - Concurso de Redação patrocinado pela DPU !', SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];

                break;
            //SEO 404
            default :
                /*                 * Dados de otimização: Nome do site-titulo da pagina, descrição, link, imagem(imagem para aparecer quando compartilhado em redes sociais) */
                $this->Data = [ '404 OHOU, Nada encontrado  ', SITEDESC, HOME . '/404', INCLUDE_PATH . '/images/site.png'];
        endswitch;

        if ($this->Data):
            $this->setTags();
        endif;
    }

    /**
     * <b>setTags:</b> Método responsável por pegar os dados povoados pelo metodo getSeo e realiza a montagem das tags de acordo com o arquivo acessado
     * Realiza otimização das paginas em 3 níveis: NORMAL PAGE = para acesso oriundos do site, FACEBOOK: Para otimizar a busca e o compartilhamento no facebook, 
     * TWITTER e outras redes sociais: Para otimizar a busca e compartilhamento no twitter e outras redes sociais;
     * 
     * Também realiza a limpeza de codigos e espaços denecessários antes de montar as tags propriamente ditas
     * 
     * OBS: 
     * Data[0]: Contém o titulo da pagina
     * Data[1]: Contém o conteudo da pagina (25 palavras) e faz tratamento utlizando html_entity_decode para a conversao de charset e remoção de codigos html
     * Data[2]: Contém o link da página 
     * Data[3]: Contém a imagem da pagina
     */
    private function setTags() {
        $this->Tags['Title'] = $this->Data[0];/** Site name e titulo da página */
        /*         * html_entity_decode: Converte o charset, caso venha html na leitura do banco ou por erro de conversão do tim.php */
        $this->Tags['Content'] = Check::Words(html_entity_decode($this->Data[1]), 25);
        $this->Tags['Link'] = $this->Data[2];
        $this->Tags['Image'] = $this->Data[3];

        /** Removendo codigos e espaços desnecessários */
        $this->Tags = array_map('strip_tags', $this->Tags);
        $this->Tags = array_map('trim', $this->Tags);

        /** Limpando o objeto, pois os dados para serem usados nas tags, foram colocados no array tags */
        $this->Data = null;

        //Otimização NORMAL PAGE
        $this->seoTags = '<title>' . $this->Tags['Title'] . '</title>' . "\n";
        $this->seoTags .= '<meta name="description " content=" ' . $this->Tags['Content'] . ' "/>' . "\n";
        $this->seoTags .= '<meta name="author" content=" ' . SITEAUTHOR . ' "/>' . "\n";
        $this->seoTags .= '<meta name=robots content="index, follow" />' . "\n";
        $this->seoTags .= '<link rel="canonical href=" ' . $this->Tags['Link'] . ' "/>' . "\n";
        $this->seoTags .= "\n";

        //Otimização para FACEBOOK
        $this->seoTags.='<meta property="og:site_name" content=" ' . SITENAME . ' " />' . "\n";
        $this->seoTags.='<meta property="og:locale" content="pt_br " />' . "\n";
        $this->seoTags.='<meta property="og:title" content=" ' . $this->Tags['Title'] . '" />' . "\n";
        $this->seoTags.='<meta property="og:description" content=" ' . $this->Tags['Content'] . '" />' . "\n";
        $this->seoTags.='<meta property="og:image" content=" ' . $this->Tags['Image'] . '" />' . "\n";
        $this->seoTags.='<meta property="og:url" content=" ' . $this->Tags['Link'] . '" />' . "\n";
        $this->seoTags.='<meta property="og:url" content="article" />' . "\n";
        $this->seoTags .= "\n";

        //Otimização para TWITTER e outras redes sociais
        $this->seoTags.='<meta itemprop="name" content=" ' . $this->Tags['Title'] . ' " />' . "\n";
        $this->seoTags.='<meta itemprop="description" content=" ' . $this->Tags['Content'] . ' " />' . "\n";
        $this->seoTags.='<meta itemprop="url" content=" ' . $this->Tags['Link'] . ' " />' . "\n";

        /** Limpando o objeto, pois os dados a serem usados foram montados nas tags dentro do seoTags */
        $this->Tags = null;
    }

}
