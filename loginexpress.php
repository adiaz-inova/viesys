<?php
	/* 
	* loginexpress.php
	* creado			30/08/2012
	* modificado  		30/08/2012
	* autor			  I.S.C. Alejandro Diaz Garcia
	* Este modulo validara una session para ejecutar reportes y otros..
	*/

$addruta = (isset($addruta))? $addruta:'';

if(!isset($_SESSION['GEV2_PERFIL_ID']) || !isset($_SESSION['GEV2_ADMINCUENTA']) || !isset($_SESSION['GEV2_USUARIO_ID']) || !isset($_SESSION['GEV2_TIMEOUT_IDLE']))
{
	# idu - id de usuario
	if ( !isset($idu)  || $idu=='' || !isset($del)  || $del=='' || !isset($deledo)  || $deledo=='' || !isset($delrc)  || $delrc=='' || !isset($delmun)  || $delmun==''){
		#faltanParametros ();
	}else
	{	
		$sql=" SELECT lower(U.admusuario_usuario) usuario, UD.admdel_id, DM.edo_cve, DM.mun_cve
		from adm_usuario U
		left join repos_usuariodelegacion UD on UD.admusuario_id=U.admusuario_id
		left join repos_delegacionmunicipio DM on DM.admdel_id=UD.admdel_id
		where U.admusuario_id=".$idu."
		and UD.admdel_id=".$del."
		and DM.edo_cve=".$deledo."
		and DM.mun_cve=".$delmun;

		$stid = pg_query($conn2, $sql);// se pone la 2 porque la uno quedo cerrada
		
		if(!$stid)
			echo '<p>Hay un error en la consulta.</p> ' . pg_last_error();

		$row = pg_fetch_assoc($stid);

		$getUsuario = $row['usuario'];

		$_SESSION['__GEV2_DEL_ID']=$del;
		$_SESSION['__GEV2_EDO_CVE']=$deledo;
		$_SESSION['__GEV2_REGCAT_CVE']=$delrc;
		$_SESSION['__GEV2_MUN_CVE']=$delmun;

		$css_extras='
	  <link href="'.$addruta.'css/redmond/ui.css" rel="stylesheet" type="text/css" />';

		require_once($addruta.'loginexpress.inc.php');		
	}//else	

}else
{
	$passport=true;

	# Tiempo Maximo de Sesión en minutos
	$max = (isset($gev2_tiempo_de_vida_de_sesion) && $gev2_tiempo_de_vida_de_sesion!='')?$gev2_tiempo_de_vida_de_sesion:15;
	$maxinseg = time() + (60 * $max);
	
	# Solo reasigno el tiempo maximo para que no rebote por inactividad
	$_SESSION['GEV2_TIMEOUT_IDLE'] = $maxinseg ;	
}

function faltanParametros () {
		die('<p style="color:#333;">No se han establecido los parametros necesarios para procesar los datos.</p>');
	}
?>