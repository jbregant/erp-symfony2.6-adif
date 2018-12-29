var $collectionHolder;

jQuery(document).ready(function () {

    initNumericValues();

    initValidaciones();

    initCamposFormulario();

    initSelects();

    initTooltip();

    initAnio();

    updateConceptoDeleteLink();

    initSubmitHandler();

    $collectionHolder = $('div.prototype-concepto-formulario572');

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $('.prototype-link-add-concepto-formulario572').on('click', function (e) {
        e.preventDefault();

        addConceptoForm($collectionHolder);

        initSelects();

        initCurrencies();
    });

    initCUIT();

    initSelectHandler();

    restringirCamposConcepto();

});


// Actualiza el form si se trata de un concepto que es cargaFamiliar
function actualizarForm(concepto) {
    if ($(concepto).val() != '') {
        if (conceptos[$(concepto).val()]['detalle']) {
            concepto.closest('.row').find('.detallef572').removeClass('hidden');
            if (conceptos[$(concepto).val()]['cargaFamiliar']) {
                concepto.closest('.row').find('.montof572').addClass('hidden');
            } else {
                concepto.closest('.row').find('.montof572').removeClass('hidden');
            }
        } else {
            concepto.closest('.row').find('.detallef572').addClass('hidden');
        }

        if (conceptos[$(concepto).val()]['esOtrosIngresos']) {
            $(concepto).parents('.concepto572row').addClass('otrosIngresos');
            $(concepto).parents('.concepto572row').find('.mesHasta').parent().parent().hide();

            var nuevoLabel = conceptos[$(concepto).val()]['periodo'];
            $(concepto).parents('.concepto572row').find('.mesDesde').parent().parent().find('label').html(nuevoLabel);
            if (nuevoLabel == 'Mes') {
                $(concepto).parents('.concepto572row').find('.mesDesde').inputmask("Regex", {regex: "^([1-9]|10|11|12)$", rightAlign: true});
            } else {
                $(concepto).parents('.concepto572row').find('.mesDesde').inputmask("Regex", {regex: "^([0-9]{1,4})$", rightAlign: true});
            }

        } else {
            $(concepto).select2('readonly', false);
            $(concepto).parents('.concepto572row').find('.mesDesde').parent().parent().find('label').html('Desde');
            $(concepto).parents('.concepto572row').find('.mesHasta').parent().parent().show();
            $(concepto).parents('.concepto572row').find('.mesDesde').inputmask("Regex", {regex: "^([1-9]|10|11|12)$", rightAlign: true});
        }
    }
}

function initSelectHandler() {
    $(document).on("change", '[id^=adif_recursoshumanosbundle_formulario572_conceptos][id$=conceptoGanancia]', function () {
        actualizarForm($(this));
        $(this).closest('.row').find('[id^=adif_recursoshumanosbundle_formulario572_conceptos][id$=mesDesde]').val(1);
        $(this).closest('.row').find('[id^=adif_recursoshumanosbundle_formulario572_conceptos][id$=mesHasta]').val(12);
        $(this).closest('.row').find('[id^=adif_recursoshumanosbundle_formulario572_conceptos][id$=monto]').val(0);
    });
    $('[id^=adif_recursoshumanosbundle_formulario572_conceptos][id$=conceptoGanancia]').not('.nuevoConcepto').select2('readonly', true);
}


function initValidaciones() {
    $.validator.addMethod("equalYear", function (value, element, param) {
        anioElegido = $('#adif_recursoshumanosbundle_formulario572_anio').val();
        return (jQuery.inArray(parseInt(anioElegido), aniosFormularios)) == -1;
    });
    $.validator.addMethod("biggerYear", function (value, element, param) {
        anioElegido = $('#adif_recursoshumanosbundle_formulario572_anio').val();
        return anioElegido <= anioActual;
    });

    // Validacion del Formulario
    $('form[name=adif_recursoshumanosbundle_formulario572]').validate({
        rules: {
            'adif_recursoshumanosbundle_formulario572[anio]': {
                equalYear: true,
                biggerYear: true
            }
        },
        messages: {
            'adif_recursoshumanosbundle_formulario572[anio]': {
                equalYear: "Ya posee formulario en ese año",
                biggerYear: "No puede cargar el formulario de ese año aún"
            }
        }
    });
}

function initCamposFormulario() {
    $("[id^=adif_recursoshumanosbundle_formulario572_conceptos][id$=conceptoGanancia]").each(function () {
        actualizarForm($(this));
        //console.debug($(this).val());
        if (typeof conceptos[$(this).val()] != 'undefined') {
            if (!(conceptos[$(this).val()]['esOtrosIngresos'])) {
                $(this).addClass('nuevoConcepto');
            }
        } else {
            return false;
        }
    });
    
    //$('.concepto572row[es-borrable="0"] .row-boton-eliminar').remove();
}


/**
 * 
 * @returns {undefined}
 */
