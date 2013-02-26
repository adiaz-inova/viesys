<?php 
/* + + + + + + + + + + + + + + + + + + + + + 	
	VIE 2012
	Fecha de desarrollo: 08-10-2012
	Desarrollado por: Ing. Alejandro Diaz Garcia
	configuration.inc.php 
+ + + + + + + + + + + + + + + + + + + + + */

/* linea de edgar para test !!! */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
Configuracion de Depuracion de Errores
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
		set_time_limit(1000); 

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
Configuracion de sesión - tiempo de vida de la sesion en minutos
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		define('TIEMPODEVIDA', 45); #minutos

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
Ruta absoluta de intalacion
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */	
		# Ruta absoluta de intalacion del portal SGC
		$dir_app = getcwd() . '\\';//'C:\ms4w\Apache\htdocs\viesys\\';

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
Datos utilizados para el Panel de Control
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		$id_modulo_panel_administracion = 0;
		$id_grupo_administradores_del_panel = 0;
		
		# Ruta al archivo de conexcion
		$VIE_CONNDB = $dir_app.'vie.cfg';
		
		# Muy Importante definir el ID del grupo Administrador del Sistema
		@define('IDGRUPOADMINISTRADOR', 1);
		
		# Muy Importante definir los ID de grupos intocables(basicos para que funcione el sistema) separar con ',' en caso de multiples
		$modulosbasicos = '1, 12' ;
		
		# Mostrar datos de usuario (usuario | nombre) default=usuario
		$mostrar_dusuario = 'nombre';
		
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
Ruta absoluta donde seran almacenados los archivos temporales
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */	
		# Importante agregar / o \ al final
		#linux: /var/tmp
		#windows: C:\WINDOWS\Temp
		$dir_temp = $dir_app.'temp'. '\\';
		
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
Configuracion para envio de Correo Electronico con servidor SMTP
mas informacion visita: http://api.joomla.org/Unknown/PHPMailer.html
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

		# Activar autenticacion SMTP (true | false)
		$mail_SMTPAuth   = true;
		# Prefijo del servidor ("", "ssl" or "tls")
		$mail_SMTPSecure = "ssl";
		# Servidor SMTP (ejemplo google "smtp.gmail.com")
		$mail_Host       = "smtp.gmail.com";
		# Puerto del servidor SMTP
		$mail_Port       = 465;
		# Password SMTP (En caso de utilizar la autenticacion SMTP)
		$mail_Username   = "correo@dominio.com.mx";
		# Usuario SMTP (En caso de utilizar la autenticacion SMTP)
		$mail_Password   = "99999999999999";
		# Responder a (correo electronico):
		$mail_AddReplyToMail = "no-responder@dominio.mx";
		# Responder a (nombre):
		$mail_AddReplyToName = utf8_decode("Soporte VIE");
		# Enviado por:
		$mail_From       = "no-responder@dominio.mx";
		# Nombre de la persona que envia
		$mail_FromName   = utf8_decode("VIE");


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
Configuraciones para los reportes pdf
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */	
		$vie_margen = 10;

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
Configuracion de visualizacion - numero de registros para pagina
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		$vie_max_regxpag = 10;
?>