var iniciarComprobantes = false;

var dt_banco_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    numeroTransaccion: 3,
    codigo: 4,
    monto: 5,
    esManual: 6,
    id_banco: 7,
    codigo_real: 8,
    tipo: 9
//    cliente: 9
};

dt_banco = dt_datatable($('#table-banco'), {
    ajax: {
        url: __AJAX_PATH__ + 'rengloncobranza/index_table_banco/',
        data: function (d) {
            d.id_cuenta = $('#id_cuenta_bancaria').val();
        }
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_banco_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_banco_column_index.codigo,
            "render": function (data, type, full, meta) {
                if (full[dt_banco_column_index.codigo] == '-' || isNaN(full[dt_banco_column_index.codigo])) {
                    return full[dt_banco_column_index.codigo];
                } else {
                    return full[dt_banco_column_index.codigo] + ' <a href="" title="Ver detalle" class="buscar-cliente-para-codigo" target="" data-codigo="' + full[dt_banco_column_index.codigo] + '"><i class="fa fa-search"></i></a>';
                }    
            }
        },         
        {
            className: "text-center",
            targets: [
                dt_banco_column_index.multiselect,
                dt_banco_column_index.codigo
            ]
        },
        {
            className: "text-right nowrap",
            targets: [
                dt_banco_column_index.monto
            ]
        },
        {
            className: "text-center nowrap",
            targets: [        
                dt_banco_column_index.numeroTransaccion
            ]
        },                
        {
            className: "hidden",
            targets: [
                dt_banco_column_index.esManual,
                dt_banco_column_index.id_banco,
                dt_banco_column_index.codigo_real,
                dt_banco_column_index.tipo
            ]
        }
    ]
});

dt_banco_a_imputar = dt_datatable($('#table-banco-a-imputar'), {
    ajax: {
        url: __AJAX_PATH__ + 'rengloncobranza/index_table_banco_a_imputar/',
        data: function (d) {
            var id = null;
            d.id_cuenta = $('#id_cuenta_bancaria').val();
        }
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_banco_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_banco_column_index.codigo,
            "render": function (data, type, full, meta) {
                if (full[dt_banco_column_index.codigo] == '-' || isNaN(full[dt_banco_column_index.codigo])) {
                    return full[dt_banco_column_index.codigo];
                } else {
                    return full[dt_banco_column_index.codigo] + ' <a href="" title="Ver detalle" class="buscar-cliente-para-codigo" target="" data-codigo="' + full[dt_banco_column_index.codigo] + '"><i class="fa fa-search"></i></a>';
                }  
            }
        },        
        {
            className: "text-center",
            targets: [
                dt_banco_column_index.multiselect,
                dt_banco_column_index.codigo
            ]
        },
        {
            className: "text-right nowrap",
            targets: [            
                dt_banco_column_index.monto
            ]
        },
        {
            className: "text-center nowrap",
            targets: [        
                dt_banco_column_index.numeroTransaccion
            ]
        },         
        {
            className: "hidden",
            targets: [
                dt_banco_column_index.esManual,
                dt_banco_column_index.id_banco,
                dt_banco_column_index.codigo_real,
                dt_banco_column_index.tipo
            ]
        }
    ]
});

var dt_anticipos_column_index = {
    id: 0,
    multiselect: 1,
    cliente: 2,
    fecha: 3,
    referencias: 4,
    saldo: 5,
    id_cliente: 6,
    id_anticipo: 7,
    monto: 8
};

dt_anticipos = dt_datatable($('#table-anticipos'), {
    ajax: {
        url: __AJAX_PATH__ + 'rengloncobranza/index_table_anticipos/',
        data: function (d) {
            d.id_cuenta = $('#id_cuenta_bancaria').val();
        }
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_anticipos_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_anticipos_column_index.saldo,
            "render": function (data, type, full, meta) {
                return full[dt_anticipos_column_index.saldo].saldo + ' <a href="" title="Ver detalle" class="link-detalle-saldo-anticipo" target="" data-id-anticipo="' + full[dt_anticipos_column_index.saldo].id + '"><i class="fa fa-search"></i></a>';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_anticipos_column_index.multiselect
            ]
        },
        {
            className: "text-right nowrap",
            targets: [
                dt_anticipos_column_index.saldo
            ]
        },
        {
            className: "hidden",
            targets: [
                dt_anticipos_column_index.id_cliente,
                dt_anticipos_column_index.id_anticipo,
                dt_anticipos_column_index.monto
            ]
        }
    ]
});


var dt_comprobantes_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    tipo: 3,
    numero: 4,
    vencimiento: 5,
    cliente: 6,
    contrato: 7,
    codigo: 8,
    total: 9,
    saldo: 10,
    id_cliente: 11,
    id_comprobante: 12
};

dt_comprobantes = dt_datatable($('#table-comprobantes'), {
	
    ajax: {
        url: __AJAX_PATH__ + 'comprobanteventa/index_table_comprobantes/',
        data: function (d) {
            d.fecha_desde = $('#comprobantes_desde').val();
            d.fecha_hasta = $('#comprobantes_hasta').val();
			d.fake = !iniciarComprobantes;
        }
		
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_comprobantes_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_comprobantes_column_index.multiselect,
                dt_comprobantes_column_index.cliente
            ]
        },
        {
            className: "text-center nowrap",
            targets: [
                ,
                dt_comprobantes_column_index.numero,
                dt_comprobantes_column_index.contrato
            ]
        },        
        {
            className: "text-right nowrap",
            targets: [
                dt_comprobantes_column_index.saldo
            ]
        },
        {
            className: "hidden",
            targets: [
                dt_comprobantes_column_index.vencimiento,
                dt_comprobantes_column_index.codigo,
                dt_comprobantes_column_index.total,
                dt_comprobantes_column_index.id_cliente,
                dt_comprobantes_column_index.id_comprobante
            ]
        }
    ]
});

var dt_notas_credito_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    numero: 3,
    cliente: 4,
    contrato: 5,
    saldo: 6,
    id_cliente: 7,
    id_comprobante: 8,
    total: 9
};

dt_notas_credito = dt_datatable($('#table-notas-credito'), {
	
    ajax: {
        url: __AJAX_PATH__ + 'comprobanteventa/index_table_notas_credito/',
        data: function (d) {
            d.fecha_desde = $('#comprobantes_desde').val();
            d.fecha_hasta = $('#comprobantes_hasta').val();
			d.fake = !iniciarComprobantes;
        }
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_notas_credito_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_notas_credito_column_index.multiselect
            ]
        },
        {
            className: "text-right nowrap",
            targets: [
                dt_notas_credito_column_index.saldo
            ]
        },
        {
            className: "hidden",
            targets: [
                dt_notas_credito_column_index.id_cliente,
                dt_notas_credito_column_index.id_comprobante,
                dt_notas_credito_column_index.total
            ]
        }
    ]
});

var dt_cheques_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    banco: 3,
    numero: 4,
    observacion: 5,
    monto: 6,
    id_cheque: 7
};

dt_cheques = dt_datatable($('#table-cheques'), {
    ajax: {
        url: __AJAX_PATH__ + 'rengloncobranza/index_table_cheques/',
        data: function (d) {
            d.tab = 1;
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
                dt_cheques_column_index.multiselect,
                dt_cheques_column_index.observacion
            ]
        },            
        {
            className: "text-right nowrap",
            targets: [
                dt_cheques_column_index.monto
            ]
        },   
        {
            className: "hidden",
            targets: [
                dt_cheques_column_index.id_cheque
            ]
        }
    ]
});

dt_cheques_a_imputar = dt_datatable($('#table-cheques-a-imputar'), {
    ajax: {
        url: __AJAX_PATH__ + 'rengloncobranza/index_table_cheques/',
        data: function (d) {
            d.tab = 2;
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
                dt_cheques_column_index.multiselect,
                dt_cheques_column_index.observacion         
            ]
        },
        {
            className: "text-right nowrap",
            targets: [
                dt_cheques_column_index.monto
            ]
        },          
        {
            className: "hidden",
            targets: [
                dt_cheques_column_index.id_cheque
            ]
        }
    ]
});

var dt_retenciones_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    //cliente: 3,
    tipoImpuesto: 3,
    numero: 4,
    monto: 5,
    //id_cliente: 7,
    id_retencion: 6,
    id_tipo_retencion: 7
};

dt_retenciones = dt_datatable($('#table-retenciones'), {
    ajax: {
        url: __AJAX_PATH__ + 'rengloncobranza/index_table_retenciones/',
        data: function (d) {
            //d.id_cuenta = $('#id_cuenta_bancaria').val();
        }
    },
    paging: false,
    columnDefs: [
        {
            "targets": dt_retenciones_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_retenciones_column_index.multiselect
            ]
        },
        {
            className: "text-right nowrap",
            targets: [
                dt_retenciones_column_index.monto
            ]
        },
        {
            className: "hidden",
            targets: [
                //dt_retenciones_column_index.id_cliente,
                dt_retenciones_column_index.id_retencion,
                dt_retenciones_column_index.id_tipo_retencion

            ]
        }
    ]
});

