var table;
var dt_liquidacion;

$(document).ready(function() {
    table = $('#table-liquidacion');

//    bloquear(table.parents('.portlet-body'));

    table.on('init.dt', function(e, settings, data) {
//        desbloquear(table.parents('.portlet-body'));
        
        //    Setear suma de netos
        //var sumaNetos = _.reduce(table.DataTable().column($('th.th-concepto:last')).data().toArray(), function(memo, num){ return memo + parseFloat(num.replace(".","").replace(",",".")); }, 0);
		
		var todosNetos = table.DataTable().column($('th.th-concepto:last')).data().toArray();
		//console.debug(todosNetos);
		var sumaNetos = 0;
		$(todosNetos).each(function(key, val){
			var neto = parseFloat(val.replace(".","").replace(".","").replace(",","."));
			//console.debug(neto);
			sumaNetos = sumaNetos + neto;
		});
		
        console.debug("Neto total = " + sumaNetos);
        $('#total_neto').text($('<h>'+sumaNetos.toFixed(2)+'</h>').priceFormat({ prefix: '', centsSeparator: ',',thousandsSeparator: '.'}).text());

        var column_ftn = table.DataTable().column;

//      Sombrear columnas subtotales
        var bruto1_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("BRUTO 1")').index());
        var bruto2_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("BRUTO 2")').index());        
        var remunerativo_con_tope_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("TOTAL REMUNERATIVO CON TOPE")').index());
        var td_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("TOTAL DESCUENTOS")').index());
        var tnr_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("TOTAL NO REMUNERATIVOS")').index());
        var neto_idx = column_ftn.index('fromVisible', table.find('thead tr th:contains("NETO")').index());
        

        table.DataTable().column(bruto1_idx).nodes().to$().addClass('hlt');
        table.DataTable().column(bruto2_idx).nodes().to$().addClass('hlt');
        table.DataTable().column(remunerativo_con_tope_idx).nodes().to$().addClass('hlt');
        table.DataTable().column(td_idx).nodes().to$().addClass('hlt');
        table.DataTable().column(tnr_idx).nodes().to$().addClass('hlt');
        table.DataTable().column(neto_idx).nodes().to$().addClass('hlt');
    });
    
    dt_liquidacion = dt_datatable($('#table-liquidacion'), {
        ajax: __AJAX_PATH__ + 'liquidaciones/liquidacion_table',
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
            {"className": "nowrap", "targets": [3, 4, 5, 6, 7, 8, 9, 10]},
            {
                "className": "text-center nowrap",
                "targets": "th-acciones",
                "render": function(data, type, full, meta) {
                    return '<a href="' + __AJAX_PATH__ + 'empleados/' + data + '/excel/1" '
                            + 'class="btn btn-xs dark tooltips" '
                            + 'data-original-title="Impuesto a las Ganancias">'
                            + '<i class="fa fa-letter">IG</i></a>';
                }
            }
        ],
        "pageLength": 100
    });
            
    dt_liquidacion = dt_liquidacion.DataTable();

    $('#btn-cerrar-liquidacion').on('click', function() {
        /*
        if ($(this).attr('es_sac')){
            bloquear();
            var post_href = $(this).attr('href_cerrar')
            open_window('POST', post_href, {es_sac : true});
            return;
        }
        */
        $.ajax({
            url: __AJAX_PATH__ + 'liquidaciones/liquidacion/form_cerrar',
            type: 'POST'
        }).done(function(rHTML) {
            show_dialog({
                titulo: 'Cierre de liquidaci&oacute;n',
                contenido: rHTML,
                callbackCancel: function() {
                    return;
                },
                callbackSuccess: function() {
                    $('#fecha_ultimo_aporte').val($('#fecha_ultimo_aporte_dp').datepicker('getDate').getFullYear() + '-' + ($('#fecha_ultimo_aporte_dp').datepicker('getDate').getMonth() + 1) + '-' + '01');
                    if ($('#form_liquidacion_cierre').valid()) {
                        $('#form_liquidacion_cierre').submit();
                    } else {
                        return false;
                    }
                }
            });

            $('#form_liquidacion_cierre').parents('.modal-dialog').css('width', '60%');

            // BANCOS
            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'bancos/lista_bancos',
                success: function(data) {
                    for (var i = 0, total = data.length; i < total; i++) {
                        $('#banco_aporte').append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                    }
                    $('#banco_aporte').select2();
                }
            });
            
            if ($(this).attr('es_sac')){
                $('#fecha_ultimo_aporte,#banco_aporte,#fecha_deposito_aporte').removeClass('required').removeAttr('required');
            } else {
                $('#fecha_ultimo_aporte,#banco_aporte,#fecha_deposito_aporte').attr('required', 'required');
            }
            
            $('#form_liquidacion_cierre .datepicker:not(#fecha_ultimo_aporte_dp)').each(function() {
                initDatepicker($(this));
            });

            initDatepicker($('#fecha_ultimo_aporte_dp'), {
                format: "MM yyyy",
                viewMode: "months",
                minViewMode: "months"
            });
        }); 
    });

    $('#btn_imprimir_recibos').on('click', function(e) {
        e.preventDefault();
        bloquear();
        var table = $('#table-liquidacion');

        var ids = dt_getSelectedRowsIds(table);
        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un empleado para imprimir su recibo.'});
            desbloquear();
            return;
        }

        var options = {
            ids: JSON.stringify(ids.toArray())
        };

        open_window('POST', __AJAX_PATH__ + 'liquidaciones/recibos/imprimir', options, '_blank');

        desbloquear();

        e.stopPropagation();
    });
       
    
    $('#table-liquidacion').on('selected_element', function (e, cantidad) {
        $('#cant_seleccionados').text(cantidad);
        
        $('#cant_seleccionados').parent().removeClass('flash animated').addClass('flash animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
            $(this).removeClass('flash animated');
        });
        
        //cantidad > 0 ? $('.hide-if-non-selected').fadeIn(200) : $('.hide-if-non-selected').fadeOut(200);
    });
    
//    $('#btn_ver_columnas').on('click', function(){
//        var todas = $(this).hasClass('todas');
//        $(this).removeClass('resumida todas');
//        if (todas){
////          Ocultar
//            dt_liquidacion.columns('th[ver_columna!="1"]').visible( false );
//            $(this).addClass('resumida');
//        } else {
////          Mostrar todas
//            dt_liquidacion.columns('th[ver_columna!="1"]').visible( true );
//            $(this).addClass('todas');
//        }
//        //dt_liquidacion.columns.adjust().draw( false );
//    })

    $('#imprimir_recibos_sueldos_session').on('click', function() {
        
        $.ajax({
            url: __AJAX_PATH__ + 'liquidaciones/liquidacion/form_imprimir_recibos_session',
            type: 'POST'
        }).done(function(rHTML) {
            show_dialog({
                titulo: 'Imprimir recibos',
                contenido: rHTML,
                callbackCancel: function() {
                    return;
                },
                callbackSuccess: function() {
                    if ($('#form_imprimir_recibos_session').valid()) {
                        $('#form_imprimir_recibos_session').submit();
                        desbloquear();
                    } else {
                        return false;
                    }
                }
            });
            
        }); 
    });
    
});
