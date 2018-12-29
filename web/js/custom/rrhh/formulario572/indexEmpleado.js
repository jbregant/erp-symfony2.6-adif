$(document).ready(function () {

    initTable();

});

/**
 * 
 * @returns {undefined}
 */
function initTable() {

    dt_init($('#table-formulario572'));

    $('#table-formulario572').DataTable().order([4, 'desc']).draw();
}