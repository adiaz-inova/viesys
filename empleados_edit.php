<?php
define('MODULO', 100);
/* 
  * VIE 2012
  * creado      01/08/2012
  * modificado  14/04/2012
  * autor       I.S.C. Alejandro Diaz Garcia
  */
	$addruta = '';
	#require_once('postgresql.php');
	require_once('token.inc.php');
	require_once('includes/getvalues.php');
	require_once('includes/session_principal.php');
	
	if( !isset($task) || trim($task) == '')
		$task = 'list';

	/* incluyo mis js extras antes de llamar el header*/
	$js_extras_onready='';
	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);
	
	require_once('includes/html_template.php');
	


			# Control de seguridad
			$id=$_SESSION[TOKEN.'USUARIO_ID'];

			# Consultamos la informacion del usuario
			$sql="
			select
				empleados.id_emp usuario_id,
				empleados.nombre nombres,
				empleados.ape_pat paterno,
				empleados.ape_mat materno,
				empleados.tel,
				empleados.email usuario,
				empleados.dir,
				empleados.foto,
				empleados.psw password,
				empleados.id_est,
				estatus.id_est, estatus.nombre,
				empleados.id_gru,
				empleados.admin
				from empleados
				inner join estatus using(id_est)
				where 1=1 and empleados.id_emp=".$_SESSION[TOKEN.'USUARIO_ID'];
			
			$stid = mysql_query($sql);
			$row = mysql_fetch_assoc($stid);

			# generamos variables para marcar es privilegio de administrador de la cuenta actual
			$varsi='';
			$varno='';
			if($row['admin']==1)
				$varsi='checked';
			else
				$varno='checked';
			?>
			<h3>MODIFICAR DATOS DEL EMPLEADO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Nombre</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="32%" align="left">
								<label><span class="required">*</span>NOMBRES </label>
								<input class="" name="Fnombres" type="text" id="Fnombres" value="<?php echo $row['nombres']; ?>" size="" maxlength="100" title="nombre(s) del usuario" onkeypress="return vLetras(event, this);" req="req" lab="Nombres" />
							</td>
							<td width="34%" align="left">
								<label><span class="required">*</span>APELLIDO PATERNO </label>
								<input class="" name="Fapaterno" type="text" id="Fapaterno" value="<?php echo $row['paterno']; ?>" size="" maxlength="100" onkeypress="return vLetras(event, this);" req="req" lab="Apellido paterno" />
							</td>
							<td width="34%" align="left">
								<label><span class="required">*</span>APELLIDO MATERNO </label>
								<input class="" name="Famaterno" type="text" id="Famaterno" value="<?php echo $row['materno']; ?>" size="" maxlength="100" onkeypress="return vLetras(event, this);" />
							</td>
						</tr>
					</table>
					<input name="Fself" type="hidden" id="Fself" value="999"/>
					<input name="Festatus" type="hidden" id="Festatus" value="1"/>
					<hr class="bleed-flush compact" />

					<h4>Cuenta</h4>
					<table width="100%">
						<tr>
							<td width="32%" align="left">
								<label><span class="required">*</span>EMAIL </label>
								<input class="" name="Fusuario" type="text" id="Fusuario" value="<?php echo $row['usuario']; ?>" size="25" maxlength="60" readonly="readonly" req="req" lab="Email" />
							</td>
							<td width="34%" align="left">
								<label><span class="required">*</span>CONTRASE&Ntilde;A </label>
								<input class="" name="Fpwd" type="password" id="Fpwd" size="25" maxlength="60" onkeypress="return vPassword(event, this);" req="req" lab="Contraseña" />
							</td>
							<td width="34%" align="left">
								<label><span class="required">*</span>CONFIRMAR </label>
								<input class="" name="Fpwd2" type="password" id="Fpwd2" size="25" maxlength="60" onkeypress="return vPassword(event, this);" req="req" lab="Confirmar contraseña" />
							</td>
						</tr>
						<tr>
						  <td colspan="3">
						  	<span class="notice2">Llene los campos contraseña y confirmar, solo si desea cambiar la contraseña del usuario.</span>
						  </td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Dirección</h4>
					<table width="100%">
						<tr>
							<td colspan="3" align="left">
								<label>DIRECCIÓN </label>
								<input class="" name="Fdireccion" type="text" id="Fdireccion" value="<?php echo $row['dir']; ?>" size="80" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td width="32%" align="left">
								<label><span class="required">*</span>TELÉFONO </label>
								<input class="" name="Ftel" type="text" id="Ftel" value="<?php echo $row['tel']; ?>" size="12" maxlength="12" onkeypress="return vNumeros(event, this);" req="req" lab="Teléfono" />
							</td>
						</tr>
					</table>				
					<hr class="bleed-flush compact" />

					<h4>Rol</h4>
					<table width="100%">
						<tr>
							<td valign="top" align="left">
								<label><span class="required">*</span>GRUPO </label>
								<select class="" name="Fperfil" id="Fperfil" req="req" lab="Grupo" >
								<?php
									# Consulta para obtener los grupos existentes
									$sql="select id_gru, nombre, descripcion from grupo where id_est=1 order by nombre";
									$stid = mysql_query($sql);

									while (($rowPer = mysql_fetch_assoc($stid))) {
										if( $rowPer['id_gru'] == $row['id_gru']) {
											echo '	<option selected="selected" value="'.$rowPer['id_gru'].'">'.$rowPer['nombre'].'</option>';
										}else{	
											echo '	<option value="'.$rowPer['id_gru'].'">'.$rowPer['nombre'].'</option>';
										}
									}
								?>
								</select>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center"><br />
					            <input class="" type="button" name="" value="Guardar" act="exit" onclick="update_empleado(this);" />&nbsp;
					            <input class="" name="" type="button" value="Cancelar" onclick="javascript:window.location.href='index.php';" />
								<div class="avisorequired"><span class="required">* campos requeridos</span></div>
							</td>
						</tr>
					</table>
				</form>
			</div>
<?php
	mysql_close($conn);
	require_once('includes/html_footer.php');
?>