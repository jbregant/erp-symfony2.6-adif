var _dt_libro_iva_compras;
var _data_last = [];

jQuery(document).ready(function () {

    initTable();

    initFiltro();

    initFiltroButton();

    initExportHandlers();
});


/**
 *
 * @returns {undefined}
 */
function initTable() {
	
	
    _dt_libro_iva_compras = dt_init($('#libro_iva_compras_table'));

    $('#libro_iva_compras_table').DataTable().order([0, 'asc']).draw();
//	updateCaptionTitle();
}

/**
 *
 * @returns {undefined}
 */
function initFiltro() {
    /*
    var $fechaInicio = getFirstDateOfCurrentMonth(ejercicioContableSesion);
    var $fechaFin = getEndingDateOfCurrentMonth(ejercicioContableSesion);

    $('#adif_contablebundle_filtro_fechaInicio').datepicker("setDate", $fechaInicio);
    $('#adif_contablebundle_filtro_fechaFin').datepicker("setDate", $fechaFin);
    */

	filtrarLibroIVACompras();
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#filtrar').on('click', function (e) {
        filtrarLibroIVACompras();
    });
}

/**
 *
 * @returns {undefined}
 */
function filtrarLibroIVACompras() {
    var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();
    var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();
    
    setFechasFiltro($fechaInicio, $fechaFin);

    if (validarRangoFechas($fechaInicio, $fechaFin)) {
        if ($fechaInicio && $fechaFin) {
            var data = {
                fechaInicio: $fechaInicio,
                fechaFin: $fechaFin
            };

            $.ajax({
                type: "POST",
                data: data,
                url: __AJAX_PATH__ + 'comprobantescompra/filtrar_libroiva_compras/'
            }).done(function (comprobantes) {
                _data_last = comprobantes;

                actualizarTabla(comprobantes);

                updateCaptionTitle();
            });
        } else {
            show_alert({
                title: 'Error en las fechas',
                msg: 'Ingrese la fecha de inicio y la de fin para filtrar la tabla',
                type: 'error'
            });
        }
    }
}

/**
 *
 * @param {type} comprobantes
 * @returns {undefined}
 */
function actualizarTabla(comprobantes) {
    $('#libro_iva_compras_table').DataTable().rows().remove().draw();
    $('#libro_iva_compras_table tbody tr').remove();

    jQuery.each(comprobantes, function (index, comprobante) {
        addComprobanteTD(comprobante);
    });

    $('#libro_iva_compras_table').DataTable().draw();

    if ($('#libro_iva_compras_table tbody td').length === 0) {
        $('#libro_iva_compras_table').hide();

        $('.no-result').remove();
        $('.libro_iva_compras_content').append('<span class="no-result">No se encontraron resultados.</span>');
    } else {
        $('.no-result').remove();
        $('#libro_iva_compras_table').show();
    }
}

/**
 *
 * @param {type} comprobante
 * @returns {undefined}
 */
