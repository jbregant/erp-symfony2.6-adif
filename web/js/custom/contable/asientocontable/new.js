

var _formulario_asiento = $('form[name ^="adif_contablebundle_"][name $="asientocontable"]');

var _asiento_contable_validator = null;

var _cuenta_contable_select = $('#renglon_asiento_cuentaContable');

var _operacion_contable_select = $('#renglon_asiento_operacionContable');

var isEdit = $('[name=_method]').length > 0;

var _tipo_moneda_select = $('#renglon_asiento_tipoMoneda');

var _add_renglon_asiento_clicked = false;

var $diferencia = 0;

var json = {
    renglones_asiento_contable: []
};

var _renglon_asiento_required_dependency = function (element) {
    return _add_renglon_asiento_clicked;
};

/**
 * 
 */
jQuery(document).ready(function () {

    initValidate();

    initSubmitButton();

    initGuardarGenerarModeloButton();

    initCargarModeloButton();

    updateTotales();

    initAddRenglonAsientoContableButton();

    initRemoveRenglonAsientoContableButton();

    initEditarRenglonAsientoContableButton();

    initGuardarCambiosRenglonAsiento();

    initCancelarCambiosRenglonAsiento();

    if (isEdit) {
        initEditarAsientoContable();
    }

});
/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Prevenir enter submit
    $(document).ready(function () {
        $('form[name=adif_contablebundle_asientocontable]').keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });

    // Validacion del Formulario
    _asiento_contable_validator = _formulario_asiento.validate();

    // Init rules
    if ($('#row_renglon_asiento').length > 0) {
        $("#renglon_asiento_cuentaContable").rules('add', {required: _renglon_asiento_required_dependency});
        //$("#renglon_asiento_operacionContable").rules('add', {required: _renglon_asiento_required_dependency});
        $("#renglon_asiento_tipoMoneda").rules('add', {required: _renglon_asiento_required_dependency});
        $("#renglon_asiento_importeMO").rules('add', {required: _renglon_asiento_required_dependency});
        $("#renglon_asiento_detalle").rules('add', {required: _renglon_asiento_required_dependency});
    }
    else {
        $('#row_detalle_asiento').addClass('cleardiv');
    }

    // Config fecha asiento contable    
    $('#adif_contablebundle_asientocontable_fechaContable')
            .datepicker('setStartDate', fechaMesCerradoSuperior);

    $('#adif_contablebundle_asientocontable_fechaContable')
            .datepicker('setEndDate', getCurrentDate());
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('button[ id ^= "adif_contablebundle_"][ id $= "asientocontable_submit"]').on('click', function (e) {

        if (_formulario_asiento.valid()) {

            e.preventDefault();
            show_confirm({
                msg: '¿Desea guardar el asiento?',
                callbackOK: function () {

                    if (validForm()) {

                        // Agrega JSON data antes del submit
                        _formulario_asiento.addHiddenInputData(json);
                        _formulario_asiento.submit();
                    }
                }
            });
            e.stopPropagation();
            return false;
        }

        return false;
    });
}

/**
 * 
 * @returns {undefined}
 */
function initGuardarGenerarModeloButton() {

    // Handler para el boton "Guardar"
    $('#guardar_generar_modelo_button').on('click', function (e) {
        if (_formulario_asiento.valid()) {
            e.preventDefault();
            show_confirm({
                msg: '¿Desea guardar el asiento y generar el modelo?',
                callbackOK: function () {
                    if (validForm()) {
                        json['copiar_modelo'] = 'true';
                        // Agrega JSON data antes del submit
                        _formulario_asiento.addHiddenInputData(json);
                        _formulario_asiento.submit();
                    }
                }
            });
            e.stopPropagation();
            return false;
        }

        return false;
    });
}

/**
 * 
 * @returns {undefined}
 */
function validForm() {

    return validarRenglones() && validarBalanceo();
}

/**
 * 
 * @returns {undefined}
 */
function validarRenglones() {

    // Si el [Modelo]AsientoContable tiene renglones cargados
    if ($('#renglon_asiento_contable_table tbody td').length > 0) {

        return true;
    }
    else {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe completar el detalle del asiento."
        });
        show_alert(options);
        return false;
    }
}

/**
 * 
 * @returns {undefined}
 */
