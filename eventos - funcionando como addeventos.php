<?php
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
	require_once('includes/html_template.php');
	
	switch($task) {
		case 'list': #- - - - - - - - - - - - - - -- - - LISTAR
							
			$sql = "select ev.id_eve			
			, ev.id_tip_eve
			, date_format(ev.fecha, '%d/%m/%y')fecha
			, ev.hora
			, ev.num_personas personas
			, ev.pagado
			, ev.id_est estatus
			, ev.cos_tot
			, sal.nombre salon
			, concat(cli.nombre, ' ', cli.ape_pat) cliente
			, tev.nombre tipodeevento
			from eventos ev
			inner join estatus using(id_est)
			inner join salones sal using(id_sal)
			inner join clientes cli using(id_cli)
			inner join tipo_evento tev using(id_tip_eve)
			where 1=1 and ev.id_est in(1,3)
			order by ev.fecha";
		
			$stid = mysql_query($sql);
			?>			

			<p class="opTitulo">EVENTOS</p>
			<div class="filtro">
				<form id="formulario" name="formulario">
					<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
					<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="inputPatternminus" tipo="eventos" placeholder="buscar"/>
				</form>
				<div align="right" class="botonsuperior">
	        		<input type="button" value="NUEVA COTIZACIÓN" href="cotizaciones.php?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
				</div>
			</div>
			
			<div class="scrool" id="cont">
			<table width="100%" cellspacing="0" cellpadding="3">
				<tr class="Cabezadefila">
					<th width="3%">#</th>
					<th width="7%">Fecha</th>
					<th width="5%">Hora</th>
					<th width="15%">Tipo</th>
					<th width="25%">Salón</th>
					<th width="5%">Pagado</th>
					<th width="20%">Cliente</th>
					<th width="5%">Estatus</th>
					<th width="5%">Edit</th>
					<th width="5%">Elim</th>
					<th width="5%">Pagar</th>
				</tr>

			<?php
			$registros = 0;
			while($row = mysql_fetch_assoc($stid)) {	
				$registros++;

				$id = $row['id_eve'];
				$tipodeevento = $row['tipodeevento'];
				$salon = $row['salon'];
				$personas = (isset($row['personas']))? number_format($row['personas']) : '';
				$pagado = (isset($row['pagado']) && $row['pagado']==0)? 'NO' : 'SI';
				$fecha = $row['fecha'];
				$hora = $row['hora'];
				$cliente = $row['cliente'];
				
				$accion = ( $row['estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="evento" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="evento accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';
																
				$rem = '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="evento" value="-" />';
				$pagar = (isset($row['pagado']) && $row['pagado']==0)?'<input type="button" href="pagos.php?task=add&id_eve='.$id.'" onclick=";window.location.href=this.getAttribute(\'href\');return false;" value="Pagar" />':'';
			
			?>
				<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
					<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
					<td align="center" class="celdaNormal"><?php echo $fecha; ?></td>
					<td align="center" class="celdaNormal"><?php echo $hora; ?></td>
					<td align="left" class="celdaNormal"><?php echo $tipodeevento; ?></td>
					<td align="left" class="celdaNormal"><a href="eventos.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo strtoupper($salon); ?></a></td>
					<td align="center" class="celdaNormal"><?php echo $pagado; ?></td>					
					<td align="center" class="celdaNormal"><?php echo $cliente; ?></td>
					<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
					<td align="center" class="celdaNormal"><a href="eventos.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td>
					<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
					<td align="center" class="celdaNormal"><?php echo $pagar; ?></td>					
				</tr>
			<?php
			}			
			?>			

				<tr class="Cabezadefila">
					<td colspan='11'><?php echo $registros; ?> Registros encontrados.</td>
				</tr>
			</table>
			</div>
		<?php
		break;
		case 'edit': #- - - - - - - - - - - - - - -- - - MODIFICAR

			# Control de seguridad
			if (!isset($id)) {
				header('Location: '.$_SERVER['PHP_SELF']);
			}
			?>
			<p class="opTitulo">MODIFICAR DATOS DE EVENTO</p>
			<div id="notices"></div>
			<div class="notices">&nbsp;</div>
			<form name="formulario" id="formulario">
			<?php					
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
			<fieldset><legend>Detalles del evento</legend>
				<table width="100%">
					<tr>
						<td width="30%" align="left">
							<span class="required">*</span>FECHA <br>
							<input type="hidden" name="Ffecha" id="Ffecha" value="<?php echo $row['fecha2']; ?>" />
							<input class="inputPattern" name="Ffecha2" type="text" id="Ffecha2" value="<?php echo $row['fecha']; ?>" size="12" maxlength="10" />
						</td>
						<td width="30%" align="left">
							<span class="required">*</span>HORA <br>
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
							<span class="required">*</span>TIPO <br>
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
				</table>
				<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>"/>

				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td width="60%" valign="top" align="left">
							NÚMERO DE INVITADOS
							<br />
							<input type="text" name="Fpersonas" id="Fpersonas" value="<?php echo $row['personas'] ?>" />							
						</td>
						<td width="40%" valign="top" align="left">
							PAGADO
							<br />
							<?php
							$pagado = '';
							if($row['pagado']=='1')
								$pagado = ' checked="checked" ';

							echo '
							<input type="checkbox" name="Fpagado" id="Fpagado" value="1" '.$pagado.' />';
							?>
							
						</td>
					</tr>
					<tr>
						<td valign="top" align="left">
							<span class="required">*</span>CLIENTE
							<br />
							<select class="inputPattern" name="Fcliente" id="Fcliente" >
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
						<td valign="top" align="left">
							<span class="required">*</span>ESTATUS
							<br />
							<select class="inputPattern" name="Festatus" id="Festatus" >
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
					</tr>
				</table>
			</fieldset>

			<fieldset><legend>Asignado al empleado</legend>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td width="50%" valign="top" align="left">
							<span class="required">*</span>EMPLEADO
							<br />
							<input type="text" name="Fempleado" id="Fempleado" value="<?php echo $_SESSION[TOKEN.'NOM_COMPLETO'] ?>" disabled="disabled" >
						</td>
						<td width="50%" valign="top" align="left">
							<span class="required">*</span>SALÓN
							<br />
							<select class="inputPattern" name="Fsalon" id="Fsalon" >
							<?php
								$sql="select id_sal, nombre from salones order by nombre";
								
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
								//$sql="select id_ser, nombre from servicios where id_tip_ser=".$id_tip_ser." order by nombre";
								$sql="select ser.id_ser, ser.nombre, (select 1 from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id.")contratado
								,(select seev.costo from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id." )costo
								from servicios ser
								where id_tip_ser=".$id_tip_ser." 
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
									<td width="15%" align="right">$ <input type="text" name="ctotal" id="ctotal" value="<?php echo $ctotal ?>" size="15" style="text-align:right;border-color:#FF353B;" readonly="readonly" /></td>
								</tr>
							</table>
						</fieldset>
			</fieldset>

		<table width="100%" cellpadding="3" cellspacing="3" align="center">
			<tr>
				<td align="center"><br />
		            <input class="" type="button" name="" value="Aplicar" onclick="update_evento(this);" />&nbsp;
		            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update_evento(this);" />&nbsp;
		            <input class="" name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
					<div class="avisorequired"><span class="required">* campos requeridos</span></div>
				</td>
			</tr>
		</table>
	</form>
<?php
		break;
		case 'add': #- - - - - - - - - - - - - - -- - - AGREGAR
# LA CANCELE PORQUE AL PARECER NO SE VAN A DAR DE ALTA EVENTOS SOLO COTIZACIONES Y DESPUES SE VAN A VENDER
if(1!=1) {
?>
			<p class='opTitulo'>AGREGAR EVENTO</p>
			<div id="notices"></div>
			<form id="formulario">
				<fieldset><legend>Detalles del evento</legend>
					<table width="100%">
						<tr>
							<td width="30%" align="left">
								<span class="required">*</span>FECHA <br>
								<input type="hidden" name="Ffecha" id="Ffecha" value="" />
								<input class="inputPattern" name="Ffecha2" type="text" id="Ffecha2" value="" size="12" maxlength="10" />
							</td>
							<td width="30%" align="left">
								<span class="required">*</span>HORA <br>
								<select name="Fhora" id="Fhora">
									<option value="">--</option>
									<?php
										$conta=0;
										$cadena='';
										
										while ($conta < 24) {
											$cadena = str_pad($conta, 2, '0', STR_PAD_LEFT);
											echo '	<option value="'.$cadena.'">'.$cadena.'</option>';											
											$conta++;
										}
									?>
								</select>
								<select name="Fminuto" id="Fminuto">
									<option value="00" selected="selected">00</option>
									<option value="15">15</option>
									<option value="30">30</option>
									<option value="45">45</option>
								</select>
							</td>
							<td width="40%" align="left">
								<span class="required">*</span>TIPO <br>
								<select name="Ftipo" id="Ftipo">
									<option value="">seleccione</option>
									<?php
										$sql="select id_tip_eve, nombre from tipo_evento order by nombre";
										
										$stid = mysql_query($sql);

										while (($rowPer = mysql_fetch_assoc($stid))) {
											echo '	<option value="'.$rowPer['id_tip_eve'].'">'.$rowPer['nombre'].'</option>';
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td valign="top" align="left">
								NÚMERO DE INVITADOS
								<br />
								<input type="text" name="Fpersonas" id="Fpersonas" value="" />							
							</td>
							<td valign="top" align="left">
								<span class="required">*</span>CLIENTE
								<br />
								<select class="inputPattern" name="Fcliente" id="Fcliente" >
									<option value="">seleccione</option>
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
							<td valign="top" align="left">
								
								
							</td>
						</tr>
						<tr>
							<td valign="top" align="left">
								
							</td>
							<td valign="top" align="left">
								<!--span class="required">*</span>ESTATUS
								<br />
								<select class="inputPattern" name="Festatus" id="Festatus" >
									<option value="">seleccione</option>
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
								</select-->
							</td>
						</tr>
					</table>
				</fieldset>

				<fieldset><legend>Asignado al empleado</legend>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="50%" valign="top" align="left">
								<span class="required">*</span>EMPLEADO
								<br />								
								<input type="text" name="Fempleado" id="Fempleado" value="<?php echo $_SESSION[TOKEN.'NOM_COMPLETO'] ?>" disabled="disabled" >									
							</td>
							<td width="50%" valign="top" align="left">
								<span class="required">*</span>SALÓN
								<br />
								<select class="inputPattern" name="Fsalon" id="Fsalon" >
									<option value="">seleccione</option>
									<?php
										$sql="select id_sal, nombre from salones order by nombre";
										
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
									//$sql="select id_ser, nombre from servicios where id_tip_ser=".$id_tip_ser." order by nombre";
									$sql="select ser.id_ser, ser.nombre, 0 contratado
									,''costo
									from servicios ser
									where id_tip_ser=".$id_tip_ser." 
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
										<td width="15%" align="right">$ <input class="inpcostos" type="text" name="Fcosto[]" id="Fcosto'.$rowSer['id_ser'].'" value="'.$rowSer['costo'].'" size="15" '.$disabled .' style="text-align:right;"  onkeypress="return vFlotante(event, this);" /></td>
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
						<fieldset><legend>Costo Total del Evento</legend>
							TOTAL $ <span id="ctotal"><?php echo number_format($ctotal) ?></span>
						</fieldset>
				</fieldset>

		<table width="100%" cellpadding="3" cellspacing="3" align="center">
			<tr>
				<td align="center"><br />
					
					<input type="button" onclick="add_evento();" value="Guardar cambios" />
					<input type="button"  value="Cancelar" href="<?php echo $_SERVER['PHP_SELF'] ?>" onclick=";window.location.href=this.getAttribute('href');return false;" />
		            
		            <div class="avisorequired"><span class="required">* campos requeridos</span></div>						
				</td>
			</tr>
		</table>
	</form>
<?php
} else {
	//header('Location:cotizaciones.php?task=add');
	echo '<h1>LA PAGINA SOLICITADA NO EXISTE.</h1>';
}
		break;		
	}// switch

	mysql_close($conn);
	require_once('includes/html_footer.php');
?>