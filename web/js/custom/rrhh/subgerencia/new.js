$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_subgerencia"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_subgerencia[nombre]': {
                required: true
            }
        }
    });
});