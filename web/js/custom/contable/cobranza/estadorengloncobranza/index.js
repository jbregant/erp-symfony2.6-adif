var dt_cobranza_estadorengloncobranza_column_index = {
id: 0,
multiselect: 1, 
    denominacion: 2, 
    descripcion: 3,acciones: 4
};

dt_cobranza_estadorengloncobranza = dt_datatable($('#table-cobranza_estadorengloncobranza'), {
ajax: __AJAX_PATH__ + 'cobranza_estadorengloncobranza/index_table/',
columnDefs: [
{
"targets": dt_cobranza_estadorengloncobranza_column_index.multiselect,
"data": "ch_multiselect",
"render": function (data, type, full, meta) {
return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
}
},
{
"targets": dt_cobranza_estadorengloncobranza_column_index.acciones,
"data": "actions",
"render": function (data, type, full, meta) {
var full_data = full[dt_cobranza_estadorengloncobranza_column_index.acciones];
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
dt_cobranza_estadorengloncobranza_column_index.multiselect
]
},
{
className: "ctn_acciones text-center nowrap",
targets: dt_cobranza_estadorengloncobranza_column_index.acciones
}
]
});