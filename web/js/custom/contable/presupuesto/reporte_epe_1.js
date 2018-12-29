
var urlEPEAccion = __AJAX_PATH__ + 'presupuesto/filtrar_epe_1/';

$(document).ready(function () {

    initReporteTitle();

    initDatepickerInputs();

    initFiltroButton();

    filtrarEPE();

});

/**
 * 
 * @param {type} cuentasPresupuestariasEconomicas
 * @returns {undefined}
 */
function actualizarTabla(cuentasPresupuestariasEconomicas) {

    $('.epe_table tbody tr').remove();

    $('.td-total').text('');

    var $noResultTr = '<tr><td colspan="6" class="no-result bold">No se encontraron resultados.</td></tr>';

    if (typeof cuentasPresupuestariasEconomicas[1] !== "undefined") {

        // Cuentas corrientes - Ingresos
        jQuery.each(cuentasPresupuestariasEconomicas[1]['suma']['movimientos'], function (index, cuentaPresupuestariaEconomica) {
            addTD($('#tabla-cuentas-corrientes'), cuentaPresupuestariaEconomica);
        });

        // Cuentas corrientes - Totales ingresos
        $('#cuentas-corrientes-total-sector-publico').text(cuentasPresupuestariasEconomicas[1]['suma']['total']['montoSectorPublico']);
        $('#cuentas-corrientes-total-sector-privado').text(cuentasPresupuestariasEconomicas[1]['suma']['total']['montoSectorPrivado']);
        $('#cuentas-corrientes-total-ejecucion-mes').text(cuentasPresupuestariasEconomicas[1]['suma']['total']['montoEjecucionMes']);
        $('#cuentas-corrientes-total-acumulado').text(cuentasPresupuestariasEconomicas[1]['suma']['total']['montoTotalAcumulado']);

        // Cuentas corrientes - Gastos
        jQuery.each(cuentasPresupuestariasEconomicas[1]['resta']['movimientos'], function (index, cuentaPresupuestariaEconomica) {
            addTD($('#tabla-gastos-corrientes'), cuentaPresupuestariaEconomica);
        });

        // Cuentas corrientes - Totales gastos
        $('#gastos-corrientes-total-sector-publico').text(cuentasPresupuestariasEconomicas[1]['resta']['total']['montoSectorPublico']);
        $('#gastos-corrientes-total-sector-privado').text(cuentasPresupuestariasEconomicas[1]['resta']['total']['montoSectorPrivado']);
        $('#gastos-corrientes-total-ejecucion-mes').text(cuentasPresupuestariasEconomicas[1]['resta']['total']['montoEjecucionMes']);
        $('#gastos-corrientes-total-acumulado').text(cuentasPresupuestariasEconomicas[1]['resta']['total']['montoTotalAcumulado']);

        // Cuentas corrientes - Resultado economico
        $('#resultado-economico-total-sector-publico').text(cuentasPresupuestariasEconomicas['resuladoEconomico']['montoSectorPublico']);
        $('#resultado-economico-total-sector-privado').text(cuentasPresupuestariasEconomicas['resuladoEconomico']['montoSectorPrivado']);
        $('#resultado-economico-total-ejecucion-mes').text(cuentasPresupuestariasEconomicas['resuladoEconomico']['montoEjecucionMes']);
        $('#resultado-economico-total-acumulado').text(cuentasPresupuestariasEconomicas['resuladoEconomico']['montoTotalAcumulado']);
    }
    else {
        $('#tabla-cuentas-corrientes tbody').append($noResultTr);

        $('#tabla-gastos-corrientes tbody').append($noResultTr);
    }


    if (typeof cuentasPresupuestariasEconomicas[2] !== "undefined") {

        // Cuenta capital - Recursos
        jQuery.each(cuentasPresupuestariasEconomicas[2]['suma']['movimientos'], function (index, cuentaPresupuestariaEconomica) {
            addTD($('#tabla-cuentas-capital'), cuentaPresupuestariaEconomica);
        });

        // Cuenta capital - Totales recursos
        $('#cuentas-capital-total-sector-publico').text(cuentasPresupuestariasEconomicas[2]['suma']['total']['montoSectorPublico']);
        $('#cuentas-capital-total-sector-privado').text(cuentasPresupuestariasEconomicas[2]['suma']['total']['montoSectorPrivado']);
        $('#cuentas-capital-total-ejecucion-mes').text(cuentasPresupuestariasEconomicas[2]['suma']['total']['montoEjecucionMes']);
        $('#cuentas-capital-total-acumulado').text(cuentasPresupuestariasEconomicas[2]['suma']['total']['montoTotalAcumulado']);

        // Cuenta capital - Gastos
        jQuery.each(cuentasPresupuestariasEconomicas[2]['resta']['movimientos'], function (index, cuentaPresupuestariaEconomica) {
            addTD($('#tabla-gastos-capital'), cuentaPresupuestariaEconomica);
        });

        // Cuenta capital - Totales gastos
        $('#gastos-capital-total-sector-publico').text(cuentasPresupuestariasEconomicas[2]['resta']['total']['montoSectorPublico']);
        $('#gastos-capital-total-sector-privado').text(cuentasPresupuestariasEconomicas[2]['resta']['total']['montoSectorPrivado']);
        $('#gastos-capital-total-ejecucion-mes').text(cuentasPresupuestariasEconomicas[2]['resta']['total']['montoEjecucionMes']);
        $('#gastos-capital-total-acumulado').text(cuentasPresupuestariasEconomicas[2]['resta']['total']['montoTotalAcumulado']);

        // Cuenta capital - Resultado financiero
        $('#resultado-financiero-total-sector-publico').text(cuentasPresupuestariasEconomicas['resuladoFinanciero']['montoSectorPublico']);
        $('#resultado-financiero-total-sector-privado').text(cuentasPresupuestariasEconomicas['resuladoFinanciero']['montoSectorPrivado']);
        $('#resultado-financiero-total-ejecucion-mes').text(cuentasPresupuestariasEconomicas['resuladoFinanciero']['montoEjecucionMes']);
        $('#resultado-financiero-total-acumulado').text(cuentasPresupuestariasEconomicas['resuladoFinanciero']['montoTotalAcumulado']);
    }
    else {
        $('#tabla-cuentas-capital tbody').append($noResultTr);

        $('#tabla-gastos-capital tbody').append($noResultTr);
    }


    if (typeof cuentasPresupuestariasEconomicas[3] !== "undefined") {

        // Cuenta financiamiento - Fuentes
        jQuery.each(cuentasPresupuestariasEconomicas[3]['suma']['movimientos'], function (index, cuentaPresupuestariaEconomica) {
            addTD($('#tabla-cuentas-financiamiento'), cuentaPresupuestariaEconomica);
        });

        // Cuenta financiamiento - Totales fuentes
        $('#cuentas-financiamiento-total-sector-publico').text(cuentasPresupuestariasEconomicas[3]['suma']['total']['montoSectorPublico']);
        $('#cuentas-financiamiento-total-sector-privado').text(cuentasPresupuestariasEconomicas[3]['suma']['total']['montoSectorPrivado']);
        $('#cuentas-financiamiento-total-ejecucion-mes').text(cuentasPresupuestariasEconomicas[3]['suma']['total']['montoEjecucionMes']);
        $('#cuentas-financiamiento-total-acumulado').text(cuentasPresupuestariasEconomicas[3]['suma']['total']['montoTotalAcumulado']);

        // Cuenta financiamiento - Aplicaciones
        jQuery.each(cuentasPresupuestariasEconomicas[3]['resta']['movimientos'], function (index, cuentaPresupuestariaEconomica) {
            addTD($('#tabla-aplicaciones-financieras'), cuentaPresupuestariaEconomica);
        });

        // Cuenta financiamiento - Totales aplicaciones
        $('#aplicaciones-financieras-total-sector-publico').text(cuentasPresupuestariasEconomicas[3]['resta']['total']['montoSectorPublico']);
        $('#aplicaciones-financieras-total-sector-privado').text(cuentasPresupuestariasEconomicas[3]['resta']['total']['montoSectorPrivado']);
        $('#aplicaciones-financieras-total-ejecucion-mes').text(cuentasPresupuestariasEconomicas[3]['resta']['total']['montoEjecucionMes']);
        $('#aplicaciones-financieras-total-acumulado').text(cuentasPresupuestariasEconomicas[3]['resta']['total']['montoTotalAcumulado']);

        // Cuenta financiamiento - Diferencia
        $('#diferencia-total-sector-publico').text(cuentasPresupuestariasEconomicas['totalAplicacionesFinancieras']['montoSectorPublico']);
        $('#diferencia-total-sector-privado').text(cuentasPresupuestariasEconomicas['totalAplicacionesFinancieras']['montoSectorPrivado']);
        $('#diferencia-total-ejecucion-mes').text(cuentasPresupuestariasEconomicas['totalAplicacionesFinancieras']['montoEjecucionMes']);
        $('#diferencia-total-acumulado').text(cuentasPresupuestariasEconomicas['totalAplicacionesFinancieras']['montoTotalAcumulado']);
    }
    else {
        $('#tabla-cuentas-financiamiento tbody').append($noResultTr);

        $('#tabla-aplicaciones-financieras tbody').append($noResultTr);
    }

    initExport();
}

