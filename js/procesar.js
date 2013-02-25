function more_filters() {
	$('#more_filters').toggle();
}
function carga(tipo, div ) {
	divUpd = $('#' + div);

	var id_tip_ser = $('#Ftipoeve');
	if(id_tip_ser.val() != '') {
		var url = 'select_dinamicos.php';
		var param = 'tipo='+tipo+'&id_tip_ser='+id_tip_ser.val();

		$('#cargando').show();
		$(divUpd).load(url, param, function(){
			$('#cargando').hide('fast');
		});
	}

}

function add_notaajax(id) {
	var notas = $('#Faddnotas');
	var divnotas = 'notices_notas';
	var div = $('#'+divnotas);
	var respuesta = '';
	var param = 'tipo=notas&task=add&id_eve='+id+'&Fnotas='+notas.val();

	div.text();

	if($.trim(notas.val()) != '') {
		var div = $('#'+divnotas);
		$.ajax({
			type: 'POST',
			url: 'op_extras.php',
			data: param,
			async: true,
			beforeSend: function(){
				$('#cargando').show();
			},
			success: function(msg){
				respuesta = msg;

				if(respuesta == 1){
					div.text('Nota agregada al evento con exito.');
					div.slideDown('slow');
					
					
				}else {
					div.text('Intente nuevamente.'+respuesta);
					div.slideDown('slow');
				}
			},
			complete: function(){
				$('#cargando').hide('fast');
				$('#vie_cont_notas').load('notas.php', 'task=ajaxlist&id_eve='+id, function(){
					$('#cargando').hide('fast');
					notas.val('');
				});

			}
		});
	}else
		notas.focus();


}

function add_nota(id) {
	var notas = $('#Faddnotas');
	var divnotas = 'notices_notas';
	var div = $('#'+divnotas);
	var respuesta = '';
	var param = 'tipo=notas&task=add&id_eve='+id+'&Fnotas='+notas.val();

	div.text();

	if($.trim(notas.val()) != '') {
		doing_ajax('op_extras.php', param, divnotas);
	
	}else
		notas.focus();

}

function doing_ajax(url, param, divnotas) {
	var res = '';
	var div = $('#'+divnotas);

	$.ajax({
		type: 'POST',
		url: url,
		data: param,
		async: true,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			respuesta = msg;

			if(respuesta == 1){
				div.text('Nota agregada al evento con exito.');
				div.slideDown('slow');
				
				
			}else {
				div.text('Intente nuevamente.'+respuesta);
				div.slideDown('slow');
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

		
}

function carga_iframe(script, tipo, task) {
	var accion = '?tipo='+tipo+'&';
	switch(task) {
		case 'add':
			accion += 'task=add';
		break;
		default: accion += 'task=add'
	}
	$('#if_nuevo').attr('src',script+'.php'+accion);
}
function cerrar_iframe() {
	$('#if_div').toggle();
}
function only_add(objeto, url, param, notices) {
	attemp = $('#' + notices);
	var div = ( attemp == null )? '' : attemp;

	if(div == '')
		return false;
	else
		div.html('');

	var urlOrigen = url + '.php';
	var valortemp = '';
	var arreglo = param.split("|");
	var label = '';

	for (var i = 0; i < arreglo.length; i++) {
		valortemp = $('#' + arreglo[i]);
		if(valortemp.attr('req') == 'req' && !valortemp.attr('disabled')) {

			if(valortemp.val() == '') {

				attemp = valortemp.attr('lab');
				label = ( attemp == null )? '' : attemp;
				mensaje = '';
				if(label == '') {
					mensaje = 'campo requerido.';
				}else{
					if(url=='eventos' && arreglo[i]=='ctotal')
						mensaje = 'Seleccione algun servicio.'
					else
						mensaje = 'El campo '+label+' es requerido.'
				}

				div.html('<div class="notice2">' + mensaje + '</div>');
				valortemp.focus();
				div.slideDown('slow')
				
				return false;
			}
		}
		if(urlOrigen=='salones.php' && arreglo[i]=='Femail') {
			if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\D{2,4})+$/.test(valortemp.val()))) {
				mensaje = 'Escriba un Email valido';

				div.html('<div class="notice2">' + mensaje + '</div>');
				valortemp.focus();
				div.slideDown('slow')
				
				return false;
			}
		}
	};

	var param = 'task=add&'+$('#formulario').serialize();


	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);
				
				// actualizar listado de salones
				var urlscript = 'ajax.php';
				var divUpd = '#div_select_'+url;
				var param = 'tabla=salones&id=Fsalon&name=Fsalon&value=id_sal&desc=nombre';
				if(url == 'clientes')
					param = 'tabla=clientes&id=Fcliente&name=Fcliente&value=id_cli&desc=nombre';

				$(divUpd, window.parent.document).load(urlscript, param, function(){
					parent.$.fancybox.close();
				});
				// cerrar poppup

		}
	},
	complete: function(){
		$('#cargando').hide('fast');
	}
});

}


