
var dt_orden_compra;

var renglon_actual;

var $formularioComprobanteServicio = $('form[name=adif_contablebundle_comprobantecompra]');

var $letraComprobanteSelect = $('#adif_contablebundle_comprobantecompra_letraComprobante');

$(document).ready(function () {

    $('#adif_contablebundle_comprobantecompra_proveedor').autocomplete({
        source: __AJAX_PATH__ + 'proveedor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {

            autocompletarProveedor(event, ui);

            updateLetraComprobanteSelect();

            checkFechaVencimientoCai();
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };

    $('#agregar_renglon_comprobante').on('click', function (e) {
        crear_renglon_comprobante(null, null, null, null, null, null, null);
        restringir_iva();
        recalcular_netos();
    });

    $(document).on('click', '.renglon_comprobante_centro_de_costo', function (e) {
        e.preventDefault();

        renglon_actual = $(this);

        show_dialog({
            titulo: 'Asignar centros de costo al renglón',
            contenido: '<form id="form_centros_de_costo" method="post" name="form_centros_de_costo">\n\
                            <div class="ctn_rows_renglon_comprobante_centro_de_costo_pop_up">\n\
                                <div class="row">\n\
                                    <div class="col-md-8"><label class="control-label">Centro de costo</label></div>\n\
                                    <div class="col-md-3"><label class="control-label">Porcentaje</label></div>\n\
                                </div>\n\
                            </div>\n\
                            <div class="row">\n\
                                <div class="col-md-1">\n\
                                    <div class="form-group">\n\
                                        <button class="btn btn-sm green renglon_comprobante_centro_costo_agregar">\n\
                                            <i class="fa fa-plus"></i>\n\
                                        </button>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                        </form>\n\
            ',
            callbackCancel: function () {
                return;
            },
            callbackSuccess: function () {
                var formulario = $('form#form_centros_de_costo').validate();
                var formulario_result = formulario.form();
                if (formulario_result && validatePorcentajeCentrosCosto()) {
                    guardarCentrosDeCosto();
                    return;
                } else {
                    return false;
                }
            }
        });


        $('.modal-dialog').css('width', '60%');

        var indice_renglon = $(renglon_actual).parents('.row_renglon_comprobante').attr('indice');

        if (!($('.row_renglon_comprobante[indice=' + indice_renglon + ']').find('.row_centros_de_costo_renglon').html().trim() == "")) {
            $('.ctn_rows_renglon_comprobante_centro_de_costo_pop_up')
                    .html(
                            $('.row_renglon_comprobante[indice=' + indice_renglon + ']')
                            .find('.ctn_rows_renglon_comprobante_centro_de_costo')
                            .clone()
                            .show()
                            .children()
                            );
            $('.ctn_rows_renglon_comprobante_centro_de_costo_pop_up').find('select').each(function () {
                $(this).select2();
            });
            initFormCentroDeCosto();
        }
    });

    initFechaComprobanteValidation();

    initPuntoVentaHandler();

    initSubmitButton();

});

/**
 * 
 * @returns {undefined}
 */
function crear_renglon_comprobante_centro_de_costo() {

    var indice_renglon = $(renglon_actual).parents('.row_renglon_comprobante').attr('indice');
    var nuevo_row = $('.row_renglon_comprobante_centro_de_costo_nuevo')
            .clone()
            .show()
            .removeClass('row_renglon_comprobante_centro_de_costo_nuevo');
    nuevo_row.addClass('row_renglon_comprobante_centro_de_costo');
    nuevo_row.find('.ignore').removeClass('ignore');
    nuevo_row.find('[sname]').each(function () {
        $(this).attr('name', $(this).attr('sname'));
        $(this).removeAttr('sname');
    });
    var maximo_indice = 0;
    $('.ctn_rows_renglon_comprobante_centro_de_costo_pop_up').find('.row_renglon_comprobante_centro_de_costo').each(function () {
        var value = parseFloat($(this).attr('indice'));
        maximo_indice = (value > maximo_indice) ? value : maximo_indice;
    });
    var indice_nuevo = maximo_indice + 1;
    var nth = 0;
    nuevo_row.html(nuevo_row.html().replace(/__name__/g, function (match, i, original) {
        nth++;
        return ((nth % 2) == 0) ? indice_nuevo : match;
    }));
    nuevo_row.html(nuevo_row.html().replace(/__name__/g, indice_renglon));
    nuevo_row.attr('indice', indice_nuevo);
    nuevo_row.attr('renglon', indice_renglon);
    nuevo_row.find('select').select2();
    $('.bootbox').removeAttr('tabindex');
    initCurrencies();
    nuevo_row.appendTo('.ctn_rows_renglon_comprobante_centro_de_costo_pop_up');
}

function guardarCentrosDeCosto() {

    var indice_renglon = $(renglon_actual).parents('.row_renglon_comprobante').attr('indice');
    var renglon = $('.row_renglon_comprobante[indice=' + indice_renglon + ']');
    $('.ctn_rows_renglon_comprobante_centro_de_costo_pop_up').find('select').each(function () {
        $(this).find('option[value=' + $(this).val() + ']').attr('selected', 'selected');
        $(this).select2("destroy");
    });
    renglon.find('.row_centros_de_costo_renglon').html($('.ctn_rows_renglon_comprobante_centro_de_costo_pop_up').clone()
            .addClass('ctn_rows_renglon_comprobante_centro_de_costo')
            .removeClass('ctn_rows_renglon_comprobante_centro_de_costo_pop_up'));
}

$(document).on('click', '.renglon_comprobante_centro_costo_agregar', function (e) {
    crear_renglon_comprobante_centro_de_costo();
    initFormCentroDeCosto();
});

$(document).on('click', '.renglon_comprobante_centro_costo_borrar', function (e) {
    $(this).parents('.row_renglon_comprobante_centro_de_costo').remove();
});

/**
 * 
 * @param {type} id_renglon_oc
 * @param {type} descripcion
 * @param {type} bien_economico
 * @param {type} cantidad
 * @param {type} precio_unitario
 * @param {type} monto_neto
 * @param {type} id_alicuota_iva
 * @param {type} monto_iva
 * @param {type} renglones_centros_de_costo
 * @returns {undefined}
 */
function crear_renglon_comprobante(id_renglon_oc, descripcion, bien_economico, cantidad, precio_unitario, monto_neto, id_alicuota_iva, monto_iva, renglones_centros_de_costo) {

    var nuevo_row =
            $('.row_renglon_comprobante_nuevo')
            .clone()
            .show()
            .removeClass('row_renglon_comprobante_nuevo');
    nuevo_row.addClass('row_renglon_comprobante');
    nuevo_row.find('.ignore').removeClass('ignore');
    nuevo_row.find('[sname]').each(function () {
        $(this).attr('name', $(this).attr('sname'));
        $(this).removeAttr('sname');
    });
    var maximo_indice = 0;
    $('.row_renglon_comprobante').each(function () {
        var value = parseFloat($(this).attr('indice'));
        maximo_indice = (value > maximo_indice) ? value : maximo_indice;
    });
    var indice_nuevo = maximo_indice + 1;
    nuevo_row.html(nuevo_row.html().replace(/__name__/g, indice_nuevo));
    nuevo_row.attr('indice', indice_nuevo);
    nuevo_row.appendTo('.ctn_rows_renglon_comprobante');
    var row_sel_prefix = '#adif_contablebundle_comprobantecompra_renglonesComprobante_';
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_idRenglonOrdenCompra').val(id_renglon_oc);
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_descripcion').val(descripcion ? descripcion : '');
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_cantidad').val(cantidad ? cantidad : 0);
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_precioUnitario').val(precio_unitario ? precio_unitario : 0);
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_neto').val(monto_neto ? monto_neto : 0);
    nuevo_row.find(row_sel_prefix + indice_nuevo + '_montoIva').val(monto_iva ? monto_iva : 0);

    nuevo_row.find('select').select2();
    if (id_alicuota_iva) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_alicuotaIva').select2('val', id_alicuota_iva);
    }
    if (bien_economico) {
        nuevo_row.find(row_sel_prefix + indice_nuevo + '_bienEconomico').select2('val', bien_economico);
    }
    $('.row_headers_renglon_comprobante').show();
    $('.row_footer_renglon_comprobante').show();
    initCurrencies();

    if (renglones_centros_de_costo) {
        indice_nuevo_costo = 0;

        nuevo_row.find('.row_centros_de_costo_renglon').append('<div class="ctn_rows_renglon_comprobante_centro_de_costo">\n\
                                                                        <div class="row">\n\
                                                                            <div class="col-md-8"><label class="control-label">Centro de costo</label></div>\n\
                                                                            <div class="col-md-3"><label class="control-label">Porcentaje</label></div>\n\
                                                                        </div>\n\
                                                                    </div>');

        $.each(renglones_centros_de_costo, function (index, renglonCentro) {
            indice_nuevo_costo++;
            var nuevo_row_costo = $('.row_renglon_comprobante_centro_de_costo_nuevo')
                    .clone()
                    .show()
                    .removeClass('row_renglon_comprobante_centro_de_costo_nuevo');
            nuevo_row_costo.addClass('row_renglon_comprobante_centro_de_costo');
            nuevo_row_costo.find('.ignore').removeClass('ignore');
            nuevo_row_costo.find('[sname]').each(function () {
                $(this).attr('name', $(this).attr('sname'));
                $(this).removeAttr('sname');
            });
            var nth = 0;
            nuevo_row_costo.html(nuevo_row_costo.html().replace(/__name__/g, function (match, i, original) {
                nth++;
                return ((nth % 2) == 0) ? indice_nuevo_costo : match;
            }));
            nuevo_row_costo.html(nuevo_row_costo.html().replace(/__name__/g, indice_nuevo));
            nuevo_row_costo.attr('indice', indice_nuevo_costo);
            nuevo_row_costo.attr('renglon', indice_nuevo);
            nuevo_row_costo.find('select').select2();
            $('.bootbox').removeAttr('tabindex');
            initCurrencies();

            nuevo_row_costo.find('input').val(renglonCentro.porcentaje);
            nuevo_row_costo.find('select').select2('val', renglonCentro.idCentroDeCosto);

            nuevo_row_costo.find('select').each(function () {
                $(this).find('option[value=' + $(this).val() + ']').attr('selected', 'selected');
                $(this).select2("destroy");
            });

            nuevo_row.find('.ctn_rows_renglon_comprobante_centro_de_costo').append(nuevo_row_costo);

        });
    }

}

