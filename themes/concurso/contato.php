 <!-- Content Row -->
        <div class="row">
    
            <!-- Map Column -->
            <div class="col-md-8">
                <!-- Embedded Google Map -->
                <!--iframe width="100%" height="400px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?hl=en&amp;ie=UTF8&amp;ll=37.0625,-95.677068&amp;spn=56.506174,79.013672&amp;t=m&amp;z=4&amp;output=embed"></iframe-->
                <iframe width="100%" height="400px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d684.0821175555853!2d-47.875092957916!3d-15.786587874278233!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x935a3a50e40ba00f%3A0xab003b5692854161!2sDefensoria+P%C3%BAblica+Geral+da+Uni%C3%A3o!5e0!3m2!1spt-BR!2sbr!4v1449667694340" ></iframe>
            </div>
            <!-- Contact Details Column -->
            <div class="col-md-4">
                <h1 class="text-uppercase text-center">Contato</h1>
                <p>
                   <br>SAUN – Quadra 5 – Lote C – Centro Empresarial CNC – Bloco C – 15º Andar - CEP 70.040-250 - Brasília/DF<br>
                </p>
                <p><i class="fa fa-phone"></i> 
                    <abbr title="Phone">Fone</abbr>: (61) 3318-1632/3318-1628</p>
                <p><i class="fa fa-envelope-o"></i> 
                    <abbr title="Email">Email</abbr>: <a href="mailto:dpunasescolas@dpu.gov.br">dpunasescolas@dpu.gov.br</a>
                </p>
                <p><i class="fa fa-clock-o"></i> 
                    <abbr title="Hours"> Horário de atendimento</abbr>:08Hs as 18Hs</p>
                <ul class="list-unstyled list-inline list-social-icons">
                    <li>
                        <a href="https://www.facebook.com/defensoriauniao" target="_blank"><i class="fa fa-facebook-square fa-2x"></i></a>
                    </li>
                    <li>
                        <a href="https://twitter.com/imprensaDPU" target="_blank"><i class="fa fa-twitter-square fa-2x"></i></a>
                    </li>
                </ul>
                <p>Curta nossa página</p>
                <div class="fb-page" data-href="https://www.facebook.com/defensoriauniao" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/defensoriauniao"><a href="https://www.facebook.com/defensoriauniao">Defensoria Pública da União - DPU</a></blockquote></div></div>
            </div>   
  
        </div> <!-- /.row -->
         <?php
        
            $Contato = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            if ($Contato && $Contato['SendFormContato']):
                unset($Contato['SendFormContato']);
                $Contato['DestinoNome']='DPU NAS ESCOLAS';
                $Contato['DestinoEmail']=MAILUSER;

                $sendMail = new Email();
                $sendMail->Enviar($Contato);

                if ($sendMail->getError()):
                    MSGErro($sendMail->getError()[0], $sendMail->getError()[1]);
                endif;
            endif;
            ?>
        <div class="row">
            <div class="col-md-8">
                <h3>Entre em contato com a equipe DPU NAS ESCOLAS</h3>
                <form name="FormContato" action="" method="post" >
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Nome:<i class="text-red">*</i></label>
                            <input type="text" class="form-control" name="RemetenteNome" required>
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Email: <i class="text-red">*</i></label>
                            <input type="email" class="form-control" name="RemetenteEmail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required >
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Assunto: <i class="text-red">*</i></label>
                            <input type="text" class="form-control" name="Assunto" required>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Mensagem: <i class="text-red">*</i></label>
                            <textarea rows="10" cols="100" class="form-control" name="Mensagem" required  maxlength="999" style="resize:none"></textarea>
                        </div>
                    </div>
           
                    <div class="control-group form-group">
                        <div class="controls">
                            <div class="text-center">
                                <input type="submit" name="SendFormContato" class="btn btn-primary" value="Enviar Mensagem">
                            </div>    
                        </div>
                    </div>        
                </form>
            </div>

        </div>
  <!-- /.row -->


        