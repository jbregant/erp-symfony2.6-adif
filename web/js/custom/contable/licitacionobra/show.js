
$(document).ready(function () {

    $('.tools-tramo a').click();

    initMostrarDetalleSaldoHandler();

    initDeleteLink();

});


/**
 * 
 * @returns {undefined}
 */
function initMostrarDetalleSaldoHandler() {

    // Handler al apretar el botón de "Ver detalle"
    $('.link-detalle-saldo').click(function () {

        var idTramo = $(this).data('id-tramo');

        var data = {
            id_tramo: idTramo,
            sin_filtro: true
        };

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'documento_financiero/index_table_tramo/',
            data: data,
            success: function (documentosFinancieros) {

                var $contenidoDetalle = $('<div class="portlet-body">');

                $contenidoDetalle.append($('<div class="table-toolbar">'));

                $contenidoDetalle
                        .append(
                                $('<table id="table-documentos-financieros" dataexport-title="documentos-financieros" \n\
                                    class="table table-striped table-hover table-bordered table-condensed dt-multiselect export-excel">')
                                .append(
                                        $('<thead>')
                                        .append(
                                                $('<tr class="headers">')
                                                .append($('<th class="no-order entity_id">'))
                                                .append($('<th class="text-center table-checkbox no-order">')
                                                        .append($('<input type="checkbox" class="group-checkable" data-set="#table-documentos-financieros .checkboxes" />'
                                                                ))
                                                        )
												//.append($('<th class="nowrap text-center">').text('ID'))
                                                .append($('<th class="nowrap text-center">').text('Tipo'))
                                                .append($('<th date class="nowrap text-center">').text('Fecha inicio'))
                                                .append($('<th date class="nowrap text-center">').text('Fecha fin'))
                                                .append($('<th currency class="nowrap text-center">').text('Monto sin IVA'))
                                                .append($('<th currency>').text('Monto fondo reparo'))
												.append($('<th currency>').text('Monto total documento financiero'))
                                                )
                                        )
                                .append($('<tbody>'))
                                );

                jQuery.each(documentosFinancieros, function (index, documentoFinanciero) {

                    $contenidoDetalle.find('tbody')
                            .append($('<tr>')
                                    .append($('<td>').text(documentoFinanciero['id']))
                                    .append($('<td class="text-center">')
                                            .append($('<input type="checkbox" class="checkboxes" value="" />')))
									//.append($('<td class="nowrap">').text(documentoFinanciero['id']))
                                    .append($('<td class="nowrap">').text(documentoFinanciero['tipoDocumentoFinanciero'] + ' ' + documentoFinanciero['numero']))
                                    .append($('<td class="nowrap">').text(documentoFinanciero['fechaDocumentoFinancieroInicio']))
                                    .append($('<td class="nowrap">').text(documentoFinanciero['fechaDocumentoFinancieroFin']))
                                    .append($('<td class="nowrap money-format">').text(documentoFinanciero['montoSinIVA']))
                                    .append($('<td class="money-format">').text(documentoFinanciero['montoFondoReparo']))
									.append($('<td class="money-format">').text(documentoFinanciero['montoTotalDocumentoFinanciero']))
                                    );
                });

                show_dialog({
                    titulo: 'Detalle de saldo',
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
            }
        });

    });
}


/**
 * 
 * @returns {undefined}
 */
function initTable() {

    $('.modal-dialog').css('width', '80%');

    $('.modal-footer').find('.btn-default').remove();
    $('.modal-footer').find('.btn-primary').text('Cerrar');

    setMasks();

    var options = {
        "searching": false,
        "ordering": true,
        "info": false,
        "paging": true
    };

    dt_init($('#table-documentos-financieros'), options);
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

function initDeleteLink() {

    // BOTON ELIMINAR
    $('.link-eliminar-tramo').click(function (e) {

        e.preventDefault();

        var url = $(this).attr('back-url');

        show_confirm({
            msg: '¿Desea eliminar el tramo?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });
}