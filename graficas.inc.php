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
		case 'eventos':

			$sql="SELECT count(*)eventos, nombre as servicio
			FROM  servicios_eventos
			INNER JOIN servicios ser USING(id_ser)
			INNER JOIN eventos ev USING(id_eve)
			WHERE ev.estatus IN ('VENDIDO','TERMINADO','CANCELADO')
			GROUP BY id_ser
			LIMIT 10 ";
			$stidPer = mysql_query($sql);
			$elcienes = 0;
			$string_generado = '';
			$registros = 0;
			while($row = mysql_fetch_assoc($stidPer)) {
				if ($registros==0) 
					$string_generado .= "['".$row['servicio']."', ".$row['eventos']."]";
				else
					$string_generado .= ",['".$row['servicio']."', ".$row['eventos']."]";

				$registros++;
			} $string_generado;
			?>
			<script type="text/javascript">
		      
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['algo','algo'],
		          <?php echo $string_generado ?>
		        ]);

		        var options = {
		          title: 'EVENTOS POR SERVICIOS'
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
			$sql="select 
			(select count(*) from eventos ev where ev.estatus in('VENDIDO','TERMINADO'))vendido,
			(select count(*) from eventos ev where ev.estatus in('CANCELADO'))cancelado
			";
			$stidPer = mysql_query($sql);
			$row = mysql_fetch_assoc($stidPer);

			$elcienes = 0;
			$string_generado = '';

			if($row) {
				$elcienes = $row['vendido'] + $row['cancelado'];
				$string_generado .= "['Vendido', ".$row['vendido']."]";
				$string_generado .= ",['Cancelado', ".$row['cancelado']."]";

				$vendido = porcentage($row['vendido'], $elcienes);
				$vendido_hay = number_format($row['vendido']);

				$cancelado = porcentage($row['cancelado'], $elcienes);
				$cancelado_hay = number_format($row['cancelado']);

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
			<script type="text/javascript">
		      drawChart();
		    </script>
		    <?php
		    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
		    $sql="select 
			(select count(*) from eventos ev where ev.estatus in('VENDIDO','TERMINADO') AND pagado=1)pagado,
			(select count(*) from eventos ev where ev.estatus in('VENDIDO','TERMINADO') AND pagado!=1)nopagado
			";
			$stidPer = mysql_query($sql);
			$row = mysql_fetch_assoc($stidPer);

			$elcienes = 0;
			$string_generado = '';

			if($row) {
				$elcienes = $row['pagado'] + $row['nopagado'];
				$string_generado .= "['Pagado', ".$row['pagado']."]";
				$string_generado .= ",['No pagado', ".$row['nopagado']."]";

				$pagado = porcentage($row['pagado'], $elcienes);
				$pagado_hay = number_format($row['pagado']);

				$nopagado = porcentage($row['nopagado'], $elcienes);
				$nopagado_hay = number_format($row['nopagado']);
					
			}
			?>
			<script type="text/javascript">
		      
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['algo','algo'],
		          <?php echo $string_generado ?>
		        ]);

		        var options = {
		          title: 'EVENTOS POR ESTATUS DE PAGO'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf6'));
		        chart.draw(data, options);
		      }
			</script>
			<script type="text/javascript">
		      drawChart();
		    </script>
		    <?php # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  ?>

			<table width="100%">
				<tr>
					<td width="50%"><div id="aquiva_lagraf"></div></td>
					<td width="50%"><div id="aquiva_lagraf6"></div></td>
				</tr>
				<tr>
					<td><div id="aquiva_lagraf4"></div></td>
					<td><div id="aquiva_lagraf5"></div></td>
				</tr>
				<tr>
					<td><div id="aquiva_lagraf3"></div></td>
					<td><div id="aquiva_lagraf2"></div></td>
				</tr>
			</table>
			<?php
			# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

			$sql="select 
			(select count(*) from eventos ev where ev.estatus in('VENDIDO') and CURDATE() <= ev.fecha )vigentes,
			(select count(*) from eventos ev where ev.estatus in('VENDIDO') and CURDATE() > ev.fecha )vencidas
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
		          title: 'EVENTOS AL DÍA'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf2'));
		        chart.draw(data, options);
		      }
			</script>
			<script type="text/javascript">
		      // drawChart();
		    </script>
			<?php
			# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

			$sql="SELECT count(*)eventos, IF(empresa='',CONCAT(clientes.nombre, ' ', clientes.ape_pat, ' ', clientes.ape_mat),empresa)dato
				FROM clientes
				INNER JOIN eventos ev USING(id_cli)
				WHERE ev.estatus IN ('VENDIDO','TERMINADO')
				GROUP BY empresa, id_cli
				LIMIT 10";

			$stidPer = mysql_query($sql);
			$string_generado = '';
			while($row = mysql_fetch_assoc($stidPer)) {
				if($string_generado == '') {
					$string_generado = "['".$row['dato']."', ".$row['eventos']."]";	
				}else {
					$string_generado .= ",['".$row['dato']."', ".$row['eventos']."]";
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
		          title: 'EVENTOS POR CLIENTE'
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
					and salones.id_est=1
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
		          title: 'EVENTOS POR SALÓN'
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
		          title: 'EVENTOS POR TIPO DE EVENTO'
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
		case 'cotizaciones':
			$sql="
			SELECT count(*)eventos, nombre as servicio
			FROM  servicios_eventos
			INNER JOIN servicios ser USING(id_ser)
			INNER JOIN eventos ev USING(id_eve)
			WHERE ev.estatus IN ('COTIZADO','RECHAZADO')
			GROUP BY id_ser
			LIMIT 10 ";
			$stidPer = mysql_query($sql);
			$elcienes = 0;
			$string_generado = '';
			$registros = 0;
			while($row = mysql_fetch_assoc($stidPer)) {
				if ($registros==0) 
					$string_generado .= "['".$row['servicio']."', ".$row['eventos']."]";
				else
					$string_generado .= ",['".$row['servicio']."', ".$row['eventos']."]";

				$registros++;
			} $string_generado;
			?>
			<script type="text/javascript">
		      
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['algo','algo'],
		          <?php echo $string_generado ?>
		        ]);

		        var options = {
		          title: 'COTIZACIONES POR SERVICIOS'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf3'));
		        chart.draw(data, options);
		      }
			</script>
			<script type="text/javascript">
		      drawChart();
		    </script>
		    <?php
		    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - cotizaciones
		    $sql="select 
			(select count(*) from eventos ev where ev.estatus in('COTIZADO'))cotizado,
			(select count(*) from eventos ev where ev.estatus in('RECHAZADO'))rechazado
			";
			$stidPer = mysql_query($sql);
			$row = mysql_fetch_assoc($stidPer);

			$elcienes = 0;
			$string_generado = '';

			if($row) {
				$elcienes = $row['cotizado'] + $row['rechazado'];
				$string_generado .= "['Cotizado', ".$row['cotizado']."]";
				$string_generado .= ",['Rechazado', ".$row['rechazado']."]";

				$cotizado = porcentage($row['cotizado'], $elcienes);
				$cotizado_hay = number_format($row['cotizado']);

				$rechazado = porcentage($row['rechazado'], $elcienes);
				$rechazado_hay = number_format($row['rechazado']);
					
			}
			?>
			<script type="text/javascript">
		      
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['algo','algo'],
		          <?php echo $string_generado ?>
		        ]);

		        var options = {
		          title: 'COTIZACIONES POR ESTATUS'
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf6'));
		        chart.draw(data, options);
		      }
			</script>
			<script type="text/javascript">
		      drawChart();
		    </script>
		    <?php # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  ?>

			<table width="100%">
				<tr>
					<td width="50%"><div id="aquiva_lagraf"></div></td>
					<td width="50%"><div id="aquiva_lagraf6"></div></td>
				</tr>
				<tr>
					<td><div id="aquiva_lagraf4"></div></td>
					<td><div id="aquiva_lagraf5"></div></td>
				</tr>
				<tr>
					<td><div id="aquiva_lagraf3"></div></td>
					<td><div id="aquiva_lagraf2"></div></td>
				</tr>
			</table>
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
		      // drawChart();
		    </script>
			<?php
			# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

			$sql="SELECT count(*)eventos, IF(empresa='',CONCAT(clientes.nombre, ' ', clientes.ape_pat, ' ', clientes.ape_mat),empresa)dato
				FROM clientes
				INNER JOIN eventos ev USING(id_cli)
				WHERE estatus IN ('cotizado','rechazado') 
				GROUP BY empresa, id_cli
				LIMIT 10";

			$stidPer = mysql_query($sql);
			$string_generado = '';
			while($row = mysql_fetch_assoc($stidPer)) {
				if($string_generado == '') {
					$string_generado = "['".$row['dato']."', ".$row['eventos']."]";	
				}else {
					$string_generado .= ",['".$row['dato']."', ".$row['eventos']."]";
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

		        var chart = new google.visualization.PieChart(document.getElementById('aquiva_lagraf'));
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
					where estatus in ('cotizado','rechazado') 
					and salones.id_est=1
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
					where estatus in ('cotizado','rechazado') 
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