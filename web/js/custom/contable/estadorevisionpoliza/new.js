var $formularioEstadoRevisionPoliza = $('form[name="adif_contablebundle_estadorevisionpoliza"]');

$(document).ready(function () {

    initSubmitButton();

});

function initSubmitButton() {

    // Handler para el boton "Guardar"

    $('#adif_contablebundle_estadorevisionpoliza_submit').on('click', function (e) {

        if ($formularioEstadoRevisionPoliza.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el estado de revisi&oacute;n de p&oacute;liza?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'save'};

                        $formularioEstadoRevisionPoliza.addHiddenInputData(json);
                        $formularioEstadoRevisionPoliza.submit();
                    } else {
                      show_alert({
                          msg: 'El formulario no se ha completado correctamente'
                      });
                    }
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;
    });
}

function validForm() {

    var formularioValido = true;

    if( $('#adif_contablebundle_estadorevisionpoliza_denominacion').val().length < 5 ) {
      formularioValido = false;
    }

    return formularioValido;
}