
var dt_reportecobranzas;

var dt_reportecobranzas_column_index = {
    id: 0,
    multiselect: 1,
    fechaRecibo: 2,
    numeroRecibo: 3,
    tipoCobranza: 4,
    referencia: 5,
    fechaCobranza: 6,
    comprobante: 7,
    contrato: 8,
    cliente: 9,
    concepto: 10,
    cuentaContable: 11,
    importe: 12,
    observaciones: 13
};


$(document).ready(function () {

    initFiltro();

    initDataTable();

    initFiltroButton();

});

/**
 * 
 * @returns {undefined}
 */
function initFiltro() {

    var $fechaInicio = getFirstDateOfCurrentMonth(ejercicioContableSesion);
    
//    var fechaInicioEjercicioUsuarioDate = getDateFromString('01/01/' + ejercicioContableSesion);

    var $fechaFin = getEndingDateOfCurrentMonth(ejercicioContableSesion);
    
//    var fechaFinEjercicioUsuarioDate = getDateFromString('31/12/' + ejercicioContableSesion);

    $('#adif_contablebundle_filtro_fechaInicio').datepicker("setDate", $fechaInicio);

//    $('#adif_contablebundle_filtro_fechaInicio').datepicker('setStartDate', fechaInicioEjercicioUsuarioDate);

//    $('#adif_contablebundle_filtro_fechaInicio').datepicker('setEndDate', fechaFinEjercicioUsuarioDate);

    $('#adif_contablebundle_filtro_fechaFin').datepicker("setDate", $fechaFin);

//    $('#adif_contablebundle_filtro_fechaFin').datepicker('setStartDate', fechaInicioEjercicioUsuarioDate);

//    $('#adif_contablebundle_filtro_fechaFin').datepicker('setEndDate', fechaFinEjercicioUsuarioDate);
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#filtrar').on('click', function (e) {
        var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
        var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();

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
                url: __AJAX_PATH__ + 'rengloncobranza/index_table_reportecobranzas/',
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
                        dt_reportecobranzas_column_index.fechaRecibo,
                        dt_reportecobranzas_column_index.numeroRecibo,
                        dt_reportecobranzas_column_index.tipoCobranza,
                        dt_reportecobranzas_column_index.referencia,
                        dt_reportecobranzas_column_index.fechaCobranza,
                        dt_reportecobranzas_column_index.comprobante,
                        dt_reportecobranzas_column_index.contrato,
                        dt_reportecobranzas_column_index.cliente,
                        dt_reportecobranzas_column_index.concepto,
                        dt_reportecobranzas_column_index.cuentaContable,
                        dt_reportecobranzas_column_index.importe,
                        dt_reportecobranzas_column_index.observaciones                    
                    ]
                }
            ]
        });
    }

}
