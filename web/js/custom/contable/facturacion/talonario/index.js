
var index = 0;

var dt_talonario_column_index = {
    id: index++,
    multiselect: index++,
    tipoComprobante: index++,
    letraComprobante: index++,
    puntoVenta: index++,
    numeroDesde: index++,
    numeroHasta: index++,
    numeroCAI: index++,
    fechaVencimientoCAI: index++,
    acciones: index
};

dt_licitacionobra = dt_datatable($('#table-talonario'), {
    ajax: __AJAX_PATH__ + 'talonarios/index_table/',
    columnDefs: [
        {
            "targets": dt_talonario_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_talonario_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_talonario_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined
                                ? '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-placement="left" data-original-title="Editar">\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>'
                                : '')
                        ;
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_talonario_column_index.tipoComprobante,
                dt_talonario_column_index.letraComprobante,
                dt_talonario_column_index.puntoVenta,
                dt_talonario_column_index.numeroDesde,
                dt_talonario_column_index.numeroHasta,
                dt_talonario_column_index.numeroCAI

            ]
        },
        {
            className: "text-center",
            targets: [
                dt_talonario_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_talonario_column_index.acciones
        }
    ]
});