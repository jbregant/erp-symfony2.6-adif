
var urlEPEAccion = __AJAX_PATH__ + 'presupuesto/filtrar_epe_3/';

$(document).ready(function () {

    initReporteTitle();

    initDatepickerInputs();

    initFiltroButton();

    filtrarEPE();
});

/**
 * 
 * @param {type} conceptosPresupuestarios
 * @returns {undefined}
 */
function actualizarTabla(conceptosPresupuestarios) {

    $('#epe_3_table tbody tr').remove();

    var indice = 'I';

    financiamiento = 0;
    ejecucion = 0;

    jQuery.each(conceptosPresupuestarios, function (index, tipoArray) {
        $('#epe_3_table').find('tbody').append(
                '<tr>\n\
                    <td class="text-left bold">' + indice + '. ' + tipoArray['denominacion'].toUpperCase() + '</td>\n\
                    <td class="text-right bold money-format">' + tipoArray['financiamiento'] + '</td>\n\
                    <td class="text-right bold money-format">' + tipoArray['ejecucion'] + '</td>\n\\n\
                </tr>'
                );
        jQuery.each(tipoArray['conceptos'], function (index, conceptoArray) {
            $('#epe_3_table').find('tbody').append(
                    '<tr>\n\
                    <td class="text-left" style="padding-left: 2em;">' + (index + 1) + ') ' + conceptoArray['denominacion'].toUpperCase() + '</td>\n\
                    <td class="text-right money-format">' + conceptoArray['financiamiento'] + '</td>\n\
                    <td class="text-right money-format">' + conceptoArray['ejecucion'] + '</td>\n\\n\
                </tr>'
                    );
        });
        $('#epe_3_table').find('tbody').append(
                '<tr>\n\
                    <td class="text-left bold" style="padding-left: 0em;"></td>\n\
                    <td class="text-right bold money-format"></td>\n\
                    <td class="text-right bold money-format"></td>\n\\n\
                </tr>'
                );
        indice += 'I';
        financiamiento += tipoArray['financiamiento'];
        ejecucion += tipoArray['ejecucion'];
    });

    $('#total-cta-ahorro').html(financiamiento);
    $('#total-ejecucion-caja').html(ejecucion);
    
    initExportCustom($('#epe_3_table'));

}