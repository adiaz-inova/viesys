<?php
define('MODULO', 300);
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
			id_sal,
			empresa,
			nombre,
			responsable,
			fec_ingreso,
			tel,
			email,
			dir,
			foto,
			id_est estatus
			from salones
			WHERE 1=1 AND id_est in(1,3) order by nombre";
		
			$stid = mysql_query($sql);
			?>			

			<h3>SALONES</h3>
			<div class="boxed-group-inner clearfix">
				<div class="filtro">
					<form id="formulario" name="formulario">
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="salones" placeholder="buscar"/>
					</form>
					<div align="right" class="botonsuperior">
		        		<input type="button" value="Agregar" href="<?php echo $_SERVER['PHP_SELF'] ?>?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
					</div>
				</div>
				<div class="scrool" id="cont">
					<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="Cabezadefila">
							<th width="3%">#</th>
							<th width="25%">Salón</th>
							<th width="25%">Responsable</th>
							<th width="12%">Teléfono</th>
							<th width="10%">Cotizaciones</th>
							<th width="10%">Eventos</th>
							<th width="5%">Acción</th>
							<th width="5%">Edit</th>
							<th width="5%">Elim</th>
						</tr>

					<?php
					$registros = 0;
					while($row = mysql_fetch_assoc($stid)) {	
						$registros++;

						$id = $row['id_sal'];
						$salon = $row['nombre'];
						$responsable = $row['responsable'];
						$email = $row['email'];
						$tel = $row['tel'];
						$empresa = $row['empresa'];
						$accion = ( $row['estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="salon" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="salon" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';												
						$rem = '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="salon" value="-" />';
						$ver_cotizaciones = '<a href="cotizaciones.php?id_sal='.$id.'">ver</a>';
						$ver_eventos = '<a href="eventos.php?id_sal='.$id.'">ver</a>';

					?>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
							<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
							<td align="left" class="celdaNormal"><?php echo $salon; ?></td>
							<td align="left" class="celdaNormal"><?php echo $responsable; ?></td>
							<td align="left" class="celdaNormal"><?php echo $tel; ?></td>
							<td align="center" class="celdaNormal"><?php echo $ver_cotizaciones; ?></td>
							<td align="center" class="celdaNormal"><?php echo $ver_eventos; ?></td>
							<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
							<td align="center" class="celdaNormal"><a href="salones.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td>
							<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
						</tr>
					<?php
					}			
					?>			

						<tr class="Cabezadefila">
							<td colspan='9'><?php echo $registros; ?> Registros encontrados.</td>
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
			salones.id_sal,
			salones.empresa,
			salones.nombre,
			salones.responsable,
			salones.fec_ingreso ingreso,
			salones.tel,
			salones.tel2,
			salones.email,
			salones.dir,
			salones.porcentaje,
			salones.id_est
			from salones
			INNER JOIN estatus USING(id_est)
			WHERE 1=1 and salones.id_sal=".$id;
			
			$stid = mysql_query($sql);
			$row = mysql_fetch_assoc($stid);
			?>
			
			<h3>MODIFICAR DATOS DE SALÓN</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
			
					<h4>Acerca del Salón</h4>
					<table width="100%">
						<tr>
							<td width="50%" align="left">
								<label><span class="required">*</span>NOMBRE </label>
								<input class="" name="Fnombre" type="text" id="Fnombre" value="<?php echo $row['nombre']; ?>" size="50" maxlength="50" onkeypress="return vAbierta(event, this);" req="req" lab="Nombre del salón" />
							</td>
							<td width="50%" align="left">
								<label>RESPONSABLE </label>
								<input class="" name="Fresponsable" type="text" id="Fresponsable" value="<?php echo $row['responsable']; ?>" size="50" maxlength="75" onkeypress="return vLetras(event, this);" req="" lab="Nombre del Responsable" />
							</td>
						</tr>
						<tr>
							<td align="left" colspan="2">
								<label>EMPRESA </label>
								<input class="" name="Fempresa" type="text" id="Fempresa" value="<?php echo $row['empresa']; ?>" size="50" maxlength="100" title="empresa" onkeypress="return vAbierta(event, this);" />
							</td>
						</tr>
					</table>
					<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>"/>
					<hr class="bleed-flush compact" />
				
					<h4>Contacto</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="100%" lign="left" colspan="2">
								<label>DIRECCIÓN </label>
								<input class="" name="Fdireccion" type="text" id="Fdireccion" value="<?php echo $row['dir']; ?>" size="80" maxlength="100" />
							</td>
						</tr>
					</table>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="40%" align="left">
								<label>EMAIL </label>
								<input class="" name="Femail" type="text" id="Femail" value="<?php echo $row['email']; ?>" size="25" maxlength="60" />
							</td>
							<td width="30%" align="left">
								<label>TELÉFONO </label>
								<input class="" name="Ftel" type="text" id="Ftel" value="<?php echo $row['tel']; ?>" size="20" maxlength="25" title="número de teléfono" onkeypress="return vAbierta(event, this);" req="" lab="Teléfono" />
							</td>
							<td width="30%" align="left">
								<label>TELÉFONO 2</label>
								<input class="" name="Ftel2" type="text" id="Ftel2" value="<?php echo $row['tel2']; ?>" size="20" maxlength="25" title="número de teléfono adicional" onkeypress="return vAbierta(event, this);" req="" lab="Teléfono" />
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<!-- <h4>Comisión</h4>
					<table width="100%" cellpadding="5" cellspacing="0">				
						<tr>
							<td valign="top" align="left">
								<label>PORCENTAJE OBTENIDO POR EL SALÓN </label>
								<input type="text" size="4" maxlength="3" value="<?php echo $row['porcentaje']; ?>" name="Fporc" id="Fporc" onkeypress="return vNumeros(event, this);" req="" lab="Porcentaje" /> %
							</td>
						</tr>				
					</table>
					<hr class="bleed-flush compact" /> -->
						<input type="hidden" value="<?php echo $row['porcentaje']; ?>" name="Fporc" id="Fporc" req="" lab="Porcentaje" />

					<h4>Estatus</h4>
					<table width="100%" cellpadding="5" cellspacing="0">				
					<tr>
						<td valign="top" align="left">
							<label><span class="required">*</span>ESTATUS </label>
							<select class="" name="Festatus" id="Festatus" >
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
				            <input type="button" name="" value="Guardar y Salir" act="exit" onclick="update(this, 'salones', 'Fnombre|Festatus', 'notices');" />&nbsp;
				            <input name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
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
		<h3>AGREGAR SALÓN</h3>
		<div class="boxed-group-inner clearfix">
			<div id="notices"></div>
			<form name="formulario" id="formulario">
		
				<h4>Acerca del Salón</h4>
				<table width="100%">
					<tr>
						<td width="50%" align="left">
							<label><span class="required">*</span>NOMBRE </label>
							<input class="" name="Fnombre" type="text" id="Fnombre" value="" size="50" maxlength="50" onkeypress="return vAbierta(event, this);" req="req" lab="Nombre del salón" />
						</td>
						<td width="50%" align="left">
							<label>RESPONSABLE </label>
							<input class="" name="Fresponsable" type="text" id="Fresponsable" value="" size="50" maxlength="75" onkeypress="return vLetras(event, this);" req="" lab="Nombre del Responsable" />
						</td>
					</tr>
					<tr>
						<td align="left" colspan="2">
							<label>EMPRESA </label>
							<input class="" name="Fempresa" type="text" id="Fempresa" value="" size="50" maxlength="100" title="empresa" onkeypress="return vAbierta(event, this);" />
						</td>
					</tr>
				</table>
				<hr class="bleed-flush compact" />
			
				<h4>Contacto</h4>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td width="100%" lign="left" colspan="2">
							<label>DIRECCIÓN </label>
							<input class="" name="Fdireccion" type="text" id="Fdireccion" value="" size="80" maxlength="100" />
						</td>
					</tr>
				</table>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td width="40%" align="left">
							<label>EMAIL </label>
							<input class="" name="Femail" type="text" id="Femail" value="" size="25" maxlength="60" />
						</td>
						<td width="30%" align="left">
							<label><span class="required">*</span>TELÉFONO </label>
							<input class="" name="Ftel" type="text" id="Ftel" value="" size="20" maxlength="25" title="número de teléfono" onkeypress="return vAbierta(event, this);" req="req" lab="Teléfono" />
						</td>
						<td width="30%" align="left">
							<label>TELÉFONO 2</label>
							<input class="" name="Ftel2" type="text" id="Ftel2" value="" size="20" maxlength="25" title="número de teléfono" onkeypress="return vAbierta(event, this);" req="" lab="Teléfono" />
						</td>
					</tr>
				</table>
				<hr class="bleed-flush compact" />

				<!-- <h4>Comisión</h4>
				<table width="100%" cellpadding="5" cellspacing="0">				
					<tr>
						<td valign="top" align="left">
							<label>PORCENTAJE OBTENIDO POR EL SALÓN </label>
							<input type="text" size="4" maxlength="3" value="" name="Fporc" id="Fporc" onkeypress="return vNumeros(event, this);" req="" lab="Porcentaje" /> %
						</td>
					</tr>				
				</table>
				<hr class="bleed-flush compact" /> -->
					<input type="hidden" value="" name="Fporc" id="Fporc" req="" lab="Porcentaje" />

				<table width="100%" cellpadding="3" cellspacing="3" align="center">
					<tr>
						<td align="center"><br />
				            <input type="button" onclick="add(this, 'salones', 'Fnombre|Festatus', 'notices');" value="Guardar cambios" />
				            <input name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
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