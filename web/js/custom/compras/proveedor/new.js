
var isEdit = $('[name=_method]').length > 0;

var $inputCUIT = $('input[id ^="adif_comprasbundle_proveedor"][id $="CUIT"]');

var $inputCDI = $('input[id ^="adif_comprasbundle_proveedor"][id $="codigoIdentificacion"]');

var $inputExento = $('input[id ^= adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_exento]');

var $switchInputAplicaConvenioMultilateral = $('input[id ^="adif_comprasbundle_proveedor"][id $="aplicaConvenioMultilateralIngresosBrutos"]');

var $switchInputEsUTE = $('#adif_comprasbundle_proveedor_esUTE');

var $selectProvinciaDomicilioComercial = $('#adif_comprasbundle_proveedor_clienteProveedor_domicilioComercial_idProvincia');

var $selectLocalidadDomicilioComercial = $('#adif_comprasbundle_proveedor_clienteProveedor_domicilioComercial_localidad');

var proveedorProvinciaDomicilioComercial = $('#cliente_proveedor_datos_domicilio > [name=cliente_proveedor_provincia][data-tipo-domicilio="comercial"]');

var proveedorLocalidadDomicilioComercial = $('#cliente_proveedor_datos_domicilio > [name=cliente_proveedor_localidad][data-tipo-domicilio="comercial"]');

var editLocalidadDomicilioComercial = false;

var $selectProvinciaDomicilioLegal = $('#adif_comprasbundle_proveedor_clienteProveedor_domicilioLegal_idProvincia');

var $selectLocalidadDomicilioLegal = $('#adif_comprasbundle_proveedor_clienteProveedor_domicilioLegal_localidad');

var proveedorProvinciaDomicilioLegal = $('#cliente_proveedor_datos_domicilio > [name=cliente_proveedor_provincia][data-tipo-domicilio="legal"]');

var proveedorLocalidadDomicilioLegal = $('#cliente_proveedor_datos_domicilio > [name=cliente_proveedor_localidad][data-tipo-domicilio="legal"]');

var editLocalidadDomicilioLegal = false;

var collectionHolderDatoContactoProveedor;

var collectionHolderContactoProveedor;

var collectionHolderCAIProveedor;

var collectionHolderProveedorUTE;

/**
 * 
 */
jQuery(document).ready(function () {
    initValidate();

    initSelects();

    updateDeleteLinks($(".prototype-link-remove-dato-contacto"));
    updateDeleteLinks($(".prototype-link-remove-contacto-proveedor"));
    updateDeleteLinks($(".prototype-link-remove-dato-contacto-clienteproveedor"));
    updateDeleteLinks($(".prototype-link-remove-cai"));
    updateDeleteLinks($(".prototype-link-remove-proveedor-ute"));
    updateDeleteLinks($(".prototype-link-remove-archivo"));

    initReadOnlySelect();

    initDomicilioSelects();

    if (!isEdit) {
        initChainedDomicilios();
    }

    initEsUTEHandler();

    initArchivosClienteProveedorForm();

    initDatoContactoProveedorForm();

    initContactoProveedorForm();

    initContactoProveedorDatoContactoForm();

    initCAIProveedorForm();

    initProveedorUTEForm();

    initCondicionResponsableMonotributoHandler();

    initConvenioMultilateralHandler();

    initDatosComercialesHandler();

    initFormulariosCertificadoExencion();

    initRating();

    configPreSubmit();

    $('#adif_comprasbundle_proveedor_clienteProveedor_esExtranjero').on('switch-change', function () {
        initCUITCDI($(this));
    });

    $(document).on('change', '#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionIVA', function () {
        validarIVA();
        $inputExento.trigger('switch-change');
    });

    $(document).on('change', '#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionGanancias', function () {
        validarGanancias();
    });

    $(document).on('change', '#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionSUSS', function () {
        validarSUSS();
    });

    $(document).on('change', '#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionIngresosBrutos', function () {
        validarIIBB();
    });

    initInputExento();

    checkObservacionExentoIVA();
});

/**
 * 
 * @returns {undefined}
 */
