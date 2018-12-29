
/* global __AJAX_PATH__, esTramoIntermedio */

var isEdit = $('[name=_method]').length > 0;

var collectionHolderAllPropiedadesCMPO = $('select.propiedad:first > option').clone();
var collectionHolderAllAtributos = $('select.atributo:first > option').clone();
var collectionHolderFotosActivoLineal;
var id_prefix = "adif_inventariobundle_activolineal_";

//Campos que se deshabilidarán en caso de ser tramo intermedio
var camposTramoIntermedio = ['linea', 'operador', 'corredor', 'division', 'tipoActivo'];

/**
 *
 */
jQuery(document).ready(function () {

    initSelects();
    initChainedSelects();
    initCalculoKilometraje();
    initValoresAtributoActivoLinealForm();
    initValoresPropiedadActivoLinealForm();

    initFiltroValoresAtributo();
    initFiltroValoresPropiedad();
    updateSelectItems();
    updateSelectItemsAtributos();

    //Init Fotos
    updateDeletePhoto($(".prototype-link-remove-foto"));
    initFileInput();
    initFotosActivoLinealForm();

    initViaEstacion();

    initProgresivaInicioTramo();

    initCorreccionEstacionVia(); //CORRECCION PARA IA-243

});

/**
 *
 * @returns {undefined}
 */
function initSelects() {
    $('select.choice').each(function () {
        $(this).select2({
            allowClear: true
        });
    });
}

/**
 *
 */
function initFiltroValoresPropiedad() {
    $('form').on('change','select.propiedad',function () {
        if ($(this).val()) {
            var data = { id: $(this).val() };

            var idValor = $(this).attr('id').replace('idPropiedad','propiedadValor');
            var  $selectorValor = $('#' + idValor);

            resetSelect( $selectorValor );
            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'propiedadvalor/lista_por_propiedad',
                data: data,
                selectorValor:  $selectorValor,
                success: function(data) {

                    if (data.length > 0) {
                        this.selectorValor.select2('readonly', false);

                        for (var i = 0, total = data.length; i < total; i++) {
                            this.selectorValor.append('<option value="' + data[i].id + '">' + data[i].valor + '</option>');
                        }

                        this.selectorValor.select2();
                    } else {
                        this.selectorValor.keyup();
                    }
                }
            });
        }
        resetSelectPropiedadaes();
        updateSelectItems();
    });
}

/**
 *
 */
function initFiltroValoresAtributo() {
    $('form').on('change','select.atributo',function () {
        if ($(this).val()) {
            var data = { id: $(this).val() };

            var idValor = $(this).attr('id').replace('atributo','valoresAtributo');
            var $selectorValor = $('#' + idValor);
            resetSelect( $selectorValor );
            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'valoresatributo/lista_por_atributo',
                data: data,
                selectorValor:  $selectorValor,
                success: function(data) {

                    if (data.length > 0) {
                        this.selectorValor.select2('readonly', false);

                        for (var i = 0, total = data.length; i < total; i++) {
                            this.selectorValor.append('<option value="' + data[i].id + '">' + data[i].denominacion + '</option>');
                        }

                        this.selectorValor.select2();
                    } else {
                        this.selectorValor.keyup();
                    }
                }
            });
        }
        resetSelectAtributos();
        updateSelectItemsAtributos();
    });
}

/**
 *
 */

