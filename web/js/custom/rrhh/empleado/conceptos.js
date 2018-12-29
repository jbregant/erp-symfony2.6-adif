$(document).ready(function(){
    
    bloquear($('#table-empleado-conceptos').parents('.portlet-body'));
    
    $('#table-empleado-conceptos').on('preXhr.dt', function ( e, settings, data ) {
        
    })
    
    $('#table-empleado-conceptos').on('xhr.dt', function ( e, settings, data ) {
//        desbloquear($('#table-empleado-conceptos').parents('.portlet-body'));
    })
    
    $('#table-empleado-conceptos').on('init.dt', function ( e, settings, data ) {
        // En la ultima columna de la tabla está la lista de conceptos por cada empleado
//        var c_table = $('#table-empleado-conceptos').DataTable();
//        _.each(c_table.rows().nodes(), function(el_tr){
//            var ids_conceptos = c_table.cell(el_tr._DT_RowIndex,'th:last').data().split(',');
////            var ids_conceptos = $(el_tr).find('td:last').html().split(',');
//            if (ids_conceptos !== null){
//                _.each(ids_conceptos, function(id_concepto){
//                    var cel = c_table.cell(el_tr._DT_RowIndex,'th[c_id='+id_concepto+']');
//                    if (cel.length){
//                        cel.data('Si');
//                    }
//                })
//            }
//        })
        
        var c_table = $('#table-empleado-conceptos').DataTable();
        _.each(c_table.rows().nodes(), function(el_tr){
            _.each($(el_tr).find("td:contains('Si')"), function(td){
                $(td).addClass('concepto-si');
            })
        })
        
        $('#table-empleado-conceptos').show();
        desbloquear($('#table-empleado-conceptos').parents('.portlet-body'));
    })
    
    dt_datatable($('#table-empleado-conceptos'),{
        // deferRender: true,
        autoWidth: false,
        ajax: __AJAX_PATH__+'empleados/conceptos_table/',
        columnDefs : [
            {
                "targets": 1,
                "data": "ch_multiselect",
                "render": function ( data, type, full, meta ) {
                    return '<input type="checkbox" class="checkboxes" value="'+data+'" />';
                }
            },
            { "width": "20px",  "targets": "th-concepto" },
            { "width": "20px",  "targets": "th-concepto" },
            { "orderable": false, "targets": "th-concepto" },
            { "className": "text-center", "targets": [ 1 ] },
            { "className": "text-center", "targets": "th-concepto" },
            { "className": "text-center nowrap", "targets": [ 2, 3, 6] },
            { "className": "nowrap", "targets": [ 4, 5 ] }
        ],
    });
        
})


