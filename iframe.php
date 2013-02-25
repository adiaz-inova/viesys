<?php 
require_once('includes/getvalues.php');
$tipo = (isset($tipo))? $tipo:''; 
?>
<iframe id="if_nuevo" src="add_script.php?tipo=<?php echo $tipo?>" width="800" height="500"></iframe>