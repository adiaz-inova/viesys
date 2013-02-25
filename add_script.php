<?php
define('MODULO', 300);
/* 
  * salones.php
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
	
	// if( !isset($task) || trim($task) == '')
	// 	$task = 'list';

	/* incluyo mis js extras antes de llamar el header*/
	$js_extras_onready='';
	
#	if(!isset($_SESSION[TOKEN.'ADMIN']) || !$_SESSION[TOKEN.'ADMIN']) {
#		$contenido_html = 'NO TIENE PRIVILEGIOS PARA CONSULTAR ESTE MÓDULO';
#	}

	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);
	
	require_once('includes/html_template_sinheader.php');
	
	switch($tipo) {
		case 'salones': #- - - - - - - - - - - - - - -- - - AGREGAR
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
				
				<input type="hidden" value="" name="Fporc" id="Fporc" req="" lab="Porcentaje" />

				<table width="100%" cellpadding="3" cellspacing="3" align="center">
					<tr>
						<td align="center"><br />
				            <input type="button" onclick="only_add(this, 'salones', 'Fnombre|Ftel', 'notices');" value="Guardar cambios" />
				            <input name="" type="button" value="Cancelar" onclick="javascript:parent.jQuery.fancybox.close();" />
							<div class="avisorequired"><span class="required">* campos requeridos</span></div>
						</td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
		case 'clientes':
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
							<input class="" name="Fnombres" type="text" id="Fnombres" value="" size="" maxlength="100" title="nombre(s) del usuario" onkeypress="return vLetras(event, this);" req="req" lab="Nombres" />
						</td>
						<td width="34%" align="left">
							<label><span class="required">*</span>APELLIDO PATERNO </label>
							<input class="" name="Fapaterno" type="text" id="Fapaterno" value="" size="" maxlength="100" title="apellido paterno del usuario" onkeypress="return vLetras(event, this);" req="req" lab="Apellido Paterno" />
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
							<input class="" name="Femail" type="text" id="Femail" value="" size="25" maxlength="60" req="req" lab="Email" />
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
							<input class="" name="Ftel" type="text" id="Ftel" value="" size="20" maxlength="25" title="número de teléfono" onkeypress="return vAbierta(event, this);" req="req" lab="Teléfono" />
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
				            <!-- <input type="button" onclick="add_cliente();" value="Guardar cambios" /> -->
				            <input type="button" onclick="only_add(this, 'clientes', 'Fnombres|Fapaterno|Femail|Ftel', 'notices');" value="Guardar cambios" />

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
	// require_once('includes/html_footer.php');
?>