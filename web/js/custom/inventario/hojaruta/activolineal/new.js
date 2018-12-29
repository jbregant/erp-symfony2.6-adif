/* global id_prefix */

var index = 0;
var dt_itemhojaruta_column_index = {
    id: index++,
    multiselect: index++,

    operador: index++,
    linea: index++,
    division: index++,
    tipoActivo: index++,
    progresivaInicioTramo: index++,
    progresivaFinalTramo: index++,

    acciones: index
};

$(document).ready(function () {
    initChainedSelects();
    initSelectProgresivaInicio();
});

function addEspecial(id, id_prefix, $selectorValue, itemForm){
    var idItem = $(itemForm).find("[id ^= '" + id_prefix + "'][id $= '_" + id + "']").attr('id');
    var valor = $selectorValue.val();
    if( (id === 'progresivaInicioTramo' || id === 'progresivaFinalTramo') && valor !== ''){ //Para item de Activo Lineal
        valor = Number($selectorValue.find('option:selected').text()).toFixed(3).toString().replace('.',',');
        $("#" + idItem).val(valor); //Pongo el valor en texto para cuando hay errores
        //var idItemAL = $(itemForm).find("[id ^= '" + id_prefix + "'][id $= '_activoLineal']").attr('id');
        //$("#" + idItemAL).val($selectorValue.val()); //Setteo el activoLineal
    }
    return valor;
}

function transformValue(campo){
    if(campo === 'progresivaInicioTramo' || campo === 'progresivaFinalTramo'){ 
        return $('#' + id_prefix + campo).find('option:selected').text();
    }
    return $('#' + id_prefix + campo).val();
}

function initChainedSelects() {
    var selects = {//Defino dependencias:
        linea: ['division'],
        operador: ['division']
    };
    var values = [];

    $('.form-item').on('change', 'select.choice', function () {
        if ($(this).val()) {
            var change = $(this).attr('id').replace(id_prefix, '');
            for (var j in selects[change]) { //Itero las dependencias
                var data = [
                    {name: change, value: $(this).val()} //Agrego el valor que cambió
                ];
                var target = selects[change][j];

                for (var k in selects) { //Busco el target en otras dependencias para filtrado compuesto
                    var $selectExtra = $('#' + id_prefix + k);
                    if (k !== change && selects[k].indexOf(target) !== -1 && $selectExtra.val() !== '') {
                        data.push({name: k, value: $selectExtra.val()}); //Agrego otras dependencias si no son null
                    }
                }
                var $selectTarget = $('#' + id_prefix + target);
                var link = (target === 'division') ? 'divisiones' : ((target === 'categoria') ? 'categorizacion' : target); //Diferencias de nomenclatura
                $.ajax({
                    type: 'post',
                    url: __AJAX_PATH__ + link + '/lista',
                    data: data,
                    target: target,
                    id_prefix: id_prefix,
                    values: values,
                    success: function (data) {
                        // Si se encontraron al menos un ramal
                        resetSelect('#' + this.id_prefix + this.target);
                        if (data.length > 0) {
                            $selectTarget = $('#' + this.id_prefix + this.target);
                            $selectTarget.select2('readonly', false);

                            for (var i = 0, total = data.length; i < total; i++) {
                                var texto = ((typeof data[i].denominacionCorta !== 'undefined') ? data[i].denominacionCorta + ' - ' : '') + data[i].denominacion;
                                $selectTarget.append('<option value="' + data[i].id + '">' + texto + '</option>');
                            }

                            $selectTarget.select2();

                            //Selecciono valor preseleccionado:
                            $selectTarget.val(this.values[this.target]);
                            $selectTarget.select2("val", this.values[this.target]);
                            if (null === $selectTarget.val()) {
                                $selectTarget.select2("val", "");
                            }

                        } else {
                            $selectTarget.keyup();
                        }
                    }
                });
            }

        }
    });

    for (var i in selects) { //Itero por todos los selects
        for (var j in selects[i]) {
            //Guardo los valores originales
            values[selects[i][j]] = $('#' + id_prefix + selects[i][j]).val();
            $('#' + id_prefix + selects[i][j]).select2('readonly', true); //Bloqueo
        }
        //Filtro según valores ya seleccionados:
        if ($('#' + id_prefix + i).val() !== '') {
            $('#' + id_prefix + i).trigger('change');
        }
    }
}

/**
 * Carga las progresivas de inicio y final según los datos
 * identificadores del activo Lineal
 *
 */
function initSelectProgresivaInicio(){
    var selects = ['linea', 'operador', 'division', 'tipoActivo'];
    $('#' + id_prefix + 'progresivaInicioTramo').select2('readonly', true);
    $('#' + id_prefix + 'progresivaFinalTramo').select2('readonly', true);
    $('.form-item').on('change','select.choice',function () {
        var data = [];
        for(var i in selects){
            var $select = $('#' + id_prefix + selects[i]);
            if($select.val() !== ''){
                data.push({name:selects[i],value:$select.val()});
            }
        }
        var change = $(this).attr('id').replace(id_prefix,'');
        if( selects.length === data.length && selects.indexOf(change) !== -1){
            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'activolineal/hojaruta_progresivas',
                data: data,
                id_prefix: id_prefix,
                dataType: 'json',
                success: function (data) {
                    resetSelect('#' + this.id_prefix + 'progresivaInicioTramo');
                    resetSelect('#' + this.id_prefix + 'progresivaFinalTramo');
                    if (Object.keys(data.progresivaInicioTramo).length > 0) {
                        for(var progresiva in data){
                            $selectTarget = $('#' + this.id_prefix + progresiva);
                            $selectTarget.select2('readonly', false);

                            for (var id in data[progresiva]) {
                                $selectTarget.append('<option value="' + id + '">' + data[progresiva][id] + '</option>');
                            }

                            $selectTarget.select2();
                        }

                    }
                }
            });
        }
    }).change();

    //Selecciono la progresivaFinal en base a la inicial
    $('#' + id_prefix + 'progresivaInicioTramo').change(function(){
        $('#' + id_prefix + 'progresivaFinalTramo').val($('#' + id_prefix + 'progresivaInicioTramo').val());
        $('#' + id_prefix + 'progresivaFinalTramo').select2("val", $('#' + id_prefix + 'progresivaInicioTramo').val());
    });
    $('#' + id_prefix + 'progresivaFinalTramo').change(function(){
        $('#' + id_prefix + 'progresivaInicioTramo').val($('#' + id_prefix + 'progresivaFinalTramo').val());
        $('#' + id_prefix + 'progresivaInicioTramo').select2("val", $('#' + id_prefix + 'progresivaFinalTramo').val());
    });
}

function validacionEspecial(datos){
    var campVal = []
    datos.forEach(function(e){
        campVal[e['name']] = e['value'];
    })
    if(typeof campVal['tipoActivo'] !== 'undefined'){
        return (typeof campVal['division']  !== 'undefined' &&
                typeof campVal['operador']  !== 'undefined' &&
                typeof campVal['linea']     !== 'undefined') ? true : false;
    }
    if(typeof campVal['division'] !== 'undefined'){
        return (typeof campVal['operador']  !== 'undefined' &&
                typeof campVal['linea']     !== 'undefined') ? true : false;
    }
    return false
}

$('form').submit(function(){ //Deselecciona para evitar validación
    $('#' + id_prefix + 'progresivaInicioTramo').val('');
    $('#' + id_prefix + 'progresivaInicioTramo').select2("val", '');
    $('#' + id_prefix + 'progresivaFinalTramo').val('');
    $('#' + id_prefix + 'progresivaFinalTramo').select2("val", '');
});
