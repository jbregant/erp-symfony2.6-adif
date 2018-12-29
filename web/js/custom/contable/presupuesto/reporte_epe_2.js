
var urlEPEAccion = __AJAX_PATH__ + 'presupuesto/filtrar_epe_2/';

$(document).ready(function () {

    initDatepickerInputs();

    initFiltroButton();

    filtrarEPE();

    $('#adif_contablebundle_filtro_fechaInicio')
            .prop('readonly', true).unbind();

});

/**
 * 
 * @param {type} cuentasPresupuestariasEconomicas
 * @returns {undefined}
 */
function actualizarTabla(cuentasPresupuestariasEconomicas) {

    $('#epe_2_table tbody tr').remove();

    jQuery.each(cuentasPresupuestariasEconomicas, function (index, cuentaPresupuestariaEconomica) {
        addTD(cuentaPresupuestariaEconomica);
    });

    if ($('#epe_2_table tbody td').length === 0) {

        $('#epe_2_table').hide();

        $('.no-result').remove();
        $('.epe_content').append('<span class="no-result">No se encontraron resultados.</span>');
    } else {

        $('.no-result').remove();
        $('#epe_2_table').show();
    }

    initExportCustom($('#epe_2_table'));
}

/**
 * 
 * @param {type} cuentaPresupuestariaEconomica
 * @returns {undefined}
 */
function addTD(cuentaPresupuestariaEconomica) {

    var indiceNuevo = $('#epe_2_table tbody tr').length + 1;

    $('#epe_2_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td class="text-left">' + cuentaPresupuestariaEconomica['concepto'] + '</td>\n\
                <td class="money-format text-right">' + cuentaPresupuestariaEconomica['montoSectorPublico'] + '</td>\n\
                <td class="money-format text-right">' + cuentaPresupuestariaEconomica['montoSectorPrivado'] + '</td>\n\
                <td class="money-format text-right">' + cuentaPresupuestariaEconomica['montoEjecucionMes'] + '</td>\n\
                <td class="money-format text-right">' + cuentaPresupuestariaEconomica['montoTotalAcumulado'] + '</td>\n\
            </tr>');
}