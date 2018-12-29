var index = 0;

var dt_auditoria_column_index = {
    usuario: index++,
    cuit: index++,
    razonSocial: index++,
    leido: index++,
    fechaHora: index++,
};


dt_auditoria = dt_datatable($('#table-detalle'), {
    ajax: __AJAX_PATH__ + 'notificacion/items_table/'+idNoti+'/Detalle',
});