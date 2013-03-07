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
	function llenadoauto() {
		Fsubtotal = $("#Fsubtotal");
		Fiva = $("#Fiva");
		Ftotal = $("#Ftotal");

		if(Fsubtotal.val() != "") {
			iva = 0;
			total = parseFloat(Fsubtotal.val());
			Ftotal.val(total);
			Fiva.val(iva);
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
			iva =  total - (total / 1.16);
			subtotal = total / 1.16;
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
			pa.id_pag id
			, pa.id_eve evento
			, pa.num_pago
			, pa.recibo
			, pa.subtotal
			, pa.iva
			, pa.total
			, pa.id_tip_pag
			, if(tpa.nombre='OTRO',pa.tip_pag_otro,tpa.nombre)tipo
			, pa.id_est estatus
			, date_format(pa.fec_pag, '%d/%m/%Y %H:%i')fec_pag
			from pagos pa
			inner join tipo_pago tpa using(id_tip_pag)
			WHERE 1=1 AND pa.id_est in(1,3) order by pa.id_eve, pa.fec_pag, pa.num_pago";
		
			$stid = mysql_query($sql);
			?>

			<h3>PAGOS</h3>
			<div class="boxed-group-inner clearfix">
			<div class="filtro">
				<form id="formulario" name="formulario">
					<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
					<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="pagos" placeholder="buscar"/>
				</form>
			</div>
			
			<div class="scrool" id="cont">
			<table width="100%" cellspacing="0" cellpadding="3">
				<tr class="Cabezadefila">
					<th width="3%">#</th>
					<th width="15%">Fecha</th>
					<th width="10%">Evento</th>
					<th width="5%">Pago No.</th>
					<th width="10%">Tipo</th>
					<th width="10%">Recibo</th>
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

				$recibo = $row['recibo'];

				$subtotal = formateo($row['subtotal']);
				$iva = formateo($row['iva']);
				$total = formateo($row['total']);

				$tipo = $row['tipo'];
				$accion = ( $row['estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="pago" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="pago" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';												
				$rem = '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="pago" value="-"  class="rppermiso" />';
				$clase = ($row['tipo']=='DEVOLUCION')?' class="numeros_rojos"':'';
			?>
				<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
					<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
					<td align="center" class="celdaNormal"><?php echo $row['fec_pag'] ?></td>
					<td align="center" class="celdaNormal"><?php echo $evento ?></td>
					<td align="left" class="celdaNormal"><?php echo $num_pago; ?></td>
					<td align="left" class="celdaNormal"><?php echo $tipo; ?></td>
					<td align="left" class="celdaNormal"><?php echo $recibo; ?></td>
					<td align="right" class="celdaNormal"><span <?php echo $clase ?>><?php echo number_format($subtotal,2); ?></span></td>
					<td align="right" class="celdaNormal"><span <?php echo $clase ?>><?php echo number_format($iva,2); ?></span></td>
					<td align="right" class="celdaNormal"><span <?php echo $clase ?>><?php echo number_format($total,2); ?></span></td>
					<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
					<td align="center" class="celdaNormal"><a href="pagos.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar" class="rppermiso"><img src="images/Edit-icon-16.png" border="0"></a></td>
					<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
				</tr>
			<?php
			}			
			?>			

				<tr class="Cabezadefila">
					<td colspan='12'><?php echo $registros; ?> Registros encontrados.</td>
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
			, pa.recibo
			, pa.total
			, ev.facturar
			, (select sum(total) from pagos where id_eve=pa.id_eve and id_est=1)pagototal
			, pa.id_tip_pag
			, pa.id_est estatus
			, date_format(pa.fec_pag, '%d/%m/%Y')fecha
			, pa.fec_pag fecha2
			, pa.tip_pag_otro
			from pagos pa
			inner join eventos ev using(id_eve)
				WHERE 1=1 and pa.id_pag=".$id;
			
			$stid = mysql_query($sql);
			
			$row = mysql_fetch_assoc($stid);

			$id_eve = $row['id_eve'];
			$subtotal = formateo($row['subtotal']);
			$iva = formateo($row['iva']);
			$total = formateo($row['total']);
			$pagototal = formateo($row['pagototal']);
			$cap_total = (isset($row['facturar']) && $row['facturar']=='1')?true:false;

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
								SUBTOTAL<br><input class="" name="Fsubtotal" type="text" id="Fsubtotal" value="<?php echo $subtotal; ?>" size="10" maxlength="11" onkeypress="return vFlotante(event, this);" req="" lab="Subtotal de factura" style="text-align:right;"  onchange="llenadoauto()">
							</p>
						</td>
						<td align="left">
							<p>
								IVA<br><input class="" name="Fiva" type="text" id="Fiva" value="<?php echo $iva; ?>" size="10" maxlength="11" onkeypress="return vFlotante(event, this);" req="" lab="IVA de factura" style="text-align:right;"  onchange="llenadoauto()">
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

			<h4>RECIBO</h4>
				<table width="100%" cellpadding="5" cellspacing="0">				
				<tr>
					<td valign="top" align="left">
						<label><span class="required">*</span> RECIBO No.</label>
						<input class="" name="Frecibo" type="text" id="Frecibo" value="<?php echo $row['recibo']; ?>" size="10" maxlength="11" onkeypress="return vAbierta(event, this);" req="req" lab="Número de recibo" style="text-align:right;" />
					</td>
				</tr>				
			</table>
			<hr class="bleed-flush compact">

		<table width="100%" cellpadding="3" cellspacing="3" align="center">
			<tr>
				<td align="center"><br />
		            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update(this, 'pagos', 'Fnumpago|Ftipo|Fotro|Ffecha|Ftotal|Festatus|Frecibo', 'notices');" />&nbsp;
		            <input class="" name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
					<div class="avisorequired"><span class="required">* campos requeridos</span></div>
				</td>
			</tr>
		</table>
	</div>
	</form>
	<script type="text/javascript">
		<?php 
		if($cap_total) { ?>
			$("#Fsubtotal").attr('readonly','readonly');
			$("#Fiva").attr('readonly','readonly');
			$("#Ftotal").removeAttr('readonly');
		<?php 
		}else {
		?>
			$("#Fsubtotal").removeAttr('readonly');
			$("#Ftotal").attr('readonly','readonly');
			$("#Fiva").attr('readonly','readonly');
		<?php
		} 
		?>	
	</script>
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
				<div id="notices"></div>
				<h4>Detalle del Evento</h4>
				<table width="100%" cellpadding="5" cellspacing="0" border="1">
					<tr>
						<td width="30%">Número de Evento</td>
						<td width="20%"><input readonly="readonly" name="Fnoevento" type="text" id="Fnoevento" value="<?php echo $id_eve ?>" size="11" maxlength="11" onkeypress="return vNumeros(event, this);" req="req" lab="Número de Evento" style="text-align:right;" /></td>
						<td width="50%"></td>
					</tr>
				</table>
				<div class="div_segundo_plano">
				<!--  -->
				<?php
					$sql = "SELECT distinct ev.id_eve			
					, ev.id_tip_eve
					, date_format(ev.fecha, '%d/%m/%Y')fecha
					, ev.hora
					, ev.facturar
					, ev.num_personas personas
					, ev.pagado
					, ev.estatus
					, ev.cos_tot, ev.cos_tot*0.16 iva
					, sal.nombre salon
					, concat(cli.nombre, ' ', cli.ape_pat) cliente
					, tev.nombre tipodeevento
					, (SELECT (count(*) +1) from pagos where id_eve=".$id_eve.") Fnumpago
					from eventos ev
					inner join salones sal using(id_sal)
					inner join clientes cli using(id_cli)
					inner join tipo_evento tev using(id_tip_eve)
					inner join servicios_eventos serev using(id_eve)
					WHERE 1=1 AND ev.id_eve=".$id_eve;
					$stid = mysql_query($sql);

					if($row = mysql_fetch_assoc($stid)) {

						$cliente = $row['cliente'];
						$tipodeevento = $row['tipodeevento'];
						$fecha = $row['fecha'];
						$Fnumpago = $row['Fnumpago'];
						$cos_tot = number_format($row['cos_tot'],2);
						$cos_iva = number_format($row['iva'],2);
						$cos_total = $row['cos_tot'] + $row['iva'];
						$cos_total = number_format($cos_total,2);
						$facturar = (isset($row['facturar']) && $row['facturar']=='1')?'SÍ':'NO';
						$cap_subtotal = (isset($row['facturar']) && $row['facturar']=='0')?true:false;
						$cap_total = (isset($row['facturar']) && $row['facturar']=='1')?true:false;

						$estatus = $row['estatus'];
						$pagado = $row['pagado'];

						// linea funcional al 29012013
						// if($estatus != 'VENDIDO'){
						if($estatus != 'VENDIDO' and $estatus != 'TERMINADO'){
							echo '<p>El evento debe ser vendido o terminado para poder agregar pagos.</p>';
							exit;
						}
						
						if($pagado==1){
							echo '<p>Este evento ya ha sido pagado.</p>';
							exit;
						}
						?>
					<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="Cabezadefila">
							<th width="10%">FECHA</th>
							<th width="20%">TIPO</th>
							<th width="30%">CLIENTE</th>
							<th width="10%">COSTO</th>
							<?php
								echo $imprime_iva_total = (isset($row['facturar']) && $row['facturar']=='1')?'<th width="10%">IVA</th><th width="10%">TOTAL</th>':' ';
							?>
							<th width="10%">FACTURAR</th>
						</tr>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
							<td align="center" class="celdaNormal"><?php echo $fecha; ?></td>
							<td align="center" class="celdaNormal"><?php echo $tipodeevento; ?></td>
							<td align="center" class="celdaNormal"><?php echo $cliente; ?></td>
							<td align="center" class="celdaNormal">$ <?php echo $cos_tot; ?></td>
							<?php
								echo $imprime_iva_total = (isset($row['facturar']) && $row['facturar']=='1')?'<th width="10%">'.$cos_iva.'</th><th width="10%">'.$cos_total.'</th>':' ';
							?>
							<td align="center" class="celdaNormal"><?php echo $facturar; ?></td>
						</tr>
					</table>

					<h4>Información de Pagos</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr class="Cabezadefila">
							<th width="5%" >No.</th>
							<th>FECHA</th>
							<th>TIPO</th>
							<?php if($facturar!='NO') { ?>
							<th>SUBTOTAL</th>
							<th>IVA</th>
							<th>TOTAL</th>
							<?php }else { 
							?>
							<th>TOTAL</th>
							<?php }#else ?>
						</tr>
						<?php
						$sql="
						select 
						pa.id_pag
						, pa.id_eve
						, pa.num_pago
						, pa.subtotal
						, pa.iva
						, pa.total
						, pa.id_tip_pag
						, pa.id_est estatus
						, date_format(pa.fec_pag, '%d/%m/%Y')fecha
						, pa.fec_pag fecha2
						, tp.nombre tipodepago
						from pagos pa
						inner join tipo_pago tp using(id_tip_pag)
						WHERE 1=1 and pa.id_est = 1 and pa.id_eve=".$id_eve." order by num_pago";
						$stid = mysql_query($sql);
						
						$registros_pagos=0;
						while($rowP = mysql_fetch_assoc($stid)) {
							$registros_pagos++;
							$tmp_subtotal = number_format($rowP['subtotal'], 2);
							$tmp_iva = number_format($rowP['iva'], 2);
							$tmp_total = number_format($rowP['total'], 2);
						?>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" >
							<td valign="top" align="center" class="vdato"><?php echo $rowP['num_pago'] ?></td>
							<td valign="top" align="center" class="vdato"><?php echo $rowP['fecha'] ?></td>
							<td valign="top" align="left" class="vdato"><?php echo $rowP['tipodepago'] ?></td>
							<?php if($facturar!='NO') { ?>
							<td valign="top" align="right" class="vdato"><?php echo $tmp_subtotal ?></td>
							<td valign="top" align="right" class="vdato"><?php echo $tmp_iva ?></td>
							<td valign="top" align="right" class="vdato"><?php echo $tmp_total ?></td>
							<?php }else { 
							?>
							<td valign="top" align="right" class="vdato"><?php echo $tmp_subtotal ?></td>
							<?php }#else ?>
						</tr>
						<?php
						}

						if($registros_pagos ==0) {
							echo '
						<tr>
							<td valign="top" align="left" colspan="6"><span class="vdato">No hay pagos registrados.</span></td>
						</tr>';
						}
						$notas = '<input type="button" class="ch_eve_notas" value="VER NOTAS" />';

						?>
					</table>
					<?php
					}else 
						echo "El Evento ".$Fnoevento." no existe.";
				?>
				<!--  -->
				</div>
				<input type="hidden" value="<?php echo $id_eve ?>" name="Fideve" id="Fideve" />
				<?php
				$sql = "SELECT (count(*) + 1)Fnumpago from pagos where id_eve=".$id_eve;
				$stidNP = mysql_query($sql);

				if($rowNP = mysql_fetch_assoc($stidNP)) {
					$Fnumpago = $rowNP['Fnumpago'];
				}
				?>
				<input type="hidden" value="<?php echo $Fnumpago ?>" name="Fnumpago" id="Fnumpago" />
				

				<div id="mostrarform">
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

					<h4>RECIBO</h4>
						<table width="100%" cellpadding="5" cellspacing="0">				
						<tr>
							<td valign="top" align="left">
								<label><span class="required">*</span> RECIBO No.</label>
								<input class="" name="Frecibo" type="text" id="Frecibo" value="" size="10" maxlength="11" onkeypress="return vAbierta(event, this);" req="req" lab="Número de recibo" style="text-align:right;" />
							</td>
						</tr>				
					</table>
					<hr class="bleed-flush compact">

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center"><br />					
								<input type="button" onclick="add(this, 'pagos', 'Fnoevento|Ftipo|Fotro|Ffecha2|Fsubtotal|Fiva|Ftotal|Frecibo', 'notices');" value="Guardar cambios" />
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

		<?php
		if($cap_subtotal) {
		?>
			$("#Ftotal").attr("readonly","readonly");
		<?php
		}else if($cap_total) {
		?>
			$("#Fsubtotal").attr("readonly","readonly");
		<?php
		}
		?>
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