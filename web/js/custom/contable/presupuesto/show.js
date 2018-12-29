
$(document).ready(function () {
    initExport();
    initBusqueda();
});

function initExport() {
    $('.presupuesto').prepend(
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
    $('#tabla-corrientes-suma').find('tbody').find('tr').each(function (e, v) {
        corrienteSuma[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            corrienteSuma[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    corrienteSuma[e][f + index] = '';
                }
            }
        });
    });
    var corrienteResta = Array();
    $('#tabla-corrientes-resta').find('tbody').find('tr').each(function (e, v) {
        corrienteResta[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            corrienteResta[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    corrienteResta[e][f + index] = '';
                }
            }
        });
    });
    var corriente = Array();
    $('#tabla-cuentas-corrientes').find('tbody').find('tr').each(function (e, v) {
        corriente[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            corriente[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    corriente[e][f + index] = '';
                }
            }
        });
    });

    var capitalSuma = Array();
    $('#tabla-capital-suma').find('tbody').find('tr').each(function (e, v) {
        capitalSuma[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            capitalSuma[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    capitalSuma[e][f + index] = '';
                }
            }
        });
    });
    var capitalResta = Array();
    $('#tabla-capital-resta').find('tbody').find('tr').each(function (e, v) {
        capitalResta[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            capitalResta[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    capitalResta[e][f + index] = '';
                }
            }
        });
    });
    var capital = Array();
    $('#tabla-cuentas-capital').find('tbody').find('tr').each(function (e, v) {
        capital[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            capital[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    capital[e][f + index] = '';
                }
            }
        });
    });

    var fuentesSuma = Array();
    $('#tabla-fuentes-suma').find('tbody').find('tr').each(function (e, v) {
        fuentesSuma[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            fuentesSuma[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    fuentesSuma[e][f + index] = '';
                }
            }
        });
    });
    var fuentesResta = Array();
    $('#tabla-fuentes-resta').find('tbody').find('tr').each(function (e, v) {
        fuentesResta[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            fuentesResta[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    fuentesResta[e][f + index] = '';
                }
            }
        });
    });
    var fuentes = Array();
    $('#tabla-cuentas-fuentes').find('tbody').find('tr').each(function (e, v) {
        fuentes[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            fuentes[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    fuentes[e][f + index] = '';
                }
            }
        });
    });

    footerCorriente = $('#tabla-cuentas-corrientes .total-inicial');
    footerCorrienteSuma = $('#tabla-corrientes-suma tfoot .bold');
    footerCorrienteResta = $('#tabla-corrientes-resta tfoot .bold');

    footerCapital = $('#tabla-cuentas-capital .total-inicial');
    footerCapitalSuma = $('#tabla-capital-suma tfoot .bold');
    footerCapitalResta = $('#tabla-capital-resta tfoot .bold');

    footerFinanciamiento = $('#tabla-cuentas-fuentes .total-inicial');
    footerFinanciamientoSuma = $('#tabla-fuentes-suma tfoot .bold');
    footerFinanciamientoResta = $('#tabla-fuentes-resta tfoot .bold');

    content = {
        content: {
            title: $('.presupuesto').attr('data-export-title'),
            sheets: {
                0: {
                    title: 'Corriente',
                    tables: {
                        0: {
                            title: 'Ingresos Corrientes',
                            titulo_alternativo: '',
                            data: JSON.stringify(corrienteSuma),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-corrientes-suma'))),
                            footer: {
                                titulo: $(footerCorrienteSuma[0]).html(),
                                inicial: $(footerCorrienteSuma[1]).find('span').html().replace('$ ', ''),
                                actual: $(footerCorrienteSuma[2]).find('span').html().replace('$ ', '')
                            }
                        },
                        1: {
                            title: 'Gastos Corrientes',
                            titulo_alternativo: '',
                            data: JSON.stringify(corrienteResta),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-corrientes-resta'))),
                            footer: {
                                titulo: $(footerCorrienteResta[0]).html(),
                                inicial: $(footerCorrienteResta[1]).find('span').html().replace('$ ', ''),
                                actual: $(footerCorrienteResta[2]).find('span').html().replace('$ ', '')
                            }
                        }
                    },
                    footer: {
                        titulo: $('#tabla-cuentas-corrientes td:first').html(),
                        inicial: $(footerCorriente[0]).html().replace('$ ', ''),
                        actual: $(footerCorriente[1]).html().replace('$ ', '')
                    }
                },
                1: {
                    title: 'Capital',
                    tables: {
                        0: {
                            title: 'Recursos de capital',
                            titulo_alternativo: '',
                            data: JSON.stringify(capitalSuma),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-capital-suma'))),
                            footer: {
                                titulo: $(footerCapitalSuma[0]).html(),
                                inicial: $(footerCapitalSuma[1]).find('span').html().replace('$ ', ''),
                                actual: $(footerCapitalSuma[2]).find('span').html().replace('$ ', '')
                            }
                        },
                        1: {
                            title: 'Gastos de capital',
                            titulo_alternativo: '',
                            data: JSON.stringify(capitalResta),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-capital-resta'))),
                            footer: {
                                titulo: $(footerCapitalResta[0]).html(),
                                inicial: $(footerCapitalResta[1]).find('span').html().replace('$ ', ''),
                                actual: $(footerCapitalResta[2]).find('span').html().replace('$ ', '')
                            }
                        }
                    },
                    footer: {
                        titulo: $('#tabla-cuentas-capital td:first').html(),
                        inicial: $(footerCapital[0]).html().replace('$ ', ''),
                        actual: $(footerCapital[1]).html().replace('$ ', '')
                    }
                },
                2: {
                    title: 'Financiamiento',
                    tables: {
                        0: {
                            title: 'Fuentes financieras',
                            titulo_alternativo: '',
                            data: JSON.stringify(fuentesSuma),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-fuentes-suma'))),
                            footer: {
                                titulo: $(footerFinanciamientoSuma[0]).html(),
                                inicial: $(footerFinanciamientoSuma[1]).find('span').html().replace('$ ', ''),
                                actual: $(footerFinanciamientoSuma[2]).find('span').html().replace('$ ', '')
                            }
                        },
                        1: {
                            title: 'Aplicaciones financieras',
                            titulo_alternativo: '',
                            data: JSON.stringify(fuentesResta),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-fuentes-resta'))),
                            footer: {
                                titulo: $(footerFinanciamientoResta[0]).html(),
                                inicial: $(footerFinanciamientoResta[1]).find('span').html().replace('$ ', ''),
                                actual: $(footerFinanciamientoResta[2]).find('span').html().replace('$ ', '')
                            }
                        }
                    },
                    footer: {
                        titulo: $('#tabla-cuentas-fuentes td:first').html(),
                        inicial: $(footerFinanciamiento[0]).html().replace('$ ', ''),
                        actual: $(footerFinanciamiento[1]).html().replace('$ ', '')
                    }
                }
            }
        }
    };

    open_window('POST', __AJAX_PATH__ + 'presupuesto/export_' + tipo, content, '_blank');
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