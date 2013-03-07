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
				$this->Image($logo_izq,25,0,45,45);
			
			if(trim($logo_der)!='')
				$this->Image($logo_der,185,8,25,25);
			
			/*$this->SetX($vie_margen);
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
			}*/

			$this->SetY(45);
			//$this->Ln();
			
			$this->SetX($vie_margen);
			$this->SetFont('Arial','',10);
			// $this->Cell(0,4, dame_la_fecha(),0,1,'R',0);
			/*
			$this->SetX($vie_margen);
			$this->SetFont('Arial','',8);
			$this->Cell(0,4, utf8_decode('Hora de elaboración: ').date('h:i:s a'),0,1,'R',0);
			*/
			$this->Ln();
			$this->Ln();


		}
		
		
		function Footer() //Pie de página
		{
			//include('token.inc.php');
			global $vie_margen;

			#$this->SetY(-15); //Posición: a 1,5 cm del final			
			#$this->SetFont('Arial','I',8); //Arial italic 8
			#$this->Cell(0,10, utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C'); //Número de página
			
			$this->SetX($vie_margen);
			$this->SetY(-15); //Posición: a 1,5 cm del final			
			$this->SetFont('Arial','',8);
			$this->SetTextColor(179, 179, 179);#B3B3B3

			$this->Cell(0,3, utf8_decode('Oficinas: Daniel Espinoza No.5, Mz 16, Lt 5, P.A. Col. Jesús Jiménez Gallardo,'),0,1,'C',0);
			$this->Cell(0,3, utf8_decode('C.P. 52167 Metepec, Estado de México, Conmutador (con 3 líneas) 722 219 9242,'),0,1,'C',0);
			$this->Cell(0,3, utf8_decode('Email: ventas@vie.com.mx'),0,1,'C',0);

		}
		
		function Generar()
		{			
			$fill=false; // Datos	
			global $conn, $addsql, $vie_margen, $id, $tipo;	// Conexion utilizada para la BD
			
			# Configuracion de colores, ancho de linea y fuente en negrita
			$this->SetFillColor(222,26,124);//color lineas
			$this->SetLineWidth(.1);//ancho lineas
			#$this->SetFontSize(8);
			$this->SetFont('Arial','',10);

			$sql="
			select 
			ev.id_eve
			, ev.id_emp
			, concat(emp.nombre, ' ', emp.ape_pat, ' ', emp.ape_mat)vendedor
			, emp.tel
			, emp.email
			, ev.id_cli
			, concat(cli.nombre, ' ', cli.ape_pat, ' ', cli.ape_mat)cliente
			, cli.nombre nombre_cliente
			, cli.empresa
			, cli.dir dir_cliente
			, cli.tel tel_cliente
			, ev.id_sal
			, sal.nombre salon
			, ev.id_tip_eve
			, teve.nombre tipo
			, ev.num_personas personas
			, ev.pagado
			, ev.cos_tot
			, ev.fecha fecha2
			, ev.observaciones
			, date_format(ev.fecha, '%d/%m/%Y')fecha
			, date_format(ev.hora, '%H:%i %p')hora
			, date_format(ev.falta, '%d/%m/%Y')falta
			from eventos ev 
			inner join tipo_evento teve using(id_tip_eve)
			inner join clientes cli using(id_cli)
			inner join salones sal using(id_sal)
			inner join empleados emp using(id_emp)

			where 1=1 and ev.id_eve=".$id;
			
			$stid = mysql_query($sql);
			
			if($row = mysql_fetch_assoc($stid)) {

				# Configuracion de colores, ancho de linea y fuente en negrita			
				$this->SetFillColor(255, 255, 255);
				$this->SetDrawColor(128, 128, 128);
				$this->SetTextColor(0,0,0);
				$this->SetFont('','');

				

				$alto_de_celda = 4;
				$this->SetX($vie_margen);
				$this->Cell(0, $alto_de_celda, utf8_decode($row['cliente']), 0 ,0 ,'L' ,1);
				$this->Ln();

				if(isset($row['empresa']) && $row['empresa']!='') {
					$this->SetX($vie_margen);
					$this->Cell(0, $alto_de_celda, utf8_decode($row['empresa']), 0 ,0 ,'L' ,1);
					$this->Ln();
				}
				if(isset($row['dir_cliente']) && $row['dir_cliente']!='') {
					$arr = explode(',', $row['dir_cliente']);

					foreach ($arr as $key => $value) {
						$value = trim($value);
						$this->SetX($vie_margen);
						$this->Cell(0, $alto_de_celda, utf8_decode($value), 0 ,0 ,'L' ,1);
						$this->Ln();
					}

					
				}
				if(isset($row['tel_cliente']) && $row['tel_cliente']!='') {
					$this->SetX($vie_margen);
					$this->Cell(0, $alto_de_celda, 'Tel. '.utf8_decode($row['tel_cliente']), 0 ,0 ,'L' ,1);
					$this->Ln();
				}
				$this->Ln();

				$no_cotizacion = $row['id_eve'];
				$no_cotizacion = str_pad($no_cotizacion, 5, '0', STR_PAD_LEFT );
				$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode('Ref. Cotización: ').$no_cotizacion, 0 ,0 ,'C' ,1);
				$this->Ln();

				/*$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode('Fecha Cotizada: ').$row['falta'], 0 ,0 ,'C' ,1);
				$this->Ln();
				$this->Ln();*/		

				$texto_editado = 'Estimado(a) '.$row['nombre_cliente'].': ';
				$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode($texto_editado), 0 ,0 ,'L' ,1);
				$this->Ln();
				$this->Ln();

				
				list($Xdia, $Xmes, $Xano) = explode('/', $row['fecha']);

				$Xdia = (int)$Xdia;

				$texto_editado = 'De acuerdo a lo solicitado, la cotización de nuestros servicios para el evento a realizarse tentativamente el día   '.$Xdia.' de';
				$this->SetX($vie_margen);
				$this->Cell(190, 5, utf8_decode($texto_editado), 0 ,0 ,'L' ,1);
				$this->Ln();

				$texto_editado = ''.dame_la_fecha($row['fecha']).',  a las '.$row['hora'].', es la siguiente: ';
				$this->SetX($vie_margen);
				$this->Cell(190, 5, utf8_decode($texto_editado), 0 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetFont('Arial','',8);
				$this->SetX($vie_margen);
				$this->Cell(10, 5, utf8_decode('CANT'), 1, 0, 'C', 1);
				$this->Cell(100, 5, utf8_decode('CONCEPTO'), 1, 0, 'C', 1);
				$this->Cell(40, 5, utf8_decode('IMPORTE SIN IVA'), 1, 0, 'C', 1);
				$this->Cell(40, 5, utf8_decode('IMPORTE CON IVA'), 1, 0, 'C', 1);
				$this->Ln();
				$sql="select ser.id_ser, ser.nombre servicio, (select 1 from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id.")contratado
								,(select seev.costo from servicios_eventos seev where seev.id_ser=ser.id_ser and seev.id_eve=".$id." )costo
								, tser.nombre tipo, detalle
								from servicios ser
								inner join tipo_servicio tser using(id_tip_ser)
								inner join servicios_eventos sev ON ser.id_ser=sev.id_ser and sev.id_eve=".$id."
								where 1=1
								order by tipo, servicio";
				$stidSer = mysql_query($sql);
				$cont = 0;
				$ctotal = 0;
				$ctotal_sin_iva = 0;
				while ($rowSer = mysql_fetch_assoc($stidSer)) {
					if($rowSer['contratado']==1) {
						$cont++;
						$servicio = utf8_decode($rowSer['servicio']);
						$costo = number_format($rowSer['costo'], 2, '.', ',');
						$iva = (($rowSer['costo'] * 16) / 100 );
						$costo_con_iva = $rowSer['costo'] + $iva;
						$ctotal_sin_iva += $costo_con_iva;
						$costo_con_iva = number_format($costo_con_iva, 2, '.', ',');
						$ctotal += $rowSer['costo'];

						$this->SetX($vie_margen);
						$this->Cell(10, 5, '1', 1, 0, 'C', 1);
						$this->Cell(100, 5, $servicio, 1, 0, 'L', 1);
						$this->Cell(40, 5, $costo, 1, 0, 'R', 1);
						$this->Cell(40, 5, $costo_con_iva, 1, 0, 'R', 1);
						$this->Ln();

						if (isset($rowSer['detalle']) and $rowSer['detalle'] != '') {
							$this->SetX($vie_margen);
							$this->MultiCell(190, 5, utf8_decode($rowSer['detalle']), 1, 'L', 1);
						}

						
					}//if
				}//while
				$ctotal = number_format($ctotal, 2, '.', ',');
				$ctotal_sin_iva = number_format($ctotal_sin_iva, 2, '.', ',');

				if($cont==0) {
					$this->SetX($vie_margen);
					$this->Cell(130, 5, utf8_decode('NO SE ENCONTRARON SERVICIOS CONTRATADOS.'), 1, 0, 'L', 1);
					$this->Ln();
				}else {
					if(isset($tipo) and $tipo!='cotizacionst'){

						$this->SetX($vie_margen);
						$this->Cell(110, 5, utf8_decode('TOTAL $'), 1, 0, 'R', 1);
						$this->Cell(40, 5, $ctotal, 1, 0, 'R', 1);
						$this->Cell(40, 5, $ctotal_sin_iva, 1, 0, 'R', 1);
						$this->Ln();
					}

				}
						

				$this->SetFont('Arial','',10);
				$this->Ln();
				
				$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode('Observaciones:'), 0, 0, 'L', 1);
				$this->Ln();
				$observaciones = trim($row['observaciones']);
				$arr_obs = explode("\n", $observaciones);
				foreach ($arr_obs as $key => $value) {
					$this->MultiCell(0, 5, utf8_decode($value),0 ,'L' ,1);
					$this->Ln();
				}
				// $this->Cell(0, 5, utf8_decode($row['observaciones']), 0 ,0 ,'L' ,1);
				//$this->Ln();

				/*$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode('Detalles:'), 0, 0, 'L', 1);
				$this->Ln();
				$texto_editado = 'Forma de pago: 50% del total al reservar los servicios y firmar el contrato respectivo, y el 50% restante a más tardar';
				$this->SetX($vie_margen);
				$this->Cell(5, 5, '1.- ', 0, 0, 'L', 1);
				$this->Cell(185, 5, utf8_decode($texto_editado), 0 ,0 ,'L' ,1);

				$this->SetX($vie_margen);
				$this->Ln();
				$texto_editado = 'dentro de los 7 días naturales posteriores al evento.';
				$this->Cell(5, 5, '', 0, 0, 'L', 1);
				$this->Cell(185, 5, utf8_decode($texto_editado), 0 ,0 ,'L' ,1);
				$this->Ln();

				$texto_editado = 'Se considera realizar la mayor parte del montaje una tarde anterior al evento e iniciar el desmontaje una vez finalizado';
				$this->SetX($vie_margen);
				$this->Cell(5, 5, '2.- Se considera realizar la mayor parte del montaje una tarde anterior al evento e iniciar el desmontaje una vez finalizado', 0, 0, 'L', 1);
				$this->Cell(185, 5, utf8_decode($texto_editado), 0 ,0 ,'L' ,1);
				$this->Ln();
				$texto_editado = 'el mismo.';
				$this->SetX($vie_margen);
				$this->Cell(5, 5, '', 0, 0, 'L', 1);
				$this->Cell(185, 5, utf8_decode($texto_editado), 0 ,0 ,'L' ,1);
				 */
				$this->Ln();
				$this->Ln();

				$texto_editado = 'Sin más por el momento y en espera de tener la oportunidad de trabajar para ustedes, quedamos a sus órdenes.';
				$this->SetX($vie_margen);
				$this->Cell(190, 5, utf8_decode($texto_editado), 0 ,0 ,'L' ,1);
				$this->Ln();
				$this->Ln();
				$this->Ln();
				

				$this->SetX($vie_margen);
				$this->Cell(190, 5, utf8_decode('Atentamente'), 0 ,0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(80, 5, utf8_decode($row['vendedor']), 0, 0 ,'L' ,1);
				$this->Ln();

				$this->SetX($vie_margen);
				$this->Cell(80, 5, utf8_decode('Ventas'), 0, 0 ,'L' ,1);
				$this->Ln();

				if(isset($row['tel']) && $row['tel']!='') {
					$this->SetX($vie_margen);
					$this->Cell(80, 5, 'Tel. '.utf8_decode($row['tel']), 0, 0 ,'L' ,1);
					$this->Ln();
				}
				
				$this->SetX($vie_margen);
				$this->Cell(80, 5, utf8_decode('ventas@vie.com.mx'), 0, 0 ,'L' ,1);
				$this->Ln();
				


				##intento ponerla arriba
				$this->SetY(45);
				$this->SetX($vie_margen);
				$this->Cell(0, 5, dame_la_fecha_otra_vez($row['falta']), 0 ,0 ,'R' ,0);
			
			}//si hay registros
			else {
				$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode('LA COTIZACIÓN SOLICITADA NO EXISTE'), 0 ,0 ,'L' ,1);
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
	$subtitle = "C O T I Z A C I Ó N";

	# Generamos el Query dependiendo a variaciones
	$addsql = "";

	#Proceso de generacion de PDF
	$archivo = 'temp/vie_cotizacion_'.$id.'.pdf'; # Nombre del archivo por defecto
	
	$pdf = new PDF();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','',12);
	$pdf->AddPage('P', 'Letter'); //una hoja carta tiene masomenos 215 de ancho en vertical
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
			// echo '<p>El archivo <a href="'.$archivo.'" target="_blank" class="ligaalreporte">'.$archivo.'</a> se genero exitosamente.</p>';
			if($tipo!='cotizaciontemp') {
				header('Location:'.$archivo);
			}
		}else{
			 echo 'Error al generar el archivo <span class="ligaalreporte">'.$archivo.'</span>';
		}
	}


	mysql_close($conn);

 ?>