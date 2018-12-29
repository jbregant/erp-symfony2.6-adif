
var index = 0;

var dt_movimientoministerial_column_index = {
    id: index++,
    multiselect: index++,
    fecha: index++,
    referencia: index++,
    origen: index++,
    destino: index++,
    monto: index++,
    detalle: index++,
    fechaAnulacion: index++,    
    acciones: index
};

dt_licitacionobra = dt_datatable($('#table-movimientoministerial'), {
    ajax: __AJAX_PATH__ + 'movimientoministerial/index_table/',
    columnDefs: [
        {
            "targets": dt_movimientoministerial_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_movimientoministerial_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_movimientoministerial_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.anular !== undefined ?
                            '<a href="' + full_data.anular + '" class="btn btn-xs red tooltips accion-anular" data-original-title="Anular">\n\
                                <i class="fa fa-times"></i>\n\
                            </a>'
                        : '');                        

            }
        },
        {
            className: "nowrap",
            targets: [
                dt_movimientoministerial_column_index.fecha,
                dt_movimientoministerial_column_index.referencia,
                dt_movimientoministerial_column_index.monto,
                dt_movimientoministerial_column_index.fechaAnulacion                
            ]
        },
        {
            className: "text-right",
            targets: [
                dt_movimientoministerial_column_index.monto
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_movimientoministerial_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_movimientoministerial_column_index.acciones
        }
    ]
});

$(document).ready(function () {

    initEditarFechaAsientoContableHandler();
});


/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    var data = {
        id_movimiento: $('.mensaje-asiento-contable').data('id-movimiento'),
        numero_asiento: $('#numero-asiento').data('numero-asiento'),
        fecha: $('#fecha-asiento').val()
    };

    $.ajax({
        type: "POST",
        data: data,
        url: __AJAX_PATH__ + 'movimientoministerial/editar_fecha/'
    }).done(function (response) {

        return true;
    });

}