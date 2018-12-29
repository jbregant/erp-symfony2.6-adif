
var formulario = $('form[name=adif_contablebundle_movimientoministerial]');

$(document).ready(function () {

    formulario.validate({
        rules: {
            'adif_contablebundle_movimientoministerial[cuentaBancariaADIF]': {
                required: true
            },
            'adif_contablebundle_movimientoministerial[monto]': {
                required: true
            },
            'adif_contablebundle_movimientoministerial[conceptoTransaccionMinisterial]': {
                required: true
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
    $('#adif_contablebundle_movimientoministerial_submit').on('click', function (e) {
        if (formulario.valid()) {
            e.preventDefault();

            var origen = $('#adif_contablebundle_movimientoministerial_esIngreso').prop("checked")
                    ? $("#adif_contablebundle_movimientoministerial_conceptoTransaccionMinisterial option:selected").html()
                    : $("#adif_contablebundle_movimientoministerial_cuentaBancariaADIF option:selected").html();

            var destino = $('#adif_contablebundle_movimientoministerial_esIngreso').prop("checked")
                    ? $("#adif_contablebundle_movimientoministerial_cuentaBancariaADIF option:selected").html()
                    : $("#adif_contablebundle_movimientoministerial_conceptoTransaccionMinisterial option:selected").html();

            var contenido = '<div>Se transferirán $' + $('#adif_contablebundle_movimientoministerial_monto').val()
                    + ' de <b>' + origen + '</b> a <b>' + destino + '</b>.</div>';

            show_dialog({
                titulo: 'Confirmaci&oacute;n de movimiento ministerial',
                contenido: contenido,
                callbackCancel: function () {
                },
                callbackSuccess: function () {
                    formulario.submit();
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;
    });
}