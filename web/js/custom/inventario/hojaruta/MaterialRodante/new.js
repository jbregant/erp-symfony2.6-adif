/* global id_prefix*/

var index = 0;
var dt_itemhojaruta_column_index = {
    id: index++,
    multiselect: index++,

    operador: index++,
    linea: index++,
    estacion: index++,
    grupoRodante: index++,
    tipoRodante: index++,
    numeroVehiculo: index++,

    acciones: index
};

$(document).ready(function () {
    initChainedSelects();
});

function initChainedSelects() {
    var selects = {//Defino dependencias:
        operador: ['numeroVehiculo'],
        linea: ['estacion'],
        estacion: ['numeroVehiculo'],
        grupoRodante: ['tipoRodante', 'numeroVehiculo'],
        tipoRodante: ['numeroVehiculo'],

    };
    var values = [];

    $('form').on('change', 'select.choice', function () {
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
                var link = (target === 'numeroVehiculo') ? 'catalogomaterialesrodantes/mr_por_num_inventario' : target + '/lista'; //Diferencias de nomenclatura
                $.ajax({
                    type: 'post',
                    url: __AJAX_PATH__ + link.toLowerCase(),
                    data: data,
                    target: target,
                    id_prefix: id_prefix,
                    values: values,
                    success: function (data) {
                        resetSelect('#' + this.id_prefix + this.target);
                        if (data.length > 0) {
                            $selectTarget = $('#' + this.id_prefix + this.target);
                            $selectTarget.select2('readonly', false);

                            for (var i = 0, total = data.length; i < total; i++) {
                                var texto = ((typeof data[i].denominacionCorta !== 'undefined') ? data[i].denominacionCorta + ' - ' : '') + data[i].denominacion;
                                var texto = (typeof data[i].numeroVehiculo !== 'undefined') ? data[i].numeroVehiculo : texto;
                                $selectTarget.append('<option value="' + data[i].id + '">' + texto + '</option>');
                            }

                            $selectTarget.select2();

                            $selectTarget.val(this.values[this.target]);
                            $selectTarget.select2("val", this.values[this.target]);
                            if (null === $selectTarget.val()) {
                                $selectTarget.select2("val", "");
                            }

                        } else {
                            resetSelect($selectTarget);
                            $selectTarget.select2('readonly', true);
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

function validacionEspecial(datos){
    var campVal = []
    datos.forEach(function(e){
        campVal[e['name']] = e['value'];
    })

    if(typeof campVal['numeroVehiculo'] !== 'undefined'){
        return (typeof campVal['estacion']  !== 'undefined' &&
                typeof campVal['linea']  !== 'undefined' &&
                typeof campVal['operador']  !== 'undefined') ? true : false;
    }

    if(typeof campVal['tipoRodante'] !== 'undefined'){
        return (typeof campVal['grupoRodante']  !== 'undefined' &&
                typeof campVal['estacion']  !== 'undefined' &&
                typeof campVal['linea']  !== 'undefined' &&
                typeof campVal['operador']  !== 'undefined') ? true : false;
    }

    if(typeof campVal['grupoRodante'] !== 'undefined'){
        return (typeof campVal['estacion']  !== 'undefined' &&
                typeof campVal['linea']  !== 'undefined' &&
                typeof campVal['operador']  !== 'undefined') ? true : false;
    }

    if(typeof campVal['estacion'] !== 'undefined'){
        return (typeof campVal['linea']  !== 'undefined' &&
                typeof campVal['operador'] !== 'undefined') ? true : false;
    }


    return false
}

function transformValue(campo){
    if(campo === 'numeroVehiculo'){
        return $('#' + id_prefix + campo).find('option:selected').text();
    }
    return $('#' + id_prefix + campo).val();
}


function fixMrTransform(id, datosItem){
    if(id === 'numeroVehiculo'){
        return datosItem['id'];
    }
    return datosItem[id];
}

function fixValidMR(data){
    var flag = true
    for(var i=0 ; i < data.length ; i++){
        if( data[i]['name'] == "numeroVehiculo"){
            flag = false
        }
    }
    return flag
}

function idToDenominacion(id, value){
    if( value == "" && ( id == 'estacion' || id == 'numeroVehiculo') ){
        return "-";
    }else{
        if( id == 'estacion'){
            return $('#adif_inventariobundle_hojarutamaterialrodante_estacion option[value = "'+value+'"]').text()
        }
        else if ( id == 'numeroVehiculo') {
            return $('#adif_inventariobundle_hojarutamaterialrodante_numeroVehiculo option[value = "'+value+'"]').text()
        }
    }


    return value
}
