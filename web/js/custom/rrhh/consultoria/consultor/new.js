
var isEdit = $('[name=_method]').length > 0;

var $inputExento = $('input[id ^= adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_exento]');

var $selectCondicionIngresosBrutos = $('select[id ^="adif_recursoshumanosbundle"][id $="condicionIngresosBrutos"]');

var $inputPorcentajeCABA = $('input[id ^="adif_recursoshumanosbundle"][id $="convenioMultilateralIngresosBrutos_porcentajeAplicacionCABA"]');

var $selectTipoPago = $('select[id ^="adif_recursoshumanosbundle"][id $="tipoPago"]');

var $selectBanco = $('select[id ^="adif_recursoshumanosbundle"][id $="cuenta_idBanco"]');

var $selectTipoCuenta = $('select[id ^="adif_recursoshumanosbundle"][id $="cuenta_idTipoCuenta"]');

var $inputCBU = $('input[id ^="adif_recursoshumanosbundle"][id $="cuenta_cbu"]');

var $inputCUIT = $('input[id ^="adif_recursoshumanosbundle"][id $="CUIT"]');

var $selectProvinciaDomicilioComercial = $('#adif_recursoshumanosbundle_consultoria_consultor_domicilioComercial_idProvincia');
var $selectLocalidadDomicilioComercial = $('#adif_recursoshumanosbundle_consultoria_consultor_domicilioComercial_localidad');

var proveedorProvinciaDomicilioComercial = $('#consultor_datos_domicilio > [name=consultor_provincia][data-tipo-domicilio="comercial"]');
var proveedorLocalidadDomicilioComercial = $('#consultor_datos_domicilio > [name=consultor_localidad][data-tipo-domicilio="comercial"]');

var editLocalidadDomicilioComercial = false;

var $selectProvinciaDomicilioFiscal = $('#adif_recursoshumanosbundle_consultoria_consultor_domicilioFiscal_idProvincia');
var $selectLocalidadDomicilioFiscal = $('#adif_recursoshumanosbundle_consultoria_consultor_domicilioFiscal_localidad');

var proveedorProvinciaDomicilioFiscal = $('#consultor_datos_domicilio > [name=consultor_provincia][data-tipo-domicilio="fiscal"]');
var proveedorLocalidadDomicilioFiscal = $('#consultor_datos_domicilio > [name=consultor_localidad][data-tipo-domicilio="fiscal"]');

var editLocalidadDomicilioFiscal = false;

var $domicilioComercialCalle = $('input[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioComercial_calle"]');
var $domicilioComercialNumero = $('input[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioComercial_numero"]');
var $domicilioComercialPiso = $('input[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioComercial_piso"]');
var $domicilioComercialDepto = $('input[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioComercial_depto"]');
var $domicilioComercialProvincia = $('select[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioComercial_idProvincia"]');
var $domicilioComercialLocalidad = $('select[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioComercial_localidad"]');

var $domicilioLegalCalle = $('input[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioFiscal_calle"]');
var $domicilioLegalNumero = $('input[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioFiscal_numero"]');
var $domicilioLegalPiso = $('input[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioFiscal_piso"]');
var $domicilioLegalDepto = $('input[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioFiscal_depto"]');
var $domicilioLegalProvincia = $('select[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioFiscal_idProvincia"]');
var $domicilioLegalLocalidad = $('select[id ^= "adif_recursoshumanosbundle_"][id $= "_domicilioFiscal_localidad"]');

var collectionHolderArchivos;
var collectionHolderCAI;

/**
 * 
 */
jQuery(document).ready(function () {

    initValidate();

    updateDeleteLinks($(".prototype-link-remove-archivo"));
    updateDeleteLinks($(".prototype-link-remove-cai"));

    initDomicilioSelects();

    if (!isEdit) {
        initChainedDomicilios();
    }

    initArchivosForm();

    initCAIForm();

    initFormulariosCertificadoExencion();

    initConvenioMultilateralHandler();

    initDatosComercialesHandler();

    initDatosImpositivosHandler();

    configPreSubmit();
        
    initInputExento();

    checkObservacionExentoIVA();
});


