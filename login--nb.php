
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>*cliente 3.0 - Panel de Control de NEUBOX Internet</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="resources/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="resources/css/style.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="resources/css/invalid.css" type="text/css" media="screen" />
        <link rel="shortcut icon" href="img/favicon.ico" />

        <script type="text/javascript" src="includes/js/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="includes/js/jquery_ui.js"></script>
        <script type="text/javascript" src="includes/js/menu_effects.js"></script>
        <script type="text/javascript" src="includes/js/jquery.browser.min.js"></script>

        <link rel="stylesheet" href="includes/css/t-012/jquery-ui.css" type="text/css" media="screen" />
        <script type="text/javascript" src="includes/functions_general.js"></script>

        <link rel="stylesheet" type="text/css" href="includes/bloqueador/mascara.css" />
        <script type="text/javascript" src="includes/bloqueador/mascara.js"></script>

        <!--[if IE]>
        <script src="https://neubox.net/libs/beauty_tooltip/excanvas.js" type="text/javascript" charset="utf-8"></script>
        <![endif]-->
        <script type="text/javascript" src="https://neubox.net/libs/beauty_tooltip/jquery.bt.js"></script>

        <link rel="stylesheet" href="https://neubox.net/libs/EngineValidationBasic/validationEngine.css" type="text/css" media="all" />
        <script type="text/javascript" src="https://neubox.net/libs/EngineValidationBasic/validationEngine.js"></script>
        <script type="text/javascript" src="https://neubox.net/libs/EngineValidationBasic/validationEngine_EN.js"></script>
        <link rel="stylesheet" href="https://neubox.net/libs/masking/masking.css" type="text/css" media="all" />
        <script type="text/javascript" src="https://neubox.net/libs/masking/masking.js"></script>

        <link rel="stylesheet" href="https://neubox.net/libs/estilizador/chosen.css" type="text/css" media="all" />
        <script type="text/javascript" src="https://neubox.net/libs/estilizador/chosen.jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="flexigrid/css/flexigrid.css" />
        <script type="text/javascript" src="flexigrid/flexigrid_updated.js"></script>

        <script type="text/javascript" src="includes/functions_general.js"></script>
        <link rel="stylesheet" href="includes/css/jquery-ui-1.8.18.custom.css" type="text/css" media="screen" />

<script type="text/javascript" language="javascript">
$.ajaxSetup ({
    cache: false
});

$(document).ready(function(){
    //When the page loads for the first time, it shows the Home and News content
    ajaxCall('Home.php');

    //Displays Home Content with News when Logo is Clicked
    $("#logo").click(function(){
        ajaxCall('Home.php');
    });
    $(".menuHome").click(function(){
        ajaxCall('Home.php');
    });
    //Displays SubMenu Contents when a SubMenu is clicked
    $(".opcionMenu").click(function(){
        //load selected section
        switch(this.id){
            case "sub1":
                ajaxCall('Info.php');
                break;
            case "sub2":
                ajaxCall('Billing.php');
                break;
            case "sub3":
                ajaxCall('ActionLog.php');
                break;
            case "sub4":
                ajaxCall('Services.php');
                break;
            case "sub5":
                ajaxCall('Domains.php');
                break;
            case "sub6":
                ajaxCall('Transfers.php');
                break;
            case "sub7":
                ajaxCall('Hosting.php');
                break;
            case "sub8":
                ajaxCall('OtherServices.php');
                break;
            case "sub9":
                ajaxCall('ContactsWhoIs.php');
                break;
            case "sub10":
                ajaxCall('ElectronicBilling.php');
                break;
            case "sub11":
                ajaxCall('SEO.php');
                break;
            case "sub12":
                ajaxCall('Backorders.php');
                break;
            case "sub13":
                ajaxCall('MyClients.php');
                break;
            case "sub14":
                ajaxCall('Layouts.php');
                break;
            case "sub15":
                ajaxCall('DNS.php');
                break;
            case "sub16":
                ajaxCall('ListSEO.php');
                break;
            default:
                break;
        }
    });
});

function ajaxCall(url){
    $(".contentMask").mask('Cargando');
    $("#forma_info").validationEngine('hide');
    $.ajax({
        url: url,
        dataType: 'html',
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            //Once we receive the data, set it to the content pane.
            $("#content").text();
            $("#content").html(data);
            $(".contentMask").unmask();
        }
    });
}

function creaGridContactos(){
        $("#contactos_table").flexigrid
        ({
            url: 'Datatables/dt-contactos.php',
            dataType: 'json',
            colModel : [
                {display: 'Contactos', name : 'contacto_Neubox', width : 542, sortable : true, align: 'center', hide: false},
                {display: 'Editar', name : 'edit', width : 100, sortable : false, align: 'center', hide: false},
                {display: 'Eliminar', name : 'delete', width : 100, sortable : false, align: 'center', hide: false}
            ],
            searchitems : [
		{display: 'Contactos', name : 'contacto_Neubox'}
		],
            sortname: "contacto_Neubox",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Contactos',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '780',
            width: 'auto',
            height: '400'
        });
}

function creaGridBilling(){
        $("#billing_table").flexigrid
        ({
            url: 'Datatables/dt-billing.php',
            dataType: 'json',
            colModel : [
                {display: 'R.F.C.', name : 'facturacion_rfc', width : 160, sortable : true, align: 'center', hide: false},
                {display: 'Raz&oacute;n Social', name : 'facturacion_nombre', width : 400, sortable : true, align: 'center', hide: false},
                {display: 'Editar', name : 'edit', width : 65, sortable : false, align: 'center', hide: false},
                {display: 'Eliminar', name : 'delete', width : 65, sortable : false, align: 'center', hide: false}
            ],
            searchitems : [
		{display: 'R.F.C.', name : 'facturacion_rfc'},
                {display: 'Raz&oacute;n Social', name : 'facturacion_nombre'}
		],
            sortname: "facturacion_rfc",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Facturacion',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '740',
            width: 'auto',
            height: '320'
        });
}

function creaGridActionLog(){
        $("#actionLog_table").flexigrid
        ({
            url: 'Datatables/dt-actionLog.php',
            dataType: 'json',
            colModel : [
                {display: 'Orden', name : 'orden', width : 100, sortable : true, align: 'center'},
                {display: 'Acci&oacute;n', name : 'accion_texto', width : 500, sortable : true, align: 'center'},
                {display: 'D&iacute;a', name: 'fechayhora', width : 150, sortable : true, align: 'center'},
                {display: 'Hora', name: 'hora', width : 100, sortable : false, align: 'center'}
            ],
            searchitems : [
		{display: 'Orden', name : 'orden'},
                {display: 'Acci&oacute;n', name : 'accion_texto'}
		],
            sortname: "fechayhora",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,10,13,50,100],
            rp: 13,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Registro de Acciones',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '900',
            width: 'auto',
            height: '400',
            onSuccess: function(){        
                $(".description").live("mouseover mouseout", function(event){   
                      var html = $(this).prev().val();
                      if ( event.type == "mouseover" ) {
                          $(this).bt(      
                                html,
                                {
                                  trigger: 'hover',
                                  width: 350,
                                  fill: 'rgba(134, 134, 134, .95)',
                                  strokeWidth: 0, /*no stroke*/
                                  spikeLength: 10,
                                  spikeGirth: 10,
                                  cornerRadius: 7,
                                  positions: ['bottom'],
                                  cssStyles: {
                                    fontFamily: 'Verdana,arial,sans-serif',                     
                                    color:'#FFFFFF',
                                    lineHeight:'100%'
                                  }
                            });
                            $(this).btOn();
                      } else {
                          $(this).btOff();
                      }
                  });
             }
        });
}

function creaGridServices(){
        $("#services_table").flexigrid
        ({
            url: 'Datatables/dt-services.php',
            dataType: 'json',
            colModel : [
                {display: 'Orden', name : 'contratacion_id', width : 100, sortable : true, align: 'center'},
                {display: 'Estado',  width : 100, sortable : true, align: 'center'},
                {display: 'Concepto',  width : 600, sortable : false, align: 'center'},
                {display: 'Activaci&oacute;n',  width : 100, sortable : true, align: 'center'},
                {display: 'Vencimiento', width : 100, sortable : true, align: 'center'}
            ],
            searchitems : [
		{display: 'Orden', name : 'contratacion_id'}
		],
            sortname: "contratacion_id",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Todos',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '1050',
            width: 'auto',
            height: 'auto'
        });
}

function creaGridDomains(){
        $("#domains_table").flexigrid
        ({
            url: 'Datatables/dt-domains.php',
            dataType: 'json',
            colModel : [
                {display: 'Orden', name : 'c.contratacion_id', width : 100, sortable : true, align: 'center'},
                {display: 'Dominio', name : 'ec_dnombre', width : 300, sortable : false, align: 'center'},
                {display: 'Activaci&oacute;n', name : 'fechaActivacion', width : 100, sortable : false, align: 'center'},
                {display: 'Vencimiento', name : 'fechaVencimiento', width : 100, sortable : false, align: 'center'},
                {display: 'Contactos', name : 'boton1', width : 90, sortable : false, align: 'center'},
                {display: 'Name Servers', name : 'boton2', width : 90, sortable : false, align: 'center'},
                {display: 'Redirecci&oacute;n', name : 'boton3', width : 90, sortable : false, align: 'center'},
                {display: 'AuthCode', name : 'boton4', width : 90, sortable : false, align: 'center'},
                {display: '', name : 'checkBox', width : 20, sortable : false, align: 'center'}
            ],
            searchitems : [
		{display: 'Orden', name : 'c.contratacion_id'},
                {display: 'Dominio', name : 'ec_dnombre'}
		],
            sortname: "c.contratacion_id",
            sortorder: "desc",
            minwidth: 100,
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Lista de Dominios',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '1050',
            width: 'auto',
            height: '360'
        });
}

