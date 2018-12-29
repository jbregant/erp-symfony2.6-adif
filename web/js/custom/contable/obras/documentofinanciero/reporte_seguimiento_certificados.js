$(document).ready(function(){    
    $('form[name="form"]').validate({        
        rules: {
            'form[tiposDocumento]': {
                required: true
            }
        },
        messages: {
            'form[tiposDocumento]': {
                required: "Por favor, seleccione al menos un tipo de documento"
            }  
        }
    });
});