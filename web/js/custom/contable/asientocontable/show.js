
/**
 * 
 */
jQuery(document).ready(function () {

    initImprimirButton();
});

/**
 * 
 * @returns {undefined}
 */
function initImprimirButton() {

    $('#adif_contablebundle_asientocontable_print').on('click', function (e) {

        $('#htmlCuadro').val($('#content-table-show').html());

        $('#form_imprimir_show').submit();

        desbloquear();
    });
}

