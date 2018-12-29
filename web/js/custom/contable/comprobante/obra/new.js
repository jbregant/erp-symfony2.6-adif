
var esEdit = $('[name=_method]').length > 0;

var $formularioComprobanteObra = $('form[name="adif_contablebundle_comprobanteobra"]');

var documentoFinancieroSeleccionadoId;

var documentoFinancieroSeleccionadoSaldo = 0;

$(document).ready(function () {

    initValidate();

    initTable();

    initAutocompleteProveedor();

    initAgregarRenglonComprobanteHandler();

    initFechaComprobanteValidation();

    initLetraHandler();

    initPuntoVentaHandler();

    initSubmitButton();

    initEditParcial();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion para total del documento financiero
    $.validator.addMethod("precioUnitario", function (value, element, param) {

        var $precioUnitarioInput = $('#adif_contablebundle_comprobanteobra_renglonesComprobante_1_precioUnitario');

        var precioUnitario = parseFloat(clearCurrencyValue($precioUnitarioInput.val()));

        // Valido que el precio unitario no supere el saldo del documento financiero
        return precioUnitario <= documentoFinancieroSeleccionadoSaldo;

    });

    // Validacion del Formulario
    $formularioComprobanteObra.validate();
}


/**
 * 
 * @returns {undefined}
 */
function initTable() {

    var options = {
        "searching": false,
        "ordering": true,
        "info": true,
        "paging": true,
        "pageLength": 12,
        "lengthMenu": [[12, 24, 48, 100, -1], [12, 24, 48, 100, "Todos"]],
        "drawCallback": function () {

            setMasks();

            initSeleccionarDocumentoFinancieroHandler();

            invalidarDocumentosFinancieros();

            seleccionarDocumentoFinancieroActivo();
        }
    };

    dt_init($('#table_documento_financiero_proveedor'), options);
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_comprobanteobra_submit').on('click', function (e) {

        if ($formularioComprobanteObra.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el comprobante?',
                callbackOK: function () {

                    if (validForm()) {
                        $formularioComprobanteObra.submit();
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

    // Si seleccionó un DocumentoFinanciero
    if (documentoFinancieroSeleccionadoId) {

        // Si NO hay un renglon cargado al comprobante
        if ($('.row_renglon_comprobante').length === 0) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "Debe cargar al menos un renglón al comprobante."
            });

            show_alert(options);

            return false;
        }
    }
    else {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe seleccionar un documento financiero para continuar."
        });

        show_alert(options);

        return false;
    }

    return true;
}

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteProveedor() {

    $('#adif_contablebundle_comprobanteobra_proveedor').autocomplete({
        source: __AJAX_PATH__ + 'proveedor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {

            completarDocumentosFinancieros(event, ui, null);

            checkFechaVencimientoCai();
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };
}

function completarDocumentosFinancieros(event, ui, idDocumentoFinancieroSeleccionado) {

    $('#adif_contablebundle_comprobanteobra_proveedor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_comprobanteobra_proveedor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_comprobanteobra_idProveedor').val(ui.item.id);

    caisProveedor = ui.item.cais;

    // Elimino todos los renglones previamente seleccionados
    $('tr.selected').each(function () {
        eliminarRenglonComprobanteObra($(this));
    });

    $('.row_table_documento_financiero_proveedor').hide();

    $.ajax({
        type: "POST",
        url: __AJAX_PATH__ + 'documento_financiero/index_table_proveedor/',
        data: {id_proveedor: ui.item.id}
    }).done(function (result) {
        var documentosFinancieros = JSON.parse(result).data;

        $('#table_documento_financiero_proveedor').DataTable().rows().remove().draw();
        $('#table_documento_financiero_proveedor tbody').empty();
        $('.row_table_documento_financiero_proveedor').show();

        $(documentosFinancieros).each(function () {
            var $tr = $('<tr />', {
                id_documento_financiero: this[0],
                fecha_documento_financiero: this[5],
                style: 'cursor: pointer;'});

            $tr.on('click', function () {
                $(this).parents('tbody').find('tr').removeClass('active');
                $(this).addClass('active');
            });

            $('<td />', {text: this[1]}).addClass('licitacion nowrap').appendTo($tr);
            $('<td />', {text: this[5]}).addClass('fecha nowrap text-center').appendTo($tr);
            $('<td />', {text: this[2]}).addClass('descripcionTramo').appendTo($tr);
            $('<td />', {text: this[3]}).addClass('tipoDocumentoFinanciero nowrap').appendTo($tr);
            $('<td />', {text: this[8]}).addClass('correspondePago nowrap text-center').appendTo($tr);
            $('<td />', {text: this[4]}).addClass('money-format montoSinIVA nowrap text-right').appendTo($tr);
            $('<td />', {text: this[6]}).addClass('money-format saldo nowrap text-right').appendTo($tr);
            $('<td />', {text: this[7]}).addClass('hidden tieneComprobantes').appendTo($tr);

            $('#table_documento_financiero_proveedor').DataTable().row.add($tr);

            if (idDocumentoFinancieroSeleccionado && this[0] == idDocumentoFinancieroSeleccionado) {

                $tr.addClass('activo');

                documentoFinancieroSeleccionadoId = idDocumentoFinancieroSeleccionado;
            }
        });

        $('#table_documento_financiero_proveedor').DataTable().draw();
        seleccionarPaginaInicial();
    });

    var idLetraComprobanteC = $('#adif_contablebundle_comprobanteobra_letraComprobante option')
            .filter(function () {
                return $(this).html() == __letraComprobanteC;
            }).val();

    if (ui.item.condicionIVA === __responsableMonotributo) {

        // El proveedor es monotributista, sólo puede cargar facturas "C"
        $('#adif_contablebundle_comprobanteobra_letraComprobante')
                .val(idLetraComprobanteC).attr('readonly', true).select2();
    }
    else {

        if (!idDocumentoFinancieroSeleccionado) {
            $('#adif_contablebundle_comprobanteobra_letraComprobante')
                    .val(idLetraComprobanteC).attr('readonly', false).select2();
        }
    }
}

/**
 * 
 * @returns {undefined}
 */
function seleccionarPaginaInicial() {
    var i = 0;
    var paginaInicial = 0;
    //$('#table_documento_financiero_proveedor').DataTable().rows().every(function(rowIdx, tableLoop, rowLoop) {
    $('#table_documento_financiero_proveedor').DataTable().rows().nodes().to$().each(function () {
        if ($(this).hasClass('activo')) {
            paginaInicial = Math.floor(i / $('#table_documento_financiero_proveedor').DataTable().page.len());
        }
        i++;
    });

    $('#table_documento_financiero_proveedor').dataTable().fnPageChange(paginaInicial);
}

/**
 * 
 * @returns {undefined}
 */
function seleccionarDocumentoFinancieroActivo() {

    $('#table_documento_financiero_proveedor tbody tr.activo').click();
}

/**
 * 
 * @returns {undefined}
 */
function initSeleccionarDocumentoFinancieroHandler() {

    $(document).off().on('click', '#table_documento_financiero_proveedor tbody tr', function (e) {

        if ($(this).hasClass('readonly')) {

            e.preventDefault();

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "Existen documentos financieros anteriores pendientes de pago."
            });

            show_alert(options);

            e.stopPropagation();

            return false;
        }
        else {

            if ($(this).hasClass('selected')) {

                eliminarRenglonComprobanteObra($(this));

                // Desmarco el TR seleccionado como "selected"
                $(this).removeClass('selected');
                $(this).addClass('active');

                recalcular_netos();

                $('select[id ^= adif_contablebundle_comprobante][id $= documentoFinanciero]')
                        .val(null);
            } else {

                $('#table_documento_financiero_proveedor').DataTable().rows('.selected').nodes().each(function () {

                    // Elimino todos los renglones previamente seleccionados
                    eliminarRenglonComprobanteObra($(this));

                    // Desmarco los TR seleccionados
                    $(this).removeClass('selected');
                });

                // Agrego el nuevo renglon
                agregarRenglonComprobanteObra($(this));

                // Seteo el ID del DocumentoFinanciero seleccionado
                documentoFinancieroSeleccionadoId = $(this).attr('id_documento_financiero');

                $('select[id ^= adif_contablebundle_comprobante][id $= documentoFinanciero]')
                        .val(documentoFinancieroSeleccionadoId);


                // Seteo el saldo del DocumentoFinanciero seleccionado
                documentoFinancieroSeleccionadoSaldo = parseFloat(clearCurrencyValue($(this).find('.saldo').text()));

                // Si NO es una edicion
                if (!esEdit) {

                    // Actualizo datepicker de fecha de ComprobanteObra segun fecha de DocumentoFinanciero
                    $('#adif_contablebundle_comprobanteobra_fechaComprobante').val(null);
                }

                var documentoFinancieroSeleccionadoFecha = $(this).attr('fecha_documento_financiero');

                var documentoFinancieroSeleccionadoFechaDate = getDateFromString(documentoFinancieroSeleccionadoFecha);

                $('#adif_contablebundle_comprobanteobra_fechaComprobante')
                        .datepicker('setStartDate', documentoFinancieroSeleccionadoFechaDate);

                // Marco el TR seleccionado como "selected"
                $(this).addClass('selected');
                $(this).removeClass('active');

                $('#adif_contablebundle_comprobanteobra_letraComprobante')
                        .trigger('change');

                initEditParcial();
            }
        }
    });

}

