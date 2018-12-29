
var esEdit = $('[name=_method]').length > 0;

var $categoriaContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_categoriaContrato').val();

var $busquedaConsultorInput = $('#adif_contablebundle_consultoria_contratoconsultoria_consultor');

var $claseContratoSelect = $('#adif_contablebundle_consultoria_contratoconsultoria_claseContrato');

var $formularioContrato = $('form[name="adif_contablebundle_consultoria_contratoconsultoria"]');

var collectionHolderCicloFacturacion;

/**
 * 
 */
jQuery(document).ready(function () {

    initValidate();

    updateDeleteLinks($(".prototype-link-remove-ciclo-facturacion"));

    setMasks();

    initEdit();

    initCategoriaContrato();

    initAutocompleteConsultor();

    initCicloFacturacionForm();

    initFechaInicioContratoHandler();

    updateFechaFinContrato();

    initFechaFinCicloFacturacionHandler();

    initSubmitButton();

    updateFechasCicloFacturacion();

    initCantidadFacturaChangeHandler();

    actualizarSubtotal();

    initCiclosHandler();

    setUnidadTiempoReadOnly();

    initCalculoSubtotalHandler();

    initAdenda();

    initEditParcial();
});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    $.validator.addMethod("fechaInicioContrato", function (value, element, param) {

        var fechaInicioContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaInicio').val();
        var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);

        var fechaFinContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaFin').val();
        var fechaFinContratoDate = getDateFromString(fechaFinContrato);

        return  fechaInicioContratoDate.getTime() <= fechaFinContratoDate.getTime();
    });

    // Validacion del Formulario
    $formularioContrato.validate();

    $('input[id ^= "adif_contablebundle_consultoria_contratoconsultoria_fechaInicio"]')
            .rules('add', {
                fechaInicioContrato: true,
                messages: {
                    fechaInicioContrato: "La fecha debe ser mayor a la de fin."
                }
            });
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteConsultor() {

    $('#adif_contablebundle_consultoria_contratoconsultoria_consultor').autocomplete({
        source: __AJAX_PATH__ + 'consultor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            selectConsultor(event, ui);
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
function selectConsultor(event, ui) {

    $('#adif_contablebundle_consultoria_contratoconsultoria_consultor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_consultoria_contratoconsultoria_consultor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_consultoria_contratoconsultoria_idConsultor').val(ui.item.id);
}


/**
 * 
 * @returns {undefined}
 */
function initCicloFacturacionForm() {

    collectionHolderCicloFacturacion = $('div.prototype-ciclo-facturacion');

    collectionHolderCicloFacturacion.data('index', collectionHolderCicloFacturacion.find(':input').length);

    $('.prototype-link-add-ciclo-facturacion').on('click', function (e) {
        e.preventDefault();

        if (fechaFinCicloFacturacionValida()) {

            // Si no es una Adenda
            if ($categoriaContrato !== __categoriaContratoAdenda) {

                inicioCicloValido = true;

                fechaFinContratoDate = getDateFromString($('#adif_contablebundle_consultoria_contratoconsultoria_fechaFin').val());

                $('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaFin]').each(function (e) {
                    inicioCicloValido &= (getDateFromString($(this).val()) < fechaFinContratoDate);
                });

                // Si la fecha de inicio de algun ciclo es mayor al fin de contrato
                if (!inicioCicloValido) {

                    var options = $.extend({
                        title: 'Ha ocurrido un error',
                        msg: "Quiere agregar ciclos fuera del rango del contrato."
                    });

                    show_alert(options);

                    return false;
                }
            }

            addCicloFacturacionForm(collectionHolderCicloFacturacion);

            setFechaCicloFacturacion();

            initFechaFinCicloFacturacionHandler();

            setMasks();

            initCantidadFacturaChangeHandler();

            initCalculoSubtotalHandler();

            setUnidadTiempoReadOnly();
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initCalculoSubtotalHandler() {

    $('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $=_cantidadFacturas]')
            .change(function () {
                actualizarSubtotal();
            });

    $('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $=_importe]')
            .change(function () {
                actualizarSubtotal();
            });
}

/**
 * 
 * @returns {undefined}
 */
function actualizarSubtotal() {

    var total = 0;

    $('.row_ciclo_facturacion').each(function () {

        var cantidadFacturas = $(this).find('[id $=_cantidadFacturas]').val();

        var importe = clearCurrencyValue($(this).find('[id $=_importe]').val());

        if (cantidadFacturas.length > 0 && importe.length > 0) {
            total += parseInt(cantidadFacturas) * parseFloat(importe);
        }
    });

    var totalFormateado = total.toString().replace(/\./g, ',');

    $('#adif_contablebundle_facturacion_contrato_totalCalculado')
            .val(totalFormateado).autoNumeric('update');
}

/**
 * 
 * @returns {undefined}
 */
function initFechaInicioContratoHandler() {

    $('#adif_contablebundle_consultoria_contratoconsultoria_fechaInicio, #adif_contablebundle_consultoria_contratoconsultoria_fechaFin').on('change', function () {

        updateFechaFinContrato();

        actualizarFechaInicioPrimerCicloFacturacion();

        updateFechasCicloFacturacion();
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateFechaFinContrato() {

    var fechaInicioContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaInicio').val();
    var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);

    $('#adif_contablebundle_consultoria_contratoconsultoria_fechaFin')
            .datepicker('setStartDate', fechaInicioContratoDate);
}

/**
 * 
 * @returns {undefined}
 */
function actualizarFechaInicioPrimerCicloFacturacion() {

    var fechaInicioContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaInicio').val();
    var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);

    var $fechaInicioPrimerCicloFacturacion = $('.row_ciclo_facturacion').first()
            .find('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaInicio]');

    $fechaInicioPrimerCicloFacturacion.datepicker("update", fechaInicioContratoDate)
            .prop('readonly', true);

    $fechaInicioPrimerCicloFacturacion.unbind();
}


/**
 * 
 * @returns {undefined}
 */
function updateFechasCicloFacturacion() {

    // Fecha fin del Contrato
    var fechaFinContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaFin').val();

    if (fechaFinContrato.length > 0) {
        var fechaFinContratoDate = getDateFromString(fechaFinContrato);
    }

    // Fecha inicio del Contrato
    var fechaInicioContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaInicio').val();

    if (fechaInicioContrato.length > 0) {
        var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);
    }

    if ((fechaFinContrato.length > 0) && (fechaInicioContrato.length > 0)) {

        $('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaFin]').each(function (e) {

            var fechaInicioCicloFacturacionActual = $(this).parents('.row_ciclo_facturacion')
                    .find('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaInicio]');

            var fechaInicioCicloFacturacionActualDate = getDateFromString(fechaInicioCicloFacturacionActual.val());

            fechaInicioCicloFacturacionActualDate.setDate(1);
            fechaInicioCicloFacturacionActualDate.setMonth(fechaInicioCicloFacturacionActualDate.getMonth() + 1);
            fechaInicioCicloFacturacionActualDate.setDate(fechaInicioCicloFacturacionActualDate.getDate() - 1);

            // Si es una Adenda
            if ($categoriaContrato === __categoriaContratoAdenda) {
                if (fechaInicioContrato.length > 0) {
                    $(this).datepicker('setStartDate', fechaInicioContratoDate);
                }
            }
            else {
                if (fechaFinContratoDate < fechaInicioCicloFacturacionActualDate) {
                    $(this).datepicker('setStartDate', fechaFinContratoDate);
                } else {
                    $(this).datepicker('setStartDate', fechaInicioCicloFacturacionActualDate);
                }
            }


            var ejercicioFechaInicioCicloFacturacionActual = fechaInicioCicloFacturacionActualDate.getFullYear();

            var fechaFinEjercicioDate = getDateFromString('31/12/' + ejercicioFechaInicioCicloFacturacionActual);

            // Si la fecha de fin de contrato es menor a la fecha de fin del ejercicio del ciclo actual
            if (fechaFinContratoDate.getTime() <= fechaFinEjercicioDate.getTime()) {
                $(this).datepicker('setEndDate', fechaFinContratoDate);
            }
            else {
                $(this).datepicker('setEndDate', fechaFinEjercicioDate);
            }
        });
    }
    else if (fechaInicioContrato.length > 0) {

        $('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaFin]').each(function (e) {

            var fechaInicioCicloFacturacionActual = $(this).parents('.row_ciclo_facturacion')
                    .find('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaInicio]');

            var fechaInicioCicloFacturacionActualDate = getDateFromString(fechaInicioCicloFacturacionActual.val());

            fechaInicioCicloFacturacionActualDate.setDate(1);
            fechaInicioCicloFacturacionActualDate.setMonth(fechaInicioCicloFacturacionActualDate.getMonth() + 1);
            fechaInicioCicloFacturacionActualDate.setDate(fechaInicioCicloFacturacionActualDate.getDate() - 1);

            var ejercicioFechaInicioCicloFacturacionActual = fechaInicioCicloFacturacionActualDate.getFullYear();

            var fechaFinEjercicioDate = getDateFromString('31/12/' + ejercicioFechaInicioCicloFacturacionActual);

            $(this).datepicker('setEndDate', fechaFinEjercicioDate);

        });
    }

    // Si es una Adenda
    if ($categoriaContrato === __categoriaContratoAdenda) {

        $('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaInicio]').each(function (e) {

            if (fechaInicioContrato.length > 0) {
                $(this).datepicker('setStartDate', fechaInicioContratoDate);
            }

            if (fechaFinContrato.length > 0) {
                $(this).datepicker('setEndDate', fechaFinContratoDate);
            }
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initCantidadFacturaChangeHandler() {

    $('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $=_cantidadFacturas]')
            .change(function () {
                $(this).parents('.row_ciclo_facturacion')
                        .find('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $=_cantidadFacturasPendientes]')
                        .val($(this).val());
            });
}

/**
 * 
 * @returns {undefined}
 */
function initCiclosHandler() {
    $(document).on('change', 'input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaFin]', function (e) {
        setFechaCicloFacturacion();
    });
}


/**
 * 
 * @returns {undefined}
 */
function setFechaCicloFacturacion() {

    var cantidadRows = $('.row_ciclo_facturacion').length;

    // Si es el primer ciclo de facturación
    if (cantidadRows === 1) {
        actualizarFechaInicioPrimerCicloFacturacion();
    }
    // Sino,
    else {
        for (i = 1; i < cantidadRows; i++) {

            var $fechaInicioCicloFacturacion = $($('.row_ciclo_facturacion')[i])
                    .find('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaInicio]');

            // Si no es una Adenda
            if ($categoriaContrato !== __categoriaContratoAdenda) {

                var $cicloFacturacionAnterior = $($('.row_ciclo_facturacion')[i]).prev();

                var $fechasFinCicloFacturacionAnteriorInput = $cicloFacturacionAnterior
                        .find('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaFin]');

                var fechaFinCicloFacturacionAnterior = $fechasFinCicloFacturacionAnteriorInput.last().val();
                var fechaFinCicloFacturacionAnteriorDate = getDateFromString(fechaFinCicloFacturacionAnterior);

                fechaFinCicloFacturacionAnteriorDate.setDate(fechaFinCicloFacturacionAnteriorDate.getDate() + 1);

                $fechaInicioCicloFacturacion.datepicker("update", fechaFinCicloFacturacionAnteriorDate);

                $fechaInicioCicloFacturacion.prop('readonly', true);

                $fechaInicioCicloFacturacion.unbind();
            }
            else {

                // Fecha inicio del Contrato
                var fechaInicioContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaInicio').val();

                // Fecha fin del Contrato
                var fechaFinContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaFin').val();

                if (fechaInicioContrato.length > 0) {
                    var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);

                    $fechaInicioCicloFacturacion.datepicker('setStartDate', fechaInicioContratoDate);

                    if ($fechaInicioCicloFacturacion.val() === '') {
                        $fechaInicioCicloFacturacion.datepicker("update", fechaInicioContratoDate);
                    }
                }

                if (fechaFinContrato.length > 0) {
                    var fechaFinContratoDate = getDateFromString(fechaFinContrato);

                    $fechaInicioCicloFacturacion.datepicker('setEndDate', fechaFinContratoDate);
                }
            }
        }
    }

    updateFechasCicloFacturacion();
}


/**
 * 
 * @returns {undefined}
 */
function initFechaFinCicloFacturacionHandler() {

    $('[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_]')
            .not("[id $= _cantidadFacturas], [id $= _importe]").on('change', function () {

        var $rowCicloFacturacion = $(this).parents('.row_ciclo_facturacion');

        actualizarCantidadFacturas($rowCicloFacturacion);
    });
}

/**
 * 
 * @param {type} $rowCicloFacturacion
 * @returns {undefined}
 */
function actualizarCantidadFacturas($rowCicloFacturacion) {

    var fechaInicioCicloFacturacion = $rowCicloFacturacion.find('input[id $= _fechaInicio]').val();
    var fechaFinCicloFacturacion = $rowCicloFacturacion.find('input[id $= _fechaFin]').val();

    if (fechaInicioCicloFacturacion.length > 0 && fechaFinCicloFacturacion.length > 0) {

        var fechaInicioCicloFacturacionDate = getDateFromString(fechaInicioCicloFacturacion);
        var fechaFinCicloFacturacionDate = getDateFromString(fechaFinCicloFacturacion);

        var cantidadMeses = monthDiff(fechaInicioCicloFacturacionDate, fechaFinCicloFacturacionDate);

        var mesFechaInicioCicloFacturacion = fechaInicioCicloFacturacionDate.getMonth();
        var mesFechaFinCicloFacturacion = fechaFinCicloFacturacionDate.getMonth();

        cantidadMeses++;

        // Si el ciclo No está comprendido en un mismo mes
        if (mesFechaInicioCicloFacturacion !== mesFechaFinCicloFacturacion) {
            cantidadMeses++;
        }

        var cantidadUnidadTiempo = $rowCicloFacturacion.find('input[id $= _cantidadUnidadTiempo]').val();

        var idUnidadTiempo = $rowCicloFacturacion.find('select[id $= unidadTiempo]').val();

        var cantidadFacturas = Math.ceil((parseInt(cantidadMeses) / (getCantidadMeses(idUnidadTiempo) * parseInt(cantidadUnidadTiempo))));

        $rowCicloFacturacion.find('input[id $= _cantidadFacturas]')
                .val(cantidadFacturas).trigger('change');

        actualizarSubtotal();
    }
}

/**
 * 
 * @returns {jQuery.length|Number|$.length|window.jQuery.length}
 */
function getTotalCiclosFacturacion() {
    return $('.row_ciclo_facturacion').length;
}

/**
 * 
 * @returns {unresolved}
 */
function fechaFinCicloFacturacionValida() {

    if ($('.row_ciclo_facturacion').length > 0) {

        var $cicloFacturacionAnterior = $('.row_ciclo_facturacion').last();

        var $fechaFinCicloFacturacionAnteriorInput = $cicloFacturacionAnterior
                .find('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaFin]');

        // Si la fecha fin del ciclo anterior fue completada
        return $fechaFinCicloFacturacionAnteriorInput.valid();
    }

    return true;
}


/**
 * 
 * @param {type} $collectionHolder
 * @returns {addContactoProveedorForm}
 */
function addCicloFacturacionForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var cicloFacturacionForm = prototype.replace(/__ciclo_facturacion__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-ciclo-facturacion').closest('.row').before(cicloFacturacionForm);

    initSelects();

    initCurrencies();

    var $newRow = $('.row_ciclo_facturacion').last();

    $newRow.data("index", index + 1);

    initDatepickers($newRow);

    var $cicloFacturacionDeleteLink = $(".prototype-link-remove-ciclo-facturacion");

    updateDeleteLinks($cicloFacturacionDeleteLink);
}


/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_consultoria_contratoconsultoria_submit').on('click', function (e) {

        if ($formularioContrato.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el contrato?',
                callbackOK: function () {

                    if (validForm()) {

                        $('.changeable').each(function () {
                            $(this).val(clearCurrencyValue($(this).val()));
                        });

                        $formularioContrato.submit();
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
function validForm() {

    // Si hay al menos un renglon cargado al contrato
    if ($('.row_ciclo_facturacion').length > 0) {
        if (validarFechas()) {
            return validarImporteTotal();
        } else {
            return false;
        }
    }
    else {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe cargar al menos un ciclo de facturaci&oacute;n al contrato."
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
function validarFechas() {

    // Fecha inicio del Contrato
    var fechaInicioContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaInicio').val();
    var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);

    // Fecha fin del Contrato
    var fechaFinContrato = $('#adif_contablebundle_consultoria_contratoconsultoria_fechaFin').val();
    var fechaFinContratoDate = getDateFromString(fechaFinContrato);

    // Fecha inicio del primer CicloFacturacion
    var fechaInicioPrimerCicloFacturacion = $('.row_ciclo_facturacion').first()
            .find('input[id^=adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id$=_fechaInicio]')
            .val();

    var fechaInicioPrimerCicloFacturacionDate = getDateFromString(fechaInicioPrimerCicloFacturacion);

    // Si la fecha de inicio del contrato no coincide con la del primer ciclo de facturación
    if (fechaInicioContratoDate.getTime() !== fechaInicioPrimerCicloFacturacionDate.getTime()) {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "La fecha del primer ciclo de facturaci&oacute;n debe coincidir con el inicio del contrato."
        });

        show_alert(options);

        return false;
    }

    // Si no es una Adenda
    if ($categoriaContrato !== __categoriaContratoAdenda) {

        // Fecha fin del último CicloFacturacion
        var fechaFinUltimoCicloFacturacion = $('.row_ciclo_facturacion').last()
                .find('input[id^=adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id$=_fechaFin]')
                .val();

        var fechaFinUltimoCicloFacturacionDate = getDateFromString(fechaFinUltimoCicloFacturacion);


        // Si la fecha de fin del contrato no coincide con la del último ciclo de facturación
        if (fechaFinContratoDate.getTime() !== fechaFinUltimoCicloFacturacionDate.getTime()) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "La fecha del &uacute;ltimo ciclo de facturaci&oacute;n debe coincidir con el fin del contrato."
            });

            show_alert(options);

            return false;
        }

        inicioCicloValido = true;

        $('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaInicio]').each(function (e) {
            inicioCicloValido &= (getDateFromString($(this).val()) <= fechaFinContratoDate);
        });

        // Si la fecha de inicio de algun ciclo es mayor al fin de contrato
        if (!inicioCicloValido) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "Quiere agregar ciclos fuera del rango del contrato."
            });

            show_alert(options);

            return false;
        }
    }
    else {

        // Fecha fin superior de los CicloFacturacion
        var fechaFinSuperiorCicloFacturacion = null;
        var fechaFinSuperiorCicloFacturacionDate = null;
        var fechaFinActualCicloFacturacion = null;
        var fechaFinActualCicloFacturacionDate = null;

        // Por cada ciclo de facturación
        $('.row_ciclo_facturacion').each(function () {

            if (fechaFinSuperiorCicloFacturacion === null) {

                fechaFinSuperiorCicloFacturacion = $(this)
                        .find('input[id^=adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id$=_fechaFin]')
                        .val();

                fechaFinSuperiorCicloFacturacionDate = getDateFromString(fechaFinSuperiorCicloFacturacion);
            }

            fechaFinActualCicloFacturacion = $(this)
                    .find('input[id^=adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id$=_fechaFin]')
                    .val();

            fechaFinActualCicloFacturacionDate = getDateFromString(fechaFinActualCicloFacturacion);


            if (fechaFinActualCicloFacturacionDate.getTime() > fechaFinSuperiorCicloFacturacionDate.getTime()) {
                fechaFinSuperiorCicloFacturacionDate = fechaFinActualCicloFacturacionDate;
            }
        });

        // Si la fecha de fin del contrato no coincide con la del último ciclo de facturación
        if (fechaFinContratoDate.getTime() !== fechaFinSuperiorCicloFacturacionDate.getTime()) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "La fecha de al menos un ciclo de facturaci&oacute;n debe coincidir con el fin del contrato."
            });

            show_alert(options);

            return false;
        }
    }

    return true;
}

/**
 * 
 * @returns {undefined}
 */
function validarImporteTotal() {

    var importeTotalContrato = clearCurrencyValue(
            $('#adif_contablebundle_consultoria_contratoconsultoria_importeTotal').val());

    var importeTotalCalculado = clearCurrencyValue($('.importe-total').val());

    if (importeTotalContrato.length > 0 && importeTotalCalculado > 0) {

        var esValido = parseFloat(importeTotalContrato) === parseFloat(importeTotalCalculado);

        if (!esValido) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "El importe total de los ciclos de facturaci&oacute;n no coincide con el total del contrato."
            });

            show_alert(options);
        }
        return esValido;
    }
    else {

        return false;
    }
}