function addComprobanteTD(comprobante) {
    var indiceNuevo = $('#libro_iva_compras_table tbody tr').length + 1;

    var jRow = $(['<tr tr_index="' + indiceNuevo + '">',
        '<td class="text-center nowrap">' + comprobante['idAsiento'] + '</td>',
        '<td class="text-center nowrap">' + comprobante['fechaComprobante'] + '</td>',
        '<td class="text-center nowrap">' + comprobante['tipoComprobante'] + ((comprobante['letraComprobante'] != '') ? ' (' + comprobante['letraComprobante'] + ')' : '') + '</td>',
        '<td class="text-center nowrap">' + comprobante['numeroComprobante'] + '</td>',
        '<td class="text-center nowrap">' + comprobante['razonSocial'] + ' <br /> ' + comprobante['cuit'] + '</td>',
        '<td class="text-center nowrap">' + comprobante['condicionImpositiva'] + '</td>',
        '<td class="neto mr nowrap">',
        '<table class="full">',
        '<tr><td class="l">2,5%:</td><td class="v">' + comprobante['importeNeto2_5'] + '</td></tr>',
        '<tr><td class="l">10,5%:</td><td class="v">' + comprobante['importeNeto105'] + '</td></tr>',
        '<tr><td class="l">21,0%:</td><td class="v">' + comprobante['importeNeto21'] + '</td></tr>',
        '<tr><td class="l">27,0%:</td><td class="v">' + comprobante['importeNeto27'] + '</td></tr>',
        '<tr><td class="l bold"></td><td class="v total">' + comprobante['importeTotalNeto'] + '</td></tr>',
        '</table>',
        '</td>',
        '<td class="text-right bold">' + comprobante['importeTotalExento'] + '</td>',
        '<td class="iva mr nowrap">',
        '<table class="full">',
        '<tr><td class="l">2,5%:</td><td class="v">' + comprobante['iva2_5'] + '</td></tr>',
        '<tr><td class="l">10,5%:</td><td class="v">' + comprobante['iva105'] + '</td></tr>',
        '<tr><td class="l">21,0%:</td><td class="v">' + comprobante['iva21'] + '</td></tr>',
        '<tr><td class="l">27,0%:</td><td class="v">' + comprobante['iva27'] + '</td></tr>',
        '<tr><td class="l"></td><td class="v total">' + comprobante['totalIVA'] + '</td></tr>',
        '</table>',
        '</td>',
        _.map(comprobante['percepciones'], function (tipo_percepcion, k_per) {
            return [
                '<td class="mr nowrap">',
                '<table class="full">',
                _.map(tipo_percepcion, function (tipo_percepcion, jurisdiccion) {
                    return ('<tr><td class="l">' + tipo_percepcion.jurisdiccion + ': </td><td class="v">' + tipo_percepcion.monto + '</td></tr>');
                }).join(''),
                (comprobante['total_percepciones'][k_per] > '0,00' ? '<tr><td class="l"></td><td class="v total">' + comprobante['total_percepciones'][k_per] + '</td></tr>' : ''),
                '</table>',
                '</td>'
            ].join('');
        }),
        '<td class="text-right">' + comprobante['total_percepciones']['GCIAS'] + '</td>',
        '<td class="text-right">' + comprobante['total_percepciones']['IVA'] + '</td>',
        '<td class="text-right">' + comprobante['total_percepciones']['SUSS'] + '</td>',
        '<td class="text-right">' + comprobante['totalOtrosImpuestos'] + '</td>',
        '<td class="text-right bold">' + comprobante['totalFactura'] + '</td>',
        '</tr>'].join(''));

    $('#libro_iva_compras_table').DataTable().row.add(jRow);
}


/**
 *
 * @returns {undefined}
 */
function updateCaptionTitle() {
    var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
    var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();


    var fechaInicioSplited = $fechaInicio.split("/");
    var fechaInicioDate = new Date(fechaInicioSplited[2], fechaInicioSplited[1] - 1, fechaInicioSplited[0]);

    var $ejercicioInicio = fechaInicioDate.getFullYear();

    var fechaFinSplited = $fechaFin.split("/");
    var fechaFinDate = new Date(fechaFinSplited[2], fechaFinSplited[1] - 1, fechaFinSplited[0]);

    var $ejercicioFin = fechaFinDate.getFullYear();

    if ($ejercicioInicio === $ejercicioFin) {
        $('.caption-ejercicio').text($ejercicioInicio);
    } else {
        $('.caption-ejercicio').text($ejercicioInicio + ' - ' + $ejercicioFin);
    }

    $('.caption-fecha-desde').text($fechaInicio);
    $('.caption-fecha-hasta').text($fechaFin);

    $('.reporte_contable_title').show();
}

function initExportHandlers() {
    $(document).on('click', '#export-excel-button, #export-pdf-button', function (e) {

        var pdf = $(this).hasClass('pdf');
        
        if (!pdf) {
            var cod_jurisdicciones = [];

            _.each(_data_last, function (row_comprobante) {
                _.each(row_comprobante.percepciones.IIBB, function (percepcion_j) {

                    if (jQuery.inArray(percepcion_j.jurisdiccion, cod_jurisdicciones) === -1) {
                        cod_jurisdicciones.push(percepcion_j.jurisdiccion);
                    }
                });
            });
        }

        var export_data = _.map(_data_last, function (row_comprobante) {
            var row =
                    [
                        row_comprobante.idAsiento,
                        row_comprobante.fechaComprobante,
                        row_comprobante.tipoComprobante,
                        row_comprobante.letraComprobante,
                        row_comprobante.numeroComprobante,
                        row_comprobante.razonSocial,
                        row_comprobante.cuit,
                        row_comprobante.condicionImpositiva,
                        row_comprobante.importeNeto2_5,
                        row_comprobante.importeNeto105,
                        row_comprobante.importeNeto21,
                        row_comprobante.importeNeto27,
                        row_comprobante.importeTotalNeto,
                        row_comprobante.importeTotalExento,
                        row_comprobante.iva2_5,
                        row_comprobante.iva105,
                        row_comprobante.iva21,
                        row_comprobante.iva27,
                        row_comprobante.totalIVA
                    ];
            if (!pdf) {
                for (i = 0; i < cod_jurisdicciones.length; i++) {
                    row.push(0);
                }

                var indice = 19;
                var index = 0;

                /* Agregar los montos de percepciones por IIBB */
                _.each(cod_jurisdicciones, function (cod_jur) {

                    _.each(row_comprobante.percepciones.IIBB, function (per) {

                        if (per.jurisdiccion == cod_jur) {
                            row[indice + index] = per.monto;
                        }
                    });

                    index++;
                });
            }

            row.push(row_comprobante.total_percepciones.IIBB);
            row.push(row_comprobante.total_percepciones.GCIAS);
            row.push(row_comprobante.total_percepciones.IVA);
            row.push(row_comprobante.total_percepciones.SUSS);

            row.push(row_comprobante.totalOtrosImpuestos);
            row.push(row_comprobante.totalFactura);

            return row;
        });

        /* headers */
        var headers = [
            {texto: 'Id Asiento', formato: 'text'},
            {texto: 'Fecha', formato: 'date'},
            {texto: 'Comprobante', formato: 'text'},
            {texto: 'Letra', formato: 'text'},
            {texto: 'Número', formato: 'text'},
            {texto: 'Proveedor/Consultor', formato: 'text'},
            {texto: 'CUIT', formato: 'text'},
            {texto: 'Condición impositiva', formato: 'text'},
            {texto: 'Neto 2,5%', formato: 'currency'},
            {texto: 'Neto 10,5%', formato: 'currency'},
            {texto: 'Neto 21%', formato: 'currency'},
            {texto: 'Neto 27%', formato: 'currency'},
            {texto: 'Total neto', formato: 'currency'},
            {texto: 'Total exento', formato: 'currency'},
            {texto: 'IVA 2,5%', formato: 'currency'},
            {texto: 'IVA 10,5%', formato: 'currency'}, {texto: 'IVA 21%', formato: 'currency'},
            {texto: 'IVA 27%', formato: 'currency'},
            {texto: 'Total IVA', formato: 'currency'}
        ];
        if (!pdf) {
            _.each(cod_jurisdicciones, function (cod_jur) {
                headers.push({texto: 'Perc. IIBB - ' + cod_jur, formato: 'currency'});
            });
        }
        headers.push({texto: 'Total perc. IIBB', formato: 'currency'});
        headers.push({texto: 'Perc. GCIAS', formato: 'currency'});
        headers.push({texto: 'Perc. IVA', formato: 'currency'});
        headers.push({texto: 'Perc. SUSS', formato: 'currency'});
        headers.push({texto: 'Otros impuestos', formato: 'currency'});
        headers.push({texto: 'Importe total', formato: 'currency'});

        var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();
        var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();

        content = {
            content: {title: 'Libro IVA COMPRAS ' + $fechaInicio.replace(/\-/g, '_') + '__' + $fechaFin.replace(/\-/g, '_'),
                sheets: {
                    0: {
                        title: 'Libro IVA COMPRAS ',
                        tables: {
                            0: {
                                title: 'Libro IVA COMPRAS ' + $fechaInicio.replace(/\-/g, '_') + '__' + $fechaFin.replace(/\-/g, '_'),
                                titulo_alternativo: '',
                                data: JSON.stringify(export_data),
                                headers: JSON.stringify(headers)
                            }
                        }
                    }
                }
            }
        };


        open_window('POST', __AJAX_PATH__ + (pdf ? 'libro_iva_compras/export_pdf' : 'export_excel'), content, '_blank');

        e.stopPropagation();
    });
}