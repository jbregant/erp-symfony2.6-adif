
var $detalleAsientoFormal;

$(document).ready(function () {

    initAsientoFormalLinkAction();
});

/**
 * 
 * @returns {undefined}
 */
function initAsientoFormalLinkAction() {

    $detalleAsientoFormal = $('#detalle_asiento_formal').removeClass('hidden').html();

    $('#detalle_asiento_formal').remove();

    $('.link-asiento-formal').click(function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea realizar el asiento formal?',
            callbackOK: function () {

                show_dialog({
                    titulo: 'Detalle del asiento formal',
                    contenido: $detalleAsientoFormal,
                    labelCancel: 'Cancelar',
                    closeButton: false,
                    callbackCancel: function () {

                        return;
                    },
                    callbackSuccess: function () {

                        var formulario = $('form[name=adif_contablebundle_detalle_asiento_formal]');

                        var formularioValido = formulario.validate().form();

                        // Si el formulario es válido
                        if (formularioValido) {

                            var fechaContable = $('#adif_contablebundle_asientocontable_fechaContable')
                                    .val();

                            window.location.href = url + '?fecha_contable=' + fechaContable;

                            return;
                        }
                        else {
                            return false;
                        }
                    }
                });

                initDatepickers();

                $('#detalle_asiento_formal').show();
            }
        });

        e.stopPropagation();
    });

}