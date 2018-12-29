/* global campos, id_prefix, tipoItems, errores, addEspecial, dt_itemhojaruta_column_index, __AJAX_PATH__, indiceTipo, hojaRuta, isShow, campoDatoMaestro, transformValue, camposObligatorios */

var isEdit = $('[name=_method]').length > 0;
var itemsHash = [];
var isShow = isShow !== undefined;
var $collectionHolder = $('div.prototype-item');
var camposItemForm = campos.slice(0);
camposItemForm.push('id');

var indiceMaterialNuevo = 4;

dt_itemhojaruta = dt_datatable($('#table-item'), {
    ajax: __AJAX_PATH__ + 'hojaruta/items_table/' + indiceTipo + ((hojaRuta !== '')?'/'+hojaRuta:''),
    columnDefs: [
        {
            "targets": dt_itemhojaruta_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_itemhojaruta_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_itemhojaruta_column_index.acciones];
                return (full_data !== undefined && full_data.delete !== undefined && !isShow ?
                    '<a href="' + full_data.delete + '" class="btn btn-xs red tooltips accion-borrar prototype-link-remove-item" data-original-title="Eliminar">\n\
                        <i class="fa fa-trash"></i>\n\
                    </a>' : '');
            }
        },
        {
            className: "text-center",
            targets: [
                dt_itemhojaruta_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_itemhojaruta_column_index.acciones
        }
    ],
    "initComplete": function (settings, json) {
        updateItemDeleteLinks($(".prototype-link-remove-item"));
    }
});

$(document).ready(function () {
    initCollectionForm();
    $('#table-checkbox').bootstrapSwitch('destroy');

    $('form').submit(function(e){ //previene que se envie una hoja de ruta sin items
        var count = dt_itemhojaruta.fnSettings().fnRecordsDisplay();
        if( count <= 0 ){
            show_alert({title:'ERROR', msg:'La tabla de items no puede estar vacia'});
            e.preventDefault()
            return false;
        }else{
            $(this).submit;
        }
    });
});

function hashCode(data) {
    var hash = 0;
    if (data.length == 0) return hash;
    for (i = 0; i < data.length; i++) {
        char = data.charCodeAt(i);
        hash = ((hash<<5)-hash)+char;
        hash = hash & hash; // Convert to 32bit integer
    }
    return Math.pow(hash,2);
}

/**
 *
 * @returns {undefined}
 */
function initCollectionForm() {

    $collectionHolder.data('index', $collectionHolder.children('.row').length);

    $('.prototype-link-add-item').on('click', function (e) {
        e.preventDefault();
        addItemForm();
    });

    if(errores == 1){ //Si se recarga la pág por errores:
        $collectionHolder.children('.row').each(function(i,elem){
            addDataTableItem($(this), i); //Lo agrego al dataTable
        });
    }

    var $itemDeleteLink = $(".prototype-link-remove-item");
    updateItemDeleteLinks($itemDeleteLink);
}

/**
 *
 * @returns {addValorAtributoActivoLinealForm}
 */
