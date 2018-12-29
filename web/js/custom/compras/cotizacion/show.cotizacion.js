
var oTableAdicionales = $('#table-adicionales-cotizacion');
var oTableCotizaciones = $('#table-cotizaciones');

var adicional_form = $('.adicional_form_content').html();
var renglon_cotizacion_form = $('.renglon_cotizacion_form_content').html();

var collectionHolderArchivos;

var archivosEliminadosArray = [];

/**
 * 
 */
jQuery(document).ready(function () {

    $('.adicional_form_content').remove();
    $('.renglon_cotizacion_form_content').remove();

    updateDeleteLinks($(".prototype-link-remove-archivo"));

    initTables();

    updateMasks();

    updatePrecioTotalCotizacion();

    updatePrecioTotalAdicionales();

    initLinks();

    initArchivosForm();

    configPreSubmit();
});

/**
 * 
 * @returns {undefined}
 */
function initTables() {
    dt_init(oTableAdicionales);
    dt_init(oTableCotizaciones);
}

/**
 * 
 * @returns {undefined}
 */
function initLinks() {

    initEditarCotizacionLink();

    initAgregarAdicionalLink();
    initEditarAdicionalLink();
    initEliminarAdicionalLink();
}

/**
 * 
 * @returns {undefined}
 */
