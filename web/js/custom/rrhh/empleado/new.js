var _is_edit = $('[name=_method]').length > 0;
var _empleado_validator = null;

function reset_select(id_select){
    $(id_select).empty();
//    $(id_select).append($('<option>', {value:"0", text: "-- Seleccione una opci"+_o_ACUTE+"n --"}));
    $(id_select).select2("val", "");
    $(id_select).prop('disabled', true);
}

function getEmpleadoValidator(){
    if (_empleado_validator === null){
        
        // Sacar validaciones por atributo de las secciones validables temporalmente, ya que impide el submit del form en gral
        $('[id^=estudio_],[id^=familiar_],[id^=contacto_]').removeAttr('required');
        
        _empleado_validator = $('#empleado_submit_form').validate({
            doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
        });
    }
    
    return _empleado_validator;
}

$(document).ready(function(){
    initNombreEmpleadoUpdate();
})

/**
 * 
 * @returns {undefined}
 */
function initNombreEmpleadoUpdate() {
    $('#adif_recursoshumanosbundle_empleado_persona_apellido').focusout(function() {
        updateNombreEmpleado();
    });

    $('#adif_recursoshumanosbundle_empleado_persona_nombre').focusout(function() {
        updateNombreEmpleado();
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateNombreEmpleado() {
    var nombre = $('#adif_recursoshumanosbundle_empleado_persona_nombre').val();
    var apellido = $('#adif_recursoshumanosbundle_empleado_persona_apellido').val();
    var nombreEmpleado = apellido + (apellido && nombre ? ", " : "")  + nombre;
    $('#nombre_empleado').html(nombreEmpleado);
}