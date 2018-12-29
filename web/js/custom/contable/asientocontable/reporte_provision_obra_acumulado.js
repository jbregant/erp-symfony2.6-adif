var dt_asientocontable;

var index = 0;
var dt_asientocontable_column_index = {
    id: index++,    
    fecha: index++,
    cuenta_contable: index++,
    detalle: index++,
    debe: index++
};

/**
 * 
 */
jQuery(document).ready(function () {

    initFechasFiltro();

    initFiltroButton();

});

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#filtrar_reporte_provision_obra_acumulado').on('click', function (e) {
        
        if (typeof dt_asientocontable == 'undefined') {
            filtrarReporteAcumulado();
        } else {
            dt_asientocontable.DataTable().ajax.reload();
        }
        
        $('#reporte_provision_obra_acumulado_table').removeClass('hide');
    });
}

/**
 * 
 * @returns {undefined}
 */
function filtrarReporteAcumulado() {

    var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();

    var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();

    if (validarRangoFechas($fechaInicio, $fechaFin)) {

        if ($fechaInicio && $fechaFin) {
            
            dt_asientocontable = dt_datatable($('#reporte_provision_obra_acumulado_table'), {
                ajax: {
                    url: __AJAX_PATH__ + 'asientocontable/reporte/control_provision_obras/acumulado_index_table',
                    method: 'post',
                    data: function(d) {
                        d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                        d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                    }
                },
                ordering: false
            });
        }
    }
}