
var index = 0;

var dt_movimientopresupuestario_column_index = {
    id: index++,
    multiselect: index++,
    fechaCreacion: index++,
    tipoMovimientoPresupuestario: index++,
    cuentaPresupuestariaOrigen: index++,
    cuentaPresupuestariaDestino: index++,
    monto: index++,
    usuario: index++,
    detalle: index++,
    acciones: index
};

dt_licitacionobra = dt_datatable($('#table-movimientopresupuestario'), {
    ajax: __AJAX_PATH__ + 'movimientopresupuestario/index_table/',
    columnDefs: [
        {
            "targets": dt_movimientopresupuestario_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_movimientopresupuestario_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_movimientopresupuestario_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>';
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_movimientopresupuestario_column_index.fechaCreacion,
                dt_movimientopresupuestario_column_index.tipoMovimientoPresupuestario,
                dt_movimientopresupuestario_column_index.cuentaPresupuestariaOrigen,
                dt_movimientopresupuestario_column_index.cuentaPresupuestariaDestino,
                dt_movimientopresupuestario_column_index.monto,
                dt_movimientopresupuestario_column_index.usuario
            ]
        },
        {
            className: "text-right",
            targets: [
                dt_movimientopresupuestario_column_index.monto
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_movimientopresupuestario_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_movimientopresupuestario_column_index.acciones
        }
    ]
});