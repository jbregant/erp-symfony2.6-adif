
var index = 0;

var dt_iibbcaba_column_index = {
	id: index++,
	multiselect: index++, 
    grupo: index++,
    alicuota: index++,
    esProveedor: index++,
	acciones: index
};

dt_iibbcaba = dt_datatable($('#table-iibbcaba'), {
	ajax: __AJAX_PATH__ + 'iibb_caba/index_table/?esProveedor=' + esProveedor,
	columnDefs: [
		{
			"targets": dt_iibbcaba_column_index.multiselect,
			"data": "ch_multiselect",
			"render": function (data, type, full, meta) {
				return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
			}
		},
		{
			"targets": dt_iibbcaba_column_index.acciones,
			"data": "actions",
			"render": function (data, type, full, meta) {
				var full_data = full[dt_iibbcaba_column_index.acciones];
				return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
				<i class="fa fa-search"></i>\n\
				</a>' +
				(full_data.edit !== undefined 
					?
						'<a href="' + full_data.edit + '?esProveedor=' + esProveedor + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
						<i class="fa fa-pencil"></i>\n\
						</a>' 
					: ''
				) + 
				(full_data.borrar !== undefined 
					?
						'<a href="' + full_data.borrar + '?esProveedor=' + esProveedor + '" class="btn btn-xs red accion-borrar tooltips" data-original-title="Borrar">\n\
						<i class="fa fa-times"></i>\n\
						</a>' 
					: ''
				)
				;
			}
		},
		{
			className: "text-center",
			targets: [
				dt_iibbcaba_column_index.multiselect
			]
		},
		{
			className: "hidden",
			targets: [
				dt_iibbcaba_column_index.esProveedor
			]
		},
		{
			className: "ctn_acciones text-center nowrap",
			targets: dt_iibbcaba_column_index.acciones
		}
	]
});