$(document).ready(function () {
    $(document).on('click', '#checkbox-detalle', function () {
        if ($('#checkbox-detalle:checked').size() > 0) {
            $('.ocultable').removeClass('hidden');
        } else {
            $('.ocultable').addClass('hidden');
        }
    });

//    $(document).on('click', '.mostrar-oc', function (e) {
//        e.preventDefault();
//        var id = $(this).prop('id');
//        var div_tabla = $('#detalle-oc-' + id);
//        show_alert({title: 'Detalle de la Orden de compra', msg: div_tabla.html()});
//    });

    $(document).on('click', '.mostrar-tramo', function (e) {
        e.preventDefault();
        var id = $(this).prop('id');
        var div_tabla = $('#detalle-tramo-' + id);
        show_alert({title: 'Detalle del Tramo', msg: div_tabla.html()});
    });

    $('#adif_contablebundle_filtro_fechaFin').datepicker("setDate", new Date());

    initFiltroButton();

    $('#filtrar_cc').click();

});

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#filtrar_cc').on('click', function (e) {
        filtrarCuentaCorriente();
    });
}

/**
 * 
 * @returns {undefined}
 */
function filtrarCuentaCorriente() {
    var $idProveedor = $('#adif_contablebundle_filtro_idProveedor').val();
    var $fecha = $('#adif_contablebundle_filtro_fechaFin').val().trim();

    if ($idProveedor && $fecha) {
        var data = {
            idProveedor: $idProveedor,
            fecha: $fecha
        };

        $('#detalle_cc_container').html('');
        $('#label_saldo_total').text('-');
        
        $.ajax({
            type: "POST",
            data: data,
            url: __AJAX_PATH__ + 'proveedor/filtrar_cuentacorrientedetalletotal/'
        }).done(function (detalleCuentaCorriente) {
            $('#detalle_cc_container').html(detalleCuentaCorriente);
            dt_datatable($('#cuenta_corriente_proveedor'));
            $('.btn-clear-filters').remove();
            $('.dataTables_info').remove();
            $('#label_saldo_total').text($('#saldoTotal').val());
        });
    } else {
        show_alert({
            title: 'Error',
            msg: 'Debe especificar una fecha para el reporte',
            type: 'error'
        });
    }
}
