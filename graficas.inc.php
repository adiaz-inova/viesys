<?php
define('MODULO', 1100);
	require_once('token.inc.php');
	require_once('includes/getvalues.php');
	require_once('includes/session_principal.php');	
	require_once('includes/conection.php');

	function porcentage($dato, $total) {
		$porcentage = 0;
		$porcentage = ($dato * 100) / $total;

		$porcentage = (int)$porcentage;
		$porcentage .= ' %';

		return $porcentage;
	}

	$addsql = "";
	$ancho_grafica = 360;
	$alto_grafica = 350;
			
	switch($tipo) {
		case 'eventosxestatus':

			$sql="select 
			(select count(*) from eventos ev where ev.estatus in('COTIZADO'))cotizado,
			(select count(*) from eventos ev where ev.estatus in('RECHAZADO'))rechazado,
			(select count(*) from eventos ev where ev.estatus in('VENDIDO'))vendido,
			(select count(*) from eventos ev where ev.estatus in('CANCELADO'))cancelado,
			(select count(*) from eventos ev where ev.estatus in('TERMINADO'))terminado
			";
			$stidPer = mysql_query($sql);
			$row = mysql_fetch_assoc($stidPer);

			$elcienes = 0;
			$string_generado = '';

			if($row) {
				$elcienes = $row['cotizado'] + $row['rechazado'] + $row['vendido'] + $row['cancelado'] + $row['terminado'];
				$string_generado .= "['Cotizado', ".$row['cotizado']."]";
				$string_generado .= ",['Rechazado', ".$row['rechazado']."]";
				$string_generado .= ",['Vendido', ".$row['vendido']."]";
				$string_generado .= ",['Cancelado', ".$row['cancelado']."]";
				$string_generado .= ",['Terminado', ".$row['terminado']."]";

				$cotizado = porcentage($row['cotizado'], $elcienes);
				$cotizado_hay = number_format($row['cotizado']);

				$rechazado = porcentage($row['rechazado'], $elcienes);
				$rechazado_hay = number_format($row['rechazado']);

				$vendido = porcentage($row['vendido'], $elcienes);
				$vendido_hay = number_format($row['vendido']);

				$cancelado = porcentage($row['cancelado'], $elcienes);
				$cancelado_hay = number_format($row['cancelado']);

				$terminado = porcentage($row['terminado'], $elcienes);
				$terminado_hay = number_format($row['terminado']);
					
			}
			?>
			<script type="text/javascript">
		      
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['algo','algo'],
		          <?php echo $string_generado ?>
		        ]);

		        var options = {
		          title: 'EVENTOS POR ESTATUS'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf'));
		        chart.draw(data, options);
		      }
			</script>
			
				
					<table width="100%">
						<tr>
							<td width="50%"><div id="aquiva_lagraf"></div></td>
							<td width="50%"><div id="aquiva_lagraf3"></div></td>
						</tr>
						<tr>
							<td><div id="aquiva_lagraf4"></div></td>
							<td><div id="aquiva_lagraf5"></div></td>
						</tr>
						<tr>
							<td colspan="2"><div id="aquiva_lagraf2"></div></td>
						</tr>
					</table>
					
				
			
			<script type="text/javascript">
		      drawChart();
		    </script>


			<?php
		# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

			$sql="select 
			(select count(*) from eventos ev where ev.estatus in('COTIZADO') and CURDATE() <= ev.fecha )vigentes,
			(select count(*) from eventos ev where ev.estatus in('COTIZADO') and CURDATE() > ev.fecha )vencidas
			";
			$stidPer = mysql_query($sql);
			$string_generado = '';
			while($row = mysql_fetch_assoc($stidPer)) {
				if($string_generado == '') {
					$string_generado = "['Vigentes', ".$row['vigentes']."]";	
				}else {
					$string_generado .= ",['Vencidas', ".$row['vencidas']."]";
				}
				
			}
			?>
			<script type="text/javascript">
		      
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['algo','algo'],
		          <?php echo $string_generado ?>
		        ]);

		        var options = {
		          title: 'COTIZACIONES AL DÍA'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf2'));
		        chart.draw(data, options);
		      }
			</script>
			<script type="text/javascript">
		      drawChart();
		    </script>
			<?php
		# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

			$sql="SELECT concat(clientes.nombre, ' ', clientes.ape_pat, ' ', clientes.ape_mat)cliente, count( * ) eventos
					from eventos 
					inner join clientes using(id_cli)
					where estatus in ('vendido','cancelado','terminado') 
					group by id_cli
					order by eventos desc
					limit 10
			";
			$stidPer = mysql_query($sql);
			$string_generado = '';
			while($row = mysql_fetch_assoc($stidPer)) {
				if($string_generado == '') {
					$string_generado = "['".$row['cliente']."', ".$row['eventos']."]";	
				}else {
					$string_generado .= ",['".$row['cliente']."', ".$row['eventos']."]";
				}
				
			}
			?>
			<script type="text/javascript">    
		      
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['algo','algo'],
		          <?php echo $string_generado ?>
		        ]);

		        var options = {
		          title: 'COTIZACIONES POR CLIENTE'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf3'));
		        chart.draw(data, options);
		      }
			</script>
			<script type="text/javascript">
		      drawChart();
		    </script>
			<?php
		# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

			$sql="SELECT salones.nombre salon, count( * ) eventos
					from eventos 
					inner join salones using(id_sal)
					where estatus in ('vendido','cancelado','terminado') 
					group by id_sal
					order by eventos desc
					limit 10
			";
			$stidPer = mysql_query($sql);
			$string_generado = '';
			while($row = mysql_fetch_assoc($stidPer)) {
				if($string_generado == '') {
					$string_generado = "['".$row['salon']."', ".$row['eventos']."]";	
				}else {
					$string_generado .= ",['".$row['salon']."', ".$row['eventos']."]";
				}
				
			}
			?>
			<script type="text/javascript">    
		      
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['algo','algo'],
		          <?php echo $string_generado ?>
		        ]);

		        var options = {
		          title: 'COTIZACIONES POR SALÓN'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf4'));
		        chart.draw(data, options);
		      }
			</script>
			</div><!-- class="boxed-group" -->
		    <script type="text/javascript">
		      drawChart();
		    </script>
		<?php
		# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

			$sql="SELECT tipo_evento.nombre tipodeevento, count( * ) eventos
					from eventos 
					inner join tipo_evento using(id_tip_eve)
					where estatus in ('vendido','cancelado','terminado') 
					group by id_tip_eve
					order by eventos desc
					limit 10
			";
			$stidPer = mysql_query($sql);
			$string_generado = '';
			while($row = mysql_fetch_assoc($stidPer)) {
				if($string_generado == '') {
					$string_generado = "['".$row['tipodeevento']."', ".$row['eventos']."]";	
				}else {
					$string_generado .= ",['".$row['tipodeevento']."', ".$row['eventos']."]";
				}
				
			}
			?>
			<script type="text/javascript">    
		      
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['algo','algo'],
		          <?php echo $string_generado ?>
		        ]);

		        var options = {
		          title: 'COTIZACIONES POR TIPO DE EVENTO'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf5'));
		        chart.draw(data, options);
		      }
			</script>
		    <script type="text/javascript">
		      drawChart();
		    </script>

			<?php
		break;
		
		default:
			echo 'Filtros no disponibles.';

	}

	mysql_close($conn);
?>
<script>
<!--
    $(':input[type=text], input[type=password]').addClass("text ui-widget-content ui-corner-all");
    $("input:submit, input:button, input:reset").button();
//-->
</script>