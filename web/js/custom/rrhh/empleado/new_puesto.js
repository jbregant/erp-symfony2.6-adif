var _empleado_convenio_select = $('#adif_recursoshumanosbundle_empleado_idConvenio');
var _empleado_categoria_select = $('#adif_recursoshumanosbundle_empleado_idCategoria');
var _empleado_subcategoria_select = $('#adif_recursoshumanosbundle_empleado_idSubcategoria');
var _empleado_subcategoria_fecha = $('#adif_recursoshumanosbundle_empleado_idSubcategoria_fecha');
var _empleado_subcategoria_original = $('#adif_recursoshumanosbundle_empleado_idSubcategoria_original');

var _empleado_edit_puesto_convenio = $('#empleado_edit_puesto > [name=empleado_edit_puesto_convenio]');
var _empleado_edit_puesto_categoria = $('#empleado_edit_puesto > [name=empleado_edit_puesto_categoria]');
var _empleado_edit_puesto_subcategoria = $('#empleado_edit_puesto > [name=empleado_edit_puesto_subcategoria]');

var _empleado_edit_puesto_historial_puesto = $('#puesto_puesto_historial');
var _empleado_edit_puesto_historial_categoria = $('#puesto_categoria_historial');

var _presenta_f_649 = $('#adif_recursoshumanosbundle_empleado_presenta649');

var _edit_subcategoria = false;

$(document).ready(function () {
    reset_select(_empleado_categoria_select);
    reset_select(_empleado_subcategoria_select);

    if (_is_edit && !_edit_subcategoria) {
        _empleado_convenio_select.val(_empleado_edit_puesto_convenio.val());
    }

    _empleado_categoria_select.change(function () {
        var data = {
            id_categoria: $(this).val()
        };

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'subcategorias/lista_subcategorias',
            data: data,
            success: function (data) {
                var subcategoria_selector = _empleado_subcategoria_select.empty();
                _empleado_subcategoria_select.prop('disabled', false);

                for (var i = 0, total = data.length; i < total; i++) {
                    subcategoria_selector.append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                }
                _empleado_subcategoria_select.prop('required', true);

                if (_is_edit && !_edit_subcategoria) {
                    _edit_subcategoria = false;
                    _empleado_subcategoria_select.val(_empleado_edit_puesto_subcategoria.val());
                } else {
                    _empleado_subcategoria_select.val(_empleado_subcategoria_select.find('option:first').val());
                }
                _empleado_subcategoria_select.select2();
            }
        });
    })

    _empleado_convenio_select.change(function () {
        var data = {
            id_convenio: $(this).val()
        };

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'categorias/lista_categorias',
            data: data,
            success: function (data) {
                reset_select(_empleado_categoria_select);
                reset_select(_empleado_subcategoria_select);

                _empleado_categoria_select.prop('disabled', false);
                for (var i = 0, total = data.length; i < total; i++) {
                    _empleado_categoria_select.append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                }
                _empleado_categoria_select.prop('required', true);

                if (_is_edit && !_edit_subcategoria) {
                    _empleado_categoria_select.val(_empleado_edit_puesto_categoria.val());
                } else {
                    _empleado_categoria_select.val(_empleado_categoria_select.find('option:first').val());
                }

                _empleado_categoria_select.select2();
                _empleado_categoria_select.trigger('change');
            }
        });
    }).trigger('change');

    _empleado_edit_puesto_historial_puesto.on('click', function (e) {
        var b = bootbox.dialog({
            title: 'Historial de puestos',
            message: $('#ctn_historial_puesto').html()
        });
        b.find('.modal-dialog').css('width', '70%');
    })

    _empleado_edit_puesto_historial_categoria.on('click', function (e) {
        var b = bootbox.dialog({
            title: 'Historial de categorizaci&oacute;n',
            message: $('#ctn_historial_categoria').html()
        });
        b.find('.modal-dialog').css('width', '70%');
    })


    if (_empleado_subcategoria_original.length > 0) {
        getEmpleadoValidator();
        var _empleado_subcategoria_fecha_required_dependency = function (element) {
            return function () {
                return _empleado_subcategoria_fecha.val() != _empleado_subcategoria_original.val()
            };
        };

        _empleado_subcategoria_fecha.rules('add', {required: _empleado_subcategoria_fecha_required_dependency});

        _empleado_subcategoria_select.change(function () {
            if (this.value !== _empleado_subcategoria_original.val()) {
                $('#subcategoria_fecha_ctn').show();
            } else {
                $('#subcategoria_fecha_ctn').hide();
            }
        })
    }

    if ($('#adif_recursoshumanosbundle_empleado_formulario649_gananciaAcumulada').val() != '') {
        $('form[name=adif_recursoshumanosbundle_empleado]').validate();
        $('#adif_recursoshumanosbundle_empleado_formulario649_gananciaAcumulada').rules('add', {
            required: true
        });
        $('#adif_recursoshumanosbundle_empleado_formulario649_fechaFormulario').rules('add', {
            required: true
        });
        $('#adif_recursoshumanosbundle_empleado_formulario649_totalImpuestoDeterminado').rules('add', {
            required: true
        });
    }


    _presenta_f_649.change(function () {
        if ($(this).is(':checked')) {
            $('.div_form_649').show(500);
            $('form[name=adif_recursoshumanosbundle_empleado]').validate();
            $('#adif_recursoshumanosbundle_empleado_formulario649_gananciaAcumulada').rules('add', {
                required: true
            });
            $('#adif_recursoshumanosbundle_empleado_formulario649_fechaFormulario').rules('add', {
                required: true
            });
            $('#adif_recursoshumanosbundle_empleado_formulario649_totalImpuestoDeterminado').rules('add', {
                required: true
            });
        }
        else {
            $('.div_form_649').hide(500);
            $('form[name=adif_recursoshumanosbundle_empleado]').validate();
            $('#adif_recursoshumanosbundle_empleado_formulario649_gananciaAcumulada').rules('remove');
            $('#adif_recursoshumanosbundle_empleado_formulario649_gananciaAcumulada').val('');
            $('#adif_recursoshumanosbundle_empleado_formulario649_fechaFormulario').rules('remove');
            $('#adif_recursoshumanosbundle_empleado_formulario649_fechaFormulario').val('');
            $('#adif_recursoshumanosbundle_empleado_formulario649_totalImpuestoDeterminado').rules('remove');
            $('#adif_recursoshumanosbundle_empleado_formulario649_totalImpuestoDeterminado').val('');
        }
    });
});
