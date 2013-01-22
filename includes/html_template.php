<?php
  /* 
  * loginexpress.php
  * creado      01/08/2012
  * modificado  14/04/2012
  * autor       I.S.C. Alejandro Diaz Garcia
  */
  $auth_ok = (isset($auth_message) && $auth_message!='')? false : true;
  if(!$auth_ok) {
    require_once('html_template_unauth.php');
    exit;
  }

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
  <link rel="shortcut icon"  href="<?php echo $addruta ?>favicon.ico">
  <title>VIE :: Very Important Events</title>
  <link href="<?php echo $addruta ?>css/cssraiz.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo $addruta ?>css/pag_grey.css" rel="stylesheet" type="text/css" />
  

  <link href="<?php echo $addruta ?>css/github1.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo $addruta ?>css/github2.css" rel="stylesheet" type="text/css" />

  <link href="css/redmond/ui.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" type="text/css" href="css/jquery.fancybox.css?v=2.1.2" media="screen" />
  <?php
  if (isset($css_extras) && $css_extras!='')
    echo $css_extras;
  ?>
  <script language="javascript" type="text/javascript" src="<?php echo $addruta ?>js/jquery.js"></script>
  <script language="javascript" type="text/javascript" src="<?php echo $addruta ?>js/scaninput.js"></script>
  <script language="javascript" type="text/javascript" src="<?php echo $addruta ?>js/procesar.js"></script>
  <script type="text/javascript" src="<?php echo $addruta ?>js/jquery.mousewheel-3.0.6.pack.js"></script>
  <script type="text/javascript" src="<?php echo $addruta ?>js/jquery.fancybox.js?v=2.1.2"></script>
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
    
    $('.fancybox').fancybox();

    $('.fiframe').fancybox({type: 'ajax', scrolling: 'auto', width: '900'});
    
    $("#fancybox-iframe").click(function() {
        $.fancybox.open({
          href : 'salones.html',
          type : 'iframe',
          scrolling: 'no',
          width: 700,
          padding : 5
        });
      });


    if ($("#filtro").length) {
      $("#filtro").keypress(function (e) {        
        if(e.which == 13) {         
            filtrar(this);
            return false;
          }else
          {
            return vAbierta(e, this);
          }       
      });
      $("#filtro").keyup(function (e) {
        if(e.which == 27)
          {
            if(this.value!='')
            {
              this.value='';
              filtrar(this);              
            }
            return false;
          }
          if(e.which == 46)
          {
          if(this.value=='')
            {             
              filtrar(this);
            }
            return false;
        }
      });
    }
    //$(':input[type=text], input[type=password]').addClass("text ui-widget-content ui-corner-all");
    $("input:submit, input:button, input:reset").button();
    //$("input:submit, input:button, input:reset").attr("class","classy");
    });
  
  </script>
</head>

<body>
  
  <div id="cargando" style="display:none;"><img src="<?php echo $addruta ?>images/cargando.gif" width="16" height="16" border="0" /> Cargando... </div>
  <div id="overlay" class="simplemodal-overlay" style=" display: none; filter:alpha(opacity=50);  -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5;"></div>
  <?php if(!isset($no_encabezado)) { ?>
  <div id="vie_encabezado">
    <div id="vie_logo_content"><a href="index.php"><div id="vie_logo"></div></a></div>
    <div id="topmenu">
      <a href="empleados_view.php?id=<?php echo $_SESSION[TOKEN.'USUARIO_ID'] ?>" class="fiframe"><?php echo $_SESSION[TOKEN.'USUARIO'] ?></a>
      <a href="includes/logout.php">Cerrar sesión</a>
    </div>
  </div>
  <?php } ?>
    

  <!-- contenidoprincipal -->
  <div id="contenidoprincipal">
    <div id="vie_titulo">
    <?php
    if (isset($contenido_html_titulo) && $contenido_html_titulo!='')
      echo $contenido_html_titulo;
    ?>
    </div>
<div style="padding:5px 0;"></div>
<div id="settings-nav" class="menu-container js-settings-next">
  <ul class="menu accordion js-settings-pjax" data-pjax="">
        <li class="section">
          <a href="index.php" class="section-head">
            <img height="20" src="images/empleado_icon.png" width="20">
            <?php echo $_SESSION[TOKEN.'NOMBRES'] ?>
          </a>
          <ul class="expanded section-nav">
          <?php
          $arrParallel = array(-1, 100,700, 200, 300, 400, 500, 500, 600, 900, 1200, 1100, 500, 1000);
          $arrMenu = array('Inicio'=>'index.php'
          ,'Empleados'=>'empleados.php'
          ,'Grupos'=>'grupos.php'
          ,'Clientes'=>'clientes.php'
          ,'Salones'=>'salones.php'
          ,'Servicios'=>'servicios.php'
          ,'Cotizaciones'=>'cotizaciones.php'
          ,'Eventos'=>'eventos.php'
          ,'Facturas'=>'facturas.php'
          ,'Pagos'=>'pagos.php'
          ,'Reportes'=>'reportes.php'
          ,'Gráficas'=>'graficas.php'
          ,'Calendario'=>'calendario_anual.php'
          ,'Log'=>'log.php');

          $editar_tipo = (isset($editar_tipo) && $editar_tipo != '')? $editar_tipo:'';
          $ubicacion_actual = basename($_SERVER['PHP_SELF']);
          
          if($editar_tipo=='cotizaciones')
            $ubicacion_actual = $editar_tipo.'.php';

          $i=0;
          foreach ($arrMenu as $menu => $liga) {
            $class = '';
            
            if($liga == $ubicacion_actual)
              $class = ' class="selected"';
            if(isset($_SESSION[TOKEN.'PERMISOS']) && in_array($arrParallel[$i], $_SESSION[TOKEN.'PERMISOS']))
              echo '
            <li>
              <a href="'.$liga.'"'.$class.'>'.$menu.'</a>
            </li>';

            $i++;
          }
          
          ?>
          </ul>
        </li>
  </ul>
</div>

<div class="settings-content">
  <div class="boxed-group">

    <?php
    if (isset($contenido_html) && $contenido_html!='')
      echo $contenido_html;
    ?>