/**
 * 
 * @param {type} $tabla
 * @param {type} cuentaPresupuestariaEconomica
 * @returns {undefined}
 */
function addTD($tabla, cuentaPresupuestariaEconomica) {

    var indiceNuevo = $tabla.find('tbody tr').length + 1;

    var esImputable = cuentaPresupuestariaEconomica['esImputable'] == 1;

    var paddingNivel = cuentaPresupuestariaEconomica['nivel'] + 'em';

    $tabla.find('tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td class="text-left' + (esImputable ? '' : ' bold') + '" style="padding-left: ' + paddingNivel + ';">' + cuentaPresupuestariaEconomica['codigo'] + '</td>\n\
                <td class="text-left' + (esImputable ? '' : ' bold') + '" style="padding-left: ' + paddingNivel + ';">' + cuentaPresupuestariaEconomica['denominacion'] + '</td>\n\
                <td class="text-right money-format' + (esImputable ? '' : ' bold') + '">' + cuentaPresupuestariaEconomica['montoSectorPublico'] + '</td>\n\
                <td class="text-right money-format' + (esImputable ? '' : ' bold') + '">' + cuentaPresupuestariaEconomica['montoSectorPrivado'] + '</td>\n\
                <td class="text-right money-format' + (esImputable ? '' : ' bold') + '">' + cuentaPresupuestariaEconomica['montoEjecucionMes'] + '</td>\n\
                <td class="text-right money-format' + (esImputable ? '' : ' bold') + '">' + cuentaPresupuestariaEconomica['montoTotalAcumulado'] + '</td>\n\
            </tr>'
            );
}




