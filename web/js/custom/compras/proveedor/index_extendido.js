index = 0;
var dt_proveedor_column_index = {
    id: index++,
    multiselect: index++,
    rubros: index++,
    cuit: index++,
    razonSocial: index++,
    codigoproveedor: index++,
    representantelegal: index++,
    extrajero: index++,
    dc_direccion: index++,
    numeroIIBB: index++,
    condicionIVA: index++,
    exentoIVA: index++,
    condicionGANANCIAS: index++,
    exentoGANANCIAS: index++,
    condicionSUSS: index++,
    exentoSUSS: index++,
    condicionIIBB: index++,
    exentoIIBB: index++,
    calificacionfiscal: index++,
    problemasafip: index++,
    riesgofiscal: index++,
    magnitudessuperadas: index++,
    pasibleRetencionIva: index++,
    pasibleRetencionGanancias: index++,
    pasibleRetencionSUSS: index++,
    pasibleRetencionIngresosBrutos: index++,
    estadoProveedor: index++,
};

dt_proveedor = dt_datatable($('#table-proveedor'), {
    ajax: __AJAX_PATH__ + 'proveedor/index_table_extendido/',
    columnDefs: [
        {
            "targets": dt_proveedor_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_proveedor_column_index.acciones,
            "data": "actions",
        },
        {
            "targets": dt_proveedor_column_index.calificacionFinal,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_proveedor_column_index.calificacionFinal];

                $(td).addClass(full_data.claseCalificacionFinal);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_proveedor_column_index.calificacionFinal];

                return  full_data.calificacionFinal;
            }
        },
        {
            "targets": dt_proveedor_column_index.estadoProveedor,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_proveedor_column_index.estadoProveedor];

                $(td).addClass("state state-" + full_data.aliasTipoImportancia);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_proveedor_column_index.estadoProveedor];

                return  full_data.estadoProveedor;
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_proveedor_column_index.cuit,
                dt_proveedor_column_index.razonSocial,
                dt_proveedor_column_index.numeroIIBB,
                dt_proveedor_column_index.rubros
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_proveedor_column_index.multiselect,
                dt_proveedor_column_index.calificacionFinal
            ]
        },
        {
            className: "text-center nowrap",
            targets: [
                dt_proveedor_column_index.estadoProveedor
            ]
        },
    ]
});