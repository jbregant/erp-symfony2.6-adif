
var index = 0;

var dt_pagoparcial_column_index = {
    id: index++,
    multiselect: index++,
    proveedor: index++,
    comprobante: index++,
    importeComprobante: index++,
    fechaPago: index++,
    importe: index++,
    detalle: index++,
    estado: index++,
    acciones: index
};

dt_pagoparcial = dt_datatable($('#table-pagoparcial'), {
    ajax: __AJAX_PATH__ + 'pago_parcial/index_table/',
    columnDefs: [
        {
            "targets": dt_pagoparcial_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_pagoparcial_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_pagoparcial_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>';
            }
        },
        {
            "targets": dt_pagoparcial_column_index.estado,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_pagoparcial_column_index.estado];

                $(td).addClass('state state-' + full_data.estadoClass);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_pagoparcial_column_index.estado];

                return  full_data.estado;
            }
        },
        {
            className: "text-center",
            targets: [
                dt_pagoparcial_column_index.multiselect
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_pagoparcial_column_index.comprobante,
                dt_pagoparcial_column_index.proveedor,
                dt_pagoparcial_column_index.fechaPago,
                dt_pagoparcial_column_index.estado
            ]
        },
        {
            className: "nowrap text-right",
            targets: [
                dt_pagoparcial_column_index.importeComprobante,
                dt_pagoparcial_column_index.importe
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_pagoparcial_column_index.acciones
        }
    ]
});