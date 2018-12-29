var _estudios_niveles_estudio_select = $('#estudio_nivel');
var _estudios_titulo_select = $('#estudio_titulo');
var _estudios_establecimiento = $("#estudio_establecimiento");
var _estudios_fecha_inicio = $("#estudio_fecha_inicio");
var _estudios_fecha_fin = $("#estudio_fecha_fin");

var _estudios_add_clicked = false;

var _estudios_required_dependency = function(element) {
    return _estudios_add_clicked;
};

$(document).ready(function() {
    // Iniciar el validador
    getEmpleadoValidator();

    cargarTitulos();

    // NIVELES ESTUDIO
    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'nivelesestudio/lista_niveles',
        success: function(data) {
            for (var i = 0, total = data.length; i < total; i++) {
                _estudios_niveles_estudio_select.append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
            }
            _estudios_niveles_estudio_select.select2();
        }
    });

    // Agregar con dependencia
    _estudios_titulo_select.rules('add', {required: _estudios_required_dependency});
    _estudios_niveles_estudio_select.rules('add', {required: _estudios_required_dependency});
    _estudios_establecimiento.rules('add', {required: _estudios_required_dependency});

    $('#estudio_agregar').on('click', function(e) {
        _estudios_add_clicked = true;
        e.preventDefault();

        $("#estudio_establecimiento").each(function() {
            $(this).closest('.form-group').removeClass('has-success has-error');
        });

        var ok_val =
                getEmpleadoValidator().element("#estudio_establecimiento")
                & getEmpleadoValidator().element("#estudio_nivel")
                //&  getEmpleadoValidator().element("#estudio_fecha_fin")
                ;

        if (!ok_val) {
            _estudios_add_clicked = false;
            return false;
        }

        var establecimiento = $('#estudio_establecimiento').val();
        var id_titulo = $('#estudio_titulo').val();
        var titulo = id_titulo ? $('#estudio_titulo option:selected').text() : '';
        var id_nivel = $('#estudio_nivel').val();
        var nivel = id_nivel ? $('#estudio_nivel option:selected').text() : '';
        var fecha_inicio = $('#estudio_fecha_inicio').val();
        var fecha_fin = $('#estudio_fecha_fin').val();

        var indice_nuevo = $('#estudio_estudios_table tbody tr').length + 1;

        $('#estudio_estudios_table tbody').append(
                '<tr tr_index="' + indice_nuevo + '">\n\
                <td>' + establecimiento + '</td>\n\
                <td>' + titulo + '</td>\n\
                <td>' + nivel + '</td>\n\
                <td>' + fecha_inicio + '</td>\n\
                <td>' + fecha_fin + '</td>\n\
                <td>\n\
                    <a class="btn default btn-xs red estudio_borrar" >\n\
                        <i class="fa fa-trash-o"></i> Borrar\n\
                    </a>\n\
                </td>\n\
            </tr>');

        $('#estudios_datos').append('\n\
            <input name="adif_recursoshumanosbundle_empleado_estudio[' + indice_nuevo + '][id]" type="hidden" />\n\
            <input name="adif_recursoshumanosbundle_empleado_estudio[' + indice_nuevo + '][establecimiento]" type="hidden" value="' + establecimiento + '"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_estudio[' + indice_nuevo + '][fecha_desde]" type="hidden" value="' + fecha_inicio + '"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_estudio[' + indice_nuevo + '][fecha_hasta]" type="hidden" value="' + fecha_fin + '"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_estudio[' + indice_nuevo + '][id_nivel_estudio]" type="hidden" value="' + id_nivel + '"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_estudio[' + indice_nuevo + '][id_titulo_universitario]" type="hidden" value="' + id_titulo + '"/>'
                );

        $('#row_estudios input[type=text],#row_estudios select').val('');
        $("#estudio_establecimiento").each(function() {
            $(this).closest('.form-group').removeClass('has-success has-error');
        });

        _estudios_add_clicked = false;
    });

    $(document).on('click', '.estudio_borrar', function(e) {
        e.preventDefault();
        var indice_a_borrar = $(this).parents('tr').attr('tr_index');

        $('[name^=adif_recursoshumanosbundle_empleado_estudio\\[' + indice_a_borrar + '\\]]').remove();
        $(this).parents('tr').remove();
    });

    $('a#titulo_agregar').colorbox({
        iframe: true,
        fastIframe: false,
        width: "90%",
        height: '200px',
        onOpen: function(e) {

            $('body').one('popup_closed', function(e, data) {
                var nuevo_titulo_id = data.id;
                cargarTitulos(nuevo_titulo_id);
                $.colorbox.close();
            });
        },
        onComplete: function(e) {
            var iframe = $('#cboxLoadedContent iframe');

            iframe.load(function() {
                initIframe(iframe);
            }).trigger('load');
        }
    });
});

/**
 * 
 * @param {type} iframe
 * @returns {undefined}
 */
function initIframe(iframe) {

    var iframeCtn = iframe.contents();

    //$.colorbox.resize({innerHeight : $('iframe').contents().find('html').height() + 40})
    //$.colorbox.resize();

    iframeCtn.find('.page-content').css({
        padding: 0,
        minHeight: 0
    });

    iframeCtn.find('.page-content > .row').removeClass('margin-bottom-20');
    iframeCtn.find('.page-content > .row .portlet.box').css('margin-bottom', 0);

    iframeCtn.find('.btn.default.button-back').remove();

    //$.colorbox.resize();
    $.colorbox.resize({innerHeight: $('iframe').contents().find('html').height() + 45});

    iframeCtn.find('.page-content').css({
        padding: 0
    });
}

// TITULOS ESTUDIO
function cargarTitulos(seleccionado) {
    seleccionado = seleccionado || '';
    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'titulos_universitarios/lista_titulos',
        success: function(data) {
            for (var i = 0, total = data.length; i < total; i++) {
                _estudios_titulo_select.append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
            }
            _estudios_titulo_select.val(seleccionado);
            _estudios_titulo_select.select2();
        }
    });
}