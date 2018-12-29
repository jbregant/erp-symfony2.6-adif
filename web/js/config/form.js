
var $divEjercicioContableSesion;

$(document).ready(function () {

	//Prevenir el múltiple submit
	try {

		initPreventDoubleSubmission();

		// Hack Checkbox
		//initHackCheckbox();

		// Button back global
		initButtonBack();

		// Errores
		initErrorFocus();

		// Selects
		initSelects();

		// Currencies
		initCurrencies();

		// Datepickers
		initDatepickers();

		// Checks y Radios
		initChecksYRadios();

		// Files
		initFileInput();

		// Dropdowns
		initDropdown();

		// Check Checkbox
		initCheckChecked();

		//Submit block
		initSubmitBlock();

		// Init editar ejercicio
		initEjercicioContableSesionHandler();

		initMultiEmpresa();

		//initSelectCambiarEmpresa();

	} catch(err) {}
});

function initPreventDoubleSubmission() {

    $('form').on('submit', function () {

        return checkLastSubmitTime($(this));
    });

    $(document).on('click', '.btn-submit', function () {

        return checkLastSubmitTime($(this));
    });

//    $('form').on('submit', function (e) {
//        var form = $(this);
//        if (!(form.validate) || (form.validate && form.valid())) {
//            if (form.data('submitted') === true) {
//                // Previously submitted - don't submit again
//                e.preventDefault();
//            } else {
//                // Mark it so that the next submit can be ignored
//                form.data('submitted', true);
//            }
//        }
//    });
}

/**
 *
 * @param {type} $element
 * @returns {Boolean}
 */
function checkLastSubmitTime($element) {

    var lastTime = $element.data("lastSubmitTime");

    if (lastTime) {

        var now = jQuery.now();

        if ((now - lastTime) < 3000) { // 3 segundos
            return false;
        }
    }

    checkSubmitButton();

    $element.data("lastSubmitTime", jQuery.now());

    return true;
}

/**
 *
 * @returns {undefined}
 */
function checkSubmitButton() {

    $('button[type="submit"]').each(function () {
        blockSubmitButton($(this));
    });

    $('.btn-submit').each(function () {
        blockSubmitButton($(this));
    });
}

/**
 *
 * @param {type} $element
 * @returns {undefined}
 */
function blockSubmitButton($element) {

    $element.data("label", $element.html());

    $element.prop('disabled', true);
    $element.html('Cargando...');

    window.setTimeout(function () {
        $element.html($element.data("label"));
        $element.prop('disabled', false);
    }, 3000);
}


function initHackCheckbox($element) {
    /* Hack ckeckbox del template */
    if ($element == null) {
        $('div.checker').find('input[type=checkbox]').on('change', function () {
            if ($(this).is(':checked')) {
                $(this).parent().addClass('checked');
            } else {
                $(this).parent().removeClass('checked');
            }
        });
    } else {
        $($element).find('input[type=checkbox]').on('change', function () {
            if ($(this).is(':checked')) {
                $(this).parent().addClass('checked');
            } else {
                $(this).parent().removeClass('checked');
            }
        });
    }
}

/**
 *
 * @returns {undefined}
 */
function initErrorFocus() {
    $(document).on('click', '.alert-error', function (e) {
        e.preventDefault();
        $($(this).attr('href')).focus();
        e.stopPropagation();
    });
}

/**
 *
 * @returns {undefined}
 */
function initSelects() {
    $('select.choice').each(function () {
        $(this).parent().replaceWith(this);
        var old_class = $(this).attr('class');
        $(this).select2({
            //placeholder: "",
            allowClear: true
        });
        $('#s2id_' + $(this).attr('id')).addClass(old_class);
    });
}

function initSelectById(id)
{
	$('select#' + id).each(function () {
        //$(this).parent().replaceWith(this);
        var old_class = $(this).attr('class');
        $(this).select2({
            //placeholder: "",
            allowClear: true
        });
        $('#s2id_' + $(this).attr('id')).addClass(old_class);
    });
}

/**
 *
 * @returns {undefined}
 */