function initEsUTEHandler() {

    if (!$switchInputEsUTE.is(':checked')) {
        $('.proveedor-ute-data').hide(500);
    }
    else {
        $('.proveedor-ute-data').show(500);
    }

    $switchInputEsUTE.on('switch-change', function () {

        if ($(this).is(':checked')) {

            $('.proveedor-ute-data').show(500);
        }
        else {
            $('.proveedor-ute-data').hide(500, function () {
                $('.proveedor-ute-data').find('.row-proveedor-ute').remove();
            });
        }
    });

}

/**
 * 
 * @returns {undefined}
 */
function initDatoContactoProveedorForm() {

    collectionHolderDatoContactoProveedor = $('div.prototype-dato-contacto-proveedor');

    collectionHolderDatoContactoProveedor.data('index', collectionHolderDatoContactoProveedor.find(':input').length);

    $('.prototype-link-add-dato-contacto-clienteproveedor').on('click', function (e) {
        e.preventDefault();
        addDatoContactoProveedorForm(collectionHolderDatoContactoProveedor);
        initSelects();
    });
}

/**
 * 
 * @param {type} $collectionHolder
 * @returns {addContactoProveedorForm}
 */
function addDatoContactoProveedorForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var datoContactoProveedorForm = prototype.replace(/__dato_contacto_cliente_proveedor__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-dato-contacto-clienteproveedor').closest('.row').before(datoContactoProveedorForm);

    var $datoContactoProveedorDeleteLink = $(".prototype-link-remove-dato-contacto-clienteproveedor");

    updateDeleteLinks($datoContactoProveedorDeleteLink);

    initSelects();
}

/**
 * 
 * @returns {undefined}
 */
function initContactoProveedorForm() {

    collectionHolderContactoProveedor = $('div.prototype-contacto-proveedor');

    collectionHolderContactoProveedor.data('index', collectionHolderContactoProveedor.find(':input').length);

    $('.prototype-link-add-contacto-proveedor').off().on('click', function (e) {
        e.preventDefault();
        addContactoProveedorForm(collectionHolderContactoProveedor, $(this));
        initContactoProveedorDatoContactoForm();
    });
}

/**
 * 
 * @param {type} collectionHolderContactoProveedor
 * @param {type} $addLink
 * @returns {addContactoProveedorForm}
 */
function addContactoProveedorForm(collectionHolderContactoProveedor, $addLink) {

    var prototype = collectionHolderContactoProveedor.data('prototype');

    var index = collectionHolderContactoProveedor.data('index');

    var contactoProveedorForm = prototype.replace(/__contacto_proveedor__/g, index);

    collectionHolderContactoProveedor.data('index', index + 1);

    $addLink.closest('.row').before(contactoProveedorForm);

    var $contactoProveedorDeleteLink = $(".prototype-link-remove-contacto-proveedor");

    updateDeleteLinks($contactoProveedorDeleteLink);
}


/**
 * 
 * @returns {undefined}
 */
function initContactoProveedorDatoContactoForm() {

    var collectionHolderContactoProveedorDatoContacto = $('div.prototype-dato-contacto');

    collectionHolderContactoProveedorDatoContacto.data('index', collectionHolderContactoProveedorDatoContacto.find(':input').length);

    $('.prototype-link-add-dato-contacto').off().on('click', function (e) {
        e.preventDefault();
        addContactoProveedorDatoContactoForm(collectionHolderContactoProveedorDatoContacto, $(this));
        initSelects();
    });
}


/**
 * 
 * @returns {undefined}
 */
