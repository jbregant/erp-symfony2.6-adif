
var collectionHolderArchivos;

/**
 * 
 * @returns {undefined}
 */
function initArchivosForm() {

    collectionHolderArchivos = $('div.prototype-archivos');

    collectionHolderArchivos.data('index', collectionHolderArchivos.find(':input').length);

    $('.prototype-link-add-archivo').on('click', function (e) {

        e.preventDefault();

        addArchivoForm(collectionHolderArchivos);

        initFileInput();
    });
}


/**
 * 
 * @param {type} $collectionHolder
 * @returns {addArchivoForm}
 */
function addArchivoForm($collectionHolder) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var archivoForm = prototype.replace(/__adjunto__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-archivo').closest('.row').before(archivoForm);

    var $archivoDeleteLink = $(".prototype-link-remove-archivo");

    updateDeleteLinks($archivoDeleteLink);
}