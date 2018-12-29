
jQuery(document).ready(function () {

    initReemplazarPagoLink();

    $('#table_comprobantes_compra_info').remove();

    mostrarPopUpComprobante();

    initEditarFechaAsientoContableHandler();

    initAgregarPagoHandler();
	
	initEditarSUSS();
});

/**
 * 
 * @returns {undefined}
 */
function initReemplazarPagoLink() {

    $(document).on('click', '.reemplazar_pago_link', function (e) {

        e.preventDefault();

        bloquear();

        var ajaxDialogReemplazarPago = $.ajax({
            type: 'POST',
            data: {
                id: idOrdenPago
            },
            url: __AJAX_PATH__ + pathReemplazarPago + '/form_pagar'
        });

        id = $(this).data('id-pago');
        forma_pago = $(this).data('forma-pago');

        $.when(ajaxDialogReemplazarPago).done(function (dataDialogReemplazarPago) {

            var formularioReemplazarPago = dataDialogReemplazarPago;

            headerDialog = 'Reemplazar pago';

            var d = show_dialog({
                titulo: headerDialog,
                contenido: formularioReemplazarPago,
                callbackCancel: function () {
                    desbloquear();
                    return;
                },
                callbackSuccess: function () {
                    var formulario = $('#form_pagar').validate({
                        rules: {
                            cuenta_bancaria: {
                                required: true
                            },
                            forma_pago: {
                                required: true
                            }
                        }
                    });

                    var formulario_result = formulario.form();

                    if (formulario_result) {
                        bloquear();

                        var aditionalData = {
                            'idOrdenPago': idOrdenPago,
                            'id': id,
                            'forma_pago': forma_pago
                        };
                        $('#form_pagar').addHiddenInputData(aditionalData);
                        formData = $('#form_pagar').serialize();

                        $.ajax({
                            url: __AJAX_PATH__ + pathReemplazarPago + '/reemplazar_pago',
                            type: 'POST',
                            data: formData
                        }).done(function (r) {
                            if (r.result === 'OK') {
                                location.reload();
                                App.scrollTop();
                                return false;
                            } else {
                                desbloquear();
                                show_alert({
                                    msg: r.msg,
                                    title: 'Error en el reemplazo de pago',
                                    type: 'error'}
                                );
                            }
                        }).error(function (e) {
                            show_alert({
                                msg: 'Ocurri&oacute; un error al intentar efectuar el reemplazo de pago. Intente nuevamente.',
                                title: 'Error en el reemplazo de pago',
                                type: 'error'}
                            );
                        });
                    } else {
                        desbloquear();
                        return false;
                    }
                }

            });

            initFormularioPagar();

            $('.bootbox').removeAttr('tabindex');

            d.find('.modal-dialog').css('width', '90%');

            $('#agregar_renglon_pago').click();

            $('#form_pagar').validate();
            $('#form_pagar').find('input[id$=monto]').rules("remove");

            $('.row_restante').addClass('hidden');

            $('.div_monto_eliminar').addClass('hidden');

            $('.row_cuenta_bancaria').addClass('col-md-5');
            $('.row_cuenta_bancaria').removeClass('col-md-3');

            $('.row_cuenta_bancaria').addClass('col-md-5');
            $('.row_cuenta_bancaria').removeClass('col-md-3');

            $('.chequeraDiv').addClass('col-md-3');
            $('.chequeraDiv').removeClass('col-md-2');

            $('.transferenciaDivEmpty').addClass('col-md-3');
            $('.transferenciaDivEmpty').removeClass('col-md-2');

            $('.row_agregar_pago').remove();

            desbloquear();

        });
    });
}


/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    updateFechaOrdenPagoFromAsientoContable();

}

function initEditarSUSS()
{
	$('#ac_editar_suss').on('click', function() {
		
		// Recorro todos los montos suss
		var montoTotal = 0;
		$('.montos_suss_edicion').each(function(key, value) {  
			console.debug(value); 
			$elem = $(value);
			montoTotal = montoTotal + $elem.val();
			var sussNuevo = $elem.val();
			var idRetencion = $elem.attr('id_retencion');
			
			var data = {
				idOrdenPago: idOrdenPago,
				idRetencion: idRetencion,
				suss: sussNuevo
			};
			
			$.ajax({
				url: __AJAX_PATH__ + 'ordenpago/modficar_suss',
				type: 'POST',
				data: data
			}).success(function (r) {
				if (r.status == 'ok') {
					showFlashMessage('success', 'Se ha editado el regimen aplicado con exito.');
				} else {
					
					desbloquear();
					show_alert({
						msg: r.msg,
						title: 'Error en la modificacion de SUSS.',
						type: 'error'
					});
				}
			});
			
		});
		
	});
}