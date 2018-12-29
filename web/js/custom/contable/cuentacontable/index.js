
var dt_cuentaContable_column_index = {
    id: 0,
    multiselect: 1,
    codigoCuentaContable: 2,
    denominacionCuentaContable: 3,
    cuentaPresupuestariaEconomica: 4,
    cuentaPresupuestariaObjetoGasto: 5,
    esImputable: 6,
    activa: 7,
    codigoCuentaContableOrden: 8,
    acciones: 9
};

dt_cuentaContable = dt_datatable($('#table-cuentacontable'), {
    ajax: __AJAX_PATH__ + 'cuentacontable/index_table/',
    columnDefs: [
        {
            "targets": dt_cuentaContable_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_cuentaContable_column_index.codigoCuentaContable,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_cuentaContable_column_index.codigoCuentaContable];

//                $(td).css("padding-left", "calc(1em * " + full_data.nivel);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_cuentaContable_column_index.codigoCuentaContable];

                return  full_data.codigoCuentaContable;
            }
        },
        {
            "targets": dt_cuentaContable_column_index.denominacionCuentaContable,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_cuentaContable_column_index.denominacionCuentaContable];

                if (full_data.esImputable === 0) {
                    $(td).addClass("bold");
                }

//                $(td).css("padding-left", "calc(1em * " + full_data.nivel);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_cuentaContable_column_index.denominacionCuentaContable];

                return  full_data.denominacionCuentaContable;
            }
        },
        {
            "targets": dt_cuentaContable_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {

                var full_data = full[dt_cuentaContable_column_index.acciones];

                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined ? '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>' : '');
            }
        },
        {
            className: "text-center",
            targets: [
                dt_cuentaContable_column_index.multiselect
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_cuentaContable_column_index.denominacionCuentaContable
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_cuentaContable_column_index.acciones
        },
        {
            className: "hidden",
            targets: [
                dt_cuentaContable_column_index.codigoCuentaContableOrden
            ]
        }
    ]
});

dt_cuentaContable.DataTable().order([dt_cuentaContable_column_index.codigoCuentaContableOrden, 'asc']).draw();