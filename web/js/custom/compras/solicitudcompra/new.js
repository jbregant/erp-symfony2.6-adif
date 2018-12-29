
var $collectionHolder;


/**
 * 
 */
jQuery(document).ready(function () {

    // Validacion del Formulario
    $('form[name=adif_comprasbundle_solicitudcompra]').validate();

    configSubmitButtons();

    initSelects();

    setMasks();

    updateJustiprecioTotal();

    updateRenglonSolicitudDeleteLink();

    $collectionHolder = $('div.prototype-renglon-solicitud');

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $('.prototype-link-add-renglon-solicitud').on('click', function (e) {
        e.preventDefault();

        addRenglonSolicitudForm($collectionHolder);

        initSelects();
    });

    initChainedSelects();

    $('form[name=adif_comprasbundle_solicitudcompra]').submit(function () {
        checkErrors();
    });

    initAltaBienEconomicoButton();

    initSolicitudFromPedidoInternoHandler();

});

/**
 * 
 * @returns {undefined}
 */
function configSubmitButtons() {

    // Handler para el boton "Guardar Borrador"
    $('#adif_comprasbundle_solicitudcompra_save').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_solicitudcompra]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar la solicitud como borrador?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'save'};

                        $('form[name=adif_comprasbundle_solicitudcompra]').addHiddenInputData(json);
                        $('form[name=adif_comprasbundle_solicitudcompra]').submit();
                    }
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;

    });

    // Handler para el boton "Finalizar Solicitud"
    $('#adif_comprasbundle_solicitudcompra_close').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_solicitudcompra]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea finalizar la solicitud?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'close'};

                        $('form[name=adif_comprasbundle_solicitudcompra]').addHiddenInputData(json);
                        $('form[name=adif_comprasbundle_solicitudcompra]').submit();
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
 * @returns {Boolean}
 */
function validForm() {

    // Si la Solicitud tiene renglones cargados
    if ($('.renglon-solicitud-content').length > 0) {

        $('.changeable').each(function () {

            $(this).val(clearCurrencyValue($(this).val()));
        });
    }
    else {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe cargar al menos un renglón a la solicitud."
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
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '0.0000', vMax: '9999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});
    });

    $('.currency-format').each(function () {
        $(this).autoNumeric('init', {vMin: '0.0000', vMax: '9999999999.9999', aSep: '.', aDec: ','});
    });

}

/**
 * 
 * @param {type} $especificacionTecnicaInput
 * @returns {undefined}
 */
