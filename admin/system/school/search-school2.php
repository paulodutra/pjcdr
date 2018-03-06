<?php
$search = filter_input(INPUT_POST, 'search', FILTER_DEFAULT);

if (!empty($search)):

    $search = strip_tags(trim(urlencode($search)));

    header('Location: ' . HOME . '/admin/dashboard.php?exe=school/result-school&p='.$search);
    
endif;
?>
<form name="formSchool"  action="" method="post" enctype="multipart/form-data">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">

                <h4 class="text text-primary text-center">Pesquisar Escola</h4>

                <div class="form-group col-lg-4">
                    <label>INEP OU CNPJ: </label>
                    <input type="text" class="form-control"  name="search" placeholder="INEP ou CNPJ" title="Informe codigo INEP ou CNPJ" required>
                </div>

            </div><!--row-->
            <div class="text-center">
                <input type="submit" class="btn btn-success" value="Buscar Escola" name="sendSearch">           
            </div>
        </div>
    </div>
</form>
