var nEditing = null;

var dt_ordencompra_pendientes_generacion;

var dt_ordencompra_generadas;

var renglon_orden_compra_form;

var idOc = null;

var simboloTipoMoneda = null;

var $detalleMotivoAnulacion;

var i = 0;
var dt_ordencompra_pendiente_generacion_column_index = {
    id: i++,
    multiselect: i++,
    numeroOrdenCompra: i++,
    numeroCalipso: i++,
	numeroRequerimiento: i++,
    fechaOrdenCompra: i++,
    proveedor: i++,
    cotizacion: i++,
    descripcion: i++,
    simboloTipoMoneda: i++,
    monto: i++,
    rubros: i++,
    bienes: i++,
    estadoOrdenCompra: i++,
    acciones: i++
};

var i = 0;
var dt_ordencompra_generadas_column_index = {
    id: i++,
    multiselect: i++,
    numeroOrdenCompra: i++,
    numeroCalipso: i++,
	numeroRequerimiento: i++,
    fechaOrdenCompra: i++,
    proveedor: i++,
    cotizacion: i++,
    descripcion: i++,
    simboloTipoMoneda: i++,
    monto: i++,
    rubros: i++,
    bienes: i++,
    saldo: i++,
    usuario: i++,
    estadoOrdenCompra: i++,
    acciones: i++
};

$(document).ready(function () {

    initTabs();

    initFiltro();

    initTable();

    initPopover();

    initFiltroButton();

    initAnularLink();

    initEditarSaldo();

    initEditarRenglon();

    initGuardarRenglonLink();

    initCancelarCambiosLink();
});


/**
 * 
 * @returns {undefined}
 */
function initTabs() {

    // OC pendientes de generacion
    $('.tab-ordencompra-pendientes-generacion').click(function () {
        $('.caption-orden-compra').text('Órdenes de compra pendientes de generación');
    });

    if ($('.tab-ordencompra-pendientes-generacion').parent('li').hasClass('active')) {
        $('.tab-ordencompra-pendientes-generacion').click();
    }

    // OC generadas
    $('.tab-ordencompra-generadas').click(function () {
        $('.caption-orden-compra').text('Órdenes de compra generadas');
    });

    if ($('.tab-ordencompra-generadas').parent('li').hasClass('active')) {
        $('.tab-ordencompra-generadas').click();
    }
}

/**
 * 
 * @returns {undefined}
 */
function initFiltro() {

    var $fechaInicio = getFirstDateOfCurrentMonth();

    var $fechaFin = getEndingDateOfCurrentMonth();

    $('input[ id *= adif_comprasbundle_filtro_fechaInicio]').datepicker("setDate", $fechaInicio);

    $('input[ id *= adif_comprasbundle_filtro_fechaFin]').datepicker("setDate", $fechaFin);
}

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    initTableOrdenCompraPendientesGeneracion();

    initTableOrdenCompraGeneradas();
}

/**
 * 
 * @returns {undefined}
 */
