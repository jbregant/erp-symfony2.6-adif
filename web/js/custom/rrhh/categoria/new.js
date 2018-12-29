$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_categoria"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_categoria[nombre]': {
                required: true
            }
        }
    });
});