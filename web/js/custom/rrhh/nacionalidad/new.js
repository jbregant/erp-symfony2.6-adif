$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_nacionalidad"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_nacionalidad[nombre]': {
                required: true
            }
        }
    });
});