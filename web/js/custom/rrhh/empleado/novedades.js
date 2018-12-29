var dt_novedades;
var dt_novedades_data = [];
var dt_novedades_data_con_valor = [];
var tchild_template = '\
    <div class="table-responsive">\
        <table id="table-child" class="table table-bordered table-striped table-condensed flip-content">\
            <thead><tr>\
                <th width="40%">Novedad</th>\
                <th width="25%">F&oacute;rmula</th>\
                <th width="10%">Monto</th>\
                <th width="10%">Valor</th>\
                <th width="15%">Liquidaci&oacute;n</th>\
            </tr></thead>\
            <tbody>\
            _____BODY____\
            </tbody>\
        </table>\
    </div>';

$(document).ready(function(){
    $('#table-empleado-novedades').on('init.dt', function ( e, settings, data ) {
        dt_novedades = $('#table-empleado-novedades').DataTable();
        
        dt_novedades.rows().nodes().each(function(row){
            var cell_value = dt_novedades.cell(row,0).data();
            if (cell_value.novedades_codigos.length > 0){
                _.each(cell_value.novedades_codigos, function(novedad_codigo, index_nc){
                    dt_novedades.cell(row,$('th[c_id='+novedad_codigo+']')).data(cell_value.novedades_valores[index_nc]);
                })
                $(row).addClass('con-valor')
            } else {
                $(row).addClass('sin-valor');
            }
        })
        
        dt_novedades_data_con_valor = dt_novedades.rows('.con-valor').data();
        dt_novedades_data = dt_novedades.rows().data();
        
        $('#table-empleado-novedades').show();
        desbloquear($('#table-empleado-novedades').parents('.portlet-body'));
    })
        
    dt_datatable($('#table-empleado-novedades'),{
        autoWidth: false,
        ajax: __AJAX_PATH__+'empleados/novedades_table/',
        columnDefs : [
            {
                "targets": 1,
                "data": "ch_multiselect",
                "render": function ( data, type, full, meta ) {
                    return '<input type="checkbox" class="checkboxes" value="'+data+'" />';
                }
            },
            {
                "targets": 2,
                "data": "ver_historial",
                "render": function ( data, type, full, meta ) {
                    return '<button class="tooltips btn btn-sm btn-success" data-original-title="Ver historial de novedades"><i class="fa fa-plus"></i></button>';
                }
            },
            { "width": "20px",  "targets": "th-ver-historial" },
            { "width": "20px",  "targets": "th-concepto" },
            { "orderable": false, "targets": "th-concepto" },
            { "className": "text-center", "targets": [ 1 ] },
            { "className": "historico text-center", "targets": "th-ver-historial" },
            { "className": "text-center", "targets": "th-concepto" },
            { "className": "text-center nowrap", "targets": [ 3, 4, 7] },
            { "className": "nowrap", "targets": [ 5, 6 ] }
        ],
    });
    
    $('#btn_filtrar_empleados_todos').on('click', function(){
        $('#btn_filtrar_empleados_con_valor').show();
        $(this).hide();
        buscarEmpleadosConValor();
    })
    
    $('#btn_filtrar_empleados_con_valor').on('click', function(){
        $('#btn_filtrar_empleados_todos').show();
        $(this).hide();
        buscarEmpleadosConValor(true);
    })
    
    
//    $(document).on('click','#table-child > tbody > tr', function (e) {
//        e.preventDefault();
//        e.stopPropagation();
//    })
    
    $(document).on('click', '#table-empleado-novedades > tbody > tr > td.historico', function (e) {
        e.preventDefault();
        if ($(e.target).is('a')){
            return;
        }
        var tr = $(this).closest('tr');
        var row = dt_novedades.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            tr.find('.historico > button > i').toggleClass('fa-plus fa-minus');
        } else {
            tr.find('.historico > button > i').toggleClass('fa-minus fa-plus');
            // Open this row
            $.ajax({
                url: __AJAX_PATH__+'empleado_novedades/historico/'+row.data()[0].id,
                type: 'POST'
            }).done(function(novedades){
                var str_table = '';
                _.each(novedades, function(novedad){
                    str_table += '<tr><td >'+pad(novedad.conceptoVersion.codigo,4)+' - '+novedad.conceptoVersion.descripcion+'</td>';
                    str_table += '<td >'+novedad.conceptoVersion.formula+'</td>';
                    str_table += '<td  class="text-right">'+$('<h>'+novedad.monto+'</h>').priceFormat({ prefix: '', centsSeparator: ',',thousandsSeparator: '.'}).text()+'</td>';
                    str_table += '<td  class="text-right">'+$('<h>'+novedad.empleadoNovedad.valor+'</h>').priceFormat({ prefix: '', centsSeparator: ',',thousandsSeparator: '.'}).text()+'</td>';
                    str_table += '<td ><a target="_blank" href="'+__AJAX_PATH__+'liquidaciones/liquidacion/'+novedad.liquidacionEmpleado.liquidacion.id+'">N&ordm; '
                            + novedad.liquidacionEmpleado.liquidacion.numero + ' - '
                            + month((new Date(novedad.liquidacionEmpleado.liquidacion.fechaAlta.date.replace(/-/g,'/'))).getMonth())
                            +'</a></td></tr>';
                });
                
                row.child(tchild_template.replace('_____BODY____',str_table)).show();
                row.child().find('.table-responsive').parent().css('padding','10px');

                tr.addClass('shown');
            });
        }
        e.preventDefault();
        e.stopPropagation();
    });
})

function formatNovedadesHistoricas(){
    
}

function buscarEmpleadosConValor(mostrarConValor){
    if (mostrarConValor){
        dt_novedades.clear().rows.add(dt_novedades_data_con_valor).draw();
    } else {
        dt_novedades.clear().rows.add(dt_novedades_data).draw();
    }
}


