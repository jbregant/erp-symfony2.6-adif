
var dt_orden_compra;

var caisProveedor = {};

$(document).ready(function () {
    $.validator.addMethod("equalTotal", function (value, element, param) {

        var montoValidacion = $("input[id ^=adif_contablebundle_comprobante][id $= _montoValidacion]")
                .val().replace(',', '@').replace(/\./g, ',').replace('@', '.');

        var montoTotal = $("input[id ^=adif_contablebundle_comprobante][id $= _total]")
                .val().replace(',', '@').replace(/\./g, ',').replace('@', '.');

        return parseFloat(montoValidacion).toFixed(2) == parseFloat(montoTotal).toFixed(2);
    });

    $('form[name^="adif_contablebundle_comprobante"]').validate({
        ignore: '.ignore'
    });

    // Si el comprobante utiliza validación de monto
    if ($('input[id ^= "adif_contablebundle_comprobante"][id $= "_montoValidacion"]').length > 0) {

        $('input[id ^= "adif_contablebundle_comprobante"][id $= "_montoValidacion"]')
                .rules('add', {
                    equalTotal:
                            true,
                    messages: {
                        equalTotal: "El monto de validaci&oacute;n difiere del total del comprobante"
                    }
                });
    }

    // Validar renglon OC si no es una Nota de Crédito
    if ($('[name^="adif_contablebundle_comprobantecompra[renglonesComprobante]["][name$="][idRenglonOrdenCompra]"]').length > 0) {

        $('[name^="adif_contablebundle_comprobantecompra[renglonesComprobante]["][name$="][idRenglonOrdenCompra]"]')
                .rules('add', {required: function (element) {
                        return $('#adif_contablebundle_comprobantecompra_tipoComprobante').val() != __TIPO_COMPROBANTE_NC
                    }});
    }

    $('input[id ^= adif_contablebundle_comprobante][id $= puntoVenta]').inputmask({
        mask: "9999",
        numericInput: true,
        onincomplete: function () {
            $(this).val($(this).val().replace(/_/g, '0'));
        }
    });

    $('input[id ^= adif_contablebundle_comprobante][id $= numero]').inputmask({
        mask: "99999999",
        numericInput: true,
        onincomplete: function () {
            $(this).val($(this).val().replace(/_/g, '0'));
        }
    });

    $('#agregar_renglon_percepcion').on('click', function (e) {

        crear_renglon_percepcion_impuesto('percepcion');

        initBorrarRenglonPercepcionHandler();

        initMostrarJurisdiccionHandler();

        initRecalcularImpuestosHandler();
    });

    $('#agregar_renglon_impuesto').on('click', function (e) {

        crear_renglon_percepcion_impuesto('impuesto');

        initBorrarRenglonImpuestoHandler();

        initRecalcularImpuestosHandler();
    });

    $('#agregar_renglon_adicional').on('click', function (e) {
        crear_renglon_adicional(null, null, '+', 0, '$', null);
    });

    initRecalcularNetosHandler();

    initRecalcularSubtotalNetoHandler();

    initRecalcularIVAHandler();

    initRecalcularSubtotalIVAHandler();

    initRecalcularImpuestosHandler();

    initBorrarRenglonComprobanteHandler();

    initBorrarRenglonPercepcionHandler();

    initBorrarRenglonImpuestoHandler();

    $(document).on('click', '.renglon_adicional_borrar', function (e) {

        e.preventDefault();

        var renglonAdicional = $(this).parents('.row_renglon_adicional');

        show_confirm({
            msg: '¿Desea eliminar el adicional?',
            callbackOK: function () {
                renglonAdicional.hide('slow', function () {

                    renglonAdicional.remove();

                    $('.row_headers_renglon_adicional,.row_footer_renglon_adicional')
                            .css('display', $('.row_renglon_adicional').length == 0 ? 'none' : 'block');

                    recalcular_subtotal_neto();
                });
            }
        });

        e.stopPropagation();
    });

    /*
     $(document).on('change', '#adif_contablebundle_comprobantecompra_bonificacionValor,#adif_contablebundle_comprobantecompra_bonificacionTipo', function () {
     recalcular_total();
     });
     */
    $(document).on('change', 'select[name^="adif_contablebundle_comprobante"][name *= "\[adicionales\]"][name $= "\[tipoAdicional\]"], select[name^="adif_contablebundle_comprobante"][name *= "\[adicionales\]"][name $= "\[signo\]"], select[name^="adif_contablebundle_comprobante"][name *= "\[adicionales\]"][name $= "\[tipoValor\]"], input[name^="adif_contablebundle_comprobante"][name *= "\[adicionales\]"][name $= "\[valor\]"]', function () {
        recalcular_netos_adicionales();
    });

    initRecalcularNetosAdicionalesHandler();

    $(document).on('change', 'select[name^="adif_contablebundle_comprobante"][name *= "\[adicionales\]"][name $= "\[alicuotaIva\]"]', function () {
        recalcular_iva_adicionales();
    });


    $(document).on('change', 'select[id ^= adif_contablebundle_comprobante][id $= letraComprobante]', function () {
        restringir_iva();
    });

    initMostrarJurisdiccionHandler();

    $('input[id ^= adif_contablebundle_comprobante][id $=_total]').val(0);

    restringir_iva();
    init_validaciones();
    recalcular_subtotal_neto();
});

