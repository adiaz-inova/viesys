<?php
define('MODULO', 600);
/* 
  * facturas.php
  * VIE 2012
  * creado      01/08/2012
  * modificado  14/04/2012
  * autor       I.S.C. Alejandro Diaz Garcia
  */
	$addruta = '';
	require_once('token.inc.php');
	require_once('includes/getvalues.php');
	require_once('includes/session_principal.php');
	
	if( !isset($task) || trim($task) == '')
		$task = 'list';

	/* incluyo mis js extras antes de llamar el header*/
	$js_extras_onready='
	$.datepicker.setDefaults({
		inline: true,
		dateFormat: "dd/mm/yy",
		numberOfMonths: 1,
		monthNames: ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
		dayNames: ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
		dayNamesMin: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
		showAnim: "slide"			
	});
	
	$("#Ffecha2").datepicker({
		altField: "#Ffecha",
        altFormat: "yy-mm-dd"
	});

	$("#Fsubtotal").change(function () {
		llenadoauto();
	});
	
	$("#Ftotal").change(function () {
		llenadoauto_rev();
	});

	';

	$js = '
	function llenadoauto() {
		Fsubtotal = $("#Fsubtotal");
		Fiva = $("#Fiva");
		Ftotal = $("#Ftotal");

		if(Fsubtotal.val() != "") {
			var subtotal = parseFloat(Fsubtotal.val());
			iva = subtotal * 0.16;
			total = subtotal + iva;
			Ftotal.val(total.toFixed(2));
			Fiva.val(iva.toFixed(2));
		}
	}
	function llenadoauto_rev() {
		Fsubtotal = $("#Fsubtotal");
		Fiva = $("#Fiva");
		Ftotal = $("#Ftotal");

		if(Ftotal.val() != "") {
			subtotal = 0;
			iva = 0;
			total = parseFloat(Ftotal.val());
			subtotal = total / 1.16;
			iva =  total - subtotal;
			Fsubtotal.val(subtotal.toFixed(2));
			Fiva.val(iva.toFixed(2));
		}
	}
	';
	
