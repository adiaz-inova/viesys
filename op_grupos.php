<?php
define('MODULO', 700);

	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	switch($task) {
		case 'delete':
	
			$qry = "UPDATE grupo SET id_est = 2 WHERE id_gru = ".$Fid;

		break;
		case 'alter':

			$qry = "
			UPDATE grupo SET nombre=UPPER('".$Fnombre."'), descripcion=UPPER('".$Fdescripcion."'), id_est=".$Festatus."
			WHERE id_gru=".$Fid;
			
			$stid = mysql_query($qry);
				
			$qry2 = "DELETE FROM permisos WHERE id_gru=".$Fid;
			mysql_query($qry2);

			if(isset($_POST['Fmodulos'])) {
				$Fmodulos = $_POST['Fmodulos'];
				if(count($Fmodulos)>0) {
					
						foreach ($Fmodulos as $Fmodulo) {

							$qry3 = "INSERT INTO permisos (id_mod, id_gru) values(".$Fmodulo.",".$Fid.")";
							if(!$stid3 = mysql_query($qry3))
								echo 'Ocurrio un error al actualizar los permisos del grupo.'.$qry3.'<br>';
							
						}
				}
			}

			echo 1;
			mysql_close($conn);
			exit;

		break;
		case 'add':
			
			$qry = "INSERT INTO grupo(nombre, descripcion, id_est) 
					VALUES (UPPER('".$Fnombre."'), UPPER('".$Fdescripcion."'), 1)";
			
			$stid = mysql_query($qry);
			$Fid = mysql_insert_id();

			if(isset($_POST['Fmodulos'])) {
				$Fmodulos = $_POST['Fmodulos'];
				if(count($Fmodulos)>0) {
					
						foreach ($Fmodulos as $Fmodulo) {

							$qry3 = "INSERT INTO permisos (id_mod, id_gru) values(".$Fmodulo.",".$Fid.")";
							if(!$stid3 = mysql_query($qry3))
								echo 'Ocurrio un error al actualizar los permisos del grupo.'.$qry3.'<br>';
							
						}
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

	echo $op;
		
	mysql_close($conn);

?>