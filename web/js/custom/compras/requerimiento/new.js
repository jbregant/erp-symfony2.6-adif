
var isEdit = $('[name=_method]').length > 0;

var oTableRenglonSolicitud = $('#table-renglonsolicitudcompra');

var oTableRenglonRequerimiento = $('#table-renglon-requerimiento');

var tiposContrataciones;

var nEditing = null;

var cantidadesOriginales = [];


/**
 * 
 */
jQuery(document).ready(function () {

    // Validacion del Formulario
    $('form[name=adif_comprasbundle_requerimiento]').validate();

    initTables();

    configSubmitButtons();

    initTiposContrataciones();
});

/**
 * 
 * @returns {undefined}
 */
function initTables() {

    var optionsRenglonSolicitud = {
        "searching": true,
        "ordering": true,
        "info": true,
        "paging": true,
        "pageLength": 15,
        "lengthMenu": [[15, 30, 50, 100, -1], [15, 30, 50, 100, "Todos"]],
        "drawCallback": function () {

            setMasks();

            initAgregarRenglonRequerimientoLink();

            initTooltip();
        }
    };

    dt_init(oTableRenglonSolicitud, optionsRenglonSolicitud);


    var optionsRenglonRequerimiento = {
        "searching": true,
        "ordering": true,
        "info": true,
        "paging": true,
        "pageLength": 15,
        "lengthMenu": [[15, 30, 50, 100, -1], [15, 30, 50, 100, "Todos"]],
        "drawCallback": function () {

            setMasks();

            updateJustiprecioTotalRequerimiento();

            initCantidadesOriginales();

            initEditarRenglonRequerimientoLink();

            initEliminarRenglonRequerimientoLink();

            initTooltip();
        }
    };

    dt_init(oTableRenglonRequerimiento, optionsRenglonRequerimiento);

}

/**
 * 
 * @returns {undefined}
 */
function configSubmitButtons() {

    // Handler para el boton "Guardar Borrador"
    $('#adif_comprasbundle_requerimiento_save').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_requerimiento]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el requerimiento como borrador?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'save'};

                        $('form[name=adif_comprasbundle_requerimiento]').addHiddenInputData(json);
                        $('form[name=adif_comprasbundle_requerimiento]').submit();
                    }
                }
            });

            e.stopPropagation();

            return false;
        }

        $('.has-error').first().find('input').focus();

        return false;
    });

    // Handler para el boton "Finalizar Requerimiento"
    $('#adif_comprasbundle_requerimiento_close').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_requerimiento]').valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea finalizar el requerimiento?',
                callbackOK: function () {

                    if (validForm()) {

                        var json = {accion: 'close'};

                        $('form[name=adif_comprasbundle_requerimiento]').addHiddenInputData(json);
                        $('form[name=adif_comprasbundle_requerimiento]').submit();
                    }
                }
            });

            e.stopPropagation();

            return false;
        }

        $('.has-error').first().find('input').focus();

        return false;
    });
}

/**
 * 
 * @returns {Boolean}
 */
function validForm() {

    var formularioValido = true;

    var oTable = oTableRenglonRequerimiento.dataTable();

    // Si el Requerimiento tiene renglones cargados
    if (oTable.fnSettings().aoData.length > 0) {

        $justiprecioTotalRequerimiento = $('#adif_comprasbundle_requerimiento_justiprecio').val();

        // Si el justiprecio total es distinto de cero
        if (clearCurrencyValue($justiprecioTotalRequerimiento) != 0) {

            var json = {renglones: []};

            var renglonRequerimientodNodes = oTable.fnGetNodes();

            // Por cada NODO en la tabla de RenglonRequerimiento
            jQuery.each(renglonRequerimientodNodes, function (key, row) {

                var dataRenglonRequerimiento = oTableRenglonRequerimiento.dataTable().fnGetData(row);

                json.renglones.push({
                    'id': clearFormatId(dataRenglonRequerimiento[2]),
                    'cantidad': dataRenglonRequerimiento[6],
                    'justiprecio': clearCurrencyValue(dataRenglonRequerimiento[8])
                });

            });

            // Validacion de la fecha del Requerimiento
            $fechaRequerimiento = $('#adif_comprasbundle_requerimiento_fechaRequerimiento').val();

            $.ajax({
                type: 'post',
                async: false,
                url: __AJAX_PATH__ + 'requerimiento/validar_fecha',
                data: {
                    renglones: json.renglones
                },
                success: function (fechaSolicitud) {

                    var fechaRequerimientoSplited = $fechaRequerimiento.split("/");
                    var fechaRequerimientoDate = new Date(fechaRequerimientoSplited[2], fechaRequerimientoSplited[1] - 1, fechaRequerimientoSplited[0]);

                    var fechaSolicitudSplited = $fechaRequerimiento.split("/");
                    var fechaSolicitudDate = new Date(fechaSolicitudSplited[2], fechaSolicitudSplited[1] - 1, fechaSolicitudSplited[0]);

                    if (fechaRequerimientoDate >= fechaSolicitudDate) {

                        // Agrega JSON data antes del submit
                        $('form[name=adif_comprasbundle_requerimiento]').addHiddenInputData(json);
                    }
                    else {
                        var options = $.extend({
                            title: 'Ha ocurrido un error',
                            msg: "La fecha del requerimiento debe ser posterior a la fecha " + fechaSolicitud + "."
                        });

                        show_alert(options);

                        formularioValido = false;
                    }
                }
            });
        }
        else {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "El justiprecio total del requerimiento debe ser mayor a cero."
            });

            show_alert(options);

            formularioValido = false;
        }
    }
    else {
        var options = $.extend({
            title: 'Ha ocurrido un error',
            msg: "Debe cargar al menos un renglón al requerimiento."
        });

        show_alert(options);

        formularioValido = false;
    }

    return formularioValido;
}

