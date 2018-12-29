$.validator.setDefaults({
    errorElement: 'span',
    errorClass: 'help-block',
    focusInvalid: true,
    ignore: "input[name*='_currency']",
    errorPlacement: function (error, element) {
        // render error placement for each input type
        if ($(element).parents('div.input-group').length > 0) {
            // Si el input está dentro de un input-group
//            error.insertAfter(element.parent().parent());
            element.parent().parent().parent().append(error);
        } else {
            error.insertAfter(element);
        }

        var icon = $(element).parent('.input-icon').children('i');
        //icon.removeClass('fa-check').addClass("fa-warning");  
        icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
    },
    highlight: function (element) { // hightlight error inputs
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group 
        var icon = $(element).parent('.input-icon').children('i');
        icon.removeClass('fa-check').addClass("fa-warning");

    },
    unhighlight: function (element) { // revert the change done by hightlight        
        $(element).closest('.form-group').removeClass('has-error');//.addClass('has-success');
//
//        var icon = $(element).parent('.input-icon').children('i');
//        icon.removeClass('fa-warning').addClass('fa-check');

    },
    success: function (label, element) {
//        var icon = $(element).parent('.input-icon').children('i');
//        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
//        icon.removeClass("fa-warning").addClass("fa-check");
    }
});

$.validator.addMethod(
        "cuil",
        function (value) {

            if (!/^\d{2}-\d{8}-\d{1}$/.test(value)) {
                return false;
            }

            var aMult = '5432765432';
            var aMult = aMult.split('');
            var sCUIT = value.replace(/-/g, "").replace(/_/g, "").replace(/ /g, "");

            if (sCUIT && sCUIT != 0 && sCUIT.length == 11) {

                var aCUIT = sCUIT.split('');
                var iResult = 0;

                for (i = 0; i <= 9; i++) {
                    iResult += aCUIT[i] * aMult[i];
                }

                iResult = (iResult % 11);
                iResult = 11 - iResult;

                if (iResult == 11) {
                    iResult = 0;
                }

                if (iResult == 10) {
                    iResult = 9;
                }

                if (iResult == aCUIT[10]) {
                    return true;
                }
            }
            return false;
        },
        "Formato de CUIL incorrecto"
        );

$.validator.addMethod("cuil_igual_dni", function (value, element, dni) {
    return this.optional(element) || value.split('-')[1] === dni;
}, $.format("El CUIL ingresado no corresponde con el DNI: {0}"));

$.validator.addMethod(
        "cbu",
        function (value) {
            var ponderador = '97139713971397139713971397139713';
            var i;
            var nDigito;
            var nPond;
            var bloque1;
            var bloque2;
            var nTotal = 0;
            bloque1 = '0' + value.substring(0, 7);
            for (i = 0; i <= 7; i++) {
                nDigito = bloque1.charAt(i);
                nPond = ponderador.charAt(i);
                nTotal = nTotal + (nPond * nDigito) - ((Math.floor(nPond * nDigito / 10)) * 10);
            }
            i = 0;
            while (((Math.floor((nTotal + i) / 10)) * 10) != (nTotal + i)) {
                i = i + 1;
            }

            if (value.substring(7, 8) != i) {
                return false;
            }
            nTotal = 0;
            bloque2 = '000' + value.substring(8, 21);
            for (i = 0; i <= 15; i++) {
                nDigito = bloque2.charAt(i)
                nPond = ponderador.charAt(i)
                nTotal = nTotal + (nPond * nDigito) - ((Math.floor(nPond * nDigito / 10)) * 10);
            }
            i = 0;
            while (((Math.floor((nTotal + i) / 10)) * 10) != (nTotal + i)) {
                i = i + 1;
            }
            if (value.substring(21, 22) != i) {
                return false;
            } else {
                return true;
            }
        },
        "Formato de CBU incorrecto"
        );

$.validator.addMethod("formula_balanceada", function (value, element) {
    var removeComments = function (str) {
        var re_comment = /(\/[*][^*]*[*]\/)|(\/\/[^\n]*)/gm;
        return ("" + str).replace(re_comment, "");
    };

    var getOnlyBrackets = function (str) {
        var re = /[^()\[\]{}]/g;
        return ("" + str).replace(re, "");
    };

    var areBracketsInOrder = function (str) {
        str = "" + str;
        var bracket = {
            "]": "[",
            "}": "{",
            ")": "("
        },
        openBrackets = [],
                isClean = true,
                i = 0,
                len = str.length;

        for (; isClean && i < len; i++) {
            if (bracket[ str[ i ] ]) {
                isClean = (openBrackets.pop() === bracket[ str[ i ] ]);
            } else {
                openBrackets.push(str[i]);
            }
        }
        return isClean && !openBrackets.length;
    };

    var endsWithOperator = function (str) {
        return /.*(\*|\+|\-|\/)$/.test(str);
    };

    var isCorrect = function (str) {
        str = $.trim(str);

        if (endsWithOperator(str)) {
            return false;
        }

        str = removeComments(str);
        str = getOnlyBrackets(str);
        return areBracketsInOrder(str);
    };

    return this.optional(element) || isCorrect(value);
}, $.format("La f&oacute;rmula no est&aacute; balanceada."));

$.validator.addMethod(
        "fecha_custom",
        function (value, element) {
            return this.optional(element) || /^(([0-9])|([0-2][0-9])|(3[0-1]))\/(([1-9])|(0[1-9])|(1[0-2]))\/([1-2][0,9][0-9][0-9])$/i.test(value);
        },
        'La fecha no es válida.'
        );

$.validator.addMethod(
        "valor_maximo",
        function (value, element, max_value) {
            return parseFloat(value.replace(',', '.')) <= max_value;
        },
        'El valor m&aacute;ximo es {0}.'
        );


$.validator.addMethod(
        "minStrict",
        function (value, element, param) {
            return value > param;
        },
        'El valor debe ser mayor a {0}.'
        );

/**
 * Valida un rango de fechas de inicio a fin.
 * 
 * @param {type} fechaInicio
 * @param {type} fechaFin
 * @returns {Boolean}
 */
function validarRangoFechas(fechaInicio, fechaFin)
{

    if (fechaInicio == '' || fechaFin == '') {
        show_alert({msg: 'Las fechas son obligatorias para realizar el filtro.'});
        return false;
    }

    if (!isValidDate(fechaInicio)) {
        show_alert({msg: 'La fecha de inicio es inválida.'});
        return false;
    }

    if (!isValidDate(fechaFin)) {
        show_alert({msg: 'La fecha de fin es inválida.'});
        return false;
    }

    var fechaInicioPartes = fechaInicio.split('/');
    var fechaFinPartes = fechaFin.split('/');

    var dateFechaInicio = new Date(fechaInicioPartes[2], fechaInicioPartes[1] - 1, fechaInicioPartes[0]);
    var dateFechaFin = new Date(fechaFinPartes[2], fechaFinPartes[1] - 1, fechaFinPartes[0]);

    if (dateFechaInicio > dateFechaFin) {
        show_alert({msg: 'El rango de las fechas es inválido.'});
        return false;
    }

    return true;
}

/**
 * 
 * @param {type} s
 * @returns {Boolean|Date|isValidDate.d}
 */
function isValidDate(s) {

    var validformat = /^\d{2}\/\d{2}\/\d{4}$/;

    if (!validformat.test(s)) {
        return false;
    }

    var bits = s.split('/');

    var d = new Date(bits[2], bits[1] - 1, bits[0]);

    return d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[0]);
}