/**
 * 
 * @returns {undefined}
 */
function initEdit() {

    // Si es una edición
    if (esEdit || $categoriaContrato !== __categoriaContratoOriginal) {

        // Deshabilito la búsqueda de clientes
        $busquedaConsultorInput.prop('readonly', true);

        // Deshabilito el select de "ClaseContrato"
        $claseContratoSelect.select2('readonly', true);

        // Deshabilito las fechas de inicio de los ciclos de facturación
        $('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _fechaInicio]')
                .prop('readonly', true).unbind();
    }
}

/**
 *   * @returns {undefined}
 */ function initCategoriaContrato() {

    // Si es una adenda
    if ($categoriaContrato === __categoriaContratoAdenda) {

        // Por cada ciclo de facturación
        $('.row_ciclo_facturacion').each(function () {
            setCiclosFacturacionReadonly($(this));
        });

    }
}

/**
 * 
 * @param {type} $rowCicloFacturacion
 * @returns {undefined}
 */ function setCiclosFacturacionReadonly($rowCicloFacturacion) {

    var cantidadFacturasPendientes = $rowCicloFacturacion
            .find('input[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _cantidadFacturasPendientes]')
            .val();
    if (cantidadFacturasPendientes == 0) {
        $rowCicloFacturacion.addClass('disabled');

        $rowCicloFacturacion.find('input, select')
                .prop('readonly', true).unbind();

        $rowCicloFacturacion
                .find('.prototype-link-remove-ciclo-facturacion')
                .remove();
    }
}

