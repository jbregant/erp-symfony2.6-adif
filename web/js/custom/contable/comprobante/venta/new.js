
var importePliego = 0;

var $formularioComprobanteVenta = $('form[name="adif_contablebundle_comprobanteventa"]');

var $tipoComprobanteSelect = $('#adif_contablebundle_comprobanteventa_tipoComprobante');

var $letraComprobanteSelect = $('#adif_contablebundle_comprobanteventa_letraComprobante');

var $rendicionLiquidoProductoPuntoVenta = $('#adif_contablebundle_comprobanterendicionliquidoproducto_puntoVenta');

var $puntoVentaSelect = $('#adif_contablebundle_comprobanteventa_puntoVentaSelect');

var $puntoVentaInput = $('#adif_contablebundle_comprobanteventa_puntoVenta');

var $numeroComprobanteInput = $('#adif_contablebundle_comprobanteventa_numero');

var $numeroCuponInput = $('#adif_contablebundle_comprobanteventa_numeroCupon');

var $diasAtrasoInput = $('#adif_contablebundle_comprobanteventa_diasAtraso');

var $montoInteresInput = $('#adif_contablebundle_comprobanteventa_montoInteres');

var $porcentajeTasaInteresMensualInput = $('#adif_contablebundle_comprobanteventa_porcentajeTasaInteresMensual');

