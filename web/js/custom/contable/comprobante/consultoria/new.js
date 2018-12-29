
var $formularioComprobanteConsultoria = $('form[name="adif_contablebundle_comprobanteconsultoria"]');

var $letraComprobanteSelect = $('#adif_contablebundle_comprobanteconsultoria_letraComprobante');

var $fechaIngresoADIF = $('#adif_contablebundle_comprobanteconsultoria_fechaIngresoADIF');

var $fechaComprobante = $('#adif_contablebundle_comprobanteconsultoria_fechaComprobante');

var __TIPO_COMPROBANTE_NC = 3;

$(document).ready(function () {

    initValidate();

    initAutocompleteConsultor();

    initAgregarRenglonComprobanteHandler();

    initFechaValidation();

    initPuntoVentaHandler();

    initSubmitButton();

    initComprobanteChange();

    initSeleccionarCicloFacturacionHandler();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $formularioComprobanteConsultoria.validate({
        ignore: '.ignore'
    });
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_comprobanteconsultoria_submit').on('click', function (e) {

        if ($formularioComprobanteConsultoria.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el comprobante?',
                callbackOK: function () {
                    addCiclosFacturacionPendientes();
                    $formularioComprobanteConsultoria.submit();
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
function addCiclosFacturacionPendientes() {

    var json = {
        ciclos_facturacion: []
    };

    $('.row_renglon_comprobante').each(function () {

        var idCicloFacturacion = $(this).attr('id_ciclo_facturacion');

        json.ciclos_facturacion[idCicloFacturacion] = typeof json.ciclos_facturacion[idCicloFacturacion] === "undefined"
                ? 1
                : json.ciclos_facturacion[idCicloFacturacion] + 1;
    });

    $formularioComprobanteConsultoria.addHiddenInputData(json);
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteConsultor() {

    $('#adif_contablebundle_comprobanteconsultoria_consultor').autocomplete({
        source: __AJAX_PATH__ + 'consultor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            selectConsultor(event, ui);

            updateLetraComprobanteSelect();

            checkFechaVencimientoCai();
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };
}

/**
 * 
 * @param {type} event
 * @param {type} ui
 * @returns {undefined}
 */
function selectConsultor(event, ui) {

    $('#adif_contablebundle_comprobanteconsultoria_consultor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_comprobanteconsultoria_consultor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_comprobanteconsultoria_consultor_id').val(ui.item.id);
    
    $('#adif_contablebundle_comprobanteconsultoria_idContrato').val(ui.item.idContrato);

    caisProveedor = ui.item.cais;

    $('.ciclo_facturacion_consultor-data').hide();
    
    $('.ctn_rows_renglon_comprobante').empty();

    renglonesComprobantes(ui.item.id);
}

/**
 * 
 * @returns {undefined}
 */
function initSeleccionarCicloFacturacionHandler() {

    $(document).on('click', '#table_ciclo_facturacion_consultor tbody tr', function (e, tr) {

        bloquear();

        if ($(this).hasClass('selected')) {

            eliminarRenglonCicloFacturacion($(this));

            // Desmarco el TR seleccionado como "selected"
            $(this).removeClass('selected');
            $(this).addClass('active');
            recalcular_netos();
            $('#adif_contablebundle_comprobanteconsultoria_idContrato')
                    .val(null);
        } else {

            if ($('#adif_contablebundle_comprobanteconsultoria_idContrato')
                    .val() === "" || $('#adif_contablebundle_comprobanteconsultoria_idContrato')
                    .val() === $(this).attr('id_contrato')) {

                agregarRenglonCicloFacturacion($(this));

                // Marco el TR seleccionado como "selected"
                $(this).addClass('selected');
                $(this).removeClass('active');

                $('#adif_contablebundle_comprobanteconsultoria_idContrato')
                        .val($(this).attr('id_contrato'));
            } else {

                if ($('#adif_contablebundle_comprobanteconsultoria_idContrato')
                        .val() !== $(this).attr('id_contrato')) {
                    var options = $.extend({
                        title: 'Ha ocurrido un error',
                        msg: "Debe seleccionar ciclos del mismo contrato."
                    });

                    show_alert(options);
                }
            }
        }

        chequearCiclosFacturacionAnteriores($(this).attr('id_ciclo_facturacion'), $(this).attr('id_renglon'), $(this).hasClass('active'));

        desbloquear();

//        ordenarCiclosFacturacion();
    });

}

/**
 * 
 * @param {type} id_ciclo_facturacion
 * @param {type} id_renglon
 * @param {type} seleccionado
 * @returns {undefined}
 */
function chequearCiclosFacturacionAnteriores(id_ciclo_facturacion, id_renglon, seleccionado) {

    $('#table_ciclo_facturacion_consultor')
            .find('tr[id_ciclo_facturacion=' + id_ciclo_facturacion + ']')
            .each(function () {

                if ((!seleccionado && parseInt($(this).attr('id_renglon')) < parseInt(id_renglon)) || (seleccionado && parseInt($(this).attr('id_renglon')) > parseInt(id_renglon))) {

                    if (seleccionado) {
                        eliminarRenglonCicloFacturacion($(this));

                        $(this).removeClass('selected');
                    } else {
                        if (!$(this).hasClass('selected')) {
                            agregarRenglonCicloFacturacion($(this));

                            $(this).addClass('selected');
                        }
                    }

                }
            });
}

/**
 * 
 * @returns {undefined}
 */
function ordenarCiclosFacturacion() {
    var divList = $(".row_renglon_comprobante");

    divList.sort(function (a, b) {
//        return parseInt($(a).attr("numero_cuota")) - parseInt($(b).attr("numero_cuota"));
        return parseInt($(a).attr("id_renglon")) - parseInt($(b).attr("id_renglon"));
    });

    $('.row_renglon_comprobante').each(function (i, e) {
        $(this).attr('indice', ++i);
    });

    $(".ctn_rows_renglon_comprobante").html(divList);
}

/**
 * 
 * @param {type} cicloFacturacionSeleccionado
 * @returns {undefined}
 */
function agregarRenglonCicloFacturacion(cicloFacturacionSeleccionado) {

    bloquear();

    cicloFacturacionSeleccionadoMes = cicloFacturacionSeleccionado.find('.mes').text();

    renglonSeleccionadoId = cicloFacturacionSeleccionado.attr('id_renglon');

    cicloFacturacionSeleccionadoId = cicloFacturacionSeleccionado.attr('id_ciclo_facturacion');

    cicloFacturacionSeleccionadoImporte = parseFloat(clearCurrencyValue(cicloFacturacionSeleccionado.find('.importe').text()));

    cicloFacturacionSeleccionadoDescripcion = 'Honorarios contrato Nº ' + cicloFacturacionSeleccionado.find('.nro_contrato').text() + ' - MES ' + cicloFacturacionSeleccionadoMes;

    cicloFacturacionSeleccionadoNumeroCuota = cicloFacturacionSeleccionado.attr('numero_cuota');


    crearRenglonComprobanteConsultoria(cicloFacturacionSeleccionadoId, cicloFacturacionSeleccionadoNumeroCuota, cicloFacturacionSeleccionadoDescripcion, 1, cicloFacturacionSeleccionadoImporte, cicloFacturacionSeleccionadoImporte, 1, 0, cicloFacturacionSeleccionadoMes);
    restringir_iva();
    recalcular_netos();

    $('.row_renglon_comprobante').last().attr('numero_cuota', cicloFacturacionSeleccionadoNumeroCuota);
    $('.row_renglon_comprobante').last().attr('id_renglon', renglonSeleccionadoId);
    $('.row_renglon_comprobante').last().attr('id_ciclo_facturacion', cicloFacturacionSeleccionadoId);

    desbloquear();
}

/**
 * 
 * @param {type} cicloFacturacionSeleccionado
 * @returns {undefined}
 */
function eliminarRenglonCicloFacturacion(cicloFacturacionSeleccionado) {

    bloquear();

    renglonSeleccionadoId = parseInt(cicloFacturacionSeleccionado.attr('id_renglon'));

    $('.row_renglon_comprobante[id_renglon=' + renglonSeleccionadoId + ']').remove();

    // Si era el último renglón
    if ($('.row_renglon_comprobante').length === 0) {
        $('.row_headers_renglon_comprobante').hide();
        $('.row_footer_renglon_comprobante').hide();
    }

    desbloquear();
}

/**
 * 
 * @returns {undefined}
 */
function initAgregarRenglonComprobanteHandler() {
    $('#agregar_renglon_comprobante').on('click', function (e) {
        crearRenglonComprobanteConsultoria();
        restringir_iva();
        recalcular_netos();
    });
}

/**
 * 
 * @param {type} idCicloFacturacion
 * @param {type} numeroCuota
 * @param {type} descripcion
 * @param {type} cantidad
 * @param {type} precioUnitario
 * @param {type} montoNeto
 * @param {type} idAlicuotaIva
 * @param {type} montoIva
 * @returns {undefined}
 */
function crearRenglonComprobanteConsultoria(idCicloFacturacion, numeroCuota, descripcion, cantidad, precioUnitario, montoNeto, idAlicuotaIva, montoIva, mes) {

    var nuevoRow =
            nuevoCicloFacturacion
            .clone()
            .show()
            .removeClass('row_renglon_comprobante_nuevo');

    nuevoRow.addClass('row_renglon_comprobante');

    nuevoRow.find('.ignore').removeClass('ignore');

    nuevoRow.find('[sname]').each(function () {
        $(this).attr('name', $(this).attr('sname'));
        $(this).removeAttr('sname');
    });

    var maximoIndice = 0;

    $('.row_renglon_comprobante').each(function () {
        var value = parseFloat($(this).attr('indice'));
        maximoIndice = (value > maximoIndice) ? value : maximoIndice;
    });

    var indiceNuevo = maximoIndice + 1;

    nuevoRow.html(nuevoRow.html().replace(/__name__/g, indiceNuevo));
    nuevoRow.attr('indice', indiceNuevo);
    nuevoRow.appendTo('.ctn_rows_renglon_comprobante');

    var row_sel_prefix = '#adif_contablebundle_comprobanteconsultoria_renglonesComprobante_';

    nuevoRow.find(row_sel_prefix + indiceNuevo + '_cicloFacturacion').val(idCicloFacturacion ? idCicloFacturacion : '');
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_numeroCuota').val(numeroCuota ? numeroCuota : '');
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_descripcion').val(descripcion ? descripcion : '');
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_cantidad').val(cantidad ? cantidad : 1);
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_precioUnitario').val(precioUnitario ? precioUnitario : 0);
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_montoNeto').val(montoNeto ? montoNeto : 0);
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_mes').val(mes ? mes : '');

    if (idAlicuotaIva) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_alicuotaIva').val(idAlicuotaIva);
    }

    nuevoRow.find(row_sel_prefix + indiceNuevo + '_montoIva').val(montoIva ? montoIva : 0);

    nuevoRow.find('select').select2();

    $('.row_headers_renglon_comprobante').show();
    $('.row_footer_renglon_comprobante').show();

    initCurrencies();
}

/**
 * 
 * @returns {undefined}
 */
function initFechaValidation() {

    var currentDate = getCurrentDate();

    $fechaIngresoADIF.datepicker('setEndDate', currentDate);

    $fechaComprobante.datepicker('setEndDate', currentDate);

    $fechaIngresoADIF.on('changeDate', function () {

        var fechaIngresoADIFDate = getDateFromString($(this).val());

        $fechaComprobante.datepicker('setEndDate', fechaIngresoADIFDate);

        $fechaComprobante.val(null);

    });
}

/**
 * 
 * @returns {undefined}
 */
function updateLetraComprobanteSelect() {

    resetSelect($letraComprobanteSelect);

    var data = {
        idConsultor: $('#adif_contablebundle_comprobanteconsultoria_consultor_id').val()
    };

    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'consultor/letras_comprobante',
        data: data
    }).done(function (letrasComprobante) {

        for (var i = 0, total = letrasComprobante.length; i < total; i++) {
            $letraComprobanteSelect.append('<option value="' + letrasComprobante[i].id + '">' + letrasComprobante[i].letra + '</option>');
        }

        $letraComprobanteSelect.select2('val', $letraComprobanteSelect.find('option:first').val());

        $letraComprobanteSelect.select2('readonly', false);
    });
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {
    $('.money-format').each(function () {
        $(this).autoNumeric('init', {aSign: '$ ', aSep: '.', aDec: ','});
    });
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value.replace('$', '').replace(/\./g, '').replace(/\,/g, '.').trim();
}


function renglonesNotaCredito(id) {

    $('.ciclo_facturacion_consultor-data').hide();

    $.ajax({
        url: __AJAX_PATH__ + 'contratoconsultoria/cuotas_cancelables/',
        data: {id_consultor: id}
    }).done(function (ciclosFacturacion) {
        generarReenglonesCuotas(ciclosFacturacion);
    });
}


function initComprobanteChange() {
    $(document).on('change', '#adif_contablebundle_comprobanteconsultoria_tipoComprobante', function (e) {
        if ($('#adif_contablebundle_comprobanteconsultoria_consultor_id').val() != '') {
            if ($('#adif_contablebundle_comprobanteconsultoria_tipoComprobante').val() == __TIPO_COMPROBANTE_NC) {
                renglonesNotaCredito($('#adif_contablebundle_comprobanteconsultoria_consultor_id').val());
                $('.label-ciclos').html('Cuotas a cancelar con nota de cr&eacute;dito');
            } else {
                renglonesComprobantes($('#adif_contablebundle_comprobanteconsultoria_consultor_id').val());
                $('.label-ciclos').html('Ciclos del consultor');
            }
        }
    });
}

function renglonesComprobantes(id) {
    $.ajax({
        url: __AJAX_PATH__ + 'contratoconsultoria/ciclos_facturacion_pendientes/',
        data: {id_consultor: id}
    }).done(function (ciclosFacturacion) {
        generarReenglonesCuotas(ciclosFacturacion);
    });
}

/**
 * 
 * @param {type} ciclosFacturacion
 * @returns {undefined}
 */
function generarReenglonesCuotas(ciclosFacturacion) {
    $('#table_ciclo_facturacion_consultor tbody').empty();

    $('.ciclo_facturacion_consultor-data').show();

    $.each(ciclosFacturacion, function (index) {

        var row = ciclosFacturacion[index];

        var $tr = $('<tr />', {
            numero_cuota: row['numeroCuota'],
            id_renglon: index,
            id_ciclo_facturacion: row['idCicloFacturacion'],
            id_contrato: row['idContrato'],
            style: 'cursor: pointer;'}
        );

        $tr.on('click', function () {
            $(this).parents('tbody').find('tr').removeClass('active');
            $(this).addClass('active');
        });

        $('<td />', {text: index + 1})
                .addClass('text-center nowrap hidden')
                .appendTo($tr);

        $('<td />', {text: row['nroContrato']})
                .addClass('text-center nowrap nro_contrato')
                .appendTo($tr);

        $('<td />', {text: row['gerencia'] + ' - ' + row['area']})
                .addClass('nowrap')
                .appendTo($tr);

        $('<td />', {text: row['mes']})
                .addClass('nowrap mes')
                .appendTo($tr);
        
        $('<td />', {text: row['anio']})
                .addClass('nowrap anio')
                .appendTo($tr);

        $('<td />', {text: row['importe']})
                .addClass('money-format importe')
                .appendTo($tr);

        $('#table_ciclo_facturacion_consultor tbody').append($tr);
    });

    setMasks();
}