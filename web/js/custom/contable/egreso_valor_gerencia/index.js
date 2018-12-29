var dt_egresovalor_egresovalorgerencia_column_index = {
    id: 0,
    multiselect: 1,
    tipoEgresoValor: 2,
    gerencia: 3,
    monto: 4,
    cuentaContable: 5,
    acciones: 6
};

dt_egresovalor_egresovalorgerencia = dt_datatable($('#table-egresovalor_egresovalorgerencia'), {
    ajax: __AJAX_PATH__ + 'egresovalor_egresovalorgerencia/index_table/',
    columnDefs: [
        {
            "targets": dt_egresovalor_egresovalorgerencia_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_egresovalor_egresovalorgerencia_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_egresovalor_egresovalorgerencia_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined ?
                        '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>' : ''
                        );
            }
        },
        {
            className: "text-center",
            targets: [
                dt_egresovalor_egresovalorgerencia_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_egresovalor_egresovalorgerencia_column_index.acciones
        }
    ]
});