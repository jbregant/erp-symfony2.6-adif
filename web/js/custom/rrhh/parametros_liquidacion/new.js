$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_parametrosliquidacion"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_parametrosliquidacion[valor]': {
                required: true
            }
        },
        ignore: ".ignore"
    });
});