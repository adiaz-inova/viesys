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
		var detalle = $("#detalle");
		// alert(detalle.val())
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

					//esta parte solo arma la cadena de arrserv
					if(arrserv.val()=="") {
						cadserv = Fservicio.val()+"|";
					}else {
						var arritems = arrserv.val().split("|");
						if($.inArray(""+Fservicio.val()+"", arritems) != -1) {
							var costovalidoTemp = parseFloat($("#Fcostoxserv"+Fservicio.val()).val());
							// primero le resto su mismo valor anterior
							var total_sincomas = ctotal.val().replace(",","");
							var resta = parseFloat(total_sincomas) - costovalidoTemp;

							var iva = resta * 0.16;
							var campototal = resta + iva;
							iva = iva.toFixed(2);
							campototal = campototal.toFixed(2);

							ctotal.val(resta);
							$("#civa").val(iva);
							$("#ccosto").val(campototal);

							$("#list_item_serv"+Fservicio.val()).remove();
							cadserv = arrserv.val();
						} else
							cadserv = arrserv.val()+Fservicio.val()+"|";
					}
					arrserv.val(cadserv);

					var respaldo = destino.html();
					var costoxserv = parseFloat(Fservicio.val());
					costoxserv = costoxserv.toFixed(2);
					respaldo += "<div id=\"list_item_serv"+Fservicio.val()+"\"><input type=\"hidden\" value=\""+costoxserv+"\" name=\"Fservicios[]\" /><input type=\"button\" value=\"-\" onclick=\"quitar_servicio(this)\" identif=\""+Fservicio.val()+"\" class=\"ui-button ui-widget ui-state-default ui-corner-all\" role=\"button\" ><textarea name=\"Fdetallesxserv[]\" id=\"Fdetallesxserv"+Fservicio.val()+"\">"+detalle.val()+"</textarea><input type=\"text\" value=\""+Fcosto.val()+"\" name=\"Fcostoxserv[]\" id=\"Fcostoxserv"+Fservicio.val()+"\" readonly=\"readonly\" style=\"text-align:right;\"/> <span>"+$("#Fservicio option[value=\""+Fservicio.val()+"\"]").text()+"</span></div>";
					destino.html(respaldo);
					
					if(ctotal.val()=="")
						ctotal.val(0);
					
					var total_sincomas = ctotal.val().replace(",","");
					var suma = parseFloat(total_sincomas) + costovalido;
					var iva = suma * 0.16;
					var campototal = suma + iva;
					iva = iva.toFixed(2);
					campototal = campototal.toFixed(2);
					suma = suma.toFixed(2);
					ctotal.val(suma);
					$("#civa").val(iva);
					$("#ccosto").val(campototal);

					Fcosto.val("");
					detalle.val("");
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
			var iva = resta * 0.16;
			var campototal = resta + iva;
			iva = (iva > 0)?iva.toFixed(2):0.00;
			campototal = (campototal > 0)?campototal.toFixed(2):0.00;
			resta = (resta > 0)?resta.toFixed(2):0.00;
			ctotal.val(resta);
			$("#civa").val(iva);
			$("#ccosto").val(campototal);
		}

		$("#list_item_serv"+id).remove();

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
	});';
	// VIE requiere registrar cotizaciones o ventas de dias pasados .. por que por las prisas no se registraron y se quieren meter al sistema
	//$("#Ffecha2").datepicker( "option", "minDate", "+0d" );

	$js_extras_onready.='$(".inpservicios").change(function() {
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

	$("#Ffactura").change(function() {
		if($(this).attr("checked")) {
			$("#span_ccosto").css("display","");
			$("#span_civa").css("display","");
		}else {
			$("#span_ccosto").css("display","none");
			$("#span_civa").css("display","none");
		}
	});

	$(".sel_nuevo").click(function() {
		// alert($(this).attr("tipo"))
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
			$filtrar_por_tipo_evento = (isset($id_tipo_evento) && (int)$id_tipo_evento>0)? " AND ev.id_tip_eve = ".$id_tipo_evento:"";

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
			where 1=1 
			and ev.estatus in ('VENDIDO')
			$filtrar_por_cliente
			$filtrar_por_empleado
			$filtrar_por_servicio
			$filtrar_por_salon
			$filtrar_por_tipo_evento
			group by id_eve
			order by ev.estatus asc, ev.fecha";
		
			$stid = mysql_query($sql);
			?>			

			<h3>EVENTOS</h3>
			<div class="boxed-group-inner clearfix">
				<form id="formulario" name="formulario">
					<div class="filtro">
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="eventos" placeholder="buscar"/><a href="javascript:more_filters();" class="sel_nuevo">Avanzado</a>
						<div align="right" class="botonsuperior">
			        		<input type="button" value="NUEVA COTIZACIÓN" href="<?php echo $_SERVER['PHP_SELF'] ?>?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
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
										<option value="">Seleccione Salón</option>
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
										<option value="TODOS">Todos</option>
										<option value="VENDIDO" selected="selected">Vendido</option>
										<option value="CANCELADO">Cancelado</option>
										<option value="TERMINADO">Terminado</option>
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
									<input type="button" value="Filtrar" onclick="filtrar(this);" tipo="eventos" />
									<input type="reset" value="Limpiar" />
								</td>
							</tr>
						</table>
					</div>
				</form>
				
				<div class="scrool" id="cont">
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
				</div>
			</div><!-- class="boxed-group-inner clearfix" -->
		<?php
		break;
		case 'edit': #- - - - - - - - - - - - - - -- - - MODIFICAR

			# Control de seguridad
			if (!isset($id)) {
				header('Location: '.$_SERVER['PHP_SELF']);
			}

			$editar_tipo = (isset($editar_tipo) && $editar_tipo != '')? '<input type="hidden" name="editar_tipo" id="editar_tipo" value="'.$editar_tipo.'">':'';

			# Consultamos la informacion del usuario
			$sql="
			select 
			ev.id_eve
			, ev.id_emp
			, emp.nombre, emp.ape_pat, emp.ape_mat
			, ev.id_cli
			, ev.id_sal
			, ev.id_tip_eve
			, ev.num_personas personas
			, ev.pagado
			, ev.estatus
			, ev.cos_tot
			, ev.facturar
			, ev.fecha fecha2
			, ev.observaciones
			, date_format(ev.fecha, '%d/%m/%y')fecha
			, date_format(ev.falta, '%d/%m/%y')falta
			, date_format(ev.hora, '%H')hora
			, date_format(ev.hora, '%i')minuto
			from eventos ev 
			left join empleados emp using(id_emp)
			where 1=1 
			and ev.id_eve=".$id;
			
			$stid = mysql_query($sql);
			$row = mysql_fetch_assoc($stid);

			$ctotal = $row['cos_tot'];

			$reqfactura = (isset($row['facturar']) && $row['facturar']=='1')?' checked="checked" ':'';
			?>
			<h3>MODIFICAR DATOS DE EVENTO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Detalles del evento</h4>
					<table width="100%" cellpadding="5" cellspacing="5">
						<tr>
							<td width="30%" align="left">
								<label><span class="required">*</span>FECHA </label>
								<input type="hidden" name="Ffecha" id="Ffecha" value="<?php echo $row['fecha2']; ?>" />
								<?php echo $editar_tipo ?>
								<input class="" name="Ffecha2" type="text" id="Ffecha2" value="<?php echo $row['fecha']; ?>" size="12" maxlength="10" req="req" lab="Fecha del evento" />
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
								<label><span class="required">*</span>TIPO </label>
								<select name="Ftipo" id="Ftipo" req="req" lb="Tipo de evento" >
									<option value="">seleccione</option>
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
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="30%" valign="top" align="left">
								<label>NÚMERO DE INVITADOS </label>
								<input type="text" name="Fpersonas" id="Fpersonas" value="<?php echo $row['personas'] ?>" size="6" onkeypress="return vNumeros(event, this);" />
							</td>
							<td width="70%" valign="top" align="left">
								<label><span class="required">*</span>SALÓN </label>
								<select class="" name="Fsalon" id="Fsalon" req="req" lab="Salón"  >
									<option value="">Seleccione Salón</option>
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
						<tr>
							<td>
								<label for="Ffactura">¿REQUIERE FACTURA? </label>
								SÍ <input type="checkbox" name="Ffactura" id="Ffactura" value="1" <?php echo $reqfactura?>>
							</td>
							<td>
								<label><span class="required">*</span>CLIENTE </label>
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
					<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>" />
					<hr class="bleed-flush compact" />

					<h4>Vendedor</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="100%" valign="top" align="left">
								<label>NOMBRE DE LA PERSONA QUE REALIZA LA VENTA </label>
								<input type="text" name="Fempleado" id="Fempleado" value="<?php echo "(".$row['id_emp'].") ".$row['nombre']." ".$row['ape_pat']." ".$row['ape_mat']; ?>" size="60" readonly="readonly" >
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Servicios solicitados</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<input name="Ftipoeve" type="hidden" id="Ftipoeve" value="5"/><!-- el ID 5 es UNICO_TIPO -->
							<!--<td width="30%" valign="top" align="left">
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
							</td> -->
							<td width="40%" valign="top" align="left" id="selServ"></td>
							<script language="javascript"> carga('servicios', 'selServ'); </script>
							<td width="30%" valign="top" align="left">
								<label><span class="required">*</span>COSTO</label>
								$ <input type="text" name="Fcosto" id="Fcosto" value="" size="10" style="text-align:right;"  onkeypress="return vFlotante(event, this);" />
								<input type="button" value="+" onclick="add2list()" />
							</td>
						</tr>
						<tr>
							<td colspan="3" id="list_servicios">
								<?php
								$sql="select ser.id_ser, ser.nombre, (select 1 from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id.")contratado
								,(select seev.costo from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id." )costo
								from servicios ser
								where id_est in(1)
								order by nombre";
								$stidSer = mysql_query($sql);

								$disabled = ' disabled="disabled" ';
								$list_servicios = '';
								$list_ctotal = 0;
								while (($rowSer = mysql_fetch_assoc($stidSer))) {

									if($rowSer['contratado']=='1') {
										$list_id_ser = $rowSer['id_ser'];
										$list_id_nombre = $rowSer['nombre'];
										$list_servicios .= $list_id_ser.'|';

										echo '
										<div id="list_item_serv'.$list_id_ser.'">
											<input type="hidden" value="'.$list_id_ser.'" name="Fservicios[]" />
											<input type="button" value="-" onclick="quitar_servicio(this)" identif="'.$list_id_ser.'" class="ui-button ui-widget ui-state-default ui-corner-all" role="button" />
											<input type="text" value="'.$rowSer['costo'].'" name="Fcostoxserv[]" id="Fcostoxserv'.$list_id_ser.'" readonly="readonly" style="text-align:right;" />
											<span>'.$list_id_nombre.'</span>
										</div>';

										$list_ctotal += $rowSer['costo'];
									}

								}
								?>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Costo Total del evento</h4>
					<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr>							
							<td width="100%" align="right">
								<?php 
									$iva = $list_ctotal * 0.16;
									$campototal = $list_ctotal + $iva;
									$list_ctotal = number_format($list_ctotal,2);
									$iva = number_format($iva,2);
									$campototal = number_format($campototal,2);
								?>
								COSTO $ <input type="text" name="ctotal" id="ctotal" value="<?php echo $list_ctotal ?>" size="15" style="text-align:right;border:solid 2px #333;" readonly="readonly" req="req" lab="Costo" />
								<span class="cajitas" id="span_civa" style="display:none;">IVA $ <input type="text" name="civa" id="civa" value="<?php echo $iva ?>" size="15" style="text-align:right;border:solid 2px #333;" readonly="readonly" req="req" lab="IVA" /></span>
								<span class="cajitas" id="span_ccosto" style="display:none;">TOTAL $ <input type="text" name="ccosto" id="ccosto" value="<?php echo $campototal ?>" size="15" style="text-align:right;border:solid 2px #333;" readonly="readonly" req="req" lab="Total" /></span>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>OBSERVACIONES</h4>
					<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr>
							<td width="100%" align="center">
								<textarea name="Fobservaciones" id="Fnotas" cols="40" rows="5" style="width:450px" onkeypress="return vAbierta(event, this);"><?php echo $row['observaciones'] ?></textarea>
								<div class="avisorequired"><span class="required">Esta información se usará en la impresión de la cotización</span></div>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center"><br />
					            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update_evento(this);" />&nbsp;
					            <input class="" name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
								<div class="avisorequired"><span class="required">* campos requeridos</span></div>
								<input type="hidden" value="<?php echo $list_servicios ?>" id="arrserv" name="arrserv" />
							</td>
						</tr>
					</table>
				</form>
			</div>
			<script type="text/javascript">
				$(document).ready(function() {
					<?php if(isset($row['facturar']) && $row['facturar']=='1') { ?>
					$("#Ffactura").attr("checked","checked");
					$("#span_ccosto").css("display","");
					$("#span_civa").css("display","");
					<?php }else { ?>
					$("#Ffactura").removeAttr("checked");
					<?php } ?>

				});

			</script>
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
									<option value="00" selected="selected">00</option>
									<?php
										$conta=1;
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
								<input type="text" name="Fpersonas" id="Fpersonas" size="6" value="" onkeypress="return vNumeros(event, this);" />
							</td>
							<td valign="top" align="left">
								<label><span class="required">*</span>SALÓN 
									<a class="fiframe sel_nuevo" href="iframe.php?tipo=salones">Nuevo</a>
									<!-- <a class="fiframe sel_nuevo" href="iframe.php?" >Nuevo</a> -->
									<!-- onclick="carga_iframe('add_script','salones','add')" -->
								</label>
								<div id="div_select_salones">
									<select class="" name="Fsalon" id="Fsalon" req="req" lab="Salón"  >
									<option value="">Seleccione Salón</option>
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
								</div>
								
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Cliente</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="40%">
								<label for="Ffactura">¿REQUIERE FACTURA? </label>
								SÍ <input type="checkbox" name="Ffactura" id="Ffactura" value="1">
							</td>
							<td>
							<td width="60%" valign="top" align="left">
								<label><span class="required">*</span>CLIENTE 
									<!-- <a class="sel_nuevo" href="#" onclick="carga_iframe('add_script','clientes','add')" >Nuevo</a> -->
									<a class="fiframe sel_nuevo" href="iframe.php?tipo=clientes">Nuevo</a>
								</label>
								<div id="div_select_clientes">
									<select class="" name="Fcliente" id="Fcliente" req="req" lab="Cliente" >
										<option value="">...</option>
										<?php
											$id_cli = (isset($id_cli) && $id_cli != '')? $id_cli :'';

											$sql="select id_cli, concat(nombre, ' ', ape_pat, ' ', ape_mat)nombre from clientes where id_est in(1) order by nombre";
											
											$stid = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stid))) {
												if( $rowPer['id_cli'] == $id_cli) {
													echo '	<option selected="selected" value="'.$rowPer['id_cli'].'">'.$rowPer['nombre'].'</option>';
												}else{	
													echo '	<option value="'.$rowPer['id_cli'].'">'.$rowPer['nombre'].'</option>';
												}
											}
										?>
									</select>
								</div>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Vendedor</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="100%" valign="top" align="left">
								<label>NOMBRE DE LA PERSONA QUE REALIZA LA VENTA </label>
								<input type="text" name="Fempleado" id="Fempleado" value="<?php echo $_SESSION[TOKEN.'NOM_COMPLETO'] ?>" size="60" readonly="readonly" >
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Servicios solicitados</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<input name="Ftipoeve" type="hidden" id="Ftipoeve" value="5"/>
							<td width="40%" valign="top" align="left" id="selServ"></td>
							<script language="javascript"> carga('servicios', 'selServ'); </script>
							<td width="30%" valign="top" align="left">
								<label><span class="required">*</span>COSTO</label>
								$ <input type="text" name="Fcosto" id="Fcosto" value="" size="10" style="text-align:right;"  onkeypress="return vFlotante(event, this);" />
								<input type="button" value="+" onclick="add2list()" />
							</td>
						</tr>
						<tr>
							<td>
								<label>DETALLE DEL SERVICIO </label>
								<textarea name="detalle" id="detalle" cols="60" rows="5"></textarea></td>
						</tr>
						<tr>
							<td colspan="3" id="list_servicios">
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>COSTO TOTAL DEL EVENTO</h4>
					<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr>
							<td width="100%" align="right">
								COSTO $ <input type="text" name="ctotal" id="ctotal" value="" size="15" style="text-align:right;border:solid 2px #333;" readonly="readonly" req="req" lab="Costo" />
								<span class="cajitas" id="span_civa" style="display:none;">IVA $ <input type="text" name="civa" id="civa" value="" size="15" style="text-align:right;border:solid 2px #333;" readonly="readonly" req="req" lab="IVA" /></span>
								<span class="cajitas" id="span_ccosto" style="display:none;">TOTAL $ <input type="text" name="ccosto" id="ccosto" value="" size="15" style="text-align:right;border:solid 2px #333;" readonly="readonly" req="req" lab="Total" /></span>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>OBSERVACIONES</h4>
					<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr>
							<td width="100%" align="center">
								<textarea name="Fobservaciones" id="Fnotas" cols="40" rows="5" style="width:450px" onkeypress="return vAbierta(event, this);">
