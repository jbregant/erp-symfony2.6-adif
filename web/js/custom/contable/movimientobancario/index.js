
var index = 0;

var dt_movimientobancario_column_index = {
    id: index++,
    multiselect: index++,
    cuentaOrigen: index++,
    cuentaDestino: index++,
    fecha: index++,
    monto: index++,
    detalle: index++,
    acciones: index
};

dt_licitacionobra = dt_datatable($('#table-movimientobancario'), {
    ajax: __AJAX_PATH__ + 'movimientobancario/index_table/',
    columnDefs: [
        {
            "targets": dt_movimientobancario_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_movimientobancario_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_movimientobancario_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>' +
                        '<a href="' + full_data.anular + '" class="btn btn-xs red tooltips accion-anular" data-original-title="Anular">\n\
                            <i class="fa fa-times"></i>\n\
                        </a>';
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_movimientobancario_column_index.cuentaOrigen,
                dt_movimientobancario_column_index.cuentaDestino,
                dt_movimientobancario_column_index.fecha,
                dt_movimientobancario_column_index.monto
            ]
        },
        {
            className: "text-right",
            targets: [
                dt_movimientobancario_column_index.monto
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_movimientobancario_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_movimientobancario_column_index.acciones
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
        url: __AJAX_PATH__ + 'movimientobancario/editar_fecha/'
    }).done(function (response) {

        return true;
    });

}