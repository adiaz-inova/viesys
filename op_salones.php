<?php
define('MODULO', 300);
	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	switch($task) {
		case 'delete':
	
			$qry = "UPDATE salones SET id_est = 2 WHERE id_sal = ".$Fid;

		break;
		case 'alter':

			$Fporc = (isset($Fporc) && $Fporc != '')? ', porcentaje='.$Fporc : ', porcentaje=null';

		 	$qry = "UPDATE salones SET empresa=UPPER('$Fempresa'), nombre=UPPER('$Fnombre'), responsable=UPPER('".$Fresponsable."')
					, tel = '".$Ftel."', tel2 = '".$Ftel2."', email=LOWER('".$Femail."'), dir=UPPER('".$Fdireccion."'), id_est=".$Festatus.$Fporc." WHERE id_sal = ".$Fid;

		break;
		case 'add':
			
			$Fporc = (isset($Fporc) && $Fporc != '')? $Fporc : 'null';
			$qry = "INSERT INTO salones ( empresa, nombre, responsable, fec_ingreso, tel, tel2, email, dir, id_est, porcentaje)
			VALUES (UPPER('".$Fempresa."'), UPPER('".$Fnombre."'), UPPER('".$Fresponsable."'), now(), '".$Ftel."', '".$Ftel2."', LOWER('".$Femail."'), UPPER('".$Fdireccion."'), 1, $Fporc)";
		
		break;
	}
	#echo $qry;

	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();

	echo $op;
		
	mysql_close($conn);

?>