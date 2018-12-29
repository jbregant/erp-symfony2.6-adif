
var isEdit = $('[name=_method]').length > 0;

var $collectionHolder;


/**
 * 
 */
jQuery(document).ready(function () {

    // Validacion del Formulario
    $('form[name=adif_comprasbundle_pedidointerno]').validate();

    configSubmitButtons();

    initSelects();

    setMasks();

    updateRenglonPedidoInternoDeleteLink();

    $collectionHolder = $('div.prototype-renglon-pedido');

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $('.prototype-link-add-renglon-pedido').on('click', function (e) {
        e.preventDefault();

        addRenglonPedidoInternoForm($collectionHolder);

        initSelects();
    });

    initChainedSelects();

    initAltaBienEconomicoButton();

    if (!isEdit) {
        $('.prototype-link-add-renglon-pedido').click();
    }

});


/**
 * 
 * @returns {undefined}
 */
function configSubmitButtons() {

    // Handler para el boton "Guardar borrador"
    $('#adif_comprasbundle_pedidointerno_save').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_pedidointerno]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el pedido como borrador?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'save'};

                        $('form[name=adif_comprasbundle_pedidointerno]').addHiddenInputData(json);
                        $('form[name=adif_comprasbundle_pedidointerno]').submit();
                    }
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;

    });

    // Handler para el boton "Enviar pedido"
    $('#adif_comprasbundle_pedidointerno_send').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_pedidointerno]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea enviar el pedido?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'send'};

                        $('form[name=adif_comprasbundle_pedidointerno]').addHiddenInputData(json);
                        $('form[name=adif_comprasbundle_pedidointerno]').submit();
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

    // Si el Pedido tiene renglones cargados
    if ($('.renglon-pedido-content').length > 0) {

        $('.changeable').each(function () {

            $(this).val(clearCurrencyValue($(this).val()));
        });
    }
    else {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe cargar al menos un renglón al pedido."
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

    $('.currency-format').each(function () {
        $(this).autoNumeric('init', {aSep: '.', aDec: ','});
    });
}



/**
 * 
 * @returns {undefined}
 */
function updateRenglonPedidoInternoDeleteLink() {

    $(".prototype-link-remove-renglon-pedido").each(function () {

        $(this).tooltip();

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var renglonPedido = $(this).closest('.renglon-pedido-content');

            show_confirm({
                msg: '¿Desea eliminar el renglón?',
                callbackOK: function () {
                    renglonPedido.hide('slow', function () {
                        renglonPedido.remove();
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
 * @returns {addRenglonPedidoInternoForm}
 */
function addRenglonPedidoInternoForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var renglonPedidoInternoForm = prototype.replace(/__renglon_pedido__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-renglon-pedido').closest('.row').before(renglonPedidoInternoForm);

    updateRenglonPedidoInternoDeleteLink();

    initSelects();

    setMasks();

    initAltaBienEconomicoButton();

    var $row = $('.prototype-link-add-renglon-pedido').parents('.row').prev('fieldset');

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
        $selectRubro = $(row).find('select[id ^= adif_comprasbundle_pedidointerno_renglonesPedidoInterno][id $= rubro]');
    }
    else {
        $selectRubro = $('select[id ^= adif_comprasbundle_pedidointerno_renglonesPedidoInterno][id $= rubro]');
    }

    if (isEdit) {

        $selectRubro.each(function (index) {

            var $bienEconomicoSelect = $(this).closest('.row')
                    .find('select[id ^= adif_comprasbundle_pedidointerno_renglonesPedidoInterno][id $= bienEconomico]');

            $bienEconomicoVal[index] = $bienEconomicoSelect.val();

            resetSelect($bienEconomicoSelect);

            $bienEconomicoSelect.val($bienEconomicoVal);
        });
    }

    $selectRubro.change(function () {

        var data = {
            id_rubro: $(this).val()
        };

        var $bienEconomicoSelect = $(this).closest('.row')
                .find('select[id ^= adif_comprasbundle_pedidointerno_renglonesPedidoInterno][id $= bienEconomico]');

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
    }).trigger('change');

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
                .find('select[id ^= adif_comprasbundle_pedidointerno_renglonesPedidoInterno][id $= rubro]');

        $rubroSelect.val(idRubroSeleccionado).select2();

        var $bienEconomicoSelect = button.closest('.row')
                .find('select[id ^= adif_comprasbundle_pedidointerno_renglonesPedidoInterno][id $= bienEconomico]');

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
    }
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value.replace('$', '').replace(/\./g, '').replace(/\,/g, '.');
}