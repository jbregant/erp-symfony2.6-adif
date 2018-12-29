
var dt_renglonsolicitud_column_index = {
    id: 0,
    multiselect: 1,
    numero: 2,
    prioridad: 3,
    rubro: 4,
    bienEconomico: 5,
    descripcion: 6,
    cantidadPendiente: 7,
    unidadMedida: 8,
    justiprecioUnitario: 9,
    justiprecioTotal: 10,
    acciones: 11
};

var i = 0;
var dt_requerimiento_column_index = {
    id: i++,
    multiselect: i++,
    numero: i++,
	numerosSC: i++,
    fechaRequerimiento: i++,
    descripcion: i++,
    justiprecioTotal: i++,
    tipoContratacion: i++,
    usuario: i++,
    estadoRequerimiento: i++,
    acciones: i++
};


$(document).ready(function () {

    initTabs();

    initTables();

    initIndexLink();
});


/**
 * 
 * @returns {undefined}
 */
function initTabs() {

    // Solicitudes pendientes
    $('.tab-solicitudes-pendientes').click(function () {
        $('.caption-requerimiento').text('Solicitudes pendientes');
    });

    if ($('.tab-solicitudes-pendientes').parent('li').hasClass('active')) {
        $('.tab-solicitudes-pendientes').click();
    }

    // Requerimientos enviados
    $('.tab-requerimientos-enviados').click(function () {
        $('.caption-requerimiento').text('Requerimientos enviados');
    });

    if ($('.tab-requerimientos-enviados').parent('li').hasClass('active')) {
        $('.tab-requerimientos-enviados').click();
    }

    // Requerimientos pendientes de aprobación
    $('.tab-requerimientos-pendientes-aprobacion').click(function () {
        $('.caption-requerimiento').text('Requerimientos pendientes de aprobación');
    });

    if ($('.tab-requerimientos-pendientes-aprobacion').parent('li').hasClass('active')) {
        $('.tab-requerimientos-pendientes-aprobacion').click();
    }

    // Requerimientos pendientes de envío
    $('.tab-requerimientos-pendientes-envio').click(function () {
        $('.caption-requerimiento').text('Requerimientos pendientes de envío');
    });

    if ($('.tab-requerimientos-pendientes-envio').parent('li').hasClass('active')) {
        $('.tab-requerimientos-pendientes-envio').click();
    }

    // Todos los requerimientos
    $('.tab-requerimientos-todos').click(function () {
        $('.caption-requerimiento').text('Todos los requerimientos');
    });

    if ($('.tab-requerimientos-todos').parent('li').hasClass('active')) {
        $('.tab-requerimientos-todos').click();
    }
}

/**
 * 
 * @returns {undefined}
 */
function initTables() {

    initTableRenglonSolicitud();

    initTableRequerimientoEnviados();

    initTableRequerimientoPendientesAprobacion();

    initTableRequerimientoPendientesEnvio();

    initTableRequerimientoTodos();
}

/**
 * 
 * @returns {undefined}
 */
function initTableRenglonSolicitud() {

    dt_renglonsolicitud = dt_datatable($("#table-renglonsolicitudcompra"), {
        order: [2, 'desc'],
        ajax: {
            url: __AJAX_PATH__ + 'requerimiento/index_table_renglon_solicitud/'
        },
        columnDefs: [
            {
                "targets": dt_renglonsolicitud_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes not-checkbox-transform" value="' + data + '" />';
                }
            },
            {
                "targets": dt_renglonsolicitud_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_renglonsolicitud_column_index.acciones];

                    return (full_data.showAcciones == 1) ? '<a href="' + full_data.agregar_renglon_requerimiento + '" class="btn btn-xs green tooltips agregar_renglon_requerimiento" data-original-title="Agregar a requerimiento">\n\
                            <i class="fa fa-plus"></i>\n\
                        </a>' : '';
                }
            },
            {
                className: "nowrap",
                targets: [
                    dt_renglonsolicitud_column_index.numero,
                    dt_renglonsolicitud_column_index.rubro
                ]
            },
            {
                className: "text-center",
                targets: [
                    dt_renglonsolicitud_column_index.multiselect
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_renglonsolicitud_column_index.acciones
            }
        ]
    });
}

/**
 * 
 * @returns {undefined}
 */
function initTableRequerimientoEnviados() {

    var $table = $('#table-requerimiento-enviados');

    var tipo = 'enviados';

    initTableRequerimiento($table, tipo);
}

/**
 * 
 * @returns {undefined}
 */
