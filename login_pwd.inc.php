<?php
/* + + + + + + + + + + + + + + + + + + + + + 
	* VIE 2012
	* creado			08/10/2012
	* autor			  	I.S.C. Alejandro Diaz Garcia
	* Valida/Cambia el pwd de usuarios
+ + + + + + + + + + + + + + + + + + + + + */

	session_start();

	require_once('includes/functions.php');
	require_once('includes/getvalues.php');

	if($task != 'none')	
		require_once('includes/conection.php');
	
	switch($task) {
		case 'Login':
			$est = 0;

			$ip = $_SERVER['REMOTE_ADDR'];
			$hostname = gethostbyaddr($ip);
			$_SESSION['SID'] = session_id();
			$modid = 0; // Modulo al que esta accediendo el usuario
			
			$sql = "SELECT
			empleados.id_emp usuario_id,
			empleados.nombre nombres,
			empleados.ape_pat paterno,
			empleados.ape_mat materno,
			empleados.tel,
			empleados.email,
			empleados.dir,
			empleados.foto,
			empleados.admin,
			empleados.psw password,
			empleados.id_est u_estatus,
			estatus.id_est, estatus.nombre,
			grupo.id_gru grupo_id,
			grupo.nombre grupo,
			grupo.id_est g_estatus
			FROM empleados
			INNER JOIN estatus USING(id_est)
			INNER JOIN grupo USING(id_gru)
			WHERE LOWER(empleados.email)=LOWER('".$u."')
			";

			//if existe usuario
			$res = mysql_query($sql);
			if($row = mysql_fetch_assoc($res)) {
				
				$pReal = $row["password"];
				$pTemp = obtenerunPassword($p,$pReal);
				if($pReal == $pTemp){

					if($row['u_estatus']==2)
						echo 'usuario inactivo'; #usuario inactivo
					elseif($row['u_estatus']==3)
						echo 'usuario suspendido'; #usuario suspendido
					elseif($row['g_estatus']==2)
						echo 'grupo inactivo'; #grupo inactivo
					elseif($row['g_estatus']==3)
						echo 'grupo suspendido'; #grupo suspendido
					else {
						require_once('token.inc.php');
						$sql = "select per.id_mod from permisos per where per.id_gru=".$row['grupo_id'];
						$res_permisos = mysql_query($sql);

						$arr_permisos = array();
						$i = 0;
						$_SESSION[TOKEN.'PERMISOS'] = NULL;
						while($row_permisos = mysql_fetch_assoc($res_permisos)) {
							$arr_permisos[$i] = $row_permisos['id_mod'];
							$i++;
						}

						if($i > 0)
							$_SESSION[TOKEN.'PERMISOS'] = $arr_permisos;

						$_SESSION[TOKEN.'USUARIO_ID'] = $row['usuario_id'];
						$_SESSION[TOKEN.'USUARIO'] = $u;
						$_SESSION[TOKEN.'NOMBRES'] = $row['nombres'];
						$_SESSION[TOKEN.'APATERNO'] = $row['paterno'];
						$_SESSION[TOKEN.'AMATERNO'] = $row['materno'];
						$_SESSION[TOKEN.'NOM_COMPLETO'] = $_SESSION[TOKEN.'NOMBRES'].' '.$_SESSION[TOKEN.'APATERNO'].' '.$_SESSION[TOKEN.'AMATERNO'];
						$_SESSION[TOKEN.'GRUPO_ID'] = $row['grupo_id'];
						$_SESSION[TOKEN.'GRUPO'] = $row['grupo'];
						$_SESSION[TOKEN.'HORAINGRESO'] = date('d/m/Y H:i:s a');
						$_SESSION[TOKEN.'SID'] = session_id();

						# Verificamos si es un usuario administrador de la cuenta y si es asi lo marcamos
						if(isset($row['admin']) && $row['admin'] == 1)
							$_SESSION[TOKEN.'ADMIN'] = true;
						else
							$_SESSION[TOKEN.'ADMIN'] = false;

						# Reinicio tiempo de sesion
						$max = (isset($TIEMPODEVIDA) && $TIEMPODEVIDA > 0)? $TIEMPODEVIDA : 15;
						$maxSeg = time() + (60 * $max);

						$_SESSION[TOKEN.'TIMEOUT'] = $maxSeg ;

						echo $est = 4; //Usuario logeado
					}
				}else
					echo 'Credenciales invalidas.';

			}else
			{				
				echo 'No se encontro el usuario.';

			}
			
		break;
		case 'Chpwd':
			$p = trim($p);
			$pnew = trim($pnew);

			$est = 0; // Si est = 0 entonces el usuario no existe | Si est = 1 entonces el password actual es incorrecto | Si est = 2 Actualizacion Satisfactoria
		
			$sql = "select U.ADMUSUARIO_CONTRASENA CONTRASENA from ADM_USUARIO U where U.ESTATUS_ID = 1 and U.ADMUSUARIO_ID = ".$_SESSION['GEV2_USUARIO_ID'];
		
			$stid = pg_query($sql);// or die('<p>Hay un error en la consulta.</p> ' . pg_last_error());
			
			while($row = pg_fetch_array($stid, NULL, PGSQL_ASSOC)) {	
				$pReal = $row['contrasena'];
				$Filasafectadas = 0;
			}
		
			$pTemp = obtenerunPassword($p,$pReal);
			$pNuevo = josHashPassword($pnew);			

			if($pReal == $pTemp){		
				$est = 1;			
				$sql = "update ADM_USUARIO set ADMUSUARIO_CONTRASENA = '".$pNuevo."' where ADMUSUARIO_ID=".$_SESSION['GEV2_USUARIO_ID'];
				
				$stid2 = pg_query($sql);// or die('<p>Hay un error en la consulta.</p> ' . pg_last_error());
				
				if($stid2) {
					
					# Registro en el LOG el evento cambio de contrasena (El 20 es cambiar contrasena en Administracion) 
					$sql = "insert into repos_log (replog_id, replog_fecha, admusuario_id, reposmodacc_id) 
values (nextval('replog_id'), now(), ".$_SESSION['GEV2_USUARIO_ID'].", 20)";
					pg_query($sql) or die('<p>Hay un error en la consulta.</p> ' . pg_last_error());
					
				}
				
				$Filasafectadas = pg_affected_rows($stid2);
								
				if( (int)$Filasafectadas == 1){
					$est = 2;
				}		
				
			}
			
			echo $est;	
		
		break;
		case 'Verifpwd':
			$p = trim($Fpwd);

			$est = 0; // Si est = 1 entonces el password actual es incorrecto
		
			$sql = "select U.ADMUSUARIO_CONTRASENA CONTRASENA 
			from ADM_USUARIO U 
			where U.estatus_id = 1 
			and U.ADMUSUARIO_ID = ".$_SESSION['GEV2_USUARIO_ID'];
		
			$stid = pg_query($sql);
			if(!$stid)
				echo '<p>Hay un error en la consulta.</p> ' . pg_last_error();
			
			while($row = pg_fetch_assoc($stid)) {	
				$pReal = $row['contrasena'];
				$Filasafectadas = 0;
			}
		
			$pTemp = obtenerunPassword($p,$pReal);

			if($pReal == $pTemp){		
				$est = 1;			
			}
			
			echo $est;	
		
		break;
		case 'Recpwd':
			$pnew = trim($pnew);
			$pNuevo = josHashPassword($pnew);

			$est = 0; // Si est = 0 entonces el usuario no existe | Si est = 1 entonces el password actual es incorrecto | Si est = 2 Actualizacion Satisfactoria

			$sql = "update ADM_USUARIO set ADMUSUARIO_CONTRASENA = '".$pNuevo."' where ADMUSUARIO_ID=".$_SESSION['SGC_USUARIO_ID'];
			
			$stid2 =  pg_query($sql) or die('<p>Hay un error en la consulta.</p> ' . pg_last_error());
						
			$Filasafectadas = pg_affected_rows($stid2);
							
			if( (int)$Filasafectadas == 1){
				$est = 2;
				$qry ="delete from REPOS_CAMBIOCONTRASENA where ADMUSUARIO_ID=".$_SESSION['SGC_USUARIO_ID'];
				$stidDel = pg_query($qry) or die('<p>Hay un error en la consulta.</p> ' . pg_last_error());
			}		
			
			echo $est;	
		
		break;		
	}

	mysql_close($conn);
?>