/**
 * 
 * @returns {undefined}
 */
function initAgregarRenglonRequerimientoLink() {

// Agregar RenglonSolicitud a Requerimiento
    $(document).off('click', '.agregar_renglon_requerimiento').on('click', '.agregar_renglon_requerimiento', function (e) {

        e.preventDefault();

        // Obtengo el TR del RenglonSolicitud clickeado
        var tr_renglonSolicitud = $(this).parents('tr');

        var data = oTableRenglonSolicitud.dataTable().fnGetData(tr_renglonSolicitud[0]);

        var idRenglonSolicitud = $(data[0]).val();

        // Indica si el RenglonSolicitud se puede agregar o no al Requerimiento
        var sePuedeAgregar = true;


        // Obtengo todos los NODOS de la tabla de RenglonRequerimiento
        var renglonRequerimientodNodes = oTableRenglonRequerimiento.dataTable().fnGetNodes();

        // Por cada NODO en la tabla de RenglonRequerimiento
        jQuery.each(renglonRequerimientodNodes, function (key, row) {

            var dataRenglonRequerimiento = oTableRenglonRequerimiento.dataTable().fnGetData(row);

            //  Valido que el RenglonSolicitud seleccionado NO esté ya en el Requerimiento
            if (idRenglonSolicitud == clearFormatId(dataRenglonRequerimiento[2])) {

                sePuedeAgregar = false;

                return false;
            }
        });

        if (sePuedeAgregar) {

            // Consulto via AJAX que el RenglonSolicitud NO este siendo utilizado por otro usuario
            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'renglonsolicitudcompra/check-usuario',
                data: {
                    id_renglon_solicitud: idRenglonSolicitud
                },
                success: function (resultAjax) {

                    // Si el RenglonSolicitud NO está siendo utilizado por otro usuario
                    if (true === resultAjax['continuarEjecucion']) {

                        var acciones = '<a class="btn btn-xs green tooltips editar_renglon_requerimiento" '
                                + 'data-original-title="Editar" href="#"><i class="fa fa-pencil"></i></a>&nbsp'
                                + '<a class="btn btn-xs red tooltips quitar_renglon_requerimiento" '
                                + 'data-original-title="Eliminar" href="#">'
                                + '<i class="fa fa-times"></i></a>';

                        var justiprecioTotal = (data[7] * clearCurrencyValue(data[9])).toString().replace(/\./g, ',');

                        oTableRenglonRequerimiento.dataTable().fnAddData([
                            null, // ID Renglon Requerimiento
                            data[1], // Checkbox
                            pad(idRenglonSolicitud, 8), // ID Renglon Solicitud con formato
                            data[4], // Rubro
                            data[5], // Bien Económico
                            data[6], // Descripción
                            data[7], // Cantidad a Cotizar
                            data[8], // Unidad Medida
                            data[9], // Justiprecio Unitario
                            justiprecioTotal, // Justiprecio Total
                            data[7], // Cantidad Pendiente
                            acciones // Acciones  
                        ]);

                        // Deshabilito el TR RenglonSolicitud
                        tr_renglonSolicitud.addClass('disabled');

                        // Seteo estilos al TD de acciones de RenglonRequerimiento
                        oTableRenglonRequerimiento.DataTable().rows().nodes().to$().each(function () {

                            // Init Checkbox
                            initHackCheckbox($(this).find('div.checker'));

                            // Le agrego la clase al TD de Rubro
                            $(this).find('td').eq(2).addClass('nowrap');

                            // Formateo el campo JustiprecioTotal a Money
                            $($(this).find('td').get(8)).autoNumeric('init', {vMin: '0.000', vMax: '9999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});

                            // Le agrego la clase al TD de Justiprecio Total
                            $(this).find('td').eq(8).addClass('justiprecio-total');

                            // Oculto el TD de la Cantidad Pendiente
                            $(this).find('td').eq(9).addClass('hidden');

                            // Le agrego estilos al TD de Acciones
                            $(this).find('td').last().addClass('ctn_acciones text-center nowrap');
                        });

                        updateJustiprecioTotalRequerimiento();
                    }
                    else {
                        var options = $.extend({
                            title: 'Ha ocurrido un error',
                            msg: "No se pudo agregar el ítem ya que el mismo está siendo utilizado por el usuario "
                                    + resultAjax['nombreUsuario'] + "."
                        });

                        show_alert(options);
                    }
                }
            });
        }
        else {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "No se puede agregar el ítem ya que el mismo ya forma parte del requerimiento."
            });

            show_alert(options);
        }
    });

}