$(document).ready(function () {

    initValidate();

    initTipoComprobanteValues();

    initTipoComprobanteHandler();

    initAgregarRenglonComprobanteHandler();

    initAgregarPercepcionHandler();

    initPuntoVentaSelectHandler();

    initCalculoAlicuotaIVAHandler();

    initFechaComprobanteValidation();

    initLetraComprobante();

    initNetoRenglonHandler();

    initMontoIVAHandler();

    initCalcularInteresesHandler();

    initReadOnlyInputs();

    //initAutocompleteLicitacion();

    initAutocompleteCliente();

    initSubmitButton();

    restringir_iva();

    initComprobanteCancelables();
    
    initFiltroLicitacion();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $formularioComprobanteVenta.validate();
}

/**
 * 
 * @returns {undefined}
 */
function initReadOnlyInputs() {

    $letraComprobanteSelect.select2('readonly', true);

    $numeroComprobanteInput.prop('readonly', true);

    $numeroCuponInput.prop('readonly', true);

    $('select[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _alicuotaIva]')
            .select2('readonly', true);

    //$('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _neto]')
    $('input[id^=adif_contablebundle_comprobanteventa_renglonesComprobante_][id $=_neto]:not([id $=_subtotal_neto])')
            .prop('readonly', true)
            .val(0);

    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _montoIva]')
            .prop('readonly', true)
            .val(0);

    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
            .val(0)
            .trigger('change');

    // Si aplica alicuota de IIBB
    if (__alicuotaIIBB !== 0 || __alicuotaPercepcionIVA !== 0) {

        $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _conceptoPercepcion]')
                .select2('readonly', true);

        $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _jurisdiccion]')
                .select2('readonly', true);

        $('input[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _monto]')
                .prop('readonly', true);
    }

    // Si el comprobante es para un pliego
    if (__esPliegoObra === 1 || __esPliegoCompra === 1) {

        $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _cantidad]')
                .prop('readonly', true);
    }

    if (__esPliegoObra === 1 || __esPliegoCompra === 1 || __esVentaGeneral === 1) {

        $('#adif_contablebundle_comprobanteventa_esCuponGarantia')
                .bootstrapSwitch('setState', false);

        $('#adif_contablebundle_comprobanteventa_esCuponGarantia')
                .parents('.has-switch').block({
            message: null, overlayCSS: {
                backgroundColor: 'black',
                opacity: 0.05,
                cursor: 'not-allowed'}
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateMontoComprobanteLicitacion() {

    // Si el comprobante es para un pliego 
    if (__esPliegoObra === 1 || __esPliegoCompra === 1) {

        $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
                .val(importePliego).trigger('change');

        //$('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _neto]')
        //$('input[id^=adif_contablebundle_comprobanteventa_renglonesComprobante_][id $=_neto]:not([id $=_subtotal_neto])').val(importePliego).trigger('change');

        //$('#adif_contablebundle_comprobanteventa_total').val(importePliego);
    }
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteLicitacion() {

    // Si el comprobante es para un pliego de obra
    if (__esPliegoObra === 1) {

        $('#adif_contablebundle_comprobanteventa_licitacion').autocomplete({
            source: __AJAX_PATH__ + 'licitacion_obra/autocomplete/form',
            minLength: 1,
            select: function (event, ui) {

                completarInformacionLicitacion(event, ui);

                updateMontoComprobanteLicitacion();
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<a>" + item.numero + "</a>")
                    .appendTo(ul);
        };
    }

    // Si el comprobante es para un pliego de compra
    if (__esPliegoCompra === 1) {

        $('#adif_contablebundle_comprobanteventa_licitacion').autocomplete({
            source: __AJAX_PATH__ + 'licitacion_compra/autocomplete/form',
            minLength: 1,
            select: function (event, ui) {

                completarInformacionLicitacion(event, ui);

                updateMontoComprobanteLicitacion();
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<a>" + item.numero + "</a>")
                    .appendTo(ul);
        };
    }
}

function initFiltroLicitacion()
{
    $("#filtrar_licitacion").on('click', function(event){
        var tipoContratacion = $("#adif_contablebundle_comprobanteventa_tipoContratacion_name_alias").val().trim();
        var numero = $("#adif_contablebundle_comprobanteventa_numero_licitacion_busqueda").val().trim();
        var anio = $("#adif_contablebundle_comprobanteventa_anio_licitacion_busqueda").val().trim();
        
        if (tipoContratacion == '' || numero == '' || anio == '') {
            importePliego = 0;
            updateMontoComprobanteLicitacion();
            $("#adif_contablebundle_comprobanteventa_numero_licitacion").val('');
            $("#adif_contablebundle_comprobanteventa_idLicitacion").val(null);
            var opciones = {
                msg: 'El tipo de contración, número y año son obligatorios para la busqueda.'
            };
            show_alert(opciones);
          
            return false;
        } 
        
        var data = {
            tipoContratacion: tipoContratacion,
            numero: numero,
            anio: anio
        };
        
        var url = null;
        // Si el comprobante es para un pliego de obra
        if (__esPliegoObra === 1) {
            url = __AJAX_PATH__ + 'licitacion_obra/getLicitacionObraByTipoContratacionAndNumeroAndAnio'; 
           
        }

        // Si el comprobante es para un pliego de compra
        if (__esPliegoCompra === 1) {
            url = __AJAX_PATH__ + 'licitacion_compra/getLicitacionCompraByTipoContratacionAndNumeroAndAnio';
        }
        
        $.ajax({
            type: 'post',
            url: url,
            data: data,
            success: function(response) { 
                if (response == '') {
                    importePliego = 0;
                    updateMontoComprobanteLicitacion();
                    $("#adif_contablebundle_comprobanteventa_numero_licitacion").val('');
                    $("#adif_contablebundle_comprobanteventa_idLicitacion").val(null);
                    var opciones = {
                        msg: 'No existe la licitación para la busqueda solicitada.'
                    };
                    show_alert(opciones);
                    return false;
                }
                completarInformacionLicitacionBotonFiltrado(response);
                updateMontoComprobanteLicitacion();
            }
        });
    });
    
}

/**
 * 
 * @param {type} event
 * @param {type} ui
 * @returns {undefined}
 */
function completarInformacionLicitacion(event, ui) {

    $('#adif_contablebundle_comprobanteventa_numero_licitacion').val(ui.item.numero);
    $('#adif_contablebundle_comprobanteventa_idLicitacion').val(ui.item.id);

    importePliego = (ui.item.importePliego).toString().replace('.', ',');
}

/**
 * 
 * @param {type} ui
 * @returns {undefined}
 */
function completarInformacionLicitacionBotonFiltrado(response) {

    $('#adif_contablebundle_comprobanteventa_numero_licitacion').val(response.tipoContratacion + ' ' + response.numero + '/' + response.anio);
    $('#adif_contablebundle_comprobanteventa_idLicitacion').val(response.id);

    importePliego = (response.importePliego).toString().replace('.', ',');
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteCliente() {

    // Si el comprobante indica Cliente
    if (__esPliegoObra === 1 || __esPliegoCompra === 1 || __esVentaGeneral === 1) {

        $('#adif_contablebundle_comprobanteventa_cliente').autocomplete({
            source: __AJAX_PATH__ + 'cliente/autocomplete/form',
            minLength: 3,
            select: function (event, ui) {

                completarInformacionCliente(event, ui);

                updateLetraComprobante();

                updatePercepciones();
                
                if ( $('#adif_contablebundle_comprobanteventa_tipoComprobante').val() == 7 ) {
                    // Si es cupon
                    getContratosCliente();
                }
                
                
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                    .appendTo(ul);
        };
    }
}

/**
 * 
 * @param {type} event
 * @param {type} ui
 * @returns {undefined}
 */
function completarInformacionCliente(event, ui) {

    $('#adif_contablebundle_comprobanteventa_cliente_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_comprobanteventa_cliente_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_comprobanteventa_idCliente').val(ui.item.id);
}

/**
 * 
 * @returns {undefined}
 */
function updateLetraComprobante() {

    var data = {
        idCliente: $('#adif_contablebundle_comprobanteventa_idCliente').val()
    };

    if(__esVentaGeneral === 1 && $tipoComprobanteSelect.val() && $tipoComprobanteSelect.val() == 9){
        $letraComprobanteSelect.select2("val", 1).trigger('change');
    } else {
        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'cliente/letra_comprobante',
            data: data
        }).done(function (letraComprobante) {

            var letraValue = $letraComprobanteSelect.find('option')
                    .filter(function () {
                        return ($(this).html().indexOf(letraComprobante) >= 0);
                    }).val();

            $letraComprobanteSelect.select2("val", letraValue).trigger('change');
        });
    }

}

/**
 * 
 * @returns {undefined}
 */
function updatePercepciones() {

    var data = {
        idCliente: $('#adif_contablebundle_comprobanteventa_idCliente').val()
    };

    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'cliente/percepciones',
        data: data
    }).done(function (percepciones) {

        if (percepciones['alicuota_iibb'] > 0) {
            __alicuotaIIBB = percepciones['alicuota_iibb'];
        }

        if (percepciones['alicuota_iva'] > 0) {
            __alicuotaPercepcionIVA = percepciones['alicuota_iva'];
        }

        initAgregarPercepcionHandler();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initLetraComprobante() {

    var letraValue = $letraComprobanteSelect.find('option')
            .filter(function () {
                return ($(this).html().indexOf(__letraComprobante) >= 0);
            }).val();

    if ( $tipoComprobanteSelect.val() == 9 ) {
        $letraComprobanteSelect.select2("val", 1).trigger('change');
    } else {
        $letraComprobanteSelect.select2("val", letraValue).trigger('change');
    }
}

/**
 * 
 * @returns {undefined}
 */
function initNetoRenglonHandler() {

    //$('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _neto]')
    $('input[id^=adif_contablebundle_comprobanteventa_renglonesComprobante_][id $=_neto]:not([id $=_subtotal_neto])')
            .on('change', function () {

                updateNetoPercepcionIIBB();

                updateMontoIntereses();

                var tipoCambio = clearCurrencyValue($('#adif_contablebundle_comprobanteventa_tipoCambio').val());

                var montoNetoFormatted = (clearCurrencyValue($(this).val()) * tipoCambio).toString().replace(/\./g, ',');

                $(this).val(montoNetoFormatted);
            });
}

/**
 * 
 * @returns {undefined}
 */
function initAgregarPercepcionHandler() {

    // Si aplica alicuota de IIBB
    if (__alicuotaIIBB !== 0) {

        crear_renglon_percepcion_impuesto('percepcion');

        // Init select de concepto de percepción
        var $conceptoPercepcionSelect = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _conceptoPercepcion]');

        var conceptoValue = $conceptoPercepcionSelect.find('option')
                .filter(function () {
                    return ($(this).html().indexOf(__percepcionIIBB) >= 0);
                }).val();

        $conceptoPercepcionSelect.select2("val", conceptoValue);

        // Init select de jurisdicción de percepción
        var $jurisdiccionPercepcionSelect = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _jurisdiccion]');

        var jurisdiccionValue = $jurisdiccionPercepcionSelect.find('option')
                .filter(function () {
                    return ($(this).html().indexOf("CABA") >= 0);
                }).val();

        $jurisdiccionPercepcionSelect.select2("val", jurisdiccionValue);
    }

    // Si aplica alicuota de IVA
    if (__alicuotaPercepcionIVA !== 0) {
        crear_renglon_percepcion_impuesto('percepcion');

        // Init select de concepto de percepción
        //var $conceptoPercepcionSelect = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _conceptoPercepcion]');        

        var $contenedor = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _conceptoPercepcion]:not([sname]) option:contains("IIBB"):not(:selected)').parents('.row_renglon_percepcion');

        var $conceptoPercepcionIVASelect = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _conceptoPercepcion]:not([sname]) option:contains("IIBB"):not(:selected)').parent();

        var conceptoValue = $conceptoPercepcionIVASelect.find('option')
                .filter(function () {
                    return ($(this).html().indexOf(__percepcionIVA) >= 0);
                }).val();

        $conceptoPercepcionIVASelect.select2("val", conceptoValue);

        var $jurisdiccionPercepcionSelect = $contenedor.find('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _jurisdiccion]').parent().hide().select2("val", '');

        // Init select de jurisdicción de percepción
//        var $jurisdiccionPercepcionSelect = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _jurisdiccion]');
//
//        var jurisdiccionValue = $jurisdiccionPercepcionSelect.find('option')
//                .filter(function () {
//                    return ($(this).html().indexOf("CABA") >= 0);
//                }).val();
//
//        $jurisdiccionPercepcionSelect.select2("val", jurisdiccionValue);
    }
}

/**
 * 
 * @returns {undefined}
 */
function initTipoComprobanteValues() {

    // Si el contrato es de alquiler
    if (__esContratoAlquiler == 1) {

        $tipoComprobanteSelect
                .find('option[value="' + __tipoComprobanteFactura + '"]')
                .remove();
    }

    // Si el contrato es de Venta de Plazo
    if (__idClaseContrato === __contratoVentaPlazo) {

        $tipoComprobanteSelect
                .find('option[value!="' + __tipoComprobanteCupon + '"][value!="' + __tipoComprobanteNotaDebitoIntereses + '"]')
                .remove();
    }
}

/**
 * 
 * @returns {undefined}
 */
function initTipoComprobanteHandler() {

    $tipoComprobanteSelect.on('change', function () {

        $('.mensaje-electronico').hide();

        $('.nota-credito-cancela').hide();
        $('#adif_contablebundle_comprobanteventa_cancelaCuota').bootstrapSwitch('setState', false);
        $('#table_cuotas_cancelables tbody').empty();
        $('.cuotas-cancelables-data').hide();
        $('.ctn_rows_renglon_percepcion').html('');
        $('#adif_contablebundle_comprobanteventa_comprobanteCancelado').val('');
        initAgregarPercepcionHandler();

        // Si el tipo de comprobante es "Cupon"
        if ($(this).val() === __tipoComprobanteCupon) {

            $('.datos-no-cupon').hide();
            $('.datos-no-cupon').find('select, input').prop('required', false);
            $('.datos-no-cupon').find('select').select2("val", "");
            $('.datos-no-cupon').find('select, input').keyup();
            $('.datos-rendicion-liquido-producto').hide();
            $('#div_adif_contablebundle_comprobanterendicionliquidoproducto_puntoVenta').hide();
            $('.datos-rendicion-liquido-producto').find('select, input').prop('required', false);
            $('.datos-rendicion-liquido-producto').keyup();

            $('.datos-cupon').find('input:not(":checkbox")').prop('required', true);
            $('.datos-cupon').show();

            if (__esVentaGeneral === 1) {
                $('.datos-cupon-general').show();
            } else {
                $('.datos-cupon-general').hide();
            }

            updateSiguienteNumeroCupon();

            $('.percepciones-content').addClass('hidden');

        } else {

            $('.datos-cupon').hide();
            $('.datos-cupon-general').hide();

            $('#adif_contablebundle_comprobanteventa_letraComprobante').val(null);

            $('.datos-cupon').find('input').prop('required', false);
            $('.datos-cupon').find('input').keyup();

            $numeroCuponInput.val(null);

            $('.datos-no-cupon').find('select, input').prop('required', true);
            $('.datos-no-cupon').find('select').select2();
            $('.datos-no-cupon').show();

            if ( __esVentaGeneral === 1 && $tipoComprobanteSelect.val() && $tipoComprobanteSelect.val() == 9 ) {
                $('#adif_contablebundle_comprobanteventa_puntoVenta').parent().hide();
                $puntoVentaInput.prop('required', false);
                $puntoVentaSelect.prop('required', false);
                $('#div_adif_contablebundle_comprobanterendicionliquidoproducto_puntoVenta').show();
                $('#adif_contablebundle_comprobanterendicionliquidoproducto_puntoVenta').find('input').prop('required', true);
            }

            $('.percepciones-content').removeClass('hidden');
        }

        // Si el tipo de comprobante es "Nota débito intereses"
        if ($(this).val() === __tipoComprobanteNotaDebitoIntereses) {

            $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _cantidad]')
                    .prop('readonly', true);

            $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
                    .val(0).prop('readonly', true);

            $('.datos-debito-interes').find('input').prop('required', true);

            $diasAtrasoInput.val(0);
            $montoInteresInput.val(0);

            $('.datos-debito-interes').show();
        }
        else {

            // Si el comprobante NO es para un pliego
            if (__esPliegoObra !== 1 && __esPliegoCompra !== 1) {

                $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _cantidad]')
                        .prop('readonly', false);

                $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
                        .prop('readonly', false);
            }

            $('.datos-debito-interes').hide();
            $('.datos-debito-interes').find('input').prop('required', false);
            $('.datos-debito-interes').find('input').keyup();

            $diasAtrasoInput.val(null);
            $montoInteresInput.val(null);
        }

        checkFechaComprobanteValidation();

        initLetraComprobante();

        calcularAlicuotaIVA();

        // Si el comprobante es para un pliego de obra o de compra
        if (__esPliegoObra === 1 || __esPliegoCompra === 1) {

            updateMontoComprobanteLicitacion();

            if ($('#adif_contablebundle_comprobanteventa_idCliente').val()) {
                updateLetraComprobante();
            }
        }

        if (__esVentaGeneral === 1 && $('#adif_contablebundle_comprobanteventa_idCliente').val()) {
            if( $tipoComprobanteSelect.val() == 9 ){
                $letraComprobanteSelect.select2("val", 1).trigger('change');
                $puntoVentaSelect.select2('readonly', false);
                $numeroComprobanteInput.prop('readonly', false);
            }
            updateLetraComprobante();
        }

        // si el comprobante es nota de credito
        if ((__esContratoAlquiler) && ($(this).val() === __tipoComprobanteNotaCredito)) {
            $('.nota-credito-cancela').show();
        }

    }).trigger('change');
}

/**
 * 
 * @returns {undefined}
 */
function initMontoIVAHandler() {

    // Handler para el input de Monto IVA
    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _montoIva]').on('change', function () {

        $(this).prop('readonly', true);

    });
}

/**
 * 
 * @returns {undefined}
 */
function initCalcularInteresesHandler() {

    // Handler para el input de días de atraso
    $diasAtrasoInput.on('change paste keyup', function () {
        updateMontoIntereses();
    });

    // Handler para el input de monto de interes
    $montoInteresInput.on('change paste keyup', function () {
        updateMontoIntereses();
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateMontoIntereses() {

    // Si el comprobante es una Nota de débito de intereses
    if ($tipoComprobanteSelect.val() === __tipoComprobanteNotaDebitoIntereses) {

        var porcentajeTasaInteresMensual = $porcentajeTasaInteresMensualInput.val();
        
        var porcentajeDiario = parseFloat((porcentajeTasaInteresMensual / 30).toFixed(3));

        var porcentajeMontoInteres = (porcentajeDiario * $diasAtrasoInput.val()) / 100;

        var montoInteres = parseFloat($montoInteresInput.val()) * parseFloat(porcentajeMontoInteres);

        var montoInteresFormatted = montoInteres.toString().replace(/\./g, ',');

        //$('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _neto]')

        //Estaba antes 

        $('input[id^=adif_contablebundle_comprobanteventa_renglonesComprobante_][id $=_neto]:not([id $=_subtotal_neto])')
                .val(montoInteresFormatted);
        $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
                .val(montoInteresFormatted);

        if (montoInteres > 0) {

            recalcular_subtotal_neto();
            updateNetoPercepcionIIBB();
        }
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateSiguienteNumeroCupon() {

    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'comprobanteventa/siguiente_numero_cupon'
    }).done(function (numeroCupon) {

        $numeroCuponInput.val(numeroCupon);
    });
}

/**
 * 
 * @returns {undefined}
 */
function initPuntoVentaSelectHandler() {

    // Handler para el select de TipoComprobante
    $tipoComprobanteSelect.on('change', function () {

        updatePuntoVentaSelect();
        updateNumeroComprobante();

    }).trigger('change');


    // Handler para el select de LetraComprobante
    $letraComprobanteSelect.on('change', function () {

        updatePuntoVentaSelect();
        updateNumeroComprobante();

    }).trigger('change');


    // Handler para el select de PuntoVenta
    $puntoVentaSelect.on('change', function () {

        $puntoVentaInput.val($(this).val());

        updateNumeroComprobante();

    }).trigger('change');


    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _cantidad]')
            .bind('focusout', function (event) {

                // Si el tipo de comprobante NO es "Cupon"
                if ($tipoComprobanteSelect.val() !== __tipoComprobanteCupon) {
                    event.stopPropagation();
                    updatePuntoVentaSelect();
                }
            });

    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
            .bind('focusout', function (event) {

                // Si el tipo de comprobante NO es "Cupon"
                if ($tipoComprobanteSelect.val() !== __tipoComprobanteCupon) {
                    event.stopPropagation();
                    updatePuntoVentaSelect();
                }
            });

    $('#adif_contablebundle_comprobanteventa_diasAtraso').bind('focusout', function (event) {

        // Si el comprobante es una Nota de débito de intereses
        if ($tipoComprobanteSelect.val() === __tipoComprobanteNotaDebitoIntereses) {

            var precioUnitario = parseFloat($('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
                    .val().replace(',', '.'));

            if (precioUnitario > 0) {
                event.stopPropagation();
                updatePuntoVentaSelect();
            }
        }
    });

    $('#adif_contablebundle_comprobanteventa_montoInteres').bind('focusout', function (event) {

        // Si el comprobante es una Nota de débito de intereses
        if ($tipoComprobanteSelect.val() === __tipoComprobanteNotaDebitoIntereses) {

            var precioUnitario = parseFloat($('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
                    .val().replace(',', '.'));

            if (precioUnitario > 0) {
                event.stopPropagation();
                updatePuntoVentaSelect();
            }
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function updatePuntoVentaSelect() {

    if ($tipoComprobanteSelect.val() !== __tipoComprobanteCupon) {

        var puntoVentaValue = 0;

        if (( $tipoComprobanteSelect.val() && $tipoComprobanteSelect.val() != 9 ) && $letraComprobanteSelect.val()) {

            //var montoNeto = $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _neto]')
            var montoNeto = $('input[id^=adif_contablebundle_comprobanteventa_renglonesComprobante_][id $=_neto]:not([id $=_subtotal_neto])')
                    .val();

            var data = {
                claseContrato: __idClaseContrato,
                montoNeto: montoNeto,
                tipoComprobante: $tipoComprobanteSelect.val(),
                letraComprobante: $letraComprobanteSelect.val()
            };

            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'comprobanteventa/puntos_venta',
                data: data
            }).done(function (puntosVentaResponse) {


                puntosVenta = puntosVentaResponse['puntos_venta'];

                $puntoVentaSelect.find('option').remove();

                $puntoVentaInput.val(null);

                $puntoVentaSelect
                        .append('<option value="">-- Punto venta --</option>');

                for (var i = 0, total = puntosVenta.length; i < total; i++) {

                    $puntoVentaSelect
                            .append(
                                    '<option value="' + puntosVenta[i].id + '">' + puntosVenta[i].numero + '</option>'
                                    );
                    puntoVentaValue = puntosVenta[i].id;
                }
                if (puntoVentaValue) {
                    if (puntosVentaResponse['electronico']) {
                        $('#adif_contablebundle_comprobanteventa_numero').parents('.datos-no-cupon').hide();
                        $('.mensaje-electronico').show();
                        $numeroComprobanteInput.prop('required', false);
                    } else {
                        $('.mensaje-electronico').hide();
                        $('#adif_contablebundle_comprobanteventa_numero').parents('.datos-no-cupon').show();
                        $numeroComprobanteInput.prop('required', true);
                    }
                    $puntoVentaSelect.select2("val", puntoVentaValue).trigger('change');
                    updateNumeroComprobante();
                }
                else {
                    $puntoVentaSelect.select2();
                }

                $puntoVentaSelect.select2('readonly', true);

                $numeroComprobanteInput.val(null);
            });
        } else {
            $puntoVentaSelect.select2().hide();
            $rendicionLiquidoProductoPuntoVenta.show();
        }
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateNumeroComprobante() {

    if ($('#adif_contablebundle_comprobanteventa_numero:visible').length > 0) {

        if (($tipoComprobanteSelect.val() && $tipoComprobanteSelect.val() != 9) && $letraComprobanteSelect.val() && $puntoVentaSelect.val()) {

            var data = {
                tipoComprobante: $tipoComprobanteSelect.val(),
                letraComprobante: $letraComprobanteSelect.val(),
                puntoVenta: $puntoVentaSelect.val()
            };

            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'comprobanteventa/siguiente_numero_comprobante',
                data: data
            }).done(function (numeroComprobante) {

                $numeroComprobanteInput.val(numeroComprobante);
            });
        } else {
            $numeroComprobanteInput.val(null);
        }
    }
    else {
        $numeroComprobanteInput.val(null);
    }
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

    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _cantidad]')
            .bind('change paste keyup', function (event) {
                event.stopPropagation();
                calcularAlicuotaIVA();
            });

    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
            .bind('change paste keyup', function (event) {
                event.stopPropagation();
                calcularAlicuotaIVA();
            });
}

/**
 * 
 * @param {type} descripcion
 * @param {type} cantidad
 * @param {type} precioUnitario
 * @param {type} montoNeto
 * @param {type} idAlicuotaIva
 * @param {type} montoIva
 * @returns {undefined}
 */
function crearRenglonComprobanteVenta(descripcion, cantidad, precioUnitario, montoNeto, idAlicuotaIva, montoIva) {

    var nuevoRow =
            $('.row_renglon_comprobante_nuevo')
            .clone()
            .show()
            .removeClass('row_renglon_comprobante_nuevo');

    nuevoRow.addClass('row_renglon_comprobante');

    nuevoRow.find('.ignore').removeClass('ignore');

    nuevoRow.find('[sname]').each(function () {
        $(this).attr('name', $(this).attr('sname'));
        $(this).removeAttr('sname');
    });

    var maximoIndice = 0;

    $('.row_renglon_comprobante').each(function () {
        var value = parseFloat($(this).attr('indice'));
        maximoIndice = (value > maximoIndice) ? value : maximoIndice;
    });

    var indiceNuevo = maximoIndice + 1;

    nuevoRow.html(nuevoRow.html().replace(/__name__/g, indiceNuevo));
    nuevoRow.attr('indice', indiceNuevo);
    nuevoRow.appendTo('.ctn_rows_renglon_comprobante');

    var row_sel_prefix = '#adif_contablebundle_comprobanteventa_renglonesComprobante_';

    nuevoRow.find(row_sel_prefix + indiceNuevo + '_descripcion').val(descripcion ? descripcion : '');
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_cantidad').val(cantidad ? cantidad : 1);
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_precioUnitario').val(precioUnitario ? precioUnitario : 0);
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_montoNeto').val(montoNeto ? montoNeto : 0);

    if (idAlicuotaIva) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_alicuotaIva').val(idAlicuotaIva);
    }

    nuevoRow.find(row_sel_prefix + indiceNuevo + '_montoIva').val(montoIva ? montoIva : 0);

    nuevoRow.find('select').select2();

    $('.row_headers_renglon_comprobante').show();
    $('.row_footer_renglon_comprobante').show();

    initCurrencies();

    $('.row_renglon_comprobante_nuevo').remove();
}


/**
 * 
 * @returns {undefined}
 */
function initFechaComprobanteValidation() {

    var currentDate = getCurrentDate();

    $('#adif_contablebundle_comprobanteventa_fechaComprobante')
            .datepicker('update', currentDate);

    checkFechaComprobanteValidation();
}

/**
 * 
 * @returns {undefined}
 */
function checkFechaComprobanteValidation() {

    var currentDate = getCurrentDate();

    if ($tipoComprobanteSelect.val() === __tipoComprobanteCupon) {

        $('#adif_contablebundle_comprobanteventa_fechaComprobante')
                .datepicker('setEndDate', null);
    }
    else {

        $('#adif_contablebundle_comprobanteventa_fechaComprobante')
                .val(null);

        $('#adif_contablebundle_comprobanteventa_fechaComprobante')
                .datepicker('setEndDate', currentDate);
    }
}


/**
 * 
 * @returns {undefined}
 */
function calcularAlicuotaIVA() {
    var cantidad = parseFloat($('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _cantidad]')
            .val());

    var precioUnitario = parseFloat(clearCurrencyValue($('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]').val()));

    var montoNeto = parseFloat(cantidad * precioUnitario);

    var calculaIva = __calculaIVA;

    if ($tipoComprobanteSelect.val() === __tipoComprobanteCupon) {
        // Si el tipo de comprobante es "Cupon"
        calculaIva = false;
//    } else {
//        if (__esContratoAlquiler == 1) {
//            // Si el monto está seteado y es menor a 1500
//            if (montoNeto && parseFloat(montoNeto) < 1500) {
//                calculaIva = false;
//            }
//        }
    }

    if (calculaIva) {
        // Seteo alicuota
        var alicuotaValue = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _alicuotaIva] option')
                .filter(function () {
                    return ($(this).html().indexOf(__alicuotaIVA) >= 0);
                }).val();

        $('select[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _alicuotaIva]').select2("val", alicuotaValue).trigger('change');
    } else {
        var alicuotaValue = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _alicuotaIva] option')
                .filter(function () {
                    return ($(this).html().indexOf("0") >= 0);
                }).val();

        $('select[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _alicuotaIva]')
                .select2("val", alicuotaValue).trigger('change');
    }

    montoNeto = isNaN(montoNeto) ? 0 : montoNeto;

    var montoNetoFormatted = montoNeto.toString().replace(/\./g, ',');

    $('input[id^=adif_contablebundle_comprobanteventa_renglonesComprobante_][id $=_neto]:not([id $=_subtotal_neto])')
            .val(montoNetoFormatted)
            .trigger('change');
}

/**
 * 
 * @returns {undefined}
 */
function updateNetoPercepcionIIBB() {

    // Si aplica alicuota de IIBB
    if (__alicuotaIIBB !== 0) {

        var $contenedor = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _conceptoPercepcion]:not([sname]) option:contains("IIBB"):selected')
                .parents('.row_renglon_percepcion');

        var $inputNetoPercepcion = $contenedor
                .find('input[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _monto]');

        var montoNetoPercepcionFormatted = 0;

        // Si el comprobante NO es un Cupon
        if ($tipoComprobanteSelect.val() !== __tipoComprobanteCupon) {

            var precioUnitario = parseFloat($('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
                    .val().replace(',', '.'));

            var cantidad = parseFloat($('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _cantidad]')
                    .val());

            var montoNetoPercepcion = parseFloat(parseFloat(precioUnitario * cantidad * (__alicuotaIIBB / 100)).toFixed(3));

            montoNetoPercepcionFormatted = montoNetoPercepcion.toString().replace(/\./g, ',');
        }

        $inputNetoPercepcion.val(montoNetoPercepcionFormatted).trigger('change');
    }

    // Si aplica alicuota de IVA
    if (__alicuotaPercepcionIVA !== 0) {

        var $contenedor = $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _conceptoPercepcion]:not([sname]) option:contains("IIBB"):not(:selected)').parents('.row_renglon_percepcion');
        var $inputNetoPercepcion = $contenedor.find('input[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _monto]');


        var montoNetoPercepcionFormatted = 0;

        // Si el comprobante NO es un Cupon
        if ($tipoComprobanteSelect.val() !== __tipoComprobanteCupon) {

            var precioUnitario = parseFloat($('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]')
                    .val().replace(',', '.'));

            var cantidad = parseFloat($('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _cantidad]')
                    .val());

            var montoIva = parseFloat($('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _montoIva]')
                    .val());

            var montoNetoPercepcion = ((precioUnitario * cantidad) + montoIva) * __alicuotaPercepcionIVA.toFixed(3);

            montoNetoPercepcionFormatted = montoNetoPercepcion.toString().replace(/\./g, ',');
        }

        $inputNetoPercepcion.val(montoNetoPercepcionFormatted).trigger('change');
    }

}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_comprobanteventa_submit').on('click', function (e) {

        if ($formularioComprobanteVenta.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el comprobante?',
                callbackOK: function () {

                    // Si el comprobante es una Nota de débito de intereses
                    if ($tipoComprobanteSelect.val() === __tipoComprobanteNotaDebitoIntereses) {

                        var json = {
                            porcentajeTasaInteresMensual: $porcentajeTasaInteresMensualInput.val(),
                            diasAtraso: $diasAtrasoInput.val(),
                            montoInteres: $montoInteresInput.val()
                        };

                        $formularioComprobanteVenta.addHiddenInputData(json);
                    }

                    // Si el comprobante es un Cupon
                    if ($tipoComprobanteSelect.val() === __tipoComprobanteCupon) {

                        $('.row_renglon_percepcion').remove();
                    }

                    __alicuotaIVA = parseFloat($('select[id ^= "adif_contablebundle_comprobante"][id $= "renglonesComprobante_1_alicuotaIva"] > option:selected').html());

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

function initComprobanteCancelables() {
    checkNotaCredito();
    $('#adif_contablebundle_comprobanteventa_cancelaCuota').on('switch-change', function () {
        checkNotaCredito();
    });
}

function checkNotaCredito() {
    if ((__esContratoAlquiler) && ($tipoComprobanteSelect.val() === __tipoComprobanteNotaCredito)) {
        if ($('#adif_contablebundle_comprobanteventa_cancelaCuota').is(':checked')) {
            buscarCuotasCancelables();
        }
        else {
            $('#table_cuotas_cancelables tbody').empty();
            $('.cuotas-cancelables-data').hide();
        }
    }
}

function buscarCuotasCancelables() {
    $.ajax({
        url: __AJAX_PATH__ + 'contrato/cuotas_cancelables/',
        data: {id_contrato: __idContrato}
    }).done(function (cuotasCancelables) {
        generarReenglonesCuotas(cuotasCancelables);
    });
}

function generarReenglonesCuotas(cuotasCancelables) {
    $('#table_cuotas_cancelables tbody').empty();

    if (cuotasCancelables.length > 0) {

        $('.cuotas-cancelables-data').show();

        $.each(cuotasCancelables, function (index) {

            var row = cuotasCancelables[index];

            var $tr = $('<tr />', {
                id_comprobante: row['id'],
                precio_unitario: row['montoNeto'],
                monto_iva: row['montoIVA'],
                id_alicuota_IVA: row['idAlicuotaIva'],
                monto_perc_IIBB: row['montoPercIIBB'],
                monto_perc_IVA: row['montoPercIVA'],
                total: row['total'],
                id_renglon: index,
                numero_cuota: row['numeroCuota'],
                style: 'cursor: pointer;'}
            );

            $tr.on('click', function () {
                $(this).parents('tbody').find('tr').removeClass('active');
                $(this).addClass('active');
                actualizarCamposNotaCredito($(this));
            });

            $('<td />', {text: row['fecha']})
                    .addClass('nowrap')
                    .appendTo($tr);

            $('<td />', {text: row['tipo']})
                    .appendTo($tr);

            $('<td />', {text: row['letra']})
                    .appendTo($tr);

            $('<td />', {text: row['numero']})
                    .addClass('nowrap')
                    .appendTo($tr);

            $('<td />', {text: row['observaciones']})
                    .appendTo($tr);

            $('<td />', {text: row['numeroCuota']})
                    .appendTo($tr);

            $('<td />', {text: row['montoNeto']})
                    .addClass('money-format nowrap')
                    .appendTo($tr);

            $('<td />', {text: row['montoIVA']})
                    .addClass('money-format nowrap')
                    .appendTo($tr);

            $('<td />', {text: row['montoPercIIBB']})
                    .addClass('money-format nowrap')
                    .appendTo($tr);

            $('<td />', {text: row['montoPercIVA']})
                    .addClass('money-format nowrap')
                    .appendTo($tr);

            $('<td />', {text: row['total']})
                    .addClass('money-format nowrap')
                    .appendTo($tr);
//
//        $('<td />', {text: row['totalOrigen']})
//                .addClass('money-format nowrap')
//                .appendTo($tr);

            $('#table_cuotas_cancelables tbody').append($tr);

            setMasks();

        });
    } else {
        $('.cuotas-cancelables-data').hide();
        $('#adif_contablebundle_comprobanteventa_cancelaCuota').bootstrapSwitch('setState', false);
        show_alert({msg: 'No existen comprobantes de venta a cancelar con notas de cr&eacute;dito.', type: 'error'});
    }
}

function setMasks() {
    $('.money-format').each(function () {
        $(this).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
    });
}

function actualizarCamposNotaCredito(factura) {
    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _descripcion]').val('Anula cuota ' + $(factura).attr('numero_cuota'));
    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _cantidad]').val(1);
    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _precioUnitario]').val($(factura).attr('precio_unitario').replace('.', ','));
    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _neto]').val($(factura).attr('precio_unitario').replace('.', ','));
    $('select[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _alicuotaIva]').select2("val", $(factura).attr('id_alicuota_IVA'));
    $('input[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _montoIva]').val($(factura).attr('monto_iva').replace('.', ','));
    $('#adif_contablebundle_comprobanteventa_comprobanteCancelado').val($(factura).attr('id_comprobante'));

    $('.ctn_rows_renglon_percepcion').html('');
    if (($(factura).attr('monto_perc_IIBB') > 0) || ($(factura).attr('monto_perc_IVA') > 0)) {

        if ($(factura).attr('monto_perc_IIBB') > 0) {
            crear_renglon_percepcion_impuesto('percepcion', {conceptoPercepcion: $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _conceptoPercepcion] option:contains("' + __percepcionIIBB + '")').attr('value'), jurisdiccion: $('select[id ^= adif_contablebundle_comprobanteventa_renglonesPercepcion_][id $= _jurisdiccion] option:contains("CABA")').attr('value'), monto: $(factura).attr('monto_perc_IIBB').replace('.', ',')}, null);
        }
        if ($(factura).attr('monto_perc_IVA') > 0) {
            crear_renglon_percepcion_impuesto('percepcion', {conceptoPercepcion: $('select[id ^= adif_contablebundle_comprobanteventa_renglonesComprobante_][id $= _conceptoPercepcion] option:contains("' + __percepcionIVA + '")').attr('value'), monto: $(factura).attr('monto_perc_IVA').replace('.', ',')}, null);
        }
    } else {
        $('.percepciones-content').hide();
    }

    $('#adif_contablebundle_comprobanteventa_renglonesComprobante_subtotal_fixed').val($(factura).attr('total').replace(/\./g, ','));
    $('#adif_contablebundle_comprobanteventa_total').val($(factura).attr('total').replace(/\./g, ','));

    updatePuntoVentaSelect();

    setMasks();
}

function getContratosCliente()
{
    var idCliente = $('#adif_contablebundle_comprobanteventa_idCliente').val();
    // 
    
    var data = {
        idCliente: idCliente
    };
    
     $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'comprobanteventa/get_contratos_by_idCliente',
        data: data
    }).success(function (contratos) {
        var select = armarSelectContratos(contratos);
        $('#select_contratos_innerHtml').html(select);
        initSelectById('adif_contablebundle_comprobanteventa_contratoCli');
    });
}

function armarSelectContratos(contratos)
{
    var select = '<select name="adif_contablebundle_comprobanteventa[contratoCli]" id="adif_contablebundle_comprobanteventa_contratoCli" class="form-control">';
    for(i in contratos){
        var contrato = contratos[i];
        select = select + '<option value="' + contrato.id + '">' + contrato.numeroContrato + '</option>';
    }
    
    select = select + '</select>';
    
    return select;
}