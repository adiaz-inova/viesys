<?php
/* + + + + + + + + + + + + + + + + + + + + + 
	* creado			08/10/2012
	* modificado  		08/10/2012
	* autor			  I.S.C. Alejandro Diaz Garcia
+ + + + + + + + + + + + + + + + + + + + + */

	/* Leemos archivo de conexion y obtenemos los valores */	
	$vlineas = file($VIE_CONNDB); 
			 
	foreach ($vlineas as $sLinea) 
	{
		if(trim($sLinea) != '')
		{
			//echo $sLinea."<br>"; 
			$arreglo = explode('=', trim($sLinea));
			$var = trim($arreglo[0]);
			$$var = trim($arreglo[1]);
		}
	}

?>