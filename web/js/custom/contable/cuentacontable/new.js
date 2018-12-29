
var isEdit = $('[name=_method]').length > 0;

var $selectCuentaPresupuestariaEconomica =
        $('#adif_contablebundle_cuentacontable_cuentaPresupuestariaEconomica');

var $selectCuentaPresupuestariaObjetoGasto =
        $('#adif_contablebundle_cuentacontable_cuentaPresupuestariaObjetoGasto');

/**
 * 
 */
jQuery(document).ready(function () {

    initValidate();

    initCodigoCuentaContable();

    initChainedSelects();

    setMasks();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {
    $('form[name=adif_contablebundle_cuentacontable]').validate({
        rules: {
            'adif_contablebundle_cuentacontable[codigoCuentaContable]': {
                required: true,
                minlength: mask.replace(/[^\d]/g, '').length
            }
        },
        messages: {
            'adif_contablebundle_cuentacontable[codigoCuentaContable]': {
                minlength: jQuery.format("Por favor, complete el código de la cuenta contable.")
            }
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initCodigoCuentaContable() {

    var codigoActual = $('#adif_contablebundle_cuentacontable_codigoCuentaContable').val();

    if (codigoActual != '') {

        $.when(updateInputCodigo($("#adif_contablebundle_cuentacontable_cuentaContablePadre").val())).done(function () {
            $('#adif_contablebundle_cuentacontable_codigoCuentaContable').val(codigoActual);
        });
    }

    $("#adif_contablebundle_cuentacontable_cuentaContablePadre").change(function () {
        updateInputCodigo($(this).val());
    });
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {
    $('#adif_contablebundle_cuentacontable_codigoCuentaContable').inputmask(mask, {
        autoUnmask: true,
        clearMaskOnLostFocus: false
    });
}


/**
 * 
 * @param {type} codigoPadre
 * @returns {unresolved}
 */
function updateInputCodigo(codigoPadre) {

    if (codigoPadre) {

        var data = {
            id: codigoPadre
        };

        return $.ajax({
            type: "POST",
            data: data,
            url: pathCodigoInicial
        }).done(function (codigoInicial) {
            cod = codigoInicial;
            fatherMask = codigoInicial.concat(mask.substr(codigoInicial.length));

            fatherMaskSegment = fatherMask.split('.');
            newMask = fatherMaskSegment[0] + '.' + fatherMaskSegment[1] + '.' + '99' + '.' + fatherMaskSegment[3] + '.' + fatherMaskSegment[4] + '.' + fatherMaskSegment[5] + '.' + fatherMaskSegment[6] + '.' + fatherMaskSegment[7];


            $('#adif_contablebundle_cuentacontable_codigoCuentaContable').inputmask(newMask, {
                clearIncomplete: true,
                clearMaskOnLostFocus: false
            });
            $('#adif_contablebundle_cuentacontable_codigoCuentaContable').val(cod);
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initChainedSelects() {

    $selectCuentaPresupuestariaObjetoGasto.select2('readonly', true);

    if (isEdit) {
        var $cuentaPresupuestariaObjetoGastoVal =
                $selectCuentaPresupuestariaObjetoGasto.val();
    }

    $selectCuentaPresupuestariaEconomica.change(function () {

        if ($(this).val()) {

            var data = {
                id_cuenta_presupuestaria_economica: $(this).val()
            };

            resetSelect($selectCuentaPresupuestariaObjetoGasto);

            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'cuentapresupuestariaobjetogasto/lista_cuentas',
                data: data,
                success: function (data) {

                    // Si se encontraron al menos una cuenta contable de objeto de gasto
                    if (data.length > 0) {
                        $selectCuentaPresupuestariaObjetoGasto.select2('readonly', false);

                        for (var i = 0, total = data.length; i < total; i++) {
                            $selectCuentaPresupuestariaObjetoGasto.append('<option value="' + data[i].id + '">' + data[i].codigo + ' - ' + data[i].denominacion + '</option>');
                        }

                        if (isEdit) {
                            $selectCuentaPresupuestariaObjetoGasto.val($cuentaPresupuestariaObjetoGastoVal);

                            if (null === $selectCuentaPresupuestariaObjetoGasto.val()) {
                                $selectCuentaPresupuestariaObjetoGasto.select2("val", "");
                            }
                        }
                        else {
                            $selectCuentaPresupuestariaObjetoGasto.val($selectCuentaPresupuestariaObjetoGasto.find('option:first').val());
                        }

                        $selectCuentaPresupuestariaObjetoGasto.prop('required', true);
                        $selectCuentaPresupuestariaObjetoGasto.select2();
                    }
                    else {
                        $selectCuentaPresupuestariaObjetoGasto.prop('required', false);
                        $selectCuentaPresupuestariaObjetoGasto.keyup();
                    }
                }
            });
        }
    }).trigger('change');
}