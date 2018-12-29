
var isEdit = $('[name=_method]').length > 0;

var $inputCUIT = $('input[id ^="adif_comprasbundle_cliente"][id $="CUIT"]');

var $inputCDI = $('input[id ^="adif_comprasbundle_cliente"][id $="codigoIdentificacion"]');

var $inputExento = $('input[id ^= adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_exento]');

var $switchInputAplicaConvenioMultilateral = $('input[id ^="adif_comprasbundle_cliente"][id $="aplicaConvenioMultilateralIngresosBrutos"]');

var $selectProvinciaDomicilioComercial = $('#adif_comprasbundle_cliente_clienteProveedor_domicilioComercial_idProvincia');

var $selectLocalidadDomicilioComercial = $('#adif_comprasbundle_cliente_clienteProveedor_domicilioComercial_localidad');

var clienteProvinciaDomicilioComercial = $('#cliente_proveedor_datos_domicilio > [name=cliente_proveedor_provincia][data-tipo-domicilio="comercial"]');

var clienteLocalidadDomicilioComercial = $('#cliente_proveedor_datos_domicilio > [name=cliente_proveedor_localidad][data-tipo-domicilio="comercial"]');

var editLocalidadDomicilioComercial = false;

var $selectProvinciaDomicilioLegal = $('#adif_comprasbundle_cliente_clienteProveedor_domicilioLegal_idProvincia');

var $selectLocalidadDomicilioLegal = $('#adif_comprasbundle_cliente_clienteProveedor_domicilioLegal_localidad');

var clienteProvinciaDomicilioLegal = $('#cliente_proveedor_datos_domicilio > [name=cliente_proveedor_provincia][data-tipo-domicilio="legal"]');

var clienteLocalidadDomicilioLegal = $('#cliente_proveedor_datos_domicilio > [name=cliente_proveedor_localidad][data-tipo-domicilio="legal"]');

var editLocalidadDomicilioLegal = false;

var collectionHolderDatoContactoCliente;


/**
 * 
 */
jQuery(document).ready(function () {

    initValidate();

    initSelects();

    initDomicilioSelects();

    if (!isEdit) {
        initChainedDomicilios();
    }

    updateDeleteLinks($(".prototype-link-remove-dato-contacto-clienteproveedor"));
    updateDeleteLinks($(".prototype-link-remove-archivo"));

    initArchivosClienteProveedorForm();

    initDatoContactoProveedorForm();

    initFormulariosCertificadoExencion();

    initCondicionResponsableMonotributoHandler();

    initConvenioMultilateralHandler();

    initExencionHandler();

    $('form[name=adif_comprasbundle_cliente]').submit(function () {
        checkErrors();
    });

    $('#adif_comprasbundle_cliente_clienteProveedor_esExtranjero').on('switch-change', function () {
        initCUITCDI($(this));
    });

    $(document).on('change', '#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_condicionIVA', function () {
        validarIVA();
        $inputExento.trigger('switch-change');
    });

    $(document).on('change', '#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_condicionIngresosBrutos', function () {
        validarIIBB();
    });

    if (!isEdit) {
        initPasiblePercepcionIIBB();
    }

    initInputExento();

    checkObservacionExentoIVA();
});

/**
 * 
 * @returns {undefined}
 */
function initDatoContactoProveedorForm() {

    collectionHolderDatoContactoCliente = $('div.prototype-dato-contacto-cliente');

    collectionHolderDatoContactoCliente.data('index', collectionHolderDatoContactoCliente.find(':input').length);

    $('.prototype-link-add-dato-contacto-clienteproveedor').on('click', function (e) {
        e.preventDefault();
        addDatoContactoClienteForm(collectionHolderDatoContactoCliente);
        initSelects();
    });
}

/**
 * 
 * @param {type} $collectionHolder
 * @returns {addDatoContactoClienteForm}
 */
function addDatoContactoClienteForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var datoContactoProveedorForm = prototype.replace(/__dato_contacto_cliente_proveedor__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-dato-contacto-clienteproveedor').closest('.row').before(datoContactoProveedorForm);

    var $datoContactoClienteDeleteLink = $(".prototype-link-remove-dato-contacto-clienteproveedor");

    updateDeleteLinks($datoContactoClienteDeleteLink);

    initSelects();
}

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $('form[name=adif_comprasbundle_cliente]').validate();

    // Validacion CUIT
//    $inputCUIT.rules('add', {
//        cuil: true,
//        messages: {
//            cuil: "Formato de CUIT incorrecto."
//        }
//    });

    $inputCUIT.inputmask({
        mask: "99-99999999-9",
        placeholder: "_"
    });

    //Datos generales
    $('#adif_comprasbundle_cliente_clienteProveedor_codigo')
            .attr('required', true);
    $('label[for ="adif_comprasbundle_cliente_clienteProveedor_codigo"]')
            .addClass('required');

    $('#adif_comprasbundle_cliente_clienteProveedor_razonSocial')
            .attr('required', true);
    $('label[for ="adif_comprasbundle_cliente_clienteProveedor_razonSocial"]')
            .addClass('required');

    // Domicilios
    $('input[id ^="adif_comprasbundle_cliente_clienteProveedor_domicilio"][id $="calle"]')
            .attr('required', true);
    $('label[for ^="adif_comprasbundle_cliente_clienteProveedor_domicilio"][for $="calle"]')
            .addClass('required');

    $('input[id ^="adif_comprasbundle_cliente_clienteProveedor_domicilio"][id $="numero"]')
            .attr('required', true);
    $('label[for ^="adif_comprasbundle_cliente_clienteProveedor_domicilio"][for $="numero"]')
            .addClass('required');

    //Datos Impositivos
    $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_situacionClienteProveedor').attr('required', true);
    $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_situacionClienteProveedor').parents('.form-group').find('label').addClass('required');
}

/**
 * 
 * @returns {undefined}
 */