/**
 * Cada vez que se recarga la página, se setean los DIRTY a false
 */
$(window).unload(function () {

    // Obtengo todos los NODOS de la tabla de RenglonRequerimiento
    var renglonRequerimientodNodes = oTableRenglonRequerimiento.dataTable().fnGetNodes();

    // Por cada NODO en la tabla de RenglonRequerimiento
    jQuery.each(renglonRequerimientodNodes, function (key, row) {

        var dataRenglonRequerimiento = oTableRenglonRequerimiento.dataTable().fnGetData(row);

        $.ajax({
            type: 'post',
            async: false,
            url: __AJAX_PATH__ + 'renglonsolicitudcompra/clear-usuario',
            data: {
                id_renglon_solicitud: clearFormatId(dataRenglonRequerimiento[2])
            }
        });

    });
});


/**
 * 
 * @returns {undefined}
 */
function initEditarRenglonRequerimientoLink() {

    $(document).off('click', '.editar_renglon_requerimiento').on('click', '.editar_renglon_requerimiento', function (e) {

        e.preventDefault();

        // Obtengo el TR del RenglonSolicitud clickeado
        var nRow = $(this).parents('tr')[0];

        var dataRenglonRequerimiento = oTableRenglonRequerimiento.dataTable().fnGetData(nRow);

        var idRenglonSolicitud = clearFormatId(dataRenglonRequerimiento[2]);

        // Consulto via AJAX que el RenglonSolicitud NO este siendo utilizado por otro usuario
        $.ajax({
            type: 'post',
            url: __AJAX_PATH__ + 'renglonsolicitudcompra/check-usuario',
            data: {
                id_renglon_solicitud: idRenglonSolicitud
            },
            success: function (resultAjax) {

                // Si el RenglonSolicitud NO está siendo utilizado por otro usuario
                if (true === resultAjax['continuarEjecucion']) {
                    if (nEditing === null) {

                        editRow(oTableRenglonRequerimiento, nRow);

                        nEditing = nRow;

                        initGuardarRenglonRequerimientoLink();

                        initTooltip();
                    }
                    else {
                        return false;
                    }
                }
                else {
                    var options = $.extend({
                        title: 'Ha ocurrido un error',
                        msg: "No se puede editar el ítem ya que el mismo está siendo utilizado por el usuario "
                                + resultAjax['nombreUsuario'] + "."
                    });

                    show_alert(options);
                }
            }
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function initGuardarRenglonRequerimientoLink() {

    $(document).off('click', '.guardar_renglon_requerimiento').on('click', '.guardar_renglon_requerimiento', function (e) {

        e.preventDefault();

        saveRow(oTableRenglonRequerimiento, nEditing);

        nEditing = null;
    });
}

/**
 * 
 * @returns {undefined}
 */
function initEliminarRenglonRequerimientoLink() {

    // Quitar Renglon Requerimiento
    $(document).off('click', '.quitar_renglon_requerimiento').on('click', '.quitar_renglon_requerimiento', function (e) {

        e.preventDefault();

        if (nEditing === null) {
            // Obtengo el TR de RenglonRequerimiento
            var tr_renglonRequerimiento = $(this).parents('tr');

            show_confirm({
                msg: 'Desea eliminar el renglón del requerimiento?',
                callbackOK: function () {

                    // Obtengo la DATA de dicho TR
                    var dataRenglonRequerimiento = oTableRenglonRequerimiento.dataTable().fnGetData(tr_renglonRequerimiento[0]);

                    var idRenglonSolicitudSeleccionado = clearFormatId(dataRenglonRequerimiento[2]);

                    $.ajax({
                        type: 'post',
                        url: __AJAX_PATH__ + 'renglonsolicitudcompra/clear-usuario',
                        data: {
                            id_renglon_solicitud: idRenglonSolicitudSeleccionado
                        },
                        success: function () {

                            // Elimino el RenglonRequerimiento
                            oTableRenglonRequerimiento.dataTable().fnDeleteRow(tr_renglonRequerimiento[0]);

                            // Obtengo todos los NODOS de la tabla de RenglonSolicitud
                            var renglonSolicitudNodes = oTableRenglonSolicitud.dataTable().fnGetNodes();

                            // Por cada NODO en la tabla de RenglonSolicitud
                            jQuery.each(renglonSolicitudNodes, function (key, row) {

                                var dataRenglonSolicitud = oTableRenglonSolicitud.dataTable().fnGetData(row);

                                if (idRenglonSolicitudSeleccionado == $(dataRenglonSolicitud[0]).val()) {

                                    $(row).removeClass('disabled');

                                    return false;
                                }

                            });

                            updateJustiprecioTotalRequerimiento();
                        }
                    });
                }
            });
        }
        else {
            return false;
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initTiposContrataciones() {

    $.ajax({
        type: 'post',
        url: __AJAX_PATH__ + 'tipocontratacion/tipos-contrataciones',
        async: false,
        success: function (tiposContratacionesJson) {
            tiposContrataciones = tiposContratacionesJson;
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initCantidadesOriginales() {

    // Obtengo todos los NODOS de la tabla de RenglonRequerimiento
    var renglonRequerimientodNodes = oTableRenglonRequerimiento.dataTable().fnGetNodes();

    // Por cada NODO en la tabla de RenglonRequerimiento
    jQuery.each(renglonRequerimientodNodes, function (key, row) {

        var dataRenglonRequerimiento = oTableRenglonRequerimiento.dataTable().fnGetData(row);

        // Si el RenglonRequerimiento está persistido en la base
        if (null !== dataRenglonRequerimiento[0]) {
            // Guardo la cantidad a cotizar original
            cantidadesOriginales[dataRenglonRequerimiento[0]] = dataRenglonRequerimiento[6];
        }

    });
}


/**
 * 
 * @returns {undefined}
 */
function updateJustiprecioTotalRequerimiento() {

    $justiprecioTotalRequerimiento = 0;

    // Por cada Justiprecio Total en la tabla de RenglonRequerimiento
    oTableRenglonRequerimiento.DataTable().rows().nodes().to$().each(function () {

        $justiprecioTotalRenglonRequerimiento = $(this).find('.justiprecio-total').html();

        $justiprecioTotalRequerimiento += parseFloat(clearCurrencyValue($justiprecioTotalRenglonRequerimiento));

    });

    $('#adif_comprasbundle_requerimiento_justiprecio')
            .val($justiprecioTotalRequerimiento.toString().replace(/\./g, ','))
            .autoNumeric('update');

    setTipoContratacion($justiprecioTotalRequerimiento);

}

/**
 * 
 * @param {type} $justiprecioTotal
 * @returns {undefined}
 */
function setTipoContratacion($justiprecioTotal) {

    if (!isEdit) {

        if ($justiprecioTotal > 0) {

            // Por cada TipoContratacion
            $.each(tiposContrataciones, function (i, tipoContratacion) {

                // Segun el Justiprecio Total del Requerimiento 
                if ($justiprecioTotal > tipoContratacion['montoDesde']
                        && $justiprecioTotal <= tipoContratacion['montoHasta']) {

                    // Actualizo el select de TipoContratacion
                    $('#adif_comprasbundle_requerimiento_tipoContratacion')
                            .val(tipoContratacion['id']);

                    return false;
                }
            });
        }
        else {
            // Actualizo el select de TipoContratacion
            $('#adif_comprasbundle_requerimiento_tipoContratacion')
                    .val("");
        }
    }
}

/**
 * 
 * @param {type} oTableRenglonRequerimiento
 * @param {type} nRow
 * @returns {undefined}
 */
function editRow(oTableRenglonRequerimiento, nRow) {

    var aData = oTableRenglonRequerimiento.dataTable().fnGetData(nRow);

    var jqTds = $('>td', nRow);

    jqTds[5].innerHTML = '<input type="number" class="input-small ignore" value="' + aData[6] + '">';

    jqTds[7].innerHTML = '<input type="text" class="input-small money-format ignore" value="' + aData[8] + '">';

    jqTds[10].innerHTML = '<a class="btn btn-xs blue tooltips guardar_renglon_requerimiento"'
            + ' data-original-title="Guardar" href="#"><i class="fa fa-check"></i></a>';

    setMasks();

    initGuardarRenglonRequerimientoLink();
}

/**
 * 
 * @param {type} oTableRenglonRequerimiento
 * @param {type} nRow
 * @returns {undefined}
 */
function saveRow(oTableRenglonRequerimiento, nRow) {

    var jqInputs = $('input', nRow);

    if (nRow !== null) {

        var oTable = oTableRenglonRequerimiento.dataTable();

        var aData = oTable.fnGetData(nRow);

        var cantidadACotizar = jqInputs[1].value;

        var justiprecioUnitario = jqInputs[2].value;

        var cantidadMaximaACotizar = aData[10]; // aData[10] = cantidadPendiente

        // Si es un RenglonRequerimiento persistido en la BBDD
        if (null !== aData[0]) {
            // Suma la cantidad a cotizar Original + la cantidad Pendiente del renglonSolicitud asociado.
            cantidadMaximaACotizar = parseFloat(cantidadesOriginales[aData[0]]) + parseFloat(aData[10]);
        }

        var acciones = '<a class="btn btn-xs green tooltips editar_renglon_requerimiento" '
                + 'data-original-title="Editar" href="#"><i class="fa fa-pencil"></i></a>&nbsp'
                + '<a class="btn btn-xs red tooltips quitar_renglon_requerimiento" '
                + 'data-original-title="Eliminar" href="#">'
                + '<i class="fa fa-times"></i></a>';

        // Si la cantidad a cotizar no es válida
        if (!$.isNumeric(cantidadACotizar)) {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "La cantidad a cotizar ingresada no es válida."
            });

            show_alert(options);

            cantidadACotizar = aData[6];
        }

        else if (cantidadACotizar == 0) {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "La cantidad a cotizar no puede ser cero."
            });

            show_alert(options);

            cantidadACotizar = aData[6];
        }

        // Sino, Si la cantidad a cotizar es menor a la pendiente del RenglonSolicitud
        else if (parseFloat(cantidadACotizar) > parseFloat(cantidadMaximaACotizar)) {
            var options = $.extend({
                title: 'Ha ocurrido un error',
                msg: "La cantidad a cotizar no puede superar la cantidad pendiente solicitada."
            });

            show_alert(options);

            cantidadACotizar = aData[6];
        }

        // Actualiza Justiprecio Total del RENGlON con la nueva "cantidadACotizar"
        $justiprecioTotalRenglonRequerimiento = parseFloat(clearCurrencyValue(justiprecioUnitario)) * cantidadACotizar;

        oTable.fnUpdate(cantidadACotizar, nRow, 6, false);
        oTable.fnUpdate(justiprecioUnitario, nRow, 8, false);
        oTable.fnUpdate($justiprecioTotalRenglonRequerimiento, nRow, 9, false);
        oTable.fnUpdate(acciones, nRow, 11, false);

        oTable.fnDraw();

        $(nRow).find('.justiprecio-total').autoNumeric('destroy');
        $(nRow).find('.justiprecio-total').autoNumeric('init', {vMin: '0.000', vMax: '9999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});


        updateJustiprecioTotalRequerimiento();

    }
}

/**
 * 
 * @returns {undefined}
 */
function setMasks() {

    $('.money-format').each(function () {
        $(this).autoNumeric('init', {vMin: '0.000', vMax: '9999999999.9999', aSign: '$ ', aSep: '.', aDec: ','});
    });

    $('.currency-format').each(function () {
        $(this).autoNumeric('init', {vMin: '0.000', vMax: '9999999999.9999', aSep: '.', aDec: ','});
    });
}

/**
 * 
 * @param {type} $value
 * @returns {unresolved}
 */
function clearCurrencyValue($value) {

    if (typeof $value !== "undefined") {
        return $value.replace('$', '').replace(/\./g, '').replace(/\,/g, '.');
    }
}

/**
 * 
 * @param {type} $idWithFormat
 * @returns {unresolved}
 */
function clearFormatId($idWithFormat) {
    return parseInt($idWithFormat.replace(/[^\d.]/g, ''));
}