var dt_table_reporte_column_index = {
    id: 0,
    multiselect: 1,
    legajo: 2,
    razonSocial: 3,
    cuit: 4,
    tipoContratacion: 5,
    numero: 6,
    cuentaContable: 7,
    saldoPendienteCobro: 8
};


dt_table_reporte = dt_datatable($('#table-reporte'), {
    ajax: {
        url: __AJAX_PATH__ + 'proveedor/index_table_reporte_resumen_cuenta_corriente/',
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
                dt_table_reporte_column_index.legajo,
                dt_table_reporte_column_index.razonSocial,
                dt_table_reporte_column_index.cuit,
                dt_table_reporte_column_index.tipoContratacion,
                dt_table_reporte_column_index.numero,
                dt_table_reporte_column_index.cuentaContable
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_table_reporte_column_index.legajo,
                dt_table_reporte_column_index.cuit,
                dt_table_reporte_column_index.tipoContratacion,
                dt_table_reporte_column_index.numero
            ]
        },
        {
            className: "text-right",
            targets: [
                dt_table_reporte_column_index.saldoPendienteCobro
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

        var idProveedor = $(this).data('id-proveedor');

        var data = {
            id_proveedor: idProveedor,
            fechaFin: $('#adif_contablebundle_filtro_fechaFin').val()
        };

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'proveedor/detalle_resumen_cuenta_corriente/',
            data: data,
            success: function (items) {

                var $contenidoDetalle = $('<div class="portlet-body">');

                $contenidoDetalle.append($('<div class="table-toolbar">'));

                $contenidoDetalle
                        .append(
                                $('<table id="table-tramo-oc" dataexport-title="tramo-oc" \n\
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
                                                .append($('<th class="nowrap text-center">').text('Tramo/Orden compra'))
                                                .append($('<th class="nowrap text-center">').text('Nº de tramo/orden compra'))
                                                .append($('<th currency class="nowrap text-center">').text('Saldo pendiente de pago'))
                                                )
                                        )
                                .append($('<tbody>'))
                                );

                jQuery.each(items, function (index, item) {

                    $contenidoDetalle.find('tbody')
                            .append($('<tr>')
                                    .append($('<td>').text(item['id']))
                                    .append($('<td class="text-center">')
                                            .append($('<input type="checkbox" class="checkboxes" value="" />')))
                                    .append($('<td class="nowrap">').text(item['tipoContratacion']))
                                    .append($('<td class="nowrap">').text(item['numero']))
                                    .append($('<td class="nowrap money-format">').text(item['saldoPendientePago']))
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

    dt_init($('#table-tramo-oc'), options);
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