
var $oTableCotizaciones = $('table[ id ^= table-cotizaciones]');

var tipoValorMonto = "$";

var tipoValorPorcentaje = "%";

/**
 * 
 */
jQuery(document).ready(function () {

    setMasks();

    initLinks();

    initAdicionalCheckboxes();

    initCotizacionesGanadoras();

    initCotizacionesElegidas();

    actualizarTodosTotalesAdjudicados();

    configSubmitButtons();

    $('#adif_comprasbundle_cotizacion_print').on('click', function (e) {

        $('.checkboxes.ganador.selected').attr('disabled', false);
        $('.checkboxes.ganador.selected').attr('checked', true);

        $('#htmlCuadro').val($('#content-table-comparacion').html());
        $('#form_imprimir_cuadro').submit();

        $('.checkboxes.ganador.selected').attr('disabled', true);

        desbloquear();
    });

});


/**
 * 
 * @returns {undefined}
 */
function initLinks() {

    initElegirCotizadorLink();
    initDesmarcarCotizadorLink();
}

/**
 * 
 * @returns {undefined}
 */
function initAdicionalCheckboxes() {

    $('input[name="adicional"]').change(function () {

        var $trSelected = $(this).closest('tr');

        var idProveedor = $trSelected.find('.adicional-monto').data('proveedor');

        if ($(this).is(':checked')) {
            $trSelected.addClass('selected');
        }
        else {
            $trSelected.removeClass('selected');
        }

        actualizarSubtotal(idProveedor);

        actualizarIVA(idProveedor);

        actualizarTotal(idProveedor);

        actualizarTotalAdjudicado(idProveedor);
    });
}

/**
 * 
 * @param {type} idProveedor
 * @returns {undefined}
 */