function addItemForm() {

    var data = [];
    for(var i in campos){
        var $select = $('#' + id_prefix + campos[i]);
        if($select.val() !== ''){
            //Acá opcionalmente puede aplicarse una función de transformación:
            var valor = (typeof transformValue === 'function')?transformValue(campos[i]):$select.val();
            data.push({name:campos[i],value:valor});
        }
    }
    var msgError = 'Este item ya se encuentra en otra hoja de ruta activa.';

    var itemIncompleto = (campos.length !== data.length);
    var dataAux = [];
    if(itemIncompleto){ //Si el item no está completo
        if(!itemFormValido(data)){ //Se valida que se cumplan las restricciones para items incompletos
            show_alert({title:'ERROR', msg:'Debe completar los campos obligatorios.'});
            return; //Datos incompletos (mensaje se muestra en función)
        }else{
            
            for(var campo in data){
                dataAux[ data[campo]['name'] ] = data[campo]['value'];
            }

        }
        msgError = 'No se encontraron registros que cumplan los filtros seleccionados';
    }else{
        
        for(var campo in data){
            dataAux[ data[campo]['name'] ] = data[campo]['value'];
        }

        addItem(dataAux);
        return true;
    }

    var url = __AJAX_PATH__ + 'hojaruta/obtener_items/' + indiceTipo;
    $.post(url, data, function(resp) {
        if( resp.length > 0 ){ //Se obtiene un ARRAY de JSON:
            // Si indiceTipo es de de tipo material Nuevo no tiene que agregar
            if(itemIncompleto ){
                //También se agrega el item incompleto
                var dataAux1 = [];
                for(var campo in data){
                    dataAux1[ data[campo]['name'] ] = data[campo]['value'];
                }

                addItem(dataAux1);
            }
            addMultiplesItems(resp);
        }else{
            if(msgError === 'No se encontraron registros que cumplan los filtros seleccionados'
               && itemIncompleto){
                if(indiceTipo=='1'){
                    msgError = 'No se encontraron materiales producidos de obra para relevar en el Almacén seleccionado. '+
                           'Se realizará una toma de inventario inicial.';
                }else{
                    msgError = 'No se encontraron materiales nuevos para relevar en el Almacén seleccionado. '+
                           'Se realizará una toma de inventario inicial.';
                }
                
                //show_alert({title:'AVISO', msg:msgError});
                show_confirm({
                    title:'AVISO',
                    msg:msgError,
                    callbackOK: function(){
                        addItem(dataAux);
                    }
                })
            }else{
                show_alert({title:'ERROR', msg:msgError});
            }
        }
    });
}

//Funciones de Validacion:
function existeEnTabla(id, mensaje = false){
    if( $('div.prototype-item').find('[id $= "_' + campoDatoMaestro + '"][value="' + id + '"]').length !== 0 ){
        if(mensaje){ //Si mensaje = false NO muestra mensaje
            show_alert({title:'ERROR', msg:'Esta intentando ingresar un item que ya esta en la tabla'});
        }
        return true;
    }
    return false;
}

function itemFormValido(datos){
    if(typeof validacionEspecial === 'function'){
        return validacionEspecial(datos);
    }

    if(camposObligatorios.length > datos.length){
        return false;
    }

    for(var campo in camposObligatorios){
        if(datos[campo].name != camposObligatorios[campo]){
            return false;
        }
    }
    return true;
}

/**
 * Metodo para agregar item al Collection y el DataTable
 * -> Si recibe los datos del Item lo carga directamente en lugar de buscar
 * en el formulario del item
 *
 * @param {json} datosItem
 */
function addItem(datosItem = false){
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var itemForm = prototype.replace(/__item_hoja_ruta__/g, index);

    $collectionHolder.data('index', index + 1);

    $collectionHolder.append(itemForm); //Agrego un item al collectionForm

    //Cargo los datos en el collectionForm:
    $selector = (!datosItem)?$('.form-item'):false;
    if( cargarCollectionItem(itemForm, $selector, datosItem) ){
        addDataTableItem($(itemForm), index); //Agrego el item al dataTable
        updateItemDeleteLinks($(".prototype-link-remove-item"));
    }else{ //Si no pasa la validación por hash
        $collectionHolder.find("[index='" + index + "' ]").remove(); //Lo elimno del collection
    }
}

/**
 * Settea los valores del item creado en el collection
 * - Puede recibir el elemento padre del formulario del item o
 * - Directamente los datos a cargar en el nuevo item
 *
 * @param {string} itemForm
 * @param {jQuery elem} $selector
 * @param {json} datosItem
 *
 * @returns {boolean} validacion por hash
 */
