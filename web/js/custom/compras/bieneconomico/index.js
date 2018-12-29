
var dt_bienEconomico_column_index = {
    id: 0,
    multiselect: 1,
    codigoBienEconomico: 2,
    esProducto: 3,
    denominacionBienEconomico: 4,
    rubro: 5,
    requiereEspecificacionTecnica: 6,
    regimenSUSS: 7,
    regimenIVA: 8,
    regimenIIBB: 9,
    regimenGanancias: 10,
    cuentaContable: 11,
    estadoBienEconomico: 12,
    acciones: 13
};

dt_bienEconomico = dt_datatable($('#table-bieneconomico'), {
    // "processing": true,
    // "serverSide": true,
    // "ajax": __AJAX_PATH__ + 'bieneconomico/ajax_table/',
    "ajax": __AJAX_PATH__ + 'bieneconomico/index_table/',    
    columnDefs: [
        {
            "targets": dt_bienEconomico_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_bienEconomico_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_bienEconomico_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>\n\
                        <a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>';
            }
        },
        {
            "targets": dt_bienEconomico_column_index.estadoBienEconomico,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_bienEconomico_column_index.estadoBienEconomico];

                $(td).addClass("state state-" + full_data.aliasTipoImportancia);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_bienEconomico_column_index.estadoBienEconomico];

                return  full_data.estadoBienEconomico;
            }
        },
        {
            className: "text-center",
            targets: [
                dt_bienEconomico_column_index.multiselect,
                dt_bienEconomico_column_index.estadoBienEconomico
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_bienEconomico_column_index.acciones
        }
    ]
});

var impuestosOptions = '<option value="" selected="selected">-- Elija un impuesto --</option>';
for (var index in impuestos) {
    impuestosOptions += "<option value=" + index + ">" + impuestos[index] + "</option>";
}

$(document).ready(function () {

    $('#btn_asignar_regimen').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var table = $('#table-bieneconomico');
        var ids = [];

        ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un bien para asignar un régimen.'});
            desbloquear();
            return;
        }

        formulario_retencion = '<form id="form_multiples_bienes" method="post" name="form_multiples_bienes">\n\
                                            <div class="row">\n\
                                                <div class="col-md-12">\n\
                                                    <div class="form-group">\n\
                                                        <label class="control-label required" for="select_impuesto">Impuesto</label>\n\
                                                        <div class="input-icon right">\n\
                                                            <i class="fa"></i>\n\
                                                            <select id="select_impuesto" name="select_impuesto" class="required form-control choice">' + impuestosOptions + '</select>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                            <div class="row">\n\
                                                <div class="col-md-12">\n\
                                                    <div class="form-group">\n\
                                                        <label class="control-label required" for="select_regimen">Régimen de retención</label>\n\
                                                        <div class="input-icon right">\n\
                                                            <i class="fa"></i>\n\
                                                            <select id="select_regimen" name="select_regimen" class="required form-control choice">\n\
                                                                <option value="" selected="selected">-- Elija un régimen de retención --</option>\n\
                                                            </select>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                        </form>';
        show_dialog({
            titulo: 'Asignar régimen de retención a múltiples bienes',
            contenido: formulario_retencion,
            callbackCancel: function () {
                desbloquear();
            },
            callbackSuccess: function () {

                var formulario = $('#form_multiples_bienes').validate({
                    rules: {
                        'form_multiples_bienes[select_regimen]': {
                            required: true,
                        },
                        'form_multiples_bienes[select_impuesto]': {
                            required: true,
                        }
                    }
                });
                var formulario_result = formulario.form();
                if (formulario_result) {
                    $.ajax({
                        type: "POST",
                        data: {ids: JSON.stringify(ids.toArray()), regimen: $('#select_regimen').val(), impuesto: $('#select_impuesto').val()},
                        url: __AJAX_PATH__ + 'regimenretencionbieneconomico/regimenesmultiple/'
                    }).always(function (data, textStatus, jqXHR) {
                        switch (jqXHR.status) {
                            case 200:
                                show_alert({title: 'Asignación múltiple', msg: 'Asignación realizada con éxito'});
                                break;
                            default:
                                show_alert({title: 'Error', msg: 'Ha ocurrido un error. Intente nuevamente', type: 'error'});
                        }
                        dt_bienEconomico.DataTable().ajax.reload();
                    });
                } else {
                    return false;
                }
            }
        });

        $("#select_impuesto").on('change', function () {
            actualizarRegimenes($(this).val());
        });

        $('.bootbox').removeAttr('tabindex');

        $('#select_impuesto').select2();
        $('#select_regimen').select2();

        desbloquear();

        e.stopPropagation();
    });

});


function actualizarRegimenes(impuesto) {
    var regimenesOptions = '<option value="" selected="selected">-- Elija un régimen de retención ' + impuestos[impuesto] + ' --</option>';
    for (var index in regimenes[impuesto]) {
        regimenesOptions += "<option value=" + index + ">" + regimenes[impuesto][index] + "</option>";
    }
    $('#select_regimen').html(regimenesOptions);
    $('#select_regimen').select2("val", "");

}