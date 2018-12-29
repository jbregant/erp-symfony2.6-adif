
/**
 * 
 */
jQuery(document).ready(function () {

    dt_init($("#table-logger"));

    $("#table-logger").DataTable().order([2, 'desc']).draw();

    initEllipsis();
});