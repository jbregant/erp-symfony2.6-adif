index = 0;
var dt_proveedor_column_index = {
    id: index++,
    multiselect: index++,
    cuit: index++,
    razonSocial: index++,
    codigoproveedor: index++,
    representantelegal: index++,
    extrajero: index++,
    dc_direccion: index++,
    numeroIIBB: index++,
    condicionIVA: index++,
    exentoIVA: index++,
    condicionGANANCIAS: index++,
    exentoGANANCIAS: index++,
    condicionSUSS: index++,
    exentoSUSS: index++,
    condicionIIBB: index++,
    exentoIIBB: index++,
    calificacionfiscal: index++,
    problemasafip: index++,
    riesgofiscal: index++,
    magnitudessuperadas: index++,
    calificacionFinal: index++,
    estadoProveedor: index++,
    acciones: index++
};

dt_proveedor = dt_datatable($('#table-proveedor'), {
    ajax: __AJAX_PATH__ + 'proveedor/index_table/',
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
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_proveedor_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>'
                        +
                        (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>' : '')
                        +
                        (full_data.cta_cte !== undefined ?
                                '<a href="' + full_data.cta_cte + '" class="btn btn-xs yellow tooltips" data-original-title="Ver cuenta corriente">\n\
                                    <i class="fa fa-letter">CC</i>\n\
                                </a>' : '')
                        ;
            }
        },
        {
            "targets": dt_proveedor_column_index.calificacionFinal,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_proveedor_column_index.calificacionFinal];

                $(td).addClass(full_data.claseCalificacionFinal);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_proveedor_column_index.calificacionFinal];

                return  full_data.calificacionFinal;
            }
        },
        {
            "targets": dt_proveedor_column_index.estadoProveedor,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_proveedor_column_index.estadoProveedor];

                $(td).addClass("state state-" + full_data.aliasTipoImportancia);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_proveedor_column_index.estadoProveedor];

                return  full_data.estadoProveedor;
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_proveedor_column_index.cuit,
                dt_proveedor_column_index.razonSocial,
                dt_proveedor_column_index.numeroIIBB
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_proveedor_column_index.multiselect,
                dt_proveedor_column_index.calificacionFinal
            ]
        },
        {
            className: "text-center nowrap",
            targets: [
                dt_proveedor_column_index.estadoProveedor
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_proveedor_column_index.acciones
        }
    ]
});

var $formularioImportarPadron = $('#form_importar_padron');
var $formularioGuardarPadron = $('#form_guardar_padron');
var $tabla_padrones = $('#tabla_padrones');
var $form_guardar_padron_action =$('#form_guardar_padron_action');
var $form_guardar_borrador_padron_action =$('#form_guardar_borrador_padron_action');

$(document).ready(function () {
    initRiesgoFiscalHandler();
    initMagnitudesSuperadasHandler();
    initPadronesHandler();
    initSubmitImportarPadronButton();
    initSubmitGuardarPadronButton();
    initSubmitGuardarBorradorPadronButton();
    initTablaPadrones();
});

function initTablaPadrones(){
    if($tabla_padrones.length > 0) {
        dt_datatable($tabla_padrones);
    }
}

