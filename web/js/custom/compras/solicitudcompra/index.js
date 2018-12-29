
var stock_pedido_form;

var inicializoSolapaTodas = false;

var dt_renglonpedidointerno_column_index = {
    id: 0,
    multiselect: 1,
    fechaPedido: 2,
    area: 3,
    usuario: 4,
    rubro: 5,
    bienEconomico: 6,
    descripcion: 7,
    cantidadSolicitada: 8,
    cantidadPendiente: 9,
    unidadMedida: 10,
    prioridad: 11,
    acciones: 12
};

var dt_solicitudcompra_column_index = {
    id: 0,
    multiselect: 1,
    numero: 2,
    fechaSolicitud: 3,
    tipoSolicitudCompra: 4,
    area: 5,
    descripcion: 6,
    justiprecio: 7,
    estadoSolicitudCompra: 8,
    acciones: 9
};

/**
 * 
 */
$(document).ready(function () {

    stock_pedido_form = $('.stock_pedido_form_content').removeClass('hidden').html();

    $('.stock_pedido_form_content').remove();

    initTabs();

    initTable();

    initIndexLinks();

    initCrearSolicitudButton();

    initVisarButton();
    
    $('.solicitudes-todas').on('click', function() {
        if (!inicializoSolapaTodas) {
            initTableSolicitudCompraTodas();
            inicializoSolapaTodas = true;
        }
    });
});

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    initTableRenglonPedidoInterno();

    initTableSolicitudCompraEnviadas();

    initTableSolicitudCompraPendientes();
    
    $('.solicitudes-enviadas').click();
}

/**
 * Solapa "Pedidos"
 * @returns {undefined}
 */
function initTableRenglonPedidoInterno() {

    dt_renglonpedidointerno = dt_datatable($("#table-renglonespedidointerno"), {
        order: [2, 'desc'],
        ajax: {
            url: __AJAX_PATH__ + 'solicitudcompra/index_table_renglon_pedido_interno/'
        },
        columnDefs: [
            {
                "targets": dt_renglonpedidointerno_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes not-checkbox-transform" value="' + data + '" />';
                }
            },
            {
                "targets": dt_renglonpedidointerno_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {
                    var full_data = full[dt_renglonpedidointerno_column_index.acciones];

                    return '<a href="' + full_data.cargar_stock + '" class="btn btn-xs dark tooltips link-cargar-stock" data-original-title="Cargar stock">\n\
                            <i class="fa fa-check-square-o"></i>\n\
                        </a>';
                }
            },
            {
                className: "nowrap",
                targets: [
                    dt_renglonpedidointerno_column_index.fechaPedido,
                    dt_renglonpedidointerno_column_index.usuario,
                    dt_renglonpedidointerno_column_index.cantidadSolicitada,
                    dt_renglonpedidointerno_column_index.cantidadPendiente,
                    dt_renglonpedidointerno_column_index.unidadMedida,
                    dt_renglonpedidointerno_column_index.prioridad
                ]
            },
            {
                className: "text-center",
                targets: [
                    dt_renglonpedidointerno_column_index.multiselect
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_renglonpedidointerno_column_index.acciones
            }
        ],
        "fnDrawCallback": function () {
            initCargarStockButton();
        }
    });

}

/**
 * Solapa "Area"
 * @returns {undefined}
 */
function initTableSolicitudCompraEnviadas() {

    var $table = $('#table-solicitudcompra-enviadas');

    var tipo = 'enviadas';

    initTableSolicitudCompra($table, tipo);
}

/**
 * 
 * @returns {undefined}
 */
function initTableSolicitudCompraPendientes() {

    var $table = $('#table-solicitudcompra-pendientes');

    var tipo = 'pendientes';

    initTableSolicitudCompra($table, tipo);
}

/**
 * 
 * @returns {undefined}
 */
function initTableSolicitudCompraTodas() {

    var $table = $('#table-solicitudcompra-todas');

    var tipo = 'todas';

    initTableSolicitudCompra($table, tipo);
}

/**
 * 
 * @param {type} $table
 * @param {type} tipo
 * @returns {undefined}
 */
