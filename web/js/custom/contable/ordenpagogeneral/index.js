
var index = 0;

var dt_ordenpagogeneral_column_index = {
    id: index++,
    multiselect: index++,
    fecha: index++,
    proveedor: index++,
    concepto: index++,
    detalle: index++,
    importe: index++,
    acciones: index
};


jQuery(document).ready(function () {

    initDataTable();

    initEditarFechaAsientoContableHandler();

});

/**
 * 
 * @returns {undefined}
 */
function initDataTable() {

    dt_ordenpagogeneral = dt_datatable($('#table-ordenpagogeneral'), {
        ajax: __AJAX_PATH__ + 'orden_pago_general/index_table/',
        columnDefs: [
            {
                "targets": dt_ordenpagogeneral_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                }
            },
            {
                "targets": dt_ordenpagogeneral_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {
                    var full_data = full[dt_ordenpagogeneral_column_index.acciones];
                    return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>';
                }
            },
            {
                className: "text-center",
                targets: [
                    dt_ordenpagogeneral_column_index.multiselect
                ]
            },
            {
                className: "nowrap",
                targets: [
                    dt_ordenpagogeneral_column_index.fecha,
                    dt_ordenpagogeneral_column_index.proveedor,
                    dt_ordenpagogeneral_column_index.detalle,
                    dt_ordenpagogeneral_column_index.importe
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_ordenpagogeneral_column_index.acciones
            }
        ]
    });
}

/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    updateFechaOrdenPagoFromAsientoContable();

}