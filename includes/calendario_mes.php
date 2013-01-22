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

	$sql = "
	SELECT e.evento_id, e.evento_titulo, e.evento_descripcion, e.evento_finicio, e.evento_ftermino, e.evento_zhoraria, e.evento_cattema_id, t.cattema_tema, t.cattema_color,
	DATE_FORMAT(e.evento_finicio, '%e')dia_ini, DATE_FORMAT(e.evento_finicio, '%c')mes_ini, DATE_FORMAT(e.evento_finicio, '%Y')ano_ini, 
	DATE_FORMAT(e.evento_ftermino, '%e')dia_fin, DATE_FORMAT(e.evento_ftermino, '%c')mes_fin, DATE_FORMAT(e.evento_ftermino, '%Y')ano_fin 
	FROM cursos_eventos e LEFT JOIN cursos_cat_temas t ON t.cattema_id = e.evento_cattema_id
	WHERE (DATE_FORMAT(e.evento_finicio, '%c') = ".$mes." 	AND DATE_FORMAT(e.evento_finicio, '%Y') = ".$ano."
	OR DATE_FORMAT(e.evento_ftermino, '%c') = ".$mes." AND DATE_FORMAT(e.evento_ftermino, '%Y') = ".$ano.")
	AND e.evento_estatus='1' 
	ORDER BY e.evento_finicio";
	
	$stid = mysql_query($sql);
	
	if (!$stid)
			die('Hay un error en la consulta: ' . mysql_error());

	$arrayeventos = array();
	$arrayfechainicioeventos = array();
	$arrayfechaterminoeventos = array();
	$i=0;
	while ($row = mysql_fetch_assoc($stid)) {
		$arrayeventos[$i] = array("evento_id"=>$row['evento_id'], "inicio"=>$row['dia_ini'], "fin"=>$row['dia_fin'], "color"=>$row['cattema_color'], "tema"=>$row['cattema_tema'], "titulo"=>$row['evento_titulo'], "descripcion"=>$row['evento_descripcion']);
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
	echo "<div id=divCalend ><table width=100% cellspacing=0 cellpadding=5 border=0 class=mes_body >";
	//calculo el mes y ano del mes anterior
	$mes_anterior = $mes - 1;
	$ano_anterior = $ano;
	if ($mes_anterior==0){
		$ano_anterior--;
		$mes_anterior=12;
	}

	//calculo el mes y ano del mes siguiente
	$mes_siguiente = $mes + 1;
	$ano_siguiente = $ano;
	if ($mes_siguiente==13){
		$ano_siguiente++;
		$mes_siguiente=1;
	}

	echo '<tr>
			    <td width=14% align=center class=cal_mes_labeldiasG>Lunes</td>
			    <td width=14% align=center class=cal_mes_labeldiasG>Martes</td>
			    <td width=14% align=center class=cal_mes_labeldiasG>Miercoles</td>
			    <td width=14% align=center class=cal_mes_labeldiasG>Jueves</td>
			    <td width=14% align=center class=cal_mes_labeldiasG>Viernes</td>
			    <td width=14% align=center class=cal_mes_labeldiasG>Sabado</td>
			    <td width=14% align=center class=cal_mes_labeldiasG>Domingo</td>
			</tr>';
	
	//Variable para llevar la cuenta del dia actual
	$dia_actual = 1;
	
	//calculo el numero del dia de la semana del primer dia
	$numero_dia = calcula_numero_dia_semana(1,$mes,$ano);
	//echo "Numero del dia de demana del primer: $numero_dia <br>";
	
	//calculo el último dia del mes
	$ultimo_dia = ultimoDia($mes,$ano);
	
//escribo la primera fila de la semana
	echo "<tr class=cal_mesbkgnumdia>";
	for ($i=0; $i<7; $i++){

		/*****************************************************/
		# En esta parte del codigo valido si el dia que estoy recorriendo cae dentro de un dia de cursos
		$banderita = false;
		$mensajito='';
		$color='';
		$ev_titulo = '';
		$ev_tema = '';
		$title = '';
		$detalles = '';
		$addevento = '';
		foreach($arrayeventos as $key => $value)
		{
			if($dia_actual >= $value["inicio"] && $dia_actual <= $value["fin"])
			{
				$banderita = true;
				$color = $value["color"];
				$mensajito = 'background-color:'.$color;
				
				$detalles = ' titulo="'.$value["titulo"].'" descripcion="'.$value["descripcion"].'" tema="'.$value["tema"].'"';
				$ev_tema = $value["tema"];
				
				$addevento = ' onclick=";window.location.href=this.getAttribute(\'href\');return false;" href="cursos.php?ev_id='.$value["evento_id"].'" style="cursor:pointer;"';
			}
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
					echo '<td class="da" style="border:solid 2px #E17B51;" valign=top '.$detalles.'><span class="ndia">'.$dia_actual.'</span><div '.$addevento.'>'.$ev_tema.'</div></td>';
				}
				else
				{
					echo '<td class="cal_mesbkgfs" style="'.$mensajito.'" valign=top '.$detalles.'><span class="ndia">'.$dia_actual.'</span><div '.$addevento.'>'.$ev_tema.'</div></td>';
				}
			}
			else
			{		
				if ($dia_actual == $hoy)
				{
					echo '<td class="da" style="border:solid 2px #E17B51;" valign=top '.$detalles.'><span class="ndia">'.$dia_actual.'</span><div '.$addevento.'>'.$ev_tema.'</div></td>';
				}
				else
				{
					echo '<td align="center" style="'.$mensajito.'" valign=top '.$detalles.'><span class="ndia">'.$dia_actual.'</span><div '.$addevento.'>'.$ev_tema.'</div></td>';
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
			echo "<tr class=cal_mesbkgnumdia>";
		//si es el uñtimo de la semana, me pongo al principio de la semana y escribo el </tr>


		/*****************************************************/
		# En esta parte del codigo valido si el dia que estoy recorriendo cae dentro de un dia de cursos
		$banderita = false;
		$mensajito='';
		$color='';
		$ev_titulo = '';
		$ev_tema = '';
		$title = '';
		$detalles = '';
		$addevento = '';
		foreach($arrayeventos as $key => $value)
		{
			if($dia_actual >= $value["inicio"] && $dia_actual <= $value["fin"])
			{
				$banderita = true;
				$color = $value["color"];
				$mensajito = 'background-color:'.$color;
				
				$detalles = ' titulo="'.$value["titulo"].'" descripcion="'.$value["descripcion"].'" tema="'.$value["tema"].'"';
				$ev_tema = $value["tema"];

				$addevento = ' onclick=";window.location.href=this.getAttribute(\'href\');return false;" href="cursos.php?ev_id='.$value["evento_id"].'" style="cursor:pointer;"';
			}
		}
		/*****************************************************/

			if (($numero_dia == 5) || ($numero_dia == 6))
			{
				if ($dia_actual == $hoy)
				{
					echo '<td class="da" style="border:solid 2px #E17B51;" valign=top '.$detalles.'><span class="ndia">'.$dia_actual.'</span><div '.$addevento.'>'.$ev_tema.'</div></td>';
				}
				else
				{
					echo '<td class="cal_mesbkgfs" style="'.$mensajito.'" valign=top '.$detalles.'><span class="ndia">'.$dia_actual.'</span><div '.$addevento.'>'.$ev_tema.'</div></td>';
				}
			}
			else
			{		
				if ($dia_actual == $hoy)
				{
					echo '<td class="da" style="border:solid 2px #E17B51;" valign=top '.$detalles.'><span class="ndia">'.$dia_actual.'</span><div '.$addevento.'>'.$ev_tema.'</div></td>';
				}
				else
				{
					echo '<td align="center" style="'.$mensajito.'" valign=top '.$detalles.'><span class="ndia">'.$dia_actual.'</span><div '.$addevento.'>'.$ev_tema.'</div></td>';
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
	echo "</table></div>";
}	
?>