function initCurrencies() {

    $('.currency').each(function () {

        var digits = 2;

        if (typeof $(this).data('digits') !== "undefined") {

            digits = $(this).data('digits');
        }

        $(this).val($(this).val().replace(/\./g, ','));

        $(this).inputmask("decimal", {radixPoint: ",", digits: digits});
    });

    /*
     $('.currency:not([sname])').each(function() {
     var copia = $(this).clone();
     var original = $(this);
     $(copia).attr('id', $(this).attr('id') + '_currency')
     .attr('name', $(this).attr('name') + '_currency')
     .val(parseFloat($(original).val()).toFixed(2).replace(/\./g, ','))
     .removeClass('currency')
     .addClass('currency_real')
     .insertBefore($(this))
     .on('blur', function() {
     $(original).val(parseFloat($(this).val().replace(/\./g, '').replace(/,/g, ".")));
     $(original).trigger('change');
     });

     $(this).removeClass('currency')
     .hide()
     .on('change', function() {
     $(copia).val(agregarSeparadorMiles(parseFloat($(original).val()).toFixed(2).replace(/\./g, ',')));
     });
     });

     $('.currency_real').priceFormat({
     prefix: '',
     centsSeparator: ',',
     thousandsSeparator: '.',
     centsLimit: 2
     });
     */

    $('.percentage').each(function () {
        $(this).val($(this).val().replace(/\./g, ','));
        $(this).inputmask('Regex', {regex: "^100$|^[0-9]{1,2}$|^[0-9]{1,2}\,[0-9]{1,5}$"});
    });

    $('.number').each(function () {

        var digits = 2;

        if (typeof $(this).data('digits') !== "undefined") {

            digits = $(this).data('digits');
        }

        $(this).inputmask("decimal", {allowMinus: false, allowPlus: false, digits: digits});
    });

    $('.numberPositive').each(function () {

        var digits = 2;

        if (typeof $(this).data('digits') !== "undefined") {

            digits = $(this).data('digits');
        }

        $(this).val($(this).val().replace(/\./g, ','));

        $(this).inputmask("decimal", {radixPoint: ",", allowMinus: false, allowPlus: false, digits: digits});
    });

    $('.integerPositive').each(function () {
        $(this).inputmask("integer", {allowMinus: false, allowPlus: false});
    });

    $('.currency, .percentage, .number, .numberPositive, .integerPositive').each(function () {
        $(this).bind('paste keypress', function (e) {

            // '46' = keyCode de '.'
            if (e.keyCode === 46 || e.charCode === 46) {

                e.preventDefault();

                $(this).val($(this).val() + ',');
            }
        });
    });

}

function agregarSeparadorMiles(nStr) {
    nStr += '';
    x = nStr.split(',');
    x1 = x[0];
    x2 = x.length > 1 ? ',' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
}

/**
 *
 * @param {type} element
 * @returns {undefined}
 */
function initDatepickers(element) {

    element = element === undefined ? document : element;

    $(element).find('input.datepicker').each(function () {

        //$(this).attr('readonly', 'readonly');
        if (!$(this).parent().parent().hasClass('input-group')) {
            $(this).parent().wrap('<div class="input-group"></div>');

            $('<span class="input-group-addon"><i class="fa fa-calendar"></i></span>')
                    .insertBefore($(this).parent());
        }

    });

    $('.datepicker').each(function () {
        initDatepicker($(this));
    });
}

/**
 *
 * @param {type} element
 * @param {type} options
 * @returns {undefined}
 */
function initDatepicker(element, options) {
    if (options === undefined) {
        options = {};
    }

    var def_opts = {
        format: 'dd/mm/yyyy',
        language: "es",
        autoclose: true
    };

    $.extend(def_opts, options);

    $(element).datepicker(def_opts);

    if (!$(element).hasClass('novalidate')) {
        $(element).addClass('fecha_custom');
    }

    if ($.inputmask && !$(element).hasClass('nomask')) {
        $(element).inputmask('date', {placeholder: "_", yearrange: {minyear: 1900, maxyear: 2200}});
    }
}

/**
 *
 * @returns {undefined}
 */
function initChecksYRadios() {
    var swts = $('form input[type=checkbox]').not('.not-checkbox-transform, [baseClass=bootstrap-switch]');

    if (swts.length == 0) {
        return;
    }

    swts.attr({
        'data-on-label': 'Si',
        'data-off-label': 'No',
        'data-on': "success",
        'data-off': "default",
        'baseClass': "bootstrap-switch"
    }).bootstrapSwitch();
}

/**
 *
 * @returns {undefined}
 */
function initFileInput() {

    $(".filestyle").each(function () {
        $(this).filestyle();

        var $nombreArchivo = $(this).attr('data-file');

        if ($nombreArchivo) {
            $(this).next('.bootstrap-filestyle').find('input').val($nombreArchivo);
        }
    });
}

/**
 *
 * @param {type} parent
 * @returns {undefined}
 */
function reInitCheckBoxes(parent) {
    var test = $(parent).find("input[type=checkbox]:not(.toggle, .make-switch), input[type=radio]:not(.toggle, .star, .make-switch)");
    if (test.size() > 0) {
        test.each(function () {
            if ($(this).parents(".checker").size() == 0) {
                $(this).show();
                $(this).uniform();
            }
        });
    }
}

