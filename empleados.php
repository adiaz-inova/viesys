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
	$js_extras_onready='
		if ($("#Fusuario").length) {		
			
			$("#Fusuario").change(function() {
				//verifyuser();
			});
		}
	';
	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);
	
	require_once('includes/html_template.php');
	
	switch($task) {
		case 'list': #- - - - - - - - - - - - - - -- - - LISTAR
							
			$sql = "select
			empleados.id_emp usuario_id,
			concat(empleados.nombre,' ',empleados.ape_pat,' ',empleados.ape_mat) nombres,
			empleados.tel,
			empleados.email usuario,
			empleados.dir,
			empleados.psw password,
			empleados.id_est u_estatus,
			estatus.id_est, estatus.nombre,
			grupo.id_gru grupo_id,
			grupo.nombre grupo,
			grupo.id_est g_estatus
			from empleados
			inner join estatus using(id_est)
			inner join grupo using(id_gru)
			where 1=1 and empleados.id_est in(1,3)";
		
			$stid = mysql_query($sql);
			?>			

			<h3>EMPLEADOS</h3>
			<div class="boxed-group-inner clearfix">
				<div class="filtro">
					<form id="formulario" name="formulario">
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="empleados" placeholder="buscar"/>
					</form>
					<div align="right" class="botonsuperior">
		        		<input type="button" value="Agregar" href="<?php echo $_SERVER['PHP_SELF'] ?>?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
					</div>
				</div>
			
				<div class="scrool" id="cont">
				<table width="100%" cellspacing="0" cellpadding="3">
					<tr class="Cabezadefila">
						<th width="3%">#</th>
						<th width="25%">Empleado</th>
						<th width="5%">Estatus</th>
						<th width="17%">Usuario</th>					
						<th width="25%">Grupo</th>
						<th width="5%">Cotizaciones</th>
						<th width="5%">Eventos</th>
						<!-- <th width="5%">Edit</th> -->
						<th width="5%">Elim</th>
					</tr>

				<?php
				$registros = 0;
				while($row = mysql_fetch_assoc($stid)) {	
					$registros++;

					$id = $row['usuario_id'];				
					$nombre = $row['nombres'];
					$usuario = $row['usuario'];
					$grupoid = $row['grupo_id'];
					$grupo = $row['grupo'];
					
					$accion = ( $row['u_estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="empleado" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="empleado" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';
																	
					$rem = ($id != $_SESSION[TOKEN.'USUARIO_ID'])? '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="empleado" value="-" class="rppermiso" />' : '';

					if($id == $_SESSION[TOKEN.'USUARIO_ID'])
						$accion = '';				
					?>
					<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
						<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
						<td align="left" class="celdaNormal"><a href="empleados.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo strtoupper($nombre); ?></a></td>
						<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
						<td align="left" class="celdaNormal"><a href="empleados.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $usuario; ?></a></td>					
						<td align="left" class="celdaNormal"><a href="grupos.php?task=edit&id=<?php echo $grupoid; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $grupo; ?></a></td>
						<td align="center" class="celdaNormal"><a href="cotizaciones.php?id_emp=<?php echo $id; ?>">VER</a></td>
						<td align="center" class="celdaNormal"><a href="eventos.php?id_emp=<?php echo $id; ?>">VER</a></td>
						<!-- <td align="center" class="celdaNormal"><a href="empleados.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td> -->
						<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
					</tr>
				<?php
				}			
				?>
					<tr class="Cabezadefila">
						<td colspan='11'><?php echo $registros; ?> Registros encontrados.</td>
					</tr>
				</table>
				</div>
			</div><!-- class="boxed-group-inner clearfix" -->
		<?php
		break;
		case 'edit': #- - - - - - - - - - - - - - -- - - MODIFICAR

			# Control de seguridad
			if (!isset($id)) {
				header('Location: '.$_SERVER['PHP_SELF']);
			}

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
				where 1=1 and empleados.id_emp=".$id;
			
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
					<input name="Fidusuario" type="hidden" id="Fidusuario" value="<?php echo $id; ?>"/>
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
								<input class="" name="Ftel" type="text" id="Ftel" value="<?php echo $row['tel']; ?>" size="20" maxlength="25" onkeypress="return vAbierta(event, this);" req="req" lab="Teléfono" />
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
						<tr>
							<td valign="top" align="left">
								<label><span class="required">*</span>ESTATUS </label>
								<select class="" name="Festatus" id="Festatus" req="req" lab="Estatus" >
								<?php
									$sql="select id_est, nombre from estatus where id_est in(1,3) order by nombre";
									
									$stid = mysql_query($sql);

									while (($rowPer = mysql_fetch_assoc($stid))) {
										if( $rowPer['id_est'] == $row['id_est']) {
											echo '	<option selected="selected" value="'.$rowPer['id_est'].'">'.$rowPer['nombre'].'</option>';
										}else{	
											echo '	<option value="'.$rowPer['id_est'].'">'.$rowPer['nombre'].'</option>';
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
					            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update_empleado(this);" />&nbsp;
					            <input class="" name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
								<div class="avisorequired"><span class="required">* campos requeridos</span></div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		<?php
		break;
		case 'add': #- - - - - - - - - - - - - - -- - - AGREGAR
		?>
			<h3>AGREGAR EMPLEADO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Nombre</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="32%" align="left">
								<label><span class="required">*</span>NOMBRES </label>
								<input class="" name="Fnombres" type="text" id="Fnombres" value="" size="" maxlength="100" title="nombre(s) del usuario" onkeypress="return vLetras(event, this);" req="req" lab="Nombres" />
							</td>
							<td width="34%" align="left">
								<label><span class="required">*</span>APELLIDO PATERNO </label>
								<input class="" name="Fapaterno" type="text" id="Fapaterno" value="" size="" maxlength="100" onkeypress="return vLetras(event, this);" req="req" lab="Apellido paterno" />
							</td>
							<td width="34%" align="left">
								<label><label><span class="required">*</span>APELLIDO MATERNO </label>
								<input class="" name="Famaterno" type="text" id="Famaterno" value="" size="" maxlength="100" onkeypress="return vLetras(event, this);" />
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Cuenta</h4>
					<table width="100%">
						<tr>
							<td width="32%" align="left">
								<label><span class="required">*</span>EMAIL </label>
								<input class="" name="Fusuario" type="text" id="Fusuario" value="" size="25" maxlength="60" req="req" lab="Email" />
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
								<input class="" name="Fdireccion" type="text" id="Fdireccion" value="" size="80" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td width="32%" align="left">
								<label><span class="required">*</span>TELÉFONO </label>
								<input class="" name="Ftel" type="text" id="Ftel" value="" size="20" maxlength="25" onkeypress="return vAbierta(event, this);" req="req" lab="Teléfono" />
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
									<option value="">...</option>
									<?php
									# Consulta para obtener los grupos existentes
									$sql="select id_gru, nombre, descripcion from grupo where id_est=1 order by nombre";
									$stid = mysql_query($sql);

									while (($rowPer = mysql_fetch_assoc($stid))) {
										echo '	<option value="'.$rowPer['id_gru'].'">'.$rowPer['nombre'].'</option>';
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
					            <input type="button" onclick="add_empleado();" value="Guardar cambios" />
					            <input type="button"  value="Cancelar" href="<?php echo $_SERVER['PHP_SELF'] ?>" onclick=";window.location.href=this.getAttribute('href');return false;" />
								<div class="avisorequired"><span class="required">* campos requeridos</span></div>
							</td>
						</tr>
					</table>
				</form>
			</div>
<?php
		break;		
	}// switch

	mysql_close($conn);
	require_once('includes/html_footer.php');
?>