$(document).ready(function () {

    initCurrencies();

    limpiar_entrada_de_datos();

    initMostrarDetalleSaldoSaldoAnticipoHandler();
    
    initBuscarComprobanteHandler();

    id_comprobante = null;

    fecha_para_asiento = null;

    $('#seleccion-tipo-pago').on('change', function (e) {

        var tipoPagoSelectVal = $('#select_tipoPago_multiple').val();
        switch (parseFloat(tipoPagoSelectVal)) {
            case 0:
                $('#formulario-banco').hide();
                $('#formulario-cheque').hide();
                $('#formulario-retencion').hide();
                break;
            case 1:
            case 4:
                // Cobro de Banco y Cobro ONABE
                $('#formulario-banco').show();
                $('#formulario-cheque').hide();
                $('#formulario-retencion').hide();
                break;
            case 2:
                // Valor a depositar
                $('#formulario-banco').hide();
                $('#formulario-cheque').show();
                $('#formulario-retencion').hide();
                break;
            case 3:
                // Retencion de cliente
                $('#formulario-banco').hide();
                $('#formulario-cheque').hide();
                $('#formulario-retencion').show();
                break;
        }
    });

    $('#tab_cobranza').bootstrapWizard({
        onTabClick: function (tab, navigation, index, clickedIndex) {

            switch (clickedIndex) {
                case 0:

                    if (esNacion) {
                        $('#autoimputar').show();
                    }

                    if ((dt_getSelectedRows('#table-banco').length > 0) &&
                            (dt_getSelectedRows('#table-comprobantes').length == 0) &&
                            (dt_getSelectedRows('#table-notas-credito').length == 0) &&
                            (tab == '#tab_1')) {
                        $('#crear_anticipo').show();
                    }
                    else {
                        $('#crear_anticipo').hide();
                    }

                    $('#div-header-comprobantes').appendTo($('#div-header-comprobantes-tab1'));
                    $('#div-table-comprobantes').appendTo($('#div-table-comprobantes-tab1'));
                    $('#div-table-notas-credito').appendTo($('#div-table-comprobantes-tab1'));
                    $('#div-table-retenciones').appendTo($('#div-table-cobros-tab1'));
                    //$('#div-table-cheques').appendTo($('#div-table-cobros-tab1'));
                    break;
                case 1:
                    $('#autoimputar').hide();
                    $('#crear_anticipo').hide();
                    $('#div-header-comprobantes').appendTo($('#div-header-comprobantes-tab2'));
                    $('#div-table-comprobantes').appendTo($('#div-table-comprobantes-tab2'));
                    $('#div-table-notas-credito').appendTo($('#div-table-comprobantes-tab2'));
                    $('#div-table-retenciones').appendTo($('#div-table-cobros-tab2'));
                    //$('#div-table-cheques').appendTo($('#div-table-cobros-tab2'));                    
                    break;
            }
        }
    });

    //TAB 1
    $('#table-banco,#table-comprobantes').on('selected_element', function () {
        controlarBotones();
    });

    $('#table-notas-credito').on('selected_element', function () {
        controlarBotones();
    });

    $('#table-cheques').on('selected_element', function () {
        controlarBotones();
    });

    $('#table-retenciones').on('selected_element', function () {
        controlarBotones();
    });


    //TAB 2
    $('#table-banco-a-imputar').on('selected_element', function () {
        controlarBotones();
    });
    $('#table-anticipos').on('selected_element', function () {
        controlarBotones();
    });

    $('#table-cheques-a-imputar').on('selected_element', function () {
        controlarBotones();
    });

    $('#eliminar-movimiento').on('click', function (e) {
        e.preventDefault();
        var ids = dt_getSelectedRowsIds($('#table-banco'));

        $.ajax({
            type: "POST",
            data: {ids: JSON.stringify(ids.toArray())},
            url: __AJAX_PATH__ + 'rengloncobranza/eliminar_movimientos/'
        }).done(function (respuesta) {
            if (respuesta.status === 'OK') {
                reCargarBanco();

                showFlashMessage('success', respuesta.message, 5000);

                $('#eliminar-movimiento').hide();
                $('#cobranzas_a_imputar').hide();
                $('#crear_anticipo').hide();
            } else {
                showFlashMessage('danger', respuesta.message, 5000);
            }

            App.scrollTop();
        });
    });

    $('#eliminar-cheque').on('click', function (e) {
        e.preventDefault();
        var ids = dt_getSelectedRowsIds($('#table-cheques'));

        $.ajax({
            type: "POST",
            data: {ids: JSON.stringify(ids.toArray())},
            url: __AJAX_PATH__ + 'rengloncobranza/eliminar_movimientos/'
        }).done(function (respuesta) {
            if (respuesta.status === 'OK') {

                dt_cheques.DataTable().ajax.reload();

                showFlashMessage('success', respuesta.message, 5000);

                $('#eliminar-cheque').hide();
            } else {
                showFlashMessage('danger', respuesta.message, 5000);
            }

            App.scrollTop();
        });
    });

    $('#eliminar-retencion').on('click', function (e) {
        e.preventDefault();
        var ids = dt_getSelectedRowsIds($('#table-retenciones'));

        $.ajax({
            type: "POST",
            data: {ids: JSON.stringify(ids.toArray())},
            url: __AJAX_PATH__ + 'rengloncobranza/eliminar_retenciones/'
        }).done(function (respuesta) {
            if (respuesta.status === 'OK') {

                dt_cheques.DataTable().ajax.reload();

                showFlashMessage('success', respuesta.message, 5000);

                $('#eliminar-retencion').hide();

                dt_retenciones.DataTable().ajax.reload();
            }
            else {
                showFlashMessage('danger', respuesta.message, 5000);
            }

            App.scrollTop();
        });
    });


    $('#filtrar-comprobantes').on('click', function (e) {
        e.preventDefault();

        $desde = $('#comprobantes_desde');
        $hasta = $('#comprobantes_hasta');

        /*if ($desde.val() == '' || $hasta.val() == '') {
         alert('');
         show_alert({title: 'Error', msg: 'Debe ingresar al menos una de las dos fechas', type: 'error'});
         return;            
         } else {*/
        reCargarComprobantes();
        //}
    });

    $('#actualizar-retenciones').on('click', function (e) {
        e.preventDefault();
        dt_retenciones.DataTable().ajax.reload();
    });

    $('#actualizar-nc').on('click', function (e) {
        e.preventDefault();
        dt_notas_credito.DataTable().ajax.reload();
    });

    $('#actualizar-c').on('click', function (e) {
        e.preventDefault();
        dt_banco.DataTable().ajax.reload();
    });

    $('#actualizar-c-i').on('click', function (e) {
        e.preventDefault();
        dt_banco_a_imputar.DataTable().ajax.reload();
    });

    $('#actualizar-a').on('click', function (e) {
        e.preventDefault();
        dt_anticipos.DataTable().ajax.reload();
    });

    $('#actualizar-cheques').on('click', function (e) {
        e.preventDefault();
        dt_cheques.DataTable().ajax.reload();
    });

    $('#actualizar-cheques-i').on('click', function (e) {
        e.preventDefault();
        dt_cheques_a_imputar.DataTable().ajax.reload();
    });

    $('#crear-movimiento').on('click', function (e) {

        e.preventDefault();

        bloquear();

        var fecha = $('#adif_contablebundle_cobranza_rengloncobranzabanco_fecha');
        var monto = $('#adif_contablebundle_cobranza_rengloncobranzabanco_monto');
        var referencia = $('#adif_contablebundle_cobranza_rengloncobranzabanco_numeroTransaccion');
        var tipoRegistro = $("#select_tipoPago_multiple").val();

        if (fecha.val() == '' || monto.val() == '' || referencia.val() == '') {

            var message = 'Debe ingresar la fecha, el n&uacute;mero de referencia y el monto del movimiento bancario (todos los campos son obligatorios)';

            showFlashMessage('danger', message, 5000);

            App.scrollTop();

            desbloquear();

            return;
        } else {

            $.ajax({
                type: "POST",
                data: {
                    fecha: fecha.val(), 
                    referencia: referencia.val(), 
                    monto: monto.val().replace(',', '.'), 
                    id_cuenta: $('#id_cuenta_bancaria').val(),
                    observacion: $('#adif_contablebundle_cobranza_rengloncobranzabanco_observacion').val(),
                    tipoRegistro: tipoRegistro
                },
                url: __AJAX_PATH__ + 'rengloncobranza/agregar_movimiento/'
            }).done(function (respuesta) {
                if (respuesta.status === 'OK') {

                    monto.val("");
                    fecha.val("");
                    referencia.val("");
                    limpiar_entrada_de_datos();

                    dt_banco.DataTable().ajax.reload();

                    showFlashMessage('success', respuesta.message, 3000);

                    desbloquear();
                } else {
                    showFlashMessage('danger', respuesta.message, 3000);
                    desbloquear();
                    return;
                }

                App.scrollTop();
            });
        }
    });

    //id_tipo_de_impuesto_seleccionado = null;
    //id_cliente_seleccionado = null;

    $('#crear-cheque').on('click', function (e) {
        e.preventDefault();
        bloquear();

        var fecha = $('#adif_contablebundle_cobranza_rengloncobranzacheque_fecha');
        var monto = $('#adif_contablebundle_cobranza_rengloncobranzacheque_monto');
        var referencia = $('#adif_contablebundle_cobranza_rengloncobranzacheque_numero');
        var id_banco_seleccionado = $('#adif_contablebundle_cobranza_rengloncobranzacheque_banco').val();

        if (fecha.val() == '' || monto.val() == '' || referencia.val() == '' || id_banco_seleccionado == '') {

            var message = 'Debe ingresar la fecha, el banco emisor, el n&uacute;mero y el importe del cheque (todos los campos son obligatorios)';

            showFlashMessage('danger', message, 5000);

            desbloquear();

            return;
        } else {

            $.ajax({
                type: "POST",
                data: {
                    fecha: fecha.val(), 
                    referencia: referencia.val(), 
                    monto: monto.val().replace(',', '.'), 
                    id_banco: parseInt(id_banco_seleccionado),
                    observacion: $('#adif_contablebundle_cobranza_rengloncobranzacheque_observacion').val()
                },
                url: __AJAX_PATH__ + 'rengloncobranza/agregar_cheque/'
            }).done(function (respuesta) {
                if (respuesta.status === 'OK') {
                    monto.val("");
                    fecha.val("");
                    referencia.val("");

                    $('#adif_contablebundle_cobranza_rengloncobranzacheque_banco').select2('val', '0');

                    limpiar_entrada_de_datos();

                    dt_cheques.DataTable().ajax.reload();

                    showFlashMessage('success', respuesta.message, 5000);

                    desbloquear();
                } else {
                    showFlashMessage('danger', respuesta.message, 5000);
                    desbloquear();
                    return;
                }

                App.scrollTop();
            });
        }
    });

    $('#crear-retencion').on('click', function (e) {
        e.preventDefault();

        bloquear();

        var fecha = $('#adif_contablebundle_cobranza_retencioncliente_fecha');
        var monto = $('#adif_contablebundle_cobranza_retencioncliente_monto');
        var referencia = $('#adif_contablebundle_cobranza_retencioncliente_numero');

        //var id_cliente_seleccionado = $('#adif_contablebundle_cobranza_retencioncliente_cliente').val();

        var id_impuesto_seleccionado = $('#adif_contablebundle_cobranza_retencioncliente_tipoImpuesto').val();

        if (fecha.val() == '' || monto.val() == '' || referencia.val() == '' || id_impuesto_seleccionado == '') {//|| id_cliente_seleccionado == '') {

            var message = 'Debe ingresar la fecha, el n&uacute;mero, el importe, el tipo de impuesto y el cliente (todos los campos son obligatorios)';

            showFlashMessage('danger', message, 5000);

            desbloquear();
            return;
        } else {

            $.ajax({
                type: "POST",
                data: {fecha: fecha.val(), referencia: referencia.val(), monto: monto.val().replace(',', '.'), id_impuesto: parseInt(id_impuesto_seleccionado)}, //, id_cliente: parseInt(id_cliente_seleccionado)},
                url: __AJAX_PATH__ + 'rengloncobranza/agregar_retencion/'
            }).done(function (respuesta) {
                if (respuesta.status === 'OK') {
                    monto.val("");
                    fecha.val("");
                    referencia.val("");
                    $('#adif_contablebundle_cobranza_retencioncliente_tipoImpuesto').select2('val', '0');
                    limpiar_entrada_de_datos();
                    dt_retenciones.DataTable().ajax.reload();

                    showFlashMessage('success', respuesta.message, 5000);

                    desbloquear();
                } else {
                    showFlashMessage('danger', respuesta.message, 5000);
                    desbloquear();
                    return;
                }

                App.scrollTop();
            });
        }

    });

    $('#cargar-archivo').on('click', function () {
        var input_archivo = $('#input_archivo');
        if (input_archivo.val() == '') {

            var message = 'Debe ingresar el archivo de cobranzas del banco';

            showFlashMessage('danger', message, 5000);
        } else {
            var formData = new FormData();
            formData.append('id_cuenta', $('#id_cuenta_bancaria').val());
            formData.append('tipoRegistro', $('#select_tipoPago_multiple').val());
            var ext = input_archivo.val().split('.').pop().toLowerCase();
            var ext_valida = '';
            var str_arch = '';

            if (esNacion) {
                ext_valida = ['txt'];
                str_arch = 'archivo de texto (extensión TXT)';
            } else {
                ext_valida = ['xls', 'xlsx'];
                str_arch = 'archivo de excel (extensión XLS o XLSX)';
            }

            if ($.inArray(ext, ext_valida) == -1) {

                var message = 'Por favor, ingrese un ' + str_arch;

                showFlashMessage('danger', message, 5000);

                return;
            } else {
                formData.append('nombre_archivo', input_archivo.val());
                formData.append('archivo', $('#input_archivo').prop('files')[0]);
            }

            $.ajax({
                type: 'POST',
                url: __AJAX_PATH__ + 'rengloncobranza/cargar_archivo_' + ext_valida[0] + '/',
                processData: false,
                contentType: false,
                data: formData
            }).done(function (respuesta) {
                reCargarBanco();
                limpiar_entrada_de_datos();

                $('#remover-archivo').click();

                show_dialog({
                    titulo: 'Carga de archivo',
                    contenido: respuesta,
                    labelSuccess: 'Guardar Archivo',
                    labelCancel: 'Aceptar',
                    callbackSuccess: function () {
                        exportRechazados();
                    },
                    callbackCancel: function () {

                    }
                });

                if ($('#div_error_formato').length) {
                    $('.modal-header').css('background-color', 'rgb(210, 50, 45)');
                    $('.modal-content .cancel').css({
                        'color': '#FFF',
                        'background-color': '#3276B1',
                        'border-color': '#285E8E'
                    });

                    $('.modal-content .success').remove();
                }

                if ($('#div_renglones_rechazados').length) {
                    $('.modal-content .cancel').remove();
                }

                //initExport();

            }
            );
            setMasks();
        }
    });

    $('#autoimputar').on('click', function (e) {
        e.preventDefault();
        var tab = id_tab_activo();
        if (tab == '#tab_1') {
            if (verificar_fechas_banco_cheque(tab, true, true)) {

                imputacionAutomatica();

            } else {
                var message = 'Las cobranzas que se imputan autom&aacute;ticamente tienen que ser de la misma fecha';

                showFlashMessage('danger', message, 5000);

                App.scrollTop();

                desbloquear();

                return;
            }
        }
    });

    $('#imputar_manual').on('click', function (e) {
        e.preventDefault();

        var tab = id_tab_activo();
        var filas_comprobantes = dt_getSelectedRows('#table-comprobantes');
        var filas_notas_credito = dt_getSelectedRows('#table-notas-credito');
        var filas_cheques = dt_getSelectedRows('#table-cheques');
        var filas_retenciones = dt_getSelectedRows('#table-retenciones');
        var filas_banco = dt_getSelectedRows('#table-banco');

        if (verificar_fechas_banco_cheque(tab, false, false)) {

            if (tab == '#tab_1') {

                var ok = ((filas_notas_credito.length == 1 && filas_cheques.length == 0 && filas_retenciones.length == 0 && filas_banco.length == 0) ||
                        (filas_notas_credito.length == 0 && filas_cheques.length == 1 && filas_retenciones.length == 0 && filas_banco.length == 0) ||
                        (filas_notas_credito.length == 0 && filas_cheques.length == 0 && filas_retenciones.length == 1 && filas_banco.length == 0) ||
                        (filas_notas_credito.length == 0 && filas_cheques.length == 0 && filas_retenciones.length == 0 && filas_banco.length == 1));

                if (!ok) {

                    var message = 'A los comprobantes pendientes no se les puede imputar más de una cobranza, valor a depositar, retenci&oacute;n o nota de cr&eacute;dito';

                    showFlashMessage('danger', message, 5000);

                    App.scrollTop();

                    return;
                }

                if (filas_banco.length == 1) {
                    imputacionManual(filas_comprobantes, filas_banco[0], 1);
                }
                else
                if (filas_notas_credito.length == 1) {
                    imputacionManual(filas_comprobantes, filas_notas_credito[0], 2);
                }
                else // 3 es anticipos pero sólo se los puede imputar en tab2
                if (filas_cheques.length == 1) {
                    imputacionManual(filas_comprobantes, filas_cheques[0], 4);
                } //4 es cheque
                else
                if (filas_retenciones.length == 1) {
                    imputacionManual(filas_comprobantes, filas_retenciones[0], 5);
                } //4 es cheque
            }

            var cobranzas_a_imputar = dt_getSelectedRows('#table-banco-a-imputar');
            var anticipos = dt_getSelectedRows('#table-anticipos');
            var cheques_a_imputar = dt_getSelectedRows('#table-cheques-a-imputar');

            if (tab == '#tab_2') {
                if ((cobranzas_a_imputar.length > 0 || cheques_a_imputar.length > 0) && (anticipos.length > 0)) {

                    var message = 'Si se quiere imputar un anticipo de cliente no se puede imputar un cobro de banco y/o cobro por cheque';

                    showFlashMessage('danger', message, 5000);

                    App.scrollTop();

                    return;
                } else {
                    var ok = ((filas_retenciones.length == 1 && filas_notas_credito.length == 0 && cobranzas_a_imputar.length == 0 && anticipos.length == 0 && cheques_a_imputar.length == 0) ||
                            (filas_retenciones.length == 0 && filas_notas_credito.length == 1 && cobranzas_a_imputar.length == 0 && anticipos.length == 0 && cheques_a_imputar.length == 0) ||
                            (filas_retenciones.length == 0 && filas_notas_credito.length == 0 && cobranzas_a_imputar.length == 1 && anticipos.length == 0 && cheques_a_imputar.length == 0) ||
                            (filas_retenciones.length == 0 && filas_notas_credito.length == 0 && cobranzas_a_imputar.length == 0 && anticipos.length == 1 && cheques_a_imputar.length == 0) ||
                            (filas_retenciones.length == 0 && filas_notas_credito.length == 0 && cobranzas_a_imputar.length == 0 && anticipos.length == 0 && cheques_a_imputar.length == 1));

                    if ((filas_comprobantes.length > 1) && (!ok)) {

                        var message = 'Al elegir m&aacute;s de un comprobante pendiente solamente se puede imputar una cobranza o una nota de cr&eacute;dito o un anticipo o un cheque o una retenci&oacute;n';

                        showFlashMessage('danger', message, 5000);

                        App.scrollTop();

                        return;
                    }
                    //bloquear();               
                    //if (filas_comprobantes.length == 1) {
                    if (!ok) {
                        imputacionManualMezcladoTab2(filas_comprobantes[0]);
                    }
                    //else imputacionManual(filas_comprobantes, cobranzas_a_imputar[0], 1);
                    //}
                    else {

                        if (cobranzas_a_imputar.length == 1) {
                            imputacionManual(filas_comprobantes, cobranzas_a_imputar[0], 1);
                        } else {
                            if (filas_notas_credito.length == 1) {
                                imputacionManual(filas_comprobantes, filas_notas_credito[0], 2);
                            } else {
                                if (anticipos.length == 1) {
                                    imputacionManual(filas_comprobantes, anticipos[0], 3);
                                } else {
                                    if (cheques_a_imputar.length == 1) {
                                        imputacionManual(filas_comprobantes, cheques_a_imputar[0], 4);
                                    } else {
                                        imputacionManual(filas_comprobantes, filas_retenciones[0], 4);
                                    }
                                }
                            }
                        }
                    }
                    //desbloquear();
                }
            }
        } else {

            var message = 'Las cobranzas de banco, los valores a depositar y las retenciones seleccionados para imputar tienen que tener la misma fecha';

            showFlashMessage('danger', message, 5000);

            App.scrollTop();

            desbloquear();

            return;
        }
    });

    $('#cobranzas_a_imputar, #cheques_a_imputar').on('click', function (e) {
        e.preventDefault();

        var id_tipo = $(this).attr('id');
        var tipo = '';
        var ids = null;

        if (id_tipo == 'cobranzas_a_imputar') {
            tipo = 'banco';
            ids = dt_getSelectedRowsIds($('#table-banco'));
        }
        else {
            tipo = 'cheque';
            ids = dt_getSelectedRowsIds($('#table-cheques'));
        }

        actualizar_movimientos_banco(ids, 'imputar', tipo);

    });

    $('#cobranzas_pendientes').on('click', function (e) {
        e.preventDefault();
        actualizar_movimientos_banco(dt_getSelectedRowsIds($('#table-banco-a-imputar')), 'pendientes', 'banco');
    });

    $('#cheques_pendientes').on('click', function (e) {
        e.preventDefault();
        actualizar_movimientos_banco(dt_getSelectedRowsIds($('#table-cheques-a-imputar')), 'pendientes', 'cheque');
    });

    $('#crear_anticipo').on('click', function (e) {

        e.preventDefault();
        bloquear();
        var tab = id_tab_activo();
        if (tab == '#tab_1') {
            var ids = dt_getSelectedRowsIds($('#table-banco'));
            var ids_cheques = dt_getSelectedRowsIds($('#table-cheques'));
        } else {
            var ids = dt_getSelectedRowsIds($('#table-banco-a-imputar'));
            var ids_cheques = dt_getSelectedRowsIds($('#table-cheques-a-imputar'));

        }
        if (ids.length == 0 && ids_cheques.length == 0) {

            var message = 'Debe seleccionar al menos un movimiento o un cheque para crear un anticipo de cliente.';

            showFlashMessage('danger', message, 5000);

            App.scrollTop();

            desbloquear();

            return;
        }

        if (verificar_fechas_banco_cheque(id_tab_activo(), false, false)) {

            clientesHTML = "<option value='' selected='selected'>-- Elija un cliente --</option>";

            $.each(clientes, function (id, data) {
                clientesHTML = clientesHTML + "<option value='" + id + "'>" + clientes[id] + "</option>";
            });

            formulario_clientes = '<form id="form_multiples_clientes" method="post" name="form_multiples_clientes">\n\
                                        <div class="row">\n\
                                            <div class="col-md-12">\n\
                                                <div class="form-group">\n\
                                                    <label class="control-label required" for="select_clientes_multiple">Cliente</label>\n\
                                                    <div class="input-icon right">\n\
                                                        <i class="fa"></i>\n\
                                                        <select id="select_clientes_multiple" name="select_clientes_multiple" class="required form-control choice">' + clientesHTML + '</select>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </form>';
            show_dialog({
                titulo: 'Crear anticipo de cliente',
                contenido: formulario_clientes,
                callbackCancel: function () {
                    desbloquear();
                },
                callbackSuccess: function () {

                    var formulario = $('form[name=form_multiples_clientes]').validate();
                    var formulario_result = formulario.form();

                    if (formulario_result) {
                        $.ajax({
                            type: "POST",
                            data: {ids: JSON.stringify(ids.toArray()), ids_cheques: JSON.stringify(ids_cheques.toArray()), id_cliente: $('#select_clientes_multiple').val(), tab: tab, },
                            url: __AJAX_PATH__ + 'rengloncobranza/crear_anticipo/'
                        }).done(function (respuesta) {

                            if (respuesta.status === 'OK') {
                                if (tab == '#tab_1') {
                                    reCargarBanco();
                                    dt_cheques.DataTable().ajax.reload();
                                } else {
                                    dt_banco_a_imputar.DataTable().ajax.reload();
                                    dt_cheques_a_imputar.DataTable().ajax.reload();
                                }
                                dt_anticipos.DataTable().ajax.reload();

                                if (typeof respuesta.mensajeAsiento !== 'undefined') {
                                    showFlashMessage('info', respuesta.mensajeAsiento, 0);
                                }
                                
                                if (tab == '#tab_1') {
                                    $('.fecha-asiento-contable').html(fecha_para_asiento);
                                } else {
                                    fecha_para_asiento = getCurrentDate();
                                }
                            
                                showFlashMessage('success', respuesta.message, 0);

                                $('#eliminar-movimiento').hide();
                                $('#cobranzas_a_imputar').hide();
                                $('#eliminar-cheque').hide();
                                //$('#cheque-a-imputar').hide();                            
                                $('#crear_anticipo').hide();

                            } else {
                                showFlashMessage('danger', respuesta.message, 0);
                            }

                            App.scrollTop();

                        });
                    } else {
                        return false;
                    }
                }
            });

            desbloquear();

            $('.bootbox').removeAttr('tabindex');

            $('.modal-dialog').css('width', '60%');

            $('#select_clientes_multiple').select2();
        } else {

            var message = 'Los cobros del banco y los valores a depositar seleccionados para imputar tienen que tener la misma fecha';

            showFlashMessage('danger', message, 5000);

            App.scrollTop();

            desbloquear();

            return;
        }
    });

    initEditarFechaAsientoContableHandler();

});

