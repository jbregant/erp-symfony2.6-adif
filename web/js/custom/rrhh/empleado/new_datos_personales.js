var _empleado_dp_nro_doc = $('#adif_recursoshumanosbundle_empleado_persona_nroDocumento');
var _empleado_dp_cuil = $('#adif_recursoshumanosbundle_empleado_persona_cuil');
var _empleado_dp_nacionalidad = $('#adif_recursoshumanosbundle_empleado_persona_idNacionalidad');
var _empleado_dp_email = $('#adif_recursoshumanosbundle_empleado_persona_email');
var _empleado_dp_telefono = $('#adif_recursoshumanosbundle_empleado_persona_telefono');
var _empleado_dp_celular = $('#adif_recursoshumanosbundle_empleado_persona_celular');
var _empleado_dp_domicilio_numero = $('#adif_recursoshumanosbundle_empleado_persona_domicilio_numero');
var _empleado_dp_localidades_select = $('#adif_recursoshumanosbundle_empleado_persona_domicilio_localidad');
var _empleado_dp_provincias_select = $('#adif_recursoshumanosbundle_empleado_persona_domicilio_idProvincia');

var _empleado_edit_datos_personales_localidad = $('#empleado_edit_datos_personales > [name=empleado_edit_datos_personales_localidad]')
var _empleado_edit_datos_personales_provincia = $('#empleado_edit_datos_personales > [name=empleado_edit_datos_personales_provincia]')

var _edit_localidades = false;

$(document).ready(function() {

    // Iniciar el validador
    getEmpleadoValidator();

    $(_empleado_dp_localidades_select,_empleado_dp_nacionalidad).rules('add', {
        required: true
    })

    _empleado_dp_cuil.rules('add', {
        cuil: true,
        cuil_igual_dni: function(){ return _empleado_dp_nro_doc.val().replace(/_/g,"") }
    })

    _empleado_dp_email.rules('add', {
        email: true,
    })

    _empleado_dp_nro_doc.inputmask({
        mask: "99999999"
    });
    
    _empleado_dp_cuil.inputmask({
        mask: "99-99999999-9",
        placeholder : "_"
    });
    
    _empleado_dp_telefono.inputmask({mask: "9", repeat: 40, placeholder: ""});
    _empleado_dp_celular.inputmask({mask: "9", repeat: 40, placeholder: ""});
    
    
    reset_select(_empleado_dp_localidades_select);
    
    if (_is_edit && !_edit_localidades){
        _empleado_dp_provincias_select.val(_empleado_edit_datos_personales_provincia.val());
    }
    
    $(_empleado_dp_provincias_select).change(function() {
        var data = {
            id_provincia: $(this).val()
        };
        
        reset_select(_empleado_dp_localidades_select);
        
        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'localidades/lista_localidades',
            data: data,
            success: function(data) {
                $(_empleado_dp_localidades_select).prop('disabled', false);
                for (var i = 0, total = data.length; i < total; i++) {
                    _empleado_dp_localidades_select.append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                }
                
                if (_is_edit && !_edit_localidades){
                    _edit_localidades = true;
                    _empleado_dp_localidades_select.val(_empleado_edit_datos_personales_localidad.val());
                } else {
                    _empleado_dp_localidades_select.val(_empleado_dp_localidades_select.find('option:first').val());
                }
                _empleado_dp_localidades_select.select2();
                
            }
        });
    }).trigger('change');
})