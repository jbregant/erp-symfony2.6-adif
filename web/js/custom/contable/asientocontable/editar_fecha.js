
/**
 * 
 * @returns {undefined}
 */
function initEditarFechaAsientoContableHandler() {

    $(document).on('click', '.link-editar-fecha-asiento', function (e) {

        e.preventDefault();

        var currentDate = getCurrentDate();

        $(this).parents('.mensaje-asiento-contable').find('.fecha-asiento-contable')
                .html('<input type="text" id="fecha-asiento" class="input form-control input-sm datepicker inline" style="height: auto; width:120px">\n\
                       <a class="btn btn-xs blue tooltips guardar-fecha" data-original-title="Guardar" href="#"><i class="fa fa-check"></i></a>'
                        );

        initDatepicker($('#fecha-asiento'));

        var fechaMesCerradoSuperior = $('.mensaje-asiento-contable').data('fecha-mes-cerrado-superior');

        $('#fecha-asiento').datepicker("update", currentDate);

        $('#fecha-asiento').datepicker('setStartDate', fechaMesCerradoSuperior);

        $('#fecha-asiento').datepicker('setEndDate', currentDate);

        $('#fecha-asiento').prop('readonly', true);

        customDatepickerInit();

        $('.guardar-fecha').click(function () {

            var data = {
                numero_asiento: $('#numero-asiento').data('numero-asiento'),
                fecha: $('#fecha-asiento').val()
            };

            $.ajax({
                type: "POST",
                data: data,
                url: __AJAX_PATH__ + 'asientocontable/editar_fecha/'
            }).done(function (response) {

                if (response.status === 'OK') {

                    customEditarFechaAsientoContableHandler();

                    window.onbeforeunload = function () {
                        blockBody();
                    };

                    location.reload();
                }
                else {

                    if (typeof response.message !== "undefined") {
                        showFlashMessage('danger', response.message);
                    }
                }
            });
        });
    });
    /*
     $(document).on('finish_edita_fecha_asiento', function (e) {
     location.reload();
     });
     */
}

/**
 * 
 * @returns {undefined}
 */
function updateFechaComprobanteFromAsientoContable() {

    var data = {
        id_comprobante: $('.mensaje-asiento-contable').data('id-comprobante'),
        numero_asiento: $('#numero-asiento').data('numero-asiento'),
        fecha: $('#fecha-asiento').val()
    };

    $.ajax({
        type: "POST",
        data: data,
        async: false,
        url: __AJAX_PATH__ + 'comprobantes/editar_fecha/'
    }).done(function (response) {

        return true;
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateFechaCobrosFromAsientoContable() {

    var data = {
        id_cobros: $('.mensaje-asiento-contable').data('id-cobros'),
        numero_asiento: $('#numero-asiento').data('numero-asiento'),
        fecha: $('#fecha-asiento').val()
    };

    $.ajax({
        type: "POST",
        data: data,
        async: false,
        url: __AJAX_PATH__ + 'rengloncobranza/editar_fecha_cobros/'
    }).done(function (response) {

        return true;
    });
}

/**
 * 
 * @returns {undefined}
 */
function  updateFechaMovimientosFromAsientoContable() {

    var data = {
        id_movimientos: $('.mensaje-asiento-contable').data('id-movimientos'),
        numero_asiento: $('#numero-asiento').data('numero-asiento'),
        fecha: $('#fecha-asiento').val()
    };

    $.ajax({
        type: "POST",
        data: data,
        async: false,
        url: __AJAX_PATH__ + 'rengloncobranza/editar_fecha_movimientos/'
    }).done(function (response) {
        return true;
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateFechaChequesFromAsientoContable() {

    var data = {
        id_cheques: $('.mensaje-asiento-contable').data('id-cheques'),
        numero_asiento: $('#numero-asiento').data('numero-asiento'),
        fecha: $('#fecha-asiento').val()
    };

    $.ajax({
        type: "POST",
        data: data,
        async: false,
        url: __AJAX_PATH__ + 'rengloncobranza/editar_fecha_cheques/'
    }).done(function (response) {

        return true;
    });
}


/**
 * 
 * @returns {undefined}
 */
function updateFechaOrdenPagoFromAsientoContable() {

    var data = {
        id_orden_pago: $('.mensaje-asiento-contable').data('id-orden-pago'),
        numero_asiento: $('#numero-asiento').data('numero-asiento'),
        fecha: $('#fecha-asiento').val()
    };

    $.ajax({
        type: "POST",
        data: data,
        async: false,
        url: __AJAX_PATH__ + 'ordenpago/editar_fecha/'
    }).done(function (response) {

        return true;
    });
}

/**
 * 
 * @returns {Boolean}
 */
function customDatepickerInit() {

}

/**
 * 
 * @returns {undefined}
 */
function updateFechaAnulacionComprobanteFromAsientoContable() {

    var data = {
        id_comprobante: $('.mensaje-asiento-contable').data('id-comprobante'),
        numero_asiento: $('#numero-asiento').data('numero-asiento'),
        fecha: $('#fecha-asiento').val()
    };

    $.ajax({
        type: "POST",
        data: data,
        async: false,
        url: __AJAX_PATH__ + 'comprobantes/editar_fecha_anulacion/'
    }).done(function (response) {

        return true;
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateFechaAnulacionOrdenPagoFromAsientoContable() {

    var data = {
        id_orden_pago: $('.mensaje-asiento-contable').data('id-orden-pago'),
        numero_asiento: $('#numero-asiento').data('numero-asiento'),
        fecha: $('#fecha-asiento').val()
    };

    $.ajax({
        type: "POST",
        data: data,
        async: false,
        url: __AJAX_PATH__ + 'ordenpago/editar_fecha_anulacion/'
    }).done(function (response) {

        return true;
    });
}