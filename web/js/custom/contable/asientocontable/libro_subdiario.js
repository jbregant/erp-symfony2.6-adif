

var _subdiario_select = $('#adif_contablebundle_filtro_subdiario');

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
function initFiltroButton() {

    $('#filtrar_libro_subdiario').on('click', function (e) {

        filtrarLibroMayor();

    });
}

/**
 * 
 * @returns {undefined}
 */
function filtrarLibroMayor() {

    var $subdiario = $('#adif_contablebundle_filtro_subdiario').val();

    var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();

    var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();

    if (validarRangoFechas($fechaInicio, $fechaFin)) {

        if ($subdiario && $fechaInicio && $fechaFin) {

            var data = {
                subdiario: $subdiario,
                fechaInicio: $fechaInicio,
                fechaFin: $fechaFin
            };

            $.ajax({
                type: "POST",
                data: data,
                url: __AJAX_PATH__ + 'asientocontable/filtrar_librosubdiario/'
            }).done(function (renglonesAsientoContable) {

                actualizarTabla(renglonesAsientoContable);

                updateCurrencies();

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

    $('#libro_subdiario_table tbody td').remove();

    var $idAsiento = -1;

    var $nuevoAsiento = false;

    var $totalDebe = 0;
    var $totalHaber = 0;

    jQuery.each(renglonesAsientoContable, function (index, renglonAsientoContable) {

        $nuevoAsiento = $idAsiento != renglonAsientoContable['idAsiento'];

        if ($nuevoAsiento) {

            if (index !== 0) {
                addTotalTD($totalDebe, $totalHaber);
            }

            $idAsiento = renglonAsientoContable['idAsiento'];

            addAsientoContableTD(renglonAsientoContable);

            $totalDebe = $totalHaber = 0;

            $nuevoAsiento = false;
        }

        if (renglonAsientoContable['tipoImputacion'] === $tipoOperacionDebe) {
            $totalDebe += parseFloat(renglonAsientoContable['importeMCL']);
        }
        else if (renglonAsientoContable['tipoImputacion'] === $tipoOperacionHaber) {
            $totalHaber += parseFloat(renglonAsientoContable['importeMCL']);
        }

        addRenglonAsientoContableTD(renglonAsientoContable);
    });

    if ($('#libro_subdiario_table tbody td').length === 0) {

        hideExportCustom($('#libro_subdiario_table'));

        $('#libro_subdiario_table').hide();

        $('.no-result').remove();
        $('.libro_subdiario_content').append('<span class="no-result">No se encontraron resultados.</span>');
    } else {

        addTotalTD($totalDebe, $totalHaber);

        $('.no-result').remove();
        $('#libro_subdiario_table').show();

        initExportCustom($('#libro_subdiario_table'));
    }
}

/**
 * 
 * @param {type} renglonAsientoContable
 * @returns {undefined}
 */
function addAsientoContableTD(renglonAsientoContable) {

    var indiceNuevo = $('#libro_subdiario_table tbody tr').length + 1;

    $('#libro_subdiario_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '" class="tr-asiento-encabezado">\n\
                <td class="nowrap text-center bold">' + renglonAsientoContable['fechaContable'] + '</td>\n\\n\
                <td  class="nowrap text-center bold">' + renglonAsientoContable['numeroAsiento'] + '</td>\n\
                <td colspan="4" class="bold">' + renglonAsientoContable['denominacionAsiento'] + '</td>\n\
            </tr>');
}

/**
 * 
 * @param {type} renglonAsientoContable
 * @returns {undefined}
 */
function addRenglonAsientoContableTD(renglonAsientoContable) {

    var indiceNuevo = $('#libro_subdiario_table tbody tr').length + 1;

    $('#libro_subdiario_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td></td>\n\
                <td></td>\n\\n\
                <td>' + renglonAsientoContable['cuentaContable'] + '</td>\n\
                <td class="text-right currency td-debe">' + (renglonAsientoContable['tipoImputacion'] === $tipoOperacionDebe ? renglonAsientoContable['importeMCL'] : '') + '</td>\n\
                <td class="text-right currency td-haber">' + (renglonAsientoContable['tipoImputacion'] === $tipoOperacionHaber ? renglonAsientoContable['importeMCL'] : '') + '</td>\n\
                <td>' + renglonAsientoContable['detalle'] + '</td>\n\
            </tr>');
}

/**
 * 
 * @param {type} $totalDebe
 * @param {type} $totalHaber
 * @returns {undefined}
 */
function addTotalTD($totalDebe, $totalHaber) {

    var indiceNuevo = $('#libro_diario_table tbody tr').length + 1;

    $('#libro_subdiario_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td></td>\n\
                <td></td>\n\
                <td></td>\n\
                <td class="td-total-debe hlt text-right currency bold">' + $totalDebe + '</td>\n\
                <td class="td-total-haber hlt text-right currency bold">' + $totalHaber + '</td>\n\
                <td class="text-center"></td>\n\
            </tr>');
}


/**
 * 
 * @returns {undefined}
 */
function updateCaptionTitle() {

    var $subdiario = _subdiario_select.find('option:selected').text();

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

    $('.caption-subdiario').text($subdiario);

    $('.caption-fecha-desde').text($fechaInicio);
    $('.caption-fecha-hasta').text($fechaFin);

    $('.reporte_contable_title').show();
}

/**
 * 
 * @returns {undefined}
 */
function updateCurrencies() {

    $('#libro_subdiario_table tbody td.currency').each(function () {

        if ($.isNumeric($(this).text())) {

            var $formattedNumber = convertToCurrencyFormat($(this).text());

            $(this).text($formattedNumber);
        }
    });

}