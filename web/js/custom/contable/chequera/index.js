
var index = 0;

var dt_chequera_column_index = {
    id: index++,
    multiselect: index++,
    banco: index++,
    cbu: index++,
    responsable: index++,
    numeroSerie: index++,
    numeroInicial: index++,
    numeroFinal: index++,
    numeroSiguiente: index++,
    estadoChequera: index++,
    acciones: index
};

$(document).ready(function () {
    initTable();
});

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    dt_chequera = dt_datatable($('#table-chequera'), {
        ajax: __AJAX_PATH__ + 'chequera/index_table/',
        columnDefs: [
            {
                "targets": dt_chequera_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                }
            },
            {
                "targets": dt_chequera_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_chequera_column_index.acciones];

                    return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>'
                            + (full_data.delete !== undefined
                                    ? '<a href="' + full_data.delete + '" class="btn btn-xs red tooltips accion-borrar" data-original-title="Eliminar">\n\
                                            <i class="fa fa-trash"></i>\n\
                                        </a>'
                                    : '');
                }
            },
            {
                className: "text-center",
                targets: [
                    dt_chequera_column_index.multiselect
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_chequera_column_index.acciones
            }
        ],
        "fnDrawCallback": function () {
            initBorrarButton();
        }
    });
}