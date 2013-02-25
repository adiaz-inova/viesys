<?php
define('MODULO', 400);
/* 
  * tipo_eventos.php
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
			id_tip_eve id, nombre tipodeevento
			from tipo_evento
			WHERE 1=1 ORDER BY nombre";
		
			$stid = mysql_query($sql);
			?>

			<h3>TIPOS DE EVENTOS</h3>
			<div class="boxed-group-inner clearfix">
				<div class="filtro">
					<form id="formulario" name="formulario">
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="inputPatternminus" tipo="tipo_evento" placeholder="buscar"/>
					</form>
					<div align="right" class="botonsuperior">
		        		<input type="button" value="Agregar" href="<?php echo $_SERVER['PHP_SELF'] ?>?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
					</div>
				</div>
				<div class="scrool" id="cont">
					<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="Cabezadefila">
							<th width="5%">#</th>
							<th width="40%">Tipo de evento</th>
							<th width="5%">Cotizaciones</th>
							<th width="5%">Eventos</th>
							<th width="5%">Edit</th>
							<th width="5%">Elim</th>
						</tr>

					<?php
					$registros = 0;
					while($row = mysql_fetch_assoc($stid)) {	
						$registros++;

						$id = $row['id'];
						$tipodeevento = $row['tipodeevento'];
						$rem = '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="tipo_evento" value="-" />';

						?>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
							<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
							<td align="left" class="celdaNormal"><a href="tipo_eventos.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $tipodeevento; ?></a></td>
							<td align="center" class="celdaNormal"><a href="cotizaciones.php?id_tipo_evento=<?php echo $id; ?>">VER</a></td>
							<td align="center" class="celdaNormal"><a href="eventos.php?id_tipo_evento=<?php echo $id; ?>">VER</a></td>
							<td align="center" class="celdaNormal"><a href="tipo_eventos.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td>
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
			$sql="SELECT nombre FROM tipo_evento WHERE 1=1 and id_tip_eve=".$id;
			
			$stid = mysql_query($sql);
			$row = mysql_fetch_assoc($stid);
			?>
			<h3>MODIFICAR DATOS DEL SERVICIO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<input name="Fid" id="Fid" type="hidden" value="<?php echo $id ?>">
					<h4>Acerca del Servicio</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="50%" align="left">
								<label><span class="required">*</span>SERVICIO </label>
								<input  name="Fnombre" type="text" id="Fnombre" value="<?php echo $row['nombre']; ?>" size="50" maxlength="50" onkeypress="return vAbierta(event, this);" req="req" lab="Nombre del servicio" />
							</td>
						</tr>
					</table>	
					<hr class="bleed-flush compact">

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center"><br />
					            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update(this, 'tipo_eventos', 'Fnombre', 'notices');" />&nbsp;
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
			<h3>AGREGAR TIPO DE EVENTO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Información del tipo de evento</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="50%" align="left">
								<label><span class="required">*</span>TIPO </label>
								<input  name="Fnombre" type="text" id="Fnombre" value="" size="50" maxlength="50" onkeypress="return vAbierta(event, this);" req="req" lab="Nombre del servicio" />
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact">

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center">
					            <input type="button" onclick="add(this, 'tipo_eventos', 'Fnombre', 'notices');" value="Guardar cambios" />
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