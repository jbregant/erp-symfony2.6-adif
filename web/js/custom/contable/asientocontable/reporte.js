
/**
 * 
 * @returns {undefined}
 */
function initFiltro() {

    var fechaInicio = getFirstDateOfCurrentMonth(ejercicioContableSesion);

//    var fechaInicioEjercicioUsuarioDate = getDateFromString('01/01/' + ejercicioContableSesion);

    var fechaFin = getEndingDateOfCurrentMonth(ejercicioContableSesion);

//    var fechaFinEjercicioUsuarioDate = getDateFromString('31/12/' + ejercicioContableSesion);

    $('#adif_contablebundle_filtro_fechaInicio').datepicker("setDate", fechaInicio);

//    $('#adif_contablebundle_filtro_fechaInicio').datepicker('setStartDate', fechaInicioEjercicioUsuarioDate);

//    $('#adif_contablebundle_filtro_fechaInicio').datepicker('setEndDate', fechaFinEjercicioUsuarioDate);


    $('#adif_contablebundle_filtro_fechaFin').datepicker("setDate", fechaFin);

//    $('#adif_contablebundle_filtro_fechaFin').datepicker('setStartDate', fechaInicioEjercicioUsuarioDate);

//    $('#adif_contablebundle_filtro_fechaFin').datepicker('setEndDate', fechaFinEjercicioUsuarioDate);

//    customInitFiltro();
}

/**
 * 
 * @returns {undefined}
 */
function customInitFiltro() {

    return true;
}
