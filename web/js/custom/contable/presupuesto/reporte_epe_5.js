
var urlEPEAccion = __AJAX_PATH__ + 'presupuesto/filtrar_epe_5/';

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

    $('#epe_5_table tbody tr').remove();

    ejecucion = 0;
    acumulado = 0;

    jQuery.each(conceptosServiciosNoPersonales, function (index, concepto) {
        $('#epe_5_table').find('tbody').append(
                '<tr>\n\
                    <td class="text-left">3.' + (index + 1) + '. ' + concepto['denominacion'] + '</td>\n\
                    <td class="text-right money-format">' + concepto['ejecucion'] + '</td>\n\\n\
                    <td class="text-right money-format">' + concepto['acumulado'] + '</td>\n\
                </tr>'
                );
        acumulado += concepto['acumulado'];
        ejecucion += concepto['ejecucion'];
    });

    $('#total-ejecucion').html(ejecucion);
    $('#total-acumulado').html(acumulado);
    $('#anio_ejecucion').html($('#adif_contablebundle_filtro_ejercicio').val());

    initExportCustom($('#epe_5_table'));

}

function exportCustom(table, tipo) {

    var dataNoPersonal = Array();
    $('#epe_5_table').find('tbody').find('tr').each(function (e, v) {
        dataNoPersonal[e] = Array();
        $(v).find('td:visible').not('.ctn_acciones').each(function (f, u) {
            dataNoPersonal[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    dataNoPersonal[e][f + index] = '';
                }
            }
        });
    });

    var dataServicios = Array();
    $('#epe_5_table_servicios').find('tbody').find('tr').each(function (e, v) {
        dataServicios[e] = Array();
        $(v).find('td:visible').not('.ctn_acciones').each(function (f, u) {
            dataServicios[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    dataServicios[e][f + index] = '';
                }
            }
        });
    });

    content = {
        content: {
            title: 'EPE_5',
            sheets: {
                0: {
                    title: 'EPE_5',
                    tables: {
                        0: {
                            title: $('#epe_5_table').attr('dataexport-title'),
                            titulo_alternativo: (typeof $('#epe_5_table').attr('dataexport-title-alternativo') !== typeof undefined && $('#epe_5_table').attr('dataexport-title-alternativo') !== false ? $('#epe_5_table').attr('dataexport-title-alternativo') : ''),
                            data: JSON.stringify(dataNoPersonal),
                            headers: JSON.stringify(getHeadersTableCustom($('#epe_5_table')))
                        },
                        1: {
                            title: $('#epe_5_table_servicios').attr('dataexport-title'),
                            titulo_alternativo: (typeof $('#epe_5_table').attr('dataexport-title-alternativo') !== typeof undefined && $('#epe_5_table').attr('dataexport-title-alternativo') !== false ? $('#epe_5_table').attr('dataexport-title-alternativo') : ''),
                            data: JSON.stringify(dataServicios),
                            headers: JSON.stringify(getHeadersTableCustom($('#epe_5_table_servicios')))
                        }
                    }
                }
            }
        }
    };

    open_window('POST', __AJAX_PATH__ + 'export_' + tipo, content, '_blank');
}