function initTableSolicitudCompra($table, tipo) {

    dt_solicitudcompra = dt_datatable($table, {
        order: [2, 'desc'],
        ajax: {
            url: __AJAX_PATH__ + 'solicitudcompra/index_table/',
            data: {
                tipo: tipo
            }
        },
        columnDefs: [
            {
                "targets": dt_solicitudcompra_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes not-checkbox-transform" value="' + data + '" />';
                }
            },
            {
                "targets": dt_solicitudcompra_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {
                    var full_data = full[dt_solicitudcompra_column_index.acciones];

                    return (full_data.visar !== undefined ? '<a href="' + full_data.visar + '" class="btn btn-xs yellow tooltips link-visar-solicitud" data-original-title="Visar">\n\
                            <i class="fa fa-check"></i>\n\
                        </a>' : '') +
                            (full_data.show !== undefined ?
                                    '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="' + (full_data.checkShow == 1 ? 'Aprobar / Desaprobar' : 'Ver detalle') + '">\n\
                            <i class="fa fa-' + (full_data.checkShow == 1 ? 'check' : 'search') + '"></i>\n\
                                </a>' : '') +
                            (full_data.edit !== undefined ?
                                    '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                                </a>' : '') +
                            (full_data.historico !== undefined ?
                                    '<a href="' + full_data.historico + '" class="btn btn-xs grey-cascade tooltips" data-original-title="Hist&oacute;rico">\n\
                            <i class="fa fa-exchange"></i>\n\
                                </a>' : '') +
                            (full_data.print !== undefined ?
                                    '<a href="' + full_data.print + '" class="btn btn-xs dark tooltips" data-original-title="Imprimir">\n\
                            <i class="fa fa-print"></i>\n\
                        </a>' : '') +
                            (full_data.anular !== undefined
                                    ? '<a href="' + full_data.anular + '" class="btn btn-xs red-thunderbird tooltips link-anular-solicitud" data-placement="left" data-original-title="Anular">\n\
                                    <i class="fa fa-times"></i>\n\
                                </a>'
                                    : '');
                }
            },
            {
                "targets": dt_solicitudcompra_column_index.estadoSolicitudCompra,
                "createdCell": function (td, cellData, rowData, row, col) {

                    var full_data = rowData[dt_solicitudcompra_column_index.estadoSolicitudCompra];

                    $(td).addClass(full_data.estadoClass);
                },
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_solicitudcompra_column_index.estadoSolicitudCompra];

                    return  full_data.estadoSolicitudCompra;
                }
            },
            {
                className: "nowrap",
                targets: [
                    dt_solicitudcompra_column_index.area
                ]
            },
            {
                className: "text-center nowrap",
                targets: [
                    dt_solicitudcompra_column_index.numero,
                    dt_solicitudcompra_column_index.fechaSolicitud,
                    dt_solicitudcompra_column_index.tipoSolicitudCompra,
                    dt_solicitudcompra_column_index.estadoSolicitudCompra
                ]
            },
            {
                className: "text-center",
                targets: [
                    dt_solicitudcompra_column_index.multiselect
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_solicitudcompra_column_index.acciones
            },
            {
                className: "text-right",
                targets: dt_solicitudcompra_column_index.justiprecio
            }
        ]
    });
}

/**
 * 
 * @returns {undefined}
 */
function initTabs() {

    // Pedidos iternos
    $('.pedidos').click(function () {
        $('.caption-solicitud').text('Pedidos');
    });

    if ($('.pedidos').parent('li').hasClass('active')) {
        $('.pedidos').click();
    }

    // Solicitudes enviadas
    $('.solicitudes-enviadas').click(function () {
        $('.caption-solicitud').text('Solicitudes de compra enviadas');
    });

    if ($('.solicitudes-enviadas').parent('li').hasClass('active')) {
        $('.solicitudes-enviadas').click();
    }

    // Solicitudes pendientes de autorización
    $('.solicitudes-pendientes').click(function () {
        $('.caption-solicitud').text('Solicitudes de compra pendientes de autorización');
    });

    if ($('.solicitudes-pendientes').parent('li').hasClass('active')) {
        $('.solicitudes-pendientes').click();
    }

    // Todas las solicitudes de compra
    $('.solicitudes-todas').click(function () {
        $('.caption-solicitud').text('Todas las solicitudes de compra');
    });

    if ($('.solicitudes-todas').parent('li').hasClass('active')) {
        $('.solicitudes-todas').click();
    }
}

/**
 * 
 * @returns {undefined}
 */
