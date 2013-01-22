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
	$js = '
	function enfocarcobro() {
		$("#Fcosto").val("");
		$("#Fcosto").focus();
	}

	function add2list() {
		var destino = $("#list_servicios");
		var Ftipoeve = $("#Ftipoeve");
		var div = $("#notices");

		if(Ftipoeve.val() != null && Ftipoeve.val() !="") {

			var Fservicio = $("#Fservicio");
			if(Fservicio.val() != null && Fservicio.val() !="") {
				var Fcosto = $("#Fcosto");
				var costovalido = parseFloat(Fcosto.val());
				  
				if(Fcosto.val() == null || Fcosto.val() =="" || isNaN(costovalido) || costovalido==0) {
					div.html("<span class=\"aviso\">Introduzca un costo valido para el servicio.</span>");
					Fcosto.focus();
					return false;
				}else {
					var ctotal = $("#ctotal");
					var arrserv = $("#arrserv");
					var cadserv = "";
					if(arrserv.val()=="") {
						cadserv = Fservicio.val()+"|";
					}else {
						var arritems = arrserv.val().split("|");
						if($.inArray(""+Fservicio.val()+"", arritems) != -1) {
							var costovalidoTemp = parseFloat($("#Fcostoxserv"+Fservicio.val()).val());
							var resta = parseFloat(ctotal.val()) - costovalidoTemp;
							ctotal.val(resta);

							$("#list_item_serv"+Fservicio.val()).remove();
							cadserv = arrserv.val();
						}else
							cadserv = arrserv.val()+Fservicio.val()+"|";
					}
					arrserv.val(cadserv);

					var respaldo = destino.html();
					respaldo += "<div id=\"list_item_serv"+Fservicio.val()+"\"><input type=\"hidden\" value=\""+Fservicio.val()+"\" name=\"Fservicios[]\" /><input type=\"button\" value=\"-\" onclick=\"quitar_servicio(this)\" identif=\""+Fservicio.val()+"\" class=\"ui-button ui-widget ui-state-default ui-corner-all\" role=\"button\" ><input type=\"text\" value=\""+Fcosto.val()+"\" name=\"Fcostoxserv[]\" id=\"Fcostoxserv"+Fservicio.val()+"\" readonly=\"readonly\" style=\"text-align:right;\"/> <span>"+$("#Fservicio option[value=\""+Fservicio.val()+"\"]").text()+"</span></div>";
					destino.html(respaldo);
					
					if(ctotal.val()=="")
						ctotal.val(0);

					var suma = parseFloat(ctotal.val()) + costovalido;
					ctotal.val(suma);
					Fcosto.val("");
				}

			}else {
				div.html("<span class=\"aviso\">Seleccione un servicio.</span>");
				Fservicio.focus();
			}
		}
	}

	function quitar_servicio(objeto) {
		var id = objeto.getAttribute("identif");
		var ctotal = $("#ctotal");
		if(ctotal.val() != ""){
			var costovalido = parseFloat($("#Fcostoxserv"+id).val());
			var resta = parseFloat(ctotal.val()) - costovalido;
			ctotal.val(resta);
		}

		$("#list_item_serv"+id).remove();

		//-------------
		var arrserv = $("#arrserv");
		var cadserv = "";
		if(arrserv.val()!="") {
			var arritems = arrserv.val().split("|");
			for(var i=0; i<arritems.length; i++) {
				if(arritems[i] != id)
					cadserv += arritems[i]+"|";
			}
		}
		arrserv.val(cadserv);
	}

	function previo() {
		a = $("#formulario").serialize();
		alert(a)
		b = $("#arrserv").val();
		alert(b)
	}
	';

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

			$sql = "select distinct ev.id_eve			
			, ev.id_tip_eve
			, date_format(ev.fecha, '%d/%m/%y')fecha
			, ev.hora
			, ev.num_personas personas
			, ev.pagado
			, ev.estatus
			, ev.cos_tot
			, sal.nombre salon
			, concat(cli.nombre, ' ', cli.ape_pat) cliente
			, tev.nombre tipodeevento
			from eventos ev
			inner join salones sal using(id_sal)
			inner join clientes cli using(id_cli)
			inner join tipo_evento tev using(id_tip_eve)
			inner join servicios_eventos serev using(id_eve)
			where 1=1 
			and ev.estatus not in ('COTIZADO','RECHAZADO')
			$filtrar_por_cliente
			$filtrar_por_empleado
			$filtrar_por_servicio
			order by ev.fecha";
		
			$stid = mysql_query($sql);
			?>			

			<h3>EVENTOS</h3>
			<div class="boxed-group-inner clearfix">
				<div class="filtro">
					<form id="formulario" name="formulario">
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="eventos" placeholder="buscar"/>
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="eventos" placeholder="buscar"/>
					</form>
					<div align="right" class="botonsuperior">
		        		<input type="button" value="NUEVA COTIZACIÓN" href="<?php echo $_SERVER['PHP_SELF'] ?>?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
					</div>
				</div>
				<div class="scrool" id="cont">
					<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="Cabezadefila">
							<th width="5%">No.</th>
							<th width="5%">Fecha</th>
							<th width="5%">Hora</th>
							<th width="15%">Tipo</th>
							<th width="25%">Salón</th>
							<th width="5%">Pagado</th>
							<th width="15%">Cliente</th>
							<th width="5%">Estatus</th>
							<th width="5%">Edit</th>
							<th width="5%">Notas</th>
							<th width="5%">Cancelar</th>
							<th width="5%">Pagar</th>
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
																		
						$notas = '<a class="fiframe" href="notas.php?id_eve='.$id.'">ver</a>';
						$cancelar = (isset($row['estatus']) && $row['estatus']=='VENDIDO')?'<input type="button" onclick="cambiarest(this);" tipo="cancel" identif="'.$id.'" value="Cancelar" lab="cancelado" />':'';
						$pagar = (isset($row['pagado']) && $row['pagado']==0 && $row['estatus']=='VENDIDO')?'<input type="button" href="pagos.php?task=add&id_eve='.$id.'" onclick=";window.location.href=this.getAttribute(\'href\');return false;" value="Pagar" />':'';
					
					?>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
							<td align="center" class="celdaNormal"><?php echo $noevento; ?></td>
							<td align="center" class="celdaNormal"><?php echo $fecha; ?></td>
							<td align="center" class="celdaNormal"><?php echo $hora; ?></td>
							<td align="left" class="celdaNormal"><?php echo $tipodeevento; ?></td>
							<td align="left" class="celdaNormal"><a href="eventos.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo strtoupper($salon); ?></a></td>
							<td align="center" class="celdaNormal"><?php echo $pagado; ?></td>					
							<td align="center" class="celdaNormal"><?php echo $cliente; ?></td>
							<td align="center" class="celdaNormal"><?php echo $estatus; ?></td>
							<td align="center" class="celdaNormal"><a href="eventos.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td>
							<td align="center" class="celdaNormal"><?php echo $notas; ?></td>
							<td align="center" class="celdaNormal"><?php echo $cancelar; ?></td>
							<td align="center" class="celdaNormal"><?php echo $pagar; ?></td>					
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
		</div>
		</div>
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
			, ev.estatus
			, ev.cos_tot
			, ev.fecha fecha2
			, date_format(ev.fecha, '%d/%m/%y')fecha
			, date_format(ev.falta, '%d/%m/%y')falta
			, date_format(ev.hora, '%h')hora
			, date_format(ev.hora, '%i')minuto
			from eventos ev 
			where 1=1 
			and ev.id_eve=".$id;
			
			$stid = mysql_query($sql);
			$row = mysql_fetch_assoc($stid);
			?>
			<h3>MODIFICAR DATOS DE EVENTO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
			
					<h4>Detalles del evento</h4>
					<table width="100%">
						<tr>
							<td width="30%" align="left">
								<span class="required">*</span>FECHA <br>
								<input type="hidden" name="Ffecha" id="Ffecha" value="<?php echo $row['fecha2']; ?>" />
								<input class="" name="Ffecha2" type="text" id="Ffecha2" value="<?php echo $row['fecha']; ?>" size="12" maxlength="10" />
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
					<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>" />

					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="50%" valign="top" align="left">
								NÚMERO DE INVITADOS
								<br />
								<input type="text" name="Fpersonas" id="Fpersonas" value="<?php echo $row['personas'] ?>" />							
							</td>
							<td width="50%" valign="top" align="left">
								<span class="required">*</span>SALÓN
								<br />
								<select class="" name="Fsalon" id="Fsalon" >
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
						<tr>
							<td valign="top" align="left" colspan="2">
								<span class="required">*</span>CLIENTE
								<br />
								<select class="" name="Fcliente" id="Fcliente" req="req" lab="Cliente" >
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
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Asignado al empleado</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="50%" valign="top" align="left">
								<span class="required">*</span>EMPLEADO
								<br />
								<input type="text" name="Fempleado" id="Fempleado" value="<?php echo $_SESSION[TOKEN.'NOM_COMPLETO'] ?>" size="50" readonly="readonly" >
							</td>
							<td width="50%" valign="top" align="left">
								FECHA DE ALTA
								<br />
								<input type="text" name="Falta" id="Falta" value="<?php echo $row['falta'] ?>" readonly="readonly" />							
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Servicios solicitados</h4>
					<?php
					$sql="select id_tip_ser, nombre from tipo_servicio order by nombre";
					$stidTser = mysql_query($sql);
					$cont = 0;
					$ctotal = 0;
					while (($rowTser = mysql_fetch_assoc($stidTser))) {
						$id_tip_ser = $rowTser['id_tip_ser'];
						echo '
						<hr class="bleed-flush compact" />
						<h4>'.$rowTser['nombre'].'</h4>
							<div id="Tser_'.$id_tip_ser.'">';
						
							?>
								<table width="50%" cellpadding="5" cellspacing="0" border="0">								
									<?php
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
					<hr class="bleed-flush compact" />

					<h4>COSTO TOTAL DEL EVENTO</h4>
						<table width="50%" cellpadding="5" cellspacing="0" border="0">
							<tr>
								<td width="5%" align="right"></td>
								<td width="30%" align="right"><label>TOTAL</label></td>
								<td width="15%" align="right">$ <input type="text" name="ctotal" id="ctotal" value="<?php echo $ctotal ?>" size="15" style="text-align:right;border-color:#FF353B;" readonly="readonly" /></td>
							</tr>
						</table>
					<hr class="bleed-flush compact" />

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
			</div>
		<?php
		break;
		case 'add': #- - - - - - - - - - - - - - -- - - AGREGAR
		?>
			<h3>NUEVA COTIZACIÓN</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Detalles del evento</h4>
					<table width="100%" cellpadding="5" cellspacing="5">
						<tr>
							<td width="30%" align="left">
								<label><span class="required">*</span>FECHA </label>
								<input type="hidden" name="Ffecha" id="Ffecha" value="" />
								<input class="" name="Ffecha2" type="text" id="Ffecha2" value="" size="12" maxlength="10" req="req" lab="Fecha del evento" />
							</td>
							<td width="30%" align="left">
								<label><span class="required">*</span>HORA </label>
								<select name="Fhora" id="Fhora"  req="req" lab="Hora del evento">
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
								<label><span class="required">*</span>TIPO </label>
								<select name="Ftipo" id="Ftipo" req="req" lb="Tipo de evento" >
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
								<label>NÚMERO DE INVITADOS </label>
								<input type="text" name="Fpersonas" id="Fpersonas" value="" onkeypress="return vNumeros(event, this);" />
							</td>
							<td valign="top" align="left">
								<label><span class="required">*</span>SALÓN <a class="sel_nuevo" href="salones.php?task=add">Nuevo</a></label></label>
								<select class="" name="Fsalon" id="Fsalon" req="req" lab="Salón"  >
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
					<hr class="bleed-flush compact" />

					<h4>Vendedor</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="100%" valign="top" align="left">
								<label>NOMBRE DE LA PERSONA QUE REALIZA LA VENTA </label>
								<input type="text" name="Fempleado" id="Fempleado" value="<?php echo $_SESSION[TOKEN.'NOM_COMPLETO'] ?>" size="" disabled="disabled" >									
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Servicios solicitados</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="40%" valign="top" align="left">
								<label>TIPO</label>
								<select class="" name="Ftipoeve" id="Ftipoeve" req="req" lab="Tipo de evento" onchange="carga('servicios', 'selServ');" >
									<option value="">seleccione</option>
									<?php
										$sql="select id_tip_ser, nombre from tipo_servicio order by nombre";
										
										$stid = mysql_query($sql);

										while (($rowPer = mysql_fetch_assoc($stid))) {
											echo '	<option value="'.$rowPer['id_tip_ser'].'">'.$rowPer['nombre'].'</option>';
										}
									?>
								</select>
							</td>
							<td width="40%" valign="top" align="left" id="selServ"></td>
							<td width="20%" valign="top" align="left">
								<label><span class="required">*</span>COSTO</label>
								$ <input type="text" name="Fcosto" id="Fcosto" value="" size="10" style="text-align:right;"  onkeypress="return vFlotante(event, this);" />
								<input type="button" value="+" onclick="add2list()" />
							</td>
						</tr>
						<tr>
							<td colspan="3" id="list_servicios">
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>COSTO TOTAL DEL EVENTO</h4>
					<table width="50%" cellpadding="5" cellspacing="0" border="0">
						<tr>
							<td width="5%" align="right"></td>
							<td width="30%" align="right"><label>TOTAL </label></td>
							<td width="15%" align="right">$ <input type="text" name="ctotal" id="ctotal" value="" size="15" style="text-align:right;border-color:#FF353B;" readonly="readonly" req="req" lab="Costo" /></td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center"><br />
								<input type="button" onclick="add(this, 'eventos', 'Ffecha2|Fhora|Ftipo|Fcliente|Fsalon|ctotal', 'notices');" value="Guardar cambios" />
								<input type="button"  value="Cancelar" href="<?php echo $_SERVER['PHP_SELF'] ?>" onclick=";window.location.href=this.getAttribute('href');return false;" />
					            
					            <div class="avisorequired"><span class="required">* campos requeridos</span></div><a href="#" onclick="previo()">previo</a>						
					            <input type="hidden" value="" id="arrserv" name="arrserv" />
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