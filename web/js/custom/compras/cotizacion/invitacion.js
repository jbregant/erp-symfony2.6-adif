
var oTableProveedores = $('#table-proveedores');

var oTableInvitaciones = $('#table-invitaciones');


/**
 * 
 */
jQuery(document).ready(function () {

    // Validacion del Formulario
    $('form[name=adif_comprasbundle_invitacion]').validate();

    initTables();

    initLinks();

    configPreSubmit();

});

/**
 * 
 * @returns {undefined}
 */
function initTables() {

    dt_init(oTableProveedores);
    dt_init(oTableInvitaciones);

    deshabilitarProveedoresAsociados();
}

/**
 * 
 * @returns {undefined}
 */
function deshabilitarProveedoresAsociados() {

    var oTableProveedoresDataTable = oTableProveedores.DataTable();
    var oTableInvitacionesDataTable = oTableInvitaciones.DataTable();

    // Si hay al menos una invitacion cargada
    if (oTableInvitacionesDataTable.data().length > 0) {

        // Obtengo todos los NODOS de la tabla de Invitaciones
        var nodesInvitaciones = oTableInvitacionesDataTable.rows().nodes();

        // Por cada NODO en la tabla de Invitaciones
        jQuery.each(nodesInvitaciones, function (key, rowInvitacion) {

            // Obtengo el id del Proveedor asociado a la invitacion
            var idProveedorInvitacion = oTableInvitacionesDataTable.row(rowInvitacion).data()[1];

            // Obtengo todos los NODOS de la tabla de Proveedores
            var nodesProveedor = oTableProveedoresDataTable.rows().nodes();

            // Por cada NODO en la tabla de Proveedores
            jQuery.each(nodesProveedor, function (key, rowProveedor) {

                var dataProveedor = oTableProveedoresDataTable.row(rowProveedor).data();

                var idProveedor = dataProveedor[0];

                if (idProveedorInvitacion === idProveedor) {

                    $(rowProveedor).addClass('disabled');

                    return false;
                }

            });
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initLinks() {

    initAgregarInvitacionLink();
    initEliminarInvitacionLink();
}

/**
 * Agregar Invitacion
 * 
 * @returns {undefined}
 */
function initAgregarInvitacionLink() {

    $(document).on('click', '.agregar_invitacion', function (e) {

        e.preventDefault();

        // Obtengo el TR del Proveedor clickeado
        var trProveedor = $(this).parents('tr');

        var dataProveedor = oTableProveedores.DataTable().row(trProveedor).data();

        var acciones = '<a class="btn btn-xs red tooltips eliminar_invitacion" '
                + 'data-original-title="Eliminar" href="#">'
                + '<i class="fa fa-times"></i></a>';

        var newRow = oTableInvitaciones.DataTable().row.add([
            null, // id Invitacion
            dataProveedor[0], // ID Proveedor
            dataProveedor[2], // Razon Social
            dataProveedor[3], // CUIT
            dataProveedor[4], // Evaluacion
            getCurrentDate(), // Fecha Invitacion
            "", // Fecha Cotizacon
            acciones // Acciones
        ]).draw().node();

        trProveedor.addClass('disabled');

        // Oculto el TD del id de Proveedor
        $(newRow).find('td').eq(0).addClass('hidden');

        var claseEvaluacion = getClaseEvaluacion(dataProveedor[4]);

        // Le agrego estilos al TD de la Evaluacion
        $(newRow).find('td').eq(3).addClass('text-center');
        $(newRow).find('td').eq(3).addClass(claseEvaluacion);

        // Le agrego estilos al TD de Acciones
        $(newRow).find('td').last().addClass('ctn_acciones text-center nowrap');

        initEliminarInvitacionLink();

        initTooltip();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initEliminarInvitacionLink() {

    $('.eliminar_invitacion').off().on('click', function (e) {

        e.preventDefault();

        // Obtengo el TR de la Invitacion clickeado
        var trInvitacion = $(this).parents('tr');

        show_confirm({
            msg: '¿Desea eliminar la invitación?',
            callbackOK: function () {

                var oTableProveedoresDataTable = oTableProveedores.DataTable();

                var rowInvitacion = oTableInvitaciones.DataTable().row(trInvitacion);

                var dataInvitacion = rowInvitacion.data();

                // Elimino la Invitacion de la tabla
                rowInvitacion.remove().draw();

                // Obtengo el id del Proveedor asociado a la invitacion
                var idProveedorInvitacion = dataInvitacion[1];

                // Obtengo todos los NODOS de la tabla de Proveedores
                var nodesProveedor = oTableProveedoresDataTable.rows().nodes();

                // Por cada NODO en la tabla de Proveedores
                jQuery.each(nodesProveedor, function (key, rowProveedor) {

                    var dataProveedor = oTableProveedoresDataTable.row(rowProveedor).data();

                    var idProveedor = dataProveedor[0];

                    if (idProveedorInvitacion === idProveedor) {

                        $(rowProveedor).removeClass('disabled');

                        return false;
                    }

                });
            }
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function configPreSubmit() {

    var saveClick = false;

    /**
     * Handler para el evento SUBMIT del formulario
     */
    $('form[name=adif_comprasbundle_invitacion]').submit(function (event) {

        if ($(this).valid()) {

            var oTableInvitacionesDataTable = oTableInvitaciones.DataTable();

            // Si hay al menos una invitacion cargada
            if (oTableInvitacionesDataTable.data().length > 0) {

                var json = {
                    proveedores_invitados: [],
                    requerimiento: requerimientoId
                };

                // Obtengo todos los NODOS de la tabla de Invitaciones
                var nodesInvitacion = oTableInvitacionesDataTable.rows().nodes();

                // Por cada NODO en la tabla de Invitaciones
                jQuery.each(nodesInvitacion, function (key, rowInvitacion) {

                    var dataInvitacion = oTableInvitacionesDataTable.row(rowInvitacion).data();

                    json.proveedores_invitados.push({
                        'id': dataInvitacion[1],
                        'fecha': dataInvitacion[5]
                    });

                });

                // Agrega JSON data antes del submit
                $('form[name=adif_comprasbundle_invitacion]').addHiddenInputData(json);
            }
            else {
                var options = $.extend({
                    title: 'Ha ocurrido un error',
                    msg: "Debe invitar al menos a un proveedor."
                });

                show_alert(options);

                event.preventDefault();
            }
        }

        return;
    });

    // Handler para el boton "Guardar"
    $('#adif_comprasbundle_invitacion_submit').on('click', function (e) {

        if ($('form[name=adif_comprasbundle_invitacion]').valid()) {

            if (saveClick === true) {
                saveClick = false;
                return;
            }

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar la invitación?',
                callbackOK: function () {
                    saveClick = true;
                    $('#adif_comprasbundle_invitacion_submit').trigger('click');
                }
            });

            return;
        }

    });

}

/**
 * 
 * @param {type} calificacion
 * @returns {String}
 */
function getClaseEvaluacion(calificacion) {

    var claseEvaluacion = '';

    if (calificacion < 4) {
        claseEvaluacion = 'calificacion-mala';
    }
    else if (calificacion >= 4 && calificacion < 7) {
        claseEvaluacion = 'calificacion-regular';
    }
    else if (calificacion >= 7) {
        claseEvaluacion = 'calificacion-buena';
    }

    return claseEvaluacion;
}