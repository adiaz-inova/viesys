<?php
	/* 
	* creado			01/08/2012
	* modificado  	04/08/2012
	* autor			  I.S.C. Alejandro Diaz Garcia
	*/
	session_start();
	error_reporting(E_ALL);
	ini_set("display_errors", 0);

	session_destroy();

	header("Location:../index.php");

?>