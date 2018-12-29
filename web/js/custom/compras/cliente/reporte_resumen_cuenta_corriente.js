index = 0;

var dt_table_reporte_column_index = {
    id: index++,
    multiselect: index++,
    codigo: index++,
    razonSocial: index++,
    cuit: index++,
    tipoContratacion: index++,
    numeroContrato: index++,
    cuentaContable: index++,
    saldoPendienteCobro: index++,
    saldoComprobantesSinContrato: index++,
    anticiposCliente: index++,
    total: index++
};

dt_table_reporte = dt_datatable($('#table-reporte'), {
    ajax: {
        url: __AJAX_PATH__ + 'cliente/index_table_reporte_resumen_cuenta_corriente/',
        data: function (d) {
            d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
        }
    },
    columnDefs: [
        {
            "targets": dt_table_reporte_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_table_reporte_column_index.multiselect
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_table_reporte_column_index.codigo,
                dt_table_reporte_column_index.razonSocial,
                dt_table_reporte_column_index.cuit,
                dt_table_reporte_column_index.tipoContratacion,
                dt_table_reporte_column_index.numeroContrato,
                dt_table_reporte_column_index.cuentaContable,
                dt_table_reporte_column_index.total
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_table_reporte_column_index.codigo,
                dt_table_reporte_column_index.cuit,
                dt_table_reporte_column_index.tipoContratacion,
                dt_table_reporte_column_index.numeroContrato
            ]
        },
        {
            className: "text-right",
            targets: [
                dt_table_reporte_column_index.saldoPendienteCobro,
                dt_table_reporte_column_index.saldoComprobantesSinContrato,
                dt_table_reporte_column_index.anticiposCliente
            ]
        }
    ]
});

$(document).ready(function () {

    initFiltroButton();

    initMostrarDetalleSaldoHandler();

});


/**
 * 
 * @returns {undefined}
 */
function initMostrarDetalleSaldoHandler() {

    // Handler al apretar el botón de "Ver detalle"
    $(document).on('click', '.link-detalle-saldo', function () {
//    $('.link-detalle-saldo').click(function () {

        var idCliente = $(this).data('id-cliente');

        var data = {
            id_cliente: idCliente,
            fechaFin: $('#adif_contablebundle_filtro_fechaFin').val()
        };

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'cliente/index_table_contrato/',
            data: data,
            success: function (comprobantesVenta) {

                var $contenidoDetalle = $('<div class="portlet-body">');

                $contenidoDetalle.append($('<div class="table-toolbar">'));

                $contenidoDetalle
                        .append(
                                $('<table id="table-comprobantes-venta" dataexport-title="comprobantes-venta" \n\
                                    class="table table-striped table-hover table-bordered table-condensed dt-multiselect export-excel">')
                                .append(
                                        $('<thead>')
                                        .append(
                                                $('<tr class="headers">')
                                                .append($('<th class="no-order entity_id">'))
                                                .append($('<th class="text-center table-checkbox no-order">')
                                                        .append($('<input type="checkbox" class="group-checkable" data-set="#table-comprobantes-venta .checkboxes" />'
                                                                ))
                                                        )
                                                .append($('<th class="nowrap text-center">').text('Tipo de contrato'))
                                                .append($('<th class="nowrap text-center">').text('Nº de contrato'))
                                                .append($('<th currency class="nowrap text-center">').text('Saldo pendiente de cobro'))
                                                )
                                        )
                                .append($('<tbody>'))
                                );

                jQuery.each(comprobantesVenta, function (index, comprobanteVenta) {

                    $contenidoDetalle.find('tbody')
                            .append($('<tr>')
                                    .append($('<td>').text(comprobanteVenta['id']))
                                    .append($('<td class="text-center">')
                                            .append($('<input type="checkbox" class="checkboxes" value="" />')))
                                    .append($('<td class="nowrap">').text(comprobanteVenta['claseContrato']))
                                    .append($('<td class="nowrap">').text(comprobanteVenta['numeroContrato']))
                                    .append($('<td class="nowrap money-format">').text(comprobanteVenta['saldoPendienteCobro']))
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

    $('.modal-dialog').css('width', '55%');

    $('.modal-footer').find('.btn-default').remove();
    $('.modal-footer').find('.btn-primary').text('Cerrar');

    setMasks();

    var options = {
        "searching": false,
        "ordering": true,
        "info": false,
        "paging": true
    };

    dt_init($('#table-comprobantes-venta'), options);
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});
    });
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    var fechaInicioEjercicioUsuarioDate = getDateFromString('01/01/' + ejercicioContableSesion);

    var fechaFin = getEndingDateOfCurrentMonth(ejercicioContableSesion);

    var fechaFinEjercicioUsuarioDate = getDateFromString('31/12/' + ejercicioContableSesion);

    $('#adif_contablebundle_filtro_fechaFin').datepicker("setDate", fechaFin);

    $('#adif_contablebundle_filtro_fechaFin').datepicker('setStartDate', fechaInicioEjercicioUsuarioDate);

    $('#adif_contablebundle_filtro_fechaFin').datepicker('setEndDate', fechaFinEjercicioUsuarioDate);

    $('#filtrar').on('click', function (e) {
        dt_table_reporte.DataTable().ajax.reload();

    });
}
