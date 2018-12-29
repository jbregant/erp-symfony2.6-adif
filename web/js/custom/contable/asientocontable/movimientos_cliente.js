
/**
 * 
 */
jQuery(document).ready(function () {

    initAutocompleteCliente();

    initReporteTitle();

    initFiltro();

    initFiltroButton();
});

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteCliente() {

    $('#adif_contablebundle_filtro_cliente').autocomplete({
        source: __AJAX_PATH__ + 'cliente/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            selectCliente(event, ui);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };
}

/**
 * 
 * @param {type} event
 * @param {type} ui
 * @returns {undefined}
 */
function selectCliente(event, ui) {

    $('#adif_contablebundle_filtro_cliente_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_filtro_cliente_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_filtro_idCliente').val(ui.item.id);
}

/**
 * 
 * @returns {undefined}
 */
function initReporteTitle() {
    $('.reporte_contable_title').hide();
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#btn-filtrar').on('click', function (e) {
        filtrarReporte();
    });
}

/**
 * 
 * @returns {undefined}
 */
function filtrarReporte() {

    var razonSocial = $('#adif_contablebundle_filtro_cliente_razonSocial').val();
    var fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();
    var fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();

    if (validarRangoFechas(fechaInicio, fechaFin)) {

        if (razonSocial && fechaInicio && fechaFin) {

            var data = {
                razonSocial: razonSocial,
                fechaInicio: fechaInicio,
                fechaFin: fechaFin
            };

            $.ajax({
                type: "POST",
                data: data,
                url: __AJAX_PATH__ + 'asientocontable/filtrar_movimientoscliente/'
            }).done(function (renglonesAsientoContable) {

                actualizarTabla(renglonesAsientoContable);

                updateCaptionTitle();
            });
        }
    }
}

/**
 * 
 * @param {type} renglonesAsientoContable
 * @returns {undefined}
 */
function actualizarTabla(renglonesAsientoContable) {

    $('#reporte_table tbody tr').remove();

    jQuery.each(renglonesAsientoContable, function (index, renglonAsientoContable) {
        addRenglonAsientoContableTD(renglonAsientoContable);
    });

    updateTotales();

    updateCurrencies();

    if ($('#reporte_table tbody td').length === 0) {

        hideExportCustom($('#reporte_table'));

        $('#reporte_table').hide();

        $('.no-result').remove();

        $('.reporte_content').append('<span class="no-result">No se encontraron resultados.</span>');
    }
    else {

        $('.no-result').remove();

        $('#reporte_table').show();

        initExportCustom($('#reporte_table'));
    }
}

/**
 * 
 * @param {type} renglonAsientoContable
 * @returns {undefined}
 */
function addRenglonAsientoContableTD(renglonAsientoContable) {

    var indiceNuevo = $('#reporte_table tbody tr').length + 1;

    $('#reporte_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td class="text-center">' + renglonAsientoContable['fechaContable'] + '</td>\n\
                <td>' + renglonAsientoContable['razonSocial'] + '</td>\n\
                <td>' + renglonAsientoContable['numeroDocumento'] + '</td>\n\
                <td class="text-center">' + renglonAsientoContable['numeroAsientoOriginal'] + '</td>\n\
                <td class="text-center">' + renglonAsientoContable['numeroAsiento'] + '</td>\n\
                <td class="text-left">' + renglonAsientoContable['conceptoAsientoContable'] + '</td>\n\
                <td class="text-right currency td-debe">' + (renglonAsientoContable['tipoImputacion'] === $tipoOperacionDebe ? renglonAsientoContable['importeMCL'] : '') + '</td>\n\
                <td class="text-right currency td-haber">' + (renglonAsientoContable['tipoImputacion'] === $tipoOperacionHaber ? renglonAsientoContable['importeMCL'] : '') + '</td>\n\
                <td>' + renglonAsientoContable['titulo'] + '</td>\n\
                <td>' + renglonAsientoContable['detalle'] + '</td>\n\
            </tr>');
}

/**
 * 
 * @returns {undefined}
 */
function updateTotales() {

    // Actualizo el total del Debe
    var $totalDebe = 0;
    var $valorDebe = 0;

    $('.td-debe').each(function () {
        $valorDebe = $(this).text().trim() === '' ? '0' : $(this).text().replace(',', '.');
        $totalDebe += parseFloat($valorDebe);
    });

    var $formattedDebe = convertToMoneyFormat($totalDebe);
    $('.total-debe').text($formattedDebe);

    // Actualizo el total del Haber
    var $totalHaber = 0;
    var $valorHaber = 0;

    $('.td-haber').each(function () {
        $valorHaber = $(this).text().trim() === '' ? '0' : $(this).text().replace(',', '.');
        $totalHaber += parseFloat($valorHaber);
    });

    var $formattedHaber = convertToMoneyFormat($totalHaber);
    $('.total-haber').text($formattedHaber);

}

/**
 * 
 * @returns {undefined}
 */
function updateCaptionTitle() {

    var razonSocial = $('#adif_contablebundle_filtro_cliente_razonSocial').val();
    var cuit = $('#adif_contablebundle_filtro_cliente_cuit').val();

    var cliente = razonSocial + ' - ' + cuit;

    var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
    var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();

    var table_title = cliente + ' - ' + $fechaInicio + ' - ' + $fechaFin;

    $('#reporte_table').attr('dataexport-title-alternativo', table_title);

    $('.caption-cliente').text(cliente);

    $('.caption-fecha-desde').text($fechaInicio);
    $('.caption-fecha-hasta').text($fechaFin);

    $('.reporte_contable_title').show();
}

/**
 * 
 * @returns {undefined}
 */
function updateCurrencies() {

    $('#reporte_table tbody td.currency').each(function () {

        if ($.isNumeric($(this).text())) {

            var $formattedNumber = convertToMoneyFormat($(this).text());

            $(this).text($formattedNumber);
        }
    });

}