<?php
session_start();

require_once('token.inc.php');

$yaestabalogeado = 1;
$addruta='';


if(!isset($_SESSION[TOKEN.'USUARIO_ID']) || !isset($_SESSION[TOKEN.'USUARIO']) || !isset($_SESSION[TOKEN.'NOMBRES']) || !isset($_SESSION[TOKEN.'APATERNO']) || !isset($_SESSION[TOKEN.'AMATERNO']) || !isset($_SESSION[TOKEN.'GRUPO_ID']) || !isset($_SESSION[TOKEN.'SID'])) {

	require_once('login.php');
	exit;
}
else

	$yaestabalogeado = 1; #defino una variable q me dice q el usuario ya ha estado en el sistema


$max = (defined(TIEMPODEVIDA) && TIEMPODEVIDA > 0)? TIEMPODEVIDA : 45;
$maxSeg = time() + (60 * $max);


#var_dump($_SESSION[TOKEN.'PERMISOS'] );

# Si no esta definido el tiempo de expiración (sesion nueva) lo definimos
if (!isset($_SESSION[TOKEN.'TIMEOUT']) ) {
	
		$_SESSION[TOKEN.'TIMEOUT'] = $maxSeg ;
	
}else {

	$tiempoinseg = time();

	if($_SESSION[TOKEN.'TIMEOUT'] < $tiempoinseg  && $yaestabalogeado == 1) {

		require_once('login.php');
		exit;

	}

	else

		$_SESSION[TOKEN.'TIMEOUT'] = $maxSeg ;

} #else


#- - - - - - - - - - - - - VALIDACION DE PERMISOS DE ACCESO
if(!defined('MODULO'))
	define('MODULO', '-1');

/*
echo '<pre>';
print_r($_SESSION[TOKEN.'PERMISOS']);
echo '</pre><--'.MODULO;
*/

$auth_message = '';
if(isset($_SESSION[TOKEN.'PERMISOS']) && !in_array(MODULO, $_SESSION[TOKEN.'PERMISOS']) && !isset($PaSpOrT)) {
	$auth_message = 'NO TIENE PERMISO PARA UTILIZAR ESTE MÓDULO.';
	
}

?>