* Forma de pago: 50% del total al reservar los servicios y firmar el contrato respectivo, y el 50% restante a más tardar dentro de los 7 días naturales posteriores al evento. 
* Se considera realizar la mayor parte del montaje una tarde anterior al evento e iniciar el desmontaje una vez finalizado Se considera realizar la mayor parte del montaje una tarde anterior al evento e iniciar el desmontaje una vez finalizado el mismo.
</textarea>
								<div class="avisorequired"><span class="required">Esta información se usará en la impresión de la cotización</span></div>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>AGREGAR NOTA AL EVENTO</h4>
					<table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr>
							<td width="100%" align="center">
								<textarea name="Fnotas" id="Fnotas" cols="40" rows="5" style="width:450px" onkeypress="return vAbierta(event, this);"></textarea>
								<div class="avisorequired"><span class="required">Esta información es de uso interno.</span></div>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center"><br />
								<input type="button" onclick="add(this, 'eventos', 'Ffecha2|Fhora|Ftipo|Fcliente|Fsalon|ctotal', 'notices');" value="Guardar cambios" />
								<input type="button"  value="Cancelar" href="<?php echo $_SERVER['PHP_SELF'] ?>" onclick=";window.location.href=this.getAttribute('href');return false;" />
					            
					            <div class="avisorequired"><span class="required">* campos requeridos</span></div>
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