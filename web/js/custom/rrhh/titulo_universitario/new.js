$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_titulouniversitario"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_titulouniversitario[nombre]': {
                required: true
            }
        }
    });
});