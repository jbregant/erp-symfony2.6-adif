
$(document).ready(function () {

    initValidate();

    initAnio();
});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $('form[name=adif_contablebundle_licitacioncompra]').validate();
}

/**
 * 
 * @returns {undefined}
 */
function initAnio() {

    $('#adif_contablebundle_licitacioncompra_anio')
            .parent().wrap('<div class="input-group"></div>');

    initDatepicker($('#adif_contablebundle_licitacioncompra_anio'), {
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });

    $('<span class="input-group-addon"><i class="fa fa-calendar"></i></span>')
            .insertBefore($('#adif_contablebundle_licitacioncompra_anio').parent());
}