function initIndexLinks() {

    // BOTON VISAR SOLICITUD
    $(document).on('click', '.link-visar-solicitud', function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea visar la solicitud?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });

    // BOTON ANULAR SOLICITUD
    $(document).on('click', '.link-anular-solicitud', function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea anular la solicitud?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initCrearSolicitudButton() {

    $('#btn-crear-solicitud a[pedidos-table]').on('click', function (e) {

        e.preventDefault();

        bloquear();

        var table = $('#table-renglonespedidointerno');

        var ids = [];

        switch ($(this).data('crear-solicitud')) {
            case _exportar_todos:
                ids = dt_getRowsIds(table);
                break;
            case _exportar_filtrados:
                ids = dt_getFilteredRowsIds(table);
                break;
            case _exportar_mostrados:
                ids = dt_getShowedRowsIds(table);
                break;
            case _exportar_seleccionados:
                ids = dt_getSelectedRowsIds(table);
                break;
        }

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un pedido.'});

            desbloquear();

            return;
        }
        else {
            open_window(
                    'post',
                    __AJAX_PATH__ + 'solicitudcompra/crear-solicitud/',
                    {ids: JSON.stringify(ids.toArray())}
            );
        }

        $('.bootbox').removeAttr('tabindex');

        e.stopPropagation();
    });
}


/**
 * 
 * @returns {undefined}
 */
function initCargarStockButton() {

    $('.link-cargar-stock').off().on('click', function (e) {

        e.preventDefault();

        // Obtengo el TR del Pedido clickeado
        var trPedido = $(this).parents('tr');

        var cantidadPendiente = trPedido.find('td').eq(8).html();

        show_dialog({
            titulo: 'Indicar stock disponible',
            contenido: stock_pedido_form,
            callbackCancel: function () {
            },
            callbackSuccess: function () {

                var formulario = $('form[name=adif_comprasbundle_pedido]');

                var formularioValido = formulario.validate().form();

                // Si el formulario es válido
                if (formularioValido) {

                    var stockIndicado = formulario
                            .find('#adif_comprasbundle_pedido_stock').val();

                    if (stockIndicado.toString() !== '0') {

                        if (parseFloat(stockIndicado) > parseFloat(cantidadPendiente)) {

                            var options = $.extend({
                                title: 'Ha ocurrido un error',
                                msg: "El stock indicado supera la cantidad solicitada."
                            });

                            show_alert(options);

                            return false;
                        }
                        else {

                            var idRenglonPedido = $('#table-renglonespedidointerno').DataTable().row(trPedido).data()[0];

                            var nuevaCantidad = cantidadPendiente - stockIndicado;

                            $.ajax({
                                type: 'post',
                                async: false,
                                url: __AJAX_PATH__ + 'pedidointerno/actualizar_cantidades/',
                                data: {
                                    id: idRenglonPedido,
                                    cantidad: nuevaCantidad
                                }
                            }).done(function () {

                                showFlashMessage('success', 'El stock disponible fue indicado con éxito');

                                // Seteo el Stock en el TR
                                trPedido.find('td').eq(8).html(nuevaCantidad);
                            });

                        }
                    }
                } //.   
                else {
                    return false;
                }
            }
        });

        if (cantidadPendiente !== null && cantidadPendiente !== "-") {
            $('#adif_comprasbundle_pedido_stock').val(cantidadPendiente);
        }

        $('.bootbox').removeAttr('tabindex');
    });
}


/**
 * 
 * @returns {undefined}
 */
function initVisarButton() {

    $('#btn-visar-solicitudes a[solicitud-table]').on('click', function (e) {

        e.preventDefault();

        bloquear();

        var table = $('#table-solicitudcompra-pendientes');

        var ids = [];

        switch ($(this).data('visar-solicitud')) {
            case _exportar_todos:
                ids = dt_getRowsIds(table);
                break;
            case _exportar_seleccionados:
                ids = dt_getSelectedRowsIds(table);
                break;
        }

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos una solicitud para visar.'});

            desbloquear();

            return;
        }
        else {

            $solicitudesVisables = true;

            $(dt_getSelectedRows($('#table-solicitudcompra-pendientes'))).each(function () {

                $estadoSolicitud = $(this)[6];

                if ($estadoSolicitud !== __estadoSolicitudAprobada) {

                    $solicitudesVisables = false;
                }
            });

            if (!$solicitudesVisables) {
                show_alert({msg: 'Algunas de las solicitudes seleccionadas no se pueden visar.'});

                desbloquear();

                return;
            }
            else {

                $.ajax({
                    type: 'post',
                    url: __AJAX_PATH__ + 'solicitudcompra/visar-solicitudes/',
                    data: {
                        ids: JSON.stringify(ids.toArray())
                    }
                }).done(function (data, textStatus) {
                    if (textStatus === 'success') {
                        location.href = __AJAX_PATH__ + 'solicitudcompra/';
                    }
                    else {

                        desbloquear();

                        show_alert({
                            msg: 'Algunas solicitudes de compra no se visaron correctamente.',
                            title: 'Error al visar',
                            type: 'error'});
                    }
                });
            }
        }

        $('.bootbox').removeAttr('tabindex');

        e.stopPropagation();
    });
}