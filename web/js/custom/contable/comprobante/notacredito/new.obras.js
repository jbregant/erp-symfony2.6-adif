
var id_oc;

var __TIPO_COMPROBANTE_NC = 3;

var caisProveedor = {};

var $formularioComprobanteObra = $('form[name=adif_contablebundle_comprobanteobra]');

var $letraComprobanteSelect = $('#adif_contablebundle_comprobanteobra_letraComprobante');

$(document).ready(function () {

    initNC();

    initAutocompleteProveedor();

    initComprobantesHandler();

    initFechaComprobanteValidation();

    initPuntoVentaHandler();

    initSubmitButton();

});

function initNC() {
    $('#adif_contablebundle_comprobanteobra_tipoComprobante').val(__TIPO_COMPROBANTE_NC).select2();
    $('#adif_contablebundle_comprobanteobra_tipoComprobante').select2('readonly', true);
}

function readOnlyInputs() {
    $('input[id^= adif_contablebundle_comprobante][id$=comprobante]').prop('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=descripcion]').prop('readonly', true);
    $('select[id^= adif_contablebundle_comprobante][id$=bonificacionTipo]').select2('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=bonificacionValor]').prop('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=neto]').prop('readonly', true);
    $('input[id^= adif_contablebundle_comprobante][id$=montoIva]').prop('readonly', true);
    $('select[id^= adif_contablebundle_comprobante][id$=alicuotaIva]').select2('readonly', true);

//    $('input[id^= adif_contablebundle_comprobante][id$=precioUnitario]').prop('readonly', true);
    $('select[id^= adif_contablebundle_comprobante][id$=regimenRetencionSUSS]').select2('readonly', true);
    $('select[id^= adif_contablebundle_comprobante][id$=regimenRetencionIVA]').select2('readonly', true);
    $('select[id^= adif_contablebundle_comprobante][id$=regimenRetencionGanancia]').select2('readonly', true);
    $('select[id^= adif_contablebundle_comprobante][id$=regimenRetencionSUSS]').select2('readonly', true);

}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteProveedor() {

    $('#adif_contablebundle_comprobanteobra_proveedor').autocomplete({
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

    $('#adif_contablebundle_comprobanteobra_proveedor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_comprobanteobra_proveedor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_comprobanteobra_idProveedor').val(ui.item.id);
    $('#legend_oc_proveedor').html(': ' + ui.item.razonSocial + ' - CUIT: ' + ui.item.CUIT);

    caisProveedor = ui.item.cais;

    $('.comprobantes_obra').hide();

    $.ajax({
        url: __AJAX_PATH__ + 'comprobanteobra/index_table_comprobantes/',
        data: {id_proveedor: ui.item.id}
    }).done(function (result) {

        var comprobantes = JSON.parse(result).data;

        $('#table_comprobantes_obra_proveedor tbody').empty();
        $('.comprobantes_obra').show();
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
            $('<td />', {text: this[4]})/*.addClass('nowrap')*/.appendTo($tr);
            $('<td />', {text: this[5]})/*.addClass('nowrap')*/.appendTo($tr);
            $('<td />', {text: this[6]})/*.addClass('nowrap')*/.appendTo($tr);
            $('<td />', {text: this[7]}).addClass('text-right nowrap').appendTo($tr);
            $('<td />', {text: this[8]}).addClass('text-right nowrap').appendTo($tr);

            $('#table_comprobantes_obra_proveedor tbody').append($tr);
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

        $('#table_comprobantes_obra_proveedor tr').has('input[type=checkbox]:checked').each(function () {
            ids.push($(this).attr('id_comprobante'));
        });

        $.ajax({
            type: 'POST',
            url: __AJAX_PATH__ + 'comprobanteobra/index_table_renglones_comprobantes/',
            data: {ids_comprobantes: JSON.stringify(ids)}
        }).done(function (renglonesComprobante) {

            $('.ctn_rows_renglon_comprobante').empty();

            $.each(renglonesComprobante, function (i, renglon) {

                crearRenglonComprobanteObra(renglon.descripcion, renglon.cantidad, renglon.precioUnitario, renglon.montoNeto, renglon.idAlicuotaIva, renglon.montoIva, renglon.idRegimenIVA, renglon.idRegimenIIBB, renglon.idRegimenGanancias, renglon.idRegimenSUSS);
                $('.row_renglon_comprobante').last().find('.comprobanteObraDelRenglon').html(renglon.comprobante);
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

    $('#adif_contablebundle_comprobanteobra_fechaComprobante').datepicker('setEndDate', currentDate);
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_comprobanteobra_submit').on('click', function (e) {

        if ($formularioComprobanteObra.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el comprobante?',
                callbackOK: function () {
                    $formularioComprobanteObra.submit();
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

/**
 * 
 * @param {type} descripcion
 * @param {type} cantidad
 * @param {type} precioUnitario
 * @param {type} montoNeto
 * @param {type} idAlicuotaIva
 * @param {type} montoIva
 * @param {type} idRegimenIVA
 * @param {type} idRegimenIIBB
 * @param {type} idRegimenGanancias
 * @param {type} idRegimenSUSS
 * @param {type} idRenglon
 * @returns {undefined}
 */
function crearRenglonComprobanteObra(descripcion, cantidad, precioUnitario, montoNeto, idAlicuotaIva, montoIva, idRegimenIVA, idRegimenIIBB, idRegimenGanancias, idRegimenSUSS, idRenglon) {

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

    var row_sel_prefix = '#adif_contablebundle_comprobanteobra_renglonesComprobante_';

    nuevoRow.find(row_sel_prefix + indiceNuevo + '_descripcion').val(descripcion ? descripcion : '');
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_cantidad').val(cantidad ? cantidad : 1);
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_precioUnitario').val(precioUnitario ? precioUnitario : 0);
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_montoNeto').val(montoNeto ? montoNeto : 0);

    // Regimenes de retencion
    if (idRegimenIVA) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_regimenRetencionIVA').val(idRegimenIVA);
    }
    if (idRegimenIIBB) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_regimenRetencionIIBB').val(idRegimenIIBB);
    }
    if (idRegimenGanancias) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_regimenRetencionGanancias').val(idRegimenGanancias);
    }
    if (idRegimenSUSS) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_regimenRetencionSUSS').val(idRegimenSUSS);
    }
    //
    if (idRenglon) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_idRenglon').val(idRenglon);
    }

    if (idAlicuotaIva) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_alicuotaIva').val(idAlicuotaIva);
    }

    nuevoRow.find(row_sel_prefix + indiceNuevo + '_montoIva').val(montoIva ? montoIva : 0);

    nuevoRow.find('select').select2();

    $('.row_headers_renglon_comprobante').show();
    $('.row_footer_renglon_comprobante').show();

    initCurrencies();

    initRenglonComprobanteHandler();

    // addValidacionPrecioUnitario();
}