/**
 * 
 * @param {type} ids
 * @param {type} accion
 * @returns {undefined}
 */
function actualizar_movimientos_banco(ids, accion, tipo) { //tipo es banco o cheque
    if (verificar_fechas_banco_cheque(id_tab_activo(), false, false)) {

        $.ajax({
            type: "POST",
            data: {ids: JSON.stringify(ids.toArray()), tipo: tipo},
            url: __AJAX_PATH__ + 'rengloncobranza/enviar_cobranzas_a_' + accion + '/'
        }).done(function (respuesta) {

            if (respuesta.status === 'OK') {

                if (tipo == 'banco') {
                    reCargarBanco();
                    dt_banco_a_imputar.DataTable().ajax.reload();
                }
                else {
                    dt_cheques.DataTable().ajax.reload();
                    dt_cheques_a_imputar.DataTable().ajax.reload();
                }

                if (typeof respuesta.mensajeAsiento !== 'undefined') {
                    showFlashMessage('info', respuesta.mensajeAsiento, 0);
                }
                
//                if (accion == 'imputar') {
//                    $('.fecha-asiento-contable').html(fecha_para_asiento);
//                } else {
//                    fecha_para_asiento = getCurrentDate();
//                }    

                showFlashMessage('success', respuesta.message, 0);

                $('#crear_anticipo').hide();

                if (tipo == 'banco') {
                    $('#cobranzas_a_imputar').hide();
                    $('#eliminar-movimiento').hide();
                    $('#cobranzas_pendientes').hide();
                }
                else {
                    $('#cheques_a_imputar').hide();
                    $('#eliminar-cheque').hide();
                    $('#cheques_pendientes').hide();
                }
            } else {
                showFlashMessage('danger', respuesta.message, 5000);
            }

            App.scrollTop();
        });

    } else {
        var message = 'Los cobros de banco y los valores a depositar seleccionados tienen que tener la misma fecha';

        showFlashMessage('danger', message, 5000);

        App.scrollTop();

        desbloquear();

        return;
    }
}

