

jQuery(document).ready(function () {

    initReadOnly();

});

/**
 * 
 * @returns {undefined}
 */
function initReadOnly() {

    if ($('#adif_contablebundle_cheque_chequera').length > 0) {

        $('#adif_contablebundle_cheque_chequera').select2('readonly', true);
    }
}