function add(objeto, url, param, notices) {
	attemp = $('#' + notices);
	var div = ( attemp == null )? '' : attemp;

	if(div == '')
		return false;
	else
		div.html('');

	var urlOrigen = url + '.php';
	var valortemp = '';
	var arreglo = param.split("|");
	var label = '';

	for (var i = 0; i < arreglo.length; i++) {
		valortemp = $('#' + arreglo[i]);
		if(valortemp.attr('req') == 'req' && !valortemp.attr('disabled')) {

			if(valortemp.val() == '') {

				attemp = valortemp.attr('lab');
				label = ( attemp == null )? '' : attemp;
				mensaje = '';
				if(label == '') {
					mensaje = 'campo requerido.';
				}else{
					if(url=='eventos' && arreglo[i]=='ctotal')
						mensaje = 'Seleccione algun servicio.'
					else
						mensaje = 'El campo '+label+' es requerido.'
				}

				div.html('<div class="notice2">' + mensaje + '</div>');
				valortemp.focus();
				div.slideDown('slow')
				
				return false;
			}
		}
		if(urlOrigen=='salones.php' && arreglo[i]=='Femail') {
			if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\D{2,4})+$/.test(valortemp.val()))) {
				mensaje = 'Escriba un Email valido';

				div.html('<div class="notice2">' + mensaje + '</div>');
				valortemp.focus();
				div.slideDown('slow')
				
				return false;
			}
		}
	};

	var param = 'task=add&'+$('#formulario').serialize();


	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
				return false;
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);
				
				if(url == 'cotizaciones')
					urlOrigen = 'eventos.php';
				
				if(confirm('¿Desea agregar otro registro?', 'Los cambios fueron guardados con exito.'))
					if(url == 'pagos') {
						window.location.href=urlOrigen + '?task=add&id_eve='+$('#Fideve').val();
					}else {
						window.location.href=urlOrigen + '?task=add';						
					}
				else
					window.location.href=urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function update(objeto, url, param, notices) {
	objeto.id = 'botonagregar2012';
	var id = objeto.id;	
	var attemp = $('#' + id).attr('act');
	action = ( attemp == null )? '' : attemp;

	attemp = $('#' + notices);
	var div = ( attemp == null )? '' : attemp;

	if(div == '')
		return false;
	else
		div.html('');

	var urlOrigen = url + '.php';
	var valortemp = '';
	var arreglo = param.split("|");
	var label = '';

	for (var i = 0; i < arreglo.length; i++) {
		
		valortemp = $('#' + arreglo[i]);
		if(valortemp.attr('req') == 'req' && !valortemp.attr('disabled')) {

			if(valortemp.val() == '') {

				attemp = valortemp.attr('lab');
				label = ( attemp == null )? '' : attemp;
				mensaje = '';
				if(label == '') {
					mensaje = 'campo requerido.';
				}else{
					mensaje = 'El campo '+label+' es requerido.'
				}

				div.html('<div class="notice2">' + mensaje + '</div>');
				valortemp.focus();
				div.slideDown('slow')
				
				return false;
			}

		}
		if(urlOrigen=='salones.php' && arreglo[i]=='Femail') {
			if (valortemp.val()!='' && !(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\D{2,4})+$/.test(valortemp.val()))) {
				mensaje = 'Escriba un Email valido';

				div.html('<div class="notice2">' + mensaje + '</div>');
				valortemp.focus();
				div.slideDown('slow')
				
				return false;
			}
		}
	};

	var param = 'task=alter&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);
