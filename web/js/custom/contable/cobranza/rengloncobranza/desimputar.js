var dt_comprobantes_con_imputaciones_column_index = {
    id: 0,
    multiselect: 1,
    cliente: 2,
    contrato: 3,
    fecha: 4,
    tipo: 5,
    numero: 6,
    referencia: 7,
    total: 8,
    saldo: 9,
    id_comprobante: 10
};

dt_comprobantes_con_imputaciones = dt_datatable($('#table-comprobantes-con-imputaciones'), {
    ajax: {
        url: __AJAX_PATH__ + 'comprobanteventa/index_table_comprobantes_con_imputaciones/',
        data: function (d) {
            d.fecha_desde = $('#comprobantes_desde').val();
            d.fecha_hasta = $('#comprobantes_hasta').val();
            d.referencia = $('#adif_contablebundle_filtro_referencia').val();
        }
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_comprobantes_con_imputaciones_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_comprobantes_con_imputaciones_column_index.multiselect
            ]
        },
        {
            className: "hidden",
            targets: [
                dt_comprobantes_con_imputaciones_column_index.saldo,
                dt_comprobantes_con_imputaciones_column_index.id_comprobante
            ]
        }
    ]
});

var dt_cobros_por_comprobante_column_index = {
    id: 0,
    multiselect: 1,
    fecha_cobro: 2,
    fecha: 3,
    descripcion: 4,
    monto: 5,
    id_cobro: 6
};

dt_cobros_por_comprobante = dt_datatable($('#table-cobros-por-comprobante'), {
    ajax: {
        url: __AJAX_PATH__ + 'rengloncobranza/index_table_cobros_por_comprobante/',
        data: function (d) {
//            var id = null;
//            var seleccionados = dt_getSelectedRows('#table-comprobantes-con-imputaciones');
//            if (seleccionados.length == 1) {
//                id = seleccionados[0][7];
//            }
//            d.id_comprobante = id;
            d.id_comprobante = $('#id_comprobante_con_imputaciones').val();
        }
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_cobros_por_comprobante_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_cobros_por_comprobante_column_index.multiselect
            ]
        },
        {
            className: "hidden",
            targets: [
                dt_cobros_por_comprobante_column_index.id_cobro
            ]
        }
    ]
});

var dt_anticipos_manuales_column_index = {
    id: 0,
    multiselect: 1,
//    cuenta: 2,
    fecha: 2,
    referencias: 3,
    cliente: 4,
    saldo: 5,
    id_cliente: 6,
    id_anticipo: 7
};

dt_anticipos_manuales = dt_datatable($('#table-anticipos-manuales'), {
    ajax: {
        url: __AJAX_PATH__ + 'rengloncobranza/anticipos_detalle/'
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_anticipos_manuales_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_anticipos_manuales_column_index.multiselect
            ]
        },
        {
            className: "text-right nowrap",
            targets: [
                dt_anticipos_manuales_column_index.saldo
            ]
        },          
        {
            className: "hidden",
            targets: [
                dt_anticipos_manuales_column_index.id_cliente,
                dt_anticipos_manuales_column_index.id_anticipo
            ]
        }
    ]
});


