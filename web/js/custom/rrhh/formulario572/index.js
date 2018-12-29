var dt_formularios572_column_index = {
    id: 0,
    multiselect: 1,
    legajo: 2,
    empleado: 3,
    ultimaPresentacion: 4,
    acciones: 5
};

dt_formularios572 = dt_datatable($('#table-formulario572'), {
    ajax: {
        url: __AJAX_PATH__ + 'formulario572/index_table/',
        data: function (d) {
            d.anio = $('#formulario572_anio').val();
        }
    },
    columnDefs: [
        {
            "targets": dt_formularios572_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_formularios572_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_formularios572_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i></a>\n\
                        <a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>';
            }
        },
        {
            className: "text-center",
            targets: [
                dt_formularios572_column_index.multiselect,
                dt_formularios572_column_index.legajo,
                dt_formularios572_column_index.ultimaPresentacion
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_formularios572_column_index.acciones
        }
    ]
});

$(document).ready(function () {

    initAnio();

    initAnioHandler();

    $('#importar_f572').on('click', function (e) {
        e.preventDefault();
        bloquear();

        importar_f572 = '<div>\n\
                            <span>El archivo a importar debe tener formato xml.</span>\n\
                            <form id="form_importar_f572" method="post" enctype="multipart/form-data" name="form_importar_f572" action="' + __AJAX_PATH__ + 'formulario572/importar/f572">\n\
                                <div class="row">\n\
                                    <div class="col-md-12">\n\
                                        <div class="form-group">\n\
                                            <div class="input-icon right">\n\
                                                <input class="filestyle" type="file" required="required" id="form_importar_f572_file" name="form_importar_f572_file" accept="text/xml">\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </form>\n\
                        </div>';
        show_dialog({
            titulo: 'Importar Formulario 572',
            contenido: importar_f572,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {
                var formulario = $('form[name=form_importar_f572]').validate();
                var formulario_result = formulario.form();
                if (formulario_result) {
                    mimeType = $('#form_importar_f572_file')[0].files[0].type;
                    //es de tipo xml
                    if (mimeType.indexOf('xml') >= 0) {
                        $('form[name=form_importar_f572]').submit();
                    }
                    else {
                        show_alert({msg: 'El archivo importado no es de tipo xml.'});
                        $('#form_importar_f572_file')[0].value = "";
                        $('#form_importar_f572_file').parent().find('.bootstrap-filestyle').find('input').val('');
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
});

/**
 * 
 * @returns {undefined}
 */
function initAnio() {
    $('#formulario572_anio').parent().wrap('<div class="input-group"></div>');

    initDatepicker($('#formulario572_anio'), {
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });

    $('<span class="input-group-addon"><i class="fa fa-calendar"></i></span>')
            .insertBefore($('#formulario572_anio').parent());

    $('#formulario572_anio').prop('readonly', true);
}

/**
 * 
 * @returns {undefined}
 */
function initAnioHandler() {
    $('#formulario572_anio').keypress(function () {
        dt_formularios572.DataTable().ajax.reload();
    });
    $('#formulario572_anio').datepicker().on('changeDate', function (ev) {
        dt_formularios572.DataTable().ajax.reload();
    });
}