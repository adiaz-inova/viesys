<?php
define('MODULO', 500);
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
	
	if( !isset($task) || trim($task) == '')
		$task = 'list';

	/* incluyo mis js extras antes de llamar el header*/
	$js_extras_onready='
	';

	require_once('includes/conection.php');

	# Grabamos en el log
	saveLog($conn, MODULO, $_SESSION[TOKEN.'USUARIO_ID']);
	#$no_encabezado = 1;
	#require_once('includes/html_template.php');
	
	switch($task) {
		case 'list': #- - - - - - - - - - - - - - -- - - LISTAR
							
			$sql = "select id_eve_notes id, notas, falta
				, date_format(falta, '%d/%m/%Y')fecha
				, date_format(falta, '%T')hora
				from eventos_notas
				where id_eve=".$id_eve."
				order by falta desc";
		
			$stid = mysql_query($sql);
			?>			

			<div class="opTitulo">NOTAS DEL EVENTO</div>
			<div id="vie_cont_notas">
			
			<?php
			$registros = 0;
			while($row = mysql_fetch_assoc($stid)) {	
				$registros++;
				$mensaje = '';

				$id = $row['id'];				
				$notas = $row['notas'];
				$fecha = $row['fecha'];
				$hora = $row['hora'];

				$mensaje .= '<p align="left"><strong>'.$fecha.' '.$hora.'</strong></p>';
				$mensaje .= '<p align="justify">'.$notas.'</p>';
				//$mensaje .= '<p align="left">Fecha: '.$fecha.'<br />Hora: '.$hora.'</p>';
				//$mensaje .= '<p align="left">Fecha: <strong>'.$fecha.' '.$hora.'</strong></p>';
				$mensaje .= '<hr />';
				
				?>
				<div class="vie_notas">
					<?php echo $mensaje; ?>
				</div>
				<?php
			}//while
			if($registros==0) {
				echo '
				<div class="vie_notas">NO SE HAN REGISTRADO NOTAS PARA ESTE EVENTO.</div>
				';
			}
			?>

			</div>
			<div class="vie_notas">
				<table width="100%" cellpadding="5">
					<tr>
						<td align="center">
							<form id="formulario_add_nota">
								<textarea name="Faddnotas" id="Faddnotas" cols="40" rows="5" style="width:450px" onkeypress="return vAbierta(event, this);"></textarea>
								<div style="text-align:center">
									<input type="button" value="Agregar nota" onclick="add_nota(<?php echo $id_eve ?>);" />
								</div>
							</form>
						</td>
					</tr>
				</table>
				<div id="notices_notas" style="display:none;"></div>

			</div>
		<?php
		break;
		case 'edit': #- - - - - - - - - - - - - - -- - - MODIFICAR

		

		break;
		case 'add': #- - - - - - - - - - - - - - -- - - AGREGAR
		?>
			<p class='opTitulo'>AGREGAR NOTA AL EVENTO</p>
			<div id="notices"></div>
			<form id="formulario">
			<fieldset><legend>Detalles del grupo</legend>
				<table width="100%">
					<tr>
						<td width="30%" align="left">
							<p>
								<span class="required">*</span>NOMBRE <br>
								<input class="" name="Fnombre" type="text" id="Fnombre" value="" size="50" maxlength="50" onkeypress="return vAbierta(event, this);" />
							</p>
						</td>
						<td width="70%" align="left">
							DESCRIPCIÓN <br>
							<input class="" name="Fdescripcion" type="text" id="Fdescripcion" value="" size="80" maxlength="100" onkeypress="return vAbierta(event, this);" />
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset><legend>Estatus</legend>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td valign="top" align="left">
							<span class="required">*</span>ESTATUS
							<br />
							<select class="" name="Festatus" id="Festatus" >
								<option value="">...</option>
							<?php
								$sql="select id_est, nombre from estatus where id_est in(1,3) order by nombre";
								
								$stid = mysql_query($sql);

								while (($rowPer = mysql_fetch_assoc($stid))) {
									if( $rowPer['id_est'] == $row['id_est']) {
										echo '	<option selected="selected" value="'.$rowPer['id_est'].'">'.$rowPer['nombre'].'</option>';
									}else{	
										echo '	<option value="'.$rowPer['id_est'].'">'.$rowPer['nombre'].'</option>';
									}
								}
							?>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset><legend>Permisos</legend>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td valign="top" align="left">
							MÓDULOS A LOS QUE TIENE ACCESO ESTE GRUPO
							<br />							
							<table width="50%" cellpadding="5" cellspacing="0" border="0">								
								<?php
								$sql="select m.id_mod, m.nombre, 0 permiso
								from modulo m
								order by nombre";
								$stid = mysql_query($sql);

								$cont = 0;
								while (($rowPer = mysql_fetch_assoc($stid))) {

									if($rowPer['permiso']=='1')
										$radios = '<input name="Fmodulos[]" id="Fmodulos'.$cont.'" type="checkbox" value="'.$rowPer['id_mod'].'" checked="checked" />';
									else
										$radios = '<input name="Fmodulos[]" id="Fmodulos'.$cont.'" type="checkbox" value="'.$rowPer['id_mod'].'" />';

									echo '
								<tr>
									<td width="5%" align="right">'.$radios.'</td>
									<td width="95%" align="left"><label for="Fmodulos'.$cont.'">'.$rowPer['nombre'].'</for></td>
								</tr>';

									$cont++;


								}
								?>
							</table>
						</td>
					</tr>
				</table>
			</fieldset>

		<table width="100%" cellpadding="3" cellspacing="3" align="center">
			<tr>
				<td align="center"><br />
					
					<input type="button" onclick="add_grupo();" value="Guardar cambios" />
					<input type="button"  value="Cancelar" href="<?php echo $_SERVER['PHP_SELF'] ?>" onclick=";window.location.href=this.getAttribute('href');return false;" />
		            
		            <div class="avisorequired"><span class="required">* campos requeridos</span></div>						
				</td>
			</tr>
		</table>
	</form>
<?php
		break;
		case 'ajaxlist': #- - - - - - - - - - - - - - -- - - LISTAR CON AJAX
							
			$sql = "select id_eve_notes id, notas, falta
				, date_format(falta, '%d/%m/%Y')fecha
				, date_format(falta, '%T')hora
				, concat(emp.nombre, ' ', emp.ape_pat, ' ', emp.ape_mat)empleado
				from eventos_notas
				left join empleados emp using(id_emp)
				where id_eve=".$id_eve."
				order by falta desc";
		
			$stid = mysql_query($sql);
			$registros = 0;
			$mensaje = '';
			while($row = mysql_fetch_assoc($stid)) {	
				$registros++;				

				$id = $row['id'];				
				$notas = $row['notas'];
				$fecha = $row['fecha'];
				$hora = $row['hora'];
				$empleado = $row['empleado'];

				$mensaje .= '<div class="vie_notas"><p align="left"><table width="100%"><tr><td align="left" class="empleado">'.$empleado.'</td><td align="right">'.$fecha.' '.$hora.'</td></tr></table></p>';
				$mensaje .= '<p align="justify" class="nota">'.$notas.'</p>';
				$mensaje .= '<hr /><div>';
				
			}//while
			if($registros==0) {
				echo '<div class="vie_notas">NO SE HAN REGISTRADO NOTAS PARA ESTE EVENTO.</div>';
			}else
				echo $mensaje;

		break;
	}// switch

	mysql_close($conn);
	#require_once('includes/html_footer.php');
?>