function initExport() {
    $('.export-epe-1').html("");
    $('.export-epe-1').prepend(
            '<div class="btn-group pull-right">\n\
                <div class="btn-group">\n\
                    <button class="btn btn-sm green excel-custom" type="button">\n\
                    <i class="fa fa-floppy-o"></i>\n\
                    Exportar a Excel</button>\n\
                </div>\n\
            </div>\n\
            <div class="btn-group pull-right">\n\
                <div class="btn-group">\n\
                    <button class="btn btn-sm dark pdf-custom" type="button">\n\
                    Exportar a PDF\n\
                    <i class="fa fa-file-pdf-o"></i></button>\n\
                </div>\n\
            </div>');
    $('.excel-custom').on('click', function (e) {
        e.preventDefault();
        exportPresupuesto('excel');
        e.stopPropagation();
    });
    $('.pdf-custom').on('click', function (e) {
        e.preventDefault();
        exportPresupuesto('pdf');
        e.stopPropagation();
    });
}

function exportPresupuesto(tipo) {

    var corrienteSuma = Array();
    $('#tabla-cuentas-corrientes').find('tbody').find('tr').each(function (e, v) {
        corrienteSuma[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            corrienteSuma[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    corrienteSuma[e][f + index] = '';
                }
            }
        });
    });
    var corrienteResta = Array();
    $('#tabla-gastos-corrientes').find('tbody').find('tr').each(function (e, v) {
        corrienteResta[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            corrienteResta[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    corrienteResta[e][f + index] = '';
                }
            }
        });
    });
