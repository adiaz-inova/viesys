<?php
define('MODULO', 500);

	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

#'COTIZADO','RECHAZADO','VENDIDO','CANCELADO','TERMINADO'
	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	$qryNotas = '';
	switch($task) {
		case 'sell':
	
			$qry = "UPDATE eventos SET estatus = 'VENDIDO' WHERE id_eve = ".$Fid." AND estatus = 'COTIZADO'";

			$qryNotas = "insert into eventos_notas (id_eve, notas, falta, id_emp) values($Fid, 'Evento vendido por: ".$_SESSION[TOKEN.'NOMBRES']." ".$_SESSION[TOKEN.'APATERNO']."', now(), ".$_SESSION[TOKEN.'USUARIO_ID'].")";

		break;
		case 'reject':
	
			$qry = "UPDATE eventos SET estatus = 'RECHAZADO' WHERE id_eve = ".$Fid." AND estatus = 'COTIZADO'";

			$qryNotas = "insert into eventos_notas (id_eve, notas, falta, id_emp) values($Fid, 'Evento rechazado por: ".$_SESSION[TOKEN.'NOMBRES']." ".$_SESSION[TOKEN.'APATERNO']."', now(), ".$_SESSION[TOKEN.'USUARIO_ID'].")";

		break;
		case 'cancel':
	
			$qry = "UPDATE eventos SET estatus = 'CANCELADO' WHERE id_eve = ".$Fid." AND estatus = 'VENDIDO'";

			$qryNotas = "insert into eventos_notas (id_eve, notas, falta, id_emp) values($Fid, 'Evento cancelado por: ".$_SESSION[TOKEN.'NOMBRES']." ".$_SESSION[TOKEN.'APATERNO']."', now(), ".$_SESSION[TOKEN.'USUARIO_ID'].")";

		break;
		case 'finish':
	
			$qry = "UPDATE eventos SET estatus = 'TERMINADO' WHERE id_eve = ".$Fid." AND estatus = 'VENDIDO'";

		break;
		case 'alter':

			$qryNotas = "insert into eventos_notas (id_eve, notas, falta, id_emp) values($Fid, 'Evento modificado por: ".$_SESSION[TOKEN.'NOMBRES']." ".$_SESSION[TOKEN.'APATERNO']."', now(), ".$_SESSION[TOKEN.'USUARIO_ID'].")";

			$num_personas = (isset($Fpersonas) && $Fpersonas!='')? $Fpersonas : 'null';
			$pagado = (isset($Fpagado) && $Fpagado!='')? $Fpagado : 0;

			$Fcosto = $_POST['Fcostoxserv'];
			$cos_total = 0;
			foreach ($Fcosto as $costo) {
				$cos_total += $costo;
			}
			
			$cos_total = number_format($cos_total, 2, '.', ''); # muy importante formatear flotantes 
			$Festatus = (isset($Festatus) && $Festatus!='')? " , id_est=".$Festatus." ":""; 
			$Ffactura = (isset($Ffactura) && $Ffactura=='1')?'1':'0';
			//, pagado=".$pagado."


			$qry = "
			UPDATE eventos SET id_cli=".$Fcliente.", id_sal=".$Fsalon."
			, id_tip_eve=".$Ftipo.", fecha='".$Ffecha."', hora='".$Fhora.":".$Fminuto."', num_personas=".$num_personas."
			$Festatus, cos_tot='".$cos_total."', facturar='".$Ffactura."'
			WHERE id_eve=".$Fid;
			
			$stid = mysql_query($qry);
				
			$qry2 = "DELETE FROM servicios_eventos WHERE id_eve=".$Fid;
			mysql_query($qry2);

			
			$Fservicios = $_POST['Fservicios'];

			if(count($Fservicios)>0) {
				$i=0;
				foreach ($Fservicios as $Fservicio) {

					$qry3 = "INSERT INTO servicios_eventos (id_ser, id_eve, costo) values(".(int)$Fservicio.",".(int)$Fid.", ".$Fcosto[$i].")";
					if(!$stid3 = mysql_query($qry3))
						echo 'Ocurrio un error al actualizar los permisos del grupo.'.$qry3.'<br>';
					$i++;
				}					

			}				

			echo 1;
			$op = mysql_affected_rows();
			
			if(isset($qryNotas) && $qryNotas!= '') {
				$stid = mysql_query($qryNotas);
			}

			mysql_close($conn);
			exit;

		break;
		case 'add':

			$num_personas = (isset($Fpersonas) && $Fpersonas!='')? $Fpersonas : 'null';
			$pagado = (isset($Fpagado) && $Fpagado!='')? $Fpagado : 0;
			$Ffactura = (isset($Ffactura) && $Ffactura=='1')?'1':'0';

			$Fcosto = $_POST['Fcostoxserv'];

			$cos_total = 0;
			foreach ($Fcosto as $costo) {
				$cos_total += $costo;
			}

			# UN EVENTO NUEVO ENTRA COMO COTIZADO
			$Festatus = 11;
			
			echo $qry = "INSERT INTO eventos(id_emp, id_cli, estatus, id_sal, id_tip_eve, fecha, hora, num_personas, cos_tot, falta, facturar) 
					VALUES (".$_SESSION[TOKEN.'USUARIO_ID'].", ".$Fcliente.", 'COTIZADO', ".$Fsalon.", ".$Ftipo.", '".$Ffecha."', '".$Fhora.":".$Fminuto."', ".$num_personas.", '".formateo($cos_total)."', now(), '".$Ffactura."')";
			
			$stid = mysql_query($qry);
			$Fid = mysql_insert_id();

			$Fservicios = $_POST['Fservicios'];
			if(count($Fservicios)>0) {
				$i=0;
				foreach ($Fservicios as $Fservicio) {

					$qry3 = "INSERT INTO servicios_eventos (id_ser, id_eve, costo) values(".(int)$Fservicio.",".(int)$Fid.", '".formateo($Fcosto[$i])."')";
					if(!$stid3 = mysql_query($qry3))
						echo 'Ocurrio un error al actualizar los permisos del grupo.'.$qry3.'<br>';
				$i++;
				}

			}				

			echo 1;
			mysql_close($conn);
			exit;
		break;
	}
	#echo '<br>'.$qry.'<br>'; exit;

	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();
	if($op != -1 && isset($qryNotas) && $qryNotas!= '') {
		$stid = mysql_query($qryNotas);
	}
	echo $op;
		
	mysql_close($conn);

?>