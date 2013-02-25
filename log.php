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
	require_once('includes/session_principal.php');
	
	if( !isset($task) || trim($task) == '')
		$task = 'list';

	$css_extras = '
	<link rel="stylesheet" type="text/css" media="screen" href="jqSuitePHP_4_4_4_0/themes/redmond/jquery-ui-custom.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="jqSuitePHP_4_4_4_0/themes/ui.jqgrid.css" />
	';

	$js_extras = '
	<script src="jqSuitePHP_4_4_4_0/js/i18n/grid.locale-es.js" type="text/javascript"></script>
	<script src="jqSuitePHP_4_4_4_0/js/jquery.jqGrid.min.js" type="text/javascript"></script>
	';

	require_once('includes/conection.php');
	# Grabamos en el log
	if($vie_grabar)
		saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);

	require_once('includes/html_template.php');
	
	switch($task) {
		case 'list': #- - - - - - - - - - - - - - -- - - LISTAR

			echo '
			<h3>REGISTROS DEL SISTEMA (LOG)</h3>
			<div class="boxed-group-inner clearfix">
				';
			include "llenagrid_log.php";
			echo '</div><!-- class="boxed-group-inner clearfix" -->';
			echo '<button id="getselected">Seleccionar</button>';
			//echo '<button id="setselection">Select Row 425</button>';
		break;
	}// switch

	//mysql_close($conn);
	require_once('includes/html_footer.php');
?>