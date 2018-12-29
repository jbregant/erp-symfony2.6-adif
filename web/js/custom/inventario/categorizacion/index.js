
var index = 0;

var dt_categorizacion_column_index = {
    id: index++,
    multiselect: index++,
    denominacion: index++,
    acciones: index
};

dt_categorizacion = dt_datatable($('#table-categorizacion'), {
    ajax: __AJAX_PATH__ + 'categorizacion/index_table/',
    columnDefs: [
        {
            "targets": dt_categorizacion_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        }, {
            "targets": dt_categorizacion_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_categorizacion_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined ?
                            '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                <i class="fa fa-pencil"></i>\n\
                            </a>' : '') +
                        (full_data.delete !== undefined ?
                            '<a href="' + full_data.delete + '" class="btn btn-xs red tooltips accion-borrar" data-original-title="Eliminar">\n\
                                <i class="fa fa-trash"></i>\n\
                            </a>' : '');
            }
        }, {
            className: "text-center",
            targets: [
                dt_categorizacion_column_index.multiselect
            ]
        }, {
            className: "ctn_acciones text-center nowrap",
            targets: dt_categorizacion_column_index.acciones
        }
    ],
    "initComplete": function (settings, json) {

        initBorrarButton();
    }
});