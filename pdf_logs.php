<?php
define('MODULO', 1200);
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
			$this->Cell(0,4, utf8_decode('Hora de elaboración: ').date('h:m:s a'),0,1,'R',0);
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

			$sql = "select l.ip, l.host, l.browser, l.id_mod, l.id_emp
			, date_format(l.date, '%d/%m/%Y')fecha
			, date_format(l.date, '%T')hora
			, l.date fechacom
			, concat(emp.nombre,' ',emp.ape_pat,' ',emp.ape_mat) empleado
			, gpo.nombre grupo
			, mods.nombre modulo
			from log l
			inner join empleados emp using(id_emp)
			left join grupo gpo using(id_gru)
			inner join modulo mods using(id_mod)
			where 1 ".$addsql."
			order by fechacom desc";
			$stid = mysql_query($sql);
			
			# Configuracion de colores, ancho de linea y fuente en negrita			
			$this->SetFillColor(255, 255, 255);
			$this->SetDrawColor(128, 128, 128);
			$this->SetTextColor(0,0,0);
			$this->SetFont('','');
			if($row = mysql_fetch_assoc($stid)) {

				$this->SetX($vie_margen);
				$this->Cell(10, 5, utf8_decode('#'), 1 ,0 ,'C' ,1);
				$this->Cell(20, 5, utf8_decode('FECHA'), 1 ,0 ,'C' ,1);
				$this->Cell(20, 5, utf8_decode('HORA'), 1 ,0 ,'C' ,1);
				$this->Cell(50, 5, utf8_decode('EMPLEADO'), 1 ,0 ,'C' ,1);
				$this->Cell(50, 5, utf8_decode('GRUPO'), 1 ,0 ,'C' ,1);
				$this->Cell(15, 5, utf8_decode('IP'), 1 ,0 ,'C' ,1);
				$this->Cell(35, 5, utf8_decode('MODULO'), 1 ,0 ,'C' ,1);
				$this->Ln();

				$registros=0;
				while ($row = mysql_fetch_assoc($stid)) {
					$registros++;

					$this->SetFontSize(6);
					$this->SetX($vie_margen);
					$this->Cell(10, 5, utf8_decode($registros), 1 ,0 ,'C' ,1);
					$this->Cell(20, 5, utf8_decode($row['fecha']), 1 ,0 ,'C' ,1);
					$this->Cell(20, 5, utf8_decode($row['hora']), 1 ,0 ,'C' ,1);
					$this->Cell(50, 5, utf8_decode($row['empleado']), 1 ,0 ,'C' ,1);
					$this->Cell(50, 5, utf8_decode($row['grupo']), 1 ,0 ,'C' ,1);
					$this->Cell(15, 5, utf8_decode($row['ip']), 1 ,0 ,'C' ,1);
					$this->Cell(35, 5, utf8_decode($row['modulo']), 1 ,0 ,'C' ,1);
					$this->Ln();
					
				}//while

				$this->SetX($vie_margen);
				$this->Cell(0, 5, utf8_decode($registros.' REGISTROS ENCONTRADOS.'), 0 ,0 ,'L' ,1);
				$this->Ln();
			
			}//si hay registros
			else {
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
	$subtitle = "REPORTE DE INGRESOS AL SISTEMA";

	# Generamos el Query dependiendo a variaciones
	$addsql = "";
	$addsql .= (isset($Fdesde) && $Fdesde!='')? " AND l.date >='".$Fdesde."' ":'';
	$addsql .= (isset($Fhasta) && $Fhasta!='')? " AND l.date <='".$Fhasta."' ":'';
	$addsql .= (isset($Fempleado) && $Fempleado!='')? " AND l.id_emp =".$Fempleado." ":'';
	$addsql .= (isset($Fgrupo) && $Fgrupo!='')? " AND gpo.id_gru =".$Fgrupo." ":'';
	$addsql .= (isset($Fmodulo) && $Fmodulo!='')? " AND mods.id_mod =".$Fmodulo." ":'';

	#Proceso de generacion de PDF
	$archivo = 'temp/vie_logs'.date('dmY').'.pdf'; # Nombre del archivo por defecto
	
	$pdf = new PDF();
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','',12);
	$pdf->AddPage('P', 'Letter'); //una hoja carta tiene masomenos 215 de ancho en vertical
	$pdf->Generar();
	$pdf->SetTitle($title);
	$pdf->SetAuthor('VIE 2012');
	$output = "F";
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
			//echo '<p>El archivo <a href="'.$archivo.'" target="_blank" class="ligaalreporte">'.$archivo.'</a> se genero exitosamente.</p>';
			header('Location:'.$archivo);
		}else{
			 echo 'Error al generar el archivo <span class="ligaalreporte">'.$archivo.'</span>';
		}
	}


	mysql_close($conn);

 ?>