
var $formularioEgresoValor = $('form[name="adif_contablebundle_egresovalor_egresovalor"]');

var $inputNroDocumento = $('#adif_contablebundle_egresovalor_egresovalor_responsableEgresoValor_nroDocumento');


$(document).ready(function () {

    initValidate();

    initAutocompleteEmpleado();

    initTopes();

    actualizarTope();

});


/**
 * 
 * @returns {undefined}
 */
function initValidate() {
	
    $(document).on('change', '#adif_contablebundle_egresovalor_egresovalor_tipoEgresoValor', function () {
        //ocultarGerencia();
    });

    // Validacion del Formulario
    $formularioEgresoValor.validate();

    $inputNroDocumento.inputmask({
        mask: "99999999",
        placeholder: "_"
    });
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteEmpleado() {

    $('#adif_contablebundle_egresovalor_egresovalor_responsableEgresoValor_nombre').autocomplete({
        source: __AJAX_PATH__ + 'empleados/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            completarEmpleado(event, ui);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (DNI: " + item.dni + ")</a>")
                .appendTo(ul);
    };
}

/**
 * 
 * @param {type} event
 * @param {type} ui
 * @returns {undefined}
 */
function completarEmpleado(event, ui) {

    $('#adif_contablebundle_egresovalor_egresovalor_responsableEgresoValor_nombre').val(ui.item.razonSocial);
    $('#adif_contablebundle_egresovalor_egresovalor_responsableEgresoValor_nroDocumento').val(ui.item.dni);

    event.preventDefault();
}

/**
 * 
 * @returns {undefined}
 */
function initTopes() {
    $(document).on('change', '#adif_contablebundle_egresovalor_egresovalor_gerencia,#adif_contablebundle_egresovalor_egresovalor_tipoEgresoValor', function () {
        actualizarTope();
    });
}

/**
 * 
 * @returns {undefined}
 */
function actualizarTope() {
    if ($('#adif_contablebundle_egresovalor_egresovalor_tipoEgresoValor').val() == 1) {
        $('#adif_contablebundle_egresovalor_egresovalor_importe').prop('readonly', true);
        $('#adif_contablebundle_egresovalor_egresovalor_importe').val((topes[$('#adif_contablebundle_egresovalor_egresovalor_tipoEgresoValor').val()][$('#adif_contablebundle_egresovalor_egresovalor_gerencia').val()]).replace(/\./g, ','));
    } else {
        $('#adif_contablebundle_egresovalor_egresovalor_importe').removeProp('readonly');
        $('#adif_contablebundle_egresovalor_egresovalor_importe').val("");
    }

}

/**
 * 
 * @returns {undefined}
 */
function ocultarGerencia() {
    if ($('#adif_contablebundle_egresovalor_egresovalor_tipoEgresoValor').val() == 5) {
        $('.div_gerencia').hide();
        $('#adif_contablebundle_egresovalor_egresovalor_gerencia').val(5).select2();
    } else {
        $('.div_gerencia').show();
        $('#adif_contablebundle_egresovalor_egresovalor_gerencia').val("").select2();
    }

}