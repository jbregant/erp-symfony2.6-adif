
var index = 0;

var dt_conciliacion_column_index = {
    id: index++,
    multiselect: index++,
    fechaInicio: index++,
    fechaFin: index++,
    fechaCierre: index++,
    cuenta: index++,
    acciones: index
};


$(document).ready(function () {

    initDataTable();

    initFiltroButton();

});

/**
 * 
 * @returns {undefined}
 */
function initDataTable() {
    var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
    var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();
	
    if ( validarRangoFechas(fechaInicio, fechaFin) ) {		
        dt_conciliacion = dt_datatable($('#table-conciliacion'), {
            ajax: {
                url: __AJAX_PATH__ + 'conciliacion/index_table/',
                data: function (d) {
                    d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_conciliacion_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_conciliacion_column_index.acciones,
                    "data": "actions",
                    "render": function (data, type, full, meta) {
                        var full_data = full[dt_conciliacion_column_index.acciones];
                        var strUrl = full_data.show;
                        
                        strUrl = ( strUrl !== undefined ? strUrl.replace("ver", "abrir") : strUrl );
                        
                        return (full_data.show !== undefined ? '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver conciliaci&oacute;n">\n\
								<i class="fa fa-search"></i>\n\
							</a><a href="' + strUrl + '" class="btn btn-xs green tooltips" data-original-title="Abrir conciliaci&oacute;n">\n\
								<i class="fa fa-folder-open"></i>\n\
							</a>' : '')
								+ (full_data.edit !== undefined ? '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar conciliaci&oacute;n">\n\
								<i class="fa fa-pencil"></i>\n\
							</a>' : '');
                    }
                },
                {
                    className: "text-center",
                    targets: [
                        dt_conciliacion_column_index.multiselect
                    ]
                },
                {
                    className: "nowrap",
                    targets: [
                        dt_conciliacion_column_index.fechaInicio,
                        dt_conciliacion_column_index.fechaFin,
                        dt_conciliacion_column_index.fechaCierre
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_conciliacion_column_index.acciones
                }
            ]
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#filtrar').on('click', function (e) {
        var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
        var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();
        
        setFechasFiltro(fechaInicio, fechaFin);
        
        if (validarRangoFechas(fechaInicio, fechaFin)) {
            dt_conciliacion.DataTable().ajax.reload();
        }
    });
}