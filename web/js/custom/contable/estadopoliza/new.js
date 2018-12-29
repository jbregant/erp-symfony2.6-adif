var $formularioEstadoPoliza = $('form[name="adif_contablebundle_estadopoliza"]');

$(document).ready(function () {

    initSubmitButton();

});

function initSubmitButton() {

    // Handler para el boton "Guardar"

    $('#adif_contablebundle_estadopoliza_submit').on('click', function (e) {

        if ($formularioEstadoPoliza.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el estado de p&oacute;liza?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'save'};

                        $formularioEstadoPoliza.addHiddenInputData(json);
                        $formularioEstadoPoliza.submit();
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

    if( $('#adif_contablebundle_estadopoliza_denominacion').val().length < 5 ) {
      formularioValido = false;
    }

    return formularioValido;
}