function initPopover() {

    $('body').popover({
        selector: '[data-toggle="popover"]',
        placement: 'top',
        html: true,
        trigger: 'click',
        container: 'body',
        template: '<div class="popover table-actions-popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>'
    });

    $('body').on('click', '[data-toggle="popover"]', function () {

        $('[data-toggle="popover"].selected')
                .not(this)
                .removeClass('selected')
                .popover('hide');

        $(this).addClass('selected');
    });
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#filtrar-pendientes-generacion').on('click', function (e) {
        dt_ordencompra_pendientes_generacion.DataTable().ajax.reload();
    });

    $('#filtrar-generadas').on('click', function (e) {
        dt_ordencompra_generadas.DataTable().ajax.reload();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initTableOrdenCompraPendientesGeneracion() {

    dt_ordencompra_pendientes_generacion = dt_datatable($('#table-ordencompra-pendientes-generacion'), {
        order: [1, 'desc'],
        ajax: {
            url: __AJAX_PATH__ + 'ordenescompra/index_table_principal/',
            data: function (d) {
                d.fechaInicio = $('#adif_comprasbundle_filtro_fechaInicio-pendientes-generacion').val();
                d.fechaFin = $('#adif_comprasbundle_filtro_fechaFin-pendientes-generacion').val();
                d.tipo = 'pendientes-generacion';
            }
        },
        columnDefs: [
            {
                "targets": dt_ordencompra_pendiente_generacion_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes not-checkbox-transform" value="' + data + '" />';
                }
            },
            {
                "targets": dt_ordencompra_pendiente_generacion_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {
                    var full_data = full[dt_ordencompra_pendiente_generacion_column_index.acciones];
                    return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                            (full_data.edit !== undefined ?
                                    '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                                </a>' : '') +
                            (full_data.print !== undefined ?
                                    '<a href="' + full_data.print + '" class="btn btn-xs dark tooltips" data-original-title="Imprimir">\n\
                            <i class="fa fa-print"></i>\n\
                        </a>' : '') +
                            (full_data.print_definitivo_compra !== undefined ?
                                    '<a href="' + full_data.print_definitivo_compra + '" class="btn btn-xs purple-wisteria tooltips" data-original-title="Imprimir definitivo">\n\
                            <i class="fa fa-file-powerpoint-o"></i>\n\
                        </a>' : '') +
                            (full_data.reporte_desvio !== undefined ?
                                    '<a href="' + full_data.reporte_desvio + '" class="btn btn-xs yellow-gold tooltips" data-original-title="Ver reporte de desv&iacute;o">\n\
                            <i class="fa fa-line-chart"></i>\n\
                        </a>' : '') +
                            (full_data.anular !== undefined ?
                                    '<a href="' + full_data.anular + '" class="btn btn-xs red tooltips link-anular" data-original-title="Anular">\n\
                            <i class="fa fa-times"></i>\n\
                        </a>' : '');
                }
            },
            {
                "targets": dt_ordencompra_pendiente_generacion_column_index.cotizacion,
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_ordencompra_pendiente_generacion_column_index.cotizacion];

                    return '<a href="' + full_data.showPath + '">' + full_data.cotizacion + '</a>';
                }
            },
            {
                "targets": dt_ordencompra_pendiente_generacion_column_index.estadoOrdenCompra,
                "createdCell": function (td, cellData, rowData, row, col) {

                    var full_data = rowData[dt_ordencompra_pendiente_generacion_column_index.estadoOrdenCompra];

                    $(td).addClass(full_data.estadoClass);
                },
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_ordencompra_pendiente_generacion_column_index.estadoOrdenCompra];

                    return  full_data.estadoOrdenCompra;
                }
            },
            {
                "targets": dt_ordencompra_pendiente_generacion_column_index.simboloTipoMoneda,
                "createdCell": function (td, cellData, rowData, row, col) {

                    var simboloTipoMoneda = cellData === "" ? "$" : cellData;

                    $(td).parent('tr').attr("data-simbolo-tipo-moneda", simboloTipoMoneda);
                }
            },
            {
                className: "hidden",
                targets: [
                    dt_ordencompra_pendiente_generacion_column_index.simboloTipoMoneda
                ]
            },
            {
                className: "nowrap",
                targets: [
                    dt_ordencompra_pendiente_generacion_column_index.numeroOrdenCompra,
                    dt_ordencompra_pendiente_generacion_column_index.fechaOrdenCompra,
                    dt_ordencompra_pendiente_generacion_column_index.cotizacion,
                    dt_ordencompra_pendiente_generacion_column_index.monto,
                    dt_ordencompra_pendiente_generacion_column_index.usuario,
                    dt_ordencompra_pendiente_generacion_column_index.estadoOrdenCompra
                ]
            },
            {
                className: "text-center",
                targets: [
                    dt_ordencompra_pendiente_generacion_column_index.multiselect
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_ordencompra_pendiente_generacion_column_index.acciones
            }
        ]
    });
}

