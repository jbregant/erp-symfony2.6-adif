var dt_consultoria_consultor;
var dt_consultoria_consultor_column_index = {
    id: 0,
    multiselect: 1,
    legajo: 2,
    CUIT: 3,
    razonSocial: 4,
    acciones: 5
};

var indexDTToVisible = function (column_index) {
    return dt_consultoria_consultor.column(column_index).index('visible');
}

dt_consultoria_consultor = dt_datatable($('#table-consultoria_consultor'), {
    ajax: __AJAX_PATH__ + 'consultor/historico/index_table/',
    columnDefs: [
        {
            "targets": dt_consultoria_consultor_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_consultoria_consultor_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_consultoria_consultor_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>'
                        +
                        (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>' : '')
                        +
                        '<a href="' + full_data.cuenta_corriente + '" class="btn btn-xs yellow tooltips" data-original-title="Ver cuenta corriente">\n\
                            <i class="fa fa-letter">CC</i>\n\
                        </a>'
                        +
                        (
                            full_data.activar !== undefined ?
                                '<a href="' + full_data.activar + '" class="btn btn-xs btn-success tooltips consultor_activar" data-toggle="tooltip" data-original-title="Activar"><i class="fa fa-check"></i></a>' 
                                : ''
                        )
                        ;
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_consultoria_consultor_column_index.legajo,
                dt_consultoria_consultor_column_index.CUIT
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_consultoria_consultor_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_consultoria_consultor_column_index.acciones
        }
    ]
});

dt_consultoria_consultor = dt_consultoria_consultor.DataTable();

$(document).on('click', '.consultor_activar', function (e) {
        e.preventDefault();
        var a = $(this);
        show_confirm({
            title: 'Desactivar consultor',
            msg: 'Confirma activar al consultor <b>' + $(this).parents('tr').find('td:nth-child(' + (indexDTToVisible(dt_consultoria_consultor_column_index.razonSocial) + 1) + ')').text() + '</b>?',
            callbackOK: function () {
                location.href = a.attr('href');
            }
        });
    });