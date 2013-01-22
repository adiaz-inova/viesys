<?php
define('MODULO', 700);
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
	
	require_once('includes/html_template.php');
	
	switch($task) {
		case 'list': #- - - - - - - - - - - - - - -- - - LISTAR
							
			$sql = "select g.id_gru, g.nombre grupo, g.descripcion, g.id_est estatus, estatus.nombre,
			(select count(*) from empleados emp where emp.id_gru=g.id_gru)usuarios,
			(select count(*) from permisos per where per.id_gru=g.id_gru)modulos
			from grupo g
			inner join estatus using(id_est)
			where 1=1 and g.id_est in(1,3)
			order by grupo";
		
			$stid = mysql_query($sql);
?>			

			<h3>GRUPOS</h3>
			<div class="boxed-group-inner clearfix">
				<div class="filtro">
					<form id="formulario" name="formulario">
						<img src="images/Search-icon-24.png" title="Filtrar" alt="Filtrar" />
						<input name="filtro" id="filtro" type="text" size="25" maxlength="50" class="minus" tipo="grupos" placeholder="buscar"/>
					</form>
					<div align="right" class="botonsuperior">
		        		<input type="button" value="Agregar" href="<?php echo $_SERVER['PHP_SELF'] ?>?task=add" onclick=";window.location.href=this.getAttribute('href');return false;" />
					</div>
				</div>
			
				<div class="scrool" id="cont">
					<table width="100%" cellspacing="0" cellpadding="3">
						<tr class="Cabezadefila">
							<th width="3%">#</th>
							<th width="37%">Grupo</th>
							<th width="20%">Usuarios</th>
							<th width="20%">Modulos</th>
							<th width="5%">Estatus</th>
							<th width="5%">Edit</th>
							<th width="5%">Elim</th>
						</tr>

					<?php
					$registros = 0;
					while($row = mysql_fetch_assoc($stid)) {	
						$registros++;

						$id = $row['id_gru'];				
						$nombre = $row['grupo'];
						$usuarios = $row['usuarios'];
						$modulos = $row['modulos'];
						
						$accion = ( $row['estatus'] == 1)? '<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="grupo" accion="suspender" title="Suspender" alt="Suspender"><img src="images/enabled2.png" border="0"></a>':'<a href="javascript:return; " onclick="activar_suspender(this)" attid="'.$id.'" tipo="grupo" accion="activar" title="Activar" alt="Activar"><img src="images/disabled2.png" border="0"></a>';
																		
						$rem = ($id != $_SESSION[TOKEN.'GRUPO_ID'] && $usuarios==0)? '<input type="button" onclick="eliminar_registro(this);" identif="'.$id.'" tipo="grupo" value="-" />' : '';

						if($id == $_SESSION[TOKEN.'GRUPO_ID'])
							$accion = '';				
						?>
						<tr onmouseover="this.className='filaActiva'" onmouseout="this.className='filaNormal'" class="filaNormal" id="row<?php echo $id; ?>">
							<td align="center" class="celdaNormal"><?php echo $registros; ?></td>
							<td align="left" class="celdaNormal"><a href="grupos.php?task=edit&id=<?php echo $id; ?>" title="Ver detalles" alt="Ver detalles"><?php echo strtoupper($nombre); ?></a></td>
							<td align="center" class="celdaNormal"><?php echo $usuarios; ?></td>
							<td align="center" class="celdaNormal"><?php echo $modulos; ?></td>
							<td align="center" class="celdaNormal"><?php echo $accion; ?></td>
							<td align="center" class="celdaNormal"><a href="grupos.php?task=edit&id=<?php echo $id; ?>" title="Editar" alt="Editar"><img src="images/Edit-icon-16.png" border="0"></a></td>
							<td align="center" class="celdaNormal"><?php echo $rem; ?></td>
						</tr>
					<?php
					}			
					?>			

						<tr class="Cabezadefila">
							<td colspan='10'><?php echo $registros; ?> Registros encontrados.</td>
						</tr>
					</table>
				</div>
			</div><!-- class="boxed-group-inner clearfix" -->
		<?php
		break;
		case 'edit': #- - - - - - - - - - - - - - -- - - MODIFICAR

			# Control de seguridad
			if (!isset($id)) {
				header('Location: '.$_SERVER['PHP_SELF']);
			}

			# Consultamos la informacion del usuario
			$sql="select * from grupo g where 1=1 and g.id_gru=".$id;
			$stid = mysql_query($sql);
			$row = mysql_fetch_assoc($stid);
			?>
			<?php	/*<div class="notices">&nbsp;</div>*/	?>
			<h3>MODIFICAR DATOS DEL GRUPO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Detalles del grupo</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="30%" align="left">
								<label><span class="required">*</span>NOMBRE </label>
								<input class="" name="Fnombre" type="text" id="Fnombre" value="<?php echo $row['nombre']; ?>" size="" maxlength="50" title="nombre(s) del usuario" onkeypress="return vAbierta(event, this);" />
							</td>
							<td width="70%" align="left">
								<label>DESCRIPCIÓN </label>
								<input class="" name="Fdescripcion" type="text" id="Fdescripcion" value="<?php echo $row['descripcion']; ?>" size="50" maxlength="100" onkeypress="return vAbierta(event, this);" />
							</td>
						</tr>
					</table>
					<input name="Fid" type="hidden" id="Fid" value="<?php echo $id; ?>"/>
					<hr class="bleed-flush compact" />

					<h4>Estatus</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td valign="top" align="left">
								<label><span class="required">*</span>ESTATUS</label>
								<select class="" name="Festatus" id="Festatus" >
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
					<hr class="bleed-flush compact" />

					<h4>Permisos</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td valign="top" align="left">
								<p>Seleccione los módulos a los que tiene acceso este grupo</p>
								<table width="100%" cellpadding="5" cellspacing="0" border="0">								
									<?php
									$sql="select m.id_mod, m.nombre, (select 1 from permisos per where per.id_mod =m.id_mod and per.id_gru=".$id." )permiso
									from modulo m
									order by nombre";
									$stid = mysql_query($sql);

									$cont = 0;
									$resaltar='';
									while (($rowPer = mysql_fetch_assoc($stid))) {

										if($rowPer['permiso']=='1')
											$radios = '<input name="Fmodulos[]" id="Fmodulos'.$cont.'" type="checkbox" value="'.$rowPer['id_mod'].'" checked="checked" />';
										else
											$radios = '<input name="Fmodulos[]" id="Fmodulos'.$cont.'" type="checkbox" value="'.$rowPer['id_mod'].'" />';

										$resaltar = ($resaltar=='')?' class="resealtado"':'';
										echo '
									<tr'.$resaltar.'>
										<td width="5%" align="left">'.$radios.'</td>
										<td width="95%" align="left"><label for="Fmodulos'.$cont.'">'.$rowPer['nombre'].'</label></td>
									</tr>';

										$cont++;
									}
									?>
								</table>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<table width="100%" cellpadding="3" cellspacing="3" align="center">
						<tr>
							<td align="center"><br />
					            <input class="" type="button" name="" value="Guardar y Salir" act="exit" onclick="update_grupo(this);" />&nbsp;
					            <input class="" name="" type="button" value="Cancelar" onclick="javascript:window.location.href='<?php echo $_SERVER['PHP_SELF'] ?>';" />
								<div class="avisorequired"><span class="required">* campos requeridos</span></div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		<?php
		break;
		case 'add': #- - - - - - - - - - - - - - -- - - AGREGAR

		?>
			<h3>AGREGAR GRUPO</h3>
			<div class="boxed-group-inner clearfix">
				<div id="notices"></div>
				<form name="formulario" id="formulario">
					<h4>Detalles del grupo</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="30%" align="left">
								<label><span class="required">*</span>NOMBRE </label>
								<input class="" name="Fnombre" type="text" id="Fnombre" value="" size="" maxlength="50" title="nombre(s) del usuario" onkeypress="return vAbierta(event, this);" />
							</td>
							<td width="70%" align="left">
								<label>DESCRIPCIÓN </label>
								<input class="" name="Fdescripcion" type="text" id="Fdescripcion" value="" size="50" maxlength="100" onkeypress="return vAbierta(event, this);" />
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

					<h4>Permisos</h4>
					<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td valign="top" align="left">
								<p>Seleccione los módulos a los que tiene acceso este grupo</p>
								<table width="100%" cellpadding="5" cellspacing="0" border="0">								
									<?php
									$sql="select m.id_mod, m.nombre, 0 permiso
									from modulo m
									order by nombre";
									$stid = mysql_query($sql);

									$cont = 0;
									$resaltar='';
									while (($rowPer = mysql_fetch_assoc($stid))) {

										if($rowPer['permiso']=='1')
											$radios = '<input name="Fmodulos[]" id="Fmodulos'.$cont.'" type="checkbox" value="'.$rowPer['id_mod'].'" checked="checked" />';
										else
											$radios = '<input name="Fmodulos[]" id="Fmodulos'.$cont.'" type="checkbox" value="'.$rowPer['id_mod'].'" />';

										$resaltar = ($resaltar=='')?' class="resealtado"':'';
										echo '
									<tr'.$resaltar.'>
										<td width="5%" align="left">'.$radios.'</td>
										<td width="95%" align="left"><label for="Fmodulos'.$cont.'">'.$rowPer['nombre'].'</label></td>
									</tr>';

										$cont++;
									}
									?>
								</table>
							</td>
						</tr>
					</table>
					<hr class="bleed-flush compact" />

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
			</div>
			
		<?php
		break;		
	}// switch

	mysql_close($conn);
	require_once('includes/html_footer.php');
?>