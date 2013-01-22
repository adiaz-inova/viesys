<?php
define('MODULO', 400);

	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;
	$Fresponsable = '';
	$Ftel = '0';
	switch($task) {
		case 'delete':
	
			$qry = "UPDATE servicios SET id_est = 2 WHERE id_ser = ".$Fid;

		break;
		case 'alter':

		 	$qry = "UPDATE servicios SET nombre=UPPER('$Fnombre') , id_tip_ser=$Ftipo , tel='$Ftel' , email=LOWER('$Femail') , dir=UPPER('$Fdireccion') , id_est=$Festatus WHERE id_ser = ".$Fid;


		break;
		case 'add':
				$qry = "INSERT INTO servicios ( fec_ingreso, nombre, id_tip_ser, tel, email, dir, id_est)
				VALUES (now(), UPPER('".$Fnombre."'), $Ftipo, '".$Ftel."', LOWER('".$Femail."'), UPPER('".$Fdireccion."'), 1)";
		
		break;
	}
	#echo $qry.'<<-';

	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();

	echo $op;
		
	mysql_close($conn);

?>