/**
 * 
 * @param {type} id_renglon_oc
 * @param {type} descripcion
 * @param {type} bien_economico
 * @param {type} cantidad
 * @param {type} precio_unitario
 * @param {type} id_alicuota_iva
 * @param {type} bonificacion_tipo
 * @param {type} bonificacion_valor
 * @param {type} monto_neto 
 * @param {type} monto_iva
 * @returns {undefined}
 */
function crear_renglon_comprobante(id_renglon_oc, descripcion, bien_economico, cantidad, precio_unitario, id_alicuota_iva, bonificacion_tipo, bonificacion_valor, monto_neto, monto_iva, tipo_cambio) {

    var nuevo_row =
            $('.row_renglon_comprobante_nuevo')
            .clone()
            .show()
            .removeClass('row_renglon_comprobante_nuevo');

    nuevo_row.addClass('row_renglon_comprobante');

    nuevo_row.find('.ignore').removeClass('ignore');

    nuevo_row.find('[sname]').each(function () {
        $(this).attr('name', $(this).attr('sname'));
        $(this).removeAttr('sname');
    });

    var maximo_indice = 0;

    $('.row_renglon_comprobante').each(function () {
        var value = parseFloat($(this).attr('indice'));
        maximo_indice = (value > maximo_indice) ? value : maximo_indice;
    });

    var indice_nuevo = maximo_indice + 1;

    nuevo_row.html(nuevo_row.html().replace(/__name__/g, indice_nuevo));
    nuevo_row.attr('indice', indice_nuevo);
    nuevo_row.appendTo('.ctn_rows_renglon_comprobante');

    var row_sel_prefix = '#adif_contablebundle_comprobante' + prefijo + '_renglonesComprobante_';

    nuevo_row.find(row_sel_prefix + indice_nuevo + '_idRenglonOrdenCompra').val(id_renglon_oc);

    var descripcion_renglon = (bien_economico ? bien_economico : '') + (descripcion ? ' - ' + descripcion : '');
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_descripcion').val(descripcion_renglon);

    nuevo_row.find(row_sel_prefix + indice_nuevo + '_cantidad').val(cantidad ? cantidad : 0);
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_precioUnitario').val(precio_unitario ? precio_unitario : 0);

    if (bonificacion_tipo) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_bonificacionTipo').val(bonificacion_tipo);
    }

    nuevo_row.find(row_sel_prefix + indice_nuevo + '_bonificacionValor').val(bonificacion_valor ? bonificacion_valor : 0);
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_neto').val(monto_neto ? monto_neto : 0);

    if (id_alicuota_iva) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_alicuotaIva').val(id_alicuota_iva);
    }
	
	if (tipo_cambio) {
		nuevo_row.find(row_sel_prefix + indice_nuevo + '_tipoCambio').val(tipo_cambio);
		nuevo_row.find(row_sel_prefix + indice_nuevo + '_precioUnitarioOriginal').val(precio_unitario ? precio_unitario : 0);
	}

    nuevo_row.find(row_sel_prefix + indice_nuevo + '_montoIva').val(monto_iva ? monto_iva : 0);

    nuevo_row.find('select').select2();

    $('.row_headers_renglon_comprobante').show();
    $('.row_footer_renglon_comprobante').show();

    initCurrencies();
}

