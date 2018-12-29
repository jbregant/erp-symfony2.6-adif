/* global __AJAX_PATH__ */

var isEdit = $('[name=_method]').length > 0;

var collectionHolderFotosCatalogoMaterialesNuevos;
var collectionHolderAllPropiedadesCMN = [];
collectionHolderAllPropiedadesCMN = $('select.propiedad:first > option').clone();

jQuery(document).ready(function () {

    initSelects();

     //Init Fotos
    updateDeletePhoto($(".prototype-link-remove-foto"));
    initFileInput();
    initFotosCatalogoMaterialesNuevosForm();

    //Init Atributos
    initValoresAtributoForm();

    //Init Propiedades
    initValoresPropiedadCMNForm();
    initSetMaterialesForm();
    initFiltroValoresPropiedad();
    updateSelectItems();

    //Init Estado
    initFiltroValidarEstado();

    //Init Cantidades x Cajas/Pallets
    initFiltroActiveCheckBoxs();
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
 * @returns {undefined}
 */
function checkErrors() {
    $('.tab-pane').each(function () {
        var $id = $(this).prop('id');
        var $tagA = $('a[href=#' + $id + ']');
        var $li = $tagA.parent('li');

        if ($(this).has(".form-group.has-error").length) {
            $li.addClass('has-error');
            if (!$tagA.has('.fa-warning').length) {
                $tagA.append('<i class="fa fa-warning"></i>');
            }
            $tagA.click();
        } else {
            $li.removeClass('has-error');
            $tagA.find('i').remove();
        }
    });
}

/**
 *
 * @returns {undefined}
 */
function initValoresAtributoForm() {

    collectionHolderValoresAtributo = $('div.prototype-valor-atributo');

    collectionHolderValoresAtributo.data('index', collectionHolderValoresAtributo.find(':input').length);

    $('.prototype-link-add-valor-atributo').on('click', function (e) {
        e.preventDefault();
        addValorAtributoForm(collectionHolderValoresAtributo);
        initSelects();
    });
}

/**
 *
 * @param {type} $collectionHolder
 * @returns {addValorAtributoForm}
 */
function addValorAtributoForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var valorAtributoForm = prototype.replace(/__atributo__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-valor-atributo').closest('.row').before(valorAtributoForm);

    var $valorAtributoDeleteLink = $(".prototype-link-remove-valor-atributo");

    updateDeleteLinks($valorAtributoDeleteLink);

    initSelects();
}


/**
 *
 * @returns {undefined}
 */
function initValoresPropiedadCMNForm() {

    collectionHolderValoresPropiedad = $('div.prototype-valor-propiedad-CMN');

    collectionHolderValoresPropiedad.data('index', collectionHolderValoresPropiedad.find(':input').length);

    $('.prototype-link-add-valor-propiedad').on('click', function (e) {
        e.preventDefault();
        addValorPropiedadCMNForm(collectionHolderValoresPropiedad);
        initSelects();
        if(collectionHolderAllPropiedadesCMN.length ===  0){
            collectionHolderAllPropiedadesCMN = $('select.propiedad > option').clone();
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

    updatePropiedadesDeleteLinks($(".prototype-link-remove-valor-propiedad-CMN"));
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
 * @param {type} $collectionHolder
 * @returns {addValorPropiedadCMNForm}
 */
function addValorPropiedadCMNForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var valorPropiedadForm = prototype.replace(/__propiedad_CMN__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-valor-propiedad').closest('.row').before(valorPropiedadForm);

    var $valorPropiedadDeleteLink = $(".prototype-link-remove-valor-propiedad-CMN");
    updatePropiedadesDeleteLinks($valorPropiedadDeleteLink);
    //updateDeleteLinks($valorPropiedadDeleteLink);

    initSelects();
    updateSelectItems();
}


/**
 *
 * @returns Array
 */
function listaPropiedadesSelecionadas(){
    var list = [];
    var selectPropiedad = $('[id ^= adif_inventariobundle_catalogomaterialesnuevos_valoresPropiedad_][id $= _idPropiedad]').find('option:selected');
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
        $(collectionHolderAllPropiedadesCMN).appendTo('select.propiedad');
        for( var i = 0; i<listLen; i++){
            $("#"+listaItemSelected[i][1])
            .find('option:selected').removeAttr('selected');
            $("#"+listaItemSelected[i][1])
            .find('option[value="'+listaItemSelected[i][0]+'"]')
            .attr("selected",true);
        }
    }
}


function resetSelectMateriales(){
    var listaItemSelected = listaMaterialesSelecionados ();
    var listLen = listaItemSelected.length;
    if (listLen!=0){
        $('select.material').empty();
        $(collectionHolderAllMaterialesCMN).appendTo('select.material');
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

    $('select.material')
    .each(function(){
        var idMaterial = $(this).children("option:selected").val();
        var itemSelected = listaMaterialesSelecionados();
        var itemLen = itemSelected.length;
        for ( i=0 ; i<itemLen ; i++){
            if( itemSelected[i][0]!= idMaterial ){
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
                            .find('[id ^= adif_inventariobundle_catalogomaterialesnuevos_valoresPropiedad_][id $= _idPropiedad]')
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
function initFotosCatalogoMaterialesNuevosForm() {
    collectionHolderFotosCatalogoMaterialesNuevos = $('div.prototype-fotos-CatalogoMaterialesNuevos');
    collectionHolderFotosCatalogoMaterialesNuevos.data('index', collectionHolderFotosCatalogoMaterialesNuevos.find(':input').length);

    $('.prototype-link-add-fotos-CatalogoMaterialesNuevos').on('click', function (e) {
        e.preventDefault();
        addFotosCatalogoMaterialesNuevosForm(collectionHolderFotosCatalogoMaterialesNuevos);
        initFileInput();
    });
}


/**
 *
 * @param {type} $collectionHolder
 * @returns {addFotosCatalogoMaterialesNuevosForm}
 */
function addFotosCatalogoMaterialesNuevosForm($collectionHolder) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var catalogoMaterialesNuevosForm = prototype.replace(/__fotos__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-fotos-CatalogoMaterialesNuevos').closest('.row').before(catalogoMaterialesNuevosForm);

    var $fotosCatalogoMaterialesNuevos = $(".prototype-link-remove-foto");

    updateDeletePhoto($fotosCatalogoMaterialesNuevos);
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


/**
 *
 * @returns {undefined}
 */
    function initFiltroValidarEstado() {

    /*
     *  Si Tiene unidad de medida de inventario y al menos uno de Medidas, Volumen, Peso.
     *  El estado pasa a Activo, sino es Borrador
     * */

    var $selectUnidad = $('#adif_inventariobundle_catalogomaterialesnuevos_unidadMedida');
    var $selectMedida = $('#adif_inventariobundle_catalogomaterialesnuevos_medida');
    var $selectVolumen = $('#adif_inventariobundle_catalogomaterialesnuevos_volumen');
    var $selectPeso = $('#adif_inventariobundle_catalogomaterialesnuevos_peso');

    $selectUnidad.change(function () {
        if ($(this).val()){
            // Cambia solo pasa activo si unidad de medida tiene un valor
//            if(  $selectUnidad.val() != "" && ( $selectMedida.val() != "" ||  $selectVolumen.val() != "" ||  $selectPeso.val() != ""  )   ){
            if(  $selectUnidad.val() != "" ){
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Activo");
            }
        }else{
            $('#estadoInventario').empty();
            $('#estadoInventario').append("Borrador");
        }

    }).trigger('change');


    // Cambia, solo me importa si la unidad de medida tiene un valor para saber si el estado es activo o borrador
    /*

    $selectMedida.change(function () {

        if ($(this).val()){
            if(  $selectUnidad.val() != "" && ( $selectMedida.val() != "" ||  $selectVolumen.val() != "" ||  $selectPeso.val() != ""  )   ){
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Activo");
            }
        }else{
            if(  $selectUnidad.val() != "" && ( $selectMedida.val() != "" ||  $selectVolumen.val() != "" ||  $selectPeso.val() != ""  )   ){
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Activo");
            }else{
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Borrador");
            }
        }

    }).trigger('change');

    $selectVolumen.change(function () {

        if ($(this).val()){
            if(  $selectUnidad.val() != "" && ( $selectMedida.val() != "" ||  $selectVolumen.val() != "" ||  $selectPeso.val() != ""  )   ){
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Activo");
            }
        }else{
            if(  $selectUnidad.val() != "" && ( $selectMedida.val() != "" ||  $selectVolumen.val() != "" ||  $selectPeso.val() != ""  )   ){
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Activo");
            }else{
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Borrador");
            }
        }

    }).trigger('change');

    $selectPeso.change(function () {

        if ($(this).val()){
            if(  $selectUnidad.val() != "" && ( $selectMedida.val() != "" ||  $selectVolumen.val() != "" ||  $selectPeso.val() != ""  )   ){
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Activo");
            }
        }else{
            if(  $selectUnidad.val() != "" && ( $selectMedida.val() != "" ||  $selectVolumen.val() != "" ||  $selectPeso.val() != ""  )   ){
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Activo");
            }else{
                $('#estadoInventario').empty();
                $('#estadoInventario').append("Borrador");
            }
        }

    }).trigger('change');

    */
}


/**
 *
 * @returns {undefined}
 */
function initFiltroActiveCheckBoxs() {

  var $activePallet = $('#adif_inventariobundle_catalogomaterialesnuevos_transportePallet');
  var $activeCaja = $('#adif_inventariobundle_catalogomaterialesnuevos_transporteCajas');
  var $activeEsSet = $('#adif_inventariobundle_catalogomaterialesnuevos_esSet');

  var $palletID = $('#unidadesxPallet');
  var $cajaID = $('#unidadesxCajas');
  var $essetID = $('#setMaterialesTab');

  if($activePallet.attr('checked')){
    $palletID.prop('disabled', false);
  }

  if($activeCaja.attr('checked')){
    $cajaID.prop('disabled', false);
  }

  if($activeEsSet.attr('checked')){
    $essetID.removeClass('disabled');
  }

  $activePallet.change(function () {
    $activePallet.attr('checked') ? $palletID.prop('disabled', false) : $palletID.prop('disabled', true);
  });

  $activeCaja.change(function () {
    $activeCaja.attr('checked') ? $cajaID.prop('disabled', false) : $cajaID.prop('disabled', true);
  });

  $activeEsSet.change(function () {
    $activeEsSet.attr('checked') ? $essetID.removeAttr('class', 'disabled'): $essetID.attr('class', 'disabled');
  });

  $essetID.click(function(event){
    if ($(this).hasClass('disabled')) {
      return false;
    }
  });

}


/**
 *
 * @returns {undefined}
 */
function initSetMaterialesForm() {

    collectionHolderSetMateriales = $('div.prototype-set-material');

    collectionHolderSetMateriales.data('index', collectionHolderSetMateriales.find(':input').length);

    $('.prototype-link-add-set-material').on('click', function (e) {
        e.preventDefault();
        addSetMaterialForm(collectionHolderSetMateriales);
        initSelects();
    });

    var $datoSetMaterialDeleteLink = $(".prototype-link-remove-set-material");
    updateDeleteLinks($datoSetMaterialDeleteLink);
}

/**
 *
 * @param {type} $collectionHolder
 * @returns {addSetMaterialForm}
 */
function addSetMaterialForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var datoSetMaterialForm = prototype.replace(/__componente_set_material__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-set-material').closest('.row').before(datoSetMaterialForm);

    var $datoSetMaterialDeleteLink = $(".prototype-link-remove-set-material");
    updateDeleteLinks($datoSetMaterialDeleteLink);

    initSelects();
}
