$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_gerencia"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_gerencia[nombre]': {
                required: true
            }
        }
    });
});