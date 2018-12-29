/* global id_prefix */

var index = 0;

var dt_itemhojaruta_column_index = {
    id: index++,
    multiselect: index++,

    provincia: index++,
    linea: index++,
    almacen: index++,
    tipoMaterial: index++,
    grupoMaterial: index++,
    estadoConservacion: index++,

    acciones: index
};

$(document).ready(function () {
    initChainedSelects();
});

function addEspecial(id, id_prefix, $selectorValue, itemForm){
}

function initChainedSelects() {
    var selects = { //Defino dependencias:
        provincia: ['almacen'],
        linea: ['almacen']
    };
    var values = [];

    $('form').on('change', 'select.choice', function () {
        if ($(this).val()) {
            var change = $(this).attr('id').replace(id_prefix,'');

            for(var j in selects[change]){ //Itero las dependencias por linea y provincia
                // buscamos los almacenes segun la provincia y/o la linea
                var provincia = $('#' + id_prefix + 'provincia').val();
                var linea = $('#' + id_prefix + 'linea').val();
                var data = [
                    {name:'provincia',value:provincia},
                    {name:'linea', value:linea} //Agregamos la provincia y la linea para buscar los almacenes
                ];

                var target = selects[change][j];

                for(var k in selects){ //Busco el target en otras dependencias para filtrado compuesto
                    var $selectExtra = $('#' + id_prefix + k);
                    if(k !== change && selects[k].indexOf(target) !== -1 && $selectExtra.val() !== ''){
                        data.push({name:k,value:$selectExtra.val()}); //Agrego otras dependencias si no son null
                    }
                }
                var $selectTarget = $('#' + id_prefix + target);
                //var link = (target === 'division')?'divisiones':((target === 'categoria')?'categorizacion':target); //Diferencias de nomenclatura
                var link = target;

                $.ajax({
                    type: 'post',
                    url: __AJAX_PATH__ + link.toLowerCase() + '/lista_por_prov_linea',
                    data: data,
                    target: target,
                    id_prefix: id_prefix,
                    values: values,
                    success: function (data) {
                        // Si se encontraron al menos un ramal
                        if (data.length > 0) {
                            resetSelect('#' + this.id_prefix + this.target);
                            $selectTarget = $('#' + this.id_prefix + this.target);
                            $selectTarget.select2('readonly', false);

                            for (var i = 0, total = data.length; i < total; i++) {
                                var texto = ((typeof data[i].denominacionCorta !== 'undefined')?data[i].denominacionCorta + ' - ':'') + data[i].denominacion;
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
                            resetSelect('#' + this.id_prefix + this.target);
                            $selectTarget.keyup();
                        }
                    }
                });
            }

        }
    });

    for(var i in selects){ //Itero por todos los selects
        for(var j in selects[i]){

            //Guardo los valores originales
            values[selects[i][j]] = $('#' + id_prefix + selects[i][j]).val();
            $('#' + id_prefix + selects[i][j]).select2('readonly', true); //Bloqueo
        }

        //Filtro según valores ya seleccionados:
        if($('#' + id_prefix + i).val() !== ''){
            $('#' + id_prefix + i).trigger('change');
        }
    }
}

function validator(row){
    if(row[2] !== '-' ){ //validar provincia
        for (var i = 3; i < 5; i++) {
            if (row[i] == '-') {
                return false
            }
        }
        return true
    }

    return false
}