/**
 * 
 * @returns {undefined}
 */
function initTableOrdenCompraGeneradas() {

    dt_ordencompra_generadas = dt_datatable($('#table-ordencompra-generadas'), {
        order: [1, 'desc'],
        ajax: {
            url: __AJAX_PATH__ + 'ordenescompra/index_table_principal/',
            data: function (d) {
                d.fechaInicio = $('#adif_comprasbundle_filtro_fechaInicio-generadas').val();
                d.fechaFin = $('#adif_comprasbundle_filtro_fechaFin-generadas').val();
                d.tipo = 'generadas';
            }
        },
        columnDefs: [
            {
                "targets": dt_ordencompra_generadas_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes not-checkbox-transform" value="' + data + '" />';
                }
            },
            {
                "targets": dt_ordencompra_generadas_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {
                    var full_data = full[dt_ordencompra_generadas_column_index.acciones];
                    return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                            (full_data.edit !== undefined ?
                                    '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                                </a>' : '') +
                            '<a tabindex="0" class="btn btn-xs dark tooltips" data-toggle="popover" data-original-title="Imprimir" data-content=\''
                            +
                            (full_data.print !== undefined ?
                                    '<a href="' + full_data.print + '" class="btn btn-xs dark tooltips" data-original-title="OC actual">\n\
                                        <i class="fa fa-letter">Actual</i>\n\
                                    </a>' : '') +
                            (full_data.print_original !== undefined ?
                                    '\n\<a href="' + full_data.print_original + '" class="btn btn-xs dark tooltips" data-original-title="OC original">\n\
                                        <i class="fa fa-letter">Original</i>\n\
                                    </a>' : '')
                            +
                            '\'><i class="fa fa-print"></i></a>'
                            +
                            (full_data.print_definitivo_compra !== undefined ?
                                    '<a href="' + full_data.print_definitivo_compra + '" class="btn btn-xs purple-wisteria tooltips" data-original-title="Imprimir definitivo">\n\
                            <i class="fa fa-file-powerpoint-o"></i>\n\
                        </a>' : '') +
                            (full_data.reporte_desvio !== undefined ?
                                    '<a href="' + full_data.reporte_desvio + '" class="btn btn-xs yellow-gold tooltips" data-original-title="Ver reporte de desv&iacute;o">\n\
                            <i class="fa fa-line-chart"></i>\n\
                        </a>' : '') +
                            (full_data.anular !== undefined ?
                                    '<a href="' + full_data.anular + '" class="btn btn-xs red tooltips link-anular" data-original-title="Anular">\n\
                            <i class="fa fa-times"></i>\n\
                        </a>' : '') +
							(full_data.oc_abierta !== undefined ?
								'<a href="' + full_data.oc_abierta + '" class="btn btn-xs blue tooltips" data-toggle="tooltip" data-placement="left" data-original-title="Crear OC abierta"><i class="fa fa-letter">A</i></a>\n\
							</a>' : '')
						;
                }
            },
            {
                "targets": dt_ordencompra_generadas_column_index.cotizacion,
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_ordencompra_generadas_column_index.cotizacion];

                    return '<a href="' + full_data.showPath + '">' + full_data.cotizacion + '</a>';
                }
            },
            {
                "targets": dt_ordencompra_generadas_column_index.simboloTipoMoneda,
                "createdCell": function (td, cellData, rowData, row, col) {

                    var simboloTipoMoneda = cellData === "" ? "$" : cellData;

                    $(td).parent('tr').attr("data-simbolo-tipo-moneda", simboloTipoMoneda);
                }
            },
            {
                "targets": dt_ordencompra_generadas_column_index.saldo,
                "createdCell": function (td, cellData, rowData, row, col) {

                    $(td).css({minWidth: "140px"});
                },
                "render": function (data, type, full, meta) {

                    var id = full[dt_ordencompra_generadas_column_index.id];

                    var full_data = full[dt_ordencompra_generadas_column_index.saldo];

                    return full_data.saldo !== undefined ?
                            full_data.saldo
                            + (full_data.muestraDetalleSaldo !== undefined && full_data.muestraDetalleSaldo === "1" ?
                                    '<a data-orden-compra-id="' + id + '" class="pull-right tooltips link-detalle-saldo" \n\
                                        data-original-title="Ver detalle del saldo">\n\
                                        <i class="fa fa-search-plus font-green-seagreen"></i>\n\
                                    </a>'
                                    : '')
                            : '';
                    ;
                }
            },
            {
                "targets": dt_ordencompra_generadas_column_index.estadoOrdenCompra,
                "createdCell": function (td, cellData, rowData, row, col) {

                    var full_data = rowData[dt_ordencompra_generadas_column_index.estadoOrdenCompra];

                    $(td).addClass(full_data.estadoClass);
                },
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_ordencompra_generadas_column_index.estadoOrdenCompra];

                    return  full_data.estadoOrdenCompra;
                }
            },
            {
                className: "hidden",
                targets: [
                    dt_ordencompra_generadas_column_index.simboloTipoMoneda
                ]
            },
            {
                className: "nowrap",
                targets: [
                    dt_ordencompra_generadas_column_index.numeroOrdenCompra,
                    dt_ordencompra_generadas_column_index.fechaOrdenCompra,
                    dt_ordencompra_generadas_column_index.cotizacion,
                    dt_ordencompra_generadas_column_index.monto,
                    dt_ordencompra_generadas_column_index.saldo,
                    dt_ordencompra_generadas_column_index.usuario,
                    dt_ordencompra_generadas_column_index.estadoOrdenCompra
                ]
            },
            {
                className: "text-center",
                targets: [
                    dt_ordencompra_generadas_column_index.multiselect
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_ordencompra_generadas_column_index.acciones
            }
        ]
    });
}

