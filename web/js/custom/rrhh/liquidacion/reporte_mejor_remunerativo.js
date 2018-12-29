var reporteDataTable;
var _rows;

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
        
        var semestre = $('#adif_rrhhbundle_filtro_semestre').val();
        var anio = $('#adif_rrhhbundle_filtro_anio').val();
         
        filtrarReporte(semestre, anio);    

    });
}

/**
 * 
 * @param {type} terminoBusqueda
 * @returns {undefined}
 */
function filtrarReporte(semestre, anio) {

    var data = {
        semestre: semestre,
        anio: anio
    };

    $.ajax({
        type: "POST",
        data: data,
        url: __AJAX_PATH__ + 'liquidaciones/reporte/get_mejor_remunerativo'
    }).done(function (resultado) {

        if (!$.fn.DataTable.isDataTable($('#reporte_table'))) {

            //initExportCustom($('#reporte_table'));
            var options = {
                "columnDefs": [
                                {
                                    "targets": 4,
                                    "render": function (data, type, full, meta) {
                                        //console.debug(meta);
                                        _rows = meta;
                                        return data;
                                    }
                                }
                ],
                "order": [ 2, 'asc' ],
                "stateSave": false
            };
            reporteDataTable = dt_init($('#reporte_table'), options);
        }

        actualizarTabla(resultado);

        setMasks();

        //updateCaptionTitle();
    });
       
}

/**
 * 
 * @param {type} resultado
 * @returns {undefined}
 */
function actualizarTabla(resultado) {

    $('#reporte_table').DataTable().rows().remove().draw();

    $('#reporte_table tbody tr').remove();

    jQuery.each(resultado, function (index, item) {
        addTD(item);
    });

    $('#reporte_table').DataTable().draw();

    initExport();

}

/**
 * 
 * @param {type} item
 * @returns {undefined}
 */
function addTD(item) {

    var jRow = $('<tr><td class="nowrap">' + item['nro_legajo'] + '</td>\n\
                <td class="nowrap">' + item['cuil'] + '</td>\n\
                <td class="nowrap">' + item['apellido'] + '</td>\n\
                <td class="nowrap">' + item['nombre'] + '</td>\n\
                <td class="nowrap money-format">' + item['monto_basico'] + '</td>\n\
                <td class="nowrap money-format">' + item['mejor_remuneracion'] + '</td>\n\
                <td class="nowrap money-format">' + item['mejor_remuneracion_final'] + '</td>\n\
                <td class="nowrap">' + item['dias_calculo_sac'] + '</td>\n\\n\\n\
                <td class="ctn_acciones text-center nowrap">\n\
                    <a href="' + item['path_empleado'] + '" target="_blank" class="btn btn-xs blue tooltips" data-original-title="Ver detalle empleado">\n\
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
function initExport() {

    if ($('#reporte_table tbody td').length === 0) {

        $('.table-responsive').hide();
        $('#reporte_table').hide();
        $('.table-toolbar').hide();

        //$('.no-result').remove();

        $('.reporte_content').append('<span class="no-result">No se encontraron resultados.</span>');
    }
    else {

        //$('.no-result').remove();

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

/*
    $('.money-format').each(function () {
        $(this).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
    });
*/
    $(_rows.settings.aoData).each(function() {
        //console.debug($(this));
        var td_basico = this.anCells[4];
        var td_mejor_remuneracion = this.anCells[5];
        var td_mejor_remuneracion_final = this.anCells[6];
        $(td_basico).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
        $(td_mejor_remuneracion).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
        $(td_mejor_remuneracion_final).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
    });
}


