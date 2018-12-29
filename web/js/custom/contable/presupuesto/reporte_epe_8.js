
var urlEPEAccion = __AJAX_PATH__ + 'presupuesto/filtrar_epe_8/';

$(document).ready(function () {

    initReporteTitle();

    initDatepickerInputs();

    initFiltroButton();

    filtrarEPE();
});

/**
 * 
 * @param {type} conceptos
 * @returns {undefined}
 */
function actualizarTabla(conceptosPresupuestarios) {
    $('#epe_8_table_dotacion_personal tbody tr').remove();

    total = 0;

    jQuery.each(conceptosPresupuestarios['dotacion'], function (index, tipoArray) {
        $('#epe_8_table_dotacion_personal').find('tbody').append(
                '<tr>\n\
                    <td class="text-left bold">PLANTA ' + tipoArray['nombre'].toUpperCase() + '</td>\n\
                    <td class="text-right bold">' + tipoArray['total'] + '</td>\n\
                </tr>'
                );
        total += tipoArray['total'];
        jQuery.each(tipoArray['conceptos'], function (index, conceptoArray) {
            $('#epe_8_table_dotacion_personal').find('tbody').append(
                    '<tr>\n\
                    <td class="text-left" style="padding-left: 2em;">' + conceptoArray['nombre'].toUpperCase() + '</td>\n\
                    <td class="text-right">' + conceptoArray['total'] + '</td>\n\
                </tr>'
                    );
        });
    });
    $('#epe_8_table_dotacion_personal').find('tbody').append(
            '<tr>\n\
                    <td class=""></td>\n\
                    <td class=""></td>\n\
                </tr>'
            );
    $('#epe_8_table_dotacion_personal').find('tbody').append(
            '<tr>\n\
                    <td class="text-left bold">TOTAL PLANTA DE PERSONAL</td>\n\
                    <td class="text-right bold">' + total + '</td>\n\
                </tr>'
            );

    $('#epe_8_table_movimientos_dotacion tbody tr').remove();
    movimientos = conceptosPresupuestarios['movimientos'];
    jQuery.each(movimientos['bajas'], function (indexBaja, tipoBaja) {
        $('#epe_8_table_movimientos_dotacion').find('tbody').append(
                '<tr>\n\
                    <td class="text-left bold">' + indexBaja + '</td>\n\
                    <td class="text-right bold">' + tipoBaja + '</td>\n\
                </tr>'
                );
    });
    $('#epe_8_table_movimientos_dotacion').find('tbody').append(
            '<tr>\n\
                    <td class=""></td>\n\
                    <td class=""></td>\n\
                </tr>'
            );
    $('#epe_8_table_movimientos_dotacion').find('tbody').append(
            '<tr>\n\
                    <td class="text-left bold">TOTAL BAJAS</td>\n\
                    <td class="text-right bold">' + movimientos['totalBajas'] + '</td>\n\
                </tr>'
            );
    $('#epe_8_table_movimientos_dotacion').find('tbody').append(
            '<tr>\n\
                    <td class=""></td>\n\
                    <td class=""></td>\n\
                </tr>'
            );
    $('#epe_8_table_movimientos_dotacion').find('tbody').append(
            '<tr>\n\
                    <td class="text-left bold">TOTAL ALTAS</td>\n\
                    <td class="text-right bold">' + movimientos['altas'] + '</td>\n\
                </tr>'
            );

    initExportCustom($('#epe_8_table_dotacion_personal'));

}

function exportCustom(table, tipo) {

    var data = Array();
    var dataMovimientos = Array();

    dotacion = $('#epe_8_table_dotacion_personal');
    movimientos = $('#epe_8_table_movimientos_dotacion');

    dotacion.find('tbody').find('tr').each(function (e, v) {
        data[e] = Array();
        $(v).find('td:visible').not('.ctn_acciones').each(function (f, u) {
            data[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    data[e][f + index] = '';
                }
            }
        });
    });
    movimientos.find('tbody').find('tr').each(function (e, v) {
        dataMovimientos[e] = Array();
        $(v).find('td:visible').not('.ctn_acciones').each(function (f, u) {
            dataMovimientos[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    dataMovimientos[e][f + index] = '';
                }
            }
        });
    });

    content = {
        content: {
            title: $(dotacion).attr('dataexport-title'),
            sheets: {
                0: {
                    title: $(dotacion).attr('dataexport-title'),
                    tables: {
                        0: {
                            title: 'Dotacion de personal',
                            titulo_alternativo: (typeof $(table).attr('dataexport-title-alternativo') !== typeof undefined && $(table).attr('dataexport-title-alternativo') !== false ? $(table).attr('dataexport-title-alternativo') : ''),
                            data: JSON.stringify(data),
                            headers: JSON.stringify(getHeadersTableCustom(dotacion))
                        },
                        1: {
                            title: 'Movimientos de la dotacion',
                            titulo_alternativo: (typeof $(table).attr('dataexport-title-alternativo') !== typeof undefined && $(table).attr('dataexport-title-alternativo') !== false ? $(table).attr('dataexport-title-alternativo') : ''),
                            data: JSON.stringify(dataMovimientos),
                            headers: JSON.stringify(getHeadersTableCustom(movimientos))
                        }
                    }
                }
            }
        }
    };

    open_window('POST', __AJAX_PATH__ + 'export_' + tipo, content, '_blank');
}