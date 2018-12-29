
var $inputCUIT = $('input[id ^="adif_comprasbundle_clienteproveedor"][id $="CUIT"]');

var $inputCDI = $('input[id ^="adif_comprasbundle_clienteproveedor"][id $="codigoIdentificacion"]');

var $inputDNI = $('input[id ^="adif_comprasbundle_clienteproveedor"][id $="DNI"]');

/**
 * 
 */
jQuery(document).ready(function () {

    initEsExtranjeroHandler();

    initValidaciones();
});

/**
 * 
 * @returns {undefined}
 */
function initEsExtranjeroHandler() {

    $('#adif_comprasbundle_clienteproveedor_esExtranjero').on('switch-change', function () {

        if ($(this).is(':checked')) {

            $('#adif_comprasbundle_clienteproveedor_CUIT').parents('.form-group').addClass('hidden');

            $('#adif_comprasbundle_clienteproveedor_codigoIdentificacion').parents('.form-group').removeClass('hidden');

            //rules
            $inputCUIT.rules("remove");

            $inputCUIT.inputmask('remove');

            $inputCUIT.val('');

            $inputCDI.rules('add', {
                required: true,
				maxlength: 15
            });

//            $inputCDI.inputmask({
//                mask: "9999999999999",
//                placeholder: "_"
//            });

            $('form[name=adif_comprasbundle_clienteproveedor]').submit();

            initTipoDocumento('CDI');

            $('#adif_comprasbundle_clienteproveedor_tipoDocumento').trigger('change');
        } else {

            $('#adif_comprasbundle_clienteproveedor_codigoIdentificacion').parents('.form-group').addClass('hidden');
            $('#adif_comprasbundle_clienteproveedor_CUIT').parents('.form-group').removeClass('hidden');
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

            $inputCDI.rules("remove");

            $inputCDI.inputmask('remove');

            $inputCDI.val('');

            $('form[name=adif_comprasbundle_clienteproveedor]').submit();

            initTipoDocumento('CUIT');

            $('#adif_comprasbundle_clienteproveedor_tipoDocumento').trigger('change');
        }
    });
}

/**
 * 
 * @param {type} tipoAdicional
 * @returns {undefined}
 */
function initTipoDocumento(tipoAdicional) {

    $('#adif_comprasbundle_clienteproveedor_tipoDocumento').select2('destroy', 'destroy');
    $('#adif_comprasbundle_clienteproveedor_tipoDocumento').html('');
    $('#adif_comprasbundle_clienteproveedor_tipoDocumento').append('<option value="dni">DNI</option>');
    $('#adif_comprasbundle_clienteproveedor_tipoDocumento').append('<option value="' + tipoAdicional.toLowerCase() + '">' + tipoAdicional + '</option>');
    $('#adif_comprasbundle_clienteproveedor_tipoDocumento').select2();
    $('#adif_comprasbundle_clienteproveedor_tipoDocumento').on('change', function () {

        if ($('#adif_comprasbundle_clienteproveedor_tipoDocumento').val() !== 'dni') {

            $('#adif_comprasbundle_clienteproveedor_DNI').parents('.form-group').addClass('hidden');

            if ($inputDNI.length > 0) {

                $inputDNI.rules("remove");
                $inputDNI.val('');
            }

            if (tipoAdicional === 'CUIT') {

                $('#adif_comprasbundle_clienteproveedor_CUIT').parents('.form-group').removeClass('hidden');

                $('#adif_comprasbundle_clienteproveedor_codigoIdentificacion').parents('.form-group').addClass('hidden');

                //rules
                $inputCDI.rules("remove");
                $inputCDI.inputmask('remove');
                $inputCDI.val('');

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

            } else {

                $('#adif_comprasbundle_clienteproveedor_codigoIdentificacion').parents('.form-group').removeClass('hidden');
                $('#adif_comprasbundle_clienteproveedor_CUIT').parents('.form-group').addClass('hidden');

                //rules
                $inputCUIT.rules("remove");
                $inputCUIT.inputmask('remove');
                $inputCUIT.val('');

                $inputCDI.rules('add', {
					required: true,
					maxlength: 15
				});

//                $inputCDI.inputmask({
//                    mask: "99-99999999-9",
//                    placeholder: "_"
//                });

            }
        } else {

            $('#adif_comprasbundle_clienteproveedor_DNI').parents('.form-group').removeClass('hidden');

            if ($inputDNI.length > 0) {

                $inputDNI.rules('add', {
                    required: true
                });
            }

            if (tipoAdicional === 'CUIT') {
                $('#adif_comprasbundle_clienteproveedor_CUIT').parents('.form-group').addClass('hidden');
            } else {
                $('#adif_comprasbundle_clienteproveedor_codigoIdentificacion').parents('.form-group').addClass('hidden');
            }

            $inputCDI.rules("remove");
            $inputCDI.inputmask('remove');
            $inputCDI.val('');
            $inputCUIT.rules("remove");
            $inputCUIT.inputmask('remove');
            $inputCUIT.val('');
        }

        $('form[name=adif_comprasbundle_clienteproveedor]').submit();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initValidaciones() {

    // Validacion del Formulario     
    $('form[name=adif_comprasbundle_clienteproveedor]').validate();

    if ($('#adif_comprasbundle_clienteproveedor_tipoDocumento').length > 0) {

        $('#adif_comprasbundle_clienteproveedor_tipoDocumento').rules('add', {
            required: true
        });
    }

    // Validacion CUIT
    $inputCUIT.rules('add', {
        cuil: true,
        messages: {
            cuil: "Formato de CUIT incorrecto."
        }
    });

    $inputCUIT.inputmask({
        mask: "99-99999999-9",
        placeholder: "_"
    });

    initTipoDocumento('CUIT');

    $('#adif_comprasbundle_clienteproveedor_tipoDocumento').trigger('change');

}