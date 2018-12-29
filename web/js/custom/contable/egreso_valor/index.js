var index = 0;

var dt_egresosValor_column_index = {
    id: index++,
    multiselect: index++,
    tipo: index++,
    carpeta: index++,
    responable: index++,
    fecha: index++,
    gerencia: index++,
    saldo: index++,
    porcentaje: index++,
    estado: index++,
    acciones: index++
};

dt_egresosValor = dt_datatable($('#table-egresovalor'), {
    order: [1, 'desc'],
    ajax: __AJAX_PATH__ + 'egresovalor/index_table/',
    columnDefs: [
        {
            "targets": dt_egresosValor_column_index.multiselect,
            "data": "ch_multiselect",
            "render": function (data, type, full, meta) {
                return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
            }
        },
        {
            "targets": dt_egresosValor_column_index.acciones,
            "data": "actions",
            "render": function (data, type, full, meta) {
                var full_data = full[dt_egresosValor_column_index.acciones];
                return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                            <i class="fa fa-search"></i>\n\
                        </a>' +
                        (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                            <i class="fa fa-pencil"></i>\n\
                        </a>' : '') +
                        (full_data.rendirContinuar !== undefined ?
                                '<a href="' + full_data.rendirContinuar + '" class="btn btn-xs purple-studio tooltips" data-original-title="Continuar rendici&oacute;n">\n\
                            <i class="fa fa-sort-amount-asc"></i>\n\
                        </a>' : '') +
                        (full_data.rendirAgregar !== undefined ?
                                '<a href="' + full_data.rendirAgregar + '" class="btn btn-xs purple-studio tooltips" data-original-title="Agregar rendici&oacute;n">\n\
                            <i class="fa fa-plus"></i>\n\
                        </a>' : '') +
                        (full_data.reponer !== undefined ?
                                '<a href="' + full_data.reponer + '" class="btn btn-xs yellow-gold reponer_link tooltips" data-original-title="Reponer">\n\
                            <i class="fa fa-refresh"></i>\n\
                        </a>' : '') +
                        (full_data.reconocimiento !== undefined ?
                                '<a href="' + full_data.reconocimiento + '" class="btn btn-xs red-pink tooltips accion-reconocimiento" data-original-title="Reconocimiento de gasto">\n\
                            <i class="fa fa-user"></i>\n\
                        </a>' : '') +
                        (full_data.ganancia !== undefined ?
                                '<a href="' + full_data.ganancia + '" class="btn btn-xs green-meadow tooltips accion-ganancia" data-original-title="Ganancia">\n\
                            <i class="fa fa-line-chart"></i>\n\
                        </a>' : '') +
                        (full_data.historico !== undefined ?
                                '<a href="' + full_data.historico + '" class="btn btn-xs grey-cascade tooltips anular_autorizacion_contable" data-original-title="Ver hist&oacute;rico">\n\
                            <i class="fa fa-exchange"></i>\n\
                        </a>' : '') +
                        (full_data.cerrar !== undefined ?
                                '<a href="' + full_data.cerrar + '" class="btn btn-xs red tooltips" data-original-title="Cerrar">\n\
                            <i class="fa fa-times"></i>\n\
                        </a>' : '');
            }
        },
        {
            "targets": dt_egresosValor_column_index.estado,
            "createdCell": function (td, cellData, rowData, row, col) {

                var full_data = rowData[dt_egresosValor_column_index.estado];

                $(td).addClass(full_data.estadoClass);
            },
            "render": function (data, type, full, meta) {

                var full_data = full[dt_egresosValor_column_index.estado];

                return  full_data.estado;
            }
        },
        {
            className: "nowrap",
            targets: [
                dt_egresosValor_column_index.tipo,
                dt_egresosValor_column_index.gerencia,
                dt_egresosValor_column_index.fecha,
                dt_egresosValor_column_index.estado
            ]
        },
        {
            className: "text-center",
            targets: [
                dt_egresosValor_column_index.multiselect
            ]
        },
        {
            className: "ctn_acciones text-center nowrap",
            targets: dt_egresosValor_column_index.acciones
        }
    ]
});


jQuery(document).ready(function () {

    initReponerLinks();

    initEditarFechaAsientoContableHandler();
    
    initGananciaButton();
    
    initReconocimientoButton();

});

/**
 * 
 * @returns {undefined}
 */
function initReponerLinks() {
    $(document).on('click', '.reponer_link', function (e) {
        e.preventDefault();
        bloquear();
        hrefSplitted = $(this).prop("href").split('/');
        id = hrefSplitted[hrefSplitted.length - 1];
        var ajax_dialog_reponer = $.ajax({
            type: 'post',
            data: {
                id: id
            },
            url: __AJAX_PATH__ + 'egresovalor/form_reponer'
        });
        $.when(ajax_dialog_reponer).done(function (dataDialogReponer) {
            show_dialog({
                titulo: 'Reponer',
                contenido: dataDialogReponer,
                callbackCancel: function () {
                    desbloquear();
                    return;
                },
                callbackSuccess: function () {
                    var formulario = $('#form_reponer').validate();
                    var formulario_result = formulario.form();
                    if (formulario_result) {
                        var json = {
                            'nombre': $('#adif_contablebundle_egresovalor_responsableegresovalor_nombre').val(),
                            'tipoDocumento': $('#adif_contablebundle_egresovalor_responsableegresovalor_tipoDocumento').val(),
                            'numeroDocumento': $('#adif_contablebundle_egresovalor_responsableegresovalor_nroDocumento').val()
                        };

                        $('#form_reponer').addHiddenInputData(json);

                        $('#form_reponer').submit();
                    } else {
                        desbloquear();
                        return false;
                    }

                }

            });

            initSelects();
        });
    });
}

/**
 * 
 * @returns {undefined}
 */
function customDatepickerInit() {

    var fechaMesCerradoSuperior = $('.mensaje-asiento-contable').data('fecha-mes-cerrado-superior');
    var fechaMesCerradoSuperiorDate = getDateFromString(fechaMesCerradoSuperior);

    var fechaUltimaReposicion = $('.mensaje-asiento-contable').data('fecha-ultima-reposicion');
    var fechaUltimaReposicionDate = getDateFromString(fechaUltimaReposicion);

    if (fechaUltimaReposicionDate.getTime() > fechaMesCerradoSuperiorDate.getTime()) {
        $('#fecha-asiento').datepicker('setStartDate', fechaUltimaReposicion);
    }
}

/**
 * 
 * @returns {undefined}
 */
function customEditarFechaAsientoContableHandler() {

    var data = {
        id_rendicion: $('.mensaje-asiento-contable').data('id-rendicion'),
        numero_asiento: $('#numero-asiento').data('numero-asiento'),
        fecha: $('#fecha-asiento').val()
    };

    $.ajax({
        type: "POST",
        data: data,
        url: __AJAX_PATH__ + 'egresovalor/editar_fecha/'
    }).done(function (response) {

        return true;
    });

}

function initReconocimientoButton() {
    $(document).on('click', 'table .ctn_acciones .accion-reconocimiento', function (e) {
        e.preventDefault();
        var a_href = $(this).attr('href');
        show_confirm({
            title: 'Confirmar',
            type: 'warning',
            msg: '¿Confirma reconocerle el saldo?',
            callbackOK: function () {
                location.href = a_href;
            }
        });
        e.stopPropagation();
    });
}

function initGananciaButton() {
    $(document).on('click', 'table .ctn_acciones .accion-ganancia', function (e) {
        e.preventDefault();
        var a_href = $(this).attr('href');
        show_confirm({
            title: 'Confirmar',
            type: 'warning',
            msg: '¿Confirma enviar a ganancia el saldo?',
            callbackOK: function () {
                location.href = a_href;
            }
        });
        e.stopPropagation();
    });
}