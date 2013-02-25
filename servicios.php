<?php
define('MODULO', 400);
/* 
  * clientes.php
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
	
#	if(!isset($_SESSION[TOKEN.'ADMIN']) || !$_SESSION[TOKEN.'ADMIN']) {
#		$contenido_html = 'NO TIENE PRIVILEGIOS PARA CONSULTAR ESTE MÓDULO';
#	}

	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);
	
	require_once('includes/html_template.php');
	
	switch($task) {
		case 'list': #- - - - - - - - - - - - - - -- - - LISTAR
							
			$sql = "SELECT
			ser.id_ser
			, ser.nombre
			, tser.nombre tipo
			, ser.responsable
			, ser.tel
			, ser.email
			, ser.dir
			, ser.id_est estatus
			from servicios ser
			inner join tipo_servicio tser using(id_tip_ser)
			WHERE 1=1 AND ser.id_est in(1,3) order by tipo, ser.nombre";
		
			$stid = mysql_query($sql);
			?>

			<h3>SERVICIOS</h3>
			<div class="boxed-group-inner clearfix">
				<div class="filtro">
					<form id="formulario" name="formulario">
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="inputPatternminus" tipo="servicios" placeholder="buscar"/>
					</form>
					<div align="right" class="botonsuperior">
		        		<input type="button" value="Agregar" href="<?php echo $_SERVER['PHP_SELF'] ?>?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
					</div>
				</div>
				<div class="scrool" id="cont">
					<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="Cabezadefila">
							<th width="5%">#</th>
							<th width="40%">Servicio</th>
							<!--<th width="30%">Tipo</th> -->
							<!--<th width="5%">Acción</th> -->
							<th width="5%">Cotizaciones</th>
							<th width="5%">Eventos</th>
							<th width="5%">Edit</th>
							<th width="5%">Elim</th>
						</tr>

					<?php
					$registros = 0;
					while($row = mysql_fetch_assoc($stid)) {	
						$registros++;

						$id = $row['id_ser'];
						$salon = $row['nombre'];
						$responsable = $row['responsable'];
						$email = $row['email'];
						$tel = $row['tel'];
						$tipo = $row['tipo'];
						$accion = ( $row['estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="servicio" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="servicio" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';												
						$rem = '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="servicio" value="-" />';

						?>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
							<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
							<td align="left" class="celdaNormal"><a href="servicios.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $salon; ?></a></td>
							<!--<td align="left" class="celdaNormal"><?php echo $tipo; ?></td> -->
							<!--<td align="center" class="celdaNormal"><?php echo $accion; ?></td> -->
							<td align="center" class="celdaNormal"><a href="cotizaciones.php?id_ser=<?php echo $id; ?>">VER</a></td>
							<td align="center" class="celdaNormal"><a href="eventos.php?id_ser=<?php echo $id; ?>">VER</a></td>
							<td align="center" class="celdaNormal"><a href="servicios.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td>
							<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
						</tr>
					<?php
					}			
					?>			

						<tr class="Cabezadefila">
							<td colspan='8'><?php echo $registros; ?> Registros encontrados.</td>
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
			SELECT
			ser.id_ser
			, ser.nombre
			, ser.responsable
			, ser.tel
			, ser.email
			, ser.dir
			, ser.id_est
			, ser.id_tip_ser
			from servicios ser
			WHERE 1=1 and ser.id_ser=".$id;
			
			$stid = mysql_query($sql);
			$row = mysql_fetch_assoc($stid);
			?>
			<h3>MODIFICAR DATOS DEL SERVICIO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Acerca del Servicio</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="50%" align="left">
								<label><span class="required">*</span>SERVICIO </label>
								<input  name="Fnombre" type="text" id="Fnombre" value="<?php echo $row['nombre']; ?>" size="50" maxlength="50" onkeypress="return vAbierta(event, this);" req="req" lab="Nombre del servicio" />
							</td>
							<!--<td width="50%" align="left">
								<label><span class="required">*</span>TIPO </label>
								<select name="Ftipo" id="Ftipo"  req="req" lab="Tipo de servicio" /> -->
									<!--option value="">seleccione</option-->
								<?php
									/*$sql="select id_tip_ser, nombre from tipo_servicio order by nombre";
									
									$stid = mysql_query($sql);

									while (($rowPer = mysql_fetch_assoc($stid))) {
										if( $rowPer['id_tip_ser'] == $row['id_tip_ser']) {
											echo '	<option selected="selected" value="'.$rowPer['id_tip_ser'].'">'.$rowPer['nombre'].'</option>';
										}else{	
											echo '	<option value="'.$rowPer['id_tip_ser'].'">'.$rowPer['nombre'].'</option>';
										}
									}*/
								?>
								</select>
							<!--</td> -->
						</tr>
					</table>
					<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>"/>
					<input name="Ftipo" type="hidden" id="Ftipo" value="5"/><!-- el ID 5 es UNICO_TIPO -->
					<input name="Fdireccion" type="hidden" id="Fdireccion" value=" "/><!-- no quisieron estos campos .. los mandare vacios -->
					<input name="Femail" type="hidden" id="Femail" value="0"/><!-- no quisieron estos campos .. los mandare vacios -->
					<hr class="bleed-flush compact" />

					<!--<h4>Acerca del Contacto</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td align="left" colspan="2">
								<label>DIRECCIÓN </label>
								<input  name="Fdireccion" type="text" id="Fdireccion" value="<?php echo $row['dir']; ?>" size="80" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td align="left" colspan="2">
								<label>--><!--span class="required">*</span>--><!--EMAIL </label>
								<input  name="Femail" type="text" id="Femail" value="<?php echo $row['email']; ?>" size="25" maxlength="60" />
							</td>
						</tr>
					</table>	-->			
					<hr class="bleed-flush compact">

					<h4>Estatus</h4>
					<table width="100%" cellpadding="5" cellspacing="0">				
						<tr>
							<td valign="top" align="left">
								<label><span class="required">*</span>ESTATUS </label>
								<select  name="Festatus" id="Festatus" req="req" lab="Estatus" />
									<!--option value="">...</option-->
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
					<hr class="bleed-flush compact">

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center"><br />
					            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update(this, 'servicios', 'Fnombre|Ftipo|Ftel|Festatus', 'notices');" />&nbsp;
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
			<h3>AGREGAR SERVICIO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Acerca del Servicio</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="50%" align="left">
								<label><span class="required">*</span>SERVICIO </label>
								<input  name="Fnombre" type="text" id="Fnombre" value="" size="50" maxlength="50" onkeypress="return vAbierta(event, this);" req="req" lab="Nombre del servicio" />
							</td>
							<!--<td width="50%" align="left">
								<label><span class="required">*</span>TIPO </label>
								<select name="Ftipo" id="Ftipo"  req="req" lab="Tipo de servicio" />
									<option value="">seleccione</option>
								<?php
									/*$sql="select id_tip_ser, nombre from tipo_servicio order by nombre";
									
									$stid = mysql_query($sql);

									while (($rowPer = mysql_fetch_assoc($stid))) {
										echo '	<option value="'.$rowPer['id_tip_ser'].'">'.$rowPer['nombre'].'</option>';
									}*/
								?>
								</select>
							</td> -->
						<input name="Ftipo" type="hidden" id="Ftipo" value="5"/><!-- el ID 5 es UNICO_TIPO -->
						<input name="Fdireccion" type="hidden" id="Fdireccion" value=" "/><!-- no quisieron estos campos .. los mandare vacios -->
						<input name="Femail" type="hidden" id="Femail" value="0"/><!-- no quisieron estos campos .. los mandare vacios -->
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<!-- <h4>Acerca del Contacto</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td align="left" colspan="2">
								<label>DIRECCIÓN </label>
								<input  name="Fdireccion" type="text" id="Fdireccion" value="" size="80" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td align="left" colspan="2">
								<label>--><!--span class="required">*</span--><!--EMAIL </label>
								<input  name="Femail" type="text" id="Femail" value="" size="25" maxlength="60" />
							</td>
						</tr>
					</table>				-->
					<hr class="bleed-flush compact">

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center">
					            <input type="button" onclick="add(this, 'servicios', 'Fnombre|Ftipo', 'notices');" value="Guardar cambios" />
					            <input class="" name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
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