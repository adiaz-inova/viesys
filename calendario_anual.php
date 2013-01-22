<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session
  $addruta = '';
  require_once('token.inc.php');
  require_once('includes/getvalues.php');
  require_once('includes/session_principal.php');
  require_once('includes/conection.php');
  require_once('includes/calendario.php');
  $elanioactual=date('Y');

  $css_extras = '<link href="css/calendarios.css" rel="stylesheet" type="text/css" />';

  require_once('includes/html_template.php');
?>

  <h3>CALENDARIO DE EVENTOS <?php echo $elanioactual ?></h3>
  <div class="content">
  	<div style="background-image:url(images/aplicaciones/servicios/cuerpo_servicio_1_px.png); background-repeat:repeat-y;">
    <div class="divSeparador"></div>
    <table width="100%" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td valign="top"><?php mostrar_calendario(1,1,$elanioactual); ?></td>
        <td valign="top"><?php mostrar_calendario(1,2,$elanioactual); ?></td>
        <td valign="top"><?php mostrar_calendario(1,3,$elanioactual); ?></td>
        <td valign="top"><?php mostrar_calendario(1,4,$elanioactual); ?></td>
      </tr>
      <tr>
        <td valign="top"><?php mostrar_calendario(1,5,$elanioactual); ?></td>
        <td valign="top"><?php mostrar_calendario(1,6,$elanioactual); ?></td>
        <td valign="top"><?php mostrar_calendario(1,7,$elanioactual); ?></td>
        <td valign="top"><?php mostrar_calendario(1,8,$elanioactual); ?></td>
      </tr>
      <tr>
        <td valign="top"><?php mostrar_calendario(1,9,$elanioactual); ?></td>
        <td valign="top"><?php mostrar_calendario(1,10,$elanioactual); ?></td>
        <td valign="top"><?php mostrar_calendario(1,11,$elanioactual); ?></td>
        <td valign="top"><?php mostrar_calendario(1,12,$elanioactual); ?></td>
      </tr>
    </table>
    </div>
    <div align="center">
      <span class="evento_cotizado"></span> Cotizado
      <span class="evento_vendido"></span> Vendido
      <span class="evento_terminado"></span> Terminado

    </div>
  </div><!-- content -->
<?php
  mysql_close($conn);
  require_once('includes/html_footer.php');
?>