<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session

require_once('includes/conection.php');
require_once('includes/session_principal.php');
# AQUI VAMOS A METER LOS MENSAJES PARA LOS USUARIOS

require_once('includes/html_template.php');
function cot_pen() {
	global $conn;

	$add_sql = " and ev.id_emp = ".$_SESSION[TOKEN.'USUARIO_ID']." ";

	$sql = "select ev.id_eve id
			, ev.id_tip_eve
			, date_format(ev.fecha, '%d/%m/%Y')fecha
			, case 
			when CURDATE() <= ev.fecha then 'VIGENTE'
			else 'VENCIDA'
			end estatus
			, ev.hora
			, ev.num_personas personas
			, ev.pagado
			, ev.cos_tot
			, sal.nombre salon
			, concat(cli.nombre, ' ', cli.ape_pat) cliente
			, cli.tel
			, cli.email
			, tev.nombre tipo
			from eventos ev
			inner join salones sal using(id_sal)
			inner join clientes cli using(id_cli)
			inner join tipo_evento tev using(id_tip_eve)
			where 1=1 and ev.estatus = 'COTIZADO'
			".$add_sql."
			order by ev.fecha";
	
	$stid = mysql_query($sql);

	$var_eventos = '
	<table width="100%" cellpadding="5" cellspacing="0" class="general">
		<tr>
			<th>FECHA</th>
			<th>EVENTO</th>
			<th>CLIENTE</th>
			<th>TELÉFONO</th>
			<th>EMAIL</th>
			<th>DETALLE</th>		
		</tr>';

	$registros = 0;
	while($row = mysql_fetch_assoc($stid)) {
		if($row['estatus'] == 'VIGENTE') {

			$id = $row['id'];
			$notas = '<a class="fiframe" href="notas.php?id_eve='.$id.'">VER NOTAS</a>';
			$ver_evento = '<a class="fiframe" href="eventos_view.php?task=view&id_eve='.$id.'" title="Ver detalles" alt="Ver detalles">VER</a>';

			$var_eventos .= '
		<tr>
			<td align="center">'.$row['fecha'].'</td>
			<td align="center">'.$row['tipo'].'</td>
			<td align="center">'.$row['cliente'].'</td>
			<td align="center">'.$row['tel'].'</td>
			<td align="center">'.$row['email'].'</td>
			<td align="center">'.$ver_evento.'</td>
		</tr>';
		
			$registros++;
		}
		
		
	}//while

	$var_eventos .= '
	</table>';

	$devolver='';
	if($registros > 0) {
		$devolver =  '
		<div class="vie_seccion_inicial">
			<h3>'.$_SESSION[TOKEN.'NOMBRES'].' tienes cotizaciones que no han sido cerradas y aun estan vigentes.</h3>';
		$devolver .= $var_eventos;
		$devolver .= '
		</div>';
	}
		

	return $devolver;
}

function cot_pen_admin() {
	global $conn;

	$add_sql = " and ev.id_emp <> ".$_SESSION[TOKEN.'USUARIO_ID']." ";

	$sql = "select count(*)eventos, concat(emp.nombre,' ',emp.ape_pat,' ',emp.ape_mat) empleado
		from eventos ev
		inner join empleados emp using(id_emp)
		where 1=1
		and ev.estatus = 'COTIZADO'
		and CURDATE() <= ev.fecha
		".$add_sql."
		group by ev.id_emp, empleado
		order by empleado";
	
	$stid = mysql_query($sql);

	$var_eventos = '
	<table width="100%" cellpadding="5" cellspacing="0" class="general">
		<tr>
			<th>EMPLEADO</th>
			<th>EVENTOS</th>
		</tr>';

	$registros = 0;
	while($row = mysql_fetch_assoc($stid)) {

		$var_eventos .= '
		<tr>
			<td align="left">'.$row['empleado'].'</td>
			<td align="center">'.number_format($row['eventos']).'</td>
		</tr>';
		
		$registros++;		
		
	}//while

	$var_eventos .= '
	</table>';

	$devolver='';
	if($registros > 0) {
		$devolver =  '
		<div class="vie_seccion_inicial">
			<h3>Listado de cotizaciones pendientes por empleados.</h3>';
		$devolver .= $var_eventos;
		$devolver .= '
		</div>';
	}
		

	return $devolver;
}

