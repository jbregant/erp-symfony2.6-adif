$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_banco"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_banco[nombre]': {
                required: true
            }
        }
    });
});