#	if(!isset($_SESSION[TOKEN.'ADMIN']) || !$_SESSION[TOKEN.'ADMIN']) {
#		$contenido_html = 'NO TIENE PRIVILEGIOS PARA CONSULTAR ESTE MÓDULO';
#	}

	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);
	
	require_once('includes/html_template.php');
	
	switch($task) {
		case 'list': #- - - - - - - - - - - - - - -- - - LISTAR
							
			$sql = "select 
			fac.id_fac id
			, fac.num_fac
			, date_format(fac.fecha, '%d/%m/%y')fecha
			, fac.id_cli
			, fac.id_eve
			, ev.pagado
			,case 
			when cli.empresa <> '' then cli.empresa
			when cli.nombre <> '' then concat(cli.nombre,' ',cli.ape_pat,' ',cli.ape_mat)
			else '---'
			end cliente
			, fac.subtotal
			, fac.iva
			, fac.total
			, fac.id_est estatus
			from facturas fac 
			inner join clientes cli using(id_cli)
			left join eventos ev using(id_eve)
			WHERE 1=1 AND fac.id_est in(1,3) order by num_fac, cliente";
		
			$stid = mysql_query($sql);
			?>

			<h3>FACTURAS</h3>
			<div class="boxed-group-inner clearfix">
			<div class="filtro">
				<form id="formulario" name="formulario">
					<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
					<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="facturas" placeholder="buscar"/>
				</form>
			</div>
			
			<div class="scrool" id="cont">
			<table width="100%" cellspacing="0" cellpadding="3">
				<tr class="Cabezadefila">
					<th width="3%">#</th>
					<th width="10%">Factura</th>
					<th width="10%">Evento</th>
					<th width="10%">Fecha</th>
					<th width="12%">Cliente</th>
					<th width="15%">Total</th>
					<th width="10%">Pagado</th>
					<!-- <th width="5%">Acción</th> -->
					<th width="5%">Edit</th>
					<th width="5%">Elim</th>
				</tr>

			<?php
			$registros = 0;
			while($row = mysql_fetch_assoc($stid)) {	
				$registros++;

				$id = $row['id'];
				$id_cli = $row['id_cli'];
				$id_eve = $row['id_eve'];
				$num_fac = $row['num_fac'];
				$fecha = $row['fecha'];
				$cliente = $row['cliente'];
				$total = $row['total'];
				$pagado = (isset($row['pagado']) && $row['pagado']==0)? 'NO' : 'SI';
				
				$accion = ( $row['estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="factura" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="factura" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';												
				$rem = '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="factura" value="-" />';

			?>
				<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
					<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
					<td align="right" class="celdaNormal"><a href="facturas.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $num_fac; ?></a></td>
					<td align="center" class="celdaNormal"><?php echo $id_eve; ?></td>
					<td align="center" class="celdaNormal"><a href="facturas.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $fecha; ?></a></td>
					<td align="left" class="celdaNormal"><a href="clientes.php?task=edit&id=<?php echo $id_cli; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $cliente; ?></a></td>
					<td align="right" class="celdaNormal"><?php echo $total; ?></td>
					<td align="center" class="celdaNormal"><?php echo $pagado; ?></td>
					<!-- <td align="center" class="celdaNormal"><?php echo $accion; ?></td> -->
					<td align="center" class="celdaNormal"><a href="facturas.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td>
					<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
				</tr>
			<?php
			}			
			?>			

				<tr class="Cabezadefila">
					<td colspan='10'><?php echo $registros; ?> Registros encontrados.</td>
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
?>
			<h3>MODIFICAR DATOS DE FACTURA</h3>
		<?php	/*<div class="notices">&nbsp;</div>*/	?>
			<form name="formulario" id="formulario" class="js-notifications-settings">
			<div id="notices"></div>
				<div class="boxed-group-inner">
<?php					
			# Consultamos la informacion del usuario
			$sql="
			select 
			fac.id_fac id
			, fac.num_fac
			, date_format(fac.fecha, '%d/%m/%y')fecha
			, fac.fecha fecha2
			, fac.id_cli
			, fac.subtotal
			, fac.iva
			, fac.id_eve
			, fac.total
			, fac.id_est estatus
			from facturas fac where 1=1
			and fac.id_fac=".$id;
			
			$stid = mysql_query($sql);
			
			$row = mysql_fetch_assoc($stid);

			$subtotal = formateo($row['subtotal']);
			$iva = formateo($row['iva']);
			$total = formateo($row['total']);

?>
			<h4>EVENTO</h4>
				<table width="100%" cellpadding="5" cellspacing="0">				
				<tr>
					<td valign="top" align="left">
						<span class="required">*</span>EVENTO
						<input class="" name="Fevento" type="text" id="Fevento" value="<?php echo $row['id_eve']; ?>" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="req" lab="Número de evento" style="text-align:right;" />
					</td>
				</tr>				
			</table>
			<hr class="bleed-flush compact">

			<h4>Acerca de la factura</h4>
				<table width="100%">
					<tr>
						<td width="30%" align="left">
							<p>
								<span class="required">*</span>FACTURA No.<br><input class="" name="Fnumfac" type="text" id="Fnumfac" value="<?php echo $row['num_fac']; ?>" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="req" lab="Número de factura" style="text-align:right;" />
							</p>
						</td>
						<td width="30%" align="left">
							<p>
								<span class="required">*</span>FECHA<br>
								<input name="Ffecha" type="hidden" id="Ffecha" value="<?php echo $row['fecha2']; ?>" />
								<input name="Ffecha2" type="text" id="Ffecha2" value="<?php echo $row['fecha']; ?>" size="10" maxlength="10" onkeypress="return vNumeros(event, this);" req="req" lab="Fecha de factura" style="text-align:right;" />
							</p>
						</td>
						<td width="40%" align="left">
							<span class="required">*</span>CLIENTE
							<br />
							<select class="" name="Fcliente" id="Fcliente" req="req" lab="Número de factura" >
								<option value="">seleccione..</option>
								<?php
									$sql="select id_cli, concat(nombre, ' ', ape_pat, ' ', ape_mat)nombre from clientes where id_est in(1,3) order by nombre";
									
									$stid = mysql_query($sql);

									while (($rowPer = mysql_fetch_assoc($stid))) {
										if( $rowPer['id_cli'] == $row['id_cli']) {
											echo '	<option selected="selected" value="'.$rowPer['id_cli'].'">'.$rowPer['nombre'].'</option>';
										}else{	
											echo '	<option value="'.$rowPer['id_cli'].'">'.$rowPer['nombre'].'</option>';
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left">
							<p>
								SUBTOTAL<br><input class="" name="Fsubtotal" type="text" id="Fsubtotal" value="<?php echo $subtotal; ?>" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="" lab="Subtotal de factura" style="text-align:right;" />
							</p>
						</td>
						<td align="left">
							<p>
								IVA<br><input readonly="readonly" class="" name="Fiva" type="text" id="Fiva" value="<?php echo $iva; ?>" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="" lab="IVA de factura" style="text-align:right;" />
							</p>
						</td>
						<td align="left">
							<p>
								TOTAL<br><input class="" name="Ftotal" type="text" id="Ftotal" value="<?php echo $total; ?>" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="" lab="Total de factura" style="text-align:right;" />
							</p>
						</td>
					</tr>
				</table>
				<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>"/>
			<hr class="bleed-flush compact">

			<h4>Estatus</h4>
				<table width="100%" cellpadding="5" cellspacing="0">				
				<tr>
					<td valign="top" align="left">
						<span class="required">*</span>ESTATUS
						<br />
						<select class="" name="Festatus" id="Festatus" req="req" lab="Estatus" />
							<!--option value="">...</option-->
							<?php
								$sql="select id_est, nombre from estatus where id_est<4 ORDER BY nombre";
								
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
		            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update(this, 'facturas', 'Fevento|Fnumfac|Ffecha|Fcliente|Festatus', 'notices');" />&nbsp;
		            <input class="" name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
					<div class="avisorequired"><span class="required">* campos requeridos</span></div>
				</td>
			</tr>
		</table>
	</div>
	</form>
<?php
		break;
		case 'add': #- - - - - - - - - - - - - - -- - - AGREGAR
?>

			<h3>MODIFICAR DATOS DE FACTURA</h3>
			<form id="formulario" class="js-notifications-settings">
			<div id="notices"></div>
				<div class="boxed-group-inner">

			<h4>EVENTO</h4>
				<table width="100%" cellpadding="5" cellspacing="0">				
				<tr>
					<td valign="top" align="left">
						<span class="required">*</span>EVENTO
						<input class="" name="Fevento" type="text" id="Fevento" value="<?php echo $id_eve ?>" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="req" lab="Número de evento" style="text-align:right;" />
					</td>
				</tr>				
			</table>
			<hr class="bleed-flush compact">

			<h4>Acerca de la factura</h4>
				<table width="100%">
					<tr>
						<td width="30%" align="left">
							<p>
								<span class="required">*</span>FACTURA No.<br><input class="" name="Fnumfac" type="text" id="Fnumfac" value="" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="req" lab="Número de factura" style="text-align:right;" />
							</p>
						</td>
						<td width="30%" align="left">
							<p>
								<span class="required">*</span>FECHA<br>
								<input name="Ffecha" type="hidden" id="Ffecha" value="" />
								<input name="Ffecha2" type="text" id="Ffecha2" value="" size="10" maxlength="10" onkeypress="return vNumeros(event, this);" style="text-align:right;" req="req" lab="Fecha de factura" />
							</p>
						</td>
						<td width="40%" align="left">
							<span class="required">*</span>CLIENTE
							<br />
							<select class="" name="Fcliente" id="Fcliente" req="req" lab="Cliente" >
								<option value="">seleccione..</option>
								<?php
									$sql="select id_cli, concat(nombre, ' ', ape_pat, ' ', ape_mat)nombre from clientes where id_est in(1,3) order by nombre";
									
									$stid = mysql_query($sql);

									while (($rowPer = mysql_fetch_assoc($stid))) {
										echo '	<option value="'.$rowPer['id_cli'].'">'.$rowPer['nombre'].'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left">
							<p>
								SUBTOTAL<br><input class="" name="Fsubtotal" type="text" id="Fsubtotal" value="" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="" lab="Subtotal de factura" style="text-align:right;" />
							</p>
						</td>
						<td align="left">
							<p>
								IVA<br><input readonly="readonly" class="" name="Fiva" type="text" id="Fiva" value="" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="" lab="IVA de factura" style="text-align:right;" />
							</p>
						</td>
						<td align="left">
							<p>
								TOTAL<br><input class="" name="Ftotal" type="text" id="Ftotal" value="" size="10" maxlength="11" onkeypress="return vNumeros(event, this);" req="" lab="Total de factura" style="text-align:right;" />
							</p>
						</td>
					</tr>
				</table>
			<hr class="bleed-flush compact">

			<h4>Estatus</h4>
				<table width="100%" cellpadding="5" cellspacing="0">				
				<tr>
					<td valign="top" align="left">
						<span class="required">*</span>ESTATUS
						<br />
						<select class="" name="Festatus" id="Festatus" req="req" lab="Estatus" />
							<option value="">...</option>
							<?php
								$sql="select id_est, nombre from estatus where id_est<4 ORDER BY nombre";
								
								$stid = mysql_query($sql);

								while (($rowPer = mysql_fetch_assoc($stid))) {
									echo '	<option value="'.$rowPer['id_est'].'">'.$rowPer['nombre'].'</option>';
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
					
					<input type="button" onclick="add(this, 'facturas', 'Fevento|Fnumfac|Ffecha2|Fcliente|Festatus', 'notices');" value="Guardar cambios" />
					<input type="button"  value="Cancelar" href="<?php echo $_SERVER['PHP_SELF'] ?>" onclick=";window.location.href=this.getAttribute('href');return false;" />
		            
		            <div class="avisorequired"><span class="required">* campos requeridos</span></div>						
				</td>
			</tr>
		</table>
	</div>
	</form>
<?php
		break;		
	}// switch

	mysql_close($conn);
	require_once('includes/html_footer.php');

?>