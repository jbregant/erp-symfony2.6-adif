
var options = {
    message: '<span class="bold">\n\
                <img src="' + __LOADING_IMG_PATH__ + '" style="margin-right: .3em" /> Por favor espere...\n\
            </span>',
    centerY: 0,
    css: {
        top: '130px',
        'font-size': '18px',
        border: 'none',
        padding: '15px'
    },
    overlayCSS: {
        backgroundColor: '#000'
    }
};

blockPageContent();

$(document).ajaxStart(function () {
    blockPageContent();
}).ajaxStop(function () {
    unblockPageContent();
});

$(document).ready(function () {

    $.ajaxSetup({
        'beforeSend': function () {
            if (!$('.blockUI').length) {
                blockPageContent();
            }
        },
        'complete': function () {
            unblockPageContent();
        }
    });

    if (!$.active) {
        unblockPageContent();
    }
});

/**
 * 
 * @returns {undefined}
 */
function blockPageContent() {

    blockTarget($(".page-content"));
}

/**
 * 
 * @returns {undefined}
 */
function unblockPageContent() {

    unblockTarget($(".page-content"));
}

/**
 * 
 * @returns {undefined}
 */
function blockBody() {

    options.timeout = 0;

    $("body").block(options);
}

/**
 * 
 * @param {type} target
 * @returns {undefined}
 */
function blockTarget(target) {

    target.block(options);
}

/**
 * 
 * @param {type} target
 * @returns {undefined}
 */
function unblockTarget(target) {

    target.unblock();
}