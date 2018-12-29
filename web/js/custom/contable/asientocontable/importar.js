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
    
    // Config fecha asiento contable    
    if (typeof fechaMesCerradoSuperior != 'undefined') {
        $('#form_fechaContable')
                .datepicker('setStartDate', fechaMesCerradoSuperior);

        $('#form_fechaContable')
                .datepicker('setEndDate', getCurrentDate());
    }
    
    $('.btn-guardar-asiento').on('click', function(e) {
        
        e.preventDefault();
        
        var url = $(this).attr('back-url');

        show_confirm({
            msg: '¿Desea guardar el asiento?',
            callbackOK: function () {
                blockBody();
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });
    
});