var __TIPO_LIQUIDACION_ADICIONAL = 2;
var __TIPO_LIQUIDACION_SAC = 3;
var dt_empleado; // Empleado DT

var index = 0;
var dt_empleado_column_index = {
    id: index++,
    multiselect: index++,
    legajo: index++,
    apellido: index++,
    nombre: index++,
    cuil: index++,
    fecha_ingreso: index++,
    convenio: index++,
    categoria: index++,
	gerencia: index++,
    acciones: index++
}
var dialog_asignar_novedades;

var indexVisibleToDT = function (column_index) {
    return dt_empleado.column(column_index).index();
}

var indexDTToVisible = function (column_index) {
    return dt_empleado.column(column_index).index('visible');
}

var formulario_anio_exportacion = '<form id="anio_exportacion" method="post" name="anio_exportacion">\n\
                                    <div class="row">\n\
                                        <div class="col-md-4">\n\
                                            <div class="form-group">\n\
                                                <label class="control-label required left" for="anio_exportacion_input">Año</label>\n\
                                                <div class="input-icon right">\n\
                                                    <i class="fa"></i>\n\
                                                    <input type="text" id="anio_exportacion_input" name="anio_exportacion_input" required="required" class=" form-control datepicker nomask novalidate" style="height: auto;">\n\
                                                </div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </form>';


