<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form action="" method="post" enctype="multipart/form-data" name="formulario" id="formulario">
  <div>  
    <label for="Fnombre">*Nombre:</label>
    <input name="Fnombre" type="text" id="Fnombre" size="35" />
  </div>
  <div>
    <label for="Fapaterno">*Apellido Paterno:</label>
    <input name="Fapaterno" type="text" id="Fapaterno" size="35" />
  </div>
  <div>
    <label for="Famaterno">*Apellido Materno:</label>
    <input name="Famaterno" type="text" id="Famaterno" size="35" />
  </div>
  <div>
    <label for="Ftel">Teléfono:</label>
    <input name="Ftel" type="text" id="Ftel" size="12" />
  </div>
  <div>
    <label for="Femail">*Correo electrónico:</label>
    <input name="Femail" type="text" id="Femail" size="35" />
  </div>
  <div>
    <label for="Fpass">*Contraseña:</label>
    <input name="Fpass" type="password" id="Fpass" size="20" />
  </div>
  <div>
    <label for="Fpass2">*Confirme la Contraseña:</label>
    <input name="Fpass2" type="password" id="Fpass2" size="20" />
  </div>
  <div>
    <label for="Fdireccion">Dirección:</label>
    <textarea name="Fdireccion" id="Fdireccion" cols="45" rows="5"></textarea>
  </div>
  <div>
    <label for="Ffoto">Fotografía:</label>
    <input type="file" name="Ffoto" id="Ffoto" />
  </div>
  <div>
    <label for="Festatus">Estatus:</label>
    <select name="Festatus" id="Festatus">
    	<option value="">Seleccione...</option>
    </select>
  </div>
</form>
</body>
</html>