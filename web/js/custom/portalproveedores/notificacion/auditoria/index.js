var index = 0;

var dt_auditoria_column_index = {
    notificaciones: index++,
    leidos: index++,
    enviados: index++,
};


dt_auditoria = dt_datatable($('#table-auditoria'), {
    ajax: __AJAX_PATH__ + 'notificacion/items_table/'+idNoti+'/Auditoria',
});