var formulario = $('form[name=adif_contablebundle_movimientobancario]');

$(document).ready(function() {
    formulario.validate({
        rules: {
            'adif_contablebundle_movimientobancario[cuentaOrigen]': {
                required: true,
            },
            'adif_contablebundle_movimientobancario[monto]': {
                required: true,
            },
            'adif_contablebundle_movimientobancario[cuentaDestino]': {
                required: true,
            }
        }
    });

    initSubmitButton();

});


/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_movimientobancario_submit').on('click', function(e) {

        if (formulario.valid()) {

            e.preventDefault();
            if (validarTransferencia()) {
                show_dialog({
                    titulo: 'Confirmación de movimiento bancario',
                    contenido: '<div>Se transferirán $' + $('#adif_contablebundle_movimientobancario_monto').val() + ' de la cuenta <b>' + $("#adif_contablebundle_movimientobancario_cuentaOrigen option:selected").html() + '</b> a la cuenta <b>' + $("#adif_contablebundle_movimientobancario_cuentaDestino option:selected").html() + '</b>.</div>',
                    callbackCancel: function() {
                    },
                    callbackSuccess: function() {
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

    if ($('#adif_contablebundle_movimientobancario_cuentaOrigen').val() == $('#adif_contablebundle_movimientobancario_cuentaDestino').val()) {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Para realizar una transferencia debe seleccionar cuentas bancarias diferentes."
        });
        show_alert(options);
        return false;
    }

    return true;

}