function initChainedSelects() {
    var selects = { //Defino dependencias:
        linea:['division','ramal','corredor','estacion'],
        operador:['division','corredor'],
        ramal:['estacion'],
        division:['corredor']
    };
    var values = [];
    var selectsTramoIntermedio = [];
    if(esTramoIntermedio){ //Si es tramo intermedio defino cuales deben permanecer deshabilitados:
        selectsTramoIntermedio = camposTramoIntermedio;
    }

    $('form').on('change','select.choice',function () {
        if ($(this).val()) {
            var change = $(this).attr('id').replace(id_prefix,'');

            for(var j in selects[change]){ //Itero las dependencias
                var data = [
                    {name:change,value:$(this).val()} //Agrego el valor que cambió
                ];
                var target = selects[change][j];

                for(var k in selects){ //Busco el target en otras dependencias para filtrado compuesto
                    var $selectExtra = $('#' + id_prefix + k);
                    if(k !== change && selects[k].indexOf(target) !== -1 && $selectExtra.val() !== ''){
                        data.push({name:k,value:$selectExtra.val()}); //Agrego otras dependencias si no son null
                    }
                }
                var $selectTarget = $('#' + id_prefix + target);
                var link = (target === 'division')?'divisiones':((target === 'categoria')?'categorizacion':target); //Diferencias de nomenclatura

                $.ajax({
                    type: 'post',
                    url: __AJAX_PATH__ + link + '/lista',
                    data: data,
                    target: target,
                    id_prefix: id_prefix,
                    values: values,
                    selectsTramoIntermedio: selectsTramoIntermedio,
                    success: function (data) {
                        resetSelect('#' + this.id_prefix + this.target);
                        $selectTarget = $('#' + this.id_prefix + this.target);
                        if (data.length > 0) { // Si se encontro al menos un registro:
                            if(this.selectsTramoIntermedio.indexOf(this.target) === -1 ){
                                $selectTarget.select2('readonly', false);
                            }

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
        if($('#' + id_prefix + i).val() !== '' && !esTramoIntermedio){
            $('#' + id_prefix + i).trigger('change');
        }
    }
}

function initCalculoKilometraje(){
    var inicioTramo = '#adif_inventariobundle_activolineal_progresivaInicioTramo';
    var finalTramo = '#adif_inventariobundle_activolineal_progresivaFinalTramo';
    $(inicioTramo + ',' + finalTramo).keyup(function(){ //Calculo el kilometraje
        var valor = $(finalTramo).val().replace(',','.') - $(inicioTramo).val().replace(',','.');
        var kilometraje = Number(valor).toFixed(3);
        if(!isNaN(kilometraje)){
            $('#adif_inventariobundle_activolineal_kilometraje').val(kilometraje.replace('.',','));
        }
    });

    $(inicioTramo + ',' + finalTramo).change(function(){
        var value = Number($(this).val().replace(',','.')).toFixed(3);
        if(!isNaN(value)){ //Cambio formato
            $(this).val(value.replace('.',','));
        }
    });
}

/**
 *
 * @returns {undefined}
 */
function initValoresAtributoActivoLinealForm() {

    collectionHolderValoresAtributo = $('div.prototype-valor-atributo');

    collectionHolderValoresAtributo.data('index', collectionHolderValoresAtributo.find(':input').length);

    $('.prototype-link-add-valor-atributo').on('click', function (e) {
        e.preventDefault();
        addValorAtributoActivoLinealForm(collectionHolderValoresAtributo);
        initSelects();
        if(collectionHolderAllAtributos.length === 0){
            collectionHolderAllAtributos = $('select.atributo > option').clone();
        }
        updateSelectItemsAtributos();
    });

    var $valorAtributoActivoLinealDeleteLink = $(".prototype-link-remove-valor-atributo");

    updateAtributosDeleteLinks($valorAtributoActivoLinealDeleteLink);
}

/**
 *
 * @param {type} $collectionHolder
 * @returns {addValorAtributoActivoLinealForm}
 */
function addValorAtributoActivoLinealForm($collectionHolder) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var valorAtributoActivoLinealForm = prototype.replace(/__atributo_activo_lineal__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-valor-atributo').closest('.row').before(valorAtributoActivoLinealForm);

    var $valorAtributoActivoLinealDeleteLink = $(".prototype-link-remove-valor-atributo");
    updateAtributosDeleteLinks($valorAtributoActivoLinealDeleteLink);
    initSelects();
    updateSelectItemsAtributos();
}

/**
 *
 * @returns {undefined}
 */
function initValoresPropiedadActivoLinealForm() {
    collectionHolderValoresPropiedad = $('div.prototype-valor-propiedad');
    collectionHolderValoresPropiedad.data('index', collectionHolderValoresPropiedad.find(':input').length);

    $('.prototype-link-add-valor-propiedad').on('click', function (e) {
        e.preventDefault();
        addValorPropiedadActivoLinealForm(collectionHolderValoresPropiedad);
        initSelects();
        if(collectionHolderAllPropiedadesCMPO.length === 0){
            collectionHolderAllPropiedadesCMPO = $('select.propiedad > option').clone();
        }
        updateSelectItems();
    });
    //En editar Selecciona el valor guardado
    $('select.propiedad').each(function(){
        var idValor = $(this).attr('id').replace('idPropiedad','propiedadValor');
        $(this).select2({
            allowClear: true
        }).select2('val',$("#" + idValor).attr('idPropiedad'));
    });

    var $valorPropiedadActivoLinealDeleteLink = $(".prototype-link-remove-valor-propiedad");
    updatePropiedadesDeleteLinks($valorPropiedadActivoLinealDeleteLink);
}

/**
 *
 * @param {type} $collectionHolder
 * @returns {addValorPropiedadActivoLinealForm}
 */
function addValorPropiedadActivoLinealForm($collectionHolder) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var valorPropiedadActivoLinealForm = prototype.replace(/__propiedad_activo_lineal__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-valor-propiedad').closest('.row').before(valorPropiedadActivoLinealForm);

    var $valorPropiedadActivoLinealDeleteLink = $(".prototype-link-remove-valor-propiedad");
    updatePropiedadesDeleteLinks($valorPropiedadActivoLinealDeleteLink);
    initSelects();
    updateSelectItems();
}

/**
 *
 * @returns Array
 */
function listaPropiedadesSelecionadas(){
    var list = [];
    var selectPropiedad = $('[id ^= adif_inventariobundle_activolineal_valoresPropiedad_][id $= _idPropiedad]').find('option:selected');
        selectPropiedad.each(function(){
            valor = $(this).val();
            idPropiedad = $(this).parent('select').attr('id');
            list.push([valor, idPropiedad]);
    });
    return list;
}

function resetSelectPropiedadaes(){
    var listaItemSelected = listaPropiedadesSelecionadas ();
    var listLen = listaItemSelected.length;
    if (listLen !== 0){
        $('select.propiedad').empty();
        $(collectionHolderAllPropiedadesCMPO).appendTo('select.propiedad');
        for( var i = 0; i<listLen; i++){
            $("#"+listaItemSelected[i][1])
            .find('option:selected').removeAttr('selected');
            $("#"+listaItemSelected[i][1])
            .find('option[value="'+listaItemSelected[i][0]+'"]')
            .attr("selected",true);
        }
    }
}

/**
 * Actualiza la lista de propiedades
 * seleccionable segÃºn las propiedades ya
 * seleccionadas.
 *
 */
function updateSelectItems(){
    $('select.propiedad')
    .each(function(){
        var idPropiedad = $(this).children("option:selected").val();
        var itemSelected = listaPropiedadesSelecionadas();
        var itemLen = itemSelected.length;
        for ( i=0 ; i<itemLen ; i++){
            if( itemSelected[i][0] !== idPropiedad ){
                $(this).children("option[value='"+itemSelected[i][0]+"']")
                .remove();
            }
        }
    });
}

function updatePropiedadesDeleteLinks(deleteLink) {

    deleteLink.each(function () {

        $(this).tooltip();

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var deletableRow = $(this).closest('.row');
            var itemAdd = $(this).closest('.row')
                            .find('[id ^= adif_inventariobundle_activolineal_valoresPropiedad_][id $= _idPropiedad]')
                            .find("option:selected");
            var itemValue = $(itemAdd).val();
            var itemText = $(itemAdd).text();

            show_confirm({
                msg: 'Â¿Desea eliminar el registro?',
                callbackOK: function () {
                    deletableRow.hide('slow', function () {
                        deletableRow.remove();
                        if($.trim(itemValue)){
                            $('select.propiedad')
                            .each(function(){
                                $(this).append('<option value="'+itemValue+'">'+itemText+'</option>');
                            });
                        }

                    });
                }
            });

            e.stopPropagation();

        });
    });
}

/**
 *
 * @returns Array
 */
function listaAtributosSeleccionados(){
    var list = [];
    var selectAtributo = $('[id ^= adif_inventariobundle_activolineal_valoresAtributo_][id $= _atributo]').find('option:selected');

        selectAtributo.each(function(){
            valor = $(this).val();
            atributo = $(this).parent('select').attr('id');
            list.push([valor, atributo]);
    });
    return list;
}

function resetSelectAtributos(){
    var listaItemSelected = listaAtributosSeleccionados();
    var listLen = listaItemSelected.length;
    if (listLen !== 0){
        $('select.atributo').empty();
        $(collectionHolderAllAtributos).appendTo('select.atributo');
        for( var i = 0; i<listLen; i++){
            $("#"+listaItemSelected[i][1])
            .find('option:selected').removeAttr('selected');
            $("#"+listaItemSelected[i][1])
            .find('option[value="'+listaItemSelected[i][0]+'"]')
            .attr("selected",true);
        }
    }

}

/**
 * Actualiza la lista de atributos
 * seleccionable segÃºn los atributos ya
 * seleccionados.
 *
 */
function updateSelectItemsAtributos(){
    $('select.atributo')
    .each(function(){
        var atributo = $(this).children("option:selected").val();
        var itemSelected = listaAtributosSeleccionados();
        var itemLen = itemSelected.length;
        for ( i=0 ; i<itemLen ; i++){
            if( itemSelected[i][0] !== atributo ){
                $(this).children("option[value='"+itemSelected[i][0]+"']")
                .remove();
            }
        }
    });
}

function updateAtributosDeleteLinks(deleteLink) {

    deleteLink.each(function () {

        $(this).tooltip();

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var deletableRow = $(this).closest('.row');
            var itemAdd = $(this).closest('.row')
                            .find('[id ^= adif_inventariobundle_activolineal_valoresAtributo_][id $= _atributo]')
                            .find("option:selected");
            var itemValue = $(itemAdd).val();
            var itemText = $(itemAdd).text();

            show_confirm({
                msg: 'Â¿Desea eliminar el registro?',
                callbackOK: function () {
                    deletableRow.hide('slow', function () {
                        deletableRow.remove();
                        if($.trim(itemValue)){
                            $('select.atributo')
                            .each(function(){
                                $(this).append('<option value="'+itemValue+'">'+itemText+'</option>');
                            });
                        }

                    });
                }
            });

            e.stopPropagation();

        });
    });
}


function initFotosActivoLinealForm() {
    collectionHolderFotosActivoLineal = $('div.prototype-fotos-ActivosLineales');
    collectionHolderFotosActivoLineal.data('index', collectionHolderFotosActivoLineal.find(':input').length);

    $('.prototype-link-add-fotos-ActivosLineales').on('click', function (e) {
        e.preventDefault();
        addFotosActivoLinealForm(collectionHolderFotosActivoLineal);
        initFileInput();
    });
}

/**
 *
 * @param {type} $collectionHolder
 * @returns {addFotosActivoLinealForm}
 */
function addFotosActivoLinealForm($collectionHolder) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var activoLinealForm = prototype.replace(/__fotos__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-fotos-ActivosLineales').closest('.row').before(activoLinealForm);

    var $fotosActivoLineal = $(".prototype-link-remove-foto");

    updateDeletePhoto($fotosActivoLineal);
    initInputPreview();
}

function initInputPreview(){
    $('input.inputPreview').change(function(){
        imgPreview(this);
    });
}

function imgPreview(input){
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(input).parent().next("div").children("img").attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
        var fecha = new Date(input.files[0].lastModified);
        $(input).parent().next("div").children("div").
        text(fecha.getDate() + '/' + ("0" + (fecha.getMonth() + 1)).slice(-2) + '/' + fecha.getFullYear());
    }
}

/**
 *
 * @param {type} deleteLink
 * @returns {undefined}
 */
function updateDeletePhoto(deleteLink) {

    deleteLink.each(function () {

        $(this).tooltip();
        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var deletableRow = $(this).closest('.imagenes');

            show_confirm({
                msg: 'Â¿Desea eliminar el registro?',
                callbackOK: function () {
                    deletableRow.hide('slow', function () {
                        deletableRow.remove();
                    });
                }
            });

            e.stopPropagation();

        });
    });
}

