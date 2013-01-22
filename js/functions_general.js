var espera = '<div align="center"><img src="panel_img/cargando.gif" /><br />Cargando informaci&oacute;n. Espera un momento por favor.</div>'
var esperalogin = '<img src="http://neubox.net/lod.gif" alt="" />';
var ha_iniciado = false;
editando_datos = false;
var SAFAVOR = 0;
var MONTO = 0;
var IVA = 0;
var TIENEIVA = false;
var xmlDoc;

var navegador = 1;
if(navigator.userAgent.indexOf("MSIE") >= 0) navegador = 0;

function crearObjetoXML(XMLfile)
{
    if (window.XMLHttpRequest) //Firefox, Chrome, Safari, etc
    {
        xmlDoc=new window.XMLHttpRequest();
        xmlDoc.open("GET",XMLfile,false);
        xmlDoc.send("");
        xmlDoc= xmlDoc.responseXML;
    }
    else //if (ActiveXObject("Microsoft.XMLDOM")) // IE 5 and IE 6
    {
        try
        {
            xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
            xmlDoc.async="false";
            xmlDoc.load(XMLfile);
        }
        catch(e)
        {
            alert('Your current internet browser does not supports this website. You should try an other one.');
        }
    }
}

/**********  Conexion XMLHTTPRequest *************/
function XHConnPOST()
{
    var xmlhttp, bComplete = false;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (e) {
            try {
                xmlhttp = new XMLHttpRequest();
            }
            catch (e) {
                xmlhttp = false;
            }
        }
    }
    if (!xmlhttp) return null;
    this.connect = function(sURL, sMethod, sVars, fnDone)
    {
        if (!xmlhttp) return false;
        bComplete = false;
        sMethod = sMethod.toUpperCase();
        try {
            if (sMethod == "GET")
            {
                xmlhttp.open(sMethod, sURL+"?"+sVars, true);
                sVars = "";
            }
            else
            {
                xmlhttp.open(sMethod, sURL, true);
                xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
                xmlhttp.send(sVars);
            }
            xmlhttp.onreadystatechange = function(){
                if (xmlhttp.readyState == 4 && !bComplete)
                {
                    bComplete = true;
                    fnDone(xmlhttp);
                }
            };
            xmlhttp.send(sVars);
        }
        catch(z) {
            return false;
        }
        return true;
    };
    return this;
}

function Mostrar_Ocultar_PWD(targets, validators)
{
    var fields = targets.split(',');
    var validate = validators.split(',');
    var total = fields.length;

    for(i=0; i<total; i++)
    {
        if( document.getElementById(fields[i]).type == "password" ){
            inputType= "text";
        }
        else
            inputType= "password";

        var htmlCode='<input type="' + inputType + '" name="' + fields[i] + '" id="' + fields[i] + '"' + ' class="' + validate[i] + ' text_form"'
        + ' value="' + document.getElementById(fields[i]).value + '"  maxlength="15" />';

        document.getElementById('aux_'+fields[i]).innerHTML = htmlCode;
    }
}

function randomPass(targets)
{
    fields= targets.split(',');

    total = fields.length;
    pass = random_pass();

    for(i=0; i<total; i++)
    {
        document.getElementById(fields[i]).value=pass;
    }
}

function random_pass()
{
    var length = 8;
    var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%&/()=?_-:;,.[]{}+*'";

    var pass = "";
    var i=0;

    for (i=0; i<length; i++)
    {
        pass = pass + random_char(chars);
    }
    return pass;
}

function random_char(charlist)
{
    var now = new Date();
    var seed = now.getSeconds();
    var num = Math.floor(Math.random(seed) * charlist.length);
    return charlist.charAt(num);
}

function Epp_convocar(params,target,tatrib)
{
    if(target != '')
    {
        switch (tatrib)
        {
            case 'innerHTML':
                document.getElementById(target).innerHTML = '<img src="http://neubox.net/v3/mx/lod.gif"/>';
                break;
            case 'value':
                document.getElementById(target).disabled = true;
                break;
        }
    }

    var myConn = new XHConnPOST();
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var regreso = function (oXML) {
        if(target != '')
        {
            switch (tatrib)
            {
                case 'innerHTML':
                    document.getElementById(target).innerHTML = oXML.responseText;
                    break;
                case 'value':
                    document.getElementById(target).value = oXML.responseText;
                    document.getElementById(target).disabled = false;
                    break;
            }
        }
    };

    switch(params)
    {
        case 'peticion=ccheck':
            params+='&contacto='+document.getElementById('contacto').value;
            break;
        case 'peticion=cnctgen':
            params+='&nombre='+escape(document.getElementById('nombre').value)+'&empresa='+escape(document.getElementById('empresa').value);
            break;
        case 'peticion=cnctcreate':
            params+='&contacto='+escape(document.getElementById('contacto').value);
            break;
    }
    myConn.connect("EppPetitions.php", "POST", params, regreso);
}

/******************************************************************
FUNCION: accion_masiva_contactos
DESCRIPCION: Realizara una accion en imprime.php dependiendo de los eventos que ocurran en el panel
ENTRADAS:---- opcionales ----
        (solo se utilizaran en los botones de editar contactos y name servers. para los combobox no aplican)
	clic: cadena con la informacion necesaria para realizar el proceso
	accion: accion que se realizara en imprime.php
	 --------------------
SALIDAS: << ninguna >>
******************************************************************/
function accion_contactos_whois(accion, confirma){
    var cad = "";

    cad = dameCadContactos();
    document.getElementById('tr_action_confirm').style.display='none';
    if(accion != '')
    {
        if( accion=='eliminar_contactos' && confirma==null )
            document.getElementById('tr_action_confirm').style.display='';
        else if( accion=='nuevo_contacto' )
        {
            //Modificar las acciones que se siguen al salir del recuadro de modificaciones
            document.getElementById('cnct_Salir').onclick=function(){
                exitCnct();
                imprimeContenido('contactos');
            };
            document.getElementById('cnct_img_salir').onclick=function(){
                exitCnct();
                imprimeContenido('contactos');
            };
            editContactsWhois(document.getElementById('cliente_id').value,'0');
        }
        else if( accion=='edita_contacto' )
        {
            //Modificar las acciones que se siguen al salir del recuadro de modificaciones
            document.getElementById('cnct_Salir').onclick=function(){
                exitCnct();
                imprimeContenido('contactos');
            };
            document.getElementById('cnct_img_salir').onclick=function(){
                exitCnct();
                imprimeContenido('contactos');
            };
            editContactsWhois(document.getElementById('cliente_id').value,confirma);
        }
        else
        {
            // Checar que halla al menos uno seleccionado
            if(cad != ''){
                if( accion=='predeterminar_contacto' )
                {
                    cad=cad.split(',');
                    cad=cad[0];
                }

                // Mandar llamar con ajax las opciones de la ccci�n masiva
                var myConn = new XHConnPOST();
                var paramPost = "contactos="+escape(cad)+"&cliente_id="+escape(document.getElementById('cliente_id').value);
                document.getElementById('contenido').innerHTML = espera;
                if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
                var peticion = function (oXML) {

                    if( accion=='eliminar_contactos' )
                        document.getElementById('contenido').innerHTML = oXML.responseText;
                    else if( accion=='predeterminar_contacto' )
                        imprimeContenido('contactos');
                };
                myConn.connect("imprime.php?contenido="+accion, "POST", paramPost, peticion);
            }
            else {
                alert('Debes seleccionar al menos un contacto por afectar');
                document.getElementById('accion_masiva').value = '';
            }
        }
    }
}



/***********************************************************************************************
FUNCION:		dameCadOrdenes
DESCRIPCION:	devuelve una cadena con nombres de contacto separados por coma
PARAMETROS:		<< ninguna >>
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function dameCadContactos(){
    var cad = "";

    inputs = document.getElementsByTagName("INPUT");
    // recorre los inputs hasta encontrar el primer checkbox y obtiene su estado
    for ( i=0; i<inputs.length; i++ )
    {
        if ( (inputs[i].type == "checkbox") && (inputs[i].value!="") && inputs[i].checked==true ) {
            cad += inputs[i].value+',';
        }
    }
    if(cad != '') {
        // Quitar la ultima coma
        cad = cad.substr(0,cad.length-1);
    }

    return cad;
}


/***********************************************************************************************
	FUNCION:		getContactData
	DESCRIPCION:	Obtiene la informaci�n de un contacto y la despliega en campos con cierto ID
	PARAMETROS:		id_Cliente:		ID del cliente que intenta obtener la informaci�n de un contacto.
					idOrden:		De existir alguna, es el n�mero de la orden que esta relacionada
									a la edici�n de este contacto. Este campo solo se usa para poner
									los datos del contacto en el campo adecuado ya que ayuda a
									diferenciar un campo de otro por su ID.
					allButtons:		Se usa para indicar si se mostrar�n los botones para crear un
									nuevo contacto o tomar los datos de la informaci�n del cliente.
									Estos botones s�lo se muestran  en la secci�n "Mi informaci�n ->
									informaci�n" por ser ah� donde est� disponible la info del cliente
									de manera directa y porque ah� es donde se administran los contactos
									del cliente.
	SALIDAS:		<< ninguna >>
	***********************************************************************************************/
function getContactData(idCliente,idOrden,allButtons)
{
    //Poner el boton de guardar para que haga modificaciones
    document.getElementById('cnct_Guardar'+idOrden).onclick=function(){
        modifCnct(idCliente,'',idOrden,allButtons);
    };

    //Deshabilitar los botones hasta que todo se haya cargado
    if( allButtons=='si' )
    {
        document.getElementById('cnct_TomaData'+idOrden).disabled=true;
        document.getElementById('cnct_Nuevo'+idOrden).disabled=true;
    }
    document.getElementById('cnct_Guardar'+idOrden).disabled=true;

    //Limpiar formulario para evitar confusion del cliente
    cleanCnctData(idOrden);

    document.getElementById('cnct_id_nfo'+idOrden).innerHTML = '<img src="http://neubox.net/v3/mx/lod.gif"/><span style="font-size:11px; text-align:left; color:#009303;">&nbsp;' + xmlDoc.getElementsByTagName('seccion')[8].getElementsByTagName('mensaje')[23].childNodes[0].nodeValue + '..</span>';
    contacto=document.getElementById('cnct_id'+idOrden).value;

    var myConn = new XHConnPOST();
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var regreso = function (oXML) {
        var ans=oXML.responseText;
        ans=html_entity_decode(ans);
        var arr_ans=ans.split("|");

        if( arr_ans[0]=='0' )
        {
            dameEstados('cnct_SelectEdos'+idOrden,'cnct_ComboEdos'+idOrden,'cnct_ComboPais'+idOrden,'San Luis Potos�','207px','combobox');
            document.getElementById('cnct_msg1'+idOrden).innerHTML='<span style="font-size:11px; color:#CC3333;">'+arr_ans[1]+'</span>';
        }
        else
        {
            document.getElementById('cnct_nombre'+idOrden).value=arr_ans[1];
            document.getElementById('cnct_email'+idOrden).value=arr_ans[6];
            document.getElementById('cnct_direccion'+idOrden).value=arr_ans[3];
            document.getElementById('cnct_cp'+idOrden).value=arr_ans[5];
            document.getElementById('cnct_ComboPais'+idOrden).value=arr_ans[7];
            document.getElementById('cnct_ciudad'+idOrden).value=arr_ans[4];
            document.getElementById('cnct_telcc'+idOrden).value=arr_ans[9];
            document.getElementById('cnct_tel'+idOrden).value=arr_ans[10];
            document.getElementById('cnct_empresa'+idOrden).value=arr_ans[2];
            document.getElementById('cnct_msg1'+idOrden).innerHTML='';

            dameEstados('cnct_SelectEdos'+idOrden,'cnct_ComboEdos'+idOrden,'cnct_ComboPais'+idOrden,arr_ans[8],'207px','combobox');

            //Volver a habilitar el boton de guardar porque el proceso fue correcto.
            document.getElementById('cnct_Guardar'+idOrden).disabled=false;
        //document.getElementById('cnct_id_nfo'+idOrden).innerHTML='<img id="cnct_img_pencil" src="panel_img/pencil.gif" style="cursor:pointer" border="0" onClick=""/>';
        }

        document.getElementById('cnct_id_nfo'+idOrden).innerHTML = '';

        //Volver a habilitar los botones para obtener los datos y crear nuevos contactos
        if(allButtons=='si')
        {
            document.getElementById('cnct_TomaData'+idOrden).disabled=false;
            document.getElementById('cnct_Nuevo'+idOrden).disabled=false;
        }
    };

    params="peticion=contact_info&contacto=" + escape(contacto) + "&id=" + escape(idCliente);
    myConn.connect("CnctPetitions.php", "POST", params, regreso);
}

/***********************************************************************************************
FUNCION: dameEstados
DESCRIPCION: Llama asincronamente al server para imprimir los estados que corresponden al pais
PARAMETROS:     id_ContEdos: El id del elemento que contiene al combo de los estados y cuyo
			     contenido va a ser remplazado por la nueva lista de estados.
		id_ComboEdos: El id con el que se debe generar el combo de los estados.
		id_ComboPais: El combo del pais que contiene el pais seleccionado. Se usa el
                              id del combo en lugar del pais directamente para que también sea
                              posible usar esta función en eventos como onchange y onclick.
		estado:	Si se quiere que un estado aparezca ya seleccionado aquí debe de ir escrito.
		width: Si se quiere que el combo de los estados tenga un tamaño determinado aquí debe especificarse.
		clase: Si se quiere que el combo de los estados tenga una clase determinada aquí debe especificarse.
SALIDAS: << ninguna >>
***********************************************************************************************/
function dameEstados(id_ContEdos, id_ComboEdos, id_ComboPais, estado, width, clase, isContactos) //'selectEstados'
{

    var accion = 'imprime_estados';
    var codigo_pais = document.getElementById(id_ComboPais).value;
    estado = escape(estado);
	var edo = '';

    document.getElementById(id_ContEdos).innerHTML = '<img src="http://neubox.net/lod.gif" alt="Cargando" />';
    var myConn = new XHConnPOST();
    var post="idCombo=" + id_ComboEdos + "&pais=" + codigo_pais + "&estado=" + estado + "&width=" + width + "&clase=" + clase + "&isContactos=" + isContactos;
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var atResponse = function (oXML) {
		document.getElementById(id_ContEdos).innerHTML = oXML.responseText;

    };

    myConn.connect("display.php?contenido="+accion, "POST", post, atResponse);
}



/***********************************************************************************************
	FUNCION:		getDataClienteCnct
	DESCRIPCION:	Obtiene los datos del cliente desde la pantalla de "Mi informaci�n -> informaci�n"
					y la copia al di�logo del contacto.
	PARAMETROS:		<< ninguno >>
	SALIDAS:		<< ninguna >>
	***********************************************************************************************/
function getDataClienteCnct()
{
    document.getElementById('cnct_nombre').value=document.getElementById('nombre').value + ' ' + document.getElementById('apellidos').value;
    document.getElementById('cnct_email').value=document.getElementById('email').value;
    document.getElementById('cnct_direccion').value=document.getElementById('direccion').value;
    document.getElementById('cnct_cp').value=document.getElementById('cp').value;
    document.getElementById('cnct_ComboPais').value=document.getElementById('pais').value;
    document.getElementById('cnct_ciudad').value=document.getElementById('ciudad').value;
    document.getElementById('cnct_telcc').value='';
    document.getElementById('cnct_tel').value=document.getElementById('telefono').value;
    document.getElementById('cnct_empresa').value=document.getElementById('empresa').value;

    dameEstados('cnct_SelectEdos','cnct_ComboEdos','cnct_ComboPais',document.getElementById('estados').value,'207px','combobox');
}

