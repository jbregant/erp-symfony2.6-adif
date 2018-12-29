
var isEdit = $('[name=_method]').length > 0;

var $selectLinea =
        $('#adif_inventariobundle_estacion_linea');

var $selectRamal =
        $('#adif_inventariobundle_estacion_ramal');

/**
 *
 */
jQuery(document).ready(function () {

    initChainedSelects();

});

/**
 *
 * @returns {undefined}
 */
function initChainedSelects() {

    $selectRamal.select2('readonly', true);

    if (isEdit) {
        var $ramalVal = $selectRamal.val();
    }

    $selectLinea.change(function () {

        if ($(this).val()) {

            var data = {
                id_linea: $(this).val()
            };

            resetSelect($selectRamal);

            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'ramal/lista_ramales',
                data: data,
                success: function (data) {

                    // Si se encontraron al menos un ramal
                    if (data.length > 0) {
                        $selectRamal.select2('readonly', false);

                        for (var i = 0, total = data.length; i < total; i++) {
                            //$selectRamal.append('<option value="' + data[i].id + '">' + data[i].denominacionCorta + ' - ' + data[i].denominacion + '</option>');
                            $selectRamal.append('<option value="' + data[i].id + '">' + data[i].denominacionCorta + '</option>');
                        }

                        if (isEdit) {
                            $selectRamal.val($ramalVal);

                            if (null === $selectRamal.val()) {
                                $selectRamal.select2("val", "");
                            }
                        }
                        else {
                            $selectRamal.val($selectRamal.find('option:first').val());
                        }

                        $selectRamal.prop('required', true);
                        $selectRamal.select2();
                    }
                    else {
                        $selectRamal.prop('required', false);
                        $selectRamal.keyup();
                    }
                }
            });
        }
    }).trigger('change');
}