function initAgregarAdicionalLink() {

    $('.agregar-adicional').off().on('click', function (e) {

        e.preventDefault();

        show_dialog({
            titulo: 'Agregar adicional',
            contenido: adicional_form,
            callbackCancel: function () {
            },
            callbackSuccess: function () {

                var formulario = $('form[name=adif_comprasbundle_adicionalcotizacion]');

                var formularioValido = formulario.validate().form();

                // Si el formulario es válido
                if (formularioValido) {

                    var checkbox = '<div class="checker">'
                            + '<span>'
                            + '<input type="checkbox" class="checkboxes" value="">'
                            + '</span>'
                            + '</div>';

                    var idTipoAdicional = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoAdicional')
                            .val();

                    var tipoAdicional = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoAdicional')
                            .select2('data').text;

                    var idSigno = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_signo').val();

                    var signo = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_signo')
                            .select2('data').text;

                    var idTipoValor = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoValor').val();

                    var valor = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_valor').val();

                    var idAlicuotaIva = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_alicuotaIva').val();

                    var alicuotaIva = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_alicuotaIva')
                            .select2('data').text;

                    var idTipoMoneda = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoMoneda').val();

                    var simboloTipoMoneda = $('form[name=adif_comprasbundle_adicionalcotizacion]')
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoMoneda')
                            .find('option:selected').data('simbolo-tipo-moneda');

                    var tipoCambio = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoCambio').val();

                    var observacion = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_observacion').val();

                    var acciones =
                            '<a class="btn btn-xs green tooltips editar_adicional" '
                            + 'data-original-title="Editar" href="#">'
                            + '<i class="fa fa-pencil"></i></a>&nbsp;'
                            + '<a class="btn btn-xs red tooltips eliminar_adicional" '
                            + 'data-original-title="Eliminar" href="#">'
                            + '<i class="fa fa-times"></i></a>';

                    var newRow = oTableAdicionales.DataTable().row.add([
                        null, // ID Adicional
                        checkbox, // Checkbox
                        idTipoAdicional, // ID Tipo Adicional
                        tipoAdicional, // Text Tipo Adicional
                        idSigno, // ID Signo
                        signo, // Signo
                        idTipoValor, // ID Tipo Valor
                        valor, // Valor
                        idAlicuotaIva, // ID Alicuota IVA
                        alicuotaIva, // Alicuota IVA
                        idTipoMoneda, // ID Tipo Moneda
                        tipoCambio, // Tipo Cambio
                        observacion, // Observacion
                        acciones // Acciones
                    ]).draw().node();

                    $(newRow).data('simbolo-tipo-moneda', simboloTipoMoneda);

                    // Oculto el TD del ID del Adicional
                    $(newRow).find('td').eq(0).addClass('hidden');

                    // Agrego estilos al TD del checkbox 
                    $(newRow).find('td').eq(1).addClass('text-center');

                    // Oculto el TD del ID del Tipo Adicional
                    $(newRow).find('td').eq(2).addClass('hidden');

                    // Oculto el TD del ID del Signo
                    $(newRow).find('td').eq(4).addClass('hidden');

                    // Oculto el TD del ID del Tipo Valor
                    $(newRow).find('td').eq(6).addClass('hidden');

                    // Agrego la clase al TD de Valor
                    $(newRow).find('td').eq(7).addClass('monto-adicional');

                    // Oculto el TD del ID de la Alicuota IVA
                    $(newRow).find('td').eq(8).addClass('hidden');

                    // Oculto el TD del ID del Tipo Moneda
                    $(newRow).find('td').eq(10).addClass('hidden');

                    // Oculto el TD del Tipo Cambio
                    $(newRow).find('td').eq(11).addClass('hidden');

                    // Le agrego estilos al TD de Acciones
                    $(newRow).find('td').last().addClass('ctn_acciones text-center nowrap');

                    initHackCheckbox();

                    updatePrecioTotalAdicionales();

                    initEditarAdicionalLink();
                    initEliminarAdicionalLink();

                    initTooltip();
                } //.   
                else {
                    return false;
                }
            }
        });

        $('.bootbox').removeAttr('tabindex');

        initSelects();

        updateMasks();

        initTipoValorAdicionalSelect();

        initAltaTipoAdicionalLink();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initEditarAdicionalLink() {

    $('.editar_adicional').off().on('click', function (e) {

        e.preventDefault();

        // Obtengo el TR del Adicional clickeado
        var trAdicional = $(this).parents('tr');

        show_dialog({
            titulo: 'Editar adicional',
            contenido: adicional_form,
            callbackCancel: function () {
            },
            callbackSuccess: function () {
                var formulario = $('form[name=adif_comprasbundle_adicionalcotizacion]');

                var formularioValido = formulario.validate().form();

                // Si el formulario es válido
                if (formularioValido) {

                    var idTipoAdicional = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoAdicional')
                            .val();

                    var tipoAdicional = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoAdicional')
                            .select2('data').text;

                    var idSigno = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_signo').val();

                    var signo = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_signo')
                            .select2('data').text;

                    var tipoValor = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoValor').val();

                    var valor = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_valor').val();

                    var idAlicuotaIva = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_alicuotaIva').val();

                    var alicuotaIva = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_alicuotaIva')
                            .select2('data').text;

                    var idTipoMoneda = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoMoneda').val();

                    var simboloTipoMoneda = $('form[name=adif_comprasbundle_adicionalcotizacion]')
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoMoneda')
                            .find('option:selected').data('simbolo-tipo-moneda');

                    var tipoCambio = clearCurrencyValue(formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_tipoCambio').val());

                    var observacion = formulario
                            .find('#adif_comprasbundle_adicionalcotizacion_observacion').val();


                    trAdicional.data('simbolo-tipo-moneda', simboloTipoMoneda);

                    // Seteo el ID Tipo Adicional
                    trAdicional.find('td').eq(2).html(idTipoAdicional);

                    // Seteo el Tipo Adicional
                    trAdicional.find('td').eq(3).html(tipoAdicional);

                    // Seteo el ID Signo
                    trAdicional.find('td').eq(4).html(idSigno);

                    // Seteo el Signo
                    trAdicional.find('td').eq(5).html(signo);

                    // Seteo el Tipo Valor
                    trAdicional.find('td').eq(6).html(tipoValor);

                    // Seteo el Valor
                    trAdicional.find('td').eq(7).html(valor);

                    // Seteo el ID Alicuota IVA
                    trAdicional.find('td').eq(8).html(idAlicuotaIva);

                    // Seteo la Alicuota IVA
                    trAdicional.find('td').eq(9).html(alicuotaIva);

                    // Seteo el ID Tipo Moneda
                    trAdicional.find('td').eq(10).html(idTipoMoneda);

                    // Seteo el Tipo Cambio
                    trAdicional.find('td').eq(11).html(tipoCambio);

                    // Seteo la Observacion
                    trAdicional.find('td').eq(12).html(observacion);

                    updatePrecioTotalAdicionales();
                } //.   
                else {
                    return false;
                }
            }
        });

        // Inicializo los valores del modal
        var idTipoAdicional = trAdicional.find('td').eq(2).html();

        if (idTipoAdicional !== null && idTipoAdicional !== "") {
            $('#adif_comprasbundle_adicionalcotizacion_tipoAdicional')
                    .val(idTipoAdicional);
        }

        var idSigno = trAdicional.find('td').eq(4).html();

        if (idSigno !== null && idSigno !== "") {
            $('#adif_comprasbundle_adicionalcotizacion_signo')
                    .val(idSigno);
        }

        var idTipoValor = trAdicional.find('td').eq(6).html();

        if (idTipoValor !== null && idTipoValor !== "") {
            $('#adif_comprasbundle_adicionalcotizacion_tipoValor')
                    .val(idTipoValor);
        }

        var valor = trAdicional.find('td').eq(7).html();

        if (valor !== null && valor !== "-") {
            $('#adif_comprasbundle_adicionalcotizacion_valor').val(valor);
        }

        var idAlicuotaIVA = trAdicional.find('td').eq(8).html();

        if (idAlicuotaIVA !== null && idAlicuotaIVA !== "") {
            $('#adif_comprasbundle_adicionalcotizacion_alicuotaIva')
                    .val(idAlicuotaIVA);
        }

        var idTipoMoneda = trAdicional.find('td').eq(10).html();

        if (idTipoMoneda !== null && idTipoMoneda !== "") {
            $('#adif_comprasbundle_adicionalcotizacion_tipoMoneda')
                    .val(idTipoMoneda);
        }

        var tipoCambioAnterior = trAdicional.find('td').eq(11).html();

        if (tipoCambioAnterior !== null && tipoCambioAnterior !== "-") {
            $('#adif_comprasbundle_adicionalcotizacion_tipoCambio').val(tipoCambioAnterior.replace(/\./g, ','));
        }

        var observacion = trAdicional.find('td').eq(12).html();

        if (observacion !== null && observacion !== "-") {
            $('#adif_comprasbundle_adicionalcotizacion_observacion').val(observacion);
        }

        $('.bootbox').removeAttr('tabindex');

        initSelects();

        updateMasks();

        initTipoValorAdicionalSelect();

        // Actualizo el select de IVA de adicional segun correpsonda
        updateIVAAdicionalSelect();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initEliminarAdicionalLink() {

    $('.eliminar_adicional').off().on('click', function (e) {

        e.preventDefault();

        // Obtengo el TR de la Invitacion clickeado
        var trAdicional = $(this).parents('tr');

        show_confirm({
            msg: '¿Desea eliminar el adicional?',
            callbackOK: function () {

                // Elimino el Adicional de la tabla
                oTableAdicionales.DataTable().row(trAdicional).remove().draw();

                updatePrecioTotalAdicionales();
            }
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initAltaTipoAdicionalLink() {

    $('a.agregar_tipo_adicional').click(function () {

        var button = $(this);

        $(this).colorbox({
            iframe: true,
            fastIframe: false,
            fixed: true,
            top: '0',
            width: "90%",
            height: '200px',
            onOpen: function (e) {
                $('body').one('popup_closed', function (e, data) {

                    var nuevoTipoAdicionalId = data.id;

                    cargarTipoAdicional(button, nuevoTipoAdicionalId);

                    $.colorbox.close();
                });
            },
            onComplete: function (e) {

                var iframe = $('#cboxLoadedContent iframe');

                iframe.load(function () {
                    initIframe(iframe, 150);
                }).trigger('load');
            }
        });
    });
}

/**
 * 
 * @param {type} button
 * @param {type} nuevoTipoAdicionalId
 * @returns {undefined}
 */
function cargarTipoAdicional(button, nuevoTipoAdicionalId) {

    var $tipoAdicionalSelect = button.closest('.row')
            .find('select[id ^= adif_comprasbundle_adicionalcotizacion][id $= tipoAdicional]');

    resetSelect($tipoAdicionalSelect);

    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'tipoadicional/lista',
        success: function (data) {

            $tipoAdicionalSelect.select2('readonly', false);

            for (var i = 0, total = data.length; i < total; i++) {
                $tipoAdicionalSelect.append('<option value="' + data[i].id + '">' + data[i].denominacionAdicional + '</option>');
            }

            $tipoAdicionalSelect.val(nuevoTipoAdicionalId);
            $tipoAdicionalSelect.select2();
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initEditarCotizacionLink() {

    $(document).on('click', '.editar_renglon_cotizacion', function (e) {

        e.preventDefault();

        // Obtengo el TR del RenglonCotizacion clickeado
        var trRenglonCotizacion = $(this).parents('tr');

        show_dialog({
            titulo: 'Editar cotización',
            contenido: renglon_cotizacion_form,
            callbackCancel: function () {
            },
            callbackSuccess: function () {

                var formulario = $('form[name=form_renglon_cotizacion]').validate();

                var formularioValido = formulario.form();

                // Si el formulario es válido
                if (formularioValido) {

                    if (cantidadValida(trRenglonCotizacion)) {

                        var cantidad = $('form[name=form_renglon_cotizacion]')
                                .find('#adif_comprasbundle_rengloncotizacion_cantidad').val();

                        var precioUnitario = $('form[name=form_renglon_cotizacion]')
                                .find('#adif_comprasbundle_rengloncotizacion_precioUnitario').val();

                        var idAlicuotaIva = $('form[name=form_renglon_cotizacion]')
                                .find('#adif_comprasbundle_rengloncotizacion_alicuotaIva')
                                .find('option:selected').val();

                        var alicuotaIva = $('form[name=form_renglon_cotizacion]')
                                .find('#adif_comprasbundle_rengloncotizacion_alicuotaIva')
                                .find('option:selected').text();

                        var idTipoMoneda = $('form[name=form_renglon_cotizacion]')
                                .find('#adif_comprasbundle_rengloncotizacion_tipoMoneda')
                                .find('option:selected').val().trim();

                        var simboloTipoMoneda = $('form[name=form_renglon_cotizacion]')
                                .find('#adif_comprasbundle_rengloncotizacion_tipoMoneda')
                                .find('option:selected').data('simbolo-tipo-moneda');

                        var tipoCambio = clearCurrencyValue($('form[name=form_renglon_cotizacion]')
                                .find('#adif_comprasbundle_rengloncotizacion_tipoCambio').val());

                        var observacion = $('form[name=form_renglon_cotizacion]')
                                .find('#adif_comprasbundle_rengloncotizacion_observacion').val();


                        trRenglonCotizacion.data('simbolo-tipo-moneda', simboloTipoMoneda);

                        // Seteo la cantidad                    
                        trRenglonCotizacion.find('td').eq(5).html(cantidad);

                        // Seteo el precioUnitario
                        trRenglonCotizacion.find('td').eq(7).html(precioUnitario);

                        // Seteo la idAlicuotaIva
                        trRenglonCotizacion.find('td').eq(8).html(idAlicuotaIva);

                        // Seteo la alicuotaIva
                        trRenglonCotizacion.find('td').eq(9).html(alicuotaIva);

                        // Seteo la idTipoMoneda 
                        trRenglonCotizacion.find('td').eq(12).html(idTipoMoneda);

                        // Seteo el tipoCambio 
                        trRenglonCotizacion.find('td').eq(13).html(tipoCambio);

                        // Seteo la observacion
                        trRenglonCotizacion.find('td').eq(14).html(observacion);

                        updatePrecioTotalCotizacion();

                        var idRenglonRequerimiento = trRenglonCotizacion.find('td').eq(1).html();

                        validateDiferenciaEntreJustiprecioYCotizacion(idRenglonRequerimiento, precioUnitario);

                        updateMasks();
                    }
                    else {
                        return false;
                    }
                } //. 
                else {
                    return false;
                }

            }
        });

        updateMasks();

        initSelects();

        // Inicializo los valores del modal
        var cantidadAnterior = trRenglonCotizacion.find('td').eq(5).html();

        if (cantidadAnterior !== null && cantidadAnterior !== "0") {
            $('#adif_comprasbundle_rengloncotizacion_cantidad').val(cantidadAnterior);
        }

        var precioUnitarioAnterior = trRenglonCotizacion.find('td').eq(7).html();

        if (precioUnitarioAnterior !== null && precioUnitarioAnterior !== "-") {
            $('#adif_comprasbundle_rengloncotizacion_precioUnitario').val(precioUnitarioAnterior);
        }

        var alicuotaIvaAnterior = trRenglonCotizacion.find('td').eq(8).html();

        if (alicuotaIvaAnterior !== null && alicuotaIvaAnterior !== "0") {
            $('#adif_comprasbundle_rengloncotizacion_alicuotaIva').val(alicuotaIvaAnterior);
            $('#adif_comprasbundle_rengloncotizacion_alicuotaIva').select2();
        }

        var tipoMonedaAnterior = trRenglonCotizacion.find('td').eq(12).html();

        if (tipoMonedaAnterior !== null && tipoMonedaAnterior !== "0") {
            $('#adif_comprasbundle_rengloncotizacion_tipoMoneda').val(tipoMonedaAnterior);
            $('#adif_comprasbundle_rengloncotizacion_tipoMoneda').select2();
        }

        var tipoCambioAnterior = trRenglonCotizacion.find('td').eq(13).html();

        if (tipoCambioAnterior !== null && tipoCambioAnterior !== "-") {
            $('#adif_comprasbundle_rengloncotizacion_tipoCambio').val(tipoCambioAnterior.replace(/\./g, ','));
        }

        var observacionAnterior = trRenglonCotizacion.find('td').eq(14).html();

        if (observacionAnterior !== null && observacionAnterior !== "-") {
            $('#adif_comprasbundle_rengloncotizacion_observacion').val(observacionAnterior);
        }

    }
    );
}

/**
 * 
 * @param {type} trRenglonCotizacion
 * @returns {Boolean}
 */
function cantidadValida(trRenglonCotizacion) {

    var cantidadOriginal = trRenglonCotizacion.find('td').eq(4).html();

    var cantidadNueva = $('form[name=form_renglon_cotizacion]')
            .find('#adif_comprasbundle_rengloncotizacion_cantidad').val().replace(',', '.');

    var resultado = parseFloat(cantidadNueva) <= parseFloat(cantidadOriginal);
	if (!resultado) {
		showFlashMessage(
			'danger', 
			'La cantidad cotizada no puede ser mayor a ' + cantidadOriginal, 
			10000, 
			'#form_renglon_cotizacion_mensajes'
		);
	}
	
	return resultado;
	
}

/**
 * 
 * @returns {undefined}
 */
function configPreSubmit() {

    var saveClick = false;

    /**
     * Handler para el evento SUBMIT del formulario
     */
    $('form[name=adif_comprasbundle_cotizacion]').submit(function () {

        if ($(this).valid()) {

            var json = {
                adicionales: [],
                archivos_eliminados: archivosEliminadosArray,
                renglon_cotizacion: [],
                cotizacion: cotizacionId,
                fecha_cotizacion: $('#adif_comprasbundle_cotizacion_fecha_cotizacion').val(),
                fecha_invitacion: $('#adif_comprasbundle_cotizacion_fecha_invitacion').val()
            };

            // Obtengo todos los NODOS de la tabla de Adicionales
            var nodesAdicional = oTableAdicionales.DataTable().rows().nodes();

            if (nodesAdicional.flatten().length > 0) {

                // Por cada NODO en la tabla de Adicionales
                jQuery.each(nodesAdicional, function (key, rowAdicional) {

                    var dataAdicional = oTableAdicionales.DataTable()
                            .row(rowAdicional).node();

                    var idAdicional = $(dataAdicional)
                            .find("td").eq(0).html();

                    var tipoAdicional = $(dataAdicional)
                            .find("td").eq(2).html();

                    var signo = $(dataAdicional)
                            .find("td").eq(4).html();

                    var tipoValor = $(dataAdicional)
                            .find("td").eq(6).html();

                    var valor = $(dataAdicional)
                            .find("td").eq(7).html();

                    var alicuotaIva = $(dataAdicional)
                            .find("td").eq(8).html();

                    var idTipoMoneda = $(dataAdicional)
                            .find("td").eq(10).html();

                    var tipoCambio = $(dataAdicional)
                            .find("td").eq(11).html();

                    var observacion = $(dataAdicional)
                            .find("td").eq(12).html();

                    json.adicionales.push({
                        'id_adicional': idAdicional,
                        'id_tipo_adicional': tipoAdicional,
                        'signo': signo,
                        'tipo_valor': tipoValor,
                        'valor': clearCurrencyValue(valor),
                        'id_alicuota_iva': alicuotaIva,
                        'id_tipo_moneda': idTipoMoneda,
                        'tipo_cambio': tipoCambio,
                        'observacion': observacion
                    });
                });
            }

            // Obtengo todos los NODOS de la tabla de RenglonCotizacion
            var nodesRenglonCotizacion = oTableCotizaciones.DataTable().rows().nodes();

            if (nodesRenglonCotizacion.flatten().length > 0) {

                // Por cada NODO en la tabla de RenglonCotizacion
                jQuery.each(nodesRenglonCotizacion, function (key, rowRenglonCotizacion) {

                    var dataRenglonCotizacion = oTableCotizaciones.DataTable()
                            .row(rowRenglonCotizacion).node();

                    var idRenglonRequerimiento = clearFormatId($(dataRenglonCotizacion)
                            .find("td").eq(1).html());

                    var cantidad = $(dataRenglonCotizacion)
                            .find("td").eq(5).html();

                    var precioUnitario = $(dataRenglonCotizacion)
                            .find("td").eq(7).html();

                    var alicuotaIva = $(dataRenglonCotizacion)
                            .find("td").eq(8).html();

                    var tipoMoneda = $(dataRenglonCotizacion)
                            .find("td").eq(12).html();

                    var tipoCambio = $(dataRenglonCotizacion)
                            .find("td").eq(13).html();

                    var observacion = $(dataRenglonCotizacion)
                            .find("td").eq(14).html();

                    json.renglon_cotizacion.push({
                        'id_renglon_requerimiento': idRenglonRequerimiento,
                        'cantidad': clearCurrencyValue(cantidad),
                        'precioUnitario': clearCurrencyValue(precioUnitario),
                        'alicuotaIva': alicuotaIva,
                        'tipoMoneda': tipoMoneda,
                        'tipoCambio': tipoCambio,
                        'observacion': observacion
                    });

                });
            }

            // Agrega JSON data antes del submit
            $('form[name=adif_comprasbundle_cotizacion]').addHiddenInputData(json);
        }

        return;
    });

    // Handler para el boton "Guardar"
    $('#adif_comprasbundle_cotizacion_submit').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_cotizacion]').valid()) {

            if (saveClick === true) {
                saveClick = false;
                return;
            }

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar?',
                callbackOK: function () {
                    saveClick = true;
                    $('#adif_comprasbundle_cotizacion_submit').trigger('click');
                }
            });

            return false;
        }

    });
}

/**
 * 
 * @returns {undefined}
 */
function updatePrecioTotalCotizacion() {

    var $precioTotalCotizacion = 0;

    // Por cada monto cotizado en la tabla de Cotizaciones 
    $('#table-cotizaciones').find('.precio-total-cotizado').each(function () {


        // Actualizo el precio Total
        var cantidad = parseFloat(clearCurrencyValue(
                $(this).parent('tr').find('.cantidad').html()
                ));

        var precioUnitario = parseFloat(clearCurrencyValue(
                $(this).parent('tr').find('.precio-unitario-cotizado').html()
                ));

        var porcentajeIva = $(this).parent('tr').find('.porcentaje-iva').html().trim() === ""
                ? 0
                : $(this).parent('tr').find('.porcentaje-iva').html();

        // Actualizo el Monto IVA
        var alicuotaIva = parseFloat(clearCurrencyValue(porcentajeIva));

        var montoTotalIva = (alicuotaIva * precioUnitario / 100) * cantidad;

        $(this).parent('tr').find('.monto-iva')
                .html(montoTotalIva.toString().replace(/\./g, ','))
                .autoNumeric('update');


        var precioTotal = cantidad * precioUnitario + montoTotalIva;

        $(this).parent('tr').find('.precio-total-cotizado')
                .html(precioTotal.toString().replace(/\./g, ','))
                .autoNumeric('update');


        var $precioTotalRenglonCotizacion = $(this).html();

        $precioTotalCotizacion += parseFloat(clearCurrencyValue($precioTotalRenglonCotizacion));

    });

    $('#adif_comprasbundle_cotizacion_precio')
            .val($precioTotalCotizacion.toString().replace(/\./g, ','))
            .autoNumeric('update');
}

/**
 * 
 * @returns {undefined}
 */
function updatePrecioTotalAdicionales() {

    var $precioTotalAdicionales = 0;

    // Por cada monto en la tabla de Adicionales 
    $('#table-adicionales-cotizacion').find('.monto-adicional').each(function () {

        var simboloTipoMoneda = $(this).parent('tr').data('simbolo-tipo-moneda');

        if (simboloTipoMoneda === null) {
            simboloTipoMoneda = "$";
        }

        var $precioTotalRenglonAdicional = $(this).html();

        var signo = $(this).parents('tr').find('td').eq(4).html();

        if (signo === "+") {
            $precioTotalAdicionales += parseFloat(clearCurrencyValue($precioTotalRenglonAdicional));
        } else {
            $precioTotalAdicionales -= parseFloat(clearCurrencyValue($precioTotalRenglonAdicional));
        }

        var tipoValor = $(this).parents('tr').find('td').eq(6).html();

        if (tipoValor === "$") {

            $(this).autoNumeric('destroy');
            $(this).autoNumeric('init', {vMin: "-999999999.9999", vMax: '9999999999.9999', aSign: simboloTipoMoneda + ' ', aSep: '.', aDec: ','});
            $(this).autoNumeric('update');
        }
    });

    $('#adif_comprasbundle_cotizacion_adicionales')
            .val($precioTotalAdicionales.toString().replace(/\./g, ','))
            .autoNumeric('update');
}

/**
 * 
 * @returns {undefined}
 */
function initTipoValorAdicionalSelect() {

    $('#adif_comprasbundle_adicionalcotizacion_tipoValor').select2().on("change", function () {

        var $valorInput = $(this).parents('.row')
                .find('#adif_comprasbundle_adicionalcotizacion_valor');

        if ($(this).find('option:selected').val() === "$") {

            $valorInput.addClass('money-format');
            $valorInput.removeClass('percent-format');
        }
        else {
            $valorInput.addClass('percent-format');
            $valorInput.removeClass('money-format');
        }

        // Actualizo el select de IVA de adicional segun corresponda
        updateIVAAdicionalSelect();

        updateMasks();

    }).trigger('change');
}

/**
 * 
 * @returns {undefined}
 */
function updateIVAAdicionalSelect() {

    var idTipoValor = $('#adif_comprasbundle_adicionalcotizacion_tipoValor').val();

    var $ivaAdicionalSelect = $('#adif_comprasbundle_adicionalcotizacion_alicuotaIva');

    if (idTipoValor === '$') {
        $ivaAdicionalSelect.select2('readonly', false);
    }
    else {

        var alicuotaValue = $ivaAdicionalSelect.find('option')
                .filter(function () {
                    return ($(this).html().indexOf("0") >= 0);
                }).val();

        $ivaAdicionalSelect.select2("val", alicuotaValue).trigger('change');

        $ivaAdicionalSelect.select2('readonly', true);
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateMasks() {

    $('.money-format').each(function () {

        var simboloTipoMoneda = $(this).parent('tr').data('simbolo-tipo-moneda');

        if (simboloTipoMoneda === null) {
            simboloTipoMoneda = "$";
        }

        $(this).autoNumeric('destroy');
        $(this).autoNumeric('init', {vMin: "-999999999.9999", vMax: '9999999999.9999', aSign: simboloTipoMoneda + ' ', aSep: '.', aDec: ','});
        $(this).autoNumeric('update');
    });

    $('.percent-format').each(function () {
        $(this).autoNumeric('destroy');
        $(this).autoNumeric('init', {vMin: '0.000', vMax: '9999999999.9999', pSign: 's', aSign: ' %', aSep: '.', aDec: ','});
        $(this).autoNumeric('update');
    });
}

/**
 * 
 * @param {type} idRenglonRequerimiento
 * @param {type} precioUnitarioCotizado
 * @returns {undefined}
 */
function validateDiferenciaEntreJustiprecioYCotizacion(idRenglonRequerimiento, precioUnitarioCotizado) {

    var data = {
        id_renglon_requerimiento: clearFormatId(idRenglonRequerimiento)
    };

    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'renglonrequerimiento/justiprecio',
        data: data,
        success: function (justiprecio) {

            var porcentajeDiferencia = ((parseFloat(clearCurrencyValue(precioUnitarioCotizado))
                    * 100 / parseFloat(justiprecio)) - 100).toFixed(2);

            if (parseFloat(porcentajeDiferencia) > parseFloat(porcentajeTopeEntreJustiprecioYCotizacion)) {

                showFlashMessage("warning", "El monto cotizado ("
                        + precioUnitarioCotizado
                        + ") supera en un "
                        + porcentajeDiferencia
                        + " % el justiprecio indicado ("
                        + convertToMoneyFormat(justiprecio, 4)
                        + ")");
            }
        }
    });

}

/**
 * 
 * @returns {undefined}
 */
function initArchivosForm() {

    collectionHolderArchivos = $('div.prototype-archivos');

    collectionHolderArchivos.data('index', collectionHolderArchivos.find(':input').length);

    $('.prototype-link-add-archivo').on('click', function (e) {

        e.preventDefault();

        addArchivoForm(collectionHolderArchivos);

        initFileInput();
    });
}

/**
 * 
 * @param {type} $collectionHolder
 * @returns {addArchivoForm}
 */
function addArchivoForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var archivoForm = prototype.replace(/__adjunto__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-archivo').closest('.row').before(archivoForm);

    var $archivoDeleteLink = $(".prototype-link-remove-archivo");

    updateDeleteLinks($archivoDeleteLink);
}

/**
 * 
 * @param {type} deleteLink
 * @returns {undefined}
 */
function updateDeleteLinks(deleteLink) {

    deleteLink.each(function () {

        $(this).tooltip();

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var deletableRow = $(this).closest('.row');

            show_confirm({
                msg: '¿Desea eliminar el registro?',
                callbackOK: function () {
                    deletableRow.hide('slow', function () {

                        updateArchivosEliminadosArray(deletableRow);

                        deletableRow.remove();
                    });
                }
            });

            e.stopPropagation();

        });
    });
}

/**
 * 
 * @param {type} deletableRow
 * @returns {undefined}
 */
function updateArchivosEliminadosArray(deletableRow) {

    var idArchivo = deletableRow.find('input[id$=archivo]').data('id');

    archivosEliminadosArray.push(idArchivo);
}

/**
 * 
 * @param {type} $idWithFormat
 * @returns {unresolved}
 */
function clearFormatId($idWithFormat) {
    return parseInt($idWithFormat.replace(/[^\d.]/g, ''));
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $.trim($value.toString()
            .replace('U$D', '')
            .replace('€', '')
            .replace('R$', '')
            .replace(/\$|\%/g, '')
            .replace(/\./g, '')
            .replace(/\,/g, '.'));
}