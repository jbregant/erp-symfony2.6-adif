var table;
$(document).ready(function() {
    table = $('#table-liquidacion-contribuciones');

    bloquear(table.parents('.portlet-body'));
    
    table.on('init.dt', function(e, settings, data) {        
        var column_ftn = table.DataTable().column;
        
        //      Sombrear columnas subtotales
        var bruto1_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("BRUTO 1")').index());
        var bruto2_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("BRUTO 2")').index());        
        var remunerativo_con_tope_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("TOTAL REMUNERATIVO CON TOPE")').index());
        var tnr_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("NO REMUNERATIVOS")').index());
        var contribuciones_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("TOTAL CONTRIBUCIONES")').index());
        
        table.DataTable().column(bruto1_idx).nodes().to$().addClass('hlt');
        table.DataTable().column(bruto2_idx).nodes().to$().addClass('hlt');
        table.DataTable().column(remunerativo_con_tope_idx).nodes().to$().addClass('hlt');        
        table.DataTable().column(tnr_idx).nodes().to$().addClass('hlt');
        table.DataTable().column(contribuciones_idx).nodes().to$().addClass('hlt');
        
    });

    table.on('xhr.dt', function(e, settings, data) {
        desbloquear(table.parents('.portlet-body'));
    });

    dt_datatable($('#table-liquidacion-contribuciones'),
        {
            ajax: __AJAX_PATH__ + 'liquidaciones/liquidacion_contribuciones_table',
            columnDefs: [
                {
                    "targets": 1,
                    "data": "ch_multiselect",
                    "render": function(data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {"className": "text-center", "targets": [1]},
                {"className": "text-right", "targets": "th-concepto"},
                {"className": "text-center nowrap", "targets": [2]},
                {"className": "nowrap", "targets": [3, 4, 5, 6, 7]}
            ],
            "pageLength": 100
        });    
});
