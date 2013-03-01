<?php
define('MODULO', 7003);

	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;
	switch($task) {
		case 'delete':
	
			$qry = "DELETE FROM tipo_evento WHERE id_tip_eve = ".$Fid;

		break;
		case 'alter':

		 	$qry = "UPDATE tipo_evento SET nombre=UPPER('$Fnombre') WHERE id_tip_eve = ".$Fid;

		break;
		case 'add':
				$qry = "INSERT INTO tipo_evento ( nombre ) VALUES (UPPER('".$Fnombre."'))";
		
		break;
	}
	#echo $qry.'<<-';

	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();

	echo $op;
		
	mysql_close($conn);

?>