var dt_localidad_column_index = {
    id: 0,
    multiselect: 1,
    localidad: 2,
    provincia: 3, 
    acciones: 4
};

dt_localidada = dt_datatable($('#table-localidad'), {
    ajax: __AJAX_PATH__ + 'localidades/index_table/',
    columnDefs: [
        {
            "targets": dt_localidad_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_localidad_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_localidad_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
    <i class="fa fa-search"></i>\n\
</a>' +
                        (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
    <i class="fa fa-pencil"></i>\n\
</a>' : '') + (full_data.delete !== undefined
                                    ? '<a href="' + full_data.delete + '" class="btn btn-xs red tooltips accion-borrar" data-original-title="Eliminar">\n\
                                            <i class="fa fa-trash"></i>\n\
                                        </a>'
                                    : '');
            }
        },
        {
            className: "text-center",
            targets: [
                dt_localidad_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_localidad_column_index.acciones
        }
    ]
});