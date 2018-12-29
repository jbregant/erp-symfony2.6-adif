var corrientes_suma = 0;
var corrientes_resta = 0;

/**
 * 
 * @returns {undefined}
 */
function initCorrientesInput() {
    $("#tabla-corrientes-suma .inicial input").each(function() {
        $(this).keyup(function() {
            calcularPresupuestoCorrientesSuma();
            actualizarTotalCorrienteSuma(this, 0);
        });
    });
    $("#tabla-corrientes-resta .inicial input").each(function() {
        $(this).keyup(function() {
            calcularPresupuestoCorrientesResta();
            actualizarTotalCorrienteResta(this, 0);
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function actualizarTotalCorrienteSuma(elemento, index) {
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
    $('#tabla-corrientes-suma .input-no-imputable-' + idPadre + '').val(tot);
    $('#' + idPadre + '').html('$ ' + (tot.toFixed(2)).replace('.', ','));

    padre = $('#' + idPadre + '').prop('class').split('total-padre-');
    if (padre.length > 1) {
        actualizarTotalCorrienteSuma($('#' + idPadre + ''), padre[padre.length - 1]);
    }
}

/**
 * 
 * @returns {undefined}
 */
function actualizarTotalCorrienteResta(elemento, index) {
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
    $('#tabla-corrientes-resta .input-no-imputable-' + idPadre + '').val(tot);
    $('#' + idPadre + '').html('$ ' + (tot.toFixed(2)).replace('.', ','));

    padre = $('#' + idPadre + '').prop('class').split('total-padre-');
    if (padre.length > 1) {
        actualizarTotalCorrienteResta($('#' + idPadre + ''), padre[padre.length - 1]);
    }
}

/**
 * 
 * @returns {undefined}
 */
function calcularPresupuestoCorrientesSuma() {

    var total = 0;

    $("#tabla-corrientes-suma .inicial input").each(function() {
        total += ($(this).val() == '') ? 0 : parseFloat($(this).val().replace(',', '.'));
    });

    $('.td-tot-ingresos-corrientes').html('$ ' + (total.toFixed(2)).replace('.', ','));

    corrientes_suma = total;
    updateTotalCorrientes();
    updateTotalCapital();
    calcularPresupuestoFinanciamientoSuma();
    calcularPresupuestoFinanciamientoResta();
}

/**
 * 
 * @returns {undefined}
 */
function calcularPresupuestoCorrientesResta() {

    var total = 0;

    $("#tabla-corrientes-resta .inicial input").each(function() {
        total += ($(this).val() == '') ? 0 : parseFloat($(this).val().replace(',', '.'));
    });

    $('.td-tot-gastos-corrientes').html('$ ' + (total.toFixed(2)).replace('.', ','));

    corrientes_resta = total;
    updateTotalCorrientes();
    updateTotalCapital();
    calcularPresupuestoFinanciamientoSuma();
    calcularPresupuestoFinanciamientoResta();
}

/**
 * 
 * @returns {undefined}
 */
function updateTotalCorrientes() {

    total_corrientes = corrientes_suma - corrientes_resta;

    $('.td-tot-corrientes').html(('$ ' + total_corrientes.toFixed('2')).replace('.', ','));

}