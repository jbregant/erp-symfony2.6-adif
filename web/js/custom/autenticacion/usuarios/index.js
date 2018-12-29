
var index = 0;

var dt_usuario_column_index = {
    id: index++,
    multiselect: index++,
    username: index++,
    email: index++,
    grupos: index++,
    area: index++,
    enabled: index++,
    lastLogin: index++,
	empresas: index++,
    acciones: index
};

dt_licitacionobra = dt_datatable($('#table-usuarios'), {
    ajax: __AJAX_PATH__ + 'usuarios/index_table/',
    columnDefs: [
        {
            "targets": dt_usuario_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_usuario_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_usuario_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>' +
                        (full_data.limpiar !== undefined
                                ? '<a href="' + full_data.limpiar + '" class="btn btn-xs yellow tooltips" data-original-title="Limpiar contrase&ntilde;a">\n\
                                    <i class="fa fa-key"></i>\n\
                                </a>'
                                : '')
                        ;
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_usuario_column_index.username,
                dt_usuario_column_index.email,
                dt_usuario_column_index.area,
                dt_usuario_column_index.enabled,
                dt_usuario_column_index.lastLogin,
				dt_usuario_column_index.empresas
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_usuario_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_usuario_column_index.acciones
        }
    ]
});