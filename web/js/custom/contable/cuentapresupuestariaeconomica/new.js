$('form[name=adif_contablebundle_cuentapresupuestariaeconomica]').validate({
    rules: {
        'adif_contablebundle_cuentapresupuestariaeconomica[codigo]': {
            required: true
        }
    }
});

$("#adif_contablebundle_cuentapresupuestariaeconomica_cuentaPresupuestariaEconomicaPadre").change(function () {

    var data = {
        id: $(this).val()
    };

    $.ajax({
        type: "POST",
        data: data,
        url: pathCodigoInicial
    }).done(function (codigoInicial) {
        $('#adif_contablebundle_cuentapresupuestariaeconomica_codigo').val(codigoInicial);
    });
});