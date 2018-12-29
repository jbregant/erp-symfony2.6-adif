

/**
 * 
 */
jQuery(document).ready(function () {

    initReporteTitle();

    initFiltro();

    initFiltroButton();

    initActualizarButton();

    initCheckBoxDetalle();

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

    var $tipoReporte = $('#adif_contablebundle_filtro_tipo').val();

    if (validarRangoFechas($fechaInicio, $fechaFin)) {

        if ($fechaInicio && $fechaFin) {

            var data = {
                fechaInicio: $fechaInicio,
                fechaFin: $fechaFin,
                tipoReporte: $tipoReporte
            };

            $.ajax({
                type: "POST",
                data: data,
                url: __AJAX_PATH__ + 'asientocontable/filtrar_librodiario/'
            }).done(function (renglonesAsientoContable) {

                actualizarTabla(renglonesAsientoContable);

                updateCurrencies();

                updateTipoReporteSelect();

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

    $('#libro_diario_table tbody tr').remove();

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

    if ($('#libro_diario_table tbody td').length === 0) {

        hideExportCustom($('#libro_diario_table'));

        $('#libro_diario_table').hide();

        $('.no-result').remove();
        $('.libro_diario_content').append('<span class="no-result">No se encontraron resultados.</span>');
    } else {

        addTotalTD($totalDebe, $totalHaber);

        $('.no-result').remove();
        $('#libro_diario_table').show();

        initExportCustom($('#libro_diario_table'));
    }
}


/**
 * 
 * @param {type} renglonAsientoContable
 * @returns {undefined}
 */
function addAsientoContableTD(renglonAsientoContable) {

    var indiceNuevo = $('#libro_diario_table tbody tr').length + 1;

    $('#libro_diario_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '" class="tr-asiento-encabezado">\n\
                <td class="nowrap text-center bold">' + renglonAsientoContable['fechaContable'] + '</td>\n\\n\
                <td class="nowrap text-center bold hiddenable">' + renglonAsientoContable['fechaCreacion'] + '</td>\n\
				<td class="nowrap text-center bold">' + renglonAsientoContable['idAsientoConFormato'] + '</td>\n\
                <td class="nowrap text-center bold">' + renglonAsientoContable['numeroAsiento'] + '</td>\n\
                <td class="nowrap text-center bold">' + renglonAsientoContable['numeroOriginal'] + '</td>\n\
                <td class="nowrap text-center bold hiddenable">' + renglonAsientoContable['tipoAsientoContable'] + '</td>\n\
                <td colspan="4" class="bold">' + renglonAsientoContable['denominacionAsiento'] + '</td>\n\
            </tr>');
}

/**
 * 
 * @param {type} renglonAsientoContable
 * @returns {undefined}
 */
function addRenglonAsientoContableTD(renglonAsientoContable) {

    var indiceNuevo = $('#libro_diario_table tbody tr').length + 1;

	if (renglonAsientoContable['detalle'] == null) {
		renglonAsientoContable['detalle'] = '-';
	}
	
    $('#libro_diario_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td></td>\n\
                <td class="hiddenable"></td>\n\
                <td></td>\n\
				<td></td>\n\
                <td></td>\n\
                <td class="hiddenable"></td>\n\
                <td>' + renglonAsientoContable['cuentaContable'] + '</td>\n\
                <td class="nowrap text-right currency">' + (renglonAsientoContable['tipoImputacion'] === $tipoOperacionDebe ? renglonAsientoContable['importeMCL'] : '') + '</td>\n\
                <td class="nowrap text-right currency">' + (renglonAsientoContable['tipoImputacion'] === $tipoOperacionHaber ? renglonAsientoContable['importeMCL'] : '') + '</td>\n\
                <td class="text-center optional">' + renglonAsientoContable['detalle'] + '</td>\n\
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

    $('#libro_diario_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td></td>\n\
                <td class="hiddenable"></td>\n\
                <td></td>\n\
                <td></td>\n\
                <td></td>\n\
                <td class="hiddenable"></td>\n\
				<td></td>\n\
                <td class="td-total-debe hlt text-right currency bold">' + $totalDebe + '</td>\n\
                <td class="td-total-haber hlt text-right currency bold">' + $totalHaber + '</td>\n\
                <td class="text-center optional"></td>\n\
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

    updateCaptionTipoReporte();

    $('.reporte_contable_title').show();
}

/**
 * 
 * @returns {undefined}
 */
function updateCaptionTipoReporte() {

    var $tipoReporteSelectVal = $('#adif_contablebundle_filtro_tipo').val();

    if ($tipoReporteSelectVal === $tipoReporteOficial) {
        $('.caption-tipo-vista').text('OFICIAL');
    }
    else {
        $('.caption-tipo-vista').text('INTERNO');
    }
}

/**
 * 
 * @returns {undefined}
 */
function initActualizarButton() {

    $('#adif_contablebundle_filtro_tipo').on('change', function (e) {

        updateTipoReporteSelect();

        updateCaptionTipoReporte();

    });

}

/**
 * 
 * @returns {undefined}
 */
function updateTipoReporteSelect() {

    var $tipoReporteSelectVal = $('#adif_contablebundle_filtro_tipo').val();

    if ($tipoReporteSelectVal === $tipoReporteOficial) {
        $('.hiddenable').hide();
        $('.checkbox-detalle').show();
        updateCheckboxDetalle();
    }
    else {
        $('.hiddenable').show();
        $('.checkbox-detalle').hide();
        $('.optional').show();
    }
}

/**
 * 
 * @returns {undefined}
 */
function initCheckBoxDetalle() {

    $(':checkbox').change(function () {
        updateCheckboxDetalle();
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateCheckboxDetalle() {
    if ($('#checkbox-detalle').is(':checked')) {
        $('.optional').hide();
    }
    else {
        $('.optional').show();
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateCurrencies() {

    $('#libro_diario_table tbody td.currency').each(function () {

        if ($.isNumeric($(this).text())) {

            var $formattedNumber = convertToMoneyFormat($(this).text());

            $(this).text($formattedNumber);
        }
    });

}

/**
 * Overrides de getHeadersTableCustom() del archivo functions.js
 * Muestro todo los TH, no importa si no estan visibles
 * @param {type} table
 * @returns {getHeadersTableCustom.a|Array}
 */
function getHeadersTableCustom(table) {
	console.debug("overrides getHeadersTableCustom()");
    var a = Array();
    $(table).find('thead').find('tr[class=headers] th').not('.ctn_acciones').each(function (e, v) {
        a.push({texto: $(v).text(),
            formato: $(v).attr('export-format') ? $(v).attr('export-format') : 'text'
        });
    });
    return a;
}