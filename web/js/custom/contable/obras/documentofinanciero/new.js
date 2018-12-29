
var isEdit = $('[name=_method]').length > 0;

var triggerInicial = false;

var $formularioDocumentoFinanciero = $('form[name="adif_contablebundle_obras_documentofinanciero"]');

var $fechaIngresoADIF = $('#adif_contablebundle_obras_documentofinanciero_fechaIngresoADIF');

var $fechaRemisionGerenciaAdministracion = $('#adif_contablebundle_obras_documentofinanciero_fechaRemisionGerenciaAdministracion');

var $fechaIngresoGerenciaAdministracion = $('#adif_contablebundle_obras_documentofinanciero_fechaIngresoGerenciaAdministracion');

var $fechaAprobacionTecnica = $('#adif_contablebundle_obras_documentofinanciero_fechaAprobacionTecnica');

var tramoSeleccionadoId;

var tramoSeleccionadoSaldoPendienteCertificacion;

var tramoSeleccionadoSaldoPendienteFondoReparo;

var collectionHolderArchivos;

$(document).ready(function () {

    updateDeleteLinks($('.prototype-link-remove-poliza'));
    updateDeleteLinks($(".prototype-link-remove-archivo"));

    initValidate();

    initRenglonLicitacionTable();

    initAutocompleteProveedor();

    initTipoDocumentoFinancieroHandler();

    initFechaDocumentoFinancieroHandler();

    initFechasDocumentoFinanciero();

    initMontoTotalHandler();

    initPolizaForm();

    initArchivosForm();

    initEditInputs();

    initSubmitButton();

});

/**
 *
 * @returns {undefined}
 * 
 */
function initValidate() {

    // Validacion para total del documento financiero
    $.validator.addMethod("totalDocumentoFinanciero", function (value, element, param) {

        var totalDocumentoFinanciero = parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').val()));
		if (esEdit == 1) {
			// El hidden #total_real_df es el valor real que viene de php de DF original y no sufre ningun cambio
			totalDocumentoFinanciero = parseFloat($('#total_real_df').val());
		}

        var montoSinIVA = parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoSinIVA').val()));

        var montoIVA = parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoIVA').val()));

        var montoFondoReparo = $('#adif_contablebundle_obras_documentofinanciero_montoFondoReparo').val() === ''
                ? 0
                : parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoFondoReparo').val()))
                ;
				
		var montoPercepciones = $('#adif_contablebundle_obras_documentofinanciero_montoPercepciones').val() === ''
				? 0
				: parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoPercepciones').val()))
				;

		var montoPercepciones = $('#adif_contablebundle_obras_documentofinanciero_montoPercepciones').val() === ''
				? 0
				: parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoPercepciones').val()))
				;

        // Si es un Fondo de Reparo
        if (getEsFondoReparo()) {

            // Valido que el monto sin IVA no supere el saldo de fondo de reparo
            // return montoSinIVA <= tramoSeleccionadoSaldoPendienteFondoReparo;
            return true;
        }

		console.debug("monto iva = " + montoIVA);
		console.debug("monto sin iva = " + montoSinIVA);
		console.debug("monto de reparo = " + montoFondoReparo);
		console.debug("percepciones = " + montoPercepciones);

        var totalCalculado = parseFloat(parseFloat(montoSinIVA + montoIVA + montoFondoReparo + montoPercepciones).toFixed(2));

		console.debug("total df calculado js = " + totalCalculado);
		console.debug("total df php = " + totalDocumentoFinanciero);
		console.debug(totalDocumentoFinanciero === totalCalculado)

		if (esEdit == 1) {
			console.debug("Es edicion no comparo contra los valores en la base (total df php), devuelvo true.")
			return true;
		}
		
        return totalDocumentoFinanciero === totalCalculado;
    });


    // Validacion del Formulario
    $formularioDocumentoFinanciero.validate();

    // Valido el total del documento financiero
    $('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').rules('add', {
        totalDocumentoFinanciero: true,
        messages: {
            totalDocumentoFinanciero: "El total del documento financiero no es válido"
        }
    });
}