function updateConceptoDeleteLink() {

    $(".prototype-link-remove-concepto-formulario572").each(function () {

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var concepto = $(this).closest('.concepto572row');

            show_confirm({
                msg: 'Desea eliminar el concepto?',
                callbackOK: function () {
                    concepto.hide('slow', function () {
                        concepto.remove();
                    });
                }
            });

            e.stopPropagation();

        });
    });
}


/**
 * 
 * @param {type} $collectionHolder
 * @returns {addConceptoForm}
 */
function addConceptoForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var conceptoForm = prototype.replace(/__concepto__/g, index);
    conceptoForm = conceptoForm.replace("es-editable=\'0\'", "es-editable=\'1\'");
    conceptoForm = conceptoForm.replace("es-borrable=\'0\'", "es-borrable=\'1\'");
    conceptoForm = conceptoForm.replace("es-editable-monto=\'0\'", "es-editable-monto=\'1\'");

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-concepto-formulario572').closest('.row').before(conceptoForm);

    initSelects();

    updateConceptoDeleteLink();

    $('.concepto572row').last().find('select').addClass('nuevoConcepto');

    initCUIT();

    initNumericValues();

}

/**
 * 
 * @returns {undefined}
 */
function initTooltip() {
    $('.prototype-link-add-concepto-formulario572').tooltip();
}

function initNumericValues() {
    $('.mesHasta').inputmask("Regex", {regex: "^([1-9]|10|11|12)$", righttAlign: true});
}

function initCUIT() {

    if ($('input[name^="adif_recursoshumanosbundle_formulario572"][name$="\[cuit\]"]').size() > 0) {

        $('input[name^="adif_recursoshumanosbundle_formulario572"][name$="\[cuit\]"]').rules('add', {
            required: true,
            cuil: true,
            messages: {
                cuil: "Formato de CUIT incorrecto."
            }
        });

        $('input[name^="adif_recursoshumanosbundle_formulario572"][name$="\[cuit\]"]').inputmask({
            mask: "99-99999999-9",
            placeholder: "_"
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initSelects() {
    $('select.choice').each(function () {
        $(this).select2({
            allowClear: true
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initAnio() {
    $('#adif_recursoshumanosbundle_formulario572_anio').parent().wrap('<div class="input-group"></div>');

    initDatepicker($('#adif_recursoshumanosbundle_formulario572_anio'), {
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });

    $('<span class="input-group-addon"><i class="fa fa-calendar"></i></span>')
            .insertBefore($('#adif_recursoshumanosbundle_formulario572_anio').parent());

    $('#adif_recursoshumanosbundle_formulario572_anio').prop('readonly', true);
}


function restringirCamposConcepto() {
    $('.otrosIngresos.concepto572row[es-editable-monto="1"]').find('.montoConcepto').prop('readonly', false);
    $('.otrosIngresos.concepto572row[es-editable-monto="1"]').find('input').not('.montoConcepto').prop('readonly', true);
    $('.otrosIngresos.concepto572row[es-editable="1"]').find('.inputEditable').prop('readonly', false);
    $('.otrosIngresos.concepto572row[es-editable="1"]').find('input').not('.inputEditable').prop('readonly', true);
    $('.otrosIngresos.concepto572row[es-editable-monto="0"][es-editable="0"]').find('input').prop('readonly', true);
}

function validarOtrosIngresos() {
    otrosIngresos = [];
    $('.concepto572row').each(function () {

        if ($(this).find("[id^=adif_recursoshumanosbundle_formulario572_conceptos][id$=conceptoGanancia]").val() == '') {
            return false;
        }

        if (conceptos[$(this).find("[id^=adif_recursoshumanosbundle_formulario572_conceptos][id$=conceptoGanancia]").val()]['esOtrosIngresos']) {
            otrosIngresos.push({'concepto': $(this).find('.conceptoConcepto').select2("val"), 'cuit': $(this).find('.cuitConcepto').val(), 'mesDesde': $(this).find('.mesDesde').val()});
        }
    });
    var unicos = _.uniq(otrosIngresos, function (item) {
        return JSON.stringify(item);
    });
    return (unicos.length == otrosIngresos.length);
}

function initSubmitHandler() {
    $('#adif_recursoshumanosbundle_formulario572_submit').click(function (e) {

        e.preventDefault();

        if (!validarOtrosIngresos()) {

            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "No puede ingresar ajustes o remuneraciones para un mismo cuit en un mismo período."
            });

            show_alert(options);

            return false;
        }

        limpiarCUITs();
        var formulario = $('form[name=adif_recursoshumanosbundle_formulario572]').validate();
        var formulario_result = formulario.form();
        if (formulario_result) {
            $('form').submit();
        } else {
            initCUIT();
        }

    });
}

function limpiarCUITs() {
    $('[id^=adif_recursoshumanosbundle_formulario572_conceptos][id$=cuit]').each(function () {
        if ($(this).val() == '') {
            $(this).rules("remove");
            $(this).inputmask('remove');
            $(this).val('');
        }
    });
}