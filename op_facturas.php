<?php
define('MODULO', 600);

	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	switch($task) {
		case 'delete':
	
			$qry = "UPDATE facturas SET id_est = 2 WHERE id_fac = ".$Fid;

		break;
		case 'alter':

		 	$qry = "UPDATE facturas SET id_eve=$Fevento, num_fac = $Fnumfac, fecha = '$Ffecha', id_cli = $Fcliente, subtotal = '$Fsubtotal', iva = '$Fiva', total = '$Ftotal', id_est = $Festatus WHERE id_fac = ".$Fid;

		break;
		case 'add':

			$Fsubtotal = (isset($Fsubtotal) && $Fsubtotal != '') ? formatInput($Fsubtotal) : 0;
			$Fiva = (isset($Fiva) && $Fiva != '') ? formatInput($Fiva) : 0;
			$Ftotal = (isset($Ftotal) && $Ftotal != '') ? formatInput($Ftotal) : 0;

			$qry = "INSERT INTO facturas ( num_fac, fecha, id_cli, subtotal, iva, total, id_est, id_eve)
				VALUES($Fnumfac, '$Ffecha', $Fcliente, '$Fsubtotal', '$Fiva', '$Ftotal', $Festatus, $Fevento)";
		
		break;
	}
	#echo $qry.'<<-';

	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();

	echo $op;
		
	mysql_close($conn);

?>