$(document).ready(function () {
    $(document).on("change", "#select_novedades_multiple", function () {
        porcentajeValor(novedades[$('#select_novedades_multiple').val()]);
    });

    $(document).on('click', '.empleado_desactivar', function (e) {
        e.preventDefault();
        var a = $(this);
        show_confirm({
            title: 'Desactivar empleado',
            msg: 'Confirma desactivar al empleado <b>' + $(this).parents('tr').find('td:nth-child(' + (indexDTToVisible(dt_empleado_column_index.apellido) + 1) + ')').text() + ', ' + $(this).parents('tr').find('td:nth-child(' + (indexDTToVisible(dt_empleado_column_index.nombre) + 1) + ')').text() + '</b>?',
            callbackOK: function () {
                location.href = a.attr('href');
            }
        });
    });

//  ACCIONES DE LOS REGISTROS
    $('body').tooltip({
        selector: '.tooltips'
    });

    $('body').popover({
        selector: '.btn-group-popover',
        placement: 'left',
        title: 'Opciones',
        html: true,
        content: function () {
            return $(this).attr('html');
        },
        trigger: 'focus',
        template: '<div class="popover table-actions-popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>'
    });

    $('#table-empleado').on('selected_element', function (e, cantidad) {
        $('#cant_seleccionados').text(cantidad);

        $('#cant_seleccionados').parent().removeClass('flash animated').addClass('flash animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            $(this).removeClass('flash animated');
        });
        // cantidad > 0 ? $('.hide-if-non-selected').fadeIn(200) : $('.hide-if-non-selected').fadeOut(200);
    });

    dt_empleado = dt_datatable($('#table-empleado'), {
        ajax: __AJAX_PATH__ + 'empleados/index_table/',
        columnDefs: [
            {
                "targets": dt_empleado_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                }
            }, {
                "targets": dt_empleado_column_index.convenio,
                "render": function (data, type, full, meta) {
                    return full[dt_empleado_column_index.convenio].valor;
//                    return '<div class="hidden" __' + full[dt_empleado_column_index.convenio].id + '__></div>' + full[dt_empleado_column_index.convenio].valor;
                }
            }, {
                "targets": dt_empleado_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {
                    var full_data = full[dt_empleado_column_index.acciones];
                    return '<a tabindex="0" class="btn btn-xs btn-primary btn-group-popover" data-toggle="popover" html=\''
                            + ((full_data.show !== undefined ?
                                    '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-toggle="tooltip" data-original-title="Ver detalle"><i class="fa fa-search"></i></a>' : '')
                                    + (full_data.edit !== undefined ?
                                            '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-toggle="tooltip" data-original-title="Editar"><i class="fa fa-pencil"></i></a>' : '')
                                    + (full_data.archivos !== undefined ?
                                            '<a href="' + full_data.archivos + '" class="btn btn-xs yellow tooltips" data-toggle="tooltip" data-original-title="Adjuntar archivos"><i class="fa fa-upload"></i></a>' : '')
                                    + (full_data.conceptos ?
                                            '<a href="' + full_data.conceptos + '" class="btn btn-xs red tooltips" data-toggle="tooltip" data-original-title="Asignar conceptos"><i class="fa fa-letter">C</i></a>' : '')
                                    + (full_data.novedades !== undefined ?
                                            '<a href="' + full_data.novedades + '" class="btn btn-xs purple tooltips" data-toggle="tooltip" data-original-title="Ver novedades"><i class="fa fa-letter">N</i></a>' : '')
                                    + (full_data.impuesto_ganancias_excel !== undefined ?
                                            '<a href="' + full_data.impuesto_ganancias_excel + '" class="btn btn-xs dark tooltips exportar_ig" data-toggle="tooltip" data-original-title="Impuesto a las Ganancias"><i class="fa fa-letter">IG</i></a>' : '')
                                    + (full_data.impuesto_ganancias_excel_res !== undefined ?
                                            '<a href="' + full_data.impuesto_ganancias_excel_res + '" class="btn btn-xs dark tooltips" data-toggle="tooltip" data-original-title="Impuesto a las Ganancias resoluci&oacute;n anterior"><i class="fa fa-letter">IG Res. ant.</i></a>' : '')
                                    + (full_data.formulario572_index_empleado !== undefined ?
                                            '<a href="' + full_data.formulario572_index_empleado + '" class="btn btn-xs blue-hoki tooltips icon-f572" data-toggle="tooltip" data-original-title="Ver formularios 572"><i class="fa fa-letter">F572</i></a>' : '')
                                    + (full_data.formulario649 !== undefined ?
                                            '<a href="' + full_data.formulario649 + '" class="btn btn-xs blue-chambray tooltips icon-f572 exportar_649" data-toggle="tooltip" data-original-title="Exportar formulario 649"><i class="fa fa-letter">F649</i></a>' : '')
                                    + (full_data.recibos !== undefined ?
                                            '<a href="' + full_data.recibos + '" class="btn btn-xs btn-success tooltips empleado_recibos" data-toggle="tooltip" data-original-title="Imprimir recibos"><i class="fa fa-letter">Recibos</i></a>' : '')
                                    + (full_data.desactivar !== undefined ?
                                            '<a href="' + full_data.desactivar + '" class="btn btn-xs btn-danger tooltips empleado_desactivar" data-toggle="tooltip" data-original-title="Desactivar"><i class="fa fa-ban"></i></a>' : '')
                                    ) + '\'>&nbsp;<i class="fa fa-bolt"></i> Opciones&nbsp;</a>';
                }
            },
            {className: "text-center", targets: [
                    dt_empleado_column_index.multiselect,
                    dt_empleado_column_index.legajo,
                    dt_empleado_column_index.cuil,
                    dt_empleado_column_index.fecha_ingreso,
                    dt_empleado_column_index.convenio
                ]},
            {className: "ctn_acciones text-center nowrap", targets: dt_empleado_column_index.acciones},
            {"width": "30px", "targets": dt_empleado_column_index.legajo},
            {"width": "100px", "targets": dt_empleado_column_index.fecha_ingreso},
            {"width": "130px", "targets": dt_empleado_column_index.convenio},
            {"width": "350px", "targets": dt_empleado_column_index.categoria}
        ]
    });

    dt_empleado = dt_empleado.DataTable();

    $('#btn_liquidar a[liquidacion-table]').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var table = $('#table-empleado');
        var ids = [];
        switch ($(this).data('que-exportar')) {
            case _exportar_todos:
                ids = dt_getRowsIds(table);
                break;
            case _exportar_seleccionados:
                ids = dt_getSelectedRowsIds(table);
                break;
        }

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un empleado para liquidar.'});
            desbloquear();
            return;
        }

        var ajax_dialog_liquidar = $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'liquidaciones/liquidacion/form_liquidar'
        });

        $.when(/*ajax_tipos_liquidacion,*/ ajax_dialog_liquidar).done(function (/*dataTiposLiquidacion, */dataDialogLiquidar) {
            var formulario_liquidacion = dataDialogLiquidar;

            show_dialog({
                titulo: 'Liquidar',
                contenido: formulario_liquidacion,
                callbackCancel: function () {
                    desbloquear();
                    return;
                },
                callbackSuccess: function () {
                    var formulario = $('#form_liquidacion').validate({
                        rules: {
                            conceptos_liquidacion_adicional: {
                                required: function (element) {
                                    return $("#ctn_conceptos_liquidacion_adicional").is(':visible');
                                }
                            }
                        }
                    });

                    var formulario_result = formulario.form();
                    if (formulario_result) {
                        bloquear();
                        $.ajax({
                            url: __AJAX_PATH__ + 'liquidaciones/liquidar/',
                            type: 'POST',
                            data: {
                                ids: JSON.stringify(ids.toArray()),
                                fecha: $('#form_liquidacion #fecha_liquidacion').val(),
                                id_tipo_liquidacion: $('#form_liquidacion #tipo_liquidacion').val(),
                                aplica_ganancias: $('#form_liquidacion #aplica_ganancias').is(':checked'),
                                ids_conceptos_adicionales:
                                        $('#form_liquidacion #tipo_liquidacion').val() == __TIPO_LIQUIDACION_ADICIONAL ?
                                        JSON.stringify($('#form_liquidacion #conceptos_liquidacion_adicional').val())
                                        : []
                            }
                        }).done(function (r) {
                            if (r.result === 'OK') {
                                location.href = __AJAX_PATH__ + 'liquidaciones/liquidacion';
                            } else {
                                desbloquear();
                                show_alert({msg: r.msg, title: 'Error en la liquidaci&oacute;n', type: 'error'});
                            }
                        }).error(function (e) {
                            show_alert({msg: 'Ocurri&oacute; un error al liquidar. Intente nuevamente.', title: 'Error en la liquidaci&oacute;n', type: 'error'});
                        });
                    } else {
                        desbloquear();
                        return false;
                    }
                }
            });

//            $('#form_liquidacion #tipo_liquidacion').html(_tipos_liquidacion);
            $('#form_liquidacion #tipo_liquidacion').on('change', function () {
                if ($(this).val() == __TIPO_LIQUIDACION_ADICIONAL) {
                    $('#form_liquidacion #ctn_conceptos_liquidacion_adicional').show();
                    $('#form_liquidacion #ctn_aplica_ganancias').show();
                    $('#form_liquidacion #conceptos_liquidacion_adicional').removeAttr('disabled');
                    $('#form_liquidacion').validate().resetForm();
                } else {
                    if ($(this).val() == __TIPO_LIQUIDACION_SAC) {
                        $('#form_liquidacion #ctn_aplica_ganancias').show();
                    } else {
                        $('#form_liquidacion #ctn_aplica_ganancias').hide();
                    }
                    $('#form_liquidacion #ctn_conceptos_liquidacion_adicional').hide();
                    $('#form_liquidacion #conceptos_liquidacion_adicional').attr('disabled', 'disabled');
                }
            });

            $('.bootbox').removeAttr('tabindex');

            initDatepickers();
            initSelects();

            $('#fecha_liquidacion').datepicker("setDate", new Date());

            desbloquear();

            e.stopPropagation();
        });
    });

    $('#btn_asignar_conceptos').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var table = $('#table-empleado');
        var ids = [];
        var convenios = [];
        var cells_convenios = [];
        ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un empleado para asignar un concepto.'});
            desbloquear();
            return;
        }

        cells_convenios = dt_empleado.cells('.' + _selected_class, dt_empleado_column_index.convenio).data();

        _.each(cells_convenios, function (elemento) {
            convenios.push(elemento.id);
        });

        convenios = jQuery.unique(convenios);
        $.ajax({
            url: __AJAX_PATH__ + 'conceptos/multiple/',
            type: 'POST',
            data: {convenios: JSON.stringify(jQuery.unique(convenios))}
        }).done(function (result) {
            if (result !== '') {
                formulario_conceptos = '<form id="form_multiples_conceptos" method="post" name="form_multiples_conceptos">\n\
                                            <div class="row">\n\
                                                <div class="col-md-12">\n\
                                                    <div class="form-group">\n\
                                                        <label class="control-label required" for="select_conceptos_multiple">Concepto</label>\n\
                                                        <div class="input-icon right">\n\
                                                            <i class="fa"></i>\n\
                                                            <select id="select_conceptos_multiple" name="select_conceptos_multiple" class="required form-control choice">' + result + '</select>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                        </form>';
                show_dialog({
                    titulo: 'Agregar concepto a múltiples empleados',
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
                                url: __AJAX_PATH__ + 'empleados/asignarMultipleConcepto/'
                            }).always(function (data, textStatus, jqXHR) {
                                switch (jqXHR.status) {
                                    case 200:
                                        show_alert({title: 'Asignación múltiple', msg: 'Asignación realizada con éxito'});
                                        break;
                                    default:
                                        show_alert({title: 'Error', msg: 'Ha ocurrido un error. Intente nuevamente', type: 'error'});
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
            }
            else {
                alert('Hubo un error');
                desbloquear();
            }
        });

        e.stopPropagation();
    });

    $('#btn_asignar_novedades').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var table = $('#table-empleado');
        var ids = [];
        var cells_convenios = [];
        var convenios = [];
        ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un empleado para asignar una novedad.'});
            desbloquear();
            return;
        }

        cells_convenios = dt_empleado.cells('.' + _selected_class, dt_empleado_column_index.convenio).data();

        _.each(cells_convenios, function (elemento) {
            convenios.push(elemento.id);
        });

        convenios = jQuery.unique(convenios);

        $.ajax({
            url: __AJAX_PATH__ + 'conceptos/novedadesmultiple/',
            type: 'POST',
            data: {convenios: JSON.stringify(jQuery.unique(convenios))}
        }).done(function (result) {
            if (result !== '') {
                var $formulario_novedades = $('#form_multiples_novedades_tpl').clone()
                $formulario_novedades.attr('id', 'form_multiples_novedades')
                $formulario_novedades.find('#select_novedades_multiple').append(result)
                $formulario_novedades.find('#select_novedades_multiple').select2()
                $formulario_novedades.find('#novedades_liquidacion_ajuste').select2()



                // Mostrar opciones de ajuste si el concepto es un ajuste
                $formulario_novedades.find('#select_novedades_multiple').off().on('change', function (e) {
                    var es_ajuste = $(this).find('option:selected').attr('es-ajuste') == 1;
                    if (es_ajuste) {
                        dialog_asignar_novedades.find('#row_ajuste').show()
                        dialog_asignar_novedades.find('#novedades_liquidacion_ajuste').addClass('required');
                        dialog_asignar_novedades.find('#novedades_dias').addClass('required');
                    } else {
                        dialog_asignar_novedades.find('#row_ajuste').hide()
                        dialog_asignar_novedades.find('#novedades_liquidacion_ajuste').removeClass('required');
                        dialog_asignar_novedades.find('#novedades_dias').removeClass('required');
                    }
                })

                dialog_asignar_novedades = show_dialog({
                    titulo: 'Agregar novedad a múltiples empleados',
                    contenido: $formulario_novedades,
                    callbackCancel: function () {
                        desbloquear();
                    },
                    callbackSuccess: function () {
                        var formulario = $('form#form_multiples_novedades').validate();
                        var formulario_result = formulario.form();
                        if (formulario_result) {
                            $.ajax({
                                type: "POST",
                                data: {
                                    ids: JSON.stringify(ids.toArray()),
                                    concepto: $('#form_multiples_novedades #select_novedades_multiple').val(),
                                    fecha: $('#form_multiples_novedades #fecha_novedad').val(), valor: $('#form_multiples_novedades #novedades_valor').val(),
                                    liquidacion_ajuste:
                                            $('#form_multiples_novedades #select_novedades_multiple option:selected').attr('es-ajuste') ?
                                            $('#form_multiples_novedades').find('#novedades_liquidacion_ajuste option:selected').val() :
                                            null,
                                    dias_ajuste:
                                            $('#form_multiples_novedades #select_novedades_multiple option:selected').attr('es-ajuste') ?
                                            $('#form_multiples_novedades').find('#novedades_dias').val() :
                                            null
                                },
                                url: __AJAX_PATH__ + 'empleados/asignarMultipleNovedad'
                            }).done(function (res) {
                                // var res = JSON.parse(data);
                                if (res.status == 'OK') {
                                    show_alert({title: 'Asignación múltiple', msg: 'Asignación realizada con éxito'});
                                } else {
                                    show_alert({title: 'Error', msg: 'Ha ocurrido un error:<br/>' + res.msg + '<br/> Intente nuevamente', type: 'error'});
                                }
                            })
                            // .always(function (data, textStatus, jqXHR) {
                            //     switch (jqXHR.status) {
                            //         case 200:
                            //             show_alert({title: 'Asignación múltiple', msg: 'Asignación realizada con éxito'});
                            //             break;
                            //         default:
                            //             show_alert({title: 'Error', msg: 'Ha ocurrido un error. Intente nuevamente', type: 'error'});
                            //     }
                            // });
                        } else {
                            return false;
                        }
                    }
                });

                initDatepicker($formulario_novedades.find('#fecha_novedad'));
                initCurrencies();
                $formulario_novedades.find('#fecha_novedad').css('height', '')
                desbloquear();

                $('.bootbox').removeAttr('tabindex');


                // $formulario_novedades.find('#select_novedades_multiple').select2();
            } else {
                alert('Hubo un error');
                desbloquear();
            }
        });

        e.stopPropagation();
    });

    $(document).on('click', '.exportar_ig', function (e) {
        e.preventDefault();
        var a = $(this);
        show_dialog({
            titulo: 'Impuesto a las ganancias',
            contenido: formulario_anio_exportacion,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=anio_exportacion]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {
                    location.href = a.attr('href') + '/' + $('#anio_exportacion_input').val();
                } else {
                    return false;
                }
            }
        });

        desbloquear();

        $('.bootbox').removeAttr('tabindex');

        initAnio();


    });

    $(document).on('click', '.exportar_649', function (e) {
        e.preventDefault();
        var a = $(this);
        show_dialog({
            titulo: 'Exportación de formulario 649',
            contenido: formulario_anio_exportacion,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=anio_exportacion]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {
                    location.href = a.attr('href') + '/' + $('#anio_exportacion_input').val();
                } else {
                    return false;
                }
            }
        });

        desbloquear();

        $('.bootbox').removeAttr('tabindex');

        initAnio();


    });

});

// Define si una novedad es porcentaje o valor
function porcentajeValor(boolean) {
    if (boolean) {
        $('#novedades_valor_label').html('Porcentaje');
        $('#novedades_valor').closest('div').removeClass('input');
        $('#novedades_valor').closest('div').addClass('input-group');
        $('#novedades_valor').closest('div').append('<span class="input-group-addon">%</span>');
    } else {
        $('#novedades_valor_label').html('Valor');
        $('#novedades_valor').closest('div').removeClass('input-group');
        $('#novedades_valor').closest('div').addClass('input');
        $('#novedades_valor').closest('div').find('.input-group-addon').remove();
    }
}

/**
 * 
 * @returns {undefined}
 */
function initAnio() {
    $('#anio_exportacion_input').parent().wrap('<div class="input-group"></div>');

    initDatepicker($('#anio_exportacion_input'), {
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });

    $('<span class="input-group-addon"><i class="fa fa-calendar"></i></span>')
            .insertBefore($('#anio_exportacion_input').parent());

    $('#anio_exportacion_input').prop('readonly', true);
}