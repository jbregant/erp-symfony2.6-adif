
var $formularioChequera = $('form[name="adif_contablebundle_chequera"]');

/**
 * 
 */
jQuery(document).ready(function () {

    initValidate();

    initSubmitButton();
});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $formularioChequera.validate();
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_chequera_submit').on('click', function (e) {

        if ($formularioChequera.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar la chequera?',
                callbackOK: function () {

                    if (validForm()) {
                        $formularioChequera.submit();
                    }
                }
            });

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
function validForm() {

    var numeroInicial = $('#adif_contablebundle_chequera_numeroInicial').val();
    var numeroFinal = $('#adif_contablebundle_chequera_numeroFinal').val();

    // Si la numero final de la Chequera es mayor al numero inicial
    if (parseInt(numeroFinal) >= parseInt(numeroInicial)) {

        return true;
    }
    else {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "El número final de la chequera debe ser mayor al número inicial."
        });

        show_alert(options);

        return false;
    }
}