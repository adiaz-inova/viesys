<?php
function calcula_numero_dia_semana($dia,$mes,$ano){
	$numerodiasemana = date('w', mktime(0,0,0,$mes,$dia,$ano));
	if ($numerodiasemana == 0) 
		$numerodiasemana = 6;
	else
		$numerodiasemana--;
	return $numerodiasemana;
}

//funcion que devuelve el último día de un mes y año dados
function ultimoDia($mes,$ano){ 
    $ultimo_dia=28; 
    while (checkdate($mes,$ultimo_dia + 1,$ano)){ 
       $ultimo_dia++; 
    } 
    return $ultimo_dia; 
} 

function dame_nombre_mes($mes){
	 switch ($mes){
	 	case 1:
			$nombre_mes="Enero";
			break;
	 	case 2:
			$nombre_mes="Febrero";
			break;
	 	case 3:
			$nombre_mes="Marzo";
			break;
	 	case 4:
			$nombre_mes="Abril";
			break;
	 	case 5:
			$nombre_mes="Mayo";
			break;
	 	case 6:
			$nombre_mes="Junio";
			break;
	 	case 7:
			$nombre_mes="Julio";
			break;
	 	case 8:
			$nombre_mes="Agosto";
			break;
	 	case 9:
			$nombre_mes="Septiembre";
			break;
	 	case 10:
			$nombre_mes="Octubre";
			break;
	 	case 11:
			$nombre_mes="Noviembre";
			break;
	 	case 12:
			$nombre_mes="Diciembre";
			break;
	}
	return $nombre_mes;
}

