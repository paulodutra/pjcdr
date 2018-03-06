<?php
	$readRegion= new Read();

	$Norte=$readRegion->FullRead("SELECT COUNT(subscribers_concurso) AS Norte FROM cs_concurso INNER JOIN cs_concurso_subscribers ON concurso_id=subscribers_concurso INNER JOIN es_school ON school_id=subscribers_school INNER JOIN app_estados ON estado_id=school_uf WHERE EXISTS (SELECT MAX(concurso_id) FROM cs_concurso) AND estado_regiao='Norte'");
	$Norte=$readRegion->getResult()[0]['Norte'];

	$Sul=$readRegion->FullRead("SELECT COUNT(subscribers_concurso) AS Sul FROM cs_concurso INNER JOIN cs_concurso_subscribers ON concurso_id=subscribers_concurso INNER JOIN es_school ON school_id=subscribers_school INNER JOIN app_estados ON estado_id=school_uf WHERE EXISTS (SELECT MAX(concurso_id) FROM cs_concurso) AND estado_regiao='Sul'");
	$Sul=$readRegion->getResult()[0]['Sul'];

	$centroOeste=$readRegion->FullRead("SELECT COUNT(subscribers_concurso) AS Centro FROM cs_concurso INNER JOIN cs_concurso_subscribers ON concurso_id=subscribers_concurso INNER JOIN es_school ON school_id=subscribers_school INNER JOIN app_estados ON estado_id=school_uf WHERE EXISTS (SELECT MAX(concurso_id) FROM cs_concurso) AND estado_regiao='Centro-Oeste'");
	$centroOeste=$readRegion->getResult()[0]['Centro'];

	$Nordeste=$readRegion->FullRead("SELECT COUNT(subscribers_concurso) AS Nordeste FROM cs_concurso INNER JOIN cs_concurso_subscribers ON concurso_id=subscribers_concurso INNER JOIN es_school ON school_id=subscribers_school INNER JOIN app_estados ON estado_id=school_uf WHERE EXISTS (SELECT MAX(concurso_id) FROM cs_concurso) AND estado_regiao='Nordeste'");
	$Nordeste=$readRegion->getResult()[0]['Nordeste'];
	
	$Sudeste=$readRegion->FullRead("SELECT COUNT(subscribers_concurso) AS Sudeste FROM cs_concurso INNER JOIN cs_concurso_subscribers ON concurso_id=subscribers_concurso INNER JOIN es_school ON school_id=subscribers_school INNER JOIN app_estados ON estado_id=school_uf WHERE EXISTS (SELECT MAX(concurso_id) FROM cs_concurso) AND estado_regiao='Sudeste'");
	$Sudeste=$readRegion->getResult()[0]['Sudeste'];



?>
<div class="row">
 	<h4 class="text text-primary text-center">Inscrições do concurso por Região</h4>
 	<div class="alert alert-warning" role="alert">
 		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
 		<b>Aviso:</b><p>Este relatório se aplica ao concurso atual</p>
 	</div>
		<div class="col-lg-10 text-center">
			<div class="col-lg-2">
				<a class="btn btn-block btn-success" href="dashboard.php?exe=subscribers/details-region&region=Norte">Norte <span class="badge"><?=$Norte=($Norte=='' ? '0' : $Norte)?></span></a>
			</div>
			<div class="col-lg-2">
				<a class="btn btn-block btn-primary" href="dashboard.php?exe=subscribers/details-region&region=Sul">Sul <span class="badge"><?=$Sul=($Sul=='' ? '0' : $Sul)?></span></a>
			</div>
			<div class="col-lg-2">
				<a class="btn btn-block btn-warning" href="dashboard.php?exe=subscribers/details-region&region=Centro-Oeste">Centro-Oeste <span class="badge"><?=$centroOeste=($centroOeste=='' ? '0' : $centroOeste)?></span></a>
			</div>
			<div class="col-lg-2">
				<a class="btn btn-block btn-info" href="dashboard.php?exe=subscribers/details-region&region=Nordeste">Nordeste <span class="badge"><?=$Nordeste=($Nordeste=='' ? '0' : $Nordeste)?></span></a>
			</div>
			<div class="col-lg-2">
				<a class="btn btn-block btn-default" href="dashboard.php?exe=subscribers/details-region&region=Sudeste">Sudeste <span class="badge"><?=$Sudeste=($Sudeste=='' ? '0' : $Sudeste)?></span></a>
			</div>
		</div>
</div>	