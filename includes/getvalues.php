<?php
/* + + + + + + + + + + + + + + + + + + + + + 
	* VIE 2012
	* creado			08/10/2012
	* autor			  	I.S.C. Alejandro Diaz Garcia
	* getvalues.php
	* RECOJE LOS VALORES ENVIADOS POR GET Y POST
+ + + + + + + + + + + + + + + + + + + + + */
	
	/***VARIABLES POR GET ***/
	$numero = count($_GET);
	$tags = array_keys($_GET);// obtiene los nombres de las varibles
	$valores = array_values($_GET);// obtiene los valores de las varibles

	// arreglo de caracteres prohibidos
	$strBuscar     = array("'");
	$strReemplazar = array("");

	$url_parametros = ''.$_SERVER['PHP_SELF'].'?';
			
	// crea las variables y les asigna el valor
	for($i=0;$i<$numero;$i++){
		if(!is_array($valores[$i])){
			$valores[$i] = trim($valores[$i]);
			$valores[$i] = str_replace($strBuscar, $strReemplazar, $valores[$i]);
			$$tags[$i]=$valores[$i];

			#$url_parametros .= $tags[$i].'='.$valores[$i].'&';
		}
	}
	
	/***VARIABLES POR POST ***/
	$numero2 = count($_POST);
	$tags2 = array_keys($_POST); // obtiene los nombres de las varibles
	$valores2 = array_values($_POST);// obtiene los valores de las varibles
	
	// crea las variables y les asigna el valor
	for($i=0;$i<$numero2;$i++){
		if(!is_array($valores2[$i])){
			$valores2[$i] = trim($valores2[$i]);
			$valores2[$i] = str_replace($strBuscar, $strReemplazar, $valores2[$i]);
			$$tags2[$i]=$valores2[$i];

			#$url_parametros .= $tags[$i].'='.$valores[$i].'&';
		}
	}

	
#echo '$pag='.$pag.' -- $vie_max_regxpag='.$vie_max_regxpag;

	//Funcion para formatear las cantidades monetarias
	function formateo($original) {
		$formateada = number_format($original, 2, '.', '');

		return $formateada;
	}

	function formatInput($original) {
		$formateada = number_format($original, 2, '.', '');

		return $formateada;
	}

	function saveLog($conn, $mod, $emp) {

		$ip = $_SERVER['REMOTE_ADDR'];
		$hostname = gethostbyaddr($ip);

		$browserinfo = $_SERVER['HTTP_USER_AGENT'];

		$qry = "INSERT INTO log (ip, host, browser, id_mod, id_emp, date) VALUES ('$ip', '$hostname', '$browserinfo', $mod, $emp, now())";


		$stid = mysql_query($qry, $conn);
		//$op = mysql_affected_rows();

	}

	function dame_la_fecha($fecha=null) {
			date_default_timezone_set('America/Mexico_City');
			$dias = Array('', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
			$mes = Array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"); 
			
			if($fecha==null) {
				$fechahoy = ''.$dias[date('N')].' '.date('j').' de '.$mes[date('n')].' de '.date('Y').'';
			}else{
				#echo $fecha;
				list($Xdia, $Xmes, $Xano) = explode('/', $fecha);

				$Xdia = (int)$Xdia;
				$Xmes = (int)$Xmes;

				$fechahoy = $mes[$Xmes].' de '.$Xano.'';
			}

			return $fechahoy;
	}
?>