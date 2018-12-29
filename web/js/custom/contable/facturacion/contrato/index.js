var index = 0;
        
var dt_contratos_column_index = {
    id: index++,
    multiselect: index++,
    claseContrato: index++,
    codigoTipoMoneda: index++, //1
    numeroContrato: index++,
    numeroCarpeta: index++,
    cuitAndRazonSocial: index++,
    fechaInicio: index++,
    fechaFin: index++,
    fechaDesocupacion: index++,
    saldo: index++,
    estado: index++,
    estadoContrato: index++,
    esContratoAlquiler: index++, //10
    esContratoVentaPlazo: index++, //11
    tipoMoneda: index++, //12
    acciones: index++
};

dt_contrato = dt_datatable($('#table-contrato'), {
    order: [1, 'desc'],
    ajax: __AJAX_PATH__ + 'contrato/index_table/',
    columnDefs: [
        {
            "targets": dt_contratos_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes not-checkbox-transform" value="' + data + '" />';
            }
        },
        {
            "targets": dt_contratos_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_contratos_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                                </a>' : '') +
                        (full_data.adendar !== undefined ?
                                '<a href="' + full_data.adendar + '" class="btn btn-xs blue-madison tooltips" data-original-title="Adendar">\n\
                            <i class="fa fa-letter">A</i>\n\
                        </a>' : '') +
                        (full_data.prorroga !== undefined ?
                                '<a href="' + full_data.prorroga + '" class="btn btn-xs red-flamingo tooltips" data-original-title="Prorrogar">\n\
                            <i class="fa fa-letter">PR</i>\n\
                        </a>' : '') +
                        (full_data.historico !== undefined ?
                                '<a href="' + full_data.historico + '" class="btn btn-xs grey-cascade tooltips anular_autorizacion_contable" data-original-title="Ver hist&oacute;rico">\n\
                            <i class="fa fa-exchange"></i>\n\
                        </a>' : '') +
                        (full_data.comprobante !== undefined ?
                                '<a href="' + full_data.comprobante + '" class="btn btn-xs purple tooltips" data-original-title="Generar comprobante">\n\
                            <i class="fa fa-plus-circle"></i>\n\
                        </a>' : '') +
                        (full_data.delete !== undefined ?
                                '<a href="' + full_data.delete + '" class="btn btn-xs red accion-borrar tooltips" data-original-title="Eliminar">\n\
                            <i class="fa fa-trash"></i>\n\
                                </a>' : '');
            }
        },
        {
            "targets": dt_contratos_column_index.estado,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_contratos_column_index.estado];

                $(td).addClass(full_data.estadoClass);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_contratos_column_index.estado];

                return  full_data.estado;
            }
        },
        {
            "targets": dt_contratos_column_index.saldo,
            "render": function (data, type, full, meta) {
                var full_data = full[dt_contratos_column_index.saldo];
                return full_data.saldo
                        +
                        '<a href="' + full_data.linkSaldo + '"class="pull-right tooltips link-detalle-saldo" \n\
                                data-original-title="Ver detalle del saldo">\n\
                                <i class="fa fa-search-plus font-green-seagreen"></i>\n\
                        </a>'
                        ;
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_contratos_column_index.claseContrato,
                dt_contratos_column_index.codigoTipoMoneda,
                dt_contratos_column_index.numeroContrato,
                dt_contratos_column_index.numeroCarpeta,
                dt_contratos_column_index.fechaInicio,
                dt_contratos_column_index.fechaFin,
                dt_contratos_column_index.fechaDesocupacion,
                dt_contratos_column_index.saldo,
                dt_contratos_column_index.estado
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_contratos_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_contratos_column_index.acciones
        },
        {
            className: "hidden",
            targets: [
                dt_contratos_column_index.estadoContrato,
                dt_contratos_column_index.esContratoAlquiler,
                dt_contratos_column_index.esContratoVentaPlazo,
                dt_contratos_column_index.tipoMoneda
            ]
        }
    ],
    "fnDrawCallback": function () {
        initBorrarButton();
    }
});

var overflow = 2;

