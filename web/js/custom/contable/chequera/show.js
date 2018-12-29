
var $numeroChequeContent;

var numeroInicial = $('.numero-inicial').text().trim();

var numeroFinal = $('.numero-final').text().trim();

/**
 * 
 */
jQuery(document).ready(function () {

    initAnularChequeLinks();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    $.validator.addMethod("rangoNumeroCheque", function (value, element, param) {

        var numeroCheque = $('#adif_contablebundle_chequera_numeroCheque').val();

        return numeroInicial <= numeroCheque && numeroFinal >= numeroCheque;
    });

    $('form[name=adif_contablebundle_numero_cheque]').validate();

    // Validacion numero de cheque
    $('#adif_contablebundle_chequera_numeroCheque').rules('add', {
        rangoNumeroCheque: true,
        messages: {
            rangoNumeroCheque: "El n&uacute;mero de cheque no se encuentra dentro del rango."
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initAnularChequeLinks() {

    $numeroChequeContent = $('#numero_cheque_content').removeClass('hidden').html();

    $('#numero_cheque_content').remove();

    // BOTON ANULAR CHEQYE
    $('.link-anular-cheque').click(function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea anular un cheque?',
            callbackOK: function () {

                show_dialog({
                    titulo: 'Detalle del cheque a anular',
                    contenido: $numeroChequeContent,
                    labelCancel: 'Cancelar',
                    closeButton: false,
                    callbackCancel: function () {

                        return;
                    },
                    callbackSuccess: function () {

                        initValidate();

                        var formulario = $('form[name=adif_contablebundle_numero_cheque]');

                        var formularioValido = formulario.validate().form();

                        // Si el formulario es válido
                        if (formularioValido) {

                            var numeroCheque = $('#adif_contablebundle_chequera_numeroCheque')
                                    .val();

                            window.location.href = url + '?cheque=' + numeroCheque;

                            return;
                        }
                        else {
                            return false;
                        }
                    }
                });

                $('#numero_cheque_content').show();
            }
        });

        e.stopPropagation();
    });
}