/**
 * 
 * @param {type} descripcion
 * @param {type} cantidad
 * @param {type} precioUnitario
 * @param {type} montoNeto
 * @param {type} idAlicuotaIva
 * @param {type} montoIva
 * @param {type} idRegimenIVA
 * @param {type} idRegimenIIBB
 * @param {type} idRegimenGanancias
 * @param {type} idRegimenSUSS
 * @param {type} idRenglon
 * @returns {undefined}
 */
function crearRenglonComprobanteObra(descripcion, cantidad, precioUnitario, montoNeto, idAlicuotaIva, montoIva, idRegimenIVA, idRegimenIIBB, idRegimenGanancias, idRegimenSUSS, idRenglon) {

    var nuevoRow =
            $('.row_renglon_comprobante_nuevo')
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

    var row_sel_prefix = '#adif_contablebundle_comprobanteobra_renglonesComprobante_';

    nuevoRow.find(row_sel_prefix + indiceNuevo + '_descripcion').val(descripcion ? descripcion : '');
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_cantidad').val(cantidad ? cantidad : 1);
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_precioUnitario').val(precioUnitario ? precioUnitario : 0);
    nuevoRow.find(row_sel_prefix + indiceNuevo + '_montoNeto').val(montoNeto ? montoNeto : 0);

    // Regimenes de retencion
    if (idRegimenIVA) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_regimenRetencionIVA').val(idRegimenIVA);
    }
    if (idRegimenIIBB) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_regimenRetencionIIBB').val(idRegimenIIBB);
    }
    if (idRegimenGanancias) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_regimenRetencionGanancias').val(idRegimenGanancias);
    }
    if (idRegimenSUSS) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_regimenRetencionSUSS').val(idRegimenSUSS);
    }
    //
    if (idRenglon) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_idRenglon').val(idRenglon);
    }

    if (idAlicuotaIva) {
        nuevoRow.find(row_sel_prefix + indiceNuevo + '_alicuotaIva').val(idAlicuotaIva);
    }

    nuevoRow.find(row_sel_prefix + indiceNuevo + '_montoIva').val(montoIva ? montoIva : 0);

    nuevoRow.find('select').select2();

    $('.row_headers_renglon_comprobante').show();
    $('.row_footer_renglon_comprobante').show();

    initCurrencies();

    initRenglonComprobanteHandler();

    // addValidacionPrecioUnitario();
}

