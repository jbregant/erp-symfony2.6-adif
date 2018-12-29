
index = 0;

var dt_documentosFinancieros_column_index = {
    id: index++,
    multiselect: index++,
    fechaCreacion: index++,
    tipoContratacionAlias: index++,
    numero: index++,
    anio: index++,
    descripcion: index++,
    cuit: index++,
    razonSocial: index++,
    tipoDocumentoFinanciero: index++,
    numeroDocumentoFinanciero: index++,
    fechaAnulacion: index++,
    montoSinIVA: index++,
    correspondePago: index++,
    comprobante: index++,
    totalComprobante: index++,
    fechaIngresoADIF: index++,
    fechaIngresoGerenciaAdministracion: index++,
    numeroReferencia: index++,
    observaciones: index++,
    ordenPago: index++,
    pago: index++,
    fechaPago: index++,
    estadoPago: index++,
    montoNeto: index++
};


/**
 * 
 */
jQuery(document).ready(function () {

    initDataTable();

});

/**
 * 
 * @returns {undefined}
 */
function initDataTable() {

    dt_documentos_financieros = dt_datatable($('#table-reporte_general_documento_financiero'), {
        ajax: __AJAX_PATH__ + 'documento_financiero/reporte_general_table/',
        columnDefs: [
            {
                "targets": dt_documentosFinancieros_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                }
            },
            {
                "targets": dt_documentosFinancieros_column_index.descripcion,
                "render": function (data, type, full, meta) {
                    return '<span class="truncate tooltips" data-original-title="' + data + '">'
                            + data +
                            '</span>';
                }
            },
            {
                className: "nowrap",
                targets: [
                    dt_documentosFinancieros_column_index.cuit,
                    dt_documentosFinancieros_column_index.montoSinIVA,
                    dt_documentosFinancieros_column_index.correspondePago,
                    dt_documentosFinancieros_column_index.totalComprobante,
                    dt_documentosFinancieros_column_index.ordenPago,
                    dt_documentosFinancieros_column_index.montoNeto
                ]
            },
            {
                className: "text-center",
                targets: [
                    dt_documentosFinancieros_column_index.multiselect
                ]
            }
        ],
        "fnDrawCallback": function () {
            initEllipsis();
        }
    });
}


function getTDValue(el_td) {
    return $(el_td).html().replace('$ ', '');
}