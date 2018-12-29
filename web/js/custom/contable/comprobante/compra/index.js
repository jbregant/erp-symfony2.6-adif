var index = 0;

var dt_comprobantesCompra_column_index = {
    id: index++,
    multiselect: index++,
    fechaComprobante: index++,
    comprobante: index++,
    numero: index++,
    proveedor: index++,
    orden_compra: index++,
    proveedor_id: index++,
    total: index++,
    pendiente: index++,
    anulado: index++,
    acciones: index++
};

dt_comprobanteCompra = dt_datatable($('#table-comprobantecompra'), {
    ajax: __AJAX_PATH__ + 'comprobantescompra/index_table/',
    columnDefs: [
        {
            "targets": dt_comprobantesCompra_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes' + (full[dt_comprobantesCompra_column_index.anulado] == '1' ? ' anulado' : '') + '" value="' + data + '" />';
            }
        },
        {
            "targets": dt_comprobantesCompra_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {

                var full_data = full[dt_comprobantesCompra_column_index.acciones];

                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>'
                        + (full_data.print_devengado !== undefined ?
                                '<a href="' + full_data.print_devengado + '" \n\
                                    class="btn btn-xs purple-wisteria tooltips" \n\
                                    data-original-title="Imprimir devengado"> \n\
                                    <i class="fa fa-file-powerpoint-o"></i> \n\
                                </a>' : '')
                        + (full_data.anular !== undefined ?
                                '<a href="' + full_data.anular + '" class="btn btn-xs red accion-anular tooltips" data-original-title="Anular">\n\
                                    <i class="fa fa-times"></i>\n\
                                </a>' : '');
            }
        },
        {
            className: "hidden",
            targets: [
                dt_comprobantesCompra_column_index.proveedor_id,
                dt_comprobantesCompra_column_index.anulado
            ]
        },
        {
            className: "td-orden-compra",
            targets: [
                dt_comprobantesCompra_column_index.orden_compra
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_comprobantesCompra_column_index.fechaComprobante,
                dt_comprobantesCompra_column_index.comprobante,
                dt_comprobantesCompra_column_index.numero,
                dt_comprobantesCompra_column_index.orden_compra,
                dt_comprobantesCompra_column_index.total,
                dt_comprobantesCompra_column_index.pendiente
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_comprobantesCompra_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_comprobantesCompra_column_index.acciones
        }
    ],
    drawCallback: function (settings, json) {
        ocultarAnulados();
    }
});

var index = 0;

var dt_creditosCompra_column_index = {
    id: index++,
    multiselect: index++,
    fecha: index++,
    tipo: index++,
    tipoId: index++,
    proveedor: index++,
    oc: index++,
    proveedor_id: index++,
    total: index++,
    anulado: index++,
    acciones: index++,
    utilizada: index++
};

dt_creditosCompra = dt_datatable($('#table-comprobante-credito'), {
    ajax: __AJAX_PATH__ + 'comprobantescompra/index_table_comprobante_credito/',
    columnDefs: [
        {
            "targets": dt_creditosCompra_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes' + (full[dt_creditosCompra_column_index.anulado] == '1' ? ' anulado' : '') + (full[dt_creditosCompra_column_index.utilizada] == '1' ? ' utilizada' : '') + '" value="' + data + '" />';
            }
        },
        {
            "targets": dt_creditosCompra_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_creditosCompra_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                                <i class="fa fa-search"></i>\n\
                                </a>' +
                        (full_data.anular !== undefined ?
                                '<a href="' + full_data.anular + '" class="btn btn-xs red accion-anular tooltips" data-original-title="Anular">\n\
                                <i class="fa fa-times"></i>\n\
                                </a>'
                                : '');
            }
        },
        {
            className: "hidden",
            targets: [
                dt_creditosCompra_column_index.tipoId,
                dt_creditosCompra_column_index.proveedor_id,
                dt_creditosCompra_column_index.anulado
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_creditosCompra_column_index.tipo,
                dt_creditosCompra_column_index.oc,
                dt_creditosCompra_column_index.total
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_creditosCompra_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: [
                dt_creditosCompra_column_index.acciones
            ]
        }
    ],
    drawCallback: function (settings, json) {
        ocultarAnulados();
        ocultarUtilizados();
    }
});


