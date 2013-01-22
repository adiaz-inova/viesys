/* + + + + + + + + + + + + + + + + + + + + + 
	VIE 2012
	Fecha de desarrollo: 08-10-2012
	Desarrollado por: Ing. Alejandro Diaz Garcia
	validacampos.js - Funciones JavaScript utilizadas para validar campos
+ + + + + + + + + + + + + + + + + + + + + */

function carBasicos(keycode) 
{
	/*
	37-left
	38-up
	39-right
	40-down
	9-tab
	8-retroceso
	36-inicio
	35-fin
	13-enter
	46-suprimiR
	*/
	var caracterX = String.fromCharCode(keycode);
	var cadenota = navigator.userAgent.toLowerCase();
	
	if (cadenota.indexOf('chrome')!=-1) {
		if((keycode == 37 && caracterX != "%") 
		|| (keycode == 38 && caracterX != "&") 
		|| (keycode == 39 && caracterX != "'") 
		|| (keycode == 40 && caracterX != "(") 
		|| (keycode == 36 && caracterX != "$") 
		|| (keycode == 35 && caracterX != "#") 
		|| keycode == 8 || keycode == 9 
		|| (keycode == 46 && caracterX != "."))
			return true;
	
		else
			return false;
	}else {
		if(keycode == 37
		|| keycode == 38
		|| keycode == 39
		|| keycode == 40
		|| keycode == 36
		|| keycode == 35
		|| keycode == 8 || keycode == 9 
		|| keycode == 46)
			return true;
	
		else
			return false;
	}

		
}

function vLetras(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
//	$('#mainNotice').text('keyCode='+e.keyCode+' , which='+e.which+' , char='+caracter);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[A-Za-zñÑ ]+$/.test(caracter)){
			return true;
		}else{
			return false;
		}
	}
}

function vLetrasNum(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[0-9A-Za-z]+$/.test(caracter)){
			return true;
		}else{
			return false;
		}
	}
}

function vLetrasynum(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[0-9A-Za-zñÑ ]+$/.test(caracter)){
			return true;
		}else{
			return false;
		}
	}
}

function vFormula(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[0-9A-Z\+\-\*\/\(\)\[\]]+$/.test(caracter)){
			return true;
		}else{
			return false;
		}
	}
}

function vEspecial01(e, objeto) //letras numeros y guion bajo(_)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[A-Za-zñÑ0-9_.]+$/.test(caracter)){
			return true;
		}else{
			return false;
		}
	}
}

function vEspecial02(e, objeto) //LETRAS MAYUSCULAS GUIONES Y NUMEROS -- CUENTA PREDIAL
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[A-Z0-9-]+$/.test(caracter)){
			return true;
		}else{
			return false;
		}
	}
}

function vEspecial03(e, objeto) //GUIONES PUNTOS Y NUMEROS -- CURT
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[0-9-.]+$/.test(caracter)){
			return true;
		}else{
			return false;
		}
	}
}

function vEspecial04(e, objeto) //CURP Y RFC
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[0-9A-Za-z]+$/.test(caracter)){
			return true;
		}else{
			return false;
		}
	}
}

function vEspecial05(e, objeto) //FORMULAS
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[0-9\.\+\-\*\/\(\)]+$/.test(caracter)){
			return true;
		}else{
			return false;
		}
	}
}


function vUsuario(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
		
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[A-Za-z0-9_]+$/.test(caracter))
			return true;
		else
			return false;
	}
}

function vPassword(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(!(/^['´`~^áéíóúÁÉÍÓÚ]+$/.test(caracter)))
			return true;
		else
			return false;
	}
}

function vFlotante(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if((/^[0-9.]+$/.test(caracter)))
			return true;
		else
			return false;
	}
}

function vAbierta(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(!(/^['´áéíóúÁÉÍÓÚ]+$/.test(caracter)))
			return true;
		else
			return false;
	}
}

function vNumeros(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[0-9]+$/.test(caracter))
			return true;
		else
			return false;
	}
	
}
function vMail(e, objeto)
{
	keynum = (window.event)? e.keyCode : e.which ;
	caracter = String.fromCharCode(keynum);
	
	if(carBasicos(e.keyCode))
		return true;
	else {
		if(/^[a-zA-Z0-9-_.@]+$/.test(caracter))
			return true;
		else
			return false;
	}
	
}