var financiamiento_suma = 0;
var financiamiento_resta = 0;

/**
 * 
 * @returns {undefined}
 */
function initFinanciamientoInput() {
    $("#tabla-financiamiento-suma .inicial input").each(function() {
        $(this).keyup(function() {
            calcularPresupuestoFinanciamientoSuma();
            actualizarTotalFinanciamientoSuma(this, 0);
        });
    });
    $("#tabla-financiamiento-resta .inicial input").each(function() {
        $(this).keyup(function() {
            calcularPresupuestoFinanciamientoResta();
            actualizarTotalFinanciamientoResta(this, 0);
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function calcularPresupuestoFinanciamientoSuma() {

    var total = 0;

    $("#tabla-financiamiento-suma .inicial input").each(function() {
        total += ($(this).val() == '') ? 0 : parseFloat($(this).val().replace(',', '.'));
    });

    total += superavit;

    $('.td-tot-ingresos-financiamiento').html('$ ' + (total.toFixed(2)).replace('.', ','));

    financiamiento_suma = total;
    updateTotalFinanciamiento();
}

/**
 * 
 * @returns {undefined}
 */
function calcularPresupuestoFinanciamientoResta() {

    var total = 0;

    $("#tabla-financiamiento-resta .inicial input").each(function() {
        total += ($(this).val() == '') ? 0 : parseFloat($(this).val().replace(',', '.'));
    });

    total += deficit;

    $('.td-tot-gastos-financiamiento').html('$ ' + (total.toFixed(2)).replace('.', ','));

    financiamiento_resta = total;
    updateTotalFinanciamiento();
}

/**
 * 
 * @returns {undefined}
 */
function actualizarTotalFinanciamientoSuma(elemento, index) {
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
    $('#tabla-financiamiento-suma .input-no-imputable-' + idPadre + '').val(tot);
    $('#' + idPadre + '').html('$ ' + (tot.toFixed(2)).replace('.', ','));

    padre = $('#' + idPadre + '').prop('class').split('total-padre-');
    if (padre.length > 1) {
        actualizarTotalFinanciamientoSuma($('#' + idPadre + ''), padre[padre.length - 1]);
    }
}

/**
 * 
 * @returns {undefined}
 */
function actualizarTotalFinanciamientoResta(elemento, index) {
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
    $('#tabla-financiamiento-resta .input-no-imputable-' + idPadre + '').val(tot);
    $('#' + idPadre + '').html('$ ' + (tot.toFixed(2)).replace('.', ','));

    padre = $('#' + idPadre + '').prop('class').split('total-padre-');
    if (padre.length > 1) {
        actualizarTotalFinanciamientoResta($('#' + idPadre + ''), padre[padre.length - 1]);
    }
}

/**
 * 
 * @returns {undefined}
 */
function updateTotalFinanciamiento() {

    total_financiamiento = financiamiento_suma - financiamiento_resta;

    $('.td-tot-financiamiento').html('$ ' + total_financiamiento.toFixed('2').replace('.', ','));

}