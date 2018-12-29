
/* global __AJAX_PATH__ */

var isEdit = $('[name=_method]').length > 0;
var collectionHolderFotosCatalogoMaterialesProducidosDeObra;
var collectionHolderAllPropiedadesCMPO = [];
//var firstRun = true;
if(isEdit){
    collectionHolderAllPropiedadesCMPO = $('select.propiedad:first > option').clone();
    //collectionHolderAllPropiedadesCMPO = $('select.propiedad > option').clone();
}
//else{
//    firstRun = false;
//}

/**
 *
 */
jQuery(document).ready(function () {

    initSelects();
    //initChainedSelects();

    //Init Propiedades
    initValoresPropiedadMaterialesProducidosForm();
    initFiltroValoresPropiedad();

    //Init Fotos
    updateDeletePhoto($(".prototype-link-remove-foto"));
    initFileInput();
    initFotosCatalogoMaterialesProducidosDeObraForm();

    updateSelectItems();

    //Init Estado
    initFiltroValidarEstado();
//    $('select.propiedad').trigger('change');
});

//$(document).ajaxStop(function() {
//  firstRun = false
//});

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
 * @returns {undefined}
 *
function initChainedSelects() {
    var id_prefix = "adif_inventariobundle_catalogomaterialesproducidosdeobra_";
    var selects = { //Defino dependencias:
      propiedad:['valoresPropiedad']
    };
    var values = [];
    for(var i in selects){ //Bloqueo todos los selects que dependen de algún select
        for(var j in selects[i]){ 
            $('#' + id_prefix + selects[i][j]).select2('readonly', true);
            if (isEdit) { //Si es edit guardo los valores originales
                values[selects[i][j]] = $('#' + id_prefix + selects[i][j]).val();
            }
        }
    }
    //TODO: Filtrar según varios parametros
    $('form').on('change','select.choice',function () {
        if ($(this).val()) {
            var data = { id: $(this).val() };
            var change = $(this).attr('id').replace(id_prefix,'');

            for(var j in selects[change]){ //Itero las dependencias
                var target = selects[change][j];
                
                var $selectTarget = $('#' + id_prefix + target);
                var link = (target === 'division')?'divisiones':target; //Diferencias de nomenclatura
                //link = (link === 'categoria')?'categorizacion':link; //Diferencias de nomenclatura
                resetSelect($selectTarget);
                $.ajax({
                    type: 'post',
                    url: __AJAX_PATH__ + link + '/lista_por_'+ change,
                    data: data,
                    selectTarget: $selectTarget,
                    success: function (data) { 
                        // Si se encontraron al menos un ramal
                        if (data.length > 0) {
                            this.selectTarget.select2('readonly', false);

                            for (var i = 0, total = data.length; i < total; i++) {
                                var texto = ((typeof data[i].denominacionCorta !== 'undefined')?data[i].denominacionCorta + ' - ':'') + data[i].denominacion;
                                this.selectTarget.append('<option value="' + data[i].id + '">' + texto + '</option>');
                            }

                            if (isEdit) {
                                this.selectTarget.val(values[target]);
                                if (null === $selectTarget.val()) {
                                    this.selectTarget.select2("val", "");
                                }
                            } else {
                                this.selectTarget.val(this.selectTarget.find('option:first').val());
                            }

                            this.selectTarget.prop('required', true);
                            this.selectTarget.select2();
                        } else {
                            this.selectTarget.prop('required', false);
                            this.selectTarget.keyup();
                        }
                    }
                });
            }

        }
    }).trigger('change');
}*/



/**
 *
 * @returns {undefined}
 */