/**
 * 
 * @returns {undefined}
 */
function reCargarBanco() {
    dt_banco.DataTable().ajax.reload();
}

/**
 * 
 * @returns {undefined}
 */
function reCargarComprobantes() 
{
	iniciarComprobantes = true;
    dt_comprobantes.DataTable().ajax.reload();
	dt_notas_credito.DataTable().ajax.reload();
}

/**
 * 
 * @param {type} filas_comprobantes
 * @param {type} pago
 * @param {type} tipo_pago
 * @returns {undefined|Boolean}
 */
function imputacionManual(filas_comprobantes, pago, tipo_pago) {
    var texto = '';
    var a_favor = 0;
    var id_pago = 0;
    var id_cliente = 0;
    switch (tipo_pago) {
        case 1: //cobranza
            a_favor = parseFloat(clearCurrencyValue(pago[3]));
            id_pago = pago[5];
            if (pago[1])
                texto = 'Cobranza N° ' + pago[1];
            else
                texto = 'Cobranza sin n&uacute;mero';
            break;
        case 2: //nota de crédito
            a_favor = parseFloat(clearCurrencyValue(pago[4]));
            id_pago = pago[6];
            texto = 'Nota de Credito N° ' + pago[1];
            id_cliente = pago[5];
            break;
        case 3: //anticipo
            a_favor = parseFloat(clearCurrencyValue(pago[3]));
            id_pago = pago[5];
            id_cliente = pago[4];
            texto = 'Anticipo de Cliente ' + pago[0];
            break;
        case 4: //cheque
            a_favor = parseFloat(clearCurrencyValue(pago[3]));
            id_pago = pago[5];
            texto = 'Cheque N° ' + pago[2];
            break;
        case 5: //retencion
            a_favor = parseFloat(clearCurrencyValue(pago[3]));
            id_pago = pago[4];
            //id_cliente = pago[5];
            texto = 'Retenci&oacute;n N° ' + pago[2];
            break;

    }

    if (filas_comprobantes.length > 1) { // && tipo_pago != 5) {
        var form = '<form id="form-imputacion-manual" name="form-imputacion-manual">\n\
                ';

        form += '<label class="control-label">' + texto + '</label>\n\
                <div class="form-group">\n\
                  <div class="input-icon right">\n\
                      <i class="fa"></i>\n\
                      <input type="text" class="form-control currency" value="' + a_favor + '" readonly>\n\
                  </div>\n\
                </div>\n\
                ';

        var fila;
        var deuda = 0;
        var id_cliente_ant = filas_comprobantes[0][9];

        for (var index = 0; index < filas_comprobantes.length; index++) {
            fila = filas_comprobantes[index];

            deuda += parseFloat(clearCurrencyValue(fila[8]));

            if ((tipo_pago != 1) && (tipo_pago != 4) && (tipo_pago != 5) && (fila[9] != id_cliente)) {
                if (tipo_pago == 2) {
                    msj = 'Las notas de cr&eacute;dito utilizadas para cancelar un comprobante deben ser todas del mismo cliente';
                }
                else {
                    msj = 'Los anticipos de cliente utilizados para cancelar un comprobante deben ser todos del mismo cliente';
                }

                desbloquear();

                return false;
            }

            if (id_cliente_ant != fila[9]) {

                var message = 'No se puede imputar un &uacute;nico elemento a comprobantes de distinto cliente';

                showFlashMessage('danger', message, 5000);

                App.scrollTop();

                return;
            }

            form += '<label class="control-label" for="monto-' + fila[10] + '">' + fila[1] + ' N° ' + fila[2] + ' (con saldo $' + fila[8] + ')' + '</label>\n\
                                <div class="form-group">\n\
                                  <div class="input-icon right">\n\
                                      <i class="fa"></i>\n\
                                      <input type="text" id="monto-' + fila[10] + '" name="monto" class="form-control required currency" style="height: auto; text-align: right;">\n\
                                  </div>\n\
                                </div>\n\
                                ';
            id_cliente_ant = fila[9];
        }
        form += '</form>';
        //bloquear(); //OK
        show_dialog({
            titulo: 'Imputaci&oacute;n manual',
            contenido: form,
            callbackCancel: function () {
                //desbloquear();
                return;
            },
            callbackSuccess: function () {


                var formulario = $('form[name=form-imputacion-manual]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {
                    //var ids_comprobantes = dt_getSelectedRowsIds('#table-comprobantes');
                    //if (ids_comprobantes.length == filas_comprobantes.length) {
                    var montos_comprobantes = [];
                    var ids_comprobantes = [];
                    var suma_valores = 0;
                    var valor = 0;
                    for (var index = 0; index < filas_comprobantes.length; index++) {
                        fila = filas_comprobantes[index];
                        //for (var index = 0; index < ids_comprobantes.length; index++) {
                        valor = parseFloat($('#monto-' + fila[10]).val().replace(',', '.'));
                        //valor = parseFloat($('#monto-' + fila[10]).val());
                        //alert(valor.replace(',','.'));
                        suma_valores += valor;
                        montos_comprobantes.push(valor);
                        ids_comprobantes.push(fila[10]);
                        if (valor > 0) {
                            if (valor > parseFloat(clearCurrencyValue(fila[8]))) {

                                var message = 'No se puede imputar un valor mayor al saldo de un comprobante';

                                showFlashMessage('danger', message, 5000);

                                App.scrollTop();

                                return;
                            }
                        } else {

                            var message = 'No se puede imputar 0 a un comprobante';

                            showFlashMessage('danger', message, 5000);

                            App.scrollTop();

                            return;
                        }
                    }

                    if (suma_valores <= a_favor) {
                        if (tipo_pago == 5 && a_favor > deuda) {

                            var message = 'La retenci&oacute;n tiene que imputarse por completo';

                            showFlashMessage('danger', message, 5000);

                            App.scrollTop();

                            desbloquear();

                            return;
                        }
                        $.ajax({
                            type: "POST",
                            data: {id_pago: id_pago,
                                tipo_pago: tipo_pago,
                                tab: id_tab_activo(),
                                ids_comprobantes: JSON.stringify(ids_comprobantes),
                                montos_comprobantes: JSON.stringify(montos_comprobantes)},
                            url: __AJAX_PATH__ + 'rengloncobranza/imputar_manualmente/'
                        }).done(function (respuesta) {

                            if (respuesta.status === 'OK') {

                                $('#imputar_manual').hide();

                                switch (tipo_pago) {
                                    case 1:

                                        if (id_tab_activo() == '#tab_1') {
                                            dt_banco.DataTable().ajax.reload();
                                        }
                                        else {
                                            dt_banco_a_imputar.DataTable().ajax.reload();
                                        }

                                        dt_anticipos.DataTable().ajax.reload();

                                        break;
                                    case 2:
                                        dt_notas_credito.DataTable().ajax.reload();
                                        break;
                                    case 3:
                                        dt_anticipos.DataTable().ajax.reload();
                                        break;
                                    case 4:

                                        if (id_tab_activo() == '#tab_1') {
                                            dt_cheques.DataTable().ajax.reload();
                                        }
                                        else {
                                            dt_cheques_a_imputar.DataTable().ajax.reload();
                                        }

                                        dt_anticipos.DataTable().ajax.reload();
                                        break;
                                    case 5:
                                        dt_retenciones.DataTable().ajax.reload();
                                        //
                                        break;
                                }

                                dt_comprobantes.DataTable().ajax.reload();

                                desbloquear();

                                if (typeof respuesta.mensajeAsiento !== 'undefined') {
                                    showFlashMessage('info', respuesta.mensajeAsiento, 0);
                                }
                                
//                                if ((id_tab_activo() == '#tab_1') && (tipo_pago == 1 || tipo_pago == 4)) {
//                                    $('.fecha-asiento-contable').html(fecha_para_asiento);
//                                } else {
//                                    fecha_para_asiento = getCurrentDate();
//                                }
                            
                                showFlashMessage('success', respuesta.message, 0);

                            } else {

                                showFlashMessage('danger', respuesta.message, 5000);

                                desbloquear();

                                return;
                            }
                        });
                    } else {

                        var message = 'No se puede imputar una suma mayor a lo que se tiene a favor';

                        showFlashMessage('danger', message, 5000);

                        App.scrollTop();

                        desbloquear();

                        return;
                    }
                    //} else {
                    //    show_alert({title: 'Error', msg: 'Se produjo un error al verificar los valores ingresados', type: 'error'});   
                    //    desbloquear();
                    //    return;                        
                    //}    
                } else {

                    var message = 'Se produjo un error en ingreso de los valores a imputar';

                    showFlashMessage('danger', message, 5000);

                    App.scrollTop();

                    desbloquear();

                    return;
                }

            }
        });

        initCurrencies();

        $('.modal-dialog').css('width', '60%');

    } else {
        if ((tipo_pago != 1) && (tipo_pago != 4) && (tipo_pago != 5) && (filas_comprobantes[0][9] != id_cliente)) {

            var msj = '';

            if (tipo_pago == 2) {
                msj = 'Las notas de cr&eacute;dito utilizadas para cancelar un comprobante deben ser todas del mismo cliente';
            }
            else {
                msj = 'Los anticipos de cliente utilizados para cancelar un comprobante deben ser todos del mismo cliente';
            }

            showFlashMessage('danger', msj, 5000);

            App.scrollTop();

            return false;
        } else {
            deuda = parseFloat(clearCurrencyValue(filas_comprobantes[0][8]));

            if (tipo_pago == 5 && a_favor > deuda) {

                var message = 'La retenci&oacute;n tiene que imputarse por completo';

                showFlashMessage('danger', message, 5000);

                App.scrollTop();

                desbloquear();

                return;
            }
            $.ajax({
                type: "POST",
                data: {id_pago: id_pago,
                    tipo_pago: tipo_pago,
                    tab: id_tab_activo(),
                    id_comprobante: filas_comprobantes[0][10]},
                url: __AJAX_PATH__ + 'rengloncobranza/imputar_manualmente_1a1/'
            }).done(function (respuesta) {

                if (respuesta.status === 'OK') {

                    switch (tipo_pago) {
                        case 1:
                            if (id_tab_activo() == '#tab_1')
                                dt_banco.DataTable().ajax.reload();
                            else
                                dt_banco_a_imputar.DataTable().ajax.reload();
                            if (a_favor > clearCurrencyValue(filas_comprobantes[0][8]))
                                dt_anticipos.DataTable().ajax.reload();
                            break;
                        case 2:
                            dt_notas_credito.DataTable().ajax.reload();
                            break;
                        case 3:
                            dt_anticipos.DataTable().ajax.reload();
                            break;
                        case 4:
                            dt_cheques.DataTable().ajax.reload();
                            if (a_favor > clearCurrencyValue(filas_comprobantes[0][8]))
                                dt_anticipos.DataTable().ajax.reload();
                            break;
                        case 5:
                            dt_retenciones.DataTable().ajax.reload();
                            break;
                    }

                    dt_comprobantes.DataTable().ajax.reload();

                    desbloquear();

                    if (typeof respuesta.mensajeAsiento !== 'undefined') {
                        showFlashMessage('info', respuesta.mensajeAsiento, 0);
                    }
                    
//                    if ((id_tab_activo() == '#tab_1') && (tipo_pago == 1 || tipo_pago == 4)) {
//                        $('.fecha-asiento-contable').html(fecha_para_asiento);
//                    } else {
//                        fecha_para_asiento = getCurrentDate();                        
//                    }  

						

                    showFlashMessage('success', respuesta.message, 0);

                } else {

                    showFlashMessage('danger', respuesta.message, 5000);

                    desbloquear();

                    return;
                }
            });
        }

    }
    //desbloquear();

}


