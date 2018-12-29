var renglones_conciliados = [];
var movimientos_conciliados = [];
var dt_extracto_bancario_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    referencia: 3,
    descripcion: 4,
    concepto: 5,
    debe: 6,
    haber: 7,
    monto_original: 8,
    id_renglon: 9,
    es_contabilizable: 10,
    codigo_concepto: 11
};
columns_extracto = [
    {
        "targets": dt_extracto_bancario_column_index.multiselect,
        "data": "ch_multiselect",
        "render": function (data, type, full, meta) {
            return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
        }
    },
    {
        className: "text-center",
        targets: [
            dt_extracto_bancario_column_index.multiselect
        ]
    },
    {
        className: "nowrap",
        targets: [
            dt_extracto_bancario_column_index.fecha,
            dt_extracto_bancario_column_index.referencia
        ]
    },
    {
        className: "text-right nowrap",
        targets: [
            dt_extracto_bancario_column_index.debe,
            dt_extracto_bancario_column_index.haber
        ]
    },
    {
        "targets": dt_extracto_bancario_column_index.debe,
        "createdCell": function (td, cellData, rowData, row, col) {

            var full_data = rowData[dt_extracto_bancario_column_index.debe];
            if (clearCurrencyValue(full_data) != 0) {
                $(td).addClass("bold");
            }
        }

    },
    {
        "targets": dt_extracto_bancario_column_index.haber,
        "createdCell": function (td, cellData, rowData, row, col) {

            var full_data = rowData[dt_extracto_bancario_column_index.haber];
            if (clearCurrencyValue(full_data) != 0) {
                $(td).addClass("bold");
            }
        }

    },
    {
        className: "hidden",
        targets: [
            dt_extracto_bancario_column_index.monto_original,
            dt_extracto_bancario_column_index.id_renglon,
            dt_extracto_bancario_column_index.es_contabilizable,
            dt_extracto_bancario_column_index.codigo_concepto
        ]
    }
];
dt_extracto_bancario = dt_datatable($('#table-extracto-bancario'), {
    ajax: {
        url: __AJAX_PATH__ + 'renglonesconciliacion/index_table/',
        data: function (d) {
            d.id_conciliacion = $('#id_conciliacion_bancaria').val();
        }
    },
    paging: false,
    columnDefs: columns_extracto
});
dt_extracto_bancario_conciliado = dt_datatable($('#table-extracto-bancario-conciliado'), {
    ajax: {
        url: __AJAX_PATH__ + 'renglonesconciliacion/index_table_conciliado/',
        data: function (d) {
            d.id_conciliacion = $('#id_conciliacion_bancaria').val();
        }
    },
    paging: false,
    columnDefs: columns_extracto
});
var dt_mayor_column_index = {
    id: 0,
    multiselect: 1,
    fecha: 2,
    concepto: 3,
    referencia: 4,
    codigo_concepto: 5,
    debe: 6,
    haber: 7,
    saldo: 8,
    monto_original: 9,
    id_movimiento: 10,
    es_contabilizable: 11
};
columns_mayor = [
    {
        "targets": dt_mayor_column_index.multiselect,
        "data": "ch_multiselect",
        "render": function (data, type, full, meta) {
            return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
        }
    },
    {
        className: "text-center",
        targets: [
            dt_mayor_column_index.multiselect
        ]
    },
    {
        className: "nowrap",
        targets: [
            dt_mayor_column_index.fecha
        ]
    },
    {
        className: "text-right nowrap",
        targets: [
            dt_mayor_column_index.debe,
            dt_mayor_column_index.haber,
            dt_mayor_column_index.saldo
        ]
    },
    {
        "targets": dt_mayor_column_index.debe,
        "createdCell": function (td, cellData, rowData, row, col) {

            var full_data = rowData[dt_mayor_column_index.debe];
            if (clearCurrencyValue(full_data) != 0) {
                $(td).addClass("bold");
            }
        }

    },
    {
        "targets": dt_mayor_column_index.haber,
        "createdCell": function (td, cellData, rowData, row, col) {

            var full_data = rowData[dt_mayor_column_index.haber];
            if (clearCurrencyValue(full_data) != 0) {
                $(td).addClass("bold");
            }
        }

    },
    {
        className: "hidden",
        targets: [
            dt_mayor_column_index.referencia,
            dt_mayor_column_index.codigo_concepto,
            dt_mayor_column_index.monto_original,
            dt_mayor_column_index.id_movimiento,
            dt_mayor_column_index.es_contabilizable
        ]
    }
];
dt_mayor = dt_datatable($('#table-mayor'), {
    ajax: {
        url: __AJAX_PATH__ + 'conciliacion/index_table_mayor/',
        data: function (d) {
            d.id_conciliacion = $('#id_conciliacion_bancaria').val();
        }
    },
    paging: false,
    columnDefs: columns_mayor
});
dt_mayor_conciliado = dt_datatable($('#table-mayor-conciliado'), {
    ajax: {
        url: __AJAX_PATH__ + 'conciliacion/index_table_mayor_conciliado/',
        data: function (d) {
            d.id_conciliacion = $('#id_conciliacion_bancaria').val();
        }
    },
    paging: false,
    columnDefs: columns_mayor
});
$(document).ready(function () {
    initDatepickers();
    $('#adif_contablebundle_conciliacionbancaria_conciliacion_fechaExtracto').datepicker('setStartDate', getDateFromString($('#fecha_inicio_hidden').val()));
    $('#adif_contablebundle_conciliacionbancaria_conciliacion_fechaExtracto').datepicker('setEndDate', getDateFromString($('#fecha_fin_hidden').val()));
    var inputSaldoExtracto = $('#adif_contablebundle_conciliacionbancaria_conciliacion_saldoExtracto');
    corregir_importe(inputSaldoExtracto);
    inputSaldoExtracto.maskMoney({allowZero: true, allowNegative: true, thousands: '.', decimal: ','});

    if (tiene_tipo_cambio == 1) {
        $('#adif_contablebundle_conciliacionbancaria_conciliacion_tipoCambioImportacion').maskMoney({allowZero: false, allowNegative: false, thousands: '.', decimal: ','});
        var inputTipoCambio = $('#adif_contablebundle_conciliacionbancaria_conciliacion_tipoCambio');
        
        corregir_importe(inputTipoCambio);
      
        inputTipoCambio.maskMoney({allowZero: false, allowNegative: false, thousands: '.', decimal: ','});
    }
    
    $('#tab_conciliacion').bootstrapWizard({
        onTabClick: function (tab, navigation, index, clickedIndex) {

            if (clickedIndex == 0 || clickedIndex == 1) {
                $("#adif_contablebundle_conciliacionbancaria_conciliacion_cerrar").text('Cerrar conciliaci\u00F3n');
                $('#tabla-previsualizacion-conciliacion').remove();
            } else {
                $("#adif_contablebundle_conciliacionbancaria_conciliacion_cerrar").text('Confirmar cierre');
                $.ajax({
                    type: "POST",
                    data: {id_conciliacion: $('#id_conciliacion_bancaria').val()},
                    url: __AJAX_PATH__ + 'conciliacion/armar_previsualizacion/'
                }).done(function (respuesta) {
                    $('#div-previsualizacion-conciliacion').append(respuesta);
                }
                );
            }
        }
    });

    $('#actualizar_mayor').on('click', function (e) {
        cargarRenglonesMayor();
    });
    $('#conciliacion-automatica').on('click', function (e) {
        e.preventDefault();
        conciliacion_automatica();
    });
    $('#conciliacion-manual').on('click', function (e) {
        e.preventDefault();
        conciliacion_manual();
    });
    $('#generar-comprobante').on('click', function (e) {
        e.preventDefault();
        conciliacion_comprobante(e, $('#id_es_re_abierta').val());
    });
    $('#desconciliacion').on('click', function (e) {
        e.preventDefault();
        desconciliacion();
    });
    $('#table-extracto-bancario,#table-mayor').on('selected_element', function () {
        renglones_seleccionados = dt_getSelectedRows('#table-extracto-bancario');
        movimientos_seleccionados = dt_getSelectedRows('#table-mayor');
        if ((renglones_seleccionados.length > 0) || (movimientos_seleccionados.length > 0)) {
            if ((renglones_seleccionados.length > 0) && (movimientos_seleccionados.length == 0)) {
                $('#btn_asignar_conceptos').show();
                $('#btn_eliminar_renglon').show();
            } else {
                $('#btn_asignar_conceptos').hide();
                $('#btn_eliminar_renglon').hide();
            }
            total = 0;
            correctamenteAsignados = true;
            for (var index = 0; index < renglones_seleccionados.length; index++) {
                correctamenteAsignados &= renglones_seleccionados[index][3] != '-';
                total += parseFloat(renglones_seleccionados[index][8]);
            }

            if (correctamenteAsignados) {
                if ((total > 0) && (total == renglones_seleccionados.length) && (movimientos_seleccionados.length == 0)) {
                    $('#btn_generar_comprobante').show();
                    $('#btn_conciliacion_manual').hide();
                } else {
                    $('#btn_generar_comprobante').hide();
                }
                if ((total == 0) && !(((movimientos_seleccionados.length == 1) && (renglones_seleccionados.length == 0)) || 
                        ((movimientos_seleccionados.length == 0) && (renglones_seleccionados.length == 1)))) {
                    $('#btn_generar_comprobante').hide();
                    $('#btn_conciliacion_manual').show();
                } else {
                    $('#btn_conciliacion_manual').hide();
                }
            } else {
                $('#btn_generar_comprobante').hide();
                $('#btn_conciliacion_manual').hide();
            }


        } else {
            $('#btn_asignar_conceptos').hide();
            $('#btn_eliminar_renglon').hide();
            $('#btn_generar_comprobante').hide();
            $('#btn_conciliacion_manual').hide();
        }
    });
    $('#').on('click', function (e) {
        e.preventDefault();
        bloquear();

    });
    $('#adif_contablebundle_conciliacionbancaria_conciliacion_cerrar').on('click', function (e) {
        if ($('#adif_contablebundle_conciliacionbancaria_conciliacion_cerrar').text() == 'Confirmar cierre') {
            $('form[name=adif_contablebundle_conciliacionbancaria_conciliacion]').submit();
        } else {
            $("#adif_contablebundle_conciliacionbancaria_conciliacion_cerrar").text('Confirmar cierre');
            $('#tab_conciliacion').find("a[href*='#tab_3']").trigger('click');
        }
    });
    $('#asignar-concepto-conciliacion').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var table = $('#table-extracto-bancario');
        var ids = [];
        ids = dt_getSelectedRowsIds(table);
        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un movimiento para asignar un concepto.'});
            desbloquear();
            return;
        }

        conceptosHTML = "<option value='' selected='selected'>-- Elija un concepto --</option>";
        $.each(conceptos, function (id, data) {
            conceptosHTML = conceptosHTML + "<option value='" + id + "'>" + conceptos[id] + "</option>";
        });
        formulario_conceptos = '<form id="form_multiples_conceptos" method="post" name="form_multiples_conceptos">\n\
                                    <div class="row">\n\
                                        <div class="col-md-12">\n\
                                            <div class="form-group">\n\
                                                <label class="control-label required" for="select_conceptos_multiple">Concepto</label>\n\
                                                <div class="input-icon right">\n\
                                                    <i class="fa"></i>\n\
                                                    <select id="select_conceptos_multiple" name="select_conceptos_multiple" class="required form-control choice">' + conceptosHTML + '</select>\n\
                                                </div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </form>';
        show_dialog({
            titulo: 'Agregar concepto a múltiples movimientos',
            contenido: formulario_conceptos,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {

                var formulario = $('form[name=form_multiples_conceptos]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {
                    $.ajax({
                        type: "POST",
                        data: {ids: JSON.stringify(ids.toArray()), concepto: $('#select_conceptos_multiple').val()},
                        url: __AJAX_PATH__ + 'renglonesconciliacion/asignar_conceptos/'
                    }).done(function (respuesta) {
                        if (respuesta.status === 'OK') {
                            show_alert({title: 'Asignación múltiple', msg: respuesta.message});
                            cargarRenglonesExtracto();
                        } else {
                            show_alert({title: 'Error', msg: respuesta.message, type: 'error'});
                        }
                    });
                } else {
                    return false;
                }
            }
        });
        desbloquear();
        $('.bootbox').removeAttr('tabindex');
        $('#select_conceptos_multiple').select2();
    });
    $('#btn_eliminar_renglon').on('click', function (e) {
        e.preventDefault();
        var table = $('#table-extracto-bancario');
        var ids = [];
        ids = dt_getSelectedRowsIds(table);
        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un movimiento para eliminar.'});
            return;
        } else {
            $.ajax({
                type: "POST",
                data: {ids: JSON.stringify(ids.toArray())},
                url: __AJAX_PATH__ + 'renglonesconciliacion/eliminar_renglones/'
            }).done(function (respuesta) {
                if (respuesta.status === 'OK') {
                    show_alert({title: 'Eliminaci&oacute;n', msg: respuesta.message});
                    cargarRenglonesExtracto();
                } else {
                    show_alert({title: 'Error', msg: respuesta.message, type: 'error'});
                }
            });
        }
    });
    $('#adif_contablebundle_conciliacionbancaria_conciliacion_cargar').on('click', function () {
        var input_archivo = $('#adif_contablebundle_conciliacionbancaria_conciliacion_file');
        var saldo_extracto = $('#adif_contablebundle_conciliacionbancaria_conciliacion_saldoExtracto').maskMoney('unmasked')[0];
        var fecha_extracto = $('#adif_contablebundle_conciliacionbancaria_conciliacion_fechaExtracto');
        if (fecha_extracto.val() == '' || saldo_extracto == '') {
            show_alert({title: 'Error', msg: 'La fecha y el saldo del extracto son obligatorios', type: 'error'});
        } else {
            
            var tipo_cambio = tiene_tipo_cambio == 1 ? $('#adif_contablebundle_conciliacionbancaria_conciliacion_tipoCambio').maskMoney('unmasked')[0] : '';
     
            if (tiene_tipo_cambio == 1 && tipo_cambio == '') {
                show_alert({title: 'Error', msg: 'El tipo de cambio es obligatorio', type: 'error'});                
            } else {  
                
                var tipo_cambio_importacion = tiene_tipo_cambio == 1 ? $('#adif_contablebundle_conciliacionbancaria_conciliacion_tipoCambioImportacion').maskMoney('unmasked')[0] : '';

                if (tiene_tipo_cambio == 1 && tipo_cambio_importacion == '' && input_archivo.val() != '') {
                    show_alert({title: 'Error', msg: 'El tipo de cambio de la importaci&oacute;n es obligatorio', type: 'error'});                
                } else {              
            
                    var formData = new FormData();
                    formData.append('id_conciliacion', $('#id_conciliacion_bancaria').val());

                    formData.append('fechaExtracto', fecha_extracto.val());
                    if (tiene_tipo_cambio) {
                        formData.append('tipoCambio', tipo_cambio);
                    }    
                    formData.append('saldoExtracto', saldo_extracto);

                    if (input_archivo.val() != '') {
                        var ext = input_archivo.val().split('.').pop().toLowerCase();
                        if ($.inArray(ext, ['xls', 'xlsx']) == -1) {
                            alert('Por favor, ingrese una extensión de archivo válida (XLS, XLSX)');
                        } else {
                            if (tiene_tipo_cambio) {
                                formData.append('tipoCambioImportacion', tipo_cambio_importacion);
                            }                             
                            formData.append('archivo', $('#adif_contablebundle_conciliacionbancaria_conciliacion_file').prop('files')[0]);
                        }
                    }

                    $.ajax({
                        type: 'POST',
                        url: __AJAX_PATH__ + 'conciliacion/cargar_extracto/',
                        processData: false,
                        contentType: false,
                        data: formData
                    }).done(function (respuesta) {
                        if (respuesta.status === 'OK') {
                            show_alert({title: 'Carga extracto', msg: respuesta.message});
                            if (input_archivo.val() != '') {
                                cargarRenglonesExtracto();
                                $('.boton-remover').click();
                                $('#adif_contablebundle_conciliacionbancaria_conciliacion_tipoCambioImportacion').val('');
                            }
                        } else {
                            show_alert({title: 'Error', msg: respuesta.message, type: 'error'});
                        }
                    }
                            
                    );
                }
            }
        }
    });
});
function cargarRenglonesExtracto() {
    dt_extracto_bancario.DataTable().ajax.reload();
}

