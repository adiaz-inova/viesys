<?php
define('MODULO', 3001);
	/* 
	* modulo			Reportes 
	* creado			01/08/2011
	* modificado		04/08/2011
	* autor			  	I.S.C. Alejandro Diaz Garcia
	*/
	$addruta='../';

	require_once('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require_once('includes/fpdf/fpdf.php');
	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);
	
	class PDF extends FPDF
	{
		
		function Header() //Cabecera de página
		{
			global $title, $subtitle, $subtitle2;
			global $logo_der, $logo_izq, $vie_margen;
			
			if(trim($logo_izq)!='')
				$this->Image($logo_izq,5,8,25,25);
			
			if(trim($logo_der)!='')
				$this->Image($logo_der,185,8,25,25);
			
			$this->SetX($vie_margen);
			$this->SetFont('Times','B',14);
			$this->Cell(0,6, utf8_decode($subtitle),0,1,'C',0);
			$this->SetX($vie_margen);
			$this->SetFont('Times','B',10); //Arial bold 
			$this->Cell(0,6, utf8_decode($title),0,1,'C',0);
			$this->SetX($vie_margen);
			$this->SetFont('Times','',8); //Arial bold 
			$this->Cell(0,4, utf8_decode('Daniel Espinoza No. 5, Mz 16, Lt 5, P. A. Col. Jesús Jiménez Gallardo,'),0,1,'C',0);
			$this->Cell(0,4, utf8_decode('Metepec, Estado de México, CP 52167, Tel. 722 219 9242'),0,1,'C',0);

			if(trim($subtitle2!=''))
			{
				$this->SetX($vie_margen);
				$this->SetFont('Arial','B',10);
				$this->Cell(0,6, utf8_decode($subtitle2),0,1,'C',0);			
			}

			$this->Ln();
			
			$this->SetX($vie_margen);
			$this->SetFont('Arial','',8);
			$this->Cell(0,4, utf8_decode('Fecha de elaboración: ').date('d/m/Y'),0,1,'R',0);
			$this->SetX($vie_margen);
			$this->SetFont('Arial','',8);
			$this->Cell(0,4, utf8_decode('Hora de elaboración: ').date('h:i:s a'),0,1,'R',0);
			$this->Ln();


		}
		
		
		function Footer() //Pie de página
		{
			//include('token.inc.php');
			global $vie_margen;

			$this->SetY(-15); //Posición: a 1,5 cm del final			
			$this->SetFont('Arial','I',8); //Arial italic 8
			$this->Cell(0,10, utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C'); //Número de página
			
			$this->SetX($vie_margen);
			$this->SetY(-15); //Posición: a 1,5 cm del final			
			$this->SetFont('Arial','',8);
			$elaboro = $_SESSION[TOKEN.'NOM_COMPLETO'];
			$this->Cell(0,10, utf8_decode('Elaboró: ').$elaboro,0,1,'L',0);

		}
		
		function Generar()
		{			
			$fill=false; // Datos	
			global $conn, $addsql, $vie_margen, $id;	// Conexion utilizada para la BD
			
			# Configuracion de colores, ancho de linea y fuente en negrita
			$this->SetFillColor(222,26,124);//color lineas
			$this->SetLineWidth(.1);//ancho lineas
			$this->SetFontSize(8);

			// echo 
			$sql="
			select distinct
			ev.id_eve
			, ev.id_emp
			, concat(emp.nombre, ' ', emp.ape_pat, ' ', emp.ape_mat)vendedor
			, emp.tel
			, emp.email
			, ev.id_cli
			, concat(cli.nombre, ' ', cli.ape_pat, ' ', cli.ape_mat)cliente
			, ev.id_sal
			, sal.nombre salon
			, ev.id_tip_eve
			, teve.nombre tipo
			, ev.num_personas personas
			, ev.pagado
			, ev.cos_tot
			, ev.estatus
			, ev.fecha fecha2
			, date_format(ev.falta, '%d/%m/%Y')falta			
			, date_format(ev.fecha, '%d/%m/%Y')fecha
			, date_format(ev.hora, '%T')hora
			from eventos ev 
			inner join tipo_evento teve using( id_tip_eve )
			inner join servicios_eventos seve using( id_eve )
			left join servicios serv using( id_ser )
			left join tipo_servicio tser using( id_tip_ser ) 
			left join facturas fac using( id_eve )
			inner join clientes cli on cli.id_cli=ev.id_cli
			inner join salones sal using( id_sal )
			inner join empleados emp using( id_emp ) 
			where 1 ".$addsql."
			order by id_eve";
			
			$stid = mysql_query($sql);
			
			# Configuracion de colores, ancho de linea y fuente en negrita			
			$this->SetFillColor(255, 255, 255);
			$this->SetDrawColor(128, 128, 128);
			$this->SetTextColor(0,0,0);
			$this->SetFont('','');
			$eventos_encontrados=0;
			while($row = mysql_fetch_assoc($stid)) {
				$eventos_encontrados++;
				$id = $row['id_eve'];

				$this->AddPage('P', 'Letter');
				
				$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode('DATOS DEL EVENTO'), 'B', 0, 'L', 1);
				$this->Ln();				
				$this->Ln();

				$this->SetX($vie_margen);
				
				$no_cotizacion = $row['id_eve'];
				$no_cotizacion = str_pad($no_cotizacion, 5, '0', STR_PAD_LEFT );

				$this->Cell(50, 5, utf8_decode('NÚMERO DE EVENTO: '), 1 ,0 ,'L' ,1);
				$this->Cell(80, 5, utf8_decode($no_cotizacion), 1 ,0 ,'L' ,1);
				$this->Ln();
				$this->Cell(50, 5, utf8_decode('FECHA DE COTIZACIÓN: '), 1 ,0 ,'L' ,1);
				$this->Cell(80, 5, utf8_decode(($row['falta'])), 1 ,0 ,'L' ,1);
				$this->Ln();

				$this->Cell(50, 5, utf8_decode('ESTATUS: '), 1 ,0 ,'L' ,1);
				$this->Cell(80, 5, utf8_decode($row['estatus']), 1 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(50, 5, utf8_decode('NOMBRE DEL CLIENTE: '), 1 ,0 ,'L' ,1);
				$this->Cell(80, 5, utf8_decode($row['cliente']), 1 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(50, 5, utf8_decode('FECHA DEL EVENTO: '), 1 ,0 ,'L' ,1);
				$this->Cell(80, 5, $row['fecha'], 1 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(50, 5, utf8_decode('HORA DEL EVENTO: '), 1, 0, 'L', 1);
				$this->Cell(80, 5, $row['hora'], 1 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(50, 5, utf8_decode('TIPO DE EVENTO: '), 1, 0, 'L', 1);
				$this->Cell(80, 5, utf8_decode($row['tipo']), 1 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(50, 5, utf8_decode('NÚMERO DE INVITADOS: '), 1, 0, 'L', 1);
				$this->Cell(80, 5, number_format($row['personas']), 1 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(50, 5, utf8_decode('SALÓN DE FIESTAS: '), 1 ,0 ,'L' ,1);
				$this->Cell(80, 5, utf8_decode($row['salon']), 1 ,0 ,'L' ,1);
				$this->Ln();
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode('SERVICIOS CONTRATADOS'), 'B', 0, 'L', 1);
				$this->Ln();				
				$this->Ln();


				$this->SetX($vie_margen);
				$this->Cell(10, 5, utf8_decode('#'), 1, 0, 'C', 1);
				$this->Cell(80, 5, utf8_decode('SERVICIO'), 1, 0, 'C', 1);
				$this->Cell(40, 5, utf8_decode('COSTO'), 1, 0, 'C', 1);
				$this->Ln();

				$sql="select ser.id_ser, ser.nombre servicio, (select 1 from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id." LIMIT 1)contratado
								,(select seev.costo from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id." LIMIT 1)costo
								, tser.nombre tipo
								from servicios ser
								inner join tipo_servicio tser using(id_tip_ser)
								where 1=1
								order by tipo, servicio";
				// echo $sql."<br>";
				$stidSer = mysql_query($sql);
				$cont = 0;
				$ctotal = 0;
				while ($rowSer = mysql_fetch_assoc($stidSer)) {
					if($rowSer['contratado']==1) {
						$cont++;
						$servicio = utf8_decode($rowSer['servicio']);
						$costo = number_format($rowSer['costo'], 2, '.', ',');
						$ctotal += $rowSer['costo'];

						$this->SetX($vie_margen);
						$this->Cell(10, 5, $cont, 1, 0, 'C', 1);
						$this->Cell(80, 5, $servicio, 1, 0, 'L', 1);
						$this->Cell(40, 5, $costo, 1, 0, 'R', 1);
						$this->Ln();

						
					}//if
				}//while
				$ctotal = number_format($ctotal, 2, '.', ',');

				if($cont==0) {
					$this->SetX($vie_margen);
					$this->Cell(130, 5, utf8_decode('NO SE ENCONTRARON SERVICIOS CONTRATADOS.'), 1, 0, 'L', 1);
					$this->Ln();
				}else {
					$this->SetX($vie_margen);
					$this->Cell(90, 5, utf8_decode('TOTAL $'), 1, 0, 'R', 1);
					$this->Cell(40, 5, $ctotal, 1, 0, 'R', 1);
					$this->Ln();

				}
						

				$this->Ln();
				$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode('DATOS DEL CONTACTO DE VENTAS'), 'B', 0, 'L', 1);
				$this->Ln();
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(50, 5, utf8_decode('NOMBRE DEL CONTACTO: '), 1, 0, 'L', 1);
				$this->Cell(80, 5, utf8_decode($row['vendedor']), 1 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(50, 5, utf8_decode('TELÉFONO: '), 1, 0, 'L', 1);
				$this->Cell(80, 5, utf8_decode($row['tel']), 1 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(50, 5, utf8_decode('CORREO ELECTRÓNICO: '), 1, 0, 'L', 1);
				$this->Cell(80, 5, utf8_decode($row['email']), 1 ,0 ,'L' ,1);
				$this->Ln();

			}//si hay registros
			

			if($eventos_encontrados==0) {
				$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode('NO SE ENCONTRARON REGISTROS.'), 0 ,0 ,'L' ,1);
			}
							
		} //Generar

	} // Class

	# ---------------------- Inicia ----------------------- #
	# Obtengo parametros	
	$logo_izq = 'images/logotipo-izquierdo.jpg';
	$logo_der = '';

	# Titilo que encabezara el reporte
	$title = "VIE - VERY IMPORTANT EVENTS ";
	
	# titulo del reporte
	$subtitle = "REPORTE DE EVENTOS";

	# Generamos el Query dependiendo a variaciones
	$addsql = "";
	
	$addsql .= (isset($Fevento) && $Fevento!='')? " AND ev.id_eve = ".(int)$Fevento." ":'';
	$addsql .= (isset($Fdesde) && $Fdesde!='' and isset($Fdesde2) && $Fdesde2!='')? " AND ev.fecha >='".$Fdesde."' ":'';
	$addsql .= (isset($Fhasta) && $Fhasta!='' and isset($Fhasta2) && $Fhasta2!='')? " AND ev.fecha <='".$Fhasta."' ":'';
	$addsql .= (isset($Fempleado) && $Fempleado!='')? " AND ev.id_emp =".$Fempleado." ":'';
	$addsql .= (isset($Fsalon) && $Fsalon!='')? " AND ev.id_sal =".$Fsalon." ":'';
	$addsql .= (isset($Fcliente) && $Fcliente!='')? " AND ev.id_cli =".$Fcliente." ":'';
	$addsql .= (isset($Ftipoeve) && $Ftipoeve!='')? " AND teve.id_tip_eve =".$Ftipoeve." ":'';
	$addsql .= (isset($Ftiposer) && $Ftiposer!='')? " AND tser.id_tip_ser =".$Ftiposer." ":'';
	$addsql .= (isset($Fservicio) && $Fservicio!='')? " AND serv.id_ser =".$Fservicio." ":'';
	$addsql .= (isset($Ffactura) && $Ffactura!='')? " AND fac.num_fac = ".(int)$Ffactura." ":'';
	$addsql .= (isset($Festatus) && $Festatus!='')? " AND ev.estatus ='".$Festatus."' ":'';

	#Proceso de generacion de PDF
	$archivo = 'temp/vie_eventos'.date('dmY').'.pdf'; # Nombre del archivo por defecto
	
	$pdf = new PDF();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','',12);
	#$pdf->AddPage('P', 'Letter'); //una hoja carta tiene masomenos 215 de ancho en vertical
	$pdf->Generar();
	$pdf->SetTitle($title);
	$pdf->SetAuthor('VIE 2012');
	$output = "I";
	$pdf->Output($archivo, $output);
	/*
	I: envía el fichero al navegador de forma que se usa la extensión (plug in) si está disponible. El nombre dado en nombre se usa si el usuario escoge la opción "Guardar como..." en el enlace que genera el PDF.
	D: envía el fichero al navegador y fuerza la descarga del fichero con el nombre especificado por nombre.
	F: guarda el fichero en un fichero local de nombre nombre.
	S: devuelve el documento como una cadena. nombre se ignora. 
	*/

	# Verificamos que se genere el archivo
	if($output == "F")
	{
		if (file_exists($archivo)){
			echo '<p>El archivo <a href="'.$archivo.'" target="_blank" class="ligaalreporte">'.$archivo.'</a> se genero exitosamente.</p>';
			// header('Location:'.$archivo);
		}else{
			echo 'Error al generar el archivo <span class="ligaalreporte">'.$archivo.'</span>';
		}
	}


	mysql_close($conn);

 ?>