function imputacionAutomatica() {
    var filas_banco = dt_getFilteredRows('#table-banco');
    var filas_comprobantes = dt_getRows('#table-comprobantes');
    $('#table_imputacion_automatica tbody').empty();
    banco = [];
    comprobantes = [];
    var index_ok;
    var usados = new Array(filas_banco.length);
    for (var i = 0; i < usados.length; i++) {
        usados[i] = false;
    }
    var total_monto = 0;
    var total_saldo = 0;
    var total_saldo_final = 0;
    var total_anticipo = 0;
    var cliente = null;
    var str_cliente = '';
    var hayAnticipo = false;
    for (var index2 = 0; index2 < filas_comprobantes.length; index2++) {
        var cant_cobros = 0;
        var monto_total = 0;
        var fila_comprobantes = filas_comprobantes[index2];
        for (var index = 0; index < filas_banco.length; index++) {
            if (!usados[index]) {
                var fila_banco = filas_banco[index];
                if (fila_banco[6] === fila_comprobantes[6] &&
                        fila_comprobantes[6] != '') { //se podría verificar que fila_banco no sea manual o que su monto sea > 0
                    usados[index] = true;
                    index_ok = index;
                    cant_cobros++;
                    monto_total += parseFloat(clearCurrencyValue(fila_banco[3]));
                    //banco.push($('#table-banco').DataTable().rows(index).data()[0][0]);
                    banco.push(fila_banco[5]);
                }
            }
        }
        if (cant_cobros > 0) {

            //comprobantes.push($('#table-comprobantes').DataTable().rows(index2).data()[0][0]);
            comprobantes.push(fila_comprobantes[10]);
            var saldo = parseFloat(clearCurrencyValue(fila_comprobantes[8]));
            var anticipo = 0;
            if (saldo - monto_total < 0) {
                anticipo = (saldo - monto_total) * (-1);
                saldo = 0;
                hayAnticipo = hayAnticipo || true;
            } else {
                saldo -= monto_total;
            }
            cliente = fila_comprobantes[4].split('<br>');
            str_cliente = cliente[0];
            if (cliente[1] && cliente != '') {
                str_cliente += ' (' + cliente[1] + ')';
            }

            var $tr = $('<tr />', {style: 'cursor: pointer;'});
            $('<td />', {text: filas_banco[index_ok][7]}).appendTo($tr);  //tipo de cobro
            $('<td />', {text: cant_cobros.toString()}).appendTo($tr); //cantidad de cobros
            $('<td />', {text: filas_banco[index_ok][0]}).appendTo($tr); //fecha de la cobranza
            $('<td />', {text: filas_banco[index_ok][6]}).appendTo($tr); //código de barras utilizado en el cobro
            $('<td />', {text: monto_total.toFixed(2).toString(), class: 'money-format nowrap'}).appendTo($tr); //monto del cobro con el mismo código que el comprobante
            //$('<td />', {text: fila_comprobantes[0]}).appendTo($tr); //fecha de comprobante
            $('<td />', {text: fila_comprobantes[1]}).appendTo($tr); //tipo de comprobante
            $('<td />', {text: fila_comprobantes[2]}).appendTo($tr); //número de comprobante
            $('<td />', {text: str_cliente}).appendTo($tr); //cliente
            $('<td />', {text: clearCurrencyValue(fila_comprobantes[8]), class: 'money-format nowrap'}).appendTo($tr); //saldo del comprobante
            $('<td />', {text: saldo.toFixed(2).toString(), class: 'money-format nowrap'}).appendTo($tr); //saldo final del comprobante
            $('<td />', {text: anticipo.toFixed(2).toString(), class: 'money-format nowrap'}).appendTo($tr); //anticipo generado
            $('#table_imputacion_automatica tbody').append($tr);

            total_monto += monto_total;
            total_saldo += parseFloat(clearCurrencyValue(fila_comprobantes[8]));
            total_saldo_final += saldo;
            total_anticipo += anticipo;
        }

    }

//    var $tr = $('<tr />', {style: 'cursor: pointer;'});
//    $('<td />', {text: ''}).appendTo($tr);  //tipo de cobro
//    $('<td />', {text: ''}).appendTo($tr); //cantidad de cobros
//    $('<td />', {text: ''}).appendTo($tr); //fecha de la cobranza
//    $('<td />', {text: ''}).appendTo($tr); //código de barras utilizado en el cobro
//    $('<td />', {text: total_monto.toFixed(2).toString()}).appendTo($tr); //monto del cobro con el mismo código que el comprobante
//    //$('<td />', {text: ''}).appendTo($tr); //fecha de comprobante
//    $('<td />', {text: ''}).appendTo($tr); //tipo de comprobante
//    $('<td />', {text: ''}).appendTo($tr); //número de comprobante
//    $('<td />', {text: ''}).appendTo($tr); //cliente
//    $('<td />', {text: total_saldo.toFixed(2).toString()}).appendTo($tr); //saldo del comprobante
//    $('<td />', {text: total_saldo_final.toFixed(2).toString()}).appendTo($tr); //saldo final del comprobante
//    $('<td />', {text: total_anticipo.toFixed(2).toString()}).appendTo($tr); //anticipo generado
//    $('#table_imputacion_automatica tbody').append($tr);

    if (comprobantes.length > 0) { //se podría preguntar por banco también
        initExportCustom($('#table_imputacion_automatica'));
        show_dialog({
            titulo: 'Imputaci&oacute;n autom&aacute;tica',
            contenido: $('#renglones_conciliados'),
            callbackCancel: function () {
                $('#renglones_conciliados').appendTo('#renglones_conciliados_ctn');
                desbloquear();
            },
            callbackSuccess: function () {
                $('#renglones_conciliados').appendTo('#renglones_conciliados_ctn');
                imputar_automaticamente(hayAnticipo);
            }
        });

        $('.bootbox-close-button').parents('.modal-content').has('#renglones_conciliados').on('click', function (e) {
            e.preventDefault();
            $('#renglones_conciliados').appendTo('#renglones_conciliados_ctn');
        });

        setMasks();

        $('.modal-dialog').css('width', '60%');

    } else {

        var message = 'No se encontraron coincidencias entre los movimientos del banco y los comprobantes pendientes de cobro';

        showFlashMessage('danger', message, 5000);

        App.scrollTop();
    }
}