function cargarRenglonesExtractoConciliados() {
    dt_extracto_bancario_conciliado.DataTable().ajax.reload();
}

function cargarRenglonesMayor() {
    dt_mayor.DataTable().ajax.reload();
}

function cargarRenglonesMayorConciliados() {
    dt_mayor_conciliado.DataTable().ajax.reload();
}

function conciliacion_automatica() {
    var filas_extracto = dt_getRows('#table-extracto-bancario');
    var filas_mayor = dt_getRows('#table-mayor');
    $('#table_conciliacion_automatica tbody').empty();
    renglones_conciliados = [];
    movimientos_conciliados = [];
    for (var index2 = 0; index2 < filas_mayor.length; index2++) {
        var fila_mayor = filas_mayor[index2];
        for (var index = 0; index < filas_extracto.length; index++) {
            var fila_extracto = filas_extracto[index];
            if (fila_extracto[9] == fila_mayor[3] && //Código de concepto
                    pad(fila_extracto[1], 20) == pad(fila_mayor[2], 20) && //Referencia
                    fila_extracto[4] == fila_mayor[5] && //Debe con haber
                    fila_extracto[5] == fila_mayor[4] && //Haber con debe
                    fila_extracto[8] == '0'  //&& fila_mayor[9] == '1'    
                    ) {
                //renglones_conciliados.push($('#table-extracto-bancario').DataTable().rows(index).data()[0][0]);                
                //movimientos_conciliados.push($('#table-mayor').DataTable().rows(index2).data()[0][0]);
                renglones_conciliados.push(fila_extracto[7]);
                movimientos_conciliados.push(fila_mayor[8]);
                var $tr = $('<tr />', {style: 'cursor: pointer;'});
                $('<td />', {text: fila_extracto[0]}).appendTo($tr);
                $('<td />', {text: fila_extracto[1]}).appendTo($tr);
                $('<td />', {text: fila_extracto[2]}).appendTo($tr);
                $('<td />', {text: fila_extracto[3]}).appendTo($tr);
                $('<td />', {text: fila_extracto[4], class: 'money-format nowrap'}).appendTo($tr);
                $('<td />', {text: fila_extracto[5], class: 'money-format nowrap'}).appendTo($tr);
                $('<td />', {text: fila_mayor[0]}).appendTo($tr);
                $('<td />', {text: fila_mayor[1]}).appendTo($tr);
                $('<td />', {text: fila_mayor[4], class: 'money-format nowrap'}).appendTo($tr);
                $('<td />', {text: fila_mayor[5], class: 'money-format nowrap'}).appendTo($tr);
                $('#table_conciliacion_automatica tbody').append($tr);
                break;
            }
        }
    }

    if (renglones_conciliados.length > 0) {
        initExportCustom($('#table_conciliacion_automatica'));
        show_dialog({
            titulo: 'Concilaci&oacute;n autom&aacute;tica',
            contenido: $('#renglones_conciliados'),
            callbackCancel: function () {
                $('#renglones_conciliados').appendTo('#renglones_conciliados_ctn');
                desbloquear();
            },
            callbackSuccess: function () {
                $('#renglones_conciliados').appendTo('#renglones_conciliados_ctn');
                actualizar_renglones_movimientos_conciliados('conciliar', '');
            }
        });
        setMasks();
    } else {
        show_alert({
            title: 'Error al conciliar autom&aacute;ticamente',
            msg: '<div class="col-md-12">No se encontraron coincidencias entre el extracto y los movimientos para conciliar</div>',
            type: 'error'
        });
    }

}