/**
 * Deshabilita estación o vía según selección
 *
 */
function initViaEstacion(){
    var selects = '#' + id_prefix + 'tipoActivo, ';
    selects += '#' + id_prefix + 'linea, ';
    selects += '#' + id_prefix + 'ramal';

    $(selects).change(function(){
        var value = $('#' + id_prefix + 'tipoActivo').children('option:selected').text();
        $('#' + id_prefix + 'zonaVia').prop('disabled', (value == 'Estación' || value == '-- Tipo de Activo --'));
        $('#cod_estacion').prop('disabled', (value != 'Estación' || value == '-- Tipo de Activo --'));
    }).trigger('change');
}

/**
 * Actualiza el valor de la progresiva de inicio para que el nuevo activo lineal sea
 * contiguo al último.
 *
 */
function initProgresivaInicioTramo(){
    //Valores que identifican el activo lineal
    var selects = ['linea', 'operador', 'corredor', 'division'];
    $('form').on('change','select.choice',function () {
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
                url: __AJAX_PATH__ + 'activolineal/progresivainiciotramo',
                data: data,
                id_prefix: id_prefix,
                success: function (resp) {
                    $('#' + this.id_prefix + 'progresivaInicioTramo').val(resp);
                    //actualizarValorProgresivaFinal(); COMENTADO POR IA-247
                }
            });
        }
        if(change === 'tipoActivo'){
            //actualizarValorProgresivaFinal(); COMENTADO POR IA-247
        }
    }).trigger('change');

    /* COMENTADO POR IA-247
     * Deshabilito si es tramo intermedio para que no se produzca un hueco en la progresiva:
    if(esTramoIntermedio){
        for(var i in camposTramoIntermedio){
            var $select = $('#' + id_prefix + camposTramoIntermedio[i]);
            $select.select2('readonly',true);
        }
    }
    actualizarValorProgresivaFinal();*/
}

/**
 * Actualiza el valor de la progresiva final si es estación y
 * des/habilita la misma en caso de ser vía
 *
 */
function actualizarValorProgresivaFinal(){
    //Si es estación:
    var esEstacion = ($('#' + id_prefix + 'tipoActivo').children('option:selected').text() === 'Estación');
    if(esEstacion){ //Las progresivas deben ser iguales
        $('#' + id_prefix + 'progresivaFinalTramo').val($('#' + id_prefix + 'progresivaInicioTramo').val());
    }
    $('#' + id_prefix + 'progresivaFinalTramo').prop('readonly', esEstacion || esTramoIntermedio );// y no es editable
}

function initCorreccionEstacionVia(){
    var esEstacion = ($('#' + id_prefix + 'tipoActivo').children('option:selected').text() === 'Estación');
    if(esEstacion){
        var textEstacion = $('#' + id_prefix + 'zonaVia').val();
        var valorEstacion = $('#' + id_prefix + 'estacion').children('option[text="'+textEstacion+'"]').val();
        $('#' + id_prefix + 'estacion').val( valorEstacion );
    }
}