/**
 * 
 * @returns {undefined}
 */
function validatePorcentajeCentrosCosto() {

    valido = true;
    total = 0;
    $('.ctn_rows_renglon_comprobante_centro_de_costo_pop_up .porcentajeCentroDeCosto').each(function () {

        if ($(this).val() != "") {
            total += parseFloat(($(this).val()).replace(",", "."));
        }
        else {
            valido &= false;
        }

    });
    if ((valido) && !(total == 100)) {

        valido = false;

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Los porcentajes de los centros de costo deben sumar 100%."
        });

        show_alert(options);

    }

    return valido;
}

/**
 * 
 * @returns {undefined}
 */
function initFormCentroDeCosto() {

    $('form#form_centros_de_costo').validate();
    $('.ctn_rows_renglon_comprobante_centro_de_costo_pop_up .porcentajeCentroDeCosto').each(function () {
        $(this).rules("add", {
            required: true}
        );
    });
}

/**
 * 
 * @returns {valido|Boolean}
 */
function validateFormServicios() {

    valido = true;

    if ($('.row_renglon_comprobante').size() == 0) {

        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "El comprobante debe tener al menos un renglón."
        });

        show_alert(options);

        return false;

    }

    $('.row_renglon_comprobante').each(function () {
        valido &= ($(this).find('.row_renglon_comprobante_centro_de_costo').size() > 0);
    });


    if (!(valido)) {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe asignar centros de costo a todos los renglones."
        });

        show_alert(options);

    }

    return valido;

}

