var index = 0;

var dt_notificacion_column_index = {
    id: index++,
    multiselect: index++,
    id: index++,
    titulo: index++,
    fechaDesde: index++,
    fechaHasta: index++,
    autor: index++,
    estadoId: index++,
    //mensaje: index++,
    // idUsuarioCreacion: index++,
    // idUsuarioUltimaModificacion: index++,
    acciones: index
};

dt_notificacion = dt_datatable($('#table-notificacion'), {
    ajax: __AJAX_PATH__ + 'notificacion/index_table/',
    "createdRow": function ( row, data, index ) {
        if ( data[7] == 'Activo' ) {
            $('td', row).eq(6).css( "color", "green").css("font-weight", "bold"); //activo
        }else {
            $('td', row).eq(6).css( "color", "red").css("font-weight", "bold"); //inactivo
        } 
    },
    columnDefs: [{
            "targets": dt_notificacion_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function(data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_notificacion_column_index.acciones,
            "data": "actions",
            "render": function(data, type, full, meta) {
                var full_data = full[dt_notificacion_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
    <i class="fa fa-search"></i>\n\
</a>' +
                    (full_data.edit !== undefined ?
                        '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
    <i class="fa fa-pencil"></i>\n\
</a>' : '') +
                   
                    '<a href="' + full_data.auditoria + '" class="btn btn-xs gris tooltips" data-original-title="Auditoria">\n\
    <i class="fa fa-book color-black"></i>\n\
</a>';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_notificacion_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_notificacion_column_index.acciones
        }
    ]
});