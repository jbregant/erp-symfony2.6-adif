$(document).ready(function() {
    
    if ($(_fm_formula).val() !== ''){
        //EDICION
        $(_fm_formula).val($(_fm_formula).val().replace(' ','').replace(/[\*\+\-\÷]/g,' $& '));
        _fm_actualizar_siguientes(_fm_operando);
    } else {
        _fm_actualizar_siguientes(null, true);
    }
    
    $('form[name="adif_recursoshumanosbundle_concepto"]').validate({
        rules: {
            'adif_recursoshumanosbundle_concepto[codigo]': {
                required: true
            },
            'adif_recursoshumanosbundle_concepto[descripcion]': {
                required: true
            },
            'adif_recursoshumanosbundle_concepto[formula]': {
                required: true,
                formula_balanceada: true
            },
        },
        ignore: ".ignore",
    });
    
    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'conceptos/lista_conceptos',
        success: function(data) {
            for (var i = 0, total = data.length; i < total; i++) {
                _fm_concepto_select.append('<option value="#concepto_'+data[i].codigo+'#">'+(data[i].codigo + ' - ' +data[i].descripcion)+'</option>');
            }
        }
    });
    
    // OPERADORES
    $('.' + _fm_operador_class).on('click', function(e) {
        var tipo = $(this).data('tipo');
        if (_fm_siguientes_permitido[tipo]) {
            _fm_formula.val(_fm_formula.val() + ' ' + $(this).text())
            _fm_actualizar_siguientes(tipo);
        }
    })

    // OPERANDOS
    $('.' + _fm_operando_class).on('click', function(e) {
        var tipo = $(this).data('tipo');
        if (_fm_siguientes_permitido[tipo]) {
            _fm_formula.val(_fm_formula.val() + ' ' + $(this).text())
            _fm_actualizar_siguientes(tipo);
        }
    })

    // BOTON LIMPIAR
    _fm_limpiar_button.on('click', function(e) {
        _fm_formula.val('');
        _fm_actualizar_siguientes(null, true);
    })

    // BOTON AGREGAR NUMERO
    _fm_numero_agregar_button.on('click', function(e) {
        var tipo = $(this).data('tipo');
        if (_fm_siguientes_permitido[tipo] && !isNaN(parseFloat(_fm_numero_input.val()))) {
			var numero = _fm_numero_input.val().replace(',', '.');
            var negPos = isNaN(_fm_numero_input.val()[0]); // Símbolo positivo/negativo
            _fm_formula.val(_fm_formula.val() + (negPos ? ' (' : '') + numero + (negPos ? ') ' : '') )
            _fm_numero_input.val('');
            _fm_actualizar_siguientes(tipo);
        }
    })
    
    // BOTON AGREGAR CONCEPTO
    _fm_concepto_agregar_button.on('click', function(e) {
        var tipo = $(this).data('tipo');
        if (_fm_siguientes_permitido[tipo] && _fm_concepto_select.val() !== null) {
            _fm_formula.val(_fm_formula.val() + _fm_concepto_select.val());
            _fm_concepto_select.val('');
            _fm_concepto_select.select2();
            _fm_actualizar_siguientes(tipo);
        }
    })

    $('.ctn_operando').each(function(){
        var h = Math.max($(this).find('btn:first').height(),$(this).find('btn:last').height());
        $(this).find('btn').height(h);
    })
    
    // PERMITIR 4 DÍGITOS EN LOS DECIMALES DEL NUMERO
    _fm_numero_input.inputmask("decimal", {radixPoint: ",", digits: 4});
});

function _fm_actualizar_siguientes(tipo, fm_reset) {

    if (fm_reset !== undefined) {
        _fm_siguientes_permitido = {
            _fm_operando: true,
            _fm_suma: false,
            _fm_resta: false,
            _fm_division: false,
            _fm_multiplicacion: false,
            _fm_parent_abrir: true,
            _fm_parent_cerrar: false,
            _fm_numero: true,
            _fm_concepto: true,
        }

        _fm_parent_abrir_count = 0;
        _fm_parent_cerrar_count = 0;

        _.each(_fm_siguientes_permitido, function(value, key){ $('[data-tipo='+key+']').attr('disabled', !value); });
        
        return;
    }

    if (tipo === _fm_parent_abrir) {
        _fm_parent_abrir_count++;
    } else if (tipo === _fm_parent_cerrar) {
        _fm_parent_cerrar_count++;
    }

    _fm_siguientes_permitido = {
        _fm_operando: true,
        _fm_suma: true,
        _fm_resta: true,
        _fm_division: true,
        _fm_multiplicacion: true,
        _fm_parent_abrir: true,
        _fm_parent_cerrar: (_fm_parent_abrir_count > _fm_parent_cerrar_count),
        _fm_numero: true,
        _fm_concepto: true
    }

    switch (tipo) {
        case _fm_suma:
        case _fm_resta:
        case _fm_multiplicacion:
        case _fm_division:
        case _fm_parent_abrir:
            _fm_siguientes_permitido[_fm_parent_cerrar] = false;
            _fm_siguientes_permitido[_fm_suma] = false;
            _fm_siguientes_permitido[_fm_resta] = false;
            _fm_siguientes_permitido[_fm_multiplicacion] = false;
            _fm_siguientes_permitido[_fm_division] = false;
            break;
        case _fm_concepto:
        case _fm_numero:
        case _fm_operando:
        case _fm_parent_cerrar:
            _fm_siguientes_permitido[_fm_concepto] = false;
            _fm_siguientes_permitido[_fm_operando] = false;
            _fm_siguientes_permitido[_fm_parent_abrir] = false;
            _fm_siguientes_permitido[_fm_numero] = false;
            break;
    }
    
    _.each(_fm_siguientes_permitido, function(value, key){ $('[data-tipo='+key+']').attr('disabled', !value); });
    
}

var _fm_formula = $('#adif_recursoshumanosbundle_concepto_formula');

var _fm_siguientes_permitido = {}

var _fm_limpiar_button = $('#_fm_button_limpiar');
var _fm_numero_agregar_button = $('#_fm_button_agregar_numero');
var _fm_numero_input = $('#_fm_numero_input');

var _fm_concepto_agregar_button = $('#_fm_button_agregar_concepto');
var _fm_concepto_select = $('#_fm_concepto_select');
var _fm_concepto_input = $('#_fm_concepto_input');

var _fm_operador_class = _fm_operador = '_fm_operador';
var _fm_operando_class = _fm_operando = '_fm_operando';

var _fm_suma = '_fm_suma';
var _fm_resta = '_fm_resta';
var _fm_division = '_fm_division';
var _fm_multiplicacion = '_fm_multiplicacion';

var _fm_parent_abrir = '_fm_parent_abrir';
var _fm_parent_cerrar = '_fm_parent_cerrar';

var _fm_numero = '_fm_numero';

var _fm_concepto = '_fm_concepto';

var _fm_parent_abrir_count = 0;
var _fm_parent_cerrar_count = 0;