function creaGridSEO(){
        $("#seo_table").flexigrid
        ({
            url: 'Datatables/dt-seo.php',
            dataType: 'json',
            colModel : [
                {display: 'Orden', name : 'c.contratacion_id', width : 100, sortable : true, align: 'center'},
                {display: 'Dominio', name : 'ec_extra', width : 300, sortable : false, align: 'center'},
                {display: 'Activaci&oacute;n', name : 'fechaActivacion', width : 100, sortable : false, align: 'center'},
                {display: 'Vencimiento', name : 'fechaVencimiento', width : 100, sortable : false, align: 'center'}
            ],
            searchitems : [
		{display: 'Orden', name : 'c.contratacion_id'},
		],
            sortname: "c.contratacion_id",
            sortorder: "desc",
            minwidth: 100,
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Lista de Planes SEO',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '1050',
            width: 'auto',
            height: '360'
        });
}

function creaGridTransfers(){
        $("#transfers_table").flexigrid
        ({
            url: 'Datatables/dt-transfers.php',
            dataType: 'json',
            colModel : [
                {display: 'Orden', name : 'CC.contratacion_id', width : 120, sortable : true, align: 'center'},
                {display: 'Estado', name : 'estado', width : 100, sortable : false, align: 'center'},
                {display: 'Dominio', name : 'dominio', width : 410, sortable : false, align: 'center'},
                {display: 'Transferir', name : 'transfer', width : 100, sortable : false, align: 'center'}
            ],
            searchitems : [
		{display: 'Orden', name : 'CC.contratacion_id'}
		],
            sortname: "CC.contratacion_id",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Dominios por Transferir',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '780',
            width: 'auto',
            height: '330',
            onSuccess: function(){
                $(".description").live("mouseover mouseout", function(event){
                      var html = $(this).prev().val();
                      if ( event.type == "mouseover" ) {
                          $(this).bt(
                                html,
                                {
                                  trigger: 'hover',
                                  width: 'auto',
                                  fill: 'rgba(134, 134, 134, .95)',
                                  strokeWidth: 0, /*no stroke*/
                                  spikeLength: 10,
                                  spikeGirth: 10,
                                  padding: 10,
                                  cornerRadius: 7,
                                  cssStyles: {
                                    fontFamily: 'Verdana,arial,sans-serif',
                                    color:'#FFFFFF',
                                    lineHeight:'120%'
                                  }
                            });
                            $(this).btOn();
                      } else {
                          $(this).btOff();
                      }
                  });
             }
        });
}

function creaGridHosting(){
        $("#hosting_table").flexigrid({
            url: 'Datatables/dt-hosting.php',
            dataType: 'json',
            colModel : [
                {display: 'Orden', name : 'o.contratacion_id', width : 100, sortable : true, align: 'center'},
                {display: 'Estado', name : 'contratacion_estado', width : 65, sortable : true, align: 'center'},
                {display: 'Concepto', name : 'contratacion_concepto', width : 250, sortable : false, align: 'center'},
                {display: 'Activaci&oacute;n', name : 'activacion', width : 100, sortable : false, align: 'center'},
                {display: 'Vencimiento', name : 'contratacion_fecha', width : 100, sortable : false, align: 'center' },
                {display: 'Sitio en Minutos', name : 'sitebuilder', width : 100, sortable : false, align: 'center'},
                {display: 'Panel de Control', name : 'cPanel', width : 100, sortable : false, align: 'center'},
                {display: 'Upgrade', name : 'update', width : 150, sortable : false, align: 'center'}
            ],
            searchitems : [
		{display: 'R.F.C.', name : 'o.contratacion_id'}
		],
            sortname: "o.contratacion_id",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: false,
            rpOptions: [5,7,9,50,100],
            rp: 9,
            nowrap: false,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Mis Planes',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '1031',
            width: 'auto',
            height: '400',
            onSuccess: function(){
                $(".description").live("mouseover mouseout", function(event){
                      var html = $(this).prev().val();
                      if ( event.type == "mouseover" ) {
                          $(this).bt(
                                html,
                                {
                                  trigger: 'hover',
                                  width: 60,
                                  fill: 'rgba(134, 134, 134, .95)',
                                  strokeWidth: 0, /*no stroke*/
                                  spikeLength: 10,
                                  spikeGirth: 10,
                                  cornerRadius: 7,
                                  cssStyles: {
                                    fontFamily: 'Verdana,arial,sans-serif',
                                    color:'#FFFFFF',
                                    lineHeight:'100%'
                                  }
                            });
                            $(this).btOn();
                      } else {
                          $(this).btOff();
                      }
                  });
             }
        });
}

function creaGridOtherServices(){
        $("#otherServices_table").flexigrid
        ({
            url: 'Datatables/dt-otherServices.php',
            dataType: 'json',
            colModel : [
                {display: 'Orden', name : 'o.contratacion_id', width : 100, sortable : true, align: 'center'},
                {display: 'Estado', name : 'contratacion_estado', width : 100, sortable : true, align: 'center'},
                {display: 'Concepto', name : 'contratacion_concepto', width : 600, sortable : false, align: 'center'},
                {display: 'Activaci&oacute;n', name : 'contratacion_expira', width : 100, sortable : true, align: 'center'},
                {display: 'Vencimiento', name : 'contratacion_fecha', width : 100, sortable : true, align: 'center'}
            ],
            searchitems : [
		{display: 'R.F.C.', name : 'o.contratacion_id'},
                {display: 'Raz&oacute;n Social', name : 'contratacion_estado'},
                {display: 'Raz&oacute;n Social', name : 'contratacion_concepto'},
                {display: 'Raz&oacute;n Social', name : 'contratacion_fecha'}
		],
            sortname: "o.contratacion_id",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Otros Productos',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: 'auto',
            width: 'auto',
            height: 'auto'
        });
}

function creaGridElectronicBilling(){
        $("#electronicBilling_table").flexigrid
        ({
            url: 'Datatables/dt-electronicBilling.php',
            dataType: 'json',
            colModel : [
                {display: 'Folio', name : 'folio', width : 140, sortable : true, align: 'center'},
                {display: 'Fecha', name : 'fecha', width : 130, sortable : true, align: 'center'},
                {display: 'Subtotal', name : 'subtotal', width : 130, sortable : false, align: 'center'},
                {display: 'IVA', name : 'iva', width : 130, sortable : false, align: 'center'},
                {display: 'Total', name : 'total', width : 130, sortable : true, align: 'center'},
                {display: 'PDF', name: 'pdf', width : 100, sortable : false, align: 'center'},
                {display: 'XML', name: 'xml', width : 100 , sortable : false, align: 'center'}
                ],
            searchitems : [
		{display: 'Folio', name : 'folio'},
                {display: 'Fecha', name : 'fecha'}
		],
            sortname: "fecha",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Facturas Electr&oacute;nicas',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '950',
            width: 'auto',
            height: '330'
        });
}

function creaGridActives(){
        $("#actives_table").flexigrid
        ({
            url: 'Datatables/dt-actives.php',
            dataType: 'json',
            colModel : [
                {display: 'Servicio', name : 'servicio_cliente_id', width : 70, sortable : true, align: 'center'},
                {display: 'Estatus', name : 'status', width : 65, sortable : false, align: 'center'},
                {display: 'Dominio', name : 'domain_name', width : 400, sortable : false, align: 'center'},
                {display: 'Expiraci&oacute;n Dominio', name : 'expiration', width : 150, sortable : false, align: 'center'},
                {display: 'Expiraci&oacute;n Servicio', name : 'service_expiration', width : 150, sortable : false, align: 'center'}
            ],
            searchitems : [
		{display: 'Servicio', name : 'servicio_cliente_id'}
		],
            sortname: "servicio_cliente_id",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Activos',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '900',
            width: 'auto',
            height: '320',
            onSuccess: function(){
                $(".description").live("mouseover mouseout", function(event){
                      var html = $(this).prev().val();
                      if ( event.type == "mouseover" ) {
                          $(this).bt(
                                html,
                                {
                                  trigger: 'hover',
                                  width: 300,
                                  fill: 'rgba(134, 134, 134, .95)',
                                  strokeWidth: 0, /*no stroke*/
                                  spikeLength: 10,
                                  spikeGirth: 10,
                                  cornerRadius: 7,
                                  cssStyles: {
                                    fontFamily: 'Verdana,arial,sans-serif',
                                    color:'#FFFFFF',
                                    lineHeight:'100%'
                                  }
                            });
                            $(this).btOn();
                      } else {
                          $(this).btOff();
                      }
                  });
             }
        });
}

