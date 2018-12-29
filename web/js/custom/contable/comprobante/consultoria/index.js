
var dt_consultoria_comprobanteconsultoria_column_index = {
    id: 0,
    multiselect: 1,
    fechaComprobante: 2,
    contrato: 3,
    contratoId: 4,
    consultor: 5,
    comprobante: 6,
    numero: 7,
    total: 8,
    anulado: 9,
    acciones: 10
};

dt_consultoria_comprobanteconsultoria = dt_datatable($('#table-consultoria_comprobanteconsultoria'), {
    ajax: __AJAX_PATH__ + 'comprobante_consultoria/index_table/',
    columnDefs: [
        {
            "targets": dt_consultoria_comprobanteconsultoria_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes' + (full[dt_consultoria_comprobanteconsultoria_column_index.anulado] == '1' ? ' anulado' : '') + '" value="' + data + '" />';
            }
        },
        {
            "targets": dt_consultoria_comprobanteconsultoria_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_consultoria_comprobanteconsultoria_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>'
                        + (full_data.anular !== undefined ?
                                '<a href="' + full_data.anular + '" class="btn btn-xs red accion-anular tooltips" data-original-title="Anular">\n\
                            <i class="fa fa-times"></i>\n\
                            </a>' : '');
            }
        },
        {
            className: "hidden",
            targets: [
                dt_consultoria_comprobanteconsultoria_column_index.contratoId,
                dt_consultoria_comprobanteconsultoria_column_index.anulado
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_consultoria_comprobanteconsultoria_column_index.consultor,
                dt_consultoria_comprobanteconsultoria_column_index.comprobante,
                dt_consultoria_comprobanteconsultoria_column_index.numero
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_consultoria_comprobanteconsultoria_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_consultoria_comprobanteconsultoria_column_index.acciones
        }
    ],
    drawCallback: function (settings, json) {
        ocultarAnulados();
    }
});


var dt_anticiposConsultoria_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    tipo: 3,
    tipoId: 4,
    consultor: 5,
    contrato: 6,
    contratoId: 7,
    total: 8,
    anulado: 9,
    acciones: 10
};

dt_anticipoConsultoria = dt_datatable($('#table-consultoria_anticipoconsultoria'), {
    ajax: __AJAX_PATH__ + 'comprobante_consultoria/index_table_anticipos/',
    columnDefs: [
        {
            "targets": dt_anticiposConsultoria_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes' + (full[dt_anticiposConsultoria_column_index.anulado] == '1' ? ' anulado' : '') + '" value="' + data + '" />';
            }
        },
        {
            "targets": dt_anticiposConsultoria_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_anticiposConsultoria_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>'
                        + (full_data.anular !== undefined ?
                                '<a href="' + full_data.anular + '" class="btn btn-xs red accion-anular tooltips" data-original-title="Anular">\n\
                            <i class="fa fa-times"></i>\n\
                            </a>' : '');
            }
        },
        {
            className: "hidden",
            targets: [
                dt_anticiposConsultoria_column_index.contratoId,
                dt_anticiposConsultoria_column_index.tipoId,
                dt_anticiposConsultoria_column_index.anulado
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_anticiposConsultoria_column_index.tipo,
                dt_anticiposConsultoria_column_index.contrato,
                dt_anticiposConsultoria_column_index.total
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_anticiposConsultoria_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_anticiposConsultoria_column_index.acciones
        }
    ],
    drawCallback: function (settings, json) {
        ocultarAnulados();
    }
});


$(document).ready(function () {

    $('#table-consultoria_comprobanteconsultoria').on('selected_element', function (e, cantidad) {

        if (cantidad > 0) {
            $('#generar_autorizacion_contable').removeClass('hidden');
            if (cantidad === 1) {
                $('.plural').addClass('hidden');
            } else {
                $('.plural').removeClass('hidden');
            }
        }
        else {
            $('#generar_autorizacion_contable').addClass('hidden');
        }

        filtrar_anticipos();

        $('#cant_seleccionados').text(cantidad);

        $('#cant_seleccionados').parent().removeClass('flash animated')
                .addClass('flash animated')
                .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    $(this).removeClass('flash animated');
                });
    });

    $('#table-consultoria_anticipoconsultoria').on('selected_element', function (e, cantidad) {

        if (cantidad > 0) {
            if (cantidad === 1) {
                $('.plural_anticipo').addClass('hidden');
            } else {
                $('.plural_anticipo').removeClass('hidden');
            }
        }

        $('#cant_anticipos_seleccionados').text(cantidad);

        $('#cant_anticipos_seleccionados').parent().removeClass('flash animated')
                .addClass('flash animated')
                .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    $(this).removeClass('flash animated');
                });
    });

    initGenerarOrdenPagoHandler();

    initEditarFechaAsientoContableHandler();
});

