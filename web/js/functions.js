
$(document).ready(function () {

    //Borrar action de las tablas
    initBorrarButton();

    initAnularButton();

    initImprimirButton();

    // Button back global
    initButtonBack();

    // Interceptor del ajax
    if (location.toString().indexOf('app_dev.php') === -1) {
        initAjaxListener();
    }

    // Init .tooltips
    initTooltip();
});

function initBorrarButton() {
    $('table .ctn_acciones .accion-borrar').off().on('click', function (e) {
        e.preventDefault();
        var a_href = $(this).attr('href');
        show_confirm({
            title: 'Confirmar',
            type: 'warning',
            msg: '¿Confirma la eliminaci&oacute;n?',
            callbackOK: function () {
                location.href = a_href;
            }
        });
        e.stopPropagation();
    });
}

function initAnularButton() {
    $(document).on('click', 'table .ctn_acciones .accion-anular', function (e) {
        e.preventDefault();
        var a_href = $(this).attr('href');
        show_confirm({
            title: 'Confirmar',
            type: 'warning',
            msg: '¿Confirma la anulaci&oacute;n?',
            callbackOK: function () {
                location.href = a_href;
            }
        });
        e.stopPropagation();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initImprimirButton() {

    $(document).on('click', '.accion-imprimir', function (e) {
        e.preventDefault();
        printPage($(this).attr('print_href'));
        e.stopPropagation();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initAjaxListener() {
    $.ajaxSetup({
        statusCode: {
            403: function () {
                open_window('POST', __AJAX_PATH__ + 'error/403');
            },
            404: function () {
                open_window('POST', __AJAX_PATH__ + 'error/404');
            },
            500: function () {
                open_window('POST', __AJAX_PATH__ + 'error/500');
            }
        }
    })
}

/**
 * 
 * @param Object options_in 
 *      {
 *          title: 'Error',
 *          msg: 'Ha ocurrido un error',
 *          callbackOK: function() {},
 *          callbackCancel: function() {}
 *      }
 * @returns null
 */
function show_alert(options_in) {
    var options = $.extend({
        title: 'Error',
        msg: 'Ha ocurrido un error.',
        type: 'info'
    }, options_in);

    var d = bootbox.dialog({
        title: options.title,
        message: options.msg,
        buttons: {
            success: {
                label: "Aceptar",
                className: "btn-primary",
                callback: function () {
                    return;
                }
            }
        }
    });

    if (options.type == 'error') {
        $(d).find('.modal-header').css({'background-color': '#D2322D'});
    }

}

/**
 * 
 * @param Object options_in 
 *      {
 *          title: 'Confirmar',
 *          msg: 'Desea continuar?',
 *          callbackOK: function() {},
 *          callbackCancel: function() {}
 *      }
 * @returns null
 */
function show_confirm(options_in) {
    var options = $.extend({
        title: 'Confirmar',
        msg: '¿Desea continuar?',
        callbackOK: function () {
        },
        callbackCancel: function () {
        }
    }, options_in);

    bootbox.confirm({
        title: options.title,
        message: options.msg,
        buttons: {
            'cancel': {
                label: 'Cancelar',
                className: 'btn-default pull-left'
            },
            'confirm': {
                label: 'Confirmar',
                className: 'btn-submit btn-success pull-right'
            }
        },
        callback: function (result) {
            if (result) {
                options.callbackOK();
            } else {
                options.callbackCancel();
            }
        }
    });
}

/* 
 *  Se muestra un mensaje con un contenido dos acciones,
 *  una de cancelacion y otra de confirmacion
 *  
 *  Al invocar se deben enviar los siguientes parametros
 *  con un objeto json: titulo, contenido, callbackCancel y
 *  callbackSuccess.
 *  Ejemplo:
 *  {
 *      titulo: 'Titulo',
 *      contenido: 'contenido',
 *      callbackCancel: function() {},
 *      callbackSuccess: function() {},
 *  }
 *  
 */
function show_dialog(options) {

    return bootbox.dialog({
        class: options.class ? options.class : '',
        title: options.titulo,
        message: options.contenido,
        buttons: {
            danger: {
                label: options.labelCancel ? options.labelCancel : "Cancelar",
                className: "btn-default cancel",
                callback: function () {
                    var result = options.callbackCancel();
                    return result;
                }
            },
            success: {
                label: options.labelSuccess ? options.labelSuccess : "Guardar",
                className: "btn-submit btn-primary success",
                callback: function () {
                    var result = options.callbackSuccess();
                    return result;
                }
            }
        }
    });
}

function open_window(verb, url, data, target) {
    if ($('#open_window_form').length > 0) {
        $('#open_window_form').remove();
    }

    var form = document.createElement("form");
    form.id = 'open_window_form';
    form.action = url;
    form.method = verb;
    form.target = target || "_self";

    if (data) {
        for (var key in data) {
            var input = document.createElement("textarea");
            input.name = key;
            input.value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
            form.appendChild(input);
        }
    }

    form.style.display = 'none';
    document.body.appendChild(form);
    form.submit();
}

var substringMatcher = function (strs) {
    return function findMatches(q, cb) {
        var matches, substringRegex;

        // an array that will be populated with substring matches
        matches = [];

        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, 'i');

        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function (i, str) {
            if (substrRegex.test(str)) {
                // the typeahead jQuery plugin expects suggestions to a
                // JavaScript object, refer to typeahead docs for more info
                matches.push({value: str});
            }
        });

        cb(matches);
    };
};

/**
 * 
 * @param {type} e
 * @param {type} options
 * @returns {undefined}
 */
function bloquear(e, options) {
    if (options === undefined) {
        options = [];
    }

    var def_opts = {
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
        },
        target: e !== undefined ? e : $(".page-content")
    };

    $.extend(def_opts, options);

    App.blockUI(def_opts);
}

/**
 * 
 * @param {type} e
 * @returns {undefined}
 */
function desbloquear(e) {

    if (typeof e === "undefined" || e === null) {
        e = $(".page-content");
    }

    $('body').unblock();

    App.unblockUI(e);
}


/**
 * Formatea el <code>str</code> recibido como parámetro, agregando ceros
 * a la izquierda, hasta igualar <code>max</code>.
 * 
 * @author Manuel Becerra
 * 
 * @param {type} str
 * @param {type} max
 * @returns {String}
 */
function pad(str, max) {

    str = str.toString().replace(" ", "");

    return (str.length < max ? pad("0" + str, max) : str).replace(" ", "");
}

/**
 * 
 * Permite agregar la <code>data</code> recibida como parámetro, como inputs hidden
 * en un form.
 * 
 * @author Manuel Becerra
 * 
 * @param {type} data
 * @returns {undefined}
 */
$.fn.addHiddenInputData = function (data) {

    var keys = {};

    var addData = function (data, prefix) {

        for (var key in data) {
            var value = data[key];

            if (!prefix) {
                var nprefix = key;
            } else {
                var nprefix = prefix + '[' + key + ']';
            }

            if (typeof (value) == 'object') {
                addData(value, nprefix);
                continue;
            }

            keys[nprefix] = value;
        }
    };

    addData(data);

    var $form = $(this);

    for (var k in keys) {
        $form.addHiddenInput(k, keys[k]);
    }
};

/**
 * Agrega un input hidden a un formulario.
 * 
 * @author Manuel Becerra
 * 
 * @param {type} key
 * @param {type} value
 * @returns {undefined}
 */
$.fn.addHiddenInput = function (key, value) {

    var $input = $('<input type="hidden" name="' + key + '" />');

    $input.val(value);

    $(this).append($input);

};

/**
 * Retorna la fecha actual en formato dd/mm/yyyy.
 * 
 * @author Manuel Becerra
 * 
 * @returns {String}
 */
function getCurrentDate() {

    var fullDate = new Date();

    var day = fullDate.getDate();

    var month = fullDate.getMonth() + 1;

    var twoDigitDay = ((day.toString().length) === 2)
            ? (day) : '0' + (day);

    var twoDigitMonth = ((month.toString().length) === 2)
            ? (month) : '0' + (month);

    return twoDigitDay + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
}

/**
 * 
 * @param {type} year
 * @param {type} month
 * @returns {Number}
 */
function getLastDayOfYearAndMonth(year, month) {
    return(new Date((new Date(year, month, 1)) - 1)).getDate();
}

/**
 * 
 * Retorna una nueva fecha indicando el primer día del corriente mes para el ejercicio dado.
 * 
 * @param {type} yearParam
 * @returns {Date}
 */
function getFirstDateOfCurrentMonth(yearParam) {

    var currentDateSplited = getCurrentDate().split("/");
    var currentDate = new Date(currentDateSplited[2], currentDateSplited[1] - 1, currentDateSplited[0]);

    var month = currentDate.getMonth();
    var year = typeof yearParam === "undefined" ? currentDate.getFullYear() : yearParam;
    var firstDay = 1;

    return new Date(year, month, firstDay);
}

/**
 * 
 * Retorna una nueva fecha indicando el último día del corriente mes para el ejercicio dado.
 * 
 * @param {type} yearParam
 * @returns {Date}
 */
function getEndingDateOfCurrentMonth(yearParam) {

    var currentDateSplited = getCurrentDate().split("/");
    var currentDate = new Date(currentDateSplited[2], currentDateSplited[1] - 1, currentDateSplited[0]);

    var month = currentDate.getMonth();
    var year = typeof yearParam === "undefined" ? currentDate.getFullYear() : yearParam;
    var endingDay = getLastDayOfYearAndMonth(year, month + 1);

    return new Date(year, month, endingDay);
}

/**
 * 
 * @returns {undefined}
 */
function initButtonBack() {
    $(document).on('click', '.button-back', function () {
        if ($(this).attr("back-url")) {
            location.href = $(this).attr("back-url");
        } else {
            window.history.back();
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initTooltip() {
    $('body').tooltip({
        selector: '.tooltips'
    });
}

/**
 * 
 * @param {type} select
 * @returns {undefined}
 */
function resetSelect(select) {
    $(select).find("option[value!='']").remove();
    $(select).select2("val", "");
    $(select).select2('readonly', true);
}

/**
 * 
 * @returns {undefined}
 */
function initEllipsis() {

    $(".truncate").each(function () {

        $(this).trigger("update.dot");

        $(this).dotdotdot({
            ellipsis: '... ',
            wrap: 'letter',
            fallbackToLetter: true,
            after: null,
            watch: true,
            height: 30,
            tolerance: 0,
            lastCharacter: {
                remove: [' ', ',', ';', '.', '!', '?'],
                noEllipsis: []
            }
        });
    });
}

/**
 * 
 * @param {type} type
 * @param {type} message
 * @param {type} delay
 * @returns {undefined}
 */
function showFlashMessage(type, message, delay, className) {

    delay = typeof delay !== 'undefined' ? delay : 10000;
	className = typeof className !== 'undefined' ? className : '.breadcrumb';

    var fa = "check";

    switch (type) {
        case 'success':
            fa = "check";
            break;
        case 'danger':
            fa = "times";
            break;
        case 'warning':
            fa = "exclamation-circle";
            break;
        case 'info':
            fa = "info";
            break;
    }

    var id = Math.floor(Math.random() * 1000);

    var alertDiv =
            '<div id="' + id + '" class="alert alert-' + type + ' fade in" style="display: none;">\n\
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>\n\
                <i class="fa-lg fa fa-' + fa + '" style="margin-top: -1px; margin-right: 5px;"></i>\n\
                <span> ' + message + '.</span>\n\
            </div>';

    $(className).after(alertDiv);

    App.scrollTop();

    setTimeout(
            function () {
                if (delay > 0) {
                    $('#' + id).show('slow').delay(delay).hide('slow', function () {
                        $(this).remove();
                    });
                } else {
                    $('#' + id).show('slow');
                }
            }, 500);
}

/**
 * 
 * @param {type} table
 * @returns {undefined}
 */
function hideExportCustom(table) {
    $(table).parents('.portlet').find('.export-tools').html('');
}

/**
 * 
 * @param {type} table
 * @returns {undefined}
 */
function initExportCustom(table) {

    hideExportCustom(table);

    if ($(table).hasClass('export-custom')) {
        $(table).parents('.row').find('.export-tools').prepend(
                '<div class="btn-group pull-right">\n\
                    <div class="btn-group">\n\
                        <button class="btn btn-sm green excel-custom" type="button">\n\
                        <i class="fa fa-floppy-o"></i>\n\
                        Exportar a Excel</button>\n\
                    </div>\n\
                </div>\n\
                <div class="btn-group pull-right">\n\
                    <div class="btn-group">\n\
                        <button class="btn btn-sm dark pdf-custom" type="button">\n\
                        Exportar a PDF\n\
                        <i class="fa fa-file-pdf-o"></i></button>\n\
                    </div>\n\
                </div>');

        $('.excel-custom').on('click', function (e) {
            e.preventDefault();
            exportCustom(table, 'excel');
            e.stopPropagation();
        });

        $('.pdf-custom').on('click', function (e) {
            e.preventDefault();
            exportCustom(table, 'pdf');
            e.stopPropagation();
        });
    }
}

/**
 * 
 * @param {type} table
 * @param {type} tipo
 * @returns {undefined}
 */
function exportCustom(table, tipo) {

    var data = Array();

    var $rows = [];

    if (!$.fn.DataTable.isDataTable($(table))) {
        $rows = $(table).find('tbody').find('tr');
    }
    else {
        $rows = $(table).DataTable().rows().nodes().to$();
    }

    $rows.each(function (e, v) {

        data[e] = Array();

        $(v).find('td').not('.ctn_acciones, .no-export').each(function (f, u) {
            data[e][f] = $(u).html().replace('$ ', '');
            if ($(u).prop('colspan') > 1) {
                for (index = 1; index < $(u).prop('colspan'); index++) {
                    data[e][f + index] = '';
                }
            }
        });
    });

    open_window('POST', __AJAX_PATH__ + getControllerPath() + 'export_' + tipo, getExportContent(table, data), '_blank');
}

/**
 * 
 * @returns {String}
 */
function getControllerPath() {
    return '';
}

/**
 * 
 * @param {type} table
 * @param {type} data
 * @returns {getExportContent.functionsAnonym$11}
 */
function getExportContent(table, data) {

    return {
        content: {
            title: $(table).attr('dataexport-title'),
            sheets: {
                0: {
                    title: $(table).attr('dataexport-title'),
                    tables: {
                        0: {
                            title: $(table).attr('dataexport-title'),
                            titulo_alternativo: (typeof $(table).attr('dataexport-title-alternativo') !== typeof undefined && $(table).attr('dataexport-title-alternativo') !== false
                                    ? $(table).attr('dataexport-title-alternativo')
                                    : ''),
                            data: JSON.stringify(data),
                            headers: JSON.stringify(getHeadersTableCustom(table))
                        }
                    }
                }
            }
        }
    };
}

/**
 * 
 * @param {type} table
 * @returns {getHeadersTableCustom.a|Array}
 */
function getHeadersTableCustom(table) {
    var a = Array();
    $(table).find('thead').find('tr[class=headers] th:visible').not('.ctn_acciones').each(function (e, v) {
        a.push({texto: $(v).text(),
            formato: $(v).attr('export-format') ? $(v).attr('export-format') : 'text'
        });
    });
    return a;
}

/**
 * Retorna una nueva fecha indicando el primer día del año.
 * 
 * @returns {Date}
 */
function getFirstDateOfCurrentYear() {

    var currentDateSplited = getCurrentDate().split("/");
    var currentDate = new Date(currentDateSplited[2], currentDateSplited[1] - 1, currentDateSplited[0]);

    return new Date(currentDate.getFullYear(), 0, 1);
}

function printPage(url) {
    bloquear();

    $('#iframe_print').remove();

    $iframe = $('<iframe />', {
        id: "iframe_print",
        name: "iframe_print",
        src: url,
        style: 'display:none;'
    }).appendTo('body');

    $iframe[0].contentWindow.print();

    desbloquear();
}

/**
 * 
 * @param {type} number
 * @param {type} cantDecimals
 * @returns {String}
 */
function convertToMoneyFormat(number, cantDecimals) {

    if (typeof cantDecimals === "undefined" || cantDecimals === null) {
        cantDecimals = 2;
    }

    return ('$ ' + parseFloat(number, 10).toFixed(cantDecimals).replace(/(\d)(?=(\d{3})+\.)/g, "$1.").toString())
            .replace(/.([^.]*)$/, ",$1");
}

/**
 * 
 * @param {type} number
 * @returns {String}
 */
function convertToCurrencyFormat(number) {

    return (parseFloat(number, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1.").toString())
            .replace(/.([^.]*)$/, ",$1");
}


/**
 * 
 * @param {type} deleteLink
 * @returns {undefined}
 */
function updateDeleteLinks(deleteLink) {

    deleteLink.each(function () {

        $(this).tooltip();

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var deletableRow = $(this).closest('.row');

            show_confirm({
                msg: '¿Desea eliminar el registro?',
                callbackOK: function () {
                    deletableRow.hide('slow', function () {
                        deletableRow.remove();
                    });
                }
            });

            e.stopPropagation();

        });
    });
}

/**
 * 
 * @param {type} date1
 * @param {type} date2
 * @returns {Number}
 */
function monthDiff(date1, date2) {

    var months;

    months = (date2.getFullYear() - date1.getFullYear()) * 12;
    months -= date1.getMonth() + 1;
    months += date2.getMonth();

    return months <= 0 ? 0 : months;
}

/**
 * Retorna un Date a partir de un string en formant dd/mm/YY
 * 
 * @param {type} stringDate
 * @returns {Date}
 */
function getDateFromString(stringDate) {

    var splitedDate = stringDate.split("/");

    return new Date(splitedDate[2], splitedDate[1] - 1, splitedDate[0]);
}


/**
 * Retorna el año actual.
 * 
 * @returns {Date}
 */
function getCurrentYear() {
    return new Date().getFullYear();
}

function initBusqueda() {
    $('#search').hideseek({
        highlight: true
    });
//    $('#search').on("_after", function () {
//        $('.busqueda').children().css('display', 'table-row')
//    });
}


/**
 * 
 * @param {type} iframe
 * @param {type} additionalHeight
 * @returns {undefined}
 */
function initIframe(iframe, additionalHeight) {

    var iframeCtn = iframe.contents();

    iframeCtn.find('.page-content').css({
        padding: 0,
        minHeight: 0
    });

    iframeCtn.find('.page-content > .row').removeClass('margin-bottom-20');
    iframeCtn.find('.page-content > .row .portlet.box').css('margin-bottom', 0);

    iframeCtn.find('.btn.default.button-back').remove();

    $.colorbox.resize({innerHeight: $('iframe').contents().find('html').height() + additionalHeight});

    iframeCtn.find('.page-content').css({
        padding: 0
    });
}

function destroyEllipsis() {
    $(".truncate").each(function () {
        $(this).trigger("destroy")
    });
}