<?php
define('MODULO', 900);

	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	switch($task) {
		case 'delete':
	
			//$qry = "UPDATE pagos SET id_est = 2 WHERE id_pag = ".$Fid;

			#antes de insertar debe verificar que no esta pagado
			$sql = "SELECT distinct ev.id_eve, ev.pagado, ev.facturar, ev.cos_tot, pag.id_eve evento, subtotal*(-1) subtotal, iva*(-1) iva, total*(-1) total, num_pago numpag from eventos ev, pagos pag WHERE 1=1 AND ev.id_eve=pag.id_eve AND pag.id_pag=  ".$Fid;
			$stid = mysql_query($sql);
			if($row = mysql_fetch_assoc($stid)) {
					$qry = "INSERT INTO pagos (id_eve, num_pago, subtotal, iva, total, id_tip_pag, id_est, fec_pag, tip_pag_otro)
					VALUES (".$row['evento'].", ".$row['numpag'].", '".$row['subtotal']."', '".$row['iva']."', '".$row['total']."', 5, 1, NOW(), 'DEVOLUCION')";

					$qryNotas = "insert into eventos_notas (id_eve, notas, falta, id_emp) values(".$row['evento'].", 'Pago($ ".$row['total'].") cancelado por: ".$_SESSION[TOKEN.'NOMBRES']." ".$_SESSION[TOKEN.'APATERNO']."', now(), ".$_SESSION[TOKEN.'USUARIO_ID'].")";
			}
		break;
		case 'alter':

			#, id_eve  --- no puede cambiar el evento solo un administrador o algo asi
			
			#hay que formatear cantidades antes de insertar
			$Fsubtotal = (isset($Fsubtotal) && $Fsubtotal != '') ? formatInput($Fsubtotal) : 0;
			$Fiva = (isset($Fiva) && $Fiva != '') ? formatInput($Fiva) : 0;
			$Ftotal = (isset($Ftotal) && $Ftotal != '') ? formatInput($Ftotal) : 0;
			$Fotro = (isset($Fotro) && $Fotro != '') ? $Fotro : 'NULL';

		 	$qry = "UPDATE pagos SET recibo='$Frecibo', tip_pag_otro = UPPER('$Fotro'), num_pago = $Fnumpago, subtotal = '$Fsubtotal', iva = '$Fiva', total = '$Ftotal', id_tip_pag = $Ftipo , id_est = $Festatus , fec_pag = '$Ffecha' WHERE id_pag = ".$Fid;

		 	$qryNotas = "insert into eventos_notas (id_eve, notas, falta, id_emp) values((select id_eve from pagos where id_pag=".$Fid."), 'Pago($ ".$Ftotal.") modificado por: ".$_SESSION[TOKEN.'NOMBRES']." ".$_SESSION[TOKEN.'APATERNO']."', now(), ".$_SESSION[TOKEN.'USUARIO_ID'].")";

		break;
		case 'add':
			$Fsubtotal = (isset($Fsubtotal) && $Fsubtotal != '') ? formatInput($Fsubtotal) : 0;
			$Fiva = (isset($Fiva) && $Fiva != '') ? formatInput($Fiva) : 0;
			$Ftotal = (isset($Ftotal) && $Ftotal != '') ? formatInput($Ftotal) : 0;
			$Fotro = (isset($Fotro) && $Fotro != '') ? $Fotro : 'NULL';
	
			#antes de insertar debe verificar que no esta pagado
			$sql = "SELECT distinct ev.id_eve, ev.pagado, ev.facturar, ev.cos_tot from eventos ev WHERE 1=1 AND ev.id_eve=".$Fnoevento;
			$stid = mysql_query($sql);
			if($row = mysql_fetch_assoc($stid)) {
				if($row['pagado'] != 1) {
					$qry = "INSERT INTO pagos (recibo, id_eve, num_pago, subtotal, iva, total, id_tip_pag, id_est, fec_pag, tip_pag_otro)
					VALUES ('$Frecibo', $Fnoevento, $Fnumpago, '$Fsubtotal', '$Fiva', '$Ftotal', $Ftipo, 1, '$Ffecha', '$Fotro')";

					$qryNotas = "insert into eventos_notas (id_eve, notas, falta, id_emp) values($Fnoevento, 'Pago($ ".$Ftotal.") recibido por: ".$_SESSION[TOKEN.'NOMBRES']." ".$_SESSION[TOKEN.'APATERNO']."', now(), ".$_SESSION[TOKEN.'USUARIO_ID'].")";

				}

			}

		break;
	}
	//echo $qry;

	$stid = mysql_query($qry);
	$op = mysql_affected_rows();
	if($op != -1 && isset($qryNotas) && $qryNotas!= '') {
		$stid = mysql_query($qryNotas);
	}

	echo $op;
		
		#despues de agregar el pago vamos a validar si ya se completo el pago del evento
		$sqltotal = "select sum(total)pagado from pagos where id_est=1 and id_eve=".$row['id_eve'];
		$stidtotal = mysql_query($sqltotal);
		$row_total = mysql_fetch_assoc($stidtotal);

		//echo $row['cos_tot'].'<->'.$row_total['pagado'];


		if($row['facturar']==1)
				$costoevento=$row['cos_tot']*1.16;
		else 
				$costoevento=$row['cos_tot'];

		if($costoevento <= $row_total['pagado']) {
			$sql = "UPDATE eventos set pagado='1' WHERE id_eve=".$row['id_eve']." AND estatus='VENDIDO'";
		}
		else {
			$sql = "UPDATE eventos set pagado='0' WHERE id_eve=".$row['id_eve']." AND estatus='VENDIDO'";
		}
			//echo $sql;
			mysql_query($sql);

	mysql_close($conn);

?>