

/**
 * 
 */
jQuery(document).ready(function () {

    initReporteTitle();

    initFiltro();

    initFiltroButton();

});

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

    $('#adif_contablebundle_filtro_subdiarioAsientoContable').select2();

    filtrarLibroDiario();

}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#filtrar_libro_diario').on('click', function (e) {

        filtrarLibroDiario();

    });
}

/**
 * 
 * @returns {undefined}
 */
function filtrarLibroDiario() {

    var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();

    var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();

    var $subdiarios = $('#adif_contablebundle_filtro_subdiarioAsientoContable').val();

    if (validarRangoFechas($fechaInicio, $fechaFin)) {

        if ($fechaInicio && $fechaFin) {

            var data = {
                fechaInicio: $fechaInicio,
                fechaFin: $fechaFin,
                subdiarios: $subdiarios
            };

            $.ajax({
                type: "POST",
                data: data,
                url: __AJAX_PATH__ + 'asientocontable/filtrar_resumenlibrodiario/'
            }).done(function (resumenDiario) {

                actualizarTabla(resumenDiario);

                updateCurrencies();

                updateCaptionTitle();
            });
        }
    }
}

/**
 * 
 * @param {type} resumenDiario
 * @returns {undefined}
 */
function actualizarTabla(resumenDiario) {

    $('#resumen_libro_diario_table tbody tr').remove();

    var $subdiario = -1;

    var $nuevoSubdiario = false;

    jQuery.each(resumenDiario, function (index, diario) {

        $nuevoSubdiario = $subdiario != diario['subdiarioAsientoContable'];

        if ($nuevoSubdiario) {

            $subdiario = diario['subdiarioAsientoContable'];

            addSubdiario(diario);

            $nuevoSubdiario = false;
        }

        addDetalleDiario(diario);

    });

    if ($('#resumen_libro_diario_table tbody td').length === 0) {

        hideExportCustom($('#resumen_libro_diario_table'));

        $('#resumen_libro_diario_table').hide();

        $('.no-result').remove();
        $('.libro_diario_content').append('<span class="no-result">No se encontraron resultados.</span>');
    } else {
        $('.no-result').remove();
        $('#resumen_libro_diario_table').show();

        initExportCustom($('#resumen_libro_diario_table'));
    }
}

/**
 * 
 * @param {type} diario
 * @returns {undefined}
 */
function addSubdiario(diario) {

    var indiceNuevo = $('#resumen_libro_diario_table tbody tr').length + 1;

    $('#resumen_libro_diario_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '" class="tr-asiento-encabezado">\n\
                <td class="text-center bold">' + diario['subdiarioAsientoContable'] + '</td>\n\
                <td class="text-center bold"></td>\n\
                <td class="text-center bold"></td>\n\
                <td class="text-center bold"></td>\n\
            </tr>');
}

/**
 * 
 * @param {type} diario
 * @returns {undefined}
 */
function addDetalleDiario(diario) {

    var indiceNuevo = $('#resumen_libro_diario_table tbody tr').length + 1;

    if (diario['debe'] > diario['haber']) {
        debe = diario['debe'] - diario['haber'];
        haber = 0;
    } else {
        debe = 0;
        haber = diario['haber'] - diario['debe'];
    }

    $('#resumen_libro_diario_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td></td>\n\
                <td class="nowrap">' + diario['cuentaContable'] + '</td>\n\
                <td class="text-right nowrap currency">' + debe + '</td>\n\
                <td class="text-right nowrap currency">' + haber + '</td>\n\
            </tr>');

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

    $('.caption-fecha-desde').text($fechaInicio);
    $('.caption-fecha-hasta').text($fechaFin);

    $('.reporte_contable_title').show();
}

/**
 * 
 * @returns {undefined}
 */
function updateCurrencies() {

    $('#resumen_libro_diario_table tbody td.currency').each(function () {

        if ($.isNumeric($(this).text())) {

            var $formattedNumber = convertToMoneyFormat($(this).text());

            $(this).text($formattedNumber);
        }
    });

}