$(document).ready(function () {

    $('#table-comprobantecompra').on('selected_element', function (e, cantidad) {

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

        $('#cant_seleccionados').text(cantidad);

        $('#cant_seleccionados').parent().removeClass('flash animated')
                .addClass('flash animated')
                .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    $(this).removeClass('flash animated');
                });

        filtrarComprobantesCredito();
    });

    $('#table-comprobante-credito').on('selected_element', function (e, cantidad) {
        if (cantidad > 0) {
            if (cantidad === 1) {
                $('.plural_comprobante_credito').addClass('hidden');
            } else {
                $('.plural_comprobante_credito').removeClass('hidden');
            }
        }

        $('#cant_comprobante_credito_seleccionados').text(cantidad);

        $('#cant_comprobante_credito_seleccionados').parent().removeClass('flash animated')
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
        var ids_comprobante_credito = [];

        var total = 0;
        var totalComprobanteCredito = 0;

        var valido = true;

        var table = $('#table-comprobantecompra');
        var table_comprobante_credito = $('#table-comprobante-credito');

        ids = dt_getSelectedRowsIds(table);
        ids_comprobante_credito = dt_getSelectedRowsIds(table_comprobante_credito);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un comprobante para generar la autorización contable.'});
            desbloquear();
            return;
        }

        var primero = dt_getSelectedRows($(table))[0][5];

        total += parseFloat(dt_getSelectedRows($(table))[0][7].replace('$ ', '')
                .replace(/\./g, '').replace(',', '.'));

        for (index = 1; index < dt_getSelectedRows($(table)).length; index++) {

            valido &= primero === dt_getSelectedRows($(table))[index][5];

            total += parseFloat(dt_getSelectedRows($(table))[index][7].replace('$ ', '')
                    .replace(/\./g, '').replace(',', '.'));
        }

        if (!valido) {
            show_alert({msg: 'Debe seleccionar comprobantes del mismo proveedor.'});
            desbloquear();
            return;
        }

        if (ids_comprobante_credito.length > 0) {

            valido &= primero === dt_getSelectedRows($(table_comprobante_credito))[0][5];

            totalComprobanteCredito += parseFloat(dt_getSelectedRows($(table_comprobante_credito))[0][6]
                    .replace('$ ', '').replace(/\./g, '').replace(',', '.'));

            for (index = 1; index < dt_getSelectedRows($(table_comprobante_credito)).length; index++) {

                valido &= primero === dt_getSelectedRows($(table_comprobante_credito))[index][5];

                totalComprobanteCredito += parseFloat(dt_getSelectedRows($(table_comprobante_credito))[index][6]
                        .replace('$ ', '').replace(/\./g, '').replace(',', '.'));
            }

            if (!valido) {
                show_alert({msg: 'Debe seleccionar comprobantes y cr&eacute;ditos del mismo proveedor.'});
                desbloquear();
                return;
            }

            if (!(parseFloat(total.toFixed(2)) >= parseFloat(totalComprobanteCredito.toFixed(2)))) {
                show_alert({msg: 'El monto de los cr&eacute;ditos no puede superar el de los comprobantes.'});
                desbloquear();
                return;
            }
        }

        var json = {
            'ids': getComprobantesSeleccionados(), // ids.toArray(),
            'ids_anticipos': getAnticiposSeleccionados() // ids_comprobante_credito.toArray()
        };

        $('#form_generar_autorizacion_contable').addHiddenInputData(json);

        $('#form_generar_autorizacion_contable').submit();
    });

}

/**
 * 
 * @returns {undefined}
 */
function filtrarComprobantesCredito() {

    $('#table-comprobante-credito .btn-clear-filters').click();

    comprobantes_seleccionados = dt_getSelectedRows('#table-comprobantecompra');

    var filtro_oc = '(';

    for (var index = 0; index < comprobantes_seleccionados.length; index++) {
        filtro_oc += comprobantes_seleccionados[index][5] + '|';
    }

    if (filtro_oc.length > 1) {

        filtro_oc = filtro_oc.substr(0, filtro_oc.length - 1);

        filtro_oc += ')';

        dt_creditosCompra.fnFilter(filtro_oc, dt_creditosCompra_column_index.proveedor_id, true, true, false);
    }
}

/**
 * 
 * @returns {getComprobantesSeleccionados.ids|Array}
 */
function getComprobantesSeleccionados() {

    var ids = [];

    $('#table-comprobantecompra tr.active').each(function () {

        var data = $('#table-comprobantecompra').dataTable().fnGetData($(this));

        ids.push(data[0]);
    });

    $('#table-comprobante-credito tr.active').each(function () {

        var data = $('#table-comprobante-credito').dataTable().fnGetData($(this));

        if (data[4] == 2) {
            ids.push(data[0]);
        }
    });

    return ids;
}

/**
 * 
 * @returns {getAnticiposSeleccionados.ids|Array}
 */
function getAnticiposSeleccionados() {

    var ids = [];

    $('#table-comprobante-credito tr.active').each(function () {

        var data = $('#table-comprobante-credito').dataTable().fnGetData($(this));

        if (data[4] == 1) {
            ids.push(data[0]);
        }
    });

    return ids;
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

/**
 * 
 * @returns {undefined}
 */
function ocultarAnulados() {
    $('.anulado').parents('tr').addClass('anulado tooltips');
    $('.anulado').parents('tr').attr('data-original-title', 'COMPROBANTE ANULADO');
    $('input.anulado').remove();
}

/**
 * 
 * @returns {undefined}
 */
function ocultarUtilizados() {
    $('.utilizada').parents('tr').addClass('utilizada tooltips');
    $('.utilizada').parents('tr').attr('data-original-title', 'NOTA CRÉDITO UTILIZADA');
    $('input.utilizada').remove();
}

/**
 * 
 * @returns {undefined}
 */
function customDatepickerInit() {
    var fecha_contable = $('.mensaje-asiento-contable').data('fechaAsiento');
    if (typeof fecha_contable !== "undefined" && $('.mensaje-asiento-contable').data('es-anulacion') === 1) {
        $('#fecha-asiento').datepicker("update", fecha_contable);
    }
}