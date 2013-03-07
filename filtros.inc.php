<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session
	require_once('token.inc.php');
	require_once('includes/getvalues.php');
	require_once('includes/paginacion.php');
	require_once('includes/session_principal.php');	
	require_once('includes/conection.php');
	
	$addsql = "";
			
	switch($mod) {
		case 'pagos':
			
			$addsql = "";
			if($filtro!='')
				$addsql = " AND pa.id_eve = '".(int)$filtro."' ";

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
			WHERE 1=1 AND pa.id_est in(1,3) ";

			$sql .= $addsql;
			$sql .= " order by pa.id_eve, pa.fec_pag, pa.num_pago";

			$stid = mysql_query($sql);
			?>
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
				$rem = '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="pago" value="-" />';
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
		<?php
		break;
		case 'cotizaciones':
			$gregar_filtro = (isset($Fmes) && $Fmes!='')? " AND date_format(fecha, '%m') = '".$Fmes."' ":"";
			$gregar_filtro .= (isset($Fano) && $Fano!='')? " AND date_format(fecha, '%Y') = '".(int)$Fano."' ":"";

			$gregar_filtro .= (isset($Fcliente) && $Fcliente!='')? " AND ev.id_cli = ".(int)$Fcliente." ":"";
			$gregar_filtro .= (isset($Fempleado) && $Fempleado!='')? " AND ev.id_emp = ".(int)$Fempleado." ":"";
			$gregar_filtro .= (isset($Fsalon) && $Fsalon!='')? " AND ev.id_sal = ".(int)$Fsalon." ":"";
			$gregar_filtro .= (isset($Fservicio) && $Fservicio!='')? " AND serev.id_ser = ".(int)$Fservicio." ":"";
			$gregar_filtro .= (isset($Ftipo) && $Ftipo!='')? " AND tev.id_tip_eve = ".(int)$Ftipo." ":"";
			$gregar_filtro .= (isset($Festatus) && $Festatus!='')? " AND ev.estatus in ('".$Festatus."') ":"";
			//$gregar_filtro .= (isset($Festatus) && $Festatus=='COTIZADO')? " AND ev.estatus = 'COTIZADO' ":(($Festatus=='RECHAZADO')? " AND ev.estatus = 'RECHAZADO' ":"");
			
			$gregar_filtro .= (isset($Fvigencia) && $Fvigencia=='ACTIVA')? " AND CURDATE() <= ev.fecha ":"";
			$gregar_filtro .= (isset($Fvigencia) && $Fvigencia=='VENCIDA')? " AND CURDATE() > ev.fecha ":"";
			$addsql .= "
			AND (
				UPPER(tev.nombre) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(cli.nombre) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(cli.ape_mat) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(cli.ape_pat) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(sal.nombre) LIKE(UPPER('%".$filtro."%')) OR
				ev.id_eve = '".(int)$filtro."'
				)
			";

			$sql = "select distinct ev.id_eve			
			, ev.id_tip_eve
			, date_format(ev.fecha, '%d/%m/%Y')fecha
			, case 
			when CURDATE() <= ev.fecha then 'ACTIVA'
			else 'VENCIDA'
			end estatus
			, ev.hora
			, ev.num_personas personas
			, ev.pagado
			, ev.cos_tot
			, sal.nombre salon
			, concat(cli.nombre, ' ', cli.ape_pat) cliente
			, tev.nombre tipodeevento
			, ev.estatus estatus2
			, GROUP_CONCAT(serv.nombre SEPARATOR '<br>')servicio
			from eventos ev
			inner join salones sal using(id_sal)
			inner join clientes cli using(id_cli)
			inner join tipo_evento tev using(id_tip_eve)
			left join servicios_eventos serev using(id_eve)
			inner join servicios serv using(id_ser)
			where 1=1";// and ev.estatus in('COTIZADO', 'RECHAZADO') ";
			$sql .= $addsql . $gregar_filtro;			
			$sql .= " group by id_eve ORDER BY ev.fecha";

			$stid = mysql_query($sql);
			?>
			<table width="100%" cellspacing="0" cellpadding="3">
				<tr class="Cabezadefila">
					<th width="5%">No.</th>
					<th width="10%">Fecha</th>
					<th width="5%">Hora</th>
					<th width="15%">Tipo</th>
					<th width="15%">Salón</th>
					<th width="15%">Cliente</th>
					<th width="25%">Servicios</th>
					<th width="10%">Estatus</th>
				</tr>

			<?php
			$registros = 0;
			while($row = mysql_fetch_assoc($stid)) {	
				$registros++;

				$id = $row['id_eve'];
				$noevento = str_pad($id, 4, '0', STR_PAD_LEFT );
				$tipodeevento = $row['tipodeevento'];
				$salon = $row['salon'];
				$personas = (isset($row['personas']))? number_format($row['personas']) : '';
				$pagado = (isset($row['pagado']) && $row['pagado']==0)? 'NO' : 'SI';
				$fecha = $row['fecha'];
				$hora = $row['hora'];
				$cliente = $row['cliente'];
				$servicio = $row['servicio'];
				
				# estatus : 
				# - vigente : fecha aun no ha pasado
				# - vencida : fecha ya paso
				$estatus = $row['estatus'].'-'.$row['estatus2'];
																
				$ver_evento = '<a class="fiframe" href="eventos_view.php?task=view&id_eve='.$id.'" title="Ver detalles" alt="Ver detalles">'.$noevento.'</a>';
				$fecha = '<a class="fiframe" href="eventos_view.php?task=view&id_eve='.$id.'" title="Ver detalles" alt="Ver detalles">'.$fecha.'</a>';
				$hora = '<a class="fiframe" href="eventos_view.php?task=view&id_eve='.$id.'" title="Ver detalles" alt="Ver detalles">'.$hora.'</a>';
				$pagar = (isset($row['estatus2']) && $row['estatus2']=='COTIZADO')?'<input type="button" onclick="cambiarest(this);" tipo="sell" identif="'.$id.'" value="Vender" lab="vendido" />':'';
				$rechazar = (isset($row['estatus2']) && $row['estatus2']=='COTIZADO')?'<input type="button" onclick="cambiarest(this);" tipo="reject" identif="'.$id.'" value="Rechazar" lab="rechazado" />':'';
				$notas = '<a class="fiframe" href="notas.php?id_eve='.$id.'">ver</a>';
				$pdf = '<img src="images/pdf_icono.png" border="0" width="36" height="15" tipo="cotizacion" identif="'.$id.'" onclick="exportar_pdf(this);" style="cursor:pointer;" />';
				$editar = (isset($row['estatus2']) && ($row['estatus2']=='VENDIDO' || $row['estatus2']=='COTIZADO'))?'<a href="eventos.php?task=edit&id='.$id.'" title="Editar" alt="Editar" class="rppermiso"><img src="images/Edit-icon-16.png" border="0"></a>':'';
			?>
				<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
					<td align="center" class="celdaNormal"><?php echo $ver_evento; ?></td>
					<td align="center" class="celdaNormal"><?php echo $fecha; ?></td>
					<td align="center" class="celdaNormal"><?php echo $hora; ?></td>
					<td align="left" class="celdaNormal"><?php echo $tipodeevento; ?></td>
					<td align="left" class="celdaNormal"><?php echo $salon; ?></td>
					<td align="left" class="celdaNormal"><?php echo $cliente; ?></td>
					<td align="left" class="celdaNormal"><?php echo $servicio; ?></td>
					<td align="center" class="celdaNormal"><?php echo $estatus; ?></td>
					<!-- <td align="center" class="celdaNormal"><?php echo $editar; ?></td> -->
					<!-- <td align="center" class="celdaNormal"><?php echo $rechazar; ?></td> -->
				</tr>
			<?php
			}			
			?>
				<tr class="Cabezadefila">
					<td colspan='9'><?php echo $registros; ?> Registros encontrados.</td>
				</tr>
			</table>
		<?php
		break;
		case 'servicios':
			$addsql = "
			AND (
				UPPER(ser.nombre) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(tser.nombre) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(ser.email) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(ser.dir) LIKE(UPPER('%".$filtro."%'))
				
				)";

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
			WHERE 1=1 AND ser.id_est in(1,3)";
			$sql .= $addsql;			
			$sql .= " order by tipo, ser.nombre";

			$stid = mysql_query($sql);
			?>
			<table width="100%" cellspacing="0" cellpadding="3">
				<tr class="Cabezadefila">
					<th width="5%">#</th>
					<th width="40%">Servicio</th>
					<th width="30%">Tipo</th>
					<th width="5%">Acción</th>
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
					<td align="left" class="celdaNormal"><?php echo $tipo; ?></td>
					<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
					<td align="center" class="celdaNormal"><a href="cotizaciones.php?id_ser=<?php echo $id; ?>">VER</a></td>
					<td align="center" class="celdaNormal"><a href="eventos.php?id_ser=<?php echo $id; ?>">VER</a></td>
					<td align="center" class="celdaNormal"><a href="servicios.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar" class="rppermiso"><img src="images/Edit-icon-16.png" border="0"></a></td>
					<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
				</tr>
			<?php
			}			
			?>			

				<tr class="Cabezadefila">
					<td colspan='12'><?php echo $registros; ?> Registros encontrados.</td>
				</tr>
			</table>
			</table>
		<?php
		break;
		case 'facturas':
			$addsql = "
			AND (
				UPPER(cli.empresa) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(cli.nombre) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(cli.ape_mat) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(cli.ape_pat) LIKE(UPPER('%".$filtro."%')) OR
				fac.num_fac = ".$filtro."
				)";

			if(trim($filtro)==''){
				$addsql = "";
			}

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
			WHERE 1=1 AND fac.id_est in(1,3)";
			$sql .= $addsql;			
			$sql .= " order by num_fac, cliente";

			$stid = mysql_query($sql);
			?>
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
					<td align="center" class="celdaNormal"><a href="facturas.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar" class="rppermiso"><img src="images/Edit-icon-16.png" border="0"></a></td>
					<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
				</tr>
			<?php
			}			
			?>			

				<tr class="Cabezadefila">
					<td colspan='10'><?php echo $registros; ?> Registros encontrados.</td>
				</tr>
			</table>
		<?php
		break;
		case 'eventos':
			$gregar_filtro = (isset($Fmes) && $Fmes!='')? " AND date_format(fecha, '%m') = '".$Fmes."' ":"";
			$gregar_filtro .= (isset($Fano) && $Fano!='')? " AND date_format(fecha, '%Y') = '".(int)$Fano."' ":"";
			$gregar_filtro .= (isset($Fcliente) && $Fcliente!='')? " AND ev.id_cli = ".(int)$Fcliente." ":"";
			$gregar_filtro .= (isset($Fempleado) && $Fempleado!='')? " AND ev.id_emp = ".(int)$Fempleado." ":"";
			$gregar_filtro .= (isset($Fsalon) && $Fsalon!='')? " AND ev.id_sal = ".(int)$Fsalon." ":"";
			$gregar_filtro .= (isset($Fservicio) && $Fservicio!='')? " AND serev.id_ser = ".(int)$Fservicio." ":"";
			$gregar_filtro .= (isset($Ftipo) && $Ftipo!='')? " AND tev.id_tip_eve = ".(int)$Ftipo." ":"";
			$gregar_filtro .= (isset($Festatus) && $Festatus=='CANCELADO')? " AND ev.estatus = 'CANCELADO' ":"";
			$gregar_filtro .= (isset($Festatus) && $Festatus=='VENDIDO')? " AND ev.estatus = 'VENDIDO' ":"";
			$gregar_filtro .= (isset($Festatus) && $Festatus=='TERMINADO')? " AND ev.estatus = 'TERMINADO' ":"";
			$addsql = "
			AND (
				UPPER(tev.nombre) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(cli.nombre) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(cli.ape_mat) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(cli.ape_pat) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(sal.nombre) LIKE(UPPER('%".$filtro."%'))  OR
				ev.id_eve = '".(int)$filtro."'
				)";

				$sql = "select distinct ev.id_eve			
				, ev.id_tip_eve
				, date_format(ev.fecha, '%d/%m/%Y')fecha
				, ev.hora
				, ev.num_personas personas
				, ev.pagado
				, ev.estatus
				, ev.cos_tot
				, sal.nombre salon
				, concat(cli.nombre, ' ', cli.ape_pat) cliente
				, tev.nombre tipodeevento
				, GROUP_CONCAT(serv.nombre SEPARATOR '<br>')servicio
				, case 
				when CURDATE() <= ev.fecha then 'VIGENTE'
				else 'VENCIDA'
				end vigencia
				from eventos ev
				inner join salones sal using(id_sal)
				inner join clientes cli using(id_cli)
				inner join tipo_evento tev using(id_tip_eve)
				inner join servicios_eventos serev using(id_eve)
				inner join servicios serv using(id_ser)
				where 1=1 AND ev.estatus in('CANCELADO','VENDIDO','TERMINADO')";
				$sql .= $addsql.$gregar_filtro;
				$sql .= " group by id_eve order by ev.estatus asc, ev.fecha";

				$stid = mysql_query($sql);
				?>
				<table width="100%" cellspacing="0" cellpadding="3">
					<tr class="Cabezadefila">
						<th width="5%">No.</th>
						<th width="10%">Fecha</th>
						<th width="5%">Hora</th>
						<th width="15%">Tipo</th>
						<th width="15%">Salón</th>
						<th width="15%">Cliente</th>
						<th width="5%">Pagado</th>
						<th width="20%">Servicios</th>
						<th width="10%">Estatus</th>
						<!-- <th width="5%">Edit</th> -->
						<!-- <th width="5%">Elim</th> -->
					</tr>

				<?php
				$registros = 0;
				while($row = mysql_fetch_assoc($stid)) {	
					$registros++;

					$id = $row['id_eve'];
					$noevento = str_pad($id, 4, '0', STR_PAD_LEFT );
					$tipodeevento = $row['tipodeevento'];
					$salon = $row['salon'];
					$personas = (isset($row['personas']))? number_format($row['personas']) : '';
					$pagado = (isset($row['pagado']) && $row['pagado']==0)? 'NO' : 'SI';
					$fecha = $row['fecha'];
					$hora = $row['hora'];
					$cliente = $row['cliente'];
					$estatus = $row['estatus'];
					$servicio = $row['servicio'];
																	
					$ver_evento = '<a class="fiframe" href="eventos_view.php?task=view&id_eve='.$id.'" title="Ver detalles" alt="Ver detalles">'.$noevento.'</a>';
					$fecha = '<a class="fiframe" href="eventos_view.php?task=view&id_eve='.$id.'" title="Ver detalles" alt="Ver detalles">'.$fecha.'</a>';
					$hora = '<a class="fiframe" href="eventos_view.php?task=view&id_eve='.$id.'" title="Ver detalles" alt="Ver detalles">'.$hora.'</a>';
					$notas = '<a class="fiframe" href="notas.php?id_eve='.$id.'">ver</a>';
					$cancelar = (isset($row['estatus']) && $row['estatus']=='VENDIDO')?'<input type="button" onclick="cambiarest(this);" tipo="cancel" identif="'.$id.'" value="Cancelar" lab="cancelado" />':'';
					$pagar = (isset($row['pagado']) && $row['pagado']==0 && $row['estatus']=='VENDIDO')?'<input type="button" href="pagos.php?task=add&id_eve='.$id.'" onclick=";window.location.href=this.getAttribute(\'href\');return false;" value="Pagar" />':'';
					$editar = (isset($row['estatus']) && ($row['estatus']=='VENDIDO' || $row['estatus']=='COTIZADO'))?'<a href="eventos.php?task=edit&id='.$id.'" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a>':'';
					# Proceso para cambiar estatus a terminado
					if($row['vigencia'] == 'VENCIDA' && $estatus == 'VENDIDO') {
						$sqlTer = "UPDATE eventos SET estatus='TERMINADO' WHERE id_eve= ".$id."";
						$stidTer = mysql_query($sqlTer);
					}
				?>
					<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
						<td align="center" class="celdaNormal"><?php echo $ver_evento; ?></td>
						<td align="center" class="celdaNormal"><?php echo $fecha; ?></td>
						<td align="center" class="celdaNormal"><?php echo $hora; ?></td>
						<td align="left" class="celdaNormal"><?php echo $tipodeevento; ?></td>
						<td align="left" class="celdaNormal"><?php echo strtoupper($salon); ?></td>
						<td align="center" class="celdaNormal"><?php echo $cliente; ?></td>
						<td align="center" class="celdaNormal"><?php echo $pagado; ?></td>
						<td align="left" class="celdaNormal"><?php echo $servicio; ?></td>
						<td align="center" class="celdaNormal"><?php echo $estatus; ?></td>
						<!-- <td align="center" class="celdaNormal"><?php echo $editar; ?></td> -->
						<!-- <td align="center" class="celdaNormal"><?php echo $cancelar; ?></td> -->
					</tr>
				<?php
				}			
				?>			

					<tr class="Cabezadefila">
						<td colspan='10'><?php echo $registros; ?> Registros encontrados.</td>
					</tr>
				</table>
		<?php
		break;
		case 'clientes':
			$addsql = (isset($Fccev) and $Fccev==1)? ' AND (SELECT count(*) FROM eventos WHERE id_cli=clientes.id_cli) > 0 ':''; 
			$addsql .= "
			AND (
				UPPER(clientes.nombre) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(clientes.ape_pat) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(clientes.ape_mat) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(clientes.empresa) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(clientes.email) LIKE(UPPER('%".$filtro."%'))
				)";

			$sql = "SELECT
			clientes.id_cli,
			clientes.empresa,
			clientes.nombre,
			clientes.ape_pat,
			clientes.ape_mat,
			concat(clientes.nombre, ' ', clientes.ape_pat, ' ', clientes.ape_mat)cliente,
			clientes.tel,
			clientes.tel2,
			clientes.email,
			clientes.dir,
			clientes.id_est estatus
			, (SELECT count(*) FROM eventos WHERE id_cli=clientes.id_cli)neventos
			FROM clientes
			INNER JOIN estatus USING(id_est)
			WHERE 1=1 AND clientes.id_est in(1,3)
			";
			$sql .= $addsql;			
			$sql .= " ORDER BY cliente";

			$stid = mysql_query($sql);
			?>
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
					<td align="center" class="celdaNormal"><a href="clientes.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar" class="rppermiso"><img src="images/Edit-icon-16.png" border="0"></a></td>
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
		<?php
		break;
		case 'grupos':
			$addsql = " AND UPPER(g.nombre) LIKE(UPPER('%".$filtro."%'))";

			$sql = "SELECT g.id_gru, g.nombre grupo, g.descripcion, g.id_est estatus, estatus.nombre,
			(select count(*) from empleados emp where emp.id_gru=g.id_gru)usuarios,
			(select count(*) from permisos per where per.id_gru=g.id_gru)modulos
			FROM grupo g
			INNER JOIN estatus USING(id_est)
			WHERE 1=1 AND g.id_est in(1,3) ";
			$sql .= $addsql;			
			$sql .= " ORDER BY grupo";

			$stid = mysql_query($sql);
			?>
			<table width="100%" cellspacing="0" cellpadding="3">
				<tr class="Cabezadefila">
					<th width="3%">#</th>
					<th width="37%">Grupo</th>
					<th width="20%">Usuarios</th>
					<th width="20%">Modulos</th>
					<th width="5%">Estatus</th>					
					<th width="5%">Edit</th>
					<th width="5%">Elim</th>
				</tr>

			<?php
			$registros = 0;
			while($row = mysql_fetch_assoc($stid)) {	
				$registros++;

				$id = $row['id_gru'];				
				$nombre = $row['grupo'];
				$usuarios = $row['usuarios'];
				$modulos = $row['modulos'];
				
				$accion = ( $row['estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="grupo" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="grupo" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';
																
				$rem = ($id != $_SESSION[TOKEN.'GRUPO_ID'])? '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="empleado" value="-" />' : '';

				if($id == $_SESSION[TOKEN.'GRUPO_ID'])
					$accion = '';				
			?>
				<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
					<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
					<td align="left" class="celdaNormal"><a href="grupos.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo strtoupper($nombre); ?></a></td>
					<td align="center" class="celdaNormal"><?php echo $usuarios; ?></td>
					<td align="center" class="celdaNormal"><?php echo $modulos; ?></td>
					<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
					<td align="center" class="celdaNormal"><a href="grupos.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar" class="rppermiso"><img src="images/Edit-icon-16.png" border="0"></a></td>
					<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
				</tr>
			<?php
			}			
			?>			

				<tr class="Cabezadefila">
					<td colspan='10'><?php echo $registros; ?> Registros encontrados.</td>
				</tr>
			</table>
		<?php
		break;
		case 'empleados':
			$addsql = "
			AND ( 
				UPPER(empleados.nombre) LIKE(UPPER('%".$filtro."%'))
				OR UPPER(empleados.ape_pat) LIKE(UPPER('%".$filtro."%'))
				OR UPPER(empleados.ape_mat) LIKE(UPPER('%".$filtro."%')) 
			)";

			$sql = "SELECT
			empleados.id_emp usuario_id,
			concat(empleados.nombre,' ',empleados.ape_pat,' ',empleados.ape_mat) nombres,
			empleados.tel,
			empleados.email usuario,
			empleados.dir,
			empleados.psw password,
			empleados.id_est u_estatus,
			estatus.id_est, estatus.nombre,
			grupo.id_gru grupo_id,
			grupo.nombre grupo,
			grupo.id_est g_estatus
			FROM empleados
			INNER JOIN estatus USING(id_est)
			INNER JOIN grupo USING(id_gru)
			WHERE 1=1 AND empleados.id_est in(1,3) ";
			$sql .= $addsql;			
			$sql .= " ORDER BY nombres";
		
			$stid = mysql_query($sql);
			?>
			<table width="100%" cellspacing="0" cellpadding="3">
				<tr class="Cabezadefila">
					<th width="3%">#</th>
					<th width="35%">Empleado</th>
					<th width="5%">Estatus</th>
					<th width="17%">Usuario</th>					
					<th width="25%">Grupo</th>
					<!-- <th width="5%">Edit</th> -->
					<th width="5%">Elim</th>
				</tr>

			<?php
			$registros = 0;
			while($row = mysql_fetch_assoc($stid)) {	
				$registros++;

				$id = $row['usuario_id'];				
				$nombre = $row['nombres'];
				$usuario = $row['usuario'];
				$grupoid = $row['grupo_id'];
				$grupo = $row['grupo'];
				
				$accion = ( $row['u_estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="empleado" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="empleado" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';
																
				$rem = ($id != $_SESSION[TOKEN.'USUARIO_ID'])? '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="empleado" value="-" />' : '';

				if($id == $_SESSION[TOKEN.'USUARIO_ID'])
					$accion = '';				
			?>
				<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
					<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
					<td align="left" class="celdaNormal"><a href="empleados.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo strtoupper($nombre); ?></a></td>
					<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
					<td align="left" class="celdaNormal"><a href="empleados.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $usuario; ?></a></td>					
					<td align="left" class="celdaNormal"><a href="grupos.php?task=edit&id=<?php echo $grupoid; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $grupo; ?></a></td>
					<!-- <td align="center" class="celdaNormal"><a href="empleados.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar" class="rppermiso"><img src="images/Edit-icon-16.png" border="0"></a></td> -->
					<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
				</tr>
			<?php
			}			
			?>			

				<tr class="Cabezadefila">
					<td colspan='10'><?php echo $registros; ?> Registros encontrados.</td>
				</tr>
			</table>
		<?php
		break;
		case 'salones':
			$addsql = "
			AND ( 
				UPPER(empresa) LIKE(UPPER('%".$filtro."%'))
				OR UPPER(nombre) LIKE(UPPER('%".$filtro."%'))
				OR UPPER(responsable) LIKE(UPPER('%".$filtro."%')) 
			)";

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
			WHERE 1=1 AND id_est in(1,3) ";
			$sql .= $addsql;			
			$sql .= " ORDER BY nombre";
		
			$stid = mysql_query($sql);
			?>
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
					<td align="left" class="celdaNormal"><a href="salones.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $salon; ?></a></td>
					<td align="left" class="celdaNormal"><a href="salones.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $responsable; ?></a></td>
					<td align="left" class="celdaNormal"><a href="salones.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo $tel; ?></a></td>
					<td align="center" class="celdaNormal"><?php echo $ver_cotizaciones; ?></td>
					<td align="center" class="celdaNormal"><?php echo $ver_eventos; ?></td>
					<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
					<td align="center" class="celdaNormal"><a href="salones.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar" class="rppermiso"><img src="images/Edit-icon-16.png" border="0"></a></td>
					<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
				</tr>
			<?php
			}			
			?>			

				<tr class="Cabezadefila">
					<td colspan='9'><?php echo $registros; ?> Registros encontrados.</td>
				</tr>
			</table>
			</table>
		<?php
		break;
		case 'log':
			$gregar_filtro = (isset($Fcliente) && $Fcliente!='')? " AND ev.id_cli = ".(int)$Fcliente." ":"";
			$gregar_filtro .= (isset($Fempleado) && $Fempleado!='')? " AND ev.id_emp = ".(int)$Fempleado." ":"";
			$gregar_filtro .= (isset($Fsalon) && $Fsalon!='')? " AND ev.id_sal = ".(int)$Fsalon." ":"";
			$gregar_filtro .= (isset($Fservicio) && $Fservicio!='')? " AND serev.id_ser = ".(int)$Fservicio." ":"";
			$gregar_filtro .= (isset($Ftipo) && $Ftipo!='')? " AND tev.id_tip_eve = ".(int)$Ftipo." ":"";
			$gregar_filtro .= (isset($Festatus) && $Festatus=='CANCELADO')? " AND ev.estatus = 'CANCELADO' ":"";
			$gregar_filtro .= (isset($Festatus) && $Festatus=='VENDIDO')? " AND ev.estatus = 'VENDIDO' ":"";
			$gregar_filtro .= (isset($Festatus) && $Festatus=='TERMINADO')? " AND ev.estatus = 'TERMINADO' ":"";
			$addsql = "
			AND (
				UPPER(emp.nombre) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(emp.ape_pat) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(emp.ape_mat) LIKE(UPPER('%".$filtro."%')) OR
				UPPER(mods.nombre) LIKE(UPPER('%".$filtro."%'))
				)";

				$sqlpag = $sql = "select l.ip, l.host, l.browser, l.id_mod, l.id_emp
				, date_format(l.date, '%d/%m/%Y')fecha
				, date_format(l.date, '%T')hora
				, l.date fechacom
				, concat(emp.nombre,' ',emp.ape_pat,' ',emp.ape_mat) empleado
				, mods.nombre modulo
				from log l
				inner join empleados emp using(id_emp)
				inner join modulo mods using(id_mod)
				WHERE 1=1 ";
				$sql .= $addsql;
				$sql .= " order by fechacom desc";
				$sql .= " LIMIT {$vie_paginicial}, {$vie_max_regxpag}";

				$stid = mysql_query($sql);
				?>
				<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="Cabezadefila">
							<th width="3%">#</th>
							<th width="7%">Fecha</th>
							<th width="10%">Hora</th>
							<th width="30%">Empleado</th>
							<th width="15%">IP</th>
							<th width="15%">HOST</th>
							<th width="20%">Modulo</th>
						</tr>

					<?php
					$registros = $vie_paginicial;
					while($row = mysql_fetch_assoc($stid)) {	
						$registros++;

						//$id = $row['id_eve'];
						$empleado = $row['empleado'];
						$ip = $row['ip'];
						$host = $row['host'];
						
						$fecha = $row['fecha'];
						$hora = $row['hora'];
						$modulo = $row['modulo'];				
					
					?>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
							<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
							<td align="center" class="celdaNormal"><?php echo $fecha; ?></td>
							<td align="center" class="celdaNormal"><?php echo $hora; ?></td>
							<td align="left" class="celdaNormal"><?php echo $empleado; ?></td>
							<td align="center" class="celdaNormal"><?php echo $ip; ?></td>					
							<td align="left" class="celdaNormal"><?php echo $host; ?></td>
							<td align="center" class="celdaNormal"><?php echo $modulo; ?></td>
						</tr>
					<?php
					}			
					?>
						<tr class="Cabezadefila">
							<td colspan='11'><?php echo $vie_max_regxpag; ?> Registros mostrados.</td>
						</tr>
					</table>
				<?php
				echo paginacion($sqlpag, $vie_max_regxpag, $pag, $url_parametros);
				?>
		<?php
		break;
		case 'tipo_evento':
			$addsql = " AND UPPER(nombre) LIKE(UPPER('%".$filtro."%'))";

			$sql = "SELECT
			id_tip_eve id, nombre tipodeevento
			from tipo_evento WHERE 1=1";
			$sql .= $addsql;			
			$sql .= " ORDER BY nombre";

			$stid = mysql_query($sql);
			?>
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
					<td align="center" class="celdaNormal"><a href="tipo_eventos.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar" class="rppermiso"><img src="images/Edit-icon-16.png" border="0"></a></td>
					<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
				</tr>
			<?php
			}			
			?>			

				<tr class="Cabezadefila">
					<td colspan='8'><?php echo $registros; ?> Registros encontrados.</td>
				</tr>
			</table>
		<?php
		break;
		default:
			echo 'Imposible filtrar registros.';

	}

	mysql_close($conn);
?>
<script>
<!--
    $(':input[type=text], input[type=password]').addClass("text ui-widget-content ui-corner-all");
    $("input:submit, input:button, input:reset").button();
//-->
</script>