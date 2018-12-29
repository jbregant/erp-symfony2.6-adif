
/**
 * 
 * @returns {undefined}
 */
function initFormularioPagar() {

    $(document).on('change', 'select[name$="\[forma_pago\]"],select[name$="\[cuenta_bancaria\]"]', function () {
        $(this).parents('.row_renglon_pago').find('.chequeraDiv').addClass('hidden');
        $(this).parents('.row_renglon_pago').find('.chequeDiv').addClass('hidden');
        $(this).parents('.row_renglon_pago').find('.transferenciaDiv').addClass('hidden');
//        $(this).parents('.row_renglon_pago').find('.montoDiv').addClass('hidden');
        $(this).parents('.row_renglon_pago').find('select[id$=chequera]').html('');
        $(this).parents('.row_renglon_pago').find('input[id$=monto]').val(0);

        actualizarChequera($(this).parents('.row_renglon_pago').find('select[id$=forma_pago]').val(), $(this).parents('.row_renglon_pago').find('select[id$=cuenta_bancaria]').val(), $(this));
    });

    $(document).on('change, keyup', '.row_renglon_pago input[id$=monto]', function () {
        actualizarTotales();
    });
}

/**
 * 
 * @param {type} formaPago
 * @param {type} cuenta
 * @param {type} elemento
 * @returns {undefined}
 */
function actualizarChequera(formaPago, cuenta, elemento) {

    if ((formaPago != 0) && (cuenta != 0)) {
        $('#form_pagar').validate();

        if ((formaPago == 1)) {

            var chequeraOptions = '<option value="" selected="selected">-- Elija una chequera --</option>';
            for (var index in chequeraCuenta[cuenta]) {
                chequeraOptions += "<option value=" + chequeraCuenta[cuenta][index] + ">" + chequeras[chequeraCuenta[cuenta][index]]["chequera"] + "</option>";
            }
            $(elemento).parents('.row_renglon_pago').find('select[id$=chequera]').html(chequeraOptions);
            $(elemento).parents('.row_renglon_pago').find('select[id$=chequera]').select2("val", "");

            $(elemento).parents('.row_renglon_pago').find('.chequeraDiv').removeClass('hidden');

            $(elemento).parents('.row_renglon_pago').find('select[id$=chequera]').rules('add', {
                required: true
            });
            $(elemento).parents('.row_renglon_pago').find('input[id$=cheque]').rules('add', {
                required: true
            });
            $(elemento).parents('.row_renglon_pago').find('input[id$=transferencia]').rules("remove");

            initActualizarCheque(elemento);
        }
        else {
            $(elemento).parents('.row_renglon_pago').find('.transferenciaDiv').removeClass('hidden');

            $(elemento).parents('.row_renglon_pago').find('input[id$=transferencia]').rules('add', {
                required: true
            });
            console.log(elemento);
            $(elemento).parents('.row_renglon_pago').find('select[id$=chequera]').rules("remove");
            $(elemento).parents('.row_renglon_pago').find('input[id$=cheque]').rules("remove");

        }
//        $(elemento).parents('.row_renglon_pago').find('.montoDiv').removeClass('hidden');
    }
}

/**
 * 
 * @param {type} elemento
 * @returns {undefined}
 */
function initActualizarCheque(elemento) {

    $(elemento).parents('.row_renglon_pago').find('select[id$=cheque]').val('');

    $(elemento).parents('.row_renglon_pago').find('.chequeDiv').removeClass('hidden');
//    $('#chequera').on('change', function () {
//        $('.chequeDiv').addClass('hidden');
//        actualizarCheque($(this).val());
//    });
}

///**
// * 
// * @param {type} chequera
// * @returns {undefined}
// */
//function actualizarCheque(chequera) {
//
//    if (typeof chequeras[chequera] !== "undefined") {
//        $('#cheque').val(chequeras[chequera]["numeroSiguiente"]);
//        $('.chequeDiv').removeClass('hidden');
//    }
//}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {
    return $value
            .replace('$', '')
            .replace('%', '')
            .replace(/\./g, '')
            .replace(/\,/g, '.')
            .trim();
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '-999999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
    });

    $('.money-format').each(function () {
        $(this).autoNumeric('update', {vMin: '-999999999999.99', aSign: '$ ', aSep: '.', aDec: ','});
    });
}

/**
 * 
 * @returns {undefined}
 */
function actualizarTotales() {
    totalAcumulado = 0;

    $('.row_renglon_pago input[id$=monto]').each(function (i, e) {
        totalAcumulado += ($(e).val() !== '') ? parseFloat(clearCurrencyValue($(e).val())) : 0;
    });

//    $('.total_pago').html(totalAcumulado.toFixed(2).replace('.', ','));
    $('.restante_pago').html((totalNeto - totalAcumulado).toFixed(2).replace('.', ','));

    setMasks();
}

/**
 * 
 * @returns {undefined}
 */
function initAgregarPagoHandler() {
    $(document).on('click', '#agregar_renglon_pago', function (e) {
        e.preventDefault();

        var nuevo_row =
                $('.row_renglon_pago_nuevo')
                .clone()
                .show()
                .removeClass('row_renglon_pago_nuevo');

        nuevo_row.addClass('row_renglon_pago');

        nuevo_row.find('.ignore').removeClass('ignore');

        nuevo_row.find('[sname]').each(function () {
            $(this).attr('name', $(this).attr('sname'));
            $(this).removeAttr('sname');
        });

        var maximo_indice = 0;

        $('.row_renglon_pago').each(function () {
            var value = parseFloat($(this).attr('indice'));
            maximo_indice = (value > maximo_indice) ? value : maximo_indice;
        });

        var indice_nuevo = maximo_indice + 1;

        nuevo_row.html(nuevo_row.html().replace(/__name__/g, indice_nuevo));
        nuevo_row.attr('indice', indice_nuevo);
        nuevo_row.appendTo('.ctn_rows_renglon_pago');

        nuevo_row.find('input[id$=monto]').val(0);
        nuevo_row.find('select').select2();

        initCurrencies();

        $('#form_pagar').validate();
        nuevo_row.find('select[id$=cuenta_bancaria]').rules('add', {
            required: true
        });
        nuevo_row.find('select[id$=forma_pago]').rules('add', {
            required: true
        });
        nuevo_row.find('input[id$=monto]').rules('add', {
            required: true
        });

        return nuevo_row;

    });
}