function checkErrors() {
    $('.tab-pane').each(function () {
        var $id = $(this).prop('id');
        var $tagA = $('a[href=#' + $id + ']');
        var $li = $tagA.parent('li');

        if ($(this).has(".form-group.has-error").length) {
            $li.addClass('has-error');
            if (!$tagA.has('.fa-warning').length) {
                $tagA.append('<i class="fa fa-warning"></i>');
            }
            $tagA.click();
        } else {
            $li.removeClass('has-error');
            $tagA.find('i').remove();
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initSelects() {
    $('select.choice').each(function () {
        $(this).select2({
            allowClear: true
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initDomicilioSelects() {

    // INIT Domicilio Comercial
    if (!editLocalidadDomicilioComercial) {
        $selectProvinciaDomicilioComercial.val(clienteProvinciaDomicilioComercial.val());
    }

    $selectProvinciaDomicilioComercial.change(function () {
        var data = {
            id_provincia: $(this).val()
        };

        resetSelect($selectLocalidadDomicilioComercial);

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'localidades/lista_localidades',
            data: data,
            success: function (data) {
                $selectLocalidadDomicilioComercial.prop('disabled', false);

                for (var i = 0, total = data.length; i < total; i++) {
                    $selectLocalidadDomicilioComercial
                            .append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                }

                if (!editLocalidadDomicilioComercial) {
                    editLocalidadDomicilioComercial = true;
                    $selectLocalidadDomicilioComercial.val(clienteLocalidadDomicilioComercial.val());
                } else {
                    $selectLocalidadDomicilioComercial.val($selectLocalidadDomicilioComercial.find('option:first').val());
                }

                $selectLocalidadDomicilioComercial.select2();
            }
        });
    }).trigger('change');


    // INIT Domicilio Legal
    if (!editLocalidadDomicilioLegal) {
        $selectProvinciaDomicilioLegal.val(clienteProvinciaDomicilioLegal.val());
    }

    $selectProvinciaDomicilioLegal.change(function () {

        var data = {
            id_provincia: $(this).val()
        };

        resetSelect($selectLocalidadDomicilioLegal);

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'localidades/lista_localidades',
            data: data,
            success: function (data) {
                $selectLocalidadDomicilioLegal.prop('disabled', false);

                for (var i = 0, total = data.length; i < total; i++) {
                    $selectLocalidadDomicilioLegal
                            .append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                }

                if (!editLocalidadDomicilioLegal) {
                    editLocalidadDomicilioLegal = true;
                    $selectLocalidadDomicilioLegal.val(clienteLocalidadDomicilioLegal.val());
                } else {
                    $selectLocalidadDomicilioLegal.val($selectLocalidadDomicilioLegal.find('option:first').val());
                }

                $selectLocalidadDomicilioLegal.select2();
            }
        });
    }).trigger('change');
}

/**
 * 
 * @param {type} extranjero
 * @returns {undefined}
 */
function initCUITCDI(extranjero) {

    if ($(extranjero).is(':checked')) {
        $('#adif_comprasbundle_cliente_clienteProveedor_CUIT').parents('.form-group').addClass('hidden');
        $('#adif_comprasbundle_cliente_clienteProveedor_codigoIdentificacion').parents('.form-group').removeClass('hidden');

        //rules
        $inputCUIT.rules("remove");
        $inputCUIT.inputmask('remove');
        $inputCUIT.val('');
		
		$inputCDI.rules('add', {
			required: true,
			maxlength: 15
		});

//        $inputCDI.inputmask({
//            mask: "99-99999999-9",
//            placeholder: "_"
//        });

    } else {

        $('#adif_comprasbundle_cliente_clienteProveedor_codigoIdentificacion').parents('.form-group').addClass('hidden');
        $('#adif_comprasbundle_cliente_clienteProveedor_CUIT').parents('.form-group').removeClass('hidden');

        //rules
        $inputCUIT.rules('add', {
//            required: true,
            required: false
//            cuil: true,
//            messages: {
//                cuil: "Formato de CUIT incorrecto."
//            }
        });

        $inputCUIT.inputmask({
            mask: "99-99999999-9",
            placeholder: "_"
        });

        $inputCDI.rules("remove");
        $inputCDI.inputmask('remove');
        $inputCDI.val('');
    }
}

/**
 * 
 * @returns {undefined}
 */
function  initPasiblePercepcionIIBB() {

    var option_iibb = $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_condicionIngresosBrutos > option:selected').text();

    if (option_iibb == convenioMultilateral || option_iibb == inscripto) {
        validarIIBB();
    }

}

/**
 * 
 * @returns {undefined}
 */
function initInputExento() {

    $inputExento.each(function () {

        changeInputExento($(this));
    });
}

/**
 * 
 * @returns {undefined}
 */
function initExencionHandler() {

    $inputExento.each(function () {

        $(this).on('switch-change', function () {

            changeInputExento($(this));

        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function validarIVA() {

    var option_iva = $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_condicionIVA > option:selected').text();

    var estado_iva = true;

    if (option_iva == inscripto || option_iva == responsableMonotributo) {
        estado_iva = false;
    }

    if (option_iva == ivaExento) {

        $('.row-observacion-exento-iva').removeClass('hidden');
    }
    else {

        $('.row-observacion-exento-iva').addClass('hidden');

        $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_observacionExentoIVA')
                .val(null);
    }

    $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_exentoIVA')
            .bootstrapSwitch('setState', estado_iva);

}

/**
 * 
 * @returns {undefined}
 */
function validarIIBB() {

    var option_iibb = $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_condicionIngresosBrutos > option:selected').text();

    var estado_iibb = false;

    if (option_iibb == convenioMultilateral || option_iibb == inscripto) {
        estado_iibb = true;
    }

    $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_exentoIngresosBrutos').bootstrapSwitch('setState', !estado_iibb);

    $('#adif_comprasbundle_cliente_pasiblePercepcionIngresosBrutos').bootstrapSwitch('setState', estado_iibb);
    $('#adif_comprasbundle_cliente_pasiblePercepcionIngresosBrutos').attr('readonly', !estado_iibb);
}

/**
 * 
 * @param {type} $inputExento
 * @returns {undefined}
 */
function changeInputExento($inputExento) {
    if ($inputExento.is(':checked')) {
        // Seteo pasible retencion a "false" y lo deshabilito
        $inputExento.closest('.row')
                .find('input[id ^= adif_comprasbundle_cliente_pasiblePercepcion]')
                .bootstrapSwitch('setState', false)
                .closest('div.has-switch')
                .block({
                    message: null,
                    overlayCSS: {
                        backgroundColor: 'black',
                        opacity: 0.05,
                        cursor: 'not-allowed'}
                });
    } else {
        // Habilito pasible retencion
        $inputExento.closest('.row')
                .find('input[id ^= adif_comprasbundle_cliente_pasiblePercepcion]')
                .closest('div.has-switch')
                .unblock();
    }
}

/**
 * 
 * @param {type} idSelect
 * @returns {undefined}
 */
function resetSelect(idSelect) {
    $(idSelect).empty();
    $(idSelect).select2("val", "");
    $(idSelect).prop('disabled', true);
}


/**
 * 
 * @returns {undefined}
 */
function initFormulariosCertificadoExencion() {

    $inputExento.each(function () {

        var certificadoExencionContent = $(this).parents('fieldset').find('.certificado-exencion-content');

        var certificadoExencionForm = certificadoExencionContent.html();

        var option_iva = $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_condicionIVA > option:selected').text();

        if (!$(this).is(':checked') || ($(this).is(':checked') && certificadoExencionContent.data('esiva') == '1' && option_iva == ivaExento)) {
            certificadoExencionForm = certificadoExencionContent.hide().detach();
        }

        $(this).on('switch-change', function () {
            if ($(this).is(':checked')) {

                // Seteo pasible retencion a "false" y lo deshabilito
                $(this).closest('.row')
                        .find('input[id ^= adif_comprasbundle_cliente_pasibleRetencion]')
                        .bootstrapSwitch('setState', false)
                        .closest('div.has-switch')
                        .block({
                            message: null,
                            overlayCSS: {
                                backgroundColor: 'black',
                                opacity: 0.05,
                                cursor: 'not-allowed'}
                        });

                var option_iva = $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_condicionIVA > option:selected').text();

                if (certificadoExencionContent.data('esiva') == '1' && option_iva == ivaExento) {
                    certificadoExencionContent.hide(500, function () {
                        certificadoExencionForm = certificadoExencionContent.detach();
                    });

                    $('.row-observacion-exento-iva').removeClass('hidden');

                }
                else {

                    $('.row-observacion-exento-iva').addClass('hidden');

                    $('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_observacionExentoIVA')
                            .val(null);

                    $(certificadoExencionForm).appendTo($(this).parents('fieldset'))
                            .show(500);

                    initDatepicker($(this).parents('fieldset')
                            .find('.datepicker'));
                }
            } else {
                // Habilito pasible retencion
                $(this).closest('.row')
                        .find('input[id ^= adif_comprasbundle_proveedor_pasibleRetencion]')
                        .closest('div.has-switch')
                        .unblock();

                certificadoExencionContent.hide(500, function () {
                    certificadoExencionForm = certificadoExencionContent.detach();
                });
            }
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function checkObservacionExentoIVA() {

    if ($('#adif_comprasbundle_cliente_clienteProveedor_datosImpositivos_condicionIVA > option:selected').text() == ivaExento) {

        $('.row-observacion-exento-iva').removeClass('hidden');
    }
}