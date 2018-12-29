var $formularioDocumentoFinanciero = $('form[name="adif_recursoshumanosbundle_obrasocial"]');

$(document).ready(function () {

    initSubmitButton();

});

function initSubmitButton() {

    // Handler para el boton "Guardar"

    $('#adif_recursoshumanosbundle_obrasocial_submit').on('click', function (e) {

        if ($formularioDocumentoFinanciero.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el tipo de cobertura?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'save'};

                        $formularioDocumentoFinanciero.addHiddenInputData(json);
                        $formularioDocumentoFinanciero.submit();
                    }
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;
    });
}
