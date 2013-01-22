<?php
define('MODULO', 500);
/* 
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
	$("#Ffecha2").datepicker( "option", "minDate", "+0d" );

	$(".inpservicios").change(function() {
		ctotal = $("#ctotal").val();
		if(ctotal != "")
			ctotal = parseFloat(ctotal);
		else
			ctotal = 0.0;

		if($(this).attr("checked")) {
			$("#Fcosto"+$(this).val()).removeAttr("disabled");
			$("#Fcosto"+$(this).val()).focus()
		}else {
			$("#Fcosto"+$(this).val()).attr("disabled","disabled");
			ctotal = ctotal - parseFloat( $("#Fcosto"+$(this).val()).val());
			$("#ctotal").val(ctotal);
			
			$("#Fcosto"+$(this).val()).val("");
		}
	});

	$(".inpcostos").change(function() {
		ctotal = $("#ctotal").val();
		if(ctotal != "")
			ctotal = parseFloat(ctotal);
		else
			ctotal = 0.0;

		ctotal = ctotal + parseFloat( $(this).val());
		$("#ctotal").val(ctotal);
		
	});
	';

	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);
	
	require_once('includes/html_template.php');
	
	switch($task) {
		case 'list': #- - - - - - - - - - - - - - -- - - LISTAR
							
			$filtrar_por_cliente = (isset($id_cli) && $id_cli!='')? " and ev.id_cli=$id_cli " : "";
			$filtrar_por_empleado = (isset($id_emp) && $id_emp!='')? " and ev.id_emp=$id_emp " : "";
			$filtrar_por_servicio = (isset($id_ser) && $id_ser!='')? " and serev.id_ser=$id_ser " : "";
			$filtrar_por_salon = (isset($id_sal) && $id_sal!='')? " and ev.id_sal=$id_sal " : "";

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
			from eventos ev
			inner join salones sal using(id_sal)
			inner join clientes cli using(id_cli)
			inner join tipo_evento tev using(id_tip_eve)
			left join servicios_eventos serev using(id_eve)
			where 1=1 "; /*and ev.estatus in('COTIZADO')*/ 
			$sql .="AND CURDATE() <= ev.fecha
			$filtrar_por_cliente
			$filtrar_por_empleado
			$filtrar_por_servicio
			$filtrar_por_salon
			order by ev.fecha";
		
			$stid = mysql_query($sql);
			?>			

			<h3>COTIZACIONES</h3>
			<div class="boxed-group-inner clearfix">
				<form id="formulario" name="formulario">
					<div class="filtro">				
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="cotizaciones" placeholder="buscar"/><a href="javascript:more_filters();" class="sel_nuevo">Avanzado</a>
						<div align="right" class="botonsuperior">
			        		<input type="button" value="Nueva" href="eventos.php?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
						</div>
					</div>
					<div id="more_filters" style="display:none;">
						<table width="100%" cellspacing="5">
							<tr>
								<td align="right"><label for="Fcliente">Cliente: </label></td>
								<td align="left">
									<select class="" name="Fcliente" id="Fcliente" req="" lab="Cliente" >
										<option value="">seleccione</option>
										<?php
											#Revisamos si estan enviando la cotizacion desde la pagina de cliente
											$id_cli = (isset($id_cli) && $id_cli != '')? $id_cli :'';

											$sql="select id_cli, concat(nombre, ' ', ape_pat, ' ', ape_mat)nombre from clientes where id_est in(1) order by nombre";
											
											$stidCli = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stidCli))) {
												if( $id_cli == $rowPer['id_cli']) {
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
								<td align="right"><label for="Fsalon">Salón: </label></td>
								<td align="left">
									<select class="" name="Fsalon" id="Fsalon" req="req" lab="Salón"  >
										<option value="">seleccione</option>
										<?php
											#Revisamos si estan enviando la cotizacion desde la pagina de salones
											$id_sal = (isset($id_sal) && $id_sal != '')? $id_sal :'';
											$sql="select id_sal, nombre from salones where id_est in(1) order by nombre";
											
											$stidSal = mysql_query($sql);

											while (($rowSal = mysql_fetch_assoc($stidSal))) {
												if( $rowSal['id_sal'] == $id_sal) {
													echo '	<option selected="selected" value="'.$rowSal['id_sal'].'">'.$rowSal['nombre'].'</option>';
												}else{	
													echo '	<option value="'.$rowSal['id_sal'].'">'.$rowSal['nombre'].'</option>';
												}
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Fempleado">Empleado: </label></td>
								<td align="left">
									<select class="" name="Fempleado" id="Fempleado" req="" lab="Empleado" >
										<option value="">seleccione</option>
										<?php
											#Revisamos si estan enviando la cotizacion desde la pagina de cliente
											$id_emp = (isset($id_emp) && $id_emp != '')? $id_emp :'';

											$sql="select id_emp, concat(nombre, ' ', ape_pat, ' ', ape_mat)nombre from empleados where id_est in(1) order by nombre";
											
											$stidEmp = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stidEmp))) {
												if( $id_emp == $rowPer['id_emp']) {
													echo '	<option selected="selected" value="'.$rowPer['id_emp'].'">'.$rowPer['nombre'].'</option>';
												}else{	
													echo '	<option value="'.$rowPer['id_emp'].'">'.$rowPer['nombre'].'</option>';
												}
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Fservicio">Servicio: </label></td>
								<td align="left">
									<select class="" name="Fservicio" id="Fservicio" req="" lab="Servicio" >
										<option value="">seleccione</option>
										<?php
											#Revisamos si estan enviando la cotizacion desde la pagina de cliente
											$id_ser = (isset($id_ser) && $id_ser != '')? $id_ser :'';

											$sql="select id_ser, nombre from servicios where id_est in(1) order by nombre";
											
											$stidSer = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stidSer))) {
												if( $id_ser == $rowPer['id_ser']) {
													echo '	<option selected="selected" value="'.$rowPer['id_ser'].'">'.$rowPer['nombre'].'</option>';
												}else{	
													echo '	<option value="'.$rowPer['id_ser'].'">'.$rowPer['nombre'].'</option>';
												}
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Ftipo">Tipo de evento: </label></td>
								<td align="left">
									<select class="" name="Ftipo" id="Ftipo" req="" lab="Tipo de evento" >
										<option value="">seleccione</option>
										<?php
											#Revisamos si estan enviando la cotizacion desde la pagina de cliente
											$id_tip_eve = (isset($id_tip_eve) && $id_tip_eve != '')? $id_tip_eve :'';

											$sql="select id_tip_eve, nombre from tipo_evento order by nombre";
											
											$stidTiev = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stidTiev))) {
												if( $id_tip_eve == $rowPer['id_tip_eve']) {
													echo '	<option selected="selected" value="'.$rowPer['id_tip_eve'].'">'.$rowPer['nombre'].'</option>';
												}else{	
													echo '	<option value="'.$rowPer['id_tip_eve'].'">'.$rowPer['nombre'].'</option>';
												}
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Festatus">Estatus: </label></td>
								<td align="left">
									<select class="" name="Festatus" id="Festatus" req="" lab="Estatus" >
										<option value="">Todas</option>
										<option value="COTIZADO">Cotizada</option>
										<option value="VENDIDO">Vendido</option>
										<option value="RECHAZADO">Rechazada</option>
										<option value="CANCELADO">Cancelado</option>
										<option value="TERMINADO">Terminado</option>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Festatus">Vigencia: </label></td>
								<td align="left">
									<select class="" name="Fvigencia" id="Fvigencia" req="" lab="Vigencia" >
										<option value="">Todas</option>
										<option value="ACTIVA" selected="selected">Activa</option>
										<option value="VENCIDA">Vencida</option>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Fecha">Fecha: </label></td>
								<td align="left">
									<select class="" name="Fmes" id="Fmes" req="" lab="Mes" >
										<option value="">Seleccione Mes</option>
										<?php
										echo '<option value="01">Enero</option>';
										echo '<option value="02">Febrero</option>';
										echo '<option value="03">Marzo</option>';
										echo '<option value="04">Abril</option>';
										echo '<option value="05">Mayo</option>';
										echo '<option value="06">Junio</option>';
										echo '<option value="07">Julio</option>';
										echo '<option value="08">Agosto</option>';
										echo '<option value="09">Septiembre</option>';
										echo '<option value="10">Octubre</option>';
										echo '<option value="11">Noviembre</option>';
										echo '<option value="12">Diciembre</option>';
										/*if(date('m') == '01') echo '<option value="01" selected="selected">Enero</option>';
										else echo '<option value="01">Enero</option>';
										if(date('m') == '02') echo '<option value="02" selected="selected">Febrero</option>';
										else echo '<option value="02">Febrero</option>';
										if(date('m') == '03') echo '<option value="03" selected="selected">Marzo</option>';
										else echo '<option value="03">Marzo</option>';
										if(date('m') == '04') echo '<option value="04" selected="selected">Abril</option>';
										else echo '<option value="04">Abril</option>';
										if(date('m') == '05') echo '<option value="05" selected="selected">Mayo</option>';
										else echo '<option value="05">Mayo</option>';
										if(date('m') == '06') echo '<option value="06" selected="selected">Junio</option>';
										else echo '<option value="06">Junio</option>';
										if(date('m') == '07') echo '<option value="07" selected="selected">Julio</option>';
										else echo '<option value="07">Julio</option>';
										if(date('m') == '08') echo '<option value="08" selected="selected">Agosto</option>';
										else echo '<option value="08">Agosto</option>';
										if(date('m') == '09') echo '<option value="09" selected="selected">Septiembre</option>';
										else echo '<option value="09">Septiembre</option>';
										if(date('m') == '10') echo '<option value="10" selected="selected">Octubre</option>';
										else echo '<option value="10">Octubre</option>';
										if(date('m') == '11') echo '<option value="11" selected="selected">Noviembre</option>';
										else echo '<option value="11">Noviembre</option>';
										if(date('m') == '12') echo '<option value="12" selected="selected">Diciembre</option>';
										else echo '<option value="12">Diciembre</option>';
										*/
										?>
									</select>

									<select class="" name="Fano" id="Fano" req="" lab="A&ntilde;o" >
										<option value="">Seleccione A&ntilde;o</option>
										<?php
										$actual = (int)date('Y');
										$anterior3 = $actual - 3;
										$anterior2 = $actual - 2;
										$anterior1 = $actual - 1;
										$siguiente1 = $actual + 1;
										$siguiente2 = $actual + 2;
										$siguiente3 = $actual + 3;
										
										echo '<option value="'.$anterior3.'">'.$anterior3.'</option>';
										echo '<option value="'.$anterior2.'">'.$anterior2.'</option>';
										echo '<option value="'.$anterior1.'">'.$anterior1.'</option>';
										echo '<option value="'.$actual.'">'.$actual.'</option>';
										echo '<option value="'.$siguiente1.'">'.$siguiente1.'</option>';
										echo '<option value="'.$siguiente2.'">'.$siguiente2.'</option>';
										echo '<option value="'.$siguiente3.'">'.$siguiente3.'</option>';
										?>								
									</select>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="2">
									<input type="button" value="Filtrar" onclick="filtrar(this);" tipo="cotizaciones" />
									<input type="reset" value="Limpiar" />
								</td>
							</tr>
						</table>
					</div>
				</form>
				<div class="scrool" id="cont">
					<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="Cabezadefila">
							<th width="8%">No.</th>
							<th width="10%">Fecha</th>
							<th width="7%">Hora</th>
							<th width="15%">Tipo</th>
							<th width="15%">Salón</th>
							<th width="15%">Cliente</th>
							<th width="5%">Edit</th>
							<th width="5%">Estatus</th>
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
						$editar = (isset($row['estatus2']) && ($row['estatus2']=='VENDIDO' || $row['estatus2']=='COTIZADO'))?'<a href="eventos.php?task=edit&id='.$id.'" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a>':'';
					?>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
							<td align="center" class="celdaNormal"><?php echo $ver_evento; ?></td>
							<td align="center" class="celdaNormal"><?php echo $fecha; ?></td>
							<td align="center" class="celdaNormal"><?php echo $hora; ?></td>
							<td align="left" class="celdaNormal"><?php echo $tipodeevento; ?></td>
							<td align="left" class="celdaNormal"><?php echo $salon; ?></td>
							<td align="center" class="celdaNormal"><?php echo $cliente; ?></td>
							<td align="center" class="celdaNormal"><?php echo $editar; ?></td>
							<td align="center" class="celdaNormal"><?php echo $estatus; ?></td>
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
			$sql="
			select 
			ev.id_eve
			, ev.id_emp
			, ev.id_cli
			, ev.id_sal
			, ev.id_tip_eve
			, ev.num_personas personas
			, ev.pagado
			, ev.id_est
			, ev.cos_tot
			, ev.fecha fecha2
			, date_format(ev.fecha, '%d/%m/%y')fecha
			, date_format(ev.hora, '%h')hora
			, date_format(ev.hora, '%i')minuto
			from eventos ev where 1=1 and ev.id_eve=".$id;
			
			$stid = mysql_query($sql);
			$row = mysql_fetch_assoc($stid);
			?>
			<p class="opTitulo">MODIFICAR COTIZACIÓN</p>
			<div id="notices"></div>
			<div class="notices">&nbsp;</div>
			<form name="formulario" id="formulario">
			<?php					

			?>
			<fieldset><legend>Detalles del evento</legend>
				<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>"/>
				<table width="100%" cellpadding="5" cellspacing="5">
					<tr>
						<td width="30%" align="left">
							<label><span class="required">*</span>FECHA</label>
							<input type="hidden" name="Ffecha" id="Ffecha" value="<?php echo $row['fecha2']; ?>" />
							<input class="" name="Ffecha2" type="text" id="Ffecha2" value="<?php echo $row['fecha']; ?>" size="12" maxlength="10" />
						</td>
						<td width="30%" align="left">
							<label><span class="required">*</span>HORA</label>
							<select name="Fhora" id="Fhora">
								<option value="">--</option>
								<?php
									$conta=0;
									$cadena='';

									
									while ($conta < 24) {
										$cadena = str_pad($conta, 2, '0', STR_PAD_LEFT);
										if($conta == $row['hora']) {
											echo '	<option selected="selected" value="'.$cadena.'">'.$cadena.'</option>';
										}else{	
											echo '	<option value="'.$cadena.'">'.$cadena.'</option>';
										}
										$conta++;
									}
								?>
							</select>
							<select name="Fminuto" id="Fminuto">
								<?php
								if($row['minuto'] == '00')
									echo '<option value="00" selected="selected">00</option>';
								else
									echo '<option value="00" >00</option>';

								if($row['minuto'] == '15')
									echo '<option value="15" selected="selected">15</option>';
								else
									echo '<option value="15" >15</option>';

								if($row['minuto'] == '30')
									echo '<option value="30" selected="selected">30</option>';
								else
									echo '<option value="30" >30</option>';

								if($row['minuto'] == '45')
									echo '<option value="45" selected="selected">45</option>';
								else
									echo '<option value="45" >45</option>';
								?>
							</select>
						</td>
						<td width="40%" align="left">
							<label><span class="required">*</span>TIPO</label>
							<select name="Ftipo" id="Ftipo">
							<?php
								$sql="select id_tip_eve, nombre from tipo_evento order by nombre";
								
								$stid = mysql_query($sql);

								while (($rowPer = mysql_fetch_assoc($stid))) {
									if( $rowPer['id_tip_eve'] == $row['id_tip_eve']) {
										echo '	<option selected="selected" value="'.$rowPer['id_tip_eve'].'">'.$rowPer['nombre'].'</option>';
									}else{	
										echo '	<option value="'.$rowPer['id_tip_eve'].'">'.$rowPer['nombre'].'</option>';
									}
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top" align="left">
							NÚMERO DE INVITADOS
							<br />
							<input type="text" name="Fpersonas" id="Fpersonas" value="<?php echo $row['personas'] ?>" onkeypress="return vNumeros(event, this);" />
						</td>
						<td valign="top" align="left">
							<label><span class="required">*</span>CLIENTE</label>
							<select class="" name="Fcliente" id="Fcliente" req="req" lab="Cliente" >
								<option value="">seleccione</option>
								<?php
									$sql="select id_cli, concat(nombre, ' ', ape_pat, ' ', ape_mat)nombre from clientes where id_est in(1) order by nombre";
									
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
						<td valign="top" align="left">
							<label><span class="required">*</span>SALÓN</label>
							<select class="" name="Fsalon" id="Fsalon" req="req" lab="Salón"  >
								<option value="">seleccione</option>
								<?php
									$sql="select id_sal, nombre from salones where id_est in(1) order by nombre";
									
									$stid = mysql_query($sql);

									while (($rowPer = mysql_fetch_assoc($stid))) {
										if( $rowPer['id_sal'] == $row['id_sal']) {
											echo '	<option selected="selected" value="'.$rowPer['id_sal'].'">'.$rowPer['nombre'].'</option>';
										}else{	
											echo '	<option value="'.$rowPer['id_sal'].'">'.$rowPer['nombre'].'</option>';
										}
									}
								?>
							</select>
							
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset><legend>Vendedor</legend>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td width="100%" valign="top" align="left">
							<label>NOMBRE DE LA PERSONA QUE REALIZA LA VENTA</label>
							<input type="text" name="Fempleado" id="Fempleado" value="<?php echo $_SESSION[TOKEN.'NOM_COMPLETO'] ?>" size="100" disabled="disabled" >									
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset><legend>Servicios solicitados</legend>
				<?php
				$sql="select id_tip_ser, nombre from tipo_servicio order by nombre";
				$stidTser = mysql_query($sql);
				$cont = 0;
				$ctotal = 0;
				while (($rowTser = mysql_fetch_assoc($stidTser))) {
					$id_tip_ser = $rowTser['id_tip_ser'];
					echo '
					<fieldset><legend>'.$rowTser['nombre'].'</legend>
						<div id="Tser_'.$id_tip_ser.'">';
					
						?>
							<table width="50%" cellpadding="5" cellspacing="0" border="0">								
								<?php
								$sql="select ser.id_ser, ser.nombre, (select 1 from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id.")contratado
								,(select seev.costo from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id." )costo
								from servicios ser
								where 1
								order by nombre";

								$stidSer = mysql_query($sql);

								$servs=0;
								$disabled=' disabled="disabled" ';								
								while (($rowSer = mysql_fetch_assoc($stidSer))) {

									if($rowSer['contratado']=='1') {
										$radios = '<input class="inpservicios" name="Fservicios[]" id="Fservicios'.$cont.'" type="checkbox" value="'.$rowSer['id_ser'].'" checked="checked" />';
										$disabled = '';
										$ctotal += $rowSer['costo'];
									}else {
										$radios = '<input class="inpservicios" name="Fservicios[]" id="Fservicios'.$cont.'" type="checkbox" value="'.$rowSer['id_ser'].'" />';
									}

									echo '
								<tr>
									<td width="5%" align="right">'.$radios.'</td>
									<td width="30%" align="left"><label for="Fservicios'.$cont.'">'.$rowSer['nombre'].'</label></td>
									<td width="15%" align="right">$ <input class="inpcostos" type="text" name="Fcosto[]" id="Fcosto'.$rowSer['id_ser'].'" value="'.$rowSer['costo'].'" size="15" '.$disabled .' style="text-align:right;" onkeypress="return vFlotante(event, this);" /></td>
								</tr>';

									$cont++;
									$servs++;

								}

								if($servs == 0) {
									echo '
								<tr>
									<td colspan="3" align="left">No hay servicios en esta categoria</td>
								</tr>';
								}
								?>
							</table>
						<?php

					echo '</div>
					</fieldset>';
				}
				?>
					<fieldset><legend>COSTO TOTAL DEL EVENTO</legend>
						<table width="50%" cellpadding="5" cellspacing="0" border="0">
							<tr>
								<td width="5%" align="right"></td>
								<td width="30%" align="right"><label>TOTAL</label></td>
								<td width="15%" align="right">$ <input type="text" name="ctotal" id="ctotal" value="<?php echo $ctotal ?>" size="15" style="text-align:right;border-color:#FF353B;" readonly="readonly" req="req" lab="Costo" /></td>
							</tr>
						</table>
					</fieldset>
			</fieldset>

		<table width="100%" cellpadding="3" cellspacing="3" align="center">
			<tr>
				<td align="center"><br />
		            <input class="" type="button" name="" value="Aplicar" onclick="update(this, 'eventos', 'Ffecha2|Fhora|Ftipo|Fcliente|Fsalon|ctotal', 'notices');" />&nbsp;
		            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update(this, 'eventos', 'Ffecha2|Fhora|Ftipo|Fcliente|Fsalon|ctotal', 'notices');" />&nbsp;
		            <input class="" name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
					<div class="avisorequired"><span class="required">* campos requeridos</span></div>
				</td>
			</tr>
		</table>
	</form>
<?php
		break;
		case 'add': #- - - - - - - - - - - - - - -- - - AGREGAR

			echo '<h1>LA PAGINA SOLICITADA NO EXISTE.</h1>';

		break;		
	}// switch

	mysql_close($conn);
	require_once('includes/html_footer.php');
?>