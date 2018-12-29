var $collectionHolder;

jQuery(document).ready(function () {

    initValidaciones();

    initSelects();

    $collectionHolder = $('div.prototype-punto-venta-clase-contrato');

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $('.prototype-link-add-punto-venta-clase-contrato').on('click', function (e) {
        e.preventDefault();

        addTipoContratoForm($collectionHolder);

        initSelects();

        initCurrencies();

        initValidaciones();

    });

    updateTipoContratoDeleteLink();

    initSubmitHandler();

});


function initValidaciones() {

    $.validator.addMethod("maximoValido", function (value, element, param) {
        maximo = $(element).val();
        minimo = $(element).parents('.tipoContratoRow').find('.montoMinimo').val();
        return parseInt(minimo) < parseInt(maximo);
    });
    $.validator.addMethod("minimoValido", function (value, element, param) {
        minimo = $(element).val();
        maximo = $(element).parents('.tipoContratoRow').find('.montoMaximo').val();
        return parseInt(minimo) < parseInt(maximo);
    });

    $('form[name=adif_contablebundle_facturacion_puntoventa]').validate();

    $('input[id ^="adif_contablebundle_facturacion_puntoventa"][id $="montoMaximo"]').each(function () {
        $(this).rules('add', {
            maximoValido: true,
            messages: {
                maximoValido: "El monto máximo no puede ser igual, ni menor al mínimo"
            }
        });
    });

    $('input[id ^="adif_contablebundle_facturacion_puntoventa"][id $="montoMinimo"]').each(function () {
        $(this).rules('add', {
            minimoValido: true,
            messages: {
                minimoValido: "El monto mínimo no puede ser igual, ni mayor al máximo"
            }
        });
    });

}


/**
 * 
 * @returns {undefined}
 */
function updateTipoContratoDeleteLink() {

    $(".prototype-link-remove-punto-venta-clase-contrato").each(function () {

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var tipoContrato = $(this).closest('.tipoContratoRow');

            show_confirm({
                msg: 'Desea eliminar el tipo de venta?',
                callbackOK: function () {
                    tipoContrato.hide('slow', function () {
                        tipoContrato.remove();
                    });
                }
            });

            e.stopPropagation();

        });
    });
}


/**
 * 
 * @param {type} $collectionHolder
 * @returns {addTipoContratoForm}
 */
function addTipoContratoForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var conceptoForm = prototype.replace(/__punto_venta_clase_contrato__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-punto-venta-clase-contrato').closest('.row').before(conceptoForm);

    initSelects();

    updateTipoContratoDeleteLink();

}

/**
 * 
 * @returns {undefined}
 */
function initTooltip() {
    $('.prototype-link-add-punto-venta-clase-contrato').tooltip();
}

/**
 * 
 * @returns {undefined}
 */
function initSelects() {
    $('select.choice').each(function () {
        $(this).select2({
            allowClear: true
        });
    });
}


function initSubmitHandler() {
    $('#adif_contablebundle_facturacion_puntoventa_submit').click(function (e) {

        e.preventDefault();

        var formulario = $('form[name=adif_contablebundle_facturacion_puntoventa]').validate();

        var formulario_result = formulario.form();

        if (formulario_result) {

            $('form').submit();
        }

        return false;
    });
}

/**
 * 
 * @returns {Boolean}
 */
function validarTiposDeContrato() {

    if ($('.tipoContratoRow').length === 0) {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: 'Debe haber al menos un tipo de venta.'
        });

        show_alert(options);

        return false;
    }
    return true;
}