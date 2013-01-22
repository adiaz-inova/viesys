<?php
define('MODULO', 900);

	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	switch($task) {
		case 'delete':
	
			$qry = "UPDATE pagos SET id_est = 2 WHERE id_pag = ".$Fid;

		break;
		case 'alter':

			#, id_eve  --- no puede cambiar el evento solo un administrador o algo asi
			
			#hay que formatear cantidades antes de insertar
			$Fsubtotal = (isset($Fsubtotal) && $Fsubtotal != '') ? formatInput($Fsubtotal) : 0;
			$Fiva = (isset($Fiva) && $Fiva != '') ? formatInput($Fiva) : 0;
			$Ftotal = (isset($Ftotal) && $Ftotal != '') ? formatInput($Ftotal) : 0;
			$Fotro = (isset($Fotro) && $Fotro != '') ? $Fotro : 'NULL';

		 	$qry = "UPDATE pagos SET tip_pag_otro = UPPER('$Fotro'), num_pago = $Fnumpago, subtotal = '$Fsubtotal', iva = '$Fiva', total = '$Ftotal', id_tip_pag = $Ftipo , id_est = $Festatus , fec_pag = '$Ffecha' WHERE id_pag = ".$Fid;

		break;
		case 'add':

			$Fsubtotal = (isset($Fsubtotal) && $Fsubtotal != '') ? formatInput($Fsubtotal) : 0;
			$Fiva = (isset($Fiva) && $Fiva != '') ? formatInput($Fiva) : 0;
			$Ftotal = (isset($Ftotal) && $Ftotal != '') ? formatInput($Ftotal) : 0;
			$Fotro = (isset($Fotro) && $Fotro != '') ? $Fotro : 'NULL';
	
			#antes de insertar debe verificar que no esta pagado
			$sql = "SELECT distinct ev.pagado, ev.facturar, ev.cos_tot from eventos ev WHERE 1=1 AND ev.id_eve=".$Fnoevento;
			$stid = mysql_query($sql);
			if($row = mysql_fetch_assoc($stid)) {
				if($row['pagado'] != 1) {
					$qry = "INSERT INTO pagos (id_eve, num_pago, subtotal, iva, total, id_tip_pag, id_est, fec_pag, tip_pag_otro)
					VALUES ($Fnoevento, $Fnumpago, '$Fsubtotal', '$Fiva', '$Ftotal', $Ftipo, 1, '$Ffecha', '$Fotro')";
					#mysql_query($qry);

					#despues de agregar el pago vamos a validar si ya se completo el pago del evento
					if($row['facturar'] == 0) {
						$sql = "select sum(subtotal)pagado from pagos where id_est=1 and id_eve=".$Fnoevento;
					}else{
						$sql = "select sum(total)pagado from pagos where id_est=1 and id_eve=".$Fnoevento;
					}
					$stid = mysql_query($sql);
					$row_pagado = mysql_fetch_assoc($stid);

					#echo $row['cos_tot'].'<->'.$row_pagado['pagado'];
					
					if($row['cos_tot'] <= $row_pagado['pagado']) {
						$sql = "UPDATE eventos set pagado='1' WHERE id_eve=".$Fnoevento." AND estatus='VENDIDO'";
						mysql_query($sql);
					}
				}

			}
		
		break;
	}
	#echo $qry;

	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();

	echo $op;
		
	mysql_close($conn);

?>