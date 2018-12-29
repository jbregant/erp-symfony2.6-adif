
var index = 0;

var dt_pedidointerno_column_index = {
    id: index++,
    multiselect: index++,
    fechaPedido: index++,
    descripcion: index++,
    estadoPedidoInterno: index++,
    acciones: index
};

dt_licitacionobra = dt_datatable($('#table-pedidointerno'), {
    ajax: __AJAX_PATH__ + 'pedidointerno/index_table/',
    columnDefs: [
        {
            "targets": dt_pedidointerno_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_pedidointerno_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_pedidointerno_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined
                                ? '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-placement="left" data-original-title="Editar">\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>'
                                : '') +
                        (full_data.anular !== undefined
                                ? '<a href="' + full_data.anular + '" class="btn btn-xs red-thunderbird tooltips link-anular-pedido" data-placement="left" data-original-title="Anular">\n\
                                    <i class="fa fa-times"></i>\n\
                                </a>'
                                : '')
                        ;
            }
        },
        {
            "targets": dt_pedidointerno_column_index.estadoPedidoInterno,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_pedidointerno_column_index.estadoPedidoInterno];

                $(td).addClass("state state-" + full_data.aliasTipoImportancia);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_pedidointerno_column_index.estadoPedidoInterno];

                return  full_data.estadoPedidoInterno;
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_pedidointerno_column_index.fechaPedido,
                dt_pedidointerno_column_index.estadoPedidoInterno
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_pedidointerno_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_pedidointerno_column_index.acciones
        }
    ],
    "fnDrawCallback": function () {

        initIndexLink();
    }
});

/**
 * 
 * @returns {undefined}
 */
function initIndexLink() {

    // BOTON ANULAR PEDIDO
    $('.link-anular-pedido').click(function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea anular el pedido interno?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });
}