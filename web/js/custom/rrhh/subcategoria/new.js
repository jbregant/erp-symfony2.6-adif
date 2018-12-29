$(document).ready(function(){    
    $('form[name="adif_recursoshumanosbundle_subcategoria"]').validate({        
        rules: {
            'adif_recursoshumanosbundle_subcategoria[nombre]': {
                required: true
            },
            'adif_recursoshumanosbundle_subcategoria[montoBasico]': {
                required: true
            }
        }
    });
});