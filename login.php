<?php
  /* 
  * loginexpress.php
  * creado      01/08/2011
  * modificado  14/04/2012
  * autor       I.S.C. Alejandro Diaz Garcia
  */

  date_default_timezone_set('America/Mexico_City');
  $dias = Array('', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
  $mes = Array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"); 
  $fechahoy = ''.$dias[date('N')].' '.date('j').' de '.$mes[date('n')].' de '.date('Y').'';
  $addruta = (isset($addruta))? $addruta:'';
  require_once($addruta.'token.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<link rel="shortcut icon"  href="<?php echo $addruta ?>images/favicon.ico">
<title>VIE :: Very Important Events</title>
<link href="<?php echo $addruta ?>css/cssraiz.css" rel="stylesheet" type="text/css" />
<?php
if (isset($css_extras) && $css_extras!='')
  echo $css_extras;
?>
<script language="javascript" type="text/javascript" src="<?php echo $addruta ?>js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $addruta ?>js/scaninput.js"></script>
<?php
if (isset($js_extras) && $js_extras!='')
  echo $js_extras;
?>
<script type="text/javascript" src="<?php echo $addruta ?>js/mytools.js"></script>
<script language="javascript" type="text/javascript">
<?php
if (isset($js) && $js!='')
  echo $js;
?>

$(document).ready(function() {
<?php
if (isset($js_extras_onready) && $js_extras_onready!='')
  echo $js_extras_onready;
?>

$(':input[type=text], input[type=password]').addClass("text ui-widget-content ui-corner-all");
$("input:submit, input:button, input:reset").button();
});
function keypressed(e){
  if (((document.all)?e.keyCode:e.which)=="13"  || ((document.all)?e.keyCode:e.which)=="9") {
    Loginexpress();
  }
}

function Loginexpress(){

  var u = $('#user');
  var p = $('#pwd');  
  var div = $('#notice');
  
  if(u.val() == '' && p.val() == ''){
    div.html('Escriba su usuario');
    u.focus();
    div.show();

    return false;
  }
  if(u.val() == ''){
    div.html('Escriba su usuario');
    u.focus();
    div.show();

    return false;
  } 
  if(p.val() == ''){
    div.html('Escriba su constraseña');
    p.focus();
    div.show();

    return false;
  }

  var param = '';
  param = 'task=Login&u='+u.val()+'&p='+p.val();


  $.ajax({
    type: "POST",
    url: "<?php echo $addruta ?>login_pwd.inc.php",
    data: param,
    beforeSend: function(){
      $('#cargando').show();
    },
    success: function(msg){

      if(msg != 4){
          div.html(msg);
        
        u.focus();
        div.show();
    
        return false;
        
      }else{
        var iraurl = '<?php echo $_SERVER['REQUEST_URI']; ?>';
        window.location.href=iraurl;
  
      }     
    },
    complete: function(){
      $('#cargando').hide();
    }
  });

}
</script>
<style type="text/css">
body {
  
  margin: 0 auto;
  background-color:#FFF;
  text-align:left;
  width:350px;
}
</style>
</head>

<body>
<div id="cargando" style="display:none;"><img src="<?php echo $addruta ?>images/cargando.gif" width="16" height="16" border="0" /> Cargando... </div>
<div id="overlay" class="simplemodal-overlay" style=" display: none; filter:alpha(opacity=50);  -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5;"></div>
  <!-- contenidoprincipal -->
  <div id="loginexpress">
    <div id="loginbox_title" class="shadow">VIE - Identifiquese</div>
          <div id="loginbox_top" class="shadow">
            <?php #if (!isset($_SESSION[TOKEN.'USUARIO_ID']) || !isset($_SESSION[TOKEN.'GRUPO_ID'])) {
              if(1==1){
              $getUsuario = (isset($getUsuario))? $getUsuario : '';
            ?>
            <div id="loginbox">
              <form id="form_login" name="form_login" method="post" onsubmit="Login();">
                <table width="200" cellpadding="5" cellspacing="0" border="0" align="center">
                  <tr>
                    <td align="left">
                      <label for="user">Nombre de Usuario: </label>
                    </td>
                  </tr>
                  <tr>
                    <td align="left">
                      <input value="<?php echo $getUsuario ?>" name="user" type="text" class="" id="user" size="16" onkeypress="return vAbierta(event, this);" placeholder="Usuario" />
                    </td>
                  </tr>
                  <tr>
                    <td align="left">
                    	<label for="pwd">Contraseña: </label>
                    </td>
                  </tr>
                  <tr>
                    <td align="left">
                    	<input name="pwd" type="password" class="" id="pwd" onkeydown="keypressed(event)" size="16" onkeypress="return vPassword(event, this);" placeholder="Contraseña" />
                    </td>
                  </tr>
                  <tr>
                    <td align="center"><input type="button" onclick="Loginexpress();" value="Entrar" /></td>
                  </tr>              
                </table>
              </form>  						
            </div><!-- loginbox -->            
            
            <?php } else {?>
            <div id="loginbox">
              <table width="100%" cellpadding="5" cellspacing="0">
                <tr>
                  <td align="left">
                    <span align="left" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#494848; padding-left:5px;">Sesión iniciada </span>
                  </td>
                </tr>
                <tr>
                  <td align="left">
                    <span align="left" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#494848; padding-left:5px;">Usuario: </span>
                  </td>
                </tr>
                <tr>
                  <td align="center"><?php echo $_SESSION[TOKEN.'USUARIO'] ?></td>
                </tr>
                <tr>
                  <td align="left"><?php echo '<a href="'.$addruta.'lista-de-modulos.php">Ver módulos</a>' ?></td>
                </tr>
                <tr>
                  <td align="left"><?php echo '<a href="'.$addruta.'includes/logout.php">No soy "'.$_SESSION[TOKEN.'NOM_COMPLETO'].'"</a>' ?></td>
                </tr>
                <tr>
                  <td align="left"><?php echo '<a href="'.$addruta.'includes/logout.php">Salir</a>' ?></td>
                </tr>
              </table>
            </div><!-- loginbox -->            
            <?php } ?>
          </div><!-- loginBoxtop -->
          <div id="notice" class="aviso" style="display:none; z-index:90; float:inherit;"></div>
  </div><!--contenidoprincipal-->
<body>
</body>
</html>