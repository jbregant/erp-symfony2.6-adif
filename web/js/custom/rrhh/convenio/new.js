$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_convenio"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_convenio[nombre]': {
                required: true
            }
        }
    });
});