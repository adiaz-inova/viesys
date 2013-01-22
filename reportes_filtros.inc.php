<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session
	require_once('token.inc.php');
	require_once('includes/getvalues.php');
	require_once('includes/session_principal.php');	
	require_once('includes/conection.php');
	
	$addsql = "";
			
	switch($tipo) {
		case 'eventos':
			?>
	<script type="text/javascript">
	$(document).ready(function() {

		$.datepicker.setDefaults({
			inline: true,
			dateFormat: "dd/mm/yy",
			numberOfMonths: 1,
			monthNames: ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
			dayNames: ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
			dayNamesMin: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
			showAnim: "slide"			
		});
		
		$("#Fdesde2").datepicker({
			altField: "#Fdesde",
	        altFormat: "yy-mm-dd",
	        maxDate: "+0d"
		});
		$("#Fhasta2").datepicker({
			altField: "#Fhasta",
	        altFormat: "yy-mm-dd",
	        maxDate: "+0d"
		});
		$("#Fdesde2").change(function () {
			var min= $("#Fdesde2").datepicker("getDate");
			$("#Fhasta2").datepicker( "option", "minDate", min );
		});
	})
	</script>
	<div class="boxed-group">
		<h3>FILTROS - REPORTE DE EVENTOS</h3>
		<div class="boxed-group-inner clearfix">
			<div id="div_filtros">
				<form id="formulario" name="formulario">
					<div id="more_filters">
						<table width="100%" cellspacing="5">
							<tr>
								<td align="right"><label for="Fevento">Número de Evento: </label></td>
								<td align="left">
									<input class="" name="Fevento" id="Fevento" size="5" req="" lab="Evento" onkeypress="return vNumeros(event, this);" />
								</td>
							</tr>
							<tr>
								<td align="right">
									<label>DE LA FECHA: </label>
								</td>
								<td align="left">
									<input type="hidden" name="Fdesde" id="Fdesde" value="" />
									<input class="" name="Fdesde2" type="text" id="Fdesde2" value="" size="12" maxlength="10" req="" lab="de la fecha" />
								</td>
							</tr>
							<tr>
								<td align="right">
									<label>A LA FECHA: </label>
								</td>
								<td align="left">
									<input type="hidden" name="Fhasta" id="Fhasta" value="" />
									<input class="" name="Fhasta2" type="text" id="Fhasta2" value="" size="12" maxlength="10" req="" lab="a la fecha" />
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Festatus">Estatus: </label></td>
								<td align="left">
									<select name="Festatus" id="Festatus" req="" lb="Estatus" >
										<option value="">...</option>
										<option value="COTIZADO">COTIZADO</option>
										<option value="RECHAZADO">RECHAZADO</option>
										<option value="VENDIDO">VENDIDO</option>
										<option value="CANCELADO">CANCELADO</option>
										<option value="TERMINADO">TERMINADO</option>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Fempleado">Empleado: </label></td>
								<td align="left">
									<select class="" name="Fempleado" id="Fempleado" req="" lab="Empleado" >
										<option value="">...</option>
										<?php
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
								<td align="right"><label for="Fcliente">Cliente: </label></td>
								<td align="left">
									<select class="" name="Fcliente" id="Fcliente" req="" lab="Cliente" >
										<option value="">...</option>
										<?php
											$sql="select id_cli, concat(nombre, ' ', ape_pat, ' ', ape_mat)nombre from clientes where id_est in(1) order by nombre";
											
											$stid = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stid))) {
												echo '	<option value="'.$rowPer['id_cli'].'">'.$rowPer['nombre'].'</option>';
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Ftipoeve">Tipo de evento: </label></td>
								<td align="left">
									<select name="Ftipoeve" id="Ftipoeve" req="" lb="Tipo de evento" >
										<option value="">...</option>
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
								<td align="right"><label for="Fsalon">Salón de fiestas: </label></td>
								<td align="left">
									<select name="Fsalon" id="Fsalon" req="" lb="Tipo de evento" >
										<option value="">...</option>
										<?php
											$sql="select id_sal, nombre from salones order by nombre";
											
											$stid = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stid))) {
												echo '	<option value="'.$rowPer['id_sal'].'">'.$rowPer['nombre'].'</option>';
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Ftiposer">Tipo de servicio: </label></td>
								<td align="left">
									<select name="Ftiposer" id="Ftiposer" req="" lb="Tipo de evento" >
										<option value="">...</option>
										<?php
										$sql="select id_tip_ser, nombre from tipo_servicio order by nombre";
										$stid = mysql_query($sql);

										while (($rowPer = mysql_fetch_assoc($stid))) {
											echo '	<option value="'.$rowPer['id_tip_ser'].'">'.$rowPer['nombre'].'</option>';
										}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Fservicio">Servicio: </label></td>
								<td align="left">
									<select class="" name="Fservicio" id="Fservicio" req="" lab="Servicio" >
										<option value="">...</option>
										<?php
											#Revisamos si estan enviando la cotizacion desde la pagina de cliente
											$id_ser = (isset($id_ser) && $id_ser != '')? $id_ser :'';

											$sql="select id_ser, nombre from servicios where id_est in(1) order by nombre";
											$stidSer = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stidSer))) {
												echo '	<option value="'.$rowPer['id_ser'].'">'.$rowPer['nombre'].'</option>';
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Ffactura">Número de Factura: </label></td>
								<td align="left">
									<input class="" name="Ffactura" id="Ffactura" size="5" req="" lab="Servicio" onkeypress="return vNumeros(event, this);" />
								</td>
							</tr>
							<tr>
								<td align="center" colspan="2">
									<input type="button" value="Generar reporte" onclick="generar_reporte_pdf(this)" tipo="eventos" />
									<input type="reset" value="Limpiar" />
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div><!-- class="boxed-group-inner clearfix" -->
	</div><!-- class="boxed-group" -->
			<?php
		break;
		case 'logs':
			?>
	<script type="text/javascript">
	$(document).ready(function() {

		$.datepicker.setDefaults({
			inline: true,
			dateFormat: "dd/mm/yy",
			numberOfMonths: 1,
			monthNames: ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
			dayNames: ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"],
			dayNamesMin: ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
			showAnim: "slide"			
		});
		
		$("#Fdesde2").datepicker({
			altField: "#Fdesde",
	        altFormat: "yy-mm-dd",
	        maxDate: "+0d"
		});
		$("#Fhasta2").datepicker({
			altField: "#Fhasta",
	        altFormat: "yy-mm-dd",
	        maxDate: "+0d"
		});
		$("#Fdesde2").change(function () {
			var min= $("#Fdesde2").datepicker("getDate");
			$("#Fhasta2").datepicker( "option", "minDate", min );
		});
	})
	</script>
	<div class="boxed-group">
		<h3>FILTROS - REPORTE DE INGRESOS AL SISTEMA</h3>
		<div class="boxed-group-inner clearfix">
			<div id="div_filtros">
				<form id="formulario" name="formulario">
					<div id="more_filters">
						<table width="100%" cellspacing="5">
							<tr>
								<td align="right">
									<label>DE LA FECHA: </label>
								</td>
								<td align="left">
									<input type="hidden" name="Fdesde" id="Fdesde" value="" />
									<input class="" name="Fdesde2" type="text" id="Fdesde2" value="" size="12" maxlength="10" req="" lab="de la fecha" />
								</td>
							</tr>
							<tr>
								<td align="right">
									<label>A LA FECHA: </label>
								</td>
								<td align="left">
									<input type="hidden" name="Fhasta" id="Fhasta" value="" />
									<input class="" name="Fhasta2" type="text" id="Fhasta2" value="" size="12" maxlength="10" req="" lab="a la fecha" />
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Fempleado">Empleado: </label></td>
								<td align="left">
									<select class="" name="Fempleado" id="Fempleado" req="" lab="Empleado" >
										<option value="">...</option>
										<?php
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
								<td align="right"><label for="Fgrupo">Grupo: </label></td>
								<td align="left">
									<select class="" name="Fgrupo" id="Fgrupo" req="" lab="Grupo" >
										<option value="">...</option>
										<?php
											$sql="select id_gru, nombre, descripcion from grupo where id_est=1 order by nombre";
											$stidPer = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stidPer))) {
												if( $rowPer['id_gru'] == $row['id_gru']) {
													echo '	<option selected="selected" value="'.$rowPer['id_gru'].'">'.$rowPer['nombre'].'</option>';
												}else{	
													echo '	<option value="'.$rowPer['id_gru'].'">'.$rowPer['nombre'].'</option>';
												}
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><label for="Fmodulo">Módulo: </label></td>
								<td align="left">
									<select class="" name="Fmodulo" id="Fmodulo" req="" lab="Módulo" >
										<option value="">...</option>
										<?php
											# Consulta para obtener los grupos existentes
											$sql="select id_mod, nombre from modulo where 1 order by nombre";
											$stidPer = mysql_query($sql);

											while (($rowPer = mysql_fetch_assoc($stidPer))) {
												echo '	<option value="'.$rowPer['id_mod'].'">'.$rowPer['nombre'].'</option>';
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="2">
									<input type="button" value="Generar reporte" onclick="generar_reporte_pdf(this)" tipo="logs" />
									<input type="reset" value="Limpiar" />
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div><!-- class="boxed-group-inner clearfix" -->
	</div><!-- class="boxed-group" -->
			<?php
		break;
		default:
			echo 'Filtros no disponibles.';

	}

	mysql_close($conn);
?>
<script>
<!--
    $(':input[type=text], input[type=password]').addClass("text ui-widget-content ui-corner-all");
    $("input:submit, input:button, input:reset").button();
//-->
</script>