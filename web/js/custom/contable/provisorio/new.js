
var $collectionHolder;

/**
 * 
 */
jQuery(document).ready(function () {

    $('form[name="adif_contablebundle_provisorio"]').validate();

    configSubmitButton();

    initSelects();

    updateRenglonDeleteLink();

    $collectionHolder = $('div.renglones');

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $('.agregar-renglon-link').on('click', function (e) {
        e.preventDefault();

        addRenglonForm($collectionHolder);

        initSelects();
    });

});


/**
 * 
 * @returns {undefined}
 */
function updateRenglonDeleteLink() {

    $(".eliminar-renglon-link").each(function () {

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var renglon = $(this).closest('.row');

            show_confirm({
                msg: 'Desea eliminar el renglón?',
                callbackOK: function () {
                    renglon.hide('slow', function () {
                        renglon.remove();
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
 * @returns {addRenglonForm}
 */
function addRenglonForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var renglonForm = prototype.replace(/__renglon__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.agregar-renglon-link').closest('.row').before(renglonForm);

    initSelects();

    initCurrencies();

    initBuscarCuentaContableLink();

    updateRenglonDeleteLink();
}

/**
 * 
 * @returns {undefined}
 */
function configSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_provisorio_submit').on('click', function (e) {

        if ($('form[name=adif_contablebundle_provisorio]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el provisorio?',
                callbackOK: function () {

                    if (validForm()) {
                        $('form[name=adif_contablebundle_provisorio]').submit();
                    }
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
 * @returns {Boolean}
 */
function validForm() {

    // Si el Provisorio tiene renglones cargados
    if ($('.renglon-content').length === 0) {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe cargar al menos un renglón al provisorio."
        });

        show_alert(options);

        return false;
    }

    return true;
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