//    var corriente = Array();
//    $('#tabla-cuentas-corrientes').find('tbody').find('tr').each(function (e, v) {
//        corriente[e] = Array();
//        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
//            corriente[e][f] = $(u).html();
//            if ($(u).prop('colspan') > 1) {
//                for (index = 1; index < $(u).prop('colspan'); index++) {
//                    corriente[e][f + index] = '';
//                }
//            }
//        });
//    });

    var capitalSuma = Array();
    $('#tabla-cuentas-capital').find('tbody').find('tr').each(function (e, v) {
        capitalSuma[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            capitalSuma[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    capitalSuma[e][f + index] = '';
                }
            }
        });
    });
    var capitalResta = Array();
    $('#tabla-gastos-capital').find('tbody').find('tr').each(function (e, v) {
        capitalResta[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            capitalResta[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    capitalResta[e][f + index] = '';
                }
            }
        });
    });
//    var capital = Array();
//    $('#tabla-cuentas-capital').find('tbody').find('tr').each(function (e, v) {
//        capital[e] = Array();
//        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
//            capital[e][f] = $(u).html();
//            if ($(u).prop('colspan') > 1) {
//                for (index = 1; index < $(u).prop('colspan'); index++) {
//                    capital[e][f + index] = '';
//                }
//            }
//        });
//    });

    var fuentesSuma = Array();
    $('#tabla-cuentas-financiamiento').find('tbody').find('tr').each(function (e, v) {
        fuentesSuma[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            fuentesSuma[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    fuentesSuma[e][f + index] = '';
                }
            }
        });
    });
    var fuentesResta = Array();
    $('#tabla-aplicaciones-financieras').find('tbody').find('tr').each(function (e, v) {
        fuentesResta[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            fuentesResta[e][f] = $(u).html();
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    fuentesResta[e][f + index] = '';
                }
            }
        });
    });
//    var fuentes = Array();
//    $('#tabla-cuentas-fuentes').find('tbody').find('tr').each(function (e, v) {
//        fuentes[e] = Array();
//        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
//            fuentes[e][f] = $(u).html();
//            if ($(u).prop('colspan') > 1) {
//                for (index = 1; index < $(u).prop('colspan'); index++) {
//                    fuentes[e][f + index] = '';
//                }
//            }
//        });
//    });



    content = {
        content: {
            title: 'EPE_1',
            sheets: {
                0: {
                    title: 'Corriente',
                    tables: {
                        0: {
                            title: 'Ingresos Corrientes',
                            titulo_alternativo: '',
                            data: JSON.stringify(corrienteSuma),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-cuentas-corrientes')))
                        },
                        1: {
                            title: 'Gastos Corrientes',
                            titulo_alternativo: '',
                            data: JSON.stringify(corrienteResta),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-gastos-corrientes')))
//                        },
//                        2: {
//                            title: '',
//                            titulo_alternativo: '',
//                            data: JSON.stringify(corriente),
//                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-cuentas-corrientes')))
                        }
                    }
                },
                1: {
                    title: 'Capital',
                    tables: {
                        0: {
                            title: 'Recursos de capital',
                            titulo_alternativo: '',
                            data: JSON.stringify(capitalSuma),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-cuentas-capital')))
                        },
                        1: {
                            title: 'Gastos de capital',
                            titulo_alternativo: '',
                            data: JSON.stringify(capitalResta),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-gastos-capital')))
//                        },
//                        2: {
//                            title: '',
//                            titulo_alternativo: '',
//                            data: JSON.stringify(capital),
//                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-cuentas-capital')))
                        }
                    }
                },
                2: {
                    title: 'Financiamiento',
                    tables: {
                        0: {
                            title: 'Fuentes financieras',
                            titulo_alternativo: '',
                            data: JSON.stringify(fuentesSuma),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-cuentas-financiamiento')))
                        },
                        1: {
                            title: 'Aplicaciones financieras',
                            titulo_alternativo: '',
                            data: JSON.stringify(fuentesResta),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-aplicaciones-financieras')))
//                        },
//                        2: {
//                            title: '',
//                            titulo_alternativo: '',
//                            data: JSON.stringify(fuentes),
//                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-cuentas-fuentes')))
                        }
                    }
                }
            }
        }
    };

    open_window('POST', __AJAX_PATH__ + 'export_' + tipo, content, '_blank');
}

function getHeadersTableCustomPresupuesto(table) {
    var a = Array();
    $(table).find('thead').find('tr th').not('.ctn_acciones').each(function (e, v) {
        a.push({texto: $(v).text(),
            formato: $(v).attr('export-format') ? $(v).attr('export-format') : 'text'
        });
    });
    return a;
}