function initValoresPropiedadMaterialesProducidosForm() {

    collectionHolderValoresPropiedad = $('div.prototype-valor-propiedad-materialproducido');

    collectionHolderValoresPropiedad.data('index', collectionHolderValoresPropiedad.find(':input').length);

    $('.prototype-link-add-propiedad-materialproducido').on('click', function (e) {
        e.preventDefault();
        addValorPropiedadMaterialProducidoForm(collectionHolderValoresPropiedad);
        initSelects();
        if(collectionHolderAllPropiedadesCMPO.length === 0){
            collectionHolderAllPropiedadesCMPO = $('select.propiedad > option').clone();
        }
        updateSelectItems();
    });

    //En editar Selecciona el valor guardado
    $('select.propiedadValor').each(function(){
        $(this).select2({
            allowClear: true
        }).select2('val',$(this).siblings('span.valorPropiedad').attr('valor'));
    });
    //En editar Selecciona el valor guardado
    $('select.propiedad').each(function(){
        var idValor = $(this).attr('id').replace('idPropiedad','propiedadValor');
        $(this).select2({
            allowClear: true
        }).select2('val',$("#" + idValor).attr('idPropiedad'));
    });

    updatePropiedadesDeleteLinks($(".prototype-link-remove-valor-propiedad-materialproducido"));
    //updateDeleteLinks($(".prototype-link-remove-valor-propiedad-materialproducido"));
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
                    //editLoadValores();
                }
            });
        }
        resetSelectPropiedadaes();
        updateSelectItems();
    });
}



/**
 *
 * @param {type} $collectionHolder
 * @returns {addValorPropiedadMaterialProducidoForm}
 */
function addValorPropiedadMaterialProducidoForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var valorPropiedadMaterialProducidoForm = prototype.replace(/__propiedad_material_producido__/g, index);

    $collectionHolder.data('index', index + 1);

    var newItem = $('.prototype-link-add-propiedad-materialproducido').closest('.row').before(valorPropiedadMaterialProducidoForm);
    var $valorPropiedadMaterialProducidoDeleteLink = $(".prototype-link-remove-valor-propiedad-materialproducido");
    updatePropiedadesDeleteLinks($valorPropiedadMaterialProducidoDeleteLink);
    //updateDeleteLinks($valorPropiedadMaterialProducidoDeleteLink);
    initSelects();
    updateSelectItems();
}

/**
 *
 * @returns Array
 */
function listaPropiedadesSelecionadas(){
    var list = [];
    var selectPropiedad = $('[id ^= adif_inventariobundle_catalogomaterialesproducidosdeobra_valoresPropiedad_][id $= _idPropiedad]').find('option:selected');
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
    if (listLen!=0){
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
 * seleccionable según las propiedades ya
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
            if( itemSelected[i][0]!= idPropiedad ){
                $(this).children("option[value='"+itemSelected[i][0]+"']")
                .remove();
            }
        }
    });
}

/*
function editLoadValores(){
    if(isEdit && firstRun){
        var valores = $('span.valorPropiedad');
        var spanLen = valores.length;


        for( var i = 0 ; i < spanLen ; i++ ){
            $('#adif_inventariobundle_catalogomaterialesproducidosdeobra_valoresPropiedad_'+i+'_propiedadValor')
            .children('option:contains("'+$(valores[i]).attr('valor')+'")').prop("selected",true).trigger('change');
        }

    }
}*/

