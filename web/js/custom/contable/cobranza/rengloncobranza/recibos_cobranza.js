var index = 0;
var dt_recibos_cobranza_column_index = {
    id: index++,
    multiselect: index++,
    fecha: index++,
    numero_recibo: index++,
    cuenta_bancaria: index++,
    referencia: index++,
    cliente: index++,
    tipo_comprobantes: index++,
    nro_comprobantes: index++,
    detalle: index++,
    importe: index++,
    importe_sin_aplicar: index++,
    acciones: index++
};

$(document).ready(function () {
    initDataTable();
    initFiltroButton();
}); 

function getTDValue(el_td){
    return $(el_td).html().replace('$ ', '');
}

function initDataTable(){
    var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
    var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();
    
    if (validarRangoFechas(fechaInicio, fechaFin)) {
        dt_recibos_cobranza = dt_datatable($('#table-recibos'), {
            ajax: {
                url: __AJAX_PATH__ + 'rengloncobranza/index_table_recibos_cobranza/',
                data: function (d) {
                    d.fecha_desde = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fecha_hasta = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            paging: false,
            columnDefs: [
                {
                    "targets": dt_recibos_cobranza_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_recibos_cobranza_column_index.acciones,
                    "data": "actions",
                    "render": function (data, type, full, meta) {
                        var full_data = full[dt_recibos_cobranza_column_index.acciones];
                        return '<a href="' + full_data.print + '" class="btn btn-xs purple-wisteria tooltips" data-original-title="Imprimir recibo">\n\
                                    <i class="fa fa-file-powerpoint-o"></i>\n\
                                </a>';
                    }
                },
                {
                    className: "nowrap",
                    targets: [
                        dt_recibos_cobranza_column_index.importe,
                        dt_recibos_cobranza_column_index.importe_sin_aplicar
                    ]
                },
                {
                    className: "text-right",
                    targets: [
                        dt_recibos_cobranza_column_index.importe,
                        dt_recibos_cobranza_column_index.importe_sin_aplicar
                    ]
                },
                {
                    className: "text-center",
                    targets: [
                        dt_recibos_cobranza_column_index.multiselect,
                        dt_recibos_cobranza_column_index.referencia,
                        dt_recibos_cobranza_column_index.tipo_comprobantes,
                        dt_recibos_cobranza_column_index.nro_comprobantes
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_recibos_cobranza_column_index.acciones
                }
            ]
        });
    }
}

function initFiltroButton(){
    $('#filtrar-recibos').on('click', function (e) {
        e.preventDefault();
        var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
        var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();
        
        setFechasFiltro(fechaInicio, fechaFin);
        dt_recibos_cobranza.DataTable().ajax.reload();
    });
}