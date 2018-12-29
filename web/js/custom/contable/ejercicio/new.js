
var $formularioEjercicioContable = $('form[name="adif_contablebundle_ejerciciocontable"]');

$(document).ready(function () {

    initEstadoEjercicioHandler();

    initPeriodosRegistracion();

    initSubmitButton();

});

/**
 * 
 * @returns {undefined}
 */
function initEstadoEjercicioHandler() {

    var $periodos = $('input[type="checkbox"]:not(".estado-ejercicio")');

    if ($('.estado-ejercicio').is(':checked')) {
        bloquearPeriodos($periodos);
    }

    $('.estado-ejercicio').on('switch-change', function () {

        if ($(this).is(':checked')) {

            $periodos.each(function () {
                $(this).bootstrapSwitch('setState', false);
            });

            bloquearPeriodos($periodos);
        }
        else {

            $periodos.each(function () {
                $(this).bootstrapSwitch('setState', true);
            });

            initPeriodosRegistracion();
        }
    });

}

/**
 * 
 * @returns {undefined}
 */
function initPeriodosRegistracion() {

    var anioEjercicio = $('#adif_contablebundle_ejerciciocontable_fechaInicio').datepicker("getDate").getFullYear();

    $('input[type="checkbox"]:not(".estado-ejercicio")').bootstrapSwitch('setOnLabel', 'HABILITADO');

    $('input[type="checkbox"]:not(".estado-ejercicio")').bootstrapSwitch('setOffLabel', 'CERRADO');

    var $periodosABloquear = $('input[type="checkbox"]:not(".estado-ejercicio")').filter(function () {
//        return parseInt($(this).data("mes")) > ((new Date().getMonth()) + 1);
        return !(((new Date()).getFullYear() > anioEjercicio) || (((new Date()).getFullYear() <= anioEjercicio) && (parseInt($(this).data("mes")) <= ((new Date().getMonth()) + 1))));
    });

    bloquearPeriodos($periodosABloquear);

    var $periodosADesbloquear = $('input[type="checkbox"]:not(".estado-ejercicio")').filter(function () {
//        return parseInt($(this).data("mes")) <= ((new Date().getMonth()) + 1);
        return ((new Date()).getFullYear() > anioEjercicio) || (((new Date()).getFullYear() <= anioEjercicio) && (parseInt($(this).data("mes")) <= ((new Date().getMonth()) + 1)));
    });

    desbloquearPeriodos($periodosADesbloquear);

}


/**
 * 
 * @param {type} $periodos
 * @returns {undefined}
 */
function bloquearPeriodos($periodos) {

    $periodos.each(function () {
        $(this).parents('.has-switch').block({
            message: null, overlayCSS: {
                backgroundColor: 'black',
                opacity: 0.05,
                cursor: 'not-allowed'}
        });
    });

}


/**
 * 
 * @param {type} $periodos
 * @returns {undefined}
 */
function desbloquearPeriodos($periodos) {

    $periodos.each(function () {
        $(this).parents('.has-switch').unblock();
    });

}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_ejerciciocontable_submit').on('click', function (e) {

        if ($formularioEjercicioContable.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el ejercicio contable? La operación puede tardar varios minutos.',
                callbackOK: function () {

                    showBlockMessage();

                    $formularioEjercicioContable.submit();
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;
    });

}

/**
 * 
 * @returns {undefined}
 */
function showBlockMessage() {

    var options = {
        message: '<span class="bold">\n\
                <img src="' + __LOADING_IMG_PATH__ + '" style="margin-right: .3em" /> Por favor espere...\n\
            </span>',
        centerY: 0,
        timeout: 0,
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

    $("body").block(options);
}