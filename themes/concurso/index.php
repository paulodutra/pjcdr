<?php
$View = new View();
$article_slide = $View->Load('article_slide');

?>
<div class="space-carousel">

<header id="myCarousel" class="carousel slide">
<!-- Header Carousel -->
    
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>

        <?php
        $cat=Check::CatByName('Noticias');
        $post = new Read();
        $post->ExeRead('blog_posts',"WHERE post_status= 1 AND (post_cat_parent=:cat OR post_category =:cat) ORDER BY post_date DESC LIMIT :limit OFFSET :offset", "cat={$cat}&limit=3&offset=0");
        if (!$post->getResult()):
            MSGErro("Desculpe ainda não existem noticias cadastradas !", MSG_INFOR);
        else:
            $i = 1;
            foreach ($post->getResult() as $slide):
                $slide['post_title'] = Check::Words($slide['post_title'], 6);
                $slide['post_content'] = Check::Words($slide['post_content'], 10);
                $slide['datetime'] = date('Y-m-d', strtotime($slide['post_date']));
                $slide['pubdate'] = date('d-m-Y H:i', strtotime($slide['post_date']));

                $slide['class'] = ($i==1 ? 'active' : '');
                //var_dump($slide['class']);
                $View->Show($slide, $article_slide);
                $i++;
            endforeach;

        endif;
        ?>
<!-- Controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"></span>
        </a>

    </header> 
 
 </div>          
       

    <div class="container-fluid">

        <div class="row">

            <h1 class="page-header text-center text-uppercase">Concurso de Redação da DPU</h1>
            <div class="well">
                <div class="row">
                    
                        <div class="col-md-8">
                             <p>Prezado Diretor escolar, realize o cadastro de sua escola e participe do Concurso de Redação da DPU. O cadastro é rápido e fácil.</p>
                        </div> 
                   
                        <div class="col-md-4">
                            <a class="btn btn-lg btn-primary btn-block" href="<?= HOME ?>/cadastro">Realizar inscrição</a>
                        </div>
                      
                </div>
            </div>

            <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4></i>Como funciona ?</h4>
                        </div>
                        <div class="panel-body">
                            <p>Veja como funciona o Concurso de Redação da DPU.</p>
                            <a href="<?=HOME?>/categoria/como-funciona" class="btn btn-info">Leia Mais</a>
                        </div>
                    </div>  
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4></i>Quais são os pre-requisitos</h4>
                    </div>
                    <div class="panel-body">
                        <p>Veja todos os pré-requisitos para participar do Concurso de Redação da DPU.</p>
                        <a href="<?=HOME?>/categoria/pre-requisitos" class="btn btn-info">Leia Mais</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Como realizar inscrição</h4>
                    </div>
                    <div class="panel-body">
                        <p>Veja todos os passos para realizar a inscrição e utilizar os recursos dispíniveis para sua inscrição.</p>
                        <a href="<?=HOME?>/categoria/como-realizar-inscricao" class="btn btn-info">Leia Mais</a>
                    </div>

                </div>
            </div>
            <h1 class="page-header text-center text-uppercase">Noticias</h1>
                <?php
                    $readNoticy = new Read();
                    $readNoticy->ExeRead('blog_posts', "WHERE post_status=1 ORDER BY rand() LIMIT :limit ",  "limit=3");
                    if ($readNoticy->getResult()):
                        $article_m=$View->Load('article_m');
                         foreach ($readNoticy->getResult() as $more): 
                                $more['post_content']=Check::Words($slide['post_content'], 10);   
                                /** apresenta a view */
                                $View->Show($more, $article_m);
                            endforeach;
                    else:
                        MSGErro("Desculpe, ainda não existem noticias cadastradas !", MSG_INFOR);

                    endif;        
                         
                ?>
    </div>
</div>	
</div>