function imputar_automaticamente(hayAnticipo) {

    $.ajax({
        type: "POST",
        url: __AJAX_PATH__ + 'rengloncobranza/imputar_automaticamente/',
        data: {
            id_cuenta: $('#id_cuenta_bancaria').val(),
            ids_banco: JSON.stringify(banco),
            ids_comprobantes: JSON.stringify(comprobantes)
        }
    }).done(function (respuesta) {
        if (respuesta.status === 'OK') {

            dt_banco.DataTable().ajax.reload();

            dt_comprobantes.DataTable().ajax.reload();

            if (hayAnticipo) {
                dt_anticipos.DataTable().ajax.reload();
            }

            if (typeof respuesta.mensajeAsiento !== 'undefined') {
                showFlashMessage('info', respuesta.mensajeAsiento, 0);
            }

//            $('.fecha-asiento-contable').html(fecha_para_asiento);

            showFlashMessage('success', respuesta.message, 0);

        } else {
            showFlashMessage('danger', respuesta.message, 0);
        }



    });


}

function id_tab_activo() {
    var link = $('.nav-tabs .active').children().prop('href');
    var longitud = link.length;
    return link.substr(longitud - 6);
}

function controlarBotones() {
    notas_credito_seleccionados = dt_getSelectedRows('#table-notas-credito'); //notas_credito
    comprobantes_seleccionados = dt_getSelectedRows('#table-comprobantes'); //movimientos
    cobranzas_a_imputar = dt_getSelectedRows('#table-banco-a-imputar');
    anticipos = dt_getSelectedRows('#table-anticipos');
    cheques = dt_getSelectedRows('#table-cheques');
    retenciones = dt_getSelectedRows('#table-retenciones');
    cheques_a_imputar = dt_getSelectedRows('#table-cheques-a-imputar');
    movimientos_seleccionados = dt_getSelectedRows('#table-banco'); //renglones
    $('#imputar_manual').hide();
    var tab = id_tab_activo();
    if (tab == '#tab_1') {
        $('#eliminar-movimiento').hide();
        $('#cobranzas_a_imputar').hide();
        $('#imputar_manual').hide();
        $('#crear_anticipo').hide();
        $('#eliminar-cheque').hide();
        $('#cheques_a_imputar').hide();
        $('#eliminar-retencion').hide();

        if ((movimientos_seleccionados.length > 0) && (comprobantes_seleccionados.length == 0) && (notas_credito_seleccionados.length == 0) && (retenciones.length == 0) && (cheques.length == 0)) {
            $('#eliminar-movimiento').show();
            $('#cobranzas_a_imputar').show();
            //$('#crear_anticipo').show();
        }
        if ((movimientos_seleccionados.length == 0) && (comprobantes_seleccionados.length == 0) && (notas_credito_seleccionados.length == 0) && (retenciones.length == 0) && (cheques.length > 0)) {
            $('#eliminar-cheque').show();
            $('#cheques_a_imputar').show();
            //$('#crear_anticipo').show();
        }
        if (((cheques.length > 0) || (movimientos_seleccionados.length > 0)) && (comprobantes_seleccionados.length == 0) && (notas_credito_seleccionados.length == 0) && (retenciones.length == 0)) {
            $('#crear_anticipo').show();
        }
        if ((comprobantes_seleccionados.length > 0) && ((movimientos_seleccionados.length > 0) || (cheques.length > 0) || (notas_credito_seleccionados.length > 0) || (retenciones.length > 0))) {
            $('#imputar_manual').show();
        }
        if ((retenciones.length > 0) && (movimientos_seleccionados.length == 0) && (comprobantes_seleccionados.length == 0) && (notas_credito_seleccionados.length == 0) && (cheques.length == 0)) {
            $('#eliminar-retencion').show();

        }
    }
    if (tab == '#tab_2') {
        $('#cobranzas_pendientes').hide();
        $('#cheques_pendientes').hide();
        $('#crear_anticipo').hide();
        $('#imputar_manual').hide();
        if ((cobranzas_a_imputar.length > 0) && (comprobantes_seleccionados.length == 0)
                && (anticipos.length == 0) && (notas_credito_seleccionados.length == 0) && (retenciones.length == 0)) {
            $('#cobranzas_pendientes').show();
        }
        if ((cheques_a_imputar.length > 0) && (comprobantes_seleccionados.length == 0)
                && (anticipos.length == 0) && (notas_credito_seleccionados.length == 0) && (retenciones.length == 0)) {
            $('#cheques_pendientes').show();
        }
        if (((cheques_a_imputar.length > 0) || (cobranzas_a_imputar.length > 0)) && (comprobantes_seleccionados.length == 0) && (notas_credito_seleccionados.length == 0) && (retenciones.length == 0)) {
            $('#crear_anticipo').show();
        }
        if ((comprobantes_seleccionados.length > 0) && ((cobranzas_a_imputar.length > 0) //||cheques a imputar
                || (anticipos.length > 0) || (notas_credito_seleccionados.length > 0) || (cheques_a_imputar.length > 0) || (retenciones.length > 0))) {
            $('#imputar_manual').show();
        }
    }

    if (retenciones > 1 && comprobantes_seleccionados.length > 0) {
        var tipo_ant = retenciones[0][5];
        for (var index = 0; index < retenciones.length; index++) {
            if (retenciones[index][5] == tipo_ant) {
                $('#imputar_manual').hide();

                var message = 'No se pueden imputar a un comprobante reterciones del mismo tipo';

                showFlashMessage('danger', message, 5000);

                App.scrollTop();
            }
        }
    }
}

