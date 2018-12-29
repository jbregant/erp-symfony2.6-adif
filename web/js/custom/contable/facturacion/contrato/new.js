
var esEdit = $('[name=_method]').length > 0;

var $categoriaContrato = $('#adif_contablebundle_facturacion_contrato_categoriaContrato').val();

var $formularioContrato = $('form[name="adif_contablebundle_facturacion_contrato"]');

var $busquedaClienteInput = $('#adif_contablebundle_facturacion_contrato_cliente');
var $claseContratoSelect = $('#adif_contablebundle_facturacion_contrato_claseContrato');

var $calculaIVAInput = $('#adif_contablebundle_facturacion_contrato_calculaIVA');

var $numeroLicitacionInput = $('#adif_contablebundle_facturacion_contrato_numeroLicitacion');
var $fechaAperturaInput = $('#adif_contablebundle_facturacion_contrato_fechaApertura');

var $numeroInmuebleInput = $('#adif_contablebundle_facturacion_contrato_numeroInmueble');

var collectionHolderCicloFacturacion;
var collectionHolderPoliza;

/**
 * 
 */
jQuery(document).ready(function () {

    initValidate();

    updateDeleteLinks($(".prototype-link-remove-ciclo-facturacion"));
    updateDeleteLinks($(".prototype-link-remove-poliza"));

    setMasks();

    initEdit();

    checkCalculaIVA();

    initCategoriaContrato();

    initDetalleContrato();

    initSelects();

    initAutocompleteCliente();

    initClaseContratoHandler();

    initCicloFacturacionForm();

    initPolizaForm();

    initFechaInicioContratoHandler();

    updateFechaFinContrato();

    initFechaFinCicloFacturacionHandler();

    initCalculoSubtotalHandler();

    initCantidadFacturaChangeHandler();

    actualizarSubtotal();

    initSubmitButton();

    updateFinCicloFacturacion();

    initCiclosHandler();

    initRangoPoliza();

    checkMuestraNumeroInmueble();

    initEditParcial();
});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    $.validator.addMethod("fechaInicioContrato", function (value, element, param) {

        var fechaInicioContrato = $('#adif_contablebundle_facturacion_contrato_fechaInicio').val();
        var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);

        var fechaFinContrato = $('#adif_contablebundle_facturacion_contrato_fechaFin').val();
        var fechaFinContratoDate = getDateFromString(fechaFinContrato);

        return  fechaInicioContratoDate.getTime() <= fechaFinContratoDate.getTime();
    });

    $.validator.addMethod("diaVencimiento", function (value, element, param) {

        var clasesContratoSinNroInmuebleArray = [
            __claseContratoChatarra,
            __claseContratoServidumbrePaso,
            __claseContratoPliego,
            __claseContratoLocacionServicio
        ];

        // Si la clase de contrato requiere dia de vencimiento
        if (($.inArray($claseContratoSelect.val(), clasesContratoSinNroInmuebleArray) == -1)) {
            return ($('#adif_contablebundle_facturacion_contrato_diaVencimiento').val() != '');
        } else {
            return true;
        }
    });

    $.validator.addMethod("observacion", function (value, element, param) {

        var idEstadoContrato = $('#adif_contablebundle_facturacion_contrato_estadoContrato').val();

        if (idEstadoContrato !== __idEstadoContratoActivoComentado && idEstadoContrato !== __idEstadoContratoInactivo) {
            return true;
        }
        else {
            return $('#adif_contablebundle_facturacion_contrato_observacion').val() !== "";
        }

    });

    $.validator.addMethod("fechaPoliza", function (value, element, param) {

        var fechaInicioContrato = $('#adif_contablebundle_facturacion_contrato_fechaInicio').val();
        var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);

        var fechaPoliza = $(element).val();
        var fechaPolizaDate = getDateFromString(fechaPoliza);

        return fechaInicioContratoDate.getTime() <= fechaPolizaDate.getTime();
    });

    // Validacion del Formulario
    $formularioContrato.validate();

    $('input[id ^= "adif_contablebundle_facturacion_contrato_fechaInicio"]')
            .rules('add', {
                fechaInicioContrato: true,
                messages: {
                    fechaInicioContrato: "La fecha debe ser mayor a la de fin."
                }
            });

    $('input[id ^= "adif_contablebundle_facturacion_contrato_diaVencimiento"]')
            .rules('add', {
                diaVencimiento: true,
                messages: {
                    diaVencimiento: "Este campo es obligatorio."
                }
            });

    $('textarea[id ^= "adif_contablebundle_facturacion_contrato_observacion"]')
            .rules('add', {
                observacion: true,
                messages: {
                    observacion: "La observación es obligatoria."
                }
            });
}

