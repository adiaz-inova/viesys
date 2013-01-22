<?php
define('MODULO', 1000);
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
	require_once('includes/paginacion.php');
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
	if($vie_grabar)
		saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);

	require_once('includes/html_template.php');
	
	switch($task) {
		case 'list': #- - - - - - - - - - - - - - -- - - LISTAR

			$sqlpag = $sql = "select l.ip, l.host, l.browser, l.id_mod, l.id_emp
			, date_format(l.date, '%d/%m/%Y')fecha
			, date_format(l.date, '%r')hora
			, l.date fechacom
			, concat(emp.nombre,' ',emp.ape_pat,' ',emp.ape_mat) empleado
			, mods.nombre modulo
			from log l
			inner join empleados emp using(id_emp)
			inner join modulo mods using(id_mod)
			order by fechacom desc
			";
			
			$sql .= "LIMIT {$vie_paginicial}, {$vie_max_regxpag}";
			$stid = mysql_query($sql);
			?>			

			<h3>REGISTROS DEL SISTEMA (LOG)</h3>
			<div class="boxed-group-inner clearfix">
				<div class="filtro">
					<form id="formulario" name="formulario">
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="log" placeholder="buscar"/>
					</form>
				</div>
				<div class="scrool" id="cont">
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
				</div>
			</div><!-- class="boxed-group-inner clearfix" -->
		<?php
		break;
	}// switch

	mysql_close($conn);
	require_once('includes/html_footer.php');
?>