$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_area"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_area[nombre]': {
                required: true
            }
        }
    });
});