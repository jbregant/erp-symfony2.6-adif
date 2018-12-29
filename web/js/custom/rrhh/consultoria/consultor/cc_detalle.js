$(document).ready(function () {
    $('#checkbox-detalle').on('click', function () {
        if ($('#checkbox-detalle:checked').size() > 0) {
            $('.ocultable').removeClass('hidden');
        } else {
            $('.ocultable').addClass('hidden');
        }
    });

    $('.btn-clear-filters').remove();
    $('.dataTables_info').remove();

    $('.mostrar-contrato').on('click', function (e) {
        e.preventDefault();
        var id = $(this).prop('id');
        var div_tabla = $('#detalle-contrato-' + id);
        show_alert({title: 'Detalle del Contrato', msg: div_tabla.html()});
    });
});
