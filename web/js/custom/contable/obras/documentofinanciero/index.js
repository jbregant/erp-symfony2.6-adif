
$(document).ready(function () {

    initDataTable();

    initLinks();

});

/**
 * 
 * @returns {undefined}
 */
function initDataTable() {

    var index = 0;

    var dt_obras_documentofinanciero_column_index = {
        id: index++,
        multiselect: index++,
        fechaCreacion: index++,
        tipoLicitacion: index++,
        numeroLicitacion: index++,
        anioLicitacion: index++,
        tramo: index++,
        proveedor: index++,
        tipoDocumentoFinanciero: index++,
        fechaDocumentoFinancieroInicio: index++,
        fechaDocumentoFinancieroFin: index++,
        fechaIngresoADIF: index++,
        montoSinIVA: index++,
        montoIVA: index++,
        montoFondoReparo: index++,
        montoTotalDocumentoFinanciero: index++,
        fechaAnulacion: index++,
        acciones: index
    };

    dt_obras_documentofinanciero = dt_datatable($('#table-obras_documentofinanciero'), {
        ajax: __AJAX_PATH__ + 'documento_financiero/index_table/',
        columnDefs: [
            {
                "targets": dt_obras_documentofinanciero_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                }
            },
            {
                "targets": dt_obras_documentofinanciero_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {

                    var full_data = full[dt_obras_documentofinanciero_column_index.acciones];

                    return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                                <i class="fa fa-search"></i>\n\
                            </a>'
                            +
                            (full_data.correspondePago !== undefined ?
                                    '<a href="' + full_data.correspondePago + '" class="btn btn-xs yellow tooltips corresponde-pago-link" data-original-title="Corresponde pago">\n\
                                        <i class="fa fa-check"></i>\n\
                                    </a>' : '')
                            +
                            (full_data.noCorrespondePago !== undefined ?
                                    '<a href="' + full_data.noCorrespondePago + '" class="btn btn-xs yellow-gold tooltips corresponde-pago-link" data-original-title="No corresponde pago">\n\
                                        <i class="fa fa-ban"></i>\n\
                                    </a>' : '')
                            +
                            (full_data.edit !== undefined ?
                                    '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                        <i class="fa fa-pencil"></i>\n\
                                    </a>' : '') +
                            (full_data.edit_fecha_aprobacion_tecnica !== undefined ?
                                    '<a href="' + full_data.edit_fecha_aprobacion_tecnica + '" class="btn btn-xs yellow tooltips editar_fecha_aprobacion_tecnica_modal" data-original-title="Editar Fecha Aprobación Técnica">\n\
                                        <i class="fa fa-pencil"></i>\n\
                                    </a>' : '') +
                            (full_data.anular !== undefined ?
                                    '<a href="' + full_data.anular + '" class="btn btn-xs red accion-anular tooltips" data-original-title="Anular">\n\
                                        <i class="fa fa-times"></i>\n\
                                    </a>' : '');
                }
            },
            {
                "targets": dt_obras_documentofinanciero_column_index.tramo,
                "render": function (data, type, full, meta) {
                    return '<span class="truncate tooltips" data-original-title="' + data + '">'
                            + data + '</span>';
                }
            },
            {
                className: "nowrap",
                targets: [
                    dt_obras_documentofinanciero_column_index.fechaCreacion,
                    dt_obras_documentofinanciero_column_index.tipoLicitacion,
                    dt_obras_documentofinanciero_column_index.numeroLicitacion,
                    dt_obras_documentofinanciero_column_index.anioLicitacion,
                    dt_obras_documentofinanciero_column_index.proveedor,
                    dt_obras_documentofinanciero_column_index.tipoDocumentoFinanciero,
                    dt_obras_documentofinanciero_column_index.fechaDocumentoFinancieroInicio,
                    dt_obras_documentofinanciero_column_index.fechaDocumentoFinancieroFin,
                    dt_obras_documentofinanciero_column_index.fechaIngresoADIF,
                    dt_obras_documentofinanciero_column_index.montoSinIVA,
                    dt_obras_documentofinanciero_column_index.montoIVA,
                    dt_obras_documentofinanciero_column_index.montoFondoReparo,
                    dt_obras_documentofinanciero_column_index.montoTotalDocumentoFinanciero,
                    dt_obras_documentofinanciero_column_index.fechaAnulacion
                ]
            },
            {
                className: "text-center",
                targets: [
                    dt_obras_documentofinanciero_column_index.multiselect
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_obras_documentofinanciero_column_index.acciones
            }
        ],
        "fnDrawCallback": function () {
            initEllipsis();
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initLinks() {

    // BOTON CORRESPONDE PAGO / NO CORRESPONDE PAGO
    $(document).on('click', '.corresponde-pago-link', function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        blockPageContent();

        show_confirm({
            msg: '¿Desea modificar la correspondencia del pago?',
            callbackOK: function () {
                window.location.href = url;
            },
            callbackCancel: function () {
                unblockPageContent();
            }
        });

        e.stopPropagation();
    });
    
    $(document).on('click', '.editar_fecha_aprobacion_tecnica_modal', function (e) {

        e.preventDefault();
       
        var id = $(this).prop("href").split('?id=')[1];
        var pathModal = $(this).prop("href").split('?id=')[0];
        
        var modal = $.ajax({
            type: 'POST',
            data: {
                id: id
            },
            url: pathModal
        });
        
        $.when(modal).done(function (dataDialogEditarFechaAprobacionTecnica) {
            
            var formulario_editar = dataDialogEditarFechaAprobacionTecnica;
            
            var dialog = show_dialog({
                titulo: 'Editar fecha de aprobaci&oacute;n t&eacute;cnica al documento financiero',
                contenido: formulario_editar,
                callbackCancel: function () {
                    desbloquear();
                    return;
                },
                callbackSuccess: function () {
                    
                    var formulario = $('#form_editar_fecha_aprobacion_tecnica').validate();
                    var formulario_result = formulario.form();
                    
                    if (formulario_result) {
                        
                        bloquear();

                        var formData = $('#form_editar_fecha_aprobacion_tecnica').serialize();
                        var editPath = $("#documento_financiero_update_fecha_aprobacion_tecnica_path_ajax").val();
                        
                        $.ajax({
                            url: editPath,
                            type: 'POST',
                            data: formData,
                            success: function(response){
                                if (response.result = 'OK') {
                                    showFlashMessage('success', response.msg, 0);
                                    App.scrollTop();
                                    desbloquear();
                                    return false;
                                } else {
                                     show_alert({
                                        msg: response.msg,
                                        title: 'Error al editar',
                                        type: 'error',
                                        message: response.msg
                                    });
                                    desbloquear();
                                }
                            }
                        });
                    } else {
                        return false;
                    }
                }
            });
            
        });
        
        
    });
}