function validarBalanceo() {

    // Si el [Modelo]AsientoContable está balanceado
    if ($diferencia.toString() === '0') {

        return true;
    }
    else {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "La diferencia del asiento debe ser igual a cero."
        });
        show_alert(options);
        return false;
    }

}

/**
 * 
 * @returns {undefined}
 */
function initCargarModeloButton() {

    $('#cargar_modelo_asiento').on('click', function (e) {

        e.preventDefault();
        if ($('#adif_contablebundle_asientocontable_modeloAsientoContable').val()) {

            var data = {
                id: $('#adif_contablebundle_asientocontable_modeloAsientoContable').val()
            };
            $.ajax({
                type: "POST",
                data: data,
                url: __AJAX_PATH__ + 'modelos_asiento_contable/renglones'
            }).done(function (response) {

                updateConceptoAsientoContable(response.idConceptoAsientoContable);
                jQuery.each(response.renglones, function (index, renglonModeloAsientoContable) {

                    var $accionEliminar = '';
                    if (permiteAjuste === '1') {

                        $accionEliminar =
                                '<a class="btn default btn-xs red tooltips remove_renglon_asiento" \n\
                                data-original-title="Eliminar" >\n\
                                    <i class="fa fa-trash-o"></i>\n\
                                </a>';
                    }

                    $('#renglon_asiento_contable_table tbody').append(
                            '<tr tr_index="' + index + '">\n\
                            <td ' + (renglonModeloAsientoContable['operacionContable'] === 'Haber' ? 'class="td-tipo-operacion-debe"' : '') + '>' + renglonModeloAsientoContable['cuentaContable'] + '</td>\n\
                            <td class="text-right td-debe' + (renglonModeloAsientoContable['operacionContable'] === 'Debe' ? ' money-format' : '') + '">' + (renglonModeloAsientoContable['operacionContable'] === 'Debe' ? renglonModeloAsientoContable['importeMO'] : '') + '</td>\n\
                            <td class="text-right td-haber' + (renglonModeloAsientoContable['operacionContable'] === 'Haber' ? ' money-format' : '') + '">' + (renglonModeloAsientoContable['operacionContable'] === 'Haber' ? renglonModeloAsientoContable['importeMO'] : '') + '</td>\n\
                            <td class="td-detalle">' + renglonModeloAsientoContable['detalle'] + '</td>\n\
                            <td class="td-acciones-asiento">\n\
                                <a class="btn btn-xs green tooltips editar_renglon_asiento"\n\
                                   data-original-title="Editar" >\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>\n\
                                    ' + $accionEliminar + '\n\
                            </td>\n\
                        </tr>');
                    json.renglones_asiento_contable.push({
                        'trIndex': index,
                        'id': '',
                        'idCuentaContable': renglonModeloAsientoContable['idCuentaContable'],
                        'idOperacionContable': renglonModeloAsientoContable['idOperacionContable'],
                        'idTipoMoneda': renglonModeloAsientoContable['idTipoMoneda'],
                        'importeMO': renglonModeloAsientoContable['importeMO'],
                        'detalle': renglonModeloAsientoContable['detalle']
                    });
                });
                updateTotales();
            });
        }
    });
}

/**
 * 
 * @param {type} renglonesOriginales
 * @returns {undefined}
 */
