
var $formulario = $('form[name="adif_contablebundle_ordenpagogeneral"]');

$(document).ready(function () {

    initValidate();

    initAutocompleteProveedor();

    initSubmitButton();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $formulario.validate();
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_ordenpagogeneral_submit').on('click', function (e) {

        if ($formulario.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar la orden de pago?',
                callbackOK: function () {

                    $formulario.submit();
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
function initAutocompleteProveedor() {

    $('#adif_contablebundle_ordenpagogeneral_proveedor').autocomplete({
        source: __AJAX_PATH__ + 'proveedor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            completarProveedor(event, ui);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };
}

function completarProveedor(event, ui) {

    $('#adif_contablebundle_ordenpagogeneral_proveedor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_ordenpagogeneral_proveedor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_ordenpagogeneral_idProveedor').val(ui.item.id);
}
