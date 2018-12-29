var dt_renglon;

var ids_renglones;

var indiceRenglonesClonados = -1;

var renglonesClonado = new Array();

var renglones = {};

var precioUnitarioRenglonSeleccionado = 0;

var cantidadSeleccionada = 0;

var decimales = 2;

var $form = $('#desglose_form');

$(document).ready(function(){
	
	initRenglonTable();
	
	initSubtotal();
	
	initBotonCrearDesglose();
	
	initBotonAgregarRenglon();
	
	initCustomCurrencies();
	
	initMenuTopFijo();
	
	initBotonSubmit();
	
});

function initRenglonTable() 
{
	dt_renglon = dt_datatable($('#tabla_renglones'));
	dt_renglon = dt_renglon.DataTable();
}

function initBotonCrearDesglose()
{
	$('#btn_crear_desglose').on('click', function(){
		
		ids_renglones = dt_getSelectedRowsIds($('#tabla_renglones'));
		
		if (!ids_renglones.length) {
            show_alert({msg: 'Debe seleccionar al menos un renglón.'});
            desbloquear();
            return;
        }
		
		if (!$('#seccion_crear_desglose').hasClass('seccion_activa') ) {
			$('#seccion_crear_desglose').show().addClass('seccion_activa');
			agregarRenglon();
			$('#accion').val('desglose');
		}
	});
	
}

function initBotonAgregarRenglon()
{
	$('#btn_agregar_renglon').on('click', function(){
		
		agregarRenglon();
		
	});
}

function agregarRenglon()
{
	copiarDatosRenglon();
	
	$renglonClonado = $('.row_renglon_oc_nuevo').clone().appendTo('#renglon_clonado');
	indiceRenglonesClonados++;
	var indice = indiceRenglonesClonados;
	$renglonClonado.attr('indice', indice);
	
	// Le seteo el indice a todos los inputs
	$renglonClonado.find('input').attr('indice', indice);
	
	// Ahora renombro los ids de los inputs
	$renglonClonado.find('#renglon_cantidad').attr('id', 'renglon_cantidad_' + indice);
	$renglonClonado.find('#renglon_precioUnitario').attr('id', 'renglon_precioUnitario_' + indice);
	$renglonClonado.find('#renglon_neto').attr('id', 'renglon_neto_' + indice);
	$renglonClonado.find('#renglon_alicuota').attr('id', 'renglon_alicuota_' + indice);
	$renglonClonado.find('#renglon_iva').attr('id', 'renglon_iva_' + indice);
	$renglonClonado.find('#renglon_bruto').attr('id', 'renglon_bruto_' + indice);
	$renglonClonado.find('#idRenglon').attr('id', 'idRenglon_' + indice);
	
	$renglonClonado.find('.renglon_oc_borrar').attr('indice', indice);
	$renglonClonado.show().removeClass('row_renglon_oc_nuevo').addClass('renglon_indice_' + indice);
	$renglonClonado.find('.ignore').removeClass('ignore');
	
	initCustomCurrencies();
	
	initBotonBorrarRenglon();
	
	attachEventCalculos($renglonClonado);
	
	var subtotal = calcularSubtotal();
	
	mostrarSubtotalMenuFijo(subtotal);
	
}

/**
* Copio los datos de la grilla a la renglon original modelo
*/
function copiarDatosRenglon()
{
	var datos = dt_getSelectedRows($('#tabla_renglones'));
	
	if (typeof datos[0] != 'undefined') {
		var datosRenglon = datos[0];
		var cantidad = convertToCurrencyFormat(datosRenglon[8]);
		cantidadSeleccionada = parseFloat( cantidad.replace('.', '').replace(',', '.') );
		
		var precioUnitario = convertToCurrencyFormat(datosRenglon[9]);
		precioUnitarioRenglonSeleccionado = parseFloat( precioUnitario.replace('.', '').replace(',', '.') );
		
		var neto = convertToCurrencyFormat(datosRenglon[10]);
		var iva = convertToCurrencyFormat(datosRenglon[11]);
		var alicuotaIva = convertToCurrencyFormat(datosRenglon[12]);
		var idAlicuota = convertToCurrencyFormat(datosRenglon[13]);
		var bruto = convertToCurrencyFormat(datosRenglon[14]);
		var idRenglon = parseInt(datosRenglon[15]);
		
		$('.row_renglon_oc_nuevo').find('#renglon_cantidad').val(cantidad);
		$('.row_renglon_oc_nuevo').find('#renglon_precioUnitario').val(precioUnitario);
		$('.row_renglon_oc_nuevo').find('#renglon_neto').val(neto);
		$('.row_renglon_oc_nuevo').find('#renglon_alicuota').val(alicuotaIva);
		$('.row_renglon_oc_nuevo').find('#renglon_iva').val(iva);
		$('.row_renglon_oc_nuevo').find('#renglon_bruto').val(bruto);
		$('.row_renglon_oc_nuevo').find('#idRenglon').val(idRenglon);
	}
}