function initCAIProveedorForm() {

    collectionHolderCAIProveedor = $('div.prototype-cai-proveedor');

    collectionHolderCAIProveedor.data('index', collectionHolderCAIProveedor.find(':input').length);

    $('.prototype-link-add-cai-proveedor').on('click', function (e) {
        e.preventDefault();

        addCAIProveedorForm(collectionHolderCAIProveedor);

        setCAIMask();
    });

    setCAIMask();
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
 * @param {type} $collectionHolder
 * @returns {addContactoProveedorForm}
 */
function addCAIProveedorForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var caiProveedorForm = prototype.replace(/__cai_proveedor__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-cai-proveedor').closest('.row').before(caiProveedorForm);

    initDatepickers(caiProveedorForm);

    var $caiProveedorDeleteLink = $(".prototype-link-remove-cai");

    updateDeleteLinks($caiProveedorDeleteLink);
}

/**
 * 
 * @returns {undefined}
 */
function initProveedorUTEForm() {

    collectionHolderProveedorUTE = $('div.prototype-proveedor-ute');

    collectionHolderProveedorUTE.data('index', collectionHolderProveedorUTE.find(':input').length);

    $('.prototype-link-add-proveedor-ute').on('click', function (e) {
        e.preventDefault();

        addProveedorUTEForm(collectionHolderProveedorUTE);

        initSelects();

        initCurrencies();
    });
}

/**
 * 
 * @param {type} $collectionHolder
 * @returns {addProveedorUTEForm}
 */
function addProveedorUTEForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var proveedorUTEForm = prototype.replace(/__proveedor_ute__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-proveedor-ute').closest('.row').before(proveedorUTEForm);

    initDatepickers(proveedorUTEForm);

    var $proveedorUTEDeleteLink = $(".prototype-link-remove-proveedor-ute");

    updateDeleteLinks($proveedorUTEDeleteLink);

    initSelects();
}

/**
 * 
 * @returns {undefined}
 */
function initFormulariosCertificadoExencion() {

    $inputExento.each(function () {
        var certificadoExencionContent = $(this).parents('fieldset').find('.certificado-exencion-content');

        var certificadoExencionForm = certificadoExencionContent.html();

        var option_iva = $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionIVA > option:selected').text();

        if (!$(this).is(':checked') ||
                ($(this).is(':checked') && certificadoExencionContent.data('esiva') == '1' && option_iva == ivaExento)
                ) {
            certificadoExencionForm = certificadoExencionContent.hide().detach();
        }

        $(this).on('switch-change', function () {
            if ($(this).is(':checked')) {
                // Seteo pasible retencion a "false" y lo deshabilito
                $(this).closest('.row')
                        .find('input[id ^= adif_comprasbundle_proveedor_pasibleRetencion]')
                        .bootstrapSwitch('setState', false)
                        .closest('div.has-switch')
                        .block({
                            message: null,
                            overlayCSS: {
                                backgroundColor: 'black',
                                opacity: 0.05,
                                cursor: 'not-allowed'
                            }
                        });

                var option_iva = $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionIVA > option:selected').text();

                if (certificadoExencionContent.data('esiva') == '1' && option_iva == ivaExento) {
                    certificadoExencionContent.hide(500, function () {
                        certificadoExencionForm = certificadoExencionContent.detach();
                    });
                } else {
                    $(certificadoExencionForm).appendTo($(this).parents('fieldset')).show(500);
                    initDatepicker($(this).parents('fieldset').find('.datepicker'));
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
 * @param {type} collectionHolder
 * @param {type} addLink
 * @returns {addContactoProveedorDatoContactoForm}
 */
function addContactoProveedorDatoContactoForm(collectionHolder, addLink) {

    var prototype = $(addLink).parents('.row').siblings('.prototype-dato-contacto').data('prototype'); // collectionHolder.data('prototype');

    var index = collectionHolder.data('index');

    var datoContactoForm = prototype.replace(/__dato_contacto__/g, index);

    collectionHolder.data('index', index + 1);

    addLink.closest('.row').before(datoContactoForm);

    var $datoContactoDeleteLink = $(".prototype-link-remove-dato-contacto");

    updateDeleteLinks($datoContactoDeleteLink);

    initSelects();
}

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $('form[name=adif_comprasbundle_proveedor]').validate();

    initCUITCDI($('#adif_comprasbundle_proveedor_clienteProveedor_esExtranjero'));

    // Validacion CBU
    $('#adif_comprasbundle_proveedor_cuenta_cbu').rules('add', {
        cbu: true
    });

    $('#adif_comprasbundle_proveedor_cuenta_cbu').inputmask({mask: "9", repeat: 22, placeholder: ""});


    //Datos generales
    $('#adif_comprasbundle_proveedor_clienteProveedor_codigo')
            .attr('required', true);
    $('label[for ="adif_comprasbundle_proveedor_clienteProveedor_codigo"]')
            .addClass('required');

    $('#adif_comprasbundle_proveedor_clienteProveedor_razonSocial')
            .attr('required', true);
    $('label[for ="adif_comprasbundle_proveedor_clienteProveedor_razonSocial"]')
            .addClass('required');

    // Domicilios
    $('input[id ^="adif_comprasbundle_proveedor_clienteProveedor_domicilio"][id $="calle"]')
            .attr('required', true);
    $('label[for ^="adif_comprasbundle_proveedor_clienteProveedor_domicilio"][for $="calle"]')
            .addClass('required');
    $('input[id ^="adif_comprasbundle_proveedor_clienteProveedor_domicilio"][id $="numero"]')
            .attr('required', true);
    $('label[for ^="adif_comprasbundle_proveedor_clienteProveedor_domicilio"][for $="numero"]')
            .addClass('required');

    //Datos Impositivos
    $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_situacionClienteProveedor').attr('required', true);
    $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_situacionClienteProveedor').parents('.form-group').find('label').addClass('required');

    //Datos Comerciales
    $('#adif_comprasbundle_proveedor_cuentaContable')
            .attr('required', true);
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
 * @returns {undefined}  */
function initReadOnlySelect() {

    $('select[id ^= adif_comprasbundle_proveedor_evaluacionProveedor_evaluacionesAspectos][id $= aspectoEvaluacion]')
            .select2('readonly', true);
}

/**
 * 
 * @returns {undefined}
 */
function initDomicilioSelects() {

    // INIT Domicilio Comercial
    if (!editLocalidadDomicilioComercial) {
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

                if (!editLocalidadDomicilioComercial) {
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


    // INIT Domicilio Legal
    if (!editLocalidadDomicilioLegal) {
        $selectProvinciaDomicilioLegal.val(proveedorProvinciaDomicilioLegal.val());
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
                    $selectLocalidadDomicilioLegal.val(proveedorLocalidadDomicilioLegal.val());
                }
                else {
                    $selectLocalidadDomicilioLegal.val($selectLocalidadDomicilioLegal.find('option:first').val());
                }

                $selectLocalidadDomicilioLegal.select2();
            }
        });
    }).trigger('change');
}

/**
 * 
 * @returns {undefined}
 */
function initRating() {

    $('.raty').each(function () {

        var inputValorAlcanzado = $(this).closest('.row').find('.valor-alcanzado');

        $(this).raty({
            path: ratyPath,
            half: true,
            halfShow: true,
            hints: [null, null, null, null, null],
            cancel: true,
            cancelPlace: 'right',
            cancelHint: 'Cancelar el rating',
            round: {down: .0},
            score: function () {
                return $(this).attr('data-score');
            },
            number: function () {
                return $(this).attr('data-number');
            },
            click: function (score) {

                var roundedScore = 0;

                if (null !== score && Math.floor(score) !== score) {

                    var truncatedNumber = Math.floor(score * 10) / 10;

                    var number = truncatedNumber.toString().split(".");

                    var value = number[0];
                    var decimal = number[1];

                    if (decimal > 0 && decimal <= 5) {
                        roundedScore = parseInt(value) + 0.5;
                    }
                    else {
                        roundedScore = Math.ceil(score.toFixed(1));
                    }
                }

                $(inputValorAlcanzado).val(roundedScore);

            },
            starOff: 'star-off-big.png',
            starHalf: 'star-half-big.png',
            starOn: 'star-on-big.png'
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function configPreSubmit() {

    $('#adif_comprasbundle_proveedor_submit').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_proveedor]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el proveedor?',
                callbackOK: function () {

                    if (validForm()) {

                        $('form[name=adif_comprasbundle_proveedor]').submit();
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

    if (!validarUTEsElegidos()) {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Los proveedores cargados a la UTE no son válidos."
        });

        show_alert(options);

        formularioValido = false;
    }
    else {
        formularioValido = validatePorcentajeUTE();
    }

    if (!validateDatosComerciales($('form[name=adif_comprasbundle_proveedor]'))) {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Los datos comerciales del proveedor no fueron cargados correctamente."
        });

        show_alert(options);

        formularioValido = false;
    }

    return formularioValido;
}

/**
 * 
 * @returns {undefined}
 */
function validatePorcentajeUTE() {

    var $isValid = true;

    if ($('#adif_comprasbundle_proveedor_esUTE').is(':checked')) {

        if (!$('.row-proveedor-ute').length) {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "Debe cargar al menos un proveedor a la UTE."
            });

            show_alert(options);

            $isValid = false;

        } else {

            var $totalPorcentajeRemuneracion = 0;

            $('.porcentaje-remuneracion').each(function () {
                $totalPorcentajeRemuneracion += parseFloat(clearCurrencyValue($(this).val()));
            });

            var $totalPorcentajeGanancia = 0;

            $('.porcentaje-ganancia').each(function () {
                $totalPorcentajeGanancia += parseFloat(clearCurrencyValue($(this).val()));
            });

            $isValid = (($totalPorcentajeRemuneracion === 100)||($totalPorcentajeRemuneracion === 0)) && ($totalPorcentajeGanancia === 100);

            if (!$isValid) {
                var options = $.extend({
                    title: 'Ha ocurrido un error',
                    msg: "Los porcentajes de la UTE deben sumar 100% o 0% para remuneraciones y 100% para ganancias."
                });

                show_alert(options);
            }
        }
    }

    return $isValid;
}

/**
 * Valido que se hayan completado correctamente los Datos Impositivos
 * 
 * @param {type} formulario
 * @returns {undefined}
 */
function validateDatosComerciales(formulario) {

    var selectBanco = $('#adif_comprasbundle_proveedor_cuenta_idBanco');
    var selectTipoCuenta = $('#adif_comprasbundle_proveedor_cuenta_idTipoCuenta');
    var inputCBU = $('#adif_comprasbundle_proveedor_cuenta_cbu');

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
function initSelects() {
    $('select.choice').each(function () {
        $(this).select2({
            allowClear: true
        });
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
 * @param {type} extranjero
 * @returns {undefined}
 */
function initCUITCDI(extranjero) {

    if ($(extranjero).is(':checked')) {
        $('#adif_comprasbundle_proveedor_clienteProveedor_CUIT').parents('.form-group').addClass('hidden');
        $('#adif_comprasbundle_proveedor_clienteProveedor_codigoIdentificacion').parents('.form-group').removeClass('hidden');

        //rules
        $inputCUIT.rules("remove");
        $inputCUIT.inputmask('remove');
        $inputCUIT.val('');

        $inputCDI.rules('add', {
			required: true,
			maxlength: 15
		});

//        $inputCDI.inputmask({
//            mask: "9999999999999",
//            placeholder: "_"
//        });

    } else {
        $('#adif_comprasbundle_proveedor_clienteProveedor_codigoIdentificacion').parents('.form-group').addClass('hidden');
        $('#adif_comprasbundle_proveedor_clienteProveedor_CUIT').parents('.form-group').removeClass('hidden');

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
    }
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value.replace('$', '').replace(/\./g, '').replace(/\,/g, '.');
}

/**
 * 
 * @returns {Boolean}
 */
function validarUTEsElegidos() {

    var uteValida = true;

    var elegidos = [];

    var cuitUTE = $('input[id ^= "adif_comprasbundle_"][id $= "_CUIT"]').val();

    $('[id^="adif_comprasbundle_proveedor_proveedoresUTE"][id $="proveedor"]').each(function (e) {

        var proveedorSeleccionado = $(this).find('option:selected').text();

        if (proveedorSeleccionado.indexOf(cuitUTE) >= 0) {
            uteValida = false;
        }

        elegidos[e] = $(this).val();

    });

    uteValida = uteValida && (elegidos.length === _.uniq(elegidos).length);

    return uteValida;
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

    var option_iva = $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionIVA > option:selected').text();

    var estado_iva = true;

    if (option_iva != inscripto) {
        estado_iva = false;

        if (option_iva == responsableMonotributo) {
            // Si es monotributo en IVA, también lo es en ganancias y no es pasible de retención
            var valMonotributo = $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionGanancias option').filter(function () {
                return $(this).html() == responsableMonotributo;
            }).val();

            $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionGanancias').select2("val", valMonotributo);
            $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionGanancias').trigger('change');

            $('#adif_comprasbundle_proveedor_pasibleRetencionGanancias').bootstrapSwitch('setState', false);
        }
    }

    if (option_iva == ivaExento) {

        $('.row-observacion-exento-iva').removeClass('hidden');
    }
    else {

        $('.row-observacion-exento-iva').addClass('hidden');

        $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_observacionExentoIVA')
                .val(null);
    }

    if (option_iva != responsableMonotributo) {
        $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_exentoIVA').bootstrapSwitch('setState', !estado_iva);
    }
    else {
        $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_exentoIVA').bootstrapSwitch('setState', false);
    }

    $('#adif_comprasbundle_proveedor_pasibleRetencionIVA').bootstrapSwitch('setState', estado_iva);
    $('#adif_comprasbundle_proveedor_pasibleRetencionIVA').attr('readonly', !estado_iva);
}

function validarGanancias() {

    var option_ganancias = $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionGanancias > option:selected').text();

    var estado_ganancias = true;

    if (option_ganancias != inscripto) {

        estado_ganancias = false;

        if (option_ganancias == responsableMonotributo) {

            // Si es monotributo en ganancias, también lo es en IVA y no es pasible de retención
            var valMonotributo = $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionIVA option').filter(function () {
                return $(this).html() == responsableMonotributo;
            }).val();

            //$('#adif_comprasbundle_proveedor_clienteProveedor_condicionIVA').off();
            $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionIVA').select2("val", valMonotributo);

            //$('#adif_comprasbundle_proveedor_clienteProveedor_condicionIVA').on('change', validarRetenciones());
            $('#adif_comprasbundle_proveedor_pasibleRetencionIVA').bootstrapSwitch('setState', false);
        }
    }

    if (option_ganancias != responsableMonotributo) {
        $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_exentoGanancias').bootstrapSwitch('setState', !estado_ganancias);
    }
    else {
        $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_exentoGanancias').bootstrapSwitch('setState', false);
    }

    $('#adif_comprasbundle_proveedor_pasibleRetencionGanancias').bootstrapSwitch('setState', estado_ganancias);
    $('#adif_comprasbundle_proveedor_pasibleRetencionGanancias').attr('readonly', !estado_ganancias);
}

function validarSUSS() {

    var option_suss = $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionSUSS > option:selected').text();

    var estado_suss = true;

    if (option_suss != inscripto) {

        estado_suss = false;

        if ($switchInputEsUTE.bootstrapSwitch('state')) {
// Si es proveedor ute
            estado_suss = true;
        }
    }

    $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_exentoSUSS').bootstrapSwitch('setState', !estado_suss);

    $('#adif_comprasbundle_proveedor_pasibleRetencionSUSS').bootstrapSwitch('setState', estado_suss);
    $('#adif_comprasbundle_proveedor_pasibleRetencionSUSS').attr('readonly', !estado_suss);
}

function validarIIBB() {

    var option_iibb = $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionIngresosBrutos > option:selected').text();
    var estado_iibb = true;

    if (option_iibb == sujetoNoCategorizado || option_iibb == consumidorFinal) {
        estado_iibb = false;
    }

    $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_exentoIngresosBrutos').bootstrapSwitch('setState', !estado_iibb);

    $('#adif_comprasbundle_proveedor_pasibleRetencionIngresosBrutos').bootstrapSwitch('setState', estado_iibb);
    $('#adif_comprasbundle_proveedor_pasibleRetencionIngresosBrutos').attr('readonly', !estado_iibb);
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
                .find('input[id ^= adif_comprasbundle_proveedor_pasibleRetencion]')
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
                .find('input[id ^= adif_comprasbundle_proveedor_pasibleRetencion]')
                .closest('div.has-switch')
                .unblock();

    }
}

/**
 * 
 * @param {type} $inputExento
 * @param {type} certificadoExencionContent
 * @param {type} certificadoExencionForm
 * @returns {undefined}
 */
function changeCertificadoExencionForm($inputExento, certificadoExencionContent, certificadoExencionForm) {

    if ($inputExento.is(':checked')) {

        $('.row-observacion-exento-iva').addClass('hidden');

        $('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_observacionExentoIVA')
                .val(null);

        $(certificadoExencionForm).appendTo($inputExento.parents('fieldset')).show(500);

        initDatepicker($inputExento.parents('fieldset').find('.datepicker'));

    }
    else {

        certificadoExencionContent.hide(500, function () {
            certificadoExencionForm = certificadoExencionContent.detach();
        });

        $('.row-observacion-exento-iva').removeClass('hidden');
    }

    return certificadoExencionForm;
}

/**
 * 
 * @returns {undefined}
 */
function checkObservacionExentoIVA() {

    if ($('#adif_comprasbundle_proveedor_clienteProveedor_datosImpositivos_condicionIVA > option:selected').text() == ivaExento) {

        $('.row-observacion-exento-iva').removeClass('hidden');
    }
}