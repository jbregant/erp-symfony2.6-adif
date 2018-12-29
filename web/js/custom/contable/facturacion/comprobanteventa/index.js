
var dt_facturacion_comprobanteventa;

var imprimirComprobanteForm = $('#imprimir_comprobante_form');

var dt_facturacion_comprobanteventa_column_index = {
    id: 0,
    multiselect: 1,
    fechaComprobante: 2,
    tipoComprobante: 3,
    letra: 4,
    puntoVenta: 5,
    numero: 6,
    numeroContrato: 7,
    numeroLicitacion: 8,
    cliente: 9,
    observaciones: 10,
    neto: 11,
    iva: 12,
    percepcion: 13,
    percepcionIVA: 14,
    total: 15,
    estado: 16,
    acciones: 17
};

$(document).ready(function () {

    initDataTable();

    initFiltroButton();

    initImprimirButton();

    removeFlashMessage();

});

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#filtrar').on('click', function (e) {
        var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
        var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();
        
        setFechasFiltro(fechaInicio, fechaFin);

        if (validarRangoFechas(fechaInicio, fechaFin)) {
            dt_facturacion_comprobanteventa.DataTable().ajax.reload();
        }
    });
}

/**
 * 
 */
function initDataTable() {
    var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
    var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();

    if (validarRangoFechas(fechaInicio, fechaFin)) {
        dt_facturacion_comprobanteventa = dt_datatable($('#table-facturacion_comprobanteventa'), {
            ajax: {
                url: __AJAX_PATH__ + 'comprobanteventa/index_table/',
                data: function (d) {
                    d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_facturacion_comprobanteventa_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_facturacion_comprobanteventa_column_index.acciones,
                    "data": "actions",
                    "render": function (data, type, full, meta) {
                        var full_data = full[dt_facturacion_comprobanteventa_column_index.acciones];
                        return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                                        <i class="fa fa-search"></i>\n\
                                </a>' +
                                (full_data.imprimir !== undefined ?
                                        '<a href="' + full_data.imprimir + '" class="btn btn-xs dark accion-imprimir tooltips" data-original-title="Imprimir">\n\
                                            <i class="fa fa-print"></i>\n\
                                        </a>'
                                        : '') +
                                (full_data.anular !== undefined ?
                                        '<a href="' + full_data.anular + '" class="btn btn-xs red accion-anular tooltips" data-original-title="Anular">\n\
                                            <i class="fa fa-times"></i>\n\
                                        </a>'
                                        : '');

                    }
                },
                {
                    "targets": dt_facturacion_comprobanteventa_column_index.observaciones,
                    "render": function (data, type, full, meta) {
                        return '<span class="truncate tooltips" data-original-title="' + data + '">'
                                + data + '</span>';
                    }
                },
                {
                    className: "text-center",
                    targets: [
                        dt_facturacion_comprobanteventa_column_index.multiselect
                    ]
                },
                {
                    className: "nowrap",
                    targets: [
                        dt_facturacion_comprobanteventa_column_index.fechaComprobante,
                        dt_facturacion_comprobanteventa_column_index.tipoComprobante,
                        dt_facturacion_comprobanteventa_column_index.letra,
                        dt_facturacion_comprobanteventa_column_index.puntoVenta,
                        dt_facturacion_comprobanteventa_column_index.numero,
                        dt_facturacion_comprobanteventa_column_index.numeroContrato,
                        dt_facturacion_comprobanteventa_column_index.numeroLicitacion,
                        dt_facturacion_comprobanteventa_column_index.cliente,
                        dt_facturacion_comprobanteventa_column_index.neto,
                        dt_facturacion_comprobanteventa_column_index.iva,
                        dt_facturacion_comprobanteventa_column_index.percepcion,
                        dt_facturacion_comprobanteventa_column_index.percepcionIVA,
                        dt_facturacion_comprobanteventa_column_index.total
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_facturacion_comprobanteventa_column_index.acciones
                }
            ],
            "fnDrawCallback": function () {
                initEllipsis();
            }
        });
    }

    initEditarFechaAsientoContableHandler();
}

/**
 * 
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {
    return true;
}


/**
 * 
 * @returns {undefined}
 */
function initImprimirButton() {
    $('#btn-imprimir-comprobantes a[comprobante-table]').off().on('click', function (e) {
        e.preventDefault();
        bloquear();
        
        var table = $('#table-facturacion_comprobanteventa');
        var ids = [];

        switch ($(this).data('imprimir-comprobante')) {
            case _exportar_todos:
                ids = dt_getRowsIds(table);
                break;
            case _exportar_seleccionados:
                ids = dt_getSelectedRowsIds(table);
                break;
        }

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un comprobante para imprimir.'});
            desbloquear();
            return;
        } else {

            var json = {
                'ids': JSON.stringify(ids.toArray())
            };

            imprimirComprobanteForm.addHiddenInputData(json);
            imprimirComprobanteForm.submit();
        }

        $('.bootbox').removeAttr('tabindex');

        e.stopPropagation();

        desbloquear();
    });
}

/**
 * 
 * @returns {undefined}
 */
function removeFlashMessage() {
    $('.link-editar-fecha-asiento').parent('span').remove();
}