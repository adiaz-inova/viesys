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
$grid->SelectCommand = 'select 
      gas.id_gas id
      , fecha
      , gas.gasto
      , gas.identificador
      , gas.responsable
      , gas.descripcion
      , gas.id_est estatus
      , tpa.nombre tipo_pa
      , tga.nombre tipo_ga
      from gastos gas 
      inner join tipo_pago tpa using(id_tip_pag)
      inner join tipo_gasto tga using(id_tip_gas)
      WHERE 1=1 AND gas.id_est in (1)';
// set the ouput format to json
//$grid->table = 'gastos';
// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('llenagrid_gastos.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"CONSULTA DE GASTOS",
    "rowNum"=>20,
    "rowList"=>array(10,20,30), 
    "sortname"=>"id",
    "height"=>150));

//enable toolbarsearch
$grid->toolbarfilter = true;

// Change some property of the field(s)
$grid->setColProperty("id",array("editable"=>false,"hidden"=>true));
$grid->setColProperty("fecha", array("label"=>"Fecha","formatter"=>"date",
      "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"d/m/Y")));
$grid->setColProperty("gasto", array("label"=>"Monto Gasto", 
      "formatter"=>"currency", "align"=>"right",
      "formatoptions"=>array("thousandsSeparator"=>",","decimalSeparator"=>".","prefix"=>"$")));
$grid->setColProperty("identificador", array("label"=>"Identificador", "width"=>100));
$grid->setColProperty("responsable", array("label"=>"Responsable"));
$grid->setColProperty("estatus", array("hidden"=>true));
$grid->setColProperty("descripcion", array("hidden"=>true));

$grid->setColProperty("tipo_pa", array("label"=>"Tipo Pago"));
/*$grid->setColProperty("tipo_pa", array("label"=>"Tipo Pago",
      "width"=>"200", 
      "searchoptions"=>array("sopt"=>array('cn'))));
*/
$grid->setColProperty("tipo_ga", array("label"=>"Tipo Gasto"));
/*$grid->setColProperty("tipo_ga", array("label"=>"Tipo Gasto", 
      "width"=>"200", 
      "searchoptions"=>array("sopt"=>array('cn'))));
*/
$grid->setAutocomplete("identificador",false,"SELECT DISTINCT identificador FROM gastos WHERE identificador LIKE ? ORDER BY identificador",null,true,true); 
$grid->setDatepicker("fecha", null, false, true); 
$grid->setUserTime("d/m/Y"); 
$grid->datearray = array('fecha');

$grid->setSelect("tipo_pa", "SELECT nombre as tipo_pa, nombre as nombre_pa FROM tipo_pago ORDER BY nombre", false, false, true, array(""=>"All"));
$grid->setSelect("tipo_ga", "SELECT nombre as tipo_ga, nombre as nombre_ga FROM tipo_gasto ORDER BY nombre", false, false, true, array(""=>"All"));

// Enable navigator 
// add navigator with the default properties
$grid->navigator = true;
$grid->setNavOptions('navigator', array("del"=>true,"excel"=>true,"search"=>true,"refresh"=>true));
$grid->setNavOptions('edit', array("height"=>110,"dataheight"=>"auto"));
$grid->setNavOptions('add', array("height"=>110,"dataheight"=>"auto")); 

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid->setJSCode($search); 

// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
$conn = null;

?>
