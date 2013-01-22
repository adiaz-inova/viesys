<?php
define('MODULO', 1200);

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

	/* incluyo mis js extras antes de llamar el header*/
	$js_extras_onready='
	';

	$js='
	function cargar_filtros(objeto, div) {
		var tipo = objeto.getAttribute("tipo");

		divUpd = $("#" + div);

		if(tipo != "") {
			var url = "reportes_filtros.inc.php";
			var param = "tipo="+tipo;

			$("#cargando").show();
			$(divUpd).load(url, param, function(){
				$("#cargando").hide("fast");
			});
		}
	}';

	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);
	
	require_once('includes/html_template.php');

	?>

		<h3>REPORTES</h3>
		<div class="boxed-group-inner clearfix">
			<h4><a href="#" onclick="cargar_filtros(this, 'div_filtros');" tipo="logs">REPORTE DE INGRESOS AL SISTEMA</a></h4>
			<h4><a href="#" onclick="cargar_filtros(this, 'div_filtros');" tipo="eventos">REPORTE DE EVENTOS</a></h4>
		</div><!-- class="boxed-group-inner clearfix" -->
	</div><!-- class="boxed-group" -->
	<div id="div_filtros"></div>


<?php
	mysql_close($conn);
	require_once('includes/html_footer.php');
?>