function addRenglonAsientoToRequest(renglonesOriginales) {

    jQuery.each(renglonesOriginales, function (i, renglon) {

        json.renglones_asiento_contable.push({
            'trIndex': i + 1,
            'id': renglon['id'],
            'idCuentaContable': renglon['idCuentaContable'],
            'idOperacionContable': renglon['idOperacionContable'],
            'idTipoMoneda': renglon['idTipoMoneda'],
            'importeMO': renglon['importeMO'],
            'detalle': renglon['detalle']
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initAddRenglonAsientoContableButton() {

    $('#add_renglon_asiento').on('click', function (e) {

        _add_renglon_asiento_clicked = true;
        e.preventDefault();
        removeErrorClasses();
        if (!validateRenglonAsientoContableForm()) {
            _add_renglon_asiento_clicked = false;
            return false;
        }

        addRenglonAsientoContableATabla();

        updateTotales();

        resetRenglonAsientoContableForm();

        removeErrorClasses();

        _add_renglon_asiento_clicked = false;

        $('#renglon_asiento_cuentaContable').select2('open');
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRemoveRenglonAsientoContableButton() {
    $(document).on('click', '.remove_renglon_asiento', function (e) {
        e.preventDefault();
        var trIndex = $(this).parents('tr').attr('tr_index');
        jQuery.each(json.renglones_asiento_contable, function (i, renglonAsientoContable) {

            if (typeof renglonAsientoContable !== 'undefined' && trIndex == renglonAsientoContable['trIndex']) {

                return false;
            }
        });
        $(this).parents('tr').remove();
        //json.renglones_asiento_contable.splice(trIndex - 1, 1);
        json.renglones_asiento_contable = eliminarRenglon(json.renglones_asiento_contable, trIndex);
        updateTotales();
    });
}

/**
 * 
 * @returns {undefined}
 */
function removeErrorClasses() {

    $("#renglon_asiento_cuentaContable, #renglon_asiento_tipoMoneda, #renglon_asiento_importeMO", '#renglon_asiento_detalle').each(function () {
        $(this).closest('.form-group').removeClass('has-success has-error');
    });
}

/**
 * 
 * @returns {unresolved}
 */
function validateRenglonAsientoContableForm() {

    return  _asiento_contable_validator.element("#renglon_asiento_cuentaContable")
            //& _asiento_contable_validator.element("#renglon_asiento_operacionContable")
            & _asiento_contable_validator.element("#renglon_asiento_tipoMoneda")
            & _asiento_contable_validator.element("#renglon_asiento_importeMO");
}

/**
 * 
 * @returns {undefined}
 */
function resetRenglonAsientoContableForm() {

    $('#row_renglon_asiento input[type=text]').val('');
    $('#row_renglon_asiento select').each(function () {
        $(this).find('option')
                .removeAttr('selected')
                .first().attr('selected', 'selected');
        $(this).select2();
    });
}

/**
 * 
 * @returns {undefined}
 */
function addRenglonAsientoContableATabla() {

    var idCuentaContable = $('#renglon_asiento_cuentaContable').val();
    var cuentaContable = $('#renglon_asiento_cuentaContable option:selected').text();
    var idOperacionContable = $('input[name=renglon_asiento_operacionContable]:checked').val();
    var operacionContable = $('input[name=renglon_asiento_operacionContable]:checked').attr('text');
    var idTipoMoneda = $('#renglon_asiento_tipoMoneda').val();
    var tipoMoneda = $('#renglon_asiento_tipoMoneda option:selected').val();
    var importeMO = $('#renglon_asiento_importeMO').val();
    var detalle = $('#renglon_asiento_detalle').val();
    var indiceNuevo = $('#renglon_asiento_contable_table tbody tr').length + 1;
    var $accionEliminar = '';

    if (permiteAjuste === '1') {

        $accionEliminar =
                '<a class="btn default btn-xs red tooltips remove_renglon_asiento" \n\
                                data-original-title="Eliminar" >\n\
                                    <i class="fa fa-trash-o"></i>\n\
                                </a>';
    }

    $('#renglon_asiento_contable_table tbody').append(
            '<tr tr_index="' + indiceNuevo + '">\n\
                <td ' + (operacionContable === 'Haber' ? 'class="td-tipo-operacion-debe"' : '') + '>' + cuentaContable + '</td>\n\
                <td class="text-right td-debe money-format' + (operacionContable === 'Debe' ? ' money-format' : '') + '">' + (operacionContable === 'Debe' ? importeMO : '') + '</td>\n\
                <td class="text-right td-haber money-format' + (operacionContable === 'Haber' ? ' money-format' : '') + '">' + (operacionContable === 'Haber' ? importeMO : '') + '</td>\n\
                <td class="td-detalle">' + detalle + '</td>\n\
                <td class="td-acciones-asiento">\n\
                    <a class="btn btn-xs green tooltips editar_renglon_asiento"\n\
                       data-original-title="Editar" >\n\
                        <i class="fa fa-pencil"></i>\n\
                    </a>\n\
                    ' + $accionEliminar + '\n\
                </td>\n\
            </tr>');

    json.renglones_asiento_contable.push({
        'trIndex': indiceNuevo,
        'id': '',
        'idCuentaContable': idCuentaContable,
        'idOperacionContable': idOperacionContable,
        'idTipoMoneda': idTipoMoneda,
        'importeMO': importeMO,
        'detalle': detalle
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateTotales() {

    // Actualizo el total del Debe
    var $totalDebe = 0;
    $('#renglon_asiento_contable_table').find('.td-debe').each(function () {

        $valorDebe = $(this).text().trim() === '' ? '0' : $(this).text().trim().split('.').join('').split(',').join('.');
        $totalDebe += parseFloat($valorDebe);
    });
    $('.total-debe').text($totalDebe.toFixed(2).replace('.', ','));
    // Actualizo el total del Haber
    var $totalHaber = 0;
    $('#renglon_asiento_contable_table').find('.td-haber').each(function () {

        $valorHaber = $(this).text().trim() === '' ? '0' : $(this).text().trim().split('.').join('').split(',').join('.');
        $totalHaber += parseFloat($valorHaber);
    });
    $('.total-haber').text($totalHaber.toFixed(2).replace('.', ','));
    // Inicializo las diferencias
    $('.total-diferencia-debe').text('');
    $('.total-diferencia-debe').removeClass('td-error');
    $('.total-diferencia-haber').text('');
    $('.total-diferencia-haber').removeClass('td-error');

    var $totalHaberFixed = parseFloat($totalHaber.toFixed(3));
    var $totalDebeFixed = parseFloat($totalDebe.toFixed(3));

    // Actualizo diferencia
    if ($totalHaberFixed > $totalDebeFixed) {

        $diferencia = parseFloat($totalDebeFixed - $totalHaberFixed);

        $('.total-diferencia-debe').text($diferencia.toFixed(2).replace('.', ','));
        $('.total-diferencia-debe').addClass('td-error');
    }
    else {

        $diferencia = parseFloat($totalHaberFixed - $totalDebeFixed);

        $('.total-diferencia-haber').text($diferencia.toFixed(2).replace('.', ','));

        if ($diferencia != 0) {
            $('.total-diferencia-haber').addClass('td-error');
        }
    }

    setMasks();
}


/**
 * 
 * @returns {undefined}
 */
function initEditarRenglonAsientoContableButton() {

    $(document).on('click', '.editar_renglon_asiento', function (e) {

        e.preventDefault();
        // Obtengo el TR del RenglonAsientoContable clickeado
        var nRow = $(this).parents('tr')[0];
        var tdHaber = $(nRow).find('.td-haber');
        var tdDebe = $(nRow).find('.td-debe');
        var tdDetalle = $(nRow).find('.td-detalle');
        if (tdHaber.html().trim() != '') {
            crearCampoEditable(tdHaber, 'editable-haber', 'currency');
        }
        if (tdDebe.html().trim() != '') {
            crearCampoEditable(tdDebe, 'editable-debe', 'currency');
        }
        crearCampoEditable(tdDetalle, 'editable-detalle', '');
        initCurrencies();
        $(nRow).find('.td-acciones-asiento').html('<a class="btn btn-xs blue tooltips guardar_cambios_renglon_asiento" data-original-title="Confirmar" >\n\
                                                <i class="fa fa-check"></i>\n\
                                           </a>\n\
                                           <a class="btn btn-xs red-thunderbird tooltips cancelar_cambios_renglon_asiento"data-original-title="Cancelar" >\n\
                                                <i class="fa fa-times"></i>\n\
                                           </a>');
    });
}

/**
 * 
 * @param {type} tdObject
 * @param {type} clase
 * @param {type} tipo
 * @returns {undefined}
 */
function crearCampoEditable(tdObject, clase, tipo) {

    tdObject.html('<input type="text" style="width:100%;" class="input ' + clase + ' ' + tipo + '" value="' + tdObject.html().trim().split('.').join('').split(',').join('.') + '">');
}

function initGuardarCambiosRenglonAsiento() {

    $(document).on('click', '.guardar_cambios_renglon_asiento', function (e) {

        e.preventDefault();
        var trIndex = $(this).parents('tr').attr('tr_index');
        var nRow = $(this).parents('tr')[0];

        var $accionEliminar = '';

        if (permiteAjuste === '1') {

            $accionEliminar =
                    '<a class="btn default btn-xs red tooltips remove_renglon_asiento" \n\
                                data-original-title="Eliminar" >\n\
                                    <i class="fa fa-trash-o"></i>\n\
                                </a>';
        }

        jQuery.each(json.renglones_asiento_contable, function (i, renglonAsientoContable) {

            if (typeof renglonAsientoContable !== 'undefined' && trIndex == renglonAsientoContable['trIndex']) {

                if ($(nRow).find('.editable-haber').size() > 0) {
                    json.renglones_asiento_contable[i]['importeMO'] = $(nRow).find('.editable-haber').val();
                    $(nRow).find('.td-haber').html($(nRow).find('.editable-haber').val());
                    $(nRow).find('.td-haber').addClass('money-format');
                    $(nRow).find('.td-debe').html('');
                } else {
                    json.renglones_asiento_contable[i]['importeMO'] = $(nRow).find('.editable-debe').val();
                    $(nRow).find('.td-debe').html($(nRow).find('.editable-debe').val());
                    $(nRow).find('.td-debe').addClass('money-format');
                    $(nRow).find('.td-haber').html('');
                }

                json.renglones_asiento_contable[i]['detalle'] = $(nRow).find('.editable-detalle').val();
                $(nRow).find('.td-detalle').html($(nRow).find('.editable-detalle').val());
                $(nRow).find('.td-acciones-asiento').html('<a class="btn btn-xs green tooltips editar_renglon_asiento"data-original-title="Editar" >\n\
                                                        <i class="fa fa-pencil"></i>\n\
                                                   </a>\n\
                                                   ' + $accionEliminar);
                return false;
            }
        });
        updateTotales();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initCancelarCambiosRenglonAsiento() {

    $(document).on('click', '.cancelar_cambios_renglon_asiento', function (e) {

        e.preventDefault();

        var trIndex = $(this).parents('tr').attr('tr_index');

        var nRow = $(this).parents('tr')[0];

        var $accionEliminar = '';

        if (permiteAjuste === '1') {

            $accionEliminar =
                    '<a class="btn default btn-xs red tooltips remove_renglon_asiento" \n\
                    data-original-title="Eliminar" >\n\
                        <i class="fa fa-trash-o"></i>\n\
                    </a>';
        }

        jQuery.each(json.renglones_asiento_contable, function (i, renglonAsientoContable) {

            if (typeof renglonAsientoContable !== 'undefined' && trIndex == renglonAsientoContable['trIndex']) {

                if ($(nRow).find('.td-debe').html().trim() == '') {
                    $(nRow).find('.td-haber').html(json.renglones_asiento_contable[i]['importeMO']);
                    $(nRow).find('.td-haber').addClass('money-format');
                    $(nRow).find('.td-debe').html('');
                }
                else {
                    $(nRow).find('.td-debe').html(json.renglones_asiento_contable[i]['importeMO']);
                    $(nRow).find('.td-debe').addClass('money-format');
                    $(nRow).find('.td-haber').html('');
                }

                $(nRow).find('.td-detalle').html(json.renglones_asiento_contable[i]['detalle']);

                $(nRow).find('.td-acciones-asiento').html('<a class="btn btn-xs green tooltips editar_renglon_asiento"data-original-title="Editar" >\n\
                                                <i class="fa fa-pencil"></i>\n\
                                           </a>\n\
                                           ' + $accionEliminar);
                return false;
            }
        });
        updateTotales();
    });
}

/**
 * 
 * @param {type} idConceptoAsientoContable
 * @returns {undefined}
 */
function updateConceptoAsientoContable(idConceptoAsientoContable) {

    $('#adif_contablebundle_asientocontable_conceptoAsientoContable')
            .val(idConceptoAsientoContable).select2();
}

/**
 * 
 * @returns {undefined}
 */
function initEditarAsientoContable() {

}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric({vMin: '-999999999999.99', aSep: '.', aDec: ',', vMax: '999999999999.99'});
        $(this).autoNumeric('update', {aSep: '.', aDec: ','});
        ;
    });
}

function eliminarRenglon(arreglo_renglones, trIndex){
    var arregloResultado = [];
    for(var i = 0; i < arreglo_renglones.length; i++){
        if(arreglo_renglones[i]['trIndex'] != trIndex){
            arregloResultado.push(arreglo_renglones[i]);
        }
    }
    return arregloResultado;
}