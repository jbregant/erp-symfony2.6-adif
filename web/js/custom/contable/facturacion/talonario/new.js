$(document).ready(function () {
    $.validator.addMethod("greaterThan", function (value, element, param) {
        var numeroDesde = $("#adif_contablebundle_facturacion_talonario_numeroDesde").val();
        var numeroHasta = $("#adif_contablebundle_facturacion_talonario_numeroHasta").val();

        return parseInt(numeroDesde) < parseInt(numeroHasta);
    });

    $('form[name="adif_contablebundle_facturacion_talonario"]').validate({
        rules: {
            'adif_contablebundle_facturacion_talonario[puntoVenta]': {
                required: true
            },
            'adif_contablebundle_facturacion_talonario[numeroDesde]': {
                required: true
            },
            'adif_contablebundle_facturacion_talonario[numeroHasta]': {
                required: true,
                greaterThan: true
            },
            'adif_contablebundle_facturacion_talonario[codigoAutorizacionImpresionTalonario][numero]': {
                required: true
            },
            'adif_contablebundle_facturacion_talonario[codigoAutorizacionImpresionTalonario][fechaVencimiento]': {
                required: true
            }
        },
        messages: {
            'adif_contablebundle_facturacion_talonario[numeroHasta]': {
                greaterThan: "El n&uacute;mero de inicio debe ser menor al n&uacute;mero de fin"
            }
        }
    });

    $('#adif_contablebundle_facturacion_talonario_numeroDesde, #adif_contablebundle_facturacion_talonario_numeroHasta').inputmask({
        mask: "99999999",
        numericInput: true,
        onincomplete: function () {
            $(this).val($(this).val().replace(/_/g, '0'));
        }
    });
});