/**
 * 
 * @returns {undefined}
 */
function initFechaComprobanteValidation() {

    var currentDate = getCurrentDate();

    $('#adif_contablebundle_comprobanteobra_fechaComprobante')
            .datepicker('setEndDate', currentDate);
}

/**
 * 
 * @param {type} documentoFinancieroSeleccionado
 * @returns {undefined}
 */
function agregarRenglonComprobanteObra(documentoFinancieroSeleccionado) {

    bloquear();

    documentoFinancieroSeleccionadoId = documentoFinancieroSeleccionado.attr('id_documento_financiero');

    var tieneComprobantes = documentoFinancieroSeleccionado.find('.tieneComprobantes').text();

    // Si el DocumentoFinanciero NO tiene comprobantes relacionados
    if (tieneComprobantes === "0") {

        documentoFinancieroSeleccionadoImporte = parseFloat(clearCurrencyValue(documentoFinancieroSeleccionado.find('.montoSinIVA').text()));
    }
    else {
        documentoFinancieroSeleccionadoImporte = parseFloat(clearCurrencyValue(documentoFinancieroSeleccionado.find('.saldo').text()));
    }

    documentoFinancieroSeleccionadoDescripcion =
            documentoFinancieroSeleccionado.find('.licitacion').text() + ' - ' +
            documentoFinancieroSeleccionado.find('.descripcionTramo').text() + ' - ' +
            documentoFinancieroSeleccionado.find('.tipoDocumentoFinanciero').text();

    // Si NO es una edición
    if (!esEdit) {

        crearRenglonComprobanteObra(documentoFinancieroSeleccionadoDescripcion, 1, documentoFinancieroSeleccionadoImporte, documentoFinancieroSeleccionadoImporte, 1, 0);

    }
    else {

        initCurrencies();

        initRenglonComprobanteHandler();
    }

    restringir_iva();
    recalcular_netos();

    initBorrarRenglonComprobanteHandler();

    $('.row_renglon_comprobante').last().attr('id_documento_financiero', documentoFinancieroSeleccionadoId);

    desbloquear();
}

