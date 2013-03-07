<?php
define('MODULO', 1100);
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
			var url = "graficas.inc.php";
			var param = "tipo="+tipo;

			$("#cargando").show();
			$(divUpd).load(url, param, function(){
				$("#cargando").hide("fast");
			});
		}
	}
	';

	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);

	$js_extras = '
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages: ["corechart"]});
    </script>';
	require_once('includes/html_template.php');

	?>

		<h3>GRÁFICAS</h3>
		<div class="boxed-group-inner clearfix">

			<ul class="expanded section-nav" style="list-style-type: none;">
				<li>
					<a href="#" onclick="cargar_filtros(this, 'div_filtros');" tipo="eventos">VISUALIZAR MONITOR DE EVENTOS</a>
				</li>
				<li>
					<a href="#" onclick="cargar_filtros(this, 'div_filtros');" tipo="cotizaciones">VISUALIZAR MONITOR DE COTIZACIONES</a>
				</li>
				<!--li>
					<a href="#" onclick="cargar_filtros(this, 'div_filtros');" tipo="eventosxcliente">GRÁFICA DE EVENTOS POR CLIENTE</a>
				</li>
				<li>
					<a href="#" onclick="cargar_filtros(this, 'div_filtros');" tipo="eventosxsalon">GRÁFICA DE EVENTOS POR SALÓN</a>
				</li>
				<li>
					<a href="#" onclick="cargar_filtros(this, 'div_filtros');" tipo="eventosxtipo">GRÁFICA DE EVENTOS POR TIPO DE EVENTO</a>
				</li-->
			</ul>
			<div id="div_filtros"></div>

		</div><!-- class="boxed-group-inner clearfix" -->
	</div><!-- class="boxed-group" -->
	


<?php
	mysql_close($conn);
	require_once('includes/html_footer.php');
?>