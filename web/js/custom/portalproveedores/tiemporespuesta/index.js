
var index = 0;

var dt_tiemporespuesta_column_index = {
    id: index++,
    multiselect: index++,
    fecha: index++,
    accion: index++,
    tiempo_min: index++,
    tiempo_promedio: index++,
    tiempo_max: index++,
    acciones: index
};

$(document).ready(function () {

    $("#adif_portalproveedoresbundle_desde_hasta_form_fAcciones > option:first-child").remove();

    initFiltro();

    initDataTable();

    initFiltroButton();

});

function initDataTable(){
    
    var fechaInicio = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaDesde").val().trim();
    var fechaFin = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaHasta").val().trim();

    if (validarRangoFechas(fechaInicio, fechaFin)) {
        dt_tiemporespuesta = dt_datatable($('#table-tiemporespuesta'), {
            ajax: {
                url: __AJAX_PATH__ + 'tiemporespuesta/index_table/',
                data: function(d){
                    d.fechaInicio = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaDesde").val().trim();
                    d.fechaFin = $("#adif_portalproveedoresbundle_desde_hasta_form_fechaHasta").val().trim();
                    d.tAccion = $("#adif_portalproveedoresbundle_desde_hasta_form_fAcciones option:selected").val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_tiemporespuesta_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_tiemporespuesta_column_index.acciones,
                    "data": "actions",
                    "render": function (data, type, full, meta) {
                        var full_data = full[dt_tiemporespuesta_column_index.acciones];
                        return '';
                    }
                },
                {
                    className: "text-center",
                    targets: [
                        dt_tiemporespuesta_column_index.multiselect
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_tiemporespuesta_column_index.acciones
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
            dt_tiemporespuesta.DataTable().ajax.reload();
        }
    });

}

function setFechasFiltro(fechaInicio, fechaFin){
    localStorage.setItem('fechaInicio', fechaInicio);
    localStorage.setItem('fechaFin', fechaFin);
}