function actualizar_renglones_movimientos_conciliados(accion, urlPath) {
    $.ajax({
        type: "POST",
        url: __AJAX_PATH__ + 'conciliacion/' + accion + '_renglones_movimientos' + urlPath + '/',
        data: {
            id_conciliacion: $('#id_conciliacion_bancaria').val(),
            ids_renglones: JSON.stringify(renglones_conciliados),
            ids_movimientos: JSON.stringify(movimientos_conciliados)
        }
    }).done(function (respuesta) {
        if (respuesta.status === 'OK') {
            show_alert({title: accion, msg: respuesta.message});
            cargarRenglonesExtracto();
            cargarRenglonesExtractoConciliados();
            cargarRenglonesMayor();
            cargarRenglonesMayorConciliados();
        } else {
            show_alert({title: 'Error', msg: respuesta.message, type: 'error'});
        }
    });
}

function verificar_renglones_movimientos(id_extracto, id_mayor, accion) {
    var renglones_seleccionados = dt_getSelectedRows(id_extracto);
    var movimientos_seleccionados = dt_getSelectedRows(id_mayor);
    if (renglones_seleccionados.length > 0 || movimientos_seleccionados.length > 0) {
        var total_debe_extracto = 0;
        var total_haber_extracto = 0;
        var total_debe_mayor = 0;
        var total_haber_mayor = 0;
        renglones_conciliados = [];
        movimientos_conciliados = [];


        totalExtracto = 0; //acumula los registros contabilizables (gastos bancarios, etc)
        totalMayor = 0; //acumula los registros contabilizables (gastos bancarios, etc)

        for (var index = 0; index < renglones_seleccionados.length; index++) {
            var fila_extracto = renglones_seleccionados[index];
            total_debe_extracto += parseFloat(fila_extracto[6]) < 0 ? parseFloat(fila_extracto[6]) * -1 : 0;
            total_haber_extracto += parseFloat(fila_extracto[6]) > 0 ? parseFloat(fila_extracto[6]) : 0;
            var id_renglon = fila_extracto[7];
            if ($.inArray(id_renglon, renglones_conciliados) == -1) {
                renglones_conciliados.push(id_renglon);
                totalExtracto += parseFloat(renglones_seleccionados[index][8]);
            }
        }

        for (var index = 0; index < movimientos_seleccionados.length; index++) {
            var fila_mayor = movimientos_seleccionados[index];
            total_debe_mayor += parseFloat(fila_mayor[7]) < 0 ? parseFloat(fila_mayor[7]) * -1 : 0;
            total_haber_mayor += parseFloat(fila_mayor[7]) > 0 ? parseFloat(fila_mayor[7]) : 0;
            var id_movimiento = fila_mayor[8];
            if ($.inArray(id_movimiento, movimientos_conciliados) == -1) {
                movimientos_conciliados.push(id_movimiento);
                totalMayor += parseFloat(movimientos_seleccionados[index][9]);
            }
        }

        total_debe_extracto = total_debe_extracto.toFixed(2);
        total_haber_extracto = total_haber_extracto.toFixed(2);
        total_debe_mayor = total_debe_mayor.toFixed(2);
        total_haber_mayor = total_haber_mayor.toFixed(2);
        totalExtracto = totalExtracto.toFixed(2);
        totalMayor = totalMayor.toFixed(2);

        urlPath = '';
        if ((accion == 'desconciliar') && (totalExtracto > 0 || totalMayor > 0)) { //SON CONTABILIZABLES PARA DESCONCILIAR
            if (totalExtracto == totalMayor && renglones_seleccionados.length == movimientos_seleccionados.length && totalMayor == renglones_seleccionados.length) {
                urlPath = '_comprobante';
            } else {
                show_alert({
                    title: 'Error',
                    msg: '<div class="col-md-12">No se pueden desconciliar los gastos bancarios (hay otro tipo de movimientos o faltan seleccionar gastos)</div>',
                    type: 'error'
                });
                return false;                
            }

        }
        
        if ((accion == 'desconciliar') && (((movimientos_seleccionados.length == 1) && (renglones_seleccionados.length == 0)) || 
                        ((movimientos_seleccionados.length == 0) && (renglones_seleccionados.length == 1))  || 
                        ((movimientos_seleccionados.length == 0) && (renglones_seleccionados.length == 0)))) {
            show_alert({
                title: 'Error',
                msg: '<div class="col-md-12">Se necesitan al menos dos movimientos entre el extracto bancario y el mayor conciliable</div>',
                type: 'error'
            });
            return false;                 
            
        } else { 

            if ( (total_haber_extracto - total_debe_extracto) == (total_debe_mayor - total_haber_mayor) ) {
                show_confirm({
                    msg: 'Desea ' + accion + ' los movimientos seleccionados?',
                    callbackOK: function () {
                        actualizar_renglones_movimientos_conciliados(accion, urlPath);
                    }
                });
            } else {
                show_alert({
                    title: 'Error',
                    msg: '<div class="col-md-12">Los montos para ' + accion + ' difieren</div>',
                    type: 'error'
                });
            }
            
        }
    }
}

