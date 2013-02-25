<?php
define('MODULO', 200);
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
			clientes.id_cli,
			clientes.empresa,
			clientes.nombre,
			clientes.ape_pat,
			clientes.ape_mat,
			clientes.procedencia,
			concat(clientes.nombre, ' ', clientes.ape_pat, ' ', clientes.ape_mat)cliente,
			clientes.tel,
			clientes.tel2,
			clientes.email,
			clientes.dir,
			clientes.id_est estatus
			FROM clientes
			INNER JOIN estatus USING(id_est)
			WHERE 1=1  AND clientes.id_est in(1,3) ORDER BY cliente";
		
			$stid = mysql_query($sql);
			?>			

			<h3>CLIENTES</h3>
			<div class="boxed-group-inner clearfix">
				<div class="filtro">
					<form id="formulario" name="formulario">
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="clientes" placeholder="buscar"/>
						Clientes con eventos <input type="checkbox" name="Fccev" id="Fccev" tipo="clientes" onchange="filtrar(this)" value="1">
					</form>
					<div align="right" class="botonsuperior">
		        		<input type="button" value="Agregar" href="<?php echo $_SERVER['PHP_SELF'] ?>?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
					</div>
				</div>
				<div class="scrool" id="cont">
					<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="Cabezadefila">
							<th width="3%">#</th>
							<th width="30%">Cliente</th>
							<th width="5%">Estatus</th>
							<th width="10%">Email/Tel</th>
							<th width="25%">Empresa</th>
							<th width="7%">Cotizaciones</th>
							<th width="5%">Eventos</th>
							<th width="5%">Edit</th>
							<th width="5%">Elim</th>
							<th width="5%">Cotizar</th>
						</tr>

					<?php
					$registros = 0;
					while($row = mysql_fetch_assoc($stid)) {	
						$registros++;

						$id = $row['id_cli'];				
						$cliente = $row['cliente'];
						$email = $row['email'];
						$email .= ($email!='')? '<br>'.$row['tel']." / ".$row['tel2']:$row['tel']." / ".$row['tel2'];
						$empresa = $row['empresa'];
						
						$accion = (isset($row['estatus']) && $row['estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="cliente" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="cliente" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';
																		
						$rem = '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="cliente" value="-" />';

						$cotizar = (isset($row['estatus']) && $row['estatus'] == 1)?'<input type="button" href="eventos.php?task=add&id_cli='.$id.'" onclick=";window.location.href=this.getAttribute(\'href\');return false;" value="Cotizar" />':'';

						?>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
							<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
							<td align="left" class="celdaNormal"><a href="clientes.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $cliente; ?></a></td>
							<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
							<td align="left" class="celdaNormal"><a href="clientes.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $email; ?></a></td>					
							<td align="left" class="celdaNormal"><?php echo $empresa; ?></td>
							<td align="center" class="celdaNormal"><a href="cotizaciones.php?id_cli=<?php echo $id; ?>">VER</a></td>
							<td align="center" class="celdaNormal"><a href="eventos.php?id_cli=<?php echo $id; ?>">VER</a></td>
							<td align="center" class="celdaNormal"><a href="clientes.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td>
							<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
							<td align="center" class="celdaNormal"><?php echo $cotizar; ?></td>
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
			$sql="select
				clientes.id_cli,
				clientes.empresa,
				clientes.nombre nombres,
				clientes.ape_pat paterno,
				clientes.ape_mat materno,
				clientes.tel,
				clientes.tel2,
				clientes.email,
				clientes.dir,
				clientes.id_est,
				clientes.id_est estatus,
				clientes.procedencia
				from clientes
				inner join estatus using(id_est)
				where 1=1 and clientes.id_cli=".$id;
			
			$stid = mysql_query($sql);
			$row = mysql_fetch_assoc($stid);
			?>
			<h3>MODIFICAR DATOS DE CLIENTE</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Nombre</h4>
					<table width="100%">
						<tr>
							<td width="32%" align="left">
								<label><span class="required">*</span>NOMBRES </label>
								<input class="" name="Fnombres" type="text" id="Fnombres" value="<?php echo $row['nombres']; ?>" size="" maxlength="100" title="nombre(s) del usuario" onkeypress="return vLetras(event, this);" />
							</td>
							<td width="34%" align="left">
								<label><span class="required">*</span>APELLIDO PATERNO </label>
								<input class="" name="Fapaterno" type="text" id="Fapaterno" value="<?php echo $row['paterno']; ?>" size="" maxlength="100" title="apellido paterno del usuario" onkeypress="return vLetras(event, this);" />
							</td>
							<td width="34%" align="left">
								<label>APELLIDO MATERNO </label>
								<input class="" name="Famaterno" type="text" id="Famaterno" value="<?php echo $row['materno']; ?>" size="" maxlength="100" title="apellido materno del usuario" onkeypress="return vLetras(event, this);" />
							</td>
						</tr>
					</table>
					<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>"/>
					<hr class="bleed-flush compact" />

					<h4>Otros datos</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="50%" align="left">
								<label><!--span class="required">*</span-->EMPRESA </label>
								<input class="" name="Fempresa" type="text" id="Fempresa" value="<?php echo $row['empresa']; ?>" size="25" maxlength="60" />
							</td>
							<td width="50%" align="left">
								<label><span class="required">*</span>EMAIL </label>
								<input class="" name="Femail" type="text" id="Femail" value="<?php echo $row['email']; ?>" size="25" maxlength="60" />
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Dirección</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td align="left" colspan="2">
								<label>DIRECCIÓN </label>
								<input class="" name="Fdireccion" type="text" id="Fdireccion" value="<?php echo $row['dir']; ?>" size="80" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td width="50%" align="left">
								<label><span class="required">*</span>TELÉFONO </label>
								<input class="" name="Ftel" type="text" id="Ftel" value="<?php echo $row['tel']; ?>" size="20" maxlength="25" title="número de teléfono" onkeypress="return vAbierta(event, this);" />
							</td>
							<td width="50%" align="left">
								<label><span class="required">*</span>TELÉFONO </label>
								<input class="" name="Ftel2" type="text" id="Ftel2" value="<?php echo $row['tel2']; ?>" size="20" maxlength="25" title="número de teléfono adicional" onkeypress="return vAbierta(event, this);" />
							</td>
						</tr>
					</table>				
					<hr class="bleed-flush compact" />

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
						<td valign="top" align="left">
							<label>PROCEDENCIA </label>
							<input class="" name="Fprocedencia" type="text" id="Fprocedencia" value="<?php echo $row['procedencia']; ?>" size="60" maxlength="100" />
						</td>
					</tr>				
				</table>
				<hr class="bleed-flush compact" />

				<table width="100%" cellpadding="3" cellspacing="3" align="center">
					<tr>
						<td align="center"><br />
				            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update_cliente(this);" />&nbsp;
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
		<h3>AGREGAR CLIENTE</h3>
		<div class="boxed-group-inner clearfix">
			<div id="notices"></div>
			<form name="formulario" id="formulario">
				<h4>Nombre</h4>
				<table width="100%">
					<tr>
						<td width="32%" align="left">
							<label><span class="required">*</span>NOMBRES </label>
							<input class="" name="Fnombres" type="text" id="Fnombres" value="" size="" maxlength="100" title="nombre(s) del usuario" onkeypress="return vLetras(event, this);" />
						</td>
						<td width="34%" align="left">
							<label><span class="required">*</span>APELLIDO PATERNO </label>
							<input class="" name="Fapaterno" type="text" id="Fapaterno" value="" size="" maxlength="100" title="apellido paterno del usuario" onkeypress="return vLetras(event, this);" />
						</td>
						<td width="34%" align="left">
							<label>APELLIDO MATERNO </label>
							<input class="" name="Famaterno" type="text" id="Famaterno" value="" size="" maxlength="100" title="apellido materno del usuario" onkeypress="return vLetras(event, this);" />
						</td>
					</tr>
				</table>
				<hr class="bleed-flush compact" />

				<h4>Otros datos</h4>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td width="50%" align="left">
							<label><!--span class="required">*</span-->EMPRESA </label>
							<input class="" name="Fempresa" type="text" id="Fempresa" value="" size="25" maxlength="60" />
						</td>
						<td width="50%" align="left">
							<label><span class="required">*</span>EMAIL </label>
							<input class="" name="Femail" type="text" id="Femail" value="" size="25" maxlength="60" />
						</td>
					</tr>
				</table>
				<hr class="bleed-flush compact" />

				<h4>Dirección</h4>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td align="left" colspan="2">
							<label>DIRECCIÓN </label>
							<input class="" name="Fdireccion" type="text" id="Fdireccion" value="" size="80" maxlength="100" />
						</td>
					</tr>
					<tr>
						<td width="50%" align="left">
							<label><span class="required">*</span>TELÉFONO </label>
							<input class="" name="Ftel" type="text" id="Ftel" value="" size="20" maxlength="25" title="número de teléfono" onkeypress="return vAbierta(event, this);" />
						</td>
						<td width="50%" align="left">
							<label>TELÉFONO 2</label>
							<input class="" name="Ftel2" type="text" id="Ftel2" value="" size="20" maxlength="25" title="número de teléfono adicional" onkeypress="return vAbierta(event, this);" />
						</td>
					</tr>
				</table>				
				<hr class="bleed-flush compact" />

				<h4>Dirección</h4>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td valign="top" align="left">
							<label>PROCEDENCIA </label>
							<input class="" name="Fprocedencia" type="text" id="Fprocedencia" value="" size="60" maxlength="100" />
						</td>
					</tr>
				</table>				
				<hr class="bleed-flush compact" />

				<table width="100%" cellpadding="3" cellspacing="3" align="center">
					<tr>
						<td align="center"><br />
				            <input type="button" onclick="add_cliente();" value="Guardar cambios" />
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