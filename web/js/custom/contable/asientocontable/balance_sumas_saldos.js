

/**
 * 
 */
jQuery(document).ready(function () {

    initTable();

    initReporteTitle();

    initFiltro();

    initFiltroButton();

    initCentroCostoCheckboxHandler();

    initCheckChecked();

});

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    dt_init($('#balance_table'), {'ordering': false});
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
function customInitFiltro() {

    var fechaInicioEjercicioUsuarioDate = getDateFromString('01/01/' + ejercicioContableSesion);

    var fechaFinEjercicioUsuarioDate = getDateFromString('31/12/' + ejercicioContableSesion);

    $('#adif_contablebundle_filtro_fechaInicio').datepicker('remove');

    initDatepicker($('#adif_contablebundle_filtro_fechaInicio'), {
        viewMode: "months",
        minViewMode: "months"
    });

    $('#adif_contablebundle_filtro_fechaInicio').datepicker('setStartDate', fechaInicioEjercicioUsuarioDate);

    $('#adif_contablebundle_filtro_fechaInicio').datepicker('setEndDate', fechaFinEjercicioUsuarioDate);
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#filtrar_balance').on('click', function (e) {
        filtrarBalance();
    });
}

/**
 * 
 * @returns {undefined}
 */
function filtrarBalance() {

    var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();

    var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();

    if (validarRangoFechas($fechaInicio, $fechaFin)) {

        var $soloCuentasConSaldo = $('#checkbox-saldo').is(':checked');
        var $incluirAsientosFormales = $('#checkbox-asientos-formales').is(':checked');

        if ($fechaInicio && $fechaFin) {

            var data = {
                fechaInicio: $fechaInicio,
                fechaFin: $fechaFin,
                soloCuentasConSaldo: ($soloCuentasConSaldo) ? 1 : 0,
                incluirAsientosFormales: ($incluirAsientosFormales) ? 1 : 0
            };

            $.ajax({
                type: "POST",
                data: data,
                url: __AJAX_PATH__ + 'asientocontable/filtrar_balancesumassaldos/'
            }).done(function (renglonesAsientoContable) {

                actualizarTabla(renglonesAsientoContable);

                updateCurrencies();

                updateCaptionTitle();

                checkCentroCostoCheckbox();
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

    $('#balance_table').DataTable().rows().remove().draw();

    $('#balance_table tbody tr').remove();

    jQuery.each(renglonesAsientoContable, function (index, renglonAsientoContable) {

        addRenglonAsientoContableTD(renglonAsientoContable);
    });

    $('#balance_table').DataTable().draw();

    if (dt_getRows($('#balance_table')).length === 0) {

        $('#balance_table').hide();
        $('#balance_table_wrapper').hide();

        hideExportCustom($('#balance_table'));

        $('.no-result').remove();
        $('.balance_content').append('<span class="no-result">No se encontraron resultados.</span>');
    } else {
        $('.no-result').remove();
        $('#balance_table').show();
        $('#balance_table_wrapper').show();

        initExportCustom($('#balance_table'));
    }
}

/**
 * 
 * @param {type} renglonAsientoContable
 * @returns {undefined}
 */
function addRenglonAsientoContableTD(renglonAsientoContable) {

    var indiceNuevo = $('#balance_table tbody tr').length + 1;

    var jRow = $('<tr tr_index="' + indiceNuevo + '">\n\
                <td>' + renglonAsientoContable['cuentaContable'] + '</td>\n\
                <td class="text-right currency bold">' + renglonAsientoContable['saldoMesAnterior'] + '</td>\n\
                <td class="text-right currency">' + renglonAsientoContable['totalDebe'] + '</td>\n\
                <td class="text-right currency">' + renglonAsientoContable['totalHaber'] + '</td>\n\
                <td class="text-right currency bold">' + renglonAsientoContable['saldo'] + '</td>\n\
            </tr>');

    $('#balance_table').DataTable().row.add(jRow);
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
    }
    else {
        $('.caption-ejercicio').text($ejercicioInicio + ' - ' + $ejercicioFin);
    }

    var tableTitle = 'Balance de sumas y saldos - Desde '
            + $fechaInicio + ' hasta ' + $fechaFin
            + ' - Emisión: ' + getCurrentDate();

    $('#balance_table').attr('dataexport-title-alternativo', tableTitle);

    $('.caption-fecha-desde').text($fechaInicio);
    $('.caption-fecha-hasta').text($fechaFin);

    $('.reporte_contable_title').show();
}

/**
 * 
 * @returns {undefined}
 */
function updateCurrencies() {

    $('#balance_table').DataTable().rows().nodes().to$().each(function () {

        $(this).find('td.currency').each(function () {

            if ($.isNumeric($(this).text())) {

                var $formattedNumber = convertToMoneyFormat($(this).text());

                $(this).text($formattedNumber);
            }

        });
    });

}

/**
 * 
 * @returns {undefined}
 */
function initCentroCostoCheckboxHandler() {
/*
    $('#checkbox-centro-costo').on('change', function () {
        checkCentroCostoCheckbox();
    });
*/

}

/**
 * 
 * @returns {undefined}
 */
function checkCentroCostoCheckbox() {
/*
    if ($('#checkbox-centro-costo').is(':checked')) {
        $('.centro-costo').removeClass('no-export').show();
    }
    else {
        $('.centro-costo').addClass('no-export').hide();
    }
*/
}