/**
 * 
 * @returns {undefined}
 */
function initEdit() {

    // Si es una edición
    if (esEdit || $categoriaContrato !== __categoriaContratoOriginal) {

        // Deshabilito la búsqueda de clientes
        $busquedaClienteInput.prop('readonly', true);

        // Deshabilito el select de "ClaseContrato"
        $claseContratoSelect.select2('readonly', true);

        // Deshabilito las fechas de inicio de los ciclos de facturación
        $('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaInicio]')
                .prop('readonly', true).unbind();


        // Deshabilito las polizas de seguro
        $('input[id ^= adif_contablebundle_facturacion_contrato_polizasSeguro_]')
                .prop('readonly', true)
                .unbind();
    }
}

/**
 * 
 * @returns {undefined}
 */
function checkCalculaIVA() {

    // Si la clase del contrato es "Tenencias precarias", "Servidumbres de paso" o "Asuntos oficiales - Municipalidades":
    if ($claseContratoSelect.val() == __claseContratoTenenciaPrecaria
            || $claseContratoSelect.val() == __claseContratoServidumbrePaso
            || $claseContratoSelect.val() == __claseContratoAsuntoOficialMunicipalidad) {

        $calculaIVAInput.bootstrapSwitch('setState', false);
    }
    else {
        $calculaIVAInput.bootstrapSwitch('setState', true);
    }
}

/**
 * 
 * @returns {undefined}
 */