/**
 *
 * @returns {undefined}
 */
function initRenglonLicitacionTable() {

    var options = {
        "searching": false,
        "ordering": true,
        "info": true,
        "paging": true,
        "pageLength": 12,
        "lengthMenu": [[12, 24, 48, 100, -1], [12, 24, 48, 100, "Todos"]],
        "drawCallback": function () {

            setMasks();

            if (!disabled) {
                initSeleccionarTramoHandler();
            }
        }
    };

    dt_init($('#table_tramo_proveedor'), options);
}

/**
 *
 * @returns {undefined}
 */
function initAutocompleteProveedor() {

    $('#adif_contablebundle_obras_documentofinanciero_proveedor').autocomplete({
        source: __AJAX_PATH__ + 'proveedor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            completarTramos(event, ui, null);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };
}

function completarTramos(event, ui, idTramoSeleccionado) {

    $('#adif_contablebundle_obras_documentofinanciero_proveedor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_obras_documentofinanciero_proveedor_cuit').val(ui.item.CUIT);

    $('#table_tramo_proveedor_wrapper').hide();
    $('#table_tramo_proveedor').hide();

    $.ajax({
        url: __AJAX_PATH__ + 'obras/tramos/index_table/',
        data: {id_proveedor: ui.item.id}
    }).done(function (result) {

        var tramos = JSON.parse(result).data;

        $('#table_tramo_proveedor').DataTable().rows().remove().draw();

        if (tramos.length > 0) {

            $('#div-no-result').hide();

            $('#table_tramo_proveedor tbody').empty();

            $('#table_tramo_proveedor_wrapper').show();
            $('#table_tramo_proveedor').show();

            $('.row_table_renglon_licitacion_proveedor').show();

            $(tramos).each(function () {

                var $tr = $('<tr />', {
                    id_tramo: this[0],
                    fecha_licitacion: this[5],
                    pct_fondo_reparo: this[6],
                    style: 'cursor: pointer;'}
                );

                $tr.on('click', function () {
                    $(this).parents('tbody').find('tr').removeClass('active');
                    $(this).addClass('active');
                });

                $('<td />', {text: this[1]}).appendTo($tr);
                $('<td />', {text: this[2]}).appendTo($tr);
                $('<td />', {text: this[3]}).addClass('money-format saldoPendienteCertificacion').appendTo($tr);
                $('<td />', {text: this[4]}).addClass('money-format saldoPendienteFondoReparo').appendTo($tr);

                $('#table_tramo_proveedor').DataTable().row.add($tr);

                if (idTramoSeleccionado && this[0] == idTramoSeleccionado) {

                    tramoSeleccionadoId = idTramoSeleccionado;

                    triggerInicial = true;

                    $tr.trigger('click');
                }
            });

            setMasks();

            $('#table_tramo_proveedor').DataTable().draw();
        }
        else {

            $('#table_tramo_proveedor tbody').empty();
            ;
            $('#table_tramo_proveedor_wrapper').hide();
            $('#table_tramo_proveedor').hide();

            $('#div-no-result').show();
        }
        $('#table_tramo_proveedor>tbody>tr').unbind('click')
    });

}

/**
 *
 * @returns {undefined}
 */
function initSeleccionarTramoHandler() {

    $(document).on('click', '#table_tramo_proveedor tbody tr', function (e, tr) {

        // Seteo el ID del Tramo seleccionado
        tramoSeleccionadoId = $(this).attr('id_tramo');

        $('select[id ^= adif_contablebundle_obras][id $= tramo]').val(tramoSeleccionadoId);


        // Seteo el saldo pendiente de certificacion del Tramo seleccionado
        tramoSeleccionadoSaldoPendienteCertificacion = parseFloat(clearCurrencyValue($(this).find('.saldoPendienteCertificacion').text()));

        // Seteo el saldo pendiente de fondo de reparo del Tramo seleccionado
        tramoSeleccionadoSaldoPendienteFondoReparo = parseFloat(clearCurrencyValue($(this).find('.saldoPendienteFondoReparo').text()));

        // Actualizo datepicker de fecha de DocumentoFinanciero segun fecha de Licitacion
        if (triggerInicial) {
            triggerInicial = false;
        } else {
            $('#adif_contablebundle_obras_documentofinanciero_fechaDocumentoFinancieroInicio').val(null);
            $('#adif_contablebundle_obras_documentofinanciero_fechaDocumentoFinancieroFin').val(null);
        }

        var tramoSeleccionadoFechaAperturaLicitacion = $(this).attr('fecha_licitacion');

        var tramoSeleccionadoFechaAperturaLicitacionDate = getDateFromString(tramoSeleccionadoFechaAperturaLicitacion);

        $('#adif_contablebundle_obras_documentofinanciero_fechaDocumentoFinancieroInicio')
                .datepicker('setStartDate', tramoSeleccionadoFechaAperturaLicitacionDate);


        filterTipoDocumentoFinanciero();

        initTotalDocumentoFinanciero();

        if (getEsFondoReparo()) {
            calcularPorcentajeAbonar();
        }

        validarPolizasVencidas();

        // Marco el TR seleccionado como "selected"
        $('#table_tramo_proveedor').DataTable().rows('.selected')
                .nodes().to$().removeClass('selected');

        $(this).addClass('selected');
        $(this).removeClass('active');
    });
}

/**
 *
 * @returns {undefined}
 */
function initTipoDocumentoFinancieroHandler() {

    showSubclassInputs($('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero'));

    $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero').on('change', function () {

        $('.subclase').find('input')
                .prop('required', false)
                .val(null)
                .keyup();

        showSubclassInputs($(this));

        // Si hay un tramo seleccionado
        if ($('tr.selected').length > 0) {

            if (getEsFondoReparo()) {

                $('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero')
                        .val(tramoSeleccionadoSaldoPendienteFondoReparo);

                $('#adif_contablebundle_obras_documentofinanciero_montoSinIVA')
                        .val(tramoSeleccionadoSaldoPendienteFondoReparo);

                calcularPorcentajeAbonar();
            }
            else {

                $('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero')
                        .val(null);

                $('#adif_contablebundle_obras_documentofinanciero_montoSinIVA')
                        .val(null);

            }
        }

    });
}

