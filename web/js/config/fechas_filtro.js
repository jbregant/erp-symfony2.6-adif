$(document).ready(function () {
    initFechasFiltro(); 
});

/**
 * 
 * @returns {undefined}
 */
function initFechasFiltro() {
    var url = $(location).attr('href');
    
    var $fechaInicioLS = localStorage.getItem(url + '_fechaInicio');
    var $fechaFinLS = localStorage.getItem(url + '_fechaFin');
    
    var $fechaInicio = ($fechaInicioLS === null) ? getFirstDateOfCurrentMonth(ejercicioContableSesion) : new Date($fechaInicioLS.substring(6, 10), $fechaInicioLS.substring(3, 5) - 1, $fechaInicioLS.substring(0, 2));
    var $fechaFin = ($fechaFinLS === null) ? getEndingDateOfCurrentMonth(ejercicioContableSesion) : new Date($fechaFinLS.substring(6, 10), $fechaFinLS.substring(3, 5) - 1, $fechaFinLS.substring(0, 2));

    $('#adif_contablebundle_filtro_fechaInicio').datepicker("setDate", $fechaInicio);
    $('#adif_contablebundle_filtro_fechaFin').datepicker("setDate", $fechaFin);
}

function setFechasFiltro(fechaInicio, fechaFin){
    var url = $(location).attr('href');
    
    localStorage.setItem(url + '_fechaInicio', fechaInicio);
    localStorage.setItem(url + '_fechaFin', fechaFin);
}