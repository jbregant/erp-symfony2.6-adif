var c_originales = [];

$(document).ready(function() {
    
    initCheckChecked();
    
    // Check todos los preseleccionados    
    var rows = $(".datatable").dataTable().fnGetNodes();
    $(rows).each(function() {
        $(this).find('input[type=checkbox]').each(function() {
        // $(this).find('div.checker').find('input[type=checkbox]').each(function() {
            if ($(this).is(':checked')) {
                $(this).closest('tr').addClass('active');
                c_originales.push($(this).val());
            }
        });
    });
    
    $(".datatable").DataTable().order([2, "asc"]).draw();

    // Confirmación de Conceptos
    $('#adif_recursoshumanosbundle_empleado_conceptos_submit').on('click', function(e) {
        e.preventDefault();
        
        var c_borrados = [];
        var c_agregados = [];
        
        $('[name=adif_recursoshumanosbundle_empleado_conceptos\\[conceptos\\]\\[\\]]').each(function(i, check){
            if (!$(check).is(':checked') && ($.inArray(($(check).val()), c_originales) !== -1)){
                // BORRADO
                c_borrados.push($(check).val());
            } else if ($(check).is(':checked') && ($.inArray(($(check).val()), c_originales) === -1 )) {
                // AGREGADO
                c_agregados.push($(check).val());
            }
        });
        
        var rows = $(".datatable").dataTable().fnGetNodes();
        var contenido = 
            '<div class="ctn-columns-3"><ul class="">\
                <li class="alert-info"><i class="fa fa-letter">=</i> Sin modificar</li>\n\
                <li class="alert-success"><i class="fa fa-plus"></i> Agregado</li>\n\
                <li class="alert-danger"><i class="fa fa-minus"></i> Borrado</li>\n\
            </ul></div>';
        contenido += '<div class="ctn-columns"><ul>';
        $(rows).each(function() {
//            if ($(this).hasClass('active')) {
                var cls = $.inArray(($(this).find('td input[type=checkbox]').val()), c_borrados) !== -1 ? 'alert-danger' : 
                        ($.inArray(($(this).find('td input[type=checkbox]').val()), c_agregados) !== -1 ? 'alert-success' :                       ($.inArray(($(this).find('td input[type=checkbox]').val()), c_originales) !== -1 ? 'alert-info' : ''));
                contenido += '<li class="'+cls+'"><i class="fa fa-'+
                        (cls == 'alert-danger' ? 'minus">' : 
                        (cls == 'alert-success' ? 'plus">' : 
                        (cls == 'alert-info' ? 'letter">=' : '">'))) +'</i> ' + $(this).find('td').last().html() + '</li>';
//            }
        });
        contenido += '</ul></div>';
        var d = show_dialog({
            titulo: 'Se asignarán los siguientes conceptos a ' + empleado + '. ¿Desea continuar?',
            contenido: contenido,
            callbackCancel: function() {
            },
            callbackSuccess: function() {
                var actual = $('.datatable tbody').html();
                $('.datatable tbody').html($(".datatable").dataTable().fnGetNodes());
                $('#adif_recursoshumanosbundle_empleado_conceptos').submit();
                $('.datatable tbody').html(actual);
            }
        });
        
        d.find('.modal-dialog').css('width', '70%');
        
        e.stopPropagation();
    });
});