/***********************************************************************************************
FUNCION: createCnct
DESCRIPCION: Crea un nuevo contacto y despues de crearlo regresa la accion del boton guardar a modificar un contacto.
PARAMETROS:	idCliente:	ID del cliente que desea crear el contacto nuevo.
SALIDAS: << ninguna >>
***********************************************************************************************/
function createCnct(idCliente)
{
    //Informar que el contacto se esta creando
    document.getElementById('cnct_msg1').innerHTML = '<img src="http://neubox.net/v3/mx/lod.gif"/><span style="font-size:11px; text-align:left; color:#009303;">&nbsp;'+xmlDoc.getElementsByTagName('seccion')[8].getElementsByTagName('mensaje')[22].childNodes[0].nodeValue+'..</span>';
    //Bloquear el boton de guardar
    document.getElementById('cnct_Guardar').disabled=true;

    res_val=validaDatosCnct('');

    if(res_val==0)
    {
        var myConn = new XHConnPOST();
        if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
        var onReturn = function (oXML) {
            var ans=oXML.responseText;
            ans=html_entity_decode(ans);
            var arr_ans=ans.split("|");

            if(arr_ans[0]=='0')
            {
                document.getElementById('cnct_id_nfo').innerHTML = '';
                //No fue posible crear el contacto
                document.getElementById('cnct_msg1').innerHTML = '<img src="panel_img/panel_form_error.gif" alt="error" /><span style="font-size:11px; color:#CC3333;">&nbsp;' + arr_ans[1] + '</span>';
            }
            else
            {
                if( document.getElementById('contacto').nodeName.toLowerCase()!='select' )
                {
                    document.getElementById('selectContactos').innerHTML='<select name="contacto" class="combobox" id="contacto" width="240px" style="width: 240px" ></select>';
                    document.getElementById('CnctButtons').innerHTML+='<input type="button" name="Bmodif" id="Bmodif" value="' + xmlDoc.getElementsByTagName('seccion')[2].getElementsByTagName('boton')[15].childNodes[0].nodeValue + '" onclick="editContacts(\'' + idCliente + '\',\'0\');" class="botones" />';
                }

                document.getElementById('cnct_id_nfo').innerHTML = '';

                nuevo_cnct=document.getElementById('cnct_id').value;
                nuevo_opc='<option class="option" value="' + nuevo_cnct + '" selected >'
                + nuevo_cnct + '</option>';

                document.getElementById('contacto').innerHTML+=nuevo_opc;
                document.getElementById('contacto').value=nuevo_cnct;

                document.getElementById('cnct_id_cap').innerHTML='<select name="cnct_id" id="cnct_id" class="combobox" width="207px" style="width: 207px" onchange="getContactData(\''+idCliente+'\',\'\',\'si\')">'
                +document.getElementById('contacto').innerHTML+'</select>';
                document.getElementById('cnct_id').value=nuevo_cnct;

                document.getElementById('cnct_Nuevo').style.display='';
                document.getElementById('cnct_Guardar').onclick=function(){
                    modifCnct(idCliente,'','','no');
                };

                document.getElementById('cnct_id_nfo').innerHTML = '';
                //El contacto fue creado satisfactoriamente
                document.getElementById('cnct_msg1').innerHTML = '<img src="panel_img/panel_form_ok.gif" alt="ok" /><span style="font-size:11px; color:#009303;">&nbsp;' + xmlDoc.getElementsByTagName('seccion')[9].getElementsByTagName('notificacion')[22].childNodes[0].nodeValue + '</span>';
            }

            //Volver a habilitar el boton de Guardar

            document.getElementById('cnct_Guardar').disabled=false;
        };

        params="peticion=contact_create&id=" + escape(document.getElementById('cnct_id').value);
        params+="&nombre=" + escape(document.getElementById('cnct_nombre').value);
        params+="&compania=" + escape(document.getElementById('cnct_empresa').value);
        params+="&email=" + escape(document.getElementById('cnct_email').value);
        params+="&direccion=" + escape(document.getElementById('cnct_direccion').value);
        params+="&ciudad=" + escape(document.getElementById('cnct_ciudad').value);
        params+="&estado=" + escape(document.getElementById('cnct_ComboEdos').value);
        params+="&pais=" + escape(document.getElementById('cnct_ComboPais').value);
        params+="&cpostal=" + escape(document.getElementById('cnct_cp').value);
        params+="&telcc=" + escape(document.getElementById('cnct_telcc').value);
        params+="&tel=" + escape(document.getElementById('cnct_tel').value);


        myConn.connect("CnctPetitions.php", "POST", params, onReturn);
    }
    else
    {
        document.getElementById('cnct_msg1').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" /><span style="font-size:11px; color:#CC3333;">&nbsp;'+res_val+'</span>';
        document.getElementById('cnct_Guardar').disabled=false;
    }
}



/***********************************************************************************************
	FUNCION:		modifCnct
	DESCRIPCION:	Manda validar los datos de un contacto y lo modifica si estuvieron correctos.
	PARAMETROS:		idCliente:		ID del cliente que desea modificar el contacto.
					idSubCliente:	Si el contacto pertenece a un SubCliente de un reseller aqu� se
									especifica el id de ese subcliente.
					idOrden:		De existir alguna, es el n�mero de la orden que esta relacionada
									a la edici�n de este contacto. Este campo solo se usa para poner
									los datos del contacto en el campo adecuado ya que ayuda a
									diferenciar un campo de otro por su ID.
					allButtons:		Se usa para bloquear temporalmente los botones para crear un nuevo
									contacto o tomar los datos de la informaci�n del cliente. El bloqueo
									se realiza s�lo mientras se guardan los cambios.
									Estos botones s�lo se muestran  en la secci�n "Mi informaci�n ->
									informaci�n" por ser ah� donde est� disponible la info del cliente
									de manera directa y porque ah� es donde se administran los contactos
									del cliente.
	SALIDAS:		<< ninguna >>
	***********************************************************************************************/
