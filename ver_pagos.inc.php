<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session
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
							
	$sql = "SELECT distinct ev.id_eve			
	, ev.id_tip_eve
	, date_format(ev.fecha, '%d/%m/%Y')fecha
	, ev.hora
	, ev.facturar
	, ev.num_personas personas
	, ev.pagado
	, ev.estatus
	, ev.cos_tot
	, sal.nombre salon
	, concat(cli.nombre, ' ', cli.ape_pat) cliente
	, tev.nombre tipodeevento
	, (SELECT (count(*) +1) from pagos where id_eve=".$Fnoevento.") Fnumpago
	from eventos ev
	inner join salones sal using(id_sal)
	inner join clientes cli using(id_cli)
	inner join tipo_evento tev using(id_tip_eve)
	inner join servicios_eventos serev using(id_eve)
	WHERE 1=1 AND ev.id_eve=".$Fnoevento;
	$stid = mysql_query($sql);

	if($row = mysql_fetch_assoc($stid)) {

		$cliente = $row['cliente'];
		$tipodeevento = $row['tipodeevento'];
		$fecha = $row['fecha'];
		$Fnumpago = $row['Fnumpago'];
		$cos_tot = number_format($row['cos_tot'],2);
		$facturar = (isset($row['facturar']) && $row['facturar']=='1')?'SÍ':'NO';
		$cap_subtotal = (isset($row['facturar']) && $row['facturar']=='0')?true:false;
		$cap_total = (isset($row['facturar']) && $row['facturar']=='1')?true:false;

		$estatus = $row['estatus'];
		$pagado = $row['pagado'];

		if($estatus != 'VENDIDO'){
			echo '<p>El evento debe ser vendido para poder agregar pagos.</p>';
			exit;
		}
		elseif($pagado==1){
			echo '<p>Este evento ya ha sido pagado.</p>';
			exit;
		}
		?>
	<table width="100%" cellspacing="0" cellpadding="3">
		<tr class="Cabezadefila">
			<th width="10%">FECHA</th>
			<th width="30%">TIPO</th>
			<th width="40%">CLIENTE</th>
			<th width="10%">COSTO</th>
			<th width="10%">FACTURAR</th>
		</tr>
		<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
			<td align="center" class="celdaNormal"><?php echo $fecha; ?></td>
			<td align="center" class="celdaNormal"><?php echo $tipodeevento; ?></td>
			<td align="center" class="celdaNormal"><?php echo $cliente; ?></td>
			<td align="center" class="celdaNormal">$ <?php echo $cos_tot; ?></td>
			<td align="center" class="celdaNormal"><?php echo $facturar; ?></td>
		</tr>
	</table>
	<input type="hidden" value="<?php echo $Fnumpago; ?>" name="Fnumpago" id="Fnumpago" />

	<h4>Información de Pagos</h4>
	<table width="100%" cellpadding="5" cellspacing="0">
		<tr>
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
		WHERE 1=1 and pa.id_est = 1 and pa.id_eve=".$Fnoevento." order by num_pago";
		$stid = mysql_query($sql);
		
		$registros_pagos=0;
		while($rowP = mysql_fetch_assoc($stid)) {
			$registros_pagos++;
			$tmp_subtotal = number_format($rowP['subtotal'], 2);
			$tmp_iva = number_format($rowP['iva'], 2);
			$tmp_total = number_format($rowP['total'], 2);
		?>
		<tr>
			<td valign="top" align="left" class="vdato"><?php echo $rowP['num_pago'] ?></td>
			<td valign="top" align="left" class="vdato"><?php echo $rowP['fecha'] ?></td>
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
<script type="text/javascript">
	$("#mostrarform").show();
	<?php 
	if($cap_total) { ?>
		$("#Fsubtotal").attr('readonly','readonly');
		$("#Ftotal").removeAttr('disabled');
		$("#Fiva").removeAttr('disabled');
	<?php 
	}else {
	?>
		$("#Fsubtotal").removeAttr('readonly');
		$("#Ftotal").attr('disabled','disabled');
		$("#Fiva").attr('disabled','disabled');
	<?php
	} 
	?>	
</script>
<?php
	mysql_close($conn);
?>