/**
 * 
 * @returns {undefined}
 */
function initAnularLink() {

    $detalleMotivoAnulacion = $('#detalle_motivo_anulacion').removeClass('hidden').html();

    $('#detalle_motivo_anulacion').remove();

    // BOTON ANULAR ORDEN COMPRA
    $(document).on('click', '.link-anular', function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea anular la orden de compra?',
            callbackOK: function () {

                show_dialog({
                    titulo: 'Detalle del motivo de anulaci&oacuten',
                    contenido: $detalleMotivoAnulacion,
                    labelCancel: 'Cancelar',
                    closeButton: false,
                    callbackCancel: function () {

                        return;
                    },
                    callbackSuccess: function () {

                        var formulario = $('form[name=adif_comprasbundle_detalle_motivo_anulacion]');

                        var formularioValido = formulario.validate().form();

                        // Si el formulario es válido
                        if (formularioValido) {

                            var motivoAnulacion = $('#adif_comprasbundle_ordencompra_motivoAnulacion')
                                    .val();

                            window.location.href = url + '?motivo_anulacion=' + motivoAnulacion;

                            return;
                        }
                        else {
                            return false;
                        }
                    }
                });

                $('#detalle_motivo_anulacion').show();
            }
        });

        e.stopPropagation();
    });
}


/**
 * 
 * @returns {undefined}
 */