/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $('form[name=adif_recursoshumanosbundle_consultoria_consultor]').validate();

    // Validacion CBU
    $('#adif_recursoshumanosbundle_consultoria_consultor_cuenta_cbu').rules('add', {
        cbu: true
    });

    $('#adif_recursoshumanosbundle_consultoria_consultor_cuenta_cbu')
            .inputmask({mask: "9", repeat: 22, placeholder: ""});

    // Validacion CUIT
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
}

/**
 * 
 * @returns {undefined}
 */
function initDomicilioSelects() {

    // INIT Domicilio Comercial
    if (isEdit && !editLocalidadDomicilioComercial) {
        $selectProvinciaDomicilioComercial.val(proveedorProvinciaDomicilioComercial.val());
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

                if (isEdit && !editLocalidadDomicilioComercial) {
                    editLocalidadDomicilioComercial = true;
                    $selectLocalidadDomicilioComercial.val(proveedorLocalidadDomicilioComercial.val());
                }
                else {
                    $selectLocalidadDomicilioComercial.val($selectLocalidadDomicilioComercial.find('option:first').val());
                }

                $selectLocalidadDomicilioComercial.select2();
            }
        });
    }).trigger('change');


    // INIT Domicilio Fiscal
    if (isEdit && !editLocalidadDomicilioFiscal) {
        $selectProvinciaDomicilioFiscal.val(proveedorProvinciaDomicilioFiscal.val());
    }

    $selectProvinciaDomicilioFiscal.change(function () {

        var data = {
            id_provincia: $(this).val()
        };

        resetSelect($selectLocalidadDomicilioFiscal);

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'localidades/lista_localidades',
            data: data,
            success: function (data) {

                $selectLocalidadDomicilioFiscal.prop('disabled', false);

                for (var i = 0, total = data.length; i < total; i++) {
                    $selectLocalidadDomicilioFiscal
                            .append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                }

                if (isEdit && !editLocalidadDomicilioFiscal) {
                    editLocalidadDomicilioFiscal = true;
                    $selectLocalidadDomicilioFiscal.val(proveedorLocalidadDomicilioFiscal.val());
                }
                else {
                    $selectLocalidadDomicilioFiscal.val($selectLocalidadDomicilioFiscal.find('option:first').val());
                }

                $selectLocalidadDomicilioFiscal.select2();
            }
        });
    }).trigger('change');
}


/**
 * 
 * @returns {undefined}
 */
function initChainedDomicilios() {
    $domicilioComercialCalle.keyup(function () {
        $domicilioLegalCalle.val(this.value);
    });

    $domicilioComercialNumero.keyup(function () {
        $domicilioLegalNumero.val(this.value);
    });
    $domicilioComercialPiso.keyup(function () {
        $domicilioLegalPiso.val(this.value);
    });

    $domicilioComercialDepto.keyup(function () {
        $domicilioLegalDepto.val(this.value);
    });

    $domicilioComercialProvincia.change(function () {
        $domicilioLegalProvincia.val(this.value).select2().change();
    });

    $domicilioComercialLocalidad.change(function () {
        $domicilioLegalLocalidad.val(this.value).select2().change();
    });
}


/**
 * 
 * @returns {undefined}
 */