function initSubmitImportarPadronButton() {

    // Handler para el boton "Guardar"
    $('#form_importar_padron_submit').on('click', function (e) {

        e.preventDefault();
        if ($formularioImportarPadron.valid()) {
            show_confirm({
                msg: '¿Desea cargar el padr&oacute;n?',
                callbackOK: function () {
                    $formularioImportarPadron.submit();
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;
    });
}

function initSubmitGuardarPadronButton() {

    // Handler para el boton "Guardar"
    $('#form_guardar_padron_submit').on('click', function (e) {

        e.preventDefault();
        show_confirm({
            msg: '¿Desea actualizar el padr&oacute;n?',
            callbackOK: function () {
                $formularioGuardarPadron.attr('action',$form_guardar_padron_action.val());
                $formularioGuardarPadron.submit();
            }
        });

        e.stopPropagation();

        return false;
    });
}

function initSubmitGuardarBorradorPadronButton() {

    // Handler para el boton "Guardar Borrador"
    $('#form_guardar_borrador_padron_submit').on('click', function (e) {

        e.preventDefault();
        show_confirm({
            msg: '¿Desea guardar los cambios realizados?',
            callbackOK: function () {
                $formularioGuardarPadron.attr('action',$form_guardar_borrador_padron_action.val());
                $formularioGuardarPadron.submit();
            }
        });

        e.stopPropagation();

        return false;
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRiesgoFiscalHandler() {
    $('#importar_riesgoFiscal').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var importar_riesgoFiscal = '<div>\n\
                            <span>El archivo a importar debe tener formato CSV separado por ";".</span>\n\
                            <form id="form_importar_riesgoFiscal" method="post" enctype="multipart/form-data" name="form_importar_riesgoFiscal" action="' + __AJAX_PATH__ + 'proveedor/importarRiesgoFiscal">\n\
                                <div class="row">\n\
                                    <div class="col-md-12">\n\
                                        <div class="form-group">\n\
                                            <div class="input-icon right">\n\
                                                <input class="filestyle" type="file" required="required" id="form_importar_riesgoFiscal_file" name="form_importar_riesgoFiscal_file" accept="application/zip">\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </form>\n\
                        </div>';
        show_dialog({
            titulo: 'Importar riesgo fiscal',
            contenido: importar_riesgoFiscal,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=form_importar_riesgoFiscal]').validate();
                var formulario_result = formulario.form();

                if (formulario_result) {
                    var filename = $('#form_importar_riesgoFiscal_file')[0].files[0].name;

                    filename = filename.toUpperCase();

                    if (filename.match(/\.[0-9a-z]+$/i) == '.ZIP') {
                        $('form[name=form_importar_riesgoFiscal]').submit();
                    } else {
                        show_alert({msg: 'El archivo importado no es de tipo ZIP.'});

                        $('#form_importar_riesgoFiscal_file')[0].value = "";
                        $('#form_importar_riesgoFiscal_file').parent().find('.bootstrap-filestyle').find('input').val('');
                        return false;
                    }
                } else {
                    return false;
                }
            }
        });

        initFileInput();

        desbloquear();

        e.stopPropagation();
    });
}

/**
 * 
 * @returns {undefined}
 */
function initMagnitudesSuperadasHandler() {
    $('#importar_magnitudes').on('click', function (e) {
        e.preventDefault();
        bloquear();

        var importar_magnitudes = '<div>\n\
                            <span>El archivo a importar debe tener formato CSV separado por ";".</span>\n\
                            <form id="form_importar_magnitudes" method="post" enctype="multipart/form-data" name="form_importar_magnitudes" action="' + __AJAX_PATH__ + 'proveedor/importarMagnitudes">\n\
                                <div class="row">\n\
                                    <div class="col-md-12">\n\
                                        <div class="form-group">\n\
                                            <div class="input-icon right">\n\
                                                <input class="filestyle" type="file" required="required" id="form_importar_magnitudes_file" name="form_importar_magnitudes_file" accept="text/xml">\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </form>\n\
                        </div>';
        show_dialog({
            titulo: 'Importar magnitudes superadas',
            contenido: importar_magnitudes,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=form_importar_magnitudes]').validate();
                var formulario_result = formulario.form();

                if (formulario_result) {
                    var filename = $('#form_importar_magnitudes_file')[0].files[0].name;

                    filename = filename.toUpperCase();

                    if (filename.match(/\.[0-9a-z]+$/i) == '.RAR') {
                        $('form[name=form_importar_magnitudes]').submit();
                    } else {
                        show_alert({msg: 'El archivo importado no es de tipo RAR.'});

                        $('#form_importar_magnitudes_file')[0].value = "";
                        $('#form_importar_magnitudes_file').parent().find('.bootstrap-filestyle').find('input').val('');
                        return false;
                    }
                } else {
                    return false;
                }
            }
        });

        initFileInput();

        desbloquear();

        e.stopPropagation();
    });

}

function initPadronesHandler() {
    $('#importar_padron').on('click', function (e) {
        e.preventDefault();
        bloquear();

        var importar_padron = '<div>\n\
                            <span>El archivo a importar debe tener formato TXT.</span>\n\
                            <form id="form_importar_padron" method="post" enctype="multipart/form-data" name="form_importar_padron" action="' + __AJAX_PATH__ + '/importarPadron">\n\
                                <div class="row">\n\
                                    <div class="col-md-12">\n\
                                        <div class="form-group">\n\
                                            <label class="control-label required">Impuesto</label>\n\
                                            <div class="input-icon right">\n\
                                                <i class="fa"></i>\n\
                                                <select id="form_importar_padron_impuesto" name="form_importar_padron_impuesto" class="form-control choice ignore">\n\
                                                    <option value="" selected="selected">-- Elija un impuesto --</option>\n\
                                                    <option value="IVA2226">I.V.A. REG 2226</option>\n\
                                                    <option value="IVA18" >I.V.A. REG 18</option>\n\
                                                    <option value="IIBB">II.BB.</option>\n\
                                                    <option value="SUSS">SUSS</option>\n\
                                                    <option value="Ganancias">Ganancias</option>\n\
                                                </select>\n\
                                            </div>\n\
                                            <br />\n\
                                            <div class="input-icon right">\n\
                                                <input class="filestyle" type="file" required="required" id="form_importar_padron_file" name="form_importar_padron_file" accept="text/xml">\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </form>\n\
                        </div>';
        show_dialog({
            titulo: 'Importar padron',
            contenido: importar_padron,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=form_importar_padron]').validate();
                var formulario_result = formulario.form();

                if (formulario_result) {
                    var filename = $('#form_importar_padron_file')[0].files[0].name;
                    var impuesto = $('#form_importar_padron_impuesto');
                    var regimen  = $('form_importar_padron_regimen');

                    filename = filename.toUpperCase();

                    if(impuesto.val() == '') {
                        show_alert({msg: 'Debe seleccionar el tipo de impuesto para la carga del padron.'});
                        return false;
                    }

                    if (filename.match(/\.[0-9a-z]+$/i) == '.TXT') {
                        $('form[name=form_importar_padron]').submit();
                    } else {
                        show_alert({msg: 'El archivo importado no es de tipo TXTTTTTT.'});

                        $('#form_importar_padron_file')[0].value = "";
                        $('#form_importar_padron_file').parent().find('.bootstrap-filestyle').find('input').val('');
                        return false;
                    }
                } else {
                    return false;
                }
            }
        });

        initFileInput();

        desbloquear();

        e.stopPropagation();
    });

}
