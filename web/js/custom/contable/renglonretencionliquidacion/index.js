var dt_renglones_column_index = {
    id: 0,
    multiselect: 1,
    liquidacion: 2,
    beneficiario: 3,
    concepto: 4,
    monto: 5,
    acciones: 6,
    id_beneficiario: 7
};

$(document).ready(function () {
    initTable();
    initACButton();
    initShowDetalleRenglonLink();
});

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    columns_renglones = [
        {
            "targets": dt_renglones_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_renglones_column_index.multiselect];

                return '<input type="checkbox" class="checkboxes" value="' + full_data.id + '" />';
            }
        },
        {
            "targets": dt_renglones_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_renglones_column_index.acciones];

                return '<a href="' + full_data.show + '" data-id-renglon="' + full_data.id + '" class="btn btn-xs blue tooltips detalle_renglon_link" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_renglones_column_index.multiselect
            ]
        },
        {
            className: "hidden",
            targets: [
                dt_renglones_column_index.id_beneficiario
            ]
        }
    ];

    dt_renglones_apdfa = dt_datatable($('#table-retenciones-1'), {
        ajax: {
            url: __AJAX_PATH__ + 'renglonesretencionliquidacion/index_table/',
            data: {beneficiario: __APDFA, historico: historico}
        },
        columnDefs: columns_renglones
    });

    dt_renglones_uf = dt_datatable($('#table-retenciones-2'), {
        ajax: {
            url: __AJAX_PATH__ + 'renglonesretencionliquidacion/index_table/',
            data: {beneficiario: __UF, historico: historico}
        },
        columnDefs: columns_renglones
    });

    dt_renglones_nacion = dt_datatable($('#table-retenciones-3'), {
        ajax: {
            url: __AJAX_PATH__ + 'renglonesretencionliquidacion/index_table/',
            data: {beneficiario: __NACION, historico: historico}
        },
        columnDefs: columns_renglones
    });
    
    dt_renglones_otros = dt_datatable($('#table-retenciones-4'), {
        ajax: {
            url: __AJAX_PATH__ + 'renglonesretencionliquidacion/index_table/',
            data: {beneficiario: __OTROS, historico: historico}
        },
        columnDefs: columns_renglones
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
function initACButton() {

    $('.button-ac').on('click', function (e) {

        e.preventDefault();

        var renglonesRetencionesTable = $(this).closest('.tab-pane').find('.table-retenciones');
        var renglonesRetencionesIds = dt_getSelectedRowsIds(renglonesRetencionesTable);
        
        if (!renglonesRetencionesIds.length) {
            show_alert({msg: 'Debe seleccionar al menos un renglón.'});
            desbloquear();
            return;
        } else {            
            var renglonesRetenciones = dt_getSelectedRows(renglonesRetencionesTable);
            var id_beneficiario = null;
            var mismo_beneficiario = true;
            
            for (var index = 0; index < renglonesRetenciones.length; index++) {
                var renglon = renglonesRetenciones[index];
                if(id_beneficiario === null){
                    id_beneficiario = renglon[5];
                }
                if(id_beneficiario !== renglon[5]){
                    mismo_beneficiario = false;
                }
            }
            
            if(!mismo_beneficiario){
                show_alert({msg: 'Debe seleccionar renglones del mismo beneficiario.'});
            } else {
                show_confirm({
                    msg: '¿Desea generar la autorizaci&oacute;n contable?',
                    callbackOK: function () {
                        open_window(
                                'post',
                                __AJAX_PATH__ + 'renglonesretencionliquidacion/crear-ac/',
                                {
                                    renglones_retenciones_ids: JSON.stringify(renglonesRetencionesIds.toArray())
                                }

                        );
                    }
                });
            }

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
function initShowDetalleRenglonLink() {

    $(document).on('click', '.detalle_renglon_link', function (e) {

        e.preventDefault();

        bloquear();

        idRenglon = $(this).data('id-renglon');

        var ajaxDialog = $.ajax({
            type: 'POST',
            data: {
                id: idRenglon
            },
            url: __AJAX_PATH__ + 'renglonesretencionliquidacion/form_detalle/'
        });

        $.when(ajaxDialog).done(function (dataDialog) {
            var formulario = dataDialog;

            show_dialog({
                titulo: 'Detalle del rengl&oacute;n',
                contenido: formulario,
                labelCancel: 'Aceptar',
                callbackCancel: function () {
                    desbloquear();
                    return;
                },
                callbackSuccess: function () {
                    var formulario_result = formulario.form();

                    if (formulario_result) {
                        bloquear();
                    } else {
                        desbloquear();
                        return false;
                    }
                }

            });
            
            $('.modal-footer').find('.success').remove();
            
            $('.modal-dialog').css('width', '80%');

            dt_init($('.table-empleados'));

        });
    });
}