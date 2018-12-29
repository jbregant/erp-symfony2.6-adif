
jQuery(document).ready(function () {

    $('#table_comprobantes_compra_info').remove();

    $('.ver-regimenes-aplicados').on('click', function (e) {
        e.preventDefault();
        if ($(this).attr('regimen') == 'anticipo') {
            $('.alicuota').addClass('hidden');
        } else {
            $('.alicuota').removeClass('hidden');
        }
        if ($(this).hasClass('divide_ute')) {
            $('.ute').removeClass('hidden');
        } else {
            $('.ute').addClass('hidden');
        }
        $('.col-regimenes-aplicados .nombre_retencion').text($(this).attr('regimen-original'));
        $('.col-regimenes-aplicados').show();
        $('.col-regimenes-aplicados table tbody tr').hide();
        $('.col-regimenes-aplicados table tr[regimen=' + $(this).attr('regimen') + ']').show();
        if ($('.col-regimenes-aplicados table tr[regimen=' + $(this).attr('regimen') + '][class="divide_ute"]').length > 0) {
            $('.ute').removeClass('hidden');
        } else {
            $('.ute').addClass('hidden');
        }
        e.stopPropagation();
    });

    mostrarPopUpComprobante();
	
	$('#submit_goto_comprobantes').on('click', function(){
		$('#hidden_submit_goto_comprobantes').val(1);
		$('form[name="adif_contablebundle_ordenpagoobra"]').submit();
	});
	
	if ($('#btn_bajar_log_retencion').length == 1) {
		$('#btn_bajar_log_retencion').on('click', function(e) {
			e.preventDefault();
			var href = $(this).prop('href');
			//open_window(verb, url, data, target)
			open_window('POST', href, null, '_self');
			e.stopPropagation();
		});
	}
});


function mostrarPopUpComprobante() {

    $('a.show_comprobante_link').click(function () {

        $(this).colorbox({
            iframe: true,
            fastIframe: false,
            width: "90%",
            height: '200px',
            onComplete: function (e) {
                var iframe = $('#cboxLoadedContent iframe');

                iframe.load(function () {
                    initIframe(iframe, 45);
                }).trigger('load');
            }
        });
    });
}