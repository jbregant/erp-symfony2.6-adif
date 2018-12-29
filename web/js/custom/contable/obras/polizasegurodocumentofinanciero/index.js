
var index = 0;

var dt_polizaseguro_documentofinanciero_column_index = {
    id: index++,
    multiselect: index++,
    tipoLicitacion: index++,
    numeroLicitacion: index++,
    anioLicitacion: index++,
    proveedor: index++,
    tramo: index++,
    fechaInicio: index++,
    fechaVencimiento: index++,
    numeroPoliza: index++,
    aseguradora: index++,
    riesgoCubierto: index++,
    numeroTramiteEnvio: index++
};

dt_facturacion_polizaseguro = dt_datatable($('#table-polizaseguro'), {
    ajax: __AJAX_PATH__ + 'polizadocumentofinanciero/index_table/',
    columnDefs: [
        {
            "targets": dt_polizaseguro_documentofinanciero_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_polizaseguro_documentofinanciero_column_index.tipoLicitacion,
                dt_polizaseguro_documentofinanciero_column_index.numeroLicitacion,
                dt_polizaseguro_documentofinanciero_column_index.anioLicitacion,
                dt_polizaseguro_documentofinanciero_column_index.proveedor,
                dt_polizaseguro_documentofinanciero_column_index.fechaInicio,
                dt_polizaseguro_documentofinanciero_column_index.fechaVencimiento,
                dt_polizaseguro_documentofinanciero_column_index.numeroPoliza,
                dt_polizaseguro_documentofinanciero_column_index.aseguradora,
                dt_polizaseguro_documentofinanciero_column_index.riesgoCubierto,
                dt_polizaseguro_documentofinanciero_column_index.numeroTramiteEnvio
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_polizaseguro_documentofinanciero_column_index.multiselect
            ]
        }
    ]
});

$(document).ready(function () {

    $('#table-polizaseguro > thead > tr.replace-inputs.filter > th:nth-child(11) > input').attr('placeholder', 'Aseguradora');
    $('#table-polizaseguro > thead > tr.replace-inputs.filter > th:nth-child(12) > input').attr('placeholder', 'Aseguradora');

});