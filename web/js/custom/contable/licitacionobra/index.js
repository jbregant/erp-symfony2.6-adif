var dt_licitacionobra_column_index = {
    id: 0,
    multiselect: 1,
    tipoContratacion: 2,
    numero: 3,
    anio: 4,
    fechaApertura: 5,
    importePliego: 6,
    importeLicitacion: 7,
    porcentajeAdjudicado: 8,
    acciones: 9
};

dt_licitacionobra = dt_datatable($('#table-licitacionobra'), {
    ajax: __AJAX_PATH__ + 'licitacion_obra/index_table/',
    columnDefs: [
        {
            "targets": dt_licitacionobra_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_licitacionobra_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_licitacionobra_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.new_tramo !== undefined ?
                                '<a href="' + full_data.new_tramo + '" class="btn btn-xs yellow-gold tooltips" data-placement="left" data-original-title="Agregar rengl&oacute;n">\n\
                                    <i class="fa fa-letter bold">T</i>\n\
                                </a>' : '')
                        ;
            }
        },
        {
            "targets": dt_licitacionobra_column_index.porcentajeAdjudicado,
            "createdCell": function (td, cellData, rowData, row, col) {

                var porcentajeAdjudicado = rowData[dt_licitacionobra_column_index.porcentajeAdjudicado];

                var color = 'state state-danger';

                if (clearValue(porcentajeAdjudicado) == 100) {
                    color = 'state state-success';
                }

                $(td).addClass(color);
            },
            "render": function (data, type, full, meta) {

                return full[dt_licitacionobra_column_index.porcentajeAdjudicado];
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_licitacionobra_column_index.tipoContratacion,
                dt_licitacionobra_column_index.numero,
                dt_licitacionobra_column_index.anio,
                dt_licitacionobra_column_index.fechaApertura,
                dt_licitacionobra_column_index.importePliego,
                dt_licitacionobra_column_index.importeLicitacion
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_licitacionobra_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_licitacionobra_column_index.acciones
        }
    ]
});


jQuery(document).ready(function () {

    initEditarFechaAsientoContableHandler();

});

/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    updateFechaComprobanteFromAsientoContable();

}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearValue($value) {
    return $value
            .replace('%', '')
            .replace(/\./g, '')
            .replace(/\,/g, '.')
            .trim();
}