/**
 *
 * @returns {undefined}
 */
function filterTipoDocumentoFinanciero() {

    if (tramoSeleccionadoSaldoPendienteCertificacion === 0) {

        var idAnticipoFinanciero = $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero').find('option')
                .filter(function () {
                    return  ($(this).html().toLowerCase().indexOf("anticipo") >= 0);
                }).val();

        $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero option[value=' + idAnticipoFinanciero + ']')
                .addClass('hidden');

        var idCertificadoObra = $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero').find('option')
                .filter(function () {
                    return  ($(this).html().toLowerCase().indexOf("certificado") >= 0);
                }).val();

        $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero option[value=' + idCertificadoObra + ']')
                .addClass('hidden');


        var idFondoReparo = $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero').find('option')
                .filter(function () {
                    return  ($(this).html().toLowerCase().indexOf("fondo de reparo") >= 0);
                }).val();

        $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero')
                .select2('val', idFondoReparo);

        $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero')
                .trigger('change');
    }
    else {

        $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero option.hidden')
                .removeClass('hidden');
    }

}

/**
 *
 * @returns {undefined}
 */
function initTotalDocumentoFinanciero() {

    // Si el saldo pendiente de certificacion es cero
    if (tramoSeleccionadoSaldoPendienteCertificacion === 0) {

        $('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero')
                .val(tramoSeleccionadoSaldoPendienteFondoReparo);

        $('#adif_contablebundle_obras_documentofinanciero_montoSinIVA')
                .val(tramoSeleccionadoSaldoPendienteFondoReparo);
    }
}

/**
 *
 * @returns {undefined}
 */
