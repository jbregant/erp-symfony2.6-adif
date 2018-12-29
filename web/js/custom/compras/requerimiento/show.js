/**
 * 
 */
jQuery(document).ready(function() {

    initRequerimientoLinks();

});

/**
 * 
 * @returns {undefined}
 */
function initRequerimientoLinks() {

    // BOTON ANULAR REQUERIMIENTO
    $('.link-anular-requerimiento').click(function(e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea anular el requerimiento?',
            callbackOK: function() {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });

    // BOTON CORREGIR REQUERIMIENTO
    $('.link-corregir-requerimiento').click(function(e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea que el requerimiento sea corregido por el usuario?',
            callbackOK: function() {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });

    // BOTON ENVIAR REQUERIMIENTO
    $('.link-enviar-requerimiento').click(function(e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea enviar el requerimiento a aprobar contablemente?',
            callbackOK: function() {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });

    // BOTON APROBAR REQUERIMIENTO
    $('.link-aprobar-requerimiento').click(function(e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea aprobar el requerimiento?',
            callbackOK: function() {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });

    // BOTON DESAPROBAR REQUERIMIENTO
    $('.link-desaprobar-requerimiento').click(function(e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea desaprobar el requerimiento?',
            callbackOK: function() {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });
}