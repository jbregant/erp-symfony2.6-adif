
$(document).ready(function () {

    initMostrarDetalleHandler();
    initExport();

    initBusqueda();

});

/**
 * 
 * @returns {undefined}
 */
function initMostrarDetalleHandler() {

    $('.monto-cuenta-presupuestaria-economica').mouseover(function () {
        $(this).find('.link-detalle-presupuestario').show();
    });

    $('.monto-cuenta-presupuestaria-economica').mouseout(function () {
        $(this).find('.link-detalle-presupuestario').hide();
    });


    // Handler al apretar el botón de "Ver detalle"
    $('.link-detalle-presupuestario').click(function () {

        var idCuentaPresupuestariaEconomica = $(this).data('cuenta-presupuestaria-economica');

        var tipoAsientoPresupuestario = $(this).data('tipo-asiento-presupuestario');

        var data = {
            id_cuenta_presupuestaria_economica: idCuentaPresupuestariaEconomica,
            tipo_asiento_presupuestario: tipoAsientoPresupuestario,
            ejercicio: ejercicio
        };

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'cuentapresupuestariaeconomica/asientos_presupuestarios/',
            data: data,
            success: function (asientosPresupuestarios) {

                var $contenidoDetalle = $('<div class="portlet-body">');

                $contenidoDetalle.append($('<div class="table-toolbar">'));

                $contenidoDetalle
                        .append(
                                $('<table id="table-asientos-presupuestarios" dataexport-title="asientos-presupuestarios" \n\
                                    class="table table-striped table-hover table-bordered table-condensed dt-multiselect export-excel">')
                                .append(
                                        $('<thead>')
                                        .append(
                                                $('<tr class="replace-inputs filter">')
                                                .append($('<th>'))
                                                .append($('<th class="not-in-filter">'))
                                                .append($('<th data-type="date">').text('Fecha'))
                                                .append($('<th>').text('Monto'))
                                                .append($('<th>').text('Asiento nº'))
                                                .append($('<th>').text('Detalle'))
                                                )
                                        .append(
                                                $('<tr class="headers">')
                                                .append($('<th class="no-order entity_id">'))
                                                .append($('<th class="text-center table-checkbox no-order">')
                                                        .append($('<input type="checkbox" class="group-checkable" data-set="#table-asientos-presupuestarios .checkboxes" />'
                                                                ))
                                                        )
                                                .append($('<th date class="nowrap text-center">').text('Fecha'))
                                                .append($('<th currency class="nowrap text-center">').text('Monto'))
                                                .append($('<th class="nowrap text-center">').text('Asiento nº'))
                                                .append($('<th>').text('Detalle'))
                                                )
                                        )
                                .append($('<tbody>'))
                                );

                jQuery.each(asientosPresupuestarios, function (index, asientoPresupuestario) {

                    var monto = asientoPresupuestario['monto'];

                    $contenidoDetalle.find('tbody')
                            .append($('<tr>')
                                    .append($('<td>').text(asientoPresupuestario['id']))
                                    .append($('<td class="text-center">')
                                            .append($('<input type="checkbox" class="checkboxes" value="" />')))
                                    .append($('<td class="nowrap">').text(asientoPresupuestario['fecha']))
                                    .append($('<td class="nowrap money-format" style="margin-left:3em">')
                                            .text(monto))
                                    .append($('<td class="nowrap">')
                                            .append($('<a target="_blank" href="' + asientoPresupuestario['pathShowAsientoContable'] + '">')
                                                    .text(asientoPresupuestario['numeroAsiento'])))
                                    .append($('<td>').text(asientoPresupuestario['detalle']))
                                    );
                });

                show_dialog({
                    titulo: 'Detalle de asientos presupuestarios',
                    contenido: $contenidoDetalle,
                    callbackCancel: function () {
                        desbloquear();
                        return;
                    },
                    callbackSuccess: function () {
                        desbloquear();
                        return;
                    }
                });

                initTable();
            }
        });

    });
}

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    $('.modal-footer').find('.btn-default').remove();
    $('.modal-footer').find('.btn-primary').text('Cerrar');

    setMasks();

    var options = {
        "searching": true,
        "ordering": true,
        "info": false,
        "paging": true
    };

    dt_init($('#table-asientos-presupuestarios'), options);
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
    });
}


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
                                actual: $(footerCorrienteSuma[1]).find('span').html().replace('$ ', '')
                            }
                        },
                        1: {
                            title: 'Gastos Corrientes',
                            titulo_alternativo: '',
                            data: JSON.stringify(corrienteResta),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-corrientes-resta'))),
                            footer: {
                                titulo: $(footerCorrienteResta[0]).html(),
                                actual: $(footerCorrienteResta[1]).find('span').html().replace('$ ', '')
                            }
                        }
                    },
                    footer: {
                        titulo: $('#tabla-cuentas-corrientes td:first').html(),
                        actual: $(footerCorriente[0]).html().replace('$ ', '')
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
                                actual: $(footerCapitalSuma[1]).find('span').html().replace('$ ', '')
                            }
                        },
                        1: {
                            title: 'Gastos de capital',
                            titulo_alternativo: '',
                            data: JSON.stringify(capitalResta),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-capital-resta'))),
                            footer: {
                                titulo: $(footerCapitalResta[0]).html(),
                                actual: $(footerCapitalResta[1]).find('span').html().replace('$ ', '')
                            }
                        }
                    },
                    footer: {
                        titulo: $('#tabla-cuentas-capital td:first').html(),
                        actual: $(footerCapital[0]).html().replace('$ ', '')
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
                                actual: $(footerFinanciamientoSuma[1]).find('span').html().replace('$ ', '')
                            }
                        },
                        1: {
                            title: 'Aplicaciones financieras',
                            titulo_alternativo: '',
                            data: JSON.stringify(fuentesResta),
                            headers: JSON.stringify(getHeadersTableCustomPresupuesto($('#tabla-fuentes-resta'))),
                            footer: {
                                titulo: $(footerFinanciamientoResta[0]).html(),
                                actual: $(footerFinanciamientoResta[1]).find('span').html().replace('$ ', '')
                            }
                        }
                    },
                    footer: {
                        titulo: $('#tabla-cuentas-fuentes td:first').html(),
                        actual: $(footerFinanciamiento[0]).html().replace('$ ', '')
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