/**
 * 
 * @param {type} event
 * @param {type} ui
 * @returns {undefined}
 */
function autocompletarProveedor(event, ui) {

    $('#adif_contablebundle_comprobantecompra_proveedor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_comprobantecompra_proveedor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_comprobantecompra_idProveedor').val(ui.item.id);

    caisProveedor = ui.item.cais;
}

/**
 * 
 * @returns {undefined}
 */
function initFechaComprobanteValidation() {

    var currentDate = getCurrentDate();

    $('#adif_contablebundle_comprobantecompra_fechaComprobante')
            .datepicker('setEndDate', currentDate);
}


/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_comprobantecompra_submit').on('click', function (e) {

        if ($formularioComprobanteServicio.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el comprobante?',
                callbackOK: function () {

                    if (validateFormServicios()) {
                        $formularioComprobanteServicio.submit();
                    }
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateLetraComprobanteSelect() {

    resetSelect($letraComprobanteSelect);

    var data = {
        idProveedor: $('#adif_contablebundle_comprobantecompra_idProveedor').val()
    };

    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'proveedor/letras_comprobante',
        data: data
    }).done(function (letrasComprobante) {

        for (var i = 0, total = letrasComprobante.length; i < total; i++) {
            $letraComprobanteSelect.append('<option value="' + letrasComprobante[i].id + '">' + letrasComprobante[i].letra + '</option>');
        }

        $letraComprobanteSelect.select2('val', $letraComprobanteSelect.find('option:first').val());

        $letraComprobanteSelect.select2('readonly', false);
    });
}