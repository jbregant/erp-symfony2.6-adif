var _empleado_tiposContrato_count;
var _empleado_obrasSociales_count;
var _empleado_tiposContrato_historial = $('#empleado_tiposContrato_historial');

$(document).ready(function() {
    _empleado_tiposContrato_count = $('#adif_recursoshumanosbundle_empleado_tiposContrato_count');
//    _empleado_tiposLicencia_count = $('#adif_recursoshumanosbundle_empleado_tiposLicencia_count');
    _empleado_obrasSociales_count = $('#adif_recursoshumanosbundle_empleado_obrasSociales_count');
    
    // Iniciar el validador
    getEmpleadoValidator();
    updateNombreEmpleado();

    _empleado_tiposContrato_count.rules('add', { 
        required: true,
        min: 1,
        messages: {
            required: "Ingrese al menos un tipo de contrato",
            min: "Ingrese al menos un tipo de contrato"
        }
    });
    
    _empleado_obrasSociales_count.rules('add', { 
        required: true,
        min: 1,
        messages: {
            required: "Ingrese al menos una obra social",
            min: "Ingrese al menos una obra social"
        }
    });
    restrictDates(false);
    $('#adif_recursoshumanosbundle_empleado_condicion').rules('remove', 'required');

    $(document).on('click', '.tipo_contrato_borrar', function(e) {
        $(this).parents('.row_tipo_contrato').remove();
        $('.row_headers_tipo_contrato').css('display', $('.row_tipo_contrato').length == 0 ? 'none' : 'block');
        _empleado_tiposContrato_count.val(parseInt(_empleado_tiposContrato_count.val())-1);
    });

    $(document).on('click', '.tipo_licencia_borrar', function(e) {
        $(this).parents('.row_tipo_licencia').remove();
        $('.row_headers_tipo_licencia').css('display', $('.row_tipo_licencia').length == 0 ? 'none' : 'block');
    });

    $(document).on('click', '.obra_social_borrar', function(e) {
        $(this).parents('.row_obra_social').remove();
        $('.row_headers_obra_social').css('display', $('.row_obra_social').length == 0 ? 'none' : 'block');
        _empleado_obrasSociales_count.val(parseInt(_empleado_obrasSociales_count.val())-1);
    });

    $('.tipo_contrato_agregar').on('click', function(e) {
        e.preventDefault();
        
        if($('input[name^="adif_recursoshumanosbundle_empleado\[tiposContrato\]"][name$="\[fechaHasta\]"]:last').val() != ''){
            var nuevo_row =
                $('.row_tipo_contrato_nuevo')
                .clone()
                .show()
                .removeClass('row_tipo_contrato_nuevo');

            nuevo_row.addClass('row_tipo_contrato');

            nuevo_row.find('[sname]').each(function() {
                $(this).attr('name', $(this).attr('sname'));
                $(this).removeAttr('sname');
            });

            nuevo_row.html(nuevo_row.html().replace(/__name__/g, $('.row_tipo_contrato').length));
            nuevo_row.appendTo('.ctn_rows_tipo_contrato');
            nuevo_row.find('select').select2();

            $('.row_headers_tipo_contrato').show();

            $(nuevo_row).find('.datepicker').each(function() {
                initDatepicker($(this));
            });

            restrictDates(true);

            _empleado_tiposContrato_count.val(parseInt(_empleado_tiposContrato_count.val())+1);
        } else {
            alert('El ' + _u_ACUTE + 'ltimo tipo de contrato debe tener una fecha de finalizaci' + _o_ACUTE + 'n');
        }        
    });

    $('.tipo_licencia_agregar').on('click', function(e) {
        e.preventDefault();

        var nuevo_row =
                $('.row_tipo_licencia_nuevo')
                .clone()
                .show()
                .removeClass('row_tipo_licencia_nuevo');

        nuevo_row.addClass('row_tipo_licencia');

        nuevo_row.find('[sname]').each(function() {
            $(this).attr('name', $(this).attr('sname'));
            $(this).removeAttr('sname');
        });

        nuevo_row.html(nuevo_row.html().replace(/__name__/g, $('.row_tipo_licencia').length));
        nuevo_row.appendTo('.ctn_rows_tipo_licencia');
        nuevo_row.find('select').select2();

        $('.row_headers_tipo_licencia').show();

        $(nuevo_row).find('.datepicker').each(function() {
            initDatepicker($(this));
        });
    });

    $('.obra_social_agregar').on('click', function(e) {
        e.preventDefault();

        var nuevo_row =
                $('.row_obra_social_nuevo')
                .clone()
                .show()
                .removeClass('row_obra_social_nuevo');

        nuevo_row.addClass('row_obra_social');

        nuevo_row.find('[sname]').each(function() {
            $(this).attr('name', $(this).attr('sname'));
            $(this).removeAttr('sname');
        });

        nuevo_row.html(nuevo_row.html().replace(/__name__/g, $('.row_obra_social').length));
        nuevo_row.appendTo('.ctn_rows_obra_social');
        nuevo_row.find('select').select2();

        $('.row_headers_obra_social').show();

        $(nuevo_row).find('.datepicker').each(function() {
            initDatepicker($(this));            
        });
        
        _empleado_obrasSociales_count.val(parseInt(_empleado_obrasSociales_count.val())+1);
    });
    
    _empleado_tiposContrato_historial.on('click', function(){
        var b = bootbox.dialog({
            title: 'Historial de contrataci&oacute;n',
            message: $('#ctn_historial_contrataciones').html()
        });
        b.find('.modal-dialog').css('width', '70%');
    });
});


function restrictDates(setStartDate){
    if ($('input[name^="adif_recursoshumanosbundle_empleado\[tiposContrato\]"][name$="\[fechaDesde\]"]').length > 1){
        var minDate = $('input[name^="adif_recursoshumanosbundle_empleado\[tiposContrato\]"][name$="\[fechaHasta\]"]:eq(-2)').datepicker('getDate');
        $('input[name^="adif_recursoshumanosbundle_empleado\[tiposContrato\]"][name$="\[fechaDesde\]"]:last').datepicker('setStartDate', minDate);
        if(setStartDate){
            $('input[name^="adif_recursoshumanosbundle_empleado\[tiposContrato\]"][name$="\[fechaDesde\]"]:last').datepicker('setDate', minDate);
        }
    }
    
    $('input[name^="adif_recursoshumanosbundle_empleado\[tiposContrato\]"][name$="\[fechaHasta\]"]').each(function(){
       var name_fecha_desde = $(this).attr('name').replace('fechaHasta', 'fechaDesde');
       var minDate = $('input[name="' + name_fecha_desde + '"]').datepicker('getDate');
       $(this).datepicker('setStartDate', minDate);
   });
}