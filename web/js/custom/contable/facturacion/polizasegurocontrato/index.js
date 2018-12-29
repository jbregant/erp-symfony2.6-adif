
var dt_facturacion_polizaseguro_column_index = {
    id: 0,
    multiselect: 1,
    numeroContrato: 2,
    cliente: 3,
    numeroPoliza: 4,
    aseguradora: 5,
    fechaVencimiento: 6,
    riesgoCubierto: 7,
    numeroTramiteEnvio: 8
};

dt_facturacion_polizaseguro = dt_datatable($('#table-facturacion_polizaseguro'), {
    ajax: __AJAX_PATH__ + 'polizacontrato/index_table/',
    columnDefs: [
        {
            "targets": dt_facturacion_polizaseguro_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_facturacion_polizaseguro_column_index.numeroContrato,
                dt_facturacion_polizaseguro_column_index.numeroPoliza,
                dt_facturacion_polizaseguro_column_index.aseguradora,
                dt_facturacion_polizaseguro_column_index.fechaVencimiento,
                dt_facturacion_polizaseguro_column_index.riesgoCubierto,
                dt_facturacion_polizaseguro_column_index.numeroTramiteEnvio
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_facturacion_polizaseguro_column_index.multiselect
            ]
        }
    ]
});