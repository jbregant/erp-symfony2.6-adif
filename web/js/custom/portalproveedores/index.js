var index = 0;
var dt_proveedor_column_index = {
    id: index++,
    multiselect: index++,
    razonSocial: index++,
    proveedor: index++,
    CUIT: index++,
    rubro: index++,
    proveedorNacional: index++,
    estadoEvaluacion: index++,
    galo: index++,
    gafFinanzas: index++,
    gafImpuestos: index++,
    gcshm: index++,
    fechaSolicitud: index++,
    fachaUltimaModificacion: index++,
    errores: index++,
    acciones: index
};

var estadoColor = {
    1: 'yellow',
    2: 'green',
    3: 'red',
    4: 'blue'
};

// Introducir el motivo de rechazo por defecto
var motivoRechazoStandard = "La solicitud no ha superado el proceso de evaluación.";

dt_table_reporte = dt_datatable($('#table-proveedor-evaluacion'), {
    ajax: __AJAX_PATH__ + 'proveedorevaluacion/index_table',
    columnDefs: [
        {
            "targets": dt_proveedor_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_proveedor_column_index.acciones,
            "render": function (data, type, full, meta) {
                var links = full[dt_proveedor_column_index.acciones];
                var galo = full[dt_proveedor_column_index.galo];
                var gafFinanzas = full[dt_proveedor_column_index.gafFinanzas];
                var gafImpuestos = full[dt_proveedor_column_index.gafImpuestos];
                var gcshm = full[dt_proveedor_column_index.gcshm];
                var $output = '';

                if (links.show_galo != null && galo.id != 1){
                    $output += '<a href="' + links.show_galo + '?edit=1" class="btn btn-xs tooltips" data-original-title="Editar gerencia GALO" >\n\
                    <i class="fa fa-pencil"></i>\n\
                    </a>';
                }

                if (links.show_gaf_finanzas != null && gafFinanzas.id != 1){
                    $output += '<a href="' + links.show_gaf_finanzas + '?edit=1" class="btn btn-xs tooltips" data-original-title="Editar gerencia GAF Finanzas" >\n\
                    <i class="fa fa-pencil"></i>\n\
                    </a>';
                }

                if (links.show_gaf_impuestos != null && gafImpuestos.id != 1){
                    $output += '<a href="' + links.show_gaf_impuestos + '?edit=1" class="btn btn-xs tooltips" data-original-title="Editar gerencia GAF Impuestos" >\n\
                    <i class="fa fa-pencil"></i>\n\
                    </a>';
                }

                if (links.show_gcshm != null && gcshm.id != 1){
                    $output += '<a href="' + links.show_gcshm + '?edit=1" class="btn btn-xs tooltips" data-original-title="Editar gerencia GCSHM" >\n\
                    <i class="fa fa-pencil"></i>\n\
                    </a>';
                }

                if (links.motivoRechazo != null || links.motivoRechazoInterno != null){
                    data_rechazo =
                        '<strong>Motivo del rechazo para el solicitante:</strong><br>' +
                        links.motivoRechazo +
                        '<br><br><strong>Motivo de rechazo interno:</strong><br>' +
                        links.motivoRechazoInterno;


                    return '<a href="#" onClick="show_alert({msg: \'' + data_rechazo + '\', title: \'Motivos de rechazo del proveedor\', type: \'error\'});" class="btn btn-xs tooltips" data-original-title="Ver motivos de rechazo">\n\
                        <i class="fa fa-search"></i>\n\
                        </a>';
                }

                return $output;
            }
        },
        {
            "targets": dt_proveedor_column_index.estadoEvaluacion,
            "render": function (data, type, full, meta) {
                var full_data = full[dt_proveedor_column_index.estadoEvaluacion];
                var color = estadoColor[full_data.id];
                return '<span class="btn btn-xs ' + color + ' ">\n\
                        </i>' + ' ' + full_data.denominacion + '\n\
                        </span>';
            }
        },
        {
            "targets": dt_proveedor_column_index.galo,
            "render": function (data, type, full, meta) {
                var full_data = full[dt_proveedor_column_index.galo];
                var color = estadoColor[full_data.id];
                var link = full[dt_proveedor_column_index.acciones];
                if (link.show_galo) {
                    return '<a href="' + link.show_galo + '" class="btn btn-xs ' + color + ' tooltips" data-original-title="Editar detalle">\n\
                        <i class="fa fa-pencil"></i>' + ' ' + full_data.denominacion + '\n\
                        </a>';
                } else {
                    return '<span class="btn btn-xs ' + color + ' ">' + full_data.denominacion + '</span>';
                }
            }
        },
        {
            "targets": dt_proveedor_column_index.gafFinanzas,
            "render": function (data, type, full, meta) {
                var full_data = full[dt_proveedor_column_index.gafFinanzas];
                var color = estadoColor[full_data.id];
                var link = full[dt_proveedor_column_index.acciones];
                if (link.show_gaf_finanzas) {
                    var img = (link.show_galo)? 'fa-search' : 'fa-pencil';
                    var tooltip = (link.show_galo)? 'Ver detalle' : 'Editar detalle';
                    return '<a href="' + link.show_gaf_finanzas + '" class="btn btn-xs ' + color + ' tooltips" data-original-title="'+ tooltip +'">\n\
                        <i class="fa '+ img +'"></i>' + ' ' + full_data.denominacion + '\n\
                        </a>';
                } else {
                    return '<span class="btn btn-xs ' + color + ' ">' + full_data.denominacion + '</span>';
                }
            }
        },
        {
            "targets": dt_proveedor_column_index.gafImpuestos,
            "render": function (data, type, full, meta) {
                var full_data = full[dt_proveedor_column_index.gafImpuestos];
                var color = estadoColor[full_data.id];
                var link = full[dt_proveedor_column_index.acciones];
                if (link.show_gaf_impuestos) {
                    var img = (link.show_galo)? 'fa-search' : 'fa-pencil';
                    var tooltip = (link.show_galo)? 'Ver detalle' : 'Editar detalle';
                    return '<a href="' + link.show_gaf_impuestos + '" class="btn btn-xs ' + color + ' tooltips" data-original-title="'+ tooltip +'">\n\
                        <i class="fa '+ img +'"></i>' + ' ' + full_data.denominacion + '\n\
                        </a>';
                } else {
                    return '<span class="btn btn-xs ' + color + ' ">' + full_data.denominacion + '</span>';
                }
            }
        },
        {
            "targets": dt_proveedor_column_index.gcshm,
            "render": function (data, type, full, meta) {
                var full_data = full[dt_proveedor_column_index.gcshm];
                var color = estadoColor[full_data.id];
                var link = full[dt_proveedor_column_index.acciones];
                if (link.show_gcshm) {
                    var img = (link.show_galo)? 'fa-search' : 'fa-pencil';
                    var tooltip = (link.show_galo)? 'Ver detalle' : 'Editar detalle';
                    return '<a href="' + link.show_gcshm + '" class="btn btn-xs ' + color + ' tooltips" data-original-title="'+ tooltip +'">\n\
                        <i class="fa '+ img +'"></i>' + ' ' + full_data.denominacion + '\n\
                        </a>';
                } else {
                    return '<span class="btn btn-xs ' + color + ' ">' + full_data.denominacion + '</span>';
                }
            }
        },
        {
            "targets": dt_proveedor_column_index.errores,
            "render": function (data, type, full, meta) {
                var full_data = full[dt_proveedor_column_index.errores];
                if (full_data !== '-') {
                    var color = estadoColor[3];
                    return '<a href="#" onClick="show_alert({msg: \'' + full_data + '\', title: \'Error en datos del proveedor\', type: \'error\'});" class="btn btn-xs ' + color + ' tooltips" data-original-title="Ver errores">\n\
                        <i class="fa fa-search"></i>\n\
                        </a>';
                } else {
                    return full_data;
                }
            }
        },
        {
            className: "text-center fecha-col",
            targets: [
                dt_proveedor_column_index.fechaSolicitud,
                dt_proveedor_column_index.fachaUltimaModificacion
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_proveedor_column_index.multiselect
            ]
        },
        {
            className: "text-center cuit-col",
            targets: [
                dt_proveedor_column_index.CUIT
            ]
        },
        {
            className: "text-center rs-col",
            targets: [
                dt_proveedor_column_index.razonSocial
            ]
        },
        {
            className: "text-center rubro-col",
            targets: [
                dt_proveedor_column_index.rubro
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_proveedor_column_index.razonSocial,
                dt_proveedor_column_index.CUIT,
                dt_proveedor_column_index.rubro,
                dt_proveedor_column_index.proveedorNacional,
                dt_proveedor_column_index.estadoEvaluacion,
                dt_proveedor_column_index.galo,
                dt_proveedor_column_index.gafFinanzas,
                dt_proveedor_column_index.gafImpuestos,
                dt_proveedor_column_index.gcshm,
                dt_proveedor_column_index.fechaSolicitud,
                dt_proveedor_column_index.fachaUltimaModificacion
            ]
        },
        {
            className: "hidden",
            targets: [dt_proveedor_column_index.proveedor]
        }
    ]
});

