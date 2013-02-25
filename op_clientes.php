<?php
define('MODULO', 200);
	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	switch($task) {
		case 'delete':
	
			$qry = "UPDATE clientes SET id_est = 2 WHERE id_cli = ".$Fid;

		break;
		case 'alter':
							
			$qry = "UPDATE clientes SET empresa=UPPER('".$Fempresa."'), nombre=UPPER('".$Fnombres."'), ape_pat=UPPER('".$Fapaterno."'), ape_mat=UPPER('".$Famaterno."'), tel='".$Ftel."', tel2='".$Ftel2."', email='".$Femail."', dir=UPPER('".$Fdireccion."'), id_est=".$Festatus.", procedencia='".$Fprocedencia."'
			WHERE id_cli=".$Fid;
				
			
		break;
		case 'add':

			$qry = "INSERT INTO clientes(empresa, nombre, ape_pat, ape_mat, tel, tel2, email, dir, id_est, procedencia) 
					VALUES (
						UPPER('".$Fempresa."'), UPPER('".$Fnombres."'), UPPER('".$Fapaterno."')
						, UPPER('".$Famaterno."'), '".$Ftel."', '".$Ftel2."', '".$Femail."', UPPER('".$Fdireccion."'), 1, '".$Fprocedencia."')";
		

		break;
	}
	#echo $qry.'<br>';

	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();

	echo $op;
		
	mysql_close($conn);

?>