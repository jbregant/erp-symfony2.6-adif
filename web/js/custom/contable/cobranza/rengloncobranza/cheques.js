var dt_cheques_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    banco: 3,
    numero: 4,
    monto: 5,
    fechaRegistro: 6,
    numeroRecibo: 7,
    banco2: 8,
    fecha2: 9,
    id_cheque: 10,
    id_cuenta: 11
};

dt_cheques = dt_datatable($('#table-cheques-para-depositar'), {
    ajax: {
        url: __AJAX_PATH__ + 'rengloncobranza/index_cheques_para_depositar/',
        data: function (d) {
            d.fecha_desde = $('#cheques_desde').val();
            d.fecha_hasta = $('#cheques_hasta').val();
        }
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_cheques_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_cheques_column_index.multiselect
            ]
        },
        {
            className: "nowrap",
            targets: [
                dt_cheques_column_index.numeroRecibo,
                dt_cheques_column_index.fecha2
            ]
        },
        {
            className: "hidden",
            targets: [
                dt_cheques_column_index.id_cheque,
                dt_cheques_column_index.id_cuenta
            ]
        }
    ]
});

$(document).ready(function () {
    
    fecha_para_asiento = null;
        
    $('#table-cheques-para-depositar .group-checkable').remove();
    initDatepickers();
    $('#fecha').datepicker('setEndDate', getCurrentDate());

    cuentasHTML = "<option value='0' selected='selected'>-- Elija una cuenta --</option>";
    $.each(cuentas, function (id, data) {
        cuentasHTML = cuentasHTML + "<option value='" + id + "'>" + cuentas[id] + "</option>";

    });
    selectCuentasHTML = "<select id='cuenta' name='select_bancos_multiple' class='required form-control choice'>" + cuentasHTML + "</select>";

    $('#div_cuenta').append(selectCuentasHTML);
    $('#div_cuenta select').select2();

    $('#actualizar').on('click', function (e) {
        e.preventDefault();
        dt_cheques.DataTable().ajax.reload();
    });

    $('#depositar').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var fecha = $('#fecha');
        var cuenta = $('#cuenta');
        if (fecha.val() == '' || cuenta.val() == '') {
            show_alert({title: 'Error', msg: 'Debe ingresar la fecha y la cuenta (ambos campos son obligatorios)', type: 'error'});
            desbloquear();
            return;
        } else {
            fecha_para_asiento = fecha.val();
            var ids = dt_getSelectedRowsIds($('#table-cheques-para-depositar'));
            $.ajax({
                type: "POST",
                data: {depositar: 1, fecha: fecha_para_asiento, cuenta: cuenta.val(), ids_cheque: JSON.stringify(ids.toArray())},
                url: __AJAX_PATH__ + 'rengloncobranza/depositar_cheque/'
            }).done(function (respuesta) {
                if (respuesta.status === 'OK') {
                    fecha.val("");
                    $('#cuenta').select2('val', '0');
                    $('#div_cuenta').hide();
                    $('#div_fecha').hide();
                    $('#depositar').hide();
                    dt_cheques.DataTable().ajax.reload();

                    if (typeof respuesta.mensajeAsiento !== 'undefined') {
                        showFlashMessage('info', respuesta.mensajeAsiento, 0);
                    }
                    
                    $('.fecha-asiento-contable').html(fecha_para_asiento);

                    showFlashMessage('success', respuesta.message, 0);

                    desbloquear();
                } else {

                    showFlashMessage('danger', respuesta.message, 0);

                    desbloquear();
                    return;
                }
            });
        }
    });
//    $($('#table-cheques-para-depositar')).on('change', '.' + _select_all_checkbox_class, function (e) {
//        e.preventDefault();
//        var checked = $(this).is(":checked");
//        dt_select_all(table, checked, false);
//        dt_actualizar_seleccionados(table);
//    });
    $('#table-cheques-para-depositar').click(function () {
        //alert('hola');
        var seleccionados = dt_getSelectedRows('#table-cheques-para-depositar');
        var cantidad = seleccionados.length;
        var operacion;
        var cheque;
        var cheque_op;
        var index;
        $('#fecha').val("");
        $('#cuenta').select2('val', '0');
        $('#div_cuenta').hide();
        $('#div_fecha').hide();
        $('#depositar').hide();
        $('#deshacer').hide();
        if (cantidad > 0) {
            if (seleccionados[0][6] == '-') {
                operacion = 1;
            } else {
                operacion = 2;
            }
            index = 0;
            fecha_para_asiento = seleccionados[0][7];
            var cuenta = seleccionados[0][9]; 
            for (var index = 0; index < seleccionados.length; index++) {
                if (operacion != 3 && operacion != 4) {
                    cheque = seleccionados[index];
                    if (cheque[6] == '-') {
                        cheque_op = 1;
                    } else {
                        cheque_op = 2;  
                    }    
                    if (cheque_op != operacion) {
                        operacion = 3;
                    } else {
                        if (fecha_para_asiento != cheque[7] || cuenta != cheque[9]) {
                            operacion = 4;
                        }                            
                    }    
                }
            }
            verificar_botones(operacion);
        }
    });

    $('#deshacer').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var ids = dt_getSelectedRowsIds($('#table-cheques-para-depositar'));
        
        $.ajax({
            type: "POST",
            data: {depositar: 0, ids_cheque: JSON.stringify(ids.toArray())},
            url: __AJAX_PATH__ + 'rengloncobranza/depositar_cheque/'
        }).done(function (respuesta) {
            if (respuesta.status === 'OK') {
                $('#deshacer').hide();
                dt_cheques.DataTable().ajax.reload();

                if (typeof respuesta.mensajeAsiento !== 'undefined') {
                    showFlashMessage('info', respuesta.mensajeAsiento, 0);
                }
                
                $('.fecha-asiento-contable').html(fecha_para_asiento);

                showFlashMessage('success', respuesta.message, 0);

                desbloquear();
            } else {

                showFlashMessage('danger', respuesta.message, 0);

                desbloquear();
                return;
            }
        });

    });

    $('#filtrar-cheques').on('click', function (e) {
        e.preventDefault();
//        $desde = $('#cheques_desde');
//        $hasta = $('#cheques_hasta');
        dt_cheques.DataTable().ajax.reload();

    });


    initEditarFechaAsientoContableHandler();

});


function verificar_botones(operacion) {

    switch (operacion) {
        case 1:
            $('#div_cuenta').show();
            $('#div_fecha').show();
            $('#depositar').show();
            $('#deshacer').hide();
            break;
        case 2:
            $('#div_cuenta').hide();
            $('#div_fecha').hide();
            $('#depositar').hide();       
            $('#deshacer').show();
            break;
        case 3:
            show_alert({title: 'Error', msg: 'No puede seleccionar a la vez cheques para depositar y depositados', type: 'error'});
            desbloquear();
            break;
        case 4:
            show_alert({title: 'Error', msg: 'Para deshacer dep&oacute;sitos, los cheques seleccionados tienen que tener la misma cuenta bancaria y la misma fecha de dep&oacute;sito', type: 'error'});
            desbloquear();
            break;            
    }
    return;

}


/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    if (typeof $('.mensaje-asiento-contable').data('id-cheques') !== "undefined" && $('.mensaje-asiento-contable').data('es-deposito') === 1) {
        updateFechaChequesFromAsientoContable();
    }
}

/**
 * 
 * @returns {undefined}
 */
function customDatepickerInit() {
    $('#fecha-asiento').datepicker("update", fecha_para_asiento);
}