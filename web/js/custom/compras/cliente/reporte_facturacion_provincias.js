index = 0;

var dt_table_reporte_column_index = {
    id: index++,
    multiselect: index++,
    Provincia: index++,
    TipoComprobante: index++,
    letra: index++,
    NumeroComprobante: index++,
    Fecha: index++,
    Destinatario: index++,
    Neto: index++,
    Gravado: index++,
    Exento: index++    
};

index = 0;

var dt_table_reporte_sum_column_index = {
    id: index++,
    multiselect: index++,
    Provincia: index++,
    Neto: index++,
    Gravado: index++,
    Exento: index++    
};


$(document).ready(function () {
    initDataTable();
    initFiltroButton();
    
    $('#table-reporte-sum').on('selected_element', function (e, cantidad) {
         filtrarComprobantes();
    });
});

function initDataTable() {
    var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
    var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();
    
    if (validarRangoFechas(fechaInicio, fechaFin)) {
        dt_table_reporte = dt_datatable($('#table-reporte'), {
            ajax: {
                url: __AJAX_PATH__ + 'cliente/index_table_reporte_facturacion_provincias/',
                data: function (d) {
                    d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_table_reporte_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    className: "text-center",
                    targets: [
                        dt_table_reporte_column_index.multiselect
                    ]
                },         
                {
                    className: "nowrap",
                    targets: [
                        dt_table_reporte_column_index.Provincia,
                        dt_table_reporte_column_index.TipoComprobante,
                        dt_table_reporte_column_index.letra,
                        dt_table_reporte_column_index.NumeroComprobante,
                        dt_table_reporte_column_index.Fecha,
                        dt_table_reporte_column_index.Neto,
                        dt_table_reporte_column_index.Gravado,
                        dt_table_reporte_column_index.Exento
                    ]
                }
            ]
        });
        
        dt_table_reporte_sum = dt_datatable($('#table-reporte-sum'), {
            ajax: {
                url: __AJAX_PATH__ + 'cliente/index_table_reporte_facturacion_provincias_sum/',
                data: function (d) {
                    d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_table_reporte_sum_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    className: "text-center",
                    targets: [
                        dt_table_reporte_sum_column_index.multiselect
                    ]
                },         
                {
                    className: "nowrap",
                    targets: [
                        dt_table_reporte_sum_column_index.Provincia,
                        dt_table_reporte_sum_column_index.Neto,
                        dt_table_reporte_sum_column_index.Gravado,
                        dt_table_reporte_sum_column_index.Exento
                    ]
                }
            ]
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initTable() {
    $('.modal-dialog').css('width', '55%');

    $('.modal-footer').find('.btn-default').remove();
    $('.modal-footer').find('.btn-primary').text('Cerrar');

    setMasks();

    var options = {
        "searching": false,
        "ordering": true,
        "info": false,
        "paging": true
    };
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {
    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});
    });
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
        
        dt_table_reporte.DataTable().ajax.reload();
        dt_table_reporte_sum.DataTable().ajax.reload();
    });
}
    
function filtrarComprobantes() {
    $('#table-reporte .btn-clear-filters').click();

    comprobantes_seleccionados = dt_getSelectedRows('#table-reporte-sum');
    
    var filtro_provincia = '';

    for (var index = 0; index < comprobantes_seleccionados.length; index++) {
        filtro_provincia += comprobantes_seleccionados[index][0] + '|';
    }

    if (filtro_provincia.length > 1) {
        filtro_provincia = filtro_provincia.substr(0, filtro_provincia.length - 1);

        $('#table-reporte').DataTable().column(dt_table_reporte_column_index.Provincia).search(filtro_provincia,true, false).draw();
    }
}