function mostrar_calendario($dia,$mes,$ano){


	// $addsql = ' and CURDATE() <= ev.fecha';
	$sql = "
	select 
	ev.id_eve
	, ev.id_emp
	, ev.id_cli
	, ev.id_sal
	, ev.id_tip_eve
	, ev.num_personas personas
	, ev.pagado
	, ev.estatus
	, ev.cos_tot
	, ev.fecha fecha2
	, date_format(ev.fecha, '%d/%m/%Y')fecha
	, date_format(ev.falta, '%d/%m/%Y')falta
	, date_format(ev.hora, '%h')hora
	, date_format(ev.hora, '%i')minuto
	, teve.nombre tipodeevento
	, sal.nombre salon
	, concat(cli.nombre, ' ', cli.ape_pat, ' ', cli.ape_mat)cliente
	, concat(emp.nombre, ' ', emp.ape_pat, ' ', emp.ape_mat)vendedor
	, case 
			when CURDATE() <= ev.fecha then 'VIGENTE'
			else 'VENCIDA'
			end vigencia
	from eventos ev 
	inner join tipo_evento teve using(id_tip_eve)
	inner join salones sal using(id_sal)
	inner join clientes cli using(id_cli)
	inner join empleados emp using(id_emp)
	where 1=1
	and (estatus in('VENDIDO','COTIZADO','TERMINADO'))
	and (date_format(ev.fecha, '%c') = ".$mes." and date_format(ev.fecha, '%Y') = ".$ano.")
	";
	
	$stid = mysql_query($sql);
	
	if (!$stid)
			die('Hay un error en la consulta: ' . mysql_error());

	$arrayeventos = array();
	$arrayfechainicioeventos = array();
	$arrayfechaterminoeventos = array();
	$i=0;
	while ($row = mysql_fetch_assoc($stid)) {
		
		if(isset($row['estatus']) && $row['estatus']=='COTIZADO') {
			$arrayeventos[$i] = array("evento_id"=>$row['id_eve'], "inicio"=>$row['fecha'], "fin"=>$row['fecha'], "color"=>'#f6d729', "tema"=>'VIE TEMA', "titulo"=>$row['tipodeevento']);
		}
		
		if(isset($row['estatus']) && $row['estatus']=='VENDIDO') {
			$arrayeventos[$i] = array("evento_id"=>$row['id_eve'], "inicio"=>$row['fecha'], "fin"=>$row['fecha'], "color"=>'#1be316', "tema"=>'VIE TEMA', "titulo"=>$row['tipodeevento']);
		}

		if(isset($row['estatus']) and $row['estatus']=='COTIZADO' and $row['vigencia']=='VENCIDA') {
			$arrayeventos[$i] = array("evento_id"=>$row['id_eve'], "inicio"=>$row['fecha'], "fin"=>$row['fecha'], "color"=>'#FEF188', "tema"=>'VIE TEMA', "titulo"=>$row['tipodeevento']);
		}
		
		if(isset($row['estatus']) and $row['estatus']=='VENDIDO' and $row['vigencia']=='VENCIDA') {
			$arrayeventos[$i] = array("evento_id"=>$row['id_eve'], "inicio"=>$row['fecha'], "fin"=>$row['fecha'], "color"=>'#B1D065', "tema"=>'VIE TEMA', "titulo"=>$row['tipodeevento']);
		}

		if(isset($row['estatus']) && $row['estatus']=='TERMINADO') {
			$arrayeventos[$i] = array("evento_id"=>$row['id_eve'], "inicio"=>$row['fecha'], "fin"=>$row['fecha'], "color"=>'#D0F676', "tema"=>'VIE TEMA', "titulo"=>$row['tipodeevento']);
		}

		// COTIZADO
		// vigencia
		// VENCIDA
		// #FEF188

		// VENDIDO
		// vigencia
		// VENCIDA
		// #B1D065
		
		$i++;
	}//while
	
	
	mysql_free_result($stid);
//------------------------

	$mes_hoy=date("m");
	$ano_hoy=date("Y");
	if (($mes_hoy <> $mes) || ($ano_hoy <> $ano))
	{
		$hoy=0;
	}
	else
	{
		$hoy=date("d");
	}
	//tomo el nombre del mes que hay que imprimir
	$nombre_mes = dame_nombre_mes($mes);
	
	//construyo la cabecera de la tabla
	echo "<table cellspacing=0 cellpadding=2 border=0 class=mes_body><tr><td colspan=7 align=center >";//class=tit
	echo "<table width=100% cellspacing=2 cellpadding=2 border=0 class=cal_mes_bkgtit><tr><td >";//style=font-size:10pt;font-weight:bold;color:white
	//calculo el mes y ano del mes anterior
	$mes_anterior = $mes - 1;
	$ano_anterior = $ano;
	if ($mes_anterior==0){
		$ano_anterior--;
		$mes_anterior=12;
	}
	#<a style=color:white;text-decoration:none href=index.php?dia=1&nuevo_mes=$mes_anterior&nuevo_ano=$ano_anterior>&lt;&lt;</a>
	echo "</td>";
#	   echo "<td align=center class=tit>$nombre_mes $ano</td>";
	   echo "<td align=center class=cal_mes_tit><a href=calendario_mes.php?mes=$mes&ano=$ano >$nombre_mes</a></td>";
	   echo "<td align=right style=font-size:10pt;font-weight:bold;color:white>";
	//calculo el mes y ano del mes siguiente
	$mes_siguiente = $mes + 1;
	$ano_siguiente = $ano;
	if ($mes_siguiente==13){
		$ano_siguiente++;
		$mes_siguiente=1;
	}
	#<a style=color:white;text-decoration:none href=index.php?dia=1&nuevo_mes=$mes_siguiente&nuevo_ano=$ano_siguiente>&gt;&gt;</a>
	echo "</td></tr></table></td></tr>";
	/*echo '	<tr>
			    <td width=14% align=center class=cal_mes_labeldias>L</td>
			    <td width=14% align=center class=cal_mes_labeldias>M</td>
			    <td width=14% align=center class=cal_mes_labeldias>M</td>
			    <td width=14% align=center class=cal_mes_labeldias>J</td>
			    <td width=14% align=center class=cal_mes_labeldias>V</td>
			    <td width=14% align=center class=cal_mes_labeldias>S</td>
			    <td width=14% align=center class=cal_mes_labeldias>D</td>
			</tr>';*/
	echo '	<tr>
			    <td width=14% align=center class=cal_mes_labeldias>Lun</td>
			    <td width=14% align=center class=cal_mes_labeldias>Mar</td>
			    <td width=14% align=center class=cal_mes_labeldias>Mie</td>
			    <td width=14% align=center class=cal_mes_labeldias>Jue</td>
			    <td width=14% align=center class=cal_mes_labeldias>Vie</td>
			    <td width=14% align=center class=cal_mes_labeldias>Sab</td>
			    <td width=14% align=center class=cal_mes_labeldias>Dom</td>
			</tr>';
	
	//Variable para llevar la cuenta del dia actual
	$dia_actual = 1;
	
	//calculo el numero del dia de la semana del primer dia
	$numero_dia = calcula_numero_dia_semana(1,$mes,$ano);
	//echo "Numero del dia de demana del primer: $numero_dia <br>";
	
	//calculo el último dia del mes
	$ultimo_dia = ultimoDia($mes,$ano);
	
//escribo la primera fila de la semana
	echo "<tr class=cal_anualbkgnumdia>";
	for ($i=0;$i<7;$i++){

		/*****************************************************/
		# En esta parte del codigo valido si el dia que estoy recorriendo cae dentro de un dia de cursos
		$banderita = false;
		$mensajito='';
		$color='';
		$ev_titulo = '';
		$addevento='<span class="mes_numdia">'.$dia_actual.'</span>';
		$contador=0;
		foreach($arrayeventos as $key => $value)
		{
			
			if($dia_actual == $value["inicio"])
			{
				$contador++;
				$banderita = true;
				$color = $value["color"];
				$mensajito = 'background-color:'.$color;
				$ev_titulo = $value["titulo"];
				$evento_id = $value['evento_id'];
				$ev_fecha = $value['inicio'];
			}

		}
		
		if($contador == 1) {
			$addevento='<a class="fiframe" href="eventos_view.php?task=view&amp;id_eve='.$evento_id.'" title="Ver detalles" alt="Ver detalles">'.$dia_actual.'</a>';
		}elseif ($contador > 1) {
			$mensajito = 'background-color:#FF5C61';
			$addevento='<a href="cotizaciones.php?Fvigencia=&ev_fecha='.$ev_fecha.'" class="mes_numdia">'.$dia_actual.'</a>';
		}
		/*****************************************************/

		if ($i < $numero_dia){
			//si el dia de la semana i es menor que el numero del primer dia de la semana no pongo nada en la celda
			echo "<td></td>";
		} else {
		if (($numero_dia == 5) || ($numero_dia == 6))
		{
			if ($dia_actual == $hoy)
			{
				echo '<td class="da" style="border:solid 2px #E17B51;" valign=middle>'.$addevento.'</td>';
			}
			else
			{
				echo '<td class="cal_mesbkgfs" style="'.$mensajito.'" valign=middle>'.$addevento.'</td>';
			}
		}
		else
		{		
			if ($dia_actual == $hoy)
			{
				echo '<td class="da" style="border:solid 2px #E17B51;" valign=middle>'.$addevento.'</td>';
			}
			else
			{
				echo '<td align="center" style="'.$mensajito.'" valign=middle>'.$addevento.'</td>';
			}
		}
			$dia_actual++;
		}
	}
	echo "</tr>";
	
	//recorro todos los demás días hasta el final del mes
	$numero_dia = 0;
	while ($dia_actual <= $ultimo_dia){

		//si estamos a principio de la semana escribo el <TR>
		if ($numero_dia == 0)
			echo "<tr class=cal_anualbkgnumdia>";
		//si es el uñtimo de la semana, me pongo al principio de la semana y escribo el </tr>

		/*****************************************************/
		# En esta parte del codigo valido si el dia que estoy recorriendo cae dentro de un dia de cursos
		$banderita = false;
		$mensajito='';
		$color='';
		$ev_titulo = '';
		$addevento='<span class="mes_numdia">'.$dia_actual.'</span>';
		$contador=0;
		foreach($arrayeventos as $key => $value)
		{
			
			if($dia_actual == $value["inicio"])
			{
				$contador++;
				$banderita = true;
				$color = $value["color"];
				$mensajito = 'background-color:'.$color;
				$ev_titulo = $value["titulo"];
				$evento_id = $value['evento_id'];
				$ev_fecha = $value['inicio'];
			}

		}
		if($contador == 1) {
			$addevento='<a class="fiframe" href="eventos_view.php?task=view&amp;id_eve='.$evento_id.'" title="Ver detalles" alt="Ver detalles">'.$dia_actual.'</a>';
		}elseif ($contador > 1) {
			$mensajito = 'background-color:#FF5C61';
			$addevento='<a href="cotizaciones.php?Fvigencia=&ev_fecha='.$ev_fecha.'" class="mes_numdia">'.$dia_actual.'</a>';
		}
		/*****************************************************/
			if (($numero_dia == 5) || ($numero_dia == 6))
			{
				if ($dia_actual == $hoy)
				{
					echo '<td class="da" style="border:solid 2px #E17B51;" valign=middle>'.$addevento.'</td>';
				}
				else
				{
					echo '<td class="cal_mesbkgfs" style="'.$mensajito.'" valign=middle>'.$addevento.'</td>';
				}
			}
			else
			{		
				if ($dia_actual == $hoy)
				{
					echo '<td class="da" style="border:solid 2px #E17B51;" valign=middle>'.$addevento.'</td>';
				}
				else
				{
					echo '<td align="center" style="'.$mensajito.'" valign=middle>'.$addevento.'</td>';
				}
			}
			$dia_actual++;
			$numero_dia++;
			if ($numero_dia == 7)
			{
				$numero_dia = 0;
				echo "</tr>";
			}

	}
	
	//compruebo que celdas me faltan por escribir vacias de la última semana del mes
	for ($i=$numero_dia;$i<7;$i++){
		echo "<td></td>";
	}
	
	echo "</tr>";
	echo "</table>";
}	
?>