/**
 * 
 * @param {type} clase_renglon
 * @param {type} datos_impuesto
 * @param {type} datos_percepcion
 * @returns {undefined}
 */
function crear_renglon_percepcion_impuesto(clase_renglon, datos_percepcion, datos_impuesto) {
    var nuevo_row =
            $('.row_renglon_' + clase_renglon + '_nuevo')
            .clone()
            .show()
            .removeClass('row_renglon_' + clase_renglon + '_nuevo');

    nuevo_row.addClass('row_renglon_' + clase_renglon);

    nuevo_row.find('[sname]').each(function () {
        $(this).attr('name', $(this).attr('sname'));
        $(this).removeAttr('sname');
    });

    var maximo_indice = 0;
    $('.row_renglon_' + clase_renglon).each(function () {
        var value = parseFloat($(this).attr('indice'));
        maximo_indice = (value > maximo_indice) ? value : maximo_indice;
    });
    var indice_nuevo = maximo_indice + 1;

    nuevo_row.html(nuevo_row.html().replace(/__name__/g, indice_nuevo));
    nuevo_row.attr('indice', indice_nuevo);
    nuevo_row.appendTo('.ctn_rows_renglon_' + clase_renglon);

    var row_sel_prefix = '#adif_contablebundle_comprobante' + prefijo + '_renglones' + clase_renglon.charAt(0).toUpperCase() + clase_renglon.substring(1) + '_';

    if (datos_impuesto) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_conceptoImpuesto').val(datos_impuesto.conceptoImpuesto);
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_detalle').val(datos_impuesto.detalle);
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_monto').val(datos_impuesto.monto);
    }

    if (datos_percepcion) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_conceptoPercepcion').val(datos_percepcion.conceptoPercepcion);
        if (datos_percepcion.jurisdiccion) {
            nuevo_row.find(row_sel_prefix + indice_nuevo + '_jurisdiccion').val(datos_percepcion.jurisdiccion);
        }
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_monto').val(datos_percepcion.monto);
    }

    nuevo_row.find('select').select2();
    initCurrencies();

    $('.row_headers_renglon_' + clase_renglon).show();
    $('.row_footer_renglon_' + clase_renglon).show();

    mostrar_jurisdiccion();
    recalcular_impuestos();
}

/**
 * 
 * @param {type} id_adicional
 * @param {type} id_tipo_adicional
 * @param {type} signo
 * @param {type} valor
 * @param {type} id_tipo_valor
 * @param {type} id_alicuota_iva
 * @param {type} monto_neto
 * @param {type} monto_iva
 * @returns {undefined}
 */
