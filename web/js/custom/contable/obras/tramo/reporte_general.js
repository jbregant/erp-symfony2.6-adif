
var index = 0;

var dt_reportegeneral_column_index = {
    id: index++,
    multiselect: index++,
    tipoContratacion: index++,
    numero: index++,
    anio: index++,
    descripcion: index++,
    proveedor: index++,
    totalContrato: index++,
    porcentajeTotalCertificado: index++,
    saldo: index++,
    saldoFinanciero: index++,
    montoActivado: index++,
    cuentaContable: index++,
    estadoTramo: index
};

$(document).ready(function () {

    initFiltro();

    initDataTable();

    initFiltroButton();

    updateCaption();
});

/**
 * 
 * @returns {undefined}
 */
function initFiltro() {

    var $fechaFin = getEndingDateOfCurrentMonth(ejercicioContableSesion);

    $('#adif_contablebundle_filtro_fechaFin').datepicker("setDate", $fechaFin);
}

/**
 * 
 * @returns {undefined}
 */
function initDataTable() {

    dt_reportegeneral = dt_datatable($('#table-reporte_general_tramo'), {
        ajax: {
            url: __AJAX_PATH__ + 'obras/tramos/reporte_general_index_table/',
            data: function (d) {
                d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
            }
        },
        columnDefs: [
            {
                "targets": dt_reportegeneral_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                }
            },
            {
                "targets": dt_reportegeneral_column_index.estadoTramo,
                "createdCell": function (td, cellData, rowData, row, col) {

                    var full_data = rowData[dt_reportegeneral_column_index.estadoTramo];

                    $(td).addClass("state state-" + full_data.aliasTipoImportancia);
                },
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_reportegeneral_column_index.estadoTramo];

                    return  full_data.estadoTramo;
                }
            },
            {
                className: "nowrap",
                targets: [
                    dt_reportegeneral_column_index.numero,
                    dt_reportegeneral_column_index.anio,
                    dt_reportegeneral_column_index.tipoContratacion,
                    dt_reportegeneral_column_index.proveedor,
                    dt_reportegeneral_column_index.porcentajeTotalCertificado,
                    dt_reportegeneral_column_index.cuentaContable,
                    dt_reportegeneral_column_index.estadoTramo
                ]
            },
            {
                className: "nowrap text-right",
                targets: [,
                    dt_reportegeneral_column_index.totalContrato,
                    dt_reportegeneral_column_index.saldo,
                    dt_reportegeneral_column_index.saldoFinanciero,
                    dt_reportegeneral_column_index.montoActivado
                ]
            },
            {
                className: "text-center",
                targets: [
                    dt_reportegeneral_column_index.multiselect
                ]
            }
        ]
    });
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#filtrar').on('click', function (e) {

        dt_reportegeneral.DataTable().ajax.reload();

        updateCaption();
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateCaption() {

    var caption = "Reporte general de renglones de licitación con movimiento al: "
            + $('#adif_contablebundle_filtro_fechaFin').val();

    $('.caption').text(caption);
}
