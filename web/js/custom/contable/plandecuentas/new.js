
var $collectionHolder;
/**
 * 
 * @returns {undefined}
 */
function updateSegmentoDeleteLink() {

    $(".eliminar-segmento-link").each(function() {

        $(this).off("click").on('click', function(e) {

            e.preventDefault();

            var segmento = $(this).closest('.row');

            show_confirm({
                msg: 'Desea eliminar el segmento?',
                callbackOK: function() {
                    segmento.hide('slow', function() {
                        segmento.remove();
                        updatePosiciones();
                    });
                }
            });

            e.stopPropagation();

        });
    });
}

function updatePosiciones() {
    $('input[id ^=adif_contablebundle_plandecuentas_segmentos][id $=posicion]').each(function(index) {
        $(this).val(index + 1);
    });
}

/**
 * 
 * @param {type} $collectionHolder
 * @returns {addSegmentoForm}
 */
function addSegmentoForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var segmentoForm = prototype.replace(/__segmento__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.agregar-segmento-link').closest('.row').before(segmentoForm);

    /* @see web/js/config/form.js */
    initChecksYRadios();

    updatePosiciones();

    updateSegmentoDeleteLink();
}

/**
 * 
 * @returns {undefined}
 */
function addValidaciones() {

    $.validator.addMethod("separador", function(value, element) {
        return this.optional(element) || /^[+_\-|;,.\/]$/.test(value);
    }, "Por favor, ingrese un separador válido.");

    $('#form-plandecuentas').validate();

    $.validator.addClassRules("longitud", {
        required: true,
        digits: true,
        min: 1,
        max: 5
    });

    $.validator.addClassRules("separador", {
        separador: true,
        maxlength: 1
    });
}

/**
 * 
 */
jQuery(document).ready(function() {

    updateSegmentoDeleteLink();

    $collectionHolder = $('div.segmentos');

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $('.agregar-segmento-link').on('click', function(e) {
        e.preventDefault();
        addSegmentoForm($collectionHolder);
    });

    addValidaciones();
});
