<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session
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
		          title: 'GRÁFICA DE EVENTOS POR ESTATUS'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf'));
		        chart.draw(data, options);
		      }
			</script>
			<div class="boxed-group">
				<h3>GRÁFICA DE EVENTOS POR ESTATUS</h3>
				<div class="boxed-group-inner clearfix">
					<div id="aquiva_lagraf"></div>
				</div><!-- class="boxed-group-inner clearfix" -->
			</div><!-- class="boxed-group" -->
			<script type="text/javascript">
		      drawChart();
		    </script>


			<?php
		break;
		case 'cotizacioneshoy':

			$sql="select 
			(select count(*) from eventos ev where ev.estatus in('COTIZADO') and curdate() <= ev.fecha )vigentes,
			(select count(*) from eventos ev where ev.estatus in('COTIZADO') and curdate() > ev.fecha )vencidas
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
		          //title: 'My Daily Activities'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf'));
		        chart.draw(data, options);
		      }
			</script>
			<div class="boxed-group">
				<h3>GRÁFICA DE COTIZACIONES AL DÍA</h3>
				<div class="boxed-group-inner clearfix">
					<div id="aquiva_lagraf"></div>
				</div><!-- class="boxed-group-inner clearfix" -->
			</div><!-- class="boxed-group" -->
			<script type="text/javascript">
		      drawChart();
		    </script>
			<?php
		break;
		case 'eventosxcliente':

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
		          //title: 'My Daily Activities'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf'));
		        chart.draw(data, options);
		      }
			</script>
			<div class="boxed-group">
				<h3>GRÁFICA DE COTIZACIONES POR CLIENTE</h3>
				<div class="boxed-group-inner clearfix">
					<div id="aquiva_lagraf"></div>
				</div><!-- class="boxed-group-inner clearfix" -->
			</div><!-- class="boxed-group" -->
			<script type="text/javascript">
		      drawChart();
		    </script>
			<?php
		break;
		case 'eventosxsalon':

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
		          //title: 'My Daily Activities'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf'));
		        chart.draw(data, options);
		      }
			</script>
			<div class="boxed-group">
				<h3>GRÁFICA DE COTIZACIONES POR SALÓN</h3>
				<div class="boxed-group-inner clearfix">
					<div id="aquiva_lagraf"></div>
				</div><!-- class="boxed-group-inner clearfix" -->
			</div><!-- class="boxed-group" -->
		    <script type="text/javascript">
		      drawChart();
		    </script>
		<?php
		break;
		/*case 'eventosxtipo':

			$sql="SELECT tipo_evento.nombre salon, count( * ) eventos
					from eventos 
					inner join tipo_evento using(id_tip_eve)
					where estatus in ('vendido','cancelado','terminado') 
					group by id_tip_eve
					order by eventos desc
					limit 10
			";
			$stidPer = mysql_query($sql);
			$registros = 0;
			$elcienes = 0;
			while($row = mysql_fetch_assoc($stidPer)) {
				
				$elcienes += $row['eventos'];
					
			}			
			?>
			<table style="display:block; " border="1" id="tgra">
	            <tbody>
			<?php
			$sql="SELECT tipo_evento.nombre salon, count( * ) eventos
					from eventos 
					inner join tipo_evento using(id_tip_eve)
					where estatus in ('vendido','cancelado','terminado') 
					group by id_tip_eve
					order by eventos desc
					limit 10
			";
			$stidPer = mysql_query($sql);
			while($row = mysql_fetch_assoc($stidPer)) {
				

				$vigentes = porcentage(($row['eventos']), $elcienes);
				$vigentes_hay = number_format($row['eventos']);
				?>
	                <tr>
	                    <th scope="row"><?php echo $row['salon'].' '.$vigentes_hay.'('.$vigentes.')'; ?></th>
	                    <td><?php echo $vigentes ?></td>
	                </tr>
				<?php	
			}
			?>
	            </tbody>
	        </table>
			<script type="text/javascript">
			$(function () {
			    var values = [],
			        labels = [];
			    $("tr").each(function () {
			        values.push(parseInt($("td", this).text(), 10));
			        labels.push($("th", this).text());
			    });
			    $("table#tgra").hide();
			    Raphael("aquiva_lagraf", <?php echo $ancho_grafica ?>, <?php echo $alto_grafica ?>).pieChart(350, 160, 100, values, labels, "#fff");
			});
			</script>
			<div class="boxed-group">
				<h3>GRÁFICA DE COTIZACIONES POR TIPO DE EVENTO</h3>
				<div class="boxed-group-inner clearfix">
					<div id="aquiva_lagraf"></div>
				</div><!-- class="boxed-group-inner clearfix" -->
			</div><!-- class="boxed-group" -->
			<?php
		break;*/
		case 'eventosxtipo':

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
		          //title: 'My Daily Activities'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf'));
		        chart.draw(data, options);
		      }
			</script>
			<div class="boxed-group">
				<h3>GRÁFICA DE COTIZACIONES POR TIPO DE EVENTO</h3>
				<div class="boxed-group-inner clearfix">
					<div id="aquiva_lagraf"></div>
				</div><!-- class="boxed-group-inner clearfix" -->
			</div><!-- class="boxed-group" -->
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