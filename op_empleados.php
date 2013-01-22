<?php
	require('includes/getvalues.php');
	
	if(!isset($Fself)) {
		define('MODULO', 100);
	}else {
		$PaSpOrT = true; //esta pagino no requiere permisos, solo session
	}

	require_once('includes/session_principal.php');
	require('includes/conection.php');

	if(isset($Fself)) {
		
		$Fidusuario = $_SESSION[TOKEN.'USUARIO_ID'];
		$Festatus = 1;
	}

	# Si la tarea es delete dar de baja al usuario | Si tarea es alter modificar datos
	$op = 0;	
	switch($task) {
		case 'delete':
	
			$qry = "UPDATE empleados SET id_est = 2 WHERE id_emp = ".$Fid;

		break;
		case 'alter':

			if($Fpwd != '') {
							
				# Incluyo archivo de funciones para generar password
				require_once('includes/functions.php');
	
				# Encriptamos el password
				$Fpwd = josHashPassword($Fpwd);
							
				$qry = "
				UPDATE empleados SET nombre=UPPER('".$Fnombres."'), ape_pat=UPPER('".$Fapaterno."'), ape_mat=UPPER('".$Famaterno."'), tel='".$Ftel."', email='".$Fusuario."', dir='".$Fdireccion."', psw='".$Fpwd."', id_est=".$Festatus.", id_gru=".$Fperfil."
				WHERE id_emp=".$Fidusuario;
			}else {
			
				$qry = "
				UPDATE empleados SET nombre=UPPER('".$Fnombres."'), ape_pat=UPPER('".$Fapaterno."'), ape_mat=UPPER('".$Famaterno."'), tel='".$Ftel."', email='".$Fusuario."', dir='".$Fdireccion."', id_est=".$Festatus.", id_gru=".$Fperfil."
				WHERE id_emp=".$Fidusuario;
				
			}
		break;
		case 'add':
			# Incluyo archivo de funciones para generar password
			require_once('includes/functions.php');
			
			# Encriptamos el password
			$Fpwd = josHashPassword($Fpwd);
			
			$qry = "INSERT INTO empleados(nombre, ape_pat, ape_mat, tel, email, dir, psw, id_est, id_gru) 
					VALUES (UPPER('".$Fnombres."'), UPPER('".$Fapaterno."'), UPPER('".$Famaterno."'), '".$Ftel."', '".$Fusuario."', '".$Fdireccion."', '".$Fpwd."', 1, ".$Fperfil.")";
		

		break;
	}
	echo $qry.'<br>';

	$stid = mysql_query($qry);
		
	$op = mysql_affected_rows();

	echo $op;
		
	mysql_close($conn);

?>