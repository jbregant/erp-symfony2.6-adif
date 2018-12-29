
var $formularioComprobanteVenta = $('form[name="adif_contablebundle_comprobanterendicionliquidoproducto"]');

var $busquedaCliente = $('#adif_contablebundle_comprobanterendicionliquidoproducto_cliente');

var $razonSocial = $('#adif_contablebundle_comprobanterendicionliquidoproducto_cliente_razonSocial');

var $cuit = $('#adif_contablebundle_comprobanterendicionliquidoproducto_cliente_cuit');

var $idCliente = $('#adif_contablebundle_comprobanterendicionliquidoproducto_idCliente');

var $tipoComprobanteSelect = $('#adif_contablebundle_comprobanterendicionliquidoproducto_tipoComprobante');

var $letraComprobanteSelect = $('#adif_contablebundle_comprobanterendicionliquidoproducto_letraComprobante');

var $puntoVentaInput = $('#adif_contablebundle_comprobanterendicionliquidoproducto_strPuntoVenta');

var $numeroComprobanteInput = $('#adif_contablebundle_comprobanterendicionliquidoproducto_numero');

var $fechaComprobante = $('#adif_contablebundle_comprobanterendicionliquidoproducto_fechaComprobante');

var $fechaVencimiento = $('#adif_contablebundle_comprobanterendicionliquidoproducto_fechaVencimiento');

var $cantidad = $('#adif_contablebundle_comprobanterendicionliquidoproducto_renglonesComprobante___name___cantidad');

var $precioUnitario = $('#adif_contablebundle_comprobanterendicionliquidoproducto_renglonesComprobante___name___precioUnitario');

var $montoNeto = $('#adif_contablebundle_comprobanterendicionliquidoproducto_renglonesComprobante___name___montoNeto');

var $alicuotaIva = $('#adif_contablebundle_comprobanterendicionliquidoproducto_renglonesComprobante___name___alicuotaIva');

var $montoIva = $('#adif_contablebundle_comprobanterendicionliquidoproducto_renglonesComprobante___name___montoIva');

var $subTotalIva = $('#adif_contablebundle_comprobanteventa_renglonesComprobante_subtotal_iva');

var $subTotalNeto = $('#adif_contablebundle_comprobanteventa_renglonesComprobante_subtotal_neto');

var $total = $('#adif_contablebundle_comprobanterendicionliquidoproducto_total');

var $totalFixed = $('#adif_contablebundle_comprobanteventa_renglonesComprobante_subtotal_fixed');

var $submit = $('#adif_contablebundle_comprobanterendicionliquidoproducto_submit');

$(document).ready(function () {

    initValidate();

    initCalculoAlicuotaIVAHandler();

    initFechaComprobanteValidation();

    initNetoRenglonHandler();

    initMontoIVAHandler();

    initAutocompleteCliente();
	
	initEventAlicuotaIva();

    initSubmitButton();

//    restringir_iva();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {
	
    $('form[name^="adif_contablebundle_comprobante"]').validate({
        ignore: '.ignore'
    });

    
    $puntoVentaInput.inputmask({
        mask: "9999",
        numericInput: true,
        onincomplete: function () {
            $(this).val($(this).val().replace(/_/g, '0'));
        }
    });

    $numeroComprobanteInput.inputmask({
        mask: "99999999",
        numericInput: true,
        onincomplete: function () {
            $(this).val($(this).val().replace(/_/g, '0'));
        }
    });
	

    // Validacion del Formulario
    $formularioComprobanteVenta.validate();
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteCliente() {

	$busquedaCliente.autocomplete({
		source: __AJAX_PATH__ + 'cliente/autocomplete/form',
		minLength: 3,
		select: function (event, ui) {

			completarInformacionCliente(event, ui);

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
function completarInformacionCliente(event, ui) {

    $razonSocial.val(ui.item.razonSocial);
    $cuit.val(ui.item.CUIT);
    $idCliente.val(ui.item.id);
}


/**
 * 
 * @returns {undefined}
 */
function initNetoRenglonHandler() {

    $montoNeto.on('change', function () {

		var tipoCambio = 1;

		var montoNetoFormatted = (clearCurrencyValue($(this).val()) * tipoCambio).toString().replace(/\./g, ',');

		$(this).val(montoNetoFormatted);
	});
}

/**
 * 
 * @returns {undefined}
 */
function initMontoIVAHandler() {

    // Handler para el input de Monto IVA
    $montoIva.on('change', function () {

        $(this).prop('readonly', true);

    });
}

/**
 * 
 * @returns {undefined}
 */
function initAgregarRenglonComprobanteHandler() {
    crearRenglonComprobanteVenta();
}

/**
 * 
 * @returns {undefined}
 */
function initCalculoAlicuotaIVAHandler() {

    $cantidad
            .bind('change paste keyup', function (event) {
                event.stopPropagation();
                calcularAlicuotaIVA();
            });

    $precioUnitario
            .bind('change paste keyup', function (event) {
                event.stopPropagation();
                calcularAlicuotaIVA();
            });
}


/**
 * 
 * @returns {undefined}
 */
function initFechaComprobanteValidation() {

    var currentDate = getCurrentDate();

    $fechaComprobante
            .datepicker('update', currentDate);

    checkFechaComprobanteValidation();
}

/**
 * 
 * @returns {undefined}
 */
function checkFechaComprobanteValidation() {

	var currentDate = getCurrentDate();

	$fechaComprobante
                .val(null);

	$fechaComprobante
                .datepicker('setEndDate', currentDate);
   
   
}


/**
 * 
 * @returns {undefined}
 */
function calcularAlicuotaIVA() {
    var cantidad = parseFloat($cantidad.val());

    var precioUnitario = parseFloat(clearCurrencyValue($precioUnitario.val()));

	var alicuotaValue = parseFloat($alicuotaIva.find('option:selected').html());
	
	var montoNeto = parseFloat(cantidad * precioUnitario);
	
	var montoIva = parseFloat(cantidad * precioUnitario * alicuotaValue / 100);
	$subTotalNeto.val( montoNeto.toString().replace(/\./g, ',') );
	
	$montoIva.val( montoIva.toString().replace(/\./g, ',') );
	$subTotalIva.val( montoIva.toString().replace(/\./g, ',') );
	
	var total = montoNeto + montoIva;
	
	total = isNaN(total) ? 0 : total;
	
	$total.val( total.toString().replace(/\./g, ',') );
	$totalFixed.val( total.toString().replace(/\./g, ',') );
	
    montoNeto = isNaN(montoNeto) ? 0 : montoNeto;

    var montoNetoFormatted = montoNeto.toString().replace(/\./g, ',');

    $montoNeto
            .val(montoNetoFormatted)
            .trigger('change');
}

function initEventAlicuotaIva()
{
	$alicuotaIva.on('change', function(){
		calcularAlicuotaIVA();
	});
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $submit.on('click', function (e) {

        if ($formularioComprobanteVenta.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el comprobante?',
                callbackOK: function () {

                    $formularioComprobanteVenta.submit();
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
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value.replace('$', '').replace(/\./g, '').replace(/\,/g, '.');
}

function setMasks() {
    $('.money-format').each(function () {
        $(this).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
    });
}