function modifCnct(idCliente,idSubCliente,idOrden,allButtons)
{
    //Informar que el contacto se esta guardando
    document.getElementById('cnct_msg1'+idOrden).innerHTML = '<img src="http://neubox.net/v3/mx/lod.gif"/><span style="font-size:11px; text-align:left; color:#009303;">&nbsp;'+xmlDoc.getElementsByTagName('seccion')[8].getElementsByTagName('mensaje')[21].childNodes[0].nodeValue+'..</span>';
    //Bloquear los botones hasta que se realize la acci�n
    document.getElementById('cnct_Guardar'+idOrden).disabled=true;
    if( allButtons=='si' )
    {
        document.getElementById('cnct_TomaData'+idOrden).disabled=true;
        document.getElementById('cnct_Nuevo'+idOrden).disabled=true;
    }

    res_val=validaDatosCnct(idOrden);

    if(res_val==0)
    {
        var myConn = new XHConnPOST();
        if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
        var onReturn = function (oXML) {
            var ans=oXML.responseText;
            ans=html_entity_decode(ans);
            var arr_ans=ans.split("|");

            if(arr_ans[0]=='0')
            {
                document.getElementById('cnct_id_nfo'+idOrden).innerHTML = '';
                //No fue posible modificar el contacto
                document.getElementById('cnct_msg1'+idOrden).innerHTML = '<img src="panel_img/panel_form_error.gif" alt="error" /><span style="font-size:11px; color:#CC3333;">&nbsp;' + arr_ans[1] + '</span>';
            }
            else
            {
                document.getElementById('cnct_id_nfo'+idOrden).innerHTML = '';
                //Se han guardado los datos del contacto
                document.getElementById('cnct_msg1'+idOrden).innerHTML = '<img src="panel_img/panel_form_ok.gif" alt="ok" /><span style="font-size:11px; color:#009303;">&nbsp;' + xmlDoc.getElementsByTagName('seccion')[9].getElementsByTagName('notificacion')[20].childNodes[0].nodeValue + '</span>';
            }
            //Volver a habilitar los botones
            document.getElementById('cnct_Guardar'+idOrden).disabled=false;
            if( allButtons=='si' )
            {
                document.getElementById('cnct_TomaData'+idOrden).disabled=false;
                document.getElementById('cnct_Nuevo'+idOrden).disabled=false;
            }
        }

        params="peticion=contact_update&id=" + escape(document.getElementById('cnct_id'+idOrden).value);
        params+="&nombre=" + escape(document.getElementById('cnct_nombre'+idOrden).value);
        params+="&compania=" + escape(document.getElementById('cnct_empresa'+idOrden).value);
        params+="&email=" + escape(document.getElementById('cnct_email'+idOrden).value);
        params+="&direccion=" + escape(document.getElementById('cnct_direccion'+idOrden).value);
        params+="&ciudad=" + escape(document.getElementById('cnct_ciudad'+idOrden).value);
        params+="&estado=" + escape(document.getElementById('cnct_ComboEdos'+idOrden).value);
        params+="&pais=" + escape(document.getElementById('cnct_ComboPais'+idOrden).value);
        params+="&cpostal=" + escape(document.getElementById('cnct_cp'+idOrden).value);
        params+="&telcc=" + escape(document.getElementById('cnct_telcc'+idOrden).value);
        params+="&tel=" + escape(document.getElementById('cnct_tel'+idOrden).value);
        params+="&idCliente=" + escape(idCliente);
        params+="&idSubCliente=" + escape(idSubCliente);

        myConn.connect("CnctPetitions.php", "POST", params, onReturn);

    }
    else
    {
        document.getElementById('cnct_msg1'+idOrden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" /><span style="font-size:11px; color:#CC3333;">&nbsp;'+res_val+'</span>';

        //Volver a habilitar los botones
        document.getElementById('cnct_Guardar'+idOrden).disabled=false;
        if( allButtons=='si' )
        {
            document.getElementById('cnct_TomaData'+idOrden).disabled=false;
            document.getElementById('cnct_Nuevo'+idOrden).disabled=false;
        }
    }
}



/***********************************************************************************************
	FUNCION:		exitCnct
	DESCRIPCION:	Oculta el di�logo para editar o crear nuevos contactos.
	PARAMETROS:		<< ninguno >>
	SALIDAS:		<< ninguna >>
	***********************************************************************************************/
function exitCnct()
{
    //Volver a hacer visible el boton de Nuevo si no lo estaba y desaparecer el div de los contactos
    document.getElementById('cnct_Nuevo').style.display='';
    ocultar_div_opaco('cnct_datos');
}



/***********************************************************************************************
	FUNCION:		validaDatosCnct
	DESCRIPCION:	V�lida que los datos ingresados para modificar o crear un contacto sean correctos
					y de lo contrario marca el error.
	PARAMETROS:		id_orden:		De existir alguna, es el n�mero de la orden que esta relacionada
									a la edici�n de este contacto. Este campo solo se usa para poner
									los datos del contacto en el campo adecuado ya que ayuda a
									diferenciar un campo de otro por su ID.
	SALIDAS:		<< ninguna >>
	***********************************************************************************************/
function validaDatosCnct(id_orden)
{
    var valido=true;
    var filtro_trim=/^\s+|\s+$/g;
    var filtro;
    var msg=0;

    filtro=/^[a-z0-9_\-\+\(\)\#\s]{1,25}$/i;
    document.getElementById('cnct_id'+id_orden).value=document.getElementById('cnct_id'+id_orden).value.replace(filtro_trim,'');
    document.getElementById('cnct_id_nfo'+id_orden).innerHTML='';
    if( !filtro.test(document.getElementById('cnct_id'+id_orden).value) )
    {
        document.getElementById('cnct_id_nfo'+id_orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[55].childNodes[0].nodeValue; //Contacto Inv�lido
    }

    filtro=/^[a-z0-9�������\.\:\,\-\& ]{3,95}$/i;
    document.getElementById('cnct_nombre'+id_orden).value=document.getElementById('cnct_nombre'+id_orden).value.replace(filtro_trim,'');
    document.getElementById('cnct_nombre_nfo'+id_orden).innerHTML='';
    if( !filtro.test(document.getElementById('cnct_nombre'+id_orden).value) )
    {
        document.getElementById('cnct_nombre_nfo'+id_orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[30].childNodes[0].nodeValue; //Nombre Inv�lido
    }

    filtro=/^[a-z0-9�������\.\- ]{3,35}$/i;
    document.getElementById('cnct_ciudad'+id_orden).value=document.getElementById('cnct_ciudad'+id_orden).value.replace(filtro_trim,'');
    document.getElementById('cnct_ciudad_nfo'+id_orden).innerHTML='';
    if( !filtro.test(document.getElementById('cnct_ciudad'+id_orden).value) )
    {
        document.getElementById('cnct_ciudad_nfo'+id_orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[33].childNodes[0].nodeValue; //Ciudad inv�lida
    }

    filtro=/^[a-z0-9�������#_\.\:\,\(\)\[\]\-\/\$\�\!\?\�\&\<\> ]{2,100}$/i;
    document.getElementById('cnct_empresa'+id_orden).value=document.getElementById('cnct_empresa'+id_orden).value.replace(filtro_trim,'');
    document.getElementById('cnct_empresa_nfo'+id_orden).innerHTML='';
    if( document.getElementById('cnct_empresa'+id_orden).value=='' )
        document.getElementById('cnct_empresa'+id_orden).value='No company';
    else if( !filtro.test(document.getElementById('cnct_empresa'+id_orden).value) )
    {
        document.getElementById('cnct_empresa_nfo'+id_orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[54].childNodes[0].nodeValue; //Empresa inv�lida
    }

    filtro=/^[a-z0-9�������#_\-\.\:\,\/\& ]{3,130}$/i;
    document.getElementById('cnct_direccion'+id_orden).value=document.getElementById('cnct_direccion'+id_orden).value.replace(filtro_trim,'');
    document.getElementById('cnct_direccion_nfo'+id_orden).innerHTML='';
    if( document.getElementById('cnct_direccion'+id_orden).value=='' )
        document.getElementById('cnct_direccion'+id_orden).value='No address';
    else if( !filtro.test(document.getElementById('cnct_direccion'+id_orden).value) )
    {
        document.getElementById('cnct_direccion_nfo'+id_orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[32].childNodes[0].nodeValue; //Direcci�n inv�lida
    }

    filtro=/^[a-z][a-z0-9_\-]*(\.[a-z0-9_\-]+){0,2}@[a-z0-9][a-z0-9\-]*(\.[a-z0-9\-]{4,}){0,2}(\.[a-z]{2,3}){1,2}$/i;
    document.getElementById('cnct_email'+id_orden).value=document.getElementById('cnct_email'+id_orden).value.replace(filtro_trim,'');
    document.getElementById('cnct_email_nfo'+id_orden).innerHTML='';
    if( !filtro.test(document.getElementById('cnct_email'+id_orden).value) )
    {
        document.getElementById('cnct_email_nfo'+id_orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[34].childNodes[0].nodeValue; //Email inv�lido
    }

    filtro=/^[a-z0-9\- ]{3,10}$/i;
    document.getElementById('cnct_cp'+id_orden).value=document.getElementById('cnct_cp'+id_orden).value.replace(filtro_trim,'');
    document.getElementById('cnct_cp_nfo'+id_orden).innerHTML='';
    if( document.getElementById('cnct_cp'+id_orden).value=='' )
        document.getElementById('cnct_cp'+id_orden).value='NoZipCode';
    else if( !filtro.test(document.getElementById('cnct_cp'+id_orden).value) )
    {
        document.getElementById('cnct_cp_nfo'+id_orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[35].childNodes[0].nodeValue; //C�digo postal inv�lido
    }

    filtro=/^[0-9]{1,3}$/;
    document.getElementById('cnct_telcc'+id_orden).value=document.getElementById('cnct_telcc'+id_orden).value.replace(filtro_trim,'');
    document.getElementById('cnct_tel_nfo'+id_orden).innerHTML='';
    if( document.getElementById('cnct_telcc'+id_orden).value=='' )
        document.getElementById('cnct_telcc'+id_orden).value='52';
    else if( !filtro.test(document.getElementById('cnct_telcc'+id_orden).value) )
    {
        document.getElementById('cnct_tel_nfo'+id_orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[36].childNodes[0].nodeValue; //Tel�fono inv�lido (Solo n�meros - especificar c�digos de pa�s y ciudad);
    }

    filtro=/^[0-9]{8,12}$/;
    document.getElementById('cnct_tel'+id_orden).value=document.getElementById('cnct_tel'+id_orden).value.replace(filtro_trim,'');
    if( !filtro.test(document.getElementById('cnct_tel'+id_orden).value) )
    {
        document.getElementById('cnct_tel_nfo'+id_orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[36].childNodes[0].nodeValue; //Tel�fono inv�lido (Especificar la lada);
    }

    return msg;
}



/***********************************************************************************************
	FUNCION:		cleanCnctData
	DESCRIPCION:	Limpia los campos que muestran la informaci�n de un contacto y los que muestran
					si hubo alg�n error en alg�n campo.
	PARAMETROS:		id_orden:		De existir alguna, es el n�mero de la orden que esta relacionada
									a la edici�n de este contacto. Este campo solo se usa para limpiar
									los datos del contacto en el campo adecuado ya que ayuda a
									diferenciar un campo de otro por su ID.
	SALIDAS:		<< ninguna >>
	***********************************************************************************************/
function cleanCnctData(id_orden)
{
    document.getElementById('cnct_nombre'+id_orden).value='';
    document.getElementById('cnct_email'+id_orden).value='';
    document.getElementById('cnct_direccion'+id_orden).value='';
    document.getElementById('cnct_cp'+id_orden).value='';
    document.getElementById('cnct_ComboPais'+id_orden).value='MEX';
    document.getElementById('cnct_ciudad'+id_orden).value='';
    document.getElementById('cnct_telcc'+id_orden).value='';
    document.getElementById('cnct_tel'+id_orden).value='';
    document.getElementById('cnct_empresa'+id_orden).value='';

    document.getElementById('cnct_msg1'+id_orden).innerHTML='';
    document.getElementById('cnct_nombre_nfo'+id_orden).innerHTML='';
    document.getElementById('cnct_email_nfo'+id_orden).innerHTML='';
    document.getElementById('cnct_direccion_nfo'+id_orden).innerHTML='';
    document.getElementById('cnct_cp_nfo').innerHTML='';
    document.getElementById('cnct_ciudad_nfo'+id_orden).innerHTML='';
    document.getElementById('cnct_tel_nfo'+id_orden).innerHTML='';
    document.getElementById('cnct_empresa_nfo'+id_orden).innerHTML='';
}



function ValidaCnct()
{
    //Informar que se est� validando
    document.getElementById('cnct_nfo').innerHTML = '<img src="http://neubox.net/v3/mx/lod.gif"/><span style="font-size:11px; text-align:left; color:#009303;">&nbsp;'+xmlDoc.getElementsByTagName('seccion')[8].getElementsByTagName('mensaje')[27].childNodes[0].nodeValue+'..</span>';
    //Deshabilitar los botones para evitar problemas
    document.getElementById('Bvalidar').disabled=true;

    var filtro_trim=/^\s+|\s+$/g;
    var filtro=/^[a-z0-9_]{4,16}$/i;
    document.getElementById('contacto').value=document.getElementById('contacto').value.replace(filtro_trim,'');
    document.getElementById('cnct_nfo').innerHTML='';
    if( !filtro.test(document.getElementById('contacto').value) )
    {
        document.getElementById('cnct_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" /><span style="font-size:11px; color:#CC3333;">&nbsp;' + xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[55].childNodes[0].nodeValue + '</span>'; //Contacto Inv�lido
        //Volver a habilitar los botones
        document.getElementById('Bvalidar').disabled=false;
    }
    else
    {
        var myConn = new XHConnPOST();
        if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
        var onReturn = function (oXML){
            document.getElementById('cnct_nfo').innerHTML=oXML.responseText;
            //Volver a habilitar los botones
            document.getElementById('Bvalidar').disabled=false;
        };

        params='peticion=contact_check&contacto=' + document.getElementById('contacto').value;
        myConn.connect("CnctPetitions.php", "POST", params, onReturn);
    }

}


/***********************************************************************************************
	FUNCION:		cleanCnctBD
	DESCRIPCION:	Limpia los campos que muestran la informaci�n de un cliente y los que muestran
					si hubo alg�n error en alg�n campo.
	PARAMETROS:
	SALIDAS:		<< ninguna >>
	***********************************************************************************************/
function cleanBDData()
{
    document.getElementById('nombre').value='';
    document.getElementById('apellidos').value='';
    document.getElementById('direccion').value='';
    document.getElementById('ciudad').value='';
    document.getElementById('pais').value='MEX';
    dameEstados('selectEstados','estados','pais','Distrito Federal','240px','combobox');
    document.getElementById('cp').value='';
    document.getElementById('telefono').value='';
    document.getElementById('empresa').value='';
    document.getElementById('email').value='';
    document.getElementById('clave_act').value='';
    document.getElementById('clave_nva').value='';
    document.getElementById('clave_conf').value='';
    document.getElementById('contacto').value='';

    document.getElementById('nombre_nfo').innerHTML='';
    document.getElementById('apellidos_nfo').innerHTML='';
    document.getElementById('direccion_nfo').innerHTML='';
    document.getElementById('ciudad_nfo').innerHTML='';
    document.getElementById('cp_nfo').innerHTML='';
    document.getElementById('telefono_nfo').innerHTML='';
    document.getElementById('empresa_nfo').innerHTML='';
    document.getElementById('email_nfo').innerHTML='';
    document.getElementById('clave_nva_nfo').innerHTML='';
    document.getElementById('cnct_nfo').innerHTML='';
}



function ValidaDatos_BD(id_cliente)
{
    var filtro_trim=/^\s+|\s+$/g;
    var filtro;
    var msg=0;

    filtro=/^[a-z0-9�������\.\:\,\-\& ]{3,47}$/i;
    document.getElementById('nombre').value=document.getElementById('nombre').value.replace(filtro_trim,'');
    document.getElementById('nombre_nfo').innerHTML='';
    if( !filtro.test(document.getElementById('nombre').value) )
    {
        document.getElementById('nombre_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[30].childNodes[0].nodeValue; //Nombre Inv�lido
    }

    filtro=/^[a-z0-9�������\.\:\,\-\& ]{3,48}$/i;
    document.getElementById('apellidos').value=document.getElementById('apellidos').value.replace(filtro_trim,'');
    document.getElementById('apellidos_nfo').innerHTML='';
    if( !filtro.test(document.getElementById('apellidos').value) )
    {
        document.getElementById('apellidos_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[31].childNodes[0].nodeValue; //Apellidos Inv�lidos
    }

    filtro=/^[a-z0-9�������#_\-\.\:\,\/\& ]{3,130}$/i;
    document.getElementById('direccion').value=document.getElementById('direccion').value.replace(filtro_trim,'');
    document.getElementById('direccion_nfo').innerHTML='';
    if( !filtro.test(document.getElementById('direccion').value) )
    {
        document.getElementById('direccion_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[32].childNodes[0].nodeValue; //Direcci�n Inv�lida
    }

    filtro=/^[a-z0-9�������\.\- ]{3,35}$/i;
    document.getElementById('ciudad').value=document.getElementById('ciudad').value.replace(filtro_trim,'');
    document.getElementById('ciudad_nfo').innerHTML='';
    if( !filtro.test(document.getElementById('ciudad').value) )
    {
        document.getElementById('ciudad_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[33].childNodes[0].nodeValue; //Ciudad inv�lida
    }

    filtro=/^[a-z0-9\- ]{3,10}$/i;
    document.getElementById('cp').value=document.getElementById('cp').value.replace(filtro_trim,'');
    document.getElementById('cp_nfo').innerHTML='';
    if( document.getElementById('cp').value=='' )
        document.getElementById('cp').value='NoZipCode';
    else if( !filtro.test(document.getElementById('cp').value) )
    {
        document.getElementById('cp_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[35].childNodes[0].nodeValue; //C�digo postal inv�lido
    }

    filtro=/^[0-9]{8,12}$/;
    document.getElementById('telefono').value=document.getElementById('telefono').value.replace(filtro_trim,'');
    document.getElementById('telefono_nfo').innerHTML='';
    if( !filtro.test(document.getElementById('telefono').value) )
    {
        document.getElementById('telefono_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[36].childNodes[0].nodeValue; //Tel�fono inv�lido (Especificar la lada);
    }

    filtro=/^[a-z0-9�������#_\.\:\,\(\)\[\]\-\/\$\�\!\?\�\&\<\> ]{3,100}$/i;
    document.getElementById('empresa').value=document.getElementById('empresa').value.replace(filtro_trim,'');
    document.getElementById('empresa_nfo').innerHTML='';
    if( document.getElementById('empresa').value=='' )
        document.getElementById('empresa').value='No company';
    else if( !filtro.test(document.getElementById('empresa').value) )
    {
        document.getElementById('empresa_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[54].childNodes[0].nodeValue; //Empresa inv�lida
    }

    filtro=/^[a-z][a-z0-9_]*(\.[a-z0-9_]+){0,2}@[a-z0-9][a-z0-9\-]*(\.[a-z0-9\-]{4,}){0,2}(\.[a-z]{2,3}){1,2}$/i;
    document.getElementById('email').value=document.getElementById('email').value.replace(filtro_trim,'');
    document.getElementById('email_nfo').innerHTML='';
    if( !filtro.test(document.getElementById('email').value) )
    {
        document.getElementById('email_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[34].childNodes[0].nodeValue; //Email inv�lido
    }

    document.getElementById('clave_nva_nfo').innerHTML='';
    if( document.getElementById('clave_nva').value!='' )
    {
        filtro=/^[a-z0-9#_\-\$]{8,15}$/i;
        if( !filtro.test(document.getElementById('clave_nva').value) )
        {
            document.getElementById('clave_nva_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
            msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[37].childNodes[0].nodeValue; //Contrase�a inv�lida (Capturar al menos 8 car�cteres)
        }
        else if( document.getElementById('clave_nva').value!=document.getElementById('clave_conf').value )
        {
            document.getElementById('clave_nva_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
            msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[38].childNodes[0].nodeValue+'</span>'; //Los campos 'Contrase�a Nueva' y 'Confirmar Contrase�a' deben ser iguales.
        }
    }
    else if( id_cliente=='' || id_cliente==null ) //Si el cliente es nuevo, la clave es requisito
    {
        document.getElementById('clave_nva_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[37].childNodes[0].nodeValue; //Contrase�a inv�lida (Capturar al menos 8 car�cteres)
    }

    filtro=/^[a-z0-9_\-\+\(\)\# ]{1,25}$/i;
    document.getElementById('contacto').value=document.getElementById('contacto').value.replace(filtro_trim,'');
    document.getElementById('cnct_nfo').innerHTML='';
    if( document.getElementById('contacto').value!='' && !filtro.test(document.getElementById('contacto').value) )
    {
        document.getElementById('cnct_nfo').innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" />';
        msg=xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[55].childNodes[0].nodeValue; //Contacto Inv�lido
    }

    return msg;
}



function centrar_div(div_id)
{
    var yScroll = 0;
    var top;
    var left;

    if ( document.getElementById(div_id) )
    {
        var ancho_total,alto_total;

        if (document.all)
        {
            ancho_total = document.body.clientWidth;
            alto_total = document.body.clientHeight;
        }
        else
        {
            ancho_total = window.innerWidth
            alto_total = window.innerHeight
        }

        ancho_div = document.getElementById(div_id).offsetWidth;
        alto_div = document.getElementById(div_id).offsetHeight;

        if ( navegador == 0 ) {
            yScroll = document.documentElement.scrollTop+document.body.scrollTop
        }
        else {
            yScroll = self.pageYOffset;
        }

        top = (alto_total / 2) - (alto_div / 2) + yScroll;
        left = (ancho_total / 2) - (ancho_div / 2);

        top = parseInt(top);
        left = parseInt(left);

        if( navegador == 0 )
        {
            top = top - 100;
        //document.getElementById(div_id).style.filter='';
        }

        document.getElementById(div_id).style.top = top+'px';
        document.getElementById(div_id).style.left = left+'px';

    //carga_estilos_formularios();
    }
}

/***********************************************************************************************************
	Funcion:		mostrar_div
	Descripcion:	Despliega la ventana cuyo id es div_id
	Entradas:		<< ninguna >>
	Salidas:		<< ninguna >>
	***********************************************************************************************************/
function mostrar_div(div_id)
{
    if ( document.getElementById(div_id) )
    {
        cargar_div_opaco();

        document.getElementById(div_id).style.display = 'block';

        if(navegador==0) document.getElementById(div_id).style.filter='alpha(opacity=1)';
        else document.getElementById(div_id).style.opacity=1;

        if( navegador == 0 ) document.getElementById(div_id).style.filter='';

        centrar_div(div_id);
    }
}


/***********************************************************************************************************
	Funcion:		cargar_div_opaco
	Descripcion:	Bloquea la pantalla para que solo se pueda utilizar la ventana de compartir correo
	Entradas:		<< ninguna >>
	Salidas:		<< ninguna >>
	***********************************************************************************************************/
function cargar_div_opaco()
{
    var id = 'div___bk';
    var color = '#222222';
    var opacity = 30;

    if(!document.getElementById('div___bk'))
    {
        var frame = document.createElement("iframe");
        frame.id='frame__'+id;
        frame.style.position='absolute';
        frame.style.top=0;
        frame.style.left=0;
        frame.style.zIndex=11000;

        document.body.appendChild(frame);

        if(navegador==0) frame.style.filter='alpha(opacity=1)';
        else frame.style.opacity=1;

        var layer=document.createElement('div');
        layer.id=id;
        layer.style.backgroundColor=color;
        layer.style.position='absolute';
        layer.style.top=0;
        layer.style.left=0;
        layer.style.zIndex=11001;

        if(navegador==0) layer.style.filter='alpha(opacity='+opacity+')';
        else layer.style.opacity=opacity/100;

        document.body.appendChild(layer);
    }

    document.getElementById(id).style.width = document.body.offsetWidth+'px';
    document.getElementById(id).style.height = document.body.offsetHeight+'px';
    document.getElementById('frame__'+id).style.width = (parseInt(document.getElementById(id).style.width)-5)+ 'px';
    document.getElementById('frame__'+id).style.height = document.getElementById(id).style.height;
    document.getElementById('frame__'+id).style.display = "block";
    document.getElementById(id).style.display = "block";
}



/***********************************************************************************************************
	Funcion:		ocultar_div_opaco
	Descripcion:	Desbloquea la pantalla, y oculta la ventana para compartir noticias
	Entradas:		div_contenido -> nombre del div que actua como ventana emergente
	Salidas:		<< ninguna >>
	***********************************************************************************************************/
function ocultar_div_opaco(div_contenido)
{
    if(document.getElementById(div_contenido))
    {
        document.getElementById(div_contenido).style.display = "none";
    }

    var id = 'div___bk';
    if(document.getElementById(id))
    {
        document.getElementById(id).style.display = "none";
        document.getElementById('frame__'+id).style.display="none";
    }
}

/*************************************************************************************
	FUNCI�N: 	 convocar
	DESCRIPCION: Variaci�n del "fajax" usado anteriormente que env�a las variables por POST (verdaderamente)
	ENTRADAS:
		params  	= Parametros requerido para la accion a realizar, separados por '&' y previmante "escapados" con urlencode
		target	= id del Div o SPAN a donde se regresar� el resultado
	SALIDAS  << ninguna >>
	*************************************************************************************/

// Funci�n que crea la Clase de tipo coneccion ajax (XMLHTTPRequest)
function XHConn()
{
    var xmlhttp, bComplete = false;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (e) {
            try {
                xmlhttp = new XMLHttpRequest();
            }
            catch (e) {
                xmlhttp = false;
            }
        }
    }

if (!xmlhttp) return null;
this.connect = function(sURL, sMethod, sVars, fnDone)
{
    if (!xmlhttp) return false;

    bComplete = false;
    sMethod = sMethod.toUpperCase();

    try {
        if (sMethod == "GET"){
            xmlhttp.open(sMethod, sURL+"?"+sVars, true);
            sVars = "";
        }
        else{
            xmlhttp.open(sMethod, sURL, true);
            xmlhttp.setRequestHeader("Method", "POST "+sURL+" HTTP/1.1");
            xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        }

        xmlhttp.onreadystatechange = function(){
            if (xmlhttp.readyState == 4 && !bComplete){
                bComplete = true;
                fnDone(xmlhttp);
            }
        };
        xmlhttp.send(sVars);
    }
    catch(z) {
        return false;
    }
    return true;
};
return this;
}


function ajax(accion,param1,param2,target)
{
    if(target != '')
        document.getElementById(target).innerHTML = espera;

    var myConn = new XHConn();
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML)
    {
        if(target != '') {
            document.getElementById(target).innerHTML = oXML.responseText;
        }
    };
    myConn.connect("imprime.php?contenido="+accion+"&p1="+param1+"&p2="+param2, "POST", "", peticion);
}


function ajax2(accion,param1,param2,target)
{
    if(target != '')
        document.getElementById(target).innerHTML = esperalogin;

    var myConn = new XHConn();
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML)
    {
        if(target != '') {
            document.getElementById(target).innerHTML = oXML.responseText;
        }
    };
    myConn.connect("imprime.php?contenido="+accion+"&p1="+param1+"&p2="+param2, "POST", "", peticion);
}

/*************************************************************************************
FUNCI�N: 		imprimeContenido
DESCRIPCION:	Imprime los paneles de trabajo dependiendo del contenido solicitado
ENTRADAS:		cont: tipo de contenido que se desplegara
SALIDAS:		<< ninguna >>
*************************************************************************************/
function imprimeContenido(cont){
    MONTO = 0;
    document.location.href="#panel";
    document.getElementById('contenido').innerHTML = espera;
    // Cambiare el t�tulo de la p�gina
    switch(cont){
        case 'home':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/bienvenido.gif" alt="Bienvenido" />';
            document.getElementById('contenido').innerHTML = '<div class="contenedorTablas"><div id="home">&nbsp;</div></div>'
            break;
        case 'info':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/informaciongeneral.gif" alt="Mi informacion" />';
            break;
        case 'cuentas':
            //document.getElementById('titulo').innerHTML = 'Servicios';
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/servicios.gif" alt="Servicios" />';
            break;
        case 'cuenta':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/informacioncuenta.gif" alt="Informaci�n de Cuenta" />';
            break;
        case 'banners':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/banners.gif" alt="Banners y Enlaces" />';
            break;
        case 'comisiones':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/comisionesganadas.gif" alt="Comisiones Ganadas" />';
            break;
        case 'pagos':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/pagosrecibidos.gif" alt="Pagos Recibidos" />';
            break;
        case 'descargas':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/misdescargas.gif" alt="Mis Descargas" />';
            break;
        case 'estafeta':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/misenvios.gif" alt="Mis env�os" />';
            break;
        case 'seo':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/herramientasseo.gif" alt="Herramientas SEO" />';
            break;
        case 'dominios':
            //document.getElementById('titulo').innerHTML = 'Dominios';
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/dominios.gif" alt="Dominios" />';
            break;
        case 'hostings':
            //document.getElementById('titulo').innerHTML = 'Hosting';
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/hosting.gif" alt="Hosting" />';
            break;
        case 'otros':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/otrosproductos.gif" alt="Otros Productos" />';
            break;
        //CODIGO SOBRE FACTURACI�N ELECTR�NICA
        case 'factura':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/facturas.gif" alt="Facturas Electr�nicas" />';
            break;
        case 'facturacion':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/datosdefacturacion.gif" alt="Datos de Facturaci�n" />';
            break;
        case 'log_acciones':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/accionesrealizadas.gif" alt="Acciones Realizadas" />';
            //document.getElementById('titulo').innerHTML = 'Acciones realizadas';
            break;
        case 'depositos':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/depositosrealizados.gif" alt="Dep�sitos Realizados" />';
            break;
        case 'mis_clientes':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/mis_clientes.gif" alt="Mis Clientes" />';
            break;
        case 'cliente':
            document.getElementById('titulo').innerHTML = '<img src="panel_img/titulos/mis_clientes.gif" alt="Mis Clientes" />';
            break;
        case 'backorders_activos':
            document.getElementById('titulo').innerHTML = '<img src="http://neubox.net/includes/titulos.php?titulo=Servicios de Backorders" alt="Servicios Backorders" />';
            break;
        case 'backorders_historial':
            document.getElementById('titulo').innerHTML = '<img src="http://neubox.net/includes/titulos.php?titulo=Historico de Backorders" alt="Historial de Backorders" />';
            break;

        case 'backorders_capturas':
            document.getElementById('titulo').innerHTML = '<img src="http://neubox.net/includes/titulos.php?titulo=Backorders Capturados" alt="Backorders Capturados" />';
            break;




    }
    // Im�gen que denota una b�squeda
    editando_datos = false;
    // Conexi�n as�ncrona
    var myConn = new XHConn();
    // Si no se pudo conectar: avisar
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    // Acciones al regreso de la solcitud
    var peticion = function (oXML) {
        document.getElementById('contenido').innerHTML = oXML.responseText;
    };
    // Llamar  a imprime div.php
    if ( cont != 'home' ) {
        myConn.connect("imprime.php?contenido="+cont, "POST", "", peticion);
    }
    else {
        imprimeHomes('home2');
        imprimeHomes('home1');
    }
}

function imprimeHomes(cont) {
    if ( cont == 'home1' ) {
        document.getElementById(cont).innerHTML = espera;
    }
    var myConn = new XHConn();
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML) {
        document.getElementById(cont).innerHTML = oXML.responseText;
    };
    myConn.connect("imprime.php?contenido="+cont, "POST", "", peticion);
}

/*************************************************************************************
FUNCI�N: 		guarda_datos
DESCRIPCION:	Realiza los cambios en los formularios de datos
ENTRADAS:		cont: el panel de los datos
SALIDAS:		<< ninguna >>
*************************************************************************************/
function guarda_datos(cont) {
    var params = '';

    switch ( cont ) {
        case 'info':
            accion = 'guardar_info';
            /*params += document.forma_info.email.value + '|';
				params += document.forma_info.nombre.value + '|';
				params += document.forma_info.apellidos.value + '|';
				params += document.forma_info.direccion.value + '|';
				params += document.forma_info.pais.value + '|';
				params += document.forma_info.estado.value + '|';
				params += document.forma_info.ciudad.value + '|';
				params += document.forma_info.cp.value + '|';
				params += document.forma_info.telefono.value + '|';
				params += document.forma_info.clave.value + '|';
				params += document.forma_info.empresa.value;*/

            params += document.getElementById('email').value + '|';
            params += document.getElementById('nombre').value + '|';
            params += document.getElementById('apellidos').value + '|';
            params += document.getElementById('direccion').value + '|';
            params += document.getElementById('pais').value + '|';
            params += document.getElementById('estados').value + '|';
            params += document.getElementById('ciudad').value + '|';
            params += document.getElementById('cp').value + '|';
            params += document.getElementById('telefono').value + '|';
            params += document.getElementById('clave').value + '|';
            params += document.getElementById('empresa').value;

            document.getElementById('contenido').innerHTML = espera;
            break;
        default:
            accion = '';
            break;
    }
    params = escape(params);
    vars = 'datos_nuevos=' + params;
    var myConn = new XHConnPOST();
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML) {
        document.getElementById('contenido').innerHTML = oXML.responseText;
    };
    myConn.connect("imprime.php?contenido="+accion, "POST", vars, peticion);
}

/*************************************************************************************
FUNCI�N: 		setPointer
DESCRIPCION:	Cambia el color de la fila indica da en ther row por el color indicado
ENTRADAS:		theRow, thePointerColor
SALIDAS:		<< ninguna >>
*************************************************************************************/
function setPointer(theRow, thePointerColor)
{
    if (thePointerColor == '' || typeof(theRow.style) == 'undefined') {
        return false;
    }
    if (typeof(document.getElementsByTagName) != 'undefined') {
        var theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        var theCells = theRow.cells;
    }
    else {
        return false;
    }

    var rowCellsCnt  = theCells.length;
    for (var c = 0; c < rowCellsCnt; c++) {
        theCells[c].style.backgroundColor = thePointerColor;
    }
    return true;
}


/*************************************************************************************
FUNCI�N: 		validarFormaCuenta
DESCRIPCION:	Verifica que los datos de la cuenta del asociado sean correctos
ENTRADAS:		<< ninguna >>
SALIDAS:		<< ninguna >>
*************************************************************************************/
function validarFormaCuenta(){
    var msg = "";
    var enviar = 1;

    if(document.forma_cuenta.clabe.value == ""){
        msg += "La CLABE de la cuenta es requerida\n";
        enviar = 0;
    }
    if(document.forma_cuenta.banco.value == ""){
        msg += "El nombre del banco es requerido\n";
        enviar = 0;
    }
    if(document.forma_cuenta.titular.value == ""){
        msg += "El nombre del titular de la cuenta es requerido\n";
        enviar = 0;
    }
    if(enviar == 1) {
        document.forma_cuenta.submit();
    }
    else {
        document.getElementById('notificacion').innerHTML = msg;
    }
}

/*************************************************************************************
FUNCI�N: 		selecciona
DESCRIPCION:	copia en el portapapeles el codigo para el banner o link
ENTRADAS:		textArea: el id del textarea del que se desea copiar el contenido
SALIDAS:		<< ninguna >>
*************************************************************************************/
function selecciona(textArea){
    // Seleccionar el texto
    textArea.select()
    // Si existe el portapapeles copiar en el portapapeles
    if (window.clipboardData){
        window.clipboardData.setData("Text", textArea.value);
    }
}

/*************************************************************************************
FUNCI�N:		getTool
DESCRIPCION:	imprime la herramienta seo solicitada del archivo seo_index.php
ENTRADAS:		tool: nombre de la herramienta para seleccionar el contenido adecuado
SALIDAS:		<< ninguna >>
*************************************************************************************/
function getTool(tool){
    // Im�gen que denota una b�squeda
    document.getElementById('seotools').innerHTML = espera;
    // Conexi�n as�ncrona
    var myConn = new XHConn();
    // Si no se pudo conectar: avisar
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    // Acciones al regreso de la solcitud
    var peticion = function (oXML) {
        document.getElementById('seotools').innerHTML = oXML.responseText;
    };
    // Llamar  a post.php
    myConn.connect("seo_tools.php?tool="+tool, "POST", "", peticion);
}

// Obtener en nuestra propia pagina esta herramienta
function imprimeResultado(dominio,nofw,ex){
    var url = "http://www.iwebtool.com/visual/frme.html?domain="+dominio+"&nofw=1&ex=1";
    // Im�gen que denota una b�squeda
    //document.getElementById('resultVPR').innerHTML = espera;
    document.getElementById('resultVPR').src = url;
    document.getElementById('resultVPR').width = 500;
    document.getElementById('resultVPR').height = 300;

/*
		// Conexi�n as�ncrona
		var myConn = new XHConn();
		// Si no se pudo conectar: avisar
		if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
		// Acciones al regreso de la solcitud
		var peticion = function (oXML) {
			document.getElementById('resultVPR').innerHTML = oXML.responseText;
			document.getElementById('resultVPR').src = url;
		};
		// Llamar  a post.php
		myConn.connect(url,"POST", "", peticion);
*/
}

/*************************************************************************************
FUNCI�N:		ponMarco / quitaMarco
DESCRIPCION:	Ponen o quitan el marco que funciona como cursor para las herramientas SEO
ENTRADAS:		target: nombre del div al que se le aplicara el marco
SALIDAS:		<< ninguna >>
*************************************************************************************/
function ponMarco(target){
    document.getElementById(target).style.border = "1px solid #000000";
    document.getElementById(target).style.background = "#EFEFEF";
}

function quitaMarco(target){
    document.getElementById(target).style.border = "";
    document.getElementById(target).style.background = "";
}


/*function pulsar() {
	var pais = window.document.forma_info.pais.options[window.document.forma_info.pais.selectedIndex].value;
	var estado = window.document.forma_info.estado.options[window.document.forma_info.estado.selectedIndex].value;

	document.getElementById('estados').innerHTML = '<img src="http://neubox.net/lod.gif"/>';
	// Conexi�n as�ncrona
	var myConn = new XHConn();
	// Si no se pudo conectar: avisar
	if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
		// Acciones al regreso de la solcitud
		var peticion = function (oXML) {
			document.getElementById('estados').innerHTML = oXML.responseText;
	};
	// Llamar  a post.php
	myConn.connect("carga_estados.php?pais="+pais+"&estado="+encodeURIComponent(estado), "POST", "", peticion);
}// fin funcion pulsar*/

/*************************************************************************************
FUNCI�N:		cambiaPaginaSelect
DESCRIPCION:	Supuestamente cambia la pagina de las ordenes
ENTRADAS:		contenido:  tipo de contenido a desplegar
				destino:  indica la pagina a la que se movera (opcional, solo con el clic de las flechas)
				buscar: indica la consulta que se ejecut�. esta solo existe si destino tambien existe
SALIDAS:		<< ninguna >>
*************************************************************************************/
function cambiaPaginaSelect(contenido,destino,buscar)
{
    document.location.href="#panel";
    // si no se recibe el destino, obtenerlo del combobox
    if ( !destino ) {
        // la id es campiapag porque para el programador que le dio seguimiento
        // al proyecto resulto mas facil dejarlo asi debido a los multiples nombres
        // que se deben cambiar
        var destino = document.getElementById('campiaPag').value;
        buscar = '';
    }
    else {
        // si en el destino se especifica cb, significa que se debe obtener el destino
        if ( destino == 'cb' ) {
            destino = document.getElementById('campiaPag').value;
        }
    }
    ajax(contenido, destino, buscar, 'contenido');
}

/*************************************************************************************
FUNCI�N:		cargaDatos
DESCRIPCION:	Obtiene y carga los datos de facturacion del cliente
ENTRADAS:		<< ninguna >>
SALIDAS:		<< ninguna >>
*************************************************************************************/
function cargaDatos(){
    //OBTIENE EL ID GUARDADO EN EL CAMPO RFC, QUE SE UTILIZARA COMO REFERENCIA PARA
    //LOS CAMPOS "HIDDEN" QUE CONTIENEN LOS DATOS DE CADA RFC DE LA LISTA
    var id = document.getElementById('rfc').value;
    document.getElementById('nombre').value = document.getElementById('nom_'+id).value;
    document.getElementById('calle').value = document.getElementById('cal_'+id).value;
    document.getElementById('colonia').value = document.getElementById('col_'+id).value;
    document.getElementById('numext').value = document.getElementById('nue_'+id).value;
    document.getElementById('numint').value = document.getElementById('nui_'+id).value;
    document.getElementById('cp').value = document.getElementById('cop_'+id).value;
    document.getElementById('estado').value = document.getElementById('edo_'+id).value;
    document.getElementById('municipio').value = document.getElementById('mun_'+id).value;
    document.getElementById('localidad').value = document.getElementById('loc_'+id).value;
}

/*************************************************************************************
FUNCI�N:		guardarCambios
DESCRIPCION:	Valida los datos ingresadosy llama al ajax para guardar los datos
ENTRADAS:		<< ninguna >>
SALIDAS:		<< ninguna >>
*************************************************************************************/
function guardarCambios(){
    //VALIDA EL CAMPO NOMBRE
    if(document.getElementById('nombre').value ==""){
        alert(xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[43].childNodes[0].nodeValue); //Por favor ingresa el nombre que aparecer� en la factura.
        document.getElementById('nombre').focus();
    }
    else{
        //VALIDA EL CAMPO CALLE
        if(document.getElementById('calle').value ==""){
            alert(xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[44].childNodes[0].nodeValue); //Por favor ingresa la calle que aparecer� en la factura.
            document.getElementById('calle').focus();
        }
        else{
            //VALIDA EL CAMPO COLONIA
            if(document.getElementById('colonia').value ==""){
                alert(xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[45].childNodes[0].nodeValue); //Por favor ingresa la colonia que aparecer� en la factura.
                document.getElementById('colonia').focus();
            }
            else{
                //VALIDA EL CAMPO N�MERO EXTERIOR
                if(document.getElementById('numext').value ==""){
                    alert(xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[46].childNodes[0].nodeValue); //Por favor ingresa el n�mero exterior que aparecer� en la factura.
                    document.getElementById('numext').focus();
                }
                else{
                    //VALIDA EL CAMPO C�DIGO POSTAL
                    if(document.getElementById('cp').value ==""){
                        alert(xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[47].childNodes[0].nodeValue); //Por favor ingresa el c�digo postal que aparecer� en la factura.
                        document.getElementById('cp').focus();
                    }
                    else{
                        //VALIDA EL CAMPO MUNICIPIO
                        if(document.getElementById('municipio').value ==""){
                            alert(xmlDoc.getElementsByTagName('seccion')[6].getElementsByTagName('error')[48].childNodes[0].nodeValue); //Por favor ingresa el municipio que aparecer� en la factura.
                            document.getElementById('municipio').focus();
                        }
                        else{
                            //ID DEL CAMPO QUE SE ACTULIZARA EN LA BASE DE DATOS
                            var id = document.getElementById('rfc').value;
                            //DATOS A ACTUALIZAR
                            var nombre = document.getElementById('nombre').value;
                            var calle = document.getElementById('calle').value;
                            var colonia = document.getElementById('colonia').value;
                            var numext = document.getElementById('numext').value;
                            var numint = document.getElementById('numint').value;
                            var cp = document.getElementById('cp').value;
                            var estado = document.getElementById('cmbEstados').value;
                            var municipio = document.getElementById('municipio').value;
                            var localidad = document.getElementById('localidad').value;
                            //******************************************************
                            var mensaje = html_entity_decode(xmlDoc.getElementsByTagName('seccion')[9].getElementsByTagName('notificacion')[25].childNodes[0].nodeValue) + "\n"; //La direcci�n que aparecer� en la factura ser� la siguiente:
                            mensaje += calle+" N�mero: "+ numext;
                            if(numint!=""){
                                mensaje += " Int. "+ numint;
                            }
                            mensaje += " "+colonia+", "+ estado+" "+municipio+" "+localidad+" C.P.: "+cp ;
                            mensaje += "\n"+html_entity_decode(xmlDoc.getElementsByTagName('seccion')[8].getElementsByTagName('mensaje')[20].childNodes[0].nodeValue); //�Deseas guardar los cambios?
                            if(confirm(mensaje)){
                                accion = 'guardarCambios';
                                document.getElementById('contenido').innerHTML = espera;
                                var myConn = new XHConn();
                                if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
                                var peticion = function (oXML) {
                                    document.getElementById('contenido').innerHTML = oXML.responseText;
                                };
                                myConn.connect("imprime.php?contenido="+accion+"&p1="+nombre+"&p2="+calle+"&p3="+colonia+"&p4="+numext+"&p5="+numint+"&p6="+cp+"&p7="+estado+"&p8="+municipio+"&p9="+localidad+"&p10="+id, "POST", "", peticion);
                            }
                        }
                    }
                }
            }
        }
    }
}

/*************************************************************************************
FUNCI�N:		editarDNS
DESCRIPCION:	llama a la funcion para poner en edicion los dns, para un solo dominio
ENTRADAS:		orden: numero de orden correspondiente
SALIDAS:		<< ninguna >>
*************************************************************************************/
function editarDNS(orden)
{
    accion = 'dns_iguales';
    realiza_accion_masiva(orden,accion);
}

/*************************************************************************************
FUNCI�N:		modificaDNS
DESCRIPCION:	llama al ajax que se encargara de guardar los nuevos dns para distintos DNS
ENTRADAS:		ordenes: cadena con los numeros de orden separados por coma
				datos:  cadena con los datos necesarios separados por * (dom:ext:orden:ec)
SALIDAS:		<< ninguna >>
*************************************************************************************/
function modificaDNS(ordenes,id)
{
    var arreglo_datos = new Array();
    var param_dns='';
    var msg = '';
    var ereg_dns = /^([a-z\d][a-z\d\-]*(\.[a-z\d][a-z\d\-]*){1,2}(\.[a-z]{2,4}){1,2})?$/i;
    var ereg_ip = /^([\d]{1,3}\.){3}[\d]{1,3}$/;
    var filtro_trim=/^\s+|\s+$/g;
    var error=false;

    arreglo_ordenes = ordenes.split("*");

    for  ( i=0; i<arreglo_ordenes.length; i++ )
    {
        arreglo_datos = arreglo_ordenes[i].split(":");
        dominio = arreglo_datos[0]+'.'+arreglo_datos[1];
        ereg_dominio=new RegExp(dominio.replace(/\./g,"\\.")+'$','i');

        //Si no se pasa un id es porque hay un conjunto de campos de contacto para cada orden
        if(id!='' && id!=null) orden = id;
        else orden = arreglo_datos[2];

        for(x=1; x<5; x++)
        {
            ip=document.getElementById('ip'+x+'_'+orden).value.replace(filtro_trim,'');
            dns=document.getElementById('dns'+x+'_'+orden).value.replace(filtro_trim,'');

            if( ereg_dns.test(dns) || dns=='NC' ) //comprobar sintaxis del DNS
            {
                document.getElementById('dns'+x+'_'+'nfo'+'_'+orden).innerHTML='';

                if ( ereg_dominio.test(dns) ) //ver si es un DNS propio del dominio
                {
                    if( !ereg_ip.test(ip) ) //checar que la ip es correcta
                    {
                        document.getElementById('ip'+x+'_'+'nfo'+'_'+orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" align="left" /> <span style="font-size:11px; text-align:left; color:#CC3333;"> &nbsp; IP inv&aacute;lida </span>';
                        error=true;
                    }
                    else document.getElementById('ip'+x+'_'+'nfo'+'_'+orden).innerHTML='';
                }
                else ip='';

                if( error==false )
                    param_dns += dns+','+ip+'|';
            }
            else
            {
                document.getElementById('dns'+x+'_'+'nfo'+'_'+orden).innerHTML='<img src="panel_img/panel_form_error.gif" alt="error" align="left" /> <span style="font-size:11px; text-align:left; color:#CC3333;"> &nbsp; DNS inv&aacute;lido </span>';
                error=true;
            }
        }

        //Se cambia el �ltimo | por un * para indicar que siguen los datos de otro dominio y no de otro DNS.
        param_dns=param_dns.substr( 0, param_dns.length-1 )+'*';
    }

    if(error==false)
    {
        param_dns='info_dns='+param_dns.substr( 0, param_dns.length-1 ); //Le quitamos el �ltimo *
        document.getElementById('contenido').innerHTML = espera;

        var accion = 'modificaDNS';
        var myConn = new XHConnPOST();
        if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
        var peticion = function (oXML) {
            document.getElementById('contenido').innerHTML = oXML.responseText;
        };
        myConn.connect("imprime.php?contenido=" + accion + "&datos_ordenes=" + ordenes, "POST",param_dns, peticion);
    }
    else document.getElementById('msg0').innerHTML="<strong>Se encontraron errores. Favor de corregirlos.</strong>";
}


/*************************************************************************************
FUNCI�N:		dns_son_validos
DESCRIPCION:	verifica la correcta sintaxis de los dns e ip introducidos
ENTRADAS:		cad_dominios:
SALIDAS:		true: si las ip y dns son correctos
				false: si hay algun error en dns o ip
*************************************************************************************/
function dns_son_validos(cad_dominios)
{
    // Obtener el valor del input
    var input_host, host;
    var input_ip, ip, hidden_ip;

    var hay_uno = false; //Bandera para encontrar al menos un DNS'
    var msg = '';   // mensaje de error
    var expreg_host = /^[a-z0-9\-]+\.[a-z0-9\-]+\.[a-z]+[\.[a-z]+]?$/;  // exp. reg. dns
    var expreg_ip = /^[\d-]{1,3}\.[\d-]{1,3}\.[\d-]{1,3}\.[\d-]{1,3}$/;  // exp.reg. ip

    var notificacion = "";
    var dominio = cad_dominios;

    // limpia los posibles indicadores de error
    for ( xix=1; xix<=4; xix++ ) {
        document.getElementById('asip'+xix).innerHTML = "";
        document.getElementById('asdns'+xix).innerHTML = "";
    }

    // Primero ver que todos los DNS's est�n bien escritos
    var sinip = "";	// mensaje que indica la necesidad de una ip
    for(var i=1;i<=4 ;i++) {
        // Validar DNS's
        input_host = document.forma_dns.elements['dns'+i].value;
        // Validar que tenga el formato correcto
        if(input_host != '') {
            hay_uno = true;
            if(!expreg_host.test(input_host)){
                // Alertar del error
                msg += 'Por favor verifica el formato del DNS'+i+'<br>\n';
            }
        }
        // Validar IP's
        input_ip = document.forma_dns.elements['ip'+i].value;
        if(input_ip != '') {
            // si los dns no estan contenidos en el dominio, no verificar nada
            if(input_host.indexOf(dominio) == -1){
                msg += 'La IP s�lo es necesaria para DNS\'s propios (si el dominio est� contenido en los DNS\'s)<br>\n';
                document.getElementById('asip'+i).innerHTML = "*";
            }
            // si los dns se encuentran en el dominio, validar la ip
            else{
                if ( !validar_ip(input_ip) ) {
                    msg += 'La IP del DNS '+i+' no esta escrita correctamente o se encuentra en un rango no permitido<br>\n';
                    document.getElementById('asip'+i).innerHTML = "*";
                }
            }
        }
        else{
            if(input_host.indexOf(dominio) != -1){
                sinip = 'No puedes dejar estos DNS\'s sin una IP<br>\n';
                document.getElementById('asdns'+i).innerHTML = "*";
            }
        }
    }
    msg += sinip;
    if(!hay_uno) msg += 'Debes indicar al menos un DNS'+i+'<br>\n';


    // Si hubo errores
    if(msg != ''){
        // Alertar del error y regresar
        //alert(msg);
        document.location.href = "#panel";
        document.getElementById('notificacion').innerHTML = msg;
        return false;
    }

    return true;
}
// gemela de la anterior, solo que esta aplica para distintos dns
function dns_son_validos_distintos(cad_dominios,orden)
{
    // Obtener el valor del input
    var input_host, host;
    var input_ip, ip, hidden_ip;

    var hay_uno = false; //Bandera para encontrar al menos un DNS'
    var msg = '';   // mensaje de error
    var expreg_host = /^[\D\d-]+\.[\D\d-]+\.[\D]+[\.[\D]+]?$/;  // exp. reg. dns
    var expreg_ip = /^[\d-]{1,3}\.[\d-]{1,3}\.[\d-]{1,3}\.[\d-]{1,3}$/;  // exp.reg. ip

    var notificacion = "Aqui va una descripci&oacute;n de lo que realiza este panel";
    var dominio = cad_dominios;

    // limpia los posibles indicadores de error
    for ( xix=1; xix<=4; xix++ ) {
        document.getElementById('asip'+xix+orden).innerHTML = "";
        document.getElementById('asdns'+xix+orden).innerHTML = "";
    }

    // Primero ver que todos los DNS's est�n bien escritos
    var sinip = "";	// mensaje que indica la necesidad de una ip
    for(var i=1;i<=4 ;i++) {
        // Validar DNS's
        input_host = document.forma_dns.elements['dns'+i+orden].value;
        // Validar que tenga el formato correcto
        if(input_host != '') {
            hay_uno = true;
            if(!expreg_host.test(input_host)){
                // Alertar del error
                msg += 'Por favor verifica el formato del DNS'+i+'<br>\n';
            }
        }
        // Validar IP's
        input_ip = document.forma_dns.elements['ip'+i+orden].value;
        if(input_ip != '') {
            // si los dns no estan contenidos en el dominio, no verificar nada
            if(input_host.indexOf(dominio) == -1){
                msg += 'La IP s�lo es necesaria para DNS\'s propios (si el dominio est� contenido en los DNS\'s)<br>\n';
                document.getElementById('asip'+i+orden).innerHTML = "*";
            }
            // si los dns se encuentran en el dominio, validar la ip
            else{
                if ( !validar_ip(input_ip) ) {
                    msg += 'La IP del DNS '+i+' no esta escrita correctamente o se encuentra en un rango no permitido<br>\n';
                    document.getElementById('asip'+i+orden).innerHTML = "*";
                }
            }
        }
        else{
            if(input_host.indexOf(dominio) != -1){
                sinip = 'No puedes dejar estos DNS\'s sin una IP<br>\n';
                document.getElementById('asdns'+i+orden).innerHTML = "*";
            }
        }
    }
    msg += sinip;
    if(!hay_uno) msg += 'Debes indicar al menos un DNS'+i+'<br>\n';


    // Si hubo errores
    if(msg != ''){
        // Alertar del error y regresar
        //alert(msg);
        document.location.href = "#panel";
        document.getElementById('notificacion').innerHTML = msg;
        return false;
    }

    return true;
}
/*************************************************************************************
FUNCI�N:		validar_ip
DESCRIPCION:	verifica que una ip se encuentre en el rango permitido
ENTRADAS:		ip: la ip a validar
SALIDAS:		true si la ip es valida; false si no lo es
*************************************************************************************/
function validar_ip(ip)
{
    var ip_es_valida = false;
    var arreglo_ip = new Array(); // arreglo con los valores de la ip
    arreglo_ip = ip.split(".");
    // si son 4 grupos de numeros, validar la version 4
    if ( arreglo_ip.length == 4 ) {
        ip_es_valida = validar_ip_v4(ip);
    }
    // sino, intentar verificar si es version 69
    else {
        arreglo_ip = ip.split(":");
        if ( arreglo_ip.length == 8 ) {
            ip_es_valida = validar_ip_v6(arreglo_ip);
        }
    }
    return ( ip_es_valida );
}
function validar_ip_v4(ip)
{
    var correcta = true;
    dentro = 0;
    var arreglo_rangos = new Array();
    // define los rangos no permitidos
    arreglo_rangos[0] = new Array('0.0.0.0','0.255.255.255');
    arreglo_rangos[1] = new Array('10.0.0.0','10.255.255.255');
    arreglo_rangos[2] = new Array('127.0.0.0','127.255.255.255');
    arreglo_rangos[3] = new Array('169.254.0.0','169.254.255.255');
    arreglo_rangos[4] = new Array('172.16.0.0','172.31.255.255');
    arreglo_rangos[5] = new Array('192.0.2.0','192.0.2.255');
    arreglo_rangos[6] = new Array('192.88.99.0','192.88.99.255');
    arreglo_rangos[7] = new Array('192.168.0.0','192.168.255.255');
    arreglo_rangos[8] = new Array('198.18.0.0','198.19.255.255');
    arreglo_rangos[9] = new Array('224.0.0.0','255.255.255.255');

    for ( jk=0; jk<arreglo_rangos.length && correcta == true; jk++ ) {
        en_rango = ipEnRango(ip,arreglo_rangos[jk][0],arreglo_rangos[jk][1]);
        if ( en_rango ) {
            correcta = false;
        }
    }
    return (correcta);
}
function validar_ip_v6(arreglo_ip)
{
    return false;
}
/*************************************************************************************
FUNCI�N:		ipEnRango
DESCRIPCION:	veririca si una direccion ip se encuentra dentro de un rango
ENTRADAS:		ip: la direccion ip a verificar
				ip_inicio, ip_final: ip delimitadoras del rango
SALIDAS:		true si la ip esta en rango; false si la ip esta fuera del rango
*************************************************************************************/
function ipEnRango(ip,ip_inicio,ip_final)
{
    esta = false;
    formato_ip = new RegExp("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$");

    // si las ip a comparar tienen el formato adecuado
    if ( formato_ip.test(ip) && formato_ip.test(ip_inicio) && formato_ip.test(ip_final) ) {
        // crea los arreglos de ips para validar los rangos
        var arreglo_ip = new Array();	// ip a validar
        var arreglo_inicial = new Array(); // ip inicial
        var arreglo_final = new Array();  // ip final
        arreglo_ip = ip.split(".");
        arreglo_inicial = ip_inicio.split(".");
        arreglo_final = ip_final.split(".");
        // obtiene los binarios de la ip y los concatena
        verificar = DecABin(arreglo_ip[0],1,8)+DecABin(arreglo_ip[1],1,8)+DecABin(arreglo_ip[2],1,8)+DecABin(arreglo_ip[3],1,8);
        inicial = DecABin(arreglo_inicial[0],1,8)+DecABin(arreglo_inicial[1],1,8)+DecABin(arreglo_inicial[2],1,8)+DecABin(arreglo_inicial[3],1,8);
        final = DecABin(arreglo_final[0],1,8)+DecABin(arreglo_final[1],1,8)+DecABin(arreglo_final[2],1,8)+DecABin(arreglo_final[3],1,8);
        // convierte las cadenas de binarios a decimal para validar rangos
        verificar = BinADec ( verificar );
        inicial = BinADec ( inicial );
        final = BinADec ( final );
        // la ip inicial debe ser menor a la final
        if ( inicial < final ) {
            // si el rango es valido, verificar que la ip este dentro de este
            if ( verificar >= inicial && verificar <= final ) {
                esta = true;
            }
        }
    }
    return (esta);
}


function ip_dns_check(ereg_dominio, num, id)
{
    nom_dns=document.getElementById('dns'+num+'_'/*+id*/).value;

    filtro=new RegExp(ereg_dominio,'i');
    if( ereg_dominio!='\\s*$' && filtro.test(nom_dns) )
        document.getElementById('tr_ip'+num+'_'/*+id*/).style.display='';
    else
        document.getElementById('tr_ip'+num+'_'/*+id*/).style.display='none';
}


/*************************************************************************************
FUNCI�N:		DecABin
DESCRIPCION:	convierte un numero de Decima a Bianrio
ENTRADAS:		numero: el numero a convertir
				formato: 0 o vacio si se desea el numero; 1 si se desea convertido en cadena
				posiciones: numero de bits a regresar
SALIDAS:		suma: el numero binario covertido, false en caso de no poder convertir
*************************************************************************************/
function DecABin(numero,formato,posiciones)
{
    var numero,suma,digito,exponente;
    numero = parseInt ( numero );
    // verificar que el numero sea positivo
    if ( numero >= 0 ) {
        suma = 0;	// llevara el numero que se va convirtiendo
        exponente = 1;	// cantidad por la que se multiplicara para encontrar el numero
        do
        {
            digito = numero % 2;
            numero = Math.floor(numero / 2);
            suma = suma + digito * exponente;
            exponente = exponente * 10;
        } while(numero > 0);
        // si se solicit�, se convertira en cadena el resultado
        if ( formato == 1 ) {
            suma = suma + "";

            if ( posiciones != 0 ) {
                posiciones_actuales = suma.length;
                if ( posiciones > posiciones_actuales ) {
                    agregar = posiciones - posiciones_actuales;
                    ceros = '';
                    for ( c=0; c<agregar; c++ ) {
                        ceros += '0';
                    }
                    suma = ceros + suma;
                }
            }
        }
        return ( suma );
    }
    return (false);
}

/*************************************************************************************
FUNCI�N:		BinADec
DESCRIPCION:	convierte un numero de Binario a Decimal
ENTRADAS:		numero: el numero a convertir ( en formato cadena )
				formato: 0 o vacio si se desea el numero; 1 si se desea convertido en cadena
SALIDAS:		suma: el numero binario covertido, false en caso de no poder convertir
*************************************************************************************/
function BinADec(numero,formato)
{
    var ereg = /^[0-1]+$/;	// expresio para validar que el numero sea binario
    var decimal = 0;	// llevara el numero decimal equivalente
    var exponente = 1;	// el exponente al que se elevara el 2
    // verificar que se trate de un numero binario
    if ( ereg.test(numero) ) {
        // recorre el numero en orden inverso para ir convirtiendo
        for ( i=numero.length-1; i>=0; i-- ) {
            actual = numero.substr(i,1);
            actual = parseInt(actual);
            decimal += actual * exponente;
            exponente *= 2 ;
        }
        // si se solicit�, convertir en cadena
        if ( formato == 1 ) {
            decimal = decimal + "";
        }
        return ( decimal );
    }
    return (false);
}

/*************************************************************************************
FUNCI�N:		dameStringDNS
DESCRIPCION:	obtiene una cadena parseable con los datos de los dns e ip
ENTRADAS:		<< ninguna >>
SALIDAS:		param: cadena con los datos de dns e ip introducidos. cadena vacia si hay errores
*************************************************************************************/
function dameStringDNS () {
    var ret = "";	// cadena que se forma dinamicamente con los parametros que se var obteniendo
    var input_host, hidden_host;	// campos de formulario de host
    var input_ip, hidden_ip;		// campos de formulario de ip
    msg = "";		// mensaje de error
    var param = "cambios_dns="; // cadena de parametros que sera devuelta
    var excluidos = "-";	// cadena con los host e ip que no se verificaran si cambiaron
    existentes = "-";	// cadena con los dns ya existentes

    // ------------------ Para cada juego de IP - DNS, enviar solo los que han cambiado --------------------
    // se verifican los que estaban en blanco tanto el nuevo como el original. de ser asi no es necesaria la revision en estos.
    for( i=1; i<=4 ;i++ ) {
        // verificar los que son vacios en ambos casos y excluirlos de la comparacion
        input_host = document.getElementById('dns'+i).value;
        hidden_host = document.getElementById('dns'+i+'_original').value;
        input_ip = document.getElementById('ip'+i).value;
        hidden_ip = document.getElementById('ip'+i+'_original').value;
        if ( input_host == "" && hidden_host == "" && input_ip == "" && hidden_ip == "" ) {
            excluidos += i;
        }
    }
    //  se verificaran los cambios en los campos que no esten en blanco ni original ni nuevo
    for ( i=1; i<=4; i++ ) {
        if ( excluidos.indexOf(i) < 1 ) {
            input_host = document.getElementById('dns'+i).value;
            input_ip = document.getElementById('ip'+i).value;
            for ( j=1; j<=4; j++ ) {
                if ( excluidos.indexOf(j) < 1 ) {
                    hidden_host = document.getElementById('dns'+j+'_original').value;
                    hidden_ip = document.getElementById('ip'+j+'_original').value;
                    if ( input_host == hidden_host ) {
                        existentes += i;
                    }
                }
            }
        }
    }
    // se crea el mensaje de error y la cadena con los parametros que se enviaran
    for ( i=1; i<=4; i++ ) {
        if ( excluidos.indexOf(i) < 1 ) {
            if ( existentes.indexOf(i) > 0 ) {
                msg += ' - El valor del DNS ' + i + ' ya se encuentra registrado, y no se modificar�\n';
            }
            else {
                input_host = document.getElementById('dns'+i).value;
                hidden_host = document.getElementById('dns'+i+'_original').value;
                input_ip = document.getElementById('ip'+i).value;
                hidden_ip = document.getElementById('ip'+i+'_original').value;
                ret += hidden_host + ',' + hidden_ip +':'; // %3A= (dos puntos)
                ret += input_host + ',' + input_ip + '*'; // %2C = (coma)
            }
        }
    }

    if ( msg != '' ) {
        alert ( msg );
    }
    if ( ret.length > 1 ) {
        ret = ret.substring( 0, ret.length-1 );
        ret = escape(ret);
    }
    param += ret;
    return param;
}


/*************************************************************************************
FUNCI�N:		dameStringDNS
DESCRIPCION:	obtiene una cadena parseable con los datos de los dns e ip para dns distintos
ENTRADAS:		arreglo_ordenes: arreglo con los numeros de orden correspondietes
SALIDAS:		variables: cadena con los datos de dns e ip introducidos. cadena vacia si hay errores
*************************************************************************************/
function dameStringDNSdistintos(arreglo_ordenes)
{
    var ret = '';
    var elem;
    var variables = 'cambios_dns=';
    for ( i=0; i<arreglo_ordenes.length; i++ )
    {
        elem = '';
        // Para cada juego de IP - DNS:
        for( j=1; j<=4 ;j++ ) {
            // S�lo enviar las que hallan cambiado
            orden = arreglo_ordenes[i];
            input_host = document.getElementById('dns'+j+orden).value;
            hidden_host = document.getElementById('dns'+j+'_original'+orden).value;
            input_ip = document.getElementById('ip'+j+orden).value;
            hidden_ip = document.getElementById('ip'+j+'_original'+orden).value;
            if( (input_host != hidden_host) || (input_ip != hidden_ip) ) {
                //  %3A= (dos puntos) -  %2C = (coma)
                elem += hidden_host + ',' + hidden_ip + ':';
                elem += input_host + ',' + input_ip + "*";
            }
        }
        elem = elem.substring( 0, elem.length-1 ); // quitar el ultimo *
        ret += elem + "#";
    }
    ret = ret.substring( 0, ret.length-1 );  // quitar el ultimo #
    ret = escape(ret);
    variables += ret;
    return (variables);
}

/*************************************************************************************
FUNCI�N:		editarContactos
DESCRIPCION:	llama a la funcion para poner el formulario de edicion de contactos de dominio
ENTRADAS:		orden: numero de orden correspondiente
SALIDAS:		<< ninguna >>
*************************************************************************************/
function editarContactos(orden) {
    // ORDEN DE ELEMENTOS EN info = dominio, extension, orden, ec
    accion = 'contactos_iguales';
    realiza_accion_masiva(orden,accion);
}



/***********************************************************************************************
FUNCION:		modificaContactos
DESCRIPCION:	Realiza la modificacion de los contactos de dominios
PARAMETROS:		cadena_info: informacion de cada dominio a cambiarle los contactos ( dominio:extension:orden:ec:subcliente) separada por *
				id: Si la asignaci�n de contactos va a ser la misma para todos los dominios, aqui se especifica el id que tienen los campos
					que contienen los contactos.
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function modificaContactos(cadena_info,id)
{
    var arreglo_info = new Array();
    var arreglo_datos = new Array();
    var param_contactos = "info_contactos=";
    var msg = '';
    arreglo_info = cadena_info.split("*");
    //alert(cadena_info);

    for  ( i=0; i<arreglo_info.length; i++ )
    {
        arreglo_datos = arreglo_info[i].split(":");
        //Si no se pasa un id es porque hay un conjunto de campos de contacto para cada orden
        if(id!='' && id!=null) orden = id;
        else orden = arreglo_datos[2];

        registrant = document.getElementById('registrante' + orden).value;
        admin = document.getElementById('administrativo' + orden).value;
        tech = document.getElementById('tecnico' + orden).value;
        billing = document.getElementById('pago' + orden).value;

        registrant0 = document.getElementById('o_registrante' + orden).value;
        admin0 = document.getElementById('o_administrativo' + orden).value;
        tech0 = document.getElementById('o_tecnico' + orden).value;
        billing0 = document.getElementById('o_pago' + orden).value;

        // Crear la cadena de par�metros POST
        contactos='';
        if( registrant!=registrant0 && registrant!='' && registrant.substr(0,1)!='\'' ) contactos += registrant;
        contactos += '|';

        if( admin!=admin0 && admin!='' && admin.substr(0,1)!='\'' ) contactos += admin;
        contactos += '|';

        if( tech!=tech0 && tech!='' && tech.substr(0,1)!='\'' ) contactos += tech;
        contactos += '|';

        if( billing!=billing0 && billing!='' && billing.substr(0,1)!='\'' ) contactos += billing;
        contactos += '*';

        //A�adir los contactos a los par�metros POST
        param_contactos += escape(contactos);
    }

    //Quitar el �ltimo '*' porque no es necesario
    param_contactos=param_contactos.substr( 0, param_contactos.length-1 );

    document.location.href = "#panel";
    var accion = 'modificaContactos';
    var myConn = new XHConnPOST();
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    document.getElementById('contenido').innerHTML = espera;
    var peticion = function (oXML) {
        document.getElementById('contenido').innerHTML = oXML.responseText;
    };

    myConn.connect("imprime.php?contenido="+accion+"&info_ordenes="+cadena_info, "POST", param_contactos, peticion);
}



/***********************************************************************************************
FUNCION:		editarDatosDeContacto
DESCRIPCION:	Invoca al formulario para edicion de datos de cliente
PARAMETROS:		tipo_contacto: el tipo de contacto que se editara
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function editarDatosDeContacto(tipo_contacto,idOrden,idCliente,idSubCliente)
{
    //document.getElementById('panel_edita_contactos_'+idOrden).style.border = "";
    document.getElementById('panel_edita_contactos_'+idOrden).style.background = "url('panel_img/panel_"+tipo_contacto+".gif') no-repeat";
    contacto = document.getElementById(tipo_contacto+idOrden).value;

    var accion = 'editarDatosContacto';
    document.getElementById('panel_edita_contactos_'+idOrden).innerHTML = espera;
    var myConn = new XHConnPOST();
    var post="contacto=" + contacto + "&idOrden=" + idOrden + "&idCliente=" + idCliente + "&idSubCliente=" + idSubCliente;

    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML) {
        document.getElementById('panel_edita_contactos_'+idOrden).innerHTML = oXML.responseText;
    };

    myConn.connect("imprime.php?contenido="+accion, "POST", post, peticion);
}



/***********************************************************************************************
FUNCION:		cancelaModifCnct
DESCRIPCION:	Desaparece de pantalla el formulario de edicion de datos o el mensaje de error
				del mismo.
PARAMETROS:		tipo_contacto: el tipo de contacto
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function cancelaModifCnct(idOrden)
{
    document.getElementById('panel_edita_contactos_'+idOrden).style.background = "url('panel_img/panel_default.gif')";
    document.getElementById('panel_edita_contactos_'+idOrden).innerHTML = '<div style="margin:40px">'
    + '<strong>' + xmlDoc.getElementsByTagName('seccion')[8].getElementsByTagName('mensaje')[24].childNodes[0].nodeValue + '</strong><br />'
    + xmlDoc.getElementsByTagName('seccion')[8].getElementsByTagName('mensaje')[25].childNodes[0].nodeValue
    + '</div>';
}



/***********************************************************************************************
FUNCION:		valida_contacto
DESCRIPCION:	Verifica el correcto formato de un nombre de contacto
PARAMETROS:		cadena: nombre de contacto obtenido del textbox correspondiente
SALIDAS:		1: si es correcto;  -1: si la longitud no es la adecuada;  -2: si no es correcto
***********************************************************************************************/
function valida_contacto(cadena)
{
    var filtro_letras = /^[A-Za-z0-9_]+$/;
    var filtro_longitud = /^[A-Za-z0-9_]{4,16}$/;
    if ( filtro_letras.test(cadena) ) {
        if ( filtro_longitud.test(cadena) ) {
            return 1;
        }
        return -1;
    }
    return -2;
}
/***********************************************************************************************
FUNCION:		valida_nombre
DESCRIPCION:	Verifica el correcto formato de un nombre para un contacto
PARAMETROS:		cadena: nombre de contacto obtenido del textbox correspondiente
SALIDAS:		1: si es correcto;  -1: si la longitud no es la adecuada;  -2: si no es correcto
***********************************************************************************************/
function valida_nombre(cadena)
{
    var patron = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz�� ";
    var letra = "";
    if ( cadena.length > 11 ) {
        for ( i=0; i<cadena.length; i++ ) {
            letra = cadena.substring( i, i+1 );
            if ( patron.indexOf(letra) == -1 ) {
                return -2;
            }
        }
        return 1;
    }
    return -1
}
/***********************************************************************************************
FUNCION:		valida_telefono
DESCRIPCION:	Verifica el correcto formato de un nombre para un contacto
PARAMETROS:		cadena: nombre de contacto obtenido del textbox correspondiente
SALIDAS:		1: si es correcto;  -1: si la longitud no es la adecuada;  -2: si no es correcto
***********************************************************************************************/
function valida_telefono(cadena)
{
    var punto = 0;
    var mas = 0;
    nums_tel = new Array();
    nums_tel = cadena.split(".");
    if ( cadena.length >= 14 ) {
        if ( nums_tel[0].indexOf("+") === 0 ) {
            if ( nums_tel[0].length >2 && nums_tel[0].length <5 ) {
                nums_tel[0] = nums_tel[0].substring(1,nums_tel[0].length);
                if ( !isNaN( nums_tel[0] ) ) {
                    if ( !isNaN( nums_tel[1] ) ) {
                        return 1;
                    }
                }
            }
        }
        alert ( "El formato del tel�fono es incorrecto.\nVea el ejemplo." );
        return -2;
    }
    alert ( "La longitud del tel�fono es incorrecta" );
    return -1
}

function Solo_Acentos(texto)
{
    texto=texto.replace(/���/g,'a');
    texto=texto.replace(/���/g,'A');
    texto=texto.replace(/��/g,'I');
    texto=texto.replace(/��/g,'i');
    texto=texto.replace(/��/g,'e');
    texto=texto.replace(/E��/g,'E');
    texto=texto.replace(/o�����/g,'o');
    texto=texto.replace(/���/g,'O');
    texto=texto.replace(/��/g,'u');
    texto=texto.replace(/��/g,'U');
    texto=texto.replace(/�/g,'c');
    texto=texto.replace(/�/g,'C');
    texto=texto.replace(/#/g,'Num. ');

    texto=texto.replace(/[^A-Za-z0-9������������ ]/g,'');

    return texto;
}

/******************************************************************
FUNCION: buscar
OBJETIVO:	Recargar el contenido, enviando una variable POST llamada buscar,
				con el fin de hacer una query al servidor
VARIABLES:
	accion		-> Nombre de la accion que lo mand� llamar
	get_vars		-> Variables que ya existian antes de ser llamado
******************************************************************/
function buscarAjax(accion,get_vars){
    // Obtener los valores a buscar
    var buscar = document.forma_buscar.buscar.value;
    var tipo = document.forma_buscar.tipo.value;

    document.getElementById('contenido').innerHTML = espera;
    var myConn = new XHConnPOST();
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML) {
        document.getElementById('contenido').innerHTML = oXML.responseText;
    };
    myConn.connect("imprime.php?"+get_vars+'&buscar='+buscar+'&tipo='+tipo, "POST", '', peticion);
}

/******************************************************************
FUNCION: ajaxGetPost
OBJETIVO:	Manada llamar "imprime" con cualquier numero de varables GET o POST
VARIABLES:
	target		-> id del div o span objetivo
	get_vars		-> cadena separada por '&' de variables GET
	post_vars	-> cadena separada por '&' de variables POST
******************************************************************/
function ajaxGetPost(target,get_vars,post_vars){
    document.getElementById(target).innerHTML = espera;
    var myConn = new XHConnPOST();
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML) {
        document.getElementById(target).innerHTML = oXML.responseText;
    };
    myConn.connect("imprime.php?"+get_vars, "POST", post_vars, peticion);
}

/***********************************************************************************************
FUNCION:		dameCadOrdenes
DESCRIPCION:	devuelve una cadena con numeros de ordenes separedos por coma
SALIDAS:	cad
***********************************************************************************************/
function dameCadOrdenes(){
    var cad = "";

    inputs = document.getElementsByTagName("INPUT");
    // Recorre todos los checkbox y si estan marcados, lo agrega a la cadena
    for ( i=0; i<inputs.length; i++ )
    {
        if ( (inputs[i].type == "checkbox") && (inputs[i].value!="") && (inputs[i].className == 'check-box ez-hide') && inputs[i].checked==true ) {
            cad += inputs[i].value+',';
        }
    }
    if(cad != '') {
        // Quitar la ultima coma
        cad = cad.substr(0,cad.length-1);
    }
    return cad;
}

/******************************************************************
FUNCION:		dameInfoEditar
DESCRIPCION:	Crea una cadena de informacion en base a las ordenes que seran afectadas
ENTRADAS:		info: todos los datos de las ordenes listadas MX.
				ordenes:  cadena con las ordenes seleccionadas
SALIDAS:		cadena con la info de solo las ordenes seleccionadas
******************************************************************/
function dameInfoEditar(info,ordenes)
{
    var cadena_info = "";
    if ( info.indexOf(ordenes) > 0 ) {
        cadena_info = info;
    }
    cadena_info = cadena_info.substr(0,cadena_info.length);
    return ( cadena_info );
}

/***********************************************************************************************
FUNCION:		todasSonDefault
DESCRIPCION:	verifica que las ordenes seleccionadas sean de estado default
PARAMETROS:		cad: cadena con los numeros de ordenes
SALIDAS:		true: en caso de que todas las ordenes seleccionadas sean default
				<< num de orden >>: el numero de orden que no es default
***********************************************************************************************/
function todasSonDefault(cad){
    var arreglo =	cad.split(',')

    for( i=0; i<arreglo.length; i++ ) {
        estado = document.getElementById('estado_'+arreglo[i]).value;
        if ( estado != 'default' ) {
            return ( arreglo[i] );
        }
    }
    return true;
}

/*
@@@@@@@@@@@ Para usar saldo a favor  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
*/
/***********************************************************************************************
FUNCION:		activa_orden
DESCRIPCION:	llama al script php para activar una orden si el cliente tiene saldo a favor
PARAMETROS:		---------- opcionales ----------
				orden: num de orden si es uno solo y viene del clic de activar
				costo: costo de la orden si es una sola y viene del clic de activar
				--------------------------------
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function activa_orden(orden,costo)
{
    accion = 'activar_ordenes';
    if ( !orden ) {
        ordenes = dameStringActivables( document.getElementById('ordenes_activables').value );
    }
    else {
        ordenes = orden;
    }

    if ( ordenes != '' ) {
        document.getElementById('contenido').innerHTML = espera;
        var myConn = new XHConnPOST();
        var paramPost = "ordenes=" + ordenes;
        if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
        var peticion = function (oXML) {
            document.getElementById('contenido').innerHTML = oXML.responseText;
            SAFAVOR = parseFloat(document.getElementById('input_saldo').value);
            MONTO = parseFloat(document.getElementById('input_monto').value);
        };
        myConn.connect("imprime.php?contenido="+accion, "POST", paramPost, peticion);
    }
    else {
        alert ( "No haz seleccionado una orden que pueda ser activada" );
        document.getElementById('accion_masiva').value = "";
    }
}
/***********************************************************************************************
FUNCION:		actualiza_saldo
DESCRIPCION:	Despliega el saldo disponible del cliente despues de seleccionar alguna de las ordenes
PARAMETROS:		orden: num de orden seleccionada para activar
				costo_orden: costo de la orden seleccionada
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function actualiza_saldo(costo_orden,orden)
{
    SAFAVOR = parseFloat(SAFAVOR);			// se deben pasar los valores a flotante para evitar errores
    costo_orden = parseFloat(costo_orden);	// idem
    MONTO = parseFloat(MONTO);				// idem

    if ( document.getElementById(orden).checked == true ) {
        if ( SAFAVOR >= costo_orden ) {
            // SAFAVOR -= costo_orden;
            // MONTO += costo_orden;
            SAFAVOR = Math.round( (SAFAVOR-costo_orden) * 100 )/100
            MONTO = Math.round( (MONTO+costo_orden) * 100 )/100
        }
        else {
            alert ( "El costo de la orden " + orden + " excede tu saldo a favor" );
            document.getElementById(orden).checked = false;
        }
    }
    else {
        // SAFAVOR += costo_orden;
        // MONTO -= costo_orden;
        SAFAVOR = Math.round( (SAFAVOR+costo_orden) * 100 )/100
        MONTO = Math.round( (MONTO-costo_orden) * 100 )/100
    }
    cad_monto = '$&nbsp;' + MONTO;
    SAFAVOR = Math.round(SAFAVOR*100)/100;  // se debe redondear a dos decimales
    document.getElementById('saldo').innerHTML = '$&nbsp;' + SAFAVOR;
    document.getElementById('monto').innerHTML = cad_monto;
}
/***********************************************************************************************
FUNCION:		procesa_activacion
DESCRIPCION:	Llama al proceso de activar las ordenes seleccionadas
PARAMETROS:		cad_ordenes: cadena con todas las ordenes solicitadas en el paso 1
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function procesa_activacion(cad_ordenes)
{
    accion = 'procesa_activacion';	// accion a realizar
    // cadena con las ordenes a activar
    ordenes_por_activar = dameStringActivables(cad_ordenes,'id');
    // cadena con ordenes que requieren factura
    ordenes_con_factura = dameStringFacturables(ordenes_por_activar);

    if ( ordenes_por_activar != '' ) {
        document.getElementById('contenido').innerHTML = espera;
        var myConn = new XHConnPOST();
        var paramPost = "ordenes=" + ordenes_por_activar + "&monto=" + MONTO + "&saldo=" + SAFAVOR + "&confactura=" + ordenes_con_factura;
        if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
        var peticion = function (oXML) {
            document.getElementById('contenido').innerHTML = oXML.responseText;
            MONTO = 0;	// regresa la variable monto a ceros
        };
        myConn.connect("imprime.php?contenido="+accion, "POST", paramPost, peticion);
    }
    else {
        alert ( "Debes confirmar las ordenes que deseas activar" );
    }
}
/***********************************************************************************************
FUNCION:		dameStringActivables
DESCRIPCION:	devuelve una cadena con las ordenes que se activar�n
PARAMETROS:		activables: cadena con las ordenes que pueden ser activadas (separadas por comas)
				buscapor:  selecciona el criterio de busqueda (opcional)
SALIDAS:		la cadena con las ordenes que se intentaran activar
***********************************************************************************************/
function dameStringActivables(activables,buscapor) {
    var cad = "";
    var arreglo_activables = new Array();
    arreglo_activables = activables.split(",");

    inputs = document.getElementsByTagName("INPUT");
    // recorre los inputs hasta encontrar el primer checkbox y obtiene su estado
    for ( i=0; i<inputs.length; i++ )
    {
        if ( (inputs[i].type == "checkbox") && (inputs[i].value!="") && inputs[i].checked==true ) {
            for ( c=0; c<arreglo_activables.length; c++ ) {
                // por valor
                if ( inputs[i].value == arreglo_activables[c] ) {
                    cad += inputs[i].value+',';
                    break;
                }
                if ( buscapor == 'id' ) {
                    // por id
                    if ( inputs[i].id == arreglo_activables[c] ) {
                        cad += inputs[i].id+',';
                        break;
                    }
                }
            }

        }
    }
    if(cad != '') {
        // Quitar la ultima coma
        cad = cad.substr(0,cad.length-1);
    }

    return cad;
}
/***********************************************************************************************
FUNCION:		dameStringFacturables
DESCRIPCION:	devuelve una cadena con las ordenes que tiene factura y sus respectivos facturantes
PARAMETROS:		cad_ordenes: la cadena con todas las ordenes que se seleccionaron
SALIDAS:		la cadena con las ordenes que se intentaran activar
***********************************************************************************************/
function dameStringFacturables(cad_ordenes)
{
    cadena_facturables = "";
    var confactura = new Array();
    confactura = cad_ordenes.split(",");
    for ( i=0; i<confactura.length; i++ ) {
        // si existe el combo de facturacion, agregar a la cadena el valor seleccionado
        if ( document.getElementById('facturacion_'+confactura[i]) ) {
            cadena_facturables += confactura[i] + ":" + document.getElementById('facturacion_'+confactura[i]).value+",";
        }
    }
    // si la cadena formada tiene alguna factura, quitar la ultima coma
    if ( cadena_facturables.length > 2 ) {
        cadena_facturables = cadena_facturables.substring(0,cadena_facturables.length-1);
    }
    return ( cadena_facturables );
}

/*
@@@@@@@@@@@ Para realizar las redirecciones MX  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
*/
/***********************************************************************************************
FUNCION:		redireccionMX
DESCRIPCION:	llama al php para imprimir el formulario de redireccion de dominio mx
PARAMETROS:		dominio: nombre del dominio que posee la redireccion
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function redireccionMX(dominio,orden)
{
    accion = 'redireccion_mx';
    document.getElementById('contenido').innerHTML = espera;
    var myConn = new XHConnPOST();
    var paramPost = "dominio=" + dominio + "&orden=" + orden;
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML) {
        document.getElementById('contenido').innerHTML = oXML.responseText;
    };
    myConn.connect("imprime.php?contenido="+accion, "POST", paramPost, peticion);
}

/***********************************************************************************************
FUNCION:		editar_redireccion
DESCRIPCION:	pone el textbox para editar alguna redireccion ya existente
PARAMETROS:		<< ninguna >>
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function editar_redireccion()
{
    redir_actual = document.getElementById('hidRedir').value;
    document.getElementById('redireccion').innerHTML = 'http://www.&nbsp;<input type="text" class="textbox" id="txtRedir" style="width:220px;" value="' + redir_actual + '" />';
    document.getElementById('txtRedir').focus();
}

/***********************************************************************************************
FUNCION:		guardar_redireccion
DESCRIPCION:	guarda una redireccion solicitada
PARAMETROS:		dominio: dominio afectado por la redireccion
				orden: numero de orden
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function guardar_redireccion(dominio, orden, eliminar)
{
    var filtro_trim=/^\s+|\s+$/g;
    var filtro_http=/^http:\/\//i;

    if( eliminar==false )
    {
        document.getElementById('txtRedir').value=document.getElementById('txtRedir').value.replace(filtro_trim,'');
        document.getElementById('txtRedir').value=document.getElementById('txtRedir').value.replace(filtro_http,'');
    }
    else
        document.getElementById('txtRedir').value='';

    accion = 'guardarRedireccion';
    var myConn = new XHConnPOST();
    var paramPost = "dominio=" + dominio + "&orden=" + orden + "&redir=" + document.getElementById('txtRedir').value;
    document.getElementById('contenido').innerHTML = espera;
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML) {
        document.getElementById('contenido').innerHTML = oXML.responseText;
    };
    myConn.connect("imprime.php?contenido="+accion, "POST", paramPost, peticion);

}


/***********************************************************************************************
FUNCION:		valida_redireccion
DESCRIPCION:	guarda una redireccion solicitada
PARAMETROS:		dominio: dominio a validar
SALIDAS:		true: si es valido el dominio; false si no es correcto
***********************************************************************************************/
function valida_redireccion(dominio)
{
    //var ereg = /^[A-Za-z0-9\-]+\.[A-Za-z]{2,4}(\.[A-Za-z]{2})?[\/[A-Za-z0-9\-]+]*$/;
    //var ereg = /^([A-Za-z0-9\-]+\.)+[A-Za-z]{2,4}(\.[A-Za-z]{2})?(\/[A-Za-z0-9\-_\.]+)?$/;
    var ereg = /^([A-Za-z0-9\-]+\.)+[A-Za-z]{2,4}(\.[A-Za-z]{2})?((\/[A-Za-z0-9\-_\.]+)?)*(\/)?$/;	//modifico yared 15/09/09
    return ( ereg.test(dominio) );
}

/*
@@@@@@@@@@@ Para realizar las renovaciones  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
*/
/***********************************************************************************************
FUNCION:		renovacion_orden
DESCRIPCION:	llama al php para imprimir el formulario de renovacion de orden
PARAMETROS:		orden: numero de orden
				monto: monto total de la renovacion
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function renovacion_orden(orden,monto)
{
    TIENEIVA = false;
    accion = 'renovacion_orden';
    document.getElementById('contenido').innerHTML = espera;
    var myConn = new XHConnPOST();
    var paramPost = "orden=" + orden + "&costo=" + monto;
    if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
    var peticion = function (oXML) {
        document.getElementById('contenido').innerHTML = oXML.responseText;
        MONTO = parseFloat(document.getElementById('hidMonto').value);
        SAFAVOR = parseFloat(document.getElementById('hidSaldo').value);
        IVA = parseFloat(document.getElementById('hidIva').value);
        if ( IVA > 0 ) {
            TIENEIVA = true;
        }
    };
    myConn.connect("imprime.php?contenido="+accion, "POST", paramPost, peticion);
}
/***********************************************************************************************
FUNCION:		actualiza_monto
DESCRIPCION:	Despliega el saldo disponible del cliente despues de seleccionar alguna de las ordenes
PARAMETROS:		ec: num de orden seleccionada para activar
				costo_ec: costo de la orden seleccionada
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function actualiza_monto(costo_ec,ec)
{
    SAFAVOR = parseFloat(SAFAVOR);		// se deben pasar los valores a flotante para evitar errores
    costo_ec = parseFloat(costo_ec);	// idem
    MONTO = parseFloat(MONTO);			// idem
    IVA = parseFloat(IVA);
    if ( TIENEIVA == true ) {
        costo_comparar = Math.round( costo_ec * 1.15 * 100 ) / 100;
    }
    else {
        costo_comparar = costo_ec;
    }

    if ( document.getElementById(ec).checked == true ) {
        if ( SAFAVOR >= costo_comparar ) {
            //SAFAVOR -= costo_comparar;
            //MONTO += costo_ec;
            SAFAVOR = Math.round( (SAFAVOR-costo_comparar) * 100 ) / 100;
            MONTO = Math.round( (MONTO+costo_ec) * 100 ) / 100;
        }
        else {
            alert ( "El costo del concepto " + ec + " excede tu saldo a favor" );
            document.getElementById(ec).checked = false;
        }
    }
    else {
        //SAFAVOR += costo_comparar;
        //MONTO -= costo_ec;
        SAFAVOR = Math.round( (SAFAVOR+costo_comparar) * 100 ) / 100;
        MONTO = Math.round( (MONTO-costo_ec) * 100 ) / 100;
    }

    if ( TIENEIVA == true ) {
        IVA = Math.round( MONTO * 0.15 * 100 ) / 100;
    }
    else {
        IVA = 0;
    }

    MONTO = Math.round( MONTO *100 ) / 100;
    SAFAVOR = Math.round( SAFAVOR * 100 ) / 100;  // se debe redondear a dos decimales
    document.getElementById('subtotal').innerHTML = MONTO;
    document.getElementById('iva').innerHTML = IVA;
    document.getElementById('total').innerHTML = Math.round( ( MONTO + IVA ) * 100 ) / 100;
    document.getElementById('saldo').innerHTML = '$&nbsp;' + SAFAVOR;
}
/***********************************************************************************************
FUNCION:		guarda_renovacion
DESCRIPCION:	llama al php para imprimir el formulario de renovacion de orden
PARAMETROS:		cad_ec: cadena con los numeros de elementos compra renovados
				orden: numero de orden
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function guarda_renovacion(cad_ec,orden)
{
    var ec_renovados = "";	// cadena con los ec seleccionados para renovar
    var c = 0;	// contador
    var arreglo_ec = new Array();	// arreglo con todos los elementos compra
    arreglo_ec = cad_ec.split(",");
    var idfactura = 0;
    for ( c=0; c<arreglo_ec.length; c++ ) { // buscar elementos compra seleccionados
        if ( document.getElementById(arreglo_ec[c]).checked == true ) {
            ec_renovados += arreglo_ec[c] + ",";
        }
        document.getElementById(arreglo_ec[c]).disabled = "disabled";
    }
    // quitar la ultima coma
    ec_renovados = ec_renovados.substring( 0, ec_renovados.length - 1 );

    // verifica que al menos un ec este seleccionado
    if ( ec_renovados.length > 0 ) {
        MONTO = Math.round( (MONTO+IVA) * 100 ) / 100;
        if ( IVA != 0 && document.getElementById('facturacion_'+orden) ) {
            idfactura = document.getElementById('facturacion_'+orden).value;
            document.getElementById('facturacion_'+orden).disabled = "disabled";
        }
        accion = 'guarda_renovacion';
        document.getElementById('activacion').innerHTML = espera;
        var myConn = new XHConnPOST();
        var paramPost = "elementoscompra=" + ec_renovados + "&orden=" + orden + "&monto=" + MONTO + "&saldo=" + SAFAVOR + "&factura=" + idfactura;
        if (!myConn) alert("XMLHTTP no esta disponible. Intenta con un navegador mas reciente.");
        var peticion = function (oXML) {
            document.getElementById('activacion').innerHTML = oXML.responseText;
        };
        myConn.connect("imprime.php?contenido="+accion, "POST", paramPost, peticion);
    }
    else {
        alert ( "No haz seleccionado algun elemento para renovar" );
        for ( c=0; c<arreglo_ec.length; c++ ) { // buscar elementos compra seleccionados
            document.getElementById(arreglo_ec[c]).disabled = "";
        }
    }
}

/*
@@@@@@@@@@@ Para el log de acciones del cliente  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
*/
/***********************************************************************************************
FUNCION:		buscar_accion
DESCRIPCION:	busca una accion en el log de acciones del usuario
PARAMETROS:		<< ninguno >>
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function buscar_accion()
{
    document.location.href = "#panel";
    txtBuscar = document.getElementById ( 'txtBuscar' ).value;
    ajax( 'log_acciones', 1, txtBuscar, 'contenido');
}


/***********************************************************************************************
FUNCION:		buscar_cliente
DESCRIPCION:	busca un cliente en la bd que corresponda al cliente actual.
PARAMETROS:		<< ninguno >>
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function buscar_cliente()
{
    document.location.href = "#panel";
    txtBuscar = document.getElementById ( 'txtBuscar' ).value;
    ajax( 'mis_clientes', 1, txtBuscar, 'contenido');
}


/*
@@@@@@@@@@@ Para el detalle de los depositos  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
*/
/***********************************************************************************************
FUNCION:		mostrar_depositos_orden
DESCRIPCION:	muestra las ordenes en las que se ha gastado el deposito
PARAMETROS:		deposito_id: id del deposito a solicitar detalles
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function mostrar_depositos_orden(deposito_id)
{
    //document.location.href = '#' + deposito_id ;
    if ( document.getElementById( 'depositos_' + deposito_id ).style.display == "none" ) {
        //despliega la informacion de los depositos utilizados
        document.getElementById( 'depositos_' + deposito_id ).style.display = "";
        //cambia la imagen por el de menos detalles
        document.getElementById( 'detalles_' + deposito_id ).src = "panel_img/menos.gif";
    }
    else {
        //oculta la informacion de los depositos
        document.getElementById( 'depositos_' + deposito_id ).style.display = "none";
        //cambia la imagen por el de menos detalles
        document.getElementById( 'detalles_' + deposito_id ).src = "panel_img/mas.gif";
    }
}

/***********************************************************************************************
FUNCION:		mostrar_detalles_orden
DESCRIPCION:	muestra las ordenes en las que se ha gastado el deposito
PARAMETROS:		deposito_id: id del deposito a solicitar detalles
SALIDAS:		<< ninguna >>
***********************************************************************************************/
function mostrar_detalles_orden(orden)
{

    if ( document.getElementById( 'detalles_' + orden ).style.display == "none" ) {
        //despliega la informacion de los depositos utilizados
        document.getElementById( 'detalles_' + orden ).style.display = "";
        //cambia la imagen por el de menos detalles
        document.getElementById( 'detallesi_' + orden ).src = "panel_img/menos.gif";
    //document.location.href = '#' + orden ;
    }
    else {
        //oculta la informacion de los depositos
        document.getElementById( 'detalles_' + orden ).style.display = "none";
        //cambia la imagen por el de menos detalles
        document.getElementById( 'detallesi_' + orden ).src = "panel_img/mas.gif";
    }
}

/*****************************************************************************
	FUNCION:	html_entities
	UTILIDAD:	Convierte todos los car�cteres especiales a entidades HTML.
	ENTRADAS:
		string		-> Cadena sin entidades HTML
		quote_style	-> Permite especificar la acci�n que se realizar� con las comillas
						dobles y sencillas. La opci�n predeterminada es ENT_COMPAT.
							ENT_COMPAT -> Convierte las comillas dobles e ignora las comillas sencillas.
							ENT_QUOTES -> Convierte ambos tipos de comillas.
							ENT_NOQUOTES ->	Ignora ambos tipos de comillas.
	NOTA:	Depende de la funci�n get_html_translation_table.
	SALIDAS:
		Cadena con entidades HTML en lugar de car�cteres especiales.
	*****************************************************************************/
function htmlentities (string, quote_style)
{
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: nobbler
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman, Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous
    // -    depends on: get_html_translation_table

    var hash_map = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();

    if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }
    hash_map["'"] = '&#039;';
    for (symbol in hash_map) {
        entity = hash_map[symbol];
        tmp_str = tmp_str.split(symbol).join(entity);
    }

    return tmp_str;
}

/*****************************************************************************
	FUNCION:	html_entity_decode
	UTILIDAD:	Convierte todas las entidades HTML a sus car�cteres correspondientes
	ENTRADAS:
		string		-> Cadena con entidades HTML
		quote_style	-> Permite especificar la acci�n que se realizar� con las comillas
						dobles y sencillas. La opci�n predeterminada es ENT_COMPAT.
							ENT_COMPAT -> Convierte las comillas dobles e ignora las comillas sencillas.
							ENT_QUOTES -> Convierte ambos tipos de comillas.
							ENT_NOQUOTES ->	Ignora ambos tipos de comillas.
	NOTA:	Depende de la funci�n get_html_translation_table.
	SALIDAS:
		Cadena con sus car�cteres correspondientes a las entidades HTML que tuviese.
	*****************************************************************************/
function html_entity_decode( string, quote_style )
{
    // http://kevin.vanzonneveld.net
    // +   original by: john (http://www.jd-tech.net)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net), marc andreu
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman, Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous, ger
    // -    depends on: get_html_translation_table

    var hash_map = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();

    if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }

    for (symbol in hash_map) {
        entity = hash_map[symbol];
        tmp_str = tmp_str.split(entity).join(symbol);
    }
    tmp_str = tmp_str.split('&#039;').join("'");

    return tmp_str;
}


/*****************************************************************************
	FUNCION:	get_html_translation_table
	UTILIDAD:	Crea una tabla de equivalencias de los caracteres especiales HTML o las entidades HTML.
	ENTRADAS:
		table	-> Sirve para especificar la tabla que se desea obtener. El valor predeterminado
					es HTML_SPECIALCHARS.
						HTML_ENTITIES -> Para obtener la tabla de las entidades HTML
						HTML_SPECIALCHARS -> Para obtener la tabla de los caracteres especiales HTML.
		quote_style	-> Permite especificar como ser�n incluidas las comillas dobles y sencillas
						en la tabla. La opci�n predeterminada es ENT_COMPAT.
							ENT_COMPAT -> Las comillas dobles ser�n incluidas e ignora las comillas sencillas.
							ENT_QUOTES -> Ambos tipos de comillas ser�n incluidas.
							ENT_NOQUOTES ->	Ignora ambos tipos de comillas.
	SALIDAS:
		Tabla en forma de arreglo con las equivalencias pedidas en el par�metro table.
	*****************************************************************************/
function get_html_translation_table(table, quote_style)
{
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: noname, Alex, Marco, madipta, KELAN, Brett Zamir, T.Wild
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Frank Forte, Ratheous

    var entities = {}, hash_map = {}, decimal = 0, symbol = '';
    var constMappingTable = {}, constMappingQuoteStyle = {};
    var useTable = {}, useQuoteStyle = {};

    // Translate arguments
    constMappingTable[0]      = 'HTML_SPECIALCHARS';
    constMappingTable[1]      = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';

    useTable       = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw new Error("Table: "+useTable+' not supported');
    // return false;
    }

    entities['38'] = '&amp;';
    if (useTable === 'HTML_ENTITIES') {
        entities['160'] = '&nbsp;';
        entities['161'] = '&iexcl;';
        entities['162'] = '&cent;';
        entities['163'] = '&pound;';
        entities['164'] = '&curren;';
        entities['165'] = '&yen;';
        entities['166'] = '&brvbar;';
        entities['167'] = '&sect;';
        entities['168'] = '&uml;';
        entities['169'] = '&copy;';
        entities['170'] = '&ordf;';
        entities['171'] = '&laquo;';
        entities['172'] = '&not;';
        entities['173'] = '&shy;';
        entities['174'] = '&reg;';
        entities['175'] = '&macr;';
        entities['176'] = '&deg;';
        entities['177'] = '&plusmn;';
        entities['178'] = '&sup2;';
        entities['179'] = '&sup3;';
        entities['180'] = '&acute;';
        entities['181'] = '&micro;';
        entities['182'] = '&para;';
        entities['183'] = '&middot;';
        entities['184'] = '&cedil;';
        entities['185'] = '&sup1;';
        entities['186'] = '&ordm;';
        entities['187'] = '&raquo;';
        entities['188'] = '&frac14;';
        entities['189'] = '&frac12;';
        entities['190'] = '&frac34;';
        entities['191'] = '&iquest;';
        entities['192'] = '&Agrave;';
        entities['193'] = '&Aacute;';
        entities['194'] = '&Acirc;';
        entities['195'] = '&Atilde;';
        entities['196'] = '&Auml;';
        entities['197'] = '&Aring;';
        entities['198'] = '&AElig;';
        entities['199'] = '&Ccedil;';
        entities['200'] = '&Egrave;';
        entities['201'] = '&Eacute;';
        entities['202'] = '&Ecirc;';
        entities['203'] = '&Euml;';
        entities['204'] = '&Igrave;';
        entities['205'] = '&Iacute;';
        entities['206'] = '&Icirc;';
        entities['207'] = '&Iuml;';
        entities['208'] = '&ETH;';
        entities['209'] = '&Ntilde;';
        entities['210'] = '&Ograve;';
        entities['211'] = '&Oacute;';
        entities['212'] = '&Ocirc;';
        entities['213'] = '&Otilde;';
        entities['214'] = '&Ouml;';
        entities['215'] = '&times;';
        entities['216'] = '&Oslash;';
        entities['217'] = '&Ugrave;';
        entities['218'] = '&Uacute;';
        entities['219'] = '&Ucirc;';
        entities['220'] = '&Uuml;';
        entities['221'] = '&Yacute;';
        entities['222'] = '&THORN;';
        entities['223'] = '&szlig;';
        entities['224'] = '&agrave;';
        entities['225'] = '&aacute;';
        entities['226'] = '&acirc;';
        entities['227'] = '&atilde;';
        entities['228'] = '&auml;';
        entities['229'] = '&aring;';
        entities['230'] = '&aelig;';
        entities['231'] = '&ccedil;';
        entities['232'] = '&egrave;';
        entities['233'] = '&eacute;';
        entities['234'] = '&ecirc;';
        entities['235'] = '&euml;';
        entities['236'] = '&igrave;';
        entities['237'] = '&iacute;';
        entities['238'] = '&icirc;';
        entities['239'] = '&iuml;';
        entities['240'] = '&eth;';
        entities['241'] = '&ntilde;';
        entities['242'] = '&ograve;';
        entities['243'] = '&oacute;';
        entities['244'] = '&ocirc;';
        entities['245'] = '&otilde;';
        entities['246'] = '&ouml;';
        entities['247'] = '&divide;';
        entities['248'] = '&oslash;';
        entities['249'] = '&ugrave;';
        entities['250'] = '&uacute;';
        entities['251'] = '&ucirc;';
        entities['252'] = '&uuml;';
        entities['253'] = '&yacute;';
        entities['254'] = '&thorn;';
        entities['255'] = '&yuml;';
    }

    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {
        entities['39'] = '&#39;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';

    // ascii decimals to real symbols
    for (decimal in entities) {
        symbol = String.fromCharCode(decimal);
        hash_map[symbol] = entities[decimal];
    }

    return hash_map;
}


function posicionImagen(imagen, i)
{
    var verifica;
    var estado = new Array();
    var stringcookie = new String();

    posXImagen = findLeftObj(imagen);
    posYImagen = findTopObj(imagen);
}

this.findLeftObj = function(obj)
{
    var curleft = 0;
    if (obj.offsetParent)
    {
        while (obj.offsetParent)
        {
            curleft += obj.offsetLeft
            obj = obj.offsetParent;
        }
    }
    else
    {
        if(obj.x)
        {
            curleft += obj.x;
        }
    }
    return(curleft);
}

this.findTopObj = function(obj)
{
    var curtop = 0;
    if (obj.offsetParent)
    {
        while (obj.offsetParent)
        {
            curtop += obj.offsetTop
            obj = obj.offsetParent;
        }
    }
    else
    {
        if (obj.y)
        {
            curtop += obj.y;
        }
    }
    return(curtop);
}


// --- permitira ver a los clientes su authcode
function authcode(dominio,orden) {
    var params = new Object();

    params.dominio = dominio;
    params.orden = orden;
    params.contenido = 'authcode';
    params.contrasena = '';

    if ( document.getElementById('txtPasswordAuthCode'+orden) ) {
        contrasena = $('#txtPasswordAuthCode'+orden).val();
        if ( contrasena.length > 0 ) {
            params.contrasena = contrasena;
            muestra_authcode(dominio,orden,params);
        }
    }
    else {
        $('#divAuthcode'+orden).dialog({
            title: 'Auth Code',
            modal: true,
            resizable:false,
            width: 500,
            open: function(event,ui) {
                muestra_authcode(dominio,orden,params);
            }
        });
    }
}

function muestra_authcode(dominio,orden,params) {
    $.ajax({
        type:'GET',
        url: 'imprime.php',
        data: params,
        beforeSend: function() {
            $('#divAuthcode'+orden).html('<center><img src="cargando.gif" alt="Cargando..." /></center>');
        },
        success: function (datos) {
            $('#divAuthcode'+orden).html(datos);
        }
    })
}
