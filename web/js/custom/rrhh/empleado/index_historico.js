var dt_empleado; // Empleado DT
var dt_empleado_column_index = {
    id: 0,
    multiselect: 1,
    legajo: 2,
    apellido: 3,
    nombre: 4,
    cuil: 5,
    fecha_ingreso: 6,
    fecha_egreso: 7,
    convenio: 8,
    categoria: 9,
    acciones: 10
};

var indexVisibleToDT = function (column_index) {
    return dt_empleado.column(column_index).index();
};

var indexDTToVisible = function (column_index) {
    return dt_empleado.column(column_index).index('visible');
};

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
    $(document).on('click', '.empleado_activar', function (e) {
        e.preventDefault();
        var a = $(this);
        show_confirm({
            title: 'Activar empleado',
            msg: 'Confirma activar al empleado <b>' + $(this).parents('tr').find('td:nth-child(' + (indexDTToVisible(dt_empleado_column_index.apellido) + 1) + ')').text() + ', ' + $(this).parents('tr').find('td:nth-child(' + (indexDTToVisible(dt_empleado_column_index.nombre) + 1) + ')').text() + '</b>?',
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

    $('#table-empleado-historico').on('selected_element', function (e, cantidad) {
        $('#cant_seleccionados').text(cantidad);

        //cantidad > 0 ? $('.hide-if-non-selected').fadeIn(200) : $('.hide-if-non-selected').fadeOut(200);
    });

    dt_empleado = dt_datatable($('#table-empleado-historico'), {
        ajax: __AJAX_PATH__ + 'empleados/index_historico_table/',
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
                }
            }, {
                "targets": dt_empleado_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {
                    var full_data = full[dt_empleado_column_index.acciones];
                    return '<a tabindex="0" class="btn btn-xs btn-primary btn-group-popover" data-toggle="popover" html=\''
                            + ((full_data.show !== undefined ?
                                    '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-toggle="tooltip" data-original-title="Ver detalle"><i class="fa fa-search"></i></a>' : '')
                                    + (full_data.impuesto_ganancias_excel !== undefined ?
                                            '<a href="' + full_data.impuesto_ganancias_excel + '" class="btn btn-xs dark tooltips exportar_ig" data-toggle="tooltip" data-original-title="Impuesto a las Ganancias"><i class="fa fa-letter">IG</i></a>' : '')
                                    + (full_data.formulario572_index_empleado !== undefined ?
                                            '<a href="' + full_data.formulario572_index_empleado + '" class="btn btn-xs blue-hoki tooltips icon-f572" data-toggle="tooltip" data-original-title="Ver formularios 572"><i class="fa fa-letter">F572</i></a>' : '')
                                    + (full_data.formulario649 !== undefined ?
                                            '<a href="' + full_data.formulario649 + '" class="btn btn-xs blue-chambray tooltips icon-f572 exportar_649" data-toggle="tooltip" data-original-title="Exportar formulario 649"><i class="fa fa-letter">F649</i></a>' : '')
                                    + (full_data.recibos !== undefined ?
                                            '<a href="' + full_data.recibos + '" class="btn btn-xs btn-success tooltips empleado_recibos" data-toggle="tooltip" data-original-title="Imprimir recibos"><i class="fa fa-letter">Recibos</i></a>' : '')
                                    + (full_data.activar !== undefined ?
                                            '<a href="' + full_data.activar + '" class="btn btn-xs btn-success tooltips empleado_activar" data-toggle="tooltip" data-original-title="Activar"><i class="fa fa-check"></i></a>' : '')
                                    ) + '\'>&nbsp;<i class="fa fa-bolt"></i> Opciones&nbsp;</a>';
                }
            },
            {className: "text-center", targets: [
                    dt_empleado_column_index.multiselect,
                    dt_empleado_column_index.legajo,
                    dt_empleado_column_index.cuil,
                    dt_empleado_column_index.fecha_ingreso,
                    dt_empleado_column_index.fecha_egreso,
                    dt_empleado_column_index.convenio
                ]},
            {className: "ctn_acciones text-center nowrap", targets: dt_empleado_column_index.acciones},
            {"width": "30px", "targets": dt_empleado_column_index.legajo},
            {"width": "100px", "targets": dt_empleado_column_index.fecha_ingreso},
            {"width": "100px", "targets": dt_empleado_column_index.fecha_egreso},
            {"width": "130px", "targets": dt_empleado_column_index.convenio},
            {"width": "350px", "targets": dt_empleado_column_index.categoria}
        ]
    });

    dt_empleado = dt_empleado.DataTable();

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