function updatePropiedadesDeleteLinks(deleteLink) {

    deleteLink.each(function () {

        $(this).tooltip();

        $(this).off("click").on('click', function (e) {

            e.preventDefault();

            var deletableRow = $(this).closest('.row');
            var itemAdd = $(this).closest('.row')
                            .find('[id ^= adif_inventariobundle_catalogomaterialesproducidosdeobra_valoresPropiedad_][id $= _idPropiedad]')
                            .find("option:selected");
            var itemValue = $(itemAdd).val();
            var itemText = $(itemAdd).text();

            show_confirm({
                msg: '¿Desea eliminar el registro?',
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
 * @returns {undefined}
 */
function initFotosCatalogoMaterialesProducidosDeObraForm() {
    collectionHolderFotosCatalogoMaterialesProducidosDeObra = $('div.prototype-fotos-CatalogoMaterialesProducidosDeObra');
    collectionHolderFotosCatalogoMaterialesProducidosDeObra.data('index', collectionHolderFotosCatalogoMaterialesProducidosDeObra.find(':input').length);

    $('.prototype-link-add-fotos-CatalogoMaterialesProducidosDeObra').on('click', function (e) {
        e.preventDefault();
        addFotosCatalogoMaterialesProducidosDeObraForm(collectionHolderFotosCatalogoMaterialesProducidosDeObra);
        initFileInput();
    });
}

/**
 *
 * @param {type} $collectionHolder
 * @returns {addFotosCatalogoMaterialesProducidosDeObraForm}
 */
function addFotosCatalogoMaterialesProducidosDeObraForm($collectionHolder) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var catalogoMaterialesProducidosDeObraForm = prototype.replace(/__fotos__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-fotos-CatalogoMaterialesProducidosDeObra').closest('.row').before(catalogoMaterialesProducidosDeObraForm);

    var $fotosCatalogoMaterialesProducidosDeObra = $(".prototype-link-remove-foto");

    updateDeletePhoto($fotosCatalogoMaterialesProducidosDeObra);
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
 * @returns {undefined}
 */
function initFiltroValidarEstado() {

  var $selectUnidadMedida = $('#adif_inventariobundle_catalogomaterialesproducidosdeobra_unidadMedida');
//  var $selectMedida = $('#adif_inventariobundle_catalogomaterialesproducidosdeobra_medida');
//  var $selectVolumen = $('#adif_inventariobundle_catalogomaterialesproducidosdeobra_volumen');
//  var $selectPeso = $('#adif_inventariobundle_catalogomaterialesproducidosdeobra_peso');

    /*  Solo me importa este cambio para que sea activo */
  $selectUnidadMedida.change(function () {
     
    $('#idEstadoInventario').empty();
    $('#idEstadoInventario').append("Borrador");

    if ($(this).val()){
      if($selectUnidadMedida.val() != "" && $selectUnidadMedida.val() > 0){
        $('#idEstadoInventario').empty();
        $('#idEstadoInventario').append("Activo");
      }
    }

  }).trigger('change');
  
  
/* Cambio la forma de detectar si esta activo solo depende del campo unidad de medida.
  $selectMedida.change(function () {

    $('#idEstadoInventario').empty();
    $('#idEstadoInventario').append("Borrador");

    if($selectMedida.val().length > 0 || $selectVolumen.val().length > 0 || $selectPeso.val().length > 0){
      if($selectUnidadMedida.val()){
        $('#idEstadoInventario').empty();
        $('#idEstadoInventario').append("Activo");
      }
      else{
        $('#idEstadoInventario').empty();
        $('#idEstadoInventario').append("Borrador");
      }
    }

  }).trigger('change');

  $selectVolumen.change(function () {

    $('#idEstadoInventario').empty();
    $('#idEstadoInventario').append("Borrador");

    if($selectMedida.val().length > 0 || $selectVolumen.val().length > 0 || $selectPeso.val().length > 0){
      if($selectUnidadMedida.val()){
        $('#idEstadoInventario').empty();
        $('#idEstadoInventario').append("Activo");
      }
      else{
        $('#idEstadoInventario').empty();
        $('#idEstadoInventario').append("Borrador");
      }
    }

  }).trigger('change');

    
  $selectPeso.change(function () {

    $('#idEstadoInventario').empty();
    $('#idEstadoInventario').append("Borrador");

    if($selectMedida.val().length > 0 || $selectVolumen.val().length > 0 || $selectPeso.val().length > 0){
      if($selectUnidadMedida.val()){
        $('#idEstadoInventario').empty();
        $('#idEstadoInventario').append("Activo");
      }
      else{
        $('#idEstadoInventario').empty();
        $('#idEstadoInventario').append("Borrador");
      }
    }

  }).trigger('change'); */

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
                msg: '¿Desea eliminar el registro?',
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