$(document).ready(function () {

    // Flag para interesados que ya son proveedores.
    var esProveedor = 0;

    $.fn.dataTable.moment('DD/MM/YYYY'); // Se encarga de ordenar correctamente los campos tipo fecha.
    $.fn.dataTable.moment('DD/MM/YYYY HH:mm'); // Se encarga de ordenar correctamente los campos tipo fecha.
    $("#btn_aprobar_modificacion").hide();
    var table = $('#table-proveedor-evaluacion').DataTable();


    // Caro inicialmente solo aquellos que son interesados.
    table.columns(dt_proveedor_column_index['proveedor']).search("(2)", true, false).draw();

    // Muestro solo aquellos que son inscripciones.
    $('#inscripcionesPestaña').on('click',  function () {

        // Destildo checkbox's al cambiar de tab.
        $('.checkboxes').each(function (index) {
            $(this).prop('checked', false);
            $('.group-checkable').prop('checked', false);
        });

        table.columns(dt_proveedor_column_index['proveedor']).search("(2)", true, false).draw();
        esProveedor = 0;
    });

    // Muestro solo aquellos que son proveedores.
    $('#proveedoresPestaña').on('click',  function () {

        // oculto el boton de asociar
        $('#btn_asociar_contacto').hide();
        // Destildo checkbox's al cambiar de tab.
        $('.checkboxes').each(function (index) {
            $(this).prop('checked', false);
            $('.group-checkable').prop('checked', false);
        });

        table.columns(dt_proveedor_column_index['proveedor']).search("(1)", true, false).draw();
        esProveedor = 1;
    });

    $('#table-proveedor-evaluacion tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('active') ) {
            $(this).className = '';
            $(this > 'td').removeProp('checked');
        }
        else {
            $(this).className += 'active';
            $(this > 'td').prop('checked');
        }
    } );

    $('#btn_aprobar_interesado').on('click', function (e) {

        e.preventDefault();

        var table = $('#table-proveedor-evaluacion');
        var ids = [];

        ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar un Interesado.'});

            desbloquear();

            return;
        }

        if (ids.length >= 1) {
            var data = {'ids': ids.toArray()};
            $.ajax({
                type: "POST",
                data: data,
                //async: false,
                url: __AJAX_PATH__ + 'proveedorevaluacion/altaproveedor'

            }).done(function (response) {
                if (response.result === 'OK') {
                    //show_alert({msg: 'El Proveedor se agregó correctamente.', title: ''});
                    showFlashMessage('success', 'El Proveedor se dió de alta correctamente',50000);
                    //location.href = __AJAX_PATH__ + 'proveedorevaluacion/';
                    dt_table_reporte.api().ajax.url(__AJAX_PATH__ + 'proveedorevaluacion/index_table').load();
                } else if (response.result === 'NOK') {
                    show_alert({msg: response.message, title: 'Error enviando alta de Proveedor', type: 'error'});
                    dt_table_reporte.api().ajax.url(__AJAX_PATH__ + 'proveedorevaluacion/index_table').load();
                }

            }).fail(function () {
                show_alert({msg: 'Ocurri&oacute; un error. Intente nuevamente.', title: 'Error al enviar Proveedor', type: 'error'});
            });
        }

    });

    $('#btn_aprobar_modificacion').on('click', function (e) {

        e.preventDefault();

        var table = $('#table-proveedor-evaluacion');
        var ids = [];

        ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar una modificación.'});

            desbloquear();

            return;
        }

        if (ids.length >= 1) {
            var data = {'ids': ids.toArray(), 'flag': 1};
            $.ajax({
                type: "POST",
                data: data,
                //async: false,
                url: __AJAX_PATH__ + 'proveedorevaluacion/altaproveedor'

            }).done(function (response) {
                if (response.result === 'OK') {
                    showFlashMessage('success', 'La modificación se realizó correctamente.',50000);
                    //location.href = __AJAX_PATH__ + 'proveedorevaluacion/';
                    dt_table_reporte.api().ajax.url(__AJAX_PATH__ + 'proveedorevaluacion/index_table').load();
                } else if (response.result === 'NOK') {
                    show_alert({msg: response.message, title: 'Error enviando modificación de Proveedor', type: 'error'});
                    dt_table_reporte.api().ajax.url(__AJAX_PATH__ + 'proveedorevaluacion/index_table').load();
                }

            }).fail(function () {
                show_alert({msg: 'Ocurri&oacute; un error. Intente nuevamente.', title: 'Error al modificar Proveedor', type: 'error'});
            });
        }

    });

    $('#btn_rechazar_interesado').on('click', function (e) {

        e.preventDefault();

        var table = $('#table-proveedor-evaluacion');
        var ids = [];

        ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar un Interesado.'});

            desbloquear();

            return;
        }

        // Si es un proveedor entonces no permito modificarlo.
        if (esProveedor) {
            show_alert({msg: 'No es posible rechazar un interesado que ya fue aceptado.'});
            return;
        }


        formulario_motivo_rechazo = '<form name="form-motivo_rechazo">\n\
                                  <label class="control-label required" for="motivo_rechazo">Ingrese el motivo del rechazo para el solicitante</label>\n\
                                  <div class="form-group">\n\
                                    <div class="input-icon right">\n\
                                        <i class="fa"></i>\n\
                                        <textarea id="motivo_rechazo" name="motivo_rechazo" required="required" class="form-control">' + motivoRechazoStandard +'</textarea>\n\
                                    </div>\n\
                                  </div>\n\
                                  <label class="control-label required" for="motivo_rechazo">Ingrese el motivo de rechazo interno</label>\n\
                                  <div class="form-group">\n\
                                    <div class="input-icon right">\n\
                                        <i class="fa"></i>\n\
                                        <textarea id="motivo_rechazo_interno" name="motivo_rechazo_interno" required="required" class="form-control">' + motivoRechazoStandard +'</textarea>\n\
                                    </div>\n\
                                  </div>\n\
                                  </form>';
        show_dialog({
            titulo: 'Rechazar Interesado',
            contenido: formulario_motivo_rechazo,
            'labelSuccess': 'Enviar',
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=form-motivo_rechazo]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {

                    var data = {
                        'ids': ids.toArray(),
                        'motivoRechazo': $('#motivo_rechazo').val(),
                        'motivoRechazoInterno': $('#motivo_rechazo_interno').val()
                    };
                    $.ajax({
                        type: "POST",
                        data: data,
                        //async: false,
                        url: __AJAX_PATH__ + 'proveedorevaluacion/rechazarproveedor'

                    }).done(function (response) {
                        if (response.result === 'OK') {
                            showFlashMessage('success', 'El Proveedor se rechazó correctamente.', 50000);
                            location.href = __AJAX_PATH__ + 'proveedorevaluacion/';
                        }
                    }).fail(function () {
                        show_alert({msg: 'Ocurri&oacute; un error. Intente nuevamente.', title: 'Error al rechazar Proveedor', type: 'error'});
                    });
                } else {
                    return false;
                }
            }
        });

    });

    $('#btn_asociar_contacto').on('click', function (e) {

        e.preventDefault();

        var table = $('#table-proveedor-evaluacion');
        var ids = [];

        ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar un Interesado.'});

            desbloquear();

            return;
        }

        // Si es un proveedor entonces no permito modificarlo.
        if (esProveedor) {
            show_alert({msg: 'Utilice la opcion de agregar contacto dentro del menu proveedores.'});
            return;
        }

        formulario_asociar_contacto = '<form name="form-asociar_contacto">' +
            '<div class="row">' +
            '<div class="col-md-6">'+
            '<div class="form-group">'+
            '<label class="col-sm-6 control-label required" for="form_nombre">Nombre</label>'+
            '<input type="text" id="form_nombre" name="form[nombre]" required="required" maxlength="36" class="form-control form-control">'+
            '</div>'+
            '</div>'+
            '<div class="col-md-6">'+
            '<div class="form-group">'+
            '<label class="col-sm-6 control-label required" for="form_apellido">Apellido</label>'+
            '<input type="text" id="form_apellido" name="form[apellido]" required="required" maxlength="36" class="form-control form-control">'+
            '</div>'+
            '</div>'+
            '</div>'+
            '<div class="row">'+
            '<div class="col-md-6">'+
            '<div class="form-group">'+
            '<label class="col-sm-5 control-label required" for="form_area">Área</label>'+
            '<input type="text" id="form_area" name="form[area]" required="required" maxlength="36" class="form-control form-control">'+
            '</div>'+
            '</div>'+
            '<div class="col-md-6">'+
            '<div class="form-group">'+
            '<label class="col-sm-2 control-label" for="form_posicion">Posición</label>'+
            '<input type="text" id="form_posicion" name="form[posicion]" maxlength="36" class="form-control form-control">'+
            '</div>'+
            '</div>'+
            '</div>'+
            '<div class="row">'+
            '<div class="col-md-6">'+
            '<div class="form-group">'+
            '<label class="col-sm-5 control-label required" for="form_email">Email</label>'+
            '<input type="text" id="form_email" name="form[email]" required="required" maxlength="36" class="form-control form-control">'+
            '</div>'+
            '</div>'+
            '<div class="col-md-6">'+
            '<div class="form-group">'+
            '<label class="col-sm-5 control-label required" for="form_telefono">Teléfono</label>'+
            '<input type="text" id="form_telefono" name="form[telefono]" required="required" maxlength="15" class="form-control form-control">'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</form>';
        show_dialog({
            titulo: 'Asociar Contacto',
            contenido: formulario_asociar_contacto,
            'labelSuccess': 'Enviar',
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=form-asociar_contacto]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {

                    var data = {
                        'ids': ids.toArray(),
                        'nombre': $('#form_nombre').val(),
                        'apellido': $('#form_apellido').val(),
                        'area': $('#form_area').val(),
                        'posicion': $('#form_posicion').val(),
                        'email': $('#form_email').val(),
                        'telefono': $('#form_telefono').val()
                    };
                    $.ajax({
                        type: "POST",
                        data: data,
                        //async: false,
                        url: __AJAX_PATH__ + 'proveedorevaluacion/asociarcontactoproveedor'

                    }).done(function (response) {
                        if (response.result === 'OK') {
                            showFlashMessage('success', 'Se asocio el contacto correctamente.', 48000);
                            location.href = __AJAX_PATH__ + 'proveedorevaluacion/';
                        }
                    }).fail(function () {
                        show_alert({msg: 'Ocurri&oacute; un error. Intente nuevamente.', title: 'Error al agregarel contacto', type: 'error'});
                    });
                } else {
                    return false;
                }
            }
        });

    });
    $('#inscripcionesPestaña').on('click', function (e) {
        //muestro el boton de contacto
        $('#btn_asociar_contacto').show();

        $("#btn_aprobar_interesado").show();
        $("#btn_aprobar_modificacion").hide();
    });

    $('#proveedoresPestaña').on('click', function (e) {
        $("#btn_aprobar_interesado").hide();
        $("#btn_aprobar_modificacion").show();
    });
});
