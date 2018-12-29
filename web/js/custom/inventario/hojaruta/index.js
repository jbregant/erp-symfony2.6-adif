$(document).ready(function(){
// Obtengo la cantidad de opciones que tiene el select
var cantidadOpciones = $('#tipo_material option').size();

    // Si solo tiene una opcion, tiene que quedar disabled el select
    if( cantidadOpciones == 1 ){
        $('#tipo_material').attr('disabled', 'disabled');
    }


var index = 0;

// Obtengo el valor seleccionado
var valorSeleccionado = $("#tipo_material").attr('data-id-material');
(valorSeleccionado != 0 && $('#tipo_material option[value = "'+valorSeleccionado+'"]').length > 0)?
valorSeleccionado : valorSeleccionado = $("#tipo_material option:first").val();

$("#tipo_material").val(valorSeleccionado);
$('#link_btn_hr').attr('href',  __AJAX_PATH__ + 'hojaruta/crear/'+ valorSeleccionado );

// Funcion para tomar el cambio del select
$( "#tipo_material" ).on('change', function() {
    // Obtengo el valor seleccionado
    valorSeleccionado = $(this).val();

    //cambia el link del boton crear
    $('#link_btn_hr').attr('href',  __AJAX_PATH__ + 'hojaruta/crear/'+ valorSeleccionado );

    //Recarga el datatables para la hoja de ruta correspondiente
    dt_hojaruta.api().ajax.url( __AJAX_PATH__ + 'hojaruta/index_table/'+ valorSeleccionado ).load();
});


var dt_hojaruta_column_index = {
        id: index++,
        multiselect: index++,
        denominacion: index++,
        usuarioAsignado: index++,
        fechaVencimiento: index++,
        estadoHojaRuta: index++,
        acciones: index
};

dt_hojaruta = dt_datatable($('#table-hojaruta'), {

    ajax: __AJAX_PATH__ + 'hojaruta/index_table/'+ valorSeleccionado,
    stateSave: false,
    columnDefs: [
        {
            "targets": dt_hojaruta_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_hojaruta_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_hojaruta_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined ?
                            '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                <i class="fa fa-pencil"></i>\n\
                            </a>' : ''  )+
                        (full_data.delete !== undefined ?
                            '<a href="' + full_data.delete + '" class="btn btn-xs red tooltips accion-borrar" data-original-title="Eliminar">\n\
                                <i class="fa fa-trash"></i>\n\
                            </a>' : '');
            }
        },
{
className: "text-center",
targets: [
dt_hojaruta_column_index.multiselect
]
},
{
className: "ctn_acciones text-center nowrap",
targets: dt_hojaruta_column_index.acciones
}
],
"initComplete": function (settings, json) {
    initBorrarButton();
}
});


});
