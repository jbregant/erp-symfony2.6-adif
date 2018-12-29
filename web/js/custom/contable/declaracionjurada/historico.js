
var dt_ddjj_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    periodo: 3,
    nroOP: 4,
    pago: 5,
    importe: 6,
    acciones: 7
};

$(document).ready(function () {

    initTable();

    initShowDetalleDeclaracionJuradaLink();
});

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    columns_ddjj = [
        {
            "targets": dt_ddjj_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_ddjj_column_index.multiselect];

                return '<input type="checkbox" class="checkboxes" value="' + full_data.id + '" />';
            }
        },
        {
            "targets": dt_ddjj_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {

                var full_data = full[dt_ddjj_column_index.acciones];

                return '<a href="' + full_data.show + '" data-id-ddjj="' + full_data.id + '" class="btn btn-xs blue tooltips detalle_ddjj_link" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.descargar !== undefined ?
                                '<a href="' + full_data.descargar + '" class="btn btn-xs grey-cascade tooltips" data-original-title="Descargar archivo">\n\
                            <i class="fa fa-download"></i>\n\
                        </a>' : '');
            }
        },
        {
            className: "text-center",
            targets: [
                dt_ddjj_column_index.multiselect
            ]
        },
        {
            className: "text-center nowrap",
            targets: [
                dt_ddjj_column_index.fecha,
                dt_ddjj_column_index.periodo
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_ddjj_column_index.nroOP,
                dt_ddjj_column_index.pago
            ]
        },
        {
            className: "ctn_acciones nowrap text-center",
            targets: dt_ddjj_column_index.acciones
        }
    ];

    dt_ddjj_sicore = dt_datatable($('#table-ddjj-sicore'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/historico/index_table',
            data: {
                tipoDDJJ: tipoSicore
            }
        },
        columnDefs: columns_ddjj
    });

    dt_ddjj_sijp = dt_datatable($('#table-ddjj-sijp'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/historico/index_table',
            data: {
                tipoDDJJ: tipoSijp
            }
        },
        columnDefs: columns_ddjj
    });

    dt_ddjj_iibb = dt_datatable($('#table-ddjj-iibb'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/historico/index_table',
            data: {
                tipoDDJJ: tipoIIBB
            }
        },
        columnDefs: columns_ddjj
    });

    dt_ddjj_sicoss = dt_datatable($('#table-ddjj-sicoss'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/historico/index_table',
            data: {
                tipoDDJJ: tipoSicoss
            }
        },
        columnDefs: columns_ddjj
    });
}


/**
 * 
 * @returns {undefined}
 */
function initShowDetalleDeclaracionJuradaLink() {

    $(document).on('click', '.detalle_ddjj_link', function (e) {

        e.preventDefault();

        bloquear();

        idDeclaracionJurada = $(this).data('id-ddjj');

        var ajaxDialog = $.ajax({
            type: 'POST',
            data: {
                id: idDeclaracionJurada
            },
            url: __AJAX_PATH__ + 'declaracion_jurada/form_detalle/'
        });

        $.when(ajaxDialog).done(function (dataDialog) {

            var formulario = dataDialog;

            show_dialog({
                titulo: 'Detalle de declaración jurada',
                contenido: formulario,
                labelCancel: 'Aceptar',
                callbackCancel: function () {
                    desbloquear();
                    return;
                },
                callbackSuccess: function () {

                    var formulario_result = formulario.form();

                    if (formulario_result) {

                        bloquear();

                    } else {

                        desbloquear();

                        return false;
                    }
                }

            });

            $('.modal-footer').find('.success').remove();

            $('.modal-dialog').css('width', '80%');

            dt_init($('.table-retenciones'));
            dt_init($('.table-pagos-a-cuenta'));

        });
    });
}