function conciliacion_manual() {
    verificar_renglones_movimientos('#table-extracto-bancario', '#table-mayor', 'conciliar');
}

function desconciliacion() {
    verificar_renglones_movimientos('#table-extracto-bancario-conciliado', '#table-mayor-conciliado', 'desconciliar');
}

function conciliacion_comprobante_2() {
    generarComprobante('#table-extracto-bancario', '#table-mayor');
}

function conciliacion_comprobante(e, id_es_re_abierta) {
    e.preventDefault();
    bloquear();

    var renglones_seleccionados = dt_getSelectedRows('#table-extracto-bancario');

    renglones_conciliados = [];
    for (var index = 0; index < renglones_seleccionados.length; index++) {
        var fila_extracto = renglones_seleccionados[index];
        var id_renglon = fila_extracto[7];
        if ($.inArray(id_renglon, renglones_conciliados) == -1) {
            renglones_conciliados.push(id_renglon);
        }
    }

    formulario_comprobante = '<form id="form_comprobante" method="post" name="form_comprobante">\n\
                                <div class="row">\n\
                                    <div class="col-md-12">\n\
                                        <div class="form-group">\n\
                                            <label class="control-label required" for="fecha_comprobante">Fecha</label>\n\
                                            <div class="input-icon right">\n\
                                                <i class="fa"></i>\n\
                                                <input type="text" id="fecha_comprobante" name="fecha_comprobante" class="required form-control datepicker" />\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </form>';
    show_dialog({
        titulo: 'Generar comprobante',
        contenido: formulario_comprobante,
        callbackCancel: function () {
            desbloquear();
        },
        callbackSuccess: function () {
            var formulario = $('form[name=form_comprobante]').validate();
            var formulario_result = formulario.form();
            if (formulario_result) {
                actualizar_renglones_movimientos_conciliados_con_asiento();
            } else {
                return false;
            }
        }
    });
    desbloquear();

    var fecha_inicio_str = $('#fecha_inicio_conciliacion').val();
    var fullDate = new Date();
    var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) :(fullDate.getMonth()+1);
    //var fecha_fin_str = fullDate.getDate() + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
    var fecha_fin_str = $('#fecha_fin_conciliacion').val();

    if ( id_es_re_abierta == 1 )
    {
        fecha_fin_str = fullDate.getDate() + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
    }
    else
    {
        fecha_fin_str = $('#fecha_fin_conciliacion').val();
    }

    var dateParts = fecha_inicio_str.split("/");
    var fecha_inicio = new Date(dateParts[2], (dateParts[1] - 1), dateParts[0]);

    var dateParts = fecha_fin_str.split("/");
    var fecha_fin = new Date(dateParts[2], (dateParts[1] - 1), dateParts[0]);

    initDatepicker($('#form_comprobante').find('#fecha_comprobante'));
    $('#form_comprobante').find('#fecha_comprobante').datepicker('setStartDate', fecha_inicio);
    $('#form_comprobante').find('#fecha_comprobante').datepicker('setEndDate', fecha_fin);

    $('#form_comprobante').closest('.modal-dialog').css('width', '300px');
    $('.bootbox').removeAttr('tabindex');
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
 * @param {type} renglones_seleccionados
 * @param {type} movimientos_seleccionados
 * @returns {Boolean}
 */