function initTableRequerimientoPendientesAprobacion() {

    var $table = $('#table-requerimiento-pendientes-aprobacion');

    var tipo = 'pendientes-aprobacion';

    initTableRequerimiento($table, tipo);
}

/**
 * 
 * @returns {undefined}
 */
function initTableRequerimientoPendientesEnvio() {

    var $table = $('#table-requerimiento-pendientes-envio');

    var tipo = 'pendientes-envio';

    initTableRequerimiento($table, tipo);
}

/**
 * 
 * @returns {undefined}
 */
function initTableRequerimientoTodos() {

    var $table = $('#table-requerimiento-todos');

    var tipo = 'todos';

    initTableRequerimiento($table, tipo);
}

/**
 * 
 * @param {type} $table
 * @param {type} tipo
 * @returns {undefined}
 */
function initTableRequerimiento($table, tipo) {

    dt_requerimiento = dt_datatable($table, {
        order: [2, 'desc'],
        ajax: {
            url: __AJAX_PATH__ + 'requerimiento/index_table/',
            data: {
                tipo: tipo
            }
        },
        columnDefs: [
            {
                "targets": dt_requerimiento_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes not-checkbox-transform" value="' + data + '" />';
                }
            },
            {
                "targets": dt_requerimiento_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {
                    var full_data = full[dt_requerimiento_column_index.acciones];

                    return (full_data.show !== undefined ?
                            '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="' + (full_data.checkShow == 1 ? 'Aprobar / Desaprobar' : 'Ver detalle') + '">\n\
                                    <i class="fa fa-' + (full_data.checkShow == 1 ? 'check' : 'search') + '"></i>\n\
                                </a>' : '') +
                            (full_data.edit !== undefined ?
                                    '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                        <i class="fa fa-pencil"></i>\n\
                                    </a>' : '') +
                            (full_data.show_invitaciones !== undefined ?
                                    '<a href="' + full_data.show_invitaciones + '" class="btn btn-xs yellow-casablanca tooltips" data-original-title="Ver cotizaciones">\n\
                                        <i class="fa fa-file-text-o"></i>\n\
                                    </a>' : '') +
                            (full_data.print !== undefined ?
                                    '<a href="' + full_data.print + '" class="btn btn-xs dark tooltips" data-original-title="Imprimir">\n\
                                        <i class="fa fa-print"></i>\n\
                                    </a>' : '') +
                            (full_data.print_provisorio_compra !== undefined ?
                                    '<a href="' + full_data.print_provisorio_compra + '" class="btn btn-xs purple-wisteria tooltips" data-original-title="Imprimir provisorio">\n\
                                        <i class="fa fa-file-powerpoint-o"></i>\n\
                                    </a>' : '') +
                            (full_data.anular !== undefined ?
                                    '<a href="' + full_data.anular + '" class="btn btn-xs red-thunderbird tooltips link-anular-requerimiento" data-original-title="Anular">\n\
                                        <i class="fa fa-times"></i>\n\
                                    </a>' : '') +
                            (full_data.archivar !== undefined ?
                                    '<a href="' + full_data.archivar + '" class="btn btn-xs grey-cascade tooltips link-archivar-requerimiento" data-original-title="Archivar">\n\
                                        <i class="fa fa-archive"></i>\n\
                                    </a>' : '');
                }
            },
            {
                "targets": dt_requerimiento_column_index.estadoRequerimiento,
                "createdCell": function (td, cellData, rowData, row, col) {

                    var full_data = rowData[dt_requerimiento_column_index.estadoRequerimiento];

                    $(td).addClass(full_data.estadoClass);
                },
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_requerimiento_column_index.estadoRequerimiento];

                    return  full_data.estadoRequerimiento;
                }
            },
            {
                className: "nowrap",
                targets: [
                    dt_requerimiento_column_index.numero,
                    dt_requerimiento_column_index.fechaRequerimiento,
                    dt_requerimiento_column_index.tipoContratacion,
                    dt_requerimiento_column_index.usuario
                ]
            },
            {
                className: "text-center nowrap",
                targets: [
                    dt_requerimiento_column_index.estadoRequerimiento
                ]
            },
            {
                className: "text-center",
                targets: [
                    dt_requerimiento_column_index.multiselect
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_requerimiento_column_index.acciones
            }
        ]
    });
}

/**
 * 
 * @returns {undefined}
 */
function initIndexLink() {

    // BOTON ARCHIVAR REQUERIMIENTO
    $(document).on('click', '.link-archivar-requerimiento', function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea archivar el requerimiento?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });

    // BOTON ANULAR REQUERIMIENTO
    $(document).on('click', '.link-anular-requerimiento', function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea anular el requerimiento?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });
}