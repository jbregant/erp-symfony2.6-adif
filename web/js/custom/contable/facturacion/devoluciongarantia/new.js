
var isEdit = $('[name=_method]').length > 0;

var cuponSeleccionadoId;

var cuponSeleccionadoSaldoPendiente;

var $formulario = $('form[name="adif_contablebundle_facturacion_devoluciongarantia"]');


$(document).ready(function () {

    initValidate();

    initAutocompleteCliente();

    initSeleccionarCuponHandler();

    initSubmitButton();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion para total de la devolucion de garantia
    $.validator.addMethod("totalDevolucionGarantia", function (value, element, param) {

        var totalDocumentoFinanciero = parseFloat(clearCurrencyValue($('#adif_contablebundle_facturacion_devoluciongarantia_importe').val()));

        // Valido que el total no supere el saldo del cupon
        return totalDocumentoFinanciero <= cuponSeleccionadoSaldoPendiente;

    });

    // Validacion del Formulario
    $formulario.validate();

    // Valido el total de la devolucion de garantia
    $('#adif_contablebundle_facturacion_devoluciongarantia_importe').rules('add', {
        totalDevolucionGarantia: true,
        messages: {
            totalDevolucionGarantia: "El importe no puede superar el saldo del cupón."
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteCliente() {

    $('#adif_contablebundle_facturacion_devoluciongarantia_cliente').autocomplete({
        source: __AJAX_PATH__ + 'cliente/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {

            completarCupones(event, ui, null);
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
 * @param {type} idCuponSeleccionado
 * @returns {undefined}
 */
function completarCupones(event, ui, idCuponSeleccionado) {

    $('#adif_contablebundle_facturacion_devoluciongarantia_cliente_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_facturacion_devoluciongarantia_cliente_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_facturacion_devoluciongarantia_idCliente').val(ui.item.id);

    $('#table_cupones').hide();

    $.ajax({
        url: __AJAX_PATH__ + 'comprobanteventa/index_table_cupones/',
        data: {id_cliente: ui.item.id}
    }).done(function (result) {

        var cupones = JSON.parse(result).data;

        cuponSeleccionadoId = null;

        $('#adif_contablebundle_facturacion_devoluciongarantia_cuponGarantia')
                .select2('val', null);

        $('#table_cupones tbody').empty();
        $('#table_cupones').show();

        $(cupones).each(function () {

            var $tr = $('<tr />', {
                id_cupon: this[0],
                style: 'cursor: pointer;'}
            );

            $tr.on('click', function () {
                $(this).parents('tbody').find('tr').removeClass('active');
                $(this).addClass('active');
            });

            $('<td />', {text: this[1]}).appendTo($tr);
            $('<td />', {text: this[2]}).appendTo($tr);
            $('<td />', {text: this[3]}).appendTo($tr);
            $('<td />', {text: this[4]}).addClass('money-format').appendTo($tr);
            $('<td />', {text: this[5]}).addClass('money-format saldoPendiente').appendTo($tr);

            $('#table_cupones tbody').append($tr);

            if (idCuponSeleccionado && this[0] == idCuponSeleccionado) {

                $tr.trigger('click');
            }
        });

        setMasks();
    });
}


/**
 * 
 * @returns {undefined}
 */
function initSeleccionarCuponHandler() {

    $(document).on('click', '#table_cupones tbody tr', function (e, tr) {

        // Seteo el ID del Cupon seleccionado
        cuponSeleccionadoId = $(this).attr('id_cupon');

        $('#adif_contablebundle_facturacion_devoluciongarantia_cuponGarantia')
                .select2('val', cuponSeleccionadoId);

        // Seteo el saldo pendiente del Cupon seleccionado
        cuponSeleccionadoSaldoPendiente = parseFloat(clearCurrencyValue($(this).find('.saldoPendiente').text()));

        // Marco el TR seleccionado como "selected"
        $('tr.selected').removeClass('selected');
        $(this).addClass('selected');
        $(this).removeClass('active');
    });
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_facturacion_devoluciongarantia_submit').on('click', function (e) {

        if (hayCuponSeleccionado()) {

            if ($formulario.valid()) {

                e.preventDefault();

                show_confirm({
                    msg: '¿Desea guardar la devoluci&oacute;n de garant&iacute;a?',
                    callbackOK: function () {

                        $formulario.submit();
                    }
                });

                e.stopPropagation();

                return false;
            }
        }

        return false;
    });
}

/*+
 * 
 * @returns {Boolean}
 */
function hayCuponSeleccionado() {

    if (typeof cuponSeleccionadoId === "undefined") {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe seleccionar un cup&oacute;n para continuar."
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
