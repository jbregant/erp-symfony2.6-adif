
var id_oc;

var id_proveedor;

var __TIPO_COMPROBANTE_NC = 3;

var $formularioComprobanteCompra = $('form[name=adif_contablebundle_comprobantecompra]');

var $letraComprobanteSelect = $('#adif_contablebundle_comprobantecompra_letraComprobante');

var esOcMonedaExtranjera = false; // Este flag me indica si la OC viene con moneda extranjera

$(document).ready(function () {

    initOC();

    initAutocompleteProveedor();

    initOrdenCompraHandler();

    initFechaComprobanteValidation();

    updateRenglonDevolucionHandler();

    initPuntoVentaHandler();

    initIncluirOCSinSaldoButton();

    initSubmitButton();
	
	initTipoCambioButton(); 
	
});

function initOC() {

    if ($('#adif_contablebundle_comprobantecompra_idOrdenCompra').val() !== "") {

        id_oc = $('#adif_contablebundle_comprobantecompra_idOrdenCompra').val();

        $('tr[id_oc="' + id_oc + '"]').addClass("active");
    }
}

/**
 * 

 * @returns {undefined}
 */
function initAutocompleteProveedor() {
	
	limpiarDatosTipoCambioOC();
    $('#adif_contablebundle_comprobantecompra_proveedor').autocomplete({
        source: __AJAX_PATH__ + 'proveedor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {

            completarInformacionProveedor(event, ui);

            updateLetraComprobanteSelect();

            checkFechaVencimientoCai();
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };

}

/**
 * 
 * @param {type} event
 * @param {type} ui
 * @returns {undefined}
 */
function completarInformacionProveedor(event, ui) {

    unselectOrdenCompra();

    $('#adif_contablebundle_comprobantecompra_proveedor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_comprobantecompra_proveedor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_comprobantecompra_idProveedor').val(ui.item.id);
    $('#legend_oc_proveedor').html(': ' + ui.item.razonSocial + ' - CUIT: ' + ui.item.CUIT);

    caisProveedor = ui.item.cais;

    id_proveedor = ui.item.id;

    $('#table_ordenes_compra_proveedor').hide();

    cargarOrdenesCompra(event);
}

/**
 * 
 * @returns {undefined}
 */
function initOrdenCompraHandler() {

    $(document).on('click', '#table_ordenes_compra_proveedor tbody tr', function (e, tr) {

        bloquear();

        id_oc = $(this).attr('id_oc');

        cargarRenglonesYAdicionales($('#adif_contablebundle_comprobantecompra_tipoComprobante').val());

        updateRenglonDevolucionHandler();
    });

    $(document).on('change', '#adif_contablebundle_comprobantecompra_tipoComprobante', function (e) {
        if (!id_oc) {
            show_alert({
                title: 'Informaci&oacute;n',
                msg: 'Recuerde elegir una <b>orden de compra</b> para continuar.',
                type: 'warning'
            });
            return;
        }

        cargarRenglonesYAdicionales($(this).val());

        initInputModificaCantidad();
    });
}

/**
 * 
 * @param {type} tipoComprobanteVal
 * @returns {undefined}
 */
function cargarRenglonesYAdicionales(tipoComprobanteVal) {

    $('#nc_agregar_renlgon_ctn').hide();

    $('.div_ctn_seccion_adicionales').show();

    var esNotaCredito = 0;

    if ($('#adif_contablebundle_comprobantecompra_tipoComprobante').val() == __TIPO_COMPROBANTE_NC) {
        esNotaCredito = 1;
    }

    var ajax_renglones = $.ajax({
        url: __AJAX_PATH__ + 'ordenescompra/renglones_orden_compra/',
        data: {
            id_orden_compra: id_oc,
            es_nota_credito: esNotaCredito
        },
        type: 'POST',
        dataType: 'json'
    });

    var ajax_adicionales = $.ajax({
        url: __AJAX_PATH__ + 'ordenescompra/adicionales_cotizacion/',
        data: {id_orden_compra: id_oc},
        type: 'POST',
        dataType: 'json'
    });

    $.when(ajax_renglones, ajax_adicionales).then(function (result_renglones, result_adicionales) {

        result_renglones = result_renglones[0] ? result_renglones[0] : null;
		
        result_adicionales = result_adicionales[0] ? result_adicionales[0] : null;

        // Renglones
        if (result_renglones) {

            $('.ctn_rows_renglon_comprobante').empty();

            var id_condicion_pago = 0;

			var codigoTipoMoneda = '';
			var idTipoMoneda = 0;
			var strTipoMoneda = '';
            $.each(result_renglones, function (i, renglon) {

                var cantidad = renglon.restante;
				codigoTipoMoneda = renglon.codigoTipoMoneda;
				idTipoMoneda = renglon.idTipoMoneda;
				strTipoMoneda = renglon.strTipoMoneda;
				
                if ($('#adif_contablebundle_comprobantecompra_tipoComprobante').val() == __TIPO_COMPROBANTE_NC) {
                    cantidad = renglon.cantidad - renglon.restante;
                }
				
				var tipoCambio;
				if (typeof renglon.tipoCambio == 'undefined' || renglon.tipoCambio == '' || renglon.tipoCambio == null) {
					tipoCambio = 1;
				} else {
					tipoCambio = renglon.tipoCambio;
					tipoCambio = tipoCambio.replace('.', ',');
				}
				
                if (cantidad > 0) {
                    crear_renglon_comprobante(renglon.id, renglon.descripcion, renglon.denominacionBienEconomico, cantidad, renglon.precioUnitario, 
                        renglon.idAlicuotaIva, bonificacion_tipo = null, bonificacion_valor = null, monto_neto = null, monto_iva = null, tipoCambio);
                }
                
                id_condicion_pago = renglon.idCondicionPago;
            });

            $('#adif_contablebundle_comprobantecompra_condicionPago').val(id_condicion_pago).select2();
            $('#adif_contablebundle_comprobantecompra_idOrdenCompra').val(id_oc);
			
			if (codigoTipoMoneda != '' && codigoTipoMoneda != __tipoMonedaCodigoARS) {
				// Si la OC no es en moneda ARS, entonces habilito la seccion para que metan la cotizacion del dia de la moneda extranjera de la OC
				esOcMonedaExtranjera = true;
				$('#esOcMonedaExtranjera').val(1);
				$('#seccion_tipo_cambio').show();
				$('#idTipoMoneda').val(idTipoMoneda);
				$('#strTipoMoneda').val(strTipoMoneda);
				$('#strTipoCambio').focus();
			} else {
				esOcMonedaExtranjera = false;
				$('#esOcMonedaExtranjera').val(0);
				limpiarDatosTipoCambioOC();
			}
			
            restringir_iva(false);
            init_validaciones();
            recalcular_netos();
        }
        else {
            show_alert({msg: 'Ocurri&oacute; un error al obtener los renglones de la orden de compra. Intente nuevamente.'});
        }

        // Adicionales
        if (result_adicionales) {

            $('.ctn_rows_renglon_adicional').empty();

            $.each(result_adicionales, function (i, renglon) {
                crear_renglon_adicional(renglon.id, renglon.idTipoAdicional, renglon.signo, renglon.valor, renglon.tipoValor, renglon.idAlicuotaIva);
            });

            restringir_iva();

            recalcular_netos_adicionales();
        }
        else {
            show_alert({msg: 'Ocurri&oacute; un error al obtener los adicionales de la orden de compra. Intente nuevamente.'});
        }

        if (tipoComprobanteVal == __TIPO_COMPROBANTE_NC) {

            initNotaCredito();

            $('.descripcion').removeClass('col-md-3').addClass('col-md-2');
            $('.devolucion').show();
                
            initChecksYRadios();

            updateRenglonDevolucionHandler();
        }
        else {

            $('.descripcion').removeClass('col-md-2').addClass('col-md-3');
            $('.devolucion').hide();
        }

        initChecksYRadios();

        desbloquear();

    }, function () {

        show_alert({msg: 'Ocurri&oacute; un error al obtener los adicionales y los renglones de la orden de compra. Intente nuevamente.'});

        desbloquear();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initNotaCredito() {

    validarCantidadComprobantesDebito();
}

/**
 * 
 * @returns {undefined}
 */
function validarCantidadComprobantesDebito() {

    bloquear();

    $.ajax({
        type: 'POST',
        url: __AJAX_PATH__ + 'ordenescompra/cantidad_comprobantes_debito/',
        data: {id_oc: id_oc}
    }).done(function (cantidadComprobantesDebito) {

        if (cantidadComprobantesDebito === 0) {

            unselectOrdenCompra();

            show_alert({msg: 'No es posible cargar la nota de crédito. La orden de compra seleccionada no posee débitos relacionados.'});

            desbloquear();
        }

    });

    desbloquear();
}

/**
 * 
 * @returns {undefined}
 */
function unselectOrdenCompra() {

    id_oc = null;

    $('#adif_contablebundle_comprobantecompra_idOrdenCompra').val(null);

    $('#table_ordenes_compra_proveedor').find('tr').removeClass('active');

    $('.row_headers_renglon_comprobante').hide();
    $('.row_footer_renglon_comprobante').hide();
    $('.ctn_rows_renglon_comprobante').empty();

    $('.div_ctn_seccion_adicionales').hide();
    $('.ctn_rows_renglon_adicional').empty();

    restringir_iva();

    init_validaciones();

    recalcular_netos();
}

/**
 * 
 * @returns {undefined}
 */
function initFechaComprobanteValidation() {
    var currentDate = getCurrentDate();

    $('#adif_contablebundle_comprobantecompra_fechaComprobante').datepicker('setEndDate', currentDate);
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_comprobantecompra_submit').on('click', function (e) {

        if ($formularioComprobanteCompra.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el comprobante?',
                callbackOK: function () {

                    if (validForm(e)) {
                        
                        var esNotaCredito = false;
                        if ($('#adif_contablebundle_comprobantecompra_tipoComprobante').val() == __TIPO_COMPROBANTE_NC) {
                            esNotaCredito = true;
                        }
                        
                        if (!esNotaCredito) {
                        
                            var totalIngresado = $('#adif_contablebundle_comprobantecompra_total').val();
                            totalIngresado = parseFloat(clearCurrencyValue(totalIngresado));
                            var saldoOC = parseFloat($('#saldo_oc').val());

                            var impuestos = $('#adif_contablebundle_comprobantecompra_renglonesImpuesto_1_monto').val();
                            if (typeof impuestos != 'undefined') {
                                impuestos = parseFloat(clearCurrencyValue(impuestos));
                            } else {
                                impuestos = 0;
                            }

                            var percepciones = $('#adif_contablebundle_comprobantecompra_renglonesPercepcion_1_monto').val();
                            if (typeof percepciones != 'undefined') {
                                percepciones = parseFloat(clearCurrencyValue(percepciones));
                            } else {
                                percepciones = 0;
                            }
                        
                            // No se tiene en cuenta para el saldo de la oc, impuestos ni percepciones
                            totalIngresado = totalIngresado - impuestos - percepciones;
                            console.debug("total ingresado " + totalIngresado);
                            console.debug("saldo oc " + saldoOC);
                            if (totalIngresado > saldoOC) {

                                show_alert({msg: 'El total ingresado (neto + iva) supera al saldo de la OC.'});

                                return false;
                            }
                            
                        }
                        
                        $formularioComprobanteCompra.submit();
                    }

                    desbloquear();
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;
    });
}

/**
 * 
 * @param {type} event
 * @returns {undefined|Boolean}
 */
function validForm(event) {

    bloquear();

    if ($('#adif_contablebundle_comprobantecompra_tipoComprobante').val() == __TIPO_COMPROBANTE_NC) {
        return validarImporteTotalNotaCredito(event);
    }
    else {

        var montoComprobanteEsValido = parseFloat(clearCurrencyValue($('#adif_contablebundle_comprobantecompra_total').val())) > 0;

        if (!montoComprobanteEsValido) {

            show_alert({msg: 'El monto total del comprobante debe ser mayor a cero.'});

            return false;
        }

    }

    desbloquear();

    return true;
}

/**
 * 
 * @param {type} event
 * @returns {Boolean}
 */
function validarImporteTotalNotaCredito(event) {

    var hayError = false;

    $.ajax({
        type: 'POST',
        async: false,
        url: __AJAX_PATH__ + 'ordenescompra/importe_total_comprobantes_debito/',
        data: {id_oc: id_oc}
    }).done(function (importeTotalComprobantesDebito) {

        var montoTotal = $("input[id ^=adif_contablebundle_comprobante][id $= _total]")
                .val().replace(',', '@').replace(/\./g, ',').replace('@', '.');

		if ( $("#esOcMonedaExtranjera").val() == 1 ) {
			var tipoCambio = parseFloat($('#strTipoCambio').val().replace(',', '.'));
			if (tipoCambio != 0) {
				montoTotal = montoTotal / tipoCambio;
				montoTotal = montoTotal.toFixed(2);
			}
		}
		
        if (parseFloat(montoTotal) > parseFloat(importeTotalComprobantesDebito)) {

            event.stopPropagation();

            show_alert({msg: 'La nota de crédito no puede superar los ' + convertToMoneyFormat(importeTotalComprobantesDebito)});

            desbloquear();

            hayError = true;
        }

    });

    desbloquear();

    return !hayError;

}

/**
 * 
 * @returns {undefined}
 */
function initChecksYRadios() {

    var swts = $('form input[type=checkbox]').not('[baseClass=bootstrap-switch]').not('.ignore');

    if (swts.length == 0) {
        return;
    }

    swts.attr({
        'data-on-label': 'Si',
        'data-off-label': 'No',
        'data-on': "success",
        'data-off': "default",
        'baseClass': "bootstrap-switch"
    }).bootstrapSwitch();

    initInputModificaCantidad();
}

/**
 * 
 * @returns {undefined}
 */
function updateRenglonDevolucionHandler() {

    var $inputDevolucion = $('input[id ^= "adif_contablebundle_comprobantecompra_renglonesComprobante_"][id $= "_esDevolucion"]:not([sname])');

    $inputDevolucion.each(function () {

        checkInputCantidad($(this));

        $(this).on('switch-change', function (e) {

            e.preventDefault();
                
            $elem = $(this);

            if ($elem.bootstrapSwitch('state') == false) {
                        
                show_confirm({
                    msg: 'Si marca es devolución en "No", no restará la cantidad para esta línea.<br/> ¿Está seguro que quiere confirmar esta acción?',
                    callbackCancel: function() {
                        $elem.bootstrapSwitch('toggleState');
                    }
                });
                    
            }
        });
            
        checkInputCantidad($(this));
    });
}

/**
 * 
 * @param {type} inputDevolucion
 * @returns {undefined}
 */
function checkInputCantidad(inputDevolucion) {

    var $parentRow = inputDevolucion.parents('.row_renglon_comprobante');

    var $inputCantidad = $parentRow.find('input[id $= "_cantidad"]');

    if (inputDevolucion.is(':checked')) {
        $inputCantidad.prop('readonly', false);
    }
    else {
        $inputCantidad.prop('readonly', true);
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateLetraComprobanteSelect() {

    resetSelect($letraComprobanteSelect);

    var data = {
        idProveedor: $('#adif_contablebundle_comprobantecompra_idProveedor').val()
    };

    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'proveedor/letras_comprobante',
        data: data
    }).done(function (letrasComprobante) {

        for (var i = 0, total = letrasComprobante.length; i < total; i++) {
            $letraComprobanteSelect.append('<option value="' + letrasComprobante[i].id + '">' + letrasComprobante[i].letra + '</option>');
        }

        $letraComprobanteSelect.select2('val', $letraComprobanteSelect.find('option:first').val());

        $letraComprobanteSelect.select2('readonly', false);
    });
}

/**
 * 
 * @returns {undefined}
 */
function initInputModificaCantidad() {
    $('input[id$=_modificaCantidad').bootstrapSwitch('setState', true);
}

/**
 * 
 * @returns {undefined}
 */
function initIncluirOCSinSaldoButton() {

    $('#checkbox-filtro-oc').on('switch-change', function (event) {

        if (typeof id_proveedor !== "undefined") {

            unselectOrdenCompra();

            cargarOrdenesCompra(event);
        }

    });

}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function cargarOrdenesCompra(event) {

    $.ajax({
        url: __AJAX_PATH__ + 'ordenescompra/index_table/',
        data: {
            id_proveedor: id_proveedor,
            oc_sin_saldo: $('#checkbox-filtro-oc').is(':checked') ? 1 : 0
        }
    }).done(function (result) {

        var ocs = JSON.parse(result).data;

        $('#table_ordenes_compra_proveedor tbody').empty();
        $('#table_ordenes_compra_proveedor').show();
        $('#nc_agregar_renlgon_ctn').hide();
        $('.div_ctn_seccion_adicionales').hide();

        $(ocs).each(function (oc) {

            var $tr = $('<tr />', {id_oc: this[0], style: 'cursor: pointer;'});

            $tr.on('click', function () {
                $(this).parents('tbody').find('tr').removeClass('active');
                $(this).addClass('active');
                
                var saldoOC = $(this).find('td.td_saldo_oc').text();
                saldoOC = clearCurrencyValue(saldoOC);
                $('#saldo_oc').val(saldoOC);
            });

            $('<td />', {text: this[1]}).addClass('nowrap').appendTo($tr); // Numero
            $('<td />', {text: this[2]}).addClass('nowrap').appendTo($tr); // Fecha
            $('<td />', {text: this[3] === "" ? '-' : this[3]}).addClass('nowrap').appendTo($tr); // Carpeta
            $('<td />', {text: this[4] === "" ? '-' : this[4]}).appendTo($tr); // Observacion
            $('<td />', {text: this[5] === "" ? '-' : this[5]}).addClass('text-right').appendTo($tr); // Total OC
            $('<td />', {text: this[6] === "" ? '-' : this[6]}).addClass('text-right td_saldo_oc').appendTo($tr); // Saldo OC

            $('#table_ordenes_compra_proveedor tbody').append($tr);
        });

        $(document).trigger(event);
    });

}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value
            .replace('$', '')
            .replace('%', '')
            .replace(/\./g, '')
            .replace(/\,/g, '.')
            .trim();
}

function initTipoCambioButton()
{
	$('#adif_contablebundle_comprobantecompra_actualizar_tipo_cambio').on('click', function() {
		
		var tipoCambio = parseFloat($('#strTipoCambio').val().replace(',', '.'));
		
		var strTipoCambio = new String(tipoCambio);
		$('.renglones_tipos_cambios').val(strTipoCambio.replace('.', ','));
		
		var $montosNetos = $('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[montoNeto\]"]');
		
		var precioUnitarioOriginal = [];
		
		$montosNetos.each(function (i) {
			
			var indice = $(this).parents('.row_renglon_comprobante').attr('indice');
			//var precioUnitario = parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_precioUnitario]').val().replace(',', '.'));
			var precioUnitarioOriginal = parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_precioUnitarioOriginal]').val().replace(',', '.'));
			var precioUnitario = precioUnitarioOriginal * tipoCambio;
			
			$('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_precioUnitario]').val(precioUnitario.toFixed(2).replace('.', ','));
		});
		
		recalcular_netos();
	});	
	
}

/**
* Override de la funcion de web/js/custom/contable/comprobante/new.js => init_validaciones() para que no me tome valide el precio unitario
* si la OC es con moneda extranjera.
*/
function init_validaciones() {
	
	console.debug("overrides init_validaciones()");
	
	if (esOcMonedaExtranjera) {
		
		$('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[cantidad\]"]').each(function () {
			$(this).rules('add', {
				valor_maximo: parseFloat($(this).val().replace(',', '.'))
			});
		});
		
	} else {
		
		$('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[cantidad\]"]').each(function () {
			$(this).rules('add', {
				valor_maximo: parseFloat($(this).val().replace(',', '.'))
			});
		});
	
		// Manejo PU diferente ya que le voy a decir que maneje un margen de decimales a considerar
		$('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[precioUnitario\]"]').each(function () {
				var tolerancia = 0.0500;
				var max = parseFloat($(this).val().replace(',', '.'));
				var valor_maximo = max + tolerancia;
				valor_maximo = valor_maximo.toFixed(2);
				$(this).rules('add', {
					valor_maximo: valor_maximo
				});
		});
		
	}
}

function limpiarDatosTipoCambioOC()
{
	$('#seccion_tipo_cambio').hide();
	$('#strTipoMoneda').val('');
	$('#strTipoCambio').val('');
	$('#idTipoMoneda').val('');
}
