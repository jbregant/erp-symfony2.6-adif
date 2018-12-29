//@todo
var motivoRechazoStandard = "La solicitud no ha superado el proceso de evaluación de la gerencia.";

$('#btn_rechazar_gerencia').on('click', function (e) {

    e.preventDefault();

    let observacionesFlag = false;

    $("[id$=_observaciones]").each(function () {
        if($(this).val()){
            observacionesFlag = true;
        }
    });

    if (observacionesFlag) {
        let body_aprobacion = '<div>' +
            'Se descartarán las observaciones ingresadas, ¿desea continuar?' +
            '</div>';
        show_dialog({
            titulo: 'Aprobar Interesado',
            contenido: body_aprobacion,
            'labelSuccess': 'Aceptar',
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                $("[id$=_observaciones]").each(function () {
                    $(this).val('');
                });
                formulario_motivo_rechazo = '<form name="form-motivo_rechazo">\n\
                                  <label class="control-label required" for="motivo_rechazo">Ingrese el motivo del rechazo para el solicitante</label>\n\
                                  <div class="form-group">\n\
                                    <div class="input-icon right">\n\
                                        <i class="fa"></i>\n\
                                        <textarea id="motivo_rechazo" name="motivo_rechazo" required="required" class="form-control">' + motivoRechazoStandard + '</textarea>\n\
                                    </div>\n\
                                  </div>\n\
                                  <label class="control-label required" for="motivo_rechazo">Ingrese el motivo de rechazo interno</label>\n\
                                  <div class="form-group">\n\
                                    <div class="input-icon right">\n\
                                        <i class="fa"></i>\n\
                                        <textarea id="motivo_rechazo_interno" name="motivo_rechazo_interno" required="required" class="form-control">' + motivoRechazoStandard + '</textarea>\n\
                                    </div>\n\
                                  </div>\n\
                                  </form>';
                show_dialog({
                    titulo: 'Rechazar Interesado',
                    contenido: formulario_motivo_rechazo,
                    'labelSuccess': 'Enviar',
                    callbackCancel: function () {
                        desbloquear();
                    },
                    callbackSuccess: function () {
                        var formulario = $('form[name=form-motivo_rechazo]').validate();
                        var formulario_result = formulario.form();
                        if (formulario_result) {

                            $('<input>').attr({
                                type: 'hidden',
                                name: 'motivo_rechazo',
                                value: $('#motivo_rechazo').val()
                            }).appendTo($('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]'));

                            $('<input>').attr({
                                type: 'hidden',
                                name: 'motivo_rechazo_interno',
                                value: $('#motivo_rechazo_interno').val()
                            }).appendTo($('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]'));

                            $('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]').submit();

                        } else {
                            return false;
                        }
                    }
                });
            }
        });

    } else {

        formulario_motivo_rechazo = '<form name="form-motivo_rechazo">\n\
                                  <label class="control-label required" for="motivo_rechazo">Ingrese el motivo del rechazo para el solicitante</label>\n\
                                  <div class="form-group">\n\
                                    <div class="input-icon right">\n\
                                        <i class="fa"></i>\n\
                                        <textarea id="motivo_rechazo" name="motivo_rechazo" required="required" class="form-control">' + motivoRechazoStandard + '</textarea>\n\
                                    </div>\n\
                                  </div>\n\
                                  <label class="control-label required" for="motivo_rechazo">Ingrese el motivo de rechazo interno</label>\n\
                                  <div class="form-group">\n\
                                    <div class="input-icon right">\n\
                                        <i class="fa"></i>\n\
                                        <textarea id="motivo_rechazo_interno" name="motivo_rechazo_interno" required="required" class="form-control">' + motivoRechazoStandard + '</textarea>\n\
                                    </div>\n\
                                  </div>\n\
                                  </form>';
        show_dialog({
            titulo: 'Rechazar Interesado',
            contenido: formulario_motivo_rechazo,
            'labelSuccess': 'Enviar',
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=form-motivo_rechazo]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'motivo_rechazo',
                        value: $('#motivo_rechazo').val()
                    }).appendTo($('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]'));

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'motivo_rechazo_interno',
                        value: $('#motivo_rechazo_interno').val()
                    }).appendTo($('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]'));

                    $('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]').submit();

                } else {
                    return false;
                }
            }
        });
    }
});

$('#adif_portalProveedoresBundle_proveedorEvaluacion_aprobar').on('click', function (e) {

    e.preventDefault();

    let observacionesFlag = false;

    $("[id$=_observaciones]").each(function () {
        if($(this).val()){
            observacionesFlag = true;
        }
    });

    if (observacionesFlag){
        let body_aprobacion = '<div>' +
            'Se descartarán las observaciones ingresadas, ¿desea continuar?' +
            '</div>';
        show_dialog({
            titulo: 'Aprobar Interesado',
            contenido: body_aprobacion,
            'labelSuccess': 'Aceptar',
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                $("[id$=_observaciones]").each(function () {
                    $(this).val('');
                });
                $('<input>').attr({
                    type: 'hidden',
                    name: 'adif_portalProveedoresBundle_proveedorEvaluacion[aprobar]',
                    value: 'Aprobar'
                }).appendTo($('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]'));

                $('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]').submit();
            }
        });
    } else {
        $('<input>').attr({
            type: 'hidden',
            name: 'adif_portalProveedoresBundle_proveedorEvaluacion[aprobar]',
            value: 'Aprobar'
        }).appendTo($('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]'));

        $('form[name="adif_portalProveedoresBundle_proveedorEvaluacion"]').submit();
    }
});