var dt_licitacioncompra_column_index = {
    id: 0,
    multiselect: 1,
    tipoContratacion: 2,
    numero: 3,
    anio: 4,
    fechaApertura: 5,
    importePliego: 6,
    importeLicitacion: 7,
    acciones: 8
};

dt_licitacioncompra = dt_datatable($('#table-licitacioncompra'), {
    ajax: __AJAX_PATH__ + 'licitacion_compra/index_table/',
    columnDefs: [
        {
            "targets": dt_licitacioncompra_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_licitacioncompra_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_licitacioncompra_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>' : '');
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_licitacioncompra_column_index.tipoContratacion,
                dt_licitacioncompra_column_index.numero,
                dt_licitacioncompra_column_index.anio,
                dt_licitacioncompra_column_index.fechaApertura,
                dt_licitacioncompra_column_index.importePliego
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_licitacioncompra_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_licitacioncompra_column_index.acciones
        }
    ]
});