/**
 *
 * @returns {undefined}
 */
function initDropdown() {

    $('body')
            .off('click.dropdown touchstart.dropdown.data-api', '.dropdown')
            .on('click.dropdown touchstart.dropdown.data-api', '.dropdown form', function (e) {
                e.stopPropagation();
            });

//    $('.dropdown-menu > li > a').on('click',function(e){
//        $(this).parents('.btn-group.open').dropdown('toggle');
//    });
//}
}

/**
 *
 * @returns {undefined}
 */
function initSubmitBlock() {
    $(document).one('submit', 'form:not(.no-block)', function (event) {
        blockBody();
    });
}

/**
 *
 * @returns {undefined}
 */
function initCheckChecked() {
    $('div.checker').find('input[type=checkbox]').each(function () {
        if ($(this).is(':checked')) {
            $(this).parent().addClass('checked');
        }
    });
}

/**
 *
 * @param {type} element
 * @param {type} readonly
 * @returns {undefined}
 */
function readonlyDatePicker(element, readonly) {

    if (readonly) {
        element
                .prop('readonly', true)
                .unbind('focus')
                .off('focus');
    }
    else {
        element.prop('readonly', false).on('focus', function () {

            $(this).datepicker().datepicker('show');

            true;
        });
    }
}

/**
 *
 * @returns {undefined}
 */
function initEjercicioContableSesionHandler() {

    $divEjercicioContableSesion = $('#div_ejercicio_contable')
            .removeClass('hidden').html();

    $('#div_ejercicio_contable').remove();

    // BOTON EDITAR ASIENTO CONTABLE DE SESION
    $(document).on('click', '.ejercicio-contable-sesion', function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea modificar el ejercicio contable?',
            callbackOK: function () {

                show_dialog({
                    titulo: 'Ejercicio contable',
                    contenido: $divEjercicioContableSesion,
                    labelCancel: 'Cancelar',
                    closeButton: false,
                    callbackCancel: function () {

                        return;
                    },
                    callbackSuccess: function () {

                        var formulario = $('form[name=adif_contablebundle_ejerciciocontable_sesionform]');

                        var formularioValido = formulario.validate().form();

                        // Si el formulario es válido
                        if (formularioValido) {

                            var data = {
                                ejercicio_contable_sesion: $('#adif_contablebundle_ejercicioContableSesion').val()
                            };

                            $.ajax({
                                type: "POST",
                                data: data,
                                url: __AJAX_PATH__ + 'ejercicio/editar_ejercicio_sesion/'
                            }).done(function (response) {

                                window.onbeforeunload = function () {
                                    blockBody();
                                };

                                location.reload();
                            });

                            return;
                        }
                        else {
                            return false;
                        }
                    }
                });

                $('#detalle_asiento_reversion').show();
            }
        });

        e.stopPropagation();
    });

}

function initMultiEmpresa()
{

	$divCambiarEmpresa = $('#div_cambiar_empresa')
            .removeClass('hidden').html();

    $('#div_cambiar_empresa').remove();

    // BOTON CAMBIAR EMPRESA
    $(document).on('click', '#cambiar_empresa', function (e) {

		e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea cambiar de empresa?',
            callbackOK: function () {

                var box = show_dialog({
                    titulo: 'Cambiar de empresa',
                    contenido: $divCambiarEmpresa,
                    labelCancel: 'Cancelar',
                    closeButton: false,
                    callbackCancel: function () {

                        return;
                    },
                    callbackSuccess: function () {

						var data = {
							idEmpresa: $('#select_cambiar_empresa').val()
						};

						$.ajax({
							type: "POST",
							data: data,
							url: __AJAX_PATH__ + 'multiempresa/cambiar/'
						}).success(function (response) {

							window.onbeforeunload = function () {
								blockBody();
							};

							if (response.status == 'ok') {

								location.href = __AJAX_PATH__ + 'home/'

							} else {

								show_alert({
									title: 'Error',
									type: 'info',
									msg: response.mensaje,
								});
							}
						});

						return;
                    }
                });

				box.bind('shown.bs.modal', function(){
					initSelectCambiarEmpresa();
				});
            }
        });

        e.stopPropagation();
    });
}

function initSelectCambiarEmpresa()
{
	//$('#select_cambiar_empresa').select2();

	$('#select_cambiar_empresa').on('change', function() {

		$('.denominacion_empresa').addClass('hidden');
		$('.cuit_empresa').addClass('hidden');

		var id = $(this).val();
		$('#denominacion_empresa_' + id).removeClass('hidden');
		$('#cuit_empresa_' + id).removeClass('hidden');

	});
}
