
var $formularioComprobanteEgresoValor = $('form[name="adif_contablebundle_comprobanteegresovalor"]');
var $formularioDevolucion = $('form[name="adif_contablebundle_devolucionegresovalor"]');

var $divSaldoActual = $('.saldo-actual');
var $divSaldoRendicion = $('.saldo-rendicion');
var $divSaldoFinal = $('.saldo-final');

var $inputMontoDevolucion = $('#adif_contablebundle_comprobanteegresovalor_devolucionDinero_montoDevolucion');
var $selectCuentaBancariaADIF = $('#adif_contablebundle_comprobanteegresovalor_devolucionDinero_cuenta');
var $inputNumeroDevolucion = $('#adif_contablebundle_comprobanteegresovalor_devolucionDinero_numero');
var $inputNumeroReferenciaDevolucion = $('#adif_contablebundle_comprobanteegresovalor_devolucionDinero_numeroReferencia');
var $inputFechaIngresoADIFDevolucion = $('#adif_contablebundle_comprobanteegresovalor_devolucionDinero_fechaIngresoADIF');

var $inputBusquedaProveedor = $('#adif_contablebundle_comprobanteegresovalor_busquedaProveedor');
var $inputRazonSocial = $('#adif_contablebundle_comprobanteegresovalor_razonSocial');
var $inputCUIT = $('#adif_contablebundle_comprobanteegresovalor_CUIT');

var $selectTipoComprobante = $('#adif_contablebundle_comprobanteegresovalor_tipoComprobante');
var $selectLetraComprobante = $('#adif_contablebundle_comprobanteegresovalor_letraComprobante');
var $inputPuntoVenta = $('#adif_contablebundle_comprobanteegresovalor_puntoVenta');
var $inputNumero = $('#adif_contablebundle_comprobanteegresovalor_numero');

var $inputNumeroCupon = $('#adif_contablebundle_comprobanteegresovalor_numeroCupon');

var $inputFechaComprobante = $('#adif_contablebundle_comprobanteegresovalor_fechaComprobante');
var $inputMontoValidacion = $('#adif_contablebundle_comprobanteegresovalor_montoValidacion');

var $idDevolucion = null;

