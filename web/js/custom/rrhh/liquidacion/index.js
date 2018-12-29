var dt_liquidacion;

$(document).ready(function () {
//    dt_init(table);
//    table.DataTable().order().draw();

    
    dt_liquidacion = dt_datatable($('#table-liquidacion'), {
        'order': [[3, 'desc']]
    });
   
    $(document).on('click', '.form_netcash', function (e) {
        e.preventDefault();
        bloquear();
        var a_href = $(this).attr('href');
        form_netcash = '<form id="form_netcash" method="post" name="form_netcash">\n\
                                            <div class="row">\n\
                                                <div class="col-md-6">\n\
                                                    <div class="form-group">\n\
                                                        <label class="control-label required" for="fecha_bbva">Fecha procesamiento BBVA</label>\n\
                                                        <div class="input-icon right">\n\
                                                            <i class="fa"></i>\n\
                                                            <input type="text" required="required" id="fecha_bbva" name="fecha_bbva" class="form-control datepicker">\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                                <div class="col-md-6">\n\
                                                    <div class="form-group">\n\
                                                        <label class="control-label required" for="fecha_otros">Fecha procesamiento Otros Bancos</label>\n\
                                                        <div class="input-icon right">\n\
                                                            <i class="fa"></i>\n\
                                                            <input type="text" required="required" id="fecha_otros" name="fecha_otro" class="form-control datepicker">\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                            <div class="row">\n\
                                                <div class="col-md-6">\n\
                                                    <div class="form-group">\n\
                                                        <label class="control-label required" for="fecha_vencimiento_bbva">Fecha vencimiento BBVA</label>\n\
                                                        <div class="input-icon right">\n\
                                                            <i class="fa"></i>\n\
                                                            <input type="text" required="required" id="fecha_vencimiento_bbva" name="fecha_vencimiento_bbva" class="form-control datepicker">\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                                <div class="col-md-6">\n\
                                                    <div class="form-group">\n\
                                                        <label class="control-label required" for="fecha_vencimiento_otros">Fecha vencimiento Otros Bancos</label>\n\
                                                        <div class="input-icon right">\n\
                                                            <i class="fa"></i>\n\
                                                            <input type="text" required="required" id="fecha_vencimiento_otros" name="fecha_vencimiento_otros" class="form-control datepicker">\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                        </form>';
        show_dialog({
            titulo: 'Exportar Netcash',
            contenido: form_netcash,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=form_netcash]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {


                    var options = {
                        fechabbva: $('#fecha_bbva').val(),
                        fechaotros: $('#fecha_otros').val(),
                        fechavencimientobbva: $('#fecha_vencimiento_bbva').val(),
                        fechavencimientootros: $('#fecha_vencimiento_otros').val()
                    };

                    open_window('POST', a_href, options, '_blank');

                } else {
                    return false;
                }
            }
        });

        
        $('.bootbox').removeAttr('tabindex');
        initDatepickers();
        $('#fecha_bbva').datepicker("setDate", new Date());
        $('#fecha_otros').datepicker("setDate", new Date());
        
        desbloquear();
        
        e.stopPropagation();
    });
        
    //  ACCIONES DE LOS REGISTROS
    $('body').tooltip({
        selector: '.tooltips'
    });

    $('body').popover({
        selector: '.btn-group-popover',
        placement: 'left',
        title: 'Opciones',
        html: true,
        content: function () {
            return $(this).parent().find('.ctn_opciones_hidden').html();
        },
        trigger: 'focus',
        template: '<div class="popover table-actions-popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>'
    });
    
});