function initSubtotal()
{
	subtotal = $('#montoOc').val();
	subtotal = parseFloat(subtotal);
	$('#renglon_subtotal_neto').val(convertToCurrencyFormat(subtotal));
}

function initCustomCurrencies()
{
	$('.currency').each(function () {

        var digits = decimales;

        $(this).val($(this).val().replace(/\./g, ','));

        $(this).inputmask("decimal", {radixPoint: ",", digits: digits});
    });
}

function attachEventCalculos($renglonClonado)
{
	var indice = $renglonClonado.attr('indice');
	
	$cantidad = $('#renglon_cantidad_' + indice);
	$precioUnitario = $('#renglon_precioUnitario_' + indice);
	
	$cantidad.on('change', function(e) {
		
		$neto = $('#renglon_neto_' + indice);
		$alicuotaIva = $('#renglon_alicuota_' + indice);
		$iva = $('#renglon_iva_' + indice);
		$bruto = $('#renglon_bruto_' + indice);
	
		var cantidad = $(this).val().replace(',', '.');
		var precioUnitario = $('#renglon_precioUnitario_' + indice).val().replace('.', '').replace(',', '.');
		// Neto
		var neto = calcularNeto(cantidad, precioUnitario);
		$neto.val( convertToCurrencyFormat(neto) );
		// IVA
		var alicuotaIva = $alicuotaIva.val().replace('.', '').replace(',', '.');
		var iva = calcularIva(neto, alicuotaIva);
		$iva.val( convertToCurrencyFormat(iva) );
		// Bruto
		var bruto = calcularBruto(neto, iva);
		$bruto.val( convertToCurrencyFormat(bruto) );
		
		calcularSubtotal();
	});
	
	$precioUnitario.on('change', function(e) {
		
		$neto = $('#renglon_neto_' + indice);
		$alicuotaIva = $('#renglon_alicuota_' + indice);
		$iva = $('#renglon_iva_' + indice);
		$bruto = $('#renglon_bruto_' + indice);
		
		var cantidad = $('#renglon_cantidad_' + indice).val().replace('.', '').replace(',', '.');
		var precioUnitario = $(this).val().replace('.', '').replace(',', '.');
		// Neto
		var neto = calcularNeto(cantidad, precioUnitario);
		$neto.val( convertToCurrencyFormat(neto) );
		// IVA
		var alicuotaIva = $alicuotaIva.val().replace('.', '').replace(',', '.');
		var iva = calcularIva(neto, alicuotaIva);
		$iva.val( convertToCurrencyFormat(iva) );
		// Bruto
		var bruto = calcularBruto(neto, iva);
		$bruto.val( convertToCurrencyFormat(bruto) );
		
		calcularSubtotal();
	});
	
}

function calcularNeto(cantidad, precioUnitario)
{
	cantidad = parseFloat(cantidad);
	precioUnitario = parseFloat(precioUnitario);
	return parseFloat(cantidad * precioUnitario).toFixed(decimales);
}

function calcularIva(neto, alicuotaIva)
{
	neto = parseFloat(neto);
	alicuotaIva = parseFloat(alicuotaIva);
	return parseFloat(neto * alicuotaIva / 100).toFixed(decimales);
}

function calcularBruto(neto, iva)
{
	neto = parseFloat(neto);
	iva = parseFloat(iva);
	return parseFloat(neto + iva).toFixed(decimales);
}

function calcularSubtotal()
{
	var subtotal = 0;
	
	$('.renglones_bruto').not('.ignore').each(function() {  
		var bruto = parseFloat( $(this).val().replace('.', '').replace(',', '.') );
		subtotal = subtotal + bruto;
	});
	
	$('#renglon_subtotal_neto').val(convertToCurrencyFormat(subtotal));
	
	mostrarSubtotalMenuFijo(subtotal);
	
	return subtotal;
}

function initBotonBorrarRenglon()
{
	$('.renglon_oc_borrar').off().on('click', function(e) {
		
		e.preventDefault();
		
		var indice = $(this).attr('indice');
		
		if ($('.renglon_indice_' + indice).length > 0) {
			
			$('.renglon_indice_' + indice).remove();
		} 
		
		calcularSubtotal();
	});
}

function initMenuTopFijo()
{
	$(window).on('scroll', function(){
		if ($(this).scrollTop() >= 150) {
			$('.menu_fijo_totales').show();
		} else {
			$('.menu_fijo_totales').hide();
		}
	});
	
}

function mostrarSubtotalMenuFijo(subtotal)
{
	var simboloTipoMoneda = $('#simboloTipoMoneda').val();
	var totalOC = parseFloat($('#montoOc').val());
	var diferencia = totalOC - subtotal;
	
	subtotal = convertToCurrencyFormat(subtotal);
	subtotal = agregarSeparadorMiles(subtotal);
	
	$('#menu_fijo_totales_subtotal_desglose').html(simboloTipoMoneda + ' ' + subtotal);
	
	diferencia = convertToCurrencyFormat(diferencia);
	diferencia = agregarSeparadorMiles(diferencia);
	
	$('#menu_fijo_totales_diferencia').html(simboloTipoMoneda + ' ' + diferencia);
}