function initMontoTotalHandler() {

    $('#adif_contablebundle_obras_documentofinanciero_montoSinIVA').bind('change paste keyup', function () {

        calcularMontoTotalDocumentoFinanciero();

        $('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').keyup();

    });
	
    $('#adif_contablebundle_obras_documentofinanciero_montoIVA').bind('change paste keyup', function () {

        calcularMontoTotalDocumentoFinanciero();

		$('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').keyup();
    });

    $('#adif_contablebundle_obras_documentofinanciero_montoFondoReparo').bind('change paste keyup', function () {

        calcularMontoTotalDocumentoFinanciero();

        $('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').keyup();

    });
	
	$('#adif_contablebundle_obras_documentofinanciero_montoPercepciones').bind('change paste keyup', function () {

        calcularMontoTotalDocumentoFinanciero();

        $('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').keyup();

    });
	

    $('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').blur(function () {

        if (getEsFondoReparo()) {

            calcularPorcentajeAbonar();
        }
        else {

            $tipoCertificadoObra = $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero');

            if ($tipoCertificadoObra.val() === __tipoEconomia || $tipoCertificadoObra.val() === __tipoDemasia) {
                $('#adif_contablebundle_obras_documentofinanciero_montoFondoReparo').val(0);
            }
        }
    });
}

/**
 *
 * @param {type} $tipoCertificadoObra
 * @returns {undefined}
 */
function showSubclassInputs($tipoCertificadoObra) {

    $('.subclase').hide();

    // Inputs comunes para todos los documentos financieros
    $('.todos-documento-financiero').find('input').prop('required', true);
    $('.todos-documento-financiero').show();

    // Si el tipo de documento financiero es "CertificadoObra"
    if ($tipoCertificadoObra.val() === __tipoCertificadoObra) {

        $('.certificado-obra').find('input').prop('required', true);
        $('.certificado-obra').show();
    }

    // Si el tipo de documento financiero es "RedeterminacionObra"
    if ($tipoCertificadoObra.val() === __tipoRedeterminacionObra) {

        $('.redeterminacion-obra').find('input').prop('required', true);
        $('.redeterminacion-obra').show();
    }

    // Si el tipo de documento financiero es "AnticipoFinanciero"
    if ($tipoCertificadoObra.val() === __tipoAnticipoFinanciero) {

        $('.anticipo-financiero').find('input').prop('required', true);
        $('.anticipo-financiero').show();
    }

    // Si el tipo de documento financiero es "FondoReparo"
    if ($tipoCertificadoObra.val() === __tipoFondoReparo) {

        $('.fondo-reparo').find('input').prop('required', true);
        $('.fondo-reparo').show();
    }

    if ($tipoCertificadoObra.val() === __tipoEconomia || $tipoCertificadoObra.val() === __tipoDemasia) {
        $('#adif_contablebundle_obras_documentofinanciero_save_continue').hide();
    }
    else {
        $('#adif_contablebundle_obras_documentofinanciero_save_continue').show();
    }
}

/**
 *
 * @returns {undefined}
 */
function initFechaDocumentoFinancieroHandler() {

    $('#adif_contablebundle_obras_documentofinanciero_fechaDocumentoFinancieroFin')
            .on('change', function () {

                validarPolizasVencidas();

                updateFechasDocumentoFinanciero();
            });
}

/**
 *
 * @returns {undefined}
 */
function initFechasDocumentoFinanciero() {

    if (!isEdit) {

        readonlyDatePicker($fechaIngresoADIF, true);
        readonlyDatePicker($fechaRemisionGerenciaAdministracion, true);
        readonlyDatePicker($fechaIngresoGerenciaAdministracion, true);
        readonlyDatePicker($fechaAprobacionTecnica, true);
    }
}

/**
 *
 * @returns {undefined}
 */
function initPolizaForm() {

    collectionHolderPoliza = $('div.prototype-poliza');

    collectionHolderPoliza.data('index', collectionHolderPoliza.find(':input').length);

    $('.prototype-link-add-poliza').on('click', function (e) {
        e.preventDefault();

        addPolizaForm(collectionHolderPoliza);

        initCurrencies();
    });
}