function initEditarSaldo() {

    nEditing = null;

    $(document).on('click', '.link-detalle-saldo', function (e) {

        e.preventDefault();

        idOc = $(this).data('orden-compra-id');

        simboloTipoMoneda = $(this).parents('tr').data('simbolo-tipo-moneda');

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'ordenescompra/editar_saldo/',
            data: idOc,
            success: function (tables) {

                var $contenidoDetalle = $('<div class="portlet-body">');

                $contenidoDetalle.append(tables);

                show_dialog({
                    titulo: 'Ver saldos',
                    contenido: $contenidoDetalle,
                    callbackCancel: function () {
                        desbloquear();
                        return;
                    },
                    callbackSuccess: function () {
                        desbloquear();
                        return;
                    },
                    labelSuccess: 'Cerrar'
                });


                renglon_orden_compra_form = $('.renglon_orden_compra_form_content').html();

                $('.renglon_orden_compra_form_content').remove();

                initAgregarRenglonOrdenCompraLink();

                $('.modal-dialog').css('width', '60%');

                $('.cancel').remove();

                var ajax_renglones = $.ajax({
                    url: __AJAX_PATH__ + 'ordenescompra/renglones_orden_compra/',
                    data: {id_orden_compra: idOc},
                    type: 'POST',
                    dataType: 'json'
                });

                var ajax_adicionales = $.ajax({
                    url: __AJAX_PATH__ + 'ordenescompra/adicionales_cotizacion/',
                    data: {id_orden_compra: idOc},
                    type: 'POST',
                    dataType: 'json'
                });

                $.when(ajax_renglones, ajax_adicionales).then(function (result_renglones, result_adicionales) {

                    result_renglones = result_renglones[0] ? result_renglones[0] : null;

                    result_adicionales = result_adicionales[0] ? result_adicionales[0] : null;

                    // Renglones
                    if (result_renglones.length > 0) {

                        $.each(result_renglones, function (i, renglon) {
                            agregarRenglon(renglon);
                        });

                        dt_init($('#table-saldos-oc-renglones'));

                        $('#saldos-oc-renglones').show();
                    }

                    // Adicionales
                    if (result_adicionales.length > 0) {

                        $.each(result_adicionales, function (i, renglon) {
                            agregarAdicional(renglon);
                        });

                        dt_init($('#table-saldos-oc-adicionales'));

                        $('#saldos-oc-adicionales').show();
                    }

                    setMasks();

                    desbloquear();

                }, function () {

                    show_alert({msg: 'Ocurri&oacute; un error al obtener los adicionales y los renglones de la orden de compra. Intente nuevamente.'});

                    desbloquear();
                });
            }
        });
    });

    initBorrarRenglon();

}

/**
 * 
 * @param {type} renglon
 * @returns {undefined}
 */
function agregarRenglon(renglon) {

    var porcentajeAlicuotaIVA = typeof alicuotaIva[renglon.idAlicuotaIva] !== "undefined"
            ? alicuotaIva[renglon.idAlicuotaIva]
            : '0.00';

    var simboloTipoMoneda = typeof renglon.simboloTipoMoneda === "undefined"
            ? "$"
            : renglon.simboloTipoMoneda;

    $('#table-saldos-oc-renglones tbody')
            .append($('<tr data-simbolo-tipo-moneda="' + simboloTipoMoneda + '">')
                    .append($('<td>'))
                    .append($('<td class="text-center">')
                            .append($('<input type="checkbox" class="checkboxes" value="" />')))
                    .append($('<td class="nowrap">').text(renglon.denominacionBienEconomico))
                    .append($('<td class="nowrap">').text(renglon.restante))
                    .append($('<td class="nowrap money-format">').text(renglon.precioUnitario))
                    .append($('<td class="nowrap money-format">').text(renglon.precioUnitario * renglon.restante))
                    .append($('<td class="nowrap">').text(porcentajeAlicuotaIVA + ' %'))
                    .append($('<td class="nowrap money-format">').text((renglon.precioUnitario * renglon.restante * alicuotaIva[renglon.idAlicuotaIva] / 100).toFixed(2)))
                    .append($('<td class="ctn_acciones text-center nowrap">')
                            .append($('<a href="#" class="btn btn-xs green tooltips editar-renglon" data-renglon-id="' + renglon.id + '" data-original-title="Editar"><i class="fa fa-pencil"></i></a>'))
                            .append($('<a href="#" class="btn btn-xs red tooltips borrar-renglon" data-renglon-id="' + renglon.id + '" data-original-title="Eliminar"><i class="fa fa-times"></i></a>')))
                    );
}

/**
 * 
 * @param {type} renglon
 * @returns {undefined}
 */
