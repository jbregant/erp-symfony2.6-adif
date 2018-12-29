var isEdit = $('[name=_method]').length > 0;

var $selectProvinciaDomicilio = $('#adif_contablebundle_beneficiarioliquidacion_domicilio_idProvincia');

var $selectLocalidadDomicilio = $('#adif_contablebundle_beneficiarioliquidacion_domicilio_localidad');

var beneficiarioProvinciaDomicilio = $('#beneficiarioliquidacion_domicilio > [name=beneficiarioliquidacion_provincia]');

var beneficiarioLocalidadDomicilio = $('#beneficiarioliquidacion_domicilio > [name=beneficiarioliquidacion_localidad]');

$(document).ready(function() {
    $('form[name=adif_contablebundle_beneficiarioliquidacion]').validate({
        rules: {
            'adif_contablebundle_beneficiarioliquidacion[nombre]': {
                required: true
            },
            'adif_contablebundle_beneficiarioliquidacion[cuentasContables]': {
                required: true
            }
        }
    });
    
    var $inputCUIT = $('#adif_contablebundle_beneficiarioliquidacion_CUIT');
    
    //rules
    $inputCUIT.rules('add', {
        required: true,
        cuil: true,
        messages: {
            cuil: "Formato de CUIT incorrecto."
        }
    });
    $inputCUIT.inputmask({
        mask: "99-99999999-9",
        placeholder: "_"
    });
    
    initDomicilioSelect();
});

/**
 * 
 * @returns {undefined}
 */
function initDomicilioSelect() {

    // INIT Domicilio Entrega
    if (isEdit) {
        $selectProvinciaDomicilio.val(beneficiarioProvinciaDomicilio.val());
    }

    $selectProvinciaDomicilio.change(function () {
        var data = {
            id_provincia: $(this).val()
        };

        resetSelect($selectLocalidadDomicilio);

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'localidades/lista_localidades',
            data: data,
            success: function (data) {

                $selectLocalidadDomicilio.select2('readonly', false);

                for (var i = 0, total = data.length; i < total; i++) {
                    $selectLocalidadDomicilio
                            .append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                }

                if (isEdit) {                    
                    $selectLocalidadDomicilio.val(beneficiarioLocalidadDomicilio.val());
                }
                else {
                    $selectLocalidadDomicilio.val($selectLocalidadDomicilio.find('option:first').val());
                }

                $selectLocalidadDomicilio.select2();
            }
        });
    }).trigger('change');
}