/**
 *
 * @param {type} $collectionHolder
 * @returns {addPolizaForm}
 */
function addPolizaForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var polizaForm = prototype.replace(/__poliza__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-poliza').closest('.row').before(polizaForm);

    initDatepickers($('.row_poliza').last());

    var $polizaDeleteLink = $(".prototype-link-remove-poliza");

    updateDeleteLinks($polizaDeleteLink);
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
 * @returns {undefined}
 */
function initEditInputs() {

    if (isEdit) {

        $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero')
                .select2('readonly', true);
    }
}

/**
 *
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_obras_documentofinanciero_save').on('click', function (e) {

        if ($formularioDocumentoFinanciero.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el documento financiero?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'save'};

                        $formularioDocumentoFinanciero.addHiddenInputData(json);
                        $formularioDocumentoFinanciero.submit();
                    }
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;
    });

    // Handler para el boton "Guardar y cargar comprobante"
    $('#adif_contablebundle_obras_documentofinanciero_save_continue').on('click', function (e) {

        if ($formularioDocumentoFinanciero.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el documento financiero y cargar el comprobante?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'save_continue'};

                        $formularioDocumentoFinanciero.addHiddenInputData(json);
                        $formularioDocumentoFinanciero.submit();
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

        var formularioValido = true;

        if (!tramoSeleccionadoId) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "Debe seleccionar un tramo para continuar."
            });

            show_alert(options);

            formularioValido = false;
        } else {

            prefix  =   'adif_contablebundle_obras_documentofinanciero_polizasSeguro_';
            fIni    =   $("input[id^='"+prefix+"'][id$='fechaInicio']");
            fFin    =   $("input[id^='"+prefix+"'][id$='fechaVencimiento']");

            if ( fIni.length > 0 && fFin.length > 0 ) {
                fIni    =   fIni.val().split('/');
                fIni.reverse();
                fIni    =   fIni.join('/');
                fFin    =   fFin.val().split('/');
                fFin.reverse();
                fFin    =   fFin.join('/');
            }

            if ( ( (parseInt(fIni.length) + parseInt(fFin.length)) == 0) || Date.parse(fIni) < Date.parse(fFin) )
            {

                var data = {
                    idTramo: tramoSeleccionadoId
                };

                $.ajax({
                    type: 'post',
                    async: false,
                    url: __AJAX_PATH__ + 'documento_financiero/validaciones/',
                    data: data
                }).done(function (validaciones) {

                    var $tipoCertificadoObra = $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero');

                    // Si el tipo de documento financiero es "CertificadoObra"
                    if ($tipoCertificadoObra.val() === __tipoCertificadoObra) {

                        // formularioValido = validarCertificadoObra(validaciones);
                    }

                    // Si el tipo de documento financiero es "RedeterminacionObra"
                    if ($tipoCertificadoObra.val() === __tipoRedeterminacionObra) {

                        // formularioValido = validarRedeterminacionObra(validaciones);
                    }
                });
            } else {
                var options = $.extend({
                    title: 'Ha ocurrido un error',
                    msg: "Debe seleccionar una fecha de vencimiento posterior a fecha de fin"
                });

                show_alert(options);

                formularioValido = false;
            }
        }

        return formularioValido;
    }

/**
 *
 * @param {type} validaciones
 * @returns {Boolean}
 */
function validarCertificadoObra(validaciones) {

    var __numeroUltimoCertificado = parseInt(validaciones['numero_ultimo_certificado']);
    var __fechaUltimoCertificado = validaciones['fecha_ultimo_certificado'];

    var formularioValido = true;

    if (__numeroUltimoCertificado !== -1) {

        var $numeroCertificado = parseInt($('#adif_contablebundle_obras_documentofinanciero_numero').val());

        var $fechaCertificado = $('#adif_contablebundle_obras_documentofinanciero_fechaDocumentoFinancieroInicio').val();

        if ((__numeroUltimoCertificado + 1) !== $numeroCertificado) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "El número de certificado no es correlativo al anterior."
            });

            show_alert(options);

            formularioValido = false;
        }

        var fechaUltimoCertificadoDate = getDateFromString(__fechaUltimoCertificado);
        var fechaCertificadoDate = getDateFromString($fechaCertificado);

        if (fechaUltimoCertificadoDate.getTime() > fechaCertificadoDate.getTime()) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "La fecha de certificado es mayor a la del certificado anterior."
            });

            show_alert(options);

            formularioValido = false;
        }
    }

    return formularioValido;
}