function creaGridCaptured(){
        $("#captured_table").flexigrid
        ({
            url: 'Datatables/dt-captured.php',
            dataType: 'json',
            colModel : [
                {display: 'Orden', name : 'numero_orden', width : 100, sortable : true, align: 'center'},
                {display: 'Servicio', name : 'servicio_cliente_id', width : 70, sortable : true, align: 'center'},
                {display: 'Estatus', name : 'estatus', width : 65, sortable : false, align: 'center'},
                {display: 'Dominio', name : 'domain_name', width : 400, sortable : false, align: 'center'},
                {display: 'Expiraci&oacute;n Dominio', name : 'expiration', width : 150, sortable : false, align: 'center'},
                {display: 'Expiraci&oacute;n Servicio', name : 'service_expiration', width : 150, sortable : false, align: 'center'}
            ],
            searchitems : [
                {display: 'Orden', name : 'numero_orden'},
		{display: 'Servicio', name : 'servicio_cliente_id'}
		],
            sortname: "numero_orden",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Capturados',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '1010',
            width: 'auto',
            height: '320',
            onSuccess: function(){
                $(".description").live("mouseover mouseout", function(event){
                      var html = $(this).prev().val();
                      if ( event.type == "mouseover" ) {
                          $(this).bt(
                                html,
                                {
                                  trigger: 'hover',
                                  width: 300,
                                  fill: 'rgba(134, 134, 134, .95)',
                                  strokeWidth: 0, /*no stroke*/
                                  spikeLength: 10,
                                  spikeGirth: 10,
                                  cornerRadius: 7,
                                  cssStyles: {
                                    fontFamily: 'Verdana,arial,sans-serif',
                                    color:'#FFFFFF',
                                    lineHeight:'100%'
                                  }
                            });
                            $(this).btOn();
                      } else {
                          $(this).btOff();
                      }
                  });
             }
        });
}

function creaGridHistory(){
        $("#history_table").flexigrid
        ({
            url: 'Datatables/dt-history.php',
            dataType: 'json',
            colModel : [
                {display: 'Servicio', name : 'servicio_cliente_id', width : 70, sortable : true, align: 'center'},
                {display: 'Estatus', name : 'estatus', width : 65, sortable : true, align: 'center'},
                {display: 'Dominio', name : 'domain_name', width : 400, sortable : false, align: 'center'},
                {display: 'Expiraci&oacute;n Dominio', name : 'expiration', width : 150, sortable : false, align: 'center'},
                {display: 'Nueva Expiraci&oacute;n', name : 'new_expiration', width : 150, sortable : false, align: 'center'},
                {display: 'Fecha Terminaci&oacute;n', name : 'end_date', width : 150, sortable : false, align: 'center'}
            ],
            searchitems : [
                {display: 'Servicio', name : 'servicio_cliente_id'}
		],
            sortname: "servicio_cliente_id",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Historial',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '1060',
            width: 'auto',
            height: '320',
            onSuccess: function(){
                $(".description").live("mouseover mouseout", function(event){
                      var html = $(this).prev().val();
                      if ( event.type == "mouseover" ) {
                          $(this).bt(html, {
                                  trigger: 'hover',
                                  width: 300,
                                  fill: 'rgba(134, 134, 134, .95)',
                                  strokeWidth: 0, /*no stroke*/
                                  spikeLength: 10,
                                  spikeGirth: 10,
                                  cornerRadius: 7,
                                  cssStyles: {
                                    fontFamily: 'Verdana,arial,sans-serif',
                                    color:'#FFFFFF',
                                    lineHeight:'100%'
                                  }
                            });
                            $(this).btOn();
                      } else {
                          $(this).btOff();
                      }
                  });
             }
        });
}

function creaGridMyClients(){
        $("#myClients_table").flexigrid
        ({
            url: 'Datatables/dt-myClients.php',
            dataType: 'json',
            colModel : [
                {display: 'E-Mail', name : 'cliente_email', width : 230, sortable : true, align: 'center', hide: false},
                {display: 'Nombre', name : 'cliente_nombre', width : 200, sortable : true, align: 'center', hide: false},
                {display: 'Apellidos', name : 'cliente_apellidos', width : 200, sortable : true, align: 'center', hide: false},
                {display: 'Contacto', name : 'cliente_registrante', width : 200, sortable : true, align: 'center', hide: false}
            ],
            searchitems : [
                {display: 'E-Mail', name : 'cliente_email'},
                {display: 'Nombre', name : 'cliente_nombre'},
                {display: 'Apellidos', name : 'cliente_apellidos'},
                {display: 'Contacto', name : 'cliente_registrante'}
		],
            sortname: "cliente_email",
            sortorder: "desc",
            singleSelect: true,
            usepager: true,
            resizable: false,
            useRp: true,
            rpOptions: [7,9,11,50,100],
            rp: 11,
            pagestat: 'Mostrando {from}-{to} de {total} registros',
            pagetext: 'P&aacute;gina',
            outof: 'de',
            procmsg: 'Cargando registros, espere un momento ...',
            title: 'Mis Clientes',
            nomsg: 'No se encontraron coincidencias',
            findtext: 'Buscar:',
            showTableToggleBtn: false,
            blockOpacity: 0.7,
            //width: '880',
            width: 'auto',
            height: '330'
        });
}