function initCategoriaContrato() {

    // Si es una prórroga
    if ($categoriaContrato === __categoriaContratoProrroga) {

        // Deshabilito el número de contrato
        $('#adif_contablebundle_facturacion_contrato_numeroContrato')
                .prop('readonly', true);

        // Deshabilito la fecha de inicio de contrato
        $('#adif_contablebundle_facturacion_contrato_fechaInicio')
                .prop('readonly', true).unbind();
    }

//    // Si es una adenda
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
 */
function setCiclosFacturacionReadonly($rowCicloFacturacion) {

    var cantidadFacturasPendientes = $rowCicloFacturacion
            .find('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _cantidadFacturasPendientes]')
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
function initAutocompleteCliente() {

    $('#adif_contablebundle_facturacion_contrato_cliente').autocomplete({
        source: __AJAX_PATH__ + 'cliente/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            selectCliente(event, ui);
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
function selectCliente(event, ui) {

    $('#adif_contablebundle_facturacion_contrato_cliente_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_facturacion_contrato_cliente_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_facturacion_contrato_idCliente').val(ui.item.id);
}

/**
 * 
 * @returns {undefined}
 */
function initClaseContratoHandler() {

    $claseContratoSelect.change(function () {
        initDetalleContrato();

        checkCalculaIVA();

        checkMuestraNumeroInmueble();
    });
}

/**
 * 
 * @returns {undefined}
 */
function checkMuestraNumeroInmueble() {

    var clasesContratoSinNroInmuebleArray = [
        __claseContratoChatarra,
        __claseContratoServidumbrePaso,
        __claseContratoPliego,
        __claseContratoLocacionServicio
    ];

    // Si la clase de contrato indica nro de inmueble
    if ($.inArray($claseContratoSelect.val(), clasesContratoSinNroInmuebleArray) == -1) {
        $('.numero-inmueble').show();
    }
    else {
        $('.numero-inmueble').hide();
        $numeroInmuebleInput.val(null);
    }
}

/**
 * 
 * @returns {undefined}
 */
function initDetalleContrato() {

    // Si la clase de contrato es "Chatarra"
    if ($claseContratoSelect.val() == __claseContratoChatarra) {
        $('.detalle-ciclo-facturacion').hide();

        $numeroLicitacionInput.prop('required', true);
        $fechaAperturaInput.prop('required', true);

        $('.detalle-contrato-chatarra').show();
    }
    else {
        $('.detalle-contrato-chatarra').hide();

        $numeroLicitacionInput.prop('required', false);
        $numeroLicitacionInput.val(null);
        $numeroLicitacionInput.keyup();

        $fechaAperturaInput.prop('required', false);
        $fechaAperturaInput.val(null);
        $fechaAperturaInput.keyup();

        $('.detalle-ciclo-facturacion').show();
    }
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
            inicioCicloValido = true;
            fechaFinContratoDate = getDateFromString($('#adif_contablebundle_facturacion_contrato_fechaFin').val());
            $('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaFin]').each(function (e) {
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

            addCicloFacturacionForm(collectionHolderCicloFacturacion);

            setFechaCicloFacturacion();

            initFechaFinCicloFacturacionHandler();

            setMasks();

            initCantidadFacturaChangeHandler();

            initCalculoSubtotalHandler();
        }
    });
}

/**
 * 
 * @returns {unresolved}
 */
function fechaFinCicloFacturacionValida() {

    if ($('.row_ciclo_facturacion').length > 0) {

        var $cicloFacturacionAnterior = $('.row_ciclo_facturacion').last();

        var $fechaFinCicloFacturacionAnteriorInput = $cicloFacturacionAnterior
                .find('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaFin]');

        // Si la fecha fin del ciclo anterior fue completada
        return $fechaFinCicloFacturacionAnteriorInput.valid();
    }

    return true;
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
            var $cicloFacturacionAnterior = $($('.row_ciclo_facturacion')[i]).prev();

            var $fechasFinCicloFacturacionAnteriorInput = $cicloFacturacionAnterior
                    .find('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaFin]');

            var fechaFinCicloFacturacionAnterior = $fechasFinCicloFacturacionAnteriorInput.last().val();
            var fechaFinCicloFacturacionAnteriorDate = getDateFromString(fechaFinCicloFacturacionAnterior);

            fechaFinCicloFacturacionAnteriorDate.setDate(fechaFinCicloFacturacionAnteriorDate.getDate() + 1);

            var $fechaInicioCicloFacturacion = $($('.row_ciclo_facturacion')[i])
                    .find('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaInicio]');

            $fechaInicioCicloFacturacion.datepicker("update", fechaFinCicloFacturacionAnteriorDate)
                    .prop('readonly', true);

            $fechaInicioCicloFacturacion.unbind();
        }
    }
    updateFinCicloFacturacion();
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

        initFechaPolizaValidation();

        initRangoPoliza();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initFechaPolizaValidation() {

    // Validacion del Formulario
    $formularioContrato.validate();

    $('input[id ^= "adif_contablebundle_facturacion_contrato_polizasSeguro_"][id $= "_fechaVencimiento"]').each(function (e) {
        $(this).rules('add', {
            fechaPoliza: true,
            messages: {
                fechaPoliza: "La fecha est&aacute; fuera del rango del contrato."
            }
        });
    });
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
function initFechaInicioContratoHandler() {

    $('#adif_contablebundle_facturacion_contrato_fechaInicio, #adif_contablebundle_facturacion_contrato_fechaFin').on('change', function () {

        updateFechaFinContrato();

        actualizarFechaInicioPrimerCicloFacturacion();

        updateFinCicloFacturacion();

        initRangoPoliza();
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateFechaFinContrato() {

    var fechaInicioContrato = $('#adif_contablebundle_facturacion_contrato_fechaInicio').val();
    var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);
//    fechaInicioContratoDate.setDate(1);
//    fechaInicioContratoDate.setMonth(fechaInicioContratoDate.getMonth() + 1);
//    fechaInicioContratoDate.setDate(fechaInicioContratoDate.getDate() - 1);
    $('#adif_contablebundle_facturacion_contrato_fechaFin')
            .datepicker('setStartDate', fechaInicioContratoDate);
}

/**
 * 
 * @returns {undefined}
 */
function actualizarFechaInicioPrimerCicloFacturacion() {

    var fechaInicioContrato = $('#adif_contablebundle_facturacion_contrato_fechaInicio').val();
    var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);

    var $fechaInicioPrimerCicloFacturacion = $('.row_ciclo_facturacion').first()
            .find('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaInicio]');

    $fechaInicioPrimerCicloFacturacion.datepicker("update", fechaInicioContratoDate)
            .prop('readonly', true);

    $fechaInicioPrimerCicloFacturacion.unbind();
}

/**
 * 
 * @returns {undefined}
 */
function initFechaFinCicloFacturacionHandler() {

    $('[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_]')
            .not("[id $= _cantidadFacturas], [id $= _importe]").on('change', function () {
        var $rowCicloFacturacion = $(this).parents('.row_ciclo_facturacion');
        actualizarCantidadFacturas($rowCicloFacturacion);
    });

    $('#adif_contablebundle_facturacion_contrato_diaVencimiento').on('change', function () {
        $('.row_ciclo_facturacion').each(function () {
            actualizarCantidadFacturas($(this));
        });
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

        var cantidadMeses = 0;

        var cantidadUnidadTiempo = $rowCicloFacturacion.find('input[id $= _cantidadUnidadTiempo]').val();

        // Si es el primer ciclo de facturación, o si es el último ciclo de facturacion
        if ($rowCicloFacturacion.data("index") === 1 || $rowCicloFacturacion.data("index") === getTotalCiclosFacturacion()) {
            cantidadMeses += calcularCantidadMesesCicloFacturacion(fechaInicioCicloFacturacion, fechaFinCicloFacturacion, true);
        }
        // Sino
        else {
            cantidadMeses += calcularCantidadMesesCicloFacturacion(fechaInicioCicloFacturacion, fechaFinCicloFacturacion);
        }

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
 * @param {type} fechaInicioCicloFacturacion
 * @param {type} fechaFinCicloFacturacion
 * @param {type} esPrimeroOUltimo
 * @returns {calcularCantidadMesesCicloFacturacion.cantidadMeses}
 */
function calcularCantidadMesesCicloFacturacion(fechaInicioCicloFacturacion, fechaFinCicloFacturacion, esPrimeroOUltimo) {

    esPrimeroOUltimo = (typeof esPrimeroOUltimo === "undefined") ? false : esPrimeroOUltimo;

    var fechaInicioCicloFacturacionDate = getDateFromString(fechaInicioCicloFacturacion);
    var fechaFinCicloFacturacionDate = getDateFromString(fechaFinCicloFacturacion);

    var cantidadMeses = monthDiff(fechaInicioCicloFacturacionDate, fechaFinCicloFacturacionDate);

    var diaVencimiento = $('#adif_contablebundle_facturacion_contrato_diaVencimiento').val();

    var diaFechaInicioCicloFacturacion = fechaInicioCicloFacturacionDate.getDate();
    var diaFechaFinCicloFacturacion = fechaFinCicloFacturacionDate.getDate();

    var mesFechaInicioCicloFacturacion = fechaInicioCicloFacturacionDate.getMonth();
    var mesFechaFinCicloFacturacion = fechaFinCicloFacturacionDate.getMonth();

    // Si el ciclo está comprendido en un mismo mes
    if (mesFechaInicioCicloFacturacion === mesFechaFinCicloFacturacion) {

        // Si el día de la fecha de inicio es menor o igual al dia de vencimiento
        // O si el día de la fecha de fin es mayor o igual al dia de vencimiento
        if (parseInt(diaFechaInicioCicloFacturacion) <= parseInt(diaVencimiento)
                || parseInt(diaFechaFinCicloFacturacion) >= parseInt(diaVencimiento)) {
            cantidadMeses++;
        }
        else {
            if (esPrimeroOUltimo) {
                cantidadMeses++;
            }
        }
    }
    else {
        // Si el día de la fecha de inicio es menor o igual al dia de vencimiento
        if (parseInt(diaFechaInicioCicloFacturacion) <= parseInt(diaVencimiento)) {
            cantidadMeses++;
        }

        // Si el día de la fecha de fin es mayor o igual al dia de vencimiento
        if (parseInt(diaFechaFinCicloFacturacion) >= parseInt(diaVencimiento)
                && mesFechaInicioCicloFacturacion !== mesFechaFinCicloFacturacion) {
            cantidadMeses++;
        }
    }

    return cantidadMeses;
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_facturacion_contrato_submit').on('click', function (e) {

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

    // Si el contrato NO es de "Chatarra"
    if ($claseContratoSelect.val() != __claseContratoChatarra) {

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
    }

    return true;
}

/**
 * 
 * @returns {undefined}
 */
function validarFechas() {

    // Fecha inicio del Contrato
    var fechaInicioContrato = $('#adif_contablebundle_facturacion_contrato_fechaInicio').val();
    var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);

    // Fecha fin del Contrato
    var fechaFinContrato = $('#adif_contablebundle_facturacion_contrato_fechaFin').val();
    var fechaFinContratoDate = getDateFromString(fechaFinContrato);

    // Fecha inicio del primer CicloFacturacion
    var fechaInicioPrimerCicloFacturacion = $('.row_ciclo_facturacion').first()
            .find('input[id^=adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id$=_fechaInicio]')
            .val();
    var fechaInicioPrimerCicloFacturacionDate = getDateFromString(fechaInicioPrimerCicloFacturacion);

    // Fecha fin del último CicloFacturacion
    var fechaFinUltimoCicloFacturacion = $('.row_ciclo_facturacion').last()
            .find('input[id^=adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id$=_fechaFin]')
            .val();
    var fechaFinUltimoCicloFacturacionDate = getDateFromString(fechaFinUltimoCicloFacturacion);

    // Si la fecha de inicio del contrato no coincide con la del primer ciclo de facturación
    if (fechaInicioContratoDate.getTime() !== fechaInicioPrimerCicloFacturacionDate.getTime()) {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "La fecha del primer ciclo de facturaci&oacute;n debe coincidir con el inicio del contrato."
        });

        show_alert(options);

        return false;
    }

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

    $('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaInicio]').each(function (e) {
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

    return true;
}

/**
 * 
 * @returns {undefined}
 */
function validarImporteTotal() {

    var importeTotalContrato = clearCurrencyValue(
            $('#adif_contablebundle_facturacion_contrato_importeTotal').val()
            );

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
function initCalculoSubtotalHandler() {

    $('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $=_cantidadFacturas]')
            .change(function () {
                actualizarSubtotal();
            });

    $('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $=_importe]')
            .change(function () {
                actualizarSubtotal();
            });
}

/**
 * 
 * @returns {undefined}
 */
function initCantidadFacturaChangeHandler() {

    $('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $=_cantidadFacturas]')
            .change(function () {
                $(this).parents('.row_ciclo_facturacion')
                        .find('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $=_cantidadFacturasPendientes]')
                        .val($(this).val());
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

    $('#adif_contablebundle_facturacion_contrato_totalCalculado').val(total).autoNumeric('destroy');
    setMasks();
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

    $claseContratoSelect
            .on("change", function (e) {
                if ($claseContratoSelect.val() == __claseContratoChatarra) {
                    $('#adif_contablebundle_facturacion_contrato_fechaDesocupacion').val(null);
                    $('#divFechaDesocupacion').hide();
                } else {
                    $('#divFechaDesocupacion').show();
                }

            });

}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
    });

    // Si NO es una edicion y ( el contrato es Original o es una Adenda )
    if (!esEdit && ($categoriaContrato === __categoriaContratoOriginal || $categoriaContrato === __categoriaContratoAdenda)) {

        initContratoValidate();

        $('#adif_contablebundle_facturacion_contrato_numeroContrato').inputmask({
            mask: "AA9999999999",
            numericInput: false,
            rightAlign: false,
            placeholder: '_'
        });
    }
}


/**
 * 
 * @returns {undefined}
 */
function initContratoValidate() {

    $.validator.addMethod("numeroContrato", function (value, element, param) {

        var numeroContrato = $('#adif_contablebundle_facturacion_contrato_numeroContrato')
                .val().replace(/_/g, '');

        return numeroContrato.length === 12;
    });

    $('form[name=adif_contablebundle_facturacion_contrato]').validate();

    // Validacion numero de contrato
    $('#adif_contablebundle_facturacion_contrato_numeroContrato').rules('add', {
        numeroContrato: true,
        messages: {
            numeroContrato: "El n&uacute;mero de contrato no es v&aacute;lido."
        }
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
 * @param {type} deleteLink
 * @returns {undefined}
 */
function updateDeleteLinks(deleteLink) {

    deleteLink.each(function () {

        $(this).tooltip();

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var deletableRow = $(this).closest('.row');

            show_confirm({
                msg: '¿Desea eliminar el registro?',
                callbackOK: function () {
                    deletableRow.hide('slow', function () {
                        deletableRow.remove();
                        actualizarSubtotal();
                        actualizarFechaInicioPrimerCicloFacturacion();
                        updateFinCicloFacturacion();
                    });
                }
            });

            e.stopPropagation();

        });
    });


}

/**
 * 
 * @returns {undefined}
 */
function updateFinCicloFacturacion() {

    // Fecha fin del Contrato
    var fechaFinContrato = $('#adif_contablebundle_facturacion_contrato_fechaFin').val();

    if (fechaFinContrato.length > 0) {
        var fechaFinContratoDate = getDateFromString(fechaFinContrato);
    }

    var fechaInicioContrato = $('#adif_contablebundle_facturacion_contrato_fechaInicio').val();

//    if (fechaInicioContrato.length > 0) {
//        var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);
//    }

    if ((fechaFinContrato.length > 0) && (fechaInicioContrato.length > 0)) {

        $('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaFin]').each(function (e) {
//
//
//            fechaInicioContratoDate.setDate(1);
//            fechaInicioContratoDate.setMonth(fechaInicioContratoDate.getMonth() + 1);
//            fechaInicioContratoDate.setDate(fechaInicioContratoDate.getDate() - 1);

            var fechaInicioCicloFacturacionActual = $(this).parents('.row_ciclo_facturacion')
                    .find('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaInicio]');

            var fechaInicioCicloFacturacionActualDate = getDateFromString(fechaInicioCicloFacturacionActual.val());

            fechaInicioCicloFacturacionActualDate.setDate(1);
            fechaInicioCicloFacturacionActualDate.setMonth(fechaInicioCicloFacturacionActualDate.getMonth() + 1);
            fechaInicioCicloFacturacionActualDate.setDate(fechaInicioCicloFacturacionActualDate.getDate() - 1);

            if (fechaFinContratoDate < fechaInicioCicloFacturacionActualDate) {
                $(this).datepicker('setStartDate', fechaFinContratoDate);
            } else {
                $(this).datepicker('setStartDate', fechaInicioCicloFacturacionActualDate);
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

        $('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaFin]').each(function (e) {

            var fechaInicioCicloFacturacionActual = $(this).parents('.row_ciclo_facturacion')
                    .find('input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaInicio]');

            var fechaInicioCicloFacturacionActualDate = getDateFromString(fechaInicioCicloFacturacionActual.val());

            fechaInicioCicloFacturacionActualDate.setDate(1);
            fechaInicioCicloFacturacionActualDate.setMonth(fechaInicioCicloFacturacionActualDate.getMonth() + 1);
            fechaInicioCicloFacturacionActualDate.setDate(fechaInicioCicloFacturacionActualDate.getDate() - 1);

            var ejercicioFechaInicioCicloFacturacionActual = fechaInicioCicloFacturacionActualDate.getFullYear();

            var fechaFinEjercicioDate = getDateFromString('31/12/' + ejercicioFechaInicioCicloFacturacionActual);

            $(this).datepicker('setEndDate', fechaFinEjercicioDate);

        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initCiclosHandler() {
    $(document).on('change', 'input[id ^= adif_contablebundle_facturacion_contrato_ciclosFacturacion_][id $= _fechaFin]', function (e) {
        setFechaCicloFacturacion();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRangoPoliza() {

    $('input[id ^= "adif_contablebundle_facturacion_contrato_polizasSeguro_"][id $= "_fechaInicio"]').each(function (e) {
        $(this).datepicker('setStartDate', getDateFromString($('#adif_contablebundle_facturacion_contrato_fechaInicio').val()));
    });

    $('input[id ^= "adif_contablebundle_facturacion_contrato_polizasSeguro_"][id $= "_fechaVencimiento"]').each(function (e) {
        $(this).datepicker('setStartDate', getDateFromString($('#adif_contablebundle_facturacion_contrato_fechaInicio').val()));
    });
}

/**
 * 
 * @returns {undefined}
 */
function initEditParcial() {

    $('#adif_contablebundle_facturacion_contrato_fechaDesocupacion')
            .datepicker('setEndDate', getCurrentDate());

    $('#adif_contablebundle_facturacion_contrato_fechaDesocupacion')
            .on('changeDate', function () {
                if ($('#adif_contablebundle_facturacion_contrato_fechaDesocupacion').val() != '') {
                    $('#adif_contablebundle_facturacion_contrato_estadoContrato').val(__idEstadoContratoDesocupado).change();
                }

            });

    // Si NO es una edicion total
    if (esEdit && __esEdicionTotal === 0) {

        $('input[type="text"].no-editable, textarea.no-editable').prop('readonly', true);
        $('#adif_contablebundle_facturacion_contrato_fechaDesocupacion').prop('readonly', false);

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
            if ($(this).attr('name') != 'adif_contablebundle_facturacion_contrato[fechaDesocupacion]') {
                readonlyDatePicker($(this), true);
            }
        });

        $('select.no-editable').select2('readonly', true);

        $('.prototype-link-remove-ciclo-facturacion').remove();

        $('.prototype-link-add-ciclo-facturacion').remove();
    }
}