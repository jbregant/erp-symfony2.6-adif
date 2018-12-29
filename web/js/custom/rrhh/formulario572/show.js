$(document).ready(function () {

    initMostrarDetalleHandler();
});

/**
 * 
 * @returns {undefined}
 */
function initMostrarDetalleHandler() {

    $('.agrupado').click(function () {
        $('.div-agrupado').show();
        $('.div-todos').hide();
    });
    $('.todos').click(function () {
        $('.div-agrupado').hide();
        $('.div-todos').show();
    });

    $('.monto-concepto').mouseover(function () {
        $(this).find('.link-detalle-concepto').show();
    });

    $('.monto-concepto').mouseout(function () {
        $(this).find('.link-detalle-concepto').hide();
    });

    // Handler al apretar el botón de "Ver detalle"
    $('.link-detalle-concepto').click(function () {

        var idConcepto = $(this).data('concepto');


        var $contenidoDetalle = $('<div class="portlet-body">');

        $contenidoDetalle.append($('<div class="table-toolbar">'));

        $contenidoDetalle
                .append(
                        $('<table id="table-conceptos-formulario572" dataexport-title="conceptos-formulario572" \n\
                                    class="table datatable table-striped table-hover table-bordered table-condensed dt-multiselect export-excel">')
                        .append(
                                $('<thead>')
                                .append(
                                        $('<tr class="headers">')
                                        .append($('<th class="no-order entity_id">'))
                                        .append($('<th class="text-center table-checkbox no-order">')
                                                .append($('<input type="checkbox" class="group-checkable" data-set="#table-conceptos-formulario572 .checkboxes" />'
                                                        ))
                                                )
                                        .append($('<th>').text('Concepto'))
                                        .append($('<th>').text('Período'))
                                        .append($('<th currency>').text('Monto'))
                                        .append($('<th>').text('CUIT'))
                                        .append($('<th>').text('Detalle'))
                                        )
                                )
                        .append($('<tbody></tbody>'))
                        );

        $('tr[idConcepto=' + idConcepto + ']').each(function (index) {
            $row = $(this).clone();
            $row.prepend('<td>' + index + '</td><td class="text-center"><input type="checkbox" class="checkboxes" value="" /></td>');
            $contenidoDetalle.find('tbody').append($row);
        });

        show_dialog({
            titulo: 'Detalle del concepto ' + $(this).data('concepto-nombre'),
            contenido: $contenidoDetalle,
            callbackCancel: function () {
                desbloquear();
                return;
            },
            callbackSuccess: function () {
                desbloquear();
                return;
            }
        });

        initTable();

    });
}

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    $('.modal-footer').find('.btn-default').remove();
    $('.modal-footer').find('.btn-primary').text('Cerrar');

    setMasks();

    var options = {
        "searching": false,
        "ordering": true,
        "info": false,
        "paging": true
    };

    dt_init($('#table-conceptos-formulario572'), options);
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
    });
}