function agregarAdicional(renglon) {

    $('#table-saldos-oc-adicionales tbody')
            .append($('<tr data-simbolo-tipo-moneda= "' + simboloTipoMoneda + '">')
                    .append($('<td>'))
                    .append($('<td class="text-center">')
                            .append($('<input type="checkbox" class="checkboxes" value="" />')))
                    .append($('<td class="nowrap">').text(tipoAdicional[renglon.idTipoAdicional]))
                    .append($('<td class="nowrap">').text((renglon.signo == '-') ? 'Resta (-)' : 'Suma (+)'))
                    .append($('<td class="nowrap' + ((renglon.tipoValor == '%') ? '' : ' money-format') + '">').text((renglon.tipoValor == '%') ? renglon.valor + ' %' : renglon.valor))
                    .append($('<td class="nowrap">').text(alicuotaIva[renglon.idAlicuotaIva] + ' %'))
                    );
}

/**
 * 
 * @returns {undefined}
 */
function initBorrarRenglon() {

    $(document).on('click', '.borrar-renglon', function (e) {

        e.preventDefault();

        $element = $(this);

        show_confirm({
            msg: 'Confirma el borrado del elemento?',
            callbackOK: function () {

                blockTarget($('.modal-content'));

                $('#table-saldos-oc-renglones').DataTable().row($($element).parents('tr')).remove().draw();

                $.ajax({
                    type: "POST",
                    data: {id: $($element).data('renglon-id')},
                    url: __AJAX_PATH__ + 'ordenescompra/eliminar_renglon/'
                }).done(function (result) {
                    location.href = __AJAX_PATH__ + 'ordenescompra';

                }).fail(function () {
                    location.href = __AJAX_PATH__ + 'ordenescompra';
                });
            }
        });

        unblockTarget($('.modal-content'));
    });
}

/**
 * 
 * @returns {undefined}
 */
function initEditarRenglon() {

    $(document).on('click', '.editar-renglon', function (e) {

        e.preventDefault();

        $element = $(this);

        nEditing = $($element).data('renglon-id');

        // CANTIDAD
        cantidadValue = $($element).parents('tr').find('td').eq(2).html();

        $($element).parents('tr').find('td').eq(2).html(
                '<input type="text" id="cantidad" class="input form-control input-sm numberPositive" style="height: auto; text-align: right;">'
                );

        $('#cantidad').val(cantidadValue);


        // PRECIO UNITARIO
        precioValue = $($element).parents('tr').find('td').eq(3).html().replace('$', '').replace(/\./g, '').replace(',', '.');

        $($element).parents('tr').find('td').eq(3).html(
                '<input type="text" data-digits="4" id="precioUnitario" class="input form-control input-sm numberPositive" style="height: auto; text-align: right;">'
                );

        $('#precioUnitario').val(precioValue);


        $($element).parents('tr').find('td.ctn_acciones').html(
                '<a class="btn btn-xs blue tooltips guardar_renglon" data-cantidad-renglon="' + cantidadValue + '" data-precio-renglon="' + precioValue + '" data-renglon-id="' + nEditing + '" data-original-title="Guardar" href="#"><i class="fa fa-check"></i></a>\n\
                 <a href="#" class="btn btn-xs red tooltips cancelar_cambios"  data-cantidad-renglon="' + cantidadValue + '" data-precio-renglon="' + precioValue + '" data-renglon-id="' + nEditing + '" data-original-title="Cancelar"><i class="fa fa-times"></i></a>'
                );

        initCurrencies();

        initGuardarRenglonLink();

        initCancelarCambiosLink();

    });
}

/**
 * 
 * @returns {undefined}
 */
