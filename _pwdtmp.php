<?php

include('includes/functions.php');

$pwd = $_GET['pwd'];

echo 'Se ha solicitado generar la contraseña encriptada para: '.$pwd.'<br>';

$nuevo_pwd = MakeKey($pwd);

echo 'La nueva contraseña se genero exitosamente: '.$nuevo_pwd.'<br>';

$nuevo_pwd2 = josHashPassword($pwd);
echo '-->'.$nuevo_pwd2;

?>