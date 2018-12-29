/* global __AJAX_PATH__ */

/**
 *
 */
jQuery(document).ready(function () {

    initSelects();

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