/**
 *
 * @param {type} validaciones
 * @returns {Boolean}
 */
function validarRedeterminacionObra(validaciones) {

    var __numeroUltimaRedeterminacion = parseInt(validaciones['numero_ultima_redeterminacion']);
    var __fechaUltimaRedeterminacion = validaciones['fecha_ultima_redeterminacion'];

    var formularioValido = true;

    if (__numeroUltimaRedeterminacion !== -1) {

        var $numeroRedeterminacion = parseInt($('#adif_contablebundle_obras_documentofinanciero_numero').val());

        var $fechaRedeterminacion = $('#adif_contablebundle_obras_documentofinanciero_fechaDocumentoFinancieroInicio').val();

        if ((__numeroUltimaRedeterminacion + 1) !== $numeroRedeterminacion) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "El número de redeterminación no es correlativo al anterior."
            });

            show_alert(options);

            formularioValido = false;
        }

        var fechaUltimaRedeterminacionDate = getDateFromString(__fechaUltimaRedeterminacion);
        var fechaRedeterminacionDate = getDateFromString($fechaRedeterminacion);

        if (fechaUltimaRedeterminacionDate.getTime() > fechaRedeterminacionDate.getTime()) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "La fecha de redeterminación es mayor a la de la redeterminación anterior."
            });

            show_alert(options);

            formularioValido = false;
        }
    }

    return formularioValido;
}

/**
 *
 * @returns {undefined}
 */
function validarPolizasVencidas() {

    var idTramo = $('#adif_contablebundle_obras_documentofinanciero_tramo').val();

    var fecha = $('#adif_contablebundle_obras_documentofinanciero_fechaDocumentoFinancieroFin').val();

    if (idTramo !== '' && fecha !== '') {

        var data = {
            id: idTramo,
            fecha: fecha
        };

        $.ajax({
            type: "POST",
            data: data,
            url: __AJAX_PATH__ + 'documento_financiero/polizas_vencidas/'
        }).done(function (cantidadPolizasVencidas) {

            if (cantidadPolizasVencidas > 0) {

                var mensaje = 'El tramo seleccionado posee <span class="bold">'
                        + cantidadPolizasVencidas
                        + '</span> p&oacute;lizas vencidas al '
                        + fecha;

                showFlashMessage('warning', mensaje);
            }
        });
    }
}

/**
 *
 * @returns {undefined}
 */
function updateFechasDocumentoFinanciero() {

    var fechaDocumentoFinanciero = $('#adif_contablebundle_obras_documentofinanciero_fechaDocumentoFinancieroFin')
            .val();

    $fechaIngresoADIF.val(null);
    $fechaRemisionGerenciaAdministracion.val(null);
    $fechaIngresoGerenciaAdministracion.val(null);
    $fechaAprobacionTecnica.val(null);

    if (fechaDocumentoFinanciero !== '') {

        var fechaDocumentoFinancieroDate = getDateFromString(fechaDocumentoFinanciero);

        $fechaIngresoADIF.datepicker('setStartDate', fechaDocumentoFinancieroDate);

        $fechaRemisionGerenciaAdministracion.datepicker('setStartDate', fechaDocumentoFinancieroDate);

        $fechaIngresoGerenciaAdministracion.datepicker('setStartDate', fechaDocumentoFinancieroDate);

        $fechaAprobacionTecnica.datepicker('setStartDate', fechaDocumentoFinancieroDate);

        readonlyDatePicker($fechaIngresoADIF, false);
        readonlyDatePicker($fechaRemisionGerenciaAdministracion, false);
        readonlyDatePicker($fechaIngresoGerenciaAdministracion, false);
        readonlyDatePicker($fechaAprobacionTecnica, false);

    }
    else {

        readonlyDatePicker($fechaIngresoADIF, true);
        readonlyDatePicker($fechaRemisionGerenciaAdministracion, true);
        readonlyDatePicker($fechaIngresoGerenciaAdministracion, true);
        readonlyDatePicker($fechaAprobacionTecnica, true);

    }
}

