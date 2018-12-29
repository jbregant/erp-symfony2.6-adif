
var index = 0;

var dt_pagos_column_index = {
    id: index++,
    multiselect: index++,
    banco: index++,
    numeroSucursalYCuenta: index++,
    saldoBancario: index++,
    fechaExtracto: index++,
    montoAGenerar: index++,
    montoALaFirma: index++,
    montoEnCartera: index++,
    montoRetirado: index++,
    montoChequesPendientes: index++,
    montoIngresosPendientes: index++,
    saldoFinanciero: index++
};

$(document).ready(function () {

    initFiltro();

    initDataTable();

    initFiltroButton();

    initCancelarCambiosPendientes();

    initGuardarCambiosIngresosPendientes();
    initGuardarCambiosChequesPendientes();

});

/**
 * 
 * @returns {undefined}
 */
function  initDataTable() {

    dt_cuentasBancariasADIF = dt_datatable($('#reporte_table'), {
        ajax: {
            url: __AJAX_PATH__ + 'pagos/filtrar_reporte_parte_saldos/',
            data: function (d) {
                d.fechaPago = $('#adif_contablebundle_filtro_fechaPago').val();
                d.fechaExtracto = $('#adif_contablebundle_filtro_fechaExtracto').val();
            }
        },
        columnDefs: [
            {
                "targets": dt_pagos_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                }
            },
            {
                "targets": dt_pagos_column_index.montoIngresosPendientes,
                "createdCell": function (td, cellData, rowData, row, col) {

                    $(td).addClass('ingresoPendiente');

                    $(td).css({minWidth: "140px"});
                },
                "render": function (data, type, full, meta) {

                    return  '<a class="btn btn-xs btn-circle green pull-left tooltips editar_ingreso_pendiente" \n\
                                data-original-title="Editar">\n\
                                <i class="fa fa-pencil"></i>\n\
                             </a>'
                            + '<span class="monto">' + full[dt_pagos_column_index.montoIngresosPendientes] + '</span>'
                            ;
                }
            },
            {
                "targets": dt_pagos_column_index.montoChequesPendientes,
                "createdCell": function (td, cellData, rowData, row, col) {

                    $(td).addClass('chequePendiente');

                    $(td).css({minWidth: "140px"});
                },
                "render": function (data, type, full, meta) {

                    return  '<a class="btn btn-xs btn-circle green pull-left tooltips editar_cheques_pendiente" \n\
                                data-original-title="Editar">\n\
                                <i class="fa fa-pencil"></i>\n\
                             </a>'
                            + '<span class="monto">' + full[dt_pagos_column_index.montoChequesPendientes] + '</span>'
                            ;
                }
            },
            {
                className: "saldoBancario",
                targets: [
                    dt_pagos_column_index.saldoBancario
                ]
            },
            {
                className: "monto-resta",
                targets: [
                    dt_pagos_column_index.montoAGenerar,
                    dt_pagos_column_index.montoALaFirma,
                    dt_pagos_column_index.montoEnCartera,
                    dt_pagos_column_index.montoRetirado
                ]
            },
            {
                className: "text-right bold",
                targets: [
                    dt_pagos_column_index.montoIngresosPendientes,
                    dt_pagos_column_index.montoChequesPendientes
                ]
            },
            {
                className: "bold",
                targets: [
                    dt_pagos_column_index.banco,
                    dt_pagos_column_index.numeroSucursalYCuenta
                ]
            },
            {
                className: "text-right",
                targets: [
                    dt_pagos_column_index.saldoBancario,
                    dt_pagos_column_index.montoAGenerar,
                    dt_pagos_column_index.montoALaFirma,
                    dt_pagos_column_index.montoEnCartera,
                    dt_pagos_column_index.montoRetirado
                ]
            },
            {
                className: "text-right bold saldoFinanciero",
                targets: [
                    dt_pagos_column_index.saldoFinanciero
                ]
            },
            {
                className: "fechaExtracto",
                targets: [
                    dt_pagos_column_index.fechaExtracto
                ]
            }
        ],
        "fnDrawCallback": function () {
            validateFechaExtracto();

            initEditarIngresosPendientesButton();
            initEditarChequesPendientesButton();
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initFiltro() {

    var $fechaReporte = getDateFromString(getCurrentDate());

    var currentDay = getDateFromString(getCurrentDate());

    currentDay.setDate(currentDay.getDate() - 1);

    var $fechaExtracto = currentDay;

    $('#adif_contablebundle_filtro_fechaReporte')
            .datepicker("setDate", $fechaReporte);

    $('#adif_contablebundle_filtro_fechaExtracto')
            .datepicker("setDate", $fechaExtracto);

    $('#adif_contablebundle_filtro_fechaPago')
            .datepicker("setDate", $fechaExtracto);

    $('#adif_contablebundle_filtro_fechaPago')
            .datepicker("setStartDate", $fechaExtracto);

    $('#adif_contablebundle_filtro_fechaPago')
            .datepicker("setEndDate", $fechaReporte);

    readonlyDatePicker($('#adif_contablebundle_filtro_fechaReporte'), true);

    readonlyDatePicker($('#adif_contablebundle_filtro_fechaExtracto'), true);
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {

    $('#filtrar').on('click', function (e) {

        dt_cuentasBancariasADIF.DataTable().ajax.reload();
    });
}

/**
 * 
 * @returns {undefined}
 */
function validateFechaExtracto() {

    var fechaExtracto = $('#adif_contablebundle_filtro_fechaExtracto').val();

    var reporteInvalido = false;

    $('td.fechaExtracto').each(function () {

        if ($(this).text() !== fechaExtracto) {

            reporteInvalido = true;

            $(this).addClass('danger');
        }

    });

    if (reporteInvalido) {

        showFlashMessage('danger', 'Algunas fechas de extracto no coinciden con las fechas correspondientes al informe');
    }

}

/**
 * 
 * @returns {undefined}
 */
function initEditarIngresosPendientesButton() {

    $('.editar_ingreso_pendiente').off().on('click', function (e) {

        e.preventDefault();

        // Obtengo el TR del RenglonAsientoContable clickeado
        var nRow = $(this).parents('tr')[0];

        var tdIngresoPendiente = $(nRow).find('.ingresoPendiente');

        crearCampoEditable(tdIngresoPendiente, 'tdIngresoPendienteEditable', 'currency');

        initCurrencies();

        tdIngresoPendiente
                .prepend('<a class="btn btn-xs blue tooltips pull-left guardar_cambios_ingresos" style="margin-right: .3em;" data-original-title="Confirmar">\n\
                            <i class="fa fa-check"></i>\n\
                       </a>\n\
                        <a class="btn btn-xs red-thunderbird pull-left tooltips cancelar_cambios_ingresos" style="margin-right: .3em;" data-original-title="Cancelar" >\n\
                            <i class="fa fa-times"></i>\n\
                       </a>');
    });
}

/**
 * 
 * @returns {undefined}
 */
function initEditarChequesPendientesButton() {

    $('.editar_cheques_pendiente').off().on('click', function (e) {

        e.preventDefault();

        // Obtengo el TR del RenglonAsientoContable clickeado
        var nRow = $(this).parents('tr')[0];

        var tdChequePendiente = $(nRow).find('.chequePendiente');

        crearCampoEditable(tdChequePendiente, 'tdChequePendienteEditable', 'currency');

        initCurrencies();

        tdChequePendiente
                .prepend('<a class="btn btn-xs blue tooltips pull-left guardar_cambios_cheques" style="margin-right: .3em;" data-original-title="Confirmar">\n\
                            <i class="fa fa-check"></i>\n\
                       </a>\n\
                        <a class="btn btn-xs red-thunderbird pull-left tooltips cancelar_cambios_cheques" style="margin-right: .3em;" data-original-title="Cancelar" >\n\
                            <i class="fa fa-times"></i>\n\
                       </a>');
    });
}

/**
 * 
 * @param {type} tdObject
 * @param {type} clase
 * @param {type} tipo
 * @returns {undefined}
 */
function crearCampoEditable(tdObject, clase, tipo) {

    var value = clearCurrencyValue(tdObject.find('.monto').text());

    tdObject.html('<input type="text" class="input ' + clase + ' ' + tipo + '" value="' + value + '">');
}

/**
 * 
 * @returns {undefined}
 */
function initGuardarCambiosIngresosPendientes() {

    $(document).on('click', '.guardar_cambios_ingresos', function (e) {

        e.stopPropagation();

        e.preventDefault();

        var $td = $(this).parents('td');

        var nuevoMontoIngresoPendiente = $td.find('.tdIngresoPendienteEditable ').val();

        $.ajax({
            type: "POST",
            data: {id: dt_cuentasBancariasADIF.DataTable().row($(this).parents('tr')).data()[dt_pagos_column_index.id], monto: nuevoMontoIngresoPendiente.replace(',', '.')},
            url: __AJAX_PATH__ + 'cuentas_adif/actualizarIngresosPendientes'
        }).done(function (r) {
            if (r.result === 'OK') {

                if (typeof r.msg !== 'undefined') {
                    showFlashMessage('info', r.msg);
                }

                App.scrollTop();

                dt_cuentasBancariasADIF.DataTable().ajax.reload();

                desbloquear();

                return false;
            } else {

                show_alert({
                    msg: r.msg,
                    title: 'Error en la modificaci&oacute;n',
                    type: 'error'
                });

                dt_cuentasBancariasADIF.DataTable().ajax.reload();

                desbloquear();
            }

        }).error(function (e) {

            show_alert({
                msg: 'Ocurri&oacute; un error al intentar modificar los ingresos pendientes. Intente nuevamente.',
                title: 'Error en el pago',
                type: 'error'
            });

            desbloquear();
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initGuardarCambiosChequesPendientes() {

    $(document).on('click', '.guardar_cambios_cheques', function (e) {

        e.stopPropagation();

        e.preventDefault();

        var $td = $(this).parents('td');

        var nuevoMontoChequePendiente = $td.find('.tdChequePendienteEditable ').val();

        $.ajax({
            type: "POST",
            data: {id: dt_cuentasBancariasADIF.DataTable().row($(this).parents('tr')).data()[dt_pagos_column_index.id], monto: nuevoMontoChequePendiente.replace(',', '.')},
            url: __AJAX_PATH__ + 'cuentas_adif/actualizarChequesPendientes'
        }).done(function (r) {
            if (r.result === 'OK') {

                if (typeof r.msg !== 'undefined') {
                    showFlashMessage('info', r.msg);
                }

                App.scrollTop();

                dt_cuentasBancariasADIF.DataTable().ajax.reload();

                desbloquear();

                return false;
            } else {

                show_alert({
                    msg: r.msg,
                    title: 'Error en la modificaci&oacute;n',
                    type: 'error'
                });

                dt_cuentasBancariasADIF.DataTable().ajax.reload();

                desbloquear();
            }

        }).error(function (e) {

            show_alert({
                msg: 'Ocurri&oacute; un error al intentar modificar los cheques pendientes. Intente nuevamente.',
                title: 'Error en el pago',
                type: 'error'
            });

            desbloquear();
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initCancelarCambiosPendientes() {

    $(document).on('click', '.cancelar_cambios_ingresos, .cancelar_cambios_cheques', function (e) {

        e.stopPropagation();

        e.preventDefault();

        dt_cuentasBancariasADIF.DataTable().ajax.reload();
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
            .replace(/\./g, '')
            .replace(/\,/g, '.')
            .trim();
}
