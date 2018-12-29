var formulario = $('form[name=adif_contablebundle_movimientopresupuestario]');

$(document).ready(function () {

    initValidate();

    initTipoOperacion();

    initTipoMovimientoPresupuestarioHandler();

    initSubmitButton();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    formulario.validate({
        rules: {
            'adif_contablebundle_movimientopresupuestario[monto]': {
                required: true
            },
            'adif_contablebundle_movimientopresupuestario[cuentaPresupuestariaDestino]': {
                required: true
            }
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initTipoOperacion() {

    $('#adif_contablebundle_movimientopresupuestario_tipoOperacion')
            .bootstrapSwitch('setState', true);

    $('#adif_contablebundle_movimientopresupuestario_tipoOperacion')
            .bootstrapSwitch('setOnLabel', 'SUMA');

    $('#adif_contablebundle_movimientopresupuestario_tipoOperacion')
            .bootstrapSwitch('setOffLabel', 'RESTA');

    $('#adif_contablebundle_movimientopresupuestario_tipoOperacion')
            .bootstrapSwitch('setOffClass', 'danger');

}

/**
 * 
 * @returns {undefined}
 */
function initTipoMovimientoPresupuestarioHandler() {

    $('#adif_contablebundle_movimientopresupuestario_tipoMovimientoPresupuestario').on('change', function () {

        if (getEsTransferencia()) {

            $('.div-tipo-operacion').hide();

            $('#adif_contablebundle_movimientopresupuestario_tipoOperacion')
                    .bootstrapSwitch('setState', true);

            $('label.cuenta-origen').text('Cuenta presupuestaria origen');
            $('label.cuenta-destino').text('Cuenta presupuestaria destino');

        }
        else {

            $('.div-tipo-operacion').show();

            // Si es un ajuste
            if (getEsAjuste()) {

                $('label.cuenta-origen').text('Cuenta destino de ajuste');
                $('label.cuenta-destino').text('Contrapartida');

            }
            // Sino, si es ampliacion
            else {

                $('label.cuenta-origen').text('Cuenta destino de ampliación');
                $('label.cuenta-destino').text('Contrapartida');

            }
        }

    }).trigger('change');

}

/**
 * 
 * @returns {undefined}
 */
function updateMontoMask() {

    var $montoInput = $('#adif_contablebundle_movimientopresupuestario_monto');

    // Si es un ajuste
    if (getEsAjuste()) {

        if ($montoInput.val() === '') {
            $montoInput.val('-');
        }
        else {
            $montoInput.val($montoInput.val() * -1);
        }
    }
    else {
        $montoInput.val($montoInput.val().replace('-', ''));
    }
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_movimientopresupuestario_submit').on('click', function (e) {

        if (formulario.valid()) {

            e.preventDefault();

            if (validarTransferencia()) {

                show_dialog({
                    titulo: 'Confirmación de movimiento presupuestario',
                    contenido: getContenido(),
                    callbackCancel: function () {
                    },
                    callbackSuccess: function () {

                        var tipoOperacion = 1;

                        if (!$('#adif_contablebundle_movimientopresupuestario_tipoOperacion').is(":checked")) {

                            tipoOperacion = -1;
                        }

                        var json = {
                            tipo_operacion: tipoOperacion
                        };

                        formulario.addHiddenInputData(json);

                        formulario.submit();
                    }
                });
            }

            e.stopPropagation();

            return false;
        }

        return false;
    });
}


/**
 * 
 * @returns {undefined}
 */
function validarTransferencia() {

    if (getEsTransferencia()) {

        if ($('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaOrigen').val() === $('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaDestino').val()) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "Para realizar una transferencia debe seleccionar cuentas presupuestarias diferentes."
            });

            show_alert(options);

            return false;
        }

        if (cuentasPresupuestarias[$('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaOrigen').val()]['suma'] !== cuentasPresupuestarias[$('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaDestino').val()]['suma']) {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "Las transferencias sólo pueden realizarse entre cuentas con igual impacto presupuestario."
            });

            show_alert(options);

            return false;
        }

        if (parseFloat($('#adif_contablebundle_movimientopresupuestario_monto').val().replace(',', '.')) > cuentasPresupuestarias[$('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaOrigen').val()]['monto']) {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "El monto indicado ($" + $('#adif_contablebundle_movimientopresupuestario_monto').val() + ") supera lo disponible en la cuenta seleccionada ($" + cuentasPresupuestarias[$('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaOrigen').val()]['monto'] + ") "
            });

            show_alert(options);

            return false;
        }
    }

    return true;

}

/**
 * 
 * @returns {undefined}
 */
function getContenido() {

    var contenido = null;

    if (getEsTransferencia()) {

        contenido = '<div>Se transferirán $'
                + $('#adif_contablebundle_movimientopresupuestario_monto').val()
                + ' de la cuenta <b>' + cuentasPresupuestarias[$('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaOrigen').val()]['nombre']
                + '</b> a la cuenta <b>' + cuentasPresupuestarias[$('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaDestino').val()]['nombre']
                + '</b>.</div>';
    }
    else {

        if (getEsAjuste()) {

            contenido = '<div>Se realizará un ajuste de $'
                    + $('#adif_contablebundle_movimientopresupuestario_monto').val()
                    + ' a la cuenta <b>' + cuentasPresupuestarias[$('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaDestino').val()]['nombre']
                    + '</b>.</div>';
        }
        else {

            contenido = '<div>Se realizará una ampliación de $'
                    + $('#adif_contablebundle_movimientopresupuestario_monto').val()
                    + ' a la cuenta <b>' + cuentasPresupuestarias[$('#adif_contablebundle_movimientopresupuestario_cuentaPresupuestariaDestino').val()]['nombre']
                    + '</b>.</div>';
        }
    }

    return contenido;
}

/**
 * 
 * @returns {Boolean}
 */
function getEsTransferencia() {

    var $selectedOption = $('#adif_contablebundle_movimientopresupuestario_tipoMovimientoPresupuestario option:selected');

    // Busco el option con el texto "Transferencia"
    return  $selectedOption.html().toLowerCase().indexOf("transferencia") >= 0;
}

/**
 * 
 * @returns {Boolean}
 */
function getEsAjuste() {

    var $selectedOption = $('#adif_contablebundle_movimientopresupuestario_tipoMovimientoPresupuestario option:selected');

    // Busco el option con el texto "Ajuste"
    return  $selectedOption.html().toLowerCase().indexOf("ajuste") >= 0;
}