/**
 *
 * @returns {undefined}
 */
function calcularPorcentajeAbonar() {

    var montoTotalDocumentoFinanciero = parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').val()));

    var porcentajeAbonar = montoTotalDocumentoFinanciero / tramoSeleccionadoSaldoPendienteFondoReparo * 100;

    var porcentajeAbonarFormatted = porcentajeAbonar.toString().replace(/\./g, ',');

    $('#adif_contablebundle_obras_documentofinanciero_porcentajeAbonar').val(porcentajeAbonarFormatted);
}

/**
 *
 * @returns {Boolean}
 */
function getEsFondoReparo() {

    var $selectedOption = $('#adif_contablebundle_obras_documentofinanciero_tipoDocumentoFinanciero option:selected');

    // Busco el option con el texto "Fondo de Reparo"
    return  $selectedOption.html().toLowerCase().indexOf("fondo de reparo") >= 0;
}

/**
 *
 * @returns {undefined}
 */
function calcularMontoTotalDocumentoFinanciero() {

//    var montoSinIVA = parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoSinIVA').val()));
//
//    var montoIVA = parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoIVA').val()));
//
//    var montoFondoReparo = $('#adif_contablebundle_obras_documentofinanciero_montoFondoReparo').val() === ''
//            ? 0
//            : parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoFondoReparo').val()))
//            ;
//
//    var totalCalculado = parseFloat(parseFloat(montoSinIVA + montoIVA - montoFondoReparo).toFixed(2))
//            .toString().replace(/\./g, ',');
//
//    $('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').val(totalCalculado);

        var totalDocumentoFinanciero = parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').val()));
		if (esEdit == 1) {
			// El hidden #total_real_df es el valor real que viene de php de DF original y no sufre ningun cambio
			totalDocumentoFinanciero = parseFloat($('#total_real_df').val());
		}

        var montoSinIVA = parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoSinIVA').val()));

        var montoIVA = parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoIVA').val()));

        var montoFondoReparo = $('#adif_contablebundle_obras_documentofinanciero_montoFondoReparo').val() === ''
                ? 0
                : parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoFondoReparo').val()))
                ;

		var montoPercepciones = $('#adif_contablebundle_obras_documentofinanciero_montoPercepciones').val() === ''
				? 0
				: parseFloat(clearCurrencyValue($('#adif_contablebundle_obras_documentofinanciero_montoPercepciones').val()))
				;

		console.debug("monto iva = " + montoIVA);
		console.debug("monto sin iva = " + montoSinIVA);
		console.debug("monto de reparo = " + montoFondoReparo);
		console.debug("percepciones = " + montoPercepciones);

        var totalCalculado = parseFloat(parseFloat(montoSinIVA + montoIVA + montoFondoReparo + montoPercepciones).toFixed(2)).toString().replace(/\./g, ',');

		console.debug("total df calculado js = " + totalCalculado);
		console.debug("total df php = " + totalDocumentoFinanciero);
		//console.debug(totalDocumentoFinanciero === totalCalculado);

		$('#adif_contablebundle_obras_documentofinanciero_montoTotalDocumentoFinanciero').val(totalCalculado);
}

/**
 *
 * @returns {undefined}
 */
function setMasks() {

    $('.numero-documento-financiero').inputmask({
        mask: "9999",
        placeholder: "_",
        numericInput: true,
        onincomplete: function () {
            $(this).val($(this).val().replace(/_/g, '0'));
        }
    });

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-999999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
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