function initGuardarRenglonLink() {

    $(document).off('click', '.guardar_renglon').on('click', '.guardar_renglon', function (e) {

        bloquear();

        e.preventDefault();

        $element = $(this);

        if (parseFloat($('#cantidad').val().replace(',', '.')) > parseFloat($($element).data('cantidad-renglon'))) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "La cantidad no puede superar la cantidad pendiente solicitada."
            });

            show_alert(options);
        }
        else {
            show_confirm({
                msg: '¿Confirma la modificación del renglón?',
                callbackOK: function () {

                    blockTarget($('.modal-content'));

                    $.ajax({
                        type: "POST",
                        data: {
                            id: nEditing,
                            cantidad: $('#cantidad').val(),
                            precioUnitario: $('#precioUnitario').val()
                        },
                        url: __AJAX_PATH__ + 'ordenescompra/modificar_renglon/'
                    }).done(function (result) {
                        location.href = __AJAX_PATH__ + 'ordenescompra';

                    }).fail(function () {
                        location.href = __AJAX_PATH__ + 'ordenescompra';
                    });
                }
            });
        }

        unblockTarget($('.modal-content'));

    });
}

/**
 * 
 * @returns {undefined}
 */
function initCancelarCambiosLink() {

    $(document).on('click', '.cancelar_cambios', function (e) {

        bloquear();
        e.preventDefault();

        $element = $(this);

        nEditing = $($element).data('renglon-id');

        // CANTIDAD
        $($element).parents('tr').find('td').eq(2).html($($element).data('cantidad-renglon'));


        // PRECIO UNITARIO
        $($element).parents('tr').find('td').eq(3).html(($($element).data('precio-renglon')).replace('.', ','));

        $($element).parents('tr').find('td.ctn_acciones').html(
                '<a href="#" class="btn btn-xs green tooltips editar-renglon" data-renglon-id="' + nEditing + '" data-original-title="Editar"><i class="fa fa-pencil"></i></a>\n\
                <a href="#" class="btn btn-xs red tooltips borrar-renglon" data-renglon-id="' + nEditing + '" data-original-title="Eliminar"><i class="fa fa-times"></i></a>'
                );

        setMasks();

        nEditing = null;

        desbloquear();
    });

}

/**
 * 
 * @returns {undefined}
 */
function initAgregarRenglonOrdenCompraLink() {

    $('#agregar_renglon_oc').off().on('click', function (e) {

        e.preventDefault();

        show_dialog({
            titulo: 'Agregar renglón',
            contenido: renglon_orden_compra_form,
            callbackCancel: function () {
            },
            callbackSuccess: function () {

                var formulario = $('form[name=adif_comprasbundle_renglonordencompra]');

                var formularioValido = formulario.validate().form();

                // Si el formulario es válido
                if (formularioValido) {

                    if (!$.fn.DataTable.isDataTable($('#table-saldos-oc-renglones'))) {
                        dt_init($('#table-saldos-oc-renglones'));
                    }

                    agregarNuevoRenglonOrdenCompra(formulario);

                    $('#saldos-oc-renglones').show();

                    $('.btn-submit').click(function () {

                        location.href = __AJAX_PATH__ + 'ordenescompra';
                    });

                } //.   
                else {
                    return false;
                }
            }
        });

        $('.bootbox').removeAttr('tabindex');

        $('.modal-dialog').css('width', '60%');

        initCurrencies();

        initSelects();

        setMasks();
    });
}

/**
 * 
 * @param {type} formulario
 * @returns {undefined}
 */
