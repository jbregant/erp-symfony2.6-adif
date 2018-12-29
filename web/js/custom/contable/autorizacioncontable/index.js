
var chequeras = null;

var chequeraCuenta = null;

var dt_autorizacionesContables_column_index = {
    id: 0,
    multiselect: 1,
    fechaComprobante: 2,
    numero: 3,
    proveedor: 4,
    concepto: 5,
    montoBruto: 6,
    montoRetenciones: 7,
    montoNeto: 8,
    usuario: 9,
    estado: 10,
    acciones: 11,
    idOrdenPago: 12
};

$(document).ready(function () {

    initDataTable();

    initFiltroButton();

    initVisarButton();

    initVisarLinks();

    initAnularLinks();

    initPagarLinks();

    initEditarFechaAsientoContableHandler();

    initAgregarPagoHandler();

    initBorrarRenglonPago();
    
    initAgregarNetCash();

    initGenerarNetCash();

});

/**
 * 
 * @returns {undefined}
 */
function initDataTable() {

    var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
    var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();

    if (validarRangoFechas(fechaInicio, fechaFin)) {

        dt_autorizacionesContables = dt_datatable($('#table-autorizacioncontable'), {
            ajax: {
                url: __AJAX_PATH__ + 'autorizacioncontable/index_table/',
                data: function (d) {
                    d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_autorizacionesContables_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_autorizacionesContables_column_index.acciones,
                    "data": "actions",
                    "render": function (data, type, full, meta) {
                        var full_data = full[dt_autorizacionesContables_column_index.acciones];
                        return  (full_data.visar !== undefined ?
                                '<a href="' + full_data.visar + '" class="btn btn-xs yellow tooltips visar_autorizacion_contable" data-original-title="Autorizar pago">\n\
									<i class="fa fa-check"></i>\n\
								</a>' : '') +
                                '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
									<i class="fa fa-search"></i>\n\
								</a>' +
                                (full_data.pagar !== undefined ?
                                        '<a href="' + full_data.pagar + '" class="btn btn-xs purple-wisteria tooltips pagar_link" data-original-title="Pagar">\n\
											<i class="fa fa-usd"></i>\n\
										</a>' : '') +
                                '<a href="' + full_data.historico_general + '" class="btn btn-xs yellow-gold tooltips" data-original-title="Ver hist&oacute;rico general">\n\
									<i class="fa fa-list-ul"></i>\n\
								</a>' +
                                '<a href="' + full_data.imprimir + '" class="btn btn-xs dark tooltips" data-original-title="Imprimir">\n\
									<i class="fa fa-print"></i>\n\
								</a>' +
                                (full_data.anular !== undefined ?
                                        '<a href="' + full_data.anular + '" class="btn btn-xs red tooltips anular_autorizacion_contable" data-original-title="Anular">\n\
											<i class="fa fa-times"></i>\n\
										</a>' : '');
                    }
                },
                {
                    "targets": dt_autorizacionesContables_column_index.estado,
                    "createdCell": function (td, cellData, rowData, row, col) {

                        var full_data = rowData[dt_autorizacionesContables_column_index.estado];

                        $(td).addClass('state state-' + full_data.estadoClass);
                    },
                    "render": function (data, type, full, meta) {

                        var full_data = full[dt_autorizacionesContables_column_index.estado];

                        return  full_data.estado;
                    }
                },
                {
                    "targets": dt_autorizacionesContables_column_index.concepto,
                    "render": function (data, type, full, meta) {
                        return '<span class="truncate tooltips" data-original-title="' + data + '">'
                                + data + '</span>';
                    }
                },
                {
                    className: "nowrap",
                    targets: [
                        dt_autorizacionesContables_column_index.fechaComprobante,
                        dt_autorizacionesContables_column_index.numero,
                        dt_autorizacionesContables_column_index.proveedor,
                        dt_autorizacionesContables_column_index.montoBruto,
                        dt_autorizacionesContables_column_index.montoRetenciones,
                        dt_autorizacionesContables_column_index.usuario,
                        dt_autorizacionesContables_column_index.estado
                    ]
                },
                {
                    className: "text-center",
                    targets: [
                        dt_autorizacionesContables_column_index.multiselect
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_autorizacionesContables_column_index.acciones
                },
                {
                    className: "hidden",
                    targets: dt_autorizacionesContables_column_index.idOrdenPago
                },
                {
                    className: "nowrap montoNeto",
                    targets: dt_autorizacionesContables_column_index.montoNeto
                },
                {
                    className: "conceptoAC",
                    targets: dt_autorizacionesContables_column_index.concepto
                }
            ],
            "fnDrawCallback": function () {

                initEllipsis();

                // updateFiltroEstadoPago();
            }
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initFiltro() {

    var $fechaInicioLS = localStorage.getItem('fechaInicio');
    var $fechaFinLS = localStorage.getItem('fechaFin');

    var $fechaInicio = ($fechaInicioLS === null) ? getFirstDateOfCurrentMonth(ejercicioContableSesion) : new Date($fechaInicioLS.substring(6, 10), $fechaInicioLS.substring(3, 5) - 1, $fechaInicioLS.substring(0, 2));
    var $fechaFin = ($fechaFinLS === null) ? getEndingDateOfCurrentMonth(ejercicioContableSesion) : new Date($fechaFinLS.substring(6, 10), $fechaFinLS.substring(3, 5) - 1, $fechaFinLS.substring(0, 2));

    $('#adif_contablebundle_filtro_fechaInicio').datepicker("setDate", $fechaInicio);
    $('#adif_contablebundle_filtro_fechaFin').datepicker("setDate", $fechaFin);
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#filtrar').on('click', function (e) {
        var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
        var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();

        setFechasFiltro(fechaInicio, fechaFin);

        if (validarRangoFechas(fechaInicio, fechaFin)) {
            dt_autorizacionesContables.DataTable().ajax.reload();
        }
    });

}

/**
 * 
 * @returns {undefined}
 */
function initAnularLinks() {

    // BOTON ANULAR AUTORIZACION CONTABLE
    $(document).on('click', '.anular_autorizacion_contable', function (e) {

        e.preventDefault();
        var url = $(this).attr('href');
        show_confirm({
            msg: '¿Desea anular la autorización contable?',
            callbackOK: function () {
                window.location.href = url;
            }
        });
        e.stopPropagation();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initVisarButton() {

    $('#btn-autorizar-autorizacioncontable a[autorizacioncontable-table]').on('click', function (e) {

        e.preventDefault();

        bloquear();

        var table = $('#table-autorizacioncontable');

        var ids = [];

        switch ($(this).data('autorizar-autorizacioncontable')) {
            case _exportar_seleccionados:
                ids = dt_getSelectedRowsIds(table);
                break;
        }

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos una autorización contable para autorizar.'});

            desbloquear();

            return;
        }
        else {
            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'autorizacioncontable/autorizar-autorizaciones-contables/',
                data: {
                    ids: JSON.stringify(ids.toArray())
                }
            }).done(function (data, textStatus) {
                if (textStatus === 'success') {
                    location.href = __AJAX_PATH__ + 'autorizacioncontable/';
                }
                else {

                    desbloquear();

                    show_alert({
                        msg: 'Algunas autorizaciones contables no se autorizaron correctamente.',
                        title: 'Error al autorizar',
                        type: 'error'});
                }
            });
        }

        $('.bootbox').removeAttr('tabindex');

        e.stopPropagation();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initVisarLinks() {

    // BOTON VISAR AUTORIZACION CONTABLE
    $(document).on('click', '.visar_autorizacion_contable', function (e) {

        e.preventDefault();
        var url = $(this).attr('href');
        show_confirm({
            msg: '¿Desea visar la autorización contable?',
            callbackOK: function () {
                window.location.href = url;
            }
        });
        e.stopPropagation();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initPagarLinks() {

    $(document).on('click', '.pagar_link', function (e) {

        e.preventDefault();

        bloquear();

        id = $(this).prop("href").split('?id=')[1];

        pathPago = $(this).prop("href").split('?id=')[0];
        pathFormPagar = pathPago.toString().replace('pagar', 'form_pagar');

        totalNetoString = $(this).parents('td').parent().find('.montoNeto').html();
        totalNeto = parseFloat(clearCurrencyValue(totalNetoString));
        conceptoAC = $(this).parents('td').parent().find('.conceptoAC').html();
        totalAcumulado = 0;

        var ajax_dialog_pagar = $.ajax({
            type: 'POST',
            data: {
                id: id
            },
            url: pathFormPagar
        });

        $.when(ajax_dialog_pagar).done(function (dataDialogPagar) {

            var formulario_pagar = dataDialogPagar;

            headerDialog = 'Pagar: <span class="bold">' + conceptoAC + '<span class="pull-right">' + totalNetoString + '</span></span>';

            var d = show_dialog({
                titulo: headerDialog,
                contenido: formulario_pagar,
                callbackCancel: function () {
                    desbloquear();
                    return;
                },
                callbackSuccess: function () {
                    var formulario = $('#form_pagar').validate({
                        ignore: '.ignore'
                    });

                    var formulario_result = formulario.form();

                    if (formulario_result) {

                        if (totalAcumulado.toFixed(2) != totalNeto.toFixed(2)) {

                            var options = $.extend({
                                title: 'Ha ocurrido un error',
                                msg: "La suma total de los pagos no coincide con el monto neto de la autorizaci&oacute;n contable."
                            });

                            show_alert(options);

                            return false;
                        }

                        bloquear();

                        var aditionalData = {
                            'id': id
                        };
                        $('#form_pagar').addHiddenInputData(aditionalData);
                        formData = $('#form_pagar').serialize();

                        $.ajax({
                            url: pathPago,
                            type: 'POST',
                            data: formData
                        }).done(function (r) {
                            if (r.result === 'OK') {

                                stringPagoExitoso = 'Pago efectuado con &eacute;xito.';

                                if (typeof r.imprimir !== 'undefined') {
                                    stringPagoExitoso += ' ' + r.imprimir;
                                }

                                showFlashMessage('success', stringPagoExitoso, 0);

                                if (typeof r.msg !== 'undefined') {
                                    showFlashMessage('info', r.msg, 0);
                                }

                                App.scrollTop();

                                dt_autorizacionesContables.DataTable().ajax.reload();

                                desbloquear();

                                return false;
                            } else {

								var mensaje = '';
								if (typeof r.msg == 'undefined') {
									mensaje = r.message;
								} else {
									mensaje = r.msg;
								}
								console.debug("Mensaje = " + mensaje);
                                show_alert({
                                    msg: mensaje,
                                    title: 'Error en el pago', 
                                    type: 'error'
                                });

                                dt_autorizacionesContables.DataTable().ajax.reload();

                                desbloquear();
                            }

                        }).error(function (e) {

                            show_alert({
                                msg: 'Ocurri&oacute; un error al intentar efectuar el pago. Intente nuevamente.',
                                title: 'Error en el pago',
                                type: 'error'
                            });

                            desbloquear();
                        });
                    } else {

                        desbloquear();

                        return false;
                    }
                }


            });

            initFormularioPagar();
            $('.bootbox').removeAttr('tabindex');

            d.find('.modal-dialog').css('width', '90%');

            $('#agregar_renglon_pago').click();

            actualizarTotales();

            desbloquear();

        });

        desbloquear();
    });
}

/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    updateFechaOrdenPagoFromAsientoContable();

}

/**
 * 
 * @returns {undefined}
 */
function updateFiltroEstadoPago() {

    $('select.input-filter option[value="^Pagada$"]').remove();
    $('select.input-filter').append('<option value="^OP asignada$">OP asignada</option>');
    $('select.input-filter').select2('destroy');
    $('select.input-filter').select2();
}

/**
 * 
 * @returns {undefined}
 */
function initBorrarRenglonPago() {
    $(document).on('click', '.renglon_pago_borrar', function (e) {
        $(this).parents('.row_renglon_pago').remove();
        actualizarTotales();
    });
}

function initAgregarNetCash() {
    $('#btn-agregar-netcash').on('click', function (e) {
        e.preventDefault();

        bloquear();

        var table = $('#table-autorizacioncontable');
        var ids = dt_getSelectedRowsIds(table);
        var rows = dt_getSelectedRows(table);
        var estado_ok = true;
        var tipo_ok = true;

        var tipos_permitidos = ['obra', 'comprobante', 'pagoparcial'];

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos una autorización contable para agregar al netcash.'});
            desbloquear();
            return;
        } else {
            // Chequeo que todas las ACs estén en estado pendiente de pago y que sean de los tipos permitidos
            for (var index = 0; index < rows.length; index++) {
                estado_ok = estado_ok && (rows[index][8] == estadoPendientePago);
                var link_lupa = $(rows[index][9]).first().attr('href');
                var arr_link_lupa = link_lupa.split('/');
                tipo_ok = tipo_ok && ($.inArray(arr_link_lupa[arr_link_lupa.length - 2], tipos_permitidos) !== -1);
            }
            if (!estado_ok || !tipo_ok) {
                if (!estado_ok) {
                    show_alert({msg: 'Alguna de las autorizaciones contables no se encuentra en estado "Pendiente de pago"'});
                } else {
                    show_alert({msg: 'Alguna de las autorizaciones contables no pertenece a proveedores'});
                }
                desbloquear();
                return;
            } else {
                show_confirm({
                    msg: '¿Desea enviar las autorizaciones contables al Net Cash?',
                    callbackOK: function () {
                        $.ajax({
                            type: 'post',
                            url: __AJAX_PATH__ + 'autorizacioncontable/agregar-netcash/',
                            data: {
                                ids: JSON.stringify(ids.toArray())
                            }
                        }).done(function (data, textStatus) {
                            if (textStatus === 'success') {
                                location.href = __AJAX_PATH__ + 'autorizacioncontable/';
                            } else {
                                desbloquear();

                                show_alert({
                                    msg: 'Algunas autorizaciones contables no se agregaron al Net Cash correctamente.',
                                    title: 'Error al agregar a Net Cash',
                                    type: 'error'});
                            }
                        });
                    }
                });
                desbloquear();

                return false;
            }
        }

        $('.bootbox').removeAttr('tabindex');

        e.stopPropagation();
    });
}

function initGenerarNetCash() {
    $('#btn-generar-netcash').on('click', function (e) {
        e.preventDefault();

        bloquear();

        var table = $('#table-autorizacioncontable');
        var ids = dt_getSelectedRowsIds(table);
        var rows = dt_getSelectedRows(table);
        var estado_ok = true;

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos una autorización contable para generar el netcash.'});
            desbloquear();
            return;
        } else {
            for (var index = 0; index < rows.length; index++) {
                estado_ok = estado_ok && (rows[index][8] == estadoCorridaPendiente);
            }
            if (!estado_ok) {
                show_alert({msg: 'Alguna de las autorizaciones contables no se encuentra en estado "Net Cash corrida pendiente"'});
                desbloquear();
                return;
            } else {
                $.ajax({
                    type: 'POST',
                    url: __AJAX_PATH__ + 'netcash/datos-autorizaciones/',
                    data: {
                        ids: JSON.stringify(ids.toArray())
                    }
                }).done(function (form) {
                    show_dialog({
                        titulo: '<i class="fa fa-share"></i> ¿Desea generar el archivo NetCash de las siguientes autorizaciones contables?',
                        contenido: form,
                        color: 'blue',
                        labelCancel: 'Cancelar',
                        closeButton: false,
                        callbackCancel: function () {
                            return;
                        },
                        callbackSuccess: function () {
                            var formulario = $('form[name=generar_netcash]');

                            $('form[name="generar_netcash"]').validate({
                                ignore: '.ignore'
                            });

                            var formularioValido = formulario.validate().form();

                            if (formularioValido) {

                                var json = {
                                    'ids': JSON.stringify(ids.toArray())
                                };

                                formulario.addHiddenInputData(json);
                                formData = formulario.serialize();

                                bootbox.hideAll();

                                $.ajax({
                                    url: __AJAX_PATH__ + 'netcash/generar/',
                                    type: 'POST',
                                    data: formData
                                }).done(function (data) {
                                    if (data.result == 'OK') {
                                        showFlashMessage('success', 'Net Cash generado correctamente');         
                                        dt_autorizacionesContables.DataTable().ajax.reload();
                                        open_window('POST', __AJAX_PATH__ + 'netcash/exportar/' + data.id, null, '_blank');
                                    } else {
                                        showFlashMessage('danger', data.msg);
                                        return;
                                    }
                                    desbloquear();
                                });
                            } else {
                                return false;
                            }
                            return false;
                        }
                    });
                    $('.modal-dialog').css('width', '1000px');

                    initSelects();
                    initDatepickers();
                });
            }
        }

        $('.bootbox').removeAttr('tabindex');

        e.stopPropagation();
    });
}