function initFormulariosCertificadoExencion() {

    $inputExento.each(function () {

        var certificadoExencionContent = $(this).parents('fieldset').find('.certificado-exencion-content');

        var certificadoExencionForm = certificadoExencionContent.html();

        var option_iva = $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIVA > option:selected').text();

        if (!$(this).is(':checked') || ($(this).is(':checked') && certificadoExencionContent.data('esiva') == '1' && option_iva == ivaExento)) {
            certificadoExencionForm = certificadoExencionContent.hide().detach();
        }

        $(this).on('switch-change', function () {
            if ($(this).is(':checked')) {

                // Seteo pasible retencion a "false" y lo deshabilito
                $(this).closest('.row')
                        .find('input[id ^= adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencion]')
                        .bootstrapSwitch('setState', false)
                        .closest('div.has-switch')
                        .block(
                                {
                                    message: null,
                                    overlayCSS: {
                                        backgroundColor: 'black',
                                        opacity: 0.05,
                                        cursor: 'not-allowed'}
                                }
                        );

                var option_iva = $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIVA > option:selected')
                        .text();

                if (certificadoExencionContent.data('esiva') == '1' && option_iva == ivaExento) {
                    certificadoExencionContent.hide(500, function () {
                        certificadoExencionForm = certificadoExencionContent.detach();
                    });

                    $('.row-observacion-exento-iva').removeClass('hidden');

                }
                else {

                    $('.row-observacion-exento-iva').addClass('hidden');

                    $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_observacionExentoIVA')
                            .val(null);

                    $(certificadoExencionForm).appendTo($(this).parents('fieldset'))
                            .show(500);

                    initDatepicker($(this).parents('fieldset')
                            .find('.datepicker'));
                }
            }
            else {
                // Habilito pasible retencion
                $(this).closest('.row')
                        .find('input[id ^= adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencion]')
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
 * @returns {undefined}  */
function initConvenioMultilateralHandler() {

    var validacionCondicion = function (condicion) {
        if (condicion === convenioMultilateral) {
            $('.convenio-multilateral-data').show(500);
            $inputPorcentajeCABA.rules('add', {
                required: true
            });
        } else {
            $('.convenio-multilateral-data').hide(500);
            $inputPorcentajeCABA.rules('add', {
                required: false
            });
        }
    };

    validacionCondicion($selectCondicionIngresosBrutos.find('option:selected').text());

    $selectCondicionIngresosBrutos.change(function () {
        var condicion = $(this).find('option:selected').text();
        validacionCondicion(condicion);
    });
}

/**
 * 
 * @returns {undefined}
 */
function initDatosComercialesHandler() {

    var validacionCondicion = function (tipoPago) {
        if (tipoPago === tipoPagoTransferenciaBancaria || tipoPago === tipoPagoConciliacionBancaria) {
            $('.row-datos-bancarios').show(500);

            $selectBanco.rules('add', {
                required: true
            });

            $selectTipoCuenta.rules('add', {
                required: true
            });

            $inputCBU.rules('add', {
                required: true
            });
        } else {
            $('.row-datos-bancarios').hide(500);

            $selectBanco.rules('add', {
                required: false
            });

            $selectTipoCuenta.rules('add', {
                required: false
            });

            $inputCBU.rules('add', {
                required: false
            });

            $selectBanco.select2("val", "");
            $selectTipoCuenta.select2("val", "");
            $inputCBU.val(null);

            $selectBanco.keyup();
            $selectTipoCuenta.keyup();
            $inputCBU.keyup();
        }
    };

    validacionCondicion($selectTipoPago.find('option:selected').text());

    $selectTipoPago.change(function () {
        var tipoPago = $(this).find('option:selected').text();
        validacionCondicion(tipoPago);
    });
}

/**
 * 
 * @returns {undefined}
 */
function initDatosImpositivosHandler() {

    // Elimino "Responsable Monotributo" del select de IIBB
    var $selectIIBB = $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIngresosBrutos');

    var $valIIBB = $selectIIBB
            .find('option')
            .filter(function () {
                return ($(this).html().indexOf(responsableMonotributo) >= 0);
            }).val();

    $selectIIBB
            .find('option[value="' + $valIIBB + '"]')
            .remove();


    $(document).on('change', '#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIVA', function () {
        validarIVA();
        $inputExento.trigger('switch-change');
    });

    $(document).on('change', '#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionGanancias', function () {
        validarGanancias();
    });

    $(document).on('change', '#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionSUSS', function () {
        validarSUSS();
    });

    $(document).on('change', '#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIngresosBrutos', function () {
        validarIIBB();
    });

}

/**
 * 
 * @returns {undefined}
 */
function initArchivosForm() {

    collectionHolderArchivos = $('div.prototype-archivos');
    collectionHolderArchivos.data('index', collectionHolderArchivos.find(':input').length);

    $('.prototype-link-add-archivo').on('click', function (e) {
        e.preventDefault();

        addArchivosForm(collectionHolderArchivos);

        initFileInput();
    });
}


/**
 * 
 * @param {type} $collectionHolder
 * @returns {addArchivosForm}
 */
function addArchivosForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var archivosForm = prototype.replace(/__adjunto__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-archivo').closest('.row').before(archivosForm);

    var $archivosDeleteLink = $(".prototype-link-remove-archivo");

    updateDeleteLinks($archivosDeleteLink);
}

/**
 * 
 * @returns {undefined}
 */
function initCAIForm() {

    collectionHolderCAI = $('div.prototype-cai');

    collectionHolderCAI.data('index', collectionHolderCAI.find(':input').length);

    $('.prototype-link-add-cai').on('click', function (e) {
        e.preventDefault();

        addCAIForm(collectionHolderCAI);

        setCAIMask();
    });

    setCAIMask();
}

/**
 * 
 * @param {type} $collectionHolder
 * @returns {addCAIForm}
 */
function addCAIForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var caiForm = prototype.replace(/__cai_consultor__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-cai').closest('.row').before(caiForm);

    initDatepickers(caiForm);

    var $caiDeleteLink = $(".prototype-link-remove-cai");

    updateDeleteLinks($caiDeleteLink);
}

/**
 * 
 * @returns {undefined}
 */
function setCAIMask() {
    $('.cai-punto-venta').inputmask({
        mask: "9999",
        placeholder: "0",
        numericInput: true
    });

    $('.cai-numero').inputmask({
        mask: "9",
        repeat: 14,
        placeholder: "0",
        numericInput: true
    });
}

/**
 * 
 * @returns {undefined}
 */
function validarRetenciones() {

    // IVA
    validarIVA();

    // Ganancias
    validarGanancias();

    // SUSS
    validarSUSS();

    // IIBB
    validarIIBB();
}

/**
 * 
 * @returns {undefined}
 */
function validarIVA() {

    var option_iva = $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIVA > option:selected').text();
    var estado_iva = true;

    if (option_iva != inscripto) {

        estado_iva = false;

        if (option_iva == responsableMonotributo) {

            // Si es monotributo en IVA, también lo es en ganancias y no es pasible de retención
            var valMonotributo = $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionGanancias option').filter(function () {
                return $(this).html() == responsableMonotributo;
            }).val();

            $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionGanancias').select2("val", valMonotributo);
            $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionGanancias').trigger('change');

            $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionGanancias').bootstrapSwitch('setState', false);
        }
    }

    if (option_iva != responsableMonotributo) {
        $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_exentoIVA').bootstrapSwitch('setState', !estado_iva);
    }
    else {
        $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_exentoIVA').bootstrapSwitch('setState', false);
    }

    if (option_iva == ivaExento) {
        $('.row-observacion-exento-iva').removeClass('hidden');
    }
    else {
        $('.row-observacion-exento-iva').addClass('hidden');

        $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_observacionExentoIVA')
                .val(null);
    }

    $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionIVA').bootstrapSwitch('setState', estado_iva);
    $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionIVA').attr('readonly', !estado_iva);
}

/**
 * 
 * @returns {undefined}
 */
function validarGanancias() {

    var option_ganancias = $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionGanancias > option:selected').text();
    var estado_ganancias = true;

    if (option_ganancias != inscripto) {

        estado_ganancias = false;
        if (option_ganancias == responsableMonotributo) {

            // Si es monotributo en ganancias, también lo es en IVA y no es pasible de retención
            var valMonotributo = $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIVA option').filter(function () {
                return $(this).html() == responsableMonotributo;
            }).val();

            $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIVA').select2("val", valMonotributo);

            $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionIVA').bootstrapSwitch('setState', false);
        }
    }

    if (option_ganancias != responsableMonotributo) {
        $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_exentoGanancias').bootstrapSwitch('setState', !estado_ganancias);
    }
    else {
        $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_exentoGanancias').bootstrapSwitch('setState', false);
    }

    $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionGanancias').bootstrapSwitch('setState', estado_ganancias);
    $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionGanancias').attr('readonly', !estado_ganancias);
}

/**
 * 
 * @returns {undefined}
 */
function validarSUSS() {

    var option_suss = $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionSUSS > option:selected').text();
    var estado_suss = true;

    if (option_suss != inscripto) {
        estado_suss = false;
    }

    $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_exentoSUSS').bootstrapSwitch('setState', !estado_suss);

    $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionSUSS').bootstrapSwitch('setState', estado_suss);
    $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionSUSS').attr('readonly', !estado_suss);
}

/**
 * 
 * @returns {undefined}
 */
function validarIIBB() {
    var option_iibb = $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIngresosBrutos > option:selected').text();
    var estado_iibb = true;

    if (option_iibb == sujetoNoCategorizado || option_iibb == consumidorFinal) {
        estado_iibb = false;
    }

    $('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_exentoIngresosBrutos').bootstrapSwitch('setState', !estado_iibb);

    $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionIngresosBrutos').bootstrapSwitch('setState', estado_iibb);
    $('#adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencionIngresosBrutos').attr('readonly', !estado_iibb);
}

/**
 * 
 * @returns {undefined}
 */
function configPreSubmit() {

    $('#adif_recursoshumanosbundle_consultoria_consultor_submit').on('click', function (e) {

        if ($('form[name=adif_recursoshumanosbundle_consultoria_consultor]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el consultor?',
                callbackOK: function () {

                    if (validForm()) {

                        $('form[name=adif_recursoshumanosbundle_consultoria_consultor]').submit();
                    }
                }
            });

            e.stopPropagation();

            return false;
        }

        checkErrors();

        return false;

    });
}

/**
 * 
 * @returns {Boolean|undefined}
 */
function validForm() {

    var formularioValido = true;

    if (!validateDatosComerciales($('form[name=adif_recursoshumanosbundle_consultoria_consultor]'))) {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Los datos comerciales del consultor no fueron cargados correctamente."
        });

        show_alert(options);

        formularioValido = false;
    }

    return formularioValido;
}