function agregarNuevoRenglonOrdenCompra(formulario) {

    var checkbox = '<input type="checkbox" class="checkboxes" value="" />';

    var idBienEconomico = formulario
            .find('#adif_comprasbundle_renglonordencompra_bienEconomico').val();

    var idCentroCosto = formulario
            .find('#adif_comprasbundle_renglonordencompra_centroCosto').val();

    var denominacionBienEconomico = formulario
            .find('#adif_comprasbundle_renglonordencompra_bienEconomico')
            .select2('data').text;

    var cantidad = formulario
            .find('#adif_comprasbundle_renglonordencompra_cantidad').val();

    var precioUnitario = clearCurrencyValue(formulario
            .find('#adif_comprasbundle_renglonordencompra_precioUnitario').val());

    var tipoCambio = clearCurrencyValue(formulario
            .find('#adif_comprasbundle_renglonordencompra_tipoCambio').val());

    var total = precioUnitario * cantidad;

    var idUnidadMedida = formulario
            .find('#adif_comprasbundle_renglonordencompra_unidadMedida').val();

    var unidadMedida = formulario
            .find('#adif_comprasbundle_renglonordencompra_unidadMedida')
            .select2('data').text;

    var idAlicuotaIva = formulario
            .find('#adif_comprasbundle_renglonordencompra_alicuotaIva').val();

    var alicuotaIvaFormulario = formulario
            .find('#adif_comprasbundle_renglonordencompra_alicuotaIva')
            .select2('data').text;

    var totalIva = (precioUnitario * cantidad * alicuotaIva[idAlicuotaIva] / 100).toFixed(2);

    var acciones =
            '<a class="btn btn-xs green tooltips editar-renglon" '
            + 'data-renglon-id='
            + 'data-original-title="Editar" href="#">'
            + '<i class="fa fa-pencil"></i></a>\n\
            <a class="btn btn-xs red tooltips borrar-renglon" '
            + 'data-renglon-id='
            + 'data-original-title="Eliminar" href="#">'
            + '<i class="fa fa-times"></i></a>';

    blockTarget($('.modal-content'));

    $.ajax({
        type: "POST",
        global: false,
        data: {
            idOrdenCompra: idOc,
            idBienEconomico: idBienEconomico,
            idCentroCosto: idCentroCosto,
            cantidad: cantidad,
            precioUnitario: precioUnitario,
            tipoCambio: tipoCambio,
            idUnidadMedida: idUnidadMedida,
            idAlicuotaIva: idAlicuotaIva
        },
        url: __AJAX_PATH__ + 'ordenescompra/agregar_renglon/'
    }).done(function (resultado) {

        if (resultado['id'] !== null) {

            var newRow = $('#table-saldos-oc-renglones').DataTable().row.add([
                resultado['id'],
                checkbox, // Checkbox
                denominacionBienEconomico, // Text Bien Economico
                cantidad, // Cantidad
                precioUnitario, // Precio Unitario
                total, // Total,
                alicuotaIvaFormulario, // Alicuota IVA
                totalIva, // Total IVA
                acciones // Acciones
            ]).draw().node();

            $(newRow).data('simbolo-tipo-moneda', simboloTipoMoneda);

            // Agrego estilos al TD del checkbox 
            $(newRow).find('td').eq(0).addClass('text-center');

            // Agrego estilos al TD del Precio Unitario
            $(newRow).find('td').eq(3).addClass('money-format');

            // Agrego estilos al TD del Total 
            $(newRow).find('td').eq(4).addClass('money-format');

            // Agrego estilos al TD del Total IVA 
            $(newRow).find('td').eq(6).addClass('money-format');

            // Le agrego estilos al TD de Acciones
            $(newRow).find('td').last().addClass('ctn_acciones text-center nowrap');

            setMasks();

            showFlashMessage('success', resultado['mensaje']);
        }
        else {

            showFlashMessage('danger', resultado['mensaje']);
        }


        desbloquear($('.modal-content'));
    });
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {

        var simboloTipoMoneda = $(this).parent('tr').data('simbolo-tipo-moneda');

        if (simboloTipoMoneda === null) {
            simboloTipoMoneda = "$";
        }

        $(this).autoNumeric('destroy');
        $(this).autoNumeric('init', {vMin: '-999999999.9999', vMax: '9999999999.9999', aSign: simboloTipoMoneda + ' ', aSep: '.', aDec: ','});
        $(this).autoNumeric('update');
    });
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value
            .replace('U$D', '')
            .replace('€', '')
            .replace('R$', '')
            .replace(/\$|\%/g, '')
            .replace(/\./g, '')
            .replace(/\,/g, '.')
            .trim();
}