function imputacionManualMezcladoTab2(comprobante) {
    //filas_comprobantes[0], cobranzas_a_imputar, filas_notas_credito, anticipos
    //Verificar formas de pagos que sobren, cuenta, cliente, etc    
    //var ids_comprobantes = dt_getSelectedRowsIds('#table-comprobantes');
    var id_cliente = comprobante[9];

    if (!verificar_cliente(id_cliente)) {

        var message = 'Las notas de cr&eacute;dito y los anticipos tienen que ser del mismo cliente que el comprobante a cancelar';

        showFlashMessage('danger', message, 5000);

        App.scrollTop();

        return;
    }
    var ids_notas_credito = dt_getSelectedRowsIds('#table-notas-credito');
    var ids_cobranzas_a_imputar = dt_getSelectedRowsIds('#table-banco-a-imputar');
    var ids_anticipos = dt_getSelectedRowsIds('#table-anticipos');
    var ids_cheques_a_imputar = dt_getSelectedRowsIds('#table-cheques-a-imputar');
    var ids_retenciones = dt_getSelectedRowsIds('#table-retenciones');
    $.ajax({
        type: "POST",
        data: {id_cuenta: $('#id_cuenta_bancaria').val(),
            id_comprobante: comprobante[10],
            ids_notas_credito: JSON.stringify(ids_notas_credito.toArray()),
            ids_cobranzas_a_imputar: JSON.stringify(ids_cobranzas_a_imputar.toArray()),
            ids_anticipos: JSON.stringify(ids_anticipos.toArray()),
            ids_cheques_a_imputar: JSON.stringify(ids_cheques_a_imputar.toArray()),
            ids_retenciones: JSON.stringify(ids_retenciones.toArray())

        },
        url: __AJAX_PATH__ + 'rengloncobranza/imputar_manualmente_mezclado_tab2/'
    }).done(function (respuesta) {

        if (respuesta.status === 'OK') {

            $('#imputar_manual').hide();

            dt_comprobantes.DataTable().ajax.reload();

            if (ids_cobranzas_a_imputar.length > 0) {
                dt_banco_a_imputar.DataTable().ajax.reload();
                dt_anticipos.DataTable().ajax.reload();
            }

            if (ids_notas_credito.length > 0) {
                dt_notas_credito.DataTable().ajax.reload();
            }

            if (ids_anticipos.length > 0) {
                dt_anticipos.DataTable().ajax.reload();
            }

            if (ids_cheques_a_imputar.length > 0) {
                dt_cheques_a_imputar.DataTable().ajax.reload();
                dt_anticipos.DataTable().ajax.reload();
            }

            if (ids_retenciones.length > 0) {
                dt_retenciones.DataTable().ajax.reload();
            }

            if (typeof respuesta.mensajeAsiento !== 'undefined') {
                showFlashMessage('info', respuesta.mensajeAsiento, 0);
            }

            //$('.fecha-asiento-contable').html(fecha_para_asiento);
//            fecha_para_asiento = getCurrentDate();
            
            showFlashMessage('success', respuesta.message, 0);

        } else {
            showFlashMessage('danger', respuesta.message, 0);
        }

        App.scrollTop();
    });
}

/**
 * 
 * @param {type} id_cliente
 * @returns {Boolean}
 */
function verificar_cliente(id_cliente) {

    var filas_notas_credito = dt_getSelectedRows('#table-notas-credito');

    for (var index = 0; index < filas_notas_credito.length; index++) {
        if (filas_notas_credito[index][5] != id_cliente) {
            return false;
        }
    }

    var anticipos = dt_getSelectedRows('#table-anticipos');

    for (var index = 0; index < anticipos.length; index++) {
        if (anticipos[index][4] != id_cliente) {
            return false;
        }
    }
    return true;
}