function initBotonSubmit()
{
	$('#desglose_oc_submit').on('click', function(e) {
		
		e.preventDefault();

		if (validarPrecionUnitarios() && validarCantidades() && validarTotalDesglose()) {
			$('.row_renglon_oc_nuevo').remove();
			$form.submit();
		}
		
	});
}

function validarPrecionUnitarios()
{
	// Recorro los precio unitarios
	var valido = true; 
	$('input[id ^=renglon_precioUnitario]').not('.ignore').removeClass('error');
	$('input[id ^=renglon_precioUnitario]').not('.ignore').each(function() {
		
		var precioUnitario = parseFloat( $(this).val().replace('.', '').replace(',', '.') );
		var simboloTipoMoneda = $('#simboloTipoMoneda').val();
		var strPrecioUnitarioSeleccionado = convertToCurrencyFormat(precioUnitarioRenglonSeleccionado);
		strPrecioUnitarioSeleccionado = simboloTipoMoneda + ' ' + strPrecioUnitarioSeleccionado;
		var indice = $(this).attr('indice');
		
		if (precioUnitario > precioUnitarioRenglonSeleccionado) {
			
			valido = false;
			var options = {
				title: 'Error',
				msg: 'El precio unitario ingresado no puede ser mayor a ' + strPrecioUnitarioSeleccionado,
				type: 'info'
			};
			
			show_alert(options);
			
			$('#renglon_precioUnitario_' + indice).addClass('error');
		}
		
		if (precioUnitario <= 0) {
			
			valido = false;
			
			var options = {
				title: 'Error',
				msg: 'El precio unitario ingresado no puede ser cero o menor que cero',
				type: 'info'
			};
				
			show_alert(options);
			
			$('#renglon_precioUnitario_' + indice).addClass('error');
		}
	});
	
	return valido;
}

function validarTotalDesglose()
{
	var valido = true; 
	
	var totalOC = parseFloat($('#montoOc').val());
	
	var strTotalOC = convertToCurrencyFormat( $('#montoOc').val() );
	strTotalOC = agregarSeparadorMiles(strTotalOC);
	var simboloTipoMoneda = $('#simboloTipoMoneda').val();
	
	var subtotal = calcularSubtotal();
	
	if (subtotal > totalOC) {
		
		valido = false;
		
		var options = {
			title: 'Error',
			msg: 'El total del desglose no puede ser mayor al total de la OC ' + simboloTipoMoneda + ' ' + strTotalOC,
			type: 'info'
		};
			
		show_alert(options);
	}
	
	if ($('input[id ^=renglon_precioUnitario]').not('.ignore').length == 0 || subtotal == 0) {
		
		valido = false;
		
		var options = {
			title: 'Error',
			msg: 'El total del desglose no puede ser cero',
			type: 'info'
		};
			
		show_alert(options);
	}
	
	return valido;
}

function validarCantidades()
{
	var valido = true;
	var cantidadInput = 0;
	var cantidad = 0;
	$('input[id ^=renglon_cantidad]').not('.ignore').removeClass('error');
	$('input[id ^=renglon_cantidad]').not('.ignore').each(function() {
		
		cantidadInput = parseFloat( $(this).val().replace('.', '').replace(',', '.') );
		cantidad = cantidad + cantidadInput;
		var indice = $(this).attr('indice');
			
		if (cantidadInput > cantidadSeleccionada) {
		
			valido = false;
			
			var options = {
				title: 'Error',
				msg: 'La cantidad ingresada no puede ser mayor a la cantidad seleccionada ' + cantidadSeleccionada,
				type: 'info'
			};
				
			show_alert(options);
			
			$('#renglon_cantidad_' + indice).addClass('error');
		}
		
		if (cantidadInput <= 0) {
		
			valido = false;
			
			var options = {
				title: 'Error',
				msg: 'La cantidad ingresada no puede ser cero o menor que cero',
				type: 'info'
			};
				
			show_alert(options);
			
			$('#renglon_cantidad_' + indice).addClass('error');
		}
	});
	
	var rows = dt_getRows($('#tabla_renglones'));
	var cantidadTotal = 0;
	for(var i = 0; i < rows.length; i++) {
		cantidadTotal = cantidadTotal + parseFloat( rows[i][8].replace('.', '').replace(',', '.') );
	};
	
	if (cantidad > cantidadTotal) {
		
		valido = false;
		
		var options = {
			title: 'Error',
			msg: 'La cantidad ingresada supera a la cantidad total de la OC ' + cantidadTotal,
			type: 'info'
		};
			
		show_alert(options);
	}
	
	return valido;
}