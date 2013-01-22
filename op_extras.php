<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session
	/* 
	* opextras.php
	* modulo			Administracion
	* creado			01/08/2011
	* modificado  04/08/2011
	* autor			  I.S.C. Alejandro Diaz Garcia
	*/
	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;
	$sql = "";
	switch($tipo) {
		case 'notas':
				if($task == 'add')
				{
			
					$qry = "insert into eventos_notas (id_eve, notas, falta, id_emp) values($id_eve, '$Fnotas', now(), ".$_SESSION[TOKEN.'USUARIO_ID'].")";
			
				}
		break;
		case 'empleado':
				if($task == 'suspend')
				{
			
					$qry = "UPDATE empleados SET id_est= 3 WHERE id_emp= ".$id." ";
			
				}else if($task == 'activate')
				{
			
					$qry = "UPDATE empleados SET id_est= 1 WHERE id_emp= ".$id." ";
			
				}
		break;
		case 'grupo':
				if($task == 'suspend')
				{
			
					$qry = "UPDATE grupo SET id_est= 3 WHERE id_gru= ".$id." ";
			
				}else if($task == 'activate')
				{
			
					$qry = "UPDATE grupo SET id_est= 1 WHERE id_gru= ".$id." ";
			
				}
		break;
		case 'cliente':
				if($task == 'suspend')
				{
			
					$qry = "UPDATE clientes SET id_est= 3 WHERE id_cli= ".$id." ";
			
				}else if($task == 'activate')
				{
			
					$qry = "UPDATE clientes SET id_est= 1 WHERE id_cli= ".$id." ";
			
				}
		break;
		case 'salon':
				if($task == 'suspend')
				{
			
					$qry = "UPDATE salones SET id_est= 3 WHERE id_sal= ".$id." ";
			
				}else if($task == 'activate')
				{

			
					$qry = "UPDATE salones SET id_est= 1 WHERE id_sal= ".$id." ";
			
				}
		break;
		case 'servicio':
				if($task == 'suspend')
				{
			
					$qry = "UPDATE servicios SET id_est= 3 WHERE id_ser= ".$id." ";
			
				}else if($task == 'activate')
				{

			
					$qry = "UPDATE servicios SET id_est= 1 WHERE id_ser= ".$id." ";
			
				}
		break;
		case 'factura':
				if($task == 'suspend')
				{
			
					$qry = "UPDATE facturas SET id_est= 3 WHERE id_fac= ".$id." ";
			
				}else if($task == 'activate')
				{

			
					$qry = "UPDATE facturas SET id_est= 1 WHERE id_fac= ".$id." ";
			
				}
		break;
		case 'pago':
				if($task == 'suspend')
				{
			
					$qry = "UPDATE pagos SET id_est= 3 WHERE id_pag= ".$id." ";
			
				}else if($task == 'activate')
				{

			
					$qry = "UPDATE pagos SET id_est= 1 WHERE id_pag= ".$id." ";
			
				}
		break;
		case 'etiquetas':	
			
			if($campo=='singular') $campo='catcapa_singular';
			if($campo=='plural') $campo='catcapa_plural';
			if($campo=='abreviacion') $campo='catcapa_abreviacion';
				$qry = "UPDATE cat_capas SET ".$campo."= UPPER('".$nuevovalor."') WHERE CATCAPA_ID= ".$id." ";

		break;
		case 'servidores':	
			require('includes/conectionv2.php');
			
			if($task=='update')
			{
				$qry = "UPDATE set_servers SET ".$campo."= '".$nuevovalor."' WHERE setservers_id= ".$id." ";
			}else if($task=='add')
			{
				$qry = "INSERT INTO set_servers (setservers_id, funcionalidad, ip, puerto, url) VALUES(nextval('setservers_id'), '".$Ffuncionalidad."', '".$Fip."', '".$Fpuerto."', '".$Furl."')";			
			}
			
			$stid = pg_query($conn2,$qry) or die('Hay un error en la consulta: ' . pg_last_error());
			$op = pg_affected_rows($stid);
		
			echo $op;
				
			pg_close($conn2);			
			exit;

		break;
		case 'form_param':	
			
			$valorconstante=(isset($Ftipodato) && $Ftipodato==1)? $Ffactor:'null';
			$campobd=(isset($Fcampo) && isset($Ftipodato) && $Ftipodato==2)? $Fcampo:'';
			$tablabd=(isset($Ftabla) && isset($Ftipodato) && $Ftipodato==2)? $Ftabla:'';
			
			if($task=='update')
			{
				$qry = "UPDATE cat_parametrosformulas 
				SET catparamformula_descripcion='".$Fdescrip."', catparamformula_alias='".$Falias."', catparamformula_valorconstante=$valorconstante, catparamformula_tablabd='".$tablabd."', catparamformula_campobd='".$campobd."', catparamformula_tipovalor=$Ftipodato
				WHERE catparamformula_id= ".$Fid." ";
			
			}else if($task=='add')
			{
				$qry = "INSERT INTO cat_parametrosformulas(catparamformula_descripcion, catparamformula_alias, catparamformula_valorconstante, catparamformula_tablabd, catparamformula_campobd, catparamformula_tipovalor)
VALUES ('".$Fdescrip."', '".$Falias."', $valorconstante, '".$tablabd."', '".$campobd."', $Ftipodato);";
			
			}else if($task=='del')
			{
				$qry = "DELETE FROM cat_parametrosformulas WHERE catparamformula_id= ".$id." ";			
			
			}

		break;

	}

	echo $tipo;
	echo $qry;
	exit;
	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();

	echo $op;
		
	mysql_close($conn);
?>