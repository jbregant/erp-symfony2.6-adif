var dt_ordenesPago_column_index = {
    id: 0,
    multiselect: 1,
    fechaComprobante: 2,
    numero: 3,
    razonSocial: 4,
    cuit: 5,
    concepto: 6,
//    cuentaBancaria: 7,
    pago: 7,
    montoBruto: 8,
    montoRetenciones: 9,
    montoNeto: 10,
    usuario: 11,
    estado: 12,
    acciones: 13
};

jQuery(document).ready(function () {

    initDataTable();

    initFiltroButton();

    initIndexLinks();

    initEditarFechaAsientoContableHandler();

});

/**
 * 
 * @returns {undefined}
 */
function initDataTable() {
    var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
    var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();

    if (validarRangoFechas(fechaInicio, fechaFin)) {

        dt_ordenes_pago = dt_datatable($('#table-ordenpago'), {
            ajax: {
                url: __AJAX_PATH__ + 'ordenpago/index_table/',
                data: function (d) {
                    d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_ordenesPago_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_ordenesPago_column_index.acciones,
                    "data": "actions",
                    "render": function (data, type, full, meta) {
                        var full_data = full[dt_ordenesPago_column_index.acciones];
                        return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
								<i class="fa fa-search"></i>\n\
							</a>\n\
							<a href="' + full_data.historico_general + '" class="btn btn-xs yellow-gold tooltips" data-original-title="Ver hist&oacute;rico general">\n\
								<i class="fa fa-list-ul"></i>\n\
							</a>\n\
							<a href="' + full_data.imprimir + '" class="btn btn-xs dark tooltips" data-original-title="Imprimir">\n\
								<i class="fa fa-print"></i>\n\
							</a>' +
                                (full_data.anular !== undefined ?
                                        '<a href="' + full_data.anular + '" class="btn btn-xs red tooltips link-anular" data-original-title="Anular">\n\
								<i class="fa fa-times"></i>\n\
							</a>' : '');
                    }
                },
                {
                    "targets": dt_ordenesPago_column_index.razonSocial,
                    "render": function (data, type, full, meta) {
                        return '<span class="truncate tooltips" data-original-title="' + data + '">'
                                + data + '</span>';
                    }
                },
                {
                    "targets": dt_ordenesPago_column_index.concepto,
                    "render": function (data, type, full, meta) {
                        return '<span class="truncate tooltips" data-original-title="' + data + '">'
                                + data + '</span>';
                    }
                },
                {
                    "targets": dt_ordenesPago_column_index.estado,
                    "createdCell": function (td, cellData, rowData, row, col) {

                        var full_data = rowData[dt_ordenesPago_column_index.estado];

                        $(td).addClass('state state-' + full_data.estadoClass);
                    },
                    "render": function (data, type, full, meta) {

                        var full_data = full[dt_ordenesPago_column_index.estado];

                        return  full_data.estado;
                    }
                },
                {
                    className: "nowrap",
                    targets: [
                        dt_ordenesPago_column_index.fechaComprobante,
                        dt_ordenesPago_column_index.numero,
                        dt_ordenesPago_column_index.pago,
                        dt_ordenesPago_column_index.montoBruto,
                        dt_ordenesPago_column_index.montoRetenciones,
                        dt_ordenesPago_column_index.montoNeto,
                        dt_ordenesPago_column_index.usuario,
                        dt_ordenesPago_column_index.estado
                    ]
                },
                {
                    className: "text-center",
                    targets: [
                        dt_ordenesPago_column_index.multiselect
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_ordenesPago_column_index.acciones
                }
            ],
            "fnDrawCallback": function () {
                initEllipsis();
            }
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#filtrar').on('click', function (e) {
        var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
        var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();
        
        setFechasFiltro(fechaInicio, fechaFin);
        
        if (validarRangoFechas(fechaInicio, fechaFin)) {
            dt_ordenes_pago.DataTable().ajax.reload();
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initIndexLinks() {
    // BOTON ANULAR PAGO
    $(document).on('click', '.link-anular', function (e) {

        e.preventDefault();

        var url = $(this).attr('href');
        var detallePago = $(this).parents('tr').data('detalle');
        var msg = "¿Desea anular la orden de pago?";

        if (typeof detallePago !== "undefined" && detallePago !== "") {
            msg += "<br/><br/>Tenga en cuenta que se anular&aacute; el pago: <strong>" + detallePago + "</strong>";
        }

        show_confirm({
            msg: msg,
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
function customEditarFechaAsientoContableHandler() {
    if (typeof $('.mensaje-asiento-contable').data('id-orden-pago') !== "undefined") {
        if ($('.mensaje-asiento-contable').data('es-anulacion') === 1) {
            updateFechaAnulacionOrdenPagoFromAsientoContable();
        } else {
            updateFechaOrdenPagoFromAsientoContable();
        }
    }

}