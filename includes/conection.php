<?php
	/* 
	* Conecta a BD
	* conection.php
	* creado			01/10/2012
	* autor 			I.S.C. Alejandro Diaz Garcia
	*/

	$addruta = (isset($addruta))? $addruta:'';

	# Obtenemos datos de configuracion
	$file = $addruta.'configuration.inc.php';
	if (file_exists($file)){
	
		require_once($file);

	}else{

		//session_destroy();
		exit('<h4>Ha ocurrido un error al abrir el archivo de configuracion.<br/>El archivo no existe.</h4>');

	}

# Obtenemos datos de conexion
require_once($addruta.'getdata.php');

$conn = mysql_connect($host,  $user, $pass);
if (!$conn)
    die('Ha ocurrido un error al intentar conectar a la base de datos. ' . mysql_error());

if (!mysql_select_db($bd))
    die("No ha sido posible seleccionar la BD: " . mysql_error());

mysql_set_charset('utf8',$conn); 

#echo "host=".$host." dbname=".$bd." user=".$user." password=".$pass."<br>";
#echo "Se ha logrado conectar a la base de datos satisfactoriamente.";

# Para control de la paginacion	
$vie_grabar = (isset($pag)) ? 0 : 1;
$pag = (int) (!isset($pag) ? 1 : $pag);
$vie_max_regxpag = (int) (!isset($vie_max_regxpag)) ? 30 : $vie_max_regxpag;
$vie_paginicial = ($pag * $vie_max_regxpag) - $vie_max_regxpag;	
?>