// return;
				if(action=='exit')
					window.location = urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}
/*
function update_salon(objeto){
	var act = objeto.getAttribute('act');
	var urlOrigen = 'salones.php';
	var Fnombre = $('#Fnombre');
	var Fresponsable = $('#Fresponsable');
	var Ftel = $('#Ftel');
	var Festatus = $('#Festatus');
	var div = $('#notices');
	
	div.html('');
	
	if(Fnombre.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre de salón valido.</div>');
		Fnombre.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Fresponsable.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre para el responsable.</div>');
		Fresponsable.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Ftel.val() == ''){
		div.html('<div class=\"notice2\"> Debe ingresar un teléfono.</div>');
		Ftel.focus();
		div.slideDown('slow')
		
		return false;
	}	
	if(Festatus.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un estatus.</div>');
		Festatus.focus();
		div.slideDown('slow')
		
		return false;
	}

	//Importante enviar array procesado
	var param = 'task=alter&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);

				if(act=='exit')
					window.location = urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}
*/

function update_cliente(objeto){
	var act = objeto.getAttribute('act');
	var urlOrigen = 'clientes.php';
	var Fnombres = $('#Fnombres');
	var Fapaterno = $('#Fapaterno');
	var Ftel = $('#Ftel');
	var Femail = $('#Femail');
	var Festatus = $('#Festatus');
	var div = $('#notices');
	
	div.html('');
	
	if(Fnombres.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre valido.</div>');
		Fnombres.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Fapaterno.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un apellido paterno valido.</div>');
		Fapaterno.focus();
		div.slideDown('slow')
		
		return false;
	}
	if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\D{2,4})+$/.test(Femail.val()))){
		div.html('<div class=\"notice2\"> Escriba un email valido.</div>');
		Femail.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Ftel.val() == ''){
		div.html('<div class=\"notice2\"> Debe ingresar un teléfono.</div>');
		Ftel.focus();
		div.slideDown('slow')
		
		return false;
	}	
	if(Festatus.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un estatus.</div>');
		Festatus.focus();
		div.slideDown('slow')
		
		return false;
	}

	//Importante enviar array procesado
	var param = 'task=alter&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);

				if(act=='exit')
					window.location = urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function add_cliente(objeto){
	var urlOrigen = 'clientes.php';
	var Fnombres = $('#Fnombres');
	var Fapaterno = $('#Fapaterno');
	var Femail = $('#Femail');
	var Ftel = $('#Ftel');
	//var Festatus = $('#Festatus');
	var div = $('#notices');
	
	div.html('');
	
	if(Fnombres.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre valido.</div>');
		Fnombres.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Fapaterno.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un apellido paterno valido.</div>');
		Fapaterno.focus();
		div.slideDown('slow')
		
		return false;
	}
	if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\D{2,4})+$/.test(Femail.val()))){
		div.html('<div class=\"notice2\"> Escriba un email valido.</div>');
		Femail.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Ftel.val() == ''){
		div.html('<div class=\"notice2\"> Debe ingresar un teléfono.</div>');
		Ftel.focus();
		div.slideDown('slow')
		
		return false;
	}	
	/*if(Festatus.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un estatus.</div>');
		Festatus.focus();
		div.slideDown('slow')
		
		return false;
	}*/

	//Importante enviar array procesado
	var param = 'task=add&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
				return false;
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);

				if(confirm('¿Desea agregar otro registro?', 'Los cambios fueron guardados con exito.'))
					window.location.href=urlOrigen + '?task=add';
				else
					window.location.href=urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function add_evento(){
	var urlOrigen = 'eventos.php';
	var Ffecha = $('#Ffecha2');
	var Fhora = $('#Fhora');
	var Ftipo = $('#Ftipo');
	var Fcliente = $('#Fcliente');
	//var Festatus = $('#Festatus');
	var Fsalon = $('#Fsalon');
	var div = $('#notices');
	
	div.html('');
	
	if(Ffecha.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione una fecha valida.</div>');
		Ffecha.focus();
		div.slideDown('slow')
		
		return false;
	}if(Fhora.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione una hora valida.</div>');
		Fhora.focus();
		div.slideDown('slow')
		
		return false;
	}if(Ftipo.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un tipo de evento valido.</div>');
		Ftipo.focus();
		div.slideDown('slow')
		
		return false;
	}if(Fcliente.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un Cliente.</div>');
		Fcliente.focus();
		div.slideDown('slow')
		
		return false;
	}
	/*
	if(Festatus.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un estatus.</div>');
		Festatus.focus();
		div.slideDown('slow')
		
		return false;
	}
	*/
	if(Fsalon.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un Salón valido.</div>');
		Fsalon.focus();
		div.slideDown('slow')
		
		return false;
	}

	//Importante enviar array procesado
	var param = 'task=add&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
				return false;
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);

				if(confirm('¿Desea agregar otro registro?', 'Los cambios fueron guardados con exito.'))
					window.location.href=urlOrigen + '?task=add';
				else
					window.location.href=urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function update_evento(objeto){
	var act = objeto.getAttribute('act');
	var estatus = objeto.getAttribute('estatus');
	var urlOrigen = 'eventos.php';
	
	var Ffecha = $('#Ffecha2');
	var Fhora = $('#Fhora');
	var Ftipo = $('#Ftipo');
	var Fcliente = $('#Fcliente');
	var editar_tipo = $('#editar_tipo');
	var Fsalon = $('#Fsalon');
	var div = $('#notices');
	
	div.html('');
	
	if(Ffecha.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione una fecha valida.</div>');
		Ffecha.focus();
		div.slideDown('slow')
		
		return false;
	}if(Fhora.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione una hora valida.</div>');
		Fhora.focus();
		div.slideDown('slow')
		
		return false;
	}if(Ftipo.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un tipo de evento valido.</div>');
		Ftipo.focus();
		div.slideDown('slow')
		
		return false;
	}if(Fcliente.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un Cliente.</div>');
		Fcliente.focus();
		div.slideDown('slow')
		
		return false;
	}/*if(Festatus.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un estatus.</div>');
		Festatus.focus();
		div.slideDown('slow')
		
		return false;
	}*/if(Fsalon.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un Salón valido.</div>');
		Fsalon.focus();
		div.slideDown('slow')
		
		return false;
	}

	//Importante enviar array procesado
	var param = 'task=alter&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);

				if(act=='exit'){
					if(editar_tipo.val() != null)
						window.location = editar_tipo.val()+'.php';
					else
						window.location = urlOrigen;
				}
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function activar_suspender(objeto)
{

	var tipo = objeto.getAttribute('tipo');
	var id = objeto.getAttribute('attid');
	var accion = objeto.getAttribute('accion');
	var task = 'suspend';

	if(accion == 'activar')
		task = 'activate';
	
	if(confirm('¿Está seguro que desea continuar con la operación?', 'Confirme.'))
	{
	
		var param = 'task='+task+'&tipo='+tipo+'&id='+id;
		$.ajax({
			type: "POST",
			url: "op_extras.php",
			data: param,
			async: true,
			beforeSend: function(){
				$('#cargando').show();
			},
			success: function(msg){		
				if(msg == 1){					
					obj = document.getElementById("filtro");
					obj.value='';
					filtrar(obj);					
	
				}
			},
			complete: function(){
				$('#cargando').hide('fast');
			}
		});
	
	}

}

function add_grupo(){
	var urlOrigen = 'grupos.php';
	var Fnombre = $('#Fnombre');
	//var Festatus = $('#Festatus');
	var div = $('#notices');
	
	div.html('');
	
	if(Fnombre.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre valido.</div>');
		Fnombre.focus();
		div.slideDown('slow')
		
		return false;
	}
	/*if(Festatus.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un estatus.</div>');
		Festatus.focus();
		div.slideDown('slow')
		
		return false;
	}*/

	//Importante enviar array procesado
	var param = 'task=add&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
				return false;
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);

				if(confirm('¿Desea agregar otro registro?', 'Los cambios fueron guardados con exito.'))
					window.location.href=urlOrigen + '?task=add';
				else
					window.location.href=urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}
