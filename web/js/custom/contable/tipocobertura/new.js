var $formularioDocumentoFinanciero = $('form[name="adif_contablebundle_tipocobertura"]');

$(document).ready(function () {

    initSubmitButton();

});

function initSubmitButton() {

    // Handler para el boton "Guardar"

    $('#adif_contablebundle_tipocobertura_submit').on('click', function (e) {

        if ($formularioDocumentoFinanciero.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el tipo de cobertura?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'save'};

                        $formularioDocumentoFinanciero.addHiddenInputData(json);
                        $formularioDocumentoFinanciero.submit();
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

    if( $('#adif_contablebundle_tipocobertura_nombre').val().length < 5 ) {
      formularioValido = false;
    }

    return formularioValido;
}