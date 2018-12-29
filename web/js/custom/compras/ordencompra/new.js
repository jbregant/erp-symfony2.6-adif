
var isEdit = $('[name=_method]').length > 0;

var $selectProvinciaDomicilioEntrega = $('#adif_comprasbundle_ordencompra_domicilioEntrega_idProvincia');

var $selectLocalidadDomicilioEntrega = $('#adif_comprasbundle_ordencompra_domicilioEntrega_localidad');

var proveedorProvinciaDomicilioEntrega = $('#ordencompra_domicilio_entrega > [name=ordencompra_provincia]');

var proveedorLocalidadDomicilioEntrega = $('#ordencompra_domicilio_entrega > [name=ordencompra_localidad]');

var editLocalidadDomicilioEntrega = false;

/**
 * 
 */
jQuery(document).ready(function () {

    $('form[name=adif_comprasbundle_ordencompra]').validate();

    configSubmitButtons();

    initReadOnlySelect();

    initDomicilioSelect();
});


/**
 * 
 * @returns {undefined}
 */
function configSubmitButtons() {

    // Handler para el boton "Guardar borrador"
    $('#adif_comprasbundle_ordencompra_save').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_ordencompra]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar la orden de compra como borrador?',
                callbackOK: function () {

                    var json = {accion: 'save'};

                    $('form[name=adif_comprasbundle_ordencompra]').addHiddenInputData(json);
                    $('form[name=adif_comprasbundle_ordencompra]').submit();

                }
            });

            e.stopPropagation();

            return false;
        }

        $('.has-error').first().find('input').focus();

        return false;
    });

    // Handler para el boton "Generar orden de compra"
    $('#adif_comprasbundle_ordencompra_generate').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_ordencompra]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea generar la orden de compra?',
                callbackOK: function () {

                    var json = {accion: 'generate'};

                    $('form[name=adif_comprasbundle_ordencompra]').addHiddenInputData(json);
                    $('form[name=adif_comprasbundle_ordencompra]').submit();
                }
            });

            e.stopPropagation();

            return false;
        }

        $('.has-error').first().find('input').focus();

        return false;
    });
}

/**
 * 
 * @returns {undefined}
 */
function initDomicilioSelect() {

    // INIT Domicilio Entrega
    if (isEdit && !editLocalidadDomicilioEntrega) {
        $selectProvinciaDomicilioEntrega.val(proveedorProvinciaDomicilioEntrega.val());
    }

    $selectProvinciaDomicilioEntrega.change(function () {

        var data = {
            id_provincia: $(this).val()
        };

        resetSelect($selectLocalidadDomicilioEntrega);

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'localidades/lista_localidades',
            data: data,
            success: function (data) {

                $selectLocalidadDomicilioEntrega.select2('readonly', false);

                for (var i = 0, total = data.length; i < total; i++) {
                    $selectLocalidadDomicilioEntrega
                            .append('<option value="' + data[i].id + '">' + data[i].nombre + '</option>');
                }

                if (isEdit && !editLocalidadDomicilioEntrega) {
                    editLocalidadDomicilioEntrega = true;
                    $selectLocalidadDomicilioEntrega.val(proveedorLocalidadDomicilioEntrega.val());
                }
                else {
                    $selectLocalidadDomicilioEntrega.val($selectLocalidadDomicilioEntrega.find('option:first').val());
                }

                $selectLocalidadDomicilioEntrega.select2();
            }
        });
    }).trigger('change');
}

/**
 * 
 * @returns {undefined}
 */
function initReadOnlySelect() {

    $('#adif_comprasbundle_ordencompra_proveedor').select2('readonly', true);

    $('#adif_comprasbundle_ordencompra_tipoContratacion').select2('readonly', true);
}

