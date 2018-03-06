<?php
$search = filter_input(INPUT_POST, 'search', FILTER_DEFAULT);

if (!empty($search)):

    $search = strip_tags(trim(urlencode($search)));

    header('Location: ' . HOME . '/admin/dashboard.php?exe=school/result-school&p=' . $search);

endif;
?>
<form name="formSchool"  action="" method="post" enctype="multipart/form-data">
    <div class="tab-content">

        <div id="step1" class="p-m tab-pane active">
            <div class="row">

                <h4 class="text text-primary text-center">Pesquisar Escola</h4>

                <div class="form-group col-lg-4">
                    <label>Escolha o parametro de busca:</label>
                    <select class="form-control" id="parametro" required>
                        <option value="" selected> Selecione o parametro de busca</option>
                        <option value="1">INEP</option>
                        <option value="2">CNPJ</option>
                        <option value="3">Nome ou parte do nome</option>
                    </select>
                </div>
                <div id="cnpjvalue">
                    <div class="form-group col-lg-4">
                        <label>CNPJ: </label>
                        <input type="text" class="form-control"  id="cnpj" name="search" placeholder="CNPJ" title="Informe o CNPJ" required>
                    </div>
                </div>   
                <div id="inepvalue">
                    <div class="form-group col-lg-4">
                        <label>INEP: </label>
                        <input type="text" class="form-control"  id="inep" name="search" placeholder="INEP" title="Informe codigo INEP" pattern="[0-9]+$" required>*somente numeros
                    </div>
                </div>
                 <div id="nomevalue">
                    <div class="form-group col-lg-4">
                        <label>Nome ou parte do nome: </label>
                        <input type="text" class="form-control"  id="nome" name="search" placeholder="Nome ou parte do nome da escola" title="Informe nome ou parte do nome da escola" required>
                    </div>
                </div>

            </div><!--row-->
            <div class="text-center">
                <input type="submit" class="btn btn-success" value="Buscar Escola" name="sendSearch">           
            </div>
        </div>
    </div>
</form>