//The PopUp where you can edit your Billing Information
function popUpFormaEditBilling(data, url, forma){
    $(".contentMask").mask('Cargando');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 450,
                draggable: false,
                modal: true,
                resizable:false,
                open: function(){
                    $("#frmDatosFactura").validationEngine({scroll:false});
                },
                dragStart: function(){
                     $("#frmDatosFactura").validationEngine('hide');
                },
                close: function(){
                     $("#frmDatosFactura").validationEngine('hide');
                },
                dragStop: function(){
                     $("#frmDatosFactura").validationEngine('validateform');
                },
                buttons: [
                    {
                        text: "Guardar",
                        click: function() {
                            var municipioB = document.getElementById('txtFMunicipio').value;
                            var nombreB = document.getElementById('txtFNombre').value;
                            var calleB = document.getElementById('txtFCalle').value;
                            var ciudadB = document.getElementById('txtFCiudad').value;
                            var coloniaB = document.getElementById('txtFColonia').value;
                            document.getElementById('txtFMunicipio').value = document.getElementById('txtFMunicipio').value.trim();
                            document.getElementById('txtFNombre').value = document.getElementById('txtFNombre').value.trim();
                            document.getElementById('txtFCalle').value = document.getElementById('txtFCalle').value.trim();
                            document.getElementById('txtFCiudad').value = document.getElementById('txtFCiudad').value.trim();
                            document.getElementById('txtFColonia').value = document.getElementById('txtFColonia').value.trim();
                            if($("#frmDatosFactura").validationEngine('validateform')){
                                /*document.getElementById('txtFMunicipio').value = municipioB;
                                document.getElementById('txtFNombre').value = nombreB;
                                document.getElementById('txtFCalle').value = calleB;
                                document.getElementById('txtFCiudad').value = ciudadB;
                                document.getElementById('txtFColonia').value = coloniaB;*/
                                var factura_id = document.getElementById('txtFId').value;
                                var rfc = document.getElementById('txtFRfc').value;
                                var nombre = document.getElementById('txtFNombre').value;
                                var calle = document.getElementById('txtFCalle').value;
                                var exterior = document.getElementById('txtFExterior').value;
                                var interior = document.getElementById('txtFInterior').value;
                                var colonia = document.getElementById('txtFColonia').value;
                                var ciudad = document.getElementById('txtFCiudad').value;
                                var estado = document.getElementById('txtFEstado').value;
                                var municipio = document.getElementById('txtFMunicipio').value;
                                var cp = document.getElementById('txtFCp').value;
                                if(cp.length < 5){
                                    cp = '0'+cp;
                                    document.getElementById('txtFCp').value = cp;
                                }

                                var var_datos = escape(rfc) + '|' + escape(nombre) + '|' + escape(calle) + '|' + escape(exterior) + '|' + escape(interior) + '|';
                                var_datos += escape(colonia) + '|' + escape(ciudad) + '|' + escape(estado) + '|' + escape(municipio) + '|' + escape(cp);
                                var params = 'action=' + forma + '&factura_id=' + factura_id + '&data=' + var_datos;
                                $.ajax({
                                    url: 'dataAccess.php',
                                    type: 'POST',
                                    data: params,
                                    success: function(data){
                                        if(data == '1|1'){
                                            // el nuevo rfc estara disponible para uso futuro.
                                            var newHTML = "<div><div class='message success'>Operaci&oacute;n realizada satisfactoriamente.<br/>El nuevo RFC estar&aacute; disponible para uso futuro.</div></div>";
                                        }
                                        else if(data == '1|2'){
                                            //los nuevos datos estaran para uso futuro. los anteriors con facturas ya emitidas no pueden cambiarse
                                            var newHTML = "<div><div class='message success'>Operaci&oacute;n realizada satisfactoriamente.<br/>Los nuevos datos estar&aacute;n disponibles para uso futuro. <br />Las facturas ya emitidas con esos datos no pueden ser cambiadas.</div></div>";
                                        }
                                        else{
                                            var newHTML = "<div><div class='message warning'>"+data+"</div></div>";
                                        }
                                        $(newHTML).dialog({
                                            width: 450,
                                            modal: true,
                                            draggable: false,
                                            resizable: false,
                                            buttons: [
                                                {
                                                    text: "Cerrar",
                                                    click: function() { $(this).dialog("close").remove(); }
                                                }
                                            ]
                                        });
                                        $( 'a.ui-dialog-titlebar-close' ).remove();
                                    }
                                });
                                $(this).dialog("close").remove();
                                ajaxCall('Billing.php');
                            }
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp to confirm a delete billing request
function popUpDeleteBilling(data, url, forma, factura_id){
    $(".contentMask").mask('Cargando');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 400,
                draggable: false,
                modal: true,
                resizable:false,
                buttons: [
                    {
                        text: "Eliminar",
                        click: function() {
                            var params = 'action=' + forma + '&factura_id=' + factura_id;
                            $.ajax({
                                url: 'dataAccess.php',
                                type: 'POST',
                                data: params,
                                success: function(data){
                                    if(data == '1|1'){
                                        var newHTML = "<div><div class='message success'>La eliminaci&oacute;n se realiz&oacute; exitosamente!</div></div>";
                                    }
                                    else{
                                        var newHTML = "<div><div class='message warning'>"+data+"</div></div>";
                                    }
                                    $(newHTML).dialog({
                                        width: 450,
                                        modal: true,
                                        draggable: false,
                                        resizable: false,
                                        buttons: [
                                            {
                                                text: "Cerrar",
                                                click: function() { $(this).dialog("close").remove(); }
                                            }
                                        ]
                                    });
                                    $( 'a.ui-dialog-titlebar-close' ).remove();
                                }
                            });
                            $(this).dialog("close").remove();
                            ajaxCall('Billing.php');
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp where you can edit the WhoIs Contact information directly from Info.php
function popUpFormaEditContact(data, url, forma){
    $(".contentMask").mask('Cargando');
    contacto = document.getElementById('contacto').value;
    $.ajax({
        url: url,
        type: 'POST',
        data: data+'&contacto='+contacto,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 450,
                draggable: false,
                modal: true,
                resizable: false,
                open: function(){
                    $("#editInfoForm").validationEngine({scroll:false});
                },
                dragStart: function(){
                     $("#editInfoForm").validationEngine('hide');
                },
                close: function(){
                     $("#editInfoForm").validationEngine('hide');
                },
                dragStop: function(){
                     $("#editInfoForm").validationEngine('validateform');
                },
                buttons: [
                    {
                        text: "Guardar",
                        click: function() {
                            if($("#editInfoForm").validationEngine('validateform')){
                                var contacto = document.getElementById('cnct_id').value;
                                var nombre = document.getElementById('cnct_nombre').value;
                                var email = document.getElementById('cnct_email').value;
                                var direccion = document.getElementById('cnct_direccion').value;
                                var cp = document.getElementById('cnct_cp').value;
                                if(cp.length < 5){
                                    cp = '0'+cp;
                                    document.getElementById('cnct_cp').value = cp;
                                }
                                var pais = document.getElementById('cnct_ComboPais').value;
                                var estado = document.getElementById('miestados').value;
                                var ciudad= document.getElementById('cnct_ciudad').value;
                                var telcc = document.getElementById('cnct_telcc').value;
                                var tel = document.getElementById('cnct_tel').value;
                                if(document.getElementById('cnct_empresa').value == '')
                                    document.getElementById('cnct_empresa').value = 'No Company';
                                var empresa = document.getElementById('cnct_empresa').value;

                                var var_datos = escape(nombre) + '|' + escape(email) + '|' + escape(direccion) + '|' + escape(cp) + '|' + escape(pais) + '|';
                                var_datos += escape(estado) + '|' + escape(ciudad) + '|' + escape(telcc) + '|' + escape(tel) + '|' + escape(empresa) + '|' + escape(contacto);

                                var params = 'action=' + forma + '&data=' + var_datos;
                                $.ajax({
                                    url: 'dataAccess.php',
                                    type: 'POST',
                                    data: params,
                                    success: function(data){
                                        if(data == '1|1'){
                                            var newHTML = "<div><div class='message success'>La modificaci&oacute;n del contacto se realiz&oacute; exitosamente!</div></div>";
                                        }
                                        else{
                                            var newHTML = "<div><div class='message warning'>"+data+"</div></div>";
                                        }
                                        $(newHTML).dialog({
                                            width: 450,
                                            modal: true,
                                            draggable: false,
                                            resizable: false,
                                            buttons: [
                                                {
                                                    text: "Cerrar",
                                                    click: function() { $(this).dialog("close").remove(); }
                                                }
                                            ]
                                        });
                                        $( 'a.ui-dialog-titlebar-close' ).remove();
                                    }
                                });
                                $(this).dialog("close").remove();
                                ajaxCall('Info.php');
                            }
                        }
                    },
                    {
                        text: "Tomar datos de cliente",
                        click: function() {
                            document.getElementById('cnct_nombre').value=document.getElementById('nombre').value + ' ' + document.getElementById('apellidos').value;
                            document.getElementById('cnct_email').value=document.getElementById('email').value;
                            document.getElementById('cnct_direccion').value=document.getElementById('direccion').value;
                            document.getElementById('cnct_cp').value=document.getElementById('cp').value;
                            document.getElementById('cnct_ComboPais').value=document.getElementById('pais').value;
                            document.getElementById('cnct_ciudad').value=document.getElementById('ciudad').value;
                            document.getElementById('cnct_telcc').value='';
                            document.getElementById('cnct_tel').value=document.getElementById('telefono').value;
                            document.getElementById('cnct_empresa').value=document.getElementById('empresa').value;
                            dameEstados('cnct_ComboEdos','cnct_ComboEdos','cnct_ComboPais','','207px','validate[required] combobox','si');
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp where you can edit the WhoIs Contact information directly from Info.php
function popUpFormaNewContact(data, url, forma){
    $(".contentMask").mask('Cargando');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 450,
                draggable: false,
                modal: true,
                resizable: false,
                open: function(){
                    $("#editInfoForm").validationEngine({scroll:false});
                },
                dragStart: function(){
                     $("#editInfoForm").validationEngine('hide');
                },
                close: function(){
                     $("#editInfoForm").validationEngine('hide');
                },
                dragStop: function(){
                     $("#editInfoForm").validationEngine('validateform');
                },
                buttons: [
                    {
                        text: "Guardar",
                        click: function() {
                            if($("#editInfoForm").validationEngine('validateform')){
                                var contacto = document.getElementById('cnct_id').value;
                                var nombre = document.getElementById('cnct_nombre').value;
                                var email = document.getElementById('cnct_email').value;
                                var direccion = document.getElementById('cnct_direccion').value;
                                var cp = document.getElementById('cnct_cp').value;
                                if(cp.length < 5){
                                    cp = '0'+cp;
                                    document.getElementById('cnct_cp').value = cp;
                                }
                                var pais = document.getElementById('cnct_ComboPais').value;
                                var estado = document.getElementById('miestados').value;
                                var ciudad= document.getElementById('cnct_ciudad').value;
                                var telcc = document.getElementById('cnct_telcc').value;
                                var tel = document.getElementById('cnct_tel').value;
                                if(document.getElementById('cnct_empresa').value == '')
                                    document.getElementById('cnct_empresa').value = 'No Company';
                                var empresa = document.getElementById('cnct_empresa').value;

                                var var_datos = escape(nombre) + '|' + escape(email) + '|' + escape(direccion) + '|' + escape(cp) + '|' + escape(pais) + '|';
                                var_datos += escape(estado) + '|' + escape(ciudad) + '|' + escape(telcc) + '|' + escape(tel) + '|' + escape(empresa) + '|' + escape(contacto);

                                var params = 'action=' + forma + '&data=' + var_datos;
                                $.ajax({
                                    url: 'dataAccess.php',
                                    type: 'POST',
                                    data: params,
                                    success: function(data){
                                        if(data == '1|1'){
                                            var newHTML = "<div><div class='message success'>La creaci&oacute;n del contacto se realiz&oacute; exitosamente!</div></div>";
                                        }
                                        else{
                                            var newHTML = "<div><div class='message warning'>"+data+"</div></div>";
                                        }
                                        $(newHTML).dialog({
                                            width: 450,
                                            modal: true,
                                            draggable: false,
                                            resizable: false,
                                            buttons: [
                                                {
                                                    text: "Cerrar",
                                                    click: function() { $(this).dialog("close").remove(); }
                                                }
                                            ]
                                        });
                                        $( 'a.ui-dialog-titlebar-close' ).remove();
                                    }
                                });
                                $(this).dialog("close").remove();
                                ajaxCall('ContactsWhoIs.php');
                            }
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp where you can edit the WhoIs Contact information from the ContactWhoIs Table
function popUpFormaEditContactTable(data, url, forma, contacto){
    $(".contentMask").mask('Cargando');
    $.ajax({
        url: url,
        type: 'POST',
        data: data+'&contacto='+contacto,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 450,
                draggable: false,
                modal: true,
                resizable: false,
                open: function(){
                    $("#editInfoForm").validationEngine({scroll:false});
                },
                dragStart: function(){
                     $("#editInfoForm").validationEngine('hide');
                },
                close: function(){
                     $("#editInfoForm").validationEngine('hide');
                },
                dragStop: function(){
                     $("#editInfoForm").validationEngine('validateform');
                },
                buttons: [
                    {
                        text: "Guardar",
                        click: function() {
                            $(".contentMask").mask('Cargando');
                            if($("#editInfoForm").validationEngine('validateform')){
                                var nombre = document.getElementById('cnct_nombre').value;
                                var email = document.getElementById('cnct_email').value;
                                var direccion = document.getElementById('cnct_direccion').value;
                                var cp = document.getElementById('cnct_cp').value;
                                if(cp.length < 5){
                                    cp = '0'+cp;
                                    document.getElementById('cnct_cp').value = cp;
                                }
                                var pais = document.getElementById('cnct_ComboPais').value;
                                var estado = document.getElementById('miestados').value;
                                var ciudad= document.getElementById('cnct_ciudad').value;
                                var telcc = document.getElementById('cnct_telcc').value;
                                var tel = document.getElementById('cnct_tel').value;
                                if(document.getElementById('cnct_empresa').value == '')
                                    document.getElementById('cnct_empresa').value = 'No Company';
                                var empresa = document.getElementById('cnct_empresa').value;
                                
                                var var_datos = escape(nombre) + '|' + escape(email) + '|' + escape(direccion) + '|' + escape(cp) + '|' + escape(pais) + '|';
                                var_datos += escape(estado) + '|' + escape(ciudad) + '|' + escape(telcc) + '|' + escape(tel) + '|' + escape(empresa) + '|' + escape(contacto);

                                var params = 'action=' + forma + '&data=' + var_datos;
                                $.ajax({
                                    url: 'dataAccess.php',
                                    type: 'POST',
                                    data: params,
                                    success: function(data){
                                        $(".contentMask").unmask();
                                        if(data == '1|1'){
                                            var newHTML = "<div><div class='message success'>La modificaci&oacute;n del contacto se realiz&oacute; exitosamente!</div></div>";
                                        }
                                        else{
                                            var newHTML = "<div><div class='message warning'>"+data+"</div></div>";
                                        }
                                        $(newHTML).dialog({
                                            width: 450,
                                            modal: true,
                                            draggable: false,
                                            resizable: false,
                                            buttons: [
                                                {
                                                    text: "Cerrar",
                                                    click: function() { $(this).dialog("close").remove(); }
                                                }
                                            ]
                                        });
                                        $( 'a.ui-dialog-titlebar-close' ).remove();
                                    }
                                });
                                $(this).dialog("close").remove();
                            }
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp where you can edit the WhoIs Contact information from the ContactDomain PopUp
function popUpFormaEditContactDomainPopUp(data, url, forma, item){
    $(".contentMask").mask('Cargando');
    var contacto = document.getElementById(item).value;
    $.ajax({
        url: url,
        type: 'POST',
        data: data+'&contacto='+contacto,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 450,
                draggable: false,
                modal: true,
                resizable: false,
                open: function(){
                    $("#editInfoForm").validationEngine({scroll:false});
                },
                dragStart: function(){
                     $("#editInfoForm").validationEngine('hide');
                },
                close: function(){
                     $("#editInfoForm").validationEngine('hide');
                },
                dragStop: function(){
                     $("#editInfoForm").validationEngine('validateform');
                },
                buttons: [
                    {
                        text: "Guardar",
                        click: function() {
                            if($("#editInfoForm").validationEngine('validateform')){
                                var nombre = document.getElementById('cnct_nombre').value;
                                var email = document.getElementById('cnct_email').value;
                                var direccion = document.getElementById('cnct_direccion').value;
                                var cp = document.getElementById('cnct_cp').value;
                                if(cp.length < 5){
                                    cp = '0'+cp;
                                    document.getElementById('cnct_cp').value = cp;
                                }
                                var pais = document.getElementById('cnct_ComboPais').value;
                                var estado = document.getElementById('miestados').value;
                                var ciudad= document.getElementById('cnct_ciudad').value;
                                var telcc = document.getElementById('cnct_telcc').value;
                                var tel = document.getElementById('cnct_tel').value;
                                if(document.getElementById('cnct_empresa').value == '')
                                    document.getElementById('cnct_empresa').value = 'No Company';
                                var empresa = document.getElementById('cnct_empresa').value;

                                var var_datos = escape(nombre) + '|' + escape(email) + '|' + escape(direccion) + '|' + escape(cp) + '|' + escape(pais) + '|';
                                var_datos += escape(estado) + '|' + escape(ciudad) + '|' + escape(telcc) + '|' + escape(tel) + '|' + escape(empresa) + '|' + escape(contacto);

                                var params = 'action=' + forma + '&data=' + var_datos;
                                $.ajax({
                                    url: 'dataAccess.php',
                                    type: 'POST',
                                    data: params,
                                    success: function(data){
                                        if(data == '1|1'){
                                            var newHTML = "<div><div class='message success'>La modificaci&oacute;n del contacto se realiz&oacute; exitosamente!</div></div>";
                                        }
                                        else{
                                            var newHTML = "<div><div class='message warning'>"+data+"</div></div>";
                                        }
                                        $(newHTML).dialog({
                                            width: 450,
                                            modal: true,
                                            draggable: false,
                                            resizable: false,
                                            buttons: [
                                                {
                                                    text: "Cerrar",
                                                    click: function() { $(this).dialog("close").remove(); }
                                                }
                                            ]
                                        });
                                        $( 'a.ui-dialog-titlebar-close' ).remove();
                                    }
                                });
                                $(this).dialog("close").remove();
                            }
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp to confirm a delete request of a WhoIs Contact
function popUpDeleteContact(data, url, forma, contacto, idCliente){
    $(".contentMask").mask('Cargando');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 400,
                draggable: false,
                modal: true,
                resizable:false,
                buttons: [
                    {
                        text: "Eliminar",
                        click: function() {
                            var params = 'action=' + forma + '&cliente_id=' + idCliente + '&data=' + contacto;
                            $.ajax({
                                url: 'dataAccess.php',
                                type: 'POST',
                                data: params,
                                success: function(data){
                                    if(data == '1|1'){
                                        var newHTML = "<div><div class='message success'>La eliminaci&oacute;n se realiz&oacute; exitosamente!</div></div>";
                                    }
                                    else{
                                        var newHTML = "<div><div class='message warning'>"+data+"</div></div>";
                                    }
                                    $(newHTML).dialog({
                                        width: 450,
                                        modal: true,
                                        draggable: false,
                                        resizable: false,
                                        buttons: [
                                            {
                                                text: "Cerrar",
                                                click: function() { $(this).dialog("close").remove(); }
                                            }
                                        ]
                                    });
                                    $( 'a.ui-dialog-titlebar-close' ).remove();
                                }
                            });
                            $(this).dialog("close").remove();
                            ajaxCall('ContactsWhoIs.php');
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp to edit the WhoIs Contact linked to a Domain
function popUpFormaEditContactDomain(id, domain, url, forma){
    $(".contentMask").mask('Cargando');
    var data = "contratacion_id="+id+"&dominio="+domain;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            if(data.indexOf('No se pudo establecer una conexion con el EPP para obtener los datos.<br />Intente m&aacute;s tarde.<br />') < 0 ){
                $(data).dialog({
                    width: 870,
                    draggable: false,
                    modal: true,
                    resizable: false,
                    open: function(){
                        $("#registrante").change(function() {
                            if(document.getElementById('registrante').value.indexOf("\'") != 0)
                                document.getElementById('editableregistrante').innerHTML = '<img class=\"imgClick\" src=\"panel_img/modificar.gif\" alt=\"Editar\" border=\"0\" height=\"16\" width=\"16\" onclick=\"popUpFormaEditContactDomainPopUp(\'type=edit\',\'forms/infoForms.php\', \'editContact\', \'registrante\')\"/>';
                            else
                                document.getElementById('editableregistrante').innerHTML = '';
                        });
                        $('#administrativo').change(function(){
                            if(document.getElementById('administrativo').value.indexOf("\'") != 0)
                                document.getElementById('editableadministrativo').innerHTML = '<img class=\"imgClick\" src=\"panel_img/modificar.gif\" alt=\"Editar\" border=\"0\" height=\"16\" width=\"16\" onclick=\"popUpFormaEditContactDomainPopUp(\'type=edit\',\'forms/infoForms.php\', \'editContact\', \'administrativo\')\"/>';
                            else
                                document.getElementById('editableadministrativo').innerHTML = '';
                        });
                        $('#tecnico').change(function(){
                            if(document.getElementById('tecnico').value.indexOf("\'") != 0)
                                document.getElementById('editabletecnico').innerHTML = '<img class=\"imgClick\" src=\"panel_img/modificar.gif\" alt=\"Editar\" border=\"0\" height=\"16\" width=\"16\" onclick=\"popUpFormaEditContactDomainPopUp(\'type=edit\',\'forms/infoForms.php\', \'editContact\', \'tecnico\')\"/>';
                            else
                                document.getElementById('editabletecnico').innerHTML = '';
                        });
                        $('#pago').change(function(){
                            if(document.getElementById('pago').value.indexOf("\'") != 0)
                                document.getElementById('editablepago').innerHTML = '<img class=\"imgClick\" src=\"panel_img/modificar.gif\" alt=\"Editar\" border=\"0\" height=\"16\" width=\"16\" onclick=\"popUpFormaEditContactDomainPopUp(\'type=edit\',\'forms/infoForms.php\', \'editContact\', \'pago\')\"/>';
                            else
                                document.getElementById('editablepago').innerHTML = '';
                        });
                    },
                    buttons: [
                        {
                            text: "Guardar",
                            click: function() {
                                var params = '';
                                var minus = 1;
                                var arreglo_info = domain.split("*");
                                //var orden = '';
                                if(arreglo_info.length == 1)
                                    minus = 0;
                                for  ( i=0; i<arreglo_info.length-minus; i++ )
                                {
                                    /*
                                    var arreglo_datos = arreglo_info[i].split(":");
                                    //Si no se pasa un id es porque hay un conjunto de campos de contacto para cada orden
                                    if(minus == 0) orden = id;
                                    else orden = arreglo_datos[2];*/

                                    var registrant = document.getElementById('registrante').value;
                                    var admin = document.getElementById('administrativo').value;
                                    var tech = document.getElementById('tecnico').value;
                                    var billing = document.getElementById('pago').value;

                                    var registrant0 = document.getElementById('o_registrante').value;
                                    var admin0 = document.getElementById('o_administrativo').value;
                                    var tech0 = document.getElementById('o_tecnico').value;
                                    var billing0 = document.getElementById('o_pago').value;

                                    // Crear la cadena de par�metros POST
                                    var contactos='';
                                    if( registrant!=registrant0 && registrant!='' && registrant.substr(0,1)!='\'' ) contactos += registrant;
                                    contactos += '|';

                                    if( admin!=admin0 && admin!='' && admin.substr(0,1)!='\'' ) contactos += admin;
                                    contactos += '|';

                                    if( tech!=tech0 && tech!='' && tech.substr(0,1)!='\'' ) contactos += tech;
                                    contactos += '|';

                                    if( billing!=billing0 && billing!='' && billing.substr(0,1)!='\'' ) contactos += billing;
                                    contactos += '*';

                                    //Añadir los contactos a los parámetros POST
                                    params += escape(contactos);

                                }

                                //Quitar el �ltimo '*' porque no es necesario
                                params = params.substr( 0, params.length-1 );
                                params='action='+forma+'&info_contactos='+params+'&datos_ordenes=' + domain;
                                $(".contentMask").mask('Cargando');
                                $.ajax({
                                    url: 'dataAccess.php',
                                    type: 'POST',
                                    data: params,
                                    success: function(data){
                                        $(data).dialog({
                                            width: 450,
                                            modal: true,
                                            draggable: false,
                                            resizable: false,
                                            buttons: [
                                                {
                                                    text: "Cerrar",
                                                    click: function() { $(this).dialog("close").remove(); }
                                                }
                                            ]
                                        });
                                        $(".contentMask").unmask();
                                        $( 'a.ui-dialog-titlebar-close' ).remove();
                                    }
                                });
                                $(this).dialog("close").remove();
                            }
                        },
                        {
                            text: "Cancelar",
                            click: function() { $(this).dialog("close").remove(); }
                        }
                    ]
                });
            }
            else{
                $(data).dialog({
                    width: 870,
                    draggable: false,
                    modal: true,
                    resizable: false,
                    buttons: [
                        {
                            text: "Cancelar",
                            click: function() { $(this).dialog("close").remove(); }
                        }
                    ]
                });
            }
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp to edit the DNS's assigned to a Domain
function popUpFormaEditDNSDomain(id, domain, url, forma){
    $(".contentMask").mask('Cargando');
    var data = "contratacion_id="+id+"&dominio="+domain;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            if(data.indexOf('No se pudo establecer una conexion con el EPP para obtener los datos.<br />Intente m&aacute;s tarde.<br />') < 0){
                $(data).dialog({
                    width: 870,
                    draggable: false,
                    modal: true,
                    resizable:false,
                    buttons: [
                        {
                            text: "Guardar",
                            click: function() {
                                var params='';
                                var ereg_dns = /^([a-z\d][a-z\d\-]*(\.[a-z\d][a-z\d\-]*){1,2}(\.[a-z]{2,4}){1,2})?$/i;
                                var ereg_ip = /^([\d]{1,3}\.){3}[\d]{1,3}$/;
                                var filtro_trim=/^\s+|\s+$/g;
                                var error=false;
                                var minus = 1;
                                var dominio = '';
                                var arreglo_ordenes = domain.split("*");

                                if(arreglo_ordenes.length == 1)
                                    minus = 0;

                                for  ( i=0; i<arreglo_ordenes.length-minus; i++ )
                                {

                                    //Si no se pasa un id es porque hay un conjunto de campos de contacto para cada orden
                                    /*
                                    if(id!='' && id!=null)
                                        orden = id;
                                    else
                                        orden = arreglo_datos[2];
                                    */
                                    if(arreglo_ordenes.length == 1)
                                    {
                                        dominio = arreglo_ordenes[i];
                                        //var orden = id;
                                    }
                                    else{
                                        arreglo_datos = arreglo_ordenes[i].split(":");
                                        dominio = arreglo_datos[0]+'.'+arreglo_datos[1];
                                    }

                                    var ereg_dominio = new RegExp(dominio.replace(/\./g,"\\.")+'$','i');

                                    for(x=1; x<5; x++)
                                    {
                                        var ip = document.getElementById('ip'+x+'_'/*+orden*/).value.replace(filtro_trim,'');
                                        var dns = document.getElementById('dns'+x+'_'/*+orden*/).value.replace(filtro_trim,'');

                                        if( ereg_dns.test(dns) || dns=='NC' ) //comprobar sintaxis del DNS
                                        {
                                            if ( ereg_dominio.test(dns) ) //ver si es un DNS propio del dominio
                                            {
                                                if( !ereg_ip.test(ip) ) //checar que la ip es correcta
                                                {
                                                    error=true;
                                                }
                                            }
                                            else ip='';

                                            if( error==false )
                                                params += dns+','+ip+'|';
                                        }
                                    }
                                    //Se cambia el último | por un * para indicar que siguen los datos de otro dominio y no de otro DNS.
                                    params=params.substr( 0, params.length-1 )+'*';
                                }

                                if(error==false)
                                {
                                    params='action='+forma+'&info_dns='+params.substr( 0, params.length-1 )+'&datos_ordenes=' + domain; //Le quitamos el �ltimo *
                                    $(".contentMask").mask('Cargando');
                                    $.ajax({
                                        url: 'dataAccess.php',
                                        type: 'POST',
                                        data: params,
                                        success: function(data){
                                            $(data).dialog({
                                                width: 450,
                                                modal: true,
                                                draggable: false,
                                                resizable: false,
                                                buttons: [
                                                    {
                                                        text: "Cerrar",
                                                        click: function() { $(this).dialog("close").remove(); }
                                                    }
                                                ]
                                            });
                                            $(".contentMask").unmask();
                                            $( 'a.ui-dialog-titlebar-close' ).remove();
                                        }
                                    });
                                }
                                $(this).dialog("close").remove();
                            }
                        },
                        {
                            text: "Cancelar",
                            click: function() { $(this).dialog("close").remove(); }
                        }
                    ]
                });
            }
            else{
                $(data).dialog({
                    width: 870,
                    draggable: false,
                    modal: true,
                    resizable:false,
                    buttons: [
                        {
                            text: "Cancelar",
                            click: function() { $(this).dialog("close").remove(); }
                        }
                    ]
                });
            }
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp to edit where a domain resolves throught a redirection
function popUpFormaEditRedirectDomain(id, domain, url, forma){
    $(".contentMask").mask('Cargando');
    var data = "contratacion_id="+id+"&dominio="+domain;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            if(data.indexOf('No se pudo establecer una conexion con el EPP para obtener los datos.<br />Intente m&aacute;s tarde.<br />') < 0){
                $(data).dialog({
                    width: 870,
                    draggable: false,
                    modal: true,
                    resizable:false,
                    buttons: [
                        {
                            text: "Guardar",
                            click: function() {
                                var filtro_trim=/^\s+|\s+$/g;
                                var filtro_http=/^http:\/\//i;

                                document.getElementById('txtRedir').value = document.getElementById('txtRedir').value.replace(filtro_trim,'');
                                document.getElementById('txtRedir').value = document.getElementById('txtRedir').value.replace(filtro_http,'');

                                var params = "action=" + forma + "&orden=" + id + "&dominio=" + domain + "&redir=" + document.getElementById('txtRedir').value;
                                $(".contentMask").mask('Cargando');
                                $.ajax({
                                    url: 'dataAccess.php',
                                    type: 'POST',
                                    data: params,
                                    success: function(data){
                                        $(data).dialog({
                                            width: 450,
                                            modal: true,
                                            draggable: false,
                                            resizable: false,
                                            buttons: [
                                                {
                                                    text: "Cerrar",
                                                    click: function() { $(this).dialog("close").remove(); }
                                                }
                                            ]
                                        });
                                        $(".contentMask").unmask();
                                        $( 'a.ui-dialog-titlebar-close' ).remove();
                                    }
                                });
                                $(this).dialog("close").remove();
                            }
                        },
                        {
                            text: "Eliminar",
                            click: function() {
                                document.getElementById('txtRedir').value = '';
                                var filtro_trim=/^\s+|\s+$/g;
                                var filtro_http=/^http:\/\//i;

                                var params = "action=" + forma + "&orden=" + id + "&dominio=" + domain + "&redir=" + document.getElementById('txtRedir').value;
                                $(".contentMask").mask('Cargando');
                                $.ajax({
                                    url: 'dataAccess.php',
                                    type: 'POST',
                                    data: params,
                                    success: function(data){
                                        $(data).dialog({
                                            width: 450,
                                            modal: true,
                                            resizable: false,
                                            buttons: [
                                                {
                                                    text: "Cerrar",
                                                    click: function() { $(this).dialog("close").remove(); }
                                                }
                                            ]
                                        });
                                        $(".contentMask").unmask();
                                    }
                                });
                                $(this).dialog("close").remove();
                            }
                        },
                        {
                            text: "Cancelar",
                            click: function() { $(this).dialog("close").remove(); }
                        }
                    ]
                });
            }
            else{
                $(data).dialog({
                    width: 870,
                    draggable: false,
                    modal: true,
                    resizable:false,
                    buttons: [
                        {
                            text: "Cancelar",
                            click: function() { $(this).dialog("close").remove(); }
                        }
                    ]
                });
            }
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp that displays the AuthCode of a Domain
function popUpFormaAuthCodeDomain(id, domain, url){
    $(".contentMask").mask('Cargando');
    var data = "contratacion_id="+id+"&dominio="+domain;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 300,
                draggable: false,
                modal: true,
                resizable:false,
                buttons: [
                    {
                        text: "Cerrar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

//The PopUp to input the AuthCode of a waiting transfer
function popUpFormaAuthCodeTransfer(id, domain, auth_code, ec_id, url, forma){
    $(".contentMask").mask('Cargando');
    var data = "contratacion_id="+id+"&dominio="+domain+"&authCode="+auth_code+"&ec_id="+ec_id;
    $.ajax({
        width: 300,
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 300,
                draggable: false,
                modal: true,
                resizable:false,
                buttons: [
                    {
                        text: "Iniciar",
                        click: function() {
                            var auth_code = document.getElementById('authCode').value;
                            var params = "action=" + forma + "&orden=" + id + "&ec_id=" + ec_id + "&authCode=" + auth_code;
                            $(".contentMask").mask('Cargando');
                            $.ajax({
                                url: 'dataAccess.php',
                                type: 'POST',
                                data: params,
                                success: function(){
                                    text = "<div style='font-size: 14px !important'>-Si su dominio es <b>.MX</b> por favor solicite a su proveedor actual que acepte la transferencia. En el momento que el la acepte, la transferencia debe completarse de manera inmediata.<br/>-Si su dominio <b>NO ES .MX</b>, por favor solicite al contacto administrativo dado de alta con su proveedor actual, que acepte dicha transferencia por medio del correo electr&oacute;nico que recibir&aacute;. Si no se acepta en los pr&oacute;ximos 5 d&iacute;as la transferencia se cancelar&aacute; autom&aacute;ticamente.<br/>-Para cualquier duda o verificaci&oacute;n del estatus de su transferencia por favor pongase en contacto con <b>operaciones@neubox.net</b></div>";
                                    $(text).dialog({
                                        width: 650,
                                        draggable: false,
                                        modal: true,
                                        resizable: false,
                                        buttons: [
                                            {
                                                text: "Cerrar",
                                                click: function() { 
                                                    $(this).dialog("close").remove(); 
                                                    ajaxCall('Transfers.php');
                                                }
                                            }
                                        ]
                                    });
                                    $( 'a.ui-dialog-titlebar-close' ).remove();
                                    $(".contentMask").unmask();
                                }
                            });
                            $(this).dialog("close").remove();
                        }
                    },
                    {
                        text: "Cerrar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $( 'a.ui-dialog-titlebar-close' ).remove();
            $(".contentMask").unmask();
        }
    });
}

function popUpCM4UpgradeAGB(){
$('#agb_upgrade_dialog').dialog({
        width: 700,
        modal: true,
        resizable:false,
        draggable: false,
        buttons: [
                {
                    text: "Cerrar",
                    click: function() {      
                        $(this).dialog("close");
                    }
                }
        ]
    });
}

function popUpCM4UpgradeSetup(cliente,orden,data){    
     $('#show_opts_upgrade').dialog({
          width: 700,
          modal: true,
          draggable: false,
          resizable:false,
          closeOnEscape: false,
          open:function(){
              $(".ui-dialog").mask('Cargando');
               $.post('CM4All/masterProcess.php',
                   {
                       funcion:'muestraConfiguracionUpgradeV1',
                       ord: orden,
                       dat: data
                   },function(response){
                      $('#show_opts_upgrade').dialog({title:'Detalle del servicio'});
                      $('#show_opts_upgrade').html(response);
                      bindEvents_SetupUpgrade();
                      $(".ui-dialog").unmask();
                   }
               );
          },
          buttons: [
                {
                    text: "Procesar Upgrade",
                    click: function() {
                        processUpgradeTransaction(orden,data);
                    }
                },
                {
                    text: "Cancelar",
                    click: function() {
                        $(this).dialog("close");
                    }
                }
        ]
     });
}

function popUpCM4AllUpgradeCH(data, url, cliente, orden){
    $(".contentMask").mask('Cargando');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 700,
                modal: true,
                draggable: false,
                resizable:false,
                open:function(){
                    $(this).find( 'a.ui-dialog-titlebar-close' ).remove();
                },
                buttons: [
                    {
                        text: "Zustimmen und fortfahren",
                        click: function() {
                            $(this).dialog("close").remove();
                            processUpgradeCH(orden);
                        }
                    },
                    {
                        text: "Abbrechen",
                        click: function() {
                            $(this).dialog("close").remove();
                        }
                    },
                    {
                        text: "Bestimmungen ansehen",
                        icons:{secondary:'ui-icon-note'},
                        click: function() {
                            popUpCM4UpgradeAGB();
                        }
                    }
                ]
            });
            $(".contentMask").unmask();
        }
    });
}

function popUpCM4AllUpgradeMX(data, url,cliente,orden){
    $(".contentMask").mask('Cargando');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(data){
            alert(data);
        },
        success: function(data){
            $(data).dialog({
                width: 700,
                modal: true,
                draggable: false,
                resizable:false,
                open:function(){
                    $(this).find( 'a.ui-dialog-titlebar-close' ).remove();
                },
                buttons: [
                    {
                        text: "Aceptar y Continuar",
                        click: function() {
                            $(this).dialog("close").remove();
                            popUpCM4UpgradeSetup(cliente,orden);
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close").remove(); }
                    },
                    {
                        text: "Ver Politicas",
                        icons:{secondary:'ui-icon-note'},
                        click: function() {
                            popUpCM4UpgradeAGB();
                        }
                    }
                ]
            });
            $(".contentMask").unmask();
        }
    });
}

function popUpCancelUpgradePlanMX(data, url,cliente,orden,title_dialog){
    $(".contentMask").mask('Cargando');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(response){
            alert(response);
        },
        success: function(response){
            
            $('#cancel_plan_upgrade_dialog').dialog({
                width: 700,
                title:title_dialog,
                modal: true,
                draggable: false,
                resizable:false,
                closeOnEscape: false,
                open:function(){
                    $(this).prev().find('a.ui-dialog-titlebar-close').hide();
                    $('#cancel_plan_upgrade_dialog').html(response);      
                },
                buttons: [
                    {
                        text: "Aceptar",
                        click: function() {//Eliminar el registro elemento_compra                                
                                processCancelUpgradeTransaction(orden,data);                                
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close"); }
                    }
                ]
            });
            $(".contentMask").unmask();
        }
    });
}

function popUpUpgradePlanMX(data, url,cliente,orden,title_dialog){
    $(".contentMask").mask('Cargando');
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        ajaxError: function(response){
            alert(response);
        },
        success: function(response){
            $(response).dialog({
                width: 700,
                title:title_dialog,
                modal: true,
                draggable: false,
                resizable:false,
                closeOnEscape: false,
                open:function(){
                    $(this).prev().find('a.ui-dialog-titlebar-close').hide();
                    $('#terminos_y_conds_accepted').live('click',function(){
                           popUpCM4UpgradeAGB();
                    });
                },
                buttons: [
                    {
                        text: "Aceptar",
                        click: function() {
                            
                            if($('#chech_terms_cond').is(':checked')){
                                $(this).dialog("close").remove();
                                popUpCM4UpgradeSetup(cliente,orden,data);
                            }else{
                                display_message_simple('main_msg',4,'Es necesario aceptar los t&eacute;rminos del servicio.');
                            }
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() { $(this).dialog("close").remove(); }
                    }
                ]
            });
            $(".contentMask").unmask();
        }
    });
}

function display_message_simple(container,type,msg){
    $('#'+container).removeClass('warning information success error new-mail welcome');
    switch(type){
        case 1://Attention
            $('#'+container).addClass('warning');
            break;
        case 2://information
            $('#'+container).addClass('information');
            break;
        case 3://success
            $('#'+container).addClass('success');
            break;
        case 4://error
            $('#'+container).addClass('error');
            break;
    }
    
    $('#'+container).html(msg);
    $('#'+container).show();
}

function processUpgradeCH(orden) {
    $.ajax({
            url: 'CM4All/upgradeCH.php',
            type: 'POST',
            data: orden,
            ajaxError: function(data){
                alert(data);
            },
            success: function(response){
                $(response).dialog({
                    width: 700,
                    modal: true,
                    draggable: false,
                    resizable:false,
                    open:function(){
                        $(this).find( 'a.ui-dialog-titlebar-close' ).remove();
                    },
                    buttons: [
                    {
                        text: "Ok",
                        click: function() {
                            $(this).dialog("close").remove();
                        }
                    }
                    ]
                });
                $(".contentMask").unmask();
            }
        });
    }

function processCancelUpgradeTransaction(orden,data){
    $(".ui-dialog").mask('Cargando'); 
    $.post('CM4All/masterProcess.php',
            {
              funcion:'startProcessCancelUpgradeV1',
              ord: orden,
              dat:data
            },
            function(response){
                $(".ui-dialog").unmask(); 
                var result = jQuery.parseJSON(response);                                                
                                
                $('#cancel_plan_upgrade_dialog').dialog({                         
                    closeOnEscape: false,
                     buttons: [{
                            text: "Cerrar",                            
                            click: function() {            
                                if(!result.error){    
                                    $("#hosting_table").flexReload(); 
                                }
                                $(this).dialog("close");
                            }
                        }]
                });                                                
                
                $('#cancel_plan_upgrade_dialog').html(result.msg);
            }
        ); 
}

function processUpgradeTransaction(orden,data){
    var orden = $('#id_orden').val();    
    $(".ui-dialog").mask('Cargando'); 
    $.post('CM4All/masterProcess.php',
            {
              funcion:'startProcessUpgradeV1',
              ord: orden,
              dat:data
            },
            function(response){
                $(".ui-dialog").unmask(); 
                var result = jQuery.parseJSON(response);                                                
                $('#setup_packet').hide().remove();
                $('#show_opts_upgrade').dialog({
                     title:'Operaci&oacute;n Upgrade',
                     closeOnEscape: false,
                     buttons: [{
                            text: "Cerrar",
                            click: function() {      
                                if(!result.error){
                                    $("#hosting_table").flexReload(); 
                                }
                                $(this).dialog("close");
                            }
                        }]
                });                     
                    
                $('#show_opts_upgrade').html(result.msg);
                
            }
        ); 
}

function bindEvents_SetupUpgrade(){
    $( "#periodicidades" ).buttonset();
    $('#periodicidades input').live('click',function(){  
       var meses = $(this).val();
       var periodicidades =  $('#periodicidad_prices').val();
       var item_periodo = 0;       
       var total = 0;
       periodicidades = periodicidades.split('|');
       for(var i=0;i<periodicidades.length && total==0;i++){
           item_periodo = periodicidades[i].split('@');
           if(item_periodo[0] == meses ){
               $('#detalle_orden').removeClass('information').addClass('welcome');               
               total = item_periodo[1];               
           }
       }
       
       if(total!=0){
           var string_periodicidad = meses>1 ? ' Meses':' Mes';
           var string_detalle = 'Upgrade CM4All para plan '+$('#name_plan').val()+ ' ('+$('#id_orden').val()+') LTE-ES a FV-ES por <b>'+meses + string_periodicidad+'</b><br/>'
           string_detalle += '<strong>Total a pagar: $'+total+'M.N.</strong>';
           $('#detalle_orden').html(string_detalle);
       }
    });
       
}

</script>

        <link href="resources/css/login.css" rel="stylesheet" type="text/css" />
        
        <script type="text/javascript" language="javascript">
            $(document).ready(function(){
                $('input').keypress(function(e){
                    if(e.which == 13){
                        var username = document.getElementById('email').value;
                        var password = document.getElementById('password').value;

                                                    if(password.length > 0 && username.length > 0){
                                validarForma();
                            }
                                                var n = $("input").length;
                        var nextIndex = $('input').index(this) + 1;
                        if(nextIndex == 3)
                            $('#recaptcha_response_field').focus();
                        else if(nextIndex < n)
                            $('input')[nextIndex].focus();
                    }
                });
                /*
                $('#entradap').click(function(){
                    validarForma();
                });*/
                $('#forget_pass').click(function(){
                    $(".contentMask").mask('Cargando');
                    var email = document.getElementById('email').value;
                    var data = "email="+email;
                    $.ajax({
                        url: 'forms/passwordRequest.php',
                        type: 'POST',
                        data: data,
                        ajaxError: function(data){
                            alert(data);
                        },
                        success: function(data){
                            $(data).dialog({
                                width: 400,
                                modal: true,
                                resizable:false,
                                buttons: [
                                    {
                                        text: "Enviar Contrase\u00F1a",
                                        click: function() {
                                            if(document.getElementById('email_request').value == ''){
                                                var newHTML = "<div class='message warning'>Campo de E-mail vacio.</div>";
                                                document.getElementById('message').innerHTML = newHTML;
                                                $(this).dialog("close").remove();
                                            }
                                            else{
                                                if($("#request_Password").validationEngine('validateform')){
                                                    var email = document.getElementById('email_request').value;
                                                    //Se cargan las vareables para la solicitud de  cambio de contraseña
                                                    var variables = 'correo='+escape(email)+'&accion=renviarcontrasena';
                                                    var myConn = new XHConn();
                                                    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
                                                    var peticion = function (oXML) {
                                                        var regreso = oXML.responseText;
                                                        if ( regreso == 'si' ) {
                                                            // se envia y se despliega un mensaje de envio correcto
                                                            var newHTML = "<div class='message information'>La solicitud ha sido enviada a tu correo.</div>";
                                                            document.getElementById('message').innerHTML = newHTML;
                                                            $(".contentMask").unmask();
                                                        }
                                                        else{
                                                            // se envia y se despliega un mensaje de envio fallido
                                                            var newHTML = "<div class='message warning'>La solicitud no pudo ser enviada.</div>";
                                                            document.getElementById('message').innerHTML = newHTML;
                                                            $(".contentMask").unmask();
                                                        }
                                                    };
                                                    myConn.connect('accseg.php', "POST", variables, peticion );
                                                    $(this).dialog("close").remove();
                                                }
                                            }
                                        }
                                    },
                                    {
                                        text: "Cerrar",
                                        click: function() { $(this).dialog("close").remove(); }
                                    }
                                ]
                            });
                            $( 'a.ui-dialog-titlebar-close' ).remove();
                        }
                    });
                });
            });

            function validarForma(){
                if($("#forma").validationEngine('validateform')){
                    /*$(".contentMask").mask('Cargando');*/
                    var email = document.getElementById('email').value;
                    var password = document.getElementById('password').value;
                    var params = 'email='+ email +'&password='+ password +'&idioma=es';
                    $.ajax({
                        url: 'auth/login.php',
                        type: 'POST',
                        data: params,
                        success: function(data){
                            if(data == 'true'){
                                window.location = "index.php";
                            }
                            else if(data == 'abuso'){
                                var msg = "Usuario o Contrase&ntilde;a incorrectos.";
                                document.getElementById('msgError').value = msg;
                                document.loginMsgError.submit();
                            }
                            else{
                                var newHTML = "<div class='message warning'>"+data+"</div>";
                                $('#message').html(newHTML);
                                //document.getElementById('message').innerHTML = newHTML;
                            }
                        }
                    });
                }
            }

            /*
            function changeLang(){
                $(".contentMask").mask('Cargando');
                document.change_lang.submit()
            }*/
        </script>
        <script src="SSN/F_G_S.js"   type="text/javascript"></script>
    </head>
    <body>
        <form id="loginMsgError" name="loginMsgError" method="post" action="">
            <input type="hidden" id ="msgError" name="msgError" value="" />
        </form>
        <div id="wrap">
            <div id="wrapper">
                <div id="cuerpo">
                    <div id="contenido">
                        <div id="coords" style="font-size: 20px;"></div>
                        <table width="1000px" border="0" style="margin: 0 auto;">
                    <tr>
                        <td width="50%" valign="top"><!-- LOGO -->
                            <img src="img/neubox_logo_de.png" alt="Neubox Logo" align="left"/>
                        </td>
                        <td width="50%" align="right">
                            <p class="clientTitle">
                                                                        *Cliente 3.0
                                                           </p>
                        </td>
                    </tr>
                    <tr>

                        <td colspan="2" align="center"><!--AREA DE TRABAJO-->

                            <p id="login-title">
                                Administraci&oacute;n personal de tu cuenta en NEUBOX Internet                            </p>
                            <div id="message">
                                                            </div>
                            <br/>
                            <div class="standard_layer" style="text-align:left;">
                                <div class="layer">
                                    <div class="clayer no_padding">
                                        <form id="forma" name="forma" method="post" action="">
                                            <table align="center" cellpadding="3">
                                                <tr>
                                                    <td>
                                                        <div class="loginContenedor">
                                                            <div class="loginElementos">
                                                                <table align="center" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td width="70" rowspan="2" align="center" valign="top">
                                                                            <div class="panel-icons-lock panel-icon-lock" />
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="loginTexto">
                                                                                <!-- ## ingresa tus datos ...  -->
                                                                                <br />Ingresa tus datos para iniciar sesi&oacute;n                                                                                <table align="center" width="100%" cellpadding="2" cellspacing="2" border="0">
                                                                                    <tr>
                                                                                        <td class="loginTexto">
                                                                                            E-Mail:<!-- ## email -->
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" name="email" id="email" class="validate[required,custom[email]] text_form_login" style="width: 200px" />
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="loginTexto">
                                                                                            Contrase&ntilde;a:<!-- ## contrase�a -->
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="password" name="password" id="password" class="validate[required] text_form_login" style="width: 200px"/>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                            <div>
                                                                                <!--  ##  regenerar contrase�a -->
                                                                                <a href="#" id="forget_pass" class="loginLink">Regenerar Contrase&ntilde;a</a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <div align="right" style="padding-right: 4%;">
                                                                    <!-- ## entrar -->
                                                                    <input type="button" id="entradap" value="Entrar" onclick="validarForma()" class="botones"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div id="captcha">
                                <br/><br/>
                                                            <br/><br/>

                            </div>
                        </td>
                    </tr>
                </table>
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <div id="footer_container2">                
                            <div id="footer2">
            <div class="social_net_element first">
                            <a href="Feed_Content.php" target="_blank"><img src="img/rss_icon_big.gif" border="0" alt="Rss"/></a>
                        </div>
                        <div class="social_net_element">
                            <a href="https://www.twitter.com/neubox" target="_blank"><img src="img/twitter_icon_big.gif" border="0" alt="Twitter"/></a>
                        </div>
                        <div class="social_net_element">
                            <a href="https://www.facebook.com/NEUBOX" target="_blank"><img src="img/facebook_icon_big.gif" alt="Facebook"/></a>
                        </div>
                                                <div class="social_net_element last">
                            <a href="https://plus.google.com/111056280094990923381" target="_blank"><img src="img/gp_icon_big.gif" alt="Google Plus"/></a>
                        </div>
                                    <a id="nbt" href="http://nbt.mx"></a>
            <div class="disclaimer_footer">
                        <strong>&copy; 2005 - 2012 NEUBOX Internet SA de CV </strong> Loma Blanca #210 Col. Loma Dorada San Luis Potos&iacute;, SLP. CP 78215 M&eacute;xico. Los precios se encuentran en pesos mexicanos, estan sujetos a cambios sin previo aviso y no incluyen IVA. &reg;NEUBOX es una marca registrada. D.R. y &copy;NEUBOX Internet S.A. de C.V. 2011. Queda prohibida la reproducci&oacute;n total o parcial de cualquier parte de esta obra sin la autorizaci&oacute;n previa, expresa y por escrito de su titular.                         </div>
            
            </div>
                    </div>
        </div>
    </body>
</html>