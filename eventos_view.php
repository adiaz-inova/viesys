<?php
define('MODULO', 500);

/* 
  * VIE 2012
  * creado      01/08/2012
  * modificado  14/04/2012
  * autor       I.S.C. Alejandro Diaz Garcia
  */
	$addruta = '';
	require_once('token.inc.php');
	require_once('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require_once('includes/conection.php');

	$id = $id_eve;
	# Control de seguridad
	if (!isset($id)) {
		header('Location: '.$_SERVER['PHP_SELF']);
	}

	# Consultamos la informacion del usuario
	$sql="
	select 
	ev.id_eve
	, ev.id_emp
	, ev.id_cli
	, ev.id_sal
	, ev.id_tip_eve
	, ev.num_personas personas
	, ev.pagado
	, ev.facturar
	, ev.estatus
	, ev.cos_tot
	, ev.fecha fecha2
	, ev.observaciones
	, date_format(ev.fecha, '%d/%m/%Y')fecha
	, date_format(ev.falta, '%d/%m/%Y')falta
	, date_format(ev.hora, '%H')hora
	, date_format(ev.hora, '%i')minuto
	, teve.nombre tipodeevento
	, sal.nombre salon
	, cli.email
	, concat(cli.nombre, ' ', cli.ape_pat, ' ', cli.ape_mat)cliente
	, concat(emp.nombre, ' ', emp.ape_pat, ' ', emp.ape_mat)vendedor
	, case 
			when CURDATE() <= ev.fecha then 'VIGENTE'
			else 'VENCIDA'
			end vigencia
	from eventos ev 
	inner join tipo_evento teve using(id_tip_eve)
	inner join salones sal using(id_sal)
	inner join clientes cli using(id_cli)
	inner join empleados emp using(id_emp)
	where 1=1 
	and ev.id_eve=".$id;
	$stid = mysql_query($sql);
	$row = mysql_fetch_assoc($stid);

	$ctotal = $row['cos_tot'];
	$noevento = str_pad($id, 4, '0', STR_PAD_LEFT );
	$reqfactura = (isset($row['facturar']) && $row['facturar']=='1')?'SÍ':'NO';
	$pagado = (isset($row['pagado']) && $row['pagado']==0)? 'NO' : 'SI';
	$email = (isset($row['email']) && $row['email']!='')? $row['email'] : '';
	?>
	<script type="text/javascript">
	$(document).ready(function() {
		$('.ch_eve_notas').click(function () {
	        $('#DatossEvento').toggle();
	        $('#NotasEvento').toggle();
	     });
	});
	</script>	
<div class="settings-content">
  <div class="boxed-group" id="DatossEvento">
	<h3>CONSULTAR DATOS DE EVENTO</h3>
	<div class="boxed-group-inner clearfix">
		<form name="formulario" id="formulario">
			<h4>Detalles del evento</h4>
			<table width="100%" cellpadding="5" cellspacing="5">
				<tr>
					<td width="25%" align="center">No. EVENTO: <span class="vdato"><?php echo $noevento; ?></span></td>
					<td width="25%" align="center">ESTATUS: <span class="vdato"><?php echo $row['estatus']; ?></span></td>
					<td width="25%" align="center">VIGENCIA: <span class="vdato"><?php echo $row['vigencia']; ?></span></td>
					<td width="25%" align="center">PAGADO: <span class="vdato"><?php echo $pagado; ?></span></td>
				</tr>
			</table>

			<table width="100%" cellpadding="5" cellspacing="5">
				<tr>
					<td width="30%" align="left">
						FECHA: <span class="vdato"><?php echo $row['fecha']; ?></span>
					</td>
					<td width="30%" align="left">
						HORA: <span class="vdato"><?php echo $row['hora'].':'.$row['minuto'].' hrs'; ?></span>
					</td>
					<td width="40%" align="left">
						TIPO: <span class="vdato"><?php echo  $row['tipodeevento']; ?></span>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<td width="30%" valign="top" align="left">
						INVITADOS: <span class="vdato"><?php echo number_format($row['personas']) ?></span>
					</td>
					<td width="70%" valign="top" align="left">
						SALÓN: <span class="vdato"><?php echo $row['salon'] ?></span>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<td width="70%">
						CLIENTE: <span class="vdato"><?php echo $row['cliente'] ?></span>
					</td>
					<td width="30%">
						¿REQUIERE FACTURA?: <span class="vdato"><?php echo $reqfactura ?></span>
					</td>
				</tr>
			</table>
			<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>" />
			<hr class="bleed-flush compact" />

			<h4>Servicios contratados</h4>
			<table width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<th width="30%" valign="top" align="left">SERVICIO</th>
					<!--<th width="40%" valign="top" align="left">TIPO</th> -->
					<th width="30%" valign="top" align="left">COSTO</th>
				</tr>
				<?php
					$sql="select ser.id_ser, ser.nombre, (select 1 from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id.")contratado
					,(select seev.costo from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id." )costo,
					tser.nombre tipodeservicio
					from servicios ser
					inner join tipo_servicio tser using(id_tip_ser)
					where 1
					order by nombre";

					#$sql="select id_tip_ser, nombre from tipo_servicio order by nombre";
					$stidSer = mysql_query($sql);

					$disabled = ' disabled="disabled" ';
					$list_servicios = '';
					$list_ctotal = 0;
					$registros_ser=0;
					while (($rowSer = mysql_fetch_assoc($stidSer))) {
						$registros_ser++;
						if($rowSer['contratado']=='1') {
							$list_id_ser = $rowSer['id_ser'];
							$list_id_nombre = $rowSer['nombre'];
							$list_servicios .= $list_id_ser.'|';
							$list_servicios_costo = number_format($rowSer['costo'], 2);
							$list_tipo_servicios = $rowSer['tipodeservicio'];
							

							echo '
				<tr>
					<td valign="top" align="left"><span class="vdato">'.$list_id_nombre .'</span></td>
					<!--<td valign="top" align="left"><span class="vdato">'.$list_tipo_servicios.'</span></td> -->
					<td valign="top" align="right"><span class="vdato">'.$list_servicios_costo.'</span></td>
				</tr>';

							$list_ctotal += $rowSer['costo'];
						}

					}
					if($registros_ser ==0) {
						echo '
				<tr>
					<td valign="top" align="left" colspan="3"><span class="vdato">No hay servicios registrados.</span></td>
				</tr>';
					}
				?>
				<?php if($reqfactura=='NO') {
					$iva = 0;
					$total = $list_ctotal;
				 ?>
				 <tr>
					<td colspan="2" align="right">TOTAL</td>
					<td valign="top" align="right"><span class="vdato">$ <?php echo number_format($list_ctotal, 2); ?></span></td>
				</tr>
				<?php }else { 
					$iva = 0;
					$total = 0;
					$iva = (float)$list_ctotal * 0.16;
					$total = (float)$list_ctotal + $iva;
				?>
				<tr>
					<td colspan="2" align="right">SUBTOTAL</td>
					<td valign="top" align="right"><span class="vdato">$ <?php echo number_format($list_ctotal, 2); ?></span></td>
				</tr>
				<tr>
					<td colspan="2" align="right">IVA 16%</td>
					<td valign="top" align="right"><span class="vdato">$ <?php echo number_format($iva, 2); ?></span></td>
				</tr>
				<tr>
					<td colspan="2" align="right">TOTAL</td>
					<td valign="top" align="right"><span class="vdato">$ <?php echo number_format($total, 2); ?></span></td>
				</tr>
				<?php }#else ?>
			</table>
			<hr class="bleed-flush compact" />

			<h4>Observaciones</h4>
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td align="center">
						<div class="vdato" style="border: 1px solid #ddd;padding:5px;text-align:justify;">
						<?php echo str_ireplace("\n", "<br>", $row['observaciones']); ?>
						</div>
					</td>
				</tr>
			</table>
			<hr class="bleed-flush compact" />

			<h4>Información de Pagos</h4>
			<table width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<th width="5%" >No.</th>
					<th>FECHA</th>
					<th>TIPO</th>
					<th>SUBTOTAL</th>
					<th>IVA</th>
					<th>TOTAL</th>
					<th>RESTA</th>
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
					, if(tp.nombre='OTRO',pa.tip_pag_otro,tp.nombre)tipo
					, tp.nombre tipodepago
					from pagos pa
					inner join tipo_pago tp using(id_tip_pag)
					WHERE 1=1 and pa.id_est = 1 and pa.id_eve=".$id_eve." order by num_pago";
				$stid = mysql_query($sql);
				
				$registros_pagos=0;
				$suma_subtotal=0;
				$suma_iva=0;
				$suma_total=0;
				$list_adeudo=(isset($list_ctotal))?$list_ctotal:0;
				$list_adeudo_iva=(isset($iva))?$iva:0;
				$list_adeudo_total=(isset($total))?$total:0;
				while($rowP = mysql_fetch_assoc($stid)) {
					$registros_pagos++;
					$suma_subtotal+= $rowP['subtotal'];
					$suma_iva+= $rowP['iva'];
					$suma_total+= $rowP['total'];
					$list_adeudo-=$rowP['subtotal'];
					$list_adeudo_iva-=$rowP['iva'];
					$list_adeudo_total-=$rowP['total'];
				?>
				<tr>
					<td valign="top" align="left" class="vdato"><?php echo $rowP['num_pago'] ?></td>
					<td valign="top" align="left" class="vdato"><?php echo $rowP['fecha'] ?></td>
					<td valign="top" align="left" class="vdato"><?php echo $rowP['tipo'] ?></td>
					<td valign="top" align="right" class="vdato"><?php echo $rowP['subtotal'] ?></td>
					<td valign="top" align="right" class="vdato"><?php echo $rowP['iva'] ?></td>
					<td valign="top" align="right" class="vdato"><?php echo $rowP['total'] ?></td>
					<td valign="top" align="right" class="vdato"><?php echo number_format($list_adeudo_total,2); ?></td>
				</tr>
				<?php
				}

				if($registros_pagos ==0) {
					echo '
				<tr>
					<td valign="top" align="left" colspan="6"><span class="vdato">No hay pagos registrados.</span></td>
				</tr>';
				}else {
				?>
				<tr>
					<td valign="top" align="right" class="vdato" colspan="3">TOTAL</td>
					<td valign="top" align="right" class="vdato"><?php echo number_format($suma_subtotal,2); ?></td>
					<td valign="top" align="right" class="vdato"><?php echo number_format($suma_iva,2); ?></td>
					<td valign="top" align="right" class="vdato"><?php echo number_format($suma_total,2); ?></td>
				</tr>
				<tr>
					<td valign="top" align="right" class="num_rojos" colspan="3">SALDO X PAGAR</td>
					<td valign="top" align="right" class="vdato"><?php echo number_format($list_adeudo,2); ?></td>
					<td valign="top" align="right" class="vdato"><?php echo number_format($list_adeudo_iva,2); ?></td>
					<td valign="top" align="right" class="vdato"><?php echo number_format($list_adeudo_total,2); ?></td>
				</tr>
				<?php
				}//else
				$notas = '<input type="button" class="ch_eve_notas" value="VER NOTAS" />';
				
				$cancelar = (isset($row['estatus']) && $row['estatus']=='VENDIDO')?'<input type="button" onclick="cambiarest(this);" tipo="cancel" identif="'.$id.'" value="CANCELAR EVENTO" lab="cancelado" />':'';
				$pagar = (isset($row['pagado']) && $row['pagado']==0 && ($row['estatus']=='VENDIDO' or $row['estatus']=='TERMINADO'))?'<input type="button" href="pagos.php?task=add&id_eve='.$id.'" onclick=";window.location.href=this.getAttribute(\'href\');return false;" value="PAGAR" />':'';				

				$editar_tipo = (isset($row['estatus']) && $row['estatus']=='COTIZADO')?'&editar_tipo=cotizaciones':((isset($row['estatus']) && $row['estatus']=='VENDIDO')?'&editar_tipo=eventos':'');
				$editar = (isset($row['estatus']) && ($row['estatus']=='COTIZADO' || $row['estatus']=='VENDIDO'))?'<input type="button" onclick=";window.location.href=this.getAttribute(\'href\');return false;" href="eventos.php?task=edit'.$editar_tipo.'&id='.$id.'" value="EDITAR" lab="cancelado" />':'';
				
				$vender = (isset($row['estatus']) && $row['estatus']=='COTIZADO')?'<input type="button" onclick="cambiarest(this);" tipo="sell" identif="'.$id.'" value="VENDER" lab="vendido" />':'';
				$rechazar = (isset($row['estatus']) && $row['estatus']=='COTIZADO')?'<input type="button" onclick="cambiarest(this);" tipo="reject" identif="'.$id.'" value="RECHAZAR" lab="rechazado" />':'';				
				$pdf = '<input type="button" value="PDF" tipo="cotizacion" identif="'.$id.'" onclick="exportar_pdf(this);" />';
				$pdf_st = '<input type="button" value="PDF ST" tipo="cotizacionst" identif="'.$id.'" onclick="exportar_pdf(this);" />';

				$pdf_email = '<input type="button" value="ENVIAR" tipo="cotizaciontemp" identif="'.$id.'" onclick="outlock(this);" />';

				$mostrar_factura = ($row['estatus']=='COTIZADO' or $row['estatus']=='RECHAZADO')? false: true;
				$boton_facturar = ($reqfactura=='NO' or !$mostrar_factura)? '':'<input type="button" value="FACTURA" identif="'.$id.'" onclick=";window.location.href=this.getAttribute(\'href\');return false;" href="facturas.php?task=add&id_eve='.$id.'" />';
				$id_evento = $id;
				?>
			</table>
			<hr class="bleed-flush compact" />

			<h4>Información de la Venta</h4>
			<table width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<td width="30%" valign="top" align="left">
						FECHA: <span class="vdato"><?php echo $row['falta'] ?></span>
					</td>
					<td width="70%" valign="top" align="left">
						EMPLEADO: <span class="vdato"><?php echo $row['vendedor'] ?></span>
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<p align="center"><?php echo $notas.$vender.$cancelar.$pagar.$rechazar.$editar.$pdf.$pdf_st.$pdf_email.$boton_facturar ?></p>
					</td>
				</tr>
			</table>
			<hr class="bleed-flush compact" />

		</form>
		</div>
	</div><!--class="boxed-group"-->

  	<div class="boxed-group" id="NotasEvento" style="display:none">
		<h3>NOTAS DEL EVENTO</h3>
		<div class="boxed-group-inner clearfix">
			<div class="vie_notas">
				<table width="100%" cellpadding="5">
					<tr>
						<td align="center">
							<form id="formulario_add_nota">
								<textarea name="Faddnotas" id="Faddnotas" cols="40" rows="5" style="width:450px" onkeypress="return vAbierta(event, this);"></textarea>
								<div style="text-align:center">
									<input type="button" value="AGREGAR NOTA" onclick="add_notaajax(<?php echo $id_eve ?>);" />
									<input type="button" class="ch_eve_notas" value="OCULTAR NOTAS" />
								</div>
							</form>
						</td>
					</tr>
				</table>
				<div id="notices_notas" style="display:none;" class="aviso"></div>
			</div>
			<div id="vie_cont_notas">
				<?php
				$sql = "select id_eve_notes id, notas, falta
					, date_format(falta, '%d/%m/%Y')fecha
					, date_format(falta, '%T')hora
					, concat(emp.nombre, ' ', emp.ape_pat, ' ', emp.ape_mat)empleado
					from eventos_notas
					left join empleados emp using(id_emp)
					where id_eve=".$id_eve."
					order by falta desc";

				$stid = mysql_query($sql);
				$registros = 0;
				while($rowN = mysql_fetch_assoc($stid)) {	
					$registros++;
					$mensaje = '';

					$id = $rowN['id'];				
					$notas = $rowN['notas'];
					$fecha = $rowN['fecha'];
					$hora = $rowN['hora'];
					$empleado = $rowN['empleado'];

					$mensaje .= '<table width="100%"><tr><td align="left" class="empleado">'.$empleado.'</td><td align="right">'.$fecha.' '.$hora.'</td></tr>
					<tr><td align="left" class="nota" colspan="2">'.$notas.'</td></tr></table>';
					$mensaje .= '<hr />';
					
					?>
					<div class="vie_notas">
						<?php echo $mensaje; ?>
					</div>
					<?php
				}//while
				if($registros==0) {
					echo '
					<div class="vie_notas">NO SE HAN REGISTRADO NOTAS PARA ESTE EVENTO.</div>
					';
				}
				?>

			</div>
			
		</div>
  	</div><!--class="boxed-group"-->
</div><!--class="settings-content"-->
<script>
<!--
    $(':input[type=text], input[type=password]').addClass("text ui-widget-content ui-corner-all");
    $("input:submit, input:button, input:reset").button();

    function outlock(objeto) {
		// exportar_pdf(objeto);
		$.ajax({
			type: "GET",
			url: "pdf_cotizacion.php?id=<?php echo $id_evento; ?>&tipo=cotizaciontemp",
			success: function(msg){ 
    			window.location.href='mailto:<?php echo $email; ?>?subject=Cotización VIE&body=COMENTARIOS DESCARGAR PDF HTTP://<?php echo $_SERVER['SERVER_NAME']; ?>/viesys/temp/vie_cotizacion_<?php echo $id_evento; ?>.pdf';
			}
		});
		


    }
//-->
</script>
<?php

	mysql_close($conn);

?>