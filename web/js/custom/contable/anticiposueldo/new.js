$(document).ready(function() {
    $('form[name=adif_contablebundle_anticiposueldo]').validate();
    initAutocompleteEmpleado();
});

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteEmpleado() {
    $('#adif_contablebundle_anticiposueldo_empleado').autocomplete({
        source: __AJAX_PATH__ + 'empleados/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            $('#adif_contablebundle_anticiposueldo_empleado_razonSocial').val(ui.item.razonSocial);
            $('#adif_contablebundle_anticiposueldo_empleado_legajo').val(ui.item.legajo);
            $('#adif_contablebundle_anticiposueldo_idEmpleado').val(ui.item.id);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (Legajo: " + item.legajo + ")</a>")
                .appendTo(ul);
    };
}