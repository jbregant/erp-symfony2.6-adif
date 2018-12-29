
var dt_contratoconsultoria_column_index = {
    id: 0,
    multiselect: 1,
    numeroContrato: 2,
    numeroCarpeta: 3,
    consultor: 4,
    fechaInicio: 5,
    fechaFin: 6,
    importeTotal: 7,
    saldoPendienteFacturacion: 8,
    gerencia: 9,
	importeCiclo: 10,
    acciones: 11
};

dt_consultoria_contratoconsultoria = dt_datatable($('#table-contratoconsultoria'), {
    ajax: __AJAX_PATH__ + 'contratoconsultoria/index_table/',
    columnDefs: [
        {
            "targets": dt_contratoconsultoria_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_contratoconsultoria_column_index.saldoPendienteFacturacion,
            "render": function (data, type, full, meta) {

                return data.saldo + '<a href="' + data.href + '" class="pull-right tooltips link-detalle-saldo" data-original-title="Ver detalle del saldo">\n\
                    <i class="fa fa-search-plus font-green-seagreen"></i>\n\
                </a>';
            }
        },
        {
            "targets": dt_contratoconsultoria_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_contratoconsultoria_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined ?
                        '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>' : '')+
                        (full_data.adenda !== undefined ?
                        '<a href="' + full_data.adenda + '" class="btn btn-xs blue-madison tooltips" data-original-title="Adendar">\n\
                             <i class="fa fa-letter">A</i>\n\
                        </a>' : '')+
                        (full_data.historico !== undefined ?
                        '<a href="' + full_data.historico + '" class="btn btn-xs grey-cascade tooltips" data-original-title="Ver hist&oacute;rico">\n\
                             <i class="fa fa-exchange"></i>\n\
                        </a>' : '');
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_contratoconsultoria_column_index.numeroContrato,
                dt_contratoconsultoria_column_index.numeroCarpeta,
                dt_contratoconsultoria_column_index.fechaInicio,
                dt_contratoconsultoria_column_index.fechaFin,
                dt_contratoconsultoria_column_index.importeTotal,
                dt_contratoconsultoria_column_index.gerencia,
				dt_contratoconsultoria_column_index.importeCiclo
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_contratoconsultoria_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_contratoconsultoria_column_index.acciones
        }
    ]
});