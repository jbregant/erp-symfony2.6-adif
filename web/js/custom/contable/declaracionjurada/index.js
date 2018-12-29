
var dt_retenciones_column_index = {
    id: 0,
    multiselect: 1,
    impuesto: 2,
    regimen: 3,
    beneficiario: 4,
    fechaOP: 5,
    numeroOP: 6,
    importe: 7,
    acciones: 8
};

var dt_pagos_a_cuenta_column_index = {
    id: 0,
    multiselect: 1,
    tipo: 2,
    fecha: 3,
    importe: 4
};

$(document).ready(function () {

    initTable();

    initPagoCuentaButton();

    initDeclaracionJuradaButton();
    
    initDevolucionButton();
});

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    columns_retenciones = [
        {
            "targets": dt_retenciones_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_retenciones_column_index.multiselect];

                return (full_data.estado === __ESTADO_RENGLON_DDJJ_PENDIENTE ? '<input type="checkbox" class="checkboxes" value="' + full_data.id + '" />' : null);
            }
        },
        {
            "targets": dt_retenciones_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_retenciones_column_index.acciones];
                return '<a href="#" data-id-renglon-ddjj="' + full_data.id + '" data-monto-renglon-ddjj="' + full_data.monto + '" class="btn btn-xs blue tooltips button-devolucion" data-original-title="Generar devoluci&oacute;n">\n\
                            <i class="fa fa-reply"></i>\n\
                        </a>';
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_retenciones_column_index.impuesto,
                dt_retenciones_column_index.regimen,
                dt_retenciones_column_index.fechaOP,
                dt_retenciones_column_index.numeroOP,
                dt_retenciones_column_index.importe

            ]
        },
        {
            className: "text-center",
            targets: [
                dt_retenciones_column_index.multiselect
            ]
        }
    ];

    columns_pagos_a_cuenta = [
        {
            "targets": dt_pagos_a_cuenta_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_pagos_a_cuenta_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones nowrap text-center",
            targets: dt_pagos_a_cuenta_column_index.acciones
        }
    ];

    dt_retenciones_iva = dt_datatable($('#table-retenciones-iva-y-ganancias'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/index_table_renglones_ddjj',
            data: {impuestos: [denominacionIVA, denominacionGanancias]}
        },
        columnDefs: columns_retenciones
    });

    dt_retenciones_suss = dt_datatable($('#table-retenciones-suss'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/index_table_renglones_ddjj',
            data: {impuestos: [denominacionSUSS]}
        },
        columnDefs: columns_retenciones
    });

    dt_retenciones_iibb = dt_datatable($('#table-retenciones-iibb'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/index_table_renglones_ddjj',
            data: {impuestos: [denominacionIIBB]}
        },
        columnDefs: columns_retenciones
    });

    dt_retenciones_sicoss = dt_datatable($('#table-retenciones-sicoss'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/index_table_renglones_ddjj',
            data: {impuestos: [denominacionSICOSS]}
        },
        columnDefs: columns_retenciones
    });

    dt_pagos_a_cuenta_iva = dt_datatable($('#table-pagos-a-cuenta-iva-y-ganancias'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/index_table_pagos_a_cuenta',
            data: {tipo: tipoSicore}
        },
        columnDefs: columns_pagos_a_cuenta
    });

    dt_pagos_a_cuenta_suss = dt_datatable($('#table-pagos-a-cuenta-suss'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/index_table_pagos_a_cuenta',
            data: {tipo: tipoSijp}
        },
        columnDefs: columns_pagos_a_cuenta
    });

    dt_pagos_a_cuenta_iibb = dt_datatable($('#table-pagos-a-cuenta-iibb'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/index_table_pagos_a_cuenta',
            data: {tipo: tipoIIBB}
        },
        columnDefs: columns_pagos_a_cuenta
    });

    dt_pagos_a_cuenta_sicoss = dt_datatable($('#table-pagos-a-cuenta-sicoss'), {
        ajax: {
            url: __AJAX_PATH__ + 'declaracion_jurada/index_table_pagos_a_cuenta',
            data: {tipo: tipoSICOSS}
        },
        columnDefs: columns_pagos_a_cuenta
    });

    $(document).ready(function () {
        $('#tab_ddjj').bootstrapWizard({
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initPagoCuentaButton() {
    $('.button-pago-cuenta').on('click', function (e) {
        e.preventDefault();
        bloquear();

        var table = $(this).closest('.portlet-body').find('table');
        var ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un renglón.'});
            desbloquear();
            return;
        } else {
            show_confirm({
                msg: '¿Desea generar el pago a cuenta?',
                callbackOK: function () {
                    var tipoDeclaracionJurada = table.data('tipo-ddjj');
                    open_window(
                            'post',
                            __AJAX_PATH__ + 'declaracion_jurada/crear-pago-cuenta/',
                            {
                                ids: JSON.stringify(ids.toArray()),
                                tipo_declaracion_jurada: tipoDeclaracionJurada
                            }
                    );
                }
            });

            desbloquear();
        }

        $('.bootbox').removeAttr('tabindex');
        e.stopPropagation();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initDeclaracionJuradaButton() {

    $('.button-ddjj').on('click', function (e) {

        e.preventDefault();

        var renglonesDeclaracionJuradaTable = $(this).closest('.tab-pane').find('.table-retenciones');
        var renglonesDeclaracionJuradaIds = dt_getSelectedRowsIds(renglonesDeclaracionJuradaTable);

        var pagosACuentaTable = $(this).closest('.tab-pane').find('.table-pagos-a-cuenta');
        var pagosACuentaIds = dt_getSelectedRowsIds(pagosACuentaTable);

        if (!renglonesDeclaracionJuradaIds.length && !pagosACuentaIds.length) {
            show_alert({msg: 'Debe seleccionar al menos un renglón.'});

            desbloquear();

            return;
        }
        else {
            show_confirm({
                msg: '¿Desea generar la declaración jurada?',
                callbackOK: function () {

                    var tipoDeclaracionJurada = renglonesDeclaracionJuradaTable.data('tipo-ddjj');

                    open_window(
                            'post',
                            __AJAX_PATH__ + 'declaracion_jurada/crear-ddjj/',
                            {
                                renglones_declaracion_jurada_ids: JSON.stringify(renglonesDeclaracionJuradaIds.toArray()),
                                renglones_pago_a_cuenta_ids: JSON.stringify(pagosACuentaIds.toArray()),
                                tipo_declaracion_jurada: tipoDeclaracionJurada
                            }

                    );
                }
            });

            desbloquear();
        }

        $('.bootbox').removeAttr('tabindex');

        e.stopPropagation();
    });
}

function initDevolucionButton(){
    $(document).on('click', '.button-devolucion', function (e) {
        e.preventDefault();
        bloquear();
        
        var id_renglon = $(this).data('id-renglon-ddjj');
        var monto_renglon = $(this).data('monto-renglon-ddjj');

        formulario_devolucion = '<form id="form_devolucion" method="post" name="form_devolucion" action="' + __AJAX_PATH__ + 'declaracion_jurada/crear-devolucion/">\n\
                                    <div class="row">\n\
                                        <div class="col-md-12">\n\
                                            <div class="form-group">\n\
                                                <label class="control-label required" for="monto_devolucion">Monto devoluci&oacute;n</label>\n\
                                                <div class="input-icon right">\n\
                                                    <i class="fa"></i>\n\
                                                    <input type="hidden" id="id_renglon" name="id_renglon" value="' + id_renglon + '" \>\n\
                                                    <input type="text" id="monto_devolucion" name="monto_devolucion" class="required form-control numberPositive" value="' + monto_renglon + '" \>\n\
                                                </div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </form>';
        show_dialog({
            titulo: 'Devoluci&oacute;n de retenci&oacute;n',
            contenido: formulario_devolucion,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=form_devolucion]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {
                    $('form[name=form_devolucion]').submit();
                } else {
                    return false;
                }
            }
        });
        
        initCurrencies();
        
        $('form[name=form_devolucion]').validate();
        
        $('#monto_devolucion').rules('add', {
            valor_maximo: parseFloat(monto_renglon)
        });
        desbloquear();

        e.stopPropagation();
    });    
}