/**
 * 
 * @param {type} documentoFinancieroSeleccionado
 * @returns {undefined}
 */
function eliminarRenglonComprobanteObra(documentoFinancieroSeleccionado) {

    bloquear();

    documentoFinancieroSeleccionadoId = parseInt(documentoFinancieroSeleccionado
            .attr('id_documento_financiero'));

    $('.row_renglon_comprobante[id_documento_financiero=' + documentoFinancieroSeleccionadoId + ']')
            .remove();

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
function addValidacionPrecioUnitario() {

    // Valido el precio unitario del documento financiero
    $('#adif_contablebundle_comprobanteobra_renglonesComprobante_1_precioUnitario').rules('add', {
        precioUnitario: true,
        messages: {
            precioUnitario: "El monto neto no es válido"
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function invalidarDocumentosFinancieros() {

    var validacionArray = [];

    // Genero el arreglo para la validacion
    $('#table_documento_financiero_proveedor tbody tr').each(function () {

        $licitacion = $(this).find('.licitacion').text();

        $fecha = $(this).find('.fecha').text();

        if (typeof validacionArray[$licitacion] === "undefined") {
            validacionArray[$licitacion] = $fecha;
        }
        else {

            if (getDateFromString($fecha) < getDateFromString(validacionArray[$licitacion])) {
                validacionArray[$licitacion] = $fecha;
            }
        }

    });


    $('#table_documento_financiero_proveedor tbody tr').each(function () {

        $licitacion = $(this).find('.licitacion').text();

        $fecha = $(this).find('.fecha').text();

        if (getDateFromString($fecha) > getDateFromString(validacionArray[$licitacion])) {
            //$(this).addClass('readonly');
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initLetraHandler() {

    $('#adif_contablebundle_comprobanteobra_letraComprobante').change(function () {

        var idLetraComprobanteA = $('select[ id ^= adif_contablebundle_comprobante][id $= letraComprobante] option')
                .filter(function () {
                    return $(this).html() == __letraComprobanteA;
                }).val();

        var idLetraComprobanteALeyenda = $('select[ id ^= adif_contablebundle_comprobante][id $= letraComprobante] option')
                .filter(function () {
                    return $(this).html() == __letraComprobanteALeyenda;
                }).val();

        var idLetraComprobanteY = $('select[ id ^= adif_contablebundle_comprobante][id $= letraComprobante] option')
                .filter(function () {
                    return $(this).html() == __letraComprobanteY;
                }).val();

        // Letra A, A con leyenda o Y, tiene IVA
        if ($(this).val() == idLetraComprobanteA || $(this).val() == idLetraComprobanteALeyenda || $(this).val() == idLetraComprobanteY) {

            $('select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[alicuotaIva\]"]').each(function () {
                if (!edit) {
                   /**
                    * Si esta editando el comprobante, no quiero que me cambie el valor que viene de la base de datos
                    * @gluis - 23/03/2018
                    */
                    $(this).select2('val', 3);
                }
            });

            $('select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[alicuotaIva\]"]').select2('readonly', false);
            $('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[montoIva\]"]').attr('readonly', false);
        }
        else {

            $('select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name $= "\[alicuotaIva\]"]').each(function () {
                if (!edit) {
                    /**
                    * Si esta editando el comprobante, no quiero que me cambie el valor que viene de la base de datos
                    * @gluis - 23/03/2018
                    */
                    $(this).select2('val', 1);
                }
            });

            $('select[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[alicuotaIva\]"]').select2('readonly', true);
            $('input[name^="adif_contablebundle_comprobante"][name *= "\[renglonesComprobante\]"][name$="\[montoIva\]"]').attr('readonly', true);
        }

        restringir_iva();
        recalcular_iva();
    });

}

/**
 * 
 * @returns {undefined}
 */
function initAgregarRenglonComprobanteHandler() {

    $('#agregar_renglon_comprobante').on('click', function (e) {

        crearRenglonComprobanteObra();

        initBorrarRenglonComprobanteHandler();

        restringir_iva();

        recalcular_netos();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initEditParcial() {

    // Si ES una edicion
    if (esEdit) {

        recalcular_netos();

        $('input[type="text"].no-editable, textarea.no-editable').prop('readonly', true);

        $('input[type="checkbox"].no-editable').closest('div.has-switch')
                .block(
                        {
                            message: null,
                            overlayCSS: {
                                backgroundColor: 'black',
                                opacity: 0.05,
                                cursor: 'not-allowed'}
                        }
                );

        $('input.datepicker.no-editable').each(function () {
            readonlyDatePicker($(this), true);
        });

        $('select.no-editable').select2('readonly', true);

        $('#table_documento_financiero_proveedor tbody tr').off('click');

        $('#table_documento_financiero_proveedor tbody tr').on('click', function (e) {
            e.stopImmediatePropagation();
        });

        $('#agregar_renglon_comprobante').remove();
        $('.renglon_comprobante_borrar').remove();

        $('#agregar_renglon_impuesto').remove();
        $('.renglon_impuesto_borrar').remove();

        $('#agregar_renglon_percepcion').remove();
        $('.renglon_percepcion_borrar').remove();

        $('#adif_contablebundle_comprobanteobra_montoValidacion')
                .val($('#adif_contablebundle_comprobanteobra_total').val());

    }
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: -9999999999, aSign: '$ ', aSep: '.', aDec: ','});
    });
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value.replace('-', '').replace('$', '').replace(/\./g, '').replace(/\,/g, '.').trim();
}