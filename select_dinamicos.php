<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session

	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');


	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	switch($tipo) {
		case 'servicios':
	
			

			?>
			SERVICIOS
			<br />
			<select class="" name="Fservicio" id="Fservicio" req="req" lab="Servicios" onchange="enfocarcobro()" >
				<option value="">seleccione</option>
				<?php
					$sql="select ser.id_ser id, ser.nombre
					from servicios ser
					where id_tip_ser=".$id_tip_ser." order by nombre";
					
					$stid = mysql_query($sql);

					while (($rowPer = mysql_fetch_assoc($stid))) {
						echo '	<option value="'.$rowPer['id'].'">'.$rowPer['nombre'].'</option>';
					}
				?>
			</select>
			<?php

		break;
	}

		
	mysql_close($conn);

?>