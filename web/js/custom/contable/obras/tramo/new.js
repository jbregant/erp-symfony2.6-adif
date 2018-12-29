
var isEdit = $('[name=_method]').length > 0;

var $formularioTramo = $('form[name="adif_contablebundle_obras_tramo"]');

var fechaAperturaLicitacionDate = getDateFromString($('#licitacion-fecha-apertura').text());

var importeTramoOriginal = parseFloat($('#adif_contablebundle_obras_tramo_totalContrato').val().replace(',', '.'));

$(document).ready(function () {

    initAutoCompleteProveedor();

    initValidate();

    updateDeleteLinks($('.prototype-link-remove-fuente-financiamiento'));
    updateDeleteLinks($('.prototype-link-remove-poliza'));

    initSelects();

    initEstadoTramo();

    initFuenteFinanciamientoForm();

    initPolizaForm();

    initReadOnlyInputs();

    initSubmitButton();
});

/**
 *
 * @returns {undefined}
 */
function initAutoCompleteProveedor() {

    $('#adif_contablebundle_obras_tramo_proveedor').autocomplete({
        source: __AJAX_PATH__ + 'proveedor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            $('#adif_contablebundle_obras_tramo_proveedor_razonSocial').val(ui.item.razonSocial);
            $('#adif_contablebundle_obras_tramo_proveedor_cuit').val(ui.item.CUIT);
            $('#adif_contablebundle_obras_tramo_idProveedor').val(ui.item.id);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };
}

/**
 *
 * @returns {undefined}
 */
function initValidate() {

    // Get saldo de la licitacion
    var saldoLicitacion = parseFloat($('#licitacion-saldo').text().replace(',', '.'));

    // Validacion para fecha de vencimiento de la poliza
    $.validator.addMethod("fechaVencimientoPoliza", function (value, element, param) {

        var fechaVencimientoPoliza = $(element).val();
        var fechaVencimientoPolizaDate = getDateFromString(fechaVencimientoPoliza);

        return  fechaAperturaLicitacionDate.getTime() <= fechaVencimientoPolizaDate.getTime();
    });

    // Validacion para importe del tramo
    $.validator.addMethod("importeTramo", function (value, element, param) {

        var importeTramo = parseFloat($('#adif_contablebundle_obras_tramo_totalContrato').val().replace(/\,/g, '.'));
		
		console.debug("Importe tramo = " + importeTramo);
		console.debug("Saldo Licitacion = " + saldoLicitacion);
		console.debug("Importe tramo original = " + importeTramoOriginal);
		var suma = saldoLicitacion + importeTramoOriginal;
		console.debug("Saldo licitacion + importe tramo = " + suma ); 
		console.debug("Importe tramo es menor o igual a la suma anterior? = " + eval(importeTramo <= saldoLicitacion + importeTramoOriginal) );

        return  importeTramo <= saldoLicitacion + importeTramoOriginal;
    });

    if (isEdit) {

        // Validacion para el estado del tramo
        $.validator.addMethod("estadoTramo", function (value, element, param) {

            // Get estado de tramo seleccionado
            var estadoEsFinalizado = $('#adif_contablebundle_obras_tramo_estadoTramo')
                    .find('option:selected')
                    .html().toLowerCase().indexOf("finalizado") >= 0;

            // Si el estado ES finalizado
            if (estadoEsFinalizado) {

                // Get saldo del tramo
                var saldoTramo = parseFloat($('#adif_contablebundle_obras_tramo_saldo').val().replace(/\,/g, '.'));

                // Get fecha recepcion definitiva
                var $fechaRecepcionDefinitiva = $('#adif_contablebundle_obras_tramo_fechaRecepcionDefinitiva');

                return __saldoTotalDocumentosFinancieros === 0
                        && saldoTramo === 0
                        && $fechaRecepcionDefinitiva.val() !== '';
            }

            return true;
        });
    }

    // Validacion del Formulario
    $formularioTramo.validate();

    // Valido el importe del tramo
    $('#adif_contablebundle_obras_tramo_totalContrato').rules('add', {
        importeTramo: true,
        messages: {
            importeTramo: "El importe del tramo no puede superar el saldo de la licitaci&oacute;n (" + convertToMoneyFormat(saldoLicitacion + importeTramoOriginal) + ")."
        }
    });

    if (isEdit) {

        // Valido el estado del tramo
        $('#adif_contablebundle_obras_tramo_estadoTramo').rules('add', {
            estadoTramo: true,
            messages: {
                estadoTramo: function () {
                    return getMensajeErrorEstadoFinalizado();
                }
            }
        });
    }
}

/**
 *
 * @returns {String}
 */
function getMensajeErrorEstadoFinalizado() {

    var mensajeErrorEstadoFinalizado = 'No puede finalizar el tramo.';

    // Get saldo del tramo
    var saldoTramo = parseFloat($('#adif_contablebundle_obras_tramo_saldo').val().replace(/\,/g, '.'));

    // Get fecha recepcion definitiva
    var $fechaRecepcionDefinitiva = $('#adif_contablebundle_obras_tramo_fechaRecepcionDefinitiva');

    if (saldoTramo > 0) {
        mensajeErrorEstadoFinalizado += ' El saldo del tramo debe ser cero.';
    }

    if (__saldoTotalDocumentosFinancieros > 0) {
        mensajeErrorEstadoFinalizado += ' Existen documentos financieros con saldo.';
    }

    if ($fechaRecepcionDefinitiva.val() === '') {
        mensajeErrorEstadoFinalizado += ' Debe especificar la fecha de recepción definitiva.';
    }

    return mensajeErrorEstadoFinalizado;

}

/**
 *
 * @returns {undefined}
 */
function initEstadoTramo() {

    // Si NO es una edicion
    if (!isEdit) {

        // Busco el option con el texto "Finalizado"
        var idEstadoFinalizadoValue = $('#adif_contablebundle_obras_tramo_estadoTramo').find('option')
                .filter(function () {
                    return  ($(this).html().toLowerCase().indexOf("finalizado") >= 0);
                }).val();

        // Elimino el estado "Finalizado" del select
        $('#adif_contablebundle_obras_tramo_estadoTramo')
                .find('option[value=' + idEstadoFinalizadoValue + ']')
                .remove();
    }
}

/**
 *
 * @returns {undefined}
 */
function initFuenteFinanciamientoForm() {

    collectionHolderFuenteFinanciamiento = $('div.prototype-fuente-financiamiento');

    collectionHolderFuenteFinanciamiento.data('index', collectionHolderFuenteFinanciamiento.find(':input').length);

    $('.prototype-link-add-fuente-financiamiento').on('click', function (e) {
        e.preventDefault();

        addFuenteFinanciamientoForm(collectionHolderFuenteFinanciamiento);

        initSelects();

        initCurrencies();
    });
}


/**
 *
 * @param {type} $collectionHolder
 * @returns {addPolizaForm}
 */
function addFuenteFinanciamientoForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var fuenteFinanciamientoForm = prototype.replace(/__fuente_financiamiento__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-fuente-financiamiento').closest('.row').before(fuenteFinanciamientoForm);

    var $fuenteFinanciamientoDeleteLink = $(".prototype-link-remove-fuente-financiamiento");

    initSelects();

    updateDeleteLinks($fuenteFinanciamientoDeleteLink);

}

/**
 *
 * @returns {undefined}
 */
function initPolizaForm() {

    collectionHolderPoliza = $('div.prototype-poliza');

    collectionHolderPoliza.data('index', collectionHolderPoliza.find(':input').length);

    $('.prototype-link-add-poliza').on('click', function (e) {
        e.preventDefault();

        addPolizaForm(collectionHolderPoliza);

        initFechaVencimientoPolizaValidation();

        initRangoPoliza();

        initCurrencies();
    });
}

/**
 *
 * @param {type} $collectionHolder
 * @returns {addPolizaForm}
 */
function addPolizaForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var polizaForm = prototype.replace(/__poliza__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-poliza').closest('.row').before(polizaForm);

    initDatepickers($('.row_poliza').last());

    var $polizaDeleteLink = $(".prototype-link-remove-poliza");

    updateDeleteLinks($polizaDeleteLink);
}

/**
 *
 * @returns {undefined}
 */
function initFechaVencimientoPolizaValidation() {

    // Validacion del Formulario
    $formularioTramo.validate();

    $('input[id ^= "adif_contablebundle_obras_tramo_polizasSeguro_"][id $= "_fechaVencimiento"]').each(function (e) {
        $(this).rules('add', {
            fechaVencimientoPoliza: true,
            messages: {
                fechaVencimientoPoliza: "La fecha debe ser mayor a la apertura de la licitaci&oacute;n."
            }
        });
    });
}

/**
 *
 * @returns {undefined}
 */
function initRangoPoliza() {

    $('input[id ^= "adif_contablebundle_obras_tramo_polizasSeguro_"][id $= "_fechaVencimiento"]').each(function (e) {

        $(this).datepicker('setStartDate', fechaAperturaLicitacionDate);
    });
}

/**
 *
 * @returns {undefined}
 */
function initReadOnlyInputs() {

    if (isEdit && __tieneDocumentosFinancieros == 1) {

        $('input.no-editable').prop('readonly', true);

        $('select.no-editable').select2('readonly', true);

//        $('.prototype-link-add-poliza').remove();
    }


    // Obtengo el saldo del Tramo
    var $saldoTramo = $('#adif_contablebundle_obras_tramo_saldo').val();

    // Si el saldo no es cero
    if (parseInt($saldoTramo) !== 0) {

        $('#adif_contablebundle_obras_tramo_fechaRecepcionDefinitiva')
                .prop('readonly', true).unbind();
    }
	
	if ($('#adif_contablebundle_obras_tramo_plazoDias').val().trim() == '') {
		$('#adif_contablebundle_obras_tramo_plazoDias').val(0);
	}
}

/**
 *
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_obras_tramo_submit').on('click', function (e) {

        if ($formularioTramo.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el renglón de licitación?',
                callbackOK: function () {

                    if (validForm()) {
                        $formularioTramo.submit();
                    }
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
 * @returns {undefined}
 */
function initSelects() {
    $('select.choice').each(function () {
        $(this).select2({
            allowClear: true
        });
    });
}

/**
 *
 * @returns {undefined}
 */
function validForm() {

    prefix  =   'adif_contablebundle_obras_tramo';
    fIni    =   $("input[id^='"+prefix+"'][id$='fechaInicio']");
    fFin    =   $("input[id^='"+prefix+"'][id$='fechaVencimiento']");

    if ( fIni.length > 0 && fFin.length > 0 ) {
        fIni    =   fIni.val().split('/');
        fIni.reverse();
        fIni    =   fIni.join('/');
        fFin    =   fFin.val().split('/');
        fFin.reverse();
        fFin    =   fFin.join('/');
    }

    if ( ( (parseInt(fIni.length) + parseInt(fFin.length)) == 0) || Date.parse(fIni) < Date.parse(fFin) )
    {
        return validatePorcentajeFuenteFinanciamiento();
    } else {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe seleccionar una fecha de vencimiento posterior a fecha de fin"
        });

        show_alert(options);

        formularioValido = false;
    }

}

/**
 *
 * @returns {undefined}
 */
function validatePorcentajeFuenteFinanciamiento() {

    var $isValid = true;

    if (!$('.row_fuente_financiamiento').length) {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe cargar al menos una fuente de financiamiento."
        });

        show_alert(options);

        $isValid = false;

    } else {

        var $totalPorcentajeFuenteFinanciamiento = 0;

        $('.porcentaje-fuente-financiamiento').each(function () {
            $totalPorcentajeFuenteFinanciamiento += parseFloat(clearCurrencyValue($(this).val()));
        });

        $isValid = ($totalPorcentajeFuenteFinanciamiento === 100);

        if (!$isValid) {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "Los porcentajes de las fuentes de financiamiento deben sumar 100%."
            });

            show_alert(options);
        }
    }

    return $isValid;
}

/**
 *
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value.replace('$', '').replace(/\./g, '').replace(/\,/g, '.');
}