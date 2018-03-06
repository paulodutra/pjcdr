
<div class="panel panel-primary">
<?php
 /**
    * Todas as classes administrativas serão carregadas manualmente de acordo com o padrão front controller 
    que foi definido no painel.php(não sendo necessário navegar nas estruturas de pastas convencionais)
 */
  require_once('_models/AdminSeries.class.php');

  $category=filter_input(INPUT_GET,'category',FILTER_VALIDATE_INT);

  $readCategory= new Read();
  $readCategory->FullRead("SELECT * FROM cs_category AS cat INNER JOIN cs_category_education as educ ON cat.category_education=educ.education_id INNER JOIN cs_category_modality AS mo ON mo.modality_id=cat.category_modality WHERE category_id={$category}");
  if($readCategory->getResult()):
    foreach ($readCategory->getResult() as $serie):
      extract($serie);

?>

  <!-- Default panel contents -->
  <div class="panel-heading text-center"><?=$category_name?></div>
  <div class="panel-body">
        <p>Descrição: <?=$category_description?></p>
        <p>Tipo de ensino: <?=$education_name?> </p>
        <p>Tipo de modalidade: <?=$modality_name?></p>
        <p>Data de cadastro: <?=date('d/m/Y H:i:s',strtotime($category_date_registration))?></p>    
  <?php
      endforeach;
  else:
     MSGErro("", MSG_ALERT);    
  endif;

?>      
  </div>
  <?php
    /**Ação de alguns botões presentes da tabela*/
    $action=filter_input(INPUT_GET,'action',FILTER_DEFAULT);
    $serie=filter_input(INPUT_GET,'serie', FILTER_VALIDATE_INT);

    if($action):

      switch ($action):

        case 'delete':
            $deleteSerie= new AdminSeries();
            $deleteSerie->ExeDeleteSerie($serie);

            MSGErro($deleteSerie->getError()[0], $deleteSerie->getError()[1]);
        break;
        
        default:
           MSGErro("Ação não existe, utilize os botões!", MSG_ALERT); 
        break;

      endswitch;

    endif;  


  ?>

 <h4 class="text text-primary text-center">Series adicionadas a esta categoria</h4>
  <table class="table table-striped table-bordered table-hover">
      <thead>
          <tr>
              <th class="text-center">Serie</th>
              <th class="text-center">Tipo de ensino</th>
              <th class="text-center">-</th>
          </tr>
      </thead>
      <tbody>
      <?php
        $readSerie= new Read();
        $readSerie->FullRead("SELECT * FROM `cs_series` INNER JOIN cs_category_series ON series_id=category_series INNER JOIN cs_category ON category_id=category_category INNER JOIN cs_category_education ON education_id=category_education WHERE category_id={$category} ORDER BY series_name ASC");
        if($readSerie->getResult()):
          foreach ($readSerie->getResult() as $serie):
            extract($serie);
          
      ?>
          <tr>
              <td class="text-center"><?=$series_name?></td>
              <td class="text-center"><?=$education_name?></td>
              <td class="text-center">
              <span><?php echo "<a class=\"glyphicon glyphicon-trash\" href=\"dashboard.php?exe=categorias/details&category={$category}&serie={$category_series_id}&action=delete\" onclick=\"return confirm('Deseja realmente remover esta serie desta categoria ?')\" title=\"Deletar\"></a>";?></span>
              </td>
          </tr>
      <?php
          endforeach;
        else:
          MSGErro("<b>Não foram adicionada(s) serie(s) a esta categoria!</b>",MSG_ALERT);      
        endif;
      ?>    
      </tbody> 
  </table>    



 

</div>
