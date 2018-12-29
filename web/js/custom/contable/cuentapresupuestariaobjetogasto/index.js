
var dt_cuentapresupuestariaobjetogasto_column_index = {
    id: 0,
    multiselect: 1,
    codigo: 2,
    denominacion: 3,
    cuentaPresupuestariaEconomica: 4,
    descripcion: 5,
    acciones: 6
};

dt_cuentaPresupuestariaEconomica = dt_datatable($('#table-cuentapresupuestariaobjetogasto'), {
    ajax: __AJAX_PATH__ + 'cuentapresupuestariaobjetogasto/index_table/',
    columnDefs: [
        {
            "targets": dt_cuentapresupuestariaobjetogasto_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_cuentapresupuestariaobjetogasto_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_cuentapresupuestariaobjetogasto_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>\n\
                        <a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>';
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_cuentapresupuestariaobjetogasto_column_index.codigo,
                dt_cuentapresupuestariaobjetogasto_column_index.denominacion,
                dt_cuentapresupuestariaobjetogasto_column_index.cuentaPresupuestariaEconomica

            ]
        },
        {
            className: "text-center",
            targets: [
                dt_cuentapresupuestariaobjetogasto_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_cuentapresupuestariaobjetogasto_column_index.acciones
        }
    ]
});