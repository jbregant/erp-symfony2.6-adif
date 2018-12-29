
var $selectCondicionIVA = $('select[id ^="adif_comprasbundle"][id $="condicionIVA"]');

var $selectCondicionGanancias = $('select[id ^="adif_comprasbundle"][id $="condicionGanancias"]');

var $selectCondicionIngresosBrutos = $('select[id ^="adif_comprasbundle"][id $="condicionIngresosBrutos"]');

var $inputPorcentajeCABA = $('input[id ^="adif_comprasbundle"][id $="convenioMultilateralIngresosBrutos_porcentajeAplicacionCABA"]');

var $selectTipoPago = $('select[id ^="adif_comprasbundle"][id $="tipoPago"]');

var $selectBanco = $('select[id ^="adif_comprasbundle"][id $="cuenta_idBanco"]');

var $selectTipoCuenta = $('select[id ^="adif_comprasbundle"][id $="cuenta_idTipoCuenta"]');

var $inputCBU = $('input[id ^="adif_comprasbundle"][id $="cuenta_cbu"]');

var $domicilioComercialCalle = $('input[id ^= "adif_comprasbundle_"][id $= "_domicilioComercial_calle"]');
var $domicilioComercialNumero = $('input[id ^= "adif_comprasbundle_"][id $= "_domicilioComercial_numero"]');
var $domicilioComercialPiso = $('input[id ^= "adif_comprasbundle_"][id $= "_domicilioComercial_piso"]');
var $domicilioComercialDepto = $('input[id ^= "adif_comprasbundle_"][id $= "_domicilioComercial_depto"]');
var $domicilioComercialProvincia = $('select[id ^= "adif_comprasbundle_"][id $= "_domicilioComercial_idProvincia"]');
var $domicilioComercialLocalidad = $('select[id ^= "adif_comprasbundle_"][id $= "_domicilioComercial_localidad"]');

var $domicilioLegalCalle = $('input[id ^= "adif_comprasbundle_"][id $= "_domicilioLegal_calle"]');
var $domicilioLegalNumero = $('input[id ^= "adif_comprasbundle_"][id $= "_domicilioLegal_numero"]');
var $domicilioLegalPiso = $('input[id ^= "adif_comprasbundle_"][id $= "_domicilioLegal_piso"]');
var $domicilioLegalDepto = $('input[id ^= "adif_comprasbundle_"][id $= "_domicilioLegal_depto"]');
var $domicilioLegalProvincia = $('select[id ^= "adif_comprasbundle_"][id $= "_domicilioLegal_idProvincia"]');
var $domicilioLegalLocalidad = $('select[id ^= "adif_comprasbundle_"][id $= "_domicilioLegal_localidad"]');

var collectionHolderArchivosClienteProveedor;

/**
 * 
 * @returns {undefined}
 */
function  initCondicionResponsableMonotributoHandler() {
    var actualizarCondicion = function ($changedSelect, $otherSelect, condicion) {
        if (condicion === responsableMonotributo) {
            $otherSelect.val($changedSelect.val()).select2();
        }
    };

    $selectCondicionIVA.change(function () {
        actualizarCondicion($selectCondicionIVA, $selectCondicionGanancias, $(this).find('option:selected').text());
    });
    $selectCondicionGanancias.change(function () {
        actualizarCondicion($selectCondicionGanancias, $selectCondicionIVA, $(this).find('option:selected').text());
    });
}

/**
 * 
 * @returns {undefined}  */
function initConvenioMultilateralHandler() {
    var validacionCondicion = function (condicion) {
        if (condicion === convenioMultilateral) {
            $('.convenio-multilateral-data').show(500);
            $inputPorcentajeCABA.rules('add', {
                required: true
            });
        } else {
            $('.convenio-multilateral-data').hide(500);
            $inputPorcentajeCABA.rules("remove");
        }
    };

    validacionCondicion($selectCondicionIngresosBrutos.find('option:selected').text());

    $selectCondicionIngresosBrutos.change(function () {
        var condicion = $(this).find('option:selected').text();
        validacionCondicion(condicion);
    });
}


/**
 * 
 * @returns {undefined}
 */
function initDatosComercialesHandler() {
    var validacionCondicion = function (tipoPago) {
        if (tipoPago === tipoPagoTransferenciaBancaria || tipoPago === tipoPagoDomiciliacionBancaria) {
            $('.row-datos-bancarios').show(500);

            $selectBanco.rules('add', {
                required: true
            });

            $selectTipoCuenta.rules('add', {
                required: true
            });

            $inputCBU.rules('add', {
                required: true
            });
        } else {
            $('.row-datos-bancarios').hide(500);
            $selectBanco.rules("remove");
            $selectTipoCuenta.rules("remove");
            $inputCBU.rules("remove");
        }
    };

    validacionCondicion($selectTipoPago.find('option:selected').text());

    $selectTipoPago.change(function () {
        var tipoPago = $(this).find('option:selected').text();
        validacionCondicion(tipoPago);
    });
}

/**
 * 
 * @returns {undefined}
 */
function initArchivosClienteProveedorForm() {
    collectionHolderArchivosClienteProveedor = $('div.prototype-archivos-clienteproveedor');
    collectionHolderArchivosClienteProveedor.data('index', collectionHolderArchivosClienteProveedor.find(':input').length);

    $('.prototype-link-add-archivo-clienteproveedor').on('click', function (e) {
        e.preventDefault();
        addArchivosClienteProveedorForm(collectionHolderArchivosClienteProveedor);
        initFileInput();
    });
}


/**
 * 
 * @param {type} $collectionHolder
 * @returns {addArchivosClienteProveedorForm}
 */
function addArchivosClienteProveedorForm($collectionHolder) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var archivosClienteProveedorForm = prototype.replace(/__adjunto__/g, index);

    $collectionHolder.data('index', index + 1);

    $('.prototype-link-add-archivo-clienteproveedor').closest('.row').before(archivosClienteProveedorForm);

    var $archivosClienteProveedorDeleteLink = $(".prototype-link-remove-archivo");

    updateDeleteLinks($archivosClienteProveedorDeleteLink);
}


/**
 * 
 * @param {type} deleteLink
 * @returns {undefined}
 */
function updateDeleteLinks(deleteLink) {
    deleteLink.each(function () {
        $(this).tooltip();
        $(this).off("click").on('click', function (e) {
            e.preventDefault();
            var deletableRow = $(this).closest('.row');
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
function initChainedDomicilios() {
    $domicilioComercialCalle.keyup(function () {
        $domicilioLegalCalle.val(this.value);
    });

    $domicilioComercialNumero.keyup(function () {
        $domicilioLegalNumero.val(this.value);
    });
    $domicilioComercialPiso.keyup(function () {
        $domicilioLegalPiso.val(this.value);
    });

    $domicilioComercialDepto.keyup(function () {
        $domicilioLegalDepto.val(this.value);
    });

    $domicilioComercialProvincia.change(function () {
        $domicilioLegalProvincia.val(this.value).select2().change();
    });

    $domicilioComercialLocalidad.change(function () {
        $domicilioLegalLocalidad.val(this.value).select2().change();
    });
}