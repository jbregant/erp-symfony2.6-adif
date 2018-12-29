$(document).ready(function () {
    $('form[name=adif_contablebundle_anticipoproveedor]').validate();
    initAutocompleteProveedor();
    initSubmitButton();
    initOrdenCompraTramoHandler();
});

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteProveedor() {
    $('#adif_contablebundle_anticipoproveedor_proveedor').autocomplete({
        source: __AJAX_PATH__ + 'proveedor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            selectProveedor(event, ui, null, null);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };
}

function selectProveedor(event, ui, tipo, idOrdenCompra) {
    $('#adif_contablebundle_anticipoproveedor_proveedor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_anticipoproveedor_proveedor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_anticipoproveedor_idProveedor').val(ui.item.id);

    $('#table_ordenes_compra_tramos_proveedor').hide();
    $.ajax({
        url: __AJAX_PATH__ + 'anticiposproveedor/index_table_oc_tramo/',
        data: {id_proveedor: ui.item.id}
    }).done(function (result) {

        var ocs = JSON.parse(result).data;

        $('#table_ordenes_compra_tramos_proveedor tbody').empty();
        $('#table_ordenes_compra_tramos_proveedor').show();

        $(ocs).each(function () {

            var $tr = $('<tr />', {id_oc_tramo: this[0], tipo_anticipo: this[5], style: 'cursor: pointer;'});

            $('<td />', {text: this[1]}).addClass('nowrap').appendTo($tr);
            $('<td />', {text: this[2]}).addClass('nowrap').appendTo($tr);
            $('<td />', {text: this[3] === "" ? '-' : this[3]}).appendTo($tr);
            $('<td />', {text: this[4]}).addClass('nowrap').appendTo($tr);

            $('#table_ordenes_compra_tramos_proveedor tbody').append($tr);
        });

        if (idOrdenCompra) {

            $('#table_ordenes_compra_tramos_proveedor tbody tr[id_oc_tramo=' + idOrdenCompra + '][tipo_anticipo=' + tipo + ']')
                    .trigger('click');
        }

    });
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_anticipoproveedor_submit').on('click', function (e) {

        var tr_seleccionado = $('#table_ordenes_compra_tramos_proveedor tbody tr[class="active"]');

//      if (tr_seleccionado.length > 0) {

        if ($('form[name=adif_contablebundle_anticipoproveedor]').valid()) {
            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el anticipo?',
                callbackOK: function () {


                    var json = {
                        'id_proveedor': $('#adif_contablebundle_anticipoproveedor_idProveedor').val(),
                        'id_oc_tramo': tr_seleccionado.attr('id_oc_tramo'),
                        'tipo_anticipo': tr_seleccionado.attr('tipo_anticipo')
                    };

                    $('form[name=adif_contablebundle_anticipoproveedor]').addHiddenInputData(json);
                    $('form[name=adif_contablebundle_anticipoproveedor]').submit();
                }
            });

            e.stopPropagation();

            return false;
        }
//      } else {
//          show_alert({msg: 'Debe seleccionar al menos una Órden de compra o tramo'});
//      }

        return false;
    });
}

/**
 * 
 * @returns {undefined}
 */
function initOrdenCompraTramoHandler() {
    $(document).on('click', '#table_ordenes_compra_tramos_proveedor tbody tr', function (e) {
        e.preventDefault();
        bloquear();
        $(this).parents('tbody').find('tr').removeClass('active');
        $(this).addClass('active');
        $.uniform.update($(this).find('input'));
        desbloquear();
    });
}