function initEspecificacionTecnicaInput($especificacionTecnicaInput) {

    var dataFile = $especificacionTecnicaInput.data('file');

    // Si la ET fue especificada
    if (typeof dataFile !== 'undefined') {
        $especificacionTecnicaInput.prop('required', false).keyup();
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateJustiprecioTotal() {

    var update = function ($row) {

        var $fieldset = $row.parent('fieldset');

        $cantidadSolicitada = clearCurrencyValue(($fieldset.find('input[id $= cantidadSolicitada]').val()));

        $justiprecioUnitario = clearCurrencyValue($fieldset.find('input[id $= justiprecioUnitario]').val());

        $justiprecioTotalRenglon = $cantidadSolicitada * $justiprecioUnitario;

        if (!$.isNumeric($justiprecioTotalRenglon)) {
            $justiprecioTotalRenglon = 'Error';
        }
        else if ($justiprecioTotalRenglon) {
            $justiprecioTotalRenglon = (parseFloat($justiprecioTotalRenglon)).toString().replace(/\./g, ',');
        }

        $fieldset.find('input[id $= justiprecioTotal]').val($justiprecioTotalRenglon)
                .autoNumeric('update');

        updateJustiprecioSolicitud();
    };

    $('.changeable').each(function () {

        var $row = $(this).closest('.row');

        update($row);

        $(this).bind('change paste keyup', function () {
            update($row);
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateJustiprecioSolicitud() {
    $justiprecioTotalSolicitud = 0;

    $('input[id $= justiprecioTotal]').each(function () {
        $justiprecioTotalSolicitud += parseFloat(clearCurrencyValue($(this).val()));
    });

    $('input[id $= justiprecio]').val($justiprecioTotalSolicitud.toString().replace(/\./g, ',')).autoNumeric('update');
}

/**
 * 
 * @returns {undefined}
 */
function updateRenglonSolicitudDeleteLink() {

    $(".prototype-link-remove-renglon-solicitud").each(function () {

        $(this).tooltip();

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var renglonSolicitud = $(this).closest('.renglon-solicitud-content');

            show_confirm({
                msg: '¿Desea eliminar el renglón?',
                callbackOK: function () {
                    renglonSolicitud.hide('slow', function () {
                        renglonSolicitud.remove();
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
 * @returns {addRenglonSolicitudForm}
 */
function addRenglonSolicitudForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var renglonSolicitudForm = prototype.replace(/__renglon_solicitud__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-renglon-solicitud').closest('.row').before(renglonSolicitudForm);

    updateRenglonSolicitudDeleteLink();

    setMasks();

    updateJustiprecioTotal();

    initSelects();

    initFileInput();

    initAltaBienEconomicoButton();

    var $row = $('.prototype-link-add-renglon-solicitud').parents('.row').prev('fieldset');

    initChainedSelects($row);
}

/**
 * 
 * @param {type} row
 * @returns {undefined}
 */
function initChainedSelects(row) {

    var isEdit = $('[name=_method]').length > 0;

    var $bienEconomicoVal = [];

    if (row) {
        $selectRubro = $(row).find('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= rubro]');
    }
    else {
        $selectRubro = $('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= rubro]');
    }

    if (isEdit) {

        $selectRubro.each(function (index) {

            var $bienEconomicoSelect = $(this).closest('.row')
                    .find('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= bienEconomico]');

            $bienEconomicoVal[index] = $bienEconomicoSelect.val();

            // resetSelect($bienEconomicoSelect);
        });
    }

    $selectRubro.change(function () {

        var data = {
            id_rubro: $(this).val()
        };

        var $bienEconomicoSelect = $(this).closest('.row')
                .find('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= bienEconomico]');

        resetSelect($bienEconomicoSelect);

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'bieneconomico/lista_bienes',
            data: data,
            success: function (data) {

                $bienEconomicoSelect.select2('readonly', false);

                for (var i = 0, total = data.length; i < total; i++) {
                    $bienEconomicoSelect.append('<option value="' + data[i].id + '">' + data[i].denominacionBienEconomico + '</option>');
                }

                if (isEdit) {

                    var pattern = /[0-9]+/g;

                    var index = ($bienEconomicoSelect.attr('id')).match(pattern);

                    $bienEconomicoSelect.val($bienEconomicoVal[index]);

                    if (null === $bienEconomicoSelect.val()) {

                        $bienEconomicoSelect.select2("val", "");
                    }

                } //. 
                else {
                    $bienEconomicoSelect.val($bienEconomicoSelect.find('option:first').val());
                }

                $bienEconomicoSelect.select2();

            }
        });
    });


    if (row) {
        $selectBienEconomico = $(row).find('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= bienEconomico]');
    }
    else {
        $selectBienEconomico = $('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= bienEconomico]');
    }

    $selectBienEconomico.each(function (index) {

        $(this).change(function () {

            // Actualización de la Especificacion Tecnica
            updateEspecificacionTecnica($(this).val(), $(this));

        }).trigger('change');

    });
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value.replace('$', '').replace(/\./g, '').replace(/\,/g, '.');
}

/**
 * 
 * @returns {undefined}
 */
function checkErrors() {

    $('input[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= especificacionTecnica_archivo]')
            .each(function () {

                // Si la ET es obligatoria y no fue completada
                if (($(this).prop('required')) && !$(this).val()) {

                    $(this).prev('i').detach();

                    var $span = $(this).next('span[class = help-block]').detach();

                    $(this).next('.bootstrap-filestyle').after($span);
                }
            });
}

/**
 * 
 * @returns {undefined}
 */
function initAltaBienEconomicoButton() {

    $('a.agregar_bien_economico').click(function () {

        var button = $(this);

        var idRubroSeleccionado;

        $(this).colorbox({
            iframe: true,
            fastIframe: false,
            width: "90%",
            height: '200px',
            onOpen: function (e) {
                $('body').one('popup_closed', function (e, data) {

                    var nuevoBienEconomicoId = data.id;

                    cargarBienEconomico(button, idRubroSeleccionado, nuevoBienEconomicoId);

                    $.colorbox.close();
                });
            },
            onComplete: function (e) {

                var iframe = $('#cboxLoadedContent iframe');

                iframe.load(function () {
                    initIframe(iframe, 45);
                }).trigger('load');

                var $bienEconomicoForm = iframe.contents().find('form[name=adif_comprasbundle_bieneconomico]');

                $bienEconomicoForm.submit(function () {

                    if ($bienEconomicoForm.valid()) {
                        idRubroSeleccionado = iframe.contents().find('#adif_comprasbundle_bieneconomico_rubro').val();
                    }
                });
            }
        });
    });
}

/**
 * 
 * @param {type} button
 * @param {type} idRubroSeleccionado
 * @param {type} nuevoBienEconomicoId
 * @returns {undefined}
 */
function cargarBienEconomico(button, idRubroSeleccionado, nuevoBienEconomicoId) {

    if (idRubroSeleccionado) {

        var data = {
            id_rubro: idRubroSeleccionado
        };

        var $rubroSelect = button.closest('.row')
                .find('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= rubro]');

        $rubroSelect.val(idRubroSeleccionado).select2();

        var $bienEconomicoSelect = button.closest('.row')
                .find('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= bienEconomico]');

        resetSelect($bienEconomicoSelect);

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'bieneconomico/lista_bienes',
            data: data,
            success: function (data) {

                $bienEconomicoSelect.select2('readonly', false);

                for (var i = 0, total = data.length; i < total; i++) {
                    $bienEconomicoSelect.append('<option value="' + data[i].id + '">' + data[i].denominacionBienEconomico + '</option>');
                }

                $bienEconomicoSelect.val(nuevoBienEconomicoId);
                $bienEconomicoSelect.select2();
            }
        });

        // Actualización de la Especificacion Tecnica
        updateEspecificacionTecnica(nuevoBienEconomicoId, button);
    }
}

/**
 * Actualiza el "required" de la Especificacion Tecnica
 * 
 * @param {type} idBienEconomico
 * @param {type} element
 * @returns {undefined}
 */
function updateEspecificacionTecnica(idBienEconomico, element) {

    var data = {
        id_bien_economico: idBienEconomico
    };

    var $especificacionTecnicaLabel = element.closest('fieldset')
            .find('label[for ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][for $= especificacionTecnica_archivo]')
            .first();

    var $especificacionTecnicaInput = element.closest('fieldset')
            .find('input[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra][id $= especificacionTecnica_archivo]');

    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'bieneconomico/requiere_especificacion_tecnica',
        data: data,
        success: function (data) {
            if (true === data) {
                $especificacionTecnicaLabel.addClass('required');
                $especificacionTecnicaInput.prop('required', true);
            }
            else {
                $especificacionTecnicaLabel.removeClass('required');
                $especificacionTecnicaInput.prop('required', false);
            }

            initEspecificacionTecnicaInput($especificacionTecnicaInput);
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initSolicitudFromPedidoInternoHandler() {

    if (__incluyePedidos__ == 1) {

        $('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra_][id $= _rubro]')
                .select2('readonly', true);

        $('select[id ^= adif_comprasbundle_solicitudcompra_renglonesSolicitudCompra_][id $= _bienEconomico]')
                .select2('readonly', true);
    }
}
