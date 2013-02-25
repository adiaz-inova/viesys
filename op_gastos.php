<?php
define('MODULO', 1300);

	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	switch($task) {
		case 'delete':
	
			$qry = "UPDATE gastos SET id_est = 2 WHERE id_gas = ".$Fid;

		break;
		case 'alter':

		 	$qry = "UPDATE gastos SET identificador = $Fident, fecha = '$Ffecha', responsable = $Fresp, id_tip_pag = $Fpago, id_tipo_gas = $Fgasto, comprobante = '$Fcomp', id_est = $Festatus WHERE id_gas = ".$Fid;

		break;
		case 'add':

			$qry = "INSERT INTO gastos ( identificador, fecha, responsable, id_tip_pag, id_tipo_gas, comprobante, id_est)
				VALUES($Fident, '$Ffecha', $Fresp, $Fpago, $Fgasto, '$Fcomp', $Festatus)";
		
		break;
	}
	#echo $qry.'<<-';

	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();

	echo $op;
		
	mysql_close($conn);

?>