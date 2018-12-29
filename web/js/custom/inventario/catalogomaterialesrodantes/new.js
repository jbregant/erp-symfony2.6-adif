
/* global __AJAX_PATH__ */
var isEdit = $('[name=_method]').length > 0;
var collectionHolderFotosCatalogoMaterialesRodantes;
var collectionHolderAllPropiedadesCMPO;
if(isEdit){
    collectionHolderAllPropiedadesCMPO = $('select.propiedad:first > option').clone();
}
/**
 *
 */
jQuery(document).ready(function () {

    initSelects();
    //initChainedSelects();

    //Init Fotos
    updateDeletePhoto($(".prototype-link-remove-foto"));
    initFileInput();
    initFotosCatalogoMaterialesRodantesForm();

    //Init Propiedades
    initValoresPropiedadMaterialRodanteForm();
    initFiltroValoresPropiedad();
    updateSelectItems();

    //Init Estaciones
    initFiltroValoresEstacion();

    //Init Estado
    initFiltroValidarEstado();
    initFiltroGrupoRodante();

    //datepicker range
    $('#adif_inventariobundle_catalogomaterialesrodantes_fechaAdquisicion').datepicker('setEndDate', 'now');

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
 *
function initChainedSelects() {
    var id_prefix = "adif_inventariobundle_catalogomaterialesrodantes_";
    var selects = { //Defino dependencias:
        estacion:['idEstacion']
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
}
*/


/**
 *
 * @returns {undefined}
 */
function initValoresPropiedadMaterialRodanteForm() {

    collectionHolderValoresPropiedad = $('div.prototype-valor-propiedad-material-rodante');

    collectionHolderValoresPropiedad.data('index', collectionHolderValoresPropiedad.find(':input').length);

    $('.prototype-link-add-valor-propiedad-material-rodante').on('click', function (e) {
        e.preventDefault();
        addValorPropiedadMaterialRodanteForm(collectionHolderValoresPropiedad);
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

    updatePropiedadesDeleteLinks($(".prototype-link-remove-valor-propiedad-material-rodante"));
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
 * @returns {undefined}
 */
function initFiltroValoresEstacion() {

  var $selectLinea = $('#adif_inventariobundle_catalogomaterialesrodantes_idLinea');
  var $selectEstacion = $('#adif_inventariobundle_catalogomaterialesrodantes_idEstacion');

  $selectLinea.change(function () {

        if (isEdit) {
          var $estacionVal = $selectEstacion.val();
        }

        if ($(this).val()) {

            var data = { id: $(this).val() };
            resetSelect($selectEstacion);

            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'estacion/lista_por_linea',
                data: data,
                success: function (data) {


                    // Si se encontraron al menos un ramal
                    if (data.length > 0) {
                        $selectEstacion.select2('readonly', false);

                        for (var i = 0, total = data.length; i < total; i++) {
                            $selectEstacion.append('<option value="' + data[i].id + '">' + data[i].denominacion + '</option>');
                        }

                        if (isEdit) {
                            $selectEstacion.val($estacionVal);

                            if (null === $selectEstacion.val()) {
                                $selectEstacion.select2("val", "");
                            }
                        }
                        else {
                            $selectEstacion.val($selectEstacion.find('option:first').val());
                        }

                        $selectEstacion.prop('required', true);
                        $selectEstacion.select2();
                    }
                }
            });
        }
    }).trigger('change');
}



/**
 *
 * @returns {undefined}
 */
function initFiltroValidarEstado() {

  //var $selectMarca = $('#adif_inventariobundle_catalogomaterialesrodantes_idMarca');
  //var $selectModelo = $('#adif_inventariobundle_catalogomaterialesrodantes_idModelo');
  var $selectOperador = $('#adif_inventariobundle_catalogomaterialesrodantes_idOperador');

  /*$selectMarca.change(function () {

    if ($(this).val()){
      if($selectModelo.val() != "" && $selectOperador.val() != ""){
        $('#idEstadoInventario').empty()
        $('#idEstadoInventario').append("Activo");
      }
    }
    else{
      $('#idEstadoInventario').empty()
      $('#idEstadoInventario').append("Borrador");
    }

  }).trigger('change');

  $selectModelo.change(function () {

    if ($(this).val()){
      if($selectMarca.val() != "" && $selectOperador.val() != ""){
        $('#idEstadoInventario').empty()
        $('#idEstadoInventario').append("Activo");
      }
    }
    else{
      $('#idEstadoInventario').empty()
      $('#idEstadoInventario').append("Borrador");
    }

  }).trigger('change');*/

  $selectOperador.change(function () {

    if ($(this).val()!= ""){
      //if($selectMarca.val() != "" && $selectModelo.val() != ""){
        $('#idEstadoInventario').empty()
        $('#idEstadoInventario').append("Activo");
      //}
    }
    else{
      $('#idEstadoInventario').empty()
      $('#idEstadoInventario').append("Borrador");
    }

  }).trigger('change');

}

/**
 *
 * @param {type} $collectionHolder
 * @returns {addValorPropiedadMaterialRodanteForm}
 */
function addValorPropiedadMaterialRodanteForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var valorPropiedadMaterialRodanteForm = prototype.replace(/__propiedad_material_rodante__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-valor-propiedad-material-rodante').closest('.row').before(valorPropiedadMaterialRodanteForm);

    var $valorPropiedadMaterialRodanteDeleteLink = $(".prototype-link-remove-valor-propiedad-material-rodante");
    updatePropiedadesDeleteLinks($valorPropiedadMaterialRodanteDeleteLink);
    //updateDeleteLinks($valorPropiedadMaterialRodanteDeleteLink);

    initSelects();
    updateSelectItems();
}



/**
 *
 * @returns {undefined}
 */
function initFotosCatalogoMaterialesRodantesForm() {
    collectionHolderFotosCatalogoMaterialesRodantes = $('div.prototype-fotos-CatalogoMaterialesRodantes');
    collectionHolderFotosCatalogoMaterialesRodantes.data('index', collectionHolderFotosCatalogoMaterialesRodantes.find(':input').length);

    $('.prototype-link-add-fotos-CatalogoMaterialesRodantes').on('click', function (e) {
        e.preventDefault();
        addFotosCatalogoMaterialesRodantesForm(collectionHolderFotosCatalogoMaterialesRodantes);
        initFileInput();
    });
}


/**
 *
 * @returns Array
 */
function listaPropiedadesSelecionadas(){
    var list = [];
    var selectPropiedad = $('[id ^= adif_inventariobundle_catalogomaterialesrodantes_valoresPropiedad_][id $= _idPropiedad]').find('option:selected');
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
 * @param {type} $collectionHolder
 * @returns {addFotosCatalogoMaterialesRodantesForm}
 */
function addFotosCatalogoMaterialesRodantesForm($collectionHolder) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var CatalogoMaterialesRodantesForm = prototype.replace(/__fotos__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-fotos-CatalogoMaterialesRodantes').closest('.row').before(CatalogoMaterialesRodantesForm);

    var $fotosCatalogoMaterialesRodantes = $(".prototype-link-remove-foto");

    updateDeletePhoto($fotosCatalogoMaterialesRodantes);
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
function initFiltroGrupoRodante() {

  var $selectGrupo = $('#grupo_rodante');
  var $selectTipoRodante = $('#adif_inventariobundle_catalogomaterialesrodantes_idTipoRodante');

  if ($(this).find('option:selected').text() == "Vagón"){
    $('#cod_trafico').prop('disabled', false);
  }
  else{
    $('#cod_trafico').prop('disabled', true);
  }

  $selectGrupo.change(function () {

    if (isEdit) {
      var $tipoRodanteVal = $selectTipoRodante.val();
    }

    if ($(this).val()) {

        var data = { id: $(this).val() };

        resetSelect($selectTipoRodante);

        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'tiporodante/lista_por_grupo',
            data: data,
            success: function (data) {

                // Si se encontraron al menos un ramal
                if (data.length > 0) {
                    $selectTipoRodante.select2('readonly', false);

                    for (var i = 0, total = data.length; i < total; i++) {
                        $selectTipoRodante.append('<option value="' + data[i].id + '">' + data[i].denominacion + '</option>');
                    }

                    if (isEdit) {
                        $selectTipoRodante.val($tipoRodanteVal);

                        if (null === $selectTipoRodante.val()) {
                            $selectTipoRodante.select2("val", "");
                        }
                    }
                    else {
                        $selectTipoRodante.val($selectTipoRodante.find('option:first').val());
                    }

                    $selectTipoRodante.prop('required', true);
                    $selectTipoRodante.select2();
                }
            }
        });
    }

    if ($(this).find('option:selected').text() == "Vagón"){
      $('#cod_trafico').prop('disabled', false);
    }
    else{
      $('#cod_trafico').prop('disabled', true);
      $('#cod_trafico').prop('value', null);
    }

  }).trigger('change');

}
