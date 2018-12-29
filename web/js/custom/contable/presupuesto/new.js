
var isEdit = $('[name=_method]').length > 0;

var total_corrientes = 0;
var total_capital = 0;
var total_financiamiento = 0;
var superavit = 0;
var deficit = 0;
var formulario = $('form[name=adif_contablebundle_presupuesto]');

$(document).ready(function () {
    formulario.validate({
        rules: {
            'adif_contablebundle_presupuesto[cuentasPresupuestarias][montoInicial]': {
                required: true
            },
            'adif_contablebundle_presupuesto[ejercicioContable]': {
                required: true
            }
        }
    });

    initSubmitButton();

    initCorrientesInput();

    initCapitalInput();

    initFinanciamientoInput();

    initEdit();

});


/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_presupuesto_submit').on('click', function (e) {

        if (formulario.valid()) {

            e.preventDefault();
            if (validarTotal()) {
                show_confirm({
                    msg: '¿Desea guardar el presupuesto?',
                    callbackOK: function () {
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
function validarTotal() {

    if (total_financiamiento == 0) {

        return true;
    }
    else {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "La diferencia de las fuentes y aplicaciones financieras debe ser igual a cero."
        });
        show_alert(options);
        return false;
    }

}

/**
 * 
 * @returns {undefined}
 */
function initEdit() {

    if (isEdit) {

        $('#adif_contablebundle_presupuesto_ejercicioContable')
                .select2('readonly', true);

        $('input[id $= _montoInicial]').each(function () {
            $(this).keyup();
        });
    }
}