<div class="row">
    <div class="jumbotron">
        <h1 class="text-uppercase text-center">Concursos</h1>
        <p>-Veja o que aconteceu anteriormente, nos Concursos de Redação da DPU.</p>

    </div>
    <?php
    $getpage = (!empty($Link->getLocal()[1]) ? $Link->getLocal()[1] : '');
    /** Instancia um objeto da classe pager passando o link com todos os indices(do link) corretos  e executa a paginação */
    $Pager = new Pager(HOME . '/concursos/');
    $Pager->ExePager($getpage, 6);
    $readConcurso = new Read();
    $readConcurso->ExeRead('cs_concurso', "ORDER BY concurso_date_registration DESC LIMIT :limit OFFSET :offset","limit={$Pager->getLimit()}&offset={$Pager->getOffset()}limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
    if ($readConcurso->getResult()):
        $View = new View();
        $tpl_concurso = $View->Load('article_concurso');
        foreach ($readConcurso->getResult() as $file):
            $file['concurso_description'] = Check::Words($file['concurso_description'], 6);
            $View->Show($file, $tpl_concurso);
        endforeach;
    else:
        /** Retorna para uma pagina que tenha resultados caso o usuário passe um numero de pagina que não possui resultado */
        MSGErro("<b>Não existem concursos de redações ativos no momento!</b>", MSG_ALERT);
    endif;
    
     /** Exibe o painel de paginação */
    echo '<div class="text-center">';
    echo '<div class="row">';
    echo '<div class="col-md-8 col-md-offset-2">';
    $Pager->ExePaginator('cs_concurso', "ORDER BY concurso_date_registration DESC");
    echo $Pager->getPaginator();
    echo '</div>';
    echo '</div>';
    echo '</div>';  
    ?>


</div>  
