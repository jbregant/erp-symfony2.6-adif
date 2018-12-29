var _contactos_tipo_relacion_select = $('#contacto_tipo_relacion');
var _contactos_apellido = $("#contacto_apellido");
var _contactos_nombre = $("#contacto_nombre");
var _contactos_domicilio = $("#contacto_domicilio");
var _contactos_telefono = $("#contacto_telefono");

var _contactos_add_clicked = false;

var _contactos_required_dependency =  function(element){
    return _contactos_add_clicked;
}

$(document).ready(function(){
    
    // Iniciar el validador
    getEmpleadoValidator();
    
    // PARENTESCO
    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'tipos_relacion/lista_tipos',
        success: function(data) {
            for (var i = 0, total = data.length; i < total; i++) {
                _contactos_tipo_relacion_select.append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
            }
            _contactos_tipo_relacion_select.select2();
        }
    });
    
    // Agregar con dependencia
    _contactos_tipo_relacion_select.rules('add', { required: _contactos_required_dependency });
    _contactos_apellido.rules('add', { required: _contactos_required_dependency });
    _contactos_nombre.rules('add', { required: _contactos_required_dependency });
    _contactos_domicilio.rules('add', { required: _contactos_required_dependency });
    _contactos_telefono.rules('add', { required: _contactos_required_dependency });
    
    $('#contacto_agregar').on('click', function(e){
        _contactos_add_clicked = true;
        e.preventDefault();
        
        $("#contacto_apellido,#contacto_nombre,#contacto_domicilio,#contacto_telefono").each(function(){
            $(this).closest('.form-group').removeClass('has-success has-error');
        });
        
        var ok_val = 
               getEmpleadoValidator().element("#contacto_apellido")
            &  getEmpleadoValidator().element("#contacto_nombre")
            &  getEmpleadoValidator().element("#contacto_domicilio")
            &  getEmpleadoValidator().element("#contacto_telefono")
        ;

        if (!ok_val){
            _contactos_add_clicked = false;
            return false;
        }
                
        var apellido = $('#contacto_apellido').val();
        var nombre = $('#contacto_nombre').val();        
        var domicilio = $('#contacto_domicilio').val();
        var telefono = $('#contacto_telefono').val();
        var id_tipo_relacion = $('#contacto_tipo_relacion').val();
        var tipo_relacion = id_tipo_relacion ? $('#contacto_tipo_relacion option:selected').text() : '';
        
        var indice_nuevo = $('#contactos_table tbody tr').length + 1;
        
        $('#contactos_table tbody').append(
            '<tr tr_index="'+indice_nuevo+'">\n\
                <td>'+apellido+'</td>\n\
                <td>'+nombre+'</td>\n\
                <td>'+domicilio+'</td>\n\
                <td>'+telefono+'</td>\n\
                <td>'+tipo_relacion+'</td>\n\
                <td>\n\
                    <a class="btn default btn-xs red contacto_borrar" >\n\
                        <i class="fa fa-trash-o"></i> Borrar\n\
                    </a>\n\
                </td>\n\
            </tr>');
        
        $('#contactos_datos').append('\n\
            <input name="adif_recursoshumanosbundle_empleado_contacto['+indice_nuevo+'][id]" type="hidden" />\n\
            <input name="adif_recursoshumanosbundle_empleado_contacto['+indice_nuevo+'][apellido]" type="hidden" value="'+apellido+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_contacto['+indice_nuevo+'][nombre]" type="hidden" value="'+nombre+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_contacto['+indice_nuevo+'][domicilio]" type="hidden" value="'+domicilio+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_contacto['+indice_nuevo+'][telefono]" type="hidden" value="'+telefono+'"/>\n\
            <input name="adif_recursoshumanosbundle_empleado_contacto['+indice_nuevo+'][id_tipo_relacion]" type="hidden" value="'+id_tipo_relacion+'"/>'
        );

        $('#row_contactos input[type=text],#row_contactos select').val('');
        $("#contacto_apellido,#contacto_nombre,#contacto_domicilio,#contacto_telefono").each(function(){
            $(this).closest('.form-group').removeClass('has-success has-error');
        });
        
        _contactos_add_clicked = false;
    });
    
    $(document).on('click','.contacto_borrar',function(e){
        e.preventDefault();
        var indice_a_borrar = $(this).parents('tr').attr('tr_index');
        
        $('[name^=adif_recursoshumanosbundle_empleado_contacto\\['+indice_a_borrar+'\\]]').remove();
        $(this).parents('tr').remove();
    });
});