function cargarCollectionItem(itemForm, $selector = false, datosItem = false ){
    var rowPlain = '';
    for(var i in camposItemForm){
        var id = camposItemForm[i];
        if($selector){ //En caso de recibir el formulario del item:
            var $selectorValue = $selector.find("select[id $= '_" + id + "'], input[id $= '_" + id + "']");
            var valor = (typeof addEspecial === 'function')?addEspecial(id, id_prefix, $selectorValue, itemForm):$selectorValue.val();
        }else{
            var valor = datosItem[id]; //Obtengo directamente el dato del json
        }
        id = (id === 'id')?campoDatoMaestro:id; //Para guardar el dato del id
        var idItem = $(itemForm).find("[id ^= '" + id_prefix + "'][id $= '_" + id + "']").attr('id');
        $("#" + idItem).val(valor); //Setteo los valores en el item del collection
        rowPlain = rowPlain + valor;
    }

    var rowHash = hashCode(rowPlain);

    if ($.inArray(rowHash, itemsHash) === -1){
        itemsHash.push(rowHash);
        return true;
    }else{
        show_alert({title:'ERROR', msg:'Esta intentando ingresar un item que ya esta en la tabla'});
        return false;
    }
}

/**
 * Agrega los items obtenidos por AJAX validando que no existan
 * en el dataTable.
 *
 * @param {array} resp
 */
function addMultiplesItems(resp){
    var alert = (resp.length > 1); //Si es más de un registro no muestro el error

    for(var i in resp){
        var id = resp[i]['id']; //Se obtiene el id del dato Maestro (AL, MN, MPO, MR)
        if(!existeEnTabla(id, alert)){
            addItem(resp[i]);
        }
    }
}

/**
 * Función para agregar items al data Table
 * Se ejecuta:
 * - Por cada item en el collection al recargar la página luego de un error
 * de validación: Agrega el item en el datatable
 * - Al pulsar el btn agregar: Agrega el item a la DataTable y además
 *  crea un item en el collection (que son los items que se guardarán realmente
 *  en la bd). En este caso además se ejecuta la función addEspecial (si está
 *  implementada en el new.js) para ejecutar acciones específicas de ese tipo
 *  de hoja de ruta.
 *
 * @param {jQuery elem} $selector
 * @param {int} index
 *
 */
function addDataTableItem($selector, index){
    var dataTable = $('#table-item').DataTable();
    var row = []; //Array para cargar el dataTable

    row.push(index);
    row.push(index);

    //Itero los campos del item
    for(var i in campos){
        var id = campos[i];
        var idSelectorValue = $selector.find("[id $= '_" + id + "']").attr('id');
        var value = $('#' + idSelectorValue).attr('value');
        if($('#' + idSelectorValue).prop("tagName") === "SELECT" ){
           value = (value !== '')?$('#' + idSelectorValue).find('option:selected').text():'-';
        }
        row.push(value); //Cargo el array con los valores para la tabla
    }
    row.push({"delete": index}); //Agrego el btn para eliminar
    dataTable.row.add(row).draw(true); //Agrego el nuevo item al dataTable

}

function updateItemDeleteLinks(deleteLink) {

    deleteLink.each(function () {

        $(this).tooltip();
        if (typeof $(this).attr('hashRef') === 'undefined'){
            $(this).attr('hashRef', itemsHash[itemsHash.length-1]);
        }

        $(this).off("click").on('click', function (e) {
            e.preventDefault();
            var dataTable = $('#table-item').DataTable();
            var index = $(this).attr('href');
            var itemHash = $(this).attr('hashref');

            var deletableRow = $('div.prototype-item').find('[index="' + id_prefix + tipoItems + index + '" ]');
            var deletableRowTable = $(this).parents('tr');

            show_confirm({
                msg: '¿Desea eliminar el registro?',
                callbackOK: function () {
                    deletableRowTable.hide('slow', function () {
                        deletableRow.remove();
                        dataTable.row( deletableRowTable ).remove().draw();
                    });
                    itemsHash = jQuery.grep(itemsHash, function(value) {
                        return value != itemHash;
                    });
                }
            });

            e.stopPropagation();

        });
    });
}

function dtValidator(row){

}
