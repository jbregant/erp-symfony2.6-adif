
var dt_reportecobranzas;

var dt_reportecobranzas_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    comprobante: 3,
    contrato: 4,
    claseContrato: 5,
    cliente: 6,
    importeAABE: 7,
    importeADIF: 8
};


$(document).ready(function () {
    initDataTable();
    initFiltroButton();
});

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#filtrar').on('click', function (e) {
        var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
        var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();
        
        setFechasFiltro(fechaInicio, fechaFin);

        if (validarRangoFechas(fechaInicio, fechaFin)) {
            dt_reportecobranzas.DataTable().ajax.reload();
        }
    });
}

/**
 * 
 */
function initDataTable() {
    var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
    var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();

    if (validarRangoFechas(fechaInicio, fechaFin)) {

        dt_reportecobranzas = dt_datatable($('#table-reportecobranzas'), {
            ajax: {
                url: __AJAX_PATH__ + 'rengloncobranza/index_table_reporte_cobranzas_aabe_adif/',
                data: function (d) {
                    d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_reportecobranzas_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    className: "text-center",
                    targets: [
                        dt_reportecobranzas_column_index.multiselect
                    ]
                },
                {
                    className: "nowrap",
                    targets: [
                        dt_reportecobranzas_column_index.fecha,                        
                        dt_reportecobranzas_column_index.comprobante,
                        dt_reportecobranzas_column_index.contrato,
                        dt_reportecobranzas_column_index.claseContrato,
                        dt_reportecobranzas_column_index.cliente,
                        dt_reportecobranzas_column_index.importeAABE,
                        dt_reportecobranzas_column_index.importeADIF                    
                    ]
                }
            ]
        });
    }
}
