
var id_oc;

var __TIPO_COMPROBANTE_NC = 3;

var caisProveedor = {};

var $formularioComprobanteCompra = $('form[name=adif_contablebundle_comprobantecompra]');

var $letraComprobanteSelect = $('#adif_contablebundle_comprobantecompra_letraComprobante');

$(document).ready(function () {

    initNC();

    initAutocompleteProveedor();

    initComprobantesHandler();

    initFechaComprobanteValidation();

    initPuntoVentaHandler();

    initSubmitButton();

});

function initNC() {
    $('#adif_contablebundle_comprobantecompra_tipoComprobante').val(__TIPO_COMPROBANTE_NC).select2();
    $('#adif_contablebundle_comprobantecompra_tipoComprobante').select2('readonly', true);
}

function readOnlyInputs() {
    $('input[id^= adif_contablebundle_comprobante][id$=comprobante]').prop('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=descripcion]').prop('readonly', true);
    $('select[id^= adif_contablebundle_comprobante][id$=bonificacionTipo]').select2('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=bonificacionValor]').prop('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=neto]').prop('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=montoIva]').prop('readonly', true);
    $('select[id^= adif_contablebundle_comprobante][id$=alicuotaIva]').select2('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=comprobanteCompra]').prop('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=_cantidad]').prop('readonly', false);
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteProveedor() {

    $('#adif_contablebundle_comprobantecompra_proveedor').autocomplete({
        source: __AJAX_PATH__ + 'proveedor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {

            completarInformacionProveedor(event, ui);

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

    $('#adif_contablebundle_comprobantecompra_proveedor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_comprobantecompra_proveedor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_comprobantecompra_idProveedor').val(ui.item.id);
    $('#legend_oc_proveedor').html(': ' + ui.item.razonSocial + ' - CUIT: ' + ui.item.CUIT);

    caisProveedor = ui.item.cais;

    $('.comprobantes_compra').hide();

    $.ajax({
        url: __AJAX_PATH__ + 'comprobantescompra/index_table_comprobantes/',
        data: {id_proveedor: ui.item.id}
    }).done(function (result) {

        var comprobantes = JSON.parse(result).data;

        $('#table_comprobantes_compra_proveedor tbody').empty();
        $('.comprobantes_compra').show();
        $('#nc_agregar_renlgon_ctn').hide();
        $('.div_ctn_seccion_adicionales').hide();

        $(comprobantes).each(function (comprobante) {

            var $tr = $('<tr />', {id_comprobante: this[0], style: 'cursor: pointer;'});

            $tr.on('click', function () {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $(this).find('input[type=checkbox]').prop('checked', false);
                } else {
                    $(this).addClass('active');
                    $(this).find('input[type=checkbox]').prop('checked', true);
                }
            });

            $tr.append('<td class="text-center"><input type="checkbox" class="checkboxes" value="' + this[0] + '" /></td>');
            $('<td />', {text: this[1]}).addClass('nowrap').appendTo($tr);
            $('<td />', {text: this[2]}).addClass('nowrap').appendTo($tr);
            $('<td />', {text: this[3]}).addClass('nowrap').appendTo($tr);
            $('<td />', {text: this[4]}).addClass('nowrap').appendTo($tr);
            $('<td />', {text: this[5]}).addClass('nowrap').appendTo($tr);
            $('<td />', {text: this[6]}).addClass('text-right nowrap').appendTo($tr);
            $('<td />', {text: this[7]}).addClass('text-right nowrap').appendTo($tr);

            $('#table_comprobantes_compra_proveedor tbody').append($tr);
        });

        $(document).trigger(event);
    });
}

/**
 * 
 * @returns {undefined}
 */
function initComprobantesHandler() {
    $(document).on('click', '#buscar_renglones_comprobantes', function (e, tr) {

        bloquear();

        ids = [];

        $('#table_comprobantes_compra_proveedor tr').has('input[type=checkbox]:checked').each(function () {
            ids.push($(this).attr('id_comprobante'));
        });

        $.ajax({
            type: 'POST',
            url: __AJAX_PATH__ + 'comprobantescompra/index_table_renglones_comprobantes/',
            data: {ids_comprobantes: JSON.stringify(ids)}
        }).done(function (renglonesComprobante) {

            $('.ctn_rows_renglon_comprobante').empty();

            $.each(renglonesComprobante, function (i, renglon) {
                crear_renglon_comprobante(renglon.idRenglonOC, renglon.descripcion, renglon.denominacionBienEconomico, renglon.cantidad, renglon.precioUnitario, renglon.idAlicuotaIva, renglon.bonificacionTipo, renglon.bonificacionValor, renglon.montoNeto, renglon.montoIva);
                $('.row_renglon_comprobante').last().find('input[id^= adif_contablebundle_comprobante][id$=comprobanteCompra]').val(renglon.comprobante);
                $('.row_renglon_comprobante').last().find('input[id^= adif_contablebundle_comprobante][id$=idComprobante]').val(renglon.idComprobante);
                $('.row_renglon_comprobante').last().find('input[id^= adif_contablebundle_comprobante][id$=idRenglonComprobante]').val(renglon.id);
            });

            restringir_iva(false);
            init_validaciones();
            recalcular_netos();

            readOnlyInputs();

        });
    });
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
                    $formularioComprobanteCompra.submit();
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
}