# - - - - - - - - - - - - - - - - - - - - - - - - 
function eve_pago_pen() {
	global $conn;
	
	$add_sql = " and ev.id_emp = ".$_SESSION[TOKEN.'USUARIO_ID']." ";
	
	$sql = "select ev.id_eve id
			, ev.id_tip_eve
			, date_format(ev.fecha, '%d/%m/%Y')fecha
			, case 
			when CURDATE() <= ev.fecha then 'VIGENTE'
			else 'VENCIDA'
			end estatus
			, ev.hora
			, ev.num_personas personas
			, ev.pagado
			, ev.cos_tot
			, sal.nombre salon
			, concat(cli.nombre, ' ', cli.ape_pat) cliente
			, cli.tel
			, cli.email
			, tev.nombre tipo
			from eventos ev
			inner join salones sal using(id_sal)
			inner join clientes cli using(id_cli)
			inner join tipo_evento tev using(id_tip_eve)
			where 1=1 and ev.estatus = 'VENDIDO'
			".$add_sql."
			and ev.pagado = 0
			order by ev.fecha";
			
	$stid = mysql_query($sql);

	$var_eventos = '
	<table width="100%" cellpadding="5" cellspacing="0" class="general">
		<tr>
			<th>FECHA</th>
			<th>EVENTO</th>
			<th>CLIENTE</th>
			<th>TELÉFONO</th>
			<th>EMAIL</th>
			<th>DETALLE</th>		
		</tr>';

	$registros = 0;
	while($row = mysql_fetch_assoc($stid)) {

			$id = $row['id'];
			$notas = '<a class="fiframe" href="notas.php?id_eve='.$id.'">VER NOTAS</a>';
			$ver_evento = '<a class="fiframe" href="eventos_view.php?task=view&id_eve='.$id.'" title="Ver detalles" alt="Ver detalles">VER</a>';

			$var_eventos .= '
		<tr>
			<td align="center">'.$row['fecha'].'</td>
			<td align="center">'.$row['tipo'].'</td>
			<td align="center">'.$row['cliente'].'</td>
			<td align="center">'.$row['tel'].'</td>
			<td align="center">'.$row['email'].'</td>
			<td align="center">'.$ver_evento.'</td>
		</tr>';
		
		$registros++;
	}//while

	$var_eventos .= '
	</table>';

	$devolver='';
	if($registros > 0) {
		$devolver = '
		<div class="vie_seccion_inicial">
			<h3>'.$_SESSION[TOKEN.'NOMBRES'].' tienes eventos con pagos que aun no han sido cubiertos.</h3>';	
		$devolver .= $var_eventos;
		$devolver .= '
		</div>';
	}
	
	return $devolver;
}

function eve_pago_pen_admin() {
	global $conn;

	$add_sql = " and ev.id_emp <> ".$_SESSION[TOKEN.'USUARIO_ID']." ";
	$sql = "select count(*)eventos, concat(emp.nombre,' ',emp.ape_pat,' ',emp.ape_mat) empleado
		from eventos ev
		inner join empleados emp using(id_emp)
		where 1=1
		and ev.estatus = 'VENDIDO'
		and CURDATE() <= ev.fecha
		and ev.pagado = 0
		".$add_sql."
		group by ev.id_emp, empleado
		order by empleado";
	
	$stid = mysql_query($sql);

	$var_eventos = '
	<table width="100%" cellpadding="5" cellspacing="0" class="general">
		<tr>
			<th>EMPLEADO</th>
			<th>EVENTOS</th>
		</tr>';

	$registros = 0;
	while($row = mysql_fetch_assoc($stid)) {

		$var_eventos .= '
		<tr>
			<td align="left">'.$row['empleado'].'</td>
			<td align="center">'.number_format($row['eventos']).'</td>
		</tr>';
		
		$registros++;		
		
	}//while

	$var_eventos .= '
	</table>';

	$devolver='';
	if($registros > 0) {
		$devolver =  '
		<div class="vie_seccion_inicial">
			<h3>Listado de cotizaciones pendientes por empleados.</h3>';
		$devolver .= $var_eventos;
		$devolver .= '
		</div>';
	}
		

	return $devolver;
}

# - - - - - - - - - - - - - - - - - - - - - - - - 
function mi_log() {
	global $conn;
	$sql = "select l.ip, l.host, l.browser, l.id_mod, l.id_emp
			, date_format(l.date, '%d/%m/%Y')fecha
			, date_format(l.date, '%r')hora
			, l.date fechacom
			, concat(emp.nombre,' ',emp.ape_pat,' ',emp.ape_mat) empleado
			, mods.nombre modulo
			from log l
			inner join empleados emp using(id_emp)
			inner join modulo mods using(id_mod)
			where emp.id_emp = ".$_SESSION[TOKEN.'USUARIO_ID']."
			order by fechacom desc
			limit 10";
			
	$stid = mysql_query($sql);

	$var_log = '
	<table width="100%" cellpadding="5" cellspacing="0" class="general">
		<tr>
			<th>FECHA</th>
			<th>HORA</th>
			<th>ACTIVIDAD</th>
		</tr>';

	$registros = 0;
	while($row = mysql_fetch_assoc($stid)) {
			$var_log .= '
		<tr>
			<td align="center">'.$row['fecha'].'</td>
			<td align="center">'.$row['hora'].'</td>
			<td align="center">'.$row['modulo'].'</td>
		</tr>';	
		$registros++;
		
	}//while

	$var_log .= '
	</table>';

	$devolver='';
	if($registros > 0) {
		$devolver .= '
		<div class="vie_seccion_inicial">
			<h3>'.$_SESSION[TOKEN.'NOMBRES'].' estos son tus ultimos movimientos en el sistema.</h3>';
			$devolver .= $var_log;
			$devolver .= '
		</div>';
	}

	return $devolver;
}

# - - - - - tus ventas pendientes

	echo cot_pen();

	if(isset($_SESSION[TOKEN.'ADMIN']) && $_SESSION[TOKEN.'ADMIN']) {
		echo cot_pen_admin();
	}
	
	echo eve_pago_pen();

	if(isset($_SESSION[TOKEN.'ADMIN']) && $_SESSION[TOKEN.'ADMIN']) {
		echo eve_pago_pen_admin();
	}
	

# - - - - - tu actividad
	echo mi_log();


?>
<div id="logo_inicial">
	<img src="images/logo-inicial.png">
</div>
<?php
	mysql_close($conn);
	require_once('includes/html_footer.php');
?>
