<?php
	$PaSpOrT = true;
	require('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require('includes/conection.php');

	#se recibe un nombre de funcion se verifica si existe
	// if(isset($funcion) and $function_exists($funcion){
	//   echo("La función existe");
	// }
		
	// mysql_close($conn);
/*
$tabla - nombre de la tabla
$id - id que llevara el select
$name - name que llevara el select
$value - campo que contiene los valores
$desc - campo que contiene la descripcion
*/
if($tabla=='salones')
	construir_select_bd($tabla, $id, $name, $value, $desc);

if($tabla=='clientes')
	construir_select_clientes($tabla, $id, $name, $value, $desc);
function construir_select_clientes($tabla, $id, $name, $value, $desc) {
	global $conn;
	
	$sql="SELECT $value as value, concat(nombre,' ',ape_pat,' ',ape_mat)cliente, empresa FROM $tabla WHERE id_est in(1) ORDER BY nombre";										
	$stid = mysql_query($sql);

	$cliente = '';
	$select = 
	'<select name="'.$name.'" id="'.$id.'">';
	while ($row = mysql_fetch_assoc($stid)) {
		$cliente = (isset($row['cliente']) && trim($row['cliente'])!='')? $row['cliente']:$row['empresa'];
		$select .= '
		<option value="'.$row['value'].'">'.$cliente.'</option>';
	}
	$select .= '
	</select>';

	echo $select;
}#ESTE NOMAS CREA EL SELECT DINAMICO

function construir_select_bd($tabla, $id, $name, $value, $desc) {
	global $conn;
	
	$sql="SELECT $value as value, $desc as nombre FROM $tabla WHERE id_est in(1) ORDER BY nombre";										
	$stid = mysql_query($sql);

	$select = 
	'<select name="'.$name.'" id="'.$id.'">';
	while ($row = mysql_fetch_assoc($stid)) {
		$select .= '
		<option value="'.$row['value'].'">'.$row['nombre'].'</option>';
	}
	$select .= '
	</select>';

	echo $select;
}#ESTE NOMAS CREA EL SELECT DINAMICO
?>
