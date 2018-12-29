var dt_transferenciabancaria_column_index = {
id: 0,
multiselect: 1, 
    numeroTransferencia: 2, 
    idCuenta: 3,acciones: 4
};

dt_transferenciabancaria = dt_datatable($('#table-transferenciabancaria'), {
ajax: __AJAX_PATH__ + 'transferenciabancaria/index_table/',
columnDefs: [
{
"targets": dt_transferenciabancaria_column_index.multiselect,
"data": "ch_multiselect",
"render": function (data, type, full, meta) {
return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
}
},
{
"targets": dt_transferenciabancaria_column_index.acciones,
"data": "actions",
"render": function (data, type, full, meta) {
var full_data = full[dt_transferenciabancaria_column_index.acciones];
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
className: "text-center",
targets: [
dt_transferenciabancaria_column_index.multiselect
]
},
{
className: "ctn_acciones text-center nowrap",
targets: dt_transferenciabancaria_column_index.acciones
}
]
});