
var cuerpo_novedades = $('.cuerpo-novedades').html();

var oTable = $('#table-novedades');

$('.cuerpo-novedades').remove();

$('.datatable .edit').each(function() {
    editarNovedad($(this));
});

$('.datatable .delete').each(function() {
    borrarNovedad($(this));
});

$(document).on("change", "#adif_recursoshumanosbundle_empleado_novedades_idConcepto", function() {
    porcentajeValor(novedades[$('#adif_recursoshumanosbundle_empleado_novedades_idConcepto').val()]);
    if($('#adif_recursoshumanosbundle_empleado_novedades_idConcepto option:selected').attr('es-ajuste') == 1){
        $('#row_ajuste').show();
        $('#adif_recursoshumanosbundle_empleado_novedades_dias').addClass('required number');
        $('#adif_recursoshumanosbundle_empleado_novedades_liquidacionAjuste').addClass('required');
    } else {
        $('#row_ajuste').hide();
        $('#adif_recursoshumanosbundle_empleado_novedades_dias').removeClass('number');
        $('#adif_recursoshumanosbundle_empleado_novedades_liquidacionAjuste,#adif_recursoshumanosbundle_empleado_novedades_dias').removeClass('required');
    }
});

// Agregar Novedad
$('#agregar_novedad').off().on('click', function() {
    show_dialog({
        titulo: 'Agregar novedad a <b>'+ empleado+'</b>',
        contenido: cuerpo_novedades,
        callbackCancel: function() {
        },
        callbackSuccess: function() {
            var formulario = $('form[name=form_novedades]').validate();
            var formulario_result = formulario.form();
            if (formulario_result) {
                $.ajax({
                    type: "POST",
                    data: $('#form_novedades').serialize(),
                    url: pathNovedades
                }).done(function(result) {

                    if (result != '') {

                        msg = result.split('|');

                        var ai = oTable.dataTable().fnAddData([
                            msg[0], // ID
                            msg[1], // Checkbox
                            msg[2], // Concepto
                            msg[3], // Fecha
                            parseFloat(msg[5]).toFixed(2).replace('.', ',') + msg[4], // Valor
                            msg[6], // Acciones
                        ]);

                        var tr = oTable.dataTable().fnSettings().aoData[ ai[0] ].nTr;

                        $(tr).find('td').first().addClass('text-center');
                        $(tr).find('td').first().next().next().addClass('fechaAlta');
                        $(tr).find('td').first().next().next().next().addClass('valor');
                        $(tr).find('td').last().addClass('ctn_acciones text-center nowrap');

                        $('.datepicker').datepicker('destroy');

                        borrarNovedad($(tr).find('.delete'));
                        editarNovedad($(tr).find('.edit'));

                        $(tr).find('.edit').tooltip();
                        $(tr).find('.delete').tooltip();

                        initHackCheckbox($(tr).find('div.checker'));
                    }
                });
            } else {
                return false;
            }
        }
    });
    
    $('.bootbox').removeAttr('tabindex');

    initSelects();

    initDatepickers();

    $('#adif_recursoshumanosbundle_empleado_novedades_valor').inputmask("decimal", {radixPoint: ",", digits: 2});
});

// Editar Novedad
function editarNovedad($button) {

    $button.on('click', function(e) {
        e.preventDefault();
        $element = $(this);
        show_dialog({
            titulo: 'Editar Novedad de ' + empleado,
            contenido: cuerpo_novedades,
            callbackCancel: function() {
            },
            callbackSuccess: function() {
                var formulario = $('form[name=form_novedades]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {
                    $.ajax({
                        type: "POST",
                        data: $('#form_novedades').serialize(),
                        url: $($element).prop('href')
                    }).done(function(result) {
                        if (result != '') {
                            msg = result.split('|');

                            oTable.dataTable().fnDeleteRow($($element).closest('tr[role=row]'));

                            var ai = oTable.dataTable().fnAddData([
                                msg[0], // ID
                                msg[1], // Checkbox
                                msg[2], // Concepto
                                msg[3], // Fecha
                                parseFloat(msg[5]).toFixed(2).replace('.', ',') + msg[4], // Valor
                                msg[6], // Acciones
                            ]);

                            var tr = oTable.dataTable().fnSettings().aoData[ ai[0] ].nTr;

                            $(tr).find('td').first().addClass('text-center');
                            $(tr).find('td').first().next().next().addClass('fechaAlta');
                            $(tr).find('td').first().next().next().next().addClass('valor');
                            $(tr).find('td').last().addClass('ctn_acciones text-center nowrap');

                            $('.datepicker').datepicker('destroy');

                            borrarNovedad($(tr).find('.delete'));

                            editarNovedad($(tr).find('.edit'));

                            $(tr).find('.edit').tooltip();
                            $(tr).find('.delete').tooltip();

                            initHackCheckbox($(tr).find('div.checker'));
                        }
                    });
                } else {
                    return false;
                }
            }
        });

        $('#adif_recursoshumanosbundle_empleado_novedades_idConcepto').remove();
        $('#form_novedades .row').first().find('.form-group').html('<label>'+$($element).closest('tr[role=row]').find('.novedad').html()+'</label>');
        $('#adif_recursoshumanosbundle_empleado_novedades_fechaAlta').val($($element).closest('tr[role=row]').find('.fechaAlta').html());
        $('#adif_recursoshumanosbundle_empleado_novedades_valor').val($($element).closest('tr[role=row]').find('.valor').html().replace(' %', ''));
        porcentajeValor($($element).closest('tr[role=row]').find('.valor').html().indexOf('%') != -1);
        $('#adif_recursoshumanosbundle_empleado_novedades_valor').inputmask("decimal", {radixPoint: ",", digits: 2});

        initDatepickers();
    });
}


// Borrar Novedad
function borrarNovedad($boton) {

    $boton.on('click', function(e) {
        e.preventDefault();
        $element = $(this);
        show_confirm({
            msg: 'Confirma el borrado del elemento?',
            callbackOK: function() {
                $.ajax({
                    type: "POST",
                    data: "",
                    url: $($element).prop('href')
                }).done(function(result) {
                    if (result == 'ok') {
                        oTable.dataTable().fnDeleteRow($($element).closest('tr[role=row]'));
                    }
                }).fail(function() {
                    // Alertar Error
                });
            }
        });
    });
}

// Define si una novedad es porcentaje o valor
function porcentajeValor(boolean) {
    if (boolean) {
        $('#label_valor').html('Porcentaje');
        $('#adif_recursoshumanosbundle_empleado_novedades_valor').closest('div').removeClass('input');
        $('#adif_recursoshumanosbundle_empleado_novedades_valor').closest('div').addClass('input-group');
        $('#adif_recursoshumanosbundle_empleado_novedades_valor').closest('div').append('<span class="input-group-addon">%</span>');
    } else {
            $('#label_valor').html('Valor');
            $('#adif_recursoshumanosbundle_empleado_novedades_valor').closest('div').removeClass('input-group');
            $('#adif_recursoshumanosbundle_empleado_novedades_valor').closest('div').addClass('input');
            $('#adif_recursoshumanosbundle_empleado_novedades_valor').closest('div').find('.input-group-addon').remove();
    }
}