$(document).ready(function () {

    $('.asignar-conceptos').on('click', function (e) {
        e.preventDefault();
        bloquear();

        var idConcepto = $(this).data('id-concepto');
        var concepto = $(this).data('concepto');

        var ajax_dialog_form = $.ajax({
            type: 'post',
            data: {
                id: idConcepto
            },
            url: __AJAX_PATH__ + 'configuracionreportespresupuestarios/form_epe2'
        });
        $.when(ajax_dialog_form).done(function (dataDialogAsignar) {
            show_dialog({
                titulo: 'Asignar cuentas contables a ' + concepto,
                contenido: dataDialogAsignar,
                callbackCancel: function () {
                    desbloquear();
                    return;
                },
                callbackSuccess: function () {
                    $('#form_epe2').submit();
                }

            });

            initSelects();
        });



    });

});
