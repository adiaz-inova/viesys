<?php
require_once 'jqSuitePHP_4_4_4_0/jq-config.php';
// include the jqGrid Class
require_once "jqSuitePHP_4_4_4_0/php/jqGrid.php";
// include the PDO driver class
require_once "jqSuitePHP_4_4_4_0/php/jqGridPdo.php";
// include the autocomplete class 
require_once "jqSuitePHP_4_4_4_0/php/jqAutocomplete.php"; 
// include the datepicker class 
require_once "jqSuitePHP_4_4_4_0/php/jqCalendar.php"; 
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'select id_log, l.date fecha, l.date hora 
							, concat(emp.nombre," ",emp.ape_pat," ",emp.ape_mat) empleado
							, l.ip, l.host, l.browser, l.id_mod, l.id_emp
							, mods.nombre modu
							from log l
							inner join empleados emp using(id_emp)
							inner join modulo mods using(id_mod)';
// set the ouput format to json
$grid->dataType = 'json';

// now we should check whenever a export is lunched
//$export = $_POST['oper'];

//if($export == 'excel')
    // let set summary field
//   $grid->exportToExcel(array('Freight'=>'Freight'));
//else 
//   $grid->queryGrid();

// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('llenagrid_log.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"CONSULTA DE LOG",
    "width"=>725,
    "height"=>280,
    "rowNum"=>20,
    "rownumbers"=>true,
    "sortname"=>"id_log",
    //"sortorder"=>"desc",
    "hoverrows"=>true,
    "rowList"=>array(10,20,50),
    ));
//enable toolbarsearch
$grid->toolbarfilter = true;

	// add navigator with the default properties
	//$grid->navigator = true;
	//but use single search
	//$grid->setNavOptions('search',array("multipleSearch"=>false));

// add custom button to export the data to excel
/*jQuery("#grid").jqGrid('navButtonAdd','#pager',{
       caption:"", 
       onClickButton : function () { 
           jQuery("#grid").jqGrid('excelExport',{"url":"llenagrid_log.php"});
       } 
});*/

// Change some property of the field(s)
$grid->setColProperty("empleado", array("label"=>"Empleado", "width"=>300));
$grid->setColProperty("modu", array("label"=>"Módulo"));
$grid->setColProperty("fecha", array("label"=>"Fecha","formatter"=>"date",
			"formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"d/m/Y")));
$grid->setColProperty("hora", array("label"=>"Hora","formatter"=>"date","search"=>false, 
			"formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"H:i:s")));
$grid->setColProperty("id_log", array("hidden"=>true));
$grid->setColProperty("id_mod", array("hidden"=>true));
$grid->setColProperty("id_emp", array("hidden"=>true));

// Enable navigator 
// add navigator with the default properties
$grid->navigator = true; 
// Enable excel export 
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false,"cloneToTop"=>false)); 
// add a custom button via the build in callGridMethod 
// note the js: before the function 
$buttonoptions = array("#pager", 
    array("caption"=>"Pdf", "title"=>"Exportar a Pdf", "onClickButton"=>"js: function(){ 
        jQuery('#grid').jqGrid('excelExport',{tag:'pdf', url:'llenagrid_log.php'});}" 
    ) 
); 
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions); 

// Set it to toppager 
$buttonoptions[0] = "#grid_toppager"; 
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions); 

/***************************/
$grid->setAutocomplete("empleado",false,"SELECT DISTINCT nombre FROM empleados WHERE nombre LIKE ? ORDER BY nombre",null,true,true); 
$grid->setDatepicker("fecha", null, false, true); 
$grid->setUserTime("d/m/Y"); 
$grid->datearray = array('fecha');

/*$grid->navigator = false; 
$custom = <<<CUSTOM
jQuery("#getselected").click(function(){
    var selr = jQuery('#grid').jqGrid('getGridParam','selrow');
    if(selr) alert(selr);
    else alert("No selected row");
    return false;
});
jQuery("#setselection").click(function(){
    jQuery('#grid').jqGrid('setSelection','425');
    return false;
});
CUSTOM;
$grid->setJSCode($custom);*/

// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
$conn = null;

?>
