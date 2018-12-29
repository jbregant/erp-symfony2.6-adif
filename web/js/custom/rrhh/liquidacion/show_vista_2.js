bloquear();
$(document).ready(function() {
    desbloquear();

    // Setear suma de netos
    var sumaNetos = 0;
    _.each($('.table-le .total_neto'), function(td) {
        sumaNetos += parseFloat($(td).text().replace(".", "").replace(".","").replace(",", "."));
    });

    $('#total_neto').text($('<h>' + sumaNetos.toFixed(2) + '</h>').priceFormat({
        prefix: '',
        centsSeparator: ',',
        thousandsSeparator: '.'
    }).text());

    $('#select-buscar-empleado').on('change', function() {
        if ($(this).val() === null) {
            $('[id_empleado]').show();
            return;
        }

        $('[id_empleado]').hide();
        _.each($(this).val(), function(id_empleado) {
            $('[id_empleado=' + id_empleado + ']').show();
        });
    })

    $('#btn-cerrar-liquidacion').on('click', function() {
        $.ajax({
            url: __AJAX_PATH__ + 'liquidaciones/liquidacion/form_cerrar',
            type: 'POST'
        }).done(function(rHTML) {
            show_dialog({
                titulo: 'Cierre de liquidaci&oacute;n',
                contenido: rHTML,
                callbackCancel: function() {
                    return;
                },
                callbackSuccess: function() {
                    $('#fecha_ultimo_aporte').val($('#fecha_ultimo_aporte_dp').datepicker('getDate').getFullYear() + '-' + ($('#fecha_ultimo_aporte_dp').datepicker('getDate').getMonth() + 1) + '-' + '01');
                    if ($('#form_liquidacion_cierre').valid()) {
                        $('#form_liquidacion_cierre').submit();
                    } else {
                        return false;
                    }
                }
            });

            $('#form_liquidacion_cierre').parents('.modal-dialog').css('width', '60%');

            // BANCOS
            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'bancos/lista_bancos',
                success: function(data) {
                    for (var i = 0, total = data.length; i < total; i++) {
                        $('#banco_aporte').append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                    }
                    $('#banco_aporte').select2();
                }
            });


            $('#form_liquidacion_cierre .datepicker:not(#fecha_ultimo_aporte_dp)').each(function() {
                initDatepicker($(this));
            })

            initDatepicker($('#fecha_ultimo_aporte_dp'), {
                format: "MM yyyy",
                viewMode: "months",
                minViewMode: "months"
            });
        });
    });

    $('.btn-imprimir-empleado').on('click', function() {
        var le = $(this).parents('.table-le')
        var printIcon = $(this)
        
        bloquear();

        printIcon.hide()
        le.css({ 'font-size': '18pt' });

        html2canvas(le, {
            onrendered: function(canvas) {

                var myImage = canvas.toDataURL("image/png");

                le.css({ 'font-size': ''});

                var tWindow = window.open("");
                $(tWindow.document.body).html("<img id='Image' src=" + myImage + " style='width:100%;'></img>").ready(function() {
                    tWindow.focus();
                    tWindow.print();
                    tWindow.close();

                    printIcon.show();

                    desbloquear();
                });
            }
        });
    });
    
    $('#imprimir_recibos_sueldos_session').on('click', function() {
        
        $.ajax({
            url: __AJAX_PATH__ + 'liquidaciones/liquidacion/form_imprimir_recibos_session',
            type: 'POST'
        }).done(function(rHTML) {
            show_dialog({
                titulo: 'Imprimir recibos',
                contenido: rHTML,
                callbackCancel: function() {
                    return;
                },
                callbackSuccess: function() {
                    if ($('#form_imprimir_recibos_session').valid()) {
                        $('#form_imprimir_recibos_session').submit();
                        desbloquear();
                    } else {
                        return false;
                    }
                }
            });
            
        }); 
    });
    
});