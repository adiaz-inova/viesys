<?php
require_once('includes/conection.php');
require_once('includes/calendario_mes.php'); 
$get_mes=$_GET['mes'];
$get_ano=date('Y');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
<title>GeoVirtual :: Capacitación</title>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
    $(".cal_mesbkgnumdia td").mousemove(function(e){
			margin=5;
			cX = e.pageX;
			cY = e.pageY;
			
			var pageCoords = "( " + e.pageX + ", " + e.pageY + " )";
			var clientCoords = "( " + e.clientX + ", " + e.clientY + " )";	
			
			$('#verdetcal').css("top",cY+20);
			$('#verdetcal').css("left",cX+20);
			
			detalles ='';
			if($(this).attr("titulo")) {
				detalles = '<p>'+$(this).attr("titulo")+'.- '+$(this).attr("descripcion")+'</p>';
				$('#verdetcal').html(detalles);
				if($('#verdetcal').is (':hidden'))
					$('#verdetcal').show();
			}
			else
				$('#verdetcal').fadeOut('fast');
    });
		
		$("#divCalend").mouseleave(function(){
     // $('#verdetcal').fadeOut('fast');
    });


}); 

function intcal()
{
	var menu1 = $('#menu_temas');
	var menu2 = $('#menu_meses');
	menu1.toggle();
	menu2.toggle();
}
</script>
</head>
<body>
<div id="verdetcal"></div>
<div class="container">
	
  <div class="header">
  	<?php require_once('includes/header.php') ?>
  </div><!-- header -->

  <div class="sidebar1">
    <?php require_once('includes/menu.php') ?>
    <?php require_once('includes/menu2.php') ?>
  </div><!-- sidebar1 -->

  <div class="content">
  	<div style="background-image:url(images/aplicaciones/servicios/cuerpo_servicio_1_px.png); background-repeat:repeat-y;">
    <div class="divSeparador">
    <span class="cal_titulo"><?php echo dame_nombre_mes($get_mes).' '.$get_ano ?></span>
    </div>

    <table width="100%" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td valign="top"><?php mostrar_calendario(1, $get_mes, $get_ano); ?></td>
      </tr>
    </table>
    </div>
  </div><!-- content -->
  
  <div class="footer"></div><!-- footer -->
</div><!-- container -->
</body>
</html>