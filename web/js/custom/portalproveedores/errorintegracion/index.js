
var index = 0;

var dt_errorintegracion_column_index = {
    id: index++,
    multiselect: index++,
    email: index++,
    cuit: index++,
    fecha: index++,
    error: index++,
    acciones: index
};

$(document).ready(function () {

    initFiltro();

    initDataTable();

    initFiltroButton();

});

function initDataTable() {

    var fechaInicio = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaDesde").val().trim();
    var fechaFin = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaHasta").val().trim();

    if (validarRangoFechas(fechaInicio, fechaFin)) {
        dt_errorintegracion = dt_datatable($('#table-errorintegracion'), {
            ajax: {
                url: __AJAX_PATH__ + 'errorintegracion/index_table/',
                data:function(d){
                    d.fechaInicio = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaDesde").val().trim();
                    d.fechaFin = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaHasta").val().trim();
                }
            },
            columnDefs: [
                {
                    "targets": dt_errorintegracion_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_errorintegracion_column_index.acciones,
                    "data": "actions",
                    "render": function (data, type, full, meta) {
                        var full_data = full[dt_errorintegracion_column_index.acciones];
                        return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                                <i class="fa fa-search"></i>\n\
                            </a>';
                    }
                },
                {
                    className: "text-center",
                    targets: [
                        dt_errorintegracion_column_index.multiselect
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_errorintegracion_column_index.acciones
                }
            ]
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initFiltro() {

    var $fechaInicioLS = localStorage.getItem('fechaInicio');
    var $fechaFinLS = localStorage.getItem('fechaFin');

    var $fechaInicio = ($fechaInicioLS === null) ? getFirstDateOfCurrentMonth() : new Date($fechaInicioLS.substring(6, 10), $fechaInicioLS.substring(3, 5) - 1, $fechaInicioLS.substring(0, 2));
    var $fechaFin = ($fechaFinLS === null) ? getEndingDateOfCurrentMonth() : new Date($fechaFinLS.substring(6, 10), $fechaFinLS.substring(3, 5) - 1, $fechaFinLS.substring(0, 2));

    $('#adif_portalproveedoresbundle_desde_hasta_form_fechaDesde').datepicker("setDate", $fechaInicio);
    $('#adif_portalproveedoresbundle_desde_hasta_form_fechaHasta').datepicker("setDate", $fechaFin);
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#adif_portalproveedoresbundle_desde_hasta_form_submit').off('click').on('click', function (e) {
        e.preventDefault();
        var fechaInicio = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaDesde").val().trim();
        var fechaFin = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaHasta").val().trim();

        setFechasFiltro(fechaInicio, fechaFin);

        if (validarRangoFechas(fechaInicio, fechaFin)) {
            dt_errorintegracion.DataTable().ajax.reload();
        }
    });

}

function setFechasFiltro(fechaInicio, fechaFin){
    localStorage.setItem('fechaInicio', fechaInicio);
    localStorage.setItem('fechaFin', fechaFin);
}