$(document).ready(function () {

    initValidate();

    initTipoComprobanteHandler();

    initAgregarFormularioHandler();

    initAutocompleteProveedorHandler();

    initAgregarRenglonComprobanteHandler();

    initSubmitButton();

    setMasks();

    updateSaldoRendicion();

    updateSaldoFinal();

    initEditarDevolucion();

    initExportCustom('#table-comprobantes');

    initLinkCerrarRendicion();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    $.validator.addMethod("topeValidacion", function (value, element, param) {

        var montoValidacion = $("input[id ^=adif_contablebundle_comprobante][id $= _montoValidacion]")
                .val().replace(',', '@').replace(/\./g, ',').replace('@', '.');

        return parseFloat(montoValidacion) <= parseFloat(tope);
    });
    $.validator.addMethod("montoMinimo", function (value, element, param) {

        var montoValidacion = $("input[id ^=adif_contablebundle_comprobante][id $= _montoValidacion]")
                .val().replace(',', '@').replace(/\./g, ',').replace('@', '.');

        return parseFloat(montoValidacion) > parseFloat(0);
    });

    // Validacion del Formulario
    $formularioComprobanteEgresoValor.validate();
    $formularioDevolucion.validate();

    // Validacion CUIT
    $inputCUIT.rules('add', {
        cuil: true,
        messages: {
            cuil: "Formato de CUIT incorrecto."
        }
    });

    $inputCUIT.inputmask({
        mask: "99-99999999-9",
        placeholder: "_"
    });

    $('input[id ^= "adif_contablebundle_comprobante"][id $= "_montoValidacion"]')
            .rules('add', {
                topeValidacion:
                        true,
                messages: {
                    topeValidacion: "El monto del comprobante no debe ser superior a " + tope
                }
            });
    $('input[id ^= "adif_contablebundle_comprobante"][id $= "_montoValidacion"]')
            .rules('add', {
                montoMinimo:
                        true,
                messages: {
                    montoMinimo: "El monto del comprobante debe ser mayor a 0"
                }
            });

    if ($('input[name^="adif_contablebundle_comprobante"][name$="\[precioUnitario\]"]').length > 0) {
        $('input[name^="adif_contablebundle_comprobante"][name$="\[precioUnitario\]"]').rules('remove', 'valor_maximo');
    }

}

/**
 * 
 * @returns {undefined}
 */
function initTipoComprobanteHandler() {

    $selectTipoComprobante.on('change', function () {

        var $labelCUIT = $('label[for=adif_contablebundle_comprobanteegresovalor_CUIT]');

        // Si el tipo de comprobante es "Cupon"
        if ($(this).val() === __tipoComprobanteCupon) {

            $('.datos-no-cupon').hide();
            $('.datos-no-cupon').find('select, input').prop('required', false);

            $('.datos-no-cupon select').select2("val", "");
            $('.datos-no-cupon input').val(null);

            $('.datos-no-cupon').find('select, input').keyup();

            $('.datos-cupon').find('input:not(":checkbox")').prop('required', true);
            $('.datos-cupon').show();

            $labelCUIT.removeClass('required');
            $inputCUIT.prop('required', false);

            $inputCUIT.rules('remove', 'cuil');

        }
        else {

            $('.datos-cupon').hide();
            $('.datos-cupon').find('input').prop('required', false);
            $('.datos-cupon').find('input').keyup();

            $inputNumeroCupon.val(null);

            $('.datos-no-cupon').find('select, input').prop('required', true);
            $('.datos-no-cupon').find('select').select2();
            $('.datos-no-cupon').show();

            $labelCUIT.addClass('required');
            $inputCUIT.prop('required', true);

            $inputCUIT.rules('add', 'cuil');
        }


    }).trigger('change');
}

/**
 * 
 * @returns {undefined}
 */
function initAgregarFormularioHandler() {

    $('.link-agregar-devolucion').click(function () {
        $('.formulario-comprobante').hide();
        $('.formulario-devolucion').show();
        $('.boton_cancelar_general').hide();
        
        setInformacionComprobante();
    });

    $('.link-agregar-comprobante').click(function () {
        $('.formulario-devolucion').hide();
        $('.formulario-comprobante').show();
        $('.boton_cancelar_general').hide();

        initLimiteFecha();

        setInformacionComprobante();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteProveedorHandler() {
    $inputBusquedaProveedor.autocomplete({
        source: __AJAX_PATH__ + 'comprobanteegresovalor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            $inputRazonSocial.val(ui.item.razonSocial);
            $inputCUIT.val(ui.item.CUIT);
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
function initAgregarRenglonComprobanteHandler() {
    $('#agregar_renglon_comprobante').on('click', function (e) {
        crearRenglonComprobanteEgresoValor();
        restringir_iva();
        recalcular_netos();
        $('input[name^="adif_contablebundle_comprobante"][name$="\[precioUnitario\]"]').rules('remove', 'valor_maximo');
    });
}

/**
 * 
 * @param {type} idConcepto
 * @param {type} descripcion
 * @param {type} cantidad
 * @param {type} precioUnitario
 * @param {type} montoNeto
 * @param {type} idAlicuotaIva
 * @param {type} montoIva
 * @returns {undefined}
 */
function crearRenglonComprobanteEgresoValor(idConcepto, descripcion, cantidad, precioUnitario, montoNeto, idAlicuotaIva, montoIva) {

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

    var row_sel_prefix = '#adif_contablebundle_comprobanteegresovalor_renglonesComprobante_';

    if (idConcepto) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_conceptoEgresoValor').val(idConcepto);
    }

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
}


/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar Comprobante"
    $('#adif_contablebundle_comprobanteegresovalor_submit').on('click', function (e) {

        if ($formularioComprobanteEgresoValor.valid()) {

            e.preventDefault();

            if (parseFloat($inputMontoValidacion.val()) > parseFloat(limiteRendicion)) {

                var options = $.extend({
                    title: 'Ha ocurrido un error',
                    msg: "No puede rendir más del límite."
                });

                show_alert(options);
                return false;

            } else {

                show_confirm({
                    msg: '¿Desea guardar el comprobante?',
                    callbackOK: function () {
                        if (validComprobanteForm()) {

                            $('.changeable').each(function () {
                                $(this).val(clearCurrencyValue($(this).val()));
                            });

                            $formularioComprobanteEgresoValor.submit();
                        }
                    }
                });
            }

            e.stopPropagation();

            return false;
        }

        return false;
    });

    // Handler para el boton "Guardar Devolucion"
    $('.link-guardar-formulario-devolucion').on('click', function (e) {

        if ($formularioDevolucion.valid()) {

            e.preventDefault();

            totalDevolucion = 0;

            $('.changeable').each(function () {
                totalDevolucion += clearCurrencyValue($(this).val());
            });

            if (totalDevolucion > parseFloat(limiteRendicion)) {

                var options = $.extend({
                    title: 'Ha ocurrido un error',
                    msg: "No puede rendir más del límite."
                });

                show_alert(options);

                return false;

            } else {


                if ($idDevolucion != null) {
                    var json = {
                        'idDevolucion': $idDevolucion
                    };

                    $formularioDevolucion.addHiddenInputData(json);
                }

                show_confirm({
                    msg: '¿Desea guardar la devolución?',
                    callbackOK: function () {

                        $('.changeable').each(function () {
                            $(this).val(clearCurrencyValue($(this).val()));
                        });

                        $formularioDevolucion.submit();
                    }
                });

                e.stopPropagation();

                return false;
            }
        }

        return false;
    });


    // Handler para el boton "Cerrar rendición"
    $('.link-cerrar-rendicion').click(function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea cerrar la rendición?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });
}


/**
 * 
 * @returns {undefined}
 */
function validComprobanteForm() {

    // Si NO hay al menos un renglon cargado al comprobante
    if (!$('.row_renglon_comprobante').length > 0) {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe cargar al menos un renglón al comprobante."
        });

        show_alert(options);

        return false;
    }

    return true;
}

/**
 * 
 * @returns {undefined}
 */
function updateSaldoRendicion() {

    var totalSaldoRendicion = 0;

    $.each(dt_getRows($('#table-comprobantes')), function (i, e) {
        totalSaldoRendicion += parseFloat(clearCurrencyValue(e[5]));
    });

//    $('.total-comprobante').each(function () {
//
//        totalSaldoRendicion += parseFloat(clearCurrencyValue($(this).text()));
//
//    });

    var totalSaldoRendicionFormateado = totalSaldoRendicion.toString().replace(/\./g, ',');

    $divSaldoRendicion.text(totalSaldoRendicionFormateado).autoNumeric('update');
}

/**
 * 
 * @returns {undefined}
 */
function updateSaldoFinal() {

    var saldoActual = clearCurrencyValue($divSaldoActual.text());

    var rendicion = clearCurrencyValue($divSaldoRendicion.text());

    var saldoFinal = parseFloat(saldoActual) - parseFloat(rendicion);

    limiteRendicion -= parseFloat(rendicion);

    var saldoFinalFormateado = saldoFinal.toString().replace(/\./g, ',');

    $divSaldoFinal.text(saldoFinalFormateado).autoNumeric('update');
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-99999999', aSign: '$ ', aSep: '.', aDec: ','});
    });
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value.replace('$', '').replace(/\./g, '').replace(/\,/g, '.').trim();
}

