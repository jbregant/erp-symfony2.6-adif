
var oTable = $('#table-solicitudcompra');

var _d;

/**
 * 
 */
jQuery(document).ready(function () {

    $('form[name=form]').validate();

    initEllipsis();

    initRenglonLinks();

    initSolicitudLinks();
	
	initBotonEditarDescripcionRenglon();

});


/**
 * 
 * @returns {undefined}
 */
function initRenglonLinks() {

    // BOTON APROBAR RENGLON
    $('.link-aprobar-renglon').click(function (e) {

        e.preventDefault();

        var tr = $(this).parents('tr');

        var href = $(this).prop('href').replace('aprobarrenglon', 'desaprobarrenglon');

        $.ajax({
            type: "POST",
            url: $(this).prop('href')
        }).done(function () {

            var aPos = oTable.dataTable().fnGetPosition(tr[0]);

            var link = "<a class='btn btn-xs red tooltips link-desaprobar-renglon "
                    + "data-original-title='Desaprobar' href='" + href + "'>"
                    + "<i class='fa fa-times'></i></a>";

            // oTable.dataTable().fnUpdate('Aprobado', aPos, 10);
            oTable.dataTable().fnUpdate(link, aPos, 11);

            tr.removeClass('renglon-default');
            tr.removeClass('renglon-desaprobado');
            tr.addClass('renglon-aprobado');

            initRenglonLinks();
        });

        e.stopPropagation();
    });


    // BOTON DESAPROBAR RENGLON
    $('.link-desaprobar-renglon').click(function (e) {

        e.preventDefault();

        var tr = $(this).parents('tr');

        var href = $(this).prop('href').replace('desaprobarrenglon', 'aprobarrenglon');

        $.ajax({
            type: "POST",
            url: $(this).prop('href')
        }).done(function () {

            var aPos = oTable.dataTable().fnGetPosition(tr[0]);

            var link = "<a class='btn btn-xs green tooltips link-aprobar-renglon "
                    + "data-original-title='Aprobar' href='" + href + "'>"
                    + "<i class='fa fa-check'></i></a>";

            // oTable.dataTable().fnUpdate('Desaprobado', aPos, 10);
            oTable.dataTable().fnUpdate(link, aPos, 11);

            tr.removeClass('renglon-default');
            tr.removeClass('renglon-aprobado');
            tr.addClass('renglon-desaprobado');

            initRenglonLinks();
        });

        e.stopPropagation();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initSolicitudLinks() {

    // BOTON APROBAR SOLICITUD
    $('.link-aprobar-solicitud').click(function (e) {

        e.preventDefault();

        // Si hay Renglones Desaprobados
        if (!$('#table-solicitudcompra').find('.renglon-desaprobado').length > 0) {

            var url = $(this).attr('href');

            show_confirm({
                msg: '¿Desea aprobar la solicitud?',
                callbackOK: function () {
                    window.location.href = url;
                }
            });
        }
        else {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "No puede aceptar la solicitud si existen renglones desaprobados."
            });

            show_alert(options);
        }

        e.stopPropagation();
    });


    // BOTON DESAPROBAR SOLICITUD
    $('.link-desaprobar-solicitud').click(function (e) {

        e.preventDefault();

        show_confirm({
            msg: '¿Desea desaprobar la solicitud?',
            callbackOK: function () {
                $('form[name=form]').submit();
            }
        });
    });

    // BOTON ANULAR SOLICITUD
    $('.link-anular-solicitud').click(function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea anular la solicitud?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });

    // BOTON CORREGIR SOLICITUD
    $('.link-corregir-solicitud').click(function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea que la solicitud sea corregida por el usuario?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });

    // BOTON ENVIAR SOLICITUD
    $('.link-enviar-solicitud').click(function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea enviar la solicitud?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });


    // BOTON VISAR SOLICITUD
    $('.link-visar-solicitud').click(function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea visar la solicitud?',
            callbackOK: function () {
                window.location.href = url;
            }
        });

        e.stopPropagation();
    });
}

function initBotonEditarDescripcionRenglon()
{
	$('.editar_renglon_sc').on('click', function(e){
		
		e.preventDefault();
		
		var $tdDescripcion = $(this).parent().parent().find('.renglon_descripcion');
		_d = $tdDescripcion;
		var descripcion = $tdDescripcion.text().trim();
		
		var renglon_id = $(this).attr('renglon_id');
		
		//_d = $(this);
		console.debug(descripcion);
		//return false;
		
		var modal = ' <div class="row">';
		modal += '<div class="col-md-12">';
		modal += '<div class="form-group">';
		modal += '<label class="control-label">Descripci&oacute;n rengl&oacute;n</label>';
		modal += '<div class="input-group col-md-12">';
		modal += '<div class="input-icon right">';
		modal += '<i class="fa" data-original-title=""></i>';
		modal += '<textarea class="form-control" id="txt_renglon_descripcion">' + descripcion + '</textarea>';
		modal += '</div>'; // cierra input-icon right
		modal += '</div>'; // cierra input-group col-md-12
		modal += '</div>'; // cierra form-group
		modal += '</div>'; // cierra col-md-12
		modal += '</div>'; // cierra row
	
	
		show_dialog({
			titulo: 'Editar descripci&oacute;n',
			contenido: modal,
			labelCancel: 'Cancelar',
			closeButton: false,
			className: 'frm_editar_descripcion_renglon',
			callbackCancel: function () {

				return;
			},
			callbackSuccess: function () {
				
				// Armo el data
				var data = {
					id: renglon_id,
					descripcion: $('#txt_renglon_descripcion').val().trim()
				};
				
				console.debug(data);
				
				$.ajax({
					type: 'post',
					url: __AJAX_PATH__ + 'renglonsolicitudcompra/editar_descripcion',
					data: data,
					success: function(response) {
						
						if (response.status == 'nok') {
							
							var options = $.extend({
								title: 'Ha ocurrido un error',
								msg: response.mensaje,
								type: 'error'
							});

							show_alert(options);
							
							return;
						}
						
						showFlashMessage('success', response.mensaje);
						if (response.descripcion == '') {
							response.descripcion = '-';
						}
						$tdDescripcion.html( response.descripcion );
						
						
						return;
					}
				});
				
				
			}
		});


            
        
		
		
	});
}
