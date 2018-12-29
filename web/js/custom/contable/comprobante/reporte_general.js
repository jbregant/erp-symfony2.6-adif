
var reporteDataTable;

/**
 * 
 */
jQuery(document).ready(function () {

    initFiltroButton();

});

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#filtrar_reporte').on('click', function (e) {
        
        var terminoBusqueda = $('#adif_contablebundle_filtro_termino_busqueda').val();
        var fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
        var fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
        var fechaRadio = $('#adif_contablebundle_filtro_fechaRadio_txt').val();

        var $fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val().trim();
        var $fechaFin = $('#adif_contablebundle_filtro_fechaFin').val().trim();
       
        setFechasFiltro($fechaInicio, $fechaFin);
        
        if (validarRangoFechas($fechaInicio, $fechaFin)) {
            if ($fechaInicio && $fechaFin) {
                
                if (terminoBusqueda === '') {

                    e.preventDefault();

                    show_confirm({
                        msg: 'Est&aacute; a punto de listar todos los comprobantes. Esta acci&oacute;n puede tardar más de lo normal. ¿Desea continuar?',
                        callbackOK: function () {
                            filtrarReporte(terminoBusqueda, fechaInicio, fechaFin, fechaRadio);
                        }
                    });

                } else {
                    filtrarReporte(terminoBusqueda, fechaInicio, fechaFin, fechaRadio);            
                }
            } else {
                show_alert({
                    title: 'Error en las fechas',
                    msg: 'Ingrese la fecha de inicio y la de fin para filtrar la tabla',
                    type: 'error'
                });
            }
        }

    });
}

/**
 * 
 * @param {type} terminoBusqueda
 * @returns {undefined}
 */
function filtrarReporte(terminoBusqueda, fechaInicio, fechaFin, fechaRadio) {

    var data = {
        termino_busqueda: terminoBusqueda,
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        fechaRadio: fechaRadio
    };
    
  
    $.ajax({
        type: "POST",
        data: data,
        url: __AJAX_PATH__ + 'comprobantes/filtrar_reporte_general/'
    }).done(function (comprobantes) {

        if (!$.fn.DataTable.isDataTable($('#reporte_table'))) {

            initExportCustom($('#reporte_table'));

            reporteDataTable = dt_init($('#reporte_table'));
        }

        actualizarTabla(comprobantes);

        setMasks();

        updateCaptionTitle();
    });
       
}

/**
 * 
 * @param {type} comprobantes
 * @returns {undefined}
 */
function actualizarTabla(comprobantes) {

    $('#reporte_table').DataTable().rows().remove().draw();

    $('#reporte_table tbody tr').remove();

    jQuery.each(comprobantes, function (index, comprobante) {
        addTD(comprobante);
    });

    $('#reporte_table').DataTable().draw();

    initExport();

}

/**
 * 
 * @param {type} comprobante
 * @returns {undefined}
 */
function addTD(comprobante) {

    var indiceNuevo = $('#reporte_table tbody tr').length + 1;
    var $descripcionCbte = (comprobante['descripcionComprobante']?comprobante['descripcionComprobante']:"");

    var jRow = $('<tr ' + ((comprobante['comprobanteAnulado']) ? 'class="anulado tooltips" data-original-title="COMPROBANTE ANULADO"' : '') + 'tr_index="' + indiceNuevo + '">\n\
                <td class="nowrap">' + comprobante['fechaCreacion'] + '</td>\n\
                <td class="nowrap">' + comprobante['beneficiario'] + '</td>\n\
                <td class="nowrap">' + comprobante['cuit'] + '</td>\n\
                <td class="nowrap">' + comprobante['nombreCbte'] + (comprobante['comprobanteAnulado'] ? ' (ANULADO)' : '') + '</td>\n\
                <td class="nowrap">' + comprobante['letra'] + '</td>\n\
                <td class="nowrap">' + comprobante['nroCbte'] + '</td>\n\
		<td width="300" nowrap>' + $descripcionCbte + '</td>\n\
                <td class="nowrap">' + comprobante['fechaComprobante'] + '</td>\n\
                <td class="nowrap">' + comprobante['idAsientoContableComprobante'] + '</td>\n\
                <td class="nowrap">' + comprobante['fechaIngresoADIF'] + '</td>\n\\n\
                <td class="nowrap">' + comprobante['fechaVencimiento'] + '</td>\n\
                <td class="nowrap">' + comprobante['numeroReferencia'] + '</td>\n\
                <td class="nowrap money-format">' + comprobante['importeTotal'] + '</td>\n\
                <td class="nowrap">' + comprobante['modulo'] + '</td>\n\
                <td class="nowrap">' + comprobante['ordenCompra'] + '</td>\n\
                <td class="nowrap">' + comprobante['ordenPago'] + '</td>\n\
                <td class="nowrap">' + comprobante['fechaOrdenPago'] + '</td>\n\
                <td class="nowrap">' + comprobante['idAsientoOP'] + '</td>\n\
                <td class="nowrap">' + comprobante['pago'] + '</td>\n\
                <td class="nowrap">' + comprobante['fechaContable'] + '</td>\n\
                <td class="nowrap">' + comprobante['usuarioName'] + '</td>\n\
                <td class="ctn_acciones text-center nowrap">\n\
                    <a href="' + comprobante['showPath'] + '" target="_blank" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                        <i class="fa fa-search"></i>\n\
                    </a>\n\
                </td>\n\
            </tr>');

    $('#reporte_table').DataTable().row.add(jRow);
}

/**
 * 
 * @returns {undefined}
 */
function updateCaptionTitle() {

    var $terminoBusqueda = $('#adif_contablebundle_filtro_termino_busqueda').val();

    $('.caption-termino-busqueda').text($terminoBusqueda);

    $('.reporte_title').show();
}

/**
 * 
 * @returns {undefined}
 */
function initExport() {

    if ($('#reporte_table tbody td').length === 0) {

        $('.table-responsive').hide();
        $('#reporte_table').hide();
        $('.table-toolbar').hide();

        $('.no-result').remove();

        $('.reporte_content').append('<span class="no-result">No se encontraron resultados.</span>');
    }
    else {

        $('.no-result').remove();

        $('.table-responsive').show();
        $('#reporte_table').show();
        $('.table-toolbar').show();
    }

}

/**
 * 
 * @returns {undefined}
 */
function removerResultados() {

}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
    });
}
