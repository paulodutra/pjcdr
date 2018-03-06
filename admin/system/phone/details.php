<?php
      $school=filter_input(INPUT_GET,'school',FILTER_VALIDATE_INT);
      $readSchool = new Read();
      $readSchool->FullRead("SELECT * FROM es_school AS s INNER JOIN app_cidades AS c ON s.school_city=c.cidade_id 
        INNER JOIN app_estados AS e ON e.estado_id=s.school_uf WHERE school_id={$school}");

      if($readSchool->getResult()):
        foreach ($readSchool->getResult() as $schoolphone):
          extract($schoolphone);    
    ?>
<div class="panel panel-primary">
  <div class="panel-heading text-center"><?=$school_name;?></div>
  <div class="panel-body">
        <p>CNPJ: <?=$school_cnpj?></p>
        <p>INEP: <?=$school_inep?></p>
        <p>Email da escola: <?=$school_email?></p>
        <p>Email do diretor: <?=$school_director_email?></p>
        <p>Data de cadastro: <?=date('d/m/Y H:i:s',strtotime($school_date_registration))?></p>  
        <p>Situação: <?=$school_status=($school_status=1 ? 'Ativo' : 'Inativo')?></p>
        <hr>
        <p>Telefone(s) - Tipo de telefone:</p>
        <?php
          $readPhone = new Read();
          $readPhone->FullRead("SELECT * FROM `es_school_phone` INNER JOIN es_phone_type ON type_id=phone_type WHERE phone_school={$school}");
           if($readPhone->getResult()):
              foreach ($readPhone->getResult() as $schoolphone):
              extract($schoolphone);    
        ?>
        
        <?=$phone_telephone?> - <?=$type_name?><br>

        <?php
          endforeach;
        else:
          MSGErro("A escola não possui telefones cadastrados! ",MSG_ALERT);  
        endif;
        ?>
  </div>

  <!-- List group -->
  <ul class="list-group">
    <li class="list-group-item">Endereço: <?=$school_address?></li>
    <li class="list-group-item">Cidade-UF: <?=$cidade_nome?> - <?=$cidade_uf ?></li>
    <li class="list-group-item">CEP: <?=$school_cep?></li>
    <li class="list-group-item">Bairro: <?=$school_district?></li>
    <li class="list-group-item">Complemento: <?=$school_complement?></li>
  </ul>
  <?php

    endforeach;
  endif;
  ?>
</div>