function crear_renglon_adicional(id_adicional, id_tipo_adicional, signo, valor, id_tipo_valor, id_alicuota_iva, monto_neto, monto_iva) {
    var nuevo_row =
            $('.row_renglon_adicional_nuevo')
            .clone()
            .show()
            .removeClass('row_renglon_adicional_nuevo');

    nuevo_row.addClass('row_renglon_adicional');

    nuevo_row.find('.ignore').removeClass('ignore');

    nuevo_row.find('[sname]').each(function () {
        $(this).attr('name', $(this).attr('sname'));
        $(this).removeAttr('sname');
    });

    var maximo_indice = 0;
    $('.row_renglon_adicional').each(function () {
        var value = parseFloat($(this).attr('indice'));
        maximo_indice = (value > maximo_indice) ? value : maximo_indice;
    });
    var indice_nuevo = maximo_indice + 1;

    nuevo_row.html(nuevo_row.html().replace(/__name__/g, indice_nuevo));
    nuevo_row.attr('indice', indice_nuevo);
    nuevo_row.appendTo('.ctn_rows_renglon_adicional');

    var row_sel_prefix = '#adif_contablebundle_comprobante' + prefijo + '_adicionales_';

    if (id_adicional) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_idAdicionalCotizacion').val(id_adicional);
    }

    nuevo_row.find(row_sel_prefix + indice_nuevo + '_valor').val(valor ? valor : 0);
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_montoNeto').val(monto_neto ? monto_neto : 0);
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_montoIva').val(monto_iva ? monto_iva : 0);

    if (id_alicuota_iva) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_alicuotaIva').val(id_alicuota_iva);
    }

    if (id_tipo_adicional) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_tipoAdicional').val(id_tipo_adicional);
    }

    if (signo) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_signo').val(signo);
    }

    if (id_tipo_valor) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_tipoValor').val(id_tipo_valor);
    }

    nuevo_row.find('select').select2();

    $('.div_ctn_seccion_adicionales').show();
    $('.row_headers_renglon_adicional').show();
    $('.row_footer_renglon_adicional').show();

    initCurrencies();

    restringir_iva();
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_netos() {

    var $montosNetos = $('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[montoNeto\]"]');

    $montosNetos.each(function (i) {

        var indice = $(this).parents('.row_renglon_comprobante').attr('indice');

        var precioUnitario = parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_precioUnitario]').val().replace(',', '.'));
        
        var cantidad = parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_cantidad]').val().replace(',', '.'));
		
        var neto = parseFloat(cantidad * precioUnitario);
		
        if ($('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_bonificacionValor]').length) {
            var bonificacion = parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_bonificacionValor]').val().replace(',', '.'));

            neto = neto - ($('select[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_bonificacionTipo]').val() == 'porcentaje' ? neto * bonificacion / 100 : bonificacion);
        }

        var $inputNeto = $('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_neto]');

        $inputNeto.val(neto.toFixed(2).replace(/\./g, ','));

        // Si es el ultimo elemento
        if (i === $montosNetos.length - 1) {
            $inputNeto.trigger('change');
        }
    });

    recalcular_subtotal_neto();
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_subtotal_neto() {

    var subtotal_neto = 0;

    $('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[montoNeto\]"]').each(function () {
        subtotal_neto += parseFloat($(this).val().replace(',', '.'));
    });

    $('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_subtotal_neto]')
            .val(subtotal_neto.toFixed(2).replace(/\./g, ','))
            .trigger('change');

    recalcular_netos_adicionales();

    recalcular_iva();
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_subtotal_neto_adicionales() {

    var subtotal_neto_adicional = 0;

    if ($('#adif_contablebundle_comprobante' + prefijo + '_renglonesComprobante_subtotal_neto').length > 0) {

        var subtotal_neto = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_renglonesComprobante_subtotal_neto').val().replace(',', '.'));

        $('input[name^="adif_contablebundle_comprobante' + prefijo + '\[adicionales\]"][name$="\[montoNeto\]"]').each(function () {
            var indice = $(this).parents('.row_renglon_adicional').attr('indice');
            subtotal_neto_adicional += parseFloat($(this).val().replace(',', '.'));
        });

        subtotal_neto_adicional += subtotal_neto;
    }

    $('input[id ^= adif_contablebundle_comprobante][id $= adicionales_subtotal_neto]')
            .val(subtotal_neto_adicional.toFixed(2).replace(/\./g, ','))
            .trigger('change');

    recalcular_iva_adicionales();
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_netos_adicionales() {
    if ($('#adif_contablebundle_comprobante' + prefijo + '_renglonesComprobante_subtotal_neto').length > 0) {
        var subtotal_neto = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_renglonesComprobante_subtotal_neto').val().replace(',', '.'));
        $('input[name^="adif_contablebundle_comprobante' + prefijo + '\[adicionales\]"][name$="\[montoNeto\]"]').each(function () {
            var indice = $(this).parents('.row_renglon_adicional').attr('indice');
            var tipoValor = $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_tipoValor').val();
            var neto = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_valor').val().replace(',', '.'));
            if (tipoValor == '%') {
                neto = neto * subtotal_neto / 100;
            }
            var signo = $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_signo').val();
            if (signo == '-') {
                neto = -neto;
            }

            $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_montoNeto').val(neto.toFixed(2).replace(/\./g, ',')).trigger('change');
        });

        recalcular_subtotal_neto_adicionales();
    }
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_iva() {

    var $selectAlicuotaIVA = $('select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[alicuotaIva\]"]');

    $selectAlicuotaIVA.each(function (i) {

        var indice = $(this).parents('.row_renglon_comprobante').attr('indice');
        var montoIva = 0;

        if (prefijo !== 'consultoria' && $('select[id ^= "adif_contablebundle_comprobante"][id $= "renglonesComprobante_' + indice + '_alicuotaIva"] > option:selected').text() == '0.00') {

            $('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_montoIva]').attr('readonly', true);

        } else {

            var neto = parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_neto]').val().replace(',', '.'));

            var monto_alicuota_iva = $('select[id ^= "adif_contablebundle_comprobante"][id $= "renglonesComprobante_' + indice + '_alicuotaIva"] > option:selected').text();

            montoIva = neto * monto_alicuota_iva / 100;

            if (prefijo !== 'venta') {
                $('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_montoIva]').attr('readonly', false);
            }
        }

        var $inputMontoIVA = $('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_' + indice + '_montoIva]');

        $inputMontoIVA.val(montoIva.toFixed(2).replace(/\./g, ','));

        // Si es el ultimo elemento
        if (i === $selectAlicuotaIVA.length - 1) {
            $inputMontoIVA.trigger('change');
        }

    });

    recalcular_subtotal_iva();
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_iva_adicionales() {
    $('select[name^="adif_contablebundle_comprobante' + prefijo + '\[adicionales\]"][name$="\[alicuotaIva\]"]').each(function () {
        var indice = $(this).parents('.row_renglon_adicional').attr('indice');

        var montoIva = 0;

        if ($('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_alicuotaIva > option:selected').text() == '0.00') {
            $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_montoIva').attr('readonly', true);
        } else {
            var neto = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_montoNeto').val().replace(',', '.'));
            var monto_alicuota_iva = $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_alicuotaIva > option:selected').text();
            montoIva = neto * monto_alicuota_iva / 100;
            $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_montoIva').attr('readonly', false);
        }

        $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_montoIva').val(montoIva.toFixed(2).replace(/\./g, ',')).trigger('change');
    });

    recalcular_subtotal_iva_adicionales();
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_subtotal_iva() {

    var subtotal_iva = 0;

    $('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[montoIva\]"]').each(function () {
        subtotal_iva += parseFloat($(this).val().replace(',', '.'));
    });

    $('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_subtotal_iva]')
            .val(subtotal_iva.toFixed(2).replace(/\./g, ',')).trigger('change');

    recalcular_subtotal_iva_adicionales();
    recalcular_total();
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_subtotal_iva_adicionales() {

    var subtotal_iva_adicionales = 0;

    if ($('#adif_contablebundle_comprobante' + prefijo + '_renglonesComprobante_subtotal_iva').length > 0) {

        var subtotal_iva = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_renglonesComprobante_subtotal_iva').val().replace(',', '.'));

        $('input[name^="adif_contablebundle_comprobante' + prefijo + '\[adicionales\]"][name$="\[montoIva\]"]').each(function () {

            var indice = $(this).parents('.row_renglon_adicional').attr('indice');
            var signo = $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_signo').val();

            if (signo == '-') {
                // Si el signo es negativo, tengo que bonificar el iva de los renglones
                var tipoValor = $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_tipoValor').val();
                var neto = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_valor').val().replace(',', '.'));
                var montoIva = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_adicionales_' + indice + '_montoIva').val().replace(',', '.'));
                if (tipoValor == '%') {
                    subtotal_iva -= subtotal_iva * neto / 100;
                } else {
                    //subtotal_iva -= neto;
                    subtotal_iva -= montoIva * -1;
                }
            }
            else {
                // Si el signo es positivo, sumo el iva del adicional
                subtotal_iva_adicionales += parseFloat($(this).val().replace(',', '.'));
            }
        });

        subtotal_iva_adicionales += subtotal_iva;

    }

    $('#adif_contablebundle_comprobante' + prefijo + '_adicionales_subtotal_iva')
            .val(subtotal_iva_adicionales.toFixed(2).replace(/\./g, ','))
            .trigger('change');

    recalcular_total();
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_impuestos() {
    var subtotal_percepciones = 0;
    $('input[name^="adif_contablebundle_comprobante' + prefijo + '\[renglonesPercepcion\]"][name$="\[monto\]"]').each(function () {
        var indice = $(this).parents('.row_renglon_percepcion').attr('indice');
        var monto_percepcion = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_renglonesPercepcion_' + indice + '_monto').val().replace(',', '.'));
        subtotal_percepciones += monto_percepcion;
    });

    var subtotal_impuestos = 0;
    $('input[name^="adif_contablebundle_comprobante' + prefijo + '\[renglonesImpuesto\]"][name$="\[monto\]"]').each(function () {
        var indice = $(this).parents('.row_renglon_impuesto').attr('indice');
        var monto_impuesto = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_renglonesImpuesto_' + indice + '_monto').val().replace(',', '.'));
        subtotal_impuestos += monto_impuesto;
    });

    $('#adif_contablebundle_comprobante' + prefijo + '_renglonesPercepcion_subtotal').val(subtotal_percepciones.toFixed(2).replace(/\./g, ',')).trigger('change');
    $('#adif_contablebundle_comprobante' + prefijo + '_renglonesImpuesto_subtotal').val(subtotal_impuestos.toFixed(2).replace(/\./g, ',')).trigger('change');

    recalcular_total();
}

/**
 * 
 * @returns {undefined}
 */
function recalcular_total() {

    var total = 0;

    if ($('#adif_contablebundle_comprobante' + prefijo + '_adicionales_subtotal_neto').length) {
        total = parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_adicionales_subtotal_neto').val().replace(',', '.'))
                + parseFloat($('#adif_contablebundle_comprobante' + prefijo + '_adicionales_subtotal_iva').val().replace(',', '.'));
    } else {
        total = parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_subtotal_neto]').val().replace(',', '.')) +
                parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_subtotal_iva]').val().replace(',', '.'));
    }

    if ($('input[id ^= adif_contablebundle_comprobante][id $= renglonesPercepcion_subtotal]').length) {
        total += parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesPercepcion_subtotal]').val().replace(',', '.'));
    }

    if ($('input[id ^= adif_contablebundle_comprobante][id $= renglonesImpuesto_subtotal]').length) {
        total += parseFloat($('input[id ^= adif_contablebundle_comprobante][id $= renglonesImpuesto_subtotal]').val().replace(',', '.'));
    }

    total = total.toFixed(2);

    $('input[id ^= adif_contablebundle_comprobante][id $= _total]').val(total.replace(/\./g, ',')).trigger('change');
    $('input[id ^= adif_contablebundle_comprobante][id $= _renglonesComprobante_subtotal_fixed]').val(total.replace(/\./g, ',')).trigger('change');
}

/**
 * 
 * @returns {undefined}
 */
function init_validaciones() {
	
    $('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[cantidad\]"]').each(function () {
        $(this).rules('add', {
            valor_maximo: parseFloat($(this).val().replace(',', '.'))
        });
    });
	
	// Manejo PU diferente ya que le voy a decir que maneje un margen de decimales a considerar
	$('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[precioUnitario\]"]').each(function () {
			var tolerancia = 0.0500;
			var max = parseFloat($(this).val().replace(',', '.'));
			var valor_maximo = max + tolerancia;
			valor_maximo = valor_maximo.toFixed(2);
			$(this).rules('add', {
				valor_maximo: valor_maximo
			});
	});
}

/**
 * 
 * @param {type} recalculaIVA
 * @returns {undefined}
 */
function restringir_iva(recalculaIVA) {

    recalculaIVA = typeof recalculaIVA !== 'undefined' ? recalculaIVA : true;

    var idLetraComprobanteA = $('select[ id ^= adif_contablebundle_comprobante][id $= letraComprobante] option').filter(function () {
        return $(this).html() == __letraComprobanteA;
    }).val();

    var idLetraComprobanteALeyenda = $('select[ id ^= adif_contablebundle_comprobante][id $= letraComprobante] option').filter(function () {
        return $(this).html() == __letraComprobanteALeyenda;
    }).val();

    var idLetraComprobanteY = $('select[ id ^= adif_contablebundle_comprobante][id $= letraComprobante] option').filter(function () {
        return $(this).html() == __letraComprobanteY;
    }).val();

    var idLetraComprobanteM = $('select[ id ^= adif_contablebundle_comprobante][id $= letraComprobante] option').filter(function () {
        return $(this).html() == __letraComprobanteM;
    }).val();

    // Letra A, A con leyenda, M o Y, tiene IVA
    if ($('select[id ^= adif_contablebundle_comprobante][id $= letraComprobante]').val() == idLetraComprobanteA ||
            $('select[id ^= adif_contablebundle_comprobante][id $= letraComprobante]').val() == idLetraComprobanteALeyenda ||
            $('select[id ^= adif_contablebundle_comprobante][id $= letraComprobante]').val() == idLetraComprobanteY ||
            $('select[id ^= adif_contablebundle_comprobante][id $= letraComprobante]').val() == idLetraComprobanteM) {

        if (prefijo !== 'venta') {
            $('select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[alicuotaIva\]"]').select2('readonly', false);
            $('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[montoIva\]"]').attr('readonly', false);

            $('select[name^="adif_contablebundle_comprobante' + prefijo + '\[adicionales\]"][name$="\[alicuotaIva\]"]').select2('readonly', false);
            $('input[name^="adif_contablebundle_comprobante' + prefijo + '\[adicionales\]"][name$="\[montoIva\]"]').attr('readonly', false);
        }
    }
    else {
        if (prefijo !== 'consultoria') {
            $('select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[alicuotaIva\]"]').select2('readonly', true);
            $('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[montoIva\]"]').attr('readonly', 'readonly');

            $('select[name^="adif_contablebundle_comprobante' + prefijo + '\[adicionales\]"][name$="\[alicuotaIva\]"]').select2('readonly', true);
            $('input[name^="adif_contablebundle_comprobante' + prefijo + '\[adicionales\]"][name$="\[montoIva\]"]').attr('readonly', 'readonly');
        }
    }
    if (prefijo === 'venta') {
        $('select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[alicuotaIva\]"]').select2('readonly', false);
        $('select[name^="adif_contablebundle_comprobante' + prefijo + '\[adicionales\]"][name$="\[alicuotaIva\]"]').select2('readonly', false);
    }

    if (recalculaIVA) {
        recalcular_iva();
    }
}

/**
 * 
 * @returns {undefined}
 */
function mostrar_jurisdiccion() {
    $('select[name^="adif_contablebundle_comprobante' + prefijo + '\[renglonesPercepcion\]"][name$="\[conceptoPercepcion\]"]').each(function () {

        var indice = $(this).parents('.row_renglon_percepcion').attr('indice');

        var $selectJurisdiccion = $('#adif_contablebundle_comprobante' + prefijo + '_renglonesPercepcion_' + indice + '_jurisdiccion');

        // Percepción IIBB
        if ($(this).find('option:selected').text() === __percepcionIIBB) {

            if (typeof __jurisdiccionCABA !== "undefined") {

                var idJurisdiccionCABA = $selectJurisdiccion.find('option')
                        .filter(function () {
                            return ($(this).html().indexOf(__jurisdiccionCABA) >= 0);
                        }).val();

                // Elimino la jurisdiccion CABA
                /*
                $selectJurisdiccion
                        .find('option[value=' + idJurisdiccionCABA + ']')
                        .remove();
                */

                $selectJurisdiccion.select2();
            }

            $selectJurisdiccion.prop('required', true);

            $selectJurisdiccion.parents('.jurisdiccion-data').children().show();

        } else {

            $selectJurisdiccion.prop('required', false);

            $selectJurisdiccion.select2("val", '');

            $selectJurisdiccion.parents('.jurisdiccion-data').children().hide();
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRecalcularImpuestosHandler() {

    $(document).on('change', 'input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesPercepcion\]"][name $= "\[monto\]"], input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesImpuesto\]"][name $= "\[monto\]"]', function () {
        recalcular_impuestos();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initBorrarRenglonComprobanteHandler() {

    $(document).on('click', '.renglon_comprobante_borrar', function (e) {

        e.preventDefault();

        var renglonComprobante = $(this).parents('.row_renglon_comprobante');

        show_confirm({
            msg: '¿Desea eliminar el renglón?',
            callbackOK: function () {

                renglonComprobante.hide('slow', function () {

                    renglonComprobante.remove();

                    $('.row_headers_renglon_comprobante,.row_footer_renglon_comprobante')
                            .css('display', $('.row_renglon_comprobante').length == 0 ? 'none' : 'block');

                    recalcular_netos();

                });
            }
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initBorrarRenglonPercepcionHandler() {

    $(document).on('click', '.renglon_percepcion_borrar', function (e) {
        $(this).parents('.row_renglon_percepcion').remove();
        $('.row_headers_renglon_percepcion,.row_footer_renglon_percepcion').css('display', $('.row_renglon_percepcion').length == 0 ? 'none' : 'block');
        recalcular_impuestos();
    });
}



/**
 * 
 * @returns {undefined}
 */
function initBorrarRenglonImpuestoHandler() {

    $(document).on('click', '.renglon_impuesto_borrar', function (e) {
        $(this).parents('.row_renglon_impuesto').remove();
        $('.row_headers_renglon_impuesto,.row_footer_renglon_impuesto').css('display', $('.row_renglon_impuesto').length == 0 ? 'none' : 'block');
        recalcular_impuestos();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initMostrarJurisdiccionHandler() {

    $(document).on('change', 'select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesPercepcion\]"][name $= "\[conceptoPercepcion\]"]', function () {
        mostrar_jurisdiccion();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRecalcularNetosHandler() {

    $(document).on('change', 'input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[cantidad\]"], input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[precioUnitario\]"], input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[bonificacionTipo\]"], input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[bonificacionValor\]"]', function () {
        recalcular_netos();
    });

}

/**
 * 
 * @returns {undefined}
 */
function initRecalcularSubtotalNetoHandler() {

    $(document).on('change', 'input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[montoNeto\]"]', function () {
        recalcular_subtotal_neto();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRecalcularIVAHandler() {

    $(document).on('change', 'select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[alicuotaIva\]"]', function () {
        recalcular_iva();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRecalcularSubtotalIVAHandler() {

    $(document).on('change', 'input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[montoIva\]"]', function () {
        recalcular_subtotal_iva();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRecalcularNetosAdicionalesHandler() {

    $(document).on('change', 'input[id ^= adif_contablebundle_comprobante][id $= renglonesComprobante_subtotal_neto]', function () {
        recalcular_netos_adicionales();
//      recalcular_subtotal_neto_adicionales();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRenglonComprobanteHandler() {

    initRecalcularNetosHandler();

    initRecalcularSubtotalNetoHandler();

    initRecalcularIVAHandler();

    initRecalcularSubtotalIVAHandler();

    initRecalcularNetosAdicionalesHandler();
}

/**
 * 
 * @returns {undefined}
 */
function checkFechaVencimientoCai() {

    var fechaTopeComprobante = new Date();

    var validarFecha = false;

    // Si el proveedor tiene CAIs asociados
    if (!jQuery.isEmptyObject(caisProveedor)) {

        var puntoVenta = $('input[id ^= adif_contablebundle_][id $= _puntoVenta]').val();

        // Si hay un punto de venta especificado Y existe un CAI para dicho punto de venta
        if (puntoVenta !== "" && typeof caisProveedor[puntoVenta] !== "undefined") {

            var fechaVencimiento = getDateFromString(caisProveedor[puntoVenta]);

            fechaTopeComprobante = fechaVencimiento < new Date() ? fechaVencimiento : new Date();

            validarFecha = true;
        }
    }

    $('input[id ^= adif_contablebundle_][id $= _fechaComprobante]')
            .datepicker('setEndDate', fechaTopeComprobante);

    if (validarFecha && fechaTopeComprobante < new Date()) {

        var $mensaje = "No se puede cargar una fecha posterior. El cai está vencido";

        showFlashMessage('warning', $mensaje);

        $('input[id ^= adif_contablebundle_][id $= _fechaComprobante]').val(null);
    }

}

/**
 * 
 * @returns {undefined}
 */
function initPuntoVentaHandler() {

    $('input[id ^= adif_contablebundle_][id $= _puntoVenta]').change(function () {

        checkFechaVencimientoCai();
    });
}