$(document).ready(function () {

    initCurrencies();

    id_comprobante = null;
    
    fecha_para_asiento = null;

    //TAB 3
    $('#table-comprobantes-con-imputaciones .group-checkable').remove();
    $('#div-table-cobros-por-comprobante').hide();
    $('#div-header-cobros-por-comprobante').hide();
    $('#total-comprobante').val('0');
    $('#saldo-comprobante').val('0');
//    $('#table-comprobantes-con-imputaciones').click(function () {
//        var seleccionados = dt_getSelectedRows('#table-comprobantes-con-imputaciones');
//        var cantidad = seleccionados.length;
//        //var comprobante;
//        if (cantidad == 1 || cantidad == 2) {
//            if (cantidad == 1) {
//                dt_cobros_por_comprobante.DataTable().clear().draw();
//                comprobante_aux = $('#table-comprobantes-con-imputaciones .active');
//                $('#total-comprobante').val(seleccionados[0][5]);
//                $('#saldo-comprobante').val(seleccionados[0][6]);
//
//                dt_cobros_por_comprobante.DataTable().ajax.reload();
//                $('#div-header-cobros-por-comprobante').show();
//                $('#div-table-cobros-por-comprobante').show();
//
//            } else {
//                comprobante_aux.click();
//
//            }
//        } else {
//            $('#div-header-cobros-por-comprobante').hide();
//            $('#div-table-cobros-por-comprobante').hide();
//            $('#desimputar').hide();
//            //dt_cobros_por_comprobante.DataTable().ajax.reload();
//        }
//
//    });
    $('#table-comprobantes-con-imputaciones tbody').on( 'click', 'tr', function () {
        var id_comprobante = $(this).find('td').eq(9).html();
        var comprobantes_seleccionados = dt_getSelectedRows('#table-comprobantes-con-imputaciones');
        var cantidad_comprobantes_seleccionados = comprobantes_seleccionados.length;
        if (cantidad_comprobantes_seleccionados == 0) {
            
            $('#total-comprobante').val(0);
            $('#saldo-comprobante').val(0);
            $('#id_comprobante_con_imputaciones').val(0);
            dt_cobros_por_comprobante.DataTable().clear().draw();
            
            
            $('#id_comprobante_con_imputaciones').val(id_comprobante);            
            $('#total-comprobante').val($(this).find('td').eq(6).html());
            $('#saldo-comprobante').val($(this).find('td').eq(7).html());
            dt_cobros_por_comprobante.DataTable().ajax.reload();
            $('#div-header-cobros-por-comprobante').show();
            $('#div-table-cobros-por-comprobante').show();            
            
        } else {
            
            $('#total-comprobante').val(0);
            $('#saldo-comprobante').val(0);    
            $('#id_comprobante_con_imputaciones').val(0);
            dt_cobros_por_comprobante.DataTable().clear().draw();  
            $('#desimputar').hide();
            
            if (id_comprobante != comprobantes_seleccionados[0][7]) {
                
                $(this).parent().find('.active').find('td').eq(0).find('input').prop("checked", "");                
                $(this).parent().find('.active').removeClass('active');
                $(this).parent().find('.active').removeAttr('role');

                
                $('#id_comprobante_con_imputaciones').val(id_comprobante);
                $('#total-comprobante').val($(this).find('td').eq(6).html());
                $('#saldo-comprobante').val($(this).find('td').eq(7).html());
                dt_cobros_por_comprobante.DataTable().ajax.reload();
//                $('#div-header-cobros-por-comprobante').show();
//                $('#div-table-cobros-por-comprobante').show();                
            } else {
                
                $('#div-header-cobros-por-comprobante').hide();
                $('#div-table-cobros-por-comprobante').hide();
            
            }
        }

    } ); 

    $('#table-cobros-por-comprobante .group-checkable').remove();

    $('#table-cobros-por-comprobante').click(function () {
        var cantidad = dt_getSelectedRows('#table-cobros-por-comprobante').length;
        var cobro;
        if (cantidad == 1 || cantidad == 2) {
            if (cantidad == 1) {
                $('#desimputar').show();
                cobro_aux = $('#table-cobros-por-comprobante .active');
                cobro = dt_getSelectedRows('#table-cobros-por-comprobante');
            } else {
                cobro_aux.click();

            }
        } else
            $('#desimputar').hide();

    });

    $('#table-cobros-por-comprobante').on('draw.dt', function () {
        var cobros = dt_getRows('#table-cobros-por-comprobante');
        var cobro;
        var total = 0;
        //var error = true;
        for (var index = 0; index < cobros.length; index++) {
            //error = false;
            cobro = cobros[index];
            total += parseFloat(clearCurrencyValue(cobro[3]));
        }
        var saldo_comp = $('#saldo-comprobante').val();
        var total_comp = $('#total-comprobante').val();

		//console.debug("Total comprobantes = " + total_comp);
		//console.debug("Total = " + total);
		//console.debug("Saldo comp = " + saldo_comp);
		/*
        if ((cobros.length > 0) && ((total_comp - total).toFixed(2) != saldo_comp || total > total_comp)) {
            show_alert({title: 'Error', msg: 'Ha ocurrido un error, por favor actualice el listado de comprobantes', type: 'error'});
        }
		*/
    });

    $('#desimputar').on('click', function (e) {
        e.preventDefault();
        var seleccionados = dt_getSelectedRows($('#table-cobros-por-comprobante'));
        fecha_para_asiento = seleccionados[0][1];
        $.ajax({
            type: "POST",
            data: {id_cobro: seleccionados[0][4]},
            url: __AJAX_PATH__ + 'rengloncobranza/desimputar_cobro/'
        }).done(function (respuesta) {

            if (respuesta.status === 'OK') {

                $('#desimputar').hide();
                dt_anticipos_manuales.DataTable().ajax.reload();
                var comp_selec = dt_getSelectedRows($('#table-comprobantes-con-imputaciones'));

                showFlashMessage('success', respuesta.message);

                if (typeof respuesta.mensajeAsiento !== 'undefined') {
                    showFlashMessage('info', respuesta.mensajeAsiento, 0);
                }
                
                //$('.fecha-asiento-contable').html(fecha_para_asiento);

                var total = comp_selec[0][5];

                var saldo_comp = parseFloat($('#saldo-comprobante').val());

                var monto_cobro = parseFloat(seleccionados[0][3]);

                var nuevo_saldo = (saldo_comp + monto_cobro).toFixed(2);

                if (nuevo_saldo == total) {

                    dt_comprobantes_con_imputaciones.DataTable().ajax.reload();
                    dt_cobros_por_comprobante.DataTable().clear().draw();

                    $('#total-comprobante').val('0');
                    $('#saldo-comprobante').val('0');
                    $('#div-header-cobros-por-comprobante').hide();
                    $('#div-table-cobros-por-comprobante').hide();
                    $('#desimputar').hide();
                }

                $('#saldo-comprobante').val(nuevo_saldo.toString());

                dt_cobros_por_comprobante.DataTable().ajax.reload();

                initCurrencies();
            } else {
                showFlashMessage('danger', respuesta.message, 0);
                App.scrollTop();
            }
        });


    });

    $('#filtrar-comprobantes').on('click', function (e) {
        e.preventDefault();        
        dt_comprobantes_con_imputaciones.DataTable().ajax.reload();
        $('#div-table-cobros-por-comprobante').hide();
        $('#div-header-cobros-por-comprobante').hide();
        $('#total-comprobante').val('0');
        $('#saldo-comprobante').val('0');
        $('#id_comprobante_con_imputaciones').val(0);
        dt_cobros_por_comprobante.DataTable().clear().draw();        

    });

    $('#actualizar-comp-imp').on('click', function (e) {
        e.preventDefault();
        dt_cobros_por_comprobante.DataTable().clear().draw();
        $('#total-comprobante').val('0');
        $('#saldo-comprobante').val('0');
        $('#div-table-cobros-por-comprobante').hide();
        $('#div-header-cobros-por-comprobante').hide();
        $('#desimputar').hide();
        dt_comprobantes_con_imputaciones.DataTable().ajax.reload();
    });

    $('#table-anticipos-manuales .group-checkable').remove();

    $('#table-anticipos-manuales').click(function () {
        var cantidad = dt_getSelectedRows('#table-anticipos-manuales').length;
        var cobro;
        if (cantidad == 1 || cantidad == 2) {
            if (cantidad == 1) {
                $('#deshacer').show();
                anticipo_aux = $('#table-anticipos-manuales .active');
                anticipo = dt_getSelectedRows('#table-anticipos-manuales');
            } else {
                anticipo_aux.click();

            }
        } else
            $('#deshacer').hide();

    });

    $('#actualizar-ant-man').on('click', function (e) {
        e.preventDefault();
        dt_anticipos_manuales.DataTable().ajax.reload();
    });

    $('#deshacer').on('click', function (e) {
        e.preventDefault();
        var seleccionados = dt_getSelectedRows($('#table-anticipos-manuales'));
        fecha_para_asiento = seleccionados[0][0];
        $.ajax({
            type: "POST",
            data: {id_anticipo: seleccionados[0][5]},
            url: __AJAX_PATH__ + 'rengloncobranza/deshacer_anticipo/'
        }).done(function (respuesta) {

            if (respuesta.status === 'OK') {

                $('#deshacer').hide();

                showFlashMessage('success', respuesta.message);

                if (typeof respuesta.mensajeAsiento !== 'undefined') {
                    showFlashMessage('info', respuesta.mensajeAsiento, 0);
                }
                
                //$('.fecha-asiento-contable').html(fecha_para_asiento);

                dt_anticipos_manuales.DataTable().ajax.reload();

            } else {
                showFlashMessage('danger', respuesta.message);
            }

            App.scrollTop();

        });


    });

    initEditarFechaAsientoContableHandler();

});

/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    if (typeof $('.mensaje-asiento-contable').data('id-cobros') !== "undefined") {
        updateFechaCobrosFromAsientoContable();
    }
}

/**
 * 
 * @returns {undefined}
 */
function customDatepickerInit() {
    $('#fecha-asiento').datepicker("update", fecha_para_asiento);
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value
            .replace('$', '')
            .replace('%', '')
            .replace(/\./g, '')
            .replace(/\,/g, '.')
            .trim();
}