/**
 * 
 * @returns {undefined}
 */
function setUnidadTiempoReadOnly() {

    $('select[id ^= adif_contablebundle_consultoria_contratoconsultoria_ciclosFacturacion_][id $= _unidadTiempo]')
            .select2('readonly', true);
}

/**
 * 
 * @returns {undefined}
 */
function initAdenda() {

    // Si NO es una edición y ES una adenda
    if (!esEdit && $categoriaContrato === __categoriaContratoAdenda) {

        $('#adif_contablebundle_consultoria_contratoconsultoria_numeroContrato')
                .prop('readonly', true);

        $('#adif_contablebundle_consultoria_contratoconsultoria_numeroCarpeta')
                .prop('readonly', true);

        $('#adif_contablebundle_consultoria_contratoconsultoria_fechaInicio')
                .prop('readonly', true).unbind();

        $('#adif_contablebundle_consultoria_contratoconsultoria_fechaFin')
                .prop('readonly', true).unbind();

        $('#adif_contablebundle_consultoria_contratoconsultoria_tipoMoneda')
                .select2('readonly', true);

        $('#adif_contablebundle_consultoria_contratoconsultoria_estadoContrato')
                .select2('readonly', true);

        $('#adif_contablebundle_consultoria_contratoconsultoria_gerencia')
                .select2('readonly', true);

        $('#adif_contablebundle_consultoria_contratoconsultoria_subgerencia')
                .select2('readonly', true);

        $('#adif_contablebundle_consultoria_contratoconsultoria_area')
                .select2('readonly', true);

    }
}

/**
 * 
 * @param {type} idUnidadTiempo
 * @returns {Number}
 */
function getCantidadMeses(idUnidadTiempo) {

    var $meses = 0;

    $.each(__unidadesTiempo, function (i, elem) {
        if (elem.id == idUnidadTiempo) {
            $meses = elem.meses;
            return;
        }
    });

    return $meses;
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
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
function initEditParcial() {

    // Si NO es una edicion total
    if (esEdit && __esEdicionTotal === 0) {

        $('input[type="text"].no-editable, textarea.no-editable').prop('readonly', true);

        $('input[type="checkbox"].no-editable').closest('div.has-switch')
                .block(
                        {
                            message: null,
                            overlayCSS: {
                                backgroundColor: 'black',
                                opacity: 0.05,
                                cursor: 'not-allowed'}
                        }
                );

        $('input.datepicker.no-editable').each(function () {
            readonlyDatePicker($(this), true);
        });

        $('select.no-editable').select2('readonly', true);

        $('.prototype-link-remove-ciclo-facturacion').remove();

        $('.prototype-link-add-ciclo-facturacion').remove();
    }
}