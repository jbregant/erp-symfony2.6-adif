
var index = 0;

var dt_grupo_column_index = {
    id: index++,
    multiselect: index++,
    name: index++,
    roles: index++,
    descripcion: index++,
    acciones: index
};

dt_licitacionobra = dt_datatable($('#table-roles'), {
    ajax: __AJAX_PATH__ + 'grupos/index_table/',
    columnDefs: [
        {
            "targets": dt_grupo_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_grupo_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_grupo_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>';
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_grupo_column_index.name,
                dt_grupo_column_index.roles
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_grupo_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_grupo_column_index.acciones
        }
    ]
});