/**
 * Valido que se hayan completado correctamente los Datos Impositivos
 * 
 * @param {type} formulario
 * @returns {undefined}
 */
function validateDatosComerciales(formulario) {

    var selectBanco = $('#adif_recursoshumanosbundle_consultoria_consultor_cuenta_idBanco');
    var selectTipoCuenta = $('#adif_recursoshumanosbundle_consultoria_consultor_cuenta_idTipoCuenta');
    var inputCBU = $('#adif_recursoshumanosbundle_consultoria_consultor_cuenta_cbu');

    // Si completó algún dato de la Cuenta
    if (selectBanco.val() || selectTipoCuenta.val() || inputCBU.val()) {

        if (!selectBanco.val() || !selectTipoCuenta.val() || !inputCBU.val()) {

            selectBanco.attr('required', true);
            selectTipoCuenta.attr('required', true);
            inputCBU.attr('required', true);

            formulario.submit();

            return false;
        }
    }
    else {
        selectBanco.attr('required', false);
        selectBanco.closest('.form-group').removeClass('has-error');

        selectTipoCuenta.attr('required', false);
        selectTipoCuenta.closest('.form-group').removeClass('has-error');

        inputCBU.attr('required', false);
        inputCBU.closest('.form-group').removeClass('has-error');
    }

    return true;
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
        }
        else {

            $li.removeClass('has-error');

            $tagA.find('i').remove();
        }
    });
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
function initInputExento() {

    $inputExento.each(function () {

        changeInputExento($(this));
    });
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
                .find('input[id ^= adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencion]')
                .bootstrapSwitch('setState', false)
                .closest('div.has-switch')
                .block(
                        {
                            message: null,
                            overlayCSS: {
                                backgroundColor: 'black',
                                opacity: 0.05,
                                cursor: 'not-allowed'}
                        }
                );

    }
    else {

        // Habilito pasible retencion
        $inputExento.closest('.row')
                .find('input[id ^= adif_recursoshumanosbundle_consultoria_consultor_pasibleRetencion]')
                .closest('div.has-switch')
                .unblock();

    }
}

/**
 * 
 * @returns {undefined}
 */
function checkObservacionExentoIVA() {

    if ($('#adif_recursoshumanosbundle_consultoria_consultor_datosImpositivos_condicionIVA > option:selected').text() == ivaExento) {

        $('.row-observacion-exento-iva').removeClass('hidden');
    }
}