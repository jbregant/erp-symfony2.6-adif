$(document).ready(function(){
    _form = $('form[name=adif_portalproveedoresbundle_notificacion]');
    _fechaDesde = $('#adif_portalproveedoresbundle_notificacion_fechaDesde');
    _fechaHasta = $('#adif_portalproveedoresbundle_notificacion_fechaHasta');
    _submit = $('#adif_portalproveedoresbundle_notificacion_submit');
    _notificacion = $('#notificacion');
    _estadoNotificacion = $('#s2id_adif_portalproveedoresbundle_notificacion_estadoNotificacion');


    initFiltroDatepicker(_fechaDesde, _fechaHasta);

    _submit.click(function(e) {
        if (_form.valid()) {
            e.preventDefault();

            show_confirm({
                msg: '¿Desea enviar la notificaci&oacute;n?',
                callbackOK: function () {
                    _form.submit();
                }
            });
            return false;
        }
    });

    if(editar){
        _notificacion.removeClass('hidden'); // remuevo la clase hidden para que sea visible
        _estadoNotificacion.removeClass('hidden'); // remuevo la clase hidden para que sea visible
        // _fechaDesde.attr('readonly','readonly').off(); // desactivo los datepicker cuando le dan click
        // _fechaHasta.attr('readonly','readonly').off(); // desactivo los datepicker cuando le dan click
        if(obtenerFechaActual() > _fechaHasta.datepicker('getDate')){
            _estadoNotificacion.select2('disable'); // deshabilito el select2
            _submit.attr('disabled','disabled'); // deshabilito el boton de submit
        }
    }
    
});

function initFiltroDatepicker(desde, hasta){
    // inicializo el input fechaHasta para que no pueda seleccionar una fecha anterior a la de inicio
    $(hasta).datepicker('setStartDate', $(desde).val()); 
    // Resticcion de fechas parar el filtro
    $(desde).change(function(){
        var min = $(desde).val()
        $(hasta).datepicker('setStartDate', min);
    });
    $(hasta).change(function(){
        var max = $(hasta).val()
        $(desde).datepicker('setEndDate', max);
    });
}

function obtenerFechaActual(){

    date = new Date();
    mes = date.getMonth()+1;
    dia = date.getDate();
    anio = date.getFullYear();
    fecha_actual = anio + ',' + mes + ',' + dia;
    
    return new Date(fecha_actual);  
}