function getHeadersTableCustom(table) {
    var a = Array();
    $(table).find('thead').find('tr[class=headers] th:visible').not('.ctn_acciones').each(function (e, v) {
        if (!($(v).prop('colspan') > 1)) {
            a.push({texto: $(v).text(),
                formato: $(v).attr('export-format') ? $(v).attr('export-format') : 'text'
            });
        }
    });
    a = appendArrayElement(a, 0);
    a = appendArrayElement(a, 0);

    return a;
}

/**
 * 
 * @param {type} targetArray
 * @param {type} indexA
 * @returns newArray
 */
function appendArrayElement(targetArray, indexA) {
    tmpArray = [];
    tmp = targetArray[indexA];
    var i;
    for (i = 0; i < targetArray.length; ++i) {
        tmpArray[i] = targetArray[i + 1];
    }
    tmpArray[targetArray.length - 1] = tmp;
    return tmpArray;
}

/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    if (typeof $('.mensaje-asiento-contable').data('id-cobros') !== "undefined") {
        updateFechaCobrosFromAsientoContable();
    }

    if (typeof $('.mensaje-asiento-contable').data('id-movimientos') !== "undefined") {
        updateFechaMovimientosFromAsientoContable();
    }
}


function exportCustom(table, tipo) {

    var data = Array();
    $(table).find('tbody').find('tr').each(function (e, v) {
        data[e] = Array();
        $(v).find('td:visible').not('.ctn_acciones').each(function (f, u) {
            data[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    data[e][f + index] = '';
                }
            }
        });
    });

    content = {
        content: {
            title: $(table).attr('dataexport-title'),
            sheets: {
                0: {
                    title: $(table).attr('dataexport-title'),
                    tables: {
                        0: {
                            title: $(table).attr('dataexport-title'),
                            titulo_alternativo: (typeof $(table).attr('dataexport-title-alternativo') !== typeof undefined && $(table).attr('dataexport-title-alternativo') !== false ? $(table).attr('dataexport-title-alternativo') : ''),
                            data: JSON.stringify(data),
                            headers: JSON.stringify(getHeadersTableCustom(table))
                        }
                    }
                }
            }
        }
    };

    open_window('POST', __AJAX_PATH__ + 'cobranza/export_' + tipo, content, '_blank');
}

function limpiar_entrada_de_datos() {
    $('#formulario-banco').hide();
    $('#formulario-cheque').hide();
    $('#formulario-retencion').hide();
    $('#select_tipoPago_multiple').select2('val', '0');
}

function verificar_fechas_banco_cheque(tab, filtrados, soloBanco) {
    var banco = [];
    var cheque = [];
    var str_banco = '';
    var str_cheque = '';
    var retencion = dt_getSelectedRows('#table-retenciones');
    if (tab == '#tab_1') {
        str_banco = '#table-banco';
        str_cheque = '#table-cheques';
//        banco = dt_getSelectedRows('#table-banco');
//        cheque = dt_getSelectedRows('#table-cheques');
    } else {
        str_banco = '#table-banco-a-imputar';
        str_cheque = '#table-cheques-a-imputar';
    }

    if (filtrados) {
        banco = dt_getFilteredRows(str_banco);
        //cheque = dt_getFilteredRows(str_cheque); 

    } else {
        banco = dt_getSelectedRows(str_banco);
        cheque = dt_getSelectedRows(str_cheque);
    }

    var misma_fecha = true;
    var renglones;
    if (!soloBanco) {
        renglones = banco.concat(cheque).concat(retencion);
    } else {
        renglones = banco;
    }
    if (renglones.length > 0) {
        var fecha_aux = renglones[0][0];
        for (var index = 0; index < renglones.length; index++) {
            //alert(renglones[index][0]);
            if ((misma_fecha) && !(fecha_aux === renglones[index][0])) {
                misma_fecha = false;
            }
        }
    }
    fecha_para_asiento = fecha_aux;
    return misma_fecha;
}



//function initExport() {
//    $('.export-rechazados').html("");
//    $('.export-rechazados').prepend(
//            '<div class="btn-group pull-right">\n\
//                <div class="btn-group">\n\
//                    <button class="btn btn-sm green excel-custom-rechazados" type="button">\n\
//                    <i class="fa fa-floppy-o"></i>\n\
//                    Exportar a Excel</button>\n\
//                </div>\n\
//            </div>');
//    $('.excel-custom-rechazados').on('click', function (e) {
//        e.preventDefault();
//        exportRechazados('excel');
//        e.stopPropagation();
//    });
//}

function exportRechazados() {
    var tabla_tab1 = Array();
    $('#tabla_tab1').find('tbody').find('tr').each(function (e, v) {
        tabla_tab1[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            tabla_tab1[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    tabla_tab1[e][f + index] = '';
                }
            }
        });
    });

    var tabla_tab2 = Array();
    $('#tabla_tab2').find('tbody').find('tr').each(function (e, v) {
        tabla_tab2[e] = Array();
        $(v).find('td').not('.ctn_acciones').each(function (f, u) {
            tabla_tab2[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    tabla_tab2[e][f + index] = '';
                }
            }
        });
    });

    var detalle_fecha = $('#div_renglones_rechazados').attr('detalle-fecha');

    content = {
        content: {
            title: $('#div_renglones_rechazados').attr('datafile-name'),
            sheets: {
                0: {
                    title: $('#tabla_tab1').attr('dataexport-title'),
                    tables: {
                        0: {
                            title: '',
                            titulo_alternativo: 'Movimientos rechazados del archivo ' + $('#div_renglones_rechazados').attr('datafile-name') + detalle_fecha,
                            data: JSON.stringify(tabla_tab1),
                            headers: JSON.stringify(getHeadersTableCustomRechazados($('#tabla_tab1')))
                        }
                    }
                },
                1: {
                    title: $('#tabla_tab2').attr('dataexport-title'),
                    tables: {
                        0: {
                            title: '',
                            titulo_alternativo: 'Valores a depositar del archivo ' + $('#div_renglones_rechazados').attr('datafile-name') + detalle_fecha,
                            data: JSON.stringify(tabla_tab2),
                            headers: JSON.stringify(getHeadersTableCustomRechazados($('#tabla_tab2')))
                        }
                    }
                }
            }
        }
    };

    open_window('POST', __AJAX_PATH__ + 'export_excel', content, '_blank');
}


function getHeadersTableCustomRechazados(table) {
    var a = Array();
    $(table).find('thead').find('tr[class=headers] th').each(function (e, v) {
        a.push({texto: $(v).text(),
            formato: $(v).attr('export-format') ? $(v).attr('export-format') : 'text'
        });
    });
    return a;
}

/**
 * 
 * @returns {undefined}
 */
function customDatepickerInit() {
    $('#fecha-asiento').datepicker("update", fecha_para_asiento);
}

function getTDValue(el_td) {
    return $(el_td).html().replace('$ ', '');
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
    });

    $('.money-format').each(function () {
        $(this).autoNumeric('update', {vMin: '-999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
    });
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

/**
 * 
 * @returns {undefined}
 */
function initMostrarDetalleSaldoSaldoAnticipoHandler() {

    // Handler al apretar el botón de lupa
    $(document).on('click', '.link-detalle-saldo-anticipo', function (e) {
        
        
        e.preventDefault();
        var id_anticipo = $(this).data('id-anticipo');
        
        var data = {
            id_anticipo: id_anticipo
        };
        
        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'rengloncobranza/detalleSaldoAnticipo/',
            data: data,
            success: function (tables) {

                var $contenidoDetalle = $('<div class="portlet-body">');

                $contenidoDetalle.append(tables);

                show_dialog({
                    titulo: 'Ver detalle de anticipo de cliente',
                    contenido: $contenidoDetalle,
                    callbackCancel: function () {
                        desbloquear();
                        return;
                    },
                    callbackSuccess: function () {
                        desbloquear();
                        return;
                    },
                    labelSuccess: 'Aceptar'
                });

                $('.cancel').remove();


  
            }
        });
    });

}    

/**
 * 
 * @returns {undefined}
 */
function initBuscarComprobanteHandler() {

    // Handler al apretar el botón de lupa
    $(document).on('click', '.buscar-cliente-para-codigo', function (e) {
        
        
        e.preventDefault();
        var codigo = $(this).data('codigo');
        
        var data = {
            codigo: codigo
        };
        
        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'rengloncobranza/buscarComprobante/',
            data: data,
            success: function (detalle) {

                var $contenidoDetalle = $('<div class="portlet-body">');

                $contenidoDetalle.append(detalle);

                show_dialog({
                    titulo: 'Ver comprobantes a partir de c&oacute;digo de barras',
                    contenido: $contenidoDetalle,
                    callbackCancel: function () {
                        desbloquear();
                        return;
                    },
                    callbackSuccess: function () {
                        desbloquear();
                        return;
                    },
                    labelSuccess: 'Aceptar'
                });

                $('.cancel').remove();


  
            }
        });
    });

}  