var _empleado_cuenta_cbu = $('#adif_recursoshumanosbundle_empleado_idCuenta_cbu');
var _empleado_cuenta_banco = $('#adif_recursoshumanosbundle_empleado_idCuenta_idBanco');
var _empleado_cuenta_tipo = $('#adif_recursoshumanosbundle_empleado_idCuenta_idTipoCuenta');

var _empleado_cuenta_cargar = $('#adif_recursoshumanosbundle_empleado_idCuenta_cargar');

var _empleado_cuenta_required_dependency =  function(element){
    return function(){ 
    	return _empleado_cuenta_cargar.is(':checked')
    };
};

$(document).ready(function() {
    
    _empleado_cuenta_cargar.on('change', function(e){
        var checked = $(this).is(':checked');
        $([_empleado_cuenta_cbu,_empleado_cuenta_banco,_empleado_cuenta_tipo]).each(function(){
            $(this).attr('disabled', !checked);
        });
    });
    
    _empleado_cuenta_cargar.trigger('change');

    _empleado_cuenta_cbu.rules('add', { required: _empleado_cuenta_required_dependency });
    _empleado_cuenta_banco.rules('add', { required: _empleado_cuenta_required_dependency });
    _empleado_cuenta_tipo.rules('add', { required: _empleado_cuenta_required_dependency });

    getEmpleadoValidator();
    
    $(_empleado_cuenta_cbu).rules('add', {
        cbu: true
    });
    
    $(_empleado_cuenta_cbu).inputmask({mask: "9", repeat: 22, placeholder: ""});    
});