function generarComprobante(id_extracto) {

    var renglones_seleccionados = dt_getSelectedRows(id_extracto);

    renglones_conciliados = [];
    for (var index = 0; index < renglones_seleccionados.length; index++) {
        var fila_extracto = renglones_seleccionados[index];
        var id_renglon = fila_extracto[7];
        if ($.inArray(id_renglon, renglones_conciliados) == -1) {
            renglones_conciliados.push(id_renglon);
        }
    }

    show_confirm({
        msg: '<div class="col-md-12">Desea generar los comprobantes para los extractos seleccionados?</div>',
        callbackOK: function () {
            actualizar_renglones_movimientos_conciliados_con_asiento();
        }
    });
}

function actualizar_renglones_movimientos_conciliados_con_asiento() {
    $.ajax({
        type: "POST",
        url: __AJAX_PATH__ + 'conciliacion/conciliar_renglones_movimientos_comprobante/',
        data: {
            id_conciliacion: $('#id_conciliacion_bancaria').val(),
            ids_renglones: JSON.stringify(renglones_conciliados),
            fecha_comprobante: $('#fecha_comprobante').val()
        }
    }).done(function (respuesta) {
        if (respuesta.status === 'OK') {
            show_alert({title: 'Creaci&oacute;n comprobante de gasto', msg: respuesta.message});
            cargarRenglonesExtracto();
            cargarRenglonesExtractoConciliados();
            cargarRenglonesMayor();
            cargarRenglonesMayorConciliados();
            $('#btn_asignar_conceptos').hide();
            $('#btn_eliminar_renglon').hide();
            $('#btn_generar_comprobante').hide();
            $('#btn_conciliacion_manual').hide();
        } else {
            show_alert({title: 'Error', msg: respuesta.message, type: 'error'});
        }
    });
}

function sumar_montos(tabla, pos) {
    var total = 0;
    for (var index = 0; index < tabla.length; index++) {
        var fila = tabla[index];
        total += parseFloat(fila[pos]);

    }
    return total;
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
    return a;
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

    open_window('POST', __AJAX_PATH__ + 'conciliacion/export_' + tipo, content, '_blank');
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

function corregir_importe(inputImporte) {
    var posPunto = inputImporte.val().indexOf(".");
    var posComa = inputImporte.val().indexOf(",");
    if (posPunto == -1 && posComa == -1) {
        inputImporte.val(inputImporte.val() + ".00");
    } else {
        var posDecimal = posPunto > -1 ? posPunto : posComa;

        var cantCeros = 2 - (inputImporte.val().length - (posDecimal + 1));
        var ceros = '';
        for (var i = 0; i < cantCeros; i++) {
            ceros = ceros + '0';
        }
        inputImporte.val(inputImporte.val() + ceros);
    }  
}