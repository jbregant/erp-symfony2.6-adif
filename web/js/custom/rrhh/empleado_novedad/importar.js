$(document).ready(function(){    
    $('form[name="form"]').validate({        
        rules: {
            'form[novedades]': {
                required: true
            },
            'form[fechaNovedad]': {
                required: true
            },
            'form[file]': {
                required: true,
                extension: "xls|xlsx"
            }
        },
        messages: {
            'form[file]': {
                extension: "Por favor, ingrese una extesi&oacute;n de archivo v&aacute;lida (XLS, XLSX)"
            }  
        }
    });
    
    $(document).on("change", "#form_novedades", function() {    
        if($('#form_novedades option:selected').attr('es-ajuste') == 1){  
            $('#row_ajuste').show();
        } else {
            $('#row_ajuste').hide();
        }
    });
});