
$(document).ready(function () {

    initTablaComprobantes();

    initShowDetalleImporteLink();

});

/**
 * 
 * @returns {undefined}
 */
function initTablaComprobantes() {

    var options = {
        "searching": false,
        "ordering": false,
        "info": false,
        "paging": true,
        "pageLength": 12,
        "lengthMenu": [[12, 24, 48, 100, -1], [12, 24, 48, 100, "Todos"]]
    };

    dt_init($('.table-comprobantes'), options);
}

/**
 * 
 * @returns {undefined}
 */
function initShowDetalleImporteLink() {

    $('.link-detalle-importe').click(function () {

        $i = $(this).find('i');

        if ($i.hasClass('fa-plus')) {
            $(this).parents('tr').nextAll(':lt(2)').removeClass('hidden');

            $i.removeClass('fa-plus');
            $i.addClass('fa-minus');
        }
        else {
            $(this).parents('tr').nextAll(':lt(2)').addClass('hidden');

            $i.addClass('fa-plus');
            $i.removeClass('fa-minus');
        }

    });
}
