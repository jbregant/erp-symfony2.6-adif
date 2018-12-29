
var index = 0;

var dt_facturacion_conceptoventageneral_column_index = {
    id: index++,
    multiselect: index++,
    denominacion: index++,
    descripcion: index++,
    cuentaContable: index++,
    cuentaIngreso: index++,
    acciones: index
};

dt_facturacion_conceptoventageneral = dt_datatable($('#table-conceptoventageneral'), {
    ajax: __AJAX_PATH__ + 'concepto_venta_general/index_table/',
    columnDefs: [
        {
            "targets": dt_facturacion_conceptoventageneral_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_facturacion_conceptoventageneral_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_facturacion_conceptoventageneral_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
    <i class="fa fa-search"></i>\n\
</a>' +
                        (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
    <i class="fa fa-pencil"></i>\n\
</a>' : '');
            }
        },
        {
            className: "text-center",
            targets: [
                dt_facturacion_conceptoventageneral_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_facturacion_conceptoventageneral_column_index.acciones
        }
    ]
});