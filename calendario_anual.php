<?php
$PaSpOrT = true; //esta pagino no requiere permisos, solo session
  $addruta = '';
  require_once('token.inc.php');
  require_once('includes/getvalues.php');
  require_once('includes/session_principal.php');
  require_once('includes/conection.php');
  require_once('includes/calendario.php');
  $Fano=(isset($Fano) and $Fano!='')?$Fano:(int)date('Y');
  $anioactual=(int)date('Y');
  $css_extras = '<link href="css/calendarios.css" rel="stylesheet" type="text/css" />';
  $js_extras_onready = '
    $("#Fano").change(function () {
      $("#anocalendar").submit();
    });
  ';
  require_once('includes/html_template.php');
?>

  <h3>CALENDARIO DE EVENTOS <?php echo $Fano ?></h3>
  <div class="content">
    <div style="background-image:url(images/aplicaciones/servicios/cuerpo_servicio_1_px.png); background-repeat:repeat-y;">
    <div class="divSeparador">
      <div style="padding:5px;">
      <form id="anocalendar">
        Seleccione un año: <select name="Fano" id="Fano"  onChange="Form.submit()">
          <?php
             $selected = ''; if($Fano == ($anioactual-5)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual-5).'" '.$selected.'>'.($anioactual-5).'</option>';
             $selected = ''; if($Fano == ($anioactual-4)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual-4).'" '.$selected.'>'.($anioactual-4).'</option>';
             $selected = ''; if($Fano == ($anioactual-3)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual-3).'" '.$selected.'>'.($anioactual-3).'</option>';
             $selected = ''; if($Fano == ($anioactual-2)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual-2).'" '.$selected.'>'.($anioactual-2).'</option>';
             $selected = ''; if($Fano == ($anioactual-1)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual-1).'" '.$selected.'>'.($anioactual-1).'</option>';
             $selected = ''; if($Fano == $anioactual) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual).'" '.$selected.'>'.($anioactual).'</option>';
             $selected = ''; if($Fano == ($anioactual+1)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual+1).'" '.$selected.'>'.($anioactual+1).'</option>';
             $selected = ''; if($Fano == ($anioactual+2)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual+2).'" '.$selected.'>'.($anioactual+2).'</option>';
             $selected = ''; if($Fano == ($anioactual+3)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual+3).'" '.$selected.'>'.($anioactual+3).'</option>';
             $selected = ''; if($Fano == ($anioactual+4)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual+4).'" '.$selected.'>'.($anioactual+4).'</option>';
             $selected = ''; if($Fano == ($anioactual+5)) $selected = ' selected="selected"';
             echo '<option value="'.($anioactual+5).'" '.$selected.'>'.($anioactual+5).'</option>';
          // }


          ?>
          <option></option>
        </select>
      </form>
      </div>
    </div>
    <table width="100%" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td valign="top"><?php mostrar_calendario(1,1,$Fano); ?></td>
        <td valign="top"><?php mostrar_calendario(1,2,$Fano); ?></td>
        <td valign="top"><?php mostrar_calendario(1,3,$Fano); ?></td>
        <td valign="top"><?php mostrar_calendario(1,4,$Fano); ?></td>
      </tr>
      <tr>
        <td valign="top"><?php mostrar_calendario(1,5,$Fano); ?></td>
        <td valign="top"><?php mostrar_calendario(1,6,$Fano); ?></td>
        <td valign="top"><?php mostrar_calendario(1,7,$Fano); ?></td>
        <td valign="top"><?php mostrar_calendario(1,8,$Fano); ?></td>
      </tr>
      <tr>
        <td valign="top"><?php mostrar_calendario(1,9,$Fano); ?></td>
        <td valign="top"><?php mostrar_calendario(1,10,$Fano); ?></td>
        <td valign="top"><?php mostrar_calendario(1,11,$Fano); ?></td>
        <td valign="top"><?php mostrar_calendario(1,12,$Fano); ?></td>
      </tr>
    </table>
    </div>
    <div align="center">
      <span class="evento_cotizado"></span> Cotizado
      <span class="evento_vendido"></span> Vendido
      <span class="eventos_varios"></span> Varios
      <!-- <span class="eventos_terminado"></span> Terminado -->

    </div>
  </div><!-- content -->
<?php
  mysql_close($conn);
  require_once('includes/html_footer.php');
?>