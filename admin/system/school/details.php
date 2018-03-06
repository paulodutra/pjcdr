<?php
      $school=filter_input(INPUT_GET,'school',FILTER_VALIDATE_INT);
      $readPhone = new Read();
      $readPhone->FullRead("SELECT * FROM es_school  INNER JOIN app_cidades AS c ON school_city=c.cidade_id
       INNER JOIN app_estados AS e ON e.estado_id=school_uf  WHERE school_id={$school}");

      if($readPhone->getResult()):
        foreach ($readPhone->getResult() as $schoolphone):
          extract($schoolphone);    
    ?>
<div class="panel panel-primary">

  <!-- Default panel contents -->
  <div class="panel-heading text-center"><?=$school_name;?></div>
  <div class="panel-body">
        <p>CNPJ: <?=$school_cnpj?></p>
        <p>INEP: <?=$school_inep?></p>
        <p>Email da escola: <?=$school_email?></p>
        <p>Data de cadastro: <?=date('d/m/Y H:i:s',strtotime($school_date_registration))?></p>    
        <p>Situação: <?=$school_status=($school_status=='0' ?  'Inativo': 'Ativo')?></p>
        <hr>
        <p>Nome do diretor: <?=$school_director?></p>
        <p>Email do diretor: <?=$school_director_email?></p>
        <hr>
        <p>Telefone(s) - Tipo de telefone:</p>
        <?php
          $readPhone = new Read();
          $readPhone->FullRead("SELECT * FROM `es_school_phone` INNER JOIN es_phone_type ON type_id=phone_type WHERE phone_school={$school}");
           if($readPhone->getResult()):
              foreach ($readPhone->getResult() as $schoolphone):
              extract($schoolphone);    
        ?>
        
        <p><?=$phone_telephone?> - <?=$type_name?></p><br>

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