$(document).ready(function () {
    $('#btn_generar_comprobantes').on('click', function (e) {

        e.preventDefault();

        var table = $('#table-contrato');
        var ids = [];

        ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un contrato para generar un comprobante.'});

            desbloquear();

            return;
        }

        if (validar_contratos()) {

            var json = {
                'ids': ids.toArray()
            };

            if (!(tipoCambioPorTipoMoneda[dt_getSelectedRows(table)[0][dt_contratos_column_index.tipoMoneda - overflow]]['corriente'])) {
                generarFormularioTipoCambio(tipoCambioPorTipoMoneda[dt_getSelectedRows(table)[0][dt_contratos_column_index.tipoMoneda - overflow]]['tipoCambio'], tipoCambioPorTipoMoneda[dt_getSelectedRows(table)[0][dt_contratos_column_index.tipoMoneda - overflow]]['fecha'], dt_getSelectedRows(table)[0][dt_contratos_column_index.tipoMoneda - overflow]);
            } else {
                enviarForm(json);
            }
        } else {

            desbloquear();

            return;
        }
    });

    initEditarFechaAsientoContableHandler();

});

function validar_contratos() {
    var table = $('#table-contrato');
    var contratos_seleccionados = dt_getSelectedRows(table);

    var tipo_contrato = "";
    var moneda_contrato = "";
    var estados_validos = true;
    var total_alquileres = 0;
    var total_venta_a_plazo = 0;

    for (var index = 0; index < contratos_seleccionados.length; index++) {
        var datos_contrato = contratos_seleccionados[index];
//        var tipo_contrato_actual = datos_contrato[0];
        var moneda_contrato_actual = datos_contrato[dt_contratos_column_index.codigoTipoMoneda - overflow];
        var estado_contrato = datos_contrato[dt_contratos_column_index.estadoContrato - overflow];

//        if (tipo_contrato == "") {
//            tipo_contrato = tipo_contrato_actual;
//        }

        if (moneda_contrato == "") {
            moneda_contrato = moneda_contrato_actual;
        }

        if (datos_contrato[dt_contratos_column_index.esContratoAlquiler - overflow] == 1) {
            total_alquileres++;
        }

        if (datos_contrato[dt_contratos_column_index.esContratoVentaPlazo - overflow] == 1) {
            total_venta_a_plazo++;
        }

        estados_validos &= (estado_contrato == constanteSinDNI || estado_contrato == constanteActivoOk || estado_contrato == constanteActivoComentado || estado_contrato == constanteDesocupado);

        if ((moneda_contrato != moneda_contrato_actual) || !(estados_validos)) {
            show_alert({msg: 'Los contratos deben ser del mismo tipo y moneda y deben estar activos'});
            return false;
        }
    }

    if ((total_alquileres == 0 && total_venta_a_plazo == 0) || (((contratos_seleccionados.length != total_alquileres) && (total_alquileres > 0)) || ((contratos_seleccionados.length != total_venta_a_plazo) && (total_venta_a_plazo > 0)))) {
        show_alert({msg: 'No puede generar comprobantes automáticos para los contratos seleccionados'});
        return false;
    }

    return true;

}

/**
 * 
 * @param {type} cambio
 * @returns {Boolean}
 */
function generarFormularioTipoCambio(cambio, fecha, idMoneda) {
    formulario_tipo_cambio = '<form name="form-tipo-cambio" class="form-tipo-cambio">\n\
                                  <label class="control-label required" for="tipoCambio">Tipo cambio</label>\n\
                                  <div class="form-group">\n\
                                    <div class="input-icon right">\n\
                                        <i class="fa"></i>\n\
                                        <input type="text" id="tipoCambio" name="tipoCambio" required="required" class="form-control numberPositive tipoCambio" style="height: auto; text-align: right;">\n\
                                    </div>\n\
                                  </div>\n\
                                  <div class="pull-right">\n\
                                    <h5>Última actualización: ' + fecha + '<h5>\n\
                                  </div>\n\
                              </form>'
            ;
    show_dialog({
        titulo: 'Tipo de cambio de los comprobantes',
        contenido: formulario_tipo_cambio,
        callbackCancel: function () {
            desbloquear();
        },
        callbackSuccess: function () {
            var formulario = $('form[name=form-tipo-cambio]').validate();
            var formulario_result = formulario.form();
            if (formulario_result) {

                var table = $('#table-contrato');
                var ids = [];
                ids = dt_getSelectedRowsIds(table);

                var json = {
                    'ids': ids.toArray(),
                    'idMoneda': idMoneda,
                    'tipo-cambio': $('#tipoCambio').val().replace(',', '.')
                };
                enviarForm(json);
            } else {
                return false;
            }
        }
    });
    desbloquear();
    $('#tipoCambio').val(cambio);
    initCurrencies();
    $('.bootbox').removeAttr('tabindex');
}

/**
 * 
 * @returns {undefined}
 */
function enviarForm(json) {
    $('#form_generar_facturas').addHiddenInputData(json);
    $('#form_generar_facturas').submit();
}

/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    updateFechaComprobanteFromAsientoContable();

}