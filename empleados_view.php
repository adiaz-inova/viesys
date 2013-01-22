<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session

/* 
  * VIE 2012
  * creado      01/08/2012
  * modificado  14/04/2012
  * autor       I.S.C. Alejandro Diaz Garcia
  */
	$addruta = '';
	require_once('token.inc.php');
	require_once('includes/getvalues.php');
	require_once('includes/session_principal.php');
	require_once('includes/conection.php');

	
	$id=$_SESSION[TOKEN.'USUARIO_ID'];

	# Consultamos la informacion del usuario
	$sql = "select
			empleados.id_emp usuario_id,
			empleados.nombre nombres,
			empleados.ape_pat paterno,
			empleados.ape_mat materno,
			empleados.tel,
			empleados.email usuario,
			empleados.dir,
			empleados.psw password,
			empleados.id_est u_estatus,
			estatus.id_est, estatus.nombre,
			grupo.id_gru grupo_id,
			grupo.nombre grupo,
			grupo.id_est g_estatus
			from empleados
			inner join estatus using(id_est)
			inner join grupo using(id_gru)
			where 1=1 and empleados.id_est in(1,3)
			and empleados.id_emp=".$_SESSION[TOKEN.'USUARIO_ID'];
	$stid = mysql_query($sql);
	$row = mysql_fetch_assoc($stid);

	?>
<div class="settings-content">
  <div class="boxed-group" id="DatossEvento">
	<h3>CONSULTAR MIS DATOS</h3>
	<div class="boxed-group-inner clearfix">
		<form name="formulario" id="formulario">
			<h4>Detalles del empleado</h4>
			<table width="100%" cellpadding="5" cellspacing="5">
				<tr>
					<td width="30%" align="left">NOMBRES: <span class="vdato"><?php echo $row['nombres']; ?></span></td>
					<td width="35%" align="left">APELLIDO PATERNO: <span class="vdato"><?php echo $row['paterno']; ?></span></td>
					<td width="30%" align="left">APELLIDO MATERNO: <span class="vdato"><?php echo $row['materno']; ?></span></td>
				</tr>
			</table>
			<table width="100%" cellpadding="5" cellspacing="5">
				<tr>
					<td width="100%" align="left">DIRECCIÓN: <span class="vdato"><?php echo $row['dir']; ?></span></td>
				</tr>
			</table>
			<table width="100%" cellpadding="5" cellspacing="5">
				<tr>
					<td width="50%" align="left">TELÉFONO: <span class="vdato"><?php echo $row['tel']; ?></span></td>
					<td width="50%" align="left">GRUPO: <span class="vdato"><?php echo $row['grupo']; ?></span></td>
				</tr>
			</table>
			
			<p align="center"><a href="empleados_edit.php"><input type="button" value="MODIFICAR MIS DATOS" /></a></p>
			
		</form>
		</div>
	</div><!--class="boxed-group"-->

</div><!--class="settings-content"-->
<script>
<!--
    $(':input[type=text], input[type=password]').addClass("text ui-widget-content ui-corner-all");
    $("input:submit, input:button, input:reset").button();
//-->
</script>
<?php

	mysql_close($conn);

?>