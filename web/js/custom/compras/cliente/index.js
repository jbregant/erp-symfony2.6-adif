index = 0;
var dt_cliente_column_index = {
    id: index++,
    multiselect: index++,
    cuit: index++,
    razonSocial: index++,
    actividades: index++,
    codigocliente: index++,
    representantelegal: index++,
    extrajero: index++,
    dc_direccion: index++,
    dl_direccion: index++,
    contactos: index++,
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
    estadoCliente: index++,
    acciones: index++
};

dt_cliente = dt_datatable($('#table-cliente'), {
    ajax: __AJAX_PATH__ + 'cliente/index_table/',
    columnDefs: [
        {
            "targets": dt_cliente_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_cliente_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_cliente_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>'
                        +
                        (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>' : '')
                        +
                        (full_data.cta_cte !== undefined ?
                                '<a href="' + full_data.cta_cte + '" class="btn btn-xs yellow tooltips" data-original-title="Ver cuenta corriente">\n\
                                    <i class="fa fa-letter">CC</i>\n\
                                </a>' : '')
                        ;
            }
        },
        {
            "targets": dt_cliente_column_index.estadoCliente,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_cliente_column_index.estadoCliente];

                $(td).addClass("state state-" + full_data.aliasTipoImportancia);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_cliente_column_index.estadoCliente];

                return  full_data.estadoProveedor;
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_cliente_column_index.cuit,
                dt_cliente_column_index.razonSocial
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_cliente_column_index.multiselect
            ]
        },
        {
            className: "text-center nowrap",
            targets: [
                dt_cliente_column_index.estadoCliente
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_cliente_column_index.acciones
        }
    ]
});