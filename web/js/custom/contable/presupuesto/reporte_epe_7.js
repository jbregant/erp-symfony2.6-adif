
var urlEPEAccion = __AJAX_PATH__ + 'presupuesto/filtrar_epe_7/';

$(document).ready(function () {

    initReporteTitle();

    initDatepickerInputs();

    initFiltroButton();

    filtrarEPE();
});

/**
 * 
 * @param {type} conceptosServiciosNoPersonales
 * @returns {undefined}
 */
function actualizarTabla(conceptosServiciosNoPersonales) {

    $('#epe_7_table tbody tr').remove();

    ejecucion = 0;
    acumulado = 0;

    jQuery.each(conceptosServiciosNoPersonales, function (index, concepto) {
        $('#epe_7_table').find('tbody').append(
                '<tr>\n\
                    <td class="text-left">' + concepto['denominacion'].toUpperCase() + '</td>\n\
                    <td class="text-left">' + concepto['unidad'].toUpperCase() + '</td>\n\\n\
                    <td class="text-right money-format">' + concepto['acumulado'] + '</td>\n\
                </tr>'
                );
        acumulado += concepto['acumulado'];
    });

    $('#total-acumulado').html(acumulado);
    $('#columna_mes').html($('#adif_contablebundle_filtro_fechaFin').val().toUpperCase());
    
    initExportCustom($('#epe_7_table'));

}