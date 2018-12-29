var index = 0;

var dt_comprobantesObra_column_index = {
    id: index++,
    multiselect: index++,
    fechaComprobante: index++,
    comprobante: index++,
    numero: index++,
    proveedor: index++,
    tramo: index++,
    tramoId: index++,
    documentoFinanciero: index++,
    correspondePago: index++,
    total: index++,
    pendiente: index++,
    anulado: index++,
    acciones: index++
};

dt_comprobanteObra = dt_datatable($('#table-comprobanteobra'), {
    ajax: __AJAX_PATH__ + 'comprobanteobra/index_table/',
    columnDefs: [
        {
            "targets": dt_comprobantesObra_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes' + (full[dt_comprobantesObra_column_index.anulado] == '1' ? ' anulado' : '') + '" value="' + data + '" />';
            }
        },
        {
            "targets": dt_comprobantesObra_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_comprobantesObra_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>'
                        + (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>'
                                : '')
                        + (full_data.anular !== undefined ?
                                '<a href="' + full_data.anular + '" class="btn btn-xs red accion-anular tooltips" data-original-title="Anular">\n\
                                    <i class="fa fa-times"></i>\n\
                                </a>'
                                : '');
            }
        },
        {
            "targets": dt_comprobantesObra_column_index.tramo,
            "render": function (data, type, full, meta) {
                return '<div class="truncate">'
                        + data + '</span>';
            }
        },
        {
            className: "hidden",
            targets: [
                dt_comprobantesObra_column_index.tramoId,
                dt_comprobantesObra_column_index.anulado
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_comprobantesObra_column_index.fechaComprobante,
                dt_comprobantesObra_column_index.comprobante,
                dt_comprobantesObra_column_index.numero,
                dt_comprobantesObra_column_index.proveedor,
                dt_comprobantesObra_column_index.documentoFinanciero,
                dt_comprobantesObra_column_index.correspondePago,
                dt_comprobantesObra_column_index.total,
                dt_comprobantesObra_column_index.pendiente,
                dt_comprobantesObra_column_index.tramo
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_comprobantesObra_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: [
                dt_comprobantesObra_column_index.acciones
            ]
        }
    ],
    "fnDrawCallback": function () {

        initEllipsis();

//        $(document).on('mouseenter', ".truncate", function () {
//            var $this = $(this);
//            if (this.offsetWidth < this.scrollWidth && !$this.attr('title')) {
//                $this.tooltip({
//                    title: $this.text(),
//                    placement: "bottom"
//                });
//                $this.tooltip('show');
//            }
//        });
//
//        // initSeleccionarComprobanteHandler();

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
    tramo: index++,
    tramoId: index++,
    correspondePago: index++,
    total: index++,
    anulado: index++,
    acciones: index++,
    utilizada: index++
};

dt_creditosCompra = dt_datatable($('#table-comprobante-credito'), {
    ajax: __AJAX_PATH__ + 'comprobanteobra/index_table_comprobante_credito/',
    columnDefs: [
        {
            "targets": dt_creditosCompra_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes' + (full[dt_creditosCompra_column_index.anulado] == '1' ? ' anulado' : '') + '" value="' + data + '" />';
            }
        },
        {
            "targets": dt_creditosCompra_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_creditosCompra_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>'
                        + (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>'
                                : '')
                        + (full_data.anular !== undefined ?
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
                dt_creditosCompra_column_index.tramoId,
                dt_creditosCompra_column_index.anulado
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_creditosCompra_column_index.fecha,
                dt_creditosCompra_column_index.tipo,
                dt_creditosCompra_column_index.proveedor,
                dt_creditosCompra_column_index.correspondePago,
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
    "fnDrawCallback": function () {

        // initSeleccionarComprobanteHandler();

        ocultarAnulados();
        ocultarUtilizados();
    }
});


$(document).ready(function () {

    $('#table-comprobanteobra').on('selected_element', function (e, cantidad) {

        if (cantidad > 0) {
            $('#generar_autorizacion_contable').removeClass('hidden');
            if (cantidad === 1) {
                $('.plural').addClass('hidden');
            } else {
                $('.plural').removeClass('hidden');
            }
        } else {
            $('#generar_autorizacion_contable').addClass('hidden');
        }

        filtrarComprobantesCredito();

        $('#cant_seleccionados').text(cantidad);

        $('#cant_seleccionados').parent().removeClass('flash animated')
                .addClass('flash animated')
                .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    $(this).removeClass('flash animated');
                });
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

/**
 * 
 * @returns {undefined}
 */
function initSeleccionarComprobanteHandler() {

    $('tbody tr').on('click', function () {

        seleccionarComprobantesByRenglonLicitacion($(this));
    });
}

/**
 * 
 * @returns {undefined}
 */
function initGenerarOrdenPagoHandler() {

    $('#generar_autorizacion_contable').on('click', function (e) {

        e.preventDefault();
        bloquear();

        if (isComprobantesSinPagosParciales()) {

            var ids = [];
            var ids_comprobante_credito = [];

            var total = 0;
            var totalComprobanteCredito = 0;

            var valido = true;

            var table = $('#table-comprobanteobra');
            var table_comprobante_credito = $('#table-comprobante-credito');

            ids = dt_getSelectedRowsIds(table);
            ids_comprobante_credito = dt_getSelectedRowsIds(table_comprobante_credito);

            if (!ids.length) {
                show_alert({msg: 'Debe seleccionar al menos un comprobante para generar la autorización contable.'});
                desbloquear();
                return;
            }

            var primero = dt_getSelectedRows($(table))[0][5];

            total += parseFloat(dt_getSelectedRows($(table))[0][9].replace('$ ', '')
                    .replace(/\./g, '').replace(',', '.'));

            for (index = 1; index < dt_getSelectedRows($(table)).length; index++) {

                valido &= primero === dt_getSelectedRows($(table))[index][5];

                total += parseFloat(dt_getSelectedRows($(table))[index][9].replace('$ ', '')
                        .replace(/\./g, '').replace(',', '.'));
            }

            if (!valido) {
                show_alert({msg: 'Debe seleccionar comprobantes del mismo renglón de licitación.'});
                desbloquear();
                return;
            }

            if (ids_comprobante_credito.length > 0) {

                valido &= primero === dt_getSelectedRows($(table_comprobante_credito))[0][5];

                totalComprobanteCredito += parseFloat(dt_getSelectedRows($(table_comprobante_credito))[0][7]
                        .replace('$ ', '').replace(/\./g, '').replace(',', '.'));

                for (index = 1; index < dt_getSelectedRows($(table_comprobante_credito)).length; index++) {

                    valido &= primero === dt_getSelectedRows($(table_comprobante_credito))[index][5];

                    totalComprobanteCredito += parseFloat(dt_getSelectedRows($(table_comprobante_credito))[index][7]
                            .replace('$ ', '').replace(/\./g, '').replace(',', '.'));

                }

                if (!valido) {
                    show_alert({msg: 'Debe seleccionar comprobantes y cr&eacute;ditos del mismo rengl&oacute;n de licitaci&oacute;n.'});
                    desbloquear();
                    return;
                }
                    
                if (!(parseFloat(total.toFixed(2)) >= parseFloat(totalComprobanteCredito.toFixed(2)))) {
                    show_alert({msg: 'El monto de los cr&eacute;ditos no puede superar al de los comprobantes.'});
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

        }
        else {

            show_alert({msg: 'Existen comprobantes con pagos parciales pendientes de cancelaci&oacute;n.'});

            desbloquear();

            return;
        }
    });
}

/**
 * 
 * @returns {Boolean}
 */
function isComprobantesSinPagosParciales() {

    var isComprobantesSinPagosParciales = true;

    var idsComprobantes = getComprobantesSeleccionados();

    $.ajax({
        type: "POST",
        async: false,
        url: __AJAX_PATH__ + 'comprobantes/validar_pago_parcial/',
        data: {ids_comprobantes: idsComprobantes}
    }).done(function (booleanResult) {

        isComprobantesSinPagosParciales = booleanResult;
    });

    return isComprobantesSinPagosParciales;
}

/**
 * 
 * @returns {undefined}
 */
function filtrarComprobantesCredito() {

    $('#table-comprobante-credito .btn-clear-filters').click();

    comprobantes_seleccionados = dt_getSelectedRows($('#table-comprobanteobra'));

    var filtro_tramo = '(';

    for (var index = 0; index < comprobantes_seleccionados.length; index++) {
        filtro_tramo += comprobantes_seleccionados[index][5] + '|';
    }

    if (filtro_tramo.length > 1) {

        filtro_tramo = filtro_tramo.substr(0, filtro_tramo.length - 1);

        filtro_tramo += ')';

        dt_creditosCompra.fnFilter(filtro_tramo, dt_creditosCompra_column_index.tramoId, true, true, false);
    }
}

/**
 * 
 * @param {type} $trSeleccionado
 * @returns {undefined}
 */
function seleccionarComprobantesByRenglonLicitacion($trSeleccionado) {

    seleccionarComprobantes($trSeleccionado, $('#table-comprobanteobra'));

    seleccionarComprobantes($trSeleccionado, $('#table-comprobante-credito'));
}

/**
 * 
 * @param {type} $trSeleccionado
 * @param {type} $table
 * @returns {undefined}
 */
function seleccionarComprobantes($trSeleccionado, $table) {

    if (!$($trSeleccionado).hasClass('active')) {

        var comprobanteSeleccionado = $table.dataTable().fnGetData($trSeleccionado);

        if (comprobanteSeleccionado !== null) {

            var idTramo = comprobanteSeleccionado[7];

            $('#table-comprobanteobra tbody tr').not('.active .anulado').each(function () {

                var data = $('#table-comprobanteobra').dataTable().fnGetData($(this));

                if (data !== null && comprobanteSeleccionado[0] !== data[0] && data[7] == idTramo) {
                    $(this).addClass('active');
                    $(this).find('input[type="checkbox"]').prop('checked', true);
                }
            });

            $('#table-comprobante-credito tbody tr').not('.active .anulado').each(function () {

                var data = $('#table-comprobante-credito').dataTable().fnGetData($(this));

                if (data !== null && comprobanteSeleccionado[0] !== data[0] & data[7] == idTramo) {
                    $(this).addClass('active');
                    $(this).find('input[type="checkbox"]').prop('checked', true);
                }
            });
        }
    }
}

/**
 * 
 * @returns {getComprobantesSeleccionados.ids|Array}
 */
function getComprobantesSeleccionados() {

    var ids = [];

    $('#table-comprobanteobra tr.active').each(function () {

        var data = $('#table-comprobanteobra').dataTable().fnGetData($(this));

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