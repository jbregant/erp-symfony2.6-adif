var _familiares_tipo_documento_select = $('#familiar_tipo_documento');
var _familiares_tipo_relacion_select = $('#familiar_id_tipo_relacion');
var _familiares_sexo_select = $('#familiar_sexo');
var _familiares_fecha_nacimiento = $('#familiar_fecha_nacimiento');
var _familiares_apellido = $("#familiar_apellido");
var _familiares_nombre = $("#familiar_nombre");
var _familiares_documento =  $("#familiar_documento");

var _familiares_add_clicked = false;

var _familiares_required_dependency =  function(element){
    return _familiares_add_clicked;
};

$(document).ready(function(){
    // Iniciar el validador
    getEmpleadoValidator();
    
    // Validaciones
    _familiares_tipo_relacion_select.rules('add', { required: _familiares_required_dependency });
    _familiares_tipo_documento_select.rules('add', { required: _familiares_required_dependency });
    _familiares_sexo_select.rules('add', { required: _familiares_required_dependency });
    _familiares_fecha_nacimiento.rules('add', { required: _familiares_required_dependency });
    _familiares_apellido.rules('add', { required: _familiares_required_dependency });
    _familiares_nombre.rules('add', { required: _familiares_required_dependency });
    _familiares_documento.rules('add', { required: _familiares_required_dependency });
    
    // NIVELES ESTUDIO
    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'tipos_documento/lista_tipos_documento',
        success: function(data) {
            for (var i = 0, total = data.length; i < total; i++) {
                _familiares_tipo_documento_select.append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
            }
            _familiares_tipo_documento_select.select2();
        }
    });
    
    // TIPOS RELACION
    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'tipos_relacion/lista_tipos',
        success: function(data) {
            for (var i = 0, total = data.length; i < total; i++) {
               _familiares_tipo_relacion_select.append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
            }
            _familiares_tipo_relacion_select.select2();
        }
    });
    
    $('#familiar_agregar').on('click', function(e){
        _familiares_add_clicked = true;
        e.preventDefault();
        
        $("#familiar_apellido,#familiar_nombre,#familiar_tipo_documento,#familiar_documento,#familiar_sexo#familiar_id_tipo_relacion").each(function(){
            $(this).closest('.form-group').removeClass('has-success has-error');
        });
        
        var ok_val = 
               getEmpleadoValidator().element("#familiar_apellido")
            &  getEmpleadoValidator().element("#familiar_nombre")
            &  getEmpleadoValidator().element("#familiar_tipo_documento")
            &  getEmpleadoValidator().element("#familiar_documento")
            &  getEmpleadoValidator().element("#familiar_sexo")
            &  getEmpleadoValidator().element("#familiar_fecha_nacimiento")
            &  getEmpleadoValidator().element("#familiar_id_tipo_relacion")
        ;

        if (!ok_val){
            _familiares_add_clicked = false;
            return false;
        }
                
        var apellido = $('#familiar_apellido').val();
        var nombre = $('#familiar_nombre').val();
        var id_tipo_documento = $('#familiar_tipo_documento').val();
        var documento = $('#familiar_documento').val();
        var sexo = $('#familiar_sexo').val();
        var fecha_nacimiento = $('#familiar_fecha_nacimiento').val();
        var escolaridad = $('#familiar_escolaridad').val();
        var anio_cursa = $('#familiar_anio_cursa').val();
        var en_guarderia = $('#familiar_en_guarderia').val();
        var a_cargo_os = $('#familiar_a_cargo_os').val();
        var id_tipo_relacion = $('#familiar_id_tipo_relacion').val();
        var tipo_relacion = $('#familiar_id_tipo_relacion option:selected').text();
        
        var maximo_indice = 0;
        $('#familiar_familiares_table tbody tr').each(function(){
            var value = parseFloat($(this).attr('tr_index'));
            maximo_indice = (value > maximo_indice) ? value : maximo_indice;
        });
        var indice_nuevo = maximo_indice + 1;
        
        $('#familiar_familiares_table tbody').append(
            '<tr tr_index="'+indice_nuevo+'">\n\
                <td>'+(apellido+', '+nombre)+'</td>\n\
                <td>'+documento+'</td>\n\
                <td>'+(sexo == 'F' ? 'Femenino' : 'Masculino')+'</td>\n\
                <td>'+fecha_nacimiento+'</td>\n\
                <td>'+tipo_relacion +'</td>\n\
                <td>'+escolaridad+'</td>\n\
                <td>'+anio_cursa+'</td>\n\
                <td>'+(en_guarderia == 1 ? 'SI' : 'NO')+'</td>\n\\n\
                <td>'+(a_cargo_os == 1 ? 'SI' : 'NO')+'</td>\n\
                <td>\n\
                    <a class="btn default btn-xs red familiar_borrar" >\n\
                        <i class="fa fa-trash-o"></i> Borrar\n\
                    </a>\n\
                    <a class="btn default btn-xs green familiar_editar" >\n\
                        <i class="fa fa-pencil"></i> Editar\n\
                    </a>\n\
                </td>\n\
            </tr>');
        
        $('#familiares_datos').append('\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][id]" type="hidden" value=""/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][apellido]" type="hidden" value="'+apellido+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][nombre]" type="hidden" value="'+nombre+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][id_tipo_documento]" type="hidden" value="'+id_tipo_documento+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][documento]" type="hidden" value="'+documento+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][sexo]" type="hidden" value="'+sexo+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][fecha_nacimiento]" type="hidden" value="'+fecha_nacimiento+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][id_tipo_relacion]" type="hidden" value="'+id_tipo_relacion+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][escolaridad]" type="hidden" value="'+escolaridad+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][anio_cursa]" type="hidden" value="'+anio_cursa+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][en_guarderia]" type="hidden" value="'+en_guarderia+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_familiar['+indice_nuevo+'][a_cargo_os]" type="hidden" value="'+a_cargo_os+'"/>'
        );

        $('.row_familiares input[type=text]').val('');
        $('.row_familiares select').each(function(){
            $(this).find('option')
                .removeAttr('selected')
                .first().attr('selected','selected');
            $(this).select2();
        });
            
        $("#familiar_apellido,#familiar_nombre,#familiar_tipo_documento,#familiar_documento,#familiar_sexo#familiar_id_tipo_relacion").each(function(){
            $(this).closest('.form-group').removeClass('has-success has-error');
        });
        
        reset_form();
        
        _familiares_add_clicked = false;
    });
    
    $(document).on('click','.familiar_borrar',function(e){
        e.preventDefault();
        var indice_a_borrar = $(this).parents('tr').attr('tr_index');
        
        $('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_borrar+'\\]]').remove();
        $(this).parents('tr').remove();
    });
    
    $(document).on('click','.familiar_editar',function(e){
        e.preventDefault();
        var indice_a_editar = $(this).parents('tr').attr('tr_index');
        
        $('#familiar_apellido').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[apellido\\]]').val());
        $('#familiar_nombre').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[nombre\\]]').val());
        $('#familiar_tipo_documento').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[id_tipo_documento\\]]').val()).select2();
        $('#familiar_documento').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[documento\\]]').val());
        $('#familiar_sexo').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[sexo\\]]').val());
        $('#familiar_fecha_nacimiento').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[fecha_nacimiento\\]]').val());
        $('#familiar_id_tipo_relacion').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[id_tipo_relacion\\]]').val()).select2();
        $('#familiar_escolaridad').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[escolaridad\\]]').val());
        $('#familiar_anio_cursa').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[anio_cursa\\]]').val());
        $('#familiar_en_guarderia').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[en_guarderia\\]]').val() == '' ? 0 : 1).select2();
        $('#familiar_a_cargo_os').val($('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]\\[a_cargo_os\\]]').val() == '' ? 0 : 1).select2();
        
        $('[name^=adif_recursoshumanosbundle_empleado_familiar\\['+indice_a_editar+'\\]]').remove();
        
        $('#familiar_agregar_text').html('Guardar');
        
        $(this).parents('tr').remove();
    });
});

function reset_form(){
    $('input[name^="familiar_"]').val('');
    $("#familiar_tipo_documento").select2("val", "1");
    $("#familiar_id_tipo_relacion").select2("val", "8");
    $("#familiar_en_guarderia").select2("val", "0");
    $("#familiar_a_cargo_os").select2("val", "0");
    $('#familiar_agregar_text').html('Agregar');
}