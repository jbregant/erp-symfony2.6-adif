var formulario = $('form[name=adif_contablebundle_regimenretencion]');

$(document).ready(function() {
    formulario.validate({
        rules: {
            'adif_contablebundle_regimenretencion[denominacion]': {
                required: true,
            },
            'adif_contablebundle_regimenretencion[tipoImpuesto]': {
                required: true,
            },
            'adif_contablebundle_regimenretencion[alicuota]': {
                required: true,
            }
        }
    });
});