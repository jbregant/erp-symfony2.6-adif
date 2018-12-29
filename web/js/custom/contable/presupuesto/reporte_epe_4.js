
var urlEPEAccion = __AJAX_PATH__ + '#';

$(document).ready(function () {

    initDatepickerInputs();

    initFiltroButton();

    actualizarTabla(null);
});

/**
 * 
 * @param {type} conceptos
 * @returns {undefined}
 */
function actualizarTabla(conceptos) {

    updateCaptionTitle();

    initExportCustom($('#epe_4_table'));

}