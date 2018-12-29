
$(document).ready(function () {

    initValidate();
});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $('form[name=adif_contablebundle_facturacion_conceptoventageneral]').validate();
}
