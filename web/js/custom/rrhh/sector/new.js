$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_sector"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_sector[nombre]': {
                required: true
            }
        }
    });
});