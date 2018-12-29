$(document).ready(function () {
    initValidate();
    
    initDatepicker($('#adif_contablebundle_declaracionjuradaivacontribuyente_fechaInicioDatepicker'), {
        format: "MM yyyy",
        viewMode: "months",
        minViewMode: "months"
    });
    
    $('#adif_contablebundle_declaracionjuradaivacontribuyente_fechaInicioDatepicker').on('change', function(){
        var anio = $('#adif_contablebundle_declaracionjuradaivacontribuyente_fechaInicioDatepicker').datepicker('getDate').getFullYear();
        var mes = ($('#adif_contablebundle_declaracionjuradaivacontribuyente_fechaInicioDatepicker').datepicker('getDate').getMonth() + 1).toString();
        if(mes.length == 1){
            mes = '0' + mes;
        }
        var fecha_inicio = '01/' + mes + '/' + anio;
        $('#adif_contablebundle_declaracionjuradaivacontribuyente_fechaInicio').val(fecha_inicio);
    });
    
    if(fechaInicioPeriodo != ''){
        $('#adif_contablebundle_declaracionjuradaivacontribuyente_fechaInicioDatepicker').datepicker('setDate', new Date(fechaInicioPeriodo)).trigger('change');
    }
});
    

/**
 * 
 * @returns {undefined}
 */
function initValidate() {
    // Validacion del Formulario
    $('form[name=adif_contablebundle_declaracionjuradaivacontribuyente]').validate();
}