function initGenerarOrdenPagoHandler() {

    $('#generar_autorizacion_contable').on('click', function (e) {
        e.preventDefault();
        bloquear();

        var ids = [];
        var ids_anticipos = [];

        var total = 0;
        var total_anticipos = 0;

        var valido = true;

        var table = $('#table-consultoria_comprobanteconsultoria');
        var table_anticipos = $('#table-consultoria_anticipoconsultoria');

        ids = dt_getSelectedRowsIds(table);
        ids_anticipos = dt_getSelectedRowsIds(table_anticipos);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un comprobante para generar la autorización contable.'});
            desbloquear();
            return;
        }

        var primero = dt_getSelectedRows($(table))[0][2];

        total += parseFloat(dt_getSelectedRows($(table))[0][6].replace('$ ', '')
                .replace(/\./g, '').replace(',', '.'));

        for (index = 1; index < dt_getSelectedRows($(table)).length; index++) {

            valido &= primero === dt_getSelectedRows($(table))[index][2];

            total += parseFloat(dt_getSelectedRows($(table))[index][6].replace('$ ', '')
                    .replace(/\./g, '').replace(',', '.'));
        }

        if (!valido) {
            show_alert({msg: 'Debe seleccionar comprobantes del mismo contrato.'});
            desbloquear();
            return;
        }

        if (ids_anticipos.length > 0) {

            total_anticipos += parseFloat(dt_getSelectedRows($(table_anticipos))[0][6]
                    .replace('$ ', '').replace(/\./g, '').replace(',', '.'));

            valido &= primero === dt_getSelectedRows($(table_anticipos))[0][5];

            for (index = 1; index < dt_getSelectedRows($(table_anticipos)).length; index++) {

                valido &= primero === dt_getSelectedRows($(table_anticipos))[index][5];

                total_anticipos += parseFloat(dt_getSelectedRows($(table_anticipos))[index][6]
                        .replace('$ ', '').replace(/\./g, '').replace(',', '.'));
            }

            if (!valido) {
                show_alert({msg: 'Debe seleccionar comprobantes y anticipos del mismo contrato.'});
                desbloquear();
                return;
            }

            if (!(total >= total_anticipos)) {
                show_alert({msg: 'El monto de los anticipos no puede superar al de los comprobantes.'});
                desbloquear();
                return;
            }
        }

        var json = {
            'ids': ids.toArray(),
            'ids_anticipos': ids_anticipos.toArray()
        };

        $('#form_generar_autorizacion_contable').addHiddenInputData(json);

        $('#form_generar_autorizacion_contable').submit();
    });
}


function filtrar_anticipos() {
    $('#table-consultoria_anticipoconsultoria .btn-clear-filters').click();
    comprobantes_seleccionados = dt_getSelectedRows('#table-consultoria_comprobanteconsultoria');
    var filtro_contrato = '(';
    for (var index = 0; index < comprobantes_seleccionados.length; index++) {
        filtro_contrato += comprobantes_seleccionados[index][1] + '|';
    }
    if (filtro_contrato.length > 1) {
        filtro_contrato = filtro_contrato.substr(0, filtro_contrato.length - 1);
        filtro_contrato += ')';
        dt_anticipoConsultoria.fnFilter(filtro_contrato, dt_anticiposConsultoria_column_index.contrato, true, true, false);
    }
}

/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    if (typeof $('.mensaje-asiento-contable').data('id-comprobante') !== "undefined") {
        if ($('.mensaje-asiento-contable').data('es-anulacion') === 1) {
            updateFechaAnulacionComprobanteCompraFromAsientoContable();
        } else {
            updateFechaComprobanteFromAsientoContable();        
        }
    }

}


function ocultarAnulados() {
    $('.anulado').parents('tr').addClass('anulado tooltips');
    $('.anulado').parents('tr').attr('data-original-title', 'COMPROBANTE ANULADO');
    $('input.anulado').remove();
}