//var options = {
//        "searching": false,
//        "ordering": false,
//        "info": false,
//        "paging": false
//    };
//
//$(document).ready(function () {
//
//    configurarTablasContrato();
//});
//    
//function configurarTablasContrato() {
//    $('.table').each(function () {
//        dt_init($(this), options);
//    });
//    $('.header-contrato').each(function () {
//        var id = $(this).prop("id");
//        $(this).insertBefore($('#contrato'+id));
//        $(this).show();
//    });
//}

var dt_cupones; // Empleado DT
/** /
var dt_cupones_column_index = {
    id: 0,
    multiselect: 1
}
var indexVisibleToDT = function (column_index) {
    return dt_cupones.column(column_index).index();
}

var indexDTToVisible = function (column_index) {
    return dt_cupones.column(column_index).index('visible');
}
/**/


$(document).ready(function () {
    $('#td_total_anticipos').text($('#total_anticipos_format_hidden').val());
    
	$(".dataTables_info").remove();
	
    $('#checkbox-detalle').on('click', function () {
        if ($('#checkbox-detalle:checked').size() > 0) {
            $('.ocultable').removeClass('hidden');
        } else {
            $('.ocultable').addClass('hidden');
        }
    });

    $('.btn-clear-filters').remove();
    
    $('.mostrar-contrato').on('click', function (e) {
            e.preventDefault();
            var id = $(this).prop('id');
            var div_tabla = $('#detalle-contrato-'+id);
            show_alert({title:'Detalle de contrato', msg:div_tabla.html()});

    }); 
//    var total_contratos = $('#total_contratos_hidden').val();
//    var total_anticipos = $('#total_anticipos_hidden').val();
//    
//    var saldo_total = parseFloat(total_contratos)-parseFloat(total_anticipos);
    $('#total_cc_cliente').html($('#total_cuenta_corriente_hidden').val());
//    $('#total_cc_cliente').html(saldo_total.toString());
//    
//    $('.money-format').each(function () {
//        $(this).autoNumeric('init', {vMin: '-999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
//    });
    var id_contratos_cupones_garantia = $('#id_cotratos_cupones_garantia_hidden').val();
    var str_id_contratos_cupones_garantia = id_contratos_cupones_garantia.split(',');
    var id_contratos = $('#id_contratos_hidden').val();
	var id_contratos_cupones = $("#id_contratos_cupones_hidden").val();
//    var saldos = $('#saldos_hidden').val();
    var str_id_contratos = id_contratos.split(',');
	var str_id_contratos_cupones = id_contratos_cupones.split(',');
	
//    var str_saldos = saldos.split(',');
    var id;
    var saldo;
    for (var index = 0; index < str_id_contratos.length; index++) {
        id = str_id_contratos[index].toString();
        saldo = $('#saldo_contrato_id_'+id).val();//str_saldos[index].toString();

        $('#total_contrato_'+id).html('<i>'+saldo+'</i>');
        if (saldo == '$ 0,00') {
            $('#fila_contrato_'+id).addClass('hidden ocultable');
        }
		
    }
	for (var index = 0; index < str_id_contratos_cupones.length; index++) {
        id = str_id_contratos_cupones[index].toString();
        $('#fila_contrato_cupon_'+id).addClass('hidden ocultable');
    }
	
    $('#td_total_anticipos').html('<i>'+$('#total_anticipos_hidden').val()+'</i>');
    $('#td_total_comprobantes_sin_contrato').html('<i>'+$('#total_comprobantes_sin_contrato_hidden').val()+'</i>');
    $('#td_total_cupones_sin_contrato').html('<i>'+$('#total_cupones_sin_contrato_hidden').val()+'</i>');
    $('#td_total_cupones_con_contrato').html('<i>'+$('#total_cupones_con_contrato_hidden').val()+'</i>');
    
    var id_comprobantes = $('#id_comprobantes_hidden').val();    
    var str_id_comprobantes = id_comprobantes.split(','); 
    for (var index = 0; index < str_id_comprobantes.length; index++) { 
        id = str_id_comprobantes[index].toString();
        $('.comprobante_'+id).addClass('hidden ocultable');
    }  
    for (var index = 0; index < str_id_contratos_cupones_garantia.length; index++) { 
        id = str_id_contratos_cupones_garantia[index].toString();
        $('#garantia_'+id).addClass('hidden ocultable');        
    }    


/* * /
    dt_cupones = dt_datatable($('#cupones_cliente'), {
        ajax: __AJAX_PATH__ + 'cliente/cuentacorrientedetalletotal/'+$('#id_cliente_cc'),
        columnDefs: [
            {
                "targets": dt_cupones_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                }
            },
        ]
    });
    /**/

    //dt_cupones = dt_cupones.DataTable();



    $('#btn_saldar a[cuenta-corriente-cliente-table]').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var table = $('#cupones_cliente');
        var ids = new Array;
        var todos;
        switch ($(this).data('que-exportar')) {
            case _exportar_todos:
                todos = 'checkbox'
                break;
            case _exportar_seleccionados:
                todos = 'checked';
                break;
        }

        $('table#cupones_cliente input:'+todos).each(function(i){
            ids.push($(this).attr('id'));
        });

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un cupon para saldar.'});
            desbloquear();
            return;
        }

        var seleccionados = 'Se subiran todos ('+ids.length+') los cupones';
        var clone = '';
        if (todos == 'checked'){
            clone = $('table#cupones_cliente').clone();
            /* * /
            clone.find('tbody tr').each(function(tr){
                //$(this).remove();
            });
            clone.find('.replace-inputs .filter').each(function(tr){
                //$(this).remove();
            });
            /**/

            htmlTable = '<table class="table table-bordered table-striped table-condensed flip-content" width="75%" cellpadding="0" cellspacing="0" border="0"><thead><tr><td><b>Fecha</b></td><td><b>Saldo</b></td></tr></thead><tbody></tbody></table>';
            table = $(htmlTable);
            for (var index = 0; index < ids.length; index++) {
                $('table#cupones_cliente').find('tbody tr').each(function(e){
                    if ( $(this).hasClass('anulado') && !$(this).hasClass('hidden')) {
                        if ( $(this).attr('id') == ids[index] ) {
                            tr = '<tr></tr>'
                            numero = $('table#cupones_cliente').find('#'+ids[index]+'_fechaComprobante').clone();
                            saldo = $('table#cupones_cliente').find('#'+ids[index]+'_saldo').clone();
                            cupon = $(tr);
                            cupon.append(numero);
                            cupon.append(saldo);
                            table.find('tbody').append(cupon);
                        }
                    }
                })
            }
            seleccionados = table;
        }

        desbloquear();
        show_dialog({
            titulo: 'Saldar cupones',
            contenido: seleccionados,
            callbackCancel: function () {
                desbloquear();
                return;
            },
            callbackSuccess: function () {
                bloquear();
                $.ajax({
                    url: __AJAX_PATH__ + 'cliente/cuentacorrientedetalletotal/subircupones/',
                    type: 'POST',
                    data: {
                        ids: jQuery.makeArray(ids)
                    }
                }).done(function (r) {
                    if (r.result === 'OK') {
                        location.reload();
//                        location.href = __AJAX_PATH__ + 'cliente/cuentacorrientedetalletotal/'+$('#id_cliente_cc').val()+'/'
                    } else {
                        show_alert({msg: r.msg, title: 'Error en la subida de cupones', type: 'error'});
                        desbloquear();
                    }
                }).error(function (e) {
                    show_alert({msg: 'Ocurri&oacute; un error al subir cupones. Intente nuevamente.', title: 'Error en ajuste de cuenta', type: 'error'});
                });
            }
        });

        $('.bootbox').removeAttr('tabindex');
        e.stopPropagation();
    });
});