function update_grupo(objeto){
	var act = objeto.getAttribute('act');
	var urlOrigen = 'grupos.php';
	var Fnombre = $('#Fnombre');
	var Festatus = $('#Festatus');
	var div = $('#notices');
	
	div.html('');
	
	if(Fnombre.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre valido.</div>');
		Fnombre.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Festatus.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un estatus.</div>');
		Festatus.focus();
		div.slideDown('slow')
		
		return false;
	}

	//Importante enviar array procesado
	var param = 'task=alter&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);

				if(act=='exit')
					window.location = urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function update_empleado(objeto){
	var act = objeto.getAttribute('act');
	var urlOrigen = 'empleados.php';
	var Fnombres = $('#Fnombres');
	var Fapaterno = $('#Fapaterno');
	var Famaterno = $('#Famaterno');
	var Fusuario = $('#Fusuario');
	var Fpwd = $('#Fpwd');
	var Fpwd2 = $('#Fpwd2');
	var Ftel = $('#Ftel');
	var Fperfil = $('#Fperfil');
	var Festatus = $('#Festatus');
	var div = $('#notices');
	
	div.html('');
	
	if(Fnombres.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre valido.</div>');
		Fnombres.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Fapaterno.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un apellido paterno valido.</div>');
		Fapaterno.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Famaterno.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un apellido materno valido.</div>');
		Famaterno.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\D{2,4})+$/.test(Fusuario.val()))) {
		div.html('<div class=\"notice2\"> Escriba un email valido.</div>');
		Fusuario.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Fpwd.val() != '' || Fpwd2.val() != '') {
		if(Fpwd.val() == ''){
			div.html('<div class="notice2">Debe ingresar una contraseña.</div>');
			div.slideDown("slow");
			Fpwd.select();
			
			return false;
		}
		if(Fpwd2.val() == ''){
			div.html('<div class="notice2">Debe confirmar la contraseña.</div>');
			div.slideDown("slow");
			Fpwd2.select();
			
			return false;
		}	
		if(Fpwd.val() != Fpwd2.val()){
			div.html('<div class="notice2">Las contraseñas no coinciden.</div>');
			div.slideDown("slow");
			Fpwd.select();
			
			return false;
		}
	}
	if(Ftel.val() == ''){
		div.html('<div class=\"notice2\"> Debe ingresar un teléfono.</div>');
		Ftel.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Fperfil.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un grupo.</div>');
		Fperfil.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Festatus.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un estatus.</div>');
		Festatus.focus();
		div.slideDown('slow')
		
		return false;
	}

	//Importante enviar array procesado
	var param = 'task=alter&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);

				if(act=='exit')
					window.location = urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function add_empleado(objeto){
	var urlOrigen = 'empleados.php';
	var Fnombres = $('#Fnombres');
	var Fapaterno = $('#Fapaterno');
	var Famaterno = $('#Famaterno');
	var Fusuario = $('#Fusuario');
	var Fpwd = $('#Fpwd');
	var Fpwd2 = $('#Fpwd2');
	var Ftel = $('#Ftel');
	var Fperfil = $('#Fperfil');
	//var Festatus = $('#Festatus');
	var div = $('#notices');
	
	div.html('');
	
	if(Fnombres.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre valido.</div>');
		Fnombres.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Fapaterno.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un apellido paterno valido.</div>');
		Fapaterno.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Famaterno.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un apellido materno valido.</div>');
		Famaterno.focus();
		div.slideDown('slow')
		
		return false;
	}
	if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\D{2,4})+$/.test(Fusuario.val()))){
		div.html('<div class=\"notice2\"> Escriba un email valido.</div>');
		Fusuario.focus();
		div.slideDown('slow')
		
		return false;
	}


	if(Fpwd.val() == ''){
		div.html('<div class="notice2">Debe ingresar una contraseña.</div>');
		div.slideDown("slow");
		Fpwd.select();
		
		return false;
	}
	if(Fpwd2.val() == ''){
		div.html('<div class="notice2">Debe confirmar la contraseña.</div>');
		div.slideDown("slow");
		Fpwd2.select();
		
		return false;
	}	
	if(Fpwd.val() != Fpwd2.val()){
		div.html('<div class="notice2">Las contraseñas no coinciden.</div>');
		div.slideDown("slow");
		Fpwd.select();
		
		return false;
	}
	if(Ftel.val() == ''){
		div.html('<div class=\"notice2\"> Debe ingresar un teléfono.</div>');
		Ftel.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Fperfil.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un grupo.</div>');
		Fperfil.focus();
		div.slideDown('slow')
		
		return false;
	}
	/*if(Festatus.val() == ''){
		div.html('<div class=\"notice2\"> Seleccione un estatus.</div>');
		Festatus.focus();
		div.slideDown('slow')
		
		return false;
	}*/

	//Importante enviar array procesado
	var param = 'task=add&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		url: 'op_'+urlOrigen,
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
				return false;
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);

				if(confirm('¿Desea agregar otro registro?', 'Los cambios fueron guardados con exito.'))
					window.location.href=urlOrigen + '?task=add';
				else
					window.location.href=urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function addsalones(objeto){
	
	var urlOrigen = 'salones_add.php'
	var Fnombre = $('#Fnombre');
	var Ftel = $('#Ftel');
	var div = $('#notices');
	
	div.html('');
	
	if(Ftel.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un teléfono valido.</div>');
		Ftel.focus();
		div.slideDown('slow')
		
		return false;
	}
	if(Fnombre.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre valido.</div>');
		Fnombre.focus();
		div.slideDown('slow')
		
		return false;
	}

	//Importante enviar array procesado
	var param = 'task=add&'+$('#formulario').serialize();

	$.ajax({
		type: 'POST',
		// url: 'op_'+urlOrigen,
		url: 'op_salones.php',
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
				return false;
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);					

			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function updatesalon(objeto){
	var act = objeto.getAttribute('act');	
	var urlOrigen = 'groups.php'
	var Fgrupo = $('#Fgrupo');
	var div = $('#notices');
	
	div.html('');

	
	if(Fgrupo.val() == ''){
		div.html('<div class=\"notice2\"> Escriba un nombre de grupo.</div>');
		Fgrupo.select();
		div.slideDown('slow')
		
		return false;
	}

	// Obtengo los valores de las ACCIONES que se permitiran para este GRUPO
	var arrmods = inputchekados();

	//Importante enviar array procesado
	var param = 'task=alter&'+$('#formulario').serialize()+'&arrmods='+arrmods;

	$.ajax({
		type: 'POST',
		url: 'opgroups.php',
		data: param,
		beforeSend: function(){
			$('#cargando').show();
		},
		success: function(msg){
			if(msg == -1){
				div.html('<div class=\"notice2\">Ocurrio un error al grabar los datos.</div>');
				div.slideDown('slow');
				
				return false;
			}else{
				div.html('<div class=\"notice5\">Los cambios fueron guardados con exito.</div>');
				div.slideDown('slow').delay(900);					

				if(act=='exit')
					window.location = urlOrigen;
			}
		},
		complete: function(){
			$('#cargando').hide('fast');
		}
	});

}

function eliminar_registro(objeto){
	var tipo = objeto.getAttribute('tipo');
	var id = objeto.getAttribute('identif');
	var param='task=delete&Fid='+id;

	switch(tipo)
	{
		case 'empleado':
			urlOrigen= "empleados.php";
		break;
		case 'grupo':
			urlOrigen= "grupos.php";
		break;
		case 'evento':
			urlOrigen= "eventos.php";
		break;
		case 'cliente':
			urlOrigen= "clientes.php";
		break;
		case 'salon':
			urlOrigen= "salones.php";
		break;
		case 'servicio':
			urlOrigen= "servicios.php";
		break;
		case 'factura':
			urlOrigen= "facturas.php";
		break;
		case 'pago':
			urlOrigen= "pagos.php";
		break;
		case 'tipo_evento':
			urlOrigen= "tipo_eventos.php";
		break;
	}
	
	if(confirm('¿Está seguro que desea eliminar este registro?')){

		$.ajax({
			type: "POST",
			url: 'op_'+urlOrigen,
			data: param,
			beforeSend: function(){
				$('#cargando').show();
			},
			success: function(msg){
				$('#cargando').hide('fast');
				
				if(msg == -1){
					alert('Ocurrio un error al eliminar el registro.');
					
				}else{
					alert('Registro eliminado con éxito');
					window.location = urlOrigen;

				}
			},
			complete: function(){
				$('#cargando').hide('fast');
			}
		});	

	}//if

}

function filtrar(objeto) {
	var tipo = objeto.getAttribute('tipo');
	var filtro = objeto.value;

	var url = 'filtros.inc.php';
	var divUpd = '#cont';
	var param = 'mod='+tipo+'&'+$('#formulario').serialize();
	
	$('#cargando').show();
	$(divUpd).load(url, param, function(){
			$('#cargando').hide('fast');
	});

}

function cambiarest(objeto){
	var id = objeto.getAttribute('identif');
	var tipo = objeto.getAttribute('tipo');
	var lab = objeto.getAttribute('lab');
	
	var param = 'task='+tipo+'&Fid=' + id;

	var	urlOrigen = "eventos.php";
	
	if(confirm('¿Confirme que el evento seleccionado sea '+lab+'?')){

		$.ajax({
			type: "POST",
			url: 'op_'+urlOrigen,
			data: param,
			beforeSend: function(){
				$('#cargando').show();
			},
			success: function(msg){
				$('#cargando').hide('fast');
				
				if(msg == -1){
					alert('Ocurrio un error al realizar el proceso.');
					
				}else{
					if(tipo == 'sell')					
						window.location = urlOrigen;
					else {
						obj = document.getElementById("filtro");
						obj.value='';
						filtrar(obj);	
					}

				}
			},
			complete: function(){
				$('#cargando').hide('fast');
			}
		});	

	}//if

}

function exportar_pdf(objeto) {
	var id = objeto.getAttribute('identif');
	var tipo = objeto.getAttribute('tipo');
	var url = 'pdf_cotizacion.php?id='+id+'&tipo='+tipo;

	window.open(url, '_blank','fullscreen=yes');
}

function generar_reporte_pdf(objeto) {
	var tipo = objeto.getAttribute('tipo');
	var url = 'pdf_'+tipo+'.php';
	var param = $("#formulario").serialize();
	url = url + '?' + param;

	window.open(url, '_blank','fullscreen=yes');
}


// function dibujar_grafica(string_generado, titulo, div) {
// 	var data = google.visualization.arrayToDataTable([
// 	  ['algo','algo'], string_generado
// 	]);

// 	var options = {
// 	  title: titulo
// 	};

// 	var chart = new google.visualization.PieChart(document.getElementById(div);
// 	chart.draw(data, options);
// }