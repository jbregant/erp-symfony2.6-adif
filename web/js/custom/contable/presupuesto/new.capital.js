var capital_suma = 0;
var capital_resta = 0;

/**
 * 
 * @returns {undefined}
 */
function initCapitalInput() {
    $("#tabla-capital-suma .inicial input").each(function() {
        $(this).keyup(function() {
            calcularPresupuestoCapitalSuma();
            actualizarTotalCapitalSuma(this, 0);
        });
    });
    $("#tabla-capital-resta .inicial input").each(function() {
        $(this).keyup(function() {
            calcularPresupuestoCapitalResta();
            actualizarTotalCapitalResta(this, 0);
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function calcularPresupuestoCapitalSuma() {

    var total = 0;

    $("#tabla-capital-suma .inicial input").each(function() {
        total += ($(this).val() == '') ? 0 : parseFloat($(this).val().replace(',', '.'));
    });

    $('.td-tot-ingresos-capital').html('$ ' + (total.toFixed(2)).replace('.', ','));

    capital_suma = total;
    updateTotalCapital();
    calcularPresupuestoFinanciamientoSuma();
    calcularPresupuestoFinanciamientoResta();
}

/**
 * 
 * @returns {undefined}
 */
function calcularPresupuestoCapitalResta() {

    var total = 0;

    $("#tabla-capital-resta .inicial input").each(function() {
        total += ($(this).val() == '') ? 0 : parseFloat($(this).val().replace(',', '.'));
    });

    $('.td-tot-gastos-capital').html('$ ' + (total.toFixed(2)).replace('.', ','));

    capital_resta = total;
    updateTotalCapital();
    calcularPresupuestoFinanciamientoSuma();
    calcularPresupuestoFinanciamientoResta();
}

/**
 * 
 * @returns {undefined}
 */
function actualizarTotalCapitalSuma(elemento, index) {
    tot = 0;
    if (index == 0) {
        classArray = $(elemento).parent().prop('class').split('total-padre-');
        idPadre = classArray[classArray.length - 1];
    } else {
        idPadre = index;
    }
    $('.total-padre-' + idPadre).each(function(e, v) {
        if ($(v).hasClass('monto-total-cuenta')) {
            tot += parseFloat($(v).html().replace('$ ', '').replace(',', '.'));
        } else {
            tot += ($(v).find('input').val() == '') ? 0 : parseFloat($(v).find('input').val().replace(',', '.'));
        }
    });
    $('#tabla-capital-suma .input-no-imputable-' + idPadre + '').val(tot);
    $('#' + idPadre + '').html('$ ' + (tot.toFixed(2)).replace('.', ','));

    padre = $('#' + idPadre + '').prop('class').split('total-padre-');
    if (padre.length > 1) {
        actualizarTotalCapitalSuma($('#' + idPadre + ''), padre[padre.length - 1]);
    }
}

/**
 * 
 * @returns {undefined}
 */
function actualizarTotalCapitalResta(elemento, index) {
    tot = 0;
    if (index == 0) {
        classArray = $(elemento).parent().prop('class').split('total-padre-');
        idPadre = classArray[classArray.length - 1];
    } else {
        idPadre = index;
    }
    $('.total-padre-' + idPadre).each(function(e, v) {
        if ($(v).hasClass('monto-total-cuenta')) {
            tot += parseFloat($(v).html().replace('$ ', '').replace(',', '.'));
        } else {
            tot += ($(v).find('input').val() == '') ? 0 : parseFloat($(v).find('input').val().replace(',', '.'));
        }
    });
    $('#tabla-capital-resta .input-no-imputable-' + idPadre + '').val(tot);
    $('#' + idPadre + '').html('$ ' + (tot.toFixed(2)).replace('.', ','));

    padre = $('#' + idPadre + '').prop('class').split('total-padre-');
    if (padre.length > 1) {
        actualizarTotalCapitalResta($('#' + idPadre + ''), padre[padre.length - 1]);
    }
}


/**
 * 
 * @returns {undefined}
 */
function updateTotalCapital() {

    total_capital = capital_suma + total_corrientes - capital_resta;

    $('.td-tot-capital').html('$ ' + total_capital.toFixed('2').replace('.', ','));

    if ((capital_suma + total_corrientes) > capital_resta) {
        superavit = total_capital;
        deficit = 0;
    }
    else {
        superavit = 0;
        deficit = capital_resta - ( capital_suma  + total_corrientes );
    }

    $('.monto-superavit').html('$ ' + superavit.toFixed(2).replace('.', ','));
    $('.monto-deficit').html('$ ' + deficit.toFixed(2).replace('.', ','));

}