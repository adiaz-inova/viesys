<?php
define('MODULO', 900);
/* 
  * clientes.php
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
        altFormat: "yy-mm-dd",
        maxDate: "+0 days"
	});

	$("#verify").click(function () {
				Fnoevento = $("#Fnoevento");
				if(Fnoevento.val()!="") {
					a = Fnoevento.val().replace(/^[0]+/g,"");
					
					var url = "ver_pagos.inc.php";
					var divUpd = "#datosdelevento";
					var param = "Fnoevento="+a;
					
					$("#cargando").show();
					$(divUpd).load(url, param, function(){
							$("#cargando").hide("fast");
					});
					
					document.getElementById("formulario").reset();
					Fnoevento.val(a);
					
				}else
					$("#Fnoevento").focus();
	});


	$("#Ftipo").change(function () {
		if($(this).val()==5)
			$("#Fotro").removeAttr("disabled");
		else
			$("#Fotro").attr("disabled", "disabled");
	});
	';

	$js = '
	function llenadoauto_rev() {
		Fsubtotal = $("#Fsubtotal");
		Fiva = $("#Fiva");
		Ftotal = $("#Ftotal");

		if(Ftotal.val() != "") {
			subtotal = 0;
			iva = 0;
			total = parseFloat(Ftotal.val());
			iva =  total * 0.16;
			subtotal = total - iva;
			Fsubtotal.val(subtotal);
			Fiva.val(iva);
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
			pa.id_pag id
			, pa.id_eve evento
			, pa.num_pago
			, pa.subtotal
			, pa.iva
			, pa.total
			, pa.id_tip_pag
			, tpa.nombre tipo
			, pa.id_est estatus
			, pa.fec_pag
			from pagos pa
			inner join tipo_pago tpa using(id_tip_pag)
			WHERE 1=1 AND pa.id_est in(1,3) order by pa.id_eve, pa.num_pago";
		
			$stid = mysql_query($sql);
			?>

			<h3>PAGOS</h3>
			<div class="boxed-group-inner clearfix">
			<div class="filtro">
				<form id="formulario" name="formulario">
					<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
					<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="pagos" placeholder="buscar"/>
				</form>
				<div align="right" class="botonsuperior">
	        		<input type="button" value="Agregar" href="<?php echo $_SERVER['PHP_SELF'] ?>?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
				</div>
			</div>
			
			<div class="scrool" id="cont">
			<table width="100%" cellspacing="0" cellpadding="3">
				<tr class="Cabezadefila">
					<th width="3%">#</th>
					<th width="25%">Evento</th>
					<th width="5%">Pago No.</th>
					<th width="10%">Tipo</th>
					<th width="15%">Subtotal</th>
					<th width="10%">IVA</th>
					<th width="15%">Total</th>
					<th width="5%">Acción</th>
					<th width="5%">Edit</th>
					<th width="5%">Elim</th>
				</tr>

			<?php
			$registros = 0;
			while($row = mysql_fetch_assoc($stid)) {	
				$registros++;

				$id = $row['id'];
				$num_pago = $row['num_pago'];
				
				$evento = $row['evento'];
				$evento = str_pad($evento, 4, '0', STR_PAD_LEFT );

				$subtotal = formateo($row['subtotal']);
				$iva = formateo($row['iva']);
				$total = formateo($row['total']);

				$tipo = $row['tipo'];
				$accion = ( $row['estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="pago" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="pago" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';												
				$rem = '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="pago" value="-" />';

			?>
				<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
					<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
					<td align="center" class="celdaNormal"><a href="pagos.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $evento; ?></a></td>
					<td align="left" class="celdaNormal"><a href="pagos.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $num_pago; ?></a></td>
					<td align="left" class="celdaNormal"><a href="pagos.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $tipo; ?></a></td>
					<td align="right" class="celdaNormal"><?php echo $subtotal; ?></td>
					<td align="right" class="celdaNormal"><?php echo $iva; ?></td>
					<td align="right" class="celdaNormal"><?php echo $total; ?></td>
					<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
					<td align="center" class="celdaNormal"><a href="pagos.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td>
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

			<h3>MODIFICAR DATOS DE PAGO</h3>
			<?php	/*<div class="notices">&nbsp;</div>*/	?>
			<form name="formulario" id="formulario" class="js-notifications-settings">
			<div id="notices"></div>
				<div class="boxed-group-inner">

			<?php					
			# Consultamos la informacion del usuario
			$sql="
			select 
			pa.id_pag id
			, pa.id_eve
			, pa.num_pago
			, pa.subtotal
			, pa.iva
			, pa.total
			, (select sum(total) from pagos where id_eve=pa.id_eve and id_est=1)pagototal
			, pa.id_tip_pag
			, pa.id_est estatus
			, date_format(pa.fec_pag, '%d/%m/%Y')fecha
			, pa.fec_pag fecha2
			, pa.tip_pag_otro
			from pagos pa
				WHERE 1=1 and pa.id_pag=".$id;
			
			$stid = mysql_query($sql);
			
			$row = mysql_fetch_assoc($stid);

			$id_eve = $row['id_eve'];
			$subtotal = formateo($row['subtotal']);
			$iva = formateo($row['iva']);
			$total = formateo($row['total']);
			$pagototal = formateo($row['pagototal']);

			?>
			<h4>Acerca del pago</h4>
				<table width="100%">
					<tr>
						<td width="30%" align="left">
							<p>
								<span class="required">*</span>PAGO No.<br><input class="" name="Fnumpago" type="text" id="Fnumpago" value="<?php echo $row['num_pago']; ?>" size="10" maxlength="10" onkeypress="return vNumeros(event, this);" req="req" lab="Nombre de pago" />
							</p>
						</td>
						<td width="30%" align="left"></td>
						<td width="40%" align="left">
							<p>
								<span class="required">*</span>FECHA<br>
								<input name="Ffecha" type="hidden" id="Ffecha" value="<?php echo $row['fecha2']; ?>" />
								<input name="Ffecha2" type="text" id="Ffecha2" value="<?php echo $row['fecha']; ?>" size="10" maxlength="10" onkeypress="return vNumeros(event, this);" req="req" lab="Fecha de factura" style="text-align:right;" />
							</p>
						</td>
					<tr>
					</tr>
						<td align="left" colspan="2">
							<span class="required">*</span>TIPO <br>
							<select name="Ftipo" id="Ftipo"  req="req" lab="Tipo de pago" req="req" lab="Tipo de pago" />
								<!--option value="">seleccione</option-->
							<?php
								$sql="select id_tip_pag, nombre from tipo_pago order by nombre";
								
								$stid = mysql_query($sql);

								while (($rowPer = mysql_fetch_assoc($stid))) {
									if( $rowPer['id_tip_pag'] == $row['id_tip_pag']) {
										echo '	<option selected="selected" value="'.$rowPer['id_tip_pag'].'">'.$rowPer['nombre'].'</option>';
									}else{	
										echo '	<option value="'.$rowPer['id_tip_pag'].'">'.$rowPer['nombre'].'</option>';
									}
								}

								$enabled = ($row['id_tip_pag']==5)?'':' disabled="disabled" ';
								$Fotro = (trim($row['tip_pag_otro'])=='')?'':trim($row['tip_pag_otro']);
							?>
							</select>
						</td>
						<td width="30%" align="left">
							<p>
								OTRO TIPO DE PAGO<br><input <?php echo $enabled ?> class="" name="Fotro" type="text" id="Fotro" value="<?php echo $Fotro ?>" size="25" maxlength="50" onkeypress="return vAbierta(event, this);" req="req" lab="Otro tipo de pago" />
							</p>
						</td>
					</tr>
					<tr>
						<td align="left">
							<p>
								SUBTOTAL<br><input class="" name="Fsubtotal" type="text" id="Fsubtotal" value="<?php echo $subtotal; ?>" size="10" maxlength="11" onkeypress="return vFlotante(event, this);" req="" lab="Subtotal de factura" style="text-align:right;" />
							</p>
						</td>
						<td align="left">
							<p>
								IVA<br><input class="" name="Fiva" type="text" id="Fiva" value="<?php echo $iva; ?>" size="10" maxlength="11" onkeypress="return vFlotante(event, this);" req="" lab="IVA de factura" style="text-align:right;" />
							</p>
						</td>
						<td align="left">
							<p>
								<span class="required">*</span>TOTAL<br><input class="" name="Ftotal" type="text" id="Ftotal" value="<?php echo $total; ?>" size="10" maxlength="11" onkeypress="return vFlotante(event, this);" req="req" lab="Total de factura" style="text-align:right;" onchange="llenadoauto_rev()" />
							</p>
						</td>
					</tr>
				</table>
				<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>"/>
			<hr class="bleed-flush compact">

			<h4>Detalle del Evento</h4>
				<?php
					$sql ="select ser.id_ser, ser.nombre
							,(select 1 from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id_eve.")contratado
							,(select seev.costo from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id_eve." )costo
							,tser.nombre tipo
							from servicios ser
							inner join tipo_servicio tser using(id_tip_ser)
							order by tipo, nombre";
					$stidEv = mysql_query($sql);
				?>
				<table width="100%" cellpadding="5" cellspacing="0" border="1">
					<tr>
						<th width="50%">Servicio</th>
						<th width="20%">Tipo</th>
						<th width="30%">Costo</th>
					</tr>
					<?php
					$servicios_coontratados = 0;
					$total_servicios = 0;
					while ($rowEv = mysql_fetch_assoc($stidEv)) {						
						if( $rowEv['contratado'] == '1') {
							$servicios_coontratados++;
							$total_servicios += $rowEv['costo'];
							$costo = formateo($rowEv['costo']);
							echo '
					<tr>
						<td align="left">'.$rowEv['nombre'].'</td>
						<td align="left">'.$rowEv['tipo'].'</td>
						<td align="right">'.$costo.'</td>
					</tr>';
						}
					}
					if($servicios_coontratados==0)
						echo '
					<tr>
						<td align="left" colspan="2">No hay servicios contratados para este evento.</td>
					</tr>';
					else {
						$total_servicios = formateo($total_servicios);
						$restante = 0;
						$restante = $total_servicios - $pagototal;
						$restante = formateo($restante);
						echo '
					<tr>
						<td align="right" colspan="2">TOTAL</td>
						<td align="right">'.$total_servicios.'</td>
					</tr>
					<tr>
						<td align="right" colspan="2">PAGADO</td>
						<td align="right">'.$pagototal.'</td>
					</tr>
					<tr>
						<td align="right" colspan="2">RESTANTE</td>
						<td align="right">'.$restante.'</td>
					</tr>';
					}
					?>					
				</table>				
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
		            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update(this, 'pagos', 'Fnumpago|Ftipo|Fotro|Ffecha|Ftotal|Festatus', 'notices');" />&nbsp;
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
			# Control de seguridad
			$id_eve = (isset($id_eve) && $id_eve!='')? $id_eve : '';
			$banderilla = ' style="display:none" ';
			$banderilla1 = '';
			if (isset($id_eve)) {
				#header('Location: '.$_SERVER['PHP_SELF']);
				$banderilla = '';
				$banderilla1 = ' style="display:none" ';
			}
?>

			<h3>AGREGAR PAGO</h3>
			<form name="formulario" id="formulario" class="js-notifications-settings">
			<div class="boxed-group-inner">
				<h4>Detalle del Evento</h4>
				<table width="100%" cellpadding="5" cellspacing="0" border="1">
					<tr>
						<td width="30%">Número de Evento</td>
						<td width="20%"><input name="Fnoevento" type="text" id="Fnoevento" value="<?php echo $id_eve ?>" size="11" maxlength="11" onkeypress="return vNumeros(event, this);" req="req" lab="Número de Evento" style="text-align:right;" /></td>
						<td width="50%"><input type="button" value="Buscar" id="verify" /></td>
					</tr>
					<tr>
						<td id="datosdelevento" colspan="3"></td>
					</tr>
				</table>
			</div>
			<div id="notices"></div>
				<div class="boxed-group-inner">
				<input type="hidden" value="<?php echo $id_eve ?>" name="Fideve" id="Fideve" />
			


<div id="mostrarform"  style="display:none">
			<h4>Acerca del pago</h4>
				<table width="100%">
					<tr>
						<td width="40%" align="left">
							<span class="required">*</span>TIPO <br>
							<select name="Ftipo" id="Ftipo"  req="req" lab="Tipo de pago" req="req" lab="Tipo de pago" />
								<option value="">seleccione</option>
							<?php
								$sql="select id_tip_pag, nombre from tipo_pago order by nombre";
								
								$stid = mysql_query($sql);

								while (($rowPer = mysql_fetch_assoc($stid))) {
									echo '	<option value="'.$rowPer['id_tip_pag'].'">'.$rowPer['nombre'].'</option>';
								}
							?>
							</select>
						</td>
						<td width="30%" align="left">
							<p>
								OTRO TIPO DE PAGO<br><input disabled="disabled" class="" name="Fotro" type="text" id="Fotro" value="" size="25" maxlength="50" onkeypress="return vAbierta(event, this);" req="req" lab="Otro tipo de pago" />
							</p>
						</td>
						<td width="30%" align="left">
							<p>
								<span class="required">*</span>FECHA<br>
								<input name="Ffecha" type="hidden" id="Ffecha" value="" />
								<input name="Ffecha2" type="text" id="Ffecha2" value="" size="10" maxlength="10" onkeypress="return vNumeros(event, this);" req="req" lab="Fecha de pago" style="text-align:right;" />
							</p>
						</td>
					</tr>
					<tr>
						<td width="40%" align="left">
							<p>
								<span class="required">*</span>SUBTOTAL<br><input class="" name="Fsubtotal" type="text" id="Fsubtotal" value="" size="10" maxlength="11" onkeypress="return vFlotante(event, this);" req="req" lab="Subtotal del pago" style="text-align:right;" onchange="llenadoauto()" />
							</p>
						</td>
						<td width="30%" align="left">
							<p>
								IVA<br><input class="" name="Fiva" type="text" id="Fiva" value="" size="10" maxlength="11" onkeypress="return vFlotante(event, this);" req="" lab="IVA del pago" style="text-align:right;" onchange="llenadoauto()" readonly="readonly" />
							</p>
						</td>
						<td width="30%" align="left">
							<p>
								TOTAL<br><input class="" name="Ftotal" type="text" id="Ftotal" value="" size="10" maxlength="11" onkeypress="return vFlotante(event, this);" req="req" lab="Total del pago" style="text-align:right;" onchange="llenadoauto_rev()" />
							</p>
						</td>
					</tr>
				</table>
			<hr class="bleed-flush compact">

		<table width="100%" cellpadding="3" cellspacing="3" align="center">
			<tr>
				<td align="center"><br />					
					<input type="button" onclick="add(this, 'pagos', 'Fnoevento|Ftipo|Fotro|Ffecha2|Fsubtotal', 'notices');" value="Guardar cambios" />
					<input type="button"  value="Cancelar" href="<?php echo $_SERVER['PHP_SELF'] ?>" onclick=";window.location.href=this.getAttribute('href');return false;" />
		            
		            <div class="avisorequired"><span class="required">* campos requeridos</span></div>						
				</td>
			</tr>
		</table>
</div>
	</div>
	</form>
<?php
	if (isset($id_eve)) {
?>
	<script type="text/javascript">
	<!--
		$("#Fnoevento").trigger("focus");
		$("#verify").trigger('click');
	//-->
	</script>
<?php
	}
?>
<?php
		break;		
	}// switch

	mysql_close($conn);
	require_once('includes/html_footer.php');
?>