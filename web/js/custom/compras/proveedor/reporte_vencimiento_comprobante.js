
$(document).ready(function () {

    initReporteTable();

    initResumenAgingTable();

    $('.export-tools .dt_export_ctn ul li [data-que-exportar!="todos"]').remove();


});

/**
 * 
 * @returns {undefined}
 */
function initReporteTable() {

    var index = 0;

    var dt_table_reporte_column_index = {
        numeroDocumento: index++,
        razonSocial: index++,
        tipoProveedor: index++,
        tipoComprobante: index++,
        numeroComprobante: index++,
        importe: index++,
        fechaComprobante: index++,
        fechaIngresoADIF: index++,
        fechaVencimientoComprobante: index++,
//        plazoPrevistoPago: index++,
        estaVencida: index++,
        diasDeVencimiento: index
    };

    dt_table_reporte = dt_datatable($('#table-reporte'), {
        ajax: __AJAX_PATH__ + 'proveedor/index_table_reporte_vencimiento/',
        columnDefs: [
            {
                className: "nowrap",
                targets: [
                    dt_table_reporte_column_index.numeroDocumento,
                    dt_table_reporte_column_index.tipoProveedor,
                    dt_table_reporte_column_index.tipoComprobante,
                    dt_table_reporte_column_index.numeroComprobante,
                    dt_table_reporte_column_index.importe,
                    dt_table_reporte_column_index.fechaComprobante,
                    dt_table_reporte_column_index.fechaIngresoADIF,
                    dt_table_reporte_column_index.fechaVencimientoComprobante,
//                    dt_table_reporte_column_index.plazoPrevistoPago,
                    dt_table_reporte_column_index.estaVencida,
                    dt_table_reporte_column_index.diasDeVencimiento
                ]
            }
        ]
    });
}

function initResumenAgingTable() {

    var index = 0;

    var dt_table_reporte_column_index = {
        titulo: index++,
        rango: index++,
        dias: index++,
        sumatoria: index
    };

    dt_table_reporte_resumen_aging = dt_datatable($('#table-resumen-aging'), {
        ajax: __AJAX_PATH__ + 'proveedor/index_table_reporte_resumen_aging/',
        columnDefs: [
            {
                className: "nowrap text-right td-rango",
                targets: [
                    dt_table_reporte_column_index.rango
                ]
            },
            {
                className: "nowrap",
                targets: [
                    dt_table_reporte_column_index.titulo
                ]
            },
            {
                className: "td-sumatoria",
                targets: [
                    dt_table_reporte_column_index.sumatoria,
                    dt_table_reporte_column_index.dias

                ]
            }
        ],
        "fnDrawCallback": function () {

            initSumatoriaColumn();
        },
        paging: false,
        orderable: false,
        info: false
    });
}

/**
 * 
 * @param {type} table
 * @param {type} options
 * @returns {undefined}
 */
function dt_export(table, options) {

    var data1 = null;
    var data2 = null;

    table1 = $('#table-reporte');
    table2 = $('#table-resumen-aging');

    formato = (typeof options.formato === typeof undefined) ? 'excel' : options.formato;

    switch (options.registros) {
        case _exportar_todos:
            data1 = dt_getRows(table1, true);
            data2 = dt_getRows(table2, true);
            break;
    }


    data1 = dt_removeHiddenData(table1, data1);
    data1 = dt_formatCurrenciesFields(table1, data1);
    data1 = JSON.stringify(dt_excludeColumns(table1, data1));

    data2 = dt_removeHiddenData(table2, data2);
    data2 = dt_formatCurrenciesFields(table2, data2);
    data2 = JSON.stringify(dt_excludeColumns(table2, data2));

    content = {
        content: {
            title: 'Reporte vencimiento',
            sheets: {
                0: {
                    title: 'Reporte vencimiento',
                    tables: {
                        0: {
                            title: 'Reporte vencimiento',
                            titulo_alternativo: 'Reporte vencimiento',
                            data: data1,
                            headers: JSON.stringify(dt_getHeaders(table1))
                        },
                        1: {
                            title: 'Resumen reporte vencimiento',
                            titulo_alternativo: 'Resumen reporte vencimiento',
                            data: data2,
                            headers: JSON.stringify(dt_getHeaders(table2))
                        }
                    }
                }
            }
        }
    };

    open_window('POST', __AJAX_PATH__ + 'export_' + formato, content, '_blank');
}

/**
 * 
 * @returns {undefined}
 */
function initSumatoriaColumn() {

    $('.td-rango').each(function () {

        if ($(this).text().indexOf(" >") !== -1) {

            $(this).parent('tr')
                    .find('.td-sumatoria')
                    .addClass('font-red bold');
        }
    });
}