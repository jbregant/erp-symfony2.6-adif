
var dt_cuentaPresupuestariaEconomica_column_index = {
    id: 0,
    multiselect: 1,
    codigo: 2,
    denominacion: 3,
    descripcion: 4,
    acciones: 5
};

dt_cuentaPresupuestariaEconomica = dt_datatable($('#table-cuentapresupuestariaeconomica'), {
    ajax: __AJAX_PATH__ + 'cuentapresupuestariaeconomica/index_table/',
    columnDefs: [
        {
            "targets": dt_cuentaPresupuestariaEconomica_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_cuentaPresupuestariaEconomica_column_index.codigo,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_cuentaPresupuestariaEconomica_column_index.codigo];

                $(td).css("padding-left", "calc(1em * " + full_data.nivel);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_cuentaPresupuestariaEconomica_column_index.codigo];

                return  full_data.codigo;
            }
        },
        {
            "targets": dt_cuentaPresupuestariaEconomica_column_index.denominacion,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_cuentaPresupuestariaEconomica_column_index.denominacion];

                if (full_data.esImputable === 0) {
                    $(td).addClass("bold");
                }

                $(td).css("padding-left", "calc(1em * " + full_data.nivel);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_cuentaPresupuestariaEconomica_column_index.denominacion];

                return  full_data.denominacion;
            }
        },
        {
            "targets": dt_cuentaPresupuestariaEconomica_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_cuentaPresupuestariaEconomica_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>\n\
                        <a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_cuentaPresupuestariaEconomica_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_cuentaPresupuestariaEconomica_column_index.acciones
        }
    ]
});