/**
 * 
 * @returns {undefined}
 */
function initEditarDevolucion() {
    $(document).on('click', '.editar_devolucion_link', function (e) {
        e.preventDefault();
        $idDevolucion = $(this).prop("href").split('?idDevolucion=')[1];
        pathEdit = $(this).prop("href").split('?id=')[0];
        var ajax_dialog_edit = $.ajax({
            type: 'GET',
            url: __AJAX_PATH__ + 'egresovalor_devoluciondinero/editar/' + $idDevolucion
        });
        $.when(ajax_dialog_edit).done(function (dataDevolucion) {

            $selectCuentaBancariaADIF.select2('val', dataDevolucion.cuentaBancoAdif);
            $inputMontoDevolucion.val((dataDevolucion.monto).replace(/\./g, ','));
            $inputNumeroDevolucion.val(dataDevolucion.numero);
            $inputNumeroReferenciaDevolucion.val(dataDevolucion.numeroReferencia);
            $inputFechaIngresoADIFDevolucion.val(dataDevolucion.fechaIngresoADIF);
            $('.link-agregar-devolucion').click();
            $('.link-agregar-devolucion').parents('.div_ctn_seccion').remove();

        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initLinkCerrarRendicion() {

    if ($divSaldoRendicion.html() == "$ 0,00") {
        $('.link-cerrar-rendicion').remove();
    } else {
        $('.link-cerrar-rendicion').parents('.pull-right').show();
    }
}

/**
 * 
 * @returns {undefined}
 */
function initLimiteFecha() {

    var date = new Date();
    var currentMonth = date.getMonth();
    var currentDate = date.getDate();
    var currentYear = date.getFullYear();

    $("#adif_contablebundle_comprobanteegresovalor_fechaComprobante").datepicker('setEndDate', new Date(currentYear, currentMonth, currentDate));
    $("#adif_contablebundle_comprobanteegresovalor_fechaComprobante").datepicker('update', new Date(currentYear, currentMonth, currentDate));
}

/**
 * 
 * @returns {undefined}
 */
function setInformacionComprobante() {

    if (__fechaIngresoADIF !== "") {

        $('input[id ^= adif_contablebundle_][id $= _fechaIngresoADIF').val(__fechaIngresoADIF);
    }
    
    if (__numeroReferencia !== "") {

        $('input[id ^= adif_contablebundle_][id $= _numeroReferencia]').val(__numeroReferencia);
    }
}
