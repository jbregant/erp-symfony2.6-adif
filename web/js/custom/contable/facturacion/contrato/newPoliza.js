var $formularioContrato = $('form[name="adif_contablebundle_facturacion_contrato"]');

var collectionHolderPoliza;

/**
 * 
 */
jQuery(document).ready(function () {
    
    $('#adif_contablebundle_facturacion_contrato_ciclosFacturacion').parent().remove();
    
    initValidate();
    updateDeleteLinks($(".prototype-link-remove-poliza"));
    initPolizaForm();
    initSubmitButton();
    initRangoPoliza();
});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {
    $.validator.addMethod("fechaPoliza", function (value, element, param) {
        var fechaInicioContrato = $('#adif_contablebundle_facturacion_contrato_fechaInicio').val();
        var fechaInicioContratoDate = getDateFromString(fechaInicioContrato);
        var fechaFinContrato = $('#adif_contablebundle_facturacion_contrato_fechaFin').val();
        var fechaFinContratoDate = getDateFromString(fechaFinContrato);
        var fechaPoliza = $(element).val();
        var fechaPolizaDate = getDateFromString(fechaPoliza);
        return  (fechaInicioContratoDate.getTime() <= fechaPolizaDate.getTime()
                && fechaPolizaDate.getTime() <= fechaFinContratoDate.getTime());
    });
}

/**
 * 
 * @returns {undefined}
 */
function initPolizaForm() {
    collectionHolderPoliza = $('div.prototype-poliza');
    collectionHolderPoliza.data('index', collectionHolderPoliza.find(':input').length);
    $('.prototype-link-add-poliza').on('click', function (e) {
        e.preventDefault();
        addPolizaForm(collectionHolderPoliza);
        initFechaPolizaValidation();
        initRangoPoliza();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initFechaPolizaValidation() {
    // Validacion del Formulario
    $formularioContrato.validate();
    $('input[id ^= "adif_contablebundle_facturacion_contrato_polizasSeguro_"][id $= "_fechaVencimiento"]').each(function (e) {
        $(this).rules('add', {
            fechaPoliza: true,
            messages: {
                fechaPoliza: "La fecha est&aacute; fuera del rango del contrato."
            }
        });
    });
}

/**
 * 
 * @param {type} $collectionHolder
 * @returns {addPolizaForm}
 */
function addPolizaForm($collectionHolder) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var polizaForm = prototype.replace(/__poliza__/g, index);
    $collectionHolder.data('index', index + 1);
    $('.prototype-link-add-poliza').closest('.row').before(polizaForm);
    initDatepickers($('.row_poliza').last());
    var $polizaDeleteLink = $(".prototype-link-remove-poliza");
    updateDeleteLinks($polizaDeleteLink);
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {
    // Handler para el boton "Guardar"
    $('#adif_contablebundle_facturacion_contrato_submit').on('click', function (e) {
        if ($formularioContrato.valid()) {
            e.preventDefault();
            show_confirm({
                msg: '¿Desea guardar modificar las p&oacute;lizas?',
                callbackOK: function () {
                    $formularioContrato.submit();
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
 * @param {type} deleteLink
 * @returns {undefined}
 */
function updateDeleteLinks(deleteLink) {
    deleteLink.each(function () {
        $(this).tooltip();
        $(this).off("click").on('click', function (e) {
            e.preventDefault();
            var deletableRow = $(this).closest('.row');
            show_confirm({
                msg: '¿Desea eliminar el registro?',
                callbackOK: function () {
                    deletableRow.hide('slow', function () {
                        deletableRow.remove();
                    });
                }
            });
            e.stopPropagation();
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRangoPoliza() {
    $('input[id ^= "adif_contablebundle_facturacion_contrato_polizasSeguro_"][id $= "_fechaVencimiento"]').each(function (e) {
        $(this).datepicker('setStartDate', getDateFromString($('#adif_contablebundle_facturacion_contrato_fechaInicio').val()));
        $(this).datepicker('setEndDate', getDateFromString($('#adif_contablebundle_facturacion_contrato_fechaFin').val()));
    });
}