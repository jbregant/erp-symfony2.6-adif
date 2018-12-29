var _cuenta_contable_select = $('#adif_contablebundle_filtro_cuentaContable');

/**
 * 
 */
jQuery(document).ready(function () {
    initCuentaContableSelect();
    initReporteTitle();
    initFiltro();
    initFiltroButton();
});

/**
 * 
 * @returns {undefined}
 */
function initCuentaContableSelect() {
    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'cuentacontable/lista'
    }).done(function (data) {
        _cuenta_contable_select.append('<option value="">-- Cuenta contable --</option>');

        for (var i = 0, total = data.length; i < total; i++) {
            _cuenta_contable_select
                    .append(
                            '<option value="' + data[i].id + '">'
                            + data[i].codigoCuentaContable + ' - ' + data[i].denominacionCuentaContable
                            + '</option>'
                            );
        }

        _cuenta_contable_select.select2();
    });
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
    $('#filtrar_libro_mayor').on('click', function (e) {
        filtrarLibroMayor();
    });
}

/**
 * 
 * @returns {undefined}
 */
function filtrarLibroMayor() {
    var $cuentaContable = $('#adif_contablebundle_filtro_cuentaContable').val();
    var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();
    var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();
    var $tipoReporte = $('#adif_contablebundle_filtro_tipo').val();

    if (validarRangoFechas($fechaInicio, $fechaFin)) {

        if ($cuentaContable && $fechaInicio && $fechaFin) {

            var data = {
                cuentaContable: $cuentaContable,
                fechaInicio: $fechaInicio,
                fechaFin: $fechaFin,
                tipoReporte: $tipoReporte
            };

            $.ajax({
                type: "POST",
                data: data,
                url: __AJAX_PATH__ + 'asientocontable/filtrar_libromayor/'
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
    $('#libro_mayor_table tbody tr').remove();

    jQuery.each(renglonesAsientoContable, function (index, renglonAsientoContable) {
        addRenglonAsientoContableTD(renglonAsientoContable);
    });

    updateTotales();

    updateCurrencies();

    if ($('#libro_mayor_table tbody td').length === 0) {
        hideExportCustom($('#libro_mayor_table'));
        $('#libro_mayor_table').hide();
        $('.no-result').remove();
        $('.libro_mayor_content').append('<span class="no-result">No se encontraron resultados.</span>');
    } else {
        $('.no-result').remove();
        $('#libro_mayor_table').show();

        initExportCustom($('#libro_mayor_table'));
    }
}


/**
 * 
 * @param {type} renglonAsientoContable
 * @returns {undefined}
 */
function addRenglonAsientoContableTD(renglonAsientoContable) {

    var indiceNuevo = $('#libro_mayor_table tbody tr').length + 1;

    $('#libro_mayor_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td class="text-center">' + renglonAsientoContable['idAsiento'] + '</td>\n\
                <td class="text-center">' + renglonAsientoContable['fechaContable'] + '</td>\n\
                <td class="text-center">' + renglonAsientoContable['numeroAsientoOriginal'] + '</td>\n\
                <td class="text-center">' + renglonAsientoContable['numeroAsiento'] + '</td>\n\
                <td class="text-left">' + renglonAsientoContable['conceptoAsientoContable'] + '</td>\n\
                <td class="text-right currency td-debe">' + (renglonAsientoContable['tipoImputacion'] === $tipoOperacionDebe ? renglonAsientoContable['importeMCL'] : '') + '</td>\n\
                <td class="text-right currency td-haber">' + (renglonAsientoContable['tipoImputacion'] === $tipoOperacionHaber ? renglonAsientoContable['importeMCL'] : '') + '</td>\n\
                <td class="text-right currency td-saldo">' + renglonAsientoContable['saldo'] + '</td>\n\
                <td>' + renglonAsientoContable['titulo'] + '</td>\n\
                <td>' + renglonAsientoContable['razonSocial'] + '</td>\n\
                <td>' + renglonAsientoContable['numeroDocumento'] + '</td>\n\
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


    //Actualizo el saldo del mes anterior
    if ($('.td-debe:first').length) {

        var valorDebe = parseFloat($('.td-debe:first').text().trim() === ''
                ? '0'
                : $('.td-debe:first').text().replace(',', '.'));

        var valorHaber = parseFloat($('.td-haber:first').text().trim() === ''
                ? '0'
                : $('.td-haber:first').text().replace(',', '.'));

        var valorSaldo = parseFloat($('.td-saldo:first').text().trim() === ''
                ? '0'
                : $('.td-saldo:first').text().replace(',', '.'));

        if (valorDebe > 0) {
            valorSaldo -= valorDebe;
        }
        if (valorHaber > 0) {
            valorSaldo += valorHaber;
        }
        $('.caption-saldo-anterior').text(valorSaldo.toFixed(2).toString().replace('.', ','));
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateCaptionTitle() {
    var $cuentaContable = _cuenta_contable_select.find('option:selected').text();

    var table_title = $cuentaContable;

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

    table_title += ' - ' + $fechaInicio + ' - ' + $fechaFin;

    $('#libro_mayor_table').attr('dataexport-title-alternativo', table_title);

    $('.caption-cuenta-contable').text($cuentaContable);

    $('.caption-fecha-desde').text($fechaInicio);
    $('.caption-fecha-hasta').text($fechaFin);

    $('.reporte_contable_title').show();
}

/**
 * 
 * @returns {undefined}
 */
function updateCurrencies() {

    $('#libro_mayor_table tbody td.currency').each(function () {

        if ($.isNumeric($(this).text())) {

            var $formattedNumber = convertToMoneyFormat($(this).text());

            $(this).text($formattedNumber);
        }
    });

}