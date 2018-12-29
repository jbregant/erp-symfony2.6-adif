
var comprobanteSeleccionadoId;

var $formulario = $('form[name="adif_contablebundle_pagoparcial"]');

$(document).ready(function () {

    initValidate();

    initComprobanteTable();

    initAutocompleteProveedor();

    initSubmitButton();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $formulario.validate();
}

/**
 * 
 * @returns {undefined}
 */
function initComprobanteTable() {

    var options = {
        "searching": false,
        "ordering": true,
        "info": true,
        "paging": true,
        "pageLength": 12,
        "lengthMenu": [[12, 24, 48, 100, -1], [12, 24, 48, 100, "Todos"]],
        "drawCallback": function () {

            setMasks();

            initSeleccionarComprobanteHandler();

            seleccionarComprobanteActivo();
        }
    };

    dt_init($('#table_comprobante_proveedor'), options);
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_pagoparcial_submit').on('click', function (e) {

        if ($formulario.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el pago parcial?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {
                            id_comprobante: comprobanteSeleccionadoId
                        };

                        $formulario.addHiddenInputData(json);
                        $formulario.submit();
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
function initAutocompleteProveedor() {

    $('#adif_contablebundle_pagoparcial_proveedor').autocomplete({
        source: __AJAX_PATH__ + 'proveedor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            completarProveedor(event, ui, null);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };
}

function completarProveedor(event, ui, idComprobanteSeleccionado) {

    $('#adif_contablebundle_pagoparcial_proveedor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_pagoparcial_proveedor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_pagoparcial_idProveedor').val(ui.item.id);

    completarComprobantes(ui, idComprobanteSeleccionado);
}

/**
 * 
 * @param {type} ui
 * @param {type} idComprobanteSeleccionado
 * @returns {undefined}
 */
function completarComprobantes(ui, idComprobanteSeleccionado) {

    // Elimino todos los renglones previamente seleccionados
    $('tr.selected').each(function () {
        eliminarComprobante($(this));
    });

    $('.row_table_comprobante_proveedor').hide();

    $.ajax({
        type: "POST",
        url: __AJAX_PATH__ + 'pago_parcial/index_table_comprobante_proveedor/',
        data: {id_proveedor: ui.item.id}
    }).done(function (result) {

        var comprobantes = JSON.parse(result).data;

        $('#table_comprobante_proveedor').DataTable().rows().remove().draw();

        $('#table_comprobante_proveedor tbody').empty();

        $('.row_table_comprobante_proveedor').show();

        $(comprobantes).each(function () {

            var $tr = $('<tr />', {
                id_comprobante: this[0],
                monto_total: this[5],
                style: 'cursor: pointer;'});

            $tr.on('click', function () {
                $(this).parents('tbody').find('tr').removeClass('active');
                $(this).addClass('active');
            });

            $('<td />', {text: this[1]}).addClass('fecha nowrap').appendTo($tr);
            $('<td />', {text: this[2]}).addClass('tipoComprobante nowrap').appendTo($tr);
            $('<td />', {text: this[3]}).addClass('numeroComprobante nowrap').appendTo($tr);
            $('<td />', {text: this[4]}).addClass('importeTotal currency').appendTo($tr);

            $('#table_comprobante_proveedor').DataTable().row.add($tr);

            if (idComprobanteSeleccionado && this[0] == idComprobanteSeleccionado) {

                $tr.addClass('activo');
            }
        });

        $('#table_comprobante_proveedor').DataTable().draw();

    });
}


/**
 * 
 * @param {type} comprobanteSeleccionado
 * @returns {undefined}
 */
function eliminarComprobante(comprobanteSeleccionado) {

    bloquear();

    comprobanteSeleccionadoId = parseInt(comprobanteSeleccionado.attr('id_comprobante'));

    $('.row_renglon_comprobante[id_comprobante=' + comprobanteSeleccionadoId + ']')
            .remove();

    // Si era el último renglón
    if ($('.row_renglon_comprobante').length === 0) {
        $('.row_headers_renglon_comprobante').hide();
        $('.row_footer_renglon_comprobante').hide();
    }

    desbloquear();
}

/**
 * 
 * @returns {undefined}
 */
function initSeleccionarComprobanteHandler() {

    $(document).off().on('click', '#table_comprobante_proveedor tbody tr', function (e) {

        if ($(this).hasClass('selected')) {

            eliminarComprobante($(this));

            // Desmarco el TR seleccionado como "selected"
            $(this).removeClass('selected');
            $(this).addClass('active');

            $('select[id ^= adif_contablebundle_comprobante][id $= comprobante]')
                    .val(null);
        } else {


            $('#table_comprobante_proveedor').DataTable().rows('.selected').nodes().each(function () {

                // Elimino todos los comprobantes previamente seleccionados
                eliminarComprobante($(this));

                // Desmarco los TR seleccionados
                $(this).removeClass('selected');
            });

            // Seteo el ID del Comprobante seleccionado
            comprobanteSeleccionadoId = $(this).attr('id_comprobante');

            $('select[id ^= adif_contablebundle_comprobante][id $= comprobante]')
                    .val(comprobanteSeleccionadoId);

            // Marco el TR seleccionado como "selected"
            $(this).addClass('selected');
            $(this).removeClass('active');


            $('#adif_contablebundle_pagoparcial_importe').rules('add', {
                valor_maximo: parseFloat($(this).attr('monto_total').replace(',', '.'))
            });

        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function seleccionarComprobanteActivo() {

    $('#table_comprobante_proveedor tbody tr.activo').click();
}

/**
 * 
 * @returns {undefined}
 */
function validForm() {

    // Si seleccionó un Comprobante
    if (!comprobanteSeleccionadoId) {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe seleccionar un comprobante para continuar."
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
        $(this).autoNumeric('init', {vMin: -99999999, aSign: '$ ', aSep: '.', aDec: ','});
    });
}