function actualizarSubtotal(idProveedor) {

    var tdSubtotal = $(".subtotal[data-proveedor='" + idProveedor + "']");

    var $tdAdicionalesSeleccionados = $(".tr-adicional.selected")
            .find(".adicional-monto[data-proveedor='" + idProveedor + "']");

    var total = getMontoTotalRenglonesCotizacion(idProveedor);

    // Por cada adicional seleccionado
    $tdAdicionalesSeleccionados.each(function () {

        var signo = $(this).parent('td').find('.adicional-signo').text();

        // Obtengo el tipo de valor "$" o "%"
        var tipoValor = $(this).parent('td').find('.tipo-valor').text().trim();

        var valor = clearCurrencyValue($(this).text());

        var monto = getMonto(idProveedor, valor, tipoValor);

        if (signo === "+") {
            total += monto;
        }
        else {
            total -= monto;
        }
    });

    tdSubtotal.text(total.toString().replace('.', ','));

    tdSubtotal.autoNumeric('update', {vMin: '-999999999.9999', vMax: '9999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});
}

/**
 * 
 * @param {type} idProveedor
 * @returns {undefined}
 */
function actualizarIVA(idProveedor) {

    var totalIva = 0;

    var $renglonesCotizacion = $(".renglon-cotizacion-monto[data-proveedor='" + idProveedor + "']");

    // Por cada renglon cotizado del Proveedor
    $renglonesCotizacion.each(function () {

        var totalIvaRenglon = $(this).next('.renglon-cotizacion-iva')
                .text().trim();

        totalIva += parseFloat(totalIvaRenglon);
    });

    totalIva += getTotalIVAFromAdicionalesSeleccionados(idProveedor, $renglonesCotizacion);

    var $tdIVA = $(".total-iva[data-proveedor='" + idProveedor + "']");

    $tdIVA.text(totalIva.toString().replace('.', ','));

    $tdIVA.autoNumeric('update', {vMin: '-999999999.9999', vMax: '9999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});
}

/**
 * 
 * @param {type} idProveedor
 * @returns {undefined}
 */
function actualizarTotal(idProveedor) {

    var subtotal = $(".subtotal[data-proveedor='" + idProveedor + "']").text();

    var iva = $(".total-iva[data-proveedor='" + idProveedor + "']").text();

    var $tdTotal = $(".total[data-proveedor='" + idProveedor + "']");

    var total = parseFloat(clearCurrencyValue(subtotal)) + parseFloat(clearCurrencyValue(iva));

    $tdTotal.text(total.toString().replace('.', ','));

    $tdTotal.autoNumeric('update', {vMin: '-999999999.9999', vMax: '9999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});
}

/**
 * 
 * @param {type} idProveedor
 * @returns {undefined}
 */
function actualizarTotalAdjudicado(idProveedor) {

    var totalAdjudicado = 0;

    var $montoRenglonCotizacion = $('.total-neto-renglon.selected')
            .find(".renglon-cotizacion-monto[data-proveedor='" + idProveedor + "']");

    $montoRenglonCotizacion.each(function () {
        totalAdjudicado += parseFloat(clearCurrencyValue($(this).text().trim()));
    });

    var $tdAdicionalesSeleccionados = $(".tr-adicional.selected")
            .find(".adicional-monto[data-proveedor='" + idProveedor + "']");

    $tdAdicionalesSeleccionados.each(function () {

        var signo = $(this).parent('td').find('.adicional-signo').text();

        var tipoValor = $(this).parent('td').find('.tipo-valor').text().trim();

        var adicionalMonto = clearCurrencyValue($(this).text());

        if (tipoValor.trim() !== tipoValorMonto) {
            adicionalMonto *= getMontoTotalRenglonesCotizacionSeleccionados(idProveedor) / 100;
        }

        if (signo === "+") {
            totalAdjudicado += parseFloat(adicionalMonto);
        }
        else {
            totalAdjudicado -= parseFloat(adicionalMonto);
        }
    });

    var $ivaRenglonesCotizacionesSeleccionados = $("td.total-neto-renglon.selected")
            .find(".renglon-cotizacion-iva[data-proveedor='" + idProveedor + "']");

    $ivaRenglonesCotizacionesSeleccionados.each(function () {
        totalAdjudicado += parseFloat($(this).text().trim());
    });

    var $renglonesCotizacionesSeleccionados = $("td.total-neto-renglon.selected")
            .find(".renglon-cotizacion-monto[data-proveedor='" + idProveedor + "']");

    totalAdjudicado += getTotalIVAFromAdicionalesSeleccionados(idProveedor, $renglonesCotizacionesSeleccionados);

    var $tdTotalAdjudicado = $(".total-adjudicado[data-proveedor='" + idProveedor + "']");

    $tdTotalAdjudicado.text(totalAdjudicado.toString().replace('.', ','));

    $tdTotalAdjudicado.autoNumeric('update', {vMin: '-999999999.9999', vMax: '9999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});
}

/**
 * 
 * @returns {undefined}
 */
function actualizarTodosTotalesAdjudicados() {

    $('.th-proveedor').each(function () {

        actualizarTotalAdjudicado($(this).data('proveedor'));

    });
}

/**
 * 
 * @param {type} idProveedor
 * @returns {undefined}
 */
function getTotalAdicionalesSeleccionadosSinIva(idProveedor) {

    var $tdAdicionales = $(".tr-adicional.selected[data-proveedor='" + idProveedor + "']");

    var total = 0;

    $tdAdicionales.each(function () {

        var $adicionalIva = $(this).find('.adicional-iva');

        // Obtengo el porcentaje de IVA del Adicional
        var adicionalPorcentajeIva = getPorcentaje($adicionalIva.text());

        var signo = $(this).find('.adicional-signo').text();

        // Si el porcentaje de la alicuota es cero
        if (adicionalPorcentajeIva == 0 && signo === "-") {

            var tipoValor = $(this).find('.tipo-valor').text();

            var valor = parseFloat(clearCurrencyValue($(this).find('.adicional-monto').text()));

            var monto = getMonto(idProveedor, valor, tipoValor);

            if (signo === "+") {
                total += monto;
            }
            else {
                total -= monto;
            }
        }

    });

    return total;
}

/**
 * 
 * @returns {undefined}
 */
function initElegirCotizadorLink() {

    $('.elegir_cotizador').off().on('click', function (e) {

        e.preventDefault();

        // Obtengo el TR del Cotizador clickeado
        var trCotizador = $(this).parents('tr');

        // Deselecciono el Cotizador antes seleccionado
        trCotizador.find('.datos-cotizacion.selected').removeClass('selected');
        trCotizador.find('.elegir_cotizador').removeClass('hidden');
        trCotizador.find('.no_elegir_cotizador').addClass('hidden');


        // Selecciono el nuevo Cotizador
        $(this).parents('.datos-cotizacion').addClass('selected');
        $(this).parents('td').prev('td').addClass('selected');
        $(this).parents('td').prev('td').prev('td').addClass('selected');

        $(this).parents('td').find('.elegir_cotizador').addClass('hidden');
        $(this).parents('td').find('.no_elegir_cotizador').removeClass('hidden');

        actualizarTodosTotalesAdjudicados();
    });
}


/**
 * 
 * @returns {undefined}
 */
function initDesmarcarCotizadorLink() {

    $('.no_elegir_cotizador').off().on('click', function (e) {

        e.preventDefault();

        // Obtengo el TR del Cotizador clickeado
        var trCotizador = $(this).parents('tr');

        // Deselecciono el Cotizador seleccionado
        trCotizador.find('.datos-cotizacion.selected').removeClass('selected');

        trCotizador.find('.elegir_cotizador').removeClass('hidden');
        trCotizador.find('.no_elegir_cotizador').addClass('hidden');

        actualizarTodosTotalesAdjudicados();
    });
}

/**
 * 
 * @returns {undefined}
 */
function configSubmitButtons() {

    // Si la cantidad de renglones cotizados es igual a la cantidad de renglones con un ganador
    if ($(".tr-renglon-requerimiento").length === $(".tr-renglon-requerimiento:has('td.datos-cotizacion.ganador')").length) {
        $('#adif_comprasbundle_cotizacion_submit').addClass('disabled');
        $('#adif_comprasbundle_cotizacion_save').addClass('disabled');
    }

    // Handler para el boton "Guardar borrador"
    $('#adif_comprasbundle_cotizacion_save').on('click', function (e) {

        var msgVal = '¿Desea guardar la comparación como borrador?';
        var accionVal = 'save';

        return  submitFormulario(e, msgVal, accionVal);
    });


    // Handler para el boton "Generar"
    $('#adif_comprasbundle_cotizacion_submit').on('click', function (e) {

        var msgVal = '¿Desea continuar? Se generarán las órdenes de compra correspondientes.';
        var accionVal = 'generar';

        return  submitFormulario(e, msgVal, accionVal);
    });

}

/**
 * 
 * @param {type} e
 * @param {type} msgVal
 * @param {type} accionVal
 * @returns {Boolean}
 */
function submitFormulario(e, msgVal, accionVal) {

    if ($('form[name=adif_comprasbundle_cotizacion]').valid()) {

        e.preventDefault();

        show_confirm({
            msg: msgVal,
            callbackOK: function () {

                if (validForm()) {

                    var json = {
                        accion: accionVal,
                        renglones_cotizaciones_elegidos: [],
                        renglones_cotizaciones_no_elegidos: [],
                        adicionales_cotizaciones_elegidos: [],
                        adicionales_cotizaciones_no_elegidos: [],
                        id_requerimiento: requerimientoId
                    };

                    initCotizacionesJson(json, true);

                    initCotizacionesJson(json, false);

                    initAdicionalesJson(json, true);

                    initAdicionalesJson(json, false);

                    $('form[name=adif_comprasbundle_cotizacion]').addHiddenInputData(json);
                    $('form[name=adif_comprasbundle_cotizacion]').submit();
                }
            }
        });

        e.stopPropagation();

        return false;
    }

    return false;
}

/**
 * 
 * @returns {Boolean}
 */
function validForm() {

    return true;
}

/**
 * 
 * @param {type} idProveedor
 * @returns {undefined}
 */
function getMontoTotalRenglonesCotizacion(idProveedor) {

    var $renglonesCotizados = $('.renglon-cotizacion-monto[data-proveedor="' + idProveedor + '"]');

    var total = 0;

    $renglonesCotizados.each(function () {
        total += parseFloat(clearCurrencyValue($(this).text()));
    });

    return total;
}

/**
 * 
 * @param {type} idProveedor
 * @returns {undefined}
 */
function getMontoTotalRenglonesCotizacionSeleccionados(idProveedor) {

    var $renglonesCotizados = $('.total-neto-renglon.selected')
            .find('.renglon-cotizacion-monto[data-proveedor="' + idProveedor + '"]');

    var total = 0;

    $renglonesCotizados.each(function () {
        total += parseFloat(clearCurrencyValue($(this).text()));
    });

    return total;
}

/**
 * 
 * @param {type} idProveedor
 * @param {type} valor
 * @param {type} tipoValor
 * @returns {Number|undefined}
 */
function getMonto(idProveedor, valor, tipoValor) {

    var montoResultado = valor;

    if (tipoValor.trim() !== tipoValorMonto) {
        montoResultado = valor * getMontoTotalRenglonesCotizacion(idProveedor) / 100;
    }

    return parseFloat(montoResultado);

}

/**
 * 
 * @param {type} idProveedor
 * @param {type} $renglonesCotizados
 * @returns {Number}
 */
function getTotalIVAFromAdicionalesSeleccionados(idProveedor, $renglonesCotizados) {

    var totalIva = 0;

    var prorrateoAplicado = false;
	
	var idProveedorAnterior = 0;

    var $tdAdicionalesSeleccionados = $(".tr-adicional.selected")
            .find(".adicional-monto[data-proveedor='" + idProveedor + "']");

    // Por cada adicional seleccionado
    $tdAdicionalesSeleccionados.each(function () {

        var $adicionalIva = $(this).next('.adicional-iva');

        // Obtengo el porcentaje de IVA del Adicional
        var adicionalPorcentajeIva = getPorcentaje($adicionalIva.text());

        var tipoValor = $(this).parent('td').find('.tipo-valor').text().trim();

        var valor = clearCurrencyValue($(this).text());

        var adicionalMonto = getMonto(idProveedor, valor, tipoValor);

        var signo = $(this).parent('td').find('.adicional-signo').text();

        if ($adicionalIva.length > 0 && adicionalPorcentajeIva != 0) {

            var adicionalMontoIva = adicionalPorcentajeIva * adicionalMonto / 100;

            if (signo === "+") {
                totalIva += parseFloat(adicionalMontoIva);
            }
            else {
                totalIva -= parseFloat(adicionalMontoIva);
            }
        }
        // Prorrateo
		/*
        else {
			
            if (signo === "-" && !prorrateoAplicado) {

                prorrateoAplicado = true;

                var totalRenglonesCotizados = getMontoTotalRenglonesCotizacion(idProveedor);

                var totalAdicionalesSeleccionadosSinIva = getTotalAdicionalesSeleccionadosSinIva(idProveedor);
				
                var adicionalPorcentaje = totalAdicionalesSeleccionadosSinIva / totalRenglonesCotizados;

                // Por cada renglon cotizado del Proveedor
                $renglonesCotizados.each(function () {

                    // Obtengo el total neto del renglon
                    var totalNetoRenglon = clearCurrencyValue($(this).text());

                    // Aplico el porcentaje del adicional al monto neto del renglon
                    var montoNetoConPorcentajeAplicado = totalNetoRenglon * adicionalPorcentaje;

                    var porcentajeRenglon = $(this).next('.renglon-cotizacion-iva').text() * 100 / totalNetoRenglon;

                    var montoIva = porcentajeRenglon * montoNetoConPorcentajeAplicado / 100;

                    totalIva += parseFloat(montoIva);

                });
            }
        }
		*/
    });

	
    return totalIva;
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-999999999.9999', vMax: '9999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});
    });
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value
            .replace('$', '')
            .replace('%', '')
            .replace(/\./g, '')
            .replace(/\,/g, '.')
            .trim();
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function getPorcentaje($value) {
    return $value.replace('%', '').trim();
}

/**
 * 
 * @returns {undefined}
 */
function initCotizacionesElegidas() {
    $('.tr-adicional.selected').find('input').trigger('click');
}

/**
 * 
 * @returns {undefined}
 */
function initCotizacionesGanadoras() {

    // Init renglones
    $('.ganador').parent().find('.accion_cotizador').remove();
//    $('.elegir_cotizador').removeClass('hidden');

    // Init adicionales
    $('input[name=adicional].ganador').click();
    $('input[name=adicional].ganador').prop("disabled", true);
}

/**
 * 
 * @param {type} json
 * @param {type} estanSeleccionados
 * @returns {undefined}
 */
function initCotizacionesJson(json, estanSeleccionados) {

    var $cotizaciones = null;

    if (estanSeleccionados) {

        // Obtengo las cotizaciones que tienen indicado un ganador
        // que no tienen ya una OC asociada
        $cotizaciones = $('.total-neto-renglon.selected:not(".ganador")');
    } //.
    else {

        // Obtengo las cotizaciones que NO tienen indicado un ganador 
        // ni fueron seleccionadas
        $cotizaciones = $('.total-neto-renglon:not(".selected"):not(".ganador")');
    }

    var jsonCotizaciones = estanSeleccionados //
            ? json.renglones_cotizaciones_elegidos //
            : json.renglones_cotizaciones_no_elegidos;


    // Por cada cotizacion obtenida
    $cotizaciones.each(function () {

        var $trCotizacion = $(this).parents('tr');

        var idProveedor = $(this).find('.renglon-cotizacion-monto')
                .data('proveedor');

        var idCotizacion = $(".th-proveedor[data-proveedor='" + idProveedor + "']")
                .data('cotizacion');

        var idRenglonCotizacion = $trCotizacion
                .find(".renglon-cotizacion-id[data-proveedor='" + idProveedor + "']")
                .text().trim();

        // Si el proveedor no fue cargado en el arreglo
        if (typeof jsonCotizaciones[idProveedor] === "undefined") {
            jsonCotizaciones[idProveedor] = {
                id_proveedor: idProveedor,
                id_cotizacion: idCotizacion,
                renglones_cotizados: []
            };
        }

        // Agrego al proveedor, el id del renglon cotizado que ganó                            
        jsonCotizaciones[idProveedor].renglones_cotizados
                .push(idRenglonCotizacion);



    });


}

function initAdicionalesJson(json, estanSeleccionados) {

    var jsonAdicionales = estanSeleccionados //
            ? json.adicionales_cotizaciones_elegidos //
            : json.adicionales_cotizaciones_no_elegidos;

    var $adicionales = null;

    if (estanSeleccionados) {
        // Obtengo los adicionales seleccionados NO ganadores para cada proveedor
        $adicionales = $(".tr-adicional.selected:not('.ganador')");
    } else {
        // Obtengo los adicionales NO seleccionados para cada proveedor
        $adicionales = $(".tr-adicional:not('.selected'):not('.ganador')");
    }

    // Por cada adicionales obtenido
    $adicionales.each(function () {

        var idAdicional = $(this).data('adicional');

        jsonAdicionales.push({
            id_adicional: idAdicional
        });
    });

}