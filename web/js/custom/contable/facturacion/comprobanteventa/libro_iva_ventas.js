
var _dt_libro_iva_ventas;
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

    _dt_libro_iva_ventas = dt_init($('#libro_iva_ventas_table'));

    $('#libro_iva_ventas_table').DataTable().order([0, 'asc']).draw();
}

/**
 *
 * @returns {undefined}
 */
function initFiltro() {
    filtrarLibroIVAVentas();
}

/**
 *
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#filtrar').on('click', function (e) {
        filtrarLibroIVAVentas();
    });
}

/**
 *
 * @returns {undefined}
 */
function filtrarLibroIVAVentas() {

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
                url: __AJAX_PATH__ + 'comprobanteventa/filtrar_libroiva_ventas/'
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
    $('#libro_iva_ventas_table').DataTable().rows().remove().draw();
    $('#libro_iva_ventas_table tbody tr').remove();

    jQuery.each(comprobantes, function (index, comprobante) {
        addComprobanteTD(comprobante);
    });

    $('#libro_iva_ventas_table').DataTable().draw();

    if ($('#libro_iva_ventas_table tbody td').length === 0) {
        $('#libro_iva_ventas_table').hide();
        $('.no-result').remove();
        $('.libro_iva_ventas_content').append('<span class="no-result">No se encontraron resultados.</span>');
    } else {
        $('.no-result').remove();
        $('#libro_iva_ventas_table').show();
    }
}

/**
 *
 * @param {type} comprobante
 * @returns {undefined}
 */
function addComprobanteTD(comprobante) {

    var indiceNuevo = $('#libro_iva_ventas_table tbody tr').length + 1;

    var jRow = $(['<tr tr_index="' + indiceNuevo + '">',
        '<td class="text-center nowrap">' + comprobante['fechaComprobante'] + '</td>',
        '<td class="text-center nowrap">' + comprobante['tipoComprobante'] + ((comprobante['letraComprobante'] != '') ? ' (' + comprobante['letraComprobante'] + ')' : '') + '</td>',
        '<td class="text-center nowrap">' + comprobante['numeroComprobante'] + '</td>',
        '<td class="text-center nowrap">' + comprobante['razonSocial'] + ' <br /> ' + comprobante['cuit'] + '</td>',
        '<td class="neto mr nowrap">',
        '<table class="full">',
        '<tr><td class="l">10,5%:</td><td class="v">' + comprobante['importeNeto105'] + '</td></tr>',
        '<tr><td class="l">21,0%:</td><td class="v">' + comprobante['importeNeto21'] + '</td></tr>',
        '<tr><td class="l">27,0%:</td><td class="v">' + comprobante['importeNeto27'] + '</td></tr>',
        '<tr><td class="l bold"></td><td class="v total">' + comprobante['importeTotalNeto'] + '</td></tr>',
        '</table>',
        '</td>',
        '<td class="text-right bold">' + comprobante['importeTotalExento'] + '</td>',
        '<td class="iva mr nowrap">',
        '<table class="full">',
        '<tr><td class="l">10,5%:</td><td class="v">' + comprobante['iva105'] + '</td></tr>',
        '<tr><td class="l">21,0%:</td><td class="v">' + comprobante['iva21'] + '</td></tr>',
        '<tr><td class="l">27,0%:</td><td class="v">' + comprobante['iva27'] + '</td></tr>',
        '<tr><td class="l"></td><td class="v total">' + comprobante['totalIVA'] + '</td></tr>',
        '</table>',
        '</td>',
        '<td class="text-right bold">' + comprobante['percepcionIIBB'] + '</td>',
        '<td class="text-right bold">' + comprobante['percepcionIVA'] + '</td>',
        '<td class="text-right bold">' + comprobante['totalFactura'] + '</td>',
        '</tr>'].join(''));

    $('#libro_iva_ventas_table').DataTable().row.add(jRow);
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
        var export_data = _.map(_data_last, function (row_comprobante) {
            return row =
                    [
                        row_comprobante.fechaComprobante,
                        row_comprobante.tipoComprobante,
                        row_comprobante.letraComprobante,
                        row_comprobante.numeroComprobante,
                        row_comprobante.razonSocial,
                        row_comprobante.cuit,
                        row_comprobante.importeNeto105,
                        row_comprobante.importeNeto21,
                        row_comprobante.importeNeto27,
                        row_comprobante.importeTotalNeto,
                        row_comprobante.importeTotalExento,
                        row_comprobante.iva105,
                        row_comprobante.iva21,
                        row_comprobante.iva27,
                        row_comprobante.totalIVA,
                        row_comprobante.percepcionIIBB,
                        row_comprobante.percepcionIVA,
                        row_comprobante.totalFactura
                    ];
        });

        /* headers */
        var headers = [
            {texto: 'Fecha', formato: 'date'},
            {texto: 'Comprobante', formato: 'text'},
            {texto: 'Letra', formato: 'text'},
            {texto: 'Número', formato: 'text'},
            {texto: 'Cliente', formato: 'text'},
            {texto: 'CUIT', formato: 'text'},
            {texto: 'Neto 10,5%', formato: 'currency'},
            {texto: 'Neto 21%', formato: 'currency'},
            {texto: 'Neto 27%', formato: 'currency'},
            {texto: 'Total neto', formato: 'currency'},
            {texto: 'Total exento', formato: 'currency'},
            {texto: 'IVA 10,5%', formato: 'currency'},
            {texto: 'IVA 21%', formato: 'currency'},
            {texto: 'IVA 27%', formato: 'currency'},
            {texto: 'Total IVA', formato: 'currency'},
            {texto: 'Total perc. IIBB', formato: 'currency'},
            {texto: 'Total perc. IVA', formato: 'currency'},
            {texto: 'Importe total', formato: 'currency'}
        ];

        var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();
        var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();

        content = {
            content: {
                title: 'Libro IVA VENTAS ' + $fechaInicio.replace(/\-/g, '_') + '__' + $fechaFin.replace(/\-/g, '_'),
                sheets: {
                    0: {
                        title: 'Libro IVA VENTAS ',
                        tables: {
                            0: {
                                title: 'Libro IVA VENTAS ' + $fechaInicio.replace(/\-/g, '_') + '__' + $fechaFin.replace(/\-/g, '_'),
                                titulo_alternativo: '',
                                data: JSON.stringify(export_data),
                                headers: JSON.stringify(headers)
                            }
                        }
                    }
                }
            }
        };


        open_window('POST', __AJAX_PATH__ + (pdf ? 'libro_iva_ventas/export_pdf' : 'export_excel'), content, '_blank');

        e.stopPropagation();
    });
}