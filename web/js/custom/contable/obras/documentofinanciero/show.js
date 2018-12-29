
/**
 * 
 */
jQuery(document).ready(function () {

    initShowAdjuntos();

});

/**
 * 
 * @returns {undefined}
 */
function initShowAdjuntos() {

    $('.link-adjunto').magnificPopup({
        delegate: 'a', // child items selector, by clicking on it popup will open
        type: 'image',
        // main options
        disableOn: 400,
        gallery: {
            // options for gallery
            enabled: true,
            tPrev: 'Anterior', // title for left button
            tNext: 'Siguiente', // title for right button
            tCounter